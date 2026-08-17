<?php
/**
 * Central site configuration.
 * Single source of truth for anything that was previously copy-pasted across pages.
 */

// ---------------------------------------------------------------------------
// Environment
// ---------------------------------------------------------------------------

/**
 * Debug is OFF unless explicitly switched on for a known local host.
 * Never hardcode this to true — it leaks file paths and stack traces to visitors.
 *
 * SERVER_NAME, NOT HTTP_HOST. This read $_SERVER['HTTP_HOST'], which is the
 * Host header the client sent — attacker-controlled. On a catch-all vhost (the
 * default on most shared hosting) a request carrying `Host: localhost` reached
 * the production site and turned display_errors ON for that response, handing
 * back file paths and stack traces on demand. SERVER_NAME comes from the vhost
 * configuration, so a request cannot choose it. SITE_ORIGIN below already
 * allow-lists HTTP_HOST for precisely this reason; the same guard just had not
 * been applied up here.
 *
 * A hardened production box should still set `APP_DEBUG=0` in the environment,
 * or define APP_DEBUG in inc/config.local.php — both win over this derivation
 * (getenv is checked explicitly below; define() is first-write-wins).
 */
if (!defined('APP_DEBUG')) {
    // An explicit env var settles it either way, including switching debug OFF
    // on a local host — otherwise there is no way to test production error
    // handling without editing this file.
    $envDebug = getenv('APP_DEBUG');

    $host = strtolower((string)($_SERVER['SERVER_NAME'] ?? ''));
    $isLocalHost = $envDebug !== '0'
        && (bool)preg_match('/^(localhost|127\.0\.0\.1|\[::1\])(:\d+)?$/', $host);

    // CLI always reports. Its output goes to a terminal or a log, never to a
    // visitor, so there is nothing to leak — and suppressing it means a
    // migration or seed script dies with exit code 255 and no message, which
    // is exactly what happened the first time this ran.
    define('APP_DEBUG', PHP_SAPI === 'cli' || $isLocalHost || $envDebug === '1');
}

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

