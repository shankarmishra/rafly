<?php
/**
 * RAFly Team OS — Universal Approval Engine
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$approvals = all('SELECT a.*, c.company_name, u.name as requester_name FROM approvals a LEFT JOIN clients c ON a.client_id = c.id LEFT JOIN users u ON a.requested_by = u.id ORDER BY a.created_at DESC');

admin_head(['title' => 'Universal Approvals — RAFly Team OS', 'active' => '/admin/approvals.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Universal <span class="soft">Approvals</span></h1>
            <p class="lead">Internal & Client Sign-off Tracking for Designs, SOWs, Deliverables & Invoices.</p>
        </div>
    </div>

    <table class="table" style="width:100%; margin-top:24px;">
        <thead>
            <tr>
                <th>Title / Scope</th>
                <th>Client</th>
                <th>Type</th>
                <th>Status</th>
                <th>Requested By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($approvals)): ?>
                <tr><td colspan="6" class="text-center soft">No pending approval requests.</td></tr>
            <?php else: ?>
                <?php foreach ($approvals as $ap): ?>
                    <tr>
                        <td><strong><?= e($ap['title']) ?></strong></td>
                        <td><?= e($ap['company_name'] ?? 'Internal') ?></td>
                        <td><span class="badge"><?= e($ap['object_type']) ?></span></td>
                        <td><span class="badge badge-<?= $ap['status'] === 'approved' ? 'success' : 'warn' ?>"><?= e(strtoupper($ap['status'])) ?></span></td>
                        <td><?= e($ap['requester_name'] ?? 'System') ?></td>
                        <td><?= e(date('M d, Y', strtotime($ap['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
admin_foot();
