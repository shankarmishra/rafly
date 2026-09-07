<?php
/**
 * RAFly Team OS — Standalone Client Portal & Deliverable Approval Hub
 * Clean, isolated interface for clients to track project progress,
 * view 30/40/30 milestones, and approve deliverables.
 */

require_once __DIR__ . '/inc/bootstrap.php';

$projectCode = trim((string)($_GET['code'] ?? $_POST['code'] ?? ''));
$action      = (string)($_POST['action'] ?? '');
$message     = '';
$messageType = 'info';

// Handle Client Deliverable Approvals
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'approval_response') {
    csrf_verify();
    $approvalId = (int)($_POST['approval_id'] ?? 0);
    $status     = trim((string)($_POST['status'] ?? ''));
    $notes      = trim((string)($_POST['notes'] ?? ''));

    if ($approvalId > 0 && in_array($status, ['approved', 'revision_requested'], true)) {
        $approvedAt = ($status === 'approved') ? date('Y-m-d H:i:s') : null;
        q('UPDATE approvals SET status = ?, notes = ?, approved_at = ? WHERE id = ?',
          [$status, $notes, $approvedAt, $approvalId]);
        
        $message = ($status === 'approved') 
            ? 'Thank you! Deliverable has been approved successfully.' 
            : 'Revision request submitted. Our delivery team will review your feedback.';
        $messageType = ($status === 'approved') ? 'success' : 'warn';
    }
}

// Fetch project details if code is supplied
$project    = null;
$client     = null;
$milestones = [];
$approvals  = [];

if ($projectCode !== '') {
    $project = one('SELECT * FROM projects WHERE UPPER(code) = UPPER(?)', [$projectCode]);
    if ($project) {
        $client     = one('SELECT * FROM clients WHERE id = ?', [$project['client_id']]);
        $milestones = all('SELECT * FROM project_milestones WHERE project_id = ? ORDER BY id ASC', [$project['id']]);
        $approvals  = all('SELECT * FROM approvals WHERE project_id = ? ORDER BY created_at DESC', [$project['id']]);
    } else {
        $message = 'Project code not found. Please check your project access code.';
        $messageType = 'error';
    }
}

$crumbs = [
    ['name' => 'Home',          'url' => '/'],
    ['name' => 'Client Portal', 'url' => '/client-portal'],
];

