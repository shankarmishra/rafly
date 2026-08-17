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

$posts = require __DIR__ . '/../data/seed-posts.php';

// ---------------------------------------------------------------------------

$inserted = 0;
$existing = 0;

foreach ($posts as $p) {
    if (scalar('SELECT id FROM posts WHERE slug = ?', [$p['slug']]) !== null) {
        $existing++;
        continue;
    }

    q('
        INSERT INTO posts
            (slug, title, tag, excerpt, meta_desc, body, status, published_at, read_minutes)
        VALUES (?, ?, ?, ?, ?, ?, \'published\', now(), ?)
    ', [$p['slug'], $p['title'], $p['tag'], $p['excerpt'], $p['meta_desc'], trim($p['body']), $p['read']]);

    $inserted++;
    $words = str_word_count(strip_tags($p['body']));
    printf("  + %-42s %4d words\n", $p['slug'], $words);
}

printf("\n%d inserted, %d already present\n", $inserted, $existing);
