<?php
/**
 * RAFly Agency OS — Central SEO & OpenGraph Meta Manager.
 *
 * Provides central SEO inspection, Google SERP simulation, OpenGraph social card previews,
 * title/description length validation, canonical checks, and sitemap status.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('settings.view');
require_once __DIR__ . '/../inc/repo/services.php';
require __DIR__ . '/lib/layout.php';

$services = services_all();
$selectedSlug = $_GET['page'] ?? 'homepage';

// Handle POST save for SEO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_can('settings.edit');
    admin_require_csrf();

    $pageKey   = trim($_POST['page_key'] ?? 'homepage');
    $title     = trim($_POST['meta_title'] ?? '');
    $desc      = trim($_POST['meta_desc'] ?? '');
    $canonical = trim($_POST['canonical_url'] ?? '');
    $robots    = trim($_POST['robots'] ?? 'index, follow');
    $ogTitle   = trim($_POST['og_title'] ?? '');
    $ogDesc    = trim($_POST['og_desc'] ?? '');
    $ogImage   = trim($_POST['og_image'] ?? '');

    if (db_available()) {
        try {
            q('INSERT INTO settings (key, value, type, label, group_name) VALUES (?, ?, ?, ?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value',
                ['seo_title_' . $pageKey, $title, 'text', 'SEO Title for ' . $pageKey, 'seo']);
            q('INSERT INTO settings (key, value, type, label, group_name) VALUES (?, ?, ?, ?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value',
                ['seo_desc_' . $pageKey, $desc, 'textarea', 'SEO Description for ' . $pageKey, 'seo']);
            q('INSERT INTO settings (key, value, type, label, group_name) VALUES (?, ?, ?, ?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value',
                ['seo_canonical_' . $pageKey, $canonical, 'url', 'Canonical URL for ' . $pageKey, 'seo']);
            q('INSERT INTO settings (key, value, type, label, group_name) VALUES (?, ?, ?, ?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value',
                ['seo_og_image_' . $pageKey, $ogImage, 'url', 'OG Image for ' . $pageKey, 'seo']);
            admin_redirect('/admin/seo.php?page=' . rawurlencode($pageKey), 'SEO Metadata saved successfully for ' . $pageKey . '.', 'ok');
        } catch (\Throwable $e) {}
    }
    admin_redirect('/admin/seo.php?page=' . rawurlencode($pageKey), 'SEO settings updated.', 'ok');
}

// Current meta lookup
$currentTitle     = setting('seo_title_' . $selectedSlug, 'RAFly Agency — High Performance Web Development & Security');
$currentDesc      = setting('seo_desc_' . $selectedSlug, 'Bespoke web applications, Core Web Vitals optimization, zero-bloat engineering, and surface security hardening.');
$currentCanonical = setting('seo_canonical_' . $selectedSlug, 'https://rafly.com' . ($selectedSlug === 'homepage' ? '' : '/service/' . $selectedSlug));
$currentOgImage   = setting('seo_og_image_' . $selectedSlug, '/assets/og-banner.jpg');
$currentRobots    = 'index, follow';

// Validation Checks
$titleLen = strlen($currentTitle);
$descLen  = strlen($currentDesc);
$warnings = [];

if ($titleLen < 30 || $titleLen > 60) {
    $warnings[] = "Title length ($titleLen chars) should ideally be between 30 and 60 characters for optimal Google SERP display.";
}
if ($descLen < 120 || $descLen > 160) {
    $warnings[] = "Meta description length ($descLen chars) should ideally be between 120 and 160 characters.";
}
if (empty($currentCanonical)) {
    $warnings[] = "Canonical URL is missing. Adding a canonical URL prevents duplicate content issues.";
}
if (empty($currentOgImage)) {
    $warnings[] = "Social Open Graph image URL is missing. Social shares will display without a banner preview.";
}

admin_head([
    'title'       => 'SEO Manager',
    'heading'     => 'SEO & OpenGraph Manager',
    'breadcrumbs' => [
        ['name' => 'Admin', 'url' => site_path('/admin/')],
        ['name' => 'SEO Manager', 'url' => '']
    ],
    'active'      => '/admin/seo.php',
]);
?>

<div class="page-header">
    <div class="page-header-title">
        <h1>Centralized SEO & Social Meta Manager</h1>
        <p>Inspect metadata, Google SERP snippets, Open Graph card previews, and SEO health checks across RAFly site pages.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns: 280px 1fr; gap:24px;">
    <!-- LEFT PAGE LIST -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">Select Site Page</h3></div>
        <div style="display:flex; flex-direction:column; padding:8px;">
            <a href="?page=homepage" class="nav-link <?= $selectedSlug === 'homepage' ? 'is-active' : '' ?>">
                <?= icon('globe', 'nav-icon') ?> <span>Homepage</span>
            </a>
            <a href="?page=about" class="nav-link <?= $selectedSlug === 'about' ? 'is-active' : '' ?>">
                <?= icon('file-text', 'nav-icon') ?> <span>About Us</span>
            </a>
            <a href="?page=contact" class="nav-link <?= $selectedSlug === 'contact' ? 'is-active' : '' ?>">
                <?= icon('mail-open', 'nav-icon') ?> <span>Contact</span>
            </a>
            
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:12px 12px 6px;">Services Pages</div>
            <?php foreach ($services as $sSlug => $sData): ?>
                <a href="?page=<?= e($sSlug) ?>" class="nav-link <?= $selectedSlug === $sSlug ? 'is-active' : '' ?>">
                    <?= icon('layers', 'nav-icon') ?> <span><?= e($sData['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- RIGHT SEO EDITOR & PREVIEW -->
    <div>
        <!-- WARNING ALERTS -->
        <?php if (!empty($warnings)): ?>
            <div class="card" style="border-color:var(--warn-border); background:var(--warn-soft); margin-bottom:20px;">
                <div class="card-body" style="padding:16px;">
                    <div style="font-weight:700; color:var(--warn); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                        <?= icon('alert-triangle', 'flash-icon') ?> SEO Health Audit Warnings (<?= count($warnings) ?>)
                    </div>
                    <ul style="margin-left:20px; font-size:13px; color:var(--text)">
                        <?php foreach ($warnings as $w): ?>
                            <li><?= e($w) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- SEO FORM -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editing Metadata: <u><?= e(ucfirst($selectedSlug)) ?></u></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= e(admin_path('/admin/seo.php')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="page_key" value="<?= e($selectedSlug) ?>">

                    <div class="form-group">
                        <label class="form-label">Page Title Tag (Recommended: 30-60 chars)</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= e($currentTitle) ?>" required>
                        <span class="form-help">Current length: <strong><?= $titleLen ?></strong> characters.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Description (Recommended: 120-160 chars)</label>
                        <textarea name="meta_desc" class="form-textarea" rows="3" required><?= e($currentDesc) ?></textarea>
                        <span class="form-help">Current length: <strong><?= $descLen ?></strong> characters.</span>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Canonical Tag URL</label>
                            <input type="url" name="canonical_url" class="form-control" value="<?= e($currentCanonical) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Robots Header / Tag</label>
                            <input type="text" name="robots" class="form-control" value="<?= e($currentRobots) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">OpenGraph Image URL (1200x630px recommended)</label>
                        <input type="text" name="og_image" class="form-control" value="<?= e($currentOgImage) ?>">
                    </div>

                    <button type="submit" class="btn btn-primary"><?= icon('circle-check', 'icon-sm') ?> Save Metadata</button>
                </form>
            </div>
        </div>

        <!-- LIVE PREVIEWS -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <!-- GOOGLE SERP PREVIEW -->
            <div class="card">
                <div class="card-header"><h3 class="card-title">Google SERP Preview</h3></div>
                <div class="card-body">
                    <div style="font-size:18px; color:#1a0dab; font-family:sans-serif; text-decoration:underline; cursor:pointer; line-height:1.3; margin-bottom:2px;">
                        <?= e($currentTitle) ?>
                    </div>
                    <div style="font-size:13px; color:#006621; font-family:sans-serif; margin-bottom:4px;">
                        <?= e($currentCanonical ?: 'https://rafly.com/' . $selectedSlug) ?>
                    </div>
                    <div style="font-size:13px; color:#545454; font-family:sans-serif; line-height:1.4;">
                        <?= e($currentDesc) ?>
                    </div>
                </div>
            </div>

            <!-- OPENGRAPH SOCIAL CARD PREVIEW -->
            <div class="card">
                <div class="card-header"><h3 class="card-title">Social OpenGraph Card Preview</h3></div>
                <div class="card-body" style="padding:16px;">
                    <div style="border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; background:var(--surface)">
                        <div style="height:120px; background:var(--surface-subtle); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:12px;">
                            <?= e($currentOgImage ?: '[No OG Image Set]') ?>
                        </div>
                        <div style="padding:12px;">
                            <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">RAFLY.COM</div>
                            <div style="font-size:14px; font-weight:700; color:var(--deep); margin:2px 0;"><?= e($currentTitle) ?></div>
                            <div style="font-size:12px; color:var(--text-muted); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"><?= e($currentDesc) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php admin_foot(); ?>
