<?php
/**
 * Regenerates every derived brand asset in assets/ from the master logo.png.
 *
 *     php inc/tools/build-assets.php
 *
 * CLI only. It lives under inc/ because .htaccess 404s that directory, so it is
 * not reachable over HTTP even though it writes files.
 *
 * WHAT THIS DOES NOT DO: it never draws or traces anything. Every output pixel
 * is resampled from the customer's own artwork, which is what makes these real
 * brand assets rather than an approximation. If the logo is ever redrawn,
 * replace logo.png and re-run this — do not hand-edit the outputs.
 *
 * Format choices are forced by consumers, not preference:
 *   - og:image      — no social crawler accepts SVG
 *   - apple-touch   — iOS ignores SVG, and composites transparency on black,
 *                     so that one gets an opaque white plate
 *   - icon-512      — Google rejects Organization.logo under 112px on either
 *                     axis, and the horizontal lockup is only 99px tall
 *
 * Requires ext-gd.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if (!extension_loaded('gd')) {
    fwrite(STDERR, "ext-gd is required. Enable extension=gd in php.ini.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$src  = $root . '/logo.png';
$out  = $root . '/assets/';

if (!is_file($src)) {
    fwrite(STDERR, "Master artwork not found: {$src}\n");
    exit(1);
}

$im = imagecreatefrompng($src);
$W  = imagesx($im);
$H  = imagesy($im);

/** Near-white and transparent both count as background. */
$isInk = static function ($im, int $x, int $y): bool {
    $c = imagecolorat($im, $x, $y);
    if ((($c >> 24) & 0x7F) > 100) {
        return false;
    }
    return !((($c >> 16) & 0xFF) > 240 && (($c >> 8) & 0xFF) > 240 && ($c & 0xFF) > 240);
};

/** Ink bounding box within a window: [x, y, w, h]. */
$bbox = static function ($im, int $x0, int $x1, int $y0, int $y1) use ($isInk): array {
    $minX = $x1; $maxX = $x0; $minY = $y1; $maxY = $y0;
    for ($y = $y0; $y <= $y1; $y++) {
        for ($x = $x0; $x <= $x1; $x++) {
            if ($isInk($im, $x, $y)) {
                $minX = min($minX, $x); $maxX = max($maxX, $x);
                $minY = min($minY, $y); $maxY = max($maxY, $y);
            }
        }
    }
    return [$minX, $minY, $maxX - $minX + 1, $maxY - $minY + 1];
};

$canvas = static function (int $w, int $h, ?array $bg = null) {
    $c = imagecreatetruecolor($w, $h);
    imagealphablending($c, false);
    imagesavealpha($c, true);
    imagefill($c, 0, 0, $bg === null
        ? imagecolorallocatealpha($c, 0, 0, 0, 127)
        : imagecolorallocatealpha($c, $bg[0], $bg[1], $bg[2], 0));
    imagealphablending($c, true);
    return $c;
};

$save = static function ($c, string $name) use ($out) {
    imagesavealpha($c, true);
    imagepng($c, $out . $name, 9);
    printf("  %-24s %8s bytes  %dx%d\n",
        $name, number_format(filesize($out . $name)), imagesx($c), imagesy($c));
};

/** Centre-fit a source region into a square, with padding. */
$fitSquare = static function ($dst, $im, array $r, int $size, float $padRatio) {
    [$sx, $sy, $sw, $sh] = $r;
    $pad = (int)round($size * $padRatio);
    $box = $size - $pad * 2;
    $s   = min($box / $sw, $box / $sh);
    $dw  = (int)round($sw * $s);
    $dh  = (int)round($sh * $s);
    imagecopyresampled($dst, $im, (int)(($size - $dw) / 2), (int)(($size - $dh) / 2),
                       $sx, $sy, $dw, $dh, $sw, $sh);
};

// The whole lockup, and the R mark alone. Both are measured rather than
// hardcoded so this keeps working if the master artwork is recropped.
$lockup = $bbox($im, 0, $W - 1, 0, $H - 1);

// Split mark from wordmark at the widest fully-empty column run inside the
// lockup. A fixed percentage was tried first and was wrong: it cut into the
// "r", so the icons shipped with a sliver of the wordmark in them.
$colHasInk = [];
for ($x = $lockup[0]; $x < $lockup[0] + $lockup[2]; $x++) {
    $ink = false;
    for ($y = $lockup[1]; $y < $lockup[1] + $lockup[3]; $y++) {
        if ($isInk($im, $x, $y)) { $ink = true; break; }
    }
    $colHasInk[$x] = $ink;
}

$best = [0, 0];   // [startX, width]
$run  = null;
foreach ($colHasInk as $x => $ink) {
    if (!$ink) {
        $run ??= $x;
    } elseif ($run !== null) {
        if ($x - $run > $best[1]) { $best = [$run, $x - $run]; }
        $run = null;
    }
}
if ($best[1] < 5) {
    fwrite(STDERR, "Could not find a gutter between the mark and the wordmark.\n");
    exit(1);
}

