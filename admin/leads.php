<?php
/**
 * Leads — list, detail, status workflow, notes, assignment, CSV export.
 *
 * List and detail share one file because the dashboard already deep-links
 * /admin/leads.php?id=<int> (admin/index.php:114) and that URL shape is
 * committed to.
 *
 * Capability model: the page is gated on leads.view, which a `viewer` holds.
 * Every mutating branch therefore re-checks for itself — a hidden button is
 * not access control.
 */

require __DIR__ . '/lib/bootstrap.php';
require_can('leads.view');

const LEAD_STATUSES = ['new', 'contacted', 'qualified', 'won', 'lost', 'spam'];

$detailId = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;

function lead_detail_guard(): void
{
    if (!can('leads.view')) {
        http_response_code(403);
        admin_head(['title' => 'Not permitted', 'heading' => 'Not permitted', 'active' => '/admin/leads.php']);
        echo '<div class="card"><p>You do not have permission to view this enquiry.</p></div>';
        admin_foot();
        exit;
    }
}

// ---------------------------------------------------------------------------
// Shared list filter (search + status + sort).
//
// Built here, ABOVE the export branch, so "Export CSV" downloads exactly the
// rows the current filter shows rather than the whole table — the export link
// already carries q/status/sort/dir. The list view further down reuses these
// same variables instead of rebuilding them.
// ---------------------------------------------------------------------------

require_once __DIR__ . '/lib/list.php';

$st = list_state([
    'base'        => '/admin/leads.php',
    'sorts'       => [
        'received' => 'l.created_at',
        'company'  => 'l.company_name',
        'status'   => 'l.status',
    ],
    'default'     => 'received',
    'default_dir' => 'desc',
    'tiebreak'    => 'l.id DESC',
    'search'      => ['l.company_name', 'l.description', 'l.contact_number', 'l.contact_name', 'l.contact_email'],
    'per_page'    => 25,
    // Carried through every sort and pager link so a chosen status survives them.
    'extra'       => ['status' => in_array((string)($_GET['status'] ?? ''), LEAD_STATUSES, true)
                                    ? (string)$_GET['status'] : ''],
]);
$search       = $st['q'];
$filterStatus = $st['extra']['status'];

// The status filter is specific to leads, so it is not part of the generic
// toolbar. Built as its own bound clause and merged into the shared WHERE the
// same way — nothing from the request is interpolated.
$statusWhere = [];
$statusParam = [];
if ($filterStatus !== '') {
    $statusWhere[] = 'l.status = ?';
    $statusParam[] = $filterStatus;
}

$whereSql  = list_where($st, $statusWhere);
$allParams = [...$st['params'], ...$statusParam];

// ---------------------------------------------------------------------------
// CSV export — must run before any output, since it sends its own headers.
// ---------------------------------------------------------------------------

if (($_GET['export'] ?? '') === 'csv') {
    require_can('leads.export');

    // Same WHERE and ORDER as the list, so the download matches what is on screen.
    $rows = all('
        SELECT l.created_at, l.contact_name, l.contact_email, l.company_name,
               l.contact_number, l.description, l.status, l.notes, u.name AS assigned_name
          FROM leads l
          LEFT JOIN users u ON u.id = l.assigned_to
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
    ', $allParams);

    audit('leads.export', 'leads', '', null, ['count' => count($rows)]);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="rafly-leads-' . date('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');

    // UTF-8 BOM: without it Excel on Windows reads the file as the system
    // codepage and mangles the rupee sign and any non-ASCII company name.
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, ['Received', 'Name', 'Email', 'Company', 'Contact', 'Requirements', 'Status', 'Notes', 'Assigned to']);

    foreach ($rows as $r) {
        // csv_safe() (inc/security.php) neutralises a leading = + - @, which
        // Excel and Sheets execute as a live formula. The same guard submit.php
        // already applies on write — reapplied here because this export also
        // carries admin-entered notes, which that guard never saw.
        fputcsv($out, [
            $r['created_at'],
            csv_safe((string)$r['contact_name']),
            csv_safe((string)$r['contact_email']),
            csv_safe((string)$r['company_name']),
            (string)$r['contact_number'],
            csv_safe((string)$r['description']),
            (string)$r['status'],
            csv_safe((string)$r['notes']),
            csv_safe((string)($r['assigned_name'] ?? '')),
        ]);
    }

    fclose($out);
    exit;
}

