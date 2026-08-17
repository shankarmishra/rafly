<?php
/**
 * Bundle packages — the pricing grid on the homepage and the header mega-menu.
 *
 * Points are an ordered list edited as a repeater and REPLACED WHOLESALE on
 * save, inside a transaction. Reconciling an edited list item-by-item is far
 * more machinery than a delete-and-reinsert of four rows, and the same pattern
 * is already used by inc/tools/seed.php.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');

/** Repeater slots rendered on the form. */
const BUNDLE_POINT_SLOTS = 8;

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

/**
 * Set when a save is rejected by validation.
 *
 * A redirect on failure discards the request body — and a bundle carries up to
 * eight repeater points, so a single missing name would throw away every line
 * the editor typed. On failure the form is re-rendered in place from what was
 * submitted, points included, with the errors attached to their fields.
 */
$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        require_can('content.delete');
        $before = one('SELECT * FROM bundles WHERE id = ?', [$id]);
        if ($before !== null) {
            // bundle_points cascade on the FK, so they go with it.
            q('DELETE FROM bundles WHERE id = ?', [$id]);
            audit('bundle.delete', 'bundle', $id, $before, null);
            admin_redirect('/admin/bundles.php', 'Package deleted.', 'warn');
        }
        admin_redirect('/admin/bundles.php', 'That package was already removed.', 'warn');
    }

    if ($action === 'save') {
        require_can('content.edit');

        $name = str_cut(trim((string)($_POST['name'] ?? '')), 60);
        if ($name === '') {
            $errors['name'] = 'A package name is required.';
        }

        $fields = [
            'name'         => $name,
            'sub'          => str_cut(trim((string)($_POST['sub'] ?? '')), 160),
            'price_text'   => str_cut(trim((string)($_POST['price_text'] ?? '')), 60) ?: 'Chat for Pricing',
            'is_featured'  => isset($_POST['is_featured']),
            'is_published' => isset($_POST['is_published']),
            'sort_order'   => max(0, min(999, (int)($_POST['sort_order'] ?? 0))),
        ];

        // Collected before the transaction so a malformed list fails before any
        // write happens.
        $points = [];
        foreach ((array)($_POST['points'] ?? []) as $label) {
            $label = str_cut(trim((string)$label), 160);
            if ($label !== '') {
                $points[] = $label;
            }
        }

        // Rejected: re-render the form from what was submitted rather than
        // redirecting, so the name AND every typed point survive. $editId/$isNew
        // are reset because the request here was a POST to bundles.php with no
        // query string, and the render path below chooses the form from those.
        if ($errors) {
            $submitted           = $fields;
            $submitted['points'] = $points;
            $editId              = $id;
            $isNew               = $id === 0;
        } else {
            $before = $id > 0 ? one('SELECT * FROM bundles WHERE id = ?', [$id]) : null;
            if ($id > 0 && $before === null) {
                admin_redirect('/admin/bundles.php', 'That package no longer exists.', 'error');
            }

            $savedId = tx(static function () use ($id, $fields, $points): int {
                if ($id > 0) {
                    q('
                        UPDATE bundles
                           SET name = ?, sub = ?, price_text = ?, is_featured = ?,
                               is_published = ?, sort_order = ?, updated_at = now()
                         WHERE id = ?
                    ', [...array_values($fields), $id]);
                    $bundleId = $id;
                } else {
                    $bundleId = insert_returning_id('
                        INSERT INTO bundles (name, sub, price_text, is_featured, is_published, sort_order)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ', array_values($fields));
                }

                // "Only one package is featured" is enforced here rather than by a
                // constraint: two featured cards would render two "Most Popular"
                // tags, which is worse than merely untidy.
                if ($fields['is_featured']) {
                    q('UPDATE bundles SET is_featured = false WHERE id <> ?', [$bundleId]);
                }

                q('DELETE FROM bundle_points WHERE bundle_id = ?', [$bundleId]);
                foreach ($points as $i => $label) {
                    q('INSERT INTO bundle_points (bundle_id, label, sort_order) VALUES (?, ?, ?)',
                      [$bundleId, $label, $i]);
                }

                return $bundleId;
            });

            audit($id > 0 ? 'bundle.update' : 'bundle.create', 'bundle', $savedId,
                  $before, one('SELECT * FROM bundles WHERE id = ?', [$savedId]));
            admin_redirect('/admin/bundles.php?id=' . $savedId, 'Package saved.');
        }
    }

    // Only reached on an unrecognised action, or a rejected save — which falls
    // through to the form below rather than bouncing the editor off their work.
    if (!$errors) {
        admin_redirect('/admin/bundles.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

if ($editId > 0 || $isNew) {
    $b = $editId > 0 ? one('SELECT * FROM bundles WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $b === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Package not found', 'active' => '/admin/bundles.php']);
        echo '<div class="card"><p>That package does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/bundles.php')) . '">Back</a></p></div>';
        admin_foot();
        exit;
    }

    $points = $editId > 0
        ? array_column(all('SELECT label FROM bundle_points WHERE bundle_id = ? ORDER BY sort_order', [$editId]), 'label')
        : [];

    // What was just typed wins over what is stored. On a normal GET $submitted
    // is null and this reads the row exactly as before.
    $v = static fn(string $k, string $d = ''): string
        => (string)($submitted[$k] ?? $b[$k] ?? $d);

    /** Renders the message attached to one field, if there is one. */
    $err = static function (string $k) use ($errors): string {
        return isset($errors[$k])
            ? '<p class="field-error">' . e($errors[$k]) . '</p>'
            : '';
    };

    /** Appended to a control's class so the field itself is marked, not just labelled. */
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    // Repeater rows come back from the submitted list on a rejected save, else
    // from the stored points loaded above.
    $pointVals = $submitted['points'] ?? $points ?? [];

    admin_head([
        'title'   => $b ? 'Edit package' : 'New package',
        'heading' => $b ? $v('name') . ' Bundle' : 'New package',
        'intro'   => 'Appears in the pricing grid and the Packages menu.',
        'active'  => '/admin/bundles.php',
    ]);
?>

<div class="card">
    <h2>Package</h2>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This package was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="bundles.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="form-grid">
            <div class="field">
                <label for="name">Name</label>
                <p class="hint">Bare label — templates append the word "Bundle".</p>
                <input type="text" id="name" name="name" maxlength="60" required value="<?= e($v('name')) ?>" placeholder="Starter"<?= $bad('name') ?>>
                <?= $err('name') ?>
            </div>

            <div class="field">
                <label for="price_text">Price</label>
                <p class="hint">Free text. "Chat for Pricing", or something like "from ₹25,000".</p>
                <input type="text" id="price_text" name="price_text" maxlength="60" value="<?= e($v('price_text', 'Chat for Pricing')) ?>">
            </div>

            <div class="field span-2">
                <label for="sub">Subtitle</label>
                <input type="text" id="sub" name="sub" maxlength="160" value="<?= e($v('sub')) ?>" placeholder="For businesses getting online">
            </div>

            <div class="field">
                <label for="sort_order">Sort order</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="999" value="<?= (int)($submitted['sort_order'] ?? $b['sort_order'] ?? 0) ?>">
            </div>
        </div>

        <div class="field">
            <label>What's included</label>
            <p class="hint">Listed in this order. Leave a row blank to drop it.</p>
<?php for ($i = 0; $i < BUNDLE_POINT_SLOTS; $i++): ?>
            <div class="repeater-row">
                <span class="handle"><?= $i + 1 ?></span>
                <input type="text" name="points[]" maxlength="160" value="<?= e($pointVals[$i] ?? '') ?>">
            </div>
<?php endfor; ?>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= ($submitted['is_featured'] ?? ($b !== null && $b['is_featured'])) ? 'checked' : '' ?>>
            <label for="is_featured">
                Highlight as "Most Popular"
                <span class="hint">Only one package can be featured — ticking this un-features the others.</span>
            </label>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_published" name="is_published" value="1" <?= ($submitted['is_published'] ?? ($b === null || $b['is_published'])) ? 'checked' : '' ?>>
            <label for="is_published">
                Show on the site
                <span class="hint">Untick to hide without deleting.</span>
            </label>
        </div>

        <div class="form-actions">
<?php if (can('content.edit')): ?>
            <button type="submit" class="btn">Save</button>
<?php endif; ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/bundles.php')) ?>">Back to all</a>
        </div>
    </form>
</div>

<?php if ($b && can('content.delete')): ?>
<div class="card">
    <h2>Delete</h2>
    <p class="hint">Removes the package and everything listed in it.</p>
    <form method="post" action="bundles.php" data-confirm="Delete this package and all its points?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
        <button type="submit" class="btn btn-danger">Delete package</button>
    </form>
</div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'     => '/admin/bundles.php',
    'sorts'    => [
        'order' => 'b.sort_order',
        'name'  => 'b.name',
        'price' => 'b.price_text',
        'state' => 'b.is_published',
    ],
    'default'     => 'order',
    'default_dir' => 'asc',
    'tiebreak'    => 'b.id ASC',
    'search'      => ['b.name', 'b.sub', 'b.price_text'],
    'per_page'    => 25,
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM bundles b' . $whereSql, $st['params']);

// GROUP BY lists every selected column rather than relying on the primary key,
// for the same reason users.php does: MySQL's ONLY_FULL_GROUP_BY rejects the
// short form on older servers, and the deploy target's version is not ours to
// pick. b.* cannot be grouped, so the columns are named explicitly.
$rows = all('
    SELECT b.id, b.name, b.sub, b.price_text, b.is_featured, b.is_published, b.sort_order,
           count(p.id) AS point_count
      FROM bundles b
      LEFT JOIN bundle_points p ON p.bundle_id = b.id
' . $whereSql . '
     GROUP BY b.id, b.name, b.sub, b.price_text, b.is_featured, b.is_published, b.sort_order
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

admin_head([
    'title'   => 'Packages',
    'heading' => 'Packages',
    'intro'   => 'Bundle tiers shown in pricing and the header menu.',
    'active'  => '/admin/bundles.php',
]);
?>

<div class="card">
<?php list_toolbar($st, $total, 'package', 'packages',
        can('content.edit') ? '<a class="btn" href="' . e(site_path('/admin/bundles.php?new=1')) . '">New package</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'order', '#', 'num') ?>
                    <?= list_th($st, 'name', 'Name') ?>
                    <?= list_th($st, 'price', 'Price') ?>
                    <th class="num">Points</th>
                    <?= list_th($st, 'state', 'State') ?>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
                <?= list_empty_state(5, 'package', 'No packages yet.', $st['q'] !== '' ? 'No package matches “' . $st['q'] . '”.' : 'Create a bundled package to show on the site.') ?>
<?php else: foreach ($rows as $r): ?>
                <tr>
                    <td class="num"><?= (int)$r['sort_order'] ?></td>
                    <td>
                        <a href="<?= e(site_path('/admin/bundles.php?id=' . (int)$r['id'])) ?>"><?= e($r['name']) ?></a>
                        <?= $r['is_featured'] ? ' <span class="badge">Featured</span>' : '' ?>
                    </td>
                    <td><?= e($r['price_text']) ?></td>
                    <td class="num"><?= (int)$r['point_count'] ?></td>
                    <td>
                        <span class="badge <?= $r['is_published'] ? 'badge-ok' : 'badge-muted' ?>">
                            <?= $r['is_published'] ? 'Live' : 'Hidden' ?>
                        </span>
                    </td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
