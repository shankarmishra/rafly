<?php
/**
 * Dev-only router for PHP's built-in server: `php -S 127.0.0.1:8899 router.php`
 * (or just run serve.bat).
 *
 * PHP's built-in server never reads .htaccess, so none of the clean-URL
 * rewrites, the old-URL redirects, or the "block application internals"
 * rules apply without this file. Without it, any clean URL (/about,
 * /pricing, /web-development, ...) falls through to the nearest index.php
 * and silently renders the homepage instead of 404ing or routing correctly
 * — which is exactly the bug this file exists to prevent.
 *
 * Apache/LiteSpeed NEVER load this file — .htaccess is what runs in
 * production. The two must be kept in sync by hand; if you add a rewrite
 * rule to .htaccess, add the matching case here too.
 */

$uri     = $_SERVER['REQUEST_URI'] ?? '/';
$rawPath = (string)parse_url($uri, PHP_URL_PATH);
$rawPath = $rawPath !== '' ? $rawPath : '/';
$path    = trim($rawPath, '/');
$root    = __DIR__;

// -----------------------------------------------------------------------
// 1. Block application internals — mirrors .htaccess's
//    RedirectMatch 404 /\.git and RedirectMatch 404 /(inc|partials)/ and
//    RedirectMatch 404 /admin/lib/, plus the <FilesMatch> deny on
//    .gitignore/.env*/composer.json|lock/*.md.
//
//    The <FilesMatch> half of this was NEVER mirrored here — a real,
//    pre-existing gap this comment used to just describe two rules, not
//    three. PHP's built-in server has no equivalent to .htaccess and never
//    reads it (see this file's own header), so nothing enforced it in dev at
//    all: assets/CREDITS.md and, this engagement, every file under docs/seo/
//    were servable over the dev router with no protection whatsoever.
//    Apache/production was never affected — the real <FilesMatch> rule
//    already covered this — but "kept in sync by hand" (also this file's own
//    header) means kept in sync, not mirrored in five sixths.
// -----------------------------------------------------------------------
if (
    $path === '.git' || str_starts_with($path, '.git/') ||
    str_starts_with($path, 'inc/') ||
    str_starts_with($path, 'partials/') ||
    str_starts_with($path, 'private/') ||
    str_starts_with($path, 'admin/lib/') ||
    preg_match('#(^|/)(\.gitignore|\.env[^/]*|composer\.(json|lock)|[^/]+\.md)$#i', $path)
) {
    http_response_code(404);
    require $root . '/404.php';
    return true;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$isSafe = in_array($method, ['GET', 'HEAD'], true);

/** Sends a 301 to $to, GET/HEAD only — never redirect a POST. */
$redirect = static function (string $to) use ($isSafe): bool {
    if (!$isSafe) {
        return false;
    }
    header('Location: ' . $to, true, 301);
    return true;
};

/** Runs $file with $params merged into $_GET (query-string rewrites). */
$dispatch = static function (string $file, array $params = []) use ($root): bool {
    foreach ($params as $k => $v) {
        $_GET[$k] = $v;
    }
    if ($params) {
        $_SERVER['QUERY_STRING'] = http_build_query($params)
            . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '&' . $_SERVER['QUERY_STRING'] : '');
    }
    require $root . '/' . $file;
    return true;
};