// ---------------------------------------------------------------------------
// Mutations
// ---------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();

    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    $before = $id > 0 ? one('SELECT * FROM leads WHERE id = ?', [$id]) : null;
    if ($before === null) {
        admin_redirect('/admin/leads.php', 'That enquiry no longer exists.', 'error');
    }

    if ($action === 'update') {
        require_can('leads.edit');

        $status = (string)($_POST['status'] ?? '');
        if (!in_array($status, LEAD_STATUSES, true)) {
            admin_redirect('/admin/leads.php?id=' . $id, 'Unknown status.', 'error');
        }

        // Empty select => unassigned. Cast to int first so a non-numeric value
        // cannot reach the query as anything but 0.
        $assigned = (int)($_POST['assigned_to'] ?? 0);
        $assigned = $assigned > 0 ? $assigned : null;

        if ($assigned !== null && scalar('SELECT id FROM users WHERE id = ?', [$assigned]) === null) {
            admin_redirect('/admin/leads.php?id=' . $id, 'That user no longer exists.', 'error');
        }

        $notes = str_cut(trim((string)($_POST['notes'] ?? '')), 5000);

        q('UPDATE leads SET status = ?, notes = ?, assigned_to = ?, updated_at = now() WHERE id = ?',
          [$status, $notes, $assigned, $id]);

        audit('lead.update', 'lead', $id, $before, one('SELECT * FROM leads WHERE id = ?', [$id]));
        admin_redirect('/admin/leads.php?id=' . $id, 'Enquiry updated.');
    }

    if ($action === 'delete') {
        require_can('leads.delete');

        q('DELETE FROM leads WHERE id = ?', [$id]);
        audit('lead.delete', 'lead', $id, $before, null);
        admin_redirect('/admin/leads.php', 'Enquiry deleted.', 'warn');
    }

    admin_redirect('/admin/leads.php', 'Unrecognised action.', 'error');
}

require __DIR__ . '/lib/layout.php';

// ---------------------------------------------------------------------------
// Detail view
// ---------------------------------------------------------------------------

if ($detailId > 0) {
    lead_detail_guard();

    $lead = one('
        SELECT l.*, u.name AS assigned_name
          FROM leads l
          LEFT JOIN users u ON u.id = l.assigned_to
         WHERE l.id = ?
    ', [$detailId]);

    if ($lead === null) {
        http_response_code(404);
        admin_head(['title' => 'Not found', 'heading' => 'Enquiry not found', 'active' => '/admin/leads.php']);
        echo '<div class="card"><p>That enquiry does not exist. It may have been deleted.</p>'
           . '<p><a class="btn btn-secondary" href="' . e(site_path('/admin/leads.php')) . '">Back to all enquiries</a></p></div>';
        admin_foot();
        exit;
    }

    $staff = all("SELECT id, name FROM users WHERE status = 'active' ORDER BY name");

    admin_head([
        'title'   => 'Enquiry from ' . $lead['company_name'],
        'heading' => $lead['company_name'],
        'intro'   => 'Received ' . date('j M Y \a\t H:i', strtotime((string)$lead['created_at'])),
        'active'  => '/admin/leads.php',
    ]);
    ?>

    <div class="card">
        <h2>Enquiry</h2>

        <table class="data">
            <tbody>
                <tr><th>Name</th><td><?= e($lead['contact_name']) ?></td></tr>
                <tr><th>Email</th><td><a href="mailto:<?= e($lead['contact_email']) ?>"><?= e($lead['contact_email']) ?></a></td></tr>
                <tr><th>Company</th><td><?= e($lead['company_name']) ?></td></tr>
                <tr>
                    <th>Contact</th>
                    <td>
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string)$lead['contact_number'])) ?>"><?= e($lead['contact_number']) ?></a>
<?php $waLink = whatsapp_link_to((string)$lead['contact_number'], 'Hi, this is Rafly — regarding your enquiry.'); ?>
<?php if ($waLink !== ''): ?>
                        &nbsp;·&nbsp;
                        <a href="<?= e($waLink) ?>" target="_blank" rel="noopener">WhatsApp</a>
<?php endif; ?>
                    </td>
                </tr>
                <tr><th>Requirements</th><td><?= nl2br(e($lead['description'])) ?></td></tr>
                <tr><th>Consent given</th><td><?= $lead['consent_given'] ? '<span class="badge badge-ok">Yes</span>' : '<span class="badge badge-danger">No</span>' ?></td></tr>
<?php if ($lead['source_page'] !== ''): ?>
                <tr><th>Source page</th><td><?= e($lead['source_page']) ?></td></tr>
<?php endif; ?>
<?php if ($lead['service_slug'] !== ''): ?>
                <tr><th>Service</th><td><?= e($lead['service_slug']) ?></td></tr>
<?php endif; ?>
                <tr><th>Received</th><td><?= e(date('j M Y, H:i', strtotime((string)$lead['created_at']))) ?></td></tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Handling</h2>
<?php if (!can('leads.edit')): ?>
        <p class="hint">You have read-only access to enquiries.</p>
<?php else: ?>
        <form method="post" action="leads.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$lead['id'] ?>">

            <div class="form-grid">
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
<?php foreach (LEAD_STATUSES as $s): ?>
                        <option value="<?= e($s) ?>"<?= $lead['status'] === $s ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
<?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="assigned_to">Assigned to</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="0">Nobody</option>
<?php foreach ($staff as $u): ?>
                        <option value="<?= (int)$u['id'] ?>"<?= (int)$lead['assigned_to'] === (int)$u['id'] ? ' selected' : '' ?>><?= e($u['name']) ?></option>
