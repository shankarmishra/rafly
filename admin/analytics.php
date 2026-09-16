<?php
/**
 * RAFly Agency OS — Real Performance Analytics Engine.
 *
 * Driven strictly by local database records and real conversion metrics.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('leads.view');
require_once __DIR__ . '/lib/chart.php';
require __DIR__ . '/lib/layout.php';

$rangeDays = (int)($_GET['range'] ?? 30);
if (!in_array($rangeDays, [7, 30, 90, 365], true)) {
    $rangeDays = 30;
}

// 1. Lead Volume Trends
$leadTrend = [];
for ($i = $rangeDays - 1; $i >= 0; $i--) {
    $leadTrend[date('Y-m-d', strtotime("-{$i} days"))] = 0;
}
if (db_available()) {
    try {
        foreach (all('SELECT created_at FROM leads WHERE created_at > ' . sql_now_minus_days($rangeDays)) as $row) {
            $day = date('Y-m-d', strtotime((string)$row['created_at']));
            if (isset($leadTrend[$day])) {
                $leadTrend[$day]++;
            }
        }
    } catch (\Throwable $e) {}
}
$leadSeries = [];
foreach ($leadTrend as $date => $count) {
    $leadSeries[] = ['date' => $date, 'count' => $count];
}

// 2. Lead Source Attribution
$sources = [];
if (db_available()) {
    try {
        foreach (all("SELECT source_page, count(*) AS n FROM leads WHERE source_page <> '' GROUP BY source_page ORDER BY count(*) DESC LIMIT 6") as $row) {
            $sources[] = ['label' => str_cut((string)$row['source_page'], 40), 'value' => (int)$row['n']];
        }
    } catch (\Throwable $e) {}
}

// 3. Service Enquiries Breakdown
$serviceEnquiries = [];
if (db_available()) {
    try {
        foreach (all("SELECT service_slug, count(*) AS n FROM leads WHERE service_slug <> '' GROUP BY service_slug ORDER BY count(*) DESC LIMIT 6") as $row) {
            $serviceEnquiries[] = ['label' => (string)$row['service_slug'], 'value' => (int)$row['n']];
        }
    } catch (\Throwable $e) {}
}

// 4. Overall Key Conversion Performance Metrics
$totalLeads  = db_available() ? (int)scalar('SELECT count(*) FROM leads') : 0;
$wonLeads    = db_available() ? (int)scalar("SELECT count(*) FROM leads WHERE status = 'won'") : 0;
$lostLeads   = db_available() ? (int)scalar("SELECT count(*) FROM leads WHERE status = 'lost'") : 0;
$winRate     = ($totalLeads > 0) ? round(($wonLeads / $totalLeads) * 100, 1) : 0;
$avgQualScore= db_available() ? (int)scalar("SELECT coalesce(avg(qualification_score), 0) FROM leads") : 0;

admin_head([
    'title'       => 'Performance Analytics',
    'heading'     => 'Analytics & Insights',
    'breadcrumbs' => [
        ['name' => 'Admin', 'url' => site_path('/admin/')],
        ['name' => 'Analytics', 'url' => '']
    ],
    'active'      => '/admin/analytics.php',
]);
?>

<div class="page-header">
    <div class="page-header-title">
        <h1>Agency Performance Analytics</h1>
        <p>Real database metrics driving inbound pipeline growth, service demand, and conversion rates.</p>
    </div>

    <div class="page-header-actions">
        <div class="btn-group">
            <a href="?range=7" class="btn btn-sm <?= $rangeDays === 7 ? 'btn-primary' : 'btn-secondary' ?>">7 Days</a>
            <a href="?range=30" class="btn btn-sm <?= $rangeDays === 30 ? 'btn-primary' : 'btn-secondary' ?>">30 Days</a>
            <a href="?range=90" class="btn btn-sm <?= $rangeDays === 90 ? 'btn-primary' : 'btn-secondary' ?>">90 Days</a>
            <a href="?range=365" class="btn btn-sm <?= $rangeDays === 365 ? 'btn-primary' : 'btn-secondary' ?>">1 Year</a>
        </div>
    </div>
</div>

<!-- METRIC SUMMARY CARDS -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Inbound Leads</span>
            <div class="stat-icon-wrapper"><?= icon('mail-open') ?></div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($totalLeads) ?></span>
            <span class="badge badge-blue">Lifetime</span>
        </div>
        <div class="stat-subtext">Recorded form submissions</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Won Client Deals</span>
            <div class="stat-icon-wrapper" style="background:var(--ok-soft); color:var(--ok)"><?= icon('circle-check') ?></div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($wonLeads) ?></span>
            <span class="badge badge-ok">Closed Won</span>
        </div>
        <div class="stat-subtext">Converted agency clients</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Pipeline Win Rate</span>
            <div class="stat-icon-wrapper" style="background:var(--purple-soft); color:var(--purple)"><?= icon('trending-up') ?></div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= $winRate ?>%</span>
            <span class="badge badge-purple">Conversion</span>
        </div>
        <div class="stat-subtext">Won vs Total Inquiries</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Avg Lead Score</span>
            <div class="stat-icon-wrapper" style="background:var(--warn-soft); color:var(--warn)"><?= icon('star') ?></div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= $avgQualScore ?> <span style="font-size:16px;">/ 100</span></span>
            <span class="badge badge-warn">SOP 10</span>
        </div>
        <div class="stat-subtext">Qualification Matrix Average</div>
    </div>
</div>

<!-- CHARTS GRID -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap:24px; margin-bottom:24px;">
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Inbound Lead Volume (Last <?= $rangeDays ?> Days)</h3>
        </div>
        <div class="card-body">
            <?= chart_area($leadSeries, 'No lead activity recorded in selected timeframe.') ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Enquiry Distribution by Service Category</h3>
        </div>
        <div class="card-body">
            <?= chart_bars($serviceEnquiries, 'No service breakdown data available.') ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Top Converting Landing & Source Pages</h3>
    </div>
    <div class="card-body">
        <?= chart_bars($sources, 'No source page attribution data recorded yet.') ?>
    </div>
</div>

<?php admin_foot(); ?>