$mark = $bbox($im, 0, $best[0], 0, $H - 1);
printf("gutter at x %d..%d (width %d)\n", $best[0], $best[0] + $best[1] - 1, $best[1]);

printf("master %dx%d  lockup %dx%d  mark %dx%d\n\n", $W, $H, $lockup[2], $lockup[3], $mark[2], $mark[3]);

// Average the mark's colour for the OG accent bar.
[$mx, $my, $mw, $mh] = $mark;
$sr = $sg = $sb = $n = 0;
for ($y = $my; $y < $my + $mh; $y += 3) {
    for ($x = $mx; $x < $mx + $mw; $x += 3) {
        if ($isInk($im, $x, $y)) {
            $c = imagecolorat($im, $x, $y);
            $sr += ($c >> 16) & 0xFF; $sg += ($c >> 8) & 0xFF; $sb += $c & 0xFF; $n++;
        }
    }
}
$brand = [intdiv($sr, $n), intdiv($sg, $n), intdiv($sb, $n)];

// --- Horizontal lockup, at ~2.5x its 120px display width -------------------
[$fx, $fy, $fw, $fh] = $lockup;
$lw = 320;
$lh = (int)round($lw * $fh / $fw);
$logo = $canvas($lw, $lh);
imagecopyresampled($logo, $im, 0, 0, $fx, $fy, $lw, $lh, $fw, $fh);
$save($logo, 'logo.png');

// --- Reversed lockup, for the dark footer -----------------------------------
// The footer previously applied `filter: brightness(0) invert(1)` to the normal
// logo. That flattens EVERY colour to solid white — the blue gradient mark and
// the blue/green "BUILD GROW SCALE" tagline all collapse into one white blob.
//
// A real reversed export only lifts the dark ink (the "rafly" wordmark, which
// would otherwise be invisible on navy) and leaves the bright brand colours
// untouched.
//
// The test is BRIGHTNESS, not saturation. Saturation was tried first and was
// wrong: the wordmark navy #0d1b2a scores 0.69 saturation because the blue
// channel dominates, so a "near-neutral" rule skipped the exact pixels it was
// written to catch. Brightness separates them cleanly — the wordmark peaks
// around 42, the blue mark around 230, the green tagline around 200.
$rev = $canvas($lw, $lh);
imagecopyresampled($rev, $im, 0, 0, $fx, $fy, $lw, $lh, $fw, $fh);

for ($y = 0; $y < $lh; $y++) {
    for ($x = 0; $x < $lw; $x++) {
        $c = imagecolorat($rev, $x, $y);
        $a = ($c >> 24) & 0x7F;
        if ($a === 127) {
            continue;                       // fully transparent
        }

        $r = ($c >> 16) & 0xFF;
        $g = ($c >> 8) & 0xFF;
        $b = $c & 0xFF;

        // Dark => wordmark ink. Bright => brand colour, left exactly as drawn.
        // 110 sits in the empty gap between the two clusters.
        if (max($r, $g, $b) < 110) {
            // Preserve the original alpha so antialiased glyph edges stay smooth
            // instead of turning into a hard white fringe.
            imagesetpixel($rev, $x, $y, imagecolorallocatealpha($rev, 255, 255, 255, $a));
        }
    }
}
$save($rev, 'logo-reversed.png');

// --- Icons ------------------------------------------------------------------
$ati = $canvas(180, 180, [255, 255, 255]);      // opaque: iOS ignores alpha
$fitSquare($ati, $im, $mark, 180, 0.17);
$save($ati, 'apple-touch-icon.png');

$fav = $canvas(32, 32);                          // tight: every pixel counts at 32px
$fitSquare($fav, $im, $mark, 32, 0.06);
$save($fav, 'favicon-32.png');

foreach ([192, 512] as $size) {
    $sq = $canvas($size, $size, [255, 255, 255]);
    // 20% padding keeps the mark inside Android's maskable safe zone.
    $fitSquare($sq, $im, $mark, $size, 0.20);
    $save($sq, "icon-{$size}.png");
}

// --- Social share card ------------------------------------------------------
// Must be exactly 1200x630 — partials/head.php declares those dimensions.
// No text is drawn: GD needs a TTF/OTF and this project ships woff2 only, and a
// GD bitmap fallback font would look worse than no text at all.
$og = $canvas(1200, 630, [248, 250, 252]);
$ow = 620;
$oh = (int)round($ow * $fh / $fw);
imagecopyresampled($og, $im, (int)((1200 - $ow) / 2), (int)((630 - $oh) / 2) - 20,
                   $fx, $fy, $ow, $oh, $fw, $fh);
imagefilledrectangle($og, 0, 616, 1200, 630, imagecolorallocate($og, ...$brand));
$save($og, 'og-cover.png');

printf("\ndone — brand colour sampled from the mark: #%02x%02x%02x\n", ...$brand);
