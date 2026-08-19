<?php
/**
 * unflag-seed.php — put the honesty flags back.
 *
 *     php inc/tools/unflag-seed.php [--apply]
 *
 * WHAT WENT WRONG, SO IT IS NOT REPEATED
 *
 * inc/data/seed-preview.php is sample content. Its own header says so: "Every
 * row here is anonymised preview content... Nothing is a client claim, a real
 * person, or a figure anyone has verified." inc/tools/seed.php is careful
 * about this too, and says in its docblock that it carries the invented
 * metrics over WITH is_placeholder = true.
 *
 * The live database nevertheless held all fifteen of those rows — six case
 * studies, four testimonials, five team members — with is_placeholder = 0,
 * and the four testimonials additionally with consent_given = 1. The two
 * trust-bar figures ("120+ Projects Delivered", "98% Client Satisfaction")
 * were set verified = 1.
 *
 * Those flags are the ONLY thing standing between sample data and a published
 * client claim. inc/repo/content.php filters on exactly them, and
 * inc/repo/metrics.php states the rule outright: A NUMBER IS EITHER BACKED OR
 * IT IS NOT SHOWN AS FACT. With the flags flipped, every one of those gates
 * was passing sample rows through to a live marketing page: invented metrics,
 * named people who do not work here, and testimonials attributed to clients
 * who never said them.
 *
 * WHAT THIS DOES
 *
 * Sets is_placeholder = 1 on rows whose content matches seed-preview.php, and
 * trust.*.verified = 0 on the two unbacked metrics. It DELETES NOTHING: every
 * row stays exactly where it is and can be unflagged from the admin panel the
 * moment there is a real client behind it. That is also why this is a flag
 * reset and not a DELETE — the rows are useful as a preview of the layout.
 *
 * Runs as a dry run by default and prints what it would change. Pass --apply
 * to write. Idempotent either way.
 *
 * CLI only, under inc/ so .htaccess keeps it off the web.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../repo/seed.php';

$apply = in_array('--apply', $argv, true);

if (!db_available()) {
    fwrite(STDERR, "Database unavailable — nothing to do.\n");
    exit(1);
}

/** Values that identify a row as having come from seed-preview.php. */
/* seed_preview_all(), NOT seed_preview(). The latter deliberately returns
   nothing whenever a database exists — it is a fallback for when there is no
   DB at all — and this tool runs precisely when there IS one. Reading the
   file through seed_preview_all() is what makes the comparison possible. */
$seed = seed_preview_all();
$seedNames = [
    'case_studies' => array_column($seed['case_studies'] ?? [], 'client_name'),
    'testimonials' => array_column($seed['testimonials'] ?? [], 'author_name'),
    'team_members' => array_column($seed['team_members'] ?? [], 'name'),
];
$keyColumn = [
    'case_studies' => 'client_name',
    'testimonials' => 'author_name',
    'team_members' => 'name',
];

echo $apply ? "APPLYING\n\n" : "DRY RUN — pass --apply to write\n\n";
$total = 0;

foreach ($seedNames as $table => $names) {
    $names = array_values(array_filter($names));
    if (!$names) {
        echo str_pad($table, 16), "no seed rows defined — skipped\n";
        continue;
    }
    $col   = $keyColumn[$table];
    $marks = implode(',', array_fill(0, count($names), '?'));

    $rows = all(
        "SELECT id, `$col` AS label FROM `$table`
          WHERE NOT is_placeholder AND `$col` IN ($marks)",
        $names
    );

    foreach ($rows as $r) {
        echo str_pad($table, 16), "flag as sample: ", $r['label'], "\n";
        $total++;
    }

    if ($apply && $rows) {
        q("UPDATE `$table` SET is_placeholder = 1 WHERE `$col` IN ($marks)", $names);
        /* Consent is a separate claim from authenticity, and a seeded quote
           has neither. Reset it too, or a future edit that clears
           is_placeholder alone silently republishes an unconsented quote. */
        if ($table === 'testimonials') {
            q("UPDATE `testimonials` SET consent_given = 0 WHERE `$col` IN ($marks)", $names);
        }
    }
}

/* The trust bar. These two are the numbers metrics_trust_bar() lets through
   on the strength of a verified flag alone; neither has a source. */
foreach (['trust.projects.verified', 'trust.satisfaction.verified'] as $key) {
    $current = setting($key, '0');
    if ($current === '1') {
        echo str_pad('settings', 16), "unverify: $key\n";
        $total++;
        if ($apply) {
            q('UPDATE settings SET value = ? WHERE `key` = ?', ['0', $key]);
        }
    }
}

echo "\n", $total === 0 ? "Nothing to change — flags already correct.\n"
                        : ($apply ? "Done: $total change(s) written.\n"
                                  : "$total change(s) pending. Re-run with --apply.\n");
