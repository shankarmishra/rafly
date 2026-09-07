<?php
/**
 * RAFly Team OS — Client 360 Workspace
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('leads.view');
require __DIR__ . '/lib/layout.php';

$action   = (string)($_GET['action'] ?? $_POST['action'] ?? 'list');
$clientId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// POST Handler: Save Client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    csrf_verify();
    $companyName = trim((string)($_POST['company_name'] ?? ''));
    $industry    = trim((string)($_POST['industry'] ?? ''));
    $websiteUrl  = trim((string)($_POST['website_url'] ?? ''));
    $status      = trim((string)($_POST['status'] ?? 'active'));
    $managerId   = (int)($_POST['account_manager_id'] ?? 0);

    if ($companyName === '') {
        admin_redirect('/admin/clients.php?action=new', 'Company name is required.', 'error');
    }

    if ($clientId > 0) {
        q('UPDATE clients SET company_name = ?, industry = ?, website_url = ?, status = ?, account_manager_id = ? WHERE id = ?',
          [$companyName, $industry, $websiteUrl, $status, $managerId ?: null, $clientId]);
        admin_redirect('/admin/clients.php?action=view&id=' . $clientId, 'Client updated successfully.');
    } else {
        $newId = insert_returning_id('INSERT INTO clients (company_name, industry, website_url, status, account_manager_id) VALUES (?, ?, ?, ?, ?)',
                                      [$companyName, $industry, $websiteUrl, $status, $managerId ?: null]);
        admin_redirect('/admin/clients.php?action=view&id=' . $newId, 'Client created successfully.');
    }
}

// Render New / Edit Form
if ($action === 'new' || $action === 'edit') {
    $client = $clientId > 0 ? one('SELECT * FROM clients WHERE id = ?', [$clientId]) : null;
    $users  = all('SELECT id, name FROM users ORDER BY name ASC');
    
    admin_head(['title' => ($client ? 'Edit Client' : 'New Client') . ' — RAFly Team OS', 'active' => '/admin/clients.php']);
    ?>
    <div class="container" style="max-width: 600px;">
        <a class="btn btn-secondary btn-sm" href="<?= e(site_path('/admin/clients.php')) ?>">&larr; Back to Clients</a>
        <h1 class="display" style="margin-top:12px;"><?= $client ? 'Edit Client Account' : 'Add New Client Account' ?></h1>
        
        <form method="post" action="clients.php" style="margin-top:20px;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($client['id'] ?? 0) ?>">

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Company Name *</label>
                <input type="text" name="company_name" value="<?= e($client['company_name'] ?? '') ?>" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Industry</label>
                <input type="text" name="industry" value="<?= e($client['industry'] ?? '') ?>" placeholder="e.g. Healthcare, Real Estate, D2C Retail" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Website URL</label>
                <input type="url" name="website_url" value="<?= e($client['website_url'] ?? '') ?>" placeholder="https://example.com" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Account Status</label>
                <select name="status" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                    <option value="active" <?= ($client['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="onboarding" <?= ($client['status'] ?? '') === 'onboarding' ? 'selected' : '' ?>>Onboarding</option>
                    <option value="inactive" <?= ($client['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Assigned Account Manager</label>
                <select name="account_manager_id" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                    <option value="">-- Select Manager --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= (int)($client['account_manager_id'] ?? 0) === (int)$u['id'] ? 'selected' : '' ?>><?= e($u['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?= $client ? 'Update Client Account' : 'Create Client Account' ?></button>
        </form>
    </div>
    <?php
    admin_foot();
    exit;
}

// Render Client 360 View
if ($action === 'view' && $clientId > 0) {
    $client = one('SELECT c.*, u.name as manager_name FROM clients c LEFT JOIN users u ON c.account_manager_id = u.id WHERE c.id = ?', [$clientId]);
    if (!$client) {
        admin_redirect('/admin/clients.php', 'Client not found.', 'error');
    }
    
    $projects   = all('SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC', [$clientId]);
    $tasks      = all('SELECT t.*, u.name as creator_name FROM tasks t LEFT JOIN users u ON t.creator_id = u.id WHERE t.client_id = ? ORDER BY t.due_date ASC', [$clientId]);
    $deals      = all('SELECT * FROM crm_deals WHERE client_id = ? ORDER BY created_at DESC', [$clientId]);
    $approvals  = all('SELECT * FROM approvals WHERE client_id = ? ORDER BY created_at DESC', [$clientId]);
    
    admin_head(['title' => $client['company_name'] . ' — Client 360', 'active' => '/admin/clients.php']);
    ?>
    <div class="container">
        <div class="sec-head-split">
            <div>
                <a class="btn btn-secondary btn-sm" href="<?= e(site_path('/admin/clients.php')) ?>">&larr; Back to Clients</a>
                <h1 class="display" style="margin-top: 10px;"><?= e($client['company_name']) ?> <span class="badge badge-<?= $client['status'] === 'active' ? 'success' : 'warn' ?>"><?= e(strtoupper($client['status'])) ?></span></h1>
                <p class="lead">Industry: <?= e($client['industry'] ?? 'N/A') ?> | Website: <a href="<?= e($client['website_url'] ?? '#') ?>" target="_blank"><?= e($client['website_url'] ?? 'N/A') ?></a> | Manager: <?= e($client['manager_name'] ?? 'Unassigned') ?></p>
            </div>
            <div>
                <a class="btn btn-line" href="<?= e(site_path('/admin/clients.php?action=edit&id=' . $clientId)) ?>">Edit Client Profile</a>
                <a class="btn btn-primary" href="<?= e(site_path('/admin/projects.php?action=new&client_id=' . $clientId)) ?>">+ New Project</a>
            </div>
        </div>

        <div class="grid grid-3" style="margin-top: 24px; gap: 20px;">
            <div class="card" style="background:#0F172A; color:#FFF; padding:20px; border-radius:8px;">
                <h4 style="margin:0; opacity:0.8;">Active Projects</h4>
                <p style="font-size:28px; font-weight:bold; margin:8px 0 0;"><?= count($projects) ?></p>
            </div>
            <div class="card" style="background:#0F172A; color:#FFF; padding:20px; border-radius:8px;">
                <h4 style="margin:0; opacity:0.8;">Open Tasks</h4>
                <p style="font-size:28px; font-weight:bold; margin:8px 0 0;"><?= count($tasks) ?></p>
            </div>
            <div class="card" style="background:#0F172A; color:#FFF; padding:20px; border-radius:8px;">
                <h4 style="margin:0; opacity:0.8;">Pipeline Deals</h4>
                <p style="font-size:28px; font-weight:bold; margin:8px 0 0;"><?= count($deals) ?></p>
            </div>
        </div>

        <h3 style="margin-top: 32px;">Projects & Deliverable Milestones</h3>
        <?php if (empty($projects)): ?>
            <p class="soft">No active projects found for this client.</p>
        <?php else: ?>
            <table class="table" style="width:100%; margin-top:12px;">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Project Name</th>
                        <th>Pillar</th>
                        <th>Health</th>
                        <th>Contract Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><code><?= e($p['code']) ?></code></td>
                            <td><strong><?= e($p['name']) ?></strong></td>
                            <td><span class="badge"><?= e($p['pillar']) ?></span></td>
                            <td><span class="badge badge-<?= $p['health_status'] ?>"><?= e(strtoupper($p['health_status'])) ?></span></td>
                            <td>₹<?= number_format((float)$p['contract_value'], 2) ?></td>
                            <td><a class="btn btn-sm btn-line" href="<?= e(site_path('/admin/projects.php?action=view&id=' . $p['id'])) ?>">Open Project 360</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
    admin_foot();
    exit;
}

// Default List View
$clients = all('SELECT c.*, u.name as manager_name, (SELECT count(*) FROM projects WHERE client_id = c.id) as project_count FROM clients c LEFT JOIN users u ON c.account_manager_id = u.id ORDER BY c.created_at DESC');

admin_head(['title' => 'Client 360 Workspace — RAFly Team OS', 'active' => '/admin/clients.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Client 360 <span class="soft">Workspaces</span></h1>
            <p class="lead">One team. One source of truth. Complete client accounts, projects, tasks & payments.</p>
        </div>
        <div>
            <a class="btn btn-primary" href="<?= e(site_path('/admin/clients.php?action=new')) ?>">+ Add New Client</a>
        </div>
    </div>

    <table class="table" style="width:100%; margin-top:24px;">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Industry</th>
                <th>Status</th>
                <th>Account Manager</th>
                <th>Active Projects</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clients)): ?>
                <tr><td colspan="6" class="text-center soft">No clients registered yet. Click "+ Add New Client" to create one.</td></tr>
            <?php else: ?>
                <?php foreach ($clients as $c): ?>
                    <tr>
                        <td><strong><?= e($c['company_name']) ?></strong></td>
                        <td><?= e($c['industry'] ?? 'N/A') ?></td>
                        <td><span class="badge badge-<?= $c['status'] === 'active' ? 'success' : 'warn' ?>"><?= e(strtoupper($c['status'])) ?></span></td>
                        <td><?= e($c['manager_name'] ?? 'Unassigned') ?></td>
                        <td><span class="badge"><?= (int)$c['project_count'] ?> Projects</span></td>
                        <td><a class="btn btn-sm btn-primary" href="<?= e(site_path('/admin/clients.php?action=view&id=' . $c['id'])) ?>">Open Client 360</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
admin_foot();
