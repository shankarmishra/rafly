<?php
/**
 * Users and roles. Admin-only on both view and manage.
 *
 * Passwords are never displayed, never logged, and never written to the audit
 * trail — audit rows here carry the fields that changed with the hash removed.
 *
 * Two self-inflicted lockouts are refused explicitly: you cannot disable your
 * own account, and you cannot remove your own admin role. Both are easy to do
 * by accident and leave a site with no way in except the CLI.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('users.view');

$editId = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

function user_detail_guard(): void
{
    if (!can('users.manage')) {
        http_response_code(403);
        admin_head(['title' => 'Not permitted', 'heading' => 'Not permitted', 'active' => '/admin/users.php']);
        echo '<div class="card"><p>You do not have permission to view this user.</p></div>';
        admin_foot();
        exit;
    }
}

/** Strips secrets before an audit write. */
$redact = static function (?array $row): ?array {
    if ($row === null) {
        return null;
    }
    unset($row['password_hash']);
    return $row;
};

/**
 * Set when a save is rejected by validation.
 *
 * Every validation failure used to admin_redirect() with a flash message, which
 * is right for a successful POST and wrong for a failed one: the redirect
 * discards the request body, so an admin who retyped an email, name and role
 * lost all of it to a one-line error. On failure the form is now re-rendered in
 * place from what was submitted, with the errors attached to the offending
 * fields. Nothing is thrown away — and the password field is never repopulated.
 */
