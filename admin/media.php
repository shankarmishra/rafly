<?php
/**
 * Media library.
 *
 * Uploads are the largest new attack surface in the admin, so this is
 * deliberately strict. The defence is layered, because any single layer has
 * known bypasses:
 *
 *   1. The stored filename is generated, never derived from the upload. This is
 *      the one that actually matters — a user-supplied name is how "shell.php"
 *      or "x.php.jpg" ends up in a served path.
 *   2. The extension is chosen by US from the sniffed MIME type, not read from
 *      what was uploaded.
 *   3. finfo sniffs the real content type; the browser-supplied
 *      $_FILES['type'] is attacker-controlled and is never trusted.
 *   4. getimagesize() must also agree it is a real image — this rejects a PHP
 *      script carrying a forged magic-byte header, which can satisfy finfo.
 *   5. uploads/.htaccess denies PHP execution, so even a file that defeated all
 *      of the above would be served as bytes rather than run.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('media.view');

/**
 * Sniffed MIME => the extension we will give the stored file.
 *
 * SVG IS DELIBERATELY ABSENT. It was accepted until now, guarded by a
 * four-pattern regex denylist — and inc/sanitize.php already sets out at length
 * why denylists lose: that one missed `javascript&#58;` (an HTML entity the
 * parser decodes back to `javascript:`), handlers smuggled in via
 * <animate attributeName="onload">/<set>/<handler>, and <style> blocks carrying
 * @import or url(). SVG is also the only format here that is a scripting-capable
 * document rather than a bitmap, so it was skipping the getimagesize() check
 * every other format gets.
 *
 * The consequence was an editor→admin escalation: media.upload is an editor
 * capability (inc/auth.php), and an admin who opened a malicious SVG would run
 * it same-origin — enough to read the CSRF token out of the DOM and drive any
 * admin action.
 *
 * Removing it rather than writing a DOM allow-list parse for SVG is the right
 * trade here: nothing on the site needs uploaded SVG. The brand marks and the
 * icon sprite are committed files under vendor/ and assets/, reviewed in git,
 * and never travel this path. Adding a whole second sanitiser to support a
 * format with no users would be more code and more risk than it buys.
 *
 * If uploaded SVG is ever genuinely needed, the answer is a DOM allow-list walk
 * built on the machinery in inc/sanitize.php (parse, allow-list tags and
 * attributes, delete everything else) — NOT another set of patterns.
 */
/* MEDIA_ALLOWED, MEDIA_MAX_BYTES and the upload pipeline itself now live in
   lib/upload.php, because admin/team.php accepts a headshot on its own form and
   a second copy of security-critical code is how one copy later drifts. */
require_once __DIR__ . '/lib/upload.php';

$uploadDir = media_upload_dir();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');

    if ($action === 'delete') {
        require_can('media.delete');

        $id  = (int)($_POST['id'] ?? 0);
        $row = one('SELECT * FROM media WHERE id = ?', [$id]);

        if ($row !== null) {
            // basename() so a filename that somehow held a path cannot escape
            // the directory on unlink.
            $path = $uploadDir . '/' . basename((string)$row['filename']);
            if (is_file($path)) {
                @unlink($path);
            }
            q('DELETE FROM media WHERE id = ?', [$id]);
            audit('media.delete', 'media', $id, $row, null);
        }

        admin_redirect('/admin/media.php', 'File deleted.', 'warn');
    }

    if ($action === 'upload') {
        require_can('media.upload');

        $result = media_store_upload($_FILES['file'] ?? null, (string)($_POST['alt'] ?? ''));

        if (isset($result['error'])) {
            admin_redirect('/admin/media.php', $result['error'], 'error');
        }

        admin_redirect('/admin/media.php', 'File uploaded.');
    }

    admin_redirect('/admin/media.php', 'Unrecognised action.', 'error');
}

require __DIR__ . '/lib/layout.php';

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'     => '/admin/media.php',
    'sorts'    => [
        'created' => 'm.created_at',
        'name'    => 'm.original_name',
        'size'    => 'm.bytes',
        'type'    => 'm.mime',
    ],
    'default'  => 'created',
    'tiebreak' => 'm.id DESC',
    'search'   => ['m.original_name', 'm.alt', 'm.filename'],
    'per_page' => 24,   // Divides by 2, 3 and 4, so the last grid row is full.
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM media m' . $whereSql, $st['params']);

