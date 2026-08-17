<?php
/**
 * Writes the PREVIEW SEED (inc/data/seed-preview.php) into a real database, so
 * a server with a database can show the same populated design the local
 * no-database preview shows.
 *
 *     php inc/tools/seed-preview.php --yes
 *
 * ┌──────────────────────────────────────────────────────────────────────┐
 * │  THIS PUBLISHES SAMPLE CONTENT. Every row is anonymised preview       │
 * │  material — sample team members, sample case studies, sample quotes. │
 * │  Run it on a preview server. Do NOT leave it in a live database:     │
 * │  delete or unpublish the rows in the admin before launch. It refuses │
 * │  to run without --yes for exactly that reason.                        │
 * └──────────────────────────────────────────────────────────────────────┘
 *
 * Idempotent: team members are keyed on name, categories on slug, posts on
 * slug, case studies and testimonials on sort_order, media on filename. Media
 * rows point at the files inc/tools/build-paper.php writes to uploads/
 * (seed-team-N.png, seed-cover-0N.png); copy those to the server's uploads/
 * along with the code.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

if (!in_array('--yes', $argv, true)) {
    fwrite(STDERR, "This publishes SAMPLE content into the database. Re-run with --yes if that is what you want.\n");
    exit(1);
}
if (!db_available()) {
    fwrite(STDERR, "No database reachable (DB_DSN in inc/config.local.php).\n");
    exit(1);
}

$seed = require __DIR__ . '/../data/seed-preview.php';
$posts = require __DIR__ . '/../data/seed-posts.php';
$say = static function (string $what, int $n): void { fwrite(STDOUT, str_pad($what, 16) . $n . "\n"); };

/** Ensures a media row for an uploads/ filename and returns its id. */
$media = static function (string $filename, string $alt): ?int {
    $file = dirname(__DIR__, 2) . '/uploads/' . $filename;
    if (!is_file($file)) {
        return null;
    }
    $id = scalar('SELECT id FROM media WHERE filename = ?', [$filename]);
    if ($id !== null) {
        return (int)$id;
    }
    $size = @getimagesize($file);
    return insert_returning_id(
        'INSERT INTO media (filename, original_name, mime, bytes, width, height, alt) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$filename, $filename, $size['mime'] ?? 'image/png', (int)filesize($file), $size[0] ?? null, $size[1] ?? null, $alt]
    );
};

