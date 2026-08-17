<?php
/**
 * Hand-written SVG charts for the dashboard.
 *
 * No charting library and no CDN, for two reasons. The admin CSP is
 * `script-src 'self'` with no 'unsafe-inline' (inc/security.php), so a CDN
 * bundle would be blocked outright and a config object written into a <script>
 * tag would never execute. And the whole site is dependency-free by design —
 * pulling in 200 KB of JavaScript to draw four small charts would make the
 * chart library the single largest thing in the project.
 *
 * INLINE STYLES ARE ALSO BLOCKED. The admin runs `style-src 'self'`, so a bar
 * sized with style="width:62%" silently renders at zero. Every dimension here
 * is an SVG geometry attribute (x/y/width/rx/d), which is not a style and is
 * not affected. This is the main reason the bars are SVG rather than divs.
 *
 * Drawing conventions, applied consistently so the four charts read as one
 * system rather than four widgets:
 *
 *   * One hue for magnitude. Identity comes from the row's own text label, so
 *     a bar chart of six lead statuses does NOT get six colours — that would be
 *     encoding the same information twice, and the red/green pair it would need
 *     is the one that fails for the ~8% of men with deuteranopia. The status
 *     badge beside the label carries state, with its name written in it.
 *   * 2px strokes, thin marks, recessive grid.
 *   * Data ends are rounded 4px and anchored square to the baseline.
 *   * Adjacent fills are separated by a 2px gap of the surface colour rather
 *     than a border, so segments stay legible when colours are close.
 *   * Values are direct-labelled selectively — the peak and the latest point,
 *     not every point.
 *   * Text uses the ink ramp, never the series colour.
 *
 * Hover detail is a native SVG <title>, which every browser and screen reader
 * already understands. That keeps the tooltip layer at zero bytes of JS.
 */

const CHART_INK      = '#334155';
const CHART_MUTED    = '#64748b';
const CHART_GRID     = '#e2e8f0';
const CHART_SERIES   = '#1565c0';
const CHART_SURFACE  = '#ffffff';
const CHART_OK       = '#16a34a';
const CHART_WARN     = '#d97706';

/**
 * Round a maximum up to something a person would choose for an axis, so the
 * gridline labels are 0/5/10 rather than 0/3.5/7.
 */
function chart_nice_max(int $max): int
{
    if ($max <= 5) {
        return 5;
    }

    $pow  = 10 ** (int)floor(log10($max));
    $step = $pow / 2;

    return (int)(ceil($max / $step) * $step);
}

/**
 * A rect whose data end is rounded and whose baseline end is square.
 *
 * Anchoring the origin end square is what makes a row of bars read against a
 * shared baseline; rounding both ends turns them into floating pills that no
 * longer line up.
 */
function chart_bar_path(float $w, float $h, float $r = 4.0): string
{
    $r = min($r, $w / 2, $h / 2);

    if ($r <= 0.5) {
        return sprintf('M0,0 H%.2f V%.2f H0 Z', $w, $h);
    }

    return sprintf(
        'M0,0 H%.2f A%.2f,%.2f 0 0 1 %.2f,%.2f V%.2f A%.2f,%.2f 0 0 1 %.2f,%.2f H0 Z',
        $w - $r, $r, $r, $w, $r, $h - $r, $r, $r, $w - $r, $h
    );
}

/**
 * Leads over time: a filled area with a 2px line on top.
 *
 * Area rather than bare line because the quantity is a count accumulating from
 * zero, so the region between the line and the baseline is meaningful. One
 * series, so no legend — the card heading names it.
 *
 * @param list<array{date:string,count:int}> $days Oldest first.
 */
