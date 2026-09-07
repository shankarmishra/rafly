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
const CRM_STAGES = ['new', 'contacted', 'qualified', 'discovery', 'proposal', 'won', 'lost'];

$detailId = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$viewMode = (string)($_GET['view'] ?? 'list');

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
    'extra'       => [
        'status' => in_array((string)($_GET['status'] ?? ''), LEAD_STATUSES, true) ? (string)$_GET['status'] : '',
        'view'   => $viewMode,
    ],
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
               l.contact_number, l.description, l.status, l.deal_stage, l.qualification_score, l.notes, u.name AS assigned_name
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

    fputcsv($out, ['Received', 'Name', 'Email', 'Company', 'Contact', 'Requirements', 'Status', 'Stage', 'Score', 'Notes', 'Assigned to']);

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
            (string)($r['deal_stage'] ?? 'new'),
            (int)($r['qualification_score'] ?? 0),
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

        $status    = (string)($_POST['status'] ?? $before['status']);
        $dealStage = (string)($_POST['deal_stage'] ?? ($before['deal_stage'] ?? 'new'));

        if (!in_array($status, LEAD_STATUSES, true)) {
            $status = $before['status'];
        }
        if (!in_array($dealStage, CRM_STAGES, true)) {
            $dealStage = $before['deal_stage'] ?? 'new';
        }

        // Empty select => unassigned. Cast to int first so a non-numeric value
        // cannot reach the query as anything but 0.
        $assigned = (int)($_POST['assigned_to'] ?? 0);
        $assigned = $assigned > 0 ? $assigned : null;

        if ($assigned !== null && scalar('SELECT id FROM users WHERE id = ?', [$assigned]) === null) {
            admin_redirect('/admin/leads.php?id=' . $id, 'That user no longer exists.', 'error');
        }

        $notes          = str_cut(trim((string)($_POST['notes'] ?? '')), 5000);
        $budgetBracket  = trim((string)($_POST['budget_bracket'] ?? ''));
        $urgencyLevel   = trim((string)($_POST['urgency_level'] ?? ''));
        
        // Calculate score from matrix criteria (0-100)
        $score = 0;
        if (!empty($_POST['score_legitimacy'])) $score += 20;
        if (!empty($_POST['score_budget']))     $score += 30;
        if (!empty($_POST['score_urgency']))    $score += 20;
        if (!empty($_POST['score_dm']))         $score += 15;
        if (!empty($_POST['score_tech']))       $score += 15;
        
        // If score explicit numeric post is sent, override if valid
        if (isset($_POST['qualification_score'])) {
            $score = max(0, min(100, (int)$_POST['qualification_score']));
        }

        q('UPDATE leads SET status = ?, deal_stage = ?, qualification_score = ?, budget_bracket = ?, urgency_level = ?, notes = ?, assigned_to = ?, updated_at = now() WHERE id = ?',
          [$status, $dealStage, $score, $budgetBracket, $urgencyLevel, $notes, $assigned, $id]);

        audit('lead.update', 'lead', $id, $before, one('SELECT * FROM leads WHERE id = ?', [$id]));
        $redirectUrl = ($viewMode === 'kanban') ? '/admin/leads.php?view=kanban' : '/admin/leads.php?id=' . $id;
        admin_redirect($redirectUrl, 'Enquiry and qualification updated.');
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
        <h2>Lead Qualification Scoring Matrix (SOP 10)</h2>
        <?php
        $score = (int)($lead['qualification_score'] ?? 0);
        $scoreBadge = match (true) {
            $score >= 70 => '<span class="badge badge-ok" style="font-size:1em; padding:6px 12px;">Score: ' . $score . ' / 100 — HIGH FIT (Schedule 15-Min Discovery)</span>',
            $score >= 40 => '<span class="badge badge-warn" style="font-size:1em; padding:6px 12px;">Score: ' . $score . ' / 100 — MEDIUM FIT (Automated Audit / Nurture)</span>',
            default      => '<span class="badge badge-danger" style="font-size:1em; padding:6px 12px;">Score: ' . $score . ' / 100 — LOW FIT / DISQUALIFIED</span>',
        };
        ?>
        <p><?= $scoreBadge ?></p>

        <?php if (can('leads.edit')): ?>
        <form method="post" action="leads.php" style="margin-top:16px;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$lead['id'] ?>">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; background:#F8FAFC; padding:16px; border-radius:8px; border:1px solid #E2E8F0;">
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="score_legitimacy" value="1" <?= $score >= 20 ? 'checked' : '' ?>>
                    <strong>Business Legitimacy (+20 pts)</strong>
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="score_budget" value="1" <?= $score >= 50 || $score === 30 ? 'checked' : '' ?>>
                    <strong>Budget Alignment &ge; ₹25,000 (+30 pts)</strong>
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="score_urgency" value="1" <?= ($score % 30 >= 20 || $score >= 70) ? 'checked' : '' ?>>
                    <strong>Urgency (Need in 1-4 weeks) (+20 pts)</strong>
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="score_dm" value="1" <?= ($score % 15 === 0 && $score > 0) ? 'checked' : '' ?>>
                    <strong>Decision Maker Direct Contact (+15 pts)</strong>
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="score_tech" value="1" <?= ($score % 5 === 0 && $score > 0) ? 'checked' : '' ?>>
                    <strong>Technical Fit (Build / Protect / Grow) (+15 pts)</strong>
                </label>
            </div>

            <div class="form-grid" style="margin-top:16px;">
                <div class="field">
                    <label for="budget_bracket">Budget Bracket</label>
                    <input type="text" id="budget_bracket" name="budget_bracket" value="<?= e($lead['budget_bracket'] ?? '') ?>" placeholder="e.g. ₹25,000 - ₹50,000">
                </div>
                <div class="field">
                    <label for="urgency_level">Urgency Level</label>
                    <input type="text" id="urgency_level" name="urgency_level" value="<?= e($lead['urgency_level'] ?? '') ?>" placeholder="e.g. Immediate (1-2 weeks)">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="margin-top:12px;">Recalculate & Save Score</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Handling & Pipeline Stage</h2>
