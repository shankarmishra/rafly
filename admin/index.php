<?php
/**
 * RAFly Agency OS — Executive Command Dashboard.
 *
 * Provides real-time visibility into lead flow, active client projects, pending team tasks,
 * content publishing readiness, and system audit events.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('leads.view');
require_once __DIR__ . '/lib/chart.php';
require __DIR__ . '/lib/layout.php';

$rangeDays = (int)($_GET['range'] ?? 30);
if (!in_array($rangeDays, [1, 7, 30, 90], true)) {
    $rangeDays = 30;
}

// ---------------------------------------------------------------------------
// Real Metrics & Data Lookups
// ---------------------------------------------------------------------------
$leadsTotal      = db_available() ? (int)scalar('SELECT count(*) FROM leads') : 0;
$leadsNew        = db_available() ? (int)scalar("SELECT count(*) FROM leads WHERE status = 'new'") : 0;

$leadsCurrentPeriod = db_available() ? (int)scalar('SELECT count(*) FROM leads WHERE created_at > ' . sql_now_minus_days($rangeDays)) : 0;
$leadsPrevPeriod    = db_available() ? (int)scalar('SELECT count(*) FROM leads WHERE created_at > ' . sql_now_minus_days($rangeDays * 2) . ' AND created_at <= ' . sql_now_minus_days($rangeDays)) : 0;
$leadsDelta         = $leadsCurrentPeriod - $leadsPrevPeriod;
$leadsDeltaPct      = $leadsPrevPeriod > 0 ? (int)round(($leadsDelta / $leadsPrevPeriod) * 100) : null;

$activeProjectsCount = db_available() ? (int)scalar("SELECT count(*) FROM projects WHERE health_status <> 'completed'") : 0;
$activeClientsCount  = db_available() ? (int)scalar("SELECT count(*) FROM clients WHERE status = 'active'") : 0;
$pendingTasksCount   = db_available() ? (int)scalar("SELECT count(*) FROM tasks WHERE status NOT IN ('completed', 'done', 'approved')") : 0;
$totalEnquiries      = db_available() ? (int)scalar('SELECT count(*) FROM leads') : 0;

// Trend buckets for area chart
$buckets = [];
for ($i = $rangeDays - 1; $i >= 0; $i--) {
    $buckets[date('Y-m-d', strtotime("-{$i} days"))] = 0;
}
if (db_available()) {
    try {
        foreach (all('SELECT created_at FROM leads WHERE created_at > ' . sql_now_minus_days($rangeDays)) as $row) {
            $day = date('Y-m-d', strtotime((string)$row['created_at']));
            if (isset($buckets[$day])) {
                $buckets[$day]++;
            }
        }
    } catch (\Throwable $e) {}
}
$series = [];
foreach ($buckets as $date => $count) {
    $series[] = ['date' => $date, 'count' => $count];
}

// Pipeline Breakdown
$pipelineCounts = ['new' => 0, 'contacted' => 0, 'qualified' => 0, 'proposal' => 0, 'won' => 0, 'lost' => 0];
if (db_available()) {
    try {
        foreach (all('SELECT status, count(*) AS n FROM leads GROUP BY status') as $row) {
            $st = strtolower((string)$row['status']);
            if (isset($pipelineCounts[$st])) {
                $pipelineCounts[$st] = (int)$row['n'];
            }
        }
    } catch (\Throwable $e) {}
}
$pipelineTotal = array_sum($pipelineCounts) ?: 1;

// Recent Leads
$recentLeads = [];
if (db_available()) {
    try {
        $recentLeads = all('SELECT id, company_name, contact_number, service_slug, source_page, status, created_at FROM leads ORDER BY created_at DESC LIMIT 6');
    } catch (\Throwable $e) {}
}

// Active Projects
$activeProjects = [];
if (db_available()) {
    try {
        $activeProjects = all('SELECT p.id, p.name, p.code, p.health_status, p.pillar, p.target_date, c.company_name AS client_name FROM projects p LEFT JOIN clients c ON c.id = p.client_id ORDER BY p.id DESC LIMIT 5');
    } catch (\Throwable $e) {}
}

// Recent Activity Feed
$activity = [];
if (db_available()) {
    try {
        $activity = all('SELECT a.action, a.entity_type, a.entity_id, a.created_at, u.name AS user_name FROM audit_log a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC, a.id DESC LIMIT 6');
    } catch (\Throwable $e) {}
}

admin_head([
    'title'       => 'Dashboard',
    'heading'     => 'Dashboard',
    'breadcrumbs' => [
        ['name' => 'Admin', 'url' => site_path('/admin/')],
        ['name' => 'Overview', 'url' => '']
    ],
    'active'      => '/admin/',
]);
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-title">
        <h1>Good <?= date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= e(current_user()['name'] ?? 'Admin') ?></h1>
        <p>Here's what is happening across the RAFly Operating System.</p>
    </div>

    <div class="page-header-actions">
        <!-- Date Range Filter -->
        <div class="btn-group" role="group" aria-label="Date Range">
            <a href="?range=1" class="btn btn-sm <?= $rangeDays === 1 ? 'btn-primary' : 'btn-secondary' ?>">Today</a>
            <a href="?range=7" class="btn btn-sm <?= $rangeDays === 7 ? 'btn-primary' : 'btn-secondary' ?>">7 Days</a>
            <a href="?range=30" class="btn btn-sm <?= $rangeDays === 30 ? 'btn-primary' : 'btn-secondary' ?>">30 Days</a>
            <a href="?range=90" class="btn btn-sm <?= $rangeDays === 90 ? 'btn-primary' : 'btn-secondary' ?>">90 Days</a>
        </div>
    </div>
</div>

<!-- STAT CARDS (6 CARDS) -->
<div class="stat-grid">
    <!-- 1. Total Leads -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Leads</span>
            <div class="stat-icon-wrapper">
                <?= icon('mail-open') ?>
            </div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($leadsTotal) ?></span>
            <?php if ($leadsDeltaPct !== null): ?>
                <span class="stat-trend <?= $leadsDeltaPct >= 0 ? 'trend-up' : 'trend-down' ?>">
                    <?= $leadsDeltaPct >= 0 ? '↑' : '↓' ?> <?= abs($leadsDeltaPct) ?>%
                </span>
            <?php endif; ?>
        </div>
        <div class="stat-subtext">vs previous <?= $rangeDays ?> days</div>
    </div>

    <!-- 2. New Leads -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">New Unhandled</span>
            <div class="stat-icon-wrapper" style="background:var(--warn-soft); color:var(--warn)">
                <?= icon('clock') ?>
            </div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($leadsNew) ?></span>
            <span class="badge badge-warn">Action Needed</span>
        </div>
        <div class="stat-subtext">Requires team follow-up</div>
    </div>

    <!-- 3. Active Projects -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Projects</span>
            <div class="stat-icon-wrapper" style="background:var(--primary-soft); color:var(--primary)">
                <?= icon('rocket') ?>
            </div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($activeProjectsCount) ?></span>
            <a href="<?= e(admin_path('/admin/projects.php')) ?>" class="badge badge-blue">View All →</a>
        </div>
        <div class="stat-subtext">In-flight client deliverables</div>
    </div>

    <!-- 4. Active Clients -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Clients</span>
            <div class="stat-icon-wrapper" style="background:var(--purple-soft); color:var(--purple)">
                <?= icon('building') ?>
            </div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($activeClientsCount) ?></span>
            <a href="<?= e(admin_path('/admin/clients.php')) ?>" class="badge badge-purple">360 View →</a>
        </div>
        <div class="stat-subtext">Retainer & active accounts</div>
    </div>

    <!-- 5. Pending Tasks -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Pending Tasks</span>
            <div class="stat-icon-wrapper" style="background:var(--ok-soft); color:var(--ok)">
                <?= icon('check-square') ?>
            </div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($pendingTasksCount) ?></span>
            <a href="<?= e(admin_path('/admin/tasks.php')) ?>" class="badge badge-ok">Board →</a>
        </div>
        <div class="stat-subtext">Open work tickets</div>
    </div>

    <!-- 6. Enquiries -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Website Enquiries</span>
            <div class="stat-icon-wrapper" style="background:var(--surface-subtle); color:var(--text-muted)">
                <?= icon('message-square') ?>
            </div>
        </div>
        <div class="stat-body">
            <span class="stat-value"><?= number_format($totalEnquiries) ?></span>
            <span class="badge badge-muted">Form Submissions</span>
        </div>
        <div class="stat-subtext">Inbound conversion points</div>
    </div>
</div>

<!-- LEAD PIPELINE VISUALIZATION -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lead Conversion Pipeline</h3>
        <a href="<?= e(admin_path('/admin/leads.php')) ?>" class="btn btn-sm btn-outline">Manage CRM Leads →</a>
    </div>
    <div class="card-body">
        <div class="pipeline-container">
            <?php 
            $stages = [
                'new'       => ['label' => 'New', 'color' => 'var(--warn)'],
                'contacted' => ['label' => 'Contacted', 'color' => 'var(--primary)'],
                'qualified' => ['label' => 'Qualified', 'color' => 'var(--purple)'],
                'proposal'  => ['label' => 'Proposal', 'color' => 'var(--secondary-blue)'],
                'won'       => ['label' => 'Won', 'color' => 'var(--ok)'],
                'lost'      => ['label' => 'Lost', 'color' => 'var(--danger)'],
            ];
            foreach ($stages as $stageKey => $meta):
                $count = $pipelineCounts[$stageKey] ?? 0;
                $pct = round(($count / $pipelineTotal) * 100);
            ?>
                <a href="<?= e(admin_path('/admin/leads.php?status=' . $stageKey)) ?>" class="pipeline-step">
                    <div class="pipeline-step-header">
                        <span><?= e($meta['label']) ?></span>
                        <span class="badge badge-muted"><?= $pct ?>%</span>
                    </div>
                    <div class="pipeline-step-count" style="color: <?= $meta['color'] ?>"><?= number_format($count) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- TWO-COLUMN GRID: RECENT LEADS & ACTIVE PROJECTS -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap:24px; margin-bottom:24px;">
    
    <!-- RECENT LEADS TABLE -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Inbound Leads</h3>
            <a href="<?= e(admin_path('/admin/leads.php')) ?>" class="btn btn-sm btn-secondary">View All Leads</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Company / Name</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLeads)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted)">
                                No leads submitted yet. Inbound form submissions will appear here.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentLeads as $l): ?>
                            <tr>
                                <td>
                                    <strong><a href="<?= e(admin_path('/admin/leads.php?id=' . $l['id'])) ?>"><?= e($l['company_name'] ?: 'Enquiry #' . $l['id']) ?></a></strong>
                                    <div style="font-size:11.5px; color:var(--text-muted)"><?= e($l['contact_number']) ?></div>
                                </td>
                                <td><span class="badge badge-muted"><?= e($l['service_slug'] ?: 'General') ?></span></td>
                                <td>
                                    <?php 
                                    $st = strtolower($l['status']);
                                    $badgeClass = match($st) {
                                        'new' => 'badge-warn',
                                        'won' => 'badge-ok',
                                        'lost', 'spam' => 'badge-danger',
                                        default => 'badge-blue',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= e(ucfirst($st)) ?></span>
                                </td>
                                <td style="font-size:12px; color:var(--text-muted)"><?= e(human_ago((string)$l['created_at'])) ?></td>
                                <td>
                                    <a href="<?= e(admin_path('/admin/leads.php?id=' . $l['id'])) ?>" class="btn btn-sm btn-outline">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ACTIVE PROJECTS -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Active Projects</h3>
            <a href="<?= e(admin_path('/admin/projects.php')) ?>" class="btn btn-sm btn-secondary">Project Console</a>
        </div>
        <div class="card-body" style="padding: 12px 24px;">
            <?php if (empty($activeProjects)): ?>
                <div class="empty-state" style="border:none; padding:32px 0;">
                    <?= icon('rocket', 'empty-icon') ?>
                    <div class="empty-title">No active projects yet</div>
                    <div class="empty-desc">Create your first client project to track scope, progress, and team deadlines.</div>
                    <a href="<?= e(admin_path('/admin/projects.php?action=new')) ?>" class="btn btn-primary btn-sm">+ Create Project</a>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ($activeProjects as $p): ?>
                        <div style="padding:14px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface)">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <strong><a href="<?= e(admin_path('/admin/projects.php?id=' . $p['id'])) ?>"><?= e($p['name']) ?></a></strong>
                                <span class="badge badge-blue"><?= e(ucfirst($p['health_status'] ?: 'Active')) ?></span>
                            </div>
                            <div style="font-size:12px; color:var(--text-muted); display:flex; justify-content:space-between;">
                                <span>Client: <?= e($p['client_name'] ?: 'Internal') ?></span>
                                <span>Target: <?= e($p['target_date'] ?: 'TBD') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- TWO-COLUMN GRID: RECENT ACTIVITY & QUICK ACTIONS -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap:24px;">
    
    <!-- RECENT ACTIVITY TIMELINE -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity Log</h3>
            <a href="<?= e(admin_path('/admin/audit.php')) ?>" class="btn btn-sm btn-secondary">Full Audit Log</a>
        </div>
        <div class="card-body">
            <?php if (empty($activity)): ?>
                <p style="color:var(--text-muted); text-align:center; padding:20px 0;">No activity events recorded yet.</p>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ($activity as $a): ?>
                        <div style="display:flex; align-items:flex-start; gap:12px; font-size:13px;">
                            <div style="width:8px; height:8px; border-radius:50%; background:var(--primary); margin-top:6px; flex-shrink:0;"></div>
                            <div style="flex:1;">
                                <strong><?= e($a['user_name'] ?: 'System') ?></strong>
                                <span style="color:var(--text-muted); margin:0 4px;"><?= e($a['action']) ?></span>
                                <?php if ($a['entity_type']): ?>
                                    <span class="badge badge-muted"><?= e($a['entity_type']) ?> #<?= e($a['entity_id']) ?></span>
                                <?php endif; ?>
                            </div>
                            <span style="font-size:11.5px; color:var(--text-muted); white-space:nowrap;"><?= e(human_ago((string)$a['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Operational Actions</h3>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:12px;">
                <a href="<?= e(admin_path('/admin/leads.php?action=new')) ?>" class="btn btn-secondary" style="justify-content:flex-start; padding:14px;">
                    <?= icon('mail-open', 'nav-icon') ?>
                    <span>Create Lead</span>
                </a>
                <a href="<?= e(admin_path('/admin/clients.php?action=new')) ?>" class="btn btn-secondary" style="justify-content:flex-start; padding:14px;">
                    <?= icon('building', 'nav-icon') ?>
                    <span>Create Client</span>
                </a>
                <a href="<?= e(admin_path('/admin/projects.php?action=new')) ?>" class="btn btn-secondary" style="justify-content:flex-start; padding:14px;">
                    <?= icon('rocket', 'nav-icon') ?>
                    <span>Create Project</span>
                </a>
                <a href="<?= e(admin_path('/admin/tasks.php?action=new')) ?>" class="btn btn-secondary" style="justify-content:flex-start; padding:14px;">
                    <?= icon('check-square', 'nav-icon') ?>
                    <span>Create Task</span>
                </a>
                <a href="<?= e(admin_path('/admin/services.php')) ?>" class="btn btn-secondary" style="justify-content:flex-start; padding:14px;">
                    <?= icon('layers', 'nav-icon') ?>
                    <span>Manage Services</span>
                </a>
                <a href="<?= e(admin_path('/admin/posts.php')) ?>" class="btn btn-secondary" style="justify-content:flex-start; padding:14px;">
                    <?= icon('file-pen', 'nav-icon') ?>
                    <span>Manage Content</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php admin_foot(); ?>
