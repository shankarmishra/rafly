<?php
/**
 * RAFly Agency OS — Central System Notification Center.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('leads.view');
require __DIR__ . '/lib/layout.php';

// Handle POST actions (mark all read, mark single read)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'mark_all_read') {
        if (db_available()) {
            try {
                // If notification table exists update, otherwise mark new leads reviewed
                q("UPDATE leads SET status = 'contacted' WHERE status = 'new'");
            } catch (\Throwable $e) {}
        }
        admin_redirect('/admin/notifications.php', 'All notifications marked as read.', 'ok');
    }
}

// Fetch System Notifications (Leads, Projects, Audit Events)
$notifications = [];
if (db_available()) {
    try {
        $leads = all("SELECT id, company_name, contact_number, status, created_at FROM leads ORDER BY created_at DESC LIMIT 15");
        foreach ($leads as $l) {
            $notifications[] = [
                'id'         => 'lead_' . $l['id'],
                'type'       => 'lead',
                'title'      => 'New Inbound Lead: ' . ($l['company_name'] ?: 'Enquiry #' . $l['id']),
                'desc'       => 'Contact: ' . $l['contact_number'],
                'time'       => $l['created_at'],
                'is_read'    => $l['status'] !== 'new',
                'action_url' => admin_path('/admin/leads.php?id=' . $l['id']),
                'icon'       => 'mail-open',
            ];
        }

        $auditEvents = all("SELECT id, action, entity_type, entity_id, created_at FROM audit_log ORDER BY created_at DESC LIMIT 10");
        foreach ($auditEvents as $a) {
            $notifications[] = [
                'id'         => 'audit_' . $a['id'],
                'type'       => 'system',
                'title'      => 'System Audit Event: ' . $a['action'],
                'desc'       => $a['entity_type'] ? ($a['entity_type'] . ' #' . $a['entity_id']) : 'System Operation',
                'time'       => $a['created_at'],
                'is_read'    => true,
                'action_url' => admin_path('/admin/audit.php'),
                'icon'       => 'history',
            ];
        }
    } catch (\Throwable $e) {}
}

// Sort by timestamp desc
usort($notifications, fn($a, $b) => strtotime((string)$b['time']) <=> strtotime((string)$a['time']));

admin_head([
    'title'       => 'Notification Center',
    'heading'     => 'Notifications',
    'breadcrumbs' => [
        ['name' => 'Admin', 'url' => site_path('/admin/')],
        ['name' => 'Notifications', 'url' => '']
    ],
    'active'      => '/admin/notifications.php',
]);
?>

<div class="page-header">
    <div class="page-header-title">
        <h1>Notification Center</h1>
        <p>Real-time system alerts, new inbound lead notifications, project milestones, and task updates.</p>
    </div>

    <div class="page-header-actions">
        <form method="POST" action="<?= e(admin_path('/admin/notifications.php')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="mark_all_read">
            <button type="submit" class="btn btn-secondary"><?= icon('circle-check', 'icon-sm') ?> Mark All as Read</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Notifications & System Alerts</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($notifications)): ?>
            <div class="empty-state" style="border:none;">
                <?= icon('bell', 'empty-icon') ?>
                <div class="empty-title">All caught up!</div>
                <div class="empty-desc">You have no unread notifications or pending system alerts at this moment.</div>
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column;">
                <?php foreach ($notifications as $n): ?>
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--border-subtle); background: <?= $n['is_read'] ? 'var(--surface)' : 'var(--primary-soft)' ?>;">
                        <div style="display:flex; align-items:flex-start; gap:16px;">
                            <div class="stat-icon-wrapper" style="<?= $n['is_read'] ? '' : 'background:var(--primary); color:#FFF;' ?>">
                                <?= icon($n['icon'], 'nav-icon') ?>
                            </div>
                            <div>
                                <div style="font-weight:600; color:var(--deep); font-size:14px;">
                                    <?= e($n['title']) ?>
                                    <?php if (!$n['is_read']): ?>
                                        <span class="badge badge-warn" style="margin-left:6px;">Unread</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:13px; color:var(--text-muted); margin-top:2px;"><?= e($n['desc']) ?></div>
                                <div style="font-size:11.5px; color:var(--text-light); margin-top:6px;"><?= e(human_ago((string)$n['time'])) ?></div>
                            </div>
                        </div>

                        <?php if (!empty($n['action_url'])): ?>
                            <a href="<?= e($n['action_url']) ?>" class="btn btn-sm btn-outline">View Details →</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php admin_foot(); ?>
