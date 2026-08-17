<?php
/**
 * Audit log viewer.
 *
 * Read-only by design. An audit trail that can be edited from the interface it
 * audits is not an audit trail — if a row is wrong, that is a fact worth
 * keeping, not one worth deleting.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('audit.view');
require_once __DIR__ . '/lib/list.php';
require __DIR__ . '/lib/layout.php';

// This page had the second of the two pagers: a bare <p> whose links were
// written as `?p=<n>`, which threw away every other query parameter the moment
// you turned a page. It now uses the same component as every other list, whose
// links are built by list_url() from the full current state — so a filtered
// second page stays filtered.
$st = list_state([
    'base'     => '/admin/audit.php',
    'sorts'    => [
        'when'   => 'a.created_at',
        'action' => 'a.action',
        'entity' => 'a.entity_type',
        'who'    => 'u.name',
    ],
    'default'  => 'when',
    'tiebreak' => 'a.id DESC',
    'search'   => ['a.action', 'a.entity_type', 'a.ip', 'u.name', 'u.email'],
    'per_page' => 50,
]);

$whereSql = list_where($st);

$total = (int)scalar('
    SELECT count(*) FROM audit_log a LEFT JOIN users u ON u.id = a.user_id' . $whereSql,
    $st['params']);

$rows = all('
    SELECT a.id, a.action, a.entity_type, a.entity_id, a.ip, a.created_at,
           u.name AS user_name, u.email AS user_email
      FROM audit_log a
      LEFT JOIN users u ON u.id = a.user_id
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$st['params'], $st['per_page'], $st['offset']]);

admin_head([
    'title'   => 'Audit log',
    'heading' => 'Audit log',
    'intro'   => 'Every change made through the admin. Read-only.',
    'active'  => '/admin/audit.php',
]);
?>

<div class="card">
<?php list_toolbar($st, $total, 'recorded action', 'recorded actions'); ?>

    <p class="hint">Entries are never edited or removed from here.</p>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'when', 'When', '', 'desc') ?>
                    <?= list_th($st, 'who', 'Who') ?>
                    <?= list_th($st, 'action', 'Action') ?>
                    <?= list_th($st, 'entity', 'Entity') ?>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
                <?= list_empty_state(5, 'history',
                        $st['q'] !== '' ? 'No matching activity.' : 'Nothing recorded yet.',
                        $st['q'] !== ''
                            ? 'No log entry matches “' . $st['q'] . '”.'
                            : 'Admin actions are recorded here as they happen.') ?>
<?php else: foreach ($rows as $r): ?>
                <tr>
                    <td><?= e(date('j M Y, H:i:s', strtotime((string)$r['created_at']))) ?></td>
                    <td><?= $r['user_name'] !== null ? e($r['user_name']) : '<span class="badge badge-muted">deleted user</span>' ?></td>
                    <td><span class="badge"><?= e($r['action']) ?></span></td>
                    <td><?= $r['entity_type'] !== '' ? e($r['entity_type'] . ' #' . $r['entity_id']) : '—' ?></td>
                    <td><?= e($r['ip']) ?></td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
