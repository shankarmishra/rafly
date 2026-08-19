<?php
/**
 * indexnow.php — tells Bing (and every other engine IndexNow submissions
 * reach) that specific URLs changed, instead of waiting for the next crawl.
 *
 *     php inc/tools/indexnow.php                    # every URL from sitemap_urls()
 *     php inc/tools/indexnow.php /blog/some-slug     # just this one (may repeat)
 *     php inc/tools/indexnow.php --force             # ignore the 24h re-submit guard
 *     php inc/tools/indexnow.php --dry-run           # print what would be sent, send nothing
 *
 * CLI ONLY — same reasoning as every other inc/tools/*.php script: a
 * web-reachable "ping every search engine now" endpoint is a standing abuse
 * vector (anyone could trigger it, repeatedly), and there is no equivalent
 * risk from a shell that already has access to the box.
 *
 * Does nothing at all until INDEXNOW_KEY is set in inc/config.local.php —
 * see indexnow-key.php, which is what actually proves ownership of that key
 * to whichever engine checks it.
 *
 * NEVER submits a URL that is not in sitemap_urls() — the same function
 * sitemap.php renders from, so "canonical and indexable" means the same
 * thing to both. A noindex page, a redirect, or a typo can't reach the API
 * by construction: they are simply not in the set this script is allowed to
 * read from.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../repo/seed.php';
require_once __DIR__ . '/../sitemap.php';

if (INDEXNOW_KEY === '') {
    fwrite(STDERR, "INDEXNOW_KEY is not set — nothing to do. Set it in inc/config.local.php first,\n"
        . "and confirm https://" . SITE_DOMAIN . "/" . '<key>' . ".txt serves it (indexnow-key.php) before running this for real.\n");
    exit(1);
}

$args    = array_slice($argv, 1);
$force   = in_array('--force', $args, true);
$dryRun  = in_array('--dry-run', $args, true);
$explicit = array_values(array_filter($args, static fn(string $a): bool => !str_starts_with($a, '--')));

// The one allowed set — path => full URL. Anything requested that is not a
// key in here is refused below rather than submitted anyway.
$allowed = [];
foreach (sitemap_urls() as $u) {
    $allowed['/' . $u['loc']] = SITE_ORIGIN . '/' . $u['loc'];
}

if ($explicit) {
    $urls = [];
    foreach ($explicit as $path) {
        $path = '/' . ltrim($path, '/');
        if (!isset($allowed[$path])) {
            fwrite(STDERR, "  skip (not in sitemap_urls(), so not indexable/canonical): {$path}\n");
            continue;
        }
        $urls[] = $allowed[$path];
    }
} else {
    $urls = array_values($allowed);
}

if (!$urls) {
    fwrite(STDERR, "Nothing to submit.\n");
    exit($explicit ? 1 : 0);
}

/**
 * De-duplication / rate-limiting log — same storage location leads.php
 * (inc/config.php's LEAD_STORE_PATH) already uses: above the web root, so it
 * is host-independent rather than relying on .htaccess rules some hosts
 * ignore. Not a queue, just "when did we last tell them about this URL" —
 * IndexNow itself has no concept of a submission history to query back.
 */
$logFile = LEAD_STORE_PATH . '/indexnow-log.json';
$log     = is_file($logFile) ? (json_decode((string)file_get_contents($logFile), true) ?: []) : [];
if (!is_array($log)) {
    $log = [];
}

$RESUBMIT_AFTER = 86400; // 24h — a URL submitted today does not need submitting again today.
$now = time();

$toSend = [];
foreach ($urls as $url) {
    $last = (int)($log[$url] ?? 0);
    if (!$force && $last > 0 && ($now - $last) < $RESUBMIT_AFTER) {
        continue;
    }
    $toSend[] = $url;
}

if (!$toSend) {
    echo "All " . count($urls) . " URL(s) were submitted within the last 24h. Nothing to send (--force to override).\n";
    exit(0);
}

echo "Submitting " . count($toSend) . " of " . count($urls) . " URL(s):\n";
foreach ($toSend as $u) {
    echo "  - {$u}\n";
}

if ($dryRun) {
    echo "\n--dry-run: nothing sent.\n";
    exit(0);
}

// api.indexnow.org fans out to every participating engine (Bing included) —
// submitting once here reaches all of them, rather than needing a
// per-engine endpoint.
$payload = json_encode([
    'host'        => SITE_DOMAIN,
    'key'         => INDEXNOW_KEY,
    'keyLocation' => SITE_ORIGIN . '/' . INDEXNOW_KEY . '.txt',
    'urlList'     => $toSend,
], JSON_UNESCAPED_SLASHES);

$ch = curl_init('https://api.indexnow.org/indexnow');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($body === false) {
    fwrite(STDERR, "Request failed: {$err}\n");
    exit(1);
}

// 200 and 202 both mean accepted — IndexNow uses 202 when the key is new
// enough that not every engine has seen it yet.
if ($code !== 200 && $code !== 202) {
    fwrite(STDERR, "IndexNow returned HTTP {$code}" . ($body !== '' ? ": {$body}" : '') . "\n");
    exit(1);
}

foreach ($toSend as $u) {
    $log[$u] = $now;
}
if (!is_dir(LEAD_STORE_PATH)) {
    @mkdir(LEAD_STORE_PATH, 0750, true);
}
file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT));

echo "\nHTTP {$code}. " . count($toSend) . " URL(s) submitted and logged.\n";
