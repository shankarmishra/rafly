<?php
/**
 * Core team members.
 *
 * Rendered on team.php as a grid of native <details> cards: collapsed shows the
 * photo, name, role and brief; the arrow opens it onto the bio and the two
 * profile links.
 *
 * The photo is picked from the media library rather than uploaded here.
 * admin/media.php owns the only upload pipeline in this codebase and it is
 * deliberately strict — generated filenames, sniffed MIME, a getimagesize()
 * check and no SVG. A second, laxer upload path just for headshots would undo
 * all of that, so this page only ever stores a media.id.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');

/* The upload pipeline and the accepted-format list, shared with
   admin/media.php. A headshot can be uploaded straight from this form — going
   to the media library first and coming back was a confusing detour, and with
   an empty library the picker looked broken. */
require_once __DIR__ . '/lib/upload.php';

/** The four bitmap formats media_store_upload() accepts. */
const TEAM_PHOTO_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

/** Profile links are rendered behind a brand logo, so the host has to match it. */
const TEAM_PROFILE_HOSTS = [
    'github_url'   => ['host' => 'github.com',   'label' => 'GitHub'],
    'linkedin_url' => ['host' => 'linkedin.com', 'label' => 'LinkedIn'],
];

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isNew  = ($_GET['new'] ?? '') !== '';

/**
 * Set when a save is rejected by validation.
 *
 * Same rule as every other admin page: a failed POST is re-rendered in place
 * rather than redirected, because a redirect discards the request body and
 * someone who just typed a 300-word bio would lose it to a one-line error.
 */
