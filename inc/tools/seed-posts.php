<?php
/**
 * Seeds the four Insights articles.
 *
 *     php inc/tools/seed-posts.php
 *
 * Separate from seed.php purely for length — these are full article bodies,
 * not configuration.
 *
 * Idempotent on slug. An existing post is left completely alone: once an
 * editor has touched an article in the admin, re-running this must not revert
 * their work. To reset one deliberately, delete the row first.
 *
 * EDITORIAL NOTE: these are written from the service offering — what the team
 * does and why. They contain no client names, no case results and no metrics,
 * because none of those have been supplied and inventing them is exactly the
 * problem the PLACEHOLDER badges elsewhere exist to flag. Every claim is either
 * a general industry observation or a statement about how Rafly works.
 *
 * published_at is set to today for all four rather than backdated. They are
 * being published today; a spread of invented past dates would be a small lie
 * told to make the blog look older than it is. Adjust in the admin if you want
 * to stagger the run-out.
 *
 * CSP: inc/security.php sets default-src 'self', so these bodies must contain
 * no inline <script>, no third-party iframes (YouTube/Vimeo) and no hotlinked
 * images. Adding one is a deliberate security-header change, not a content
 * decision.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../sanitize.php'; // slugify()

$posts = require __DIR__ . '/../data/seed-posts.php';

// ---------------------------------------------------------------------------

$inserted = 0;
$existing = 0;
$categorised = 0;

foreach ($posts as $p) {
    $id = scalar('SELECT id FROM posts WHERE slug = ?', [$p['slug']]);

    if ($id === null) {
        $id = insert_returning_id('
            INSERT INTO posts
                (slug, title, tag, excerpt, meta_desc, body, status, published_at, read_minutes)
            VALUES (?, ?, ?, ?, ?, ?, \'published\', now(), ?)
        ', [$p['slug'], $p['title'], $p['tag'], $p['excerpt'], $p['meta_desc'], trim($p['body']), $p['read']]);

        $inserted++;
        $words = str_word_count(strip_tags($p['body']));
        printf("  + %-52s %4d words\n", $p['slug'], $words);
    } else {
        $existing++;
    }

    /**
     * Category linkage.
     *
     * inc/migrations/007_blog.sql backfills a category from `tag` for
     * whatever posts already exist AT MIGRATION TIME — a one-off, not an
     * ongoing rule. Every post this script adds AFTER that migration ran
     * (which in practice means every post: the four originals were what the
     * backfill was written for, and this script is how any post since is
     * meant to arrive) would otherwise sit with a tag and no category at
     * all — invisible to blog.php's category filter, and to
     * inc/repo/links.php's category -> service matching, which is exactly
     * the feature this exists to keep working as real articles are added.
     *
     * slugify() here is the same function the migration's SQL reimplements
     * for the same purpose — lowercase, non-alnum runs collapsed to one
     * hyphen, trimmed — so a category this script creates and one the
     * migration already created for the same tag resolve to the identical
     * slug rather than silently duplicating it.
     */
    $tag = trim($p['tag']);
    if ($tag === '') {
        continue;
    }
    $catSlug = slugify($tag);
    if ($catSlug === '') {
        continue;
    }

    $catId = scalar('SELECT id FROM categories WHERE slug = ?', [$catSlug]);
    if ($catId === null) {
        $catId = insert_returning_id(
            'INSERT INTO categories (slug, name) VALUES (?, ?)',
            [$catSlug, $tag]
        );
    }

    insert_ignore('INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)', [(int)$id, (int)$catId]);
    $categorised++;
}

printf("\n%d inserted, %d already present, %d categorised\n", $inserted, $existing, $categorised);
