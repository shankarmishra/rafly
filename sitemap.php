<?php
/**
 * XML sitemap, generated rather than hand-maintained.
 *
 * The five service URLs are the reason this exists: they are only reachable
 * through a nav dropdown, they differ only by query string, and until the
 * canonical bug was fixed they all claimed to be the same page. Listing them
 * explicitly is what gets them crawled as five distinct pages.
 *
 * Served at /sitemap.xml via a rewrite in .htaccess.
 */

require __DIR__ . '/inc/bootstrap.php';

header('Content-Type: application/xml; charset=UTF-8');

/**
 * loc => [changefreq, priority]
 * lastmod falls back to the file's own mtime, so it stays honest without
 * anyone remembering to update it — EXCEPT for the three pages below whose
 * real content lives in the database, where the template's mtime is
 * meaningless: editing a price in the admin doesn't touch pricing.php on
 * disk, so it never used to move that URL's lastmod at all.
 */
$pricingMod     = db_available() ? scalar("SELECT max(updated_at) FROM bundles") : null;
$caseStudiesMod = db_available() ? scalar("SELECT max(updated_at) FROM case_studies WHERE is_published") : null;
$blogMod        = db_available() ? scalar(
    "SELECT max(updated_at) FROM posts WHERE status = 'published' AND published_at IS NOT NULL AND published_at <= now()"
) : null;

$urls = [
    // 'loc' is now the clean-URL path; 'file' stays the underlying .php for
    // the mtime lookup below — the two were already decoupled before clean
    // URLs existed, so this is just giving 'loc' a different value.
    ['loc' => '',          'file' => 'index.php',    'freq' => 'weekly',  'pri' => '1.0'],
    ['loc' => 'about',     'file' => 'about.php',    'freq' => 'monthly', 'pri' => '0.8'],
    ['loc' => 'pricing',       'file' => 'pricing.php',       'freq' => 'monthly', 'pri' => '0.9']
        + ($pricingMod ? ['mod' => date('Y-m-d', strtotime((string)$pricingMod))] : []),
    ['loc' => 'case-studies',  'file' => 'case-studies.php',  'freq' => 'monthly', 'pri' => '0.7']
        + ($caseStudiesMod ? ['mod' => date('Y-m-d', strtotime((string)$caseStudiesMod))] : []),
    ['loc' => 'blog',      'file' => 'blog.php',     'freq' => 'weekly',  'pri' => '0.7']
        + ($blogMod ? ['mod' => date('Y-m-d', strtotime((string)$blogMod))] : []),
    ['loc' => 'team',      'file' => 'team.php',     'freq' => 'monthly', 'pri' => '0.6'],
    ['loc' => 'contact',   'file' => 'contact.php',  'freq' => 'monthly', 'pri' => '0.8'],
    ['loc' => 'privacy',   'file' => 'privacy.php',  'freq' => 'yearly',  'pri' => '0.3'],
];

// One entry per service, driven by the same SERVICES array that builds the nav.
foreach (array_keys(SERVICES) as $slug) {
    $urls[] = [
        'loc'  => $slug,
        'file' => 'service.php',
        'freq' => 'monthly',
        'pri'  => '0.9',
    ];
}

/**
 * One entry per published article.
 *
 * These carry an explicit 'mod' because a post's real last-modified date lives
 * in the database — blog-post.php's own mtime is the same for every article
 * and would tell a crawler that all of them changed whenever the template did.
 *
 * Guarded by db_available(): the sitemap is the one page most likely to be
 * fetched while the database is down (crawlers do not retry politely), and a
 * fatal here would drop the static URLs too.
 */
$posts = [];
if (db_available()) {
    $posts = all(
        "SELECT slug, published_at, updated_at
           FROM posts
          WHERE status = 'published'
            AND published_at IS NOT NULL
            AND published_at <= now()
       ORDER BY published_at DESC"
    );
} elseif (seed_preview_enabled()) {
    $posts = seed_preview_posts();      // design preview only, see inc/repo/seed.php
}
if ($posts) {
    foreach ($posts as $p) {
        $urls[] = [
            'loc'  => 'blog/' . rawurlencode((string)$p['slug']),
            'mod'  => date('Y-m-d', strtotime((string)($p['updated_at'] ?: $p['published_at']))),
            'freq' => 'monthly',
            'pri'  => '0.6',
        ];
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u):
    // An explicit 'mod' wins; otherwise fall back to the source file's mtime.
    if (isset($u['mod'])) {
        $mod = $u['mod'];
    } else {
        $path = __DIR__ . '/' . $u['file'];
        $mod  = is_file($path) ? date('Y-m-d', filemtime($path)) : date('Y-m-d');
    }
?>
    <url>
        <loc><?= e(SITE_ORIGIN . '/' . $u['loc']) ?></loc>
        <lastmod><?= e($mod) ?></lastmod>
        <changefreq><?= e($u['freq']) ?></changefreq>
        <priority><?= e($u['pri']) ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