$errors    = [];
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        require_can('content.delete');
        $before = one('SELECT * FROM team_members WHERE id = ?', [$id]);
        if ($before !== null) {
            q('DELETE FROM team_members WHERE id = ?', [$id]);
            audit('team_member.delete', 'team_member', $id, $before, null);
            admin_redirect('/admin/team.php', 'Team member deleted.', 'warn');
        }
        admin_redirect('/admin/team.php', 'That team member was already removed.', 'warn');
    }

    if ($action === 'save') {
        require_can('content.edit');

        $isPlaceholder = isset($_POST['is_placeholder']);

        $name  = str_cut(trim((string)($_POST['name'] ?? '')), 120);
        $role  = str_cut(trim((string)($_POST['role'] ?? '')), 120);
        $brief = str_cut(trim((string)($_POST['brief'] ?? '')), 200);
        $bio   = str_cut(trim((string)($_POST['bio'] ?? '')), 2000);

        if ($name === '') {
            $errors['name'] = 'A name is required.';
        }
        if ($role === '') {
            $errors['role'] = 'A role is required — the card shows it under the name.';
        }
        if ($brief === '') {
            $errors['brief'] = 'The brief line is what shows before the card is opened.';
        }

        // Carried over from testimonials.php: a live card signed "[Name]" is not
        // a person, whatever else is filled in.
        if (!$isPlaceholder && str_contains($name, '[')) {
            $errors['name'] = 'Add the real name before taking this member live.';
        }

        /**
         * Both profile URLs are optional, but a present one gets three gates.
         *
         * sanitize_url_ok() is the only URL validator here and it accepts
         * "#frag" and "/path" — fine for an href in a post body, meaningless
         * for an off-site profile. So: reject what it rejects, then require an
         * absolute http(s) URL, then require the host to match the logo we are
         * about to render next to it. An icon is a claim about where a link
         * goes, and the common failure is someone pasting from the wrong tab.
         */
        $urls = [];
        foreach (TEAM_PROFILE_HOSTS as $key => $spec) {
            $raw = str_cut(trim((string)($_POST[$key] ?? '')), 300);
            $urls[$key] = $raw;

            if ($raw === '') {
                continue;
            }

            if (!sanitize_url_ok($raw, 'href')) {
                $errors[$key] = 'That ' . $spec['label'] . ' link is not a URL we can render.';
                continue;
            }
            if (!preg_match('~^https?://~i', $raw)) {
                $errors[$key] = 'Paste the full ' . $spec['label'] . ' address, starting with https://.';
                continue;
            }

            $host = strtolower((string)parse_url($raw, PHP_URL_HOST));
            if ($host !== $spec['host'] && !str_ends_with($host, '.' . $spec['host'])) {
                $errors[$key] = 'That does not look like a ' . $spec['label']
                              . ' profile (' . $spec['host'] . ').';
            }
        }

        /**
         * A newly chosen file wins over whatever the picker had selected.
         *
         * media_store_upload() returns its outcome rather than redirecting, so a
         * rejected image lands as a field error and everything else typed on the
         * form survives — the same rule the rest of this page follows.
         */
        $photoId  = (int)($_POST['photo_media_id'] ?? 0);
        $photo    = null;
        $uploaded = $_FILES['photo_file'] ?? null;
        $hasFile  = is_array($uploaded) && ($uploaded['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($hasFile) {
            if (!can('media.upload')) {
                $errors['photo_file'] = 'You do not have permission to upload images.';
            } else {
                $result = media_store_upload($uploaded, $name);
                if (isset($result['error'])) {
                    $errors['photo_file'] = $result['error'];
                } else {
                    $photo   = $result['id'];
                    $photoId = $result['id'];
                }
            }
        } elseif ($photoId > 0) {
            // A <select> value is request data like any other, so the id is
            // checked against the library rather than trusted. NULL — not 0 —
            // is the "no photo" value: the foreign key would reject 0.
            $m = one('SELECT id, mime FROM media WHERE id = ?', [$photoId]);
            if ($m === null || !in_array((string)$m['mime'], TEAM_PHOTO_MIMES, true)) {
                $errors['photo_media_id'] = 'That image is no longer in the media library.';
            } else {
                $photo = (int)$m['id'];
            }
        }

        // Order matters: both statements below spread array_values($fields).
        $fields = [
            'name'           => $name,
            'role'           => $role,
            'brief'          => $brief,
            'bio'            => $bio,
            'github_url'     => $urls['github_url'],
            'linkedin_url'   => $urls['linkedin_url'],
            'photo_media_id' => $photo,
            'sort_order'     => max(0, min(999, (int)($_POST['sort_order'] ?? 0))),
            'is_placeholder' => $isPlaceholder,
            'is_published'   => isset($_POST['is_published']),
        ];

        if ($errors) {
            $submitted = $fields;
            $editId    = $id;
            $isNew     = $id === 0;
        } elseif ($id > 0) {
            $before = one('SELECT * FROM team_members WHERE id = ?', [$id]);
            if ($before === null) {
                admin_redirect('/admin/team.php', 'That team member no longer exists.', 'error');
            }

            q('
                UPDATE team_members
                   SET name = ?, role = ?, brief = ?, bio = ?,
                       github_url = ?, linkedin_url = ?, photo_media_id = ?,
                       sort_order = ?, is_placeholder = ?, is_published = ?,
                       updated_at = now()
                 WHERE id = ?
            ', [...array_values($fields), $id]);

            audit('team_member.update', 'team_member', $id, $before,
                  one('SELECT * FROM team_members WHERE id = ?', [$id]));
            admin_redirect('/admin/team.php?id=' . $id, 'Team member saved.');
        } else {
            $newId = insert_returning_id('
                INSERT INTO team_members (name, role, brief, bio,
                                          github_url, linkedin_url, photo_media_id,
                                          sort_order, is_placeholder, is_published)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ', array_values($fields));

            audit('team_member.create', 'team_member', $newId, null,
                  one('SELECT * FROM team_members WHERE id = ?', [$newId]));
            admin_redirect('/admin/team.php?id=' . $newId, 'Team member created.');
        }
    }

    // Only reached on an unrecognised action, or a rejected save — which falls
    // through to the form below rather than bouncing away from the typed work.
    if (!$errors) {
        admin_redirect('/admin/team.php', 'Unrecognised action.', 'error');
    }
}

require __DIR__ . '/lib/layout.php';

if ($editId > 0 || $isNew) {
    $t = $editId > 0 ? one('SELECT * FROM team_members WHERE id = ?', [$editId]) : null;

    if ($editId > 0 && $t === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Team member not found', 'active' => '/admin/team.php']);
        echo '<div class="card"><p>That team member does not exist.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/team.php')) . '">Back</a></p></div>';
        admin_foot();
        exit;
    }

    // What was just typed wins over what is stored. On a normal GET $submitted
    // is null and this reads the row exactly as before.
    $v = static fn(string $k, string $d = ''): string => (string)($submitted[$k] ?? $t[$k] ?? $d);

    /** Renders the message attached to one field, if there is one. */
    $err = static function (string $k) use ($errors): string {
        return isset($errors[$k])
            ? '<p class="field-error">' . e($errors[$k]) . '</p>'
            : '';
    };

    /** Appended to a control's class so the field itself is marked, not just labelled. */
    $bad = static fn(string $k): string => isset($errors[$k]) ? ' class="has-error"' : '';

    /** Checkbox state: what was submitted wins, else the row, else the new-form default. */
    $checked = static fn(string $k, bool $default): bool
        => $submitted !== null ? !empty($submitted[$k]) : ($t !== null ? (bool)$t[$k] : $default);

    $selectedPhoto = (int)($submitted['photo_media_id'] ?? $t['photo_media_id'] ?? 0);

    // Newest first, capped: a <select> is a working picker without any of the
    // JS a thumbnail grid would need, but it does get unwieldy past a hundred
    // or so images.
    $library = all('
        SELECT id, original_name, filename
          FROM media
         WHERE mime IN (?, ?, ?, ?)
      ORDER BY created_at DESC
         LIMIT 300
    ', TEAM_PHOTO_MIMES);

    $currentPhoto = $selectedPhoto > 0
        ? one('SELECT filename, alt FROM media WHERE id = ?', [$selectedPhoto])
        : null;

    admin_head([
        'title'   => $t ? 'Edit team member' : 'New team member',
        'heading' => $t ? ($v('name') ?: 'Team member') : 'New team member',
        'intro'   => 'Shown on the public team page.',
        'active'  => '/admin/team.php',
    ]);
?>

<div class="card">
    <h2>Team member</h2>

<?php if ($errors): ?>
    <div class="flash flash-error">
        This team member was not saved. Nothing you typed has been lost — correct the
        <?= count($errors) === 1 ? 'field marked below' : 'fields marked below' ?> and save again.
    </div>
<?php endif; ?>

    <form method="post" action="team.php" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)$editId ?>">

        <div class="form-grid">
            <div class="field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" maxlength="120" required value="<?= e($v('name')) ?>"<?= $bad('name') ?>>
                <?= $err('name') ?>
            </div>

            <div class="field">
                <label for="role">Role</label>
                <input type="text" id="role" name="role" maxlength="120" required value="<?= e($v('role')) ?>" placeholder="Lead Developer"<?= $bad('role') ?>>
                <?= $err('role') ?>
            </div>
        </div>

        <div class="field">
            <label for="brief">Brief line</label>
            <input type="text" id="brief" name="brief" maxlength="200" required value="<?= e($v('brief')) ?>"<?= $bad('brief') ?>>
            <p class="hint">The one line visitors read before they open the card. Keep it short.</p>
            <?= $err('brief') ?>
        </div>

        <div class="field">
            <label for="bio">Full details</label>
            <textarea id="bio" name="bio" maxlength="2000"<?= $bad('bio') ?>><?= e($v('bio')) ?></textarea>
            <p class="hint">Revealed when someone taps the arrow on the card.</p>
            <?= $err('bio') ?>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="github_url">GitHub profile</label>
                <input type="url" id="github_url" name="github_url" maxlength="300" value="<?= e($v('github_url')) ?>" placeholder="https://github.com/username"<?= $bad('github_url') ?>>
                <?= $err('github_url') ?>
            </div>

            <div class="field">
                <label for="linkedin_url">LinkedIn profile</label>
                <input type="url" id="linkedin_url" name="linkedin_url" maxlength="300" value="<?= e($v('linkedin_url')) ?>" placeholder="https://linkedin.com/in/username"<?= $bad('linkedin_url') ?>>
                <?= $err('linkedin_url') ?>
            </div>

            <div class="field">
                <label for="sort_order">Sort order</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="999" value="<?= (int)($submitted['sort_order'] ?? $t['sort_order'] ?? 0) ?>">
            </div>
        </div>

        <div class="field">
            <label>Profile photo</label>

<?php if ($currentPhoto !== null): ?>
            <p><img src="<?= e(site_path('/uploads/' . rawurlencode((string)$currentPhoto['filename']))) ?>"
                    alt="<?= e((string)$currentPhoto['alt']) ?>" width="96" height="96"
                    style="width:96px;height:96px;object-fit:cover;border-radius:50%;margin-bottom:8px;"></p>
<?php endif; ?>

<?php if (can('media.upload')): ?>
            <input type="file" id="photo_file" name="photo_file"
                   accept="image/jpeg,image/png,image/gif,image/webp"<?= $bad('photo_file') ?>>
            <p class="hint">
                JPEG, PNG, GIF or WebP, up to <?= (int)(MEDIA_MAX_BYTES / 1024 / 1024) ?> MB. A square or
                portrait crop reads best — the card shows it as a circle. Uploading also adds the image to
                the <a href="<?= e(site_path('/admin/media.php')) ?>">media library</a>.
            </p>
            <?= $err('photo_file') ?>
<?php endif; ?>

<?php if ($library): ?>
            <label for="photo_media_id" style="margin-top:10px;">…or pick one already uploaded</label>
            <select id="photo_media_id" name="photo_media_id"<?= $bad('photo_media_id') ?>>
                <option value="0">— no photo —</option>
<?php foreach ($library as $m): ?>
                <option value="<?= (int)$m['id'] ?>"<?= $selectedPhoto === (int)$m['id'] ? ' selected' : '' ?>>
                    <?= e(str_trunc((string)($m['original_name'] !== '' ? $m['original_name'] : $m['filename']), 60)) ?>
                </option>
<?php endforeach; ?>
            </select>
            <p class="hint">Choosing a file above replaces whatever is selected here.</p>
            <?= $err('photo_media_id') ?>
<?php else: ?>
            <?php /* Keeps the current photo on save when there is nothing to pick
                     from yet — without it, editing a member with a photo and no
                     library would silently clear the photo. */ ?>
            <input type="hidden" name="photo_media_id" value="<?= (int)$selectedPhoto ?>">
<?php endif; ?>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_placeholder" name="is_placeholder" value="1"
                   <?= $checked('is_placeholder', true) ? 'checked' : '' ?>>
            <label for="is_placeholder">
                Still a placeholder
                <span class="hint">
                    Renders an orange PLACEHOLDER badge visitors can see, and keeps this person
                    out of the structured data search engines read. Untick when the name, photo
                    and details are all real.
                </span>
            </label>
        </div>

        <div class="field-check">
            <input type="checkbox" id="is_published" name="is_published" value="1"
                   <?= $checked('is_published', true) ? 'checked' : '' ?>>
            <label for="is_published">
                Show on the site
                <span class="hint">Untick to hide without deleting.</span>
            </label>
        </div>

        <div class="form-actions">
<?php if (can('content.edit')): ?>
            <button type="submit" class="btn">Save</button>
<?php endif; ?>
            <a class="btn btn-secondary" href="<?= e(site_path('/admin/team.php')) ?>">Back to all</a>
        </div>
    </form>
</div>

<?php if ($t && can('content.delete')): ?>
<div class="card">
    <h2>Delete</h2>
    <form method="post" action="team.php" data-confirm="Delete this team member?">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
        <button type="submit" class="btn btn-danger">Delete team member</button>
    </form>
</div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'     => '/admin/team.php',
    'sorts'    => [
        'order' => 't.sort_order',
        'name'  => 't.name',
        'role'  => 't.role',
        'state' => 't.is_placeholder',
    ],
    'default'     => 'order',
    'default_dir' => 'asc',
    'tiebreak'    => 't.id ASC',
    'search'      => ['t.name', 't.role', 't.brief', 't.bio'],
    'per_page'    => 25,
]);

