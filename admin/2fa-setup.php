<?php
/**
 * Two-factor auth — self-service enrollment, disable, recovery-code reset.
 *
 * Deliberately not capability-gated beyond require_login(): every signed-in
 * user should be free to secure their own login regardless of role, and
 * everything here only ever touches current_user()'s own row. There is no
 * "manage someone else's 2FA" path — that is what inc/tools/reset-2fa.php is
 * for, and it is CLI-only for exactly the same reason create-user.php is.
 *
 * Enrollment is two steps by design: starting it only stores an UNCONFIRMED
 * secret (totp_confirmed_at stays NULL), so a secret generated but never
 * actually scanned can never silently start being required at the next
 * login. auth_login() only asks for a code once totp_confirmed_at is set.
 *
 * No QR image. Rendering one correctly means either a full from-scratch QR
 * encoder (Reed-Solomon error correction included) or vendoring a third-party
 * one — both carry real risk of a subtly-wrong, unscannable code with no way
 * to visually verify it here, for a feature where getting it wrong risks
 * locking someone out. Manual key entry is a first-class, fully standard path
 * every authenticator app supports ("enter a setup key" / "can't scan?"), so
 * this ships complete and correct on that path alone.
 */

require __DIR__ . '/lib/bootstrap.php';
require_login();

$userId = (int)current_user()['id'];
$user = one('
    SELECT email, password_hash, totp_secret, totp_confirmed_at
      FROM users WHERE id = ?
', [$userId]);

$errors = [];

/** Recovery codes to show exactly once, right after they are generated. */
$freshCodes = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'start' && $user['totp_confirmed_at'] === null) {
        $secret = totp_generate_secret();
        q('UPDATE users SET totp_secret = ?, totp_last_counter = NULL WHERE id = ?', [$secret, $userId]);
        admin_redirect('/admin/2fa-setup.php');
    }

    if ($action === 'cancel' && $user['totp_confirmed_at'] === null) {
        q('UPDATE users SET totp_secret = NULL, totp_last_counter = NULL WHERE id = ?', [$userId]);
        admin_redirect('/admin/2fa-setup.php', 'Two-factor setup cancelled.', 'warn');
    }

    if ($action === 'confirm' && $user['totp_secret'] !== null && $user['totp_confirmed_at'] === null) {
        $code = trim((string)($_POST['code'] ?? ''));
        $accepted = totp_verify((string)$user['totp_secret'], $code, null);

        if ($accepted === false) {
            $errors['code'] = 'That code was not accepted. Check the time on your phone and try again.';
        } else {
            $codes = totp_generate_recovery_codes(10);

            tx(static function () use ($userId, $accepted, $codes): void {
                q('UPDATE users SET totp_confirmed_at = now(), totp_last_counter = ? WHERE id = ?',
                  [$accepted, $userId]);
                q('DELETE FROM user_recovery_codes WHERE user_id = ?', [$userId]);
                foreach ($codes as $c) {
                    q('INSERT INTO user_recovery_codes (user_id, code_hash) VALUES (?, ?)',
                      [$userId, password_hash(totp_normalize_recovery_code($c), password_algo())]);
                }
            });

            audit('user.2fa_enable', 'users', $userId, null, null);

            // Shown exactly once via a session flash — not the DB, which only
            // ever holds hashes. A reload after this point must not repeat them.
            $_SESSION['flash_recovery_codes'] = $codes;
            admin_redirect('/admin/2fa-setup.php');
        }
    }

    if ($action === 'regenerate_codes' && $user['totp_confirmed_at'] !== null) {
        if (!password_verify((string)($_POST['password'] ?? ''), (string)$user['password_hash'])) {
            $errors['password'] = 'That password was not correct.';
        } else {
            $codes = totp_generate_recovery_codes(10);
            tx(static function () use ($userId, $codes): void {
                q('DELETE FROM user_recovery_codes WHERE user_id = ?', [$userId]);
                foreach ($codes as $c) {
                    q('INSERT INTO user_recovery_codes (user_id, code_hash) VALUES (?, ?)',
                      [$userId, password_hash(totp_normalize_recovery_code($c), password_algo())]);
                }
            });
            audit('user.2fa_recovery_regenerate', 'users', $userId, null, null);
            $_SESSION['flash_recovery_codes'] = $codes;
            admin_redirect('/admin/2fa-setup.php');
        }
    }

    if ($action === 'disable' && $user['totp_confirmed_at'] !== null) {
        if (!password_verify((string)($_POST['password'] ?? ''), (string)$user['password_hash'])) {
            $errors['password'] = 'That password was not correct.';
        } else {
            tx(static function () use ($userId): void {
                q('UPDATE users SET totp_secret = NULL, totp_confirmed_at = NULL, totp_last_counter = NULL WHERE id = ?',
                  [$userId]);
                q('DELETE FROM user_recovery_codes WHERE user_id = ?', [$userId]);
            });
            audit('user.2fa_disable', 'users', $userId, null, null);
            admin_redirect('/admin/2fa-setup.php', 'Two-factor authentication turned off.', 'warn');
        }
    }

    // Re-fetch: the actions above may have changed exactly the columns the
    // page below renders from.
    $user = one('
        SELECT email, password_hash, totp_secret, totp_confirmed_at
          FROM users WHERE id = ?
    ', [$userId]);
}

