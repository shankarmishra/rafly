<?php
/**
 * Blog articles — list, create, edit, publish, delete.
 *
 * Bodies are HTML written by an editor and are passed through
 * sanitize_html() ON SAVE, so the database only ever holds allow-listed markup
 * and the public template can render it without escaping. Sanitising on save
 * rather than on render means one pass per edit instead of one per page view,
 * and there is no way to render an unsanitised body by forgetting a helper.
 *
 * The section was called Insights until the blog build-out; the table, this
 * file and the row ids are unchanged, so nothing here needed migrating. What
 * migration 007 added is a cover image, a credited author, and categories —
 * the last of which is the only part that is not a plain column, because a
 * post can genuinely belong to more than one topic.
 *
 * read_minutes is now computed on save rather than typed. It was a number an
 * editor had to guess, it was wrong as often as not, and the body it describes
 * is sitting right there.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require_once __DIR__ . '/../inc/sanitize.php';
require_once __DIR__ . '/lib/upload.php';

/** Same list media_store_upload() accepts; SVG is excluded there and here. */
const POST_COVER_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

/**
 * Reading time in whole minutes, from the body.
 *
 * 200 words per minute is the middle of the range usually quoted for adult
 * silent reading of general prose. strip_tags() first, or every <p> and </p>
 * counts as a word; the body is already sanitised by the time this sees it.
 * Floored at one minute, because "0 min read" reads as a bug.
 */
function post_read_minutes(string $body): int
{
    $words = str_word_count(strip_tags($body));
    return max(1, min(60, (int)ceil($words / 200)));
}

/**
 * Replace a post's category links.
 *
 * Delete-then-insert rather than a diff: the sets are tiny, the write happens
 * once per save, and a diff would be more code to get subtly wrong. Always
 * called inside tx() so a post never lands with half its categories.
 */
function post_sync_categories(int $postId, array $categoryIds): void
{
    q('DELETE FROM post_categories WHERE post_id = ?', [$postId]);

    foreach ($categoryIds as $cid) {
        insert_ignore(
            'INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)',
            [$postId, (int)$cid]
        );
    }
}

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

/**
 * Set when a save is rejected by validation.
 *
 * The old behaviour was admin_redirect() with a flash message, which is right
 * for a successful POST and wrong for a failed one: the redirect discards the
 * request body, so an editor who spent an hour on an article and picked a slug
 * that happened to collide lost the entire thing to a one-line error message.
 * The browser's back button does not reliably recover it either, because the
 * page it returns to was served with the pre-edit content.
 *
 * On failure the form is now re-rendered in place from what was submitted, with
 * the errors attached to the fields that caused them. Nothing is thrown away.
 */
