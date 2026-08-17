<?php
/**
 * Security headers — the single definition for the whole site.
 * Previously this block was copy-pasted into 5 page files plus a divergent
 * variant in submit.php, which is how they drifted apart.
 */

/**
 * @param string $mode 'page'  normal HTML pages
 *                     'form'  POST handlers (adds no-store caching)
 *                     'admin' authenticated admin pages (no-store, noindex,
 *                             and a CSP with no inline styles)
 */
function send_security_headers(string $mode = 'page'): void
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');

    // strict-origin-when-cross-origin, not no-referrer: the old value broke
    // referral attribution in analytics while gaining essentially nothing.
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), camera=(), microphone=(), payment=()');

    // Isolates this browsing context from cross-origin windows, so a popup
    // opener cannot reach into it. Cheap, and closes a whole class of attack.
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('X-Permitted-Cross-Domain-Policies: none');

    $csp = [
        "default-src 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "frame-ancestors 'none'",
        "form-action 'self'",

        // No 'unsafe-inline': every last inline onclick has been converted to a
        // data-* hook and the site has zero inline <script> blocks, so all JS
        // loads from same-origin files. A nonce would be dead weight here —
        // nonces exist to permit inline scripts, and there are none to permit.
        // The Meta Pixel is the one third-party script the site loads — its
        // bootstrap snippet is normally inline, so it was ported into a
        // same-origin file (js/pixel.js) instead of relaxing this directive;
        // connect.facebook.net is only what that file actually fetches.
        "script-src 'self' https://connect.facebook.net",

        // style-src still needs 'unsafe-inline': the hero and illustration SVGs
        // drive their geometry from inline style="--a: …; offset-path: …",
        // which cannot be hoisted into a stylesheet because the values are
        // per-element. Tightening this means refactoring those scenes first.
        "style-src 'self' 'unsafe-inline'",

        // 'self' was MISSING from the original font-src, which silently blocked
        // every self-hosted webfont. Font Awesome has been replaced by a local
        // SVG sprite, so no third-party origin is allowed anywhere any more.
        "font-src 'self'",

        // facebook.com: the Pixel's <noscript> fallback <img> and its fbevents
        // beacon both target facebook.com/tr, not connect.facebook.net.
        "img-src 'self' data: https://www.facebook.com",
        "connect-src 'self' https://www.facebook.com",
        "media-src 'self'",
    ];

    if ($mode === 'form' || $mode === 'admin') {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    if ($mode === 'admin') {
        // Belt and braces alongside the Disallow in robots.txt: robots.txt is a
        // request, X-Robots-Tag is enforced, and an indexed admin login page is
        // a standing invitation.
        header('X-Robots-Tag: noindex, nofollow, noarchive');

        // The admin has no inline-style SVG scenes, so it can drop the
        // 'unsafe-inline' the public pages still need for their illustrations.
        // Tightened here rather than globally because the public relaxation is
        // a real constraint, not an oversight — see the note above.
        foreach ($csp as $i => $directive) {
            if (str_starts_with($directive, 'style-src')) {
                $csp[$i] = "style-src 'self'";
            }
        }
    }

    // Only meaningful over TLS, and harmful to send during local HTTP dev.
    if (IS_HTTPS) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        $csp[] = 'upgrade-insecure-requests';
    }

    header('Content-Security-Policy: ' . implode('; ', $csp));
}

// ---------------------------------------------------------------------------
// Form abuse controls
// ---------------------------------------------------------------------------

/** Name of the honeypot field. Generic enough that bots want to fill it. */
const HONEYPOT_FIELD = 'website_url';

/**
 * True when the honeypot was filled in.
 *
 * The field is present in the markup but hidden from humans via CSS, and it is
 * never labelled, so a real visitor cannot fill it. Automated form-fillers
 * populate every input they find. This catches the bulk of low-effort spam at
 * zero cost to real users, and unlike a CAPTCHA it is invisible and accessible.
 */
function honeypot_tripped(): bool
{
    return trim((string)($_POST[HONEYPOT_FIELD] ?? '')) !== '';
}

/**
 * Sliding-window rate limit, keyed per session.
 *
 * Without this, one scripted client could append rows to the lead CSV as fast
 * as it could issue requests. Because each write takes an exclusive flock(),
 * that is also a cheap denial-of-service on the form for real visitors.
 *
 * Session-keyed rather than IP-keyed: shared NAT (an office, a mobile carrier)
 * puts many legitimate visitors behind one address, and blocking them all is a
 * worse failure than letting a determined attacker rotate sessions. The
 * honeypot and CSRF check handle the naive-bot case; this handles accidental
 * double-submits and casual abuse.
 *
 * @param int $max    Submissions permitted per window.
 * @param int $window Window length in seconds.
 */
function rate_limit_ok(string $bucket, int $max = 5, int $window = 3600): bool
{
    $key = 'rl_' . $bucket;
    $now = time();

    $hits = $_SESSION[$key] ?? [];

    // Drop anything that has aged out of the window.
    $hits = array_values(array_filter($hits, static fn($t) => ($now - $t) < $window));

    if (count($hits) >= $max) {
        $_SESSION[$key] = $hits;   // persist the pruned list, don't record this attempt
        return false;
    }

    $hits[] = $now;
    $_SESSION[$key] = $hits;

    return true;
}

