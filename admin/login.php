<?php
/**
 * Admin login.
 *
 * The one admin page reachable while logged out, which is why lib/bootstrap.php
 * does not call require_login() for you.
 */

require __DIR__ . '/lib/bootstrap.php';

// admin_gate_ok() is a no-op (always true) unless ADMIN_LOGIN_GATE is set in
// inc/config.local.php — see its doc-block for what this does and doesn't buy.
//
// A minimal inline 404 rather than requiring the public 404.php: that file
// does a plain require (not require_once) of inc/bootstrap.php, which has
// already loaded here via admin/lib/bootstrap.php — pulling it in a second
// time would fatal on every redeclared function.
if (!admin_gate_ok()) {
    http_response_code(404);
    header('Content-Type: text/html; charset=UTF-8');
    // Deliberately bare — no CSS (which the admin CSP would block inline
    // anyway) and no distinguishing detail. It should look like nothing lives
    // at this address, because as far as an unauthenticated stranger is
    // concerned, nothing does.
    echo '<!doctype html><meta charset="utf-8"><title>Not found</title><h1>Not found</h1>';
    exit;
}

$next = admin_safe_next($_GET['next'] ?? null);

// Already signed in — nothing to do here.
if (is_logged_in()) {
    header('Location: ' . $next, true, 302);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF applies to login too. Without it, an attacker can silently sign a
    // victim into an account the attacker controls, and anything the victim
    // then does lands in that account — login CSRF is a real, often-skipped bug.
    admin_require_csrf();

    // Same field and check as the public contact form (inc/security.php) —
    // catches a credential-stuffing script that blindly fills every field it
    // finds. A generic rejection, not a giveaway: burning a real password
    // attempt against a known-bot request wastes account throttle budget for
    // nothing, so auth_login() never runs at all in this case.
    if (honeypot_tripped()) {
        $error = 'Those details did not match an active account.';
    } else {
        $result = auth_login(
            (string)($_POST['email'] ?? ''),
            (string)($_POST['password'] ?? '')
        );

        if ($result['needs_totp']) {
            // Correct password, but the account has 2FA confirmed — no session
            // exists yet. admin/2fa.php reads the pending marker auth_login() set.
            admin_redirect('/admin/2fa.php?next=' . rawurlencode($_POST['next'] ?? $next));
        }

        if ($result['ok']) {
            // Re-validate: the POST body carries its own next, and the session
            // was just rotated by auth_start_session().
            admin_redirect(admin_safe_next($_POST['next'] ?? null));
        }

        $error = $result['error'] ?? '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in — Rafly Admin</title>
    <link rel="stylesheet" href="<?= e(asset('admin/assets/admin.css')) ?>">
    <link rel="icon" href="<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="login">
    <form class="login-card" method="post" action="login.php">
        <img src="<?= e(asset('assets/logo.png')) ?>" alt="Rafly" width="128" height="40">

        <h1>Sign in</h1>
        <p class="sub">Rafly admin panel.</p>

<?php if ($error !== ''): ?>
        <div class="login-error" role="alert"><?= e($error) ?></div>
<?php endif; ?>

        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <?php require __DIR__ . '/../partials/honeypot.php'; ?>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="username"
                   autofocus value="<?= e((string)($_POST['email'] ?? '')) ?>">
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn">Sign in</button>
    </form>
</body>
</html>
