<?php
/**
 * Site settings.
 *
 * Rendered generically from the settings table: grouped by group_name, ordered
 * by sort_order, with the input type driven by the `type` column. Adding a
 * setting is an INSERT, not a code change — which is the whole reason the table
 * is key/value rather than one wide row.
 *
 * Readable by every role (settings.view); writable only by admin
 * (settings.edit).
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('settings.view');

/** Friendly headings for the seeded groups. Unknown groups fall back to the key. */
const SETTING_GROUP_LABELS = [
    'contact'   => 'Contact details',
    'trust'     => 'Trust bar figures',
    'social'    => 'Social links',
    'analytics' => 'Analytics & Tracking',
    'general'   => 'General',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    require_can('settings.edit');

    $submitted = (array)($_POST['settings'] ?? []);

    // Only keys that already exist are written. A POST cannot invent a setting,
    // so a crafted form body cannot pollute the table.
    $known = [];
    // "key" is quoted throughout: it is a reserved word in MySQL. inc/db.php
    // turns on ANSI_QUOTES there so this one spelling works on both drivers.
    foreach (all('SELECT "key", value, type FROM settings') as $row) {
        $known[$row['key']] = $row;
    }

    $user    = current_user();
    $changed = [];

    tx(static function () use ($submitted, $known, $user, &$changed): void {
        foreach ($known as $key => $row) {
            if ($row['type'] === 'bool') {
                // An unchecked checkbox sends nothing, so absence means false.
                $value = isset($submitted[$key]) ? '1' : '0';
            } else {
                if (!array_key_exists($key, $submitted)) {
                    continue;               // field not on this form
                }
                $value = str_cut(trim((string)$submitted[$key]), 2000);
            }

            if ($value === $row['value']) {
                continue;
            }

            q('UPDATE settings SET value = ?, updated_at = now(), updated_by = ? WHERE "key" = ?',
              [$value, $user['id'] ?? null, $key]);

            $changed[$key] = ['from' => $row['value'], 'to' => $value];
        }
    });

    if ($changed) {
        audit('settings.update', 'settings', '', null, $changed);
        admin_redirect('/admin/settings.php', count($changed) . ' setting(s) saved.');
    }

    admin_redirect('/admin/settings.php', 'Nothing changed.', 'warn');
}

require __DIR__ . '/lib/layout.php';

$rows   = all('SELECT * FROM settings ORDER BY group_name, sort_order, "key"');
$groups = [];
foreach ($rows as $row) {
    $groups[$row['group_name']][] = $row;
}

$editable = can('settings.edit');

admin_head([
    'title'   => 'Settings',
    'heading' => 'Settings',
    'intro'   => 'Values the site renders. Changes take effect immediately.',
    'active'  => '/admin/settings.php',
]);
?>

<?php if (!$editable): ?>
<div class="card">
    <h2>Read-only</h2>
    <p class="hint">Only administrators can change settings.</p>
</div>
<?php endif; ?>

<form method="post" action="settings.php">
    <?= csrf_field() ?>

<?php foreach ($groups as $group => $items): ?>
    <div class="card">
        <h2><?= e(SETTING_GROUP_LABELS[$group] ?? ucfirst($group)) ?></h2>

<?php foreach ($items as $s):
          $id    = 'set_' . preg_replace('/[^a-z0-9]+/i', '_', (string)$s['key']);
          $name  = 'settings[' . $s['key'] . ']';
          $value = (string)$s['value'];

          if ($s['type'] === 'bool'): ?>
        <div class="field-check">
            <input type="checkbox" id="<?= e($id) ?>" name="<?= e($name) ?>" value="1"
                   <?= $value === '1' ? 'checked' : '' ?><?= $editable ? '' : ' disabled' ?>>
            <label for="<?= e($id) ?>">
                <?= e($s['label'] ?: $s['key']) ?>
<?php if ($s['hint'] !== ''): ?>
                <span class="hint"><?= e($s['hint']) ?></span>
<?php endif; ?>
            </label>
        </div>
<?php     elseif ($s['type'] === 'textarea'): ?>
        <div class="field">
            <label for="<?= e($id) ?>"><?= e($s['label'] ?: $s['key']) ?></label>
<?php if ($s['hint'] !== ''): ?>
            <p class="hint"><?= e($s['hint']) ?></p>
<?php endif; ?>
            <textarea id="<?= e($id) ?>" name="<?= e($name) ?>"<?= $editable ? '' : ' disabled' ?>><?= e($value) ?></textarea>
        </div>
<?php     else:
              // text / number / url / email / tel all map to a matching input type,
              // which gets mobile keyboards and browser validation for free.
              $inputType = in_array($s['type'], ['number', 'url', 'email', 'tel'], true) ? $s['type'] : 'text';
?>
        <div class="field">
            <label for="<?= e($id) ?>"><?= e($s['label'] ?: $s['key']) ?></label>
<?php if ($s['hint'] !== ''): ?>
            <p class="hint"><?= e($s['hint']) ?></p>
<?php endif; ?>
            <input type="<?= e($inputType) ?>" id="<?= e($id) ?>" name="<?= e($name) ?>"
                   value="<?= e($value) ?>"<?= $editable ? '' : ' disabled' ?>>
        </div>
<?php     endif;
      endforeach; ?>
    </div>
<?php endforeach; ?>

<?php if (!$groups): ?>
    <div class="card">
        <p>No settings have been seeded. Run <code>php inc/tools/seed.php</code>.</p>
    </div>
<?php endif; ?>

<?php if ($editable && $groups): ?>
    <div class="card">
        <div class="form-actions">
            <button type="submit" class="btn">Save settings</button>
        </div>
    </div>
<?php endif; ?>
</form>

<?php admin_foot(); ?>