if (isset($_SESSION['flash_recovery_codes'])) {
    $freshCodes = $_SESSION['flash_recovery_codes'];
    unset($_SESSION['flash_recovery_codes']);
}

require __DIR__ . '/lib/layout.php';

admin_head([
    'title'   => 'Two-factor authentication',
    'heading' => 'Two-factor authentication',
    'intro'   => 'Adds a code from your phone to your own sign-in. Nothing here affects any other account.',
    'active'  => '/admin/2fa-setup.php',
]);
?>

<?php if ($freshCodes !== null): ?>
<div class="card">
    <h2>Save your recovery codes</h2>
    <div class="flash flash-warn">
        Each code works once. These are the only time they are ever shown — save them somewhere
        safe now (a password manager, printed and locked away). If you lose your phone, a recovery
        code is the only other way back in.
    </div>
    <pre style="font-family:monospace;font-size:1.05rem;line-height:1.9;padding:16px;border:1px solid;overflow-x:auto;"><?php
        foreach ($freshCodes as $c) { echo e($c) . "\n"; }
    ?></pre>
</div>
<?php endif; ?>

<?php if ($user['totp_confirmed_at'] !== null): ?>

<div class="card">
    <h2>Status: enabled</h2>
    <p>Signing in now asks for a code from your authenticator app after your password.</p>
</div>

<div class="card">
    <h2>Regenerate recovery codes</h2>
    <p class="hint">Generates a fresh set of 10 and invalidates every code you were shown before.</p>
    <form method="post" action="2fa-setup.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="regenerate_codes">
        <div class="field">
            <label for="regen_password">Confirm your password</label>
            <input type="password" id="regen_password" name="password" required autocomplete="current-password"
                   <?= isset($errors['password']) ? ' class="has-error"' : '' ?>>
<?php if (isset($errors['password'])): ?>
            <p class="field-error"><?= e($errors['password']) ?></p>
<?php endif; ?>
        </div>
        <button type="submit" class="btn btn-secondary">Regenerate codes</button>
    </form>
</div>

<div class="card">
    <h2>Turn off two-factor authentication</h2>
    <p class="hint">Your password alone will be enough to sign in again after this.</p>
    <form method="post" action="2fa-setup.php" data-confirm="Turn off two-factor authentication on your account?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="disable">
        <div class="field">
            <label for="disable_password">Confirm your password</label>
            <input type="password" id="disable_password" name="password" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-danger">Turn off 2FA</button>
    </form>
</div>

<?php elseif ($user['totp_secret'] !== null): ?>

<div class="card">
    <h2>Step 1 — add this account to your authenticator app</h2>
    <p>
        In Google Authenticator, Authy, Microsoft Authenticator (or any other TOTP app), choose
        <strong>“enter a setup key”</strong> (sometimes “can’t scan the code?”) and enter:
    </p>
    <dl>
        <dt>Account</dt>
        <dd><?= e($user['email']) ?></dd>
        <dt>Key</dt>
        <dd>
            <code style="font-size:1.15rem;letter-spacing:0.08em;">
                <?= e(chunk_split((string)$user['totp_secret'], 4, ' ')) ?>
            </code>
        </dd>
        <dt>Type</dt>
        <dd>Time based</dd>
    </dl>
</div>

<div class="card">
    <h2>Step 2 — confirm it worked</h2>
    <form method="post" action="2fa-setup.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="confirm">
        <div class="field">
            <label for="confirm_code">Enter the 6-digit code shown in the app</label>
            <input type="text" id="confirm_code" name="code" required autofocus inputmode="numeric"
                   placeholder="123456" maxlength="6" <?= isset($errors['code']) ? ' class="has-error"' : '' ?>>
<?php if (isset($errors['code'])): ?>
            <p class="field-error"><?= e($errors['code']) ?></p>
<?php endif; ?>
        </div>
        <button type="submit" class="btn">Confirm and turn on</button>
    </form>
</div>

<div class="card">
    <form method="post" action="2fa-setup.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="cancel">
        <button type="submit" class="btn btn-secondary">Cancel setup</button>
    </form>
</div>

<?php else: ?>

<div class="card">
    <h2>Status: not enabled</h2>
    <p>Right now, your password alone is enough to sign in. Adding a code from your phone means a
       leaked or guessed password is not enough on its own.</p>
    <form method="post" action="2fa-setup.php">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="start">
        <button type="submit" class="btn">Set up two-factor authentication</button>
    </form>
</div>

<?php endif; ?>

<?php admin_foot(); ?>