/** True when the request is served over HTTPS (respects a reverse proxy). */
if (!defined('IS_HTTPS')) {
    define('IS_HTTPS',
        (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443
    );
}

// ---------------------------------------------------------------------------
// Local overrides
//
// Loaded before anything is derived, so a host-specific file can override any
// constant below simply by defining it first — define() is first-write-wins and
// every constant here is guarded. This is where database credentials live; they
// are deliberately absent from the repository (see .gitignore).
//
// The site runs without this file. Only database-backed features fail, and they
// fail with a clear message rather than a stack trace.
// ---------------------------------------------------------------------------

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

/**
 * PREVIEW_SEED — render the sample content in inc/data/seed-preview.php when
 * no database is reachable. A design-preview switch, off by default; turn it
 * on in inc/config.local.php only. See inc/repo/seed.php.
 */
if (!defined('PREVIEW_SEED')) {
    define('PREVIEW_SEED', false);
}


/**
 * The DSN selects the driver, and the driver is the only thing that has to
 * change between local development and the production host. Everything above
 * inc/db.php is dialect-agnostic; db_driver() branches where the two SQL
 * dialects genuinely differ, and inc/migrations/ has a parallel mysql/ set.
 *
 *   PostgreSQL  pgsql:host=127.0.0.1;port=5432;dbname=rafly
 *   MySQL       mysql:host=localhost;dbname=DBNAME;charset=utf8mb4
 *
 * charset=utf8mb4 is not optional on MySQL. Without it the connection
 * negotiates the server's default — often three-byte "utf8", which silently
 * truncates at the first four-byte character (emoji, some Indic sequences).
 * inc/db.php issues SET NAMES as well, but the DSN is what governs how PHP
 * escapes on the way out.
 */
if (!defined('DB_DSN'))  { define('DB_DSN',  ''); }
if (!defined('DB_USER')) { define('DB_USER', ''); }
if (!defined('DB_PASS')) { define('DB_PASS', ''); }

// ---------------------------------------------------------------------------
// Site identity
// ---------------------------------------------------------------------------

/**
 * Production domain. Used by privacy.php, the footer, and canonical /
 * social-share meta tags.
 *
 * This value is RENDERED TO VISITORS: privacy.php prints it in the policy body
 * and builds a mailto: from CONTACT_EMAIL, so operations@ on this domain must
 * be a real, monitored mailbox.
 *
 * robots.txt is a static file and cannot read this constant — its Sitemap line
 * carries the domain literally and must be updated alongside any change here.
 */
define('SITE_DOMAIN',  'rafly.in');
define('SITE_NAME',    'Rafly');
define('SITE_TAGLINE', 'Digital Growth');
define('CONTACT_EMAIL', 'support@' . SITE_DOMAIN);

/**
 * Meta Pixel ID — fallback only. The live value is the admin-editable
 * analytics.meta_pixel_id setting (partials/head.php reads that first); this
 * constant is what the Pixel falls back to if the database is unreachable,
 * the same degrade-to-constant pattern BUNDLES uses for bundles_all(). Not a
 * secret — a Pixel ID is visible in every page's rendered HTML anyway, so
 * committing it here is safe. Guarded so inc/config.local.php can still
 * override it per environment (e.g. blank on a staging host).
 */
if (!defined('META_PIXEL_ID')) {
    define('META_PIXEL_ID', '802516299551165');
}

/**
 * Phone number.
 *
 * CONTACT_PHONE is what visitors see; WHATSAPP_NUMBER is the same line in E.164
 * (digits only, country code, no '+') because that is the format wa.me requires.
 *
 * These were once two different numbers — the display one hardcoded across three
 * templates — which meant a visitor who called got a different line from one who
 * messaged. Both now derive from RAW_PHONE so they cannot drift apart again.
 */
define('RAW_PHONE',       '8796882212');            // national number, digits only
define('CONTACT_PHONE',   '+91 ' . RAW_PHONE);      // displayed in header/footer/contact
define('WHATSAPP_NUMBER', '91' . RAW_PHONE);        // E.164 for wa.me

/**
 * Where lead notifications are delivered. Defaults to CONTACT_EMAIL so the site
 * is never silently dropping enquiries; override once a monitored inbox exists.
 */
define('LEAD_NOTIFY_EMAIL', CONTACT_EMAIL);

/**
 * Absolute origin. Derived so local dev works unchanged, but validated against
 * an allow-list first: $_SERVER['HTTP_HOST'] is attacker-controlled, and this
 * value feeds the canonical and og:url tags. Trusting it lets an attacker point
 * your own canonical at their host.
 */
define('SITE_ORIGIN', (static function (): string {
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));

    $allowed = [
        SITE_DOMAIN,
        'www.' . SITE_DOMAIN,
    ];

    // Local development hosts, with any port.
    $isLocal = (bool)preg_match('/^(localhost|127\.0\.0\.1|\[::1\])(:\d+)?$/', $host);

    if ($isLocal || in_array($host, $allowed, true)) {
        return (IS_HTTPS ? 'https://' : 'http://') . $host;
    }

    // Unrecognised Host header — fall back to the canonical domain rather than
    // reflecting whatever was sent.
    return 'https://' . SITE_DOMAIN;
})());

// ---------------------------------------------------------------------------
// Lead storage
// ---------------------------------------------------------------------------

/**
 * Contact-form submissions contain customer PII (company, phone, requirements).
 * Storing them ABOVE the web root is the only host-independent protection —
 * .htaccess rules are silently ignored by Nginx and by some CDN configurations.
 * private/.htaccess exists as defence in depth, not as the primary control.
 */
define('LEAD_STORE_PATH', dirname(__DIR__, 2) . '/rafly-data');
define('LEAD_CSV_FILE',   LEAD_STORE_PATH . '/leads.csv');

