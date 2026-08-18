<?php
/**
 * Blog index — the article listing, with topic filters and search.
 *
 * Was insights.php. The section is renamed, not rebuilt: the same posts table,
 * and .htaccess 301s every old /insights URL here so the existing search
 * ranking follows.
 */

require __DIR__ . '/inc/bootstrap.php';

$page = [
    'id'        => 'blog',
    'title'     => 'Blog | Rafly Digital Growth',
    'desc'      => 'Notes on bundled digital growth, web security, content, marketing, and the systems we build behind every package.',
    'bodyClass' => 'page-blog',
    'styles'    => ['home', 'blog'],
    'module'    => 'stage3d',
];

/**
 * Filters come off the query string, so both are request data and neither is
 * trusted. The category is resolved to a row before it is used — an unknown
 * slug is simply "no filter" rather than an error page, because a stale link
 * from a deleted category should still land somebody on the blog. The search
 * term is bound, never interpolated.
 */
$categorySlug = trim((string)($_GET['category'] ?? ''));
$q            = trim((string)($_GET['q'] ?? ''));
if (mb_strlen($q) > 80) {
    $q = mb_substr($q, 0, 80);
}

$categories     = [];
$activeCategory = null;
$posts          = [];

if (db_available()) {
    // Only categories that actually have something published in them. A chip
    // that leads to an empty list is worse than no chip.
    $categories = all(
        "SELECT c.id, c.slug, c.name, c.description,
                count(pc.post_id) AS post_count
           FROM categories c
           JOIN post_categories pc ON pc.category_id = c.id
           JOIN posts p            ON p.id = pc.post_id
          WHERE p.status = 'published'
            AND p.published_at IS NOT NULL
            AND p.published_at <= now()
       GROUP BY c.id, c.slug, c.name, c.description, c.sort_order
         HAVING count(pc.post_id) > 0
       ORDER BY c.sort_order ASC, c.name ASC"
    );

    if ($categorySlug !== '') {
        foreach ($categories as $c) {
            if ((string)$c['slug'] === $categorySlug) {
                $activeCategory = $c;
                break;
            }
        }
    }

    /**
     * One query, assembled from fixed fragments with every value bound.
     * The clauses are appended as literals chosen by PHP control flow; the only
     * things that reach the statement as data are $params, in order.
     */
    $where  = ["p.status = 'published'", 'p.published_at IS NOT NULL', 'p.published_at <= now()'];
    $params = [];
    $join   = '';

    if ($activeCategory !== null) {
        $join     = ' JOIN post_categories pcf ON pcf.post_id = p.id AND pcf.category_id = ?';
        $params[] = (int)$activeCategory['id'];
    }

    if ($q !== '') {
        // sql_ilike() picks ILIKE on PostgreSQL and LIKE on MySQL, whose default
        // collation is already case-insensitive.
        $like     = sql_ilike();
        $where[]  = '(p.title ' . $like . ' ? OR p.excerpt ' . $like . ' ? OR p.body ' . $like . ' ?)';
        $needle   = '%' . $q . '%';
        $params[] = $needle;
        $params[] = $needle;
        $params[] = $needle;
    }

    /* The card badge shows the article's first category, falling back to the
       legacy tag for anything written before categories existed. A correlated
       subquery rather than another JOIN: a post can hold several categories,
       and joining would multiply the rows and need a GROUP BY over every
       selected column to put them back. */
    $posts = all(
        'SELECT p.id, p.slug, p.title, p.tag, p.excerpt, p.published_at, p.read_minutes,
                m.filename AS cover, m.alt AS cover_alt,
                t.name AS author_name, t.role AS author_role,
                am.filename AS author_photo,
                (SELECT c2.name
                   FROM post_categories pc2
                   JOIN categories c2 ON c2.id = pc2.category_id
                  WHERE pc2.post_id = p.id
               ORDER BY c2.sort_order ASC, c2.name ASC
                  LIMIT 1) AS category_name
           FROM posts p'
        . $join . '
           LEFT JOIN media        m  ON m.id  = p.cover_media_id
           LEFT JOIN team_members t  ON t.id  = p.author_team_id
           LEFT JOIN media        am ON am.id = t.photo_media_id
          WHERE ' . implode(' AND ', $where) . '
       ORDER BY p.published_at DESC',
        $params
    );
} elseif (seed_preview_enabled()) {
    // Design preview with no database: the sample posts (inc/repo/seed.php),
    // filtered the same way the query above filters.
    $categories = seed_preview_categories();
    if ($categorySlug !== '') {
        foreach ($categories as $c) {
            if ((string)$c['slug'] === $categorySlug) { $activeCategory = $c; break; }
        }
    }
    $posts = array_values(array_filter(seed_preview_posts(), static function (array $p) use ($activeCategory, $q): bool {
        if ($activeCategory !== null && (int)$p['category_id'] !== (int)$activeCategory['id']) return false;
        if ($q !== '' && mb_stripos($p['title'] . ' ' . $p['excerpt'] . ' ' . strip_tags($p['body']), $q) === false) return false;
        return true;
    }));
}