$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    require_can('users.manage');

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);
    $me     = current_user();

    if ($action === 'revoke_sessions') {
        $n = (int)scalar('SELECT count(*) FROM admin_sessions WHERE user_id = ? AND revoked_at IS NULL', [$id]);
        q('UPDATE admin_sessions SET revoked_at = now() WHERE user_id = ? AND revoked_at IS NULL', [$id]);
        audit('user.revoke_sessions', 'user', $id, null, ['revoked' => $n]);
        admin_redirect('/admin/users.php?id=' . $id, $n . ' session(s) revoked.', 'warn');
    }

    if ($action === 'save') {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $name  = str_cut(trim((string)($_POST['name'] ?? '')), 120);
        $role  = (string)($_POST['role'] ?? '');
        $status = (string)($_POST['status'] ?? 'active');
        $password = (string)($_POST['password'] ?? '');

        if (!in_array($status, ['active', 'disabled'], true)) {
            $status = 'active';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }
        if ($name === '') {
            $errors['name'] = 'A name is required.';
        }

        $roleId = scalar('SELECT id FROM roles WHERE slug = ?', [$role]);
        if ($roleId === null) {
            $errors['role'] = 'Pick a role.';
        }

        // lower(email) is the unique index — matching it here turns a constraint
        // violation into a message.
        $clash = scalar('SELECT id FROM users WHERE lower(email) = ? AND id <> ?', [$email, $id]);
        if ($clash !== null) {
            $errors['email'] = 'Another account already uses that email address.';
        }

        // The two self-inflicted lockouts, refused before anything is written.
        if ($id > 0 && $id === (int)$me['id'] && $status === 'disabled') {
            $errors['status'] = 'You cannot disable your own account.';
        }
        if ($id > 0 && $id === (int)$me['id'] && $role !== 'admin') {
            $errors['role'] = 'You cannot remove your own administrator role.';
        }

        // A new account must set a password; an edit may leave it blank to keep
        // the current one. Either way, when one is given it has a floor.
        if ($id > 0) {
            if ($password !== '' && strlen($password) < 12) {
                $errors['password'] = 'Password must be at least 12 characters.';
            }
        } elseif (strlen($password) < 12) {
            $errors['password'] = 'Set a password of at least 12 characters for the new account.';
        }

        // Rejected: re-render the form from what was submitted rather than
        // redirecting, so nothing typed is lost. $editId/$isNew are reset because
        // the request that got here was a POST to users.php with no query string,
        // and the render path below decides which form to draw from those two.
        if ($errors) {
            $submitted = [
                'name'   => $name,
                'email'  => $email,
                'role'   => $role,
                'status' => $status,
            ];
            $editId = $id;
            $isNew  = $id === 0;
        } elseif ($id > 0) {
            $before = one('SELECT * FROM users WHERE id = ?', [$id]);
            if ($before === null) {
                admin_redirect('/admin/users.php', 'That user no longer exists.', 'error');
            }

            q('UPDATE users SET email = ?, name = ?, status = ?, updated_at = now() WHERE id = ?',
              [$email, $name, $status, $id]);

            if ($password !== '') {
                q('UPDATE users SET password_hash = ? WHERE id = ?',
                  [password_hash($password, password_algo()), $id]);

                // Changing a password ends that account's other sessions — the
                // usual reason for the change is that one may be compromised.
                q('UPDATE admin_sessions SET revoked_at = now() WHERE user_id = ? AND revoked_at IS NULL', [$id]);
                audit('user.password_reset', 'user', $id);
            }

            // One role per user in this UI, though the schema allows several.
            q('DELETE FROM user_roles WHERE user_id = ?', [$id]);
            insert_ignore('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)', [$id, $roleId]);

            // Disabling does not itself end a live session, because current_user()
            // only re-reads status on the next request — so end them here.
            if ($status === 'disabled') {
                q('UPDATE admin_sessions SET revoked_at = now() WHERE user_id = ? AND revoked_at IS NULL', [$id]);
            }

            audit('user.update', 'user', $id, $redact($before), $redact(one('SELECT * FROM users WHERE id = ?', [$id])));
            admin_redirect('/admin/users.php?id=' . $id, 'User saved.');
        } else {
            $newId = insert_returning_id('
                INSERT INTO users (email, name, status, password_hash) VALUES (?, ?, ?, ?)
            ', [$email, $name, $status, password_hash($password, password_algo())]);

            insert_ignore('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)', [$newId, $roleId]);

            audit('user.create', 'user', $newId, null, $redact(one('SELECT * FROM users WHERE id = ?', [$newId])));
            admin_redirect('/admin/users.php?id=' . $newId, 'User created.');
        }
    }

    // Only reached when the action was not recognised, or when a save was
    // rejected — in which case execution continues to the form below rather
    // than bouncing the admin away from their work.
    if (!$errors) {
        admin_redirect('/admin/users.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

$roles = all('SELECT id, slug, label, description FROM roles ORDER BY id');

if ($editId > 0 || $isNew) {
    if ($editId > 0) {
        user_detail_guard();
    }

    $u = $editId > 0 ? one('SELECT * FROM users WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $u === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'User not found', 'active' => '/admin/users.php']);
        echo '<div class="card"><p>That user does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/users.php')) . '">Back</a></p></div>';
        admin_foot();
        exit;
    }

    $currentRole = $editId > 0
        ? (string)(scalar('SELECT r.slug FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = ? LIMIT 1', [$editId]) ?? '')
        : 'editor';

    $liveSessions = $editId > 0
        ? (int)scalar('SELECT count(*) FROM admin_sessions WHERE user_id = ? AND revoked_at IS NULL AND expires_at > now()', [$editId])
        : 0;

    $isSelf = $editId > 0 && $editId === (int)current_user()['id'];

    // What was just typed wins over what is stored. On a normal GET $submitted
    // is null and this reads the row exactly as before.
    $v = static fn(string $k, string $default = ''): string
        => (string)($submitted[$k] ?? $u[$k] ?? $default);

    /** Renders the message attached to one field, if there is one. */
    $err = static function (string $k) use ($errors): string {
        return isset($errors[$k])
            ? '<p class="field-error">' . e($errors[$k]) . '</p>'
            : '';
    };

    /** Appended to a control's class so the field itself is marked, not just labelled. */
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    admin_head([
        'title'   => $u ? 'Edit user' : 'New user',
        'heading' => $u ? (string)$u['name'] : 'New user',
        'intro'   => $u && $u['last_login_at'] !== null
            ? 'Last signed in ' . date('j M Y, H:i', strtotime((string)$u['last_login_at']))
            : 'Has never signed in.',
        'active'  => '/admin/users.php',
    ]);
?>

<div class="card">
    <h2>Account</h2>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This user was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="users.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="form-grid">
            <div class="field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" maxlength="120" required value="<?= e($v('name')) ?>"<?= $bad('name') ?>>
                <?= $err('name') ?>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="off" value="<?= e($v('email')) ?>"<?= $bad('email') ?>>
                <?= $err('email') ?>
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role"<?= $bad('role') ?>>
<?php foreach ($roles as $r): ?>
                    <option value="<?= e($r['slug']) ?>"<?= $v('role', $currentRole) === $r['slug'] ? ' selected' : '' ?>>
                        <?= e($r['label']) ?>
                    </option>
<?php endforeach; ?>
                </select>
                <?= $err('role') ?>
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status"<?= $bad('status') ?>>
                    <option value="active"<?= $v('status', 'active') === 'active' ? ' selected' : '' ?>>Active</option>
                    <option value="disabled"<?= $v('status') === 'disabled' ? ' selected' : '' ?>>Disabled</option>
                </select>
                <?= $err('status') ?>
            </div>
        </div>

<?php if ($isSelf): ?>
        <div class="field-locked">
            This is your own account. You cannot disable it or remove your administrator role —
            doing either would lock you out with no way back in except the command line.
        </div>
<?php endif; ?>

        <div class="field">
            <label for="password"><?= $u ? 'New password' : 'Password' ?></label>
            <p class="hint">
                <?= $u
                    ? 'Leave blank to keep the current password. Setting a new one signs this account out everywhere.'
                    : 'At least 12 characters. Length matters more than mixed character classes.' ?>
            </p>
            <input type="password" id="password" name="password" autocomplete="new-password" minlength="12"<?= $bad('password') ?>>
            <?= $err('password') ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save user</button>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/users.php')) ?>">Back to all</a>
        </div>
    </form>
</div>

<?php if ($u): ?>
<div class="card">
    <h2>Sessions</h2>
    <p class="hint">
        <?= $liveSessions === 0
            ? 'No active sessions.'
            : $liveSessions . ' active session' . ($liveSessions === 1 ? '' : 's') . '. Revoking signs this account out everywhere immediately.' ?>
    </p>
<?php if ($liveSessions > 0): ?>
    <form method="post" action="users.php" data-confirm="Sign this account out of every device?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="revoke_sessions">
        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
        <button type="submit" class="btn btn-danger">Revoke all sessions</button>
    </form>
<?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
    <h2>What the roles can do</h2>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Role</th><th>Access</th></tr></thead>
            <tbody>
<?php foreach ($roles as $r): ?>
                <tr>
                    <td><strong><?= e($r['label']) ?></strong></td>
                    <td><?= e($r['description']) ?></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
    admin_foot();
    exit;
}

// GROUP BY lists every selected column rather than relying on the primary key.
// PostgreSQL infers the functional dependency from u.id and MySQL 8 usually
// does too, but MySQL's ONLY_FULL_GROUP_BY rejects the short form on older
// servers — and Hostinger's MySQL version is not ours to pick.
require_once __DIR__ . '/lib/list.php';

// The sortable columns are all on `users` itself. Nothing here sorts by the
// concatenated role list or the session subquery: both are computed per row,
// so sorting on them would mean ordering by an expression rather than an
// indexed column, and neither is a thing anyone looks a user up by.
$st = list_state([
    'base'     => '/admin/users.php',
    'sorts'    => [
        'name'   => 'u.name',
        'email'  => 'u.email',
        'status' => 'u.status',
        'seen'   => 'u.last_login_at',
    ],
    'default'     => 'name',
    'default_dir' => 'asc',
    'tiebreak'    => 'u.id ASC',
    'search'      => ['u.name', 'u.email'],
    'per_page'    => 25,
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM users u' . $whereSql, $st['params']);

$users = all("
    SELECT u.id, u.name, u.email, u.status, u.last_login_at,
           coalesce(" . sql_group_concat('r.label', ', ', 'r.id') . ", '—') AS roles,
           (SELECT count(*) FROM admin_sessions s
             WHERE s.user_id = u.id AND s.revoked_at IS NULL AND s.expires_at > now()) AS sessions
      FROM users u
      LEFT JOIN user_roles ur ON ur.user_id = u.id
      LEFT JOIN roles r ON r.id = ur.role_id
     " . $whereSql . "
     GROUP BY u.id, u.name, u.email, u.status, u.last_login_at
     ORDER BY " . $st['order_sql'] . "
     LIMIT ? OFFSET ?
", [...$st['params'], $st['per_page'], $st['offset']]);

admin_head([
    'title'   => 'Users',
    'heading' => 'Users',
    'intro'   => 'Who can sign in to this admin.',
    'active'  => '/admin/users.php',
]);
?>

<div class="card">
<?php list_toolbar($st, $total, 'user', 'users',
        can('users.manage') ? '<a class="btn" href="' . e(site_path('/admin/users.php?new=1')) . '">New user</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'name', 'Name') ?>
                    <?= list_th($st, 'email', 'Email') ?>
                    <th>Role</th>
                    <?= list_th($st, 'seen', 'Last sign-in', '', 'desc') ?>
                    <th class="num">Sessions</th>
                    <?= list_th($st, 'status', 'Status') ?>
                </tr>
            </thead>
            <tbody>
<?php if (!$users): ?>
                <?= list_empty_state(6, 'users', 'No users found.', $st['q'] !== '' ? 'No user matches “' . $st['q'] . '”.' : 'Add a teammate to give them admin access.') ?>
<?php endif; ?>
<?php foreach ($users as $u): ?>
                <tr>
                    <td><a href="<?= e(site_path('/admin/users.php?id=' . (int)$u['id'])) ?>"><?= e($u['name']) ?></a></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['roles']) ?></td>
                    <td><?= $u['last_login_at'] !== null ? e(date('j M Y, H:i', strtotime((string)$u['last_login_at']))) : 'Never' ?></td>
                    <td class="num"><?= (int)$u['sessions'] ?></td>
                    <td>
                        <span class="badge <?= $u['status'] === 'active' ? 'badge-ok' : 'badge-danger' ?>">
                            <?= e($u['status']) ?>
                        </span>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
