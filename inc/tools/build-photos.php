<?php
/**
 * build-photos.php — writes a WebP twin beside every photograph.
 *
 *     php inc/tools/build-photos.php [dir]         # default: assets/photos
 *     php inc/tools/build-photos.php --quality 78
 *     php inc/tools/build-photos.php --force       # rebuild even if up to date
 *
 * inc/helpers.php photo() emits a <picture> whose <source> points at
 * "<path>.webp" when that file exists, and falls back to the original <img>
 * when it does not. This is the thing that produces those twins — the helper
 * has referenced it since it was written, and until now it did not exist, so
 * every photograph on the site shipped as the heavier original.
 *
 * WebP typically lands around 40% of an equivalent-quality JPEG, and .htaccess
 * has carried the image/webp MIME type since it was written.
 *
 * The masters are never modified and never deleted. Re-running is safe: a twin
 * newer than its master is left alone unless --force is passed.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$args    = array_slice($argv, 1);
$force   = in_array('--force', $args, true);
$quality = 80;

if (($i = array_search('--quality', $args, true)) !== false && isset($args[$i + 1])) {
    $quality = max(1, min(100, (int)$args[$i + 1]));
    unset($args[$i], $args[$i + 1]);
}

$args = array_values(array_filter($args, static fn (string $a): bool => !str_starts_with($a, '--')));
$dir  = $args[0] ?? (dirname(__DIR__, 2) . '/assets/photos');

if (!is_dir($dir)) {
    fwrite(STDERR, "Not a directory: {$dir}\n");
    exit(1);
}
if (!function_exists('imagewebp')) {
    fwrite(STDERR, "This PHP build has no WebP support in GD.\n");
    exit(1);
}

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));

$made = $skipped = 0;
$before = $after = 0;

foreach ($files as $file) {
    /** @var SplFileInfo $file */
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        continue;
    }

    $src = $file->getPathname();

    /* REPLACE the extension, do not append one. photo() looks for the master's
       path with its extension swapped — 'x.jpg' -> 'x.webp' — so writing
       'x.jpg.webp' produces a file the helper never looks at and every
       photograph silently keeps shipping as the heavier original. */
    $twin = preg_replace('/\.(jpe?g|png)$/i', '.webp', $src);

    // Up to date already? Leave it. Regenerating identical files for no reason
    // rewrites every mtime, which busts asset() caches site-wide.
    if (!$force && is_file($twin) && filemtime($twin) >= filemtime($src)) {
        $skipped++;
        continue;
    }

    $im = match ($ext) {
        'png'   => @imagecreatefrompng($src),
        default => @imagecreatefromjpeg($src),
    };
    if (!$im) {
        fwrite(STDERR, "  ! could not read " . $file->getFilename() . "\n");
        continue;
    }

    // PNGs can carry transparency, which WebP supports and would otherwise be
    // flattened to black.
    if ($ext === 'png') {
        imagepalettetotruecolor($im);
        imagealphablending($im, false);
        imagesavealpha($im, true);
    }

    if (!imagewebp($im, $twin, $quality)) {
        imagedestroy($im);
        fwrite(STDERR, "  ! could not write " . basename($twin) . "\n");
        continue;
    }
    imagedestroy($im);

    $srcSize  = filesize($src);
    $twinSize = filesize($twin);
    $before  += $srcSize;
    $after   += $twinSize;
    $made++;

    printf(
        "  %-38s %7.1f KB -> %7.1f KB  (%d%%)\n",
        substr($file->getFilename(), 0, 38),
        $srcSize / 1024,
        $twinSize / 1024,
        $srcSize > 0 ? (int)round(100 - ($twinSize / $srcSize) * 100) : 0
    );
}

if ($made === 0 && $skipped === 0) {
    echo "No JPEG or PNG masters found in {$dir}\n";
    exit(0);
}

printf(
    "\n%d written, %d already up to date.  %.1f KB -> %.1f KB (%d%% smaller) at quality %d.\n",
    $made,
    $skipped,
    $before / 1024,
    $after / 1024,
    $before > 0 ? (int)round(100 - ($after / $before) * 100) : 0,
    $quality
);