/* Filtered and searched views are thin, near-duplicate pages as far as a
   crawler is concerned, so only the unfiltered index is indexed and only it
   claims the collection. The canonical points home from every filtered view. */
$isFiltered = $activeCategory !== null || $q !== '';

if ($isFiltered) {
    $page['noindex']   = true;
    $page['canonical'] = 'blog';

    if ($activeCategory !== null) {
        $page['title'] = $activeCategory['name'] . ' | Blog | ' . SITE_NAME;
        if ((string)$activeCategory['description'] !== '') {
            $page['desc'] = (string)$activeCategory['description'];
        }
    }
} elseif ($posts) {
    $page['schema'][] = schema_collection_list(
        'Blog',
        'blog',
        array_map(
            static fn(array $p): array => [
                'name' => $p['title'],
                'url'  => 'blog/' . rawurlencode((string)$p['slug']),
            ],
            $posts
        )
    );
}

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <section class="section page-head">
        <?php require __DIR__ . '/partials/head-object.php'; ?>
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Notes from the build</p>
                    <h1 class="display">The Rafly <span class="soft">blog.</span></h1>
                </div>
                <p class="lead">
                    Field notes on what is working, what is not, and what we are building next
                    &mdash; written by the people doing the work.
                </p>
            </div>
        </div>
    </section>

    <div class="section-bot">
        <div class="container">
            <?php /* GET, so a filtered view is a shareable URL and the back button
                     behaves. The category rides along in a hidden input, otherwise
                     searching would silently drop the topic the reader had chosen. */ ?>
            <div class="blog-toolbar">
                <form class="blog-search-form" method="get" action="<?= e(site_path('/blog')) ?>" role="search">
<?php if ($activeCategory !== null): ?>
                    <input type="hidden" name="category" value="<?= e((string)$activeCategory['slug']) ?>">
<?php endif; ?>
                    <label class="pill-input blog-search">
                        <span class="visually-hidden">Search articles</span>
                        <?= icon('search') ?>
                        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search articles"
                               maxlength="80" autocomplete="off">
                        <button type="submit" class="btn btn-pill btn-sm">Search</button>
                    </label>
                </form>
<?php if ($isFiltered): ?>
                <a class="link-arrow blog-clear" href="<?= e(site_path('/blog')) ?>">Clear filters <?= icon('x') ?></a>
<?php endif; ?>
            </div>

<?php if ($categories): ?>
            <nav class="cluster blog-chips" aria-label="Filter by topic">
                <a class="chip<?= $activeCategory === null ? ' is-active' : '' ?>"
                   href="<?= e(site_path('/blog' . ($q !== '' ? '?q=' . rawurlencode($q) : ''))) ?>"
                   <?= $activeCategory === null ? 'aria-current="true"' : '' ?>>All</a>
<?php foreach ($categories as $c):
                $isActive = $activeCategory !== null && (int)$c['id'] === (int)$activeCategory['id'];
                $href = '/blog?category=' . rawurlencode((string)$c['slug'])
                      . ($q !== '' ? '&q=' . rawurlencode($q) : ''); ?>
                <a class="chip<?= $isActive ? ' is-active' : '' ?>" href="<?= e(site_path($href)) ?>"
                   <?= $isActive ? 'aria-current="true"' : '' ?>>
                    <?= e((string)$c['name']) ?>
                    <span class="chip-count"><?= (int)$c['post_count'] ?></span>
                </a>
