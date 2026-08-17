<?php
/**
 * Entry point for every admin page.
 *
 * Usage, as the very first line, before any output:
 *     <?php require __DIR__ . '/lib/bootstrap.php'; ?>
 *
 * Loads the site bootstrap (config, helpers, security, session, CSRF), then the
 * auth layer, then replaces the public security headers with the admin set.
 *
 * It does NOT call require_login() — admin/login.php has to be reachable while
 * logged out. Every other page calls require_login() or require_can() itself,
 * immediately after requiring this file. That is deliberate: an implicit login
 * check here would make the one page that must skip it look identical to a page
 * that forgot it.
 */

require_once __DIR__ . '/../../inc/bootstrap.php';
require_once __DIR__ . '/../../inc/auth.php';

send_security_headers('admin');
header('X-Admin-Access: true');

/**
 * Shared page state, mirroring the $page contract on the public site.
 * @var array{title:string,active:string,heading:string,intro:string}
 */
$adminPage = [
    'title'   => 'Admin',
    'active'  => '',
    'heading' => '',
    'intro'   => '',
];

/**
 * Rejects a POST that is missing or has a bad CSRF token.
 *
 * 419 rather than 403: it distinguishes "your session expired, try again" from
 * "you are not allowed to do this", which are different problems with different
 * fixes and would otherwise be indistinguishable to whoever has to support it.
 */
function admin_require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (csrf_ok($_POST['csrf_token'] ?? null)) {
        return;
    }

    http_response_code(419);
    echo '<!doctype html><meta charset="utf-8"><title>Session expired</title>'
       . '<style>body{font:16px/1.6 system-ui,sans-serif;max-width:32rem;margin:15vh auto;padding:0 1.5rem;color:#0f172a}'
       . 'a{color:#1d4ed8}</style>'
       . '<h1>Session expired</h1>'
       . '<p>Your session timed out before the form was submitted. Nothing was saved.</p>'
       . '<p><a href="' . e(site_path('/admin/')) . '">Back to the dashboard</a></p>';
    exit;
}

/**
 * Redirect after a successful POST, so a refresh cannot resubmit it.
 * The flash message survives one request.
 */
function admin_redirect(string $path, string $flash = '', string $type = 'ok'): never
{
    if ($flash !== '') {
        $_SESSION['admin_flash'] = ['text' => $flash, 'type' => $type];
    }

    $target = $path;
    if (!preg_match('#^https?://#i', $path)) {
        $target = site_path($path);
    }

    header('Location: ' . $target, true, 303);
    exit;
}

/** Read and clear the pending flash message. */
function admin_take_flash(): ?array
{
    $flash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);
    return is_array($flash) ? $flash : null;
}

/**
 * Only allows a same-site, relative path — never a full URL.
 *
 * The ?next= parameter on the login page is attacker-supplied. Redirecting to
 * it unchecked is an open redirect: a link to your own admin login that bounces
 * to an attacker's copy of it after a successful password entry, which is a
 * credible phishing flow precisely because the first hop is genuine.
 */
function admin_safe_next(?string $next, string $fallback = '/admin/'): string
{
    $base = site_base_path();
    $basePath = $base === '' ? '' : '/' . ltrim($base, '/');

    if (!is_string($next) || $next === '') {
        return site_path($fallback);
    }

    $next = str_replace('\\', '/', $next);
    if (preg_match('#^https?://#i', $next) || str_starts_with($next, '//')) {
        return site_path($fallback);
    }

    $next = '/' . ltrim($next, '/');

    $parts = [];
    foreach (explode('/', $next) as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            if (empty($parts)) {
                return site_path($fallback);
            }
            array_pop($parts);
            continue;
        }
        $parts[] = $segment;
    }

    $next = '/' . implode('/', $parts);

    if ($basePath !== '') {
        if (str_starts_with($next, $basePath . '/')) {
            $next = substr($next, strlen($basePath));
        } elseif ($next === $basePath) {
            $next = '/';
        } else {
            return site_path($fallback);
        }
    }

    if ($next === '/admin' || str_starts_with($next, '/admin/')) {
        return site_path($next);
    }

    return site_path($fallback);
}

/**
 * Optional obscurity layer in front of admin/login.php. Configured via
 * ADMIN_LOGIN_GATE in inc/config.local.php (git-ignored, same file the
 * database credentials live in) — never in .htaccess or any other tracked
 * file, because .htaccess IS committed and public on this repo's GitHub; a
 * secret embedded there would not be a secret.
 *
 * This is NOT security. Anyone who learns the URL is in exactly the same
 * position as today — a stolen password still gets stopped by 2FA, not by
 * this. What it buys is cutting the routine scanner/bot traffic that
 * otherwise hits every internet-facing /admin/login.php by default, off the
 * request path and out of the logs entirely.
 *
 * A cookie, not a session flag, deliberately: session state is wiped
 * wholesale by auth_logout() and by the idle-timeout path in
 * current_user() (inc/auth.php), and the gate has to survive both — a real
 * admin whose session times out must still be able to find their own login
 * page without re-typing the secret URL from memory.
 *
 * ADMIN_LOGIN_GATE undefined or empty (the default) disables this entirely —
 * admin/login.php behaves exactly as it does today.
 */
function admin_gate_ok(): bool
{
    if (!defined('ADMIN_LOGIN_GATE') || ADMIN_LOGIN_GATE === '') {
        return true;
    }

    $expectedCookie = hash('sha256', ADMIN_LOGIN_GATE);

    $key = (string)($_GET['gate_key'] ?? '');
    if ($key !== '' && hash_equals(ADMIN_LOGIN_GATE, $key)) {
        setcookie('admin_gate', $expectedCookie, [
            'expires'  => time() + 60 * 60 * 24 * 180,
            'path'     => site_path('/admin/'),
            'secure'   => IS_HTTPS,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        return true;
    }

    $cookie = (string)($_COOKIE['admin_gate'] ?? '');
    return $cookie !== '' && hash_equals($expectedCookie, $cookie);
}

/**
 * Apply a strict admin-only boundary for sensitive admin pages.
 */
function admin_require_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        admin_head(['title' => 'Not permitted', 'heading' => 'Not permitted', 'active' => '/admin/']);
        echo '<div class="card"><p>This area is restricted to administrators.</p></div>';
        admin_foot();
        exit;
    }
}
