<?php
/**
 * Shared list-page toolbar: search, sorting, pagination.
 *
 * Only leads.php ever had any of this. Every other list — posts, media, users,
 * case studies, testimonials, bundles — issued an unbounded SELECT with a fixed
 * ORDER BY, so the page grew without limit and the only way to find a row was
 * the browser's own find-in-page. Six copies of the same three controls was the
 * obvious next mistake, so it lives here instead.
 *
 * SORTING AND SQL INJECTION
 *
 * A sort control is the one place a list page is tempted to put a request value
 * into the SQL, because ORDER BY cannot take a bound parameter — a placeholder
 * there is a constant, not a column, and silently sorts by nothing. The rule in
 * inc/db.php still holds: the only thing that may be interpolated is an
 * identifier the application itself chose. So the caller passes a map of
 * public sort key => SQL expression, and the request can do exactly one thing:
 * pick a key out of that map. A key that is not in the map falls back to the
 * default. The user's string never reaches the query — not escaped, not quoted,
 * not at all. Direction is matched against two literals for the same reason.
 *
 * The search term IS bound, like every other value.
 */

/**
 * Read the request into a validated list state.
 *
 * @param array{
 *     base:string,
 *     sorts:array<string,string>,
 *     default?:string,
 *     default_dir?:string,
 *     tiebreak?:string,
 *     search?:list<string>,
 *     per_page?:int,
 *     extra?:array<string,string>
 * } $spec
 *   base      Path this list lives at, for building links.
 *   sorts     Public key => SQL expression. The whitelist.
 *   default   Which key to sort by when the request names none.
 *   tiebreak  Appended to every ORDER BY so pagination is deterministic; two
 *             rows with an equal sort key must not swap places between pages.
 *   search    Columns the search box looks in. Omit for no search box.
 *   extra     Other GET keys already validated by the caller, preserved across
 *             sort and pager links.
 *
 * @return array<string,mixed>
 */
function list_state(array $spec): array
{
    $sorts   = $spec['sorts'];
    $default = $spec['default'] ?? array_key_first($sorts);

    // The whitelist lookup. $requested is attacker-controlled; $sort is not,
    // because it can only ever be a key that this file's caller hardcoded.
    $requested = (string)($_GET['sort'] ?? '');
    $sort      = isset($sorts[$requested]) ? $requested : (string)$default;

    $dir = strtolower((string)($_GET['dir'] ?? '')) === 'asc' ? 'asc'
         : (strtolower((string)($_GET['dir'] ?? '')) === 'desc' ? 'desc'
         : ($spec['default_dir'] ?? 'desc'));

    $perPage = max(1, (int)($spec['per_page'] ?? 25));
    $page    = max(1, (int)($_GET['p'] ?? 1));
    $q       = trim((string)($_GET['q'] ?? ''));

    // Two literals, chosen by comparison. Never the request string itself.
    $orderSql = $sorts[$sort] . ($dir === 'asc' ? ' ASC' : ' DESC');
    if (!empty($spec['tiebreak'])) {
        $orderSql .= ', ' . $spec['tiebreak'];
    }

    $where  = [];
    $params = [];

    if ($q !== '' && !empty($spec['search'])) {
        $op    = sql_ilike();
        $like  = '%' . $q . '%';
        $parts = [];
        foreach ($spec['search'] as $col) {
            $parts[]  = $col . ' ' . $op . ' ?';
            $params[] = $like;
        }
        $where[] = '(' . implode(' OR ', $parts) . ')';
    }

    return [
        'base'      => site_path($spec['base']),
        'sorts'     => $sorts,
        'searchable'=> !empty($spec['search']),
        'q'         => $q,
        'sort'      => $sort,
        'dir'       => $dir,
        'page'      => $page,
        'per_page'  => $perPage,
        'offset'    => ($page - 1) * $perPage,
        'order_sql' => $orderSql,
        'where'     => $where,
        'params'    => $params,
        'extra'     => $spec['extra'] ?? [],
    ];
}

/** The WHERE clause built so far, including the leading keyword, or ''. */
function list_where(array $st, array $more = []): string
{
    $all = [...$st['where'], ...$more];
    return $all ? ' WHERE ' . implode(' AND ', $all) : '';
}

/**
 * A URL for this list with some parameters overridden.
 *
 * Every current parameter is carried through. The old audit.php pager
 * hardcoded `?p=N`, which silently dropped any filter the user had set the
 * moment they turned a page.
 */
function list_url(array $st, array $override = []): string
{
    $qs = array_merge($st['extra'], [
        'q'    => $st['q'],
        'sort' => $st['sort'],
        'dir'  => $st['dir'],
        'p'    => $st['page'] > 1 ? (string)$st['page'] : '',
    ], $override);

    $qs = array_filter($qs, static fn($v) => $v !== '' && $v !== null);

    $url = $st['base'] . ($qs ? '?' . http_build_query($qs) : '');
    return site_path($url);
}

/**
 * A sortable column header.
 *
 * Clicking the active column flips direction; clicking another switches to it
 * at the sort's natural direction — text ascending, dates and numbers
 * descending, because "newest first" is what someone means by sorting a date
 * column and making them click twice for it is a small daily irritation.
 */
