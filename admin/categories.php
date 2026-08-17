<?php
/**
 * Blog categories — list, create, edit, delete.
 *
 * The topic vocabulary behind the filter chips on /blog. Deliberately its own
 * screen rather than a free-text field on the article form: a vocabulary typed
 * fresh on every post becomes "E-commerce", "Ecommerce" and "eCommerce" inside
 * a month, and three chips that should have been one.
 *
 * Same three-modes-one-file shape as every other section here — ? is the list,
 * ?new=1 the create form, ?id=N the edit form, and POST is handled at the top
 * before any output.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require_once __DIR__ . '/../inc/sanitize.php';

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        require_can('content.delete');

        $before = one('SELECT * FROM categories WHERE id = ?', [$id]);
        if ($before !== null) {
            // post_categories cascades, so the articles keep existing and simply
            // stop being filed under this topic. Worth saying out loud in the
            // flash, because "delete" next to a count of articles reads as
            // though it might take them with it.
            $used = (int)scalar('SELECT count(*) FROM post_categories WHERE category_id = ?', [$id]);

            q('DELETE FROM categories WHERE id = ?', [$id]);
            audit('category.delete', 'category', $id, $before, null);

            admin_redirect(
                '/admin/categories.php',
                $used > 0
                    ? 'Category deleted. ' . $used . ' ' . ($used === 1 ? 'article is' : 'articles are')
                        . ' no longer filed under it; nothing was removed.'
                    : 'Category deleted.',
                'warn'
            );
        }
        admin_redirect('/admin/categories.php', 'That category was already removed.', 'warn');
    }

    if ($action === 'save') {
        require_can('content.edit');

        $name = str_cut(trim((string)($_POST['name'] ?? '')), 120);
        $slug = slugify((string)($_POST['slug'] ?? '')) ?: slugify($name);

        if ($name === '') {
            $errors['name'] = 'A name is required.';
        } elseif ($slug === '') {
            $errors['slug'] = 'That name does not produce a usable slug. Enter one by hand.';
        }

        // The slug addresses a listing URL and is UNIQUE in the schema, so the
        // clash is caught here where it can be explained rather than at the
        // constraint where it would be a 500.
        if ($slug !== '') {
            $clash = scalar('SELECT id FROM categories WHERE slug = ? AND id <> ?', [$slug, $id]);
            if ($clash !== null) {
                $errors['slug'] = 'Another category already uses the slug "' . $slug . '". '
                                . 'Change it — everything else you typed has been kept.';
            }
        }

        // Order matters: both statements below spread array_values($fields).
        $fields = [
            'slug'        => $slug,
            'name'        => $name,
            'description' => str_cut(trim((string)($_POST['description'] ?? '')), 400),
            'sort_order'  => max(0, min(999, (int)($_POST['sort_order'] ?? 0))),
        ];

        if ($errors) {
            $submitted = $fields;
            $editId    = $id;
            $isNew     = $id === 0;
        } elseif ($id > 0) {
            $before = one('SELECT * FROM categories WHERE id = ?', [$id]);
            if ($before === null) {
                admin_redirect('/admin/categories.php', 'That category no longer exists.', 'error');
            }

            q('
                UPDATE categories
                   SET slug = ?, name = ?, description = ?, sort_order = ?
                 WHERE id = ?
            ', [...array_values($fields), $id]);

            audit('category.update', 'category', $id, $before,
                  one('SELECT * FROM categories WHERE id = ?', [$id]));
            admin_redirect('/admin/categories.php?id=' . $id, 'Category saved.');
        } else {
            $newId = insert_returning_id('
                INSERT INTO categories (slug, name, description, sort_order)
                VALUES (?, ?, ?, ?)
            ', array_values($fields));

            audit('category.create', 'category', $newId, null,
                  one('SELECT * FROM categories WHERE id = ?', [$newId]));
            admin_redirect('/admin/categories.php?id=' . $newId, 'Category created.');
        }
    }

    if (!$errors) {
        admin_redirect('/admin/categories.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

// ---------------------------------------------------------------------------
// Edit / create form
// ---------------------------------------------------------------------------

if ($editId > 0 || $isNew) {
    $cat = $editId > 0 ? one('SELECT * FROM categories WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $cat === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Category not found', 'active' => '/admin/categories.php']);
        echo '<div class="card"><p>That category does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/categories.php')) . '">Back to all categories</a></p></div>';
        admin_foot();
        exit;
    }

    $v   = static fn(string $k, string $default = ''): string
        => (string)($submitted[$k] ?? $cat[$k] ?? $default);
    $err = static fn(string $k): string
        => isset($errors[$k]) ? '<p class="field-error">' . e($errors[$k]) . '</p>' : '';
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    $usedBy = $editId > 0
        ? (int)scalar('SELECT count(*) FROM post_categories WHERE category_id = ?', [$editId])
        : 0;

    admin_head([
        'title'   => $cat ? 'Edit category' : 'New category',
        'heading' => $cat ? (string)$cat['name'] : 'New category',
        'intro'   => $cat
            ? ($usedBy === 1 ? 'Used by 1 article.' : 'Used by ' . $usedBy . ' articles.')
            : 'A topic readers can filter the blog by.',
        'active'  => '/admin/categories.php',
    ]);
?>

<div class="card">
    <h2>Category</h2>
<?php if (!can('content.edit')): ?>
    <p class="hint">You have read-only access to content.</p>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This category was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="categories.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="form-grid">
            <div class="field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required maxlength="120" value="<?= e($v('name')) ?>"<?= $bad('name') ?>>
                <?= $err('name') ?>
            </div>

            <div class="field">
                <label for="slug">Slug</label>
                <p class="hint">Leave blank to generate from the name. Changing it breaks existing filter links.</p>
                <input type="text" id="slug" name="slug" maxlength="120" value="<?= e($v('slug')) ?>"<?= $bad('slug') ?>>
                <?= $err('slug') ?>
            </div>

            <div class="field">
                <label for="sort_order">Sort order</label>
                <p class="hint">Lowest first. Ties fall back to name.</p>
                <input type="number" id="sort_order" name="sort_order" min="0" max="999"
                       value="<?= (int)($submitted['sort_order'] ?? $cat['sort_order'] ?? 0) ?>">
            </div>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <p class="hint">
                Intro copy on the filtered listing, and its meta description. Leaving it empty
                gives search engines a page with nothing to describe, so it is worth a sentence.
            </p>
            <textarea id="description" name="description" maxlength="400"><?= e($v('description')) ?></textarea>
        </div>

        <div class="form-actions">
<?php if (can('content.edit')): ?>
            <button type="submit" class="btn">Save category</button>
<?php endif; ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/categories.php')) ?>">Back to all</a>
            <span class="spacer"></span>
<?php if ($cat): ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/blog?category=' . rawurlencode((string)$cat['slug']))) ?>"
               target="_blank" rel="noopener">View on site</a>
<?php endif; ?>
        </div>
    </form>
</div>

<?php if ($cat && can('content.delete')): ?>
<div class="card">
    <h2>Delete</h2>
    <p class="hint">
        The articles filed under this category are not deleted — they simply stop being
        filed under it, and the filter link starts 404ing.
    </p>
    <form method="post" action="categories.php" data-confirm="Delete this category? Articles filed under it are kept.">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
        <button type="submit" class="btn btn-danger">Delete category</button>
    </form>
</div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

// ---------------------------------------------------------------------------
// List
// ---------------------------------------------------------------------------

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'     => '/admin/categories.php',
    'sorts'    => [
        'order' => 'c.sort_order',
        'name'  => 'c.name',
        'slug'  => 'c.slug',
    ],
    'default'     => 'order',
    'default_dir' => 'asc',
    'tiebreak'    => 'c.name ASC',
    'search'      => ['c.name', 'c.slug', 'c.description'],
    'per_page'    => 50,
]);

$whereSql = list_where($st);
$total    = (int)scalar('SELECT count(*) FROM categories c' . $whereSql, $st['params']);

// The count is a correlated subquery rather than a GROUP BY join, so a category
// with no articles still appears — which is precisely the row an editor is
// looking for when they come to this screen.
$rows = all('
    SELECT c.*,
           (SELECT count(*) FROM post_categories pc WHERE pc.category_id = c.id) AS post_count
      FROM categories c
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

admin_head([
    'title'   => 'Categories',
    'heading' => 'Categories',
    'intro'   => 'Topics readers can filter the blog by.',
    'active'  => '/admin/categories.php',
]);
?>

<div class="card">
<?php list_toolbar($st, $total, 'category', 'categories',
        can('content.edit') ? '<a class="btn" href="' . e(site_path('/admin/categories.php?new=1')) . '">New category</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'order', '#', 'num') ?>
                    <?= list_th($st, 'name', 'Name') ?>
                    <?= list_th($st, 'slug', 'Slug') ?>
                    <th class="num">Articles</th>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
                <?= list_empty_state(4, 'layers',
                        $st['q'] !== '' ? 'No matching categories.' : 'No categories yet.',
                        $st['q'] !== ''
                            ? 'No category matches “' . $st['q'] . '”.'
                            : 'Create one to let readers filter the blog by topic.') ?>
<?php else: foreach ($rows as $r): ?>
                <tr>
                    <td class="num"><?= (int)$r['sort_order'] ?></td>
                    <td><a href="<?= e(site_path('/admin/categories.php?id=' . (int)$r['id'])) ?>"><?= e((string)$r['name']) ?></a></td>
                    <td><code><?= e((string)$r['slug']) ?></code></td>
                    <td class="num">
                        <?= (int)$r['post_count'] > 0
                            ? (int)$r['post_count']
                            : '<span class="badge badge-muted">unused</span>' ?>
                    </td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
