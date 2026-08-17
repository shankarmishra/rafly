<?php
/**
 * Creates or updates an admin user.
 *
 *     php inc/tools/create-user.php <email> <name> <role>
 *
 * role is one of: admin, editor, viewer.
 *
 * The password is prompted for, never passed as an argument — arguments show up
 * in shell history and in the process list, where any other user on the box can
 * read them.
 *
 * CLI ONLY, and deliberately not a web installer. A web-reachable "create the
 * first admin" page is a race: whoever finds it first owns the site, and it is
 * routinely forgotten in place after setup. There is no equivalent mistake
 * available here.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php'; // password_algo()

$email = $argv[1] ?? '';
$name  = $argv[2] ?? '';
$role  = $argv[3] ?? 'admin';

if ($email === '' || $name === '') {
    fwrite(STDERR, "usage: php inc/tools/create-user.php <email> <name> [admin|editor|viewer]\n");
    exit(1);
}

$email = strtolower(trim($email));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Not a valid email address: {$email}\n");
    exit(1);
}

$roleId = scalar('SELECT id FROM roles WHERE slug = ?', [$role]);
if ($roleId === null) {
    $available = implode(', ', array_column(all('SELECT slug FROM roles ORDER BY id'), 'slug'));
    fwrite(STDERR, "Unknown role '{$role}'. Available: {$available}\n");
    exit(1);
}

/** Read a password without echoing it. */
$prompt = static function (string $label): string {
    fwrite(STDOUT, $label);

    // Windows has no stty. Fall back to a visible prompt rather than silently
    // echoing a password the operator believes is hidden.
    if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
        fwrite(STDOUT, "\n  (note: this terminal will show what you type)\n  > ");
        return trim((string)fgets(STDIN));
    }

    shell_exec('stty -echo');
    $value = trim((string)fgets(STDIN));
    shell_exec('stty echo');
    fwrite(STDOUT, "\n");
    return $value;
};

$password = $prompt('Password: ');
$confirm  = $prompt('Confirm password: ');

if ($password !== $confirm) {
    fwrite(STDERR, "Passwords did not match.\n");
    exit(1);
}

// Length over composition rules. NIST dropped the mixed-character requirements
// years ago — they push people toward predictable substitutions and shorter
// secrets, which is the opposite of the intent.
if (strlen($password) < 12) {
    fwrite(STDERR, "Password must be at least 12 characters.\n");
    exit(1);
}

$hash = password_hash($password, password_algo());

$existing = one('SELECT id, name FROM users WHERE lower(email) = ?', [$email]);

if ($existing !== null) {
    $userId = (int)$existing['id'];
    q('UPDATE users SET password_hash = ?, name = ?, status = \'active\', updated_at = now() WHERE id = ?',
      [$hash, $name, $userId]);
    echo "Updated existing user {$email} (password reset).\n";
} else {
    $userId = insert_returning_id('
        INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)
    ', [$email, $hash, $name]);
    echo "Created user {$email}.\n";
}

// Idempotent: re-running with the same role is a no-op rather than an error.
insert_ignore('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)',
  [$userId, $roleId]);

$roles = implode(', ', array_column(
    all('SELECT r.slug FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = ?', [$userId]),
    'slug'
));

echo "Roles: {$roles}\n";
echo "Sign in at /admin/login.php\n";
