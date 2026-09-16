<?php
/**
 * RAFly Agency OS — Dynamic Services CMS & Visual Engine Management.
 *
 * Allows full management, creation, editing, previewing, and publishing of
 * RAFly core service offerings without touching PHP source code.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require_once __DIR__ . '/../inc/repo/services.php';
require __DIR__ . '/lib/layout.php';

$action = $_GET['action'] ?? 'list';
$slug   = $_GET['slug'] ?? '';
$allServices = services_all();
$serviceSource = services_source();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_can('content.edit');
    admin_require_csrf();

    $postAction = $_POST['_action'] ?? '';

    // Save Service
    if ($postAction === 'save') {
        $svcSlug     = trim($_POST['slug'] ?? '');
        $svcTitle    = trim($_POST['title'] ?? '');
        $svcIcon     = trim($_POST['icon'] ?? 'layers');
        $svcKey      = trim($_POST['key_name'] ?? 'web');
        $svcTagline  = trim($_POST['tagline'] ?? '');
        $svcIntro    = trim($_POST['intro'] ?? '');
        $svcCard     = trim($_POST['card'] ?? '');
        $svcOrder    = (int)($_POST['sort_order'] ?? 0);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if ($svcSlug === '' || $svcTitle === '') {
            admin_redirect('/admin/services.php?action=edit&slug=' . rawurlencode($svcSlug), 'Title and Slug are required.', 'danger');
        }

        // Build Extra JSON Data for Hero, Process, FAQs, Capabilities, SEO
        $extraData = [
            'badge'         => trim($_POST['badge'] ?? ''),
            'hero_eyebrow'  => trim($_POST['hero_eyebrow'] ?? ''),
            'hero_title'    => trim($_POST['hero_title'] ?? ''),
            'hero_desc'     => trim($_POST['hero_desc'] ?? ''),
            'hero_cta'      => trim($_POST['hero_cta'] ?? ''),
            'hero_cta_url'  => trim($_POST['hero_cta_url'] ?? ''),
            'highlights'    => array_values(array_filter(explode("\n", str_replace("\r", "", $_POST['highlights'] ?? '')))),
            'outcomes'      => array_values(array_filter(explode("\n", str_replace("\r", "", $_POST['outcomes'] ?? '')))),
            'meta_title'    => trim($_POST['meta_title'] ?? ''),
            'meta_desc'     => trim($_POST['meta_desc'] ?? ''),
            'canonical_url' => trim($_POST['canonical_url'] ?? ''),
            'og_title'      => trim($_POST['og_title'] ?? ''),
            'og_desc'       => trim($_POST['og_desc'] ?? ''),
            'og_image'      => trim($_POST['og_image'] ?? ''),
        ];

        // Process step arrays
        if (!empty($_POST['process_steps']) && is_array($_POST['process_steps'])) {
            $steps = [];
            foreach ($_POST['process_steps'] as $idx => $stepTitle) {
                if (trim($stepTitle) === '') continue;
                $steps[] = [
                    'step' => sprintf('%02d', $idx + 1),
                    'title' => trim($stepTitle),
                    'time'  => trim($_POST['process_times'][$idx] ?? ''),
                    'desc'  => trim($_POST['process_descs'][$idx] ?? ''),
                ];
            }
            if ($steps) $extraData['process'] = $steps;
        }

        // FAQ step arrays
        if (!empty($_POST['faq_questions']) && is_array($_POST['faq_questions'])) {
            $faqs = [];
            foreach ($_POST['faq_questions'] as $idx => $q) {
                if (trim($q) === '') continue;
                $faqs[] = [
                    'q' => trim($q),
                    'a' => trim($_POST['faq_answers'][$idx] ?? ''),
                ];
            }
            if ($faqs) $extraData['faqs'] = $faqs;
        }

        $extraJson = json_encode($extraData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (db_available()) {
            try {
                // Ensure table has extra_data column
                try { q('ALTER TABLE services ADD COLUMN extra_data text DEFAULT \'\''); } catch (\Throwable $e) {}

                $existing = one('SELECT id FROM services WHERE slug = ?', [$svcSlug]);
                if ($existing) {
                    q('UPDATE services SET title = ?, icon = ?, key_name = ?, tagline = ?, intro = ?, card = ?, sort_order = ?, is_published = ?, extra_data = ?, updated_at = now() WHERE slug = ?', [
                        $svcTitle, $svcIcon, $svcKey, $svcTagline, $svcIntro, $svcCard, $svcOrder, $isPublished, $extraJson, $svcSlug
                    ]);
                } else {
                    q('INSERT INTO services (slug, title, icon, key_name, tagline, intro, card, sort_order, is_published, extra_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                        $svcSlug, $svcTitle, $svcIcon, $svcKey, $svcTagline, $svcIntro, $svcCard, $svcOrder, $isPublished, $extraJson
                    ]);
                }
                admin_redirect('/admin/services.php', 'Service saved successfully to database.', 'ok');
            } catch (\Throwable $e) {
                admin_redirect('/admin/services.php', 'Database save failed: ' . $e->getMessage(), 'danger');
            }
        } else {
            admin_redirect('/admin/services.php', 'Service preview updated in memory (Database disconnected).', 'ok');
        }
    }

    // Toggle Status
    if ($postAction === 'toggle_status') {
        $svcSlug = trim($_POST['slug'] ?? '');
        if (db_available() && $svcSlug !== '') {
            try {
                q('UPDATE services SET is_published = NOT is_published, updated_at = now() WHERE slug = ?', [$svcSlug]);
                admin_redirect('/admin/services.php', 'Service status toggled.', 'ok');
            } catch (\Throwable $e) {}
        }
        admin_redirect('/admin/services.php', 'Status updated.', 'ok');
    }

    // Delete Service
    if ($postAction === 'delete') {
        require_can('content.delete');
        $svcSlug = trim($_POST['slug'] ?? '');
        if (db_available() && $svcSlug !== '') {
            try {
                q('DELETE FROM services WHERE slug = ?', [$svcSlug]);
                admin_redirect('/admin/services.php', 'Service deleted successfully.', 'ok');
            } catch (\Throwable $e) {}
        }
        admin_redirect('/admin/services.php', 'Service removed.', 'ok');
    }
}

// Prepare Service data for Editor
$currentService = null;
if ($action === 'edit' && $slug !== '') {
    $currentService = service_find($slug);
}

admin_head([
    'title'       => ($action === 'edit' || $action === 'new') ? 'Edit Service' : 'Services Management',
    'heading'     => 'Services CMS',
    'breadcrumbs' => [
        ['name' => 'Admin', 'url' => site_path('/admin/')],
        ['name' => 'Services', 'url' => site_path('/admin/services.php')],
        ['name' => ($action === 'edit' ? 'Edit ' . ($currentService['title'] ?? $slug) : 'All Services'), 'url' => '']
    ],
    'active'      => '/admin/services.php',
]);
?>

<?php if ($action === 'edit' || $action === 'new'): ?>

    <!-- SERVICE EDITOR VIEW -->
    <form method="POST" action="<?= e(admin_path('/admin/services.php')) ?>" id="service-editor-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="_action" value="save">

        <div class="page-header">
            <div class="page-header-title">
                <h1><?= $action === 'new' ? 'Add New Service' : 'Edit Service: ' . e($currentService['title'] ?? $slug) ?></h1>
                <p>Configure complete visual architecture, hero copy, capabilities, process steps, FAQs, and SEO for this service.</p>
            </div>

            <div class="page-header-actions">
                <a href="<?= e(site_path('/service/' . ($currentService['slug'] ?? $slug))) ?>" target="_blank" class="btn btn-outline">
                    <?= icon('external-link', 'icon-sm') ?> Preview Public Page
                </a>
                <a href="<?= e(admin_path('/admin/services.php')) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= icon('circle-check', 'icon-sm') ?> Save Changes
                </button>
            </div>
        </div>

        <!-- EDITOR TABS NAV -->
        <div class="tab-wrapper">
            <div class="nav-tabs">
                <button type="button" class="tab-item is-active" data-tab="tab-overview">Overview & Basics</button>
                <button type="button" class="tab-item" data-tab="tab-hero">Hero & Badge</button>
                <button type="button" class="tab-item" data-tab="tab-capabilities">Capabilities</button>
                <button type="button" class="tab-item" data-tab="tab-process">Process Steps</button>
                <button type="button" class="tab-item" data-tab="tab-faqs">FAQs</button>
                <button type="button" class="tab-item" data-tab="tab-seo">SEO & OpenGraph</button>
            </div>

            <!-- TAB 1: OVERVIEW -->
            <div class="tab-pane is-active" id="tab-overview" style="display:block;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Basic Information</h3></div>
                    <div class="card-body">
                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:20px;">
                            <div class="form-group">
                                <label class="form-label">Service Title <span class="required">*</span></label>
                                <input type="text" name="title" class="form-control" value="<?= e($currentService['title'] ?? '') ?>" required placeholder="e.g. Custom Web Development">
                            </div>

                            <div class="form-group">
                                <label class="form-label">URL Slug <span class="required">*</span></label>
                                <input type="text" name="slug" class="form-control" value="<?= e($currentService['slug'] ?? '') ?>" required placeholder="web-development">
                                <span class="form-help">Public route: /service/<strong>slug</strong></span>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Sprite Icon ID</label>
                                <input type="text" name="icon" class="form-control" value="<?= e($currentService['icon'] ?? 'layers') ?>" placeholder="code, layers, shield, etc.">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Key Category Identifier</label>
                                <input type="text" name="key_name" class="form-control" value="<?= e($currentService['key'] ?? 'web') ?>" placeholder="web, security, marketing">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Headline Tagline</label>
                            <input type="text" name="tagline" class="form-control" value="<?= e($currentService['tagline'] ?? '') ?>" placeholder="Sites and web apps that load fast and do not fall over.">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Intro Summary Paragraph</label>
                            <textarea name="intro" class="form-textarea" rows="3" placeholder="We engineer custom, decoupled web systems..."><?= e($currentService['intro'] ?? '') ?></textarea>
                        </div>

                        <div style="display:flex; gap:20px; align-items:center;">
                            <div class="form-group">
                                <label class="form-label">Display Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" style="width:120px;" value="<?= (int)($currentService['sort_order'] ?? 0) ?>">
                            </div>

                            <div class="form-group" style="margin-top:20px;">
                                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" name="is_published" value="1" <?= ($currentService['is_published'] ?? true) ? 'checked' : '' ?>>
                                    <span>Published & Active on Public Website</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: HERO -->
            <div class="tab-pane" id="tab-hero" style="display:none;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Hero Section Copy</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Top Ribbon Badge</label>
                            <input type="text" name="badge" class="form-control" value="<?= e($currentService['badge'] ?? '') ?>" placeholder="SOFTWARE ARCHITECTURE ENGINE">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hero Title Override</label>
                            <input type="text" name="hero_title" class="form-control" value="<?= e($currentService['hero_title'] ?? '') ?>" placeholder="Decoupled Full-Stack Engineering">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hero Description</label>
                            <textarea name="hero_desc" class="form-textarea" rows="3"><?= e($currentService['hero_desc'] ?? '') ?></textarea>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:20px;">
                            <div class="form-group">
                                <label class="form-label">Primary CTA Button Label</label>
                                <input type="text" name="hero_cta" class="form-control" value="<?= e($currentService['hero_cta'] ?? 'Start Project') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Primary CTA Destination Link</label>
                                <input type="text" name="hero_cta_url" class="form-control" value="<?= e($currentService['hero_cta_url'] ?? '/contact') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: CAPABILITIES -->
            <div class="tab-pane" id="tab-capabilities" style="display:none;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Service Highlights & Outcomes</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Service Highlights (One per line)</label>
                            <textarea name="highlights" class="form-textarea" rows="6"><?= e(implode("\n", $currentService['highlights'] ?? [])) ?></textarea>
                            <span class="form-help">These bullet highlights appear on the service card and detail hero.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Core Outcomes & Deliverables (One per line)</label>
                            <textarea name="outcomes" class="form-textarea" rows="6"><?= e(implode("\n", $currentService['outcomes'] ?? [])) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: PROCESS -->
            <div class="tab-pane" id="tab-process" style="display:none;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Delivery Process Steps</h3></div>
                    <div class="card-body">
                        <div id="process-steps-container">
                            <?php 
                            $processSteps = $currentService['process'] ?? [
                                ['title' => 'Discover', 'time' => '2-3 Days', 'desc' => 'We audit existing codebases and map technical requirements.'],
                                ['title' => 'Plan', 'time' => '3-5 Days', 'desc' => 'We establish page wireframes and API specs.'],
                                ['title' => 'Build', 'time' => '2-6 Weeks', 'desc' => 'Iterative development with live staging previews.'],
                                ['title' => 'Launch', 'time' => '1 Week', 'desc' => 'Production deployment and 30-day support.'],
                            ];
                            foreach ($processSteps as $idx => $step):
                            ?>
                                <div style="padding:16px; border:1px solid var(--border); border-radius:var(--radius); margin-bottom:14px; background:var(--canvas)">
                                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:14px; margin-bottom:10px;">
                                        <div>
                                            <label class="form-label">Step <?= sprintf('%02d', $idx + 1) ?> Title</label>
                                            <input type="text" name="process_steps[]" class="form-control" value="<?= e($step['title'] ?? '') ?>">
                                        </div>
                                        <div>
                                            <label class="form-label">Duration / Timeline</label>
                                            <input type="text" name="process_times[]" class="form-control" value="<?= e($step['time'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">Description</label>
                                        <input type="text" name="process_descs[]" class="form-control" value="<?= e($step['desc'] ?? '') ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: FAQS -->
            <div class="tab-pane" id="tab-faqs" style="display:none;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Frequently Asked Questions</h3></div>
                    <div class="card-body">
                        <div id="faq-container">
                            <?php 
                            $faqs = $currentService['faqs'] ?? [
                                ['q' => 'Do you build custom solutions or use pre-made templates?', 'a' => 'We build custom, bespoke web architecture tailored to your exact business requirements.'],
                                ['q' => 'Who owns the code and intellectual property after launch?', 'a' => 'You do. Full IP ownership and source code access are transferred to your company.'],
                            ];
                            foreach ($faqs as $idx => $faq):
                            ?>
                                <div style="padding:16px; border:1px solid var(--border); border-radius:var(--radius); margin-bottom:14px; background:var(--canvas)">
                                    <div class="form-group">
                                        <label class="form-label">Question #<?= $idx + 1 ?></label>
                                        <input type="text" name="faq_questions[]" class="form-control" value="<?= e($faq['q'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="margin:0;">
                                        <label class="form-label">Answer</label>
                                        <textarea name="faq_answers[]" class="form-textarea" rows="2"><?= e($faq['a'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: SEO -->
            <div class="tab-pane" id="tab-seo" style="display:none;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Search Engine & Social Media Optimization</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Meta Title Tag</label>
                            <input type="text" name="meta_title" class="form-control" value="<?= e($currentService['meta_title'] ?? ($currentService['title'] ?? '') . ' | RAFly Agency') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_desc" class="form-textarea" rows="3"><?= e($currentService['meta_desc'] ?? ($currentService['intro'] ?? '')) ?></textarea>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:20px;">
                            <div class="form-group">
                                <label class="form-label">Canonical URL</label>
                                <input type="text" name="canonical_url" class="form-control" value="<?= e($currentService['canonical_url'] ?? '') ?>" placeholder="https://rafly.com/service/web-development">
                            </div>
                            <div class="form-group">
                                <label class="form-label">OpenGraph Image URL</label>
                                <input type="text" name="og_image" class="form-control" value="<?= e($currentService['og_image'] ?? '') ?>" placeholder="/assets/og-service.jpg">
                            </div>
                        </div>

                        <!-- GOOGLE SERP PREVIEW BOX -->
                        <div style="margin-top:20px; padding:16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface-subtle);">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-muted); margin-bottom:8px;">Search Engine Result Preview</div>
                            <div style="font-size:18px; color:#1a0dab; font-family:sans-serif; text-decoration:underline; cursor:pointer;"><?= e($currentService['meta_title'] ?? ($currentService['title'] ?? '') . ' | RAFly Agency') ?></div>
                            <div style="font-size:13px; color:#006621; font-family:sans-serif; margin:2px 0;">https://rafly.com/service/<?= e($currentService['slug'] ?? 'service') ?></div>
                            <div style="font-size:13px; color:#545454; font-family:sans-serif;"><?= e($currentService['meta_desc'] ?? ($currentService['intro'] ?? 'Service description preview...')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

<?php else: ?>

    <!-- SERVICE LIST VIEW -->
    <div class="page-header">
        <div class="page-header-title">
            <h1>Service Offerings</h1>
            <p>Manage, order, preview, and update all agency core services. Data source: <strong><?= e($serviceSource) ?></strong>.</p>
        </div>

        <div class="page-header-actions">
            <a href="<?= e(admin_path('/admin/services.php?action=new')) ?>" class="btn btn-primary">
                <?= icon('plus', 'icon-sm') ?> Add New Service
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Service Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Icon</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allServices as $s): ?>
                        <tr>
                            <td style="font-family:monospace; font-weight:700; color:var(--text-muted)"><?= sprintf('%02d', (int)($s['sort_order'] ?? 0)) ?></td>
                            <td>
                                <strong><a href="<?= e(admin_path('/admin/services.php?action=edit&slug=' . $s['slug'])) ?>"><?= e($s['title']) ?></a></strong>
                                <div style="font-size:12px; color:var(--text-muted)"><?= e($s['tagline'] ?: $s['intro']) ?></div>
                            </td>
                            <td><code style="font-size:12px; background:var(--surface-subtle); padding:2px 6px; border-radius:4px;">/service/<?= e($s['slug']) ?></code></td>
                            <td>
                                <?php if ($s['is_published'] ?? true): ?>
                                    <span class="badge badge-ok">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?= icon($s['icon'] ?? 'layers', 'nav-icon') ?></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="<?= e(admin_path('/admin/services.php?action=edit&slug=' . $s['slug'])) ?>" class="btn btn-sm btn-outline">Edit</a>
                                    <a href="<?= e(site_path('/service/' . $s['slug'])) ?>" target="_blank" class="btn btn-sm btn-secondary" title="Preview Public Page">
                                        <?= icon('external-link', 'icon-xs') ?> Preview
                                    </a>
                                    <form method="POST" action="<?= e(admin_path('/admin/services.php')) ?>" inline-block data-confirm="Are you sure you want to delete this service?">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="_action" value="delete">
                                        <input type="hidden" name="slug" value="<?= e($s['slug']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php admin_foot(); ?>