<?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="notes">Internal notes</label>
                <p class="hint">Not visible to the customer.</p>
                <textarea id="notes" name="notes"><?= e($lead['notes']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Save</button>
                <a class="btn btn-secondary" href="<?= e(site_path('/admin/leads.php')) ?>">Back to all</a>
                <span class="spacer"></span>
            </div>
        </form>
<?php endif; ?>
    </div>

<?php if (can('leads.delete')): ?>
    <div class="card">
        <h2>Delete</h2>
        <p class="hint">Permanent. The audit log keeps a record that it happened, but the enquiry itself is gone.</p>
        <form method="post" action="leads.php" data-confirm="Delete this enquiry permanently? This cannot be undone.">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$lead['id'] ?>">
            <button type="submit" class="btn btn-danger">Delete enquiry</button>
        </form>
    </div>
<?php endif; ?>

<?php
    admin_foot();
    exit;
}

// ---------------------------------------------------------------------------
// List view
// ---------------------------------------------------------------------------

// $st, $search, $filterStatus, $whereSql and $allParams were all built at the
// top of this file (above the export branch) so the CSV download and this list
// share one definition of the current filter. Nothing to rebuild here.

$total = (int)scalar('SELECT count(*) FROM leads l' . $whereSql, $allParams);

$rows = all('
    SELECT l.id, l.contact_name, l.company_name, l.contact_number, l.status, l.created_at,
           u.name AS assigned_name
      FROM leads l
      LEFT JOIN users u ON u.id = l.assigned_to
' . $whereSql . '
     ORDER BY ' . $st['order_sql'] . '
     LIMIT ? OFFSET ?
', [...$allParams, $st['per_page'], $st['offset']]);

$badge = static fn(string $s): string => match ($s) {
    'new'       => 'badge-warn',
    'won'       => 'badge-ok',
    'lost', 'spam' => 'badge-danger',
    default     => 'badge-muted',
};

admin_head([
    'title'   => 'Leads',
    'heading' => 'Leads',
    'intro'   => 'Every enquiry submitted through the site.',
    'active'  => '/admin/leads.php',
]);
?>

<div class="card">
    <form class="toolbar" method="get" action="<?= e(site_path('/admin/leads.php')) ?>">
        <?php /* Sort rides along as hidden fields so filtering does not silently
                 reset the order the column headers set. */ ?>
        <input type="hidden" name="sort" value="<?= e($st['sort']) ?>">
        <input type="hidden" name="dir" value="<?= e($st['dir']) ?>">

        <div class="field">
            <label for="q">Search</label>
            <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Name, email, company, phone or text">
        </div>

        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">All</option>
<?php foreach (LEAD_STATUSES as $s): ?>
                <option value="<?= e($s) ?>"<?= $filterStatus === $s ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
<?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-secondary">Apply</button>
<?php if ($filterStatus !== '' || $search !== ''): ?>
        <a class="btn btn-secondary" href="<?= e(site_path('/admin/leads.php')) ?>">Clear</a>
<?php endif; ?>

        <span class="count"><strong><?= number_format($total) ?></strong> <?= $total === 1 ? 'enquiry' : 'enquiries' ?></span>
        <span class="spacer"></span>

<?php if (can('leads.export')): ?>
        <a class="btn btn-secondary" href="<?= e(list_url($st, ['export' => 'csv'])) ?>">Export CSV</a>
<?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'received', 'Received', '', 'desc') ?>
                    <th>Name</th>
                    <?= list_th($st, 'company', 'Company', '', 'asc') ?>
                    <th>Contact</th>
                    <?= list_th($st, 'status', 'Status', '', 'asc') ?>
                    <th>Assigned</th>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
<?php $noFilters = $total === 0 && $filterStatus === '' && $search === ''; ?>
                <?= list_empty_state(6, 'mail-open',
                        $noFilters ? 'No enquiries yet.' : 'No matching enquiries.',
                        $noFilters
                            ? 'Enquiries submitted through the site will appear here.'
                            : 'No enquiry matches the current search or status filter.') ?>
<?php else: foreach ($rows as $l): ?>
                <tr>
                    <td><?= e(date('j M Y, H:i', strtotime((string)$l['created_at']))) ?></td>
                    <td><?= e($l['contact_name']) ?></td>
                    <td><a href="<?= e(site_path('/admin/leads.php?id=' . (int)$l['id'])) ?>"><?= e($l['company_name']) ?></a></td>
                    <td><?= e($l['contact_number']) ?></td>
                    <td><span class="badge <?= $badge((string)$l['status']) ?>"><?= e($l['status']) ?></span></td>
                    <td><?= $l['assigned_name'] !== null ? e($l['assigned_name']) : '<span class="badge badge-muted">—</span>' ?></td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
</div>

<?php admin_foot(); ?>
