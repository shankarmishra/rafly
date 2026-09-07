<?php
/**
 * Converts hero banner JPG images in assets/ to WebP format for optimal LCP performance.
 */

if (PHP_SAPI !== 'cli') {
    die("CLI execution only.\n");
}
if (!extension_loaded('gd')) {
    fwrite(STDERR, "ext-gd required.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$banners = [
    'web_dev_hero_banner',
    'security_hero_banner',
    'marketing_hero_banner',
    'content_hero_banner',
    'ecom_hero_banner',
];

foreach ($banners as $name) {
    $jpgPath  = $root . "/assets/{$name}.jpg";
    $webpPath = $root . "/assets/{$name}.webp";

    if (!is_file($jpgPath)) {
        echo "Skip: $jpgPath not found\n";
        continue;
    }

    $im = imagecreatefromjpeg($jpgPath);
    if ($im !== false) {
        imagewebp($im, $webpPath, 82);
        imagedestroy($im);
        $jpgSize  = filesize($jpgPath);
        $webpSize = filesize($webpPath);
        $savings  = round((1 - ($webpSize / $jpgSize)) * 100, 1);
        echo "[WEBP OK] {$name}.webp generated ($webpSize bytes, $savings% smaller than JPG $jpgSize bytes)\n";
    } else {
        echo "Error loading $jpgPath\n";
    }
}
