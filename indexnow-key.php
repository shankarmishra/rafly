<?php
/**
 * Serves the IndexNow key-ownership file at /<key>.txt — the proof Bing (and
 * every other engine IndexNow submissions reach) checks before trusting a
 * submission for this domain: https://rafly.in/<key>.txt must return the key
 * itself, in plain text, at 200.
 *
 * Routed here by .htaccess / router.php matching ^[A-Za-z0-9-]{8,64}\.txt$ —
 * a pattern, not a specific filename, because the key is not chosen yet
 * (INDEXNOW_KEY defaults to '' in inc/config.php). Until a real key is set in
 * inc/config.local.php, EVERY request here 404s: there is no key to prove
 * ownership of, so nothing should claim to be that proof.
 *
 * Requesting the wrong key (a typo, an old rotated key, a probe) 404s too,
 * rather than echoing back whatever *.txt was asked for — that would make
 * this an open text-reflection endpoint, which is not what a single constant
 * comparison needs to risk being.
 */

require __DIR__ . '/inc/config.php';

$requested = (string)($_GET['key'] ?? '');

if (INDEXNOW_KEY === '' || $requested === '' || !hash_equals(INDEXNOW_KEY, $requested)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Not found.\n";
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: public, max-age=3600');
echo INDEXNOW_KEY;
