<?php
/**
 * build-seed-art.php — regenerates the preview seed's images.
 *
 *     php inc/tools/build-seed-art.php
 *
 * The seed images that shipped with the previous build were halftone plates:
 * photographs reduced to two colours by ordered dithering, on a cream ground.
 * That treatment was the whole point of the paper edition and is the opposite
 * of what this build is — clean, full-colour, sharp. They also arrived through
 * a tool (build-paper.php) that no longer exists here.
 *
 * What this writes instead is ORIGINAL work: brand-coloured gradient plates
 * with a geometric field over them, and monogram discs for the sample team.
 * Nothing is traced, sampled or downloaded, so there is no licence attached
 * and nothing to credit — which is also why this can regenerate them at any
 * time without asking anyone.
 *
 * These are PREVIEW content (inc/data/seed-preview.php) and never appear on a
 * site with a real database behind it. See CONTENT-CHECKLIST.md.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../config.php';

$out = dirname(__DIR__, 2) . '/uploads';
if (!is_dir($out)) {
    fwrite(STDERR, "uploads/ not found at {$out}\n");
    exit(1);
}

if (!function_exists('imagecreatetruecolor')) {
    fwrite(STDERR, "GD is not available.\n");
    exit(1);
}

/** The palette, matching css/00-tokens.css. */
const BRAND = [
    'blue'      => [0x16, 0x52, 0xe0],
    'blue-deep' => [0x0d, 0x47, 0xa1],
    'navy'      => [0x12, 0x3f, 0x8f],
    'ink'       => [0x0a, 0x0e, 0x1a],
    'teal'      => [0x0e, 0x74, 0x90],
    'violet'    => [0x5b, 0x21, 0xb6],
    'orange'    => [0xff, 0x6b, 0x35],
    'green'     => [0x15, 0x80, 0x3d],
];

function mixc(array $a, array $b, float $t): array
{
    return [
        (int)round($a[0] + ($b[0] - $a[0]) * $t),
        (int)round($a[1] + ($b[1] - $a[1]) * $t),
        (int)round($a[2] + ($b[2] - $a[2]) * $t),
    ];
}

/**
 * A cover plate: a diagonal two-stop gradient with a faint grid and a few
 * translucent discs over it. Deterministic — the same index always produces
 * the same image, so regenerating never silently changes the design.
 */
function cover(int $w, int $h, array $from, array $to, int $seed): GdImage
{
    $im = imagecreatetruecolor($w, $h);
    imagealphablending($im, true);
    imagesavealpha($im, false);

    // Gradient, drawn along the diagonal so it reads as light falling across.
    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x += 4) {
            $t = min(1.0, max(0.0, ($x / $w) * 0.62 + ($y / $h) * 0.38));
            [$r, $g, $b] = mixc($from, $to, $t);
            $c = imagecolorallocate($im, $r, $g, $b);
            imagefilledrectangle($im, $x, $y, $x + 3, $y, $c);
        }
    }

    // A faint grid — structure, not texture.
    $grid = imagecolorallocatealpha($im, 255, 255, 255, 112);
    $step = (int)round($h / 9);
    for ($x = $step; $x < $w; $x += $step) { imageline($im, $x, 0, $x, $h, $grid); }
    for ($y = $step; $y < $h; $y += $step) { imageline($im, 0, $y, $w, $y, $grid); }

    // Discs, placed from the seed so each cover differs but never randomly.
    mt_srand($seed);
    for ($i = 0; $i < 4; $i++) {
        $cx = mt_rand((int)($w * 0.1), (int)($w * 0.9));
        $cy = mt_rand((int)($h * 0.1), (int)($h * 0.9));
        $d  = mt_rand((int)($h * 0.25), (int)($h * 0.85));
        $ring = imagecolorallocatealpha($im, 255, 255, 255, min(127, 104 + $i * 6));
        imagesetthickness($im, 2);
        imageellipse($im, $cx, $cy, $d, $d, $ring);
    }
    imagesetthickness($im, 1);

    // One solid accent disc, so the plate has a focal point.
    mt_srand($seed + 7);
    $ax = mt_rand((int)($w * 0.55), (int)($w * 0.85));
    $ay = mt_rand((int)($h * 0.2), (int)($h * 0.55));
    $ad = (int)round($h * 0.20);
    $acc = imagecolorallocatealpha($im, BRAND['orange'][0], BRAND['orange'][1], BRAND['orange'][2], 40);
    imagefilledellipse($im, $ax, $ay, $ad, $ad, $acc);

    return $im;
}

