<?php
/**
 * RAFly Team OS — Creative Studio & Reel Engine
 * Supports 9-Stage Reel Pipeline, Chunked Asset Storage, and Timestamped Annotations (00:07, 00:14).
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$assetId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$action  = (string)($_GET['action'] ?? $_POST['action'] ?? 'list');
$stages  = ['idea', 'scripting', 'recording', 'editing', 'review', 'revision_requested', 'approved', 'scheduled', 'published'];

// POST Handling: Create Asset / Add Timestamped Annotation / Update Stage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $actionType = (string)($_POST['action'] ?? '');

    if ($actionType === 'create_asset') {
        $title       = trim((string)($_POST['title'] ?? ''));
        $projectId   = (int)($_POST['project_id'] ?? 0);
        $type        = trim((string)($_POST['type'] ?? 'reel'));
        $aspectRatio = trim((string)($_POST['aspect_ratio'] ?? '9:16'));
        $rawFileUrl  = trim((string)($_POST['raw_file_url'] ?? ''));

        if ($title === '') {
            admin_redirect('/admin/creative.php', 'Asset title is required.', 'error');
        }

        $clientId = 0;
        if ($projectId > 0) {
            $p = one('SELECT client_id FROM projects WHERE id = ?', [$projectId]);
            $clientId = (int)($p['client_id'] ?? 0);
        }

        $newId = insert_returning_id('INSERT INTO creative_assets (title, project_id, client_id, type, aspect_ratio, raw_file_url, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$title, $projectId ?: null, $clientId ?: null, $type, $aspectRatio, $rawFileUrl, 'idea']);
        
        admin_redirect('/admin/creative.php?id=' . $newId, 'Creative asset created.');
    }

    if ($actionType === 'add_comment' && $assetId > 0) {
        $comment   = trim((string)($_POST['comment'] ?? ''));
        $timestamp = (float)($_POST['timestamp_seconds'] ?? 0);
        $userId    = current_user_id();

        if ($comment !== '') {
            q('INSERT INTO asset_comments (asset_id, user_id, timestamp_seconds, comment) VALUES (?, ?, ?, ?)',
              [$assetId, $userId, $timestamp, $comment]);
        }
        admin_redirect('/admin/creative.php?id=' . $assetId, 'Annotation added.');
    }

    if ($actionType === 'update_stage' && $assetId > 0) {
        $newStage = trim((string)($_POST['status'] ?? ''));
        if (in_array($newStage, $stages, true)) {
            q('UPDATE creative_assets SET status = ? WHERE id = ?', [$newStage, $assetId]);
        }
        admin_redirect('/admin/creative.php?id=' . $assetId, 'Asset stage updated.');
    }
}

// Render Asset Detail View (Video Annotator)
if ($assetId > 0) {
    $asset = one('SELECT a.*, p.name as project_name, c.company_name FROM creative_assets a LEFT JOIN projects p ON a.project_id = p.id LEFT JOIN clients c ON a.client_id = c.id WHERE a.id = ?', [$assetId]);
    if (!$asset) {
        admin_redirect('/admin/creative.php', 'Asset not found.', 'error');
    }
    
    $comments = all('SELECT ac.*, u.name as user_name FROM asset_comments ac JOIN users u ON ac.user_id = u.id WHERE ac.asset_id = ? ORDER BY ac.timestamp_seconds ASC', [$assetId]);

    admin_head(['title' => 'Asset: ' . $asset['title'] . ' — Creative Studio', 'active' => '/admin/creative.php']);
    ?>
    <div class="container" style="max-width:900px;">
        <a class="btn btn-secondary btn-sm" href="<?= e(site_path('/admin/creative.php')) ?>">&larr; Back to Creative Studio</a>
        
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-top:16px;">
            <div>
                <span class="badge"><?= e(strtoupper($asset['aspect_ratio'])) ?> &middot; <?= e(strtoupper($asset['type'])) ?></span>
                <h1 class="display" style="margin-top:4px; font-size:28px;"><?= e($asset['title']) ?></h1>
                <p class="soft" style="margin:4px 0 0; font-size:14px;"><?= e($asset['company_name'] ?? 'RAFly Internal') ?> &middot; Project: <strong><?= e($asset['project_name'] ?? 'General') ?></strong></p>
            </div>
            
            <form method="post" action="creative.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_stage">
                <input type="hidden" name="id" value="<?= (int)$asset['id'] ?>">
                <select name="status" onchange="this.form.submit()" style="padding:8px 12px; border-radius:6px; border:1px solid #CBD5E1; font-weight:bold;">
                    <?php foreach ($stages as $stg): ?>
                        <option value="<?= e($stg) ?>" <?= $asset['status'] === $stg ? 'selected' : '' ?>>Stage: <?= e(strtoupper(str_replace('_', ' ', $stg))) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="grid grid-2" style="margin-top:24px; gap:20px;">
            <div class="card" style="padding:20px;">
                <h3 style="margin-0 0 12px;">Asset Vault & Preview</h3>
                <?php if (!empty($asset['raw_file_url'])): ?>
                    <div style="background:#0F172A; padding:40px 20px; text-align:center; border-radius:8px; color:#FFF; margin-bottom:12px;">
                        <p style="margin:0; font-weight:700; font-size:16px;">📹 Asset File Vault Link Active</p>
                        <a href="<?= e($asset['raw_file_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary" style="margin-top:12px; display:inline-block;">Open File Vault Link</a>
                    </div>
                <?php else: ?>
                    <div style="background:#F8FAFC; padding:40px 20px; text-align:center; border-radius:8px; border:1px dashed #CBD5E1; margin-bottom:12px;">
                        <p class="soft">No file URL uploaded yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card" style="padding:20px;">
                <h3 style="margin:0 0 12px;">Timestamped Video Annotations</h3>
                
                <form method="post" action="creative.php" style="margin-bottom:20px; background:#F8FAFC; padding:12px; border-radius:6px; border:1px solid #E2E8F0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add_comment">
                    <input type="hidden" name="id" value="<?= (int)$asset['id'] ?>">

                    <div style="margin-bottom:8px; display:flex; gap:8px;">
                        <input type="number" step="1" name="timestamp_seconds" placeholder="Sec (e.g. 7)" required style="width:100px; padding:6px; border-radius:4px; border:1px solid #CBD5E1;">
                        <input type="text" name="comment" placeholder="Annotation feedback at timestamp..." required style="flex:1; padding:6px; border-radius:4px; border:1px solid #CBD5E1;">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary" style="width:100%;">+ Add Timestamp Comment</button>
                </form>

                <div style="display:flex; flex-direction:column; gap:8px;">
                    <?php if (empty($comments)): ?>
                        <p class="soft" style="font-size:13px;">No timestamp comments logged yet.</p>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <?php
                            $mins = floor($c['timestamp_seconds'] / 60);
                            $secs = $c['timestamp_seconds'] % 60;
                            $tsFormatted = sprintf('%02d:%02d', $mins, $secs);
                            ?>
                            <div style="background:#FFF; padding:10px; border-radius:6px; border:1px solid #CBD5E1; font-size:13px;">
                                <div style="display:flex; justify-content:space-between; font-weight:bold; margin-bottom:4px;">
                                    <span style="color:#0284C7; background:#E0F2FE; padding:2px 6px; border-radius:4px;"><?= $tsFormatted ?></span>
                                    <span><?= e($c['user_name']) ?></span>
                                </div>
                                <p style="margin:0; color:#334155;"><?= e($c['comment']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    admin_foot();
    exit;
}

// List View
$reels = all('SELECT a.*, p.name as project_name, c.company_name FROM creative_assets a LEFT JOIN projects p ON a.project_id = p.id LEFT JOIN clients c ON a.client_id = c.id ORDER BY a.created_at DESC');
$projects = all('SELECT id, name FROM projects ORDER BY name ASC');

admin_head(['title' => 'Creative Studio — RAFly Team OS', 'active' => '/admin/creative.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Creative <span class="soft">Studio</span></h1>
            <p class="lead">9-Stage Reel Engine with Timestamped Video Annotations (00:07, 00:14).</p>
        </div>
        <div>
            <button onclick="document.getElementById('creative-modal').style.display='block'" class="btn btn-primary">+ New Reel / Creative Asset</button>
        </div>
    </div>

    <!-- Modal for New Creative Asset -->
    <div id="creative-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px);">
        <div style="background:#FFF; max-width:560px; margin:60px auto; padding:28px; border-radius:12px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="margin:0; font-size:20px; font-weight:800;">Create Creative Asset</h3>
                <button type="button" onclick="document.getElementById('creative-modal').style.display='none'" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
            </div>
            
            <form method="post" action="creative.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_asset">
                
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Asset Title *</label>
                    <input type="text" name="title" required placeholder="e.g. 15-Sec High Impact Security Reel" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                </div>

                <div class="form-grid" style="grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div>
                        <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Type</label>
                        <select name="type" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                            <option value="reel">Reel / Short Video</option>
                            <option value="ad_graphic">Ad Graphic Banner</option>
                            <option value="carousel">Instagram Carousel</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Aspect Ratio</label>
                        <select name="aspect_ratio" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                            <option value="9:16">9:16 (Vertical Reels/Shorts)</option>
                            <option value="1:1">1:1 (Square Feed Post)</option>
                            <option value="16:9">16:9 (Horizontal Banner)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Project</label>
                    <select name="project_id" style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                        <option value="0">-- None / General --</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px;">Raw File URL / Vault Link</label>
                    <input type="url" name="raw_file_url" placeholder="https://..." style="width:100%; padding:10px; border-radius:6px; border:1px solid #CBD5E1;">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                    <button type="button" onclick="document.getElementById('creative-modal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Asset</button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-3" style="margin-top:24px; gap:20px;">
        <?php if (empty($reels)): ?>
            <div class="card" style="padding:20px; grid-column: span 3; text-align:center;">
                <p class="soft">No creative assets in production yet. Click "+ New Reel / Creative Asset" to create one.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reels as $r): ?>
                <div class="card" style="padding:16px;">
                    <span class="badge"><?= e(strtoupper($r['aspect_ratio'] ?? '9:16')) ?></span>
                    <h3 style="margin:8px 0 4px;">
                        <a href="<?= e(site_path('/admin/creative.php?id=' . (int)$r['id'])) ?>" style="color:#0F172A; text-decoration:none;">
                            <?= e($r['title']) ?>
                        </a>
                    </h3>
                    <p class="soft" style="font-size:13px; margin:0;"><?= e($r['company_name'] ?? 'Internal RAFly') ?> | Project: <?= e($r['project_name'] ?? 'General') ?></p>
                    <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center;">
                        <span class="badge badge-info">Stage: <?= e(strtoupper(str_replace('_', ' ', $r['status']))) ?></span>
                        <a class="btn btn-sm btn-secondary" href="<?= e(site_path('/admin/creative.php?id=' . (int)$r['id'])) ?>">Annotations &rarr;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
admin_foot();