$whereSql = list_where($st);

$total = (int)scalar('SELECT count(*) FROM team_members t' . $whereSql, $st['params']);

$rows = all('
    SELECT t.*, m.filename AS photo
      FROM team_members t
      LEFT JOIN media m ON m.id = t.photo_media_id
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

admin_head([
    'title'   => 'Team',
    'heading' => 'Team',
    'intro'   => 'People shown on the public team page.',
    'active'  => '/admin/team.php',
]);
?>

<div class="card">
<?php list_toolbar($st, $total, 'team member', 'team members',
        can('content.edit') ? '<a class="btn" href="' . e(site_path('/admin/team.php?new=1')) . '">New team member</a>' : ''); ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'order', '#', 'num') ?>
                    <?= list_th($st, 'name', 'Name') ?>
                    <?= list_th($st, 'role', 'Role') ?>
                    <th>Links</th>
                    <?= list_th($st, 'state', 'State') ?>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
                <?= list_empty_state(5, 'users', 'No team members yet.',
                        $st['q'] !== ''
                            ? 'No team member matches “' . $st['q'] . '”.'
                            : 'Add the people who should show on the team page.') ?>
<?php else: foreach ($rows as $r): ?>
                <tr>
                    <td class="num"><?= (int)$r['sort_order'] ?></td>
                    <td><a href="<?= e(site_path('/admin/team.php?id=' . (int)$r['id'])) ?>"><?= e((string)$r['name']) ?></a></td>
                    <td><?= e(str_trunc((string)$r['role'], 40)) ?></td>
                    <td>
                        <span class="badge <?= $r['github_url'] !== '' ? 'badge-ok' : 'badge-muted' ?>">GitHub</span>
                        <span class="badge <?= $r['linkedin_url'] !== '' ? 'badge-ok' : 'badge-muted' ?>">LinkedIn</span>
                    </td>
                    <td>
<?php if ($r['is_placeholder']): ?>
                        <span class="badge badge-warn">Placeholder</span>
<?php elseif (!$r['is_published']): ?>
                        <span class="badge badge-muted">Hidden</span>
<?php else: ?>
                        <span class="badge badge-ok">Live</span>
<?php endif; ?>
                    </td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
