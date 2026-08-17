<?php
/**
 * Testimonials.
 *
 * ENFORCED RULE: is_placeholder cannot be cleared while consent_given is false.
 *
 * This is the one business rule in the schema that exists for a legal reason
 * rather than a presentational one. Publishing a quote attributed to a named
 * person or company without their permission is a real problem, and the seeded
 * rows are all attributed to "[Client Name]" with consent_given = false. The
 * check runs server-side on save — it is enforced on the server regardless of
 * what the form sends, and is the only place the rule lives.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

/**
 * Set when a save is rejected by validation.
 *
 * The old behaviour was admin_redirect() with a flash message, which is right
 * for a successful POST and wrong for a failed one: the redirect discards the
 * request body, so someone who typed out a quote and tripped the consent rule
 * lost everything they had entered to a one-line error message.
 *
 * On failure the form is now re-rendered in place from what was submitted, with
 * the errors attached to the fields that caused them. Nothing is thrown away.
 */
$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        require_can('content.delete');
        $before = one('SELECT * FROM testimonials WHERE id = ?', [$id]);
        if ($before !== null) {
            q('DELETE FROM testimonials WHERE id = ?', [$id]);
            audit('testimonial.delete', 'testimonial', $id, $before, null);
            admin_redirect('/admin/testimonials.php', 'Testimonial deleted.', 'warn');
        }
        admin_redirect('/admin/testimonials.php', 'That testimonial was already removed.', 'warn');
    }

    if ($action === 'save') {
        require_can('content.edit');

        $consent       = isset($_POST['consent_given']);
        $isPlaceholder = isset($_POST['is_placeholder']);

        $quote = str_cut(trim((string)($_POST['quote'] ?? '')), 1000);
        $name  = str_cut(trim((string)($_POST['author_name'] ?? '')), 120);

        if ($quote === '') {
            $errors['quote'] = 'A quote is required.';
        }

        // The rule. Refused here, on the server, regardless of what the form sent.
        if (!$isPlaceholder && !$consent) {
            $errors['consent_given'] =
                'This testimonial cannot go live until you confirm the client gave permission to publish it.';
        }

        // A live testimonial signed "[Client Name]" is not attributed, whatever
        // the consent box says.
        if (!$isPlaceholder && ($name === '' || str_contains($name, '['))) {
            $errors['author_name'] = 'Add the real name of the person quoted before taking this live.';
        }

        $fields = [
            'quote'          => $quote,
            'author_name'    => $name,
            'author_role'    => str_cut(trim((string)($_POST['author_role'] ?? '')), 120),
            'author_company' => str_cut(trim((string)($_POST['author_company'] ?? '')), 120),
            'consent_given'  => $consent,
            'is_placeholder' => $isPlaceholder,
            'is_published'   => isset($_POST['is_published']),
            'sort_order'     => max(0, min(999, (int)($_POST['sort_order'] ?? 0))),
        ];

        // Rejected: re-render the form from $fields rather than redirecting, so
        // the typed quote survives. $editId/$isNew are reset because the request
        // that got here was a POST to testimonials.php with no query string, and
        // the render path below decides which form to draw from those two.
        if ($errors) {
            $submitted = $fields;
            $editId    = $id;
            $isNew     = $id === 0;
        } elseif ($id > 0) {
            $before = one('SELECT * FROM testimonials WHERE id = ?', [$id]);
            if ($before === null) {
                admin_redirect('/admin/testimonials.php', 'That testimonial no longer exists.', 'error');
            }

            q('
                UPDATE testimonials
                   SET quote = ?, author_name = ?, author_role = ?, author_company = ?,
                       consent_given = ?, is_placeholder = ?, is_published = ?,
                       sort_order = ?, updated_at = now()
                 WHERE id = ?
            ', [...array_values($fields), $id]);

            audit('testimonial.update', 'testimonial', $id, $before,
                  one('SELECT * FROM testimonials WHERE id = ?', [$id]));
            admin_redirect('/admin/testimonials.php?id=' . $id, 'Testimonial saved.');
        } else {
            $newId = insert_returning_id('
                INSERT INTO testimonials (quote, author_name, author_role, author_company,
                                          consent_given, is_placeholder, is_published, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ', array_values($fields));

            audit('testimonial.create', 'testimonial', $newId, null,
                  one('SELECT * FROM testimonials WHERE id = ?', [$newId]));
            admin_redirect('/admin/testimonials.php?id=' . $newId, 'Testimonial created.');
        }
    }

    // Only reached when the action was not recognised, or when a save was
    // rejected — in which case execution continues to the form below rather
    // than bouncing away from the typed-in work.
    if (!$errors) {
        admin_redirect('/admin/testimonials.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

if ($editId > 0 || $isNew) {
    $t = $editId > 0 ? one('SELECT * FROM testimonials WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $t === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Testimonial not found', 'active' => '/admin/testimonials.php']);
        echo '<div class="card"><p>That testimonial does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/testimonials.php')) . '">Back</a></p></div>';
        admin_foot();
        exit;
    }

    // What was just typed wins over what is stored. On a normal GET $submitted
    // is null and this reads the row exactly as before.
    $v = static fn(string $k, string $d = ''): string => (string)($submitted[$k] ?? $t[$k] ?? $d);

    /** Renders the message attached to one field, if there is one. */
    $err = static function (string $k) use ($errors): string {
        return isset($errors[$k])
            ? '<p class="field-error">' . e($errors[$k]) . '</p>'
            : '';
    };

    /** Appended to a control's class so the field itself is marked, not just labelled. */
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    /** Checkbox state: what was submitted wins, else the row, else the new-form default. */
    $checked = static fn(string $k, bool $default): bool
        => $submitted !== null ? !empty($submitted[$k]) : ($t !== null ? (bool)$t[$k] : $default);

    admin_head([
        'title'   => $t ? 'Edit testimonial' : 'New testimonial',
        'heading' => $t ? ($v('author_name') ?: 'Testimonial') : 'New testimonial',
        'intro'   => 'Shown in the testimonials section on the homepage.',
        'active'  => '/admin/testimonials.php',
    ]);
?>

<div class="card">
    <h2>Testimonial</h2>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This testimonial was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="testimonials.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="field">
            <label for="quote">Quote</label>
            <textarea id="quote" name="quote" maxlength="1000" required<?= $bad('quote') ?>><?= e($v('quote')) ?></textarea>
            <?= $err('quote') ?>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="author_name">Name</label>
                <input type="text" id="author_name" name="author_name" maxlength="120" value="<?= e($v('author_name')) ?>"<?= $bad('author_name') ?>>
                <?= $err('author_name') ?>
            </div>

            <div class="field">
                <label for="author_role">Role</label>
                <input type="text" id="author_role" name="author_role" maxlength="120" value="<?= e($v('author_role')) ?>" placeholder="Founder">
            </div>

            <div class="field">
                <label for="author_company">Company</label>
                <input type="text" id="author_company" name="author_company" maxlength="120" value="<?= e($v('author_company')) ?>">
            </div>

            <div class="field">
                <label for="sort_order">Sort order</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="999" value="<?= (int)($submitted['sort_order'] ?? $t['sort_order'] ?? 0) ?>">
            </div>
        </div>

        <div class="field-check">
            <input type="checkbox" id="consent_given" name="consent_given" value="1"
                   <?= $checked('consent_given', false) ? 'checked' : '' ?><?= $bad('consent_given') ?>>
            <label for="consent_given">
                The client gave permission to publish this
                <span class="hint">
                    Attributing a quote to a named person or company without their permission is
                    not something we can undo after the fact. This must be ticked before the
                    testimonial can go live.
                </span>
            </label>
            <?= $err('consent_given') ?>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_placeholder" name="is_placeholder" value="1"
                   <?= $checked('is_placeholder', true) ? 'checked' : '' ?>>
            <label for="is_placeholder">
                Still a placeholder
                <span class="hint">
                    Renders an orange PLACEHOLDER badge visitors can see. Untick only when the
                    quote is real, attributed to a real person, and consented to above.
                </span>
            </label>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_published" name="is_published" value="1"
                   <?= $checked('is_published', true) ? 'checked' : '' ?>>
            <label for="is_published">
                Show on the site
                <span class="hint">Untick to hide without deleting.</span>
            </label>
        </div>

        <div class="form-actions">
<?php if (can('content.edit')): ?>
            <button type="submit" class="btn">Save</button>
<?php endif; ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/testimonials.php')) ?>">Back to all</a>
        </div>
    </form>
</div>

<?php if ($t && can('content.delete')): ?>
<div class="card">
    <h2>Delete</h2>
    <form method="post" action="testimonials.php" data-confirm="Delete this testimonial?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
        <button type="submit" class="btn btn-danger">Delete testimonial</button>
    </form>
</div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'     => '/admin/testimonials.php',
    'sorts'    => [
        'order'   => 't.sort_order',
        'author'  => 't.author_name',
        'company' => 't.author_company',
        'consent' => 't.consent_given',
        'state'   => 't.is_placeholder',
    ],
    'default'     => 'order',
    'default_dir' => 'asc',
    'tiebreak'    => 't.id ASC',
    'search'      => ['t.quote', 't.author_name', 't.author_role', 't.author_company'],
    'per_page'    => 25,
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM testimonials t' . $whereSql, $st['params']);

$rows = all('
    SELECT t.* FROM testimonials t
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

// Counted across the whole table, not the current page: "3 without consent" is
// a fact about the site, and it would be misleading for it to change because
// someone typed a search term.
$noConsent = (int)scalar('SELECT count(*) FROM testimonials WHERE NOT consent_given');

admin_head([
    'title'   => 'Testimonials',
    'heading' => 'Testimonials',
    'intro'   => 'Client quotes shown on the homepage.',
    'active'  => '/admin/testimonials.php',
]);
?>

<?php if ($noConsent > 0): ?>
<div class="card">
    <h2><?= $noConsent ?> without recorded consent</h2>
    <p class="hint">
        These cannot be taken live until permission to publish is confirmed. The seeded quotes
        are all attributed to "[Client Name]" — they need a real name, role and company as
        well as consent.
    </p>
</div>
<?php endif; ?>

<div class="card">
<?php list_toolbar($st, $total, 'testimonial', 'testimonials',
        can('content.edit') ? '<a class="btn" href="' . e(site_path('/admin/testimonials.php?new=1')) . '">New testimonial</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'order', '#', 'num') ?>
                    <th>Quote</th>
                    <?= list_th($st, 'author', 'Attributed to') ?>
                    <?= list_th($st, 'consent', 'Consent') ?>
                    <?= list_th($st, 'state', 'State') ?>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
                <?= list_empty_state(5, 'star', 'No testimonials yet.',
                        $st['q'] !== ''
                            ? 'No testimonial matches “' . $st['q'] . '”.'
                            : 'Add a client quote to show on the homepage.') ?>
<?php else: foreach ($rows as $r): ?>
                <tr>
                    <td class="num"><?= (int)$r['sort_order'] ?></td>
                    <?php /* str_trunc(), not str_cut() . '…'. The ellipsis used to be
                             concatenated outside the truncation, so every quote got one
                             whether or not anything had been cut off — a five-word quote
                             rendered as though it trailed away mid-sentence. */ ?>
                    <td><a href="<?= e(site_path('/admin/testimonials.php?id=' . (int)$r['id'])) ?>"><?= e(str_trunc((string)$r['quote'], 70)) ?></a></td>
                    <td><?= e(trim($r['author_name'] . ', ' . $r['author_role'], ', ')) ?></td>
                    <td>
                        <span class="badge <?= $r['consent_given'] ? 'badge-ok' : 'badge-danger' ?>">
                            <?= $r['consent_given'] ? 'Given' : 'Not given' ?>
                        </span>
                    </td>
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
