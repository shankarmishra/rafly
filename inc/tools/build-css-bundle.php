<?php
/**
 * Bundles the 10 core CSS files (css/00-tokens.css -> css/09-scenes.css) into one css/core-bundle.css file.
 *
 * Usage: php inc/tools/build-css-bundle.php
 */

if (PHP_SAPI !== 'cli') {
    die("CLI execution only.\n");
}

$root = dirname(__DIR__, 2);
$files = [
    'css/00-tokens.css',
    'css/01-base.css',
    'css/02-layout.css',
    'css/03-components.css',
    'css/04-nav.css',
    'css/05-footer.css',
    'css/06-motion.css',
    'css/07-fx.css',
    'css/08-ground.css',
    'css/09-scenes.css',
];

$bundleContent = "/* RAFly Unified Core Stylesheet Bundle — Generated " . date('Y-m-d H:i:s') . " */\n\n";

foreach ($files as $relPath) {
    $fullPath = $root . '/' . $relPath;
    if (!is_file($fullPath)) {
        fwrite(STDERR, "Error: Missing CSS file $fullPath\n");
        exit(1);
    }
    $bundleContent .= "/* --- File: $relPath --- */\n";
    $bundleContent .= file_get_contents($fullPath) . "\n\n";
}

$target = $root . '/css/core-bundle.css';
if (file_put_contents($target, $bundleContent) !== false) {
    echo "[SUCCESS] Bundled 10 CSS files into $target (" . number_format(filesize($target)) . " bytes)\n";
} else {
    fwrite(STDERR, "Error writing to $target\n");
    exit(1);
}
