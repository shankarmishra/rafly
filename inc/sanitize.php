<?php
/**
 * HTML allow-list sanitiser for editor-supplied article bodies.
 *
 * The posts table has always documented that `body` is "sanitised HTML… never
 * rendered without having gone through the save-time allow-list". This is that
 * allow-list — it did not exist until now, so nothing was enforcing it.
 *
 * WHY ALLOW-LIST, NOT DENY-LIST. A deny-list ("strip <script>") loses to the
 * first encoding trick, the first `<svg onload=>`, the first
 * `javascript:` URL. This parses the document, walks it, and DELETES anything
 * not explicitly permitted. Unknown markup fails closed.
 *
 * WHY DOM, NOT REGEX. HTML is not a regular language. Regex sanitisers are
 * defeated by nesting, malformed tags and attribute quoting. DOMDocument gives
 * us the browser's own parse, which is the thing we actually need to reason
 * about — if the parser sees a node, so will the browser.
 *
 * TRUST MODEL. Authors are logged-in editors, not the public, so this is a
 * guard against a compromised or careless account rather than a hostile
 * stranger. That is still worth having: a stored XSS in an article body runs
 * for every visitor, and an editor account is a far softer target than the
 * server.
 *
 * CSP INTERACTION. inc/security.php sets `default-src 'self'`, so even if
 * something slipped through, an inline script would not execute and a remote
 * image would not load. This is defence in depth, not the only defence — but
 * neither is sufficient alone, because the CSP does not stop
 * `<a href="javascript:...">` in every browser configuration.
 *
 * Requires ext-dom (bundled with PHP by default).
 */

/** Tags an article body may contain. Everything else is unwrapped or dropped. */
const SANITIZE_ALLOWED_TAGS = [
    'p'          => [],
    'br'         => [],
    'strong'     => [],
    'em'         => [],
    'b'          => [],
    'i'          => [],
    'u'          => [],
    'h2'         => ['id'],
    'h3'         => ['id'],
    'h4'         => ['id'],
    'ul'         => [],
    'ol'         => [],
    'li'         => [],
    'blockquote' => [],
    'a'          => ['href', 'title'],
    'code'       => [],
    'pre'        => [],
    'hr'         => [],
    'figure'     => [],
    'figcaption' => [],
    'img'        => ['src', 'alt', 'width', 'height', 'loading'],
    'table'      => [],
    'thead'      => [],
    'tbody'      => [],
    'tr'         => [],
    'th'         => [],
    'td'         => [],
];

/**
 * Tags whose entire subtree is removed rather than unwrapped.
 *
 * The distinction matters. An unknown <span> should keep its text — losing a
 * sentence because it was wrapped in a stray tag is a bad editing experience.
 * But <script>foo()</script> must not leave "foo()" behind as body copy, and
 * <style> contents are not prose either.
 */
const SANITIZE_STRIP_SUBTREE = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'noscript', 'template', 'svg', 'math'];

/**
 * Returns $html reduced to the allow-list.
 *
 * Input is assumed to be a fragment (no <html>/<body> wrapper). Output is a
 * fragment too.
 */
function sanitize_html(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }

    $doc = new DOMDocument();

    // libxml chatters about HTML5 elements it does not recognise. We are
    // discarding unknown elements anyway, so the warnings are noise.
    $previous = libxml_use_internal_errors(true);

    // The meta charset is what makes DOMDocument treat the input as UTF-8;
    // without it, loadHTML assumes ISO-8859-1 and mangles anything non-ASCII.
    // LIBXML_HTML_NOIMPLIED/NODEFDTD keep it from inventing html/body wrappers.
    $doc->loadHTML(
        '<?xml encoding="UTF-8"?><div id="sanitize-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $root = $doc->getElementById('sanitize-root');
    if ($root === null) {
        return '';
    }

    sanitize_walk($root);

    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }

    return trim($out);
}

/**
 * Depth-first clean of $node's children.
 *
 * Iterates over a snapshot of childNodes because the walk mutates the live
 * NodeList — removing a node mid-iteration otherwise skips its sibling.
 */