tx(static function () use ($seed, $posts, $say, $media): void {

    // --- team --------------------------------------------------------------
    $teamIds = [];
    foreach ($seed['team_members'] as $i => $t) {
        $photoId = $t['photo'] ? $media($t['photo'], $t['photo_alt']) : null;
        $id = scalar('SELECT id FROM team_members WHERE name = ?', [$t['name']]);
        if ($id === null) {
            $id = insert_returning_id(
                'INSERT INTO team_members (name, role, brief, bio, github_url, linkedin_url, photo_media_id, sort_order, is_placeholder, is_published)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$t['name'], $t['role'], $t['brief'], $t['bio'], $t['github_url'], $t['linkedin_url'], $photoId, $i, false, true]
            );
        } else {
            q('UPDATE team_members SET role = ?, brief = ?, bio = ?, github_url = ?, linkedin_url = ?, photo_media_id = ?, sort_order = ?, is_placeholder = ?, is_published = ?, updated_at = now() WHERE id = ?',
              [$t['role'], $t['brief'], $t['bio'], $t['github_url'], $t['linkedin_url'], $photoId, $i, false, true, $id]);
        }
        $teamIds[(int)$t['id']] = (int)$id;
    }
    $say('team_members', count($seed['team_members']));

    // --- categories --------------------------------------------------------
    $catIds = [];
    foreach ($seed['categories'] as $c) {
        $id = scalar('SELECT id FROM categories WHERE slug = ?', [$c['slug']]);
        if ($id === null) {
            $id = insert_returning_id('INSERT INTO categories (slug, name, description, sort_order) VALUES (?, ?, ?, ?)',
                [$c['slug'], $c['name'], $c['description'], $c['sort_order']]);
        } else {
            q('UPDATE categories SET name = ?, description = ?, sort_order = ? WHERE id = ?', [$c['name'], $c['description'], $c['sort_order'], $id]);
        }
        $catIds[$c['slug']] = (int)$id;
    }
    $say('categories', count($seed['categories']));

    // --- posts -------------------------------------------------------------
    $n = 0;
    foreach ($posts as $p) {
        $m = $seed['posts_meta'][$p['slug']] ?? null;
        if ($m === null) {
            continue;
        }
        $coverId  = $m['cover'] ? $media($m['cover'], $p['title']) : null;
        $authorId = $teamIds[(int)($m['author'] ?? 0)] ?? null;
        $read     = max(1, min(60, (int)ceil(str_word_count(strip_tags($p['body'])) / 200)));
        $id = scalar('SELECT id FROM posts WHERE slug = ?', [$p['slug']]);
        if ($id === null) {
            $id = insert_returning_id(
                'INSERT INTO posts (slug, title, tag, excerpt, meta_desc, body, status, published_at, read_minutes, cover_media_id, author_team_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$p['slug'], $p['title'], $p['tag'], $p['excerpt'], $p['meta_desc'], $p['body'], 'published', $m['published_at'], $read, $coverId, $authorId]
            );
        } else {
            // An article an editor may have touched keeps its body; only the
            // preview wiring (cover, author, date) is refreshed.
            q('UPDATE posts SET status = ?, published_at = ?, cover_media_id = ?, author_team_id = ?, updated_at = now() WHERE id = ?',
              ['published', $m['published_at'], $coverId, $authorId, $id]);
        }
        $cid = $catIds[$m['category']] ?? null;
        if ($cid !== null) {
            insert_ignore('INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)', [(int)$id, $cid]);
        }
        $n++;
    }
    $say('posts', $n);

    // --- case studies ------------------------------------------------------
    foreach ($seed['case_studies'] as $i => $c) {
        $id = scalar('SELECT id FROM case_studies WHERE sort_order = ?', [$i]);
        $vals = [$c['client_name'], $c['sector'], $c['problem'], $c['action'], $c['metric_value'], $c['metric_label'], $c['tags'], false, true, $i];
        if ($id === null) {
            q('INSERT INTO case_studies (client_name, sector, problem, action, metric_value, metric_label, tags, is_placeholder, is_published, sort_order)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $vals);
        } else {
            q('UPDATE case_studies SET client_name = ?, sector = ?, problem = ?, action = ?, metric_value = ?, metric_label = ?, tags = ?, is_placeholder = ?, is_published = ?, sort_order = ?, updated_at = now() WHERE id = ?',
              [...$vals, $id]);
        }
    }
    $say('case_studies', count($seed['case_studies']));

    // --- testimonials ------------------------------------------------------
    foreach ($seed['testimonials'] as $i => $t) {
        $id = scalar('SELECT id FROM testimonials WHERE sort_order = ?', [$i]);
        $vals = [$t['quote'], $t['author_name'], $t['author_role'], $t['author_company'], true, false, true, $i];
        if ($id === null) {
            q('INSERT INTO testimonials (quote, author_name, author_role, author_company, consent_given, is_placeholder, is_published, sort_order)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $vals);
        } else {
            q('UPDATE testimonials SET quote = ?, author_name = ?, author_role = ?, author_company = ?, consent_given = ?, is_placeholder = ?, is_published = ?, sort_order = ?, updated_at = now() WHERE id = ?',
              [...$vals, $id]);
        }
    }
    $say('testimonials', count($seed['testimonials']));

    // --- settings (only the keys the seed carries) -------------------------
    foreach ($seed['settings'] as $key => $value) {
        $exists = scalar('SELECT "key" FROM settings WHERE "key" = ?', [$key]);
        if ($exists === null) {
            q('INSERT INTO settings ("key", value, type, label, group_name, sort_order) VALUES (?, ?, ?, ?, ?, ?)',
              [$key, $value, 'text', $key, 'general', 0]);
        } else {
            q('UPDATE settings SET value = ?, updated_at = now() WHERE "key" = ?', [$value, $key]);
        }
    }
    $say('settings', count($seed['settings']));
});

fwrite(STDOUT, "\nSAMPLE content published. Unpublish or delete it in the admin before the site goes live.\n");