function list_th(array $st, string $key, string $label, string $class = '', string $natural = 'asc'): string
{
    $isActive = $st['sort'] === $key;
    $nextDir  = $isActive ? ($st['dir'] === 'asc' ? 'desc' : 'asc') : $natural;

    // Turning a page and then re-sorting should show the first page of the new
    // order, not page 7 of it.
    $href = list_url($st, ['sort' => $key, 'dir' => $nextDir, 'p' => '']);

    $arrow = $isActive
        ? '<span class="sort-arrow" aria-hidden="true">' . ($st['dir'] === 'asc' ? '&#9650;' : '&#9660;') . '</span>'
        : '';

    $aria = $isActive ? ' aria-sort="' . ($st['dir'] === 'asc' ? 'ascending' : 'descending') . '"' : '';

    return '<th' . ($class !== '' ? ' class="' . e($class) . '"' : '') . $aria . '>'
         . '<a class="th-sort' . ($isActive ? ' is-sorted' : '') . '" href="' . e($href) . '">'
         . e($label) . $arrow . '</a></th>';
}

/**
 * The toolbar: result count, search box, and whatever primary action the page
 * has. Sort and direction ride along as hidden fields so searching does not
 * silently reset the order the user chose.
 *
 * $actions is trusted markup assembled by the calling page (a "New article"
 * button), never anything from a request.
 */
/**
 * @param array<string,string> $sortLabels
 *   Sort key => human label. Supply this for a list that is NOT a table and so
 *   has no column headers to click — the media grid, for instance. Leave it
 *   empty for a table and use list_th() on the headers instead; two ways to set
 *   the same thing on one page is a way to get them out of step.
 */
function list_toolbar(array $st, int $total, string $noun, string $nounPlural = '', string $actions = '', array $sortLabels = []): void
{
    $label = $total === 1 ? $noun : ($nounPlural !== '' ? $nounPlural : $noun . 's');
    ?>
    <form class="toolbar" method="get" action="<?= e(site_path($st['base'])) ?>">
<?php if ($sortLabels): ?>
        <div class="field">
            <label for="sort">Sort by</label>
            <select id="sort" name="sort">
<?php foreach ($sortLabels as $key => $text): if (!isset($st['sorts'][$key])) { continue; } ?>
                <option value="<?= e((string)$key) ?>"<?= $st['sort'] === $key ? ' selected' : '' ?>><?= e($text) ?></option>
<?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="dir">Order</label>
            <select id="dir" name="dir">
                <option value="desc"<?= $st['dir'] === 'desc' ? ' selected' : '' ?>>Newest / highest first</option>
                <option value="asc"<?= $st['dir'] === 'asc' ? ' selected' : '' ?>>Oldest / lowest first</option>
            </select>
        </div>
<?php else: ?>
        <input type="hidden" name="sort" value="<?= e($st['sort']) ?>">
        <input type="hidden" name="dir" value="<?= e($st['dir']) ?>">
<?php endif; ?>
<?php foreach ($st['extra'] as $k => $v): if ($v === '') { continue; } ?>
        <input type="hidden" name="<?= e((string)$k) ?>" value="<?= e((string)$v) ?>">
<?php endforeach; ?>

<?php if ($st['searchable']): ?>
        <div class="field">
            <label for="q">Search</label>
            <input type="search" id="q" name="q" value="<?= e($st['q']) ?>" placeholder="Search <?= e($nounPlural !== '' ? $nounPlural : $noun . 's') ?>">
        </div>
<?php endif; ?>
<?php if ($st['searchable'] || $sortLabels): ?>
        <button type="submit" class="btn btn-secondary">Apply</button>
<?php endif; ?>
<?php if ($st['q'] !== ''): ?>
        <a class="btn btn-secondary" href="<?= e(list_url($st, ['q' => '', 'p' => ''])) ?>">Clear</a>
<?php endif; ?>

        <span class="count"><strong><?= number_format($total) ?></strong> <?= e($label) ?></span>
        <span class="spacer"></span>
        <?= $actions ?>
    </form>
<?php
}

/**
 * The pager. One component, used everywhere.
 *
 * There were two before: a styled .pager on leads.php and a bare <p> on
 * audit.php whose links were hardcoded `?p=N` and therefore dropped every
 * other query parameter. This is the leads.php shape, with the link building
 * delegated to list_url() so that class of bug cannot come back.
 */
/**
 * A styled empty-state row for a data table.
 *
 * Every list table hand-rolled a plain `<td class="empty">text</td>`, while the
 * dashboard and the media grid used the richer `.empty-state` component (icon +
 * bold headline + explanation). This gives the tables the same treatment from
 * one place, so an empty Leads screen no longer looks like a rendering glitch.
 *
 * $colspan MUST match the table's column count or the row breaks the layout.
 */
function list_empty_state(int $colspan, string $icon, string $title, string $msg = ''): string
{
    return '<tr><td colspan="' . max(1, $colspan) . '" class="empty">'
         . '<span class="empty-state">' . icon($icon)
         . '<span class="empty-state-text"><strong>' . e($title) . '</strong>'
         . ($msg !== '' ? '<br>' . e($msg) : '') . '</span>'
         . '</span></td></tr>';
}

function list_pager(array $st, int $total): void
{
    $pages = max(1, (int)ceil($total / $st['per_page']));
    if ($pages < 2) {
        return;
    }

    $page  = $st['page'];
    $first = $st['offset'] + 1;
    $last  = min($total, $st['offset'] + $st['per_page']);
    ?>
    <div class="pager">
<?php if ($page > 1): ?>
        <a class="btn btn-secondary btn-sm" href="<?= e(list_url($st, ['p' => $page > 2 ? (string)($page - 1) : ''])) ?>" rel="prev">Previous</a>
<?php endif; ?>
        <span>Showing <?= number_format($first) ?>–<?= number_format($last) ?> of <?= number_format($total) ?> · page <?= $page ?> of <?= $pages ?></span>
        <span class="spacer"></span>
<?php if ($page < $pages): ?>
        <a class="btn btn-secondary btn-sm" href="<?= e(list_url($st, ['p' => (string)($page + 1)])) ?>" rel="next">Next</a>
<?php endif; ?>
    </div>
<?php
}