/**
 * A monogram disc for a sample team member. Deliberately NOT a photograph:
 * these are anonymised placeholder people, and putting a stock portrait on one
 * would present a stranger as Rafly staff.
 */
function monogram(int $size, string $letter, array $from, array $to): GdImage
{
    $im = imagecreatetruecolor($size, $size);

    for ($y = 0; $y < $size; $y++) {
        $t = $y / $size;
        [$r, $g, $b] = mixc($from, $to, $t);
        $c = imagecolorallocate($im, $r, $g, $b);
        imagefilledrectangle($im, 0, $y, $size, $y, $c);
    }

    // The letter, drawn from GD's built-in font scaled up — no font file to
    // licence, and at this size the shape is all that matters.
    $white = imagecolorallocate($im, 255, 255, 255);
    $tile  = imagecreatetruecolor(imagefontwidth(5), imagefontheight(5));
    $tbg   = imagecolorallocate($tile, 0, 0, 0);
    imagefilledrectangle($tile, 0, 0, imagesx($tile), imagesy($tile), $tbg);
    imagestring($tile, 5, 0, 0, $letter, $white);

    $scale = (int)round($size * 0.42 / imagefontheight(5));
    $lw    = imagesx($tile) * $scale;
    $lh    = imagesy($tile) * $scale;

    // Copy only the lit pixels, so the black tile background is not pasted in.
    for ($y = 0; $y < imagesy($tile); $y++) {
        for ($x = 0; $x < imagesx($tile); $x++) {
            $rgb = imagecolorat($tile, $x, $y);
            if ((($rgb >> 16) & 0xFF) < 128) { continue; }
            imagefilledrectangle(
                $im,
                (int)(($size - $lw) / 2 + $x * $scale),
                (int)(($size - $lh) / 2 + $y * $scale),
                (int)(($size - $lw) / 2 + ($x + 1) * $scale - 1),
                (int)(($size - $lh) / 2 + ($y + 1) * $scale - 1),
                $white
            );
        }
    }
    imagedestroy($tile);

    return $im;
}

/* ------------------------------------------------------------------ run */

$covers = [
    ['seed-cover-01.png', BRAND['blue'],      BRAND['navy']],
    ['seed-cover-02.png', BRAND['teal'],      BRAND['ink']],
    ['seed-cover-03.png', BRAND['violet'],    BRAND['blue-deep']],
    ['seed-cover-04.png', BRAND['blue-deep'], BRAND['ink']],
];

foreach ($covers as $i => [$name, $from, $to]) {
    $im = cover(1200, 675, $from, $to, 101 + $i * 13);
    imagepng($im, $out . '/' . $name, 8);
    imagedestroy($im);
    printf("%-22s 1200x675  %6.1f KB\n", $name, filesize($out . '/' . $name) / 1024);
}

$team = [
    ['seed-team-1.png', 'A', BRAND['blue'],      BRAND['navy']],
    ['seed-team-2.png', 'P', BRAND['teal'],      BRAND['ink']],
    ['seed-team-3.png', 'S', BRAND['violet'],    BRAND['blue-deep']],
    ['seed-team-4.png', 'M', BRAND['green'],     BRAND['ink']],
    ['seed-team-5.png', 'R', BRAND['blue-deep'], BRAND['ink']],
];

foreach ($team as [$name, $letter, $from, $to]) {
    $im = monogram(400, $letter, $from, $to);
    imagepng($im, $out . '/' . $name, 8);
    imagedestroy($im);
    printf("%-22s 400x400   %6.1f KB\n", $name, filesize($out . '/' . $name) / 1024);
}

echo "\nDone. These are preview-seed images only — see CONTENT-CHECKLIST.md.\n";
