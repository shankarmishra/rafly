<?php
/**
 * RAFly Team OS — Tasks Engine & My Work Workspace
 * Complete interactive Task Board with Kanban status movement & Task Creation.
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$user   = current_user();
$action = (string)($_GET['action'] ?? $_POST['action'] ?? 'list');
$taskId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// POST Handler: Task Creation / Status Update / Editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $actionType = (string)($_POST['action'] ?? '');

    if ($actionType === 'save_task') {
        $title       = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $projectId   = (int)($_POST['project_id'] ?? 0);
        $clientId    = (int)($_POST['client_id'] ?? 0);
        $priority    = trim((string)($_POST['priority'] ?? 'medium'));
        $status      = trim((string)($_POST['status'] ?? 'backlog'));
        $creatorId   = current_user_id();

        if ($title === '') {
            admin_redirect('/admin/tasks.php', 'Task title is required.', 'error');
        }

        if ($taskId > 0) {
            q('UPDATE tasks SET title = ?, description = ?, project_id = ?, client_id = ?, priority = ?, status = ? WHERE id = ?',
              [$title, $description, $projectId ?: null, $clientId ?: null, $priority, $status, $taskId]);
            admin_redirect('/admin/tasks.php', 'Task updated successfully.');
        } else {
            insert_returning_id('INSERT INTO tasks (title, description, project_id, client_id, priority, status, creator_id) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$title, $description, $projectId ?: null, $clientId ?: null, $priority, $status, $creatorId]);
            admin_redirect('/admin/tasks.php', 'New task created.');
        }
    }

    if ($actionType === 'update_status') {
        $newStatus = trim((string)($_POST['status'] ?? ''));
        if ($taskId > 0 && in_array($newStatus, ['backlog', 'in_progress', 'review', 'completed'], true)) {
            q('UPDATE tasks SET status = ? WHERE id = ?', [$newStatus, $taskId]);
            admin_redirect('/admin/tasks.php', 'Task status updated.');
        }
    }
}

// Fetch all tasks for Kanban Columns
$tasks = all('
    SELECT t.*, p.name as project_name, c.company_name, u.name as creator_name 
      FROM tasks t 
      LEFT JOIN projects p ON t.project_id = p.id 
      LEFT JOIN clients c ON t.client_id = c.id 
      LEFT JOIN users u ON t.creator_id = u.id 
     ORDER BY t.created_at DESC
');

$projects = all('SELECT id, name FROM projects ORDER BY name ASC');
$clients  = all('SELECT id, company_name FROM clients ORDER BY company_name ASC');

admin_head(['title' => 'Tasks & My Work — RAFly Team OS', 'active' => '/admin/tasks.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">My Work & <span class="soft">Task Board</span></h1>
            <p class="lead">Single source of truth for execution across all project pillars.</p>
        </div>
        <div>
            <button onclick="document.getElementById('task-modal').style.display='block'" class="btn btn-primary">+ Create New Task</button>
        </div>
    </div>

    <!-- Interactive Task Modal -->
    <div id="task-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px);">
        <div style="background:#FFF; max-width:560px; margin:60px auto; padding:28px; border-radius:12px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="margin:0; font-size:20px; font-weight:800;">Create Task</h3>
                <button type="button" onclick="document.getElementById('task-modal').style.display='none'" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
            </div>
            
            <form method="post" action="tasks.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_task">
                
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Task Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Implement SSL & Firewall Hardening" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Description</label>
                    <textarea name="description" placeholder="Details and technical scope..." style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1; min-height:80px;"></textarea>
                </div>

                <div class="form-grid" style="grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div>
                        <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Project</label>
                        <select name="project_id" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                            <option value="0">-- None / General --</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Priority</label>
                        <select name="priority" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                    <button type="button" onclick="document.getElementById('task-modal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kanban Board Grid -->
    <div class="grid grid-4" style="margin-top:24px; gap:16px;">
        <!-- Column 1: Backlog -->
        <div class="card" style="padding:16px; background:#F8FAFC;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0; font-size:14px; font-weight:700; text-transform:uppercase; color:#475569;">Backlog</h4>
                <span class="badge badge-muted"><?= count(array_filter($tasks, fn($t) => in_array($t['status'], ['backlog', 'ready']))) ?></span>
            </div>
            <?php foreach ($tasks as $t): if (in_array($t['status'], ['backlog', 'ready'])): ?>
                <div style="background:#FFF; padding:12px; border-radius:6px; margin-bottom:10px; border:1px solid #CBD5E1; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                    <strong style="display:block; color:#0F172A; font-size:14px;"><?= e($t['title']) ?></strong>
                    <p style="font-size:12px; margin:4px 0 8px;" class="soft"><?= e($t['project_name'] ?? 'General') ?> &middot; Priority: <b><?= e($t['priority']) ?></b></p>
                    <form method="post" action="tasks.php" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <select name="status" onchange="this.form.submit()" style="font-size:11px; padding:2px 4px; width:100%; border-radius:4px; border:1px solid #CBD5E1;">
                            <option value="backlog" selected>Status: Backlog</option>
                            <option value="in_progress">Move to: In Progress</option>
                            <option value="review">Move to: Review</option>
                            <option value="completed">Move to: Completed</option>
                        </select>
                    </form>
                </div>
            <?php endif; endforeach; ?>
        </div>

        <!-- Column 2: In Progress -->
        <div class="card" style="padding:16px; background:#EFF6FF;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0; font-size:14px; font-weight:700; text-transform:uppercase; color:#2563EB;">In Progress</h4>
                <span class="badge" style="background:#DBEAFE; color:#1D4ED8; font-weight:bold;"><?= count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')) ?></span>
            </div>
            <?php foreach ($tasks as $t): if ($t['status'] === 'in_progress'): ?>
                <div style="background:#FFF; padding:12px; border-radius:6px; margin-bottom:10px; border:1px solid #93C5FD; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                    <strong style="display:block; color:#0F172A; font-size:14px;"><?= e($t['title']) ?></strong>
                    <p style="font-size:12px; margin:4px 0 8px;" class="soft"><?= e($t['project_name'] ?? 'General') ?> &middot; Priority: <b><?= e($t['priority']) ?></b></p>
                    <form method="post" action="tasks.php" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <select name="status" onchange="this.form.submit()" style="font-size:11px; padding:2px 4px; width:100%; border-radius:4px; border:1px solid #CBD5E1;">
                            <option value="backlog">Move to: Backlog</option>
                            <option value="in_progress" selected>Status: In Progress</option>
                            <option value="review">Move to: Review</option>
                            <option value="completed">Move to: Completed</option>
                        </select>
                    </form>
                </div>
            <?php endif; endforeach; ?>
        </div>

        <!-- Column 3: Review -->
        <div class="card" style="padding:16px; background:#FEF3C7;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0; font-size:14px; font-weight:700; text-transform:uppercase; color:#D97706;">Review / QA</h4>
                <span class="badge" style="background:#FDE68A; color:#B45309; font-weight:bold;"><?= count(array_filter($tasks, fn($t) => in_array($t['status'], ['review', 'blocked']))) ?></span>
            </div>
            <?php foreach ($tasks as $t): if (in_array($t['status'], ['review', 'blocked'])): ?>
                <div style="background:#FFF; padding:12px; border-radius:6px; margin-bottom:10px; border:1px solid #FCD34D; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                    <strong style="display:block; color:#0F172A; font-size:14px;"><?= e($t['title']) ?></strong>
                    <p style="font-size:12px; margin:4px 0 8px;" class="soft"><?= e($t['project_name'] ?? 'General') ?> &middot; Priority: <b><?= e($t['priority']) ?></b></p>
                    <form method="post" action="tasks.php" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <select name="status" onchange="this.form.submit()" style="font-size:11px; padding:2px 4px; width:100%; border-radius:4px; border:1px solid #CBD5E1;">
                            <option value="backlog">Move to: Backlog</option>
                            <option value="in_progress">Move to: In Progress</option>
                            <option value="review" selected>Status: Review</option>
                            <option value="completed">Move to: Completed</option>
                        </select>
                    </form>
                </div>
            <?php endif; endforeach; ?>
        </div>

        <!-- Column 4: Completed -->
        <div class="card" style="padding:16px; background:#DCFCE7;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0; font-size:14px; font-weight:700; text-transform:uppercase; color:#166534;">Done / Approved</h4>
                <span class="badge" style="background:#BBF7D0; color:#15803D; font-weight:bold;"><?= count(array_filter($tasks, fn($t) => in_array($t['status'], ['approved', 'completed']))) ?></span>
            </div>
            <?php foreach ($tasks as $t): if (in_array($t['status'], ['approved', 'completed'])): ?>
                <div style="background:#FFF; padding:12px; border-radius:6px; margin-bottom:10px; border:1px solid #86EFAC; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                    <strong style="display:block; color:#0F172A; font-size:14px;"><?= e($t['title']) ?></strong>
                    <p style="font-size:12px; margin:4px 0 8px;" class="soft"><?= e($t['project_name'] ?? 'General') ?> &middot; Done</p>
                    <form method="post" action="tasks.php" style="margin:0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <select name="status" onchange="this.form.submit()" style="font-size:11px; padding:2px 4px; width:100%; border-radius:4px; border:1px solid #CBD5E1;">
                            <option value="backlog">Reopen: Move to Backlog</option>
                            <option value="in_progress">Move to: In Progress</option>
                            <option value="completed" selected>Status: Completed</option>
                        </select>
                    </form>
                </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>
<?php
admin_foot();
