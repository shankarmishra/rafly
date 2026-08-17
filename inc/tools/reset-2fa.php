<?php
/**
 * Clears two-factor auth on one account, for when a lost phone and no
 * remaining recovery code would otherwise mean a permanent lockout.
 *
 *     php inc/tools/reset-2fa.php <email>
 *
 * CLI ONLY, same reasoning as create-user.php: a web-reachable "turn off my
 * 2FA" page is a standing account-takeover shortcut for anyone who steals a
 * session cookie, and there is no equivalent risk available from a shell that
 * already requires access to the box.
 *
 * Clears the secret, the confirmation, the replay counter and every recovery
 * code. The account falls back to password-only login; the next sign-in
 * offers /admin/2fa-setup.php again exactly as if 2FA had never been turned
 * on.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

$email = strtolower(trim((string)($argv[1] ?? '')));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "usage: php inc/tools/reset-2fa.php <email>\n");
    exit(1);
}

$user = one('SELECT id, totp_confirmed_at FROM users WHERE lower(email) = ?', [$email]);

if ($user === null) {
    fwrite(STDERR, "No user with that email: {$email}\n");
    exit(1);
}

if ($user['totp_confirmed_at'] === null) {
    echo "{$email} does not have two-factor authentication enabled. Nothing to do.\n";
    exit(0);
}

tx(static function () use ($user): void {
    q('UPDATE users SET totp_secret = NULL, totp_confirmed_at = NULL, totp_last_counter = NULL WHERE id = ?',
      [$user['id']]);
    q('DELETE FROM user_recovery_codes WHERE user_id = ?', [$user['id']]);
});

echo "Two-factor authentication cleared for {$email}.\n";
echo "They can sign in with just their password, and re-enroll at /admin/2fa-setup.php.\n";