// ---------------------------------------------------------------------------
// Navigation & services — shared so the header, footer and service.php
// can never drift apart the way the duplicated copies did.
// ---------------------------------------------------------------------------

/** slug => display label. Drives the Services dropdown and validates ?service=. */
const SERVICES = [
    'web-development'          => 'Web Development',
    'web-security'             => 'Web Security',
    'marketing-advertisement'  => 'Marketing & Advertisement',
    'content-creation'         => 'Content Creation',
    'ecommerce-support'        => 'E-commerce Support',
];

/**
 * Primary navigation. 'id' matches $page['id'] for active-state highlighting.
 *
 * Unused — partials/header.php builds its nav markup by hand rather than
 * looping this. Kept in sync with the clean-URL paths anyway (rather than
 * deleted) since it's a small, harmless duplication and a stale-but-present
 * const is a worse trap for whoever revives it later than an accurate one.
 */
const NAV_ITEMS = [
    ['id' => 'about',    'label' => 'About',    'href' => '/about'],
    ['id' => 'services', 'label' => 'Services', 'href' => '/#services', 'dropdown' => 'services'],
    ['id' => 'pricing',  'label' => 'Pricing',  'href' => '/#pricing',  'dropdown' => 'pricing'],
    ['id' => 'blog',     'label' => 'Blog',     'href' => '/blog'],
    ['id' => 'contact',  'label' => 'Contact',  'href' => '/#contact'],
];

/**
 * Bundle packages.
 *
 * These existed twice — as $bundles in partials/header.php (mega-dropdown and
 * mobile drawer) and as $tiers in index.php (pricing grid) — with the same
 * three names and the same twelve bullet points maintained separately. That is
 * exactly the drift this config file was created to prevent; these two copies
 * simply escaped the original consolidation.
 *
 * 'name' is the bare label ("Starter"); templates append "Bundle" where the
 * design calls for it.
 */
const BUNDLES = [
    [
        'name'     => 'Starter',
        'sub'      => 'For businesses getting online',
        'featured' => false,
        'points'   => [
            'Website (up to 5 pages)',
            'Baseline security hardening',
            'Starter content pack',
            'Social profile setup',
        ],
    ],
    [
        'name'     => 'Growth',
        'sub'      => 'For businesses ready to scale',
        'featured' => true,
        'points'   => [
            'Everything in Starter',
            'Ongoing marketing campaigns',
            'Advanced security audit',
            'E-commerce storefront setup',
        ],
    ],
    [
        'name'     => 'Enterprise',
        'sub'      => 'For established operations',
        'featured' => false,
        'points'   => [
            'Everything in Growth',
            'Dedicated account team',
            'Custom integrations & APIs',
            'Priority support SLA',
        ],
    ],
];

/** Social links for the floating rail and the footer. */
const SOCIAL_LINKS = [
    ['key' => 'linkedin',  'label' => 'LinkedIn',  'icon' => 'linkedin',  'href' => 'https://www.linkedin.com/company/rafly-digital-growth-private-limited/'],
    ['key' => 'instagram', 'label' => 'Instagram', 'icon' => 'instagram', 'href' => 'https://www.instagram.com/officialrafly.in?igsh=MTMwYWZhb29waWZtbA=='],
    ['key' => 'facebook',  'label' => 'Facebook',  'icon' => 'facebook',  'href' => 'https://www.facebook.com/share/1Lmk1gqPSr/'],
    ['key' => 'whatsapp',  'label' => 'WhatsApp',  'icon' => 'whatsapp',  'href' => 'https://wa.me/' . WHATSAPP_NUMBER . '?text=Hi%20Rafly%20team%2C%20I%27d%20like%20to%20know%20more%20about%20your%20bundle%20packages.'],
    ['key' => 'youtube',   'label' => 'YouTube',   'icon' => 'youtube',   'href' => 'https://youtu.be/EbPOtjdrdHc?si=HIh_GJxllL8SPzPa'],
];
