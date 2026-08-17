<?php
/**
 * Single entry point for every page. Replaces the ~19-line preamble that was
 * duplicated across index/about/insights/service/privacy.
 *
 * Usage, as the very first line of a page (before any output):
 *     <?php require __DIR__ . '/inc/bootstrap.php'; ?>
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/schema.php';

/**
 * Database access for the public site.
 *
 * These only define functions and open no connection — db() is lazy — so pages
 * that never query pay a function-table entry and nothing more. Until this was
 * added, every public page was hardcoded HTML and the entire admin CMS wrote to
 * a database nothing read from.
 *
 * sanitize.php comes along because content pages render post bodies —
 * insight.php runs every body through sanitize_html() at render time rather than
 * trusting that whichever code path wrote the row remembered to — and for
 * slugify().
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sanitize.php';

/**
 * Repositories — the data layer templates read through.
 *
 * A template must never write a SELECT and must never `require` a file out of
 * inc/data/. It calls services_all(), team_all(), metrics_trust_bar() and gets
 * plain arrays back, with no idea whether the answer came from MySQL, from
 * PostgreSQL, or from a seed array standing in until the table exists.
 *
 * That indirection is the whole point: when the `services` table lands, one
 * branch inside inc/repo/services.php starts answering and not a single
 * template changes. It also collapsed three separate definitions of the five
 * services — inc/config.php, index.php and service.php — into one.
 *
 * Load order matters: metrics.php calls services_all().
 */
require_once __DIR__ . '/repo/services.php';
require_once __DIR__ . '/repo/content.php';
require_once __DIR__ . '/repo/metrics.php';
require_once __DIR__ . '/repo/seed.php';


// ---------------------------------------------------------------------------
// Session
// ---------------------------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    // The original code set 'session.cookie_pure', which is not a real PHP
    // directive — it silently did nothing, so the Secure flag was never applied.
    // Secure is set only under HTTPS, otherwise local HTTP dev loses its session.
    ini_set('session.cookie_secure',   IS_HTTPS ? '1' : '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Strict');

    // Without strict mode PHP will happily adopt a session ID it never issued,
    // which is what makes session fixation work: an attacker plants an ID, waits
    // for the victim to log in under it, then reuses it. The login already calls
    // session_regenerate_id(true), so this is defence in depth — but it is one
    // line, and it also covers every session created outside the login path.
    ini_set('session.use_strict_mode', '1');

    session_start();
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * There is deliberately no CSP nonce here any more.
 *
 * It was generated for every request and never used once. The site now has zero
 * inline <script> blocks and zero inline event handlers, so script-src is a
 * plain 'self' (see inc/security.php) — which is strictly stronger than a nonce
 * and costs nothing per request. Reintroduce a nonce only if an inline script
 * genuinely becomes unavoidable.
 */

// ---------------------------------------------------------------------------
// Headers
// ---------------------------------------------------------------------------

send_security_headers('page');
