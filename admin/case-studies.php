<?php
/**
 * Case studies — the "Selected Work" cards on the homepage.
 *
 * The three seeded rows carry INVENTED metrics (+182%, 3.4x, 0) and are flagged
 * is_placeholder = true, which renders an orange PLACEHOLDER badge to visitors.
 * Clearing that flag is the act of saying "this number is real and I can
 * evidence it", so it is a deliberate checkbox rather than something that
 * happens as a side effect of editing.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

/**
 * Set when a save is rejected by validation.
 *
 * A failed POST used to admin_redirect() with a flash message, which threw away
 * the entire request body — everything typed into the form was lost to a
 * one-line error. On failure the form is now re-rendered in place from what was
 * submitted, with the error attached to the field that caused it.
 */
$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        require_can('content.delete');
        $before = one('SELECT * FROM case_studies WHERE id = ?', [$id]);
        if ($before !== null) {
            q('DELETE FROM case_studies WHERE id = ?', [$id]);
            audit('case_study.delete', 'case_study', $id, $before, null);
            admin_redirect('/admin/case-studies.php', 'Case study deleted.', 'warn');
        }
        admin_redirect('/admin/case-studies.php', 'That case study was already removed.', 'warn');
    }

    if ($action === 'save') {
        require_can('content.edit');

        // Tags are stored as one comma-separated string (migration 002). Split,
        // trim, drop blanks and rejoin so "a,,  b ," normalises to "a, b".
        $tags = implode(', ', array_filter(array_map(
            'trim',
            explode(',', (string)($_POST['tags'] ?? ''))
        ), static fn($t) => $t !== ''));

        $fields = [
            'client_name'    => str_cut(trim((string)($_POST['client_name'] ?? '')), 120),
            'sector'         => str_cut(trim((string)($_POST['sector'] ?? '')), 60),
            'problem'        => str_cut(trim((string)($_POST['problem'] ?? '')), 2000),
            'action'         => str_cut(trim((string)($_POST['action_taken'] ?? '')), 2000),
            'metric_value'   => str_cut(trim((string)($_POST['metric_value'] ?? '')), 40),
            'metric_label'   => str_cut(trim((string)($_POST['metric_label'] ?? '')), 120),
            'tags'           => str_cut($tags, 300),
            'is_placeholder' => isset($_POST['is_placeholder']),
            'is_published'   => isset($_POST['is_published']),
            'sort_order'     => max(0, min(999, (int)($_POST['sort_order'] ?? 0))),
        ];

        if ($fields['client_name'] === '' && $fields['metric_value'] === '') {
            $errors['client_name'] = 'Give the case study at least a client name or a headline metric.';
        }

        // Rejected: re-render the form from $fields rather than redirecting, so
        // nothing typed is thrown away. $editId/$isNew are reset because the
        // request that got here was a POST with no query string, and the render
        // path below decides which form to draw from those two.
        if ($errors) {
            $submitted = $fields;
            $editId    = $id;
            $isNew     = $id === 0;
        } elseif ($id > 0) {
            $before = one('SELECT * FROM case_studies WHERE id = ?', [$id]);
            if ($before === null) {
                admin_redirect('/admin/case-studies.php', 'That case study no longer exists.', 'error');
            }

            q('
                UPDATE case_studies
                   SET client_name = ?, sector = ?, problem = ?, action = ?,
                       metric_value = ?, metric_label = ?, tags = ?,
                       is_placeholder = ?, is_published = ?, sort_order = ?,
                       updated_at = now()
                 WHERE id = ?
            ', [...array_values($fields), $id]);

            audit('case_study.update', 'case_study', $id, $before,
                  one('SELECT * FROM case_studies WHERE id = ?', [$id]));
            admin_redirect('/admin/case-studies.php?id=' . $id, 'Case study saved.');
        } else {
            $newId = insert_returning_id('
                INSERT INTO case_studies (client_name, sector, problem, action, metric_value,
                                          metric_label, tags, is_placeholder, is_published, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ', array_values($fields));

            audit('case_study.create', 'case_study', $newId, null,
                  one('SELECT * FROM case_studies WHERE id = ?', [$newId]));
            admin_redirect('/admin/case-studies.php?id=' . $newId, 'Case study created.');
        }
    }

    // Only reached on an unrecognised action, or when a save was rejected — in
    // which case execution falls through to the form below rather than bouncing
    // the editor away from their work.
    if (!$errors) {
        admin_redirect('/admin/case-studies.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

if ($editId > 0 || $isNew) {
    $cs = $editId > 0 ? one('SELECT * FROM case_studies WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $cs === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Case study not found', 'active' => '/admin/case-studies.php']);
        echo '<div class="card"><p>That case study does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/case-studies.php')) . '">Back</a></p></div>';
        admin_foot();
        exit;
    }

    // What was just typed wins over what is stored. On a normal GET $submitted
    // is null and this reads the row exactly as before.
    $v = static fn(string $k, string $d = ''): string
        => (string)($submitted[$k] ?? $cs[$k] ?? $d);

    /** Renders the message attached to one field, if there is one. */
    $err = static function (string $k) use ($errors): string {
        return isset($errors[$k])
            ? '<p class="field-error">' . e($errors[$k]) . '</p>'
            : '';
    };

    /** Appended to a control's class so the field itself is marked, not just labelled. */
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    /** Checkbox state: what was submitted wins, else the row, else checked on a fresh form. */
    $chk = static function (string $k) use ($submitted, $cs): bool {
        if ($submitted !== null) return !empty($submitted[$k]);
        if ($cs !== null)        return (bool)$cs[$k];
        return true;
    };

    admin_head([
        'title'   => $cs ? 'Edit case study' : 'New case study',
        'heading' => $cs ? ($v('client_name') ?: 'Case study') : 'New case study',
        'intro'   => 'Shown in "Selected Work" on the homepage.',
        'active'  => '/admin/case-studies.php',
    ]);
?>

<div class="card">
    <h2>Case study</h2>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This case study was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="case-studies.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="form-grid">
            <div class="field">
                <label for="client_name">Client name</label>
                <p class="hint">Or a description like "a Kolkata-based D2C brand" if they have not consented to be named.</p>
                <input type="text" id="client_name" name="client_name" maxlength="120" value="<?= e($v('client_name')) ?>"<?= $bad('client_name') ?>>
                <?= $err('client_name') ?>
            </div>

            <div class="field">
                <label for="sector">Sector</label>
                <input type="text" id="sector" name="sector" maxlength="60" value="<?= e($v('sector')) ?>" placeholder="E-commerce">
            </div>

            <div class="field">
                <label for="metric_value">Headline metric</label>
                <p class="hint">The big number on the card.</p>
                <input type="text" id="metric_value" name="metric_value" maxlength="40" value="<?= e($v('metric_value')) ?>" placeholder="+182%"<?= $bad('client_name') ?>>
            </div>

            <div class="field">
                <label for="metric_label">What it measures</label>
                <input type="text" id="metric_label" name="metric_label" maxlength="120" value="<?= e($v('metric_label')) ?>" placeholder="organic traffic in 6 months">
            </div>
        </div>

        <div class="field">
            <label for="tags">Tags</label>
            <p class="hint">Comma separated, in display order.</p>
            <input type="text" id="tags" name="tags" maxlength="300" value="<?= e($v('tags')) ?>" placeholder="Web Development, SEO, Content">
        </div>

        <div class="field">
            <label for="problem">The problem</label>
            <p class="hint">Not shown on the card yet — kept for a future detail page.</p>
            <textarea id="problem" name="problem" maxlength="2000"><?= e($v('problem')) ?></textarea>
        </div>

        <div class="field">
            <label for="action_taken">What we did</label>
            <textarea id="action_taken" name="action_taken" maxlength="2000"><?= e($v('action')) ?></textarea>
        </div>

        <div class="field">
            <label for="sort_order">Sort order</label>
            <input type="number" id="sort_order" name="sort_order" min="0" max="999" value="<?= (int)($submitted['sort_order'] ?? $cs['sort_order'] ?? 0) ?>">
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_placeholder" name="is_placeholder" value="1"
                   <?= $chk('is_placeholder') ? 'checked' : '' ?>>
            <label for="is_placeholder">
                Still a placeholder
                <span class="hint">
                    While this is ticked the card renders with an orange PLACEHOLDER badge that
                    visitors can see. Untick it only once the metric above is real and you could
                    evidence it if a prospect asked.
                </span>
            </label>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_published" name="is_published" value="1"
                   <?= $chk('is_published') ? 'checked' : '' ?>>
            <label for="is_published">
                Show on the site
                <span class="hint">Untick to hide this card without deleting it.</span>
            </label>
        </div>

        <div class="form-actions">
<?php if (can('content.edit')): ?>
            <button type="submit" class="btn">Save</button>
<?php endif; ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/case-studies.php')) ?>">Back to all</a>
        </div>
    </form>
</div>

<?php if ($cs && can('content.delete')): ?>
<div class="card">
    <h2>Delete</h2>
    <form method="post" action="case-studies.php" data-confirm="Delete this case study?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$cs['id'] ?>">
        <button type="submit" class="btn btn-danger">Delete case study</button>
    </form>
</div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'     => '/admin/case-studies.php',
    'sorts'    => [
        'order'  => 'c.sort_order',
        'client' => 'c.client_name',
        'sector' => 'c.sector',
        'state'  => 'c.is_placeholder',
    ],
    'default'     => 'order',
    'default_dir' => 'asc',
    'tiebreak'    => 'c.id ASC',
    'search'      => ['c.client_name', 'c.sector', 'c.problem', 'c.action', 'c.metric_label'],
    'per_page'    => 25,
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM case_studies c' . $whereSql, $st['params']);

$rows = all('
    SELECT c.* FROM case_studies c
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

// Whole-table figure, deliberately unaffected by the current search.
$placeholders = (int)scalar('SELECT count(*) FROM case_studies WHERE is_placeholder');

admin_head([
    'title'   => 'Case studies',
    'heading' => 'Case studies',
    'intro'   => 'The "Selected Work" cards on the homepage.',
    'active'  => '/admin/case-studies.php',
]);
?>

<?php if ($placeholders > 0): ?>
<div class="card">
    <h2><?= $placeholders ?> still marked placeholder</h2>
    <p class="hint">
        These render an orange PLACEHOLDER badge on the live homepage, and their metrics are
        the invented seed values. Replace them with results you can evidence, then untick
        "Still a placeholder" on each.
    </p>
</div>
<?php endif; ?>

<div class="card">
<?php list_toolbar($st, $total, 'case study', 'case studies',
        can('content.edit') ? '<a class="btn" href="' . e(site_path('/admin/case-studies.php?new=1')) . '">New case study</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'order', '#', 'num') ?>
                    <?= list_th($st, 'client', 'Client') ?>
                    <th>Metric</th>
                    <?= list_th($st, 'sector', 'Sector') ?>
                    <?= list_th($st, 'state', 'State') ?>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
                <?= list_empty_state(5, 'trending-up', 'No case studies yet.',
                        $st['q'] !== ''
                            ? 'No case study matches “' . $st['q'] . '”.'
                            : 'Add a client result to show on the homepage.') ?>
<?php else: foreach ($rows as $r): ?>
                <tr>
                    <td class="num"><?= (int)$r['sort_order'] ?></td>
                    <td><a href="<?= e(site_path('/admin/case-studies.php?id=' . (int)$r['id'])) ?>"><?= e($r['client_name'] ?: '(unnamed)') ?></a></td>
                    <td><strong><?= e($r['metric_value']) ?></strong> <?= e($r['metric_label']) ?></td>
                    <td><?= e($r['sector']) ?></td>
                    <td>
<?php if ($r['is_placeholder']): ?>
                        <span class="badge badge-warn">Placeholder</span>
<?php elseif (!$r['is_published']): ?>
                        <span class="badge badge-muted">Hidden</span>
<?php else: ?>
                        <span class="badge badge-ok">Live</span>
<?php endif; ?>
                    </td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
