<?php
/**
 * PREVIEW SEED accessors.
 *
 * The public site reads its content through repositories (inc/repo/*.php) and
 * two inline queries (blog.php, blog-post.php). Each of those has exactly one
 * "no database" branch. This file gives that branch a second answer: when
 * PREVIEW_SEED is on, return the sample rows from inc/data/seed-preview.php
 * in the same shape the query would have produced.
 *
 * It is a preview device, not a content source:
 *   - it is only consulted when db_available() is FALSE, so a server with a
 *     database never sees it, whatever the constant says;
 *   - PREVIEW_SEED defaults to false (inc/config.php) and is switched on in
 *     the git-ignored inc/config.local.php only;
 *   - the rows are labelled sample content in the data file itself.
 */

function seed_preview_enabled(): bool
{
    return defined('PREVIEW_SEED') && PREVIEW_SEED === true && !db_available();
}

/** @return array<string, mixed> the whole seed, memoized. */
function seed_preview_all(): array
{
    static $seed = null;
    if ($seed === null) {
        $seed = require dirname(__DIR__) . '/data/seed-preview.php';
    }
    return $seed;
}

/** @return list<array<string, mixed>> */
function seed_preview(string $key): array
{
    if (!seed_preview_enabled()) {
        return [];
    }
    $all = seed_preview_all();
    return array_values($all[$key] ?? []);
}

/** A settings value from the seed, or null. */
function seed_preview_setting(string $key): ?string
{
    if (!seed_preview_enabled()) {
        return null;
    }
    $s = seed_preview_all()['settings'] ?? [];
    return array_key_exists($key, $s) ? (string)$s[$key] : null;
}

/**
 * The seed posts, in the exact column set blog.php / blog-post.php select:
 * id, slug, title, tag, excerpt, meta_desc, body, published_at, updated_at,
 * read_minutes, cover, cover_alt, author_id, author_name, author_role,
 * author_photo, category_name, category_slug. Newest first.
 *
 * @return list<array<string, mixed>>
 */
function seed_preview_posts(): array
{
    static $rows = null;
    if ($rows !== null) {
        return $rows;
    }
    if (!seed_preview_enabled()) {
        return $rows = [];
    }
    $seed = seed_preview_all();
    $meta = $seed['posts_meta'] ?? [];
    $team = [];
    foreach ($seed['team_members'] ?? [] as $t) {
        $team[(int)$t['id']] = $t;
    }
    $cats = [];
    foreach ($seed['categories'] ?? [] as $c) {
        $cats[$c['slug']] = $c;
    }

    $rows = [];
    foreach (require dirname(__DIR__) . '/data/seed-posts.php' as $p) {
        $m = $meta[$p['slug']] ?? null;
        if ($m === null) {
            continue;
        }
        $author = $team[(int)($m['author'] ?? 0)] ?? null;
        $cat    = $cats[$m['category'] ?? ''] ?? null;
        $words  = str_word_count(strip_tags($p['body']));
        $rows[] = [
            'id'            => (int)$m['id'],
            'slug'          => $p['slug'],
            'title'         => $p['title'],
            'tag'           => $p['tag'],
            'excerpt'       => $p['excerpt'],
            'meta_desc'     => $p['meta_desc'],
            'body'          => $p['body'],
            'published_at'  => $m['published_at'],
            'updated_at'    => $m['published_at'],
            'read_minutes'  => max(1, min(60, (int)ceil($words / 200))),
            'cover'         => $m['cover'] ?? null,
            'cover_alt'     => $p['title'],
            'author_id'     => $author ? (int)$author['id'] : null,
            'author_name'   => $author['name'] ?? null,
            'author_role'   => $author['role'] ?? null,
            'author_photo'  => $author['photo'] ?? null,
            'category_name' => $cat['name'] ?? null,
            'category_slug' => $cat['slug'] ?? null,
            'category_id'   => $cat ? (int)$cat['id'] : null,
        ];
    }
    usort($rows, static fn (array $a, array $b): int => strcmp($b['published_at'], $a['published_at']));
    return $rows;
}

/**
 * Categories with post counts, the shape blog.php's chip query returns:
 * id, slug, name, description, post_count. Only categories with posts.
 *
 * @return list<array<string, mixed>>
 */
function seed_preview_categories(): array
{
    if (!seed_preview_enabled()) {
        return [];
    }
    $counts = [];
    foreach (seed_preview_posts() as $p) {
        if ($p['category_slug'] !== null) {
            $counts[$p['category_slug']] = ($counts[$p['category_slug']] ?? 0) + 1;
        }
    }
    $out = [];
    foreach (seed_preview_all()['categories'] ?? [] as $c) {
        if (!empty($counts[$c['slug']])) {
            $out[] = $c + ['post_count' => $counts[$c['slug']]];
        }
    }
    return $out;
}
