<?php
/**
 * Small view helpers shared by every page and partial.
 */

/** Escape for HTML output. Short name because it appears in every template. */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Visible breadcrumb trail, built from the exact same {name,url} shape
 * schema_breadcrumbs() (inc/schema.php) takes. Pages that emit breadcrumb
 * JSON-LD should build the array once and pass it to both — otherwise the
 * structured data can claim a trail the rendered page never shows.
 *
 * The last crumb renders as the current page (plain text, aria-current),
 * never a link to itself. Fewer than two crumbs means there's no trail worth
 * showing, so it renders nothing.
 *
 * @param array<int,array{name:string,url:string}> $crumbs
 */
function breadcrumbs(array $crumbs): string
{
    if (count($crumbs) < 2) {
        return '';
    }

    $last = count($crumbs) - 1;
    $items = '';
    foreach ($crumbs as $i => $c) {
        $items .= $i === $last
            ? '<li aria-current="page">' . e($c['name']) . '</li>'
            : '<li><a href="' . e($c['url']) . '">' . e($c['name']) . '</a></li>';
    }

    return '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol>' . $items . '</ol></nav>';
}

/**
 * Base path for the installed site under the current web root.
 *
 * This allows the project to live in a subfolder such as
 * /dev.rafly-overhaul/ on local XAMPP while still producing correct asset URLs.
 */
function site_base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $base = '';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $projectRoot = dirname(__DIR__);

    if ($documentRoot !== '') {
        $documentRoot = str_replace('\\', '/', realpath($documentRoot) ?: $documentRoot);
        $projectRoot = str_replace('\\', '/', realpath($projectRoot) ?: $projectRoot);

        if ($documentRoot !== '' && $projectRoot !== '') {
            $documentRootLower = strtolower($documentRoot);
            $projectRootLower = strtolower($projectRoot);

            if (strpos($projectRootLower, $documentRootLower) === 0) {
                $relative = substr($projectRoot, strlen($documentRoot));
                $base = $relative === '' ? '' : '/' . ltrim($relative, '/');
            }
        }
    }

    return $base;
}

/**
 * Build a site-relative URL that respects the current subfolder install.
 */
function site_path(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $base = site_base_path();

    if ($base === '') {
        return $path;
    }

    return strpos($path, $base . '/') === 0 ? $path : $base . $path;
}

/**
 * A photograph, served as WebP where the browser can take it.
 *
 * The photographs are the heaviest thing this site ships — around 1.2 MB of
 * JPEG against 173 KB of CSS and 52 KB of JS — and .htaccess has carried a
 * MIME type for .webp since it was written without anything ever producing
 * one. inc/tools/build-photos.php generates a twin beside each master; this
 * emits the <picture> that lets a browser choose.
 *
 * The JPEG remains the <img> src, so a client that cannot read WebP is served
 * exactly what it was served before. The <source> is only emitted when the
 * twin actually exists on disk, so a photograph added without re-running the
 * build tool degrades to a plain JPEG rather than to a broken image.
 *
 * width/height are MEASURED, never passed in. They are the hint the browser
 * uses to reserve space before the image arrives, and service.php already
 * learned this the hard way: they had been hardcoded as 1600x1067, the photo
 * set was later recompressed to 1400px wide, and the numbers silently became a
 * lie that reserved the wrong box and made the page jump on load.
 *
 * @param string $path  Project-relative, e.g. 'assets/photos/developer-desk.jpg'
 * @param string $alt   Alternative text. Pass '' only for decorative images.
 * @param array<string,string|bool> $attrs Extra <img> attributes. loading and
 *                      decoding default to lazy/async; pass loading => 'eager'
 *                      for anything above the fold.
 */
function photo(string $path, string $alt, array $attrs = []): string
{
    $path = ltrim($path, '/');
    $full = dirname(__DIR__) . '/' . $path;

    if (!is_file($full)) {
        return '';
    }

    $dims = @getimagesize($full);
    $w    = $dims ? (int)$dims[0] : null;
    $h    = $dims ? (int)$dims[1] : null;

    $attrs = $attrs + ['loading' => 'lazy', 'decoding' => 'async'];

    $out = '<img src="' . e(asset($path)) . '" alt="' . e($alt) . '"';
    if ($w && $h) {
        $out .= ' width="' . $w . '" height="' . $h . '"';
    }
    foreach ($attrs as $k => $v) {
        if ($v === false || $v === null) {
            continue;
        }
        $out .= ' ' . e((string)$k) . ($v === true ? '' : '="' . e((string)$v) . '"');
    }
    $out .= '>';

    $webp     = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
    $webpFull = dirname(__DIR__) . '/' . $webp;

    if ($webp === $path || !is_file($webpFull)) {
        return $out;
    }

    return '<picture><source srcset="' . e(asset($webp)) . '" type="image/webp">' . $out . '</picture>';
}