/**
 * IP-keyed rate limit, persisted in rate_limit_hits. Exists alongside
 * rate_limit_ok() rather than replacing it: that one stays session-keyed for
 * the reason its own doc-block gives, and this one closes exactly the gap
 * that leaves open — a client that simply discards its session (no cookie
 * jar between requests) never increments a session-keyed counter at all. This
 * is the same gap 003_login_attempts.sql documents for why login could not be
 * session-keyed either, applied to the same public form.
 *
 * REMOTE_ADDR only, never X-Forwarded-For — the same rule inc/auth.php's
 * auth_client_ip() follows, duplicated here rather than pulling the admin auth
 * module into the public contact-form request path for one line. If this ever
 * runs behind a CDN, add an explicit trusted-proxy allow-list — do not just
 * start reading the header.
 *
 * @param int $max    Hits permitted per window, per (bucket, ip).
 * @param int $window Window length in seconds.
 */
function rate_limit_ip_ok(string $bucket, int $max, int $window): bool
{
    $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    if ($ip === '') {
        // Nothing meaningful to key on. The honeypot, CSRF check and the
        // session-keyed rate_limit_ok() above still apply regardless.
        return true;
    }

    // This layer is persisted, so it is the one part of the submission path
    // that needs a database. Without this guard an unreachable MySQL turns
    // every single lead submission into an uncaught PDOException — a visitor
    // sees a stack trace and the lead is lost. Degrade instead: the honeypot,
    // the timing gate, the arithmetic challenge and the session-keyed
    // rate_limit_ok() all still apply, none of which touch the database.
    if (!db_available()) {
        return true;
    }

    $count = (int)scalar('
        SELECT count(*) FROM rate_limit_hits
         WHERE bucket = ? AND ip = ? AND created_at > ' . sql_now_minus_secs() . '
    ', [$bucket, $ip, $window]);

    if ($count >= $max) {
        return false;
    }

    try {
        q('INSERT INTO rate_limit_hits (bucket, ip) VALUES (?, ?)', [$bucket, $ip]);

        // No cron on shared hosting, so prune inline — rarely, and only rows
        // far outside every window. Same idiom as login_attempt_record().
        if (random_int(1, 50) === 1) {
            q('DELETE FROM rate_limit_hits WHERE created_at < ' . sql_now_minus_days(7));
        }
    } catch (Throwable $e) {
        error_log('rate_limit_ip_ok: could not record hit: ' . $e->getMessage());
    }

    return true;
}

/**
 * Session-keyed minimum-fill-time gate. A scripted client typically loads and
 * submits a form within milliseconds; the fastest real visitor still takes a
 * couple of seconds to read a field and type into it.
 *
 * Touch on every render of a page holding the form (partials/antibot.php),
 * check once on submit. Consumed on check either way, so a failed submission
 * that re-renders the form starts the clock again rather than reusing however
 * long the visitor already spent on the previous attempt.
 */
function form_timing_touch(): void
{
    $_SESSION['form_shown_at'] = time();
}

/** @param int $minSeconds Minimum elapsed time between touch and check. */
function form_timing_ok(int $minSeconds = 2): bool
{
    $shownAt = $_SESSION['form_shown_at'] ?? null;
    unset($_SESSION['form_shown_at']);

    return $shownAt !== null && (time() - (int)$shownAt) >= $minSeconds;
}

/**
 * A tiny arithmetic challenge — self-hosted, free, and (unlike a client-side
 * puzzle) verifiable end to end without a browser: it works identically with
 * JavaScript on or off, so there is no separate no-JS fallback path to keep in
 * sync with a JS one.
 *
 * Session-keyed and single-use: antibot_check() consumes the question whether
 * it was answered correctly or not, the same way csrf_token rotates on
 * submit.php's successful path — a solved answer cannot be replayed against a
 * second submission.
 *
 * @return array{a:int,b:int}
 */
function antibot_challenge(): array
{
    if (!isset($_SESSION['antibot_a'], $_SESSION['antibot_b'])) {
        $_SESSION['antibot_a'] = random_int(2, 9);
        $_SESSION['antibot_b'] = random_int(2, 9);
    }

    return ['a' => (int)$_SESSION['antibot_a'], 'b' => (int)$_SESSION['antibot_b']];
}

function antibot_check(?string $answer): bool
{
    if (!isset($_SESSION['antibot_a'], $_SESSION['antibot_b'])) {
        return false;
    }

    $expected = (int)$_SESSION['antibot_a'] + (int)$_SESSION['antibot_b'];
    $ok = $answer !== null && is_numeric($answer) && (int)$answer === $expected;

    unset($_SESSION['antibot_a'], $_SESSION['antibot_b']);

    return $ok;
}

/**
 * Re-seed both single-use gates and hand the new arithmetic pair back.
 *
 * antibot_check() and form_timing_ok() both consume their session state, by
 * design — a challenge that survived its own answer would be replayable. The
 * consequence is that after ONE submission the form on the page is dead: the
 * next attempt has no challenge to check against and fails, whatever the
 * visitor types.
 *
 * That mattered because the site renders two forms per page load (the contact
 * section and the consultation modal) and submits them over AJAX without a
 * reload. A visitor who submitted, then submitted again, silently lost the
 * second lead.
 *
 * So every JSON response carries a fresh pair, and js/forms.js writes it into
 * every `[data-antibot-a]` / `[data-antibot-b]` on the page.
 *
 * @return array{a:int,b:int}
 */
function form_challenge_reissue(): array
{
    unset($_SESSION['antibot_a'], $_SESSION['antibot_b']);
    form_timing_touch();

    return antibot_challenge();
}

/**
 * Neutralises spreadsheet formula injection.
 *
 * fputcsv() quotes correctly for CSV parsers, but Excel, LibreOffice and Google
 * Sheets treat a leading =, +, - or @ as the start of a live formula. A lead
 * whose description begins `=HYPERLINK(...)` executes when staff open the file.
 * Prefixing a tab makes the cell inert while staying readable.
 */
function csv_safe(string $value): string
{
    if ($value !== '' && strpbrk($value[0], "=+-@\t\r") !== false) {
        return "\t" . $value;
    }

    return $value;
}
