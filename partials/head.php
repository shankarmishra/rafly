<?php
/**
 * Opens the document: doctype through <body>.
 * Every per-page difference is read from $page, with defaults, so a page that
 * sets nothing still renders correctly.
 */

$page = ($page ?? []) + [
    'id'        => '',
    'title'     => SITE_NAME . ' | ' . SITE_TAGLINE,
    'desc'      => 'One partner, one bundled package — web development, content creation, digital marketing, web security, and e-commerce support.',
    'bodyClass' => '',
    'styles'    => [],
    'scripts'   => [],
    'noindex'   => false,

    /**
     * Path (relative to SITE_ORIGIN) this page should declare as canonical.
     * Leave null to use the request path with the query string stripped.
     *
     * Pages whose CONTENT varies by query string must set this explicitly —
     * otherwise all five service.php?service=<slug> pages emit the identical
     * canonical and Google indexes one while dropping the other four.
     */
    'canonical' => null,

    /**
     * Extra JSON-LD nodes for this page (Service, FAQPage, BreadcrumbList…).
     * Organization and WebSite are added automatically on every page.
     */
    'schema'    => [],

    /**
     * Social share image, relative to the web root. Must be exactly 1200x630
     * to match the og:image:width/height declared below — those are a promise
     * to the crawler, and a mismatched image gets cropped or rejected.
     */
    'ogImage'   => 'assets/og-cover.png',

    /**
     * og:type. 'website' suits every page the site has today; an article page
     * should set 'article' so the share card is typed correctly.
     */
    'ogType'    => 'website',
];

/**
 * Core stylesheet stack, in cascade order. 00-tokens must always be first.
 *
 * TWO FILES AT THE END DO DIFFERENT JOBS AND THE ORDER BETWEEN THEM MATTERS.
 *
 * 08-ground.css carries the two-ground token remap and must load after every
 * component sheet, because it wins by being last rather than by being more
 * specific.
 *
 * 09-scenes.css then loads after IT, and is allowed to because of a rule
 * written into its own header: 09-scenes may never define a ground token. It
 * reads --surface / --accent-fg / --paper and defines only scene-local
 * variables of its own. Custom properties resolve per element at use time, so
 * a .ground-chapter ancestor still wins over anything in 09-scenes — which is
 * what makes "last" safe here and would not be if that rule were broken.
 */
$coreStyles = [
    'css/00-tokens.css',
    'css/01-base.css',
    'css/02-layout.css',
    'css/03-components.css',
    'css/04-nav.css',
    'css/05-footer.css',
    'css/06-motion.css',
    'css/07-fx.css',
    'css/08-ground.css',
    'css/09-scenes.css',
];

/**
 * Fallback path when a page sets no explicit 'canonical': the request path
 * with the query string stripped AND, for anything but the root, a trailing
 * slash stripped too. .htaccess (and router.php in dev) already 301 a
 * trailing-slash request to its slash-less form — but that redirect is a
 * second file, kept in sync by hand, and this is what stops the two from
 * ever being able to disagree: even if a rewrite rule is ever missed, this
 * page still declares the slash-less URL as canonical rather than
 * self-canonicalising the duplicate.
 */
$fallbackPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
if ($fallbackPath !== '/' && str_ends_with($fallbackPath, '/')) {
    $fallbackPath = rtrim($fallbackPath, '/');
}

$canonical = $page['canonical'] !== null
    ? SITE_ORIGIN . '/' . ltrim($page['canonical'], '/')
    : SITE_ORIGIN . $fallbackPath;

$ogImageUrl = SITE_ORIGIN . '/' . ltrim(asset($page['ogImage']), '/');

// Meta Pixel — admin-editable (analytics.meta_pixel_id in Settings), with a
// guarded constant fallback (inc/config.php) for when the DB is unreachable.
// Blank id = no script tag at all, which is the whole off-switch: nothing to
// misconfigure into a broken-but-present state. Suppressed on noindex pages
// (admin-adjacent/one-shot pages like thank-you) — those aren't visits worth
// attributing to an ad campaign.
$pixelId = !$page['noindex']
    ? setting('analytics.meta_pixel_id', defined('META_PIXEL_ID') ? META_PIXEL_ID : '')
    : '';
?>
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
<?php if ($pixelId !== ''): ?>
    <!-- As early as possible in <head>, per Meta's own placement guidance —
         this is the one script on the site NOT deferred to partials/tail.php,
         deliberately: a deferred pixel under-reports short/bounced sessions,
         which defeats the point of installing one. js/pixel.js is same-origin
         (script-src 'self' already allows it); it fetches
         connect.facebook.net itself, the only third-party origin this adds. -->
    <script src="<?= e(asset('js/pixel.js')) ?>" data-pixel-id="<?= e($pixelId) ?>"></script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?= e(rawurlencode($pixelId)) ?>&ev=PageView&noscript=1" alt=""></noscript>
<?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page['title']) ?></title>
    <meta name="description" content="<?= e($page['desc']) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
<?php if ($page['noindex']): ?>
    <meta name="robots" content="noindex, nofollow">
<?php else: ?>
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<?php endif; ?>

    <meta property="og:type" content="<?= e($page['ogType']) ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:title" content="<?= e($page['title']) ?>">
    <meta property="og:description" content="<?= e($page['desc']) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:locale" content="en_IN">
    <meta property="og:image" content="<?= e($ogImageUrl) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="<?= e(SITE_NAME . ' — ' . SITE_TAGLINE) ?>">