$page = [
    'id'        => 'client-portal',
    'title'     => 'Client Portal & Deliverable Approvals — RAFly Digital',
    'desc'      => 'Client project portal for tracking project milestones, reviewing deliverables, and providing instant approval feedback.',
    'bodyClass' => 'page-client-portal',
    'styles'    => ['home'],
    'noindex'   => true,
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">
<div class="site-section" style="padding: 60px 0; background: #F8FAFC; min-height: 80vh;">
    <div class="container" style="max-width: 900px;">
        
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="badge" style="background:#E0F2FE; color:#0369A1; font-weight:700; padding:6px 16px; border-radius:20px; font-size:13px; text-transform:uppercase;">RAFly Client Experience</span>
            <h1 class="display" style="font-size: 32px; font-weight: 800; margin-top: 12px; color: #0F172A;">Client Project Portal</h1>
            <p style="color: #64748B; font-size: 16px; max-width: 600px; margin: 8px auto 0;">Track your project milestones, review 30/40/30 payment policy deliverables, and provide instant approval feedback.</p>
        </div>

        <?php if ($message !== ''): ?>
            <div style="padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; 
                background: <?= $messageType === 'success' ? '#DCFCE7' : ($messageType === 'warn' ? '#FEF9C3' : '#FEE2E2') ?>; 
                color: <?= $messageType === 'success' ? '#15803D' : ($messageType === 'warn' ? '#A16207' : '#B91C1C') ?>; border: 1px solid rgba(0,0,0,0.05);">
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <!-- Project Code Lookup Form -->
        <div style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 32px;">
            <form method="get" action="/client-portal" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 260px;">
                    <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px; color: #334155;">Enter Your Project Access Code</label>
                    <input type="text" name="code" value="<?= e($projectCode) ?>" placeholder="e.g. PRJ-ACME-01" required style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 15px;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 15px; font-weight: 700;">Access Project Portal</button>
            </form>
        </div>

        <?php if ($project): ?>
            <!-- Project Overview Header -->
            <div style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; padding: 4px 10px; border-radius: 4px; font-size: 12px;"><?= e($project['code']) ?></span>
                        <h2 style="font-size: 24px; font-weight: 800; color: #0F172A; margin-top: 6px;"><?= e($project['name']) ?></h2>
                        <p style="color: #64748B; margin-top: 4px; font-size: 14px;">Client Account: <strong><?= e($client['company_name'] ?? 'Client') ?></strong> &middot; Pillar: <strong>01 <?= e($project['pillar']) ?></strong></p>
                    </div>
                    <div>
                        <?php
                        $healthBg = match($project['health_status']) {
                            'green' => '#DCFCE7',
                            'yellow' => '#FEF9C3',
                            default => '#FEE2E2',
                        };
                        $healthColor = match($project['health_status']) {
                            'green' => '#15803D',
                            'yellow' => '#A16207',
                            default => '#B91C1C',
                        };
                        ?>
                        <span style="background: <?= $healthBg ?>; color: <?= $healthColor ?>; font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 13px; text-transform: uppercase;">
                            Status: <?= e($project['health_status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 30/40/30 Milestones Progress -->
            <div style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 16px;">30/40/30 Payment & Delivery Milestones</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                    <?php foreach ($milestones as $m): ?>
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; border: 1px solid #E2E8F0;">
                            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;"><?= e($m['percentage']) ?>% Milestone</div>
                            <div style="font-weight: 700; font-size: 16px; color: #0F172A; margin-top: 4px;"><?= e($m['title']) ?></div>
                            <div style="font-size: 14px; color: #0284C7; font-weight: 700; margin-top: 6px;">₹<?= number_format((float)$m['amount'], 2) ?></div>
                            <div style="margin-top: 10px;">
                                <span class="badge <?= $m['status'] === 'paid' ? 'badge-ok' : 'badge-warn' ?>" style="font-size: 11px; padding: 3px 8px;">
                                    <?= e(strtoupper($m['status'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Universal Deliverable Approvals -->
            <div style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 16px;">Project Deliverable Approvals</h3>
                
                <?php if (empty($approvals)): ?>
                    <p style="color: #94A3B8; font-size: 14px; text-align: center; padding: 24px; border: 1px dashed #CBD5E1; border-radius: 8px;">
                        No pending deliverable approvals for this project at this time.
                    </p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php foreach ($approvals as $app): ?>
                            <div style="border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px; background: #FAFAFA;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                                    <div>
                                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748B; background: #E2E8F0; padding: 2px 8px; border-radius: 4px;"><?= e($app['object_type']) ?></span>
                                        <h4 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-top: 4px;"><?= e($app['title']) ?></h4>
                                        <div style="font-size: 13px; color: #64748B; margin-top: 4px;"><?= nl2br(e($app['notes'] ?? '')) ?></div>
                                    </div>
                                    <div>
                                        <?php
                                        $statusClass = match($app['status']) {
                                            'approved' => 'badge-ok',
                                            'revision_requested' => 'badge-danger',
                                            default => 'badge-warn',
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>" style="font-size: 12px; padding: 4px 10px;">
                                            <?= e(strtoupper(str_replace('_', ' ', $app['status']))) ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if ($app['status'] === 'draft' || $app['status'] === 'pending'): ?>
                                    <form method="post" action="/client-portal" style="margin-top: 16px; border-top: 1px solid #E2E8F0; padding-top: 16px;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="approval_response">
                                        <input type="hidden" name="code" value="<?= e($projectCode) ?>">
                                        <input type="hidden" name="approval_id" value="<?= (int)$app['id'] ?>">

                                        <div style="margin-bottom: 12px;">
                                            <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 4px;">Approval Notes or Revision Request</label>
                                            <textarea name="notes" placeholder="e.g. Approved with no changes, or details of revision request..." style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #CBD5E1; font-size: 13px; min-height: 60px;"></textarea>
                                        </div>

                                        <div style="display: flex; gap: 8px;">
                                            <button type="submit" name="status" value="approved" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px; font-weight: 700; background: #16A34A; border-color: #16A34A;">Approve Deliverable</button>
                                            <button type="submit" name="status" value="revision_requested" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px; font-weight: 700; color: #DC2626; border-color: #FCA5A5;">Request Revision</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
