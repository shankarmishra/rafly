<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * A single article.
 *
 * Was insight.php. Renamed with the section; the row, the slug and therefore
 * the article's identity are unchanged, and .htaccess 301s /insights/{slug} to
 * /blog/{slug} so nothing that already links here breaks.
 *
 * Bodies are sanitised by sanitize_html() on save (admin/posts.php) AND AGAIN
 * ON RENDER below. The save-time pass is not sufficient on its own: it is not
 * the only writer — inc/tools/seed-posts.php inserts bodies untouched — and
 * rows can predate the allow-list. Sanitising on read is the layer that holds
 * regardless of how a row arrived, and it is idempotent, so it costs a parse and
 * nothing else.
 */

$slug = trim((string)($_GET['post'] ?? ''));

/**
 * Resolve the post before emitting a single byte, because a miss has to be able
 * to send a 404 status and swap in the whole 404 page. Anything printed first
 * would lock in a 200.
 *
 * status/published_at are both checked: a post scheduled for the future is not
 * public yet, and 'published' alone would leak it.
 */
$post = null;

if ($slug !== '' && db_available()) {
    $post = one(
        "SELECT p.id, p.slug, p.title, p.tag, p.excerpt, p.meta_desc, p.body,
                p.published_at, p.updated_at, p.read_minutes,
                m.filename AS cover, m.alt AS cover_alt,
                t.id AS author_id, t.name AS author_name, t.role AS author_role,
                am.filename AS author_photo
           FROM posts p
           LEFT JOIN media        m  ON m.id  = p.cover_media_id
           LEFT JOIN team_members t  ON t.id  = p.author_team_id
           LEFT JOIN media        am ON am.id = t.photo_media_id
          WHERE p.slug = ?
            AND p.status = 'published'
            AND p.published_at IS NOT NULL
            AND p.published_at <= now()",
        [$slug]
    );
} elseif ($slug !== '' && seed_preview_enabled()) {
    // Design preview with no database (inc/repo/seed.php).
    foreach (seed_preview_posts() as $sp) {
        if ($sp['slug'] === $slug) { $post = $sp; break; }
    }
}
$seeded = $post !== null && !db_available();

if ($post === null) {
    // Reuse the real 404 page rather than printing a bespoke "not found" here,
    // so a mistyped slug looks identical to any other dead URL.
    require __DIR__ . '/404.php';
    exit;
}

$published = !empty($post['published_at']) ? strtotime((string)$post['published_at']) : null;

// This article's topics, for the header chips and for finding related reading.
$cats = $seeded
    ? ($post['category_id'] !== null ? [['id' => $post['category_id'], 'slug' => $post['category_slug'], 'name' => $post['category_name']]] : [])
    : all(
        'SELECT c.id, c.slug, c.name
           FROM categories c
           JOIN post_categories pc ON pc.category_id = c.id
          WHERE pc.post_id = ?
       ORDER BY c.sort_order ASC, c.name ASC',
        [(int)$post['id']]
    );

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Blog', 'url' => '/blog'],
    ['name' => (string)$post['title'], 'url' => '/blog/' . rawurlencode((string)$post['slug'])],
];

$page = [
    'id'        => 'blog',
    'title'     => $post['title'] . ' | ' . SITE_NAME,
    'desc'      => (string)($post['meta_desc'] ?: $post['excerpt']),
    'bodyClass' => 'page-blog-post',
    'styles'    => ['blog', 'blog-post'],
    'ogType'    => 'article',

    // The content varies by slug, so the canonical must be set explicitly —
    // otherwise every article claims to be /blog/{first-one-crawled} and
    // Google indexes one while dropping the rest.
    'canonical' => 'blog/' . rawurlencode((string)$post['slug']),

    'articleMeta' => [
        'published' => $post['published_at'],
        'modified'  => $post['updated_at'] ?: $post['published_at'],
        // The first category is the article's section; falls back to the legacy
        // tag for anything written before categories existed.
        'section'   => $cats[0]['name'] ?? ($post['tag'] ?: null),
    ],

    'schema'    => [
        schema_article($post),
        schema_breadcrumbs($crumbs),
    ],
];

// A cover image is a far better share preview than the generic site card.
if ($post['cover'] !== null) {
    $page['ogImage'] = 'uploads/' . rawurlencode((string)$post['cover']);
}

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';

/**
 * Related reading: up to three other published posts, most shared categories
 * first, then most recent. Pulled before the article renders so a database
 * hiccup half-way down the page cannot leave a half-written document.
 *
 * When this article has no categories the JOIN would match nothing, so the
 * fallback is the previous behaviour — simply the three newest.
 */
