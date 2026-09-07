<?php
/**
 * RAFly Team OS — Global Unified Search Engine
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$q = trim((string)($_GET['q'] ?? ''));

$clients  = [];
$projects = [];
$tasks    = [];
$leads    = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $clients  = all('SELECT * FROM clients WHERE company_name LIKE ? OR website_url LIKE ?', [$like, $like]);
    $projects = all('SELECT * FROM projects WHERE name LIKE ? OR code LIKE ?', [$like, $like]);
    $tasks    = all('SELECT * FROM tasks WHERE title LIKE ? OR description LIKE ?', [$like, $like]);
    $leads    = all('SELECT * FROM leads WHERE company_name LIKE ? OR contact_name LIKE ? OR contact_email LIKE ?', [$like, $like, $like]);
}

admin_head(['title' => 'Global Unified Search — RAFly Team OS', 'active' => '/admin/search.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Global <span class="soft">Search</span></h1>
            <p class="lead">Search across Clients, Projects, Tasks, Leads & Documentation.</p>
        </div>
    </div>

    <form method="get" action="search.php" style="margin-top:24px;">
        <div style="display:flex; gap:12px;">
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Type client name, project code, task title..." style="flex:1; padding:12px; font-size:16px; border-radius:6px; border:1px solid #CBD5E1;">
            <button type="submit" class="btn btn-primary">Search System</button>
        </div>
    </form>

    <?php if ($q !== ''): ?>
        <h3 style="margin-top:32px;">Search Results for "<?= e($q) ?>"</h3>
        
        <h4>Clients (<?= count($clients) ?>)</h4>
        <?php foreach ($clients as $c): ?>
            <p><a href="<?= e(site_path('/admin/clients.php?action=view&id=' . $c['id'])) ?>"><strong><?= e($c['company_name']) ?></strong></a> — <?= e($c['industry'] ?? 'N/A') ?></p>
        <?php endforeach; ?>

        <h4>Projects (<?= count($projects) ?>)</h4>
        <?php foreach ($projects as $p): ?>
            <p><a href="<?= e(site_path('/admin/projects.php?action=view&id=' . $p['id'])) ?>"><code><?= e($p['code']) ?></code> — <strong><?= e($p['name']) ?></strong></a></p>
        <?php endforeach; ?>

        <h4>Tasks (<?= count($tasks) ?>)</h4>
        <?php foreach ($tasks as $t): ?>
            <p><strong><?= e($t['title']) ?></strong> (Status: <?= e($t['status']) ?>)</p>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
admin_foot();
