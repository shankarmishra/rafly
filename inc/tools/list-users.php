<?php
/**
 * Lists all registered users in the database.
 *
 *     php inc/tools/list-users.php
 *
 * CLI ONLY.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

try {
    $db = db();
} catch (DbUnavailable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$users = all('
    SELECT u.id, u.email, u.name, u.status, u.created_at,
           GROUP_CONCAT(r.slug SEPARATOR ", ") AS roles
      FROM users u
 LEFT JOIN user_roles ur ON ur.user_id = u.id
 LEFT JOIN roles r ON r.id = ur.role_id
  GROUP BY u.id
  ORDER BY u.id ASC
');

if (empty($users)) {
    echo "No users found in database.\n";
    echo "Create one using: php inc/tools/create-user.php <email> <name> admin\n";
    exit(0);
}

printf("%-5s | %-30s | %-20s | %-10s | %-15s\n", "ID", "Email", "Name", "Status", "Roles");
echo str_repeat("-", 90) . "\n";

foreach ($users as $u) {
    printf("%-5d | %-30s | %-20s | %-10s | %-15s\n",
        $u['id'],
        $u['email'],
        $u['name'],
        $u['status'],
        $u['roles'] ?? 'none'
    );
}
