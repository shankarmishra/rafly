<?php
/**
 * The list of URLs this site wants indexed — every indexable route, with its
 * lastmod, change frequency, priority and (where genuinely captioned) images.
 *
 * sitemap.php renders this as XML. inc/tools/indexnow.php submits the same
 * list's 'loc' values to Bing/IndexNow. One function, so the sitemap and the
 * IndexNow submitter can never disagree about which URLs this site considers
 * canonical and indexable — the exact bug class SERVICES (inc/config.php)
 * exists to prevent for the nav, the footer and service.php.
 */

/**
 * Photos worth a search engine indexing on their own.
 *
 * EMPTY, and deliberately so.
 *
 * This used to list five service photographs with rich captions. All five
 * were CC0 stock of other companies' premises — a conference room, a server
 * rack, a warehouse — captioned as though they were Rafly's own work and
 * submitted to Google Images under Rafly's URLs. They were removed with the
 * other nineteen in the Machined Paper rebuild.
 *
 * What replaced them is a rendered object that carries alt="" because it is
 * decorative: it illustrates the proposition, it does not document anything.
 * An <image:image> entry for a decorative image is noise Google Images has no
 * caption to show for, so there is nothing here to declare.
 *
 * The shape stays rather than the feature being deleted. When Rafly has real
 * photography — its own team, its own premises, a client's store it actually
 * built — this is where those go, and nothing downstream has to change.
 */
const SITEMAP_SERVICE_IMAGES = [];

/**
 * Every indexable URL, resolved and ready to render or submit.
 *
 * @return list<array{loc:string,mod:string,freq:string,pri:string,images:list<array{loc:string,title:string}>}>
 */
function sitemap_urls(): array
{
    $root = dirname(__DIR__);

    /**
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

    $entries = [
        // 'loc' is the clean-URL path; 'file' stays the underlying .php for
        // the mtime lookup below — the two were already decoupled before
        // clean URLs existed, so this is just giving 'loc' a different value.
        //
        // 'images' resolves to nothing today: SITEMAP_SERVICE_IMAGES is empty
        // and the map below is what will fill it again when there is real
        // photography to declare. See the note on that constant.
        ['loc' => '', 'file' => 'index.php', 'freq' => 'weekly', 'pri' => '1.0', 'images' => array_map(
            static fn(array $img): array => ['loc' => 'assets/photos/' . $img['file'], 'title' => $img['title']],
            SITEMAP_SERVICE_IMAGES
        )],
        ['loc' => 'about',    'file' => 'about.php',    'freq' => 'monthly', 'pri' => '0.8'],
        ['loc' => 'pricing',      'file' => 'pricing.php',      'freq' => 'monthly', 'pri' => '0.9']
            + ($pricingMod ? ['mod' => date('Y-m-d', strtotime((string)$pricingMod))] : []),
        ['loc' => 'case-studies', 'file' => 'case-studies.php', 'freq' => 'monthly', 'pri' => '0.7']
            + ($caseStudiesMod ? ['mod' => date('Y-m-d', strtotime((string)$caseStudiesMod))] : []),
        ['loc' => 'blog',     'file' => 'blog.php',     'freq' => 'weekly',  'pri' => '0.7']
            + ($blogMod ? ['mod' => date('Y-m-d', strtotime((string)$blogMod))] : []),
        ['loc' => 'team',     'file' => 'team.php',     'freq' => 'monthly', 'pri' => '0.6'],
        ['loc' => 'contact',  'file' => 'contact.php',  'freq' => 'monthly', 'pri' => '0.8'],
        ['loc' => 'privacy',  'file' => 'privacy.php',  'freq' => 'yearly',  'pri' => '0.3'],
        ['loc' => 'locations/greater-noida', 'file' => 'locations/greater-noida.php', 'freq' => 'monthly', 'pri' => '0.7'],
    ];

    // One entry per service, driven by the same SERVICES array that builds
    // the nav, each carrying its own photo — the one that actually appears
    // on that page (service.php's hero), not the whole image set above.
    foreach (array_keys(SERVICES) as $slug) {
        $img = SITEMAP_SERVICE_IMAGES[$slug] ?? null;
        $entries[] = [
            'loc'  => $slug,
            'file' => 'service.php',
            'freq' => 'monthly',
            'pri'  => '0.9',
            'images' => $img ? [['loc' => 'assets/photos/' . $img['file'], 'title' => $img['title']]] : [],
        ];
    }

    /**
     * One entry per published article. These carry an explicit 'mod' because
     * a post's real last-modified date lives in the database —
     * blog-post.php's own mtime is the same for every article and would tell
     * a crawler that all of them changed whenever the template did.
     *
     * Guarded by db_available(): this is the data most likely to be read
     * while the database is down (a crawler or the IndexNow submitter does
     * not retry politely), and a fatal here would drop the static URLs too.
     */
    $posts = [];
    if (db_available()) {
        $posts = all(
            "SELECT p.slug, p.title, p.published_at, p.updated_at, m.filename AS cover, m.alt AS cover_alt
               FROM posts p
               LEFT JOIN media m ON m.id = p.cover_media_id
              WHERE p.status = 'published'
                AND p.published_at IS NOT NULL
                AND p.published_at <= now()
           ORDER BY p.published_at DESC"
        );
    } elseif (seed_preview_enabled()) {
        $posts = seed_preview_posts();      // design preview only, see inc/repo/seed.php
    }
    foreach ($posts as $p) {
        $entries[] = [
            'loc'  => 'blog/' . rawurlencode((string)$p['slug']),
            'mod'  => date('Y-m-d', strtotime((string)($p['updated_at'] ?: $p['published_at']))),
            'freq' => 'monthly',
            'pri'  => '0.6',
            // The exact cover blog-post.php itself renders — real per-article
            // art, not a placeholder — with its own admin-entered alt text,
            // falling back to the headline the same way blog-post.php's
            // <img> tag already does.
            'images' => !empty($p['cover']) ? [[
                'loc'   => 'uploads/' . rawurlencode((string)$p['cover']),
                'title' => (string)($p['cover_alt'] ?: $p['title']),
            ]] : [],
        ];
    }

    // Resolve 'mod' to a final date string here, once, rather than making
    // every consumer repeat the "explicit mod wins, else the file's own
    // mtime" fallback sitemap.php used to inline in its own render loop.
    foreach ($entries as &$entry) {
        if (!isset($entry['mod'])) {
            $path = $root . '/' . $entry['file'];
            $entry['mod'] = is_file($path) ? date('Y-m-d', filemtime($path)) : date('Y-m-d');
        }
        unset($entry['file']);
        $entry['images'] ??= [];
    }
    unset($entry);

    return $entries;
}
