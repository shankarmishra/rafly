<?php
/**
 * LinksRepository — internal-link resolution shared by blog-post.php,
 * service.php and case-studies.php.
 *
 * Before this, each of those either had no cross-links at all or would have
 * needed its own ad-hoc query to find one. This is one place that decides
 * "which service does this article belong to" and "which case studies used
 * this service", so the three pages agree instead of drifting.
 *
 * Same contract as every other repository (services_all(), bundles_all()):
 * degrade to an empty result when the database is unavailable, never fatal.
 */

/**
 * Blog category slug => service slug, for the ONE thing this file resolves
 * that isn't a plain query: which service an article is "about".
 *
 * Deliberately short and explicit rather than a fuzzy text match ("does the
 * category name contain a service name") — a fuzzy match eventually produces
 * a confident-looking wrong answer, and a wrong internal link actively
 * misleads a reader in a way no link does not. A category with no honest
 * match — Strategy is broad enough to fit more than one service, Automation
 * is not a service Rafly offers yet (see index.php's "actively building
 * out") — is simply absent here, and both functions below return nothing
 * for it rather than guessing.
 */
const CATEGORY_TO_SERVICE = [
    // 'web-security' / 'e-commerce' are what a CLEAN production database
    // actually has: inc/tools/seed-posts.php slugifies each post's `tag`
    // into its category (the same slugify() the old one-time migration
    // backfill used), and the tags are literally "Web Security" and
    // "E-commerce" — see inc/data/seed-posts.php. This is the pairing that
    // matters for a real deploy.
    'web-security' => 'web-security',
    'e-commerce'   => 'ecommerce-support',

    // 'security' is the SAMPLE category from inc/data/seed-preview.php
    // (inc/tools/seed-preview.php — explicitly not meant to survive into
    // production, see that file's own warning). Mapped too so local
    // dev/preview, which runs both seeders, resolves the same way a clean
    // production database does. 'e-commerce' needs no separate preview
    // entry — both seeders slugify to the identical string.
    'security'     => 'web-security',
];

/**
 * The one service most relevant to a set of article categories, or null when
 * none of them map to a real service.
 *
 * Only the first match in CATEGORY_TO_SERVICE's own order is used — an
 * article filed under two mapped categories would otherwise have to pick one
 * arbitrarily anyway, so the map decides instead of the call site.
 *
 * @param array<int,array{slug:string}> $categories
 */
function related_service_for_categories(array $categories): ?array
{
    foreach ($categories as $cat) {
        $slug = CATEGORY_TO_SERVICE[(string)($cat['slug'] ?? '')] ?? null;
        if ($slug !== null) {
            return service_find($slug);
        }
    }
    return null;
}

/**
 * Up to $limit published articles whose categories map to $serviceSlug — the
 * inverse of related_service_for_categories(), for service.php's "further
 * reading" block.
 *
 * @return list<array<string,mixed>>
 */
function related_articles_for_service(string $serviceSlug, int $limit = 3): array
{
    $categorySlugs = array_keys(array_filter(
        CATEGORY_TO_SERVICE,
        static fn(string $s): bool => $s === $serviceSlug
    ));

    if (!$categorySlugs) {
        return [];
    }

    if (!db_available()) {
        // Same seed_preview_posts() + category_slug filter blog-post.php's own
        // related-reading block falls back to when the database is down — a
        // dead database should not empty this section any more than it empties
        // that one.
        if (!seed_preview_enabled()) {
            return [];
        }
        $rows = array_values(array_filter(
            seed_preview_posts(),
            static fn(array $p): bool => in_array($p['category_slug'] ?? null, $categorySlugs, true)
        ));
        usort($rows, static fn(array $a, array $b): int => strcmp((string)$b['published_at'], (string)$a['published_at']));
        return array_slice($rows, 0, max(0, $limit));
    }

    $in = implode(', ', array_fill(0, count($categorySlugs), '?'));

    return all(
        'SELECT DISTINCT p.slug, p.title, p.excerpt, p.read_minutes
           FROM posts p
           JOIN post_categories pc ON pc.post_id = p.id
           JOIN categories c ON c.id = pc.category_id
          WHERE c.slug IN (' . $in . ')
            AND p.status = \'published\'
            AND p.published_at IS NOT NULL
            AND p.published_at <= now()
       ORDER BY p.published_at DESC
          LIMIT ' . max(0, $limit),
        $categorySlugs
    );
}

/**
 * Up to $limit published, non-placeholder case studies whose tags mention
 * this service by its display title.
 *
 * case_studies.tags is admin-typed free text ("Web Development, SEO,
 * Content"), not a foreign key, so this matches on the service's own label
 * rather than joining on anything — the same string case-studies.php already
 * splits and renders as chips.
 *
 * 'index' on each returned row is its position in the SAME unlimited,
 * identically-ordered case_studies_all() call case-studies.php itself
 * renders from, which is what makes '#case-study-' . $row['index'] a real,
 * stable anchor into that page rather than a guess.
 *
 * @return list<array<string,mixed>>
 */
function related_case_studies_for_service(string $serviceTitle, int $limit = 2): array
{
    // Case-insensitive: an admin typing "Web development" for the tag and
    // SERVICES declaring "Web Development" as the title are the same real
    // service, not a near-miss worth losing the link over. Still an exact
    // match on the normalised string, not a substring or fuzzy one — "Web
    // Security" must not casually match a tag that merely contains "Security".
    $needle = mb_strtolower($serviceTitle, 'UTF-8');

    $out = [];
    foreach (case_studies_all() as $i => $cs) {
        $tags = array_map(
            static fn(string $t): string => mb_strtolower(trim($t), 'UTF-8'),
            explode(',', (string)($cs['tags'] ?? ''))
        );
        if (in_array($needle, $tags, true)) {
            $out[] = $cs + ['index' => $i];
            if (count($out) >= $limit) {
                break;
            }
        }
    }
    return $out;
}