/**
 * Cache-busting asset URL: appends ?v=<filemtime> so static files can be served
 * with a long max-age while still updating the instant you edit them.
 * There is no build step, so this replaces a bundler's content hashing.
 */
function asset(string $path): string
{
    static $cache = [];

    $path = ltrim($path, '/');
    if (isset($cache[$path])) {
        return $cache[$path];
    }

    $base = site_base_path();
    $prefix = $base !== '' ? $base : '';

    // A deploy-time BUILD_ID (e.g. git short SHA) skips the filesystem stat entirely.
    if (defined('BUILD_ID')) {
        return $cache[$path] = ($prefix === '' ? '/' : $prefix . '/') . $path . '?v=' . BUILD_ID;
    }

    $full = dirname(__DIR__) . '/' . $path;
    $ver  = is_file($full) ? filemtime($full) : null;

    return $cache[$path] = ($prefix === '' ? '/' : $prefix . '/') . $path . ($ver ? '?v=' . $ver : '');
}

/** Returns the active class when $itemId is the current page. */
function nav_active(string $itemId, ?string $currentId): string
{
    return $itemId === $currentId ? 'is-active' : '';
}

/**
 * Multibyte-safe truncation that still works if ext-mbstring is unavailable.
 * Without the fallback, a host without mbstring fatals on form submission.
 */
function str_cut(string $value, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $length, 'UTF-8');
    }

    // Cut on a UTF-8 character boundary rather than mid-sequence.
    if (strlen($value) <= $length) {
        return $value;
    }
    $cut = substr($value, 0, $length);
    while ($cut !== '' && (ord($cut[strlen($cut) - 1]) & 0xC0) === 0x80) {
        $cut = substr($cut, 0, -1);
    }
    return $cut;
}

/**
 * Cut to $length and append an ellipsis ONLY if something was removed.
 *
 * str_cut() deliberately does not do this — it is used to enforce column
 * lengths on write, where an ellipsis would be corruption. Display code wants
 * the opposite, and the two were being combined by hand as
 * `str_cut($s, 70) . '…'`, which puts a trailing ellipsis on a short string
 * that was never truncated. A one-word testimonial rendered as "Excellent…",
 * which reads as though the quote continues.
 */
function str_trunc(string $value, int $length, string $ellipsis = "\u{2026}"): string
{
    $len = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);

    if ($len <= $length) {
        return $value;
    }

    return rtrim(str_cut($value, $length)) . $ellipsis;
}

/**
 * The password-hashing algorithm to use.
 *
 * Argon2id is preferred, but it only exists if PHP was compiled with argon2
 * support (libargon2 / libsodium). Many shared hosts — Hostinger's default PHP
 * builds among them — ship without it, and referencing the undefined constant
 * PASSWORD_ARGON2ID would make password_hash() fatal, breaking login and user
 * creation on the live server. Falling back to PASSWORD_DEFAULT (bcrypt) keeps
 * the site working everywhere. password_verify() reads the algorithm from each
 * stored hash, so existing Argon2 hashes keep verifying regardless of this.
 */
function password_algo(): string|int
{
    return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
}

/** Builds a prefilled WhatsApp link to Rafly's own business number. */
function whatsapp_link(string $message): string
{
    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($message);
}

/**
 * A prefilled WhatsApp link to an ARBITRARY number — used in the admin to
 * message a lead on the number they gave, not Rafly's own line.
 *
 * wa.me needs the number in international format with no +, spaces or symbols.
 * A visitor may type "+91 9147 070619", "(0)9147070619", etc.; we keep only the
 * digits. A bare 10-digit Indian mobile with no country code gets 91 prefixed so
 * the link still opens a chat rather than failing. Returns '' when there is no
 * usable number, so callers can hide the link.
 */
function whatsapp_link_to(string $number, string $message = ''): string
{
    $digits = preg_replace('/\D+/', '', $number) ?? '';

    // Strip a leading 00 international prefix, then default a plain 10-digit
    // Indian mobile to +91. Longer strings are assumed to already carry a code.
    $digits = preg_replace('/^0+/', '', $digits) ?? $digits;
    if (strlen($digits) === 10) {
        $digits = '91' . $digits;
    }

    if ($digits === '') {
        return '';
    }

    $url = 'https://wa.me/' . $digits;
    return $message !== '' ? $url . '?text=' . rawurlencode($message) : $url;
}

