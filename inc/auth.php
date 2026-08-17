<?php
/**
 * Authentication and authorisation for the admin panel.
 *
 * Two rules govern everything in this file:
 *
 *   1. DENY BY DEFAULT. can() returns false for anything it does not recognise,
 *      and every admin page must call require_login() / require_can() before it
 *      emits a byte. A new page is inaccessible until someone deliberately
 *      opens it, rather than public until someone remembers to close it.
 *
 *   2. THE SESSION IS NOT THE AUTHORITY. PHP's session holds a token; the
 *      admin_sessions row is what makes it valid. That is what lets an admin
 *      revoke a login they no longer trust — you cannot un-issue a PHP session
 *      cookie, but you can mark its row revoked.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/totp.php';

// ---------------------------------------------------------------------------
// Capabilities
// ---------------------------------------------------------------------------

/**
 * role slug => capabilities.
 *
 * Capabilities, not roles, are checked at the call site. Asking "can this user
 * edit content" rather than "is this user an editor" means adding a role later
 * is a change to this array instead of a hunt through every page.
 *
 * 'admin' is deliberately listed in full rather than special-cased with a
 * wildcard: a wildcard hides what the role can actually do, and the day someone
 * adds a destructive capability it should be a visible decision to grant it.
 */
const ROLE_CAPABILITIES = [
    'admin' => [
        'leads.view', 'leads.edit', 'leads.export', 'leads.delete',
        'content.view', 'content.edit', 'content.publish', 'content.delete',
        'media.view', 'media.upload', 'media.delete',
        'settings.view', 'settings.edit',
        'users.view', 'users.manage',
        'audit.view',
    ],
    'editor' => [
        'leads.view', 'leads.edit', 'leads.export',
        'content.view', 'content.edit', 'content.publish',
        'media.view', 'media.upload',
        'settings.view',
    ],
    'viewer' => [
        'leads.view',
        'content.view',
        'media.view',
        'settings.view',
    ],
];

/** How long a login lasts without activity. */
const SESSION_IDLE_SECONDS = 7200;      // 2 hours

/** Absolute cap, regardless of activity. */
const SESSION_MAX_SECONDS = 43200;      // 12 hours

// ---------------------------------------------------------------------------
// Current user
// ---------------------------------------------------------------------------

/**
 * The logged-in user, or null.
 *
 * Resolved once per request and cached. Returns null — never throws — so a page
 * can ask without guarding, and so a dead database logs people out rather than
 * showing them a stack trace.
 *
 * @return array{id:int,email:string,name:string,status:string,roles:list<string>}|null
 */
