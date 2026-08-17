/**
 * Meta Pixel bootstrap — same-origin so it loads under `script-src 'self'`
 * with no CSP relaxation beyond the two Facebook origins it actually talks
 * to (see inc/security.php). The standard Meta snippet is normally inline;
 * this is that exact bootstrap logic, ported into a file, reading the pixel
 * ID from this <script> tag's own data-pixel-id attribute (set by
 * partials/head.php from the admin-editable analytics.meta_pixel_id
 * setting) instead of a literal, so the ID never needs a code change.
 *
 * No id -> no-op. That is what makes "leave the setting blank" a real way
 * to turn the Pixel off, not just an empty string floating around.
 */
(function (f, b, e, v, n, t, s) {
    var id = document.currentScript && document.currentScript.dataset.pixelId;
    if (!id || f.fbq) return;
    n = f.fbq = function () {
        n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = true;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = true;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s);

    fbq('init', id);
    fbq('track', 'PageView');
})(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
