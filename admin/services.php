<?php
/**
 * Service Management.
 *
 * Full CRUD for the five services the site offers. When the `services` table
 * has been migrated (inc/migrations/009_services.sql) and at least one row
 * inserted, inc/repo/services.php switches automatically from the seed file
 * to the database — no code change required in any template.
 *
 * Until then this page shows the current source (seed vs database) so an
 * admin never has to read the code to know which is active, and the "Import
 * from seed" action imports all five rows in one click.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';
$errors    = [];
$submitted = null;

/* -------------------------------------------------------------------------
   POST handler
   --------------------------------------------------------------------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    /* --- delete --------------------------------------------------------- */
    if ($action === 'delete') {
        require_can('content.delete');
        $before = one('SELECT * FROM services WHERE id = ?', [$id]);
        if ($before !== null) {
            q('DELETE FROM services WHERE id = ?', [$id]);
            audit('service.delete', 'service', $id, $before, null);
            admin_redirect('/admin/services.php', 'Service deleted.', 'warn');
        }
        admin_redirect('/admin/services.php', 'That service was already removed.', 'warn');
    }

    /* --- import from seed ----------------------------------------------- */
    if ($action === 'import_seed') {
        require_can('content.edit');
        $seed = require __DIR__ . '/../inc/data/services.php';
        $i = 0;
        foreach ($seed as $slug => $svc) {
            $existing = one('SELECT id FROM services WHERE slug = ?', [$slug]);
            if ($existing !== null) continue;
            insert_returning_id('services', [
                'slug'         => $slug,
                'title'        => (string)($svc['title'] ?? $slug),
                'icon'         => (string)($svc['icon']    ?? 'layers'),
                'key_name'     => (string)($svc['key']     ?? 'web'),
                'tagline'      => (string)($svc['tagline'] ?? ''),
                'intro'        => (string)($svc['intro']   ?? ''),
                'card'         => (string)($svc['card']    ?? ''),
                'sort_order'   => $i,
                'is_published' => true,
            ]);
            $i++;
        }
        audit('service.import_seed', 'service', 0, null, ['imported' => $i]);
        admin_redirect('/admin/services.php', "Imported $i service(s) from seed.", 'ok');
    }

    /* --- save ----------------------------------------------------------- */
    if ($action === 'save') {
        require_can('content.edit');

        $slug  = preg_replace('/[^a-z0-9\-]/', '', strtolower(trim((string)($_POST['slug'] ?? ''))));
        $title = str_cut(trim((string)($_POST['title'] ?? '')), 120);

        if ($slug === '') {
            $errors['slug'] = 'A URL slug is required (letters, numbers and hyphens only).';
        }
        if ($title === '') {
            $errors['title'] = 'A service title is required.';
        }

        // Slug uniqueness check (exclude current row on edit)
        if (!$errors) {
            $conflict = one(
                'SELECT id FROM services WHERE slug = ? AND id != ?',
                [$slug, $id]
            );
            if ($conflict !== null) {
                $errors['slug'] = 'That slug is already used by another service.';
            }
        }

        $fields = [
            'slug'         => $slug,
            'title'        => $title,
            'icon'         => str_cut(trim((string)($_POST['icon']    ?? '')), 60) ?: 'layers',
            'key_name'     => str_cut(trim((string)($_POST['key_name'] ?? '')), 60) ?: 'web',
            'tagline'      => str_cut(trim((string)($_POST['tagline'] ?? '')), 255),
            'intro'        => str_cut(trim((string)($_POST['intro']   ?? '')), 2000),
            'card'         => str_cut(trim((string)($_POST['card']    ?? '')), 255),
            'sort_order'   => max(0, min(999, (int)($_POST['sort_order'] ?? 0))),
            'is_published' => isset($_POST['is_published']),
            'updated_at'   => db_now(),
        ];

        if ($errors) {
            $submitted = $_POST;
            $isNew     = ($id === 0);
            $editId    = $id;
        } elseif ($id === 0) {
            $newId = insert_returning_id('services', $fields);
            audit('service.create', 'service', $newId, null, $fields);
            admin_redirect('/admin/services.php?id=' . $newId, 'Service created.', 'ok');
        } else {
            $before = one('SELECT * FROM services WHERE id = ?', [$id]);
            q('UPDATE services SET ' . implode(', ', array_map(fn($k) => "\"$k\" = ?", array_keys($fields)))
              . ' WHERE id = ?', [...array_values($fields), $id]);
            audit('service.update', 'service', $id, $before, $fields);
            admin_redirect('/admin/services.php?id=' . $id, 'Service saved.', 'ok');
        }
    }
}

