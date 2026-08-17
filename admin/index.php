<?php
/**
 * Dashboard.
 *
 * Answers the questions someone actually opens the admin to ask, in the order
 * they matter: is anything waiting on me, is the flow of enquiries going up or
 * down, where are they coming from, and what has been happening in here.
 *
 * It used to be five static counts. Counts alone answer none of those — "12
 * leads" does not tell you whether that is good, and it certainly does not tell
 * you that three of them have been sitting untouched since Tuesday. Everything
 * below comes from data the schema already holds; nothing is invented and there
 * are no sample figures.
 *
 * CROSS-DRIVER SQL
 *
 * The site develops on PostgreSQL and deploys to MySQL, so every query here
 * either is plain ANSI or goes through a bridge in inc/db.php. Two habits do
 * most of the work:
 *
 *   * Relative dates come from sql_now_minus_days()/sql_now_minus_secs(),
 *     never a literal `now() - interval '7 days'`.
 *   * Day-of-week/day bucketing is done in PHP from a plain timestamp column
 *     rather than in SQL. date_trunc() is PostgreSQL-only and its MySQL
 *     equivalent is a different function with different arguments, so the
 *     cheapest correct answer is to not ask the database for it at all.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('leads.view');
require_once __DIR__ . '/lib/chart.php';
require __DIR__ . '/lib/layout.php';

const DASH_DAYS       = 30;      // Width of the trend chart.
const DASH_STALE_SECS = 172800;  // 48 hours.

// ---------------------------------------------------------------------------
// Headline counts and the week-on-week delta
// ---------------------------------------------------------------------------

$leadsTotal = (int)scalar('SELECT count(*) FROM leads');
$leadsNew   = (int)scalar("SELECT count(*) FROM leads WHERE status = 'new'");

$week = (int)scalar(
    'SELECT count(*) FROM leads WHERE created_at > ' . sql_now_minus_days(7)
);

// The seven days before those seven, so the delta compares like with like. A
// half-open window (> 14 days ago, <= 7 days ago) rather than BETWEEN, so a
// lead landing exactly on the boundary is counted once and not twice.
$prevWeek = (int)scalar(
    'SELECT count(*) FROM leads
      WHERE created_at >  ' . sql_now_minus_days(14) . '
        AND created_at <= ' . sql_now_minus_days(7)
);

// Percentage change is meaningless against a zero base — "up 100%" from one
// lead to two is technically true and completely unhelpful — so the tile shows
// the absolute movement and only adds a percentage when there is something to
// take a percentage of.
$delta    = $week - $prevWeek;
$deltaPct = $prevWeek > 0 ? (int)round(($delta / $prevWeek) * 100) : null;

$postsPublished = (int)scalar("SELECT count(*) FROM posts WHERE status = 'published'");
$postsDraft     = (int)scalar("SELECT count(*) FROM posts WHERE status = 'draft'");

// ---------------------------------------------------------------------------
// Trend — leads per day for the last 30 days
//
// The database returns raw timestamps and PHP buckets them. Days with no leads
// are absent from the result set entirely, so the buckets are pre-seeded with
// zero for every day in the window — otherwise a quiet week would compress the
// x-axis and the chart would lie about the shape of the data.
// ---------------------------------------------------------------------------

$buckets = [];
for ($i = DASH_DAYS - 1; $i >= 0; $i--) {
    $buckets[date('Y-m-d', strtotime("-{$i} days"))] = 0;
}

foreach (all('SELECT created_at FROM leads WHERE created_at > ' . sql_now_minus_days(DASH_DAYS)) as $row) {
    $day = date('Y-m-d', strtotime((string)$row['created_at']));
    if (isset($buckets[$day])) {
        $buckets[$day]++;
    }
}

$series = [];
foreach ($buckets as $date => $count) {
    $series[] = ['date' => $date, 'count' => $count];
}

// ---------------------------------------------------------------------------
// Pipeline
// ---------------------------------------------------------------------------

$pipelineRaw = [];
foreach (all('SELECT status, count(*) AS n FROM leads GROUP BY status') as $row) {
    $pipelineRaw[(string)$row['status']] = (int)$row['n'];
}

// Driven by the canonical status list rather than by whatever the query
// returned, so an empty stage still appears as a zero row. A pipeline that
// hides its empty stages is the one place a funnel chart routinely misleads.
$statusBadge = static fn(string $s): string => match ($s) {
    'new'          => 'badge-warn',
    'won'          => 'badge-ok',
    'lost', 'spam' => 'badge-danger',
    default        => 'badge-muted',
};

$pipeline = [];
foreach (['new', 'contacted', 'qualified', 'won', 'lost', 'spam'] as $s) {
    $pipeline[] = [
        'label' => ucfirst($s),
        'value' => $pipelineRaw[$s] ?? 0,
        'badge' => '<span class="badge ' . $statusBadge($s) . '">' . e(ucfirst($s)) . '</span>',
        'href'  => '/admin/leads.php?status=' . rawurlencode($s),
    ];
}

// ---------------------------------------------------------------------------
// Needs attention — the operational queue
//
// Two ways a lead falls through the cracks: it arrives, nobody changes its
// status, and it quietly ages past the point where a reply is still credible;
// or it has a status but no owner, so everyone assumes someone else has it.
//
// The unassigned arm is restricted to statuses that are still open. A won or
// lost lead has no owner because it does not need one, and letting those fill
// the queue is how an actionable list becomes a list nobody reads.
// ---------------------------------------------------------------------------

$staleWhere = '(l.status = ? AND l.created_at < ' . sql_now_minus_secs() . ')'
            . " OR (l.assigned_to IS NULL AND l.status IN ('new', 'contacted', 'qualified'))";

$staleParams = ['new', DASH_STALE_SECS];

$staleTotal = (int)scalar("SELECT count(*) FROM leads l WHERE {$staleWhere}", $staleParams);

$stale = all("
    SELECT l.id, l.company_name, l.contact_number, l.status, l.created_at, l.assigned_to
      FROM leads l
     WHERE {$staleWhere}
     ORDER BY l.created_at ASC, l.id ASC
     LIMIT 8
", $staleParams);

// ---------------------------------------------------------------------------
// Which page converts
// ---------------------------------------------------------------------------

$sources = [];
foreach (all("
    SELECT source_page, count(*) AS n
      FROM leads
     WHERE source_page <> ''
     GROUP BY source_page
     ORDER BY count(*) DESC, source_page ASC
     LIMIT 6
") as $row) {
    $sources[] = [
        'label' => str_cut((string)$row['source_page'], 42),
        'value' => (int)$row['n'],
    ];
}

// ---------------------------------------------------------------------------
// Content readiness
//
// "Ready" means the row would not embarrass anyone if a visitor read it: a case
// study whose metric is evidenced rather than invented, a testimonial that is
// both real AND has recorded permission to publish. Consent is part of the bar,
// not a separate nicety — an attributed quote published without it is the kind
// of problem that arrives as an email from the client.
// ---------------------------------------------------------------------------

$csTotal = (int)scalar('SELECT count(*) FROM case_studies');
$csReady = (int)scalar('SELECT count(*) FROM case_studies WHERE NOT is_placeholder');

$tsTotal = (int)scalar('SELECT count(*) FROM testimonials');
$tsReady = (int)scalar('SELECT count(*) FROM testimonials WHERE NOT is_placeholder AND consent_given');

$proofTotal = $csTotal + $tsTotal;
$proofReady = $csReady + $tsReady;
$proofTodo  = $proofTotal - $proofReady;

// ---------------------------------------------------------------------------
// Activity feed
// ---------------------------------------------------------------------------

$activity = all('
    SELECT a.action, a.entity_type, a.entity_id, a.created_at, u.name AS user_name
      FROM audit_log a
      LEFT JOIN users u ON u.id = a.user_id
     ORDER BY a.created_at DESC, a.id DESC
     LIMIT 10
');

// ---------------------------------------------------------------------------
// Failed sign-ins
//
// login_attempts has been recording every attempt since migration 003 and has
// had no interface at all — the one table in the schema whose entire purpose is
// to be looked at when something is wrong. A burst of failures against a real
// address is the earliest signal available that someone is working on the door.
//
// `NOT successful` rather than `successful = false`: TINYINT(1) on MySQL and a
// real boolean on PostgreSQL both negate correctly, without binding a boolean
// literal that the two drivers marshal differently.
// ---------------------------------------------------------------------------

$failed24 = (int)scalar(
    'SELECT count(*) FROM login_attempts
      WHERE NOT successful AND created_at > ' . sql_now_minus_days(1)
);

$failedTargets = all('
    SELECT identifier, ip, count(*) AS n, max(created_at) AS last_at
      FROM login_attempts
     WHERE NOT successful AND created_at > ' . sql_now_minus_days(7) . '
     GROUP BY identifier, ip
     ORDER BY count(*) DESC, max(created_at) DESC
     LIMIT 6
');

admin_head([
    'title'   => 'Dashboard',
    'heading' => 'Dashboard',
    'intro'   => 'Enquiry flow, the queue that needs a person, and what has changed.',
    'active'  => '/admin/',
]);
?>

<div class="stat-grid">
    <div class="stat<?= $staleTotal > 0 ? ' attn' : '' ?>">
        <div class="n"><?= number_format($staleTotal) ?></div>
        <div class="k">Needs attention</div>
        <div class="sub">Stale or unassigned</div>
    </div>

    <div class="stat">
        <div class="n"><?= number_format($week) ?></div>
        <div class="k">Last 7 days</div>
        <div class="sub">
<?php if ($delta === 0): ?>
            <span class="delta delta-flat">no change</span> on previous 7
<?php else: ?>
            <span class="delta <?= $delta > 0 ? 'delta-up' : 'delta-down' ?>">
                <?= $delta > 0 ? '&#9650;' : '&#9660;' ?>
                <?= $delta > 0 ? '+' : '&minus;' ?><?= number_format(abs($delta)) ?><?php
                    if ($deltaPct !== null) { echo ' (' . ($delta > 0 ? '+' : '&minus;') . abs($deltaPct) . '%)'; }
                ?>
            </span> on previous 7
<?php endif; ?>
        </div>
    </div>

    <div class="stat">
        <div class="n"><?= number_format($leadsNew) ?></div>
        <div class="k">Unopened</div>
        <div class="sub">Still marked new</div>
    </div>

    <div class="stat">
        <div class="n"><?= number_format($leadsTotal) ?></div>
        <div class="k">Enquiries all time</div>
        <div class="sub"><?= number_format($postsPublished) ?> article<?= $postsPublished === 1 ? '' : 's' ?> published<?= $postsDraft ? ', ' . $postsDraft . ' draft' . ($postsDraft === 1 ? '' : 's') : '' ?></div>
    </div>
</div>

<div class="card card-attn">
    <h2>Needs attention</h2>
    <p class="hint">
        Enquiries still marked new after 48 hours, plus anything open that nobody
        owns. Oldest first — this is the list to work from.
    </p>

<?php if (!$stale): ?>
    <p class="empty-state">
        <?= icon('circle-check') ?>
        <strong>Nothing is waiting.</strong>
        Every open enquiry has an owner and none has gone stale.
    </p>
<?php else: ?>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Waiting</th>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Why</th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($stale as $l):
          $age       = human_ago((string)$l['created_at']);
          $unowned   = $l['assigned_to'] === null;
          $isStale   = $l['status'] === 'new'
                       && strtotime((string)$l['created_at']) < time() - DASH_STALE_SECS; ?>
                <tr>
                    <td><?= e($age) ?></td>
                    <td><a href="<?= e(site_path('/admin/leads.php?id=' . (int)$l['id'])) ?>"><?= e($l['company_name']) ?></a></td>
                    <td><?= e($l['contact_number']) ?></td>
                    <td><span class="badge <?= $statusBadge((string)$l['status']) ?>"><?= e($l['status']) ?></span></td>
                    <td class="reason">
<?php if ($isStale && $unowned): ?>
                        Unanswered and unassigned
<?php elseif ($isStale): ?>
                        No reply in 48h
<?php else: ?>
                        No owner
<?php endif; ?>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php if ($staleTotal > count($stale)): ?>
    <p class="hint after-table">
        Showing the 8 oldest of <?= number_format($staleTotal) ?>.
        <a href="<?= e(site_path('/admin/leads.php?status=new')) ?>">See all new enquiries</a>
    </p>
<?php endif; ?>
<?php endif; ?>
</div>

<div class="grid-2 grid-wide-first">
    <div class="card">
        <h2>Enquiries, last <?= DASH_DAYS ?> days</h2>
        <p class="hint">One point per day. The peak day is labelled.</p>
        <?= chart_area($series, 'No enquiries in the last ' . DASH_DAYS . ' days.') ?>
    </div>

    <div class="card">
        <h2>Pipeline</h2>
        <p class="hint">Every enquiry by stage. Click a stage to filter the list.</p>
        <?= chart_bars($pipeline, 'No enquiries yet.') ?>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h2>Where enquiries come from</h2>
        <p class="hint">The page the form was submitted from — which page is doing the converting.</p>
        <?= chart_bars($sources, 'No enquiry has recorded a source page yet.') ?>
    </div>

    <div class="card">
        <h2>Content readiness</h2>
        <p class="hint">
            Case studies with an evidenced metric, and testimonials that are both real
            and have recorded consent. Anything short of that still shows an orange
            PLACEHOLDER badge to visitors.
        </p>
        <?= chart_meter($proofReady, $proofTotal, 'ready', 'need work') ?>

<?php if ($proofTodo > 0): ?>
        <ul class="todo-list">
<?php if ($csTotal - $csReady > 0): ?>
            <li>
                <strong><?= $csTotal - $csReady ?></strong> case
                stud<?= ($csTotal - $csReady) === 1 ? 'y' : 'ies' ?> with an invented metric —
                <a href="<?= e(site_path('/admin/case-studies.php')) ?>">replace with evidenced results</a>
            </li>
<?php endif; ?>
<?php if ($tsTotal - $tsReady > 0): ?>
            <li>
                <strong><?= $tsTotal - $tsReady ?></strong>
                testimonial<?= ($tsTotal - $tsReady) === 1 ? '' : 's' ?> unattributed or without consent —
                <a href="<?= e(site_path('/admin/testimonials.php')) ?>">add real names and permission</a>
            </li>
<?php endif; ?>
        </ul>
<?php endif; ?>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h2>Recent activity</h2>
        <p class="hint">The last ten changes made through this admin.</p>

<?php if (!$activity): ?>
        <p class="chart-empty">Nothing recorded yet.</p>
<?php else: ?>
        <ul class="feed">
<?php foreach ($activity as $a): ?>
            <li>
                <span class="feed-when"><?= e(human_ago((string)$a['created_at'])) ?></span>
                <span class="feed-what">
                    <strong><?= $a['user_name'] !== null ? e($a['user_name']) : 'A deleted user' ?></strong>
                    <span class="badge badge-muted"><?= e($a['action']) ?></span>
<?php if ($a['entity_type'] !== '' && $a['entity_id'] !== ''): ?>
                    <span class="feed-entity"><?= e($a['entity_type'] . ' #' . $a['entity_id']) ?></span>
<?php endif; ?>
                </span>
            </li>
<?php endforeach; ?>
        </ul>
<?php if (can('audit.view')): ?>
        <p class="hint after-table"><a href="<?= e(site_path('/admin/audit.php')) ?>">Open the full audit log</a></p>
<?php endif; ?>
<?php endif; ?>
    </div>

    <div class="card<?= $failed24 > 0 ? ' card-attn' : '' ?>">
        <h2>Failed sign-ins</h2>
        <p class="hint">
            Rejected attempts on this admin. Throttling is automatic — this is here so
            a sustained attempt against a real address is visible rather than only logged.
        </p>

        <p class="figure-line">
            <strong class="<?= $failed24 > 0 ? 'is-warn' : '' ?>"><?= number_format($failed24) ?></strong>
            in the last 24 hours
        </p>

<?php if (!$failedTargets): ?>
        <p class="empty-state">
            <?= icon('shield') ?>
            <strong>Nothing to report.</strong>
            No failed sign-in in the last seven days.
        </p>
<?php else: ?>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Address tried</th>
                        <th>From</th>
                        <th class="num">Attempts</th>
                        <th>Last</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($failedTargets as $f): ?>
                    <tr>
                        <td><?= e(str_cut((string)$f['identifier'], 34)) ?></td>
                        <td><?= e((string)$f['ip'] !== '' ? (string)$f['ip'] : 'unknown') ?></td>
                        <td class="num"><?= (int)$f['n'] ?></td>
                        <td><?= e(human_ago((string)$f['last_at'])) ?></td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="hint after-table">Grouped by address and origin, last seven days.</p>
<?php endif; ?>
    </div>
</div>

<?php admin_foot(); ?>