/**
 * Hidden attribution fields for a public lead form. Records which page the form
 * was submitted from and, on a service page, which service — so real enquiries
 * arrive in the admin with the context the leads table is built to show.
 *
 * The modal lives in every page's tail, so this reads the CURRENT request rather
 * than being hardcoded per form. Values are validated again server-side in
 * submit.php; service_slug is checked against the SERVICES whitelist there.
 */
function lead_context_fields(): string
{
    $path = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $path = ($path !== null && $path !== '') ? $path : '/';

    $service = '';
    if (isset($_GET['service']) && defined('SERVICES') && array_key_exists($_GET['service'], SERVICES)) {
        $service = (string)$_GET['service'];
    }

    return '<input type="hidden" name="source_page" value="' . e($path) . '">'
         . '<input type="hidden" name="service_slug" value="' . e($service) . '">';
}

/**
 * Bundle packages with their points — DB-backed, three consumers
 * (index.php's pricing grid, partials/header.php's mega-dropdown, pricing.php),
 * which is what makes this a shared helper rather than an inline query the way
 * index.php's case_studies/testimonials queries are (those have exactly one
 * caller each).
 *
 * The `bundles`/`bundle_points` tables and their admin CRUD (admin/bundles.php)
 * already existed but were wired to nothing — every public page instead read
 * the hardcoded BUNDLES const in this file. This is what actually connects
 * them, so an admin editing price_text in the admin panel is reflected here
 * with no further code change.
 *
 * Falls back to BUNDLES when the database is unavailable, same contract as
 * every other DB-backed section on the site (db_available() degrades rather
 * than fatals) — the pricing grid, mega-dropdown and /pricing page all keep
 * rendering with no DB.
 *
 * @return array<int,array{name:string,sub:string,price_text:string,featured:bool,points:array<int,string>}>
 */
function bundles_all(): array
{
    /* The DB rows below carry price_text; the BUNDLES constant they fall back
       to does not — its author left pricing to the admin on purpose. Every
       template reads $b['price_text'] unconditionally, so with the database
       down (or the table empty) the pricing grid, the mega-dropdown and the
       mobile drawer each printed a PHP "Undefined array key" warning INTO the
       page — three of them across the pricing section, in dev mode, in bold.
       Normalising the fallback here means no template needs to know which
       source it got. An empty price_text renders as no price line at all,
       which is what a bundle without a stated price should look like. */
    $fallback = array_map(static fn (array $b): array => $b + ['price_text' => ''], BUNDLES);

    if (!db_available()) {
        return $fallback;
    }

    $rows = all('
        SELECT id, name, sub, price_text, is_featured
          FROM bundles
         WHERE is_published
      ORDER BY sort_order, id
    ');

    if (!$rows) {
        return $fallback;
    }

    $out = [];
    foreach ($rows as $b) {
        $points = all('SELECT label FROM bundle_points WHERE bundle_id = ? ORDER BY sort_order, id', [$b['id']]);

        $out[] = [
            'name'       => (string)$b['name'],
            'sub'        => (string)$b['sub'],
            'price_text' => (string)$b['price_text'],
            'featured'   => (bool)$b['is_featured'],
            'points'     => array_column($points, 'label'),
        ];
    }

    return $out;
}

/**
 * Renders an icon from the sprite inlined by partials/head.php.
 * Decorative by default; pass $label to expose it to assistive tech.
 *
 * The sprite is inlined rather than referenced as an external file because
 * cross-file <use href="sprite.svg#id"> renders inconsistently in older Safari.
 */
function icon(string $name, string $class = '', string $label = ''): string
{
    $aria = $label !== ''
        ? ' role="img" aria-label="' . e($label) . '"'
        : ' aria-hidden="true" focusable="false"';

    return '<svg class="icon ' . e($class) . '"' . $aria . '>'
         . '<use href="#i-' . e($name) . '"></use>'
         . '</svg>';
}

/** Injects the sprite once per request. */
function icon_sprite(): string
{
    static $done = false;
    if ($done) {
        return '';
    }
    $done = true;

    $file = dirname(__DIR__) . '/vendor/icons/sprite.svg';
    if (!is_file($file)) {
        return '';
    }

    // Strip the XML prolog — it is invalid inside an HTML document.
    return preg_replace('/^\s*<\?xml.*?\?>\s*/s', '', (string)file_get_contents($file));
}