/* -------------------------------------------------------------------------
   Data for GET / re-render after failed save
   --------------------------------------------------------------------- */

$dbAvailable  = db_available();
$serviceRows  = $dbAvailable ? all('SELECT * FROM services ORDER BY sort_order, id') : [];
$editRow      = ($editId > 0 && !$isNew) ? one('SELECT * FROM services WHERE id = ?', [$editId]) : null;
$tableExists  = false;
if ($dbAvailable) {
    try {
        q('SELECT 1 FROM services LIMIT 1');
        $tableExists = true;
    } catch (\Throwable $e) {
        $tableExists = false;
    }
}

// Values for the form — submitted > existing row > blank
function svc_val(string $key, ?array $submitted, ?array $row): string {
    if ($submitted !== null) return (string)($submitted[$key] ?? '');
    if ($row       !== null) return (string)($row[$key]       ?? '');
    return '';
}
function svc_bool(string $key, ?array $submitted, ?array $row, bool $default = true): bool {
    if ($submitted !== null) return isset($submitted[$key]);
    if ($row       !== null) return (bool)$row[$key];
    return $default;
}

$adminPage = [
    'title'   => 'Services',
    'active'  => '/admin/services.php',
    'heading' => 'Service Management',
    'intro'   => 'Manage the five services shown throughout the site. Changes here update the nav, homepage bento, and every service detail page.',
];

require __DIR__ . '/lib/layout.php';
admin_head($adminPage);
?>

<div class="admin-body">
<?php if (!$tableExists): ?>
    <div class="notice notice--warn" style="margin-bottom:1.5rem">
        <strong>Services table not yet created.</strong>
        Run <code>inc/migrations/009_services.sql</code> against your database first,
        then return here to import or add services.
        Currently serving from: <code>inc/data/services.php</code> (seed file).
    </div>
<?php elseif (!$isNew && $editId === 0): ?>

    <!-- ===== LIST VIEW ===== -->
    <div class="list-toolbar">
        <a href="?new=1" class="btn btn--sm">+ Add service</a>
        <?php if (empty($serviceRows) && can('content.edit')): ?>
        <form method="post" style="display:inline">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="import_seed">
            <button class="btn btn--sm btn--secondary"
                    onclick="return confirm('Import all 5 services from the seed file? Existing slugs will be skipped.')">
                Import from seed
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php if (empty($serviceRows)): ?>
    <div class="list-empty">
        <p>No services in the database yet.</p>
        <p>Either <a href="?new=1">add one manually</a> or use "Import from seed" to load all five at once.</p>
    </div>
    <?php else: ?>
    <table class="list-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($serviceRows as $row): ?>
            <tr>
                <td><?= e((string)$row['sort_order']) ?></td>
                <td><strong><?= e((string)$row['title']) ?></strong></td>
                <td><code>/<?= e((string)$row['slug']) ?></code></td>
                <td>
                    <?php if ($row['is_published']): ?>
                        <span class="badge badge--green">Live</span>
                    <?php else: ?>
                        <span class="badge badge--grey">Hidden</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?id=<?= (int)$row['id'] ?>" class="btn btn--xs">Edit</a>
                    <a href="/<?= e((string)$row['slug']) ?>" target="_blank" class="btn btn--xs btn--secondary">View</a>
                    <?php if (can('content.delete')): ?>
                    <form method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                        <button class="btn btn--xs btn--danger"
                                onclick="return confirm('Delete &quot;<?= e(addslashes((string)$row['title'])) ?>&quot;? This cannot be undone.')">
                            Delete
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

