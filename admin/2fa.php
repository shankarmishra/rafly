<?php
/**
 * Second-factor challenge.
 *
 * Reachable only with a pending login (auth_login() set it after a correct
 * password on a 2FA-confirmed account) — there is no session yet, so, like
 * login.php, lib/bootstrap.php's require_login() must not run here.
 *
 * One field, no mode toggle: a 6-digit input is tried as a TOTP code, anything
 * else as a recovery code. auth_login_totp()/auth_login_recovery_code() do
 * their own throttling, so a wrong guess in either form costs the same budget.
 */

require __DIR__ . '/lib/bootstrap.php';

$next = admin_safe_next($_GET['next'] ?? null);

if (is_logged_in()) {
    header('Location: ' . $next, true, 302);
    exit;
}

if (auth_pending_totp_user_id() === null) {
    header('Location: ' . site_path('/admin/login.php'), true, 302);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $code = trim((string)($_POST['code'] ?? ''));

    $error = (preg_match('/^\d{6}$/', $code) ? auth_login_totp($code) : auth_login_recovery_code($code)) ?? '';

    if ($error === '') {
        admin_redirect(admin_safe_next($_POST['next'] ?? null));
    }

    // A failed attempt may have been the last one before the 5-minute pending
    // window expired — send back to login rather than show a stale form.
    if (auth_pending_totp_user_id() === null) {
        header('Location: ' . site_path('/admin/login.php'), true, 302);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Verify — Rafly Admin</title>
    <link rel="stylesheet" href="<?= e(asset('admin/assets/admin.css')) ?>">
    <link rel="icon" href="<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="login">
    <form class="login-card" method="post" action="2fa.php">
        <img src="<?= e(asset('assets/logo.png')) ?>" alt="Rafly" width="128" height="40">

        <h1>Verify it's you</h1>
        <p class="sub">Enter the 6-digit code from your authenticator app.</p>

<?php if ($error !== ''): ?>
        <div class="login-error" role="alert"><?= e($error) ?></div>
<?php endif; ?>

        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">

        <div class="field">
            <label for="code">Authentication code</label>
            <input type="text" id="code" name="code" required autofocus
                   inputmode="numeric" autocomplete="one-time-code"
                   placeholder="123456" maxlength="11">
            <p class="hint">Lost your phone? Enter one of your recovery codes instead.</p>
        </div>

        <button type="submit" class="btn">Verify</button>
    </form>
</body>
</html>