<?php if (!can('leads.edit')): ?>
        <p class="hint">You have read-only access to enquiries.</p>
<?php else: ?>
        <form method="post" action="leads.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$lead['id'] ?>">

            <div class="form-grid">
                <div class="field">
                    <label for="status">Lead Status</label>
                    <select id="status" name="status">
<?php foreach (LEAD_STATUSES as $s): ?>
                        <option value="<?= e($s) ?>"<?= $lead['status'] === $s ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
<?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="deal_stage">CRM Deal Stage</label>
                    <select id="deal_stage" name="deal_stage">
<?php foreach (CRM_STAGES as $stg): ?>
                        <option value="<?= e($stg) ?>"<?= ($lead['deal_stage'] ?? 'new') === $stg ? ' selected' : '' ?>><?= e(ucfirst($stg)) ?></option>
<?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="assigned_to">Assigned owner</label>
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
// List / Kanban View
// ---------------------------------------------------------------------------

$total = (int)scalar('SELECT count(*) FROM leads l' . $whereSql, $allParams);

$rows = all('
    SELECT l.id, l.contact_name, l.company_name, l.contact_number, l.status, l.deal_stage, l.qualification_score, l.created_at,
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
    'title'   => 'CRM & Leads Pipeline',
    'heading' => 'CRM & Leads Pipeline',
    'intro'   => 'Manage lead qualification and sales pipeline stages.',
    'active'  => '/admin/leads.php',
]);
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div style="display:flex; gap:8px;">
            <a class="btn <?= $viewMode !== 'kanban' ? 'btn-primary' : 'btn-secondary' ?>" href="<?= e(site_path('/admin/leads.php?view=list')) ?>">Table View</a>
            <a class="btn <?= $viewMode === 'kanban' ? 'btn-primary' : 'btn-secondary' ?>" href="<?= e(site_path('/admin/leads.php?view=kanban')) ?>">Visual CRM Kanban Board</a>
        </div>
<?php if (can('leads.export')): ?>
        <a class="btn btn-secondary" href="<?= e(list_url($st, ['export' => 'csv'])) ?>">Export CSV</a>
<?php endif; ?>
    </div>

    <form class="toolbar" method="get" action="<?= e(site_path('/admin/leads.php')) ?>">
        <input type="hidden" name="view" value="<?= e($viewMode) ?>">
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
        <a class="btn btn-secondary" href="<?= e(site_path('/admin/leads.php?view=' . e($viewMode))) ?>">Clear</a>
<?php endif; ?>

        <span class="count"><strong><?= number_format($total) ?></strong> <?= $total === 1 ? 'enquiry' : 'enquiries' ?></span>
    </form>