<?php else: ?>

    <!-- ===== FORM VIEW (new or edit) ===== -->
    <?php $formRow = $submitted ?? $editRow; ?>
    <div class="list-toolbar">
        <a href="/admin/services.php" class="btn btn--sm btn--secondary">← Back to list</a>
    </div>

    <form method="post" class="admin-form" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $isNew ? 0 : $editId ?>">

        <div class="form-row">
            <div class="field <?= isset($errors['title']) ? 'field--error' : '' ?>">
                <label for="title">Service Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" maxlength="120" required
                       value="<?= e(svc_val('title', $submitted, $editRow)) ?>">
                <?php if (isset($errors['title'])): ?>
                    <div class="field-error"><?= e($errors['title']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field <?= isset($errors['slug']) ? 'field--error' : '' ?>">
                <label for="slug">URL Slug <span class="required">*</span></label>
                <div class="field-prefix-wrap">
                    <span class="field-prefix">/</span>
                    <input type="text" id="slug" name="slug" maxlength="200" required
                           pattern="[a-z0-9\-]+" placeholder="web-development"
                           value="<?= e(svc_val('slug', $submitted, $editRow)) ?>">
                </div>
                <div class="field-hint">Lowercase letters, numbers and hyphens only. Must match the existing URL.</div>
                <?php if (isset($errors['slug'])): ?>
                    <div class="field-error"><?= e($errors['slug']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="field">
                <label for="icon">Icon Name</label>
                <input type="text" id="icon" name="icon" maxlength="60" placeholder="layers"
                       value="<?= e(svc_val('icon', $submitted, $editRow) ?: 'layers') ?>">
                <div class="field-hint">Symbol ID from vendor/icons/sprite.svg (without the "i-" prefix).</div>
            </div>

            <div class="field">
                <label for="key_name">CSS Key</label>
                <input type="text" id="key_name" name="key_name" maxlength="60" placeholder="web"
                       value="<?= e(svc_val('key_name', $submitted, $editRow) ?: 'web') ?>">
                <div class="field-hint">Short handle used by CSS accent classes (svc-web, svc-security…).</div>
            </div>

            <div class="field" style="max-width:100px">
                <label for="sort_order">Order</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="999"
                       value="<?= e(svc_val('sort_order', $submitted, $editRow) ?: '0') ?>">
            </div>
        </div>

        <div class="field">
            <label for="tagline">Tagline</label>
            <input type="text" id="tagline" name="tagline" maxlength="255"
                   placeholder="Sites and web apps that load fast and do not fall over as you grow."
                   value="<?= e(svc_val('tagline', $submitted, $editRow)) ?>">
            <div class="field-hint">One line shown as the sub-heading on the service detail page. Max 255 chars.</div>
        </div>

        <div class="field">
            <label for="card">Homepage Card Copy</label>
            <input type="text" id="card" name="card" maxlength="255"
                   placeholder="Responsive, performance-focused websites and web apps…"
                   value="<?= e(svc_val('card', $submitted, $editRow)) ?>">
            <div class="field-hint">Shorter than tagline — shown in the homepage bento grid. Max 255 chars.</div>
        </div>

        <div class="field">
            <label for="intro">Introduction Paragraph</label>
            <textarea id="intro" name="intro" rows="4" maxlength="2000"
                      placeholder="Opening paragraph for the service detail page…"><?= e(svc_val('intro', $submitted, $editRow)) ?></textarea>
            <div class="field-hint">The first paragraph on the service detail page. Plain text only. Max 2000 chars.</div>
        </div>

        <div class="field">
            <label class="checkbox-label">
                <input type="checkbox" name="is_published" value="1"
                       <?= svc_bool('is_published', $submitted, $editRow) ? 'checked' : '' ?>>
                Published (shown on site)
            </label>
            <div class="field-hint">Unpublished services are hidden from the nav and all public pages.</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save service</button>
            <a href="/admin/services.php" class="btn btn--secondary">Cancel</a>
        </div>
    </form>

<?php endif; ?>
</div>

<?php admin_foot(); ?>