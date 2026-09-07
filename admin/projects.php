<?php
/**
 * RAFly Team OS — Project 360 Workspace
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$projectId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$action    = (string)($_GET['action'] ?? $_POST['action'] ?? 'list');

// POST Handler: Save Project (with Automatic 30/40/30 Milestones)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    csrf_verify();
    $clientId      = (int)($_POST['client_id'] ?? 0);
    $name          = trim((string)($_POST['name'] ?? ''));
    $code          = strtoupper(trim((string)($_POST['code'] ?? '')));
    $pillar        = trim((string)($_POST['pillar'] ?? 'BUILD'));
    $healthStatus  = trim((string)($_POST['health_status'] ?? 'green'));
    $contractValue = (float)($_POST['contract_value'] ?? 0.0);

    if ($clientId <= 0 || $name === '' || $code === '') {
        admin_redirect('/admin/projects.php?action=new', 'Client, project name, and code are required.', 'error');
    }

    if ($projectId > 0) {
        q('UPDATE projects SET name = ?, code = ?, pillar = ?, health_status = ?, contract_value = ? WHERE id = ?',
          [$name, $code, $pillar, $healthStatus, $contractValue, $projectId]);
        admin_redirect('/admin/projects.php?action=view&id=' . $projectId, 'Project updated successfully.');
    } else {
        $newId = insert_returning_id('INSERT INTO projects (client_id, name, code, pillar, health_status, contract_value) VALUES (?, ?, ?, ?, ?, ?)',
                                      [$clientId, $name, $code, $pillar, $healthStatus, $contractValue]);
        
        // Auto-generate 30/40/30 Payment Milestones
        $val30 = round($contractValue * 0.30, 2);
        $val40 = round($contractValue * 0.40, 2);

        q("INSERT INTO project_milestones (project_id, title, percentage, amount, status) VALUES 
            (?, '30% Kickoff Deposit', 30, ?, 'paid'),
            (?, '40% Staging Demo Milestone', 40, ?, 'pending'),
            (?, '30% Deployment & Handover', 30, ?, 'pending')",
          [$newId, $val30, $newId, $val40, $newId, $val30]);

        admin_redirect('/admin/projects.php?action=view&id=' . $newId, 'Project and 30/40/30 Milestones created successfully.');
    }
}

// Render New Project Form
if ($action === 'new') {
    $clients  = all('SELECT id, company_name FROM clients ORDER BY company_name ASC');
    $presetClientId = (int)($_GET['client_id'] ?? 0);

    admin_head(['title' => 'New Project — RAFly Team OS', 'active' => '/admin/projects.php']);
    ?>
    <div class="container" style="max-width: 600px;">
        <a class="btn btn-secondary btn-sm" href="<?= e(site_path('/admin/projects.php')) ?>">&larr; Back to Projects</a>
        <h1 class="display" style="margin-top:12px;">Create New Project Workspace</h1>

        <form method="post" action="projects.php" style="margin-top:20px;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Client Account *</label>
                <select name="client_id" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                    <option value="">-- Select Client --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $presetClientId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Project Name *</label>
                <input type="text" name="name" placeholder="e.g. Website Redesign & Security" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Project Code * (Short Identifier)</label>
                <input type="text" name="code" placeholder="e.g. PRJ-ACME-01" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Brand Pillar</label>
                <select name="pillar" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                    <option value="BUILD">01 BUILD (Web & E-Commerce)</option>
                    <option value="PROTECT">02 PROTECT (Security & Maintenance)</option>
                    <option value="GROW">03 GROW (Performance Ads & Automation)</option>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:bold; margin-bottom:4px;">Contract Value (₹)</label>
                <input type="number" step="0.01" name="contract_value" placeholder="35000" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
            </div>

            <button type="submit" class="btn btn-primary">Create Project & Generate 30/40/30 Milestones</button>
        </form>
    </div>
    <?php
    admin_foot();
    exit;
}

if ($action === 'view' && $projectId > 0) {
    $project = one('SELECT p.*, c.company_name FROM projects p JOIN clients c ON p.client_id = c.id WHERE p.id = ?', [$projectId]);
    if (!$project) {
        admin_redirect('/admin/projects.php', 'Project not found.', 'error');
    }
    
    $milestones = all('SELECT * FROM project_milestones WHERE project_id = ? ORDER BY id ASC', [$projectId]);
    $tasks      = all('SELECT t.*, u.name as creator_name FROM tasks t LEFT JOIN users u ON t.creator_id = u.id WHERE t.project_id = ? ORDER BY t.created_at DESC', [$projectId]);
    
    admin_head(['title' => $project['name'] . ' — Project 360', 'active' => '/admin/projects.php']);
    ?>
    <div class="container">
        <div class="sec-head-split">
            <div>
                <a class="btn btn-secondary btn-sm" href="<?= e(site_path('/admin/projects.php')) ?>">&larr; Back to Projects</a>
                <h1 class="display" style="margin-top: 10px;"><?= e($project['name']) ?> <code>(<?= e($project['code']) ?>)</code></h1>
                <p class="lead">Client: <strong><?= e($project['company_name']) ?></strong> | Pillar: <span class="badge"><?= e($project['pillar']) ?></span> | Health: <span class="badge badge-<?= $project['health_status'] ?>"><?= e(strtoupper($project['health_status'])) ?></span></p>
            </div>
            <div>
                <a class="btn btn-primary" href="<?= e(site_path('/admin/tasks.php?action=new&project_id=' . $projectId)) ?>">+ Add Task</a>
                <a class="btn btn-secondary" href="<?= e(site_path('/admin/creative.php?project_id=' . $projectId)) ?>">+ Add Creative Asset</a>
            </div>
        </div>

        <h3 style="margin-top: 32px;">30 / 40 / 30 Payment & Delivery Milestones</h3>
        <div class="grid grid-3" style="margin-top:16px; gap:16px;">
            <?php foreach ($milestones as $m): ?>
                <div class="card" style="padding:16px; border-left: 4px solid #2563EB;">
                    <span class="badge"><?= (int)$m['percentage'] ?>% Trigger</span>
                    <h4 style="margin:8px 0 4px;"><?= e($m['title']) ?></h4>
                    <p style="font-size:18px; font-weight:bold; color:#2563EB; margin:0;">₹<?= number_format((float)$m['amount'], 2) ?></p>
                    <p style="margin-top:8px;">Status: <span class="badge badge-<?= $m['status'] === 'approved' || $m['status'] === 'paid' ? 'success' : 'warn' ?>"><?= e(strtoupper($m['status'])) ?></span></p>
                </div>
            <?php endforeach; ?>
        </div>

        <h3 style="margin-top: 32px;">Project Tasks</h3>
        <table class="table" style="width:100%; margin-top:12px;">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr><td colspan="4" class="soft">No tasks assigned to this project yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($tasks as $t): ?>
                        <tr>
                            <td><strong><?= e($t['title']) ?></strong></td>
                            <td><span class="badge badge-<?= $t['priority'] === 'urgent' ? 'danger' : 'info' ?>"><?= e(strtoupper($t['priority'])) ?></span></td>
                            <td><code><?= e($t['status']) ?></code></td>
                            <td><?= e($t['due_date'] ? date('M d, Y', strtotime($t['due_date'])) : 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    admin_foot();
    exit;
}

$projects = all('SELECT p.*, c.company_name FROM projects p JOIN clients c ON p.client_id = c.id ORDER BY p.created_at DESC');

admin_head(['title' => 'Project 360 Workspaces — RAFly Team OS', 'active' => '/admin/projects.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Project 360 <span class="soft">Workspaces</span></h1>
            <p class="lead">Build · Protect · Grow projects with health monitoring and milestone triggers.</p>
        </div>
        <div>
            <a class="btn btn-primary" href="<?= e(site_path('/admin/projects.php?action=new')) ?>">+ Create New Project</a>
        </div>
    </div>

    <table class="table" style="width:100%; margin-top:24px;">
        <thead>
            <tr>
                <th>Code</th>
                <th>Project Name</th>
                <th>Client</th>
                <th>Pillar</th>
                <th>Health</th>
                <th>Contract Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr><td colspan="7" class="text-center soft">No active projects found. Click "+ Create New Project" to add one.</td></tr>
            <?php else: ?>
                <?php foreach ($projects as $p): ?>
                    <tr>
                        <td><code><?= e($p['code']) ?></code></td>
                        <td><strong><?= e($p['name']) ?></strong></td>
                        <td><?= e($p['company_name']) ?></td>
                        <td><span class="badge"><?= e($p['pillar']) ?></span></td>
                        <td><span class="badge badge-<?= $p['health_status'] ?>"><?= e(strtoupper($p['health_status'])) ?></span></td>
                        <td>₹<?= number_format((float)$p['contract_value'], 2) ?></td>
                        <td><a class="btn btn-sm btn-primary" href="<?= e(site_path('/admin/projects.php?action=view&id=' . $p['id'])) ?>">Open Project 360</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
admin_foot();