// -----------------------------------------------------------------------
// 1b. Trailing slash, mirrors .htaccess's new trailing-slash block.
//
// $path above is already trim()'d, so it alone can't tell /about from
// /about/ — this checks the UNTRIMMED $rawPath instead. Skipped for a path
// that resolves to a real directory (admin/, css/, js/, assets/, vendor/,
// uploads/), same as the .htaccess !-d guard: none of those are clean-URL
// routes, and PHP's built-in server already serves a real directory's
// index.php on its own.
// -----------------------------------------------------------------------
if ($path !== '' && $rawPath === '/' . $path . '/' && !is_dir($root . '/' . $path)) {
    if ($redirect('/' . $path . (($qs = $_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $qs : ''))) {
        return true;
    }
}

// -----------------------------------------------------------------------
// 2. Old .php URLs -> clean URL (301), mirrors .htaccess:56-72.
//
// This MUST run before the real-file passthrough below: about.php etc.
// are real files on disk, and .htaccess redirects them unconditionally
// (its RewriteRules have no `-f` exists-check), so a naive "serve real
// files as-is" rule here would silently skip the redirect and diverge
// from production.
// -----------------------------------------------------------------------
$oldToClean = [
    'index.php'        => '',
    'about.php'        => 'about',
    'team.php'         => 'team',
    'blog.php'         => 'blog',
    'privacy.php'      => 'privacy',
    'thank-you.php'    => 'thank-you',
    'submit.php'       => 'submit',
    'pricing.php'      => 'pricing',
    'case-studies.php' => 'case-studies',
    'contact.php'      => 'contact',
    'locations/greater-noida.php' => 'locations/greater-noida',
    'locations/noida.php'         => 'locations/noida',
    'locations/delhi.php'         => 'locations/delhi',
    'locations/gurgaon.php'       => 'locations/gurgaon',
];
if (isset($oldToClean[$path]) && $redirect('/' . $oldToClean[$path])) {
    return true;
}

if ($path === 'service.php' && isset($_GET['service'])) {
    $slug = (string)$_GET['service'];
    if (in_array($slug, ['web-development', 'web-security', 'marketing-advertisement', 'content-creation', 'ecommerce-support'], true)
        && $redirect('/' . $slug)) {
        return true;
    }
}

if ($path === 'blog-post.php' && isset($_GET['post'])) {
    $slug = (string)$_GET['post'];
    if (preg_match('/^[a-z0-9-]+$/', $slug) && $redirect('/blog/' . $slug)) {
        return true;
    }
}

// --- Insights -> Blog ---------------------------------------------------
// The section was renamed; these mirror the four 301s in .htaccess. They sit
// with the other redirects, before the clean-URL table below, for the same
// reason they do there: /insights would otherwise be looked up as a page in
// its own right and 404 instead of forwarding.
if (preg_match('#^insights/([a-z0-9-]+)$#', $path, $m) && $redirect('/blog/' . $m[1])) {
    return true;
}
if ($path === 'insights' && $redirect('/blog')) {
    return true;
}
if ($path === 'insights.php' && $redirect('/blog')) {
    return true;
}
if ($path === 'insight.php' && isset($_GET['post'])) {
    $slug = (string)$_GET['post'];
    if (preg_match('/^[a-z0-9-]+$/', $slug) && $redirect('/blog/' . $slug)) {
        return true;
    }
}

// -----------------------------------------------------------------------
// 3. Clean URL -> real file, mirrors .htaccess:76-96.
// -----------------------------------------------------------------------
$cleanToFile = [
    'pricing'      => 'pricing.php',
    'case-studies' => 'case-studies.php',
    'contact'      => 'contact.php',
    'about'        => 'about.php',
    'team'         => 'team.php',
    'blog'         => 'blog.php',
    'privacy'      => 'privacy.php',
    'thank-you'    => 'thank-you.php',
    'submit'       => 'submit.php',
    'locations/greater-noida' => 'locations/greater-noida.php',
    'locations/noida'         => 'locations/noida.php',
    'locations/delhi'         => 'locations/delhi.php',
    'locations/gurgaon'       => 'locations/gurgaon.php',
];
if (isset($cleanToFile[$path])) {
    return $dispatch($cleanToFile[$path]);
}

if ($path === 'sitemap.xml') {
    return $dispatch('sitemap.php');
}

// IndexNow key-ownership file, mirrors .htaccess's !-f-guarded rewrite. A
// real static .txt file at this exact path (checked first, same as the
// !-f condition there) always wins over this.
if (preg_match('#^([A-Za-z0-9-]{8,64})\.txt$#', $path, $m) && !is_file($root . '/' . $path)) {
    return $dispatch('indexnow-key.php', ['key' => $m[1]]);
}

// Root-level icon probes, mirroring .htaccess. Served rather than dispatched:
// these are static files, not PHP.
$iconAliases = [
    'favicon.ico'                      => 'assets/favicon-32.png',
    'apple-touch-icon.png'             => 'assets/apple-touch-icon.png',
    'apple-touch-icon-precomposed.png' => 'assets/apple-touch-icon.png',
];
if (isset($iconAliases[$path]) && is_file($root . '/' . $iconAliases[$path])) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    readfile($root . '/' . $iconAliases[$path]);
    return true;
}

if (preg_match('#^blog/([a-z0-9-]+)$#', $path, $m)) {
    return $dispatch('blog-post.php', ['post' => $m[1]]);
}

if (preg_match('#^(web-development|web-security|marketing-advertisement|content-creation|ecommerce-support)$#', $path, $m)) {
    return $dispatch('service.php', ['service' => $m[1]]);
}

if (preg_match('#^admin-gate/([A-Za-z0-9_-]+)$#', $path, $m)) {
    return $dispatch('admin/login.php', ['gate_key' => $m[1]]);
}

// -----------------------------------------------------------------------
// 4. Anything else that's a real file/dir — let the built-in server serve
//    it as-is (CSS, JS, images, fonts, uploads, and every remaining
//    /admin/*.php page, which already uses plain .php URLs and needs no
//    rewriting).
// -----------------------------------------------------------------------
$file = $root . '/' . $path;
if ($path !== '' && (is_file($file) || is_dir($file))) {
    return false;
}

// -----------------------------------------------------------------------
// 5. Homepage.
// -----------------------------------------------------------------------
if ($path === '') {
    require $root . '/index.php';
    return true;
}

// -----------------------------------------------------------------------
// 6. Nothing matched -> real 404, mirrors .htaccess's ErrorDocument 404.
// -----------------------------------------------------------------------
http_response_code(404);
require $root . '/404.php';
return true;