$rows = all('
    SELECT m.*, u.name AS uploader
      FROM media m
      LEFT JOIN users u ON u.id = m.uploaded_by
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

/**
 * Which stored MIME types are bitmaps the browser will actually render in an
 * <img>.
 *
 * The grid used to put every row in an <img> unconditionally. MEDIA_ALLOWED is
 * four bitmap formats today, but the library predates that list — rows uploaded
 * while SVG was still accepted are still in the table, and a PDF or anything
 * else added to MEDIA_ALLOWED later would hit the same path. A non-bitmap in an
 * <img> renders as a broken-image glyph with no indication of what the file
 * actually is.
 *
 * admin.css has carried a `.media-thumb .ext` rule for exactly this case since
 * the file was written, and nothing ever emitted the element. It does now.
 */
const MEDIA_RENDERABLE = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

$bytes = static function (int $n): string {
    if ($n < 1024)        { return $n . ' B'; }
    if ($n < 1024 * 1024) { return round($n / 1024) . ' KB'; }
    return round($n / 1024 / 1024, 1) . ' MB';
};

admin_head([
    'title'   => 'Media',
    'heading' => 'Media',
    'intro'   => 'Images available to use in articles and content.',
    'active'  => '/admin/media.php',
]);
?>

<?php if (can('media.upload')): ?>
<div class="card">
    <h2>Upload</h2>
    <p class="hint">
        JPEG, PNG, GIF or WebP, up to <?= MEDIA_MAX_BYTES / 1024 / 1024 ?> MB. The stored
        filename is generated rather than taken from your file.
    </p>

    <form method="post" action="media.php" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="upload">

        <div class="field">
            <label for="file">File</label>
            <input type="file" id="file" name="file" required accept="image/jpeg,image/png,image/gif,image/webp">
        </div>

        <div class="field">
            <label for="alt">Alt text</label>
            <p class="hint">What the image shows, for screen readers and when it fails to load.</p>
            <input type="text" id="alt" name="alt" maxlength="300">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Upload</button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card">
<?php list_toolbar($st, $total, 'file', 'files', '', [
        'created' => 'Date uploaded',
        'name'    => 'File name',
        'size'    => 'File size',
        'type'    => 'File type',
]); ?>

<?php if (!$rows): ?>
    <p class="empty-state">
        <?= icon('image') ?>
        <strong><?= $st['q'] !== '' ? 'No match.' : 'Nothing uploaded yet.' ?></strong>
        <?= $st['q'] !== ''
            ? 'No file matches “' . e($st['q']) . '”.'
            : 'Uploaded images appear here and can be used in articles.' ?>
    </p>
<?php else: ?>
    <div class="media-grid">
<?php foreach ($rows as $m):
          $url = '/uploads/' . rawurlencode((string)$m['filename']);

          // Prefer the extension we chose at upload time over the MIME string,
          // which is long and unhelpful on a 160px tile.
          $ext = strtoupper(pathinfo((string)$m['filename'], PATHINFO_EXTENSION) ?: 'file'); ?>
        <div class="media-item">
            <div class="media-thumb">
<?php if (in_array((string)$m['mime'], MEDIA_RENDERABLE, true)): ?>
                <img src="<?= e($url) ?>" alt="<?= e($m['alt']) ?>" loading="lazy">
<?php else: ?>
                <span class="ext" title="<?= e((string)$m['mime']) ?>"><?= e($ext) ?></span>
<?php endif; ?>
            </div>
            <div class="media-meta">
                <strong><?= e($m['original_name'] ?: $m['filename']) ?></strong>
                <?= $m['width'] !== null ? (int)$m['width'] . '×' . (int)$m['height'] . ' · ' : '' ?>
                <?= e($bytes((int)$m['bytes'])) ?>
                <br><?= e(date('j M Y', strtotime((string)$m['created_at']))) ?>
<?php if ($m['uploader'] !== null): ?>
                · <?= e($m['uploader']) ?>
<?php endif; ?>
                <br><a href="<?= e($url) ?>" target="_blank" rel="noopener">Open</a>
<?php if (can('media.delete')): ?>
                <form method="post" action="media.php" data-confirm="Delete this file? Anything using it will break.">

                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <button type="submit" class="btn-link">Delete</button>
                </form>
<?php endif; ?>
            </div>
        </div>
<?php endforeach; ?>
    </div>
<?php endif; ?>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