<?php if ($viewMode === 'kanban'): ?>
    <?php
    $kanbanLeads = all('
        SELECT l.id, l.contact_name, l.company_name, l.contact_number, l.status, l.deal_stage, l.qualification_score, l.created_at,
               u.name AS assigned_name
          FROM leads l
          LEFT JOIN users u ON u.id = l.assigned_to
         ORDER BY l.id DESC
    ');
    
    $cols = [];
    foreach (CRM_STAGES as $stg) {
        $cols[$stg] = [];
    }
    foreach ($kanbanLeads as $kl) {
        $stageKey = !empty($kl['deal_stage']) && isset($cols[$kl['deal_stage']]) ? $kl['deal_stage'] : 'new';
        $cols[$stageKey][] = $kl;
    }
    ?>

    <div style="display:flex; gap:12px; overflow-x:auto; padding-bottom:16px; margin-top:16px;">
        <?php foreach (CRM_STAGES as $stg): ?>
            <div style="flex:0 0 280px; background:#F1F5F9; border-radius:8px; padding:12px; border:1px solid #E2E8F0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h3 style="font-size:14px; text-transform:uppercase; font-weight:700; margin:0; color:#334155;">
                        <?= e(ucfirst($stg)) ?>
                    </h3>
                    <span class="badge badge-muted" style="font-weight:bold;"><?= count($cols[$stg]) ?></span>
                </div>

                <div style="display:flex; flex-direction:column; gap:8px;">
                    <?php if (empty($cols[$stg])): ?>
                        <div style="text-align:center; padding:20px; font-size:12px; color:#94A3B8; border:1px dashed #CBD5E1; border-radius:6px;">No leads</div>
                    <?php else: ?>
                        <?php foreach ($cols[$stg] as $item): ?>
                            <div style="background:#FFFFFF; padding:12px; border-radius:6px; border:1px solid #CBD5E1; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                                <a href="<?= e(site_path('/admin/leads.php?id=' . (int)$item['id'])) ?>" style="font-weight:700; color:#0F172A; text-decoration:none; display:block; font-size:14px; margin-bottom:4px;">
                                    <?= e($item['company_name']) ?>
                                </a>
                                <div style="font-size:12px; color:#64748B; margin-bottom:6px;"><?= e($item['contact_name']) ?> &middot; <?= e($item['contact_number']) ?></div>
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; font-size:11px;">
                                    <span class="badge <?= $badge((string)$item['status']) ?>"><?= e($item['status']) ?></span>
                                    <span style="font-weight:bold; color:#0284C7; background:#E0F2FE; padding:2px 6px; border-radius:4px;">Score: <?= (int)$item['qualification_score'] ?></span>
                                </div>
                                <form method="post" action="leads.php" style="margin-top:8px;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <input type="hidden" name="view" value="kanban">
                                    <select name="deal_stage" onchange="this.form.submit()" style="font-size:11px; padding:2px 4px; width:100%; border-radius:4px; border:1px solid #CBD5E1;">
                                        <?php foreach (CRM_STAGES as $sOpt): ?>
                                            <option value="<?= e($sOpt) ?>" <?= $item['deal_stage'] === $sOpt ? 'selected' : '' ?>>Move to: <?= e(ucfirst($sOpt)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <?= list_th($st, 'received', 'Received', '', 'desc') ?>
                    <th>Name</th>
                    <?= list_th($st, 'company', 'Company', '', 'asc') ?>
                    <th>Contact</th>
                    <th>CRM Stage</th>
                    <th>Qual. Score</th>
                    <?= list_th($st, 'status', 'Status', '', 'asc') ?>
                    <th>Assigned</th>
                </tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
<?php $noFilters = $total === 0 && $filterStatus === '' && $search === ''; ?>
                <?= list_empty_state(8, 'mail-open',
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
                    <td><span class="badge badge-muted" style="text-transform:capitalize;"><?= e($l['deal_stage'] ?? 'new') ?></span></td>
                    <td><strong style="color:#0284C7;"><?= (int)($l['qualification_score'] ?? 0) ?> pts</strong></td>
                    <td><span class="badge <?= $badge((string)$l['status']) ?>"><?= e($l['status']) ?></span></td>
                    <td><?= $l['assigned_name'] !== null ? e($l['assigned_name']) : '<span class="badge badge-muted">—</span>' ?></td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

<?php list_pager($st, $total); ?>
<?php endif; ?>

</div>

<?php admin_foot(); ?>