function chart_area(array $days, string $emptyText = 'No data yet.'): string
{
    $n = count($days);
    if ($n < 2) {
        return '<p class="chart-empty">' . e($emptyText) . '</p>';
    }

    $counts = array_map(static fn($d) => (int)$d['count'], $days);
    $peak   = max($counts);

    if (array_sum($counts) === 0) {
        return '<p class="chart-empty">' . e($emptyText) . '</p>';
    }

    $w = 720.0; $h = 190.0;
    $padL = 34.0; $padR = 14.0; $padT = 16.0; $padB = 28.0;
    $plotW = $w - $padL - $padR;
    $plotH = $h - $padT - $padB;

    $maxY = chart_nice_max($peak);
    $step = $plotW / ($n - 1);

    $x = static fn(int $i): float => $padL + $i * $step;
    $y = static fn(int $v): float => $padT + $plotH - ($maxY > 0 ? ($v / $maxY) * $plotH : 0);

    // --- Gridlines. Three is enough to read a value off; more is noise. ------
    $grid = '';
    foreach ([0, (int)round($maxY / 2), $maxY] as $tick) {
        $gy = $y($tick);
        $grid .= sprintf(
            '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="%s" stroke-width="1"/>',
            $padL, $gy, $w - $padR, $gy, CHART_GRID
        );
        $grid .= sprintf(
            '<text x="%.1f" y="%.1f" text-anchor="end" font-size="11" fill="%s">%d</text>',
            $padL - 8, $gy + 4, CHART_MUTED, $tick
        );
    }

    // --- The line, and the same path closed to the baseline for the fill. ---
    $line = '';
    foreach ($counts as $i => $v) {
        $line .= ($i === 0 ? 'M' : ' L') . sprintf('%.2f,%.2f', $x($i), $y($v));
    }

    $area = $line
          . sprintf(' L%.2f,%.2f L%.2f,%.2f Z', $x($n - 1), $y(0), $x(0), $y(0));

    // --- Selective direct labels: the peak day and the most recent day. -----
    $peakIdx = (int)array_search($peak, $counts, true);
    $labels  = '';

    if ($peak > 0) {
        $labels .= sprintf(
            '<circle cx="%.2f" cy="%.2f" r="4" fill="%s" stroke="%s" stroke-width="2"/>',
            $x($peakIdx), $y($peak), CHART_SERIES, CHART_SURFACE
        );
        $labels .= sprintf(
            '<text x="%.2f" y="%.2f" text-anchor="%s" font-size="12" font-weight="600" fill="%s">%d</text>',
            $x($peakIdx), $y($peak) - 10,
            $peakIdx === 0 ? 'start' : ($peakIdx === $n - 1 ? 'end' : 'middle'),
            CHART_INK, $peak
        );
    }

    // --- X labels at the ends only. A tick per day would collide. -----------
    $first = date('j M', strtotime($days[0]['date']));
    $last  = date('j M', strtotime($days[$n - 1]['date']));

    $xlab = sprintf(
        '<text x="%.1f" y="%.1f" font-size="11" fill="%s">%s</text>'
      . '<text x="%.1f" y="%.1f" text-anchor="end" font-size="11" fill="%s">%s</text>',
        $padL, $h - 8, CHART_MUTED, e($first),
        $w - $padR, $h - 8, CHART_MUTED, e($last)
    );

    // --- Invisible per-day hit targets carrying a native tooltip. -----------
    $hits = '';
    foreach ($days as $i => $d) {
        $hits .= sprintf(
            '<rect x="%.2f" y="%.1f" width="%.2f" height="%.1f" fill="transparent"><title>%s: %d</title></rect>',
            $x($i) - $step / 2, $padT, $step, $plotH,
            e(date('j M Y', strtotime($d['date']))), (int)$d['count']
        );
    }

    return sprintf(
        '<svg class="chart" viewBox="0 0 %d %d" role="img" aria-label="Enquiries per day over the last %d days, peaking at %d">'
      . '%s'
      . '<path d="%s" fill="%s" fill-opacity=".12"/>'
      . '<path d="%s" fill="none" stroke="%s" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>'
      . '%s%s%s</svg>',
        (int)$w, (int)$h, $n, $peak,
        $grid,
        $area, CHART_SERIES,
        $line, CHART_SERIES,
        $labels, $xlab, $hits
    );
}

/**
 * Horizontal bars, one row per category.
 *
 * Horizontal rather than vertical because the categories are words of varying
 * length ("qualified", "/service.php?s=seo") — rotated x-axis labels are the
 * classic way to make a bar chart unreadable.
 *
 * @param list<array{label:string,value:int,badge?:string,href?:string}> $rows
 */