$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        require_can('content.delete');
        $before = one('SELECT * FROM posts WHERE id = ?', [$id]);
        if ($before !== null) {
            q('DELETE FROM posts WHERE id = ?', [$id]);
            audit('post.delete', 'post', $id, $before, null);
            admin_redirect('/admin/posts.php', 'Article deleted.', 'warn');
        }
        admin_redirect('/admin/posts.php', 'That article was already removed.', 'warn');
    }

    if ($action === 'save') {
        require_can('content.edit');

        $title  = str_cut(trim((string)($_POST['title'] ?? '')), 200);
        $slug   = slugify((string)($_POST['slug'] ?? '')) ?: slugify($title);
        $status = (string)($_POST['status'] ?? 'draft');

        if (!in_array($status, ['draft', 'published'], true)) {
            $status = 'draft';
        }
        if ($status === 'published') {
            require_can('content.publish');
        }

        if ($title === '') {
            $errors['title'] = 'A title is required.';
        } elseif ($slug === '') {
            // Only reachable when the title is all punctuation, since the slug
            // otherwise falls back to slugify($title).
            $errors['slug'] = 'That title does not produce a usable slug. Enter one by hand.';
        }

        // The slug is a URL key and is UNIQUE in the schema. Checking here turns
        // a 500 from the constraint into a message the editor can act on.
        if ($slug !== '') {
            $clash = scalar('SELECT id FROM posts WHERE slug = ? AND id <> ?', [$slug, $id]);
            if ($clash !== null) {
                $errors['slug'] = 'Another article already uses the slug "' . $slug . '". '
                                . 'Change it — everything else you typed has been kept.';
            }
        }

        $body = sanitize_html((string)($_POST['body'] ?? ''));

        /**
         * Cover image. Identical shape to the headshot field in admin/team.php,
         * and deliberately so: media_store_upload() returns its outcome rather
         * than redirecting, so a rejected image becomes a field error and the
         * article body survives. A newly chosen file wins over the picker.
         */
        $coverId  = (int)($_POST['cover_media_id'] ?? 0);
        $cover    = null;
        $uploaded = $_FILES['cover_file'] ?? null;
        $hasFile  = is_array($uploaded) && ($uploaded['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($hasFile) {
            if (!can('media.upload')) {
                $errors['cover_file'] = 'You do not have permission to upload images.';
            } else {
                $result = media_store_upload($uploaded, $title);
                if (isset($result['error'])) {
                    $errors['cover_file'] = $result['error'];
                } else {
                    $cover   = $result['id'];
                    $coverId = $result['id'];
                }
            }
        } elseif ($coverId > 0) {
            // A <select> value is request data like any other. NULL — not 0 —
            // is "no cover": the foreign key would reject 0.
            $m = one('SELECT id, mime FROM media WHERE id = ?', [$coverId]);
            if ($m === null || !in_array((string)$m['mime'], POST_COVER_MIMES, true)) {
                $errors['cover_media_id'] = 'That image is no longer in the media library.';
            } else {
                $cover = (int)$m['id'];
            }
        }

        // Credited author. Same treatment: checked against the table, never
        // trusted, and NULL rather than 0 when nobody is credited.
        $authorTeam   = null;
        $authorTeamId = (int)($_POST['author_team_id'] ?? 0);
        if ($authorTeamId > 0) {
            $exists = scalar('SELECT id FROM team_members WHERE id = ?', [$authorTeamId]);
            if ($exists === null) {
                $errors['author_team_id'] = 'That team member no longer exists.';
            } else {
                $authorTeam = (int)$exists;
            }
        }

        /**
         * Categories. Every submitted id is checked against the table and the
         * survivors are what gets written — an unknown id is dropped silently
         * rather than erroring, because the only way to submit one is to edit
         * the form by hand or to race a category being deleted, and neither
         * deserves to block the save.
         */
        $categoryIds = [];
        $rawCats = $_POST['categories'] ?? [];
        if (is_array($rawCats) && $rawCats) {
            $wanted = array_slice(array_unique(array_map('intval', $rawCats)), 0, 20);
            $wanted = array_values(array_filter($wanted, static fn(int $n): bool => $n > 0));

            if ($wanted) {
                // Placeholders are generated from a count, never from input.
                $in    = implode(', ', array_fill(0, count($wanted), '?'));
                $found = all('SELECT id FROM categories WHERE id IN (' . $in . ')', $wanted);
                $categoryIds = array_map('intval', array_column($found, 'id'));
            }
        }

        $fields = [
            'title'          => $title,
            'slug'           => $slug,
            'tag'            => str_cut(trim((string)($_POST['tag'] ?? '')), 60),
            'excerpt'        => str_cut(trim((string)($_POST['excerpt'] ?? '')), 500),
            'meta_desc'      => str_cut(trim((string)($_POST['meta_desc'] ?? '')), 200),
            'body'           => $body,
            'status'         => $status,
            'read_minutes'   => post_read_minutes($body),
            'cover_media_id' => $cover,
            'author_team_id' => $authorTeam,
        ];

        // Rejected: re-render the form from $fields rather than redirecting, so
        // the body survives. $editId/$isNew are reset because the request that
        // got here was a POST to posts.php with no query string, and the render
        // path below decides which form to draw from those two.
        if ($errors) {
            $submitted = $fields;
            $submitted['categories'] = $categoryIds;   // so the boxes stay ticked
            $editId    = $id;
            $isNew     = $id === 0;
        } elseif ($id > 0) {
            $before = one('SELECT * FROM posts WHERE id = ?', [$id]);
            if ($before === null) {
                admin_redirect('/admin/posts.php', 'That article no longer exists.', 'error');
            }

            // One transaction, because the row and its category links are one
            // edit as far as the editor is concerned. Without it a failure
            // between the two leaves an article filed under its old topics.
            tx(static function () use ($fields, $status, $id, $categoryIds): void {
                // The CASE takes a bound boolean rather than comparing against a
                // literal 'published' — a quoted SQL literal inside a single-quoted
                // PHP string terminates the string.
                q('
                    UPDATE posts
                       SET title = ?, slug = ?, tag = ?, excerpt = ?, meta_desc = ?,
                           body = ?, status = ?, read_minutes = ?,
                           cover_media_id = ?, author_team_id = ?, updated_at = now(),
                           -- Stamp published_at the first time it goes live and never
                           -- again: it is the publication date, not the last-edit date,
                           -- and rewriting it on every save would make the date churn.
                           published_at = CASE
                               WHEN ? AND published_at IS NULL THEN now()
                               ELSE published_at
                           END
                     WHERE id = ?
                ', [...array_values($fields), $status === 'published', $id]);

                post_sync_categories($id, $categoryIds);
            });

            audit('post.update', 'post', $id, $before, one('SELECT * FROM posts WHERE id = ?', [$id]));
            admin_redirect('/admin/posts.php?id=' . $id, 'Article saved.');
        } else {
            $user = current_user();

            $newId = tx(static function () use ($fields, $status, $user, $categoryIds): int {
                $id = insert_returning_id('
                    INSERT INTO posts (title, slug, tag, excerpt, meta_desc, body, status, read_minutes,
                                       cover_media_id, author_team_id, published_at, author_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CASE WHEN ? THEN now() ELSE NULL END, ?)
                ', [...array_values($fields), $status === 'published', $user['id'] ?? null]);

                post_sync_categories($id, $categoryIds);
                return $id;
            });

            audit('post.create', 'post', $newId, null, one('SELECT * FROM posts WHERE id = ?', [$newId]));
            admin_redirect('/admin/posts.php?id=' . $newId, 'Article created.');
        }
    }

    // Only reached when the action was not recognised, or when a save was
    // rejected — in which case execution continues to the form below rather
    // than bouncing the editor away from their work.
    if (!$errors) {
        admin_redirect('/admin/posts.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

// ---------------------------------------------------------------------------
// Edit / create form
// ---------------------------------------------------------------------------

if ($editId > 0 || $isNew) {
    $post = $editId > 0 ? one('SELECT * FROM posts WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $post === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Article not found', 'active' => '/admin/posts.php']);
        echo '<div class="card"><p>That article does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/posts.php')) . '">Back to all articles</a></p></div>';
        admin_foot();
        exit;
    }

    // What was just typed wins over what is stored. On a normal GET $submitted
    // is null and this reads the row exactly as before.
    $v = static fn(string $k, string $default = ''): string
        => (string)($submitted[$k] ?? $post[$k] ?? $default);

    /** Renders the message attached to one field, if there is one. */
    $err = static function (string $k) use ($errors): string {
        return isset($errors[$k])
            ? '<p class="field-error">' . e($errors[$k]) . '</p>'
            : '';
    };

    /** Appended to a control's class so the field itself is marked, not just labelled. */
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    $selectedCover = (int)($submitted['cover_media_id'] ?? $post['cover_media_id'] ?? 0);

    // Newest first, capped — the same working picker admin/team.php uses, and
    // unwieldy past a hundred or so images for the same reason.
    $library = all('
        SELECT id, original_name, filename
          FROM media
         WHERE mime IN (?, ?, ?, ?)
      ORDER BY created_at DESC
         LIMIT 300
    ', POST_COVER_MIMES);

    $currentCover = $selectedCover > 0
        ? one('SELECT filename, alt FROM media WHERE id = ?', [$selectedCover])
        : null;

    $selectedAuthor = (int)($submitted['author_team_id'] ?? $post['author_team_id'] ?? 0);

    // Unpublished people are still offered: an author can be credited before
    // their own profile card goes live, and hiding them here would look like
    // the list was broken.
    $teamOptions = all('SELECT id, name, role FROM team_members ORDER BY sort_order ASC, id ASC');

    $allCategories = all('SELECT id, name, slug FROM categories ORDER BY sort_order ASC, name ASC');

    // On a rejected save the ticked set comes back from the request; otherwise
    // from the join. array_flip gives an O(1) isset() test in the loop below.
    $checkedCats = isset($submitted['categories'])
        ? array_flip(array_map('intval', (array)$submitted['categories']))
        : ($editId > 0
            ? array_flip(array_map('intval', array_column(
                all('SELECT category_id FROM post_categories WHERE post_id = ?', [$editId]),
                'category_id'
              )))
            : []);

    admin_head([
        'title'   => $post ? 'Edit article' : 'New article',
        'heading' => $post ? (string)$post['title'] : 'New article',
        'intro'   => $post
            ? 'Last updated ' . date('j M Y, H:i', strtotime((string)$post['updated_at']))
            : 'Write a new blog article.',
        'active'  => '/admin/posts.php',
    ]);
?>

<div class="card">
    <h2>Article</h2>
<?php if (!can('content.edit')): ?>
    <p class="hint">You have read-only access to content.</p>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This article was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="posts.php" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="field">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required maxlength="200" value="<?= e($v('title')) ?>"<?= $bad('title') ?>>
            <?= $err('title') ?>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="slug">Slug</label>
                <p class="hint">Leave blank to generate from the title. Changing it breaks existing links.</p>
                <input type="text" id="slug" name="slug" maxlength="200" value="<?= e($v('slug')) ?>"<?= $bad('slug') ?>>
                <?= $err('slug') ?>
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft"<?= $v('status', 'draft') === 'draft' ? ' selected' : '' ?>>Draft</option>
                    <option value="published"<?= $v('status') === 'published' ? ' selected' : '' ?>>Published</option>
                </select>
            </div>

            <div class="field">
                <label for="author_team_id">Credited author</label>
                <select id="author_team_id" name="author_team_id"<?= $bad('author_team_id') ?>>
                    <option value="0">— no byline —</option>
<?php foreach ($teamOptions as $tm): ?>
                    <option value="<?= (int)$tm['id'] ?>"<?= $selectedAuthor === (int)$tm['id'] ? ' selected' : '' ?>>
                        <?= e((string)$tm['name']) ?><?= $tm['role'] !== '' ? ' — ' . e((string)$tm['role']) : '' ?>
                    </option>
<?php endforeach; ?>
                </select>
                <p class="hint">Who the article is credited to on the site. Separate from whoever is logged in editing it.</p>
                <?= $err('author_team_id') ?>
            </div>

            <div class="field">
                <label>Read time</label>
                <?php /* Read-only rather than removed: the number still matters
                         to an editor deciding whether a piece is too long, it is
                         just no longer theirs to guess at. */ ?>
                <input type="text" value="<?= (int)($submitted['read_minutes'] ?? $post['read_minutes'] ?? 1) ?> min" readonly>
                <p class="hint">Calculated from the body when you save, at 200 words a minute.</p>
            </div>

            <div class="field">
                <label for="tag">Legacy tag</label>
                <input type="text" id="tag" name="tag" maxlength="60" value="<?= e($v('tag')) ?>"
                       placeholder="Digital Growth">
                <p class="hint">Superseded by categories below. Kept so nothing written before the change is lost; safe to leave alone.</p>
            </div>
        </div>

        <div class="field">
            <label>Categories</label>
<?php if (!$allCategories): ?>
            <p class="hint">
                No categories yet — <a href="<?= e(site_path('/admin/categories.php')) ?>">create one</a>
                to let readers filter the blog by topic.
            </p>
<?php else: ?>
            <p class="hint">
                Tick every topic that genuinely applies; a post can sit under more than one.
                Manage the list under <a href="<?= e(site_path('/admin/categories.php')) ?>">Categories</a>.
            </p>
<?php foreach ($allCategories as $c): ?>
            <div class="field-check">
                <input type="checkbox" id="cat_<?= (int)$c['id'] ?>" name="categories[]" value="<?= (int)$c['id'] ?>"
                       <?= isset($checkedCats[(int)$c['id']]) ? 'checked' : '' ?>>
                <label for="cat_<?= (int)$c['id'] ?>"><?= e((string)$c['name']) ?></label>
            </div>
<?php endforeach; ?>
<?php endif; ?>
        </div>

        <div class="field">
            <label>Cover image</label>

<?php if ($currentCover !== null): ?>
            <p><img src="<?= e(site_path('/uploads/' . rawurlencode((string)$currentCover['filename']))) ?>"
                    alt="<?= e((string)$currentCover['alt']) ?>" width="220" height="124"
                    style="width:220px;height:124px;object-fit:cover;border-radius:8px;margin-bottom:8px;"></p>
<?php endif; ?>

<?php if (can('media.upload')): ?>
            <input type="file" id="cover_file" name="cover_file"
                   accept="image/jpeg,image/png,image/gif,image/webp"<?= $bad('cover_file') ?>>
            <p class="hint">
                JPEG, PNG, GIF or WebP, up to <?= (int)(MEDIA_MAX_BYTES / 1024 / 1024) ?> MB. A wide crop reads
                best — cards and the article header both show it at 16:9. Uploading also adds the image to the
                <a href="<?= e(site_path('/admin/media.php')) ?>">media library</a>.
            </p>
            <?= $err('cover_file') ?>
<?php endif; ?>

<?php if ($library): ?>
            <label for="cover_media_id" style="margin-top:10px;">…or pick one already uploaded</label>
            <select id="cover_media_id" name="cover_media_id"<?= $bad('cover_media_id') ?>>
                <option value="0">— no cover —</option>
<?php foreach ($library as $m): ?>
                <option value="<?= (int)$m['id'] ?>"<?= $selectedCover === (int)$m['id'] ? ' selected' : '' ?>>
                    <?= e(str_trunc((string)($m['original_name'] !== '' ? $m['original_name'] : $m['filename']), 60)) ?>
                </option>
<?php endforeach; ?>
            </select>
            <p class="hint">Choosing a file above replaces whatever is selected here.</p>
            <?= $err('cover_media_id') ?>
<?php else: ?>
            <?php /* Keeps the current cover on save when there is nothing to pick
                     from yet — without it, editing a post that has a cover on an
                     empty library would silently clear it. */ ?>
            <input type="hidden" name="cover_media_id" value="<?= (int)$selectedCover ?>">
<?php endif; ?>
        </div>

        <div class="field">
            <label for="excerpt">Excerpt</label>
            <p class="hint">Shown on the blog card. Can be longer and more voiced than the meta description.</p>
            <textarea id="excerpt" name="excerpt" maxlength="500"><?= e($v('excerpt')) ?></textarea>
        </div>

        <div class="field">
            <label for="meta_desc">Meta description</label>
            <p class="hint">Search results and social previews. Around 155 characters.</p>
            <textarea id="meta_desc" name="meta_desc" maxlength="200"><?= e($v('meta_desc')) ?></textarea>
        </div>

        <div class="field">
            <label for="body">Body</label>
            <p class="hint">
                HTML. Allowed: paragraphs, h2–h4, lists, links, blockquote, code, images, tables.
                Anything else is stripped when you save — scripts, styles, iframes and event
                handlers cannot be stored. Links leaving the site get rel="noopener" automatically.
            </p>
            <textarea id="body" name="body" rows="24"><?= e($v('body')) ?></textarea>
        </div>

        <div class="form-actions">
<?php if (can('content.edit')): ?>
            <button type="submit" class="btn">Save article</button>
<?php endif; ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/posts.php')) ?>">Back to all</a>
            <span class="spacer"></span>
<?php if ($post && $post['status'] === 'published'): ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/blog/' . rawurlencode((string)$post['slug']))) ?>"
               target="_blank" rel="noopener">View on site</a>
<?php endif; ?>
        </div>
    </form>
</div>

<?php if ($post && can('content.delete')): ?>
<div class="card">
    <h2>Delete</h2>
    <p class="hint">Permanent. If the article is published, its URL will start returning 404.</p>
    <form method="post" action="posts.php" data-confirm="Delete this article permanently? This cannot be undone.">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
        <button type="submit" class="btn btn-danger">Delete article</button>
    </form>
</div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

// ---------------------------------------------------------------------------
// List
// ---------------------------------------------------------------------------

require_once __DIR__ . '/lib/list.php';

// The previous ORDER BY ended in `p.published_at DESC NULLS FIRST`, which is
// PostgreSQL-only syntax — MySQL rejects NULLS FIRST outright, so this list
// would have been a fatal error the moment the site was deployed. Null ordering
// is the one part of ORDER BY the two dialects genuinely disagree on, so the
// default is simply "most recently touched", which needs no null handling at
// all and is the more useful default for an editor anyway.
$st = list_state([
    'base'     => '/admin/posts.php',
    'sorts'    => [
        'updated'   => 'p.updated_at',
        'title'     => 'p.title',
        'tag'       => 'p.tag',
        'status'    => 'p.status',
        'published' => 'p.published_at',
        'read'      => 'p.read_minutes',
    ],
    'default'  => 'updated',
    'tiebreak' => 'p.id DESC',
    'search'   => ['p.title', 'p.slug', 'p.tag', 'p.excerpt'],
    'per_page' => 25,
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM posts p' . $whereSql, $st['params']);

$posts = all('
    SELECT p.id, p.slug, p.title, p.tag, p.status, p.published_at, p.updated_at,
           p.read_minutes, u.name AS author_name
      FROM posts p
      LEFT JOIN users u ON u.id = p.author_id
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

admin_head([
    'title'   => 'Blog',
    'heading' => 'Blog',
    'intro'   => 'Articles published to the blog.',
    'active'  => '/admin/posts.php',
]);
?>

<div class="card">
<?php list_toolbar($st, $total, 'article', 'articles',
        can('content.edit') ? '<a class="btn" href="' . e(site_path('/admin/posts.php?new=1')) . '">New article</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'title', 'Title') ?>
                    <?= list_th($st, 'tag', 'Tag') ?>
                    <?= list_th($st, 'status', 'Status') ?>
                    <?= list_th($st, 'published', 'Published', '', 'desc') ?>
                    <?= list_th($st, 'updated', 'Updated', '', 'desc') ?>
                    <?= list_th($st, 'read', 'Read', 'num', 'desc') ?>
                </tr>
            </thead>
            <tbody>
<?php if (!$posts): ?>
                <?= list_empty_state(6, 'file-pen',
                        $st['q'] !== '' ? 'No matching articles.' : 'No articles yet.',
                        $st['q'] !== ''
                            ? 'No article matches “' . $st['q'] . '”.'
                            : 'Write your first blog article to see it here.') ?>
<?php else: foreach ($posts as $p): ?>
                <tr>
                    <td><a href="<?= e(site_path('/admin/posts.php?id=' . (int)$p['id'])) ?>"><?= e($p['title']) ?></a></td>
                    <td><?= $p['tag'] !== '' ? '<span class="badge">' . e($p['tag']) . '</span>' : '—' ?></td>
                    <td>
                        <span class="badge <?= $p['status'] === 'published' ? 'badge-ok' : 'badge-muted' ?>">
                            <?= e($p['status']) ?>
                        </span>
                    </td>
                    <td><?= $p['published_at'] !== null ? e(date('j M Y', strtotime((string)$p['published_at']))) : '—' ?></td>
                    <td><?= e(date('j M Y', strtotime((string)$p['updated_at']))) ?></td>
                    <td class="num"><?= (int)$p['read_minutes'] ?> min</td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
