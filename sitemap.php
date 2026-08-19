<?php
/**
 * XML sitemap, generated rather than hand-maintained.
 *
 * The five service URLs are the reason this exists: they are only reachable
 * through a nav dropdown, they differ only by query string, and until the
 * canonical bug was fixed they all claimed to be the same page. Listing them
 * explicitly is what gets them crawled as five distinct pages.
 *
 * Served at /sitemap.xml via a rewrite in .htaccess. The actual URL list
 * lives in inc/sitemap.php's sitemap_urls() — this file only renders it —
 * because inc/tools/indexnow.php needs the exact same list to know what it
 * is allowed to submit.
 */

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/sitemap.php';

header('Content-Type: application/xml; charset=UTF-8');

$urls = sitemap_urls();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($urls as $u): ?>
    <url>
        <loc><?= e(SITE_ORIGIN . '/' . $u['loc']) ?></loc>
        <lastmod><?= e($u['mod']) ?></lastmod>
        <changefreq><?= e($u['freq']) ?></changefreq>
        <priority><?= e($u['pri']) ?></priority>
<?php foreach ($u['images'] as $img): ?>
        <image:image>
            <image:loc><?= e(SITE_ORIGIN . '/' . $img['loc']) ?></image:loc>
            <image:title><?= e($img['title']) ?></image:title>
        </image:image>
<?php endforeach; ?>
    </url>
<?php endforeach; ?>
</urlset>