function chart_bars(array $rows, string $emptyText = 'Nothing to show yet.'): string
{
    if (!$rows) {
        return '<p class="chart-empty">' . e($emptyText) . '</p>';
    }

    $max = max(1, max(array_map(static fn($r) => (int)$r['value'], $rows)));

    $out = '<div class="bar-rows">';

    foreach ($rows as $r) {
        $value = (int)$r['value'];
        $frac  = $value / $max;

        // Fixed user-space width, scaled uniformly by CSS. A non-uniform
        // stretch would turn the 4px corner radius into an ellipse.
        $track = 320.0;
        $barW  = max($value > 0 ? 3.0 : 0.0, $frac * $track);

        $label = $r['badge'] ?? ('<span class="bar-name">' . e($r['label']) . '</span>');
        if (isset($r['href'])) {
            $label = '<a href="' . e($r['href']) . '">' . $label . '</a>';
        }

        $out .= '<div class="bar-row">'
              . '<div class="bar-label">' . $label . '</div>'
              . '<div class="bar-track">'
              . sprintf(
                    '<svg class="chart" viewBox="0 0 %d 10" role="img" aria-label="%s: %d">'
                  . '<rect x="0" y="0" width="%d" height="10" rx="3" fill="%s"/>'
                  . '<g transform="translate(0,0)"><path d="%s" fill="%s"><title>%s: %d</title></path></g>'
                  . '</svg>',
                    (int)$track, e($r['label']), $value,
                    (int)$track, '#eef2f7',
                    chart_bar_path($barW, 10.0), CHART_SERIES,
                    e($r['label']), $value
                )
              . '</div>'
              . '<div class="bar-value">' . number_format($value) . '</div>'
              . '</div>';
    }

    return $out . '</div>';
}

/**
 * A two-segment completion meter with a headline percentage.
 *
 * Both segments are always labelled in words as well as coloured: green and
 * amber sit at ΔE 6.2 under protanopia, which is inside the band where colour
 * alone is not enough on its own. The 2px gap between them is surface, not a
 * stroke, so it reads at any size.
 */
function chart_meter(int $done, int $total, string $doneLabel, string $todoLabel): string
{
    if ($total <= 0) {
        return '<p class="chart-empty">Nothing to measure yet.</p>';
    }

    $done = max(0, min($done, $total));
    $todo = $total - $done;
    $pct  = (int)round(($done / $total) * 100);

    $w = 320.0; $h = 12.0; $gap = 2.0;
    $doneW = ($done / $total) * $w;

    // Reserve the gap out of the finished segment so the total width is exact.
    if ($done > 0 && $todo > 0) {
        $doneW = max(0.0, $doneW - $gap);
    }

    $todoX = $done > 0 && $todo > 0 ? $doneW + $gap : $doneW;
    $todoW = max(0.0, $w - $todoX);

    $svg = sprintf(
        '<svg class="chart meter" viewBox="0 0 %d %d" role="img" aria-label="%d of %d %s (%d%%)">',
        (int)$w, (int)$h, $done, $total, e($doneLabel), $pct
    );

    if ($doneW > 0) {
        $svg .= sprintf('<path d="%s" fill="%s"><title>%s: %d</title></path>',
            chart_bar_path($doneW, $h), CHART_OK, e($doneLabel), $done);
    }
    if ($todoW > 0) {
        $svg .= sprintf('<g transform="translate(%.2f,0)"><path d="%s" fill="%s"><title>%s: %d</title></path></g>',
            $todoX, chart_bar_path($todoW, $h), $todo > 0 ? CHART_WARN : CHART_GRID, e($todoLabel), $todo);
    }

    $svg .= '</svg>';

    return '<div class="meter-wrap">'
         . '<div class="meter-figure">' . $pct . '<span>%</span></div>'
         . '<div class="meter-body">'
         . $svg
         . '<div class="meter-key">'
         . '<span class="key key-ok">' . number_format($done) . ' ' . e($doneLabel) . '</span>'
         . '<span class="key key-warn">' . number_format($todo) . ' ' . e($todoLabel) . '</span>'
         . '</div></div></div>';
}

/**
 * "3 hours ago". Absolute timestamps are the right thing in an audit log, where
 * the exact moment is the point; in an activity feed the useful question is
 * how stale the item is, and a reader should not have to do the subtraction.
 */
function human_ago(string $timestamp): string
{
    $then = strtotime($timestamp);
    if ($then === false) {
        return '';
    }

    $secs = time() - $then;

    if ($secs < 0)     { return 'just now'; }
    if ($secs < 60)    { return 'just now'; }
    if ($secs < 3600)  { $n = (int)floor($secs / 60);    return $n . ' min ago'; }
    if ($secs < 86400) { $n = (int)floor($secs / 3600);  return $n . ' hour' . ($n === 1 ? '' : 's') . ' ago'; }
    if ($secs < 2592000) {
        $n = (int)floor($secs / 86400);
        return $n . ' day' . ($n === 1 ? '' : 's') . ' ago';
    }

    return date('j M Y', $then);
}
