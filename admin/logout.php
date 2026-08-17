<?php
/**
 * Sign out.
 *
 * POST only. A GET logout is trivially triggered by any image tag or link on
 * any site the user visits — harmless as attacks go, but it makes the admin
 * feel broken for no reason, and the fix is one form.
 */

require __DIR__ . '/lib/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    auth_logout();
    header('Location: ' . site_path('/admin/login.php'), true, 303);
    exit;
}

// GET renders a one-button confirmation rather than acting.
$loggedIn = is_logged_in();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign out — Rafly Admin</title>
    <link rel="stylesheet" href="<?= e(asset('admin/assets/admin.css')) ?>">
</head>
<body class="login">
    <div class="login-card">
        <img src="<?= e(asset('assets/logo.png')) ?>" alt="Rafly" width="128" height="40">
<?php if ($loggedIn): ?>
        <h1>Sign out?</h1>
        <p class="sub">You will need to sign in again to reach the admin.</p>
        <form method="post" action="logout.php">
            <?= csrf_field() ?>
            <button type="submit" class="btn">Sign out</button>
        </form>
<?php else: ?>
        <h1>Signed out</h1>
        <p class="sub">You are not signed in.</p>
        <a class="btn" href="<?= e(site_path('/admin/login.php')) ?>">Sign in</a>
<?php endif; ?>
    </div>
</body>
</html>