<?php if ($page['ogType'] === 'article' && isset($page['articleMeta'])): ?>
    <meta property="article:published_time" content="<?= e(date(DATE_ATOM, strtotime((string)$page['articleMeta']['published']))) ?>">
    <meta property="article:modified_time" content="<?= e(date(DATE_ATOM, strtotime((string)$page['articleMeta']['modified']))) ?>">
<?php if (!empty($page['articleMeta']['section'])): ?>
    <meta property="article:section" content="<?= e((string)$page['articleMeta']['section']) ?>">
<?php endif; ?>
<?php endif; ?>

    <!-- twitter:card needs its own title/description/image; it does not inherit og:*. -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($page['title']) ?>">
    <meta name="twitter:description" content="<?= e($page['desc']) ?>">
    <meta name="twitter:image" content="<?= e($ogImageUrl) ?>">
    <meta name="twitter:image:alt" content="<?= e(SITE_NAME . ' — ' . SITE_TAGLINE) ?>">
    <?php /* twitter:site / twitter:creator omitted — no verified @handle exists in SOCIAL_LINKS/config
             to attribute this to; adding one would be fabricated metadata. */ ?>
    <?php /* The logo blue, matching --blue in css/00-tokens.css. This paints the
             browser chrome on Android and the status bar in an iOS PWA, so it is
             the one colour a visitor sees BEFORE the stylesheet has parsed — a
             stale value here is the site introducing itself in the wrong colour.
             It was #0d47a1, from a palette two directions ago. Not a CSS
             variable: a <meta> cannot read one, and this is why the value has
             to be changed in two places by hand (here and
             assets/site.webmanifest) whenever the accent moves. */ ?>
    <meta name="theme-color" content="#0a63ff">

<?php /* Search Console / Bing Webmaster "HTML tag" verification. Both
         constants (inc/config.php) default to '' and are unset on every
         environment until a real value is pasted into inc/config.local.php —
         so this renders nothing today, on any host, rather than a verification
         tag with an empty content attribute. */ ?>
<?php if (GOOGLE_SITE_VERIFICATION !== ''): ?>
    <meta name="google-site-verification" content="<?= e(GOOGLE_SITE_VERIFICATION) ?>">
<?php endif; ?>
<?php if (BING_SITE_VERIFICATION !== ''): ?>
    <meta name="msvalidate.01" content="<?= e(BING_SITE_VERIFICATION) ?>">
<?php endif; ?>

    <!-- SVG favicon first; the 32px PNG is the fallback for browsers without
         SVG favicon support. Both icons and the touch icon are resampled from
         the master logo.png, so they are the real mark rather than a redraw.
         iOS ignores SVG for apple-touch-icon, which is why that one is PNG. -->
    <link rel="icon" href="<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="icon" href="<?= e(asset('assets/favicon-32.png')) ?>" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= e(asset('assets/apple-touch-icon.png')) ?>">
    <link rel="manifest" href="<?= e(asset('assets/site.webmanifest')) ?>">

<?php
/* Fonts: both are variable, so one file each covers every weight.
   These preload hrefs are deliberately NOT run through asset(): fonts.css
   requests the bare filenames, and a preload only matches when the URL is
   byte-identical. Adding ?v=<mtime> here made the browser treat the preload
   and the stylesheet as two different resources and download both fonts
   twice. The font binaries are immutable, so they lose nothing by skipping
   the cache-buster; fonts.css itself still gets one.

   site_path() rather than a bare relative href, though. A relative preload
   resolves against the DIRECTORY of the current URL, so it was correct on /
   and /about and wrong on every two-segment path: an article at /blog/{slug}
   asked for /blog/vendor/fonts/inter-var.woff2 and got a 404. */
?>
    <link rel="preload" href="<?= e(site_path('/vendor/fonts/space-grotesk-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= e(site_path('/vendor/fonts/inter-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('vendor/fonts/fonts.css')) ?>">

<?php foreach ($coreStyles as $href): ?>
    <link rel="stylesheet" href="<?= e(asset($href)) ?>">
<?php endforeach; ?>
<?php foreach ($page['styles'] as $s): ?>
    <link rel="stylesheet" href="<?= e(asset("css/pages/{$s}.css")) ?>">
<?php endforeach; ?>

<?php
/* Structured data. Organization + WebSite are sitewide; pages contribute
   Service / FAQPage / BreadcrumbList nodes via $page['schema'].
   Suppressed on noindex pages — describing a page you have asked not to be
   indexed is contradictory. */
if (!$page['noindex']) {
    echo "\n    " . schema_render(array_merge(
        [schema_organization(), schema_website()],
        $page['schema']
    )) . "\n";
}
?>
</head>
<body class="<?= e($page['bodyClass']) ?>">
<?= icon_sprite() ?>
<a class="skip-link" href="#main">Skip to content</a>
<div class="bg-field" aria-hidden="true"></div>
<?php /* The technical field: circuit traces with travelling data packets, and a
         cursor-reactive dot network. Painted by js/field.js, which is gated in
         js/home.js and never fetched on a phone, under reduced motion, or on
         Save-Data. The canvas is EMPTY until then and css/09-scenes.css gives
         .field a dot-grid background so the still form is the network's own
         geometry standing still — a design, not a placeholder. */ ?>
<canvas id="field" class="field-canvas" aria-hidden="true"></canvas>
<div class="scroll-progress" aria-hidden="true"></div>