if ($seeded) {
    // Preview: the other sample posts, same category first, newest first.
    $others = array_values(array_filter(seed_preview_posts(), static fn (array $o): bool => $o['slug'] !== $post['slug']));
    usort($others, static function (array $a, array $b) use ($post): int {
        $sa = (int)($a['category_id'] === $post['category_id']); $sb = (int)($b['category_id'] === $post['category_id']);
        return $sb <=> $sa ?: strcmp($b['published_at'], $a['published_at']);
    });
    $more = array_slice($others, 0, 3);
} elseif ($cats) {
    $catIds = array_map('intval', array_column($cats, 'id'));
    $in     = implode(', ', array_fill(0, count($catIds), '?'));

    $more = all(
        'SELECT p.slug, p.title, p.tag, p.excerpt, p.read_minutes,
                m.filename AS cover, m.alt AS cover_alt,
                (SELECT c2.name
                   FROM post_categories pc2
                   JOIN categories c2 ON c2.id = pc2.category_id
                  WHERE pc2.post_id = p.id
               ORDER BY c2.sort_order ASC, c2.name ASC
                  LIMIT 1) AS category_name,
                count(pc.category_id) AS shared
           FROM posts p
           JOIN post_categories pc ON pc.post_id = p.id AND pc.category_id IN (' . $in . ')
           LEFT JOIN media m ON m.id = p.cover_media_id
          WHERE p.status = \'published\'
            AND p.published_at IS NOT NULL
            AND p.published_at <= now()
            AND p.id <> ?
       GROUP BY p.id, p.slug, p.title, p.tag, p.excerpt, p.read_minutes,
                p.published_at, m.filename, m.alt
       ORDER BY shared DESC, p.published_at DESC
          LIMIT 3',
        [...$catIds, (int)$post['id']]
    );
} else {
    $more = [];
}

// The one service this article is actually about, if its categories map to
// one — inc/repo/links.php. $cats carries 'slug' in both branches above (the
// DB query selects c.slug; seed_preview_posts() sets category_slug the same
// way), so this resolves identically whether the database is up or not.
$relatedService = $cats ? related_service_for_categories($cats) : null;

if (!$more && !$seeded) {
    $more = all(
        "SELECT p.slug, p.title, p.tag, p.excerpt, p.read_minutes,
                m.filename AS cover, m.alt AS cover_alt,
                (SELECT c2.name
                   FROM post_categories pc2
                   JOIN categories c2 ON c2.id = pc2.category_id
                  WHERE pc2.post_id = p.id
               ORDER BY c2.sort_order ASC, c2.name ASC
                  LIMIT 1) AS category_name
           FROM posts p
           LEFT JOIN media m ON m.id = p.cover_media_id
          WHERE p.status = 'published'
            AND p.published_at IS NOT NULL
            AND p.published_at <= now()
            AND p.id <> ?
       ORDER BY p.published_at DESC
          LIMIT 3",
        [(int)$post['id']]
    );
}
?>
<main id="main" class="post-main">

    <article class="post">
        <div class="container container-narrow">
            <?= breadcrumbs($crumbs) ?>

            <header class="post-header">
<?php if ($cats): ?>
                <nav class="cluster post-topics" aria-label="Topics">
<?php foreach ($cats as $c): ?>
                    <a class="chip" href="<?= e(site_path('/blog?category=' . rawurlencode((string)$c['slug']))) ?>"><?= e((string)$c['name']) ?></a>
<?php endforeach; ?>
                </nav>
<?php elseif (!empty($post['tag'])): ?>
                <span class="badge badge-soft"><?= e((string)$post['tag']) ?></span>
<?php endif; ?>

                <h1 class="display post-title"><?= e((string)$post['title']) ?></h1>

<?php if (!empty($post['excerpt'])): ?>
                <p class="lead post-standfirst"><?= e((string)$post['excerpt']) ?></p>
<?php endif; ?>

                <div class="post-byline">
<?php if ($post['author_name'] !== null && $post['author_name'] !== ''): ?>
                    <span class="person">
<?php if ($post['author_photo'] !== null): ?>
                        <span class="avatar"><img src="<?= e(site_path('/uploads/' . rawurlencode((string)$post['author_photo']))) ?>" alt="" width="44" height="44" loading="lazy" decoding="async"></span>
<?php else: ?>
                        <span class="avatar avatar-t1" aria-hidden="true"><?= e(mb_substr((string)$post['author_name'], 0, 1)) ?></span>
<?php endif; ?>
                        <span>
                            <span class="person-name"><?= e((string)$post['author_name']) ?></span><br>
<?php if ((string)$post['author_role'] !== ''): ?>
                            <span class="person-role"><?= e((string)$post['author_role']) ?></span>
<?php endif; ?>
                        </span>
                    </span>
<?php endif; ?>
                    <p class="blog-meta">
<?php if ($published !== null): ?>
                        <time datetime="<?= e(date(DATE_ATOM, $published)) ?>"><?= e(date('j F Y', $published)) ?></time>