function current_user(): ?array
{
    static $user = null;
    static $resolved = false;

    if ($resolved) {
        return $user;
    }
    $resolved = true;

    $token = $_SESSION['admin_token'] ?? null;
    if (!is_string($token) || $token === '') {
        return null;
    }

    try {
        $row = one('
            SELECT u.id, u.email, u.name, u.status,
                   s.id AS session_id, s.expires_at, s.revoked_at, s.last_seen_at
              FROM admin_sessions s
              JOIN users u ON u.id = s.user_id
             WHERE s.token_hash = ?
        ', [hash('sha256', $token)]);
    } catch (Throwable $e) {
        error_log('auth: session lookup failed: ' . $e->getMessage());
        return null;
    }

    if ($row === null) {
        return null;
    }

    // Any of these invalidate the session. Checked in the application rather
    // than in the WHERE clause so the reason can be logged if needed.
    $now = time();
    $expired = strtotime((string)$row['expires_at']) <= $now;
    $idle    = ($now - strtotime((string)$row['last_seen_at'])) > SESSION_IDLE_SECONDS;

    if ($row['revoked_at'] !== null || $expired || $idle || $row['status'] !== 'active') {
        auth_logout();
        return null;
    }

    // Throttle the write: updating last_seen on every request turns a read-only
    // page load into a write, and the idle window is measured in hours.
    if (($now - strtotime((string)$row['last_seen_at'])) > 60) {
        try {
            q('UPDATE admin_sessions SET last_seen_at = now() WHERE id = ?', [$row['session_id']]);
        } catch (Throwable) {
            // Not fatal — the session is still valid, it just ages slightly faster.
        }
    }

    $roles = array_column(
        all('SELECT r.slug FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = ?',
            [$row['id']]),
        'slug'
    );

    return $user = [
        'id'     => (int)$row['id'],
        'email'  => (string)$row['email'],
        'name'   => (string)$row['name'],
        'status' => (string)$row['status'],
        'roles'  => $roles,
    ];
}

/** True if anyone is logged in. */
function is_logged_in(): bool
{
    return current_user() !== null;
}

// ---------------------------------------------------------------------------
// Authorisation
// ---------------------------------------------------------------------------

/**
 * Does the current user hold $capability?
 *
 * Unknown capability, no user, or no matching role all return false. There is
 * no path through this function that grants something by accident.
 */
function can(string $capability): bool
{
    $user = current_user();
    if ($user === null) {
        return false;
    }

    foreach ($user['roles'] as $role) {
        if (in_array($capability, ROLE_CAPABILITIES[$role] ?? [], true)) {
            return true;
        }
    }

    return false;
}

/**
 * Stronger admin gate for sensitive settings and user details.
 * Only admins may access the most sensitive pages and actions.
 */
function is_admin(): bool
{
    return has_role('admin');
}

/** True if the user holds the named role. Prefer can() at call sites. */
function has_role(string $role): bool
{
    $user = current_user();
    return $user !== null && in_array($role, $user['roles'], true);
}

/**
 * Send unauthenticated visitors to the login page, preserving where they were
 * heading. Call before any output.
 */
function require_login(): void
{
    if (is_logged_in()) {
        return;
    }

    $target = (string)($_SERVER['REQUEST_URI'] ?? '/admin/');
    $loginPath = site_path('/admin/login.php');
    header('Location: ' . $loginPath . '?next=' . rawurlencode($target), true, 302);
    exit;
}

/**
 * Require a capability. Assumes require_login() ran first.
 *
 * Answers 403 rather than redirecting: the user IS authenticated, they simply
 * are not permitted, and bouncing them to a login they already passed is a
 * confusing lie.
 */
function require_can(string $capability): void
{
    require_login();

    if (can($capability)) {
        return;
    }

    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><meta charset="utf-8"><title>Not permitted</title>'
       . '<style>body{font:16px/1.6 system-ui,sans-serif;max-width:32rem;margin:15vh auto;padding:0 1.5rem;color:#0f172a}'
       . 'a{color:#1d4ed8}</style>'
       . '<h1>Not permitted</h1>'
       . '<p>Your account does not have access to this area.</p>'
       . '<p><a href="' . e(site_path('/admin/')) . '">Back to the dashboard</a></p>';
    exit;
}

// ---------------------------------------------------------------------------
// Login / logout
// ---------------------------------------------------------------------------

/** How long a password-verified-but-2FA-pending state survives. */
const TOTP_PENDING_SECONDS = 300;

/**
 * Attempt a login.
 *
 * Returns ['ok' => bool, 'needs_totp' => bool, 'error' => ?string]. 'error' is
 * a message safe to show the visitor — deliberately identical for "no such
 * user" and "wrong password": telling them apart is a free account-
 * enumeration oracle, which matters because an admin email is often also a
 * person's real mailbox.
 *
 * When the account has 2FA confirmed, a correct password does NOT start a
 * session — auth_start_session() only runs after auth_login_totp() (or a
 * recovery code) succeeds. Until then the only trace of this password check
 * is a short-lived pending marker holding no privileges at all.
 */
function auth_login(string $email, string $password): array
{
    // Any previous pending challenge is void the moment a new password is
    // checked — one lingering pending marker must never let a code intended
    // for a different login attempt still work.
    unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_expires']);

    $email = strtolower(trim($email));
    $ip    = auth_client_ip();

    if ($email === '' || $password === '') {
        return ['ok' => false, 'needs_totp' => false, 'error' => 'Enter your email and password.'];
    }

    if (!login_throttle_ok($email, $ip)) {
        return ['ok' => false, 'needs_totp' => false, 'error' => 'Too many attempts. Wait a few minutes and try again.'];
    }

    $user = one('
        SELECT id, email, name, password_hash, status, totp_secret, totp_confirmed_at, totp_last_counter
          FROM users WHERE lower(email) = ?
    ', [$email]);

    // Always run a hash comparison, even with no user. Returning early would
    // make a missing account measurably faster to reject than a wrong password,
    // which is the same enumeration leak by a different route.
    $hash = $user['password_hash']
        ?? '$argon2id$v=19$m=65536,t=4,p=1$AAAAAAAAAAAAAAAAAAAAAA$AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';

    $ok = password_verify($password, $hash) && $user !== null && $user['status'] === 'active';

    login_attempt_record($email, $ip, $ok, 'password');

    if (!$ok) {
        return ['ok' => false, 'needs_totp' => false, 'error' => 'Those details did not match an active account.'];
    }

    // Rehash if the cost parameters have moved on since this password was set,
    // or if the host's available algorithm differs from the stored hash's.
    if (password_needs_rehash($hash, password_algo())) {
        q('UPDATE users SET password_hash = ? WHERE id = ?',
          [password_hash($password, password_algo()), $user['id']]);
    }

    if ($user['totp_confirmed_at'] !== null) {
        // Session fixation still applies before 2FA finishes, so rotate the ID
        // now — but keep $_SESSION contents (the pending marker below, and the
        // CSRF token the challenge form needs) rather than wiping them the way
        // auth_start_session() does.
        session_regenerate_id(true);
        $_SESSION['pending_2fa_user']    = (int)$user['id'];
        $_SESSION['pending_2fa_expires'] = time() + TOTP_PENDING_SECONDS;

        return ['ok' => false, 'needs_totp' => true, 'error' => null];
    }

    auth_start_session((int)$user['id']);
    return ['ok' => true, 'needs_totp' => false, 'error' => null];
}

/** The pending user id, or null if there is none or it has expired. */
function auth_pending_totp_user_id(): ?int
{
    $userId  = $_SESSION['pending_2fa_user'] ?? null;
    $expires = $_SESSION['pending_2fa_expires'] ?? 0;

    if (!is_int($userId) || time() > $expires) {
        unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_expires']);
        return null;
    }

    return $userId;
}

/**
 * Complete a pending login with a TOTP code.
 *
 * Returns a message safe to show the visitor, or null on success. Throttled
 * through the same login_attempts budget as the password step — a second
 * factor that could be brute-forced on its own separate allowance would not
 * be much of a second factor.
 */
function auth_login_totp(string $code): ?string
{
    $userId = auth_pending_totp_user_id();
    if ($userId === null) {
        return 'That sign-in attempt expired. Enter your email and password again.';
    }

    $ip   = auth_client_ip();
    $user = one('SELECT email, status, totp_secret, totp_last_counter FROM users WHERE id = ?', [$userId]);

    if ($user === null || $user['status'] !== 'active' || $user['totp_secret'] === null) {
        unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_expires']);
        return 'That sign-in attempt is no longer valid. Enter your email and password again.';
    }

    if (!login_throttle_ok($user['email'], $ip)) {
        return 'Too many attempts. Wait a few minutes and try again.';
    }

    $lastCounter = $user['totp_last_counter'] !== null ? (int)$user['totp_last_counter'] : null;
    $accepted    = totp_verify((string)$user['totp_secret'], $code, $lastCounter);

    login_attempt_record($user['email'], $ip, $accepted !== false, 'totp');

    if ($accepted === false) {
        return 'That code was not accepted. Check the time on your phone and try again.';
    }

    q('UPDATE users SET totp_last_counter = ? WHERE id = ?', [$accepted, $userId]);

    unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_expires']);
    auth_start_session($userId);
    return null;
}

/**
 * Complete a pending login with a single-use recovery code.
 * Same return convention as auth_login_totp().
 */
function auth_login_recovery_code(string $code): ?string
{
    $userId = auth_pending_totp_user_id();
    if ($userId === null) {
        return 'That sign-in attempt expired. Enter your email and password again.';
    }

    $ip   = auth_client_ip();
    $user = one('SELECT email, status FROM users WHERE id = ?', [$userId]);

    if ($user === null || $user['status'] !== 'active') {
        unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_expires']);
        return 'That sign-in attempt is no longer valid. Enter your email and password again.';
    }

    if (!login_throttle_ok($user['email'], $ip)) {
        return 'Too many attempts. Wait a few minutes and try again.';
    }

    $normalized = totp_normalize_recovery_code($code);
    $candidates = all('
        SELECT id, code_hash FROM user_recovery_codes
         WHERE user_id = ? AND used_at IS NULL
    ', [$userId]);

    $matchedId = null;
    foreach ($candidates as $row) {
        if (password_verify($normalized, (string)$row['code_hash'])) {
            $matchedId = (int)$row['id'];
            break;
        }
    }

    login_attempt_record($user['email'], $ip, $matchedId !== null, 'recovery');

    if ($matchedId === null) {
        return 'That recovery code was not recognised.';
    }

    q('UPDATE user_recovery_codes SET used_at = now() WHERE id = ?', [$matchedId]);

    unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_expires']);
    auth_start_session($userId);
    return null;
}

/**
 * Issue a session for $userId.
 *
 * Only a hash of the token is stored. A leaked database backup should not hand
 * an attacker a set of live sessions — the same reason password_hash exists.
 */
function auth_start_session(int $userId): void
{
    // Anything the visitor accumulated before authenticating is discarded, and
    // the ID is regenerated so a token fixed by an attacker beforehand is dead.
    $_SESSION = [];
    session_regenerate_id(true);

    $token = bin2hex(random_bytes(32));

    q('
        INSERT INTO admin_sessions (user_id, token_hash, ip, user_agent, expires_at)
        VALUES (?, ?, ?, ?, ' . sql_now_plus_secs() . ')
    ', [
        $userId,
        hash('sha256', $token),
        auth_client_ip(),
        substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
        SESSION_MAX_SECONDS,
    ]);

    q('UPDATE users SET last_login_at = now() WHERE id = ?', [$userId]);

    $_SESSION['admin_token'] = $token;

    // The public site's CSRF token belongs to the pre-login session and was
    // just cleared; mint a fresh one so admin forms have something to use.
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/** Revoke the current session and clear it locally. */
function auth_logout(): void
{
    $token = $_SESSION['admin_token'] ?? null;

    if (is_string($token) && $token !== '') {
        try {
            q('UPDATE admin_sessions SET revoked_at = now() WHERE token_hash = ? AND revoked_at IS NULL',
              [hash('sha256', $token)]);
        } catch (Throwable $e) {
            error_log('auth: could not revoke session: ' . $e->getMessage());
        }
    }

    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// ---------------------------------------------------------------------------
// Throttling
// ---------------------------------------------------------------------------

/**
 * False once either the per-account or per-address failure budget is spent.
 *
 * Successes are recorded too but do not count toward the limit — a busy admin
 * logging in repeatedly is not an attack.
 */
function login_throttle_ok(string $identifier, string $ip): bool
{
    $window = 900;   // 15 minutes

    $byAccount = (int)scalar('
        SELECT count(*) FROM login_attempts
         WHERE identifier = ? AND NOT successful
           AND created_at > ' . sql_now_minus_secs() . '
    ', [$identifier, $window]);

    if ($byAccount >= 8) {
        error_log('auth: login throttle triggered for identifier=' . $identifier . ' ip=' . $ip . ' (per-account)');
        return false;
    }

    if ($ip !== '') {
        $byIp = (int)scalar('
            SELECT count(*) FROM login_attempts
             WHERE ip = ? AND NOT successful
               AND created_at > ' . sql_now_minus_secs() . '
        ', [$ip, $window]);

        // Looser than the per-account limit: an office behind one address may
        // hold several admins, and locking all of them out because one person
        // fumbled their password is a worse outcome than a slower brute force.
        if ($byIp >= 25) {
            error_log('auth: login throttle triggered for identifier=' . $identifier . ' ip=' . $ip . ' (per-ip)');
            return false;
        }
    }

    return true;
}

/** Record an attempt, and opportunistically prune old rows. */
function login_attempt_record(string $identifier, string $ip, bool $ok, string $kind = 'password'): void
{
    try {
        q('INSERT INTO login_attempts (identifier, ip, successful, kind) VALUES (?, ?, ?, ?)',
          [$identifier, $ip, $ok, $kind]);

        // No cron on shared hosting, so prune inline — rarely, and only rows
        // far outside every window.
        if (random_int(1, 50) === 1) {
            q('DELETE FROM login_attempts WHERE created_at < ' . sql_now_minus_days(7));
        }
    } catch (Throwable $e) {
        error_log('auth: could not record login attempt: ' . $e->getMessage());
    }
}

// ---------------------------------------------------------------------------
// Audit
// ---------------------------------------------------------------------------

/**
 * Record a mutating admin action.
 *
 * Never throws: an audit write failing must not roll back or block the action
 * the user asked for. It is logged instead, because a silent gap in the audit
 * trail is its own problem.
 */
function audit(string $action, string $entityType = '', string|int $entityId = '',
                ?array $before = null, ?array $after = null): void
{
    try {
        $user = current_user();
        q('
            INSERT INTO audit_log (user_id, action, entity_type, entity_id, before_data, after_data, ip)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ', [
            $user['id'] ?? null,
            $action,
            $entityType,
            (string)$entityId,
            $before === null ? null : json_encode($before, JSON_UNESCAPED_UNICODE),
            $after  === null ? null : json_encode($after,  JSON_UNESCAPED_UNICODE),
            auth_client_ip(),
        ]);
    } catch (Throwable $e) {
        error_log('audit: could not write entry (' . $action . '): ' . $e->getMessage());
    }
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * The client address.
 *
 * REMOTE_ADDR only. X-Forwarded-For is attacker-supplied unless a known proxy
 * is stripping and re-setting it, and trusting it here would let anyone evade
 * the IP throttle by sending a different header each request. If this ever runs
 * behind a CDN, add an explicit allow-list of proxy addresses — do not just
 * start reading the header.
 */
function auth_client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

/** Constant-time CSRF check for admin POSTs. */
function csrf_ok(?string $submitted): bool
{
    $expected = $_SESSION['csrf_token'] ?? '';
    return is_string($submitted)
        && $expected !== ''
        && hash_equals($expected, $submitted);
}

/** Hidden CSRF input for admin forms. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
         . htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') . '">';
}