function sanitize_walk(DOMNode $node): void
{
    $children = iterator_to_array($node->childNodes);

    foreach ($children as $child) {
        if ($child instanceof DOMText) {
            continue;                                  // text is always safe once serialised
        }

        // Comments can carry conditional-comment payloads in old IE and are
        // never wanted in body copy.
        if ($child instanceof DOMComment) {
            $node->removeChild($child);
            continue;
        }

        if (!$child instanceof DOMElement) {
            $node->removeChild($child);                // PI, CDATA, doctype…
            continue;
        }

        $tag = strtolower($child->tagName);

        if (in_array($tag, SANITIZE_STRIP_SUBTREE, true)) {
            $node->removeChild($child);
            continue;
        }

        if (!array_key_exists($tag, SANITIZE_ALLOWED_TAGS)) {
            // Unwrap: clean the subtree, then splice the children into the
            // parent so the prose survives its unwanted wrapper.
            sanitize_walk($child);
            while ($child->firstChild !== null) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            continue;
        }

        sanitize_attributes($child, SANITIZE_ALLOWED_TAGS[$tag]);
        sanitize_walk($child);
    }
}

/** Removes every attribute on $el not named in $allowed, then validates URLs. */
function sanitize_attributes(DOMElement $el, array $allowed): void
{
    foreach (iterator_to_array($el->attributes) as $attr) {
        $name = strtolower($attr->nodeName);

        if (!in_array($name, $allowed, true)) {
            // This is what removes every on* handler, style="", and the
            // data-/aria- surface we have no reason to accept from an editor.
            $el->removeAttribute($attr->nodeName);
            continue;
        }

        if ($name === 'href' || $name === 'src') {
            if (!sanitize_url_ok($attr->nodeValue ?? '', $name)) {
                $el->removeAttribute($attr->nodeName);
            }
        }
    }

    // Anything that leaves the site gets the usual treatment. noopener is the
    // security-relevant half: without it the opened page can navigate ours via
    // window.opener.
    if (strtolower($el->tagName) === 'a' && $el->hasAttribute('href')) {
        $href = (string)$el->getAttribute('href');
        if (preg_match('#^https?://#i', $href)) {
            $el->setAttribute('rel', 'noopener noreferrer');
            $el->setAttribute('target', '_blank');
        }
    }
}

/**
 * True if a URL is safe to keep.
 *
 * Permits site-relative paths, fragments, and absolute http(s). Everything else
 * — javascript:, data:, vbscript:, file:, and any scheme we have not thought
 * about — is rejected.
 *
 * The whitespace strip matters: `java\tscript:alert(1)` and
 * `java\nscript:alert(1)` are both treated as javascript: by browsers, so the
 * check has to run against a de-whitespaced copy or it is trivially bypassed.
 */
function sanitize_url_ok(string $url, string $context = 'href'): bool
{
    $url = trim($url);
    if ($url === '') {
        return false;
    }

    // Strip whitespace and control characters before testing the scheme.
    $probe = strtolower(preg_replace('/[\s\x00-\x1F\x7F]+/', '', $url) ?? '');

    if ($probe === '') {
        return false;
    }

    // Fragment or site-relative. Reject protocol-relative "//host" — it is
    // off-site despite looking like a path.
    if ($probe[0] === '#') {
        return true;
    }
    if (str_starts_with($probe, '//')) {
        return false;
    }
    if ($probe[0] === '/') {
        return true;
    }

    if (str_starts_with($probe, 'http://') || str_starts_with($probe, 'https://')) {
        return true;
    }

    // A relative path with no scheme at all ("service.php?service=x") is fine.
    // The colon test is what distinguishes it from "javascript:…".
    if (!str_contains($probe, ':')) {
        return true;
    }

    return false;
}

/**
 * A URL-safe slug.
 *
 * Kept next to the sanitiser because it exists for the same reason: the slug
 * is interpolated into a canonical URL and matched against a database key, so
 * it must be reduced to a known-safe character set rather than trusted.
 */
function slugify(string $value): string
{
    $value = trim($value);

    if (function_exists('iconv')) {
        // Fold accents so "Ünicode" becomes "unicode" rather than "nicode".
        $folded = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($folded !== false) {
            $value = $folded;
        }
    }

    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

    return trim($value, '-');
}