<?php endif; ?>
<?php if ($published !== null && (int)$post['read_minutes'] > 0): ?>
                        <span aria-hidden="true">&middot;</span>
<?php endif; ?>
<?php if ((int)$post['read_minutes'] > 0): ?>
                        <span><?= (int)$post['read_minutes'] ?> min read</span>
<?php endif; ?>
                    </p>
                </div>
            </header>
        </div>

<?php if ($post['cover'] !== null): ?>
        <?php /* Eager, and with no loading="lazy": this is the largest element
                 above the fold on the article, so deferring it would push the
                 Largest Contentful Paint out rather than in. */ ?>
        <div class="container">
            <figure class="figure figure-wide post-cover">
                <img src="<?= e(site_path('/uploads/' . rawurlencode((string)$post['cover']))) ?>"
                     alt="<?= e((string)($post['cover_alt'] !== '' ? $post['cover_alt'] : $post['title'])) ?>"
                     width="1200" height="675" decoding="async" fetchpriority="high">
            </figure>
        </div>
<?php endif; ?>

        <div class="container container-narrow">
            <?php
            /* Sanitised HERE, at render, not merely trusted from the save path.

               admin/posts.php does call sanitize_html() on save, but that is not
               the only way a row gets into this table: inc/tools/seed-posts.php
               inserts bodies with no sanitiser at all, and inc/sanitize.php notes
               that rows may predate the allow-list entirely. Relying on "every
               writer sanitises" means this render breaks the day someone adds a
               sixth writer.

               sanitize_html() is idempotent — already-clean markup passes through
               unchanged — so running it again here costs one parse and makes the
               render site correct independent of every write path, present and
               future. e() would be wrong: it would print the markup as visible
               text instead of rendering it. */
            ?>
            <div class="prose post-body"><?= sanitize_html((string)$post['body']) ?></div>

            <aside class="post-cta">
                <h2>Want this handled for you?</h2>
                <p>
<?php if ($relatedService !== null): ?>
                    This is exactly what our <a href="/<?= e($relatedService['slug']) ?>"><?= e($relatedService['title']) ?></a>
                    service covers &mdash; or get it as part of a bundled package with web, content,
                    marketing and e-commerce support under one team.
<?php else: ?>
                    Every Rafly package bundles web, content, marketing, security and
                    e-commerce support under one team &mdash; so the things in this article
                    actually get done rather than added to a list.
<?php endif; ?>
                </p>
                <div class="cluster">
                    <button type="button" class="btn btn-pill" data-modal-open="consultationModal">
                        Get a free consultation <?= icon('arrow-right') ?>
                    </button>
                    <a class="btn btn-line" target="_blank" rel="noopener"
                       href="<?= e(whatsapp_link('Hi Rafly team, I just read "' . $post['title'] . '" and would like to know more.')) ?>">
                        <?= icon('whatsapp', 'icon-fill') ?> Message us
                    </a>
                </div>
            </aside>
        </div>
    </article>

<?php if ($more): ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow"><?= $cats ? 'Related reading' : 'More from the team' ?></p>
                    <h2>Keep going</h2>
                </div>
                <p class="lead"><a class="link-arrow" href="/blog">Every article <?= icon('arrow-right') ?></a></p>
            </div>

            <div class="grid grid-3 blog-grid" data-r="group">
<?php foreach ($more as $i => $m):
                $murl  = site_path('/blog/' . rawurlencode((string)$m['slug']));
                $badge = (string)($m['category_name'] ?? '') ?: (string)$m['tag'];
?>
                <article class="card card-hover blog-card">
                    <div class="card-media blog-card-media">
<?php if ($m['cover'] !== null): ?>
                        <img src="<?= e(site_path('/uploads/' . rawurlencode((string)$m['cover']))) ?>"
                             alt="<?= e((string)($m['cover_alt'] !== '' ? $m['cover_alt'] : $m['title'])) ?>"
                             width="640" height="360" loading="lazy" decoding="async">
<?php else: ?>
                        <span class="blog-card-plate" aria-hidden="true"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
<?php endif; ?>
                    </div>
                    <div class="card-body">
<?php if ($badge !== ''): ?>
                        <span class="badge badge-soft"><?= e($badge) ?></span>
<?php endif; ?>
                        <h3 class="card-title"><?= e((string)$m['title']) ?></h3>
                        <p class="card-text"><?= e(str_cut((string)$m['excerpt'], 130)) ?></p>
                        <div class="card-foot">
                            <span class="blog-meta"><?= (int)$m['read_minutes'] ?> min read</span>
                            <?= icon('arrow-up-right') ?>
                        </div>
                    </div>
                    <a class="card-link" href="<?= e($murl) ?>" aria-label="<?= e((string)$m['title']) ?>"></a>
                </article>
<?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