<?php endforeach; ?>
            </nav>
<?php endif; ?>

<?php if ($activeCategory !== null && (string)$activeCategory['description'] !== ''): ?>
            <p class="blog-category-intro"><?= e((string)$activeCategory['description']) ?></p>
<?php endif; ?>

<?php if ($isFiltered): ?>
            <p class="blog-result-count" role="status">
                <?= count($posts) === 1 ? '1 article' : count($posts) . ' articles' ?>
<?php if ($q !== ''): ?>
                matching &ldquo;<?= e($q) ?>&rdquo;
<?php endif; ?>
<?php if ($activeCategory !== null): ?>
                in <?= e((string)$activeCategory['name']) ?>
<?php endif; ?>
            </p>
<?php endif; ?>

<?php if ($posts): ?>
<?php
/* THE FEATURED STORY.
   On the unfiltered index the newest article is pulled out of the grid and
   given an editorial treatment. It is the same row the grid would have shown
   first — nothing is chosen by hand, and nothing is invented; the admin's
   publish order decides.

   Only on the unfiltered first view. A search or category result is a list the
   reader asked for, and promoting one of ITS rows to a hero would imply an
   editorial judgment that was never made. */
$featured = null;
if (!$isFiltered) {
    $featured = array_shift($posts);
}
if ($featured !== null):
    $fpub   = !empty($featured['published_at']) ? strtotime((string)$featured['published_at']) : null;
    $furl   = site_path('/blog/' . rawurlencode((string)$featured['slug']));
    $fbadge = (string)($featured['category_name'] ?? '') ?: (string)$featured['tag'];
?>
            <article class="blog-featured" data-fx="in-up" style="--travel: 52px;">
                <div class="blog-featured-visual">
<?php if ($featured['cover'] !== null): ?>
                    <img src="<?= e(site_path('/uploads/' . rawurlencode((string)$featured['cover']))) ?>"
                         alt="" width="960" height="540" loading="eager" decoding="async" fetchpriority="high">
<?php endif; ?>
                </div>
                <div class="blog-featured-body">
                    <div class="cluster">
                        <span class="badge badge-ink">Latest</span>
<?php if ($fbadge !== ''): ?>
                        <span class="badge badge-soft"><?= e($fbadge) ?></span>
<?php endif; ?>
                    </div>
                    <h2 class="blog-featured-title"><a href="<?= e($furl) ?>"><?= e((string)$featured['title']) ?></a></h2>
                    <p class="blog-featured-excerpt"><?= e(str_cut((string)$featured['excerpt'], 220)) ?></p>
                    <p class="blog-meta">
<?php if ($fpub !== null): ?>
                        <time datetime="<?= e(date(DATE_ATOM, $fpub)) ?>"><?= e(date('j M Y', $fpub)) ?></time>
                        <span aria-hidden="true">&middot;</span>
<?php endif; ?>
                        <span><?= (int)$featured['read_minutes'] ?> min read</span>
<?php if ($featured['author_name'] !== null && $featured['author_name'] !== ''): ?>
                        <span aria-hidden="true">&middot;</span>
                        <span><?= e((string)$featured['author_name']) ?></span>
<?php endif; ?>
                    </p>
                    <a href="<?= e($furl) ?>" class="link-arrow">Read the article <?= icon('arrow-right') ?></a>
                </div>
            </article>
<?php endif; ?>

<?php if ($posts): ?>
            <div class="grid grid-3 blog-grid" data-r="group">
<?php foreach ($posts as $i => $p):
                $pub   = !empty($p['published_at']) ? strtotime((string)$p['published_at']) : null;
                $url   = site_path('/blog/' . rawurlencode((string)$p['slug']));
                $badge = (string)($p['category_name'] ?? '') ?: (string)$p['tag'];
?>
                <article class="card card-hover blog-card">
                    <div class="card-media blog-card-media">
