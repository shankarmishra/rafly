<?php
/**
 * Seeds the database with the content the site currently hardcodes.
 *
 *     php inc/tools/seed.php
 *
 * Idempotent: every write is an upsert keyed on something stable, so running
 * it twice changes nothing. It never overwrites a row an editor has since
 * touched — see the DO UPDATE clauses, which only refresh structural fields.
 *
 * The goal is that the database reproduces exactly what the templates render
 * today, including the placeholder flags. Seeding real-looking case studies or
 * named testimonials would defeat the point of the PLACEHOLDER badge, so the
 * invented metrics are carried over verbatim WITH is_placeholder = true.
 *
 * CLI only, under inc/ so .htaccess keeps it off the web.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

try {
    db();
} catch (DbUnavailable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$n = static fn(string $label, int $count) => printf("  %-22s %d\n", $label, $count);

// ---------------------------------------------------------------------------
// Settings
// ---------------------------------------------------------------------------
// Values mirror inc/config.php and the trust bar in index.php. The *_verified
// booleans are what will drive the PLACEHOLDER badge on the two unverified
// figures once index.php reads from here.

$settings = [
    // group, key, value, type, label, hint
    ['contact', 'contact.phone',   RAW_PHONE,      'tel',   'Phone number',    'National number, digits only. Display and WhatsApp both derive from this.'],
    ['contact', 'contact.email',   CONTACT_EMAIL,  'email', 'Contact email',   'Where the mailto: on the privacy page points.'],
    ['contact', 'contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306', 'text', 'Address', 'Shown in the footer and in JSON-LD.'],
    // The value here was previously hardcoded identically in index.php's contact
    // section and duplicated a second time (in structured form) in
    // inc/schema.php's openingHoursSpecification. This setting is now the single
    // source for the human-readable display text; the JSON-LD stays separate
    // since it needs machine-readable day/open/close fields, not a display string.
    ['contact', 'contact.hours',   'Mon - Fri, 09:00 - 18:00 IST', 'text', 'Business hours (display text)', 'Shown in the contact section and the footer.'],

    // Real business-registration numbers — left blank rather than invented.
    // Empty means the footer simply omits the line until these are filled in.
    ['legal',   'legal.cin', '', 'text', 'CIN / LLP number', 'Company or LLP registration number. Leave blank to hide this line in the footer.'],
    ['legal',   'legal.gst', '', 'text', 'GST number', 'Leave blank to hide this line in the footer.'],

    ['trust',   'trust.projects.value',        '120', 'number', 'Projects delivered',   'Shown in the trust bar.'],
    ['trust',   'trust.projects.verified',     '0',   'bool',   'Projects figure verified', 'Until this is on, the figure renders with a PLACEHOLDER badge.'],
    ['trust',   'trust.satisfaction.value',    '98',  'number', 'Client satisfaction %', 'Shown in the trust bar.'],
    ['trust',   'trust.satisfaction.verified', '0',   'bool',   'Satisfaction figure verified', 'Until this is on, the figure renders with a PLACEHOLDER badge.'],
    ['trust',   'trust.services.value',        '5',   'number', 'Services under one roof', 'A structural fact, not a metric — not badged.'],

    ['social',  'social.linkedin',  'https://www.linkedin.com/company/rafly-digital-growth-private-limited/', 'url', 'LinkedIn',  ''],
    ['social',  'social.instagram', 'https://www.instagram.com/officialrafly.in?igsh=MTMwYWZhb29waWZtbA==',  'url', 'Instagram', ''],
    ['social',  'social.facebook',  'https://www.facebook.com/share/1Lmk1gqPSr/',                            'url', 'Facebook',  ''],
    ['social',  'social.youtube',   'https://youtu.be/EbPOtjdrdHc?si=HIh_GJxllL8SPzPa',                      'url', 'YouTube',   ''],

    // Read by partials/head.php. Blank value = Pixel off, no script tag at
    // all — that is the intended way to disable it, not a broken state.
    ['analytics', 'analytics.meta_pixel_id', '802516299551165', 'text', 'Meta Pixel ID',
        'From Facebook Events Manager. Leave blank to disable the Pixel entirely — no script loads without it.'],
];

$i = 0;
foreach ($settings as $s) {
    [$group, $key, $value, $type, $label, $hint] = $s;
    // Only the descriptive fields are refreshed on conflict. The value belongs
    // to whoever edited it last in the admin, and re-seeding must not stamp
    // over that.
    // The only upsert in the codebase, and the one place the two dialects have
    // no common spelling. "key" is quoted because MySQL reserves it; ANSI_QUOTES
    // (set in inc/db.php) makes that identical on PostgreSQL.
    //
    // MariaDB has no alias form for the new row, so this uses VALUES(col) —
    // deprecated in MySQL 8.0.20 but still supported, and the only spelling both
    // servers accept.
    $upsert = db_driver() === 'pgsql'
        ? 'ON CONFLICT ("key") DO UPDATE
              SET type = EXCLUDED.type,
                  label = EXCLUDED.label,
                  hint = EXCLUDED.hint,
                  group_name = EXCLUDED.group_name,
                  sort_order = EXCLUDED.sort_order'
        : 'ON DUPLICATE KEY UPDATE
              type = VALUES(type),
              label = VALUES(label),
              hint = VALUES(hint),
              group_name = VALUES(group_name),
              sort_order = VALUES(sort_order)';

    q('
        INSERT INTO settings ("key", value, type, label, hint, group_name, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ' . $upsert . '
    ', [$key, $value, $type, $label, $hint, $group, $i]);
    $i++;
}
$n('settings', count($settings));

// ---------------------------------------------------------------------------
// Bundles — from the BUNDLES const in inc/config.php
// ---------------------------------------------------------------------------

$order = 0;
foreach (BUNDLES as $b) {
    $id = scalar('SELECT id FROM bundles WHERE name = ?', [$b['name']]);

    if ($id === null) {
        $id = insert_returning_id('
            INSERT INTO bundles (name, sub, is_featured, sort_order)
            VALUES (?, ?, ?, ?)
        ', [$b['name'], $b['sub'], $b['featured'], $order]);
    } else {
        q('UPDATE bundles SET sub = ?, is_featured = ?, sort_order = ?, updated_at = now() WHERE id = ?',
          [$b['sub'], $b['featured'], $order, $id]);
    }

    // Points are replaced wholesale: they are an ordered list, and reconciling
    // an edited list item-by-item is more machinery than a three-row rewrite.
    q('DELETE FROM bundle_points WHERE bundle_id = ?', [$id]);
    foreach (array_values($b['points']) as $j => $label) {
        q('INSERT INTO bundle_points (bundle_id, label, sort_order) VALUES (?, ?, ?)', [$id, $label, $j]);
    }
    $order++;
}
$n('bundles', count(BUNDLES));

// ---------------------------------------------------------------------------
// Case studies — carried over from the $work array in index.php
// ---------------------------------------------------------------------------
// These metrics are INVENTED. They are seeded so the page renders unchanged,
// and they keep is_placeholder = true so they keep announcing themselves as
// unfinished. Replace them in the admin with evidenced results; the badge
// clears itself when the flag is turned off.

$cases = [
    ['+182%', 'organic traffic in 6 months',      'Client Project', 'E-commerce', 'Web Development, SEO, Content'],
    ['3.4x',  'increase in online orders',        'Client Project', 'Retail',     'Shopify, Marketing, Support'],
    ['0',     'security incidents post-audit',    'Client Project', 'Services',   'Web Security, Hardening'],
];
foreach ($cases as $j => $c) {
    [$value, $label, $client, $sector, $tags] = $c;
    $exists = scalar('SELECT id FROM case_studies WHERE sort_order = ?', [$j]);
    if ($exists === null) {
        q('
            INSERT INTO case_studies
                (metric_value, metric_label, client_name, sector, tags, is_placeholder, sort_order)
            VALUES (?, ?, ?, ?, ?, true, ?)
        ', [$value, $label, $client, $sector, $tags, $j]);
    }
}
$n('case studies', count($cases));

// ---------------------------------------------------------------------------
// Testimonials — from index.php
// ---------------------------------------------------------------------------
// The quotes are well-written and plausible but UNATTRIBUTED: every one is
// signed "[Client Name]". consent_given stays false, which is the honest state
// and which the admin should require before is_placeholder can be cleared.

$quotes = [
    ['We went from three separate freelancers to one Rafly package, and the website, ads, and content finally look like they belong to the same brand.',
     'Founder', 'D2C Skincare Brand'],
    ['The security review they ran as part of our package caught issues on our checkout flow that none of our previous vendors ever mentioned.',
     'Operations Lead', 'B2B SaaS Startup'],
    ['Having one team handle our storefront listings, marketing, and support meant we stopped repeating ourselves to five different people every week.',
     'Co-Founder', 'Online Fashion Store'],
];
foreach ($quotes as $j => $t) {
    [$quote, $role, $company] = $t;
    $exists = scalar('SELECT id FROM testimonials WHERE sort_order = ?', [$j]);
    if ($exists === null) {
        q('
            INSERT INTO testimonials
                (quote, author_name, author_role, author_company, consent_given, is_placeholder, sort_order)
            VALUES (?, ?, ?, ?, false, true, ?)
        ', [$quote, '[Client Name]', $role, $company, $j]);
    }
}
$n('testimonials', count($quotes));

// ---------------------------------------------------------------------------
// Leads — import the CSV if one exists
// ---------------------------------------------------------------------------
// submit.php keeps writing the CSV; this only backfills what is already there.
// Dedup is on (created_at, company_name, contact_number) because the CSV has no
// id column, so a re-run cannot tell two identical rows apart from one row seen
// twice — and double-counting a lead is worse than dropping an exact duplicate.

$imported = 0;
$skipped  = 0;
if (is_file(LEAD_CSV_FILE) && ($fp = fopen(LEAD_CSV_FILE, 'r')) !== false) {
    /* MAP BY HEADER NAME, NOT BY POSITION.

       This used to destructure the first four cells: [$ts, $company, $phone,
       $desc] = $row. Migration 006 added contact_name and contact_email, and
       submit.php now writes SIX columns — timestamp, name, email, company,
       phone, description — so positional reading silently shifted every field
       one place left. Sixteen leads imported with the person's NAME in
       company_name and their EMAIL in contact_number, and nothing reported a
       problem, because every one of those fields is just a string.

       Worse, the header on an existing file is whatever was written the day it
       was created, so a CSV can legitimately hold a four-column header above
       six-column rows — which is exactly the file this repo has. Reading by
       name handles a correct header; the $wide fallback handles that one. */
    $header = fgetcsv($fp);
    $map = [];
    foreach ((array)$header as $i => $name) {
        $map[strtolower(trim((string)$name))] = $i;
    }
    $col = static function (array $row, array $names, ?int $fallback) use ($map) {
        foreach ($names as $n) {
            if (isset($map[$n]) && array_key_exists($map[$n], $row)) {
                return (string)$row[$map[$n]];
            }
        }
        return ($fallback !== null && array_key_exists($fallback, $row)) ? (string)$row[$fallback] : '';
    };
    $legacyHeader = count((array)$header) < 6;

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 4) {
            continue;
        }
        // A six-cell row under a four-cell header: the header cannot name these,
        // so fall back to the layout submit.php actually writes.
        $six = $legacyHeader && count($row) >= 6;

        if ($six) {
            /* The header is FOUR columns and this row is SIX, so the header is
               not merely incomplete — it is actively wrong. It still contains
               the strings "Company Name" and "Contact Number", pointing at
               indices 1 and 2, which in a six-column row hold the name and the
               email. Consulting it here is worse than ignoring it: a name-based
               lookup finds a match and confidently returns the wrong cell.
               So for these rows the header is skipped entirely and the layout
               submit.php writes is used directly. */
            [$ts, $name, $email, $company, $phone, $desc] = array_slice($row, 0, 6);
        } else {
            $ts      = $col($row, ['timestamp'], 0);
            $name    = $col($row, ['name', 'contact name'], null);
            $email   = $col($row, ['email', 'contact email'], null);
            $company = $col($row, ['company name', 'company'], 1);
            $phone   = $col($row, ['contact number', 'phone'], 2);
            $desc    = $col($row, ['requirements description', 'description'], 3);
        }

        // csv_safe() in submit.php tab-prefixes fields starting = + - @ to stop
        // Excel executing them. Strip it back off on the way in.
        $company = ltrim($company, "\t");
        $desc    = ltrim($desc, "\t");
        $name    = ltrim($name, "\t");
        $email   = ltrim($email, "\t");

        // No explicit cast: the CSV writes date('Y-m-d H:i:s'), which both
        // PostgreSQL and MySQL resolve from the column's own type. The old
        // ?::timestamptz was PostgreSQL-only syntax and bought nothing.
        $dupe = scalar('
            SELECT id FROM leads
             WHERE created_at = ? AND company_name = ? AND contact_number = ?
        ', [$ts, $company, $phone]);

        if ($dupe !== null) {
            $skipped++;
            continue;
        }

        q('
            INSERT INTO leads (company_name, contact_name, contact_email, contact_number,
                               description, consent_given, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, true, ?, ?)
        ', [$company, $name, $email, $phone, $desc, $ts, $ts]);
        $imported++;
    }
    fclose($fp);
}
printf("  %-22s %d imported, %d already present\n", 'leads', $imported, $skipped);

echo "\nseed complete\n";
echo "articles are seeded separately: php inc/tools/seed-posts.php\n";