<?php if ($p['cover'] !== null): ?>
                        <img src="<?= e(site_path('/uploads/' . rawurlencode((string)$p['cover']))) ?>"
                             alt="<?= e((string)($p['cover_alt'] !== '' ? $p['cover_alt'] : $p['title'])) ?>"
                             width="640" height="360" loading="lazy" decoding="async">
<?php else: ?>
                        <?php /* No cover: a numbered plate rather than a grey box, so
                                 an article without an image still looks published. */ ?>
                        <span class="blog-card-plate" aria-hidden="true"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
<?php endif; ?>
                    </div>

                    <div class="card-body">
<?php if ($badge !== ''): ?>
                        <span class="badge badge-soft"><?= e($badge) ?></span>
<?php endif; ?>
                        <h2 class="card-title"><?= e((string)$p['title']) ?></h2>
                        <p class="card-text"><?= e(str_cut((string)$p['excerpt'], 150)) ?></p>

                        <p class="blog-meta">
<?php if ($pub !== null): ?>
                            <time datetime="<?= e(date(DATE_ATOM, $pub)) ?>"><?= e(date('j M Y', $pub)) ?></time>
                            <span aria-hidden="true">&middot;</span>
<?php endif; ?>
                            <span><?= (int)$p['read_minutes'] ?> min read</span>
                        </p>

                        <div class="card-foot">
<?php if ($p['author_name'] !== null && $p['author_name'] !== ''): ?>
                            <span class="person">
<?php if ($p['author_photo'] !== null): ?>
                                <span class="avatar avatar-sm"><img src="<?= e(site_path('/uploads/' . rawurlencode((string)$p['author_photo']))) ?>" alt="" width="28" height="28" loading="lazy" decoding="async"></span>
<?php else: ?>
                                <span class="avatar avatar-sm avatar-t<?= (int)(($i % 6) + 1) ?>" aria-hidden="true"><?= e(mb_substr((string)$p['author_name'], 0, 1)) ?></span>
<?php endif; ?>
                                <span class="person-name"><?= e((string)$p['author_name']) ?></span>
                            </span>
<?php else: ?>
                            <span class="tag">Rafly</span>
<?php endif; ?>
                            <?= icon('arrow-up-right') ?>
                        </div>
                    </div>

                    <?php /* A real link, so the card is keyboard reachable and
                             announces properly; .card-link stretches it. */ ?>
                    <a class="card-link" href="<?= e($url) ?>" aria-label="<?= e((string)$p['title']) ?>"></a>
                </article>
<?php endforeach; ?>
            </div>
<?php endif; ?>
<?php elseif ($isFiltered): ?>
            <p class="blog-empty">
                Nothing here yet. <a href="<?= e(site_path('/blog')) ?>">Show every article</a>,
                or <a href="<?= e(whatsapp_link('Hi Rafly team, I was looking for something on your blog and could not find it.')) ?>"
                      target="_blank" rel="noopener">ask us directly</a>.
            </p>
<?php else: ?>
            <p class="blog-empty">
                New notes are on the way &mdash;
                <a href="<?= e(whatsapp_link('Hi Rafly team, I would like to hear about your work.')) ?>"
                   target="_blank" rel="noopener">message us</a>
                in the meantime and we will answer directly.
            </p>
<?php endif; ?>
        </div>
    </div>

    <?php /* Kept from the original Insights page, moved below the articles. */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="grid grid-2" data-r="group">
                <div class="panel panel-line">
                    <h2>Our operational core</h2>
                    <p>Rafly runs every engagement through one coordinated team instead of scattered vendors. We plan, build, and review website, content, marketing, and e-commerce work together, so nothing falls through the cracks between departments that don't talk to each other.</p>
                    <p>Every project includes a baseline security pass on forms, sessions, and access handling &mdash; whether or not it was the reason you called us.</p>
                </div>
                <div class="panel panel-line">
                    <h2>Engineering &amp; solution teams</h2>
                    <p>Our specialists cover web architecture, content strategy, paid and organic marketing, and e-commerce operations &mdash; briefed on the same goals, working from the same plan.</p>
                    <p>Work is scoped in clear bundled packages with defined deliverables, so progress is easy to track and there's no ambiguity about what's included.</p>
                </div>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Rather talk than read?';
    $ctaTitle   = 'Ask us the question directly.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
