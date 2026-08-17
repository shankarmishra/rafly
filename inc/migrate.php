<?php
/**
 * Migration runner.
 *
 *     php inc/migrate.php            apply everything pending
 *     php inc/migrate.php --status   list applied / pending, change nothing
 *
 * CLI only, and under inc/ (which .htaccess 404s) so it is unreachable over
 * HTTP. A web-triggerable schema change is a remote code execution primitive
 * wearing a hat.
 *
 * Migrations are plain .sql files applied in filename order. There are two
 * parallel sets, chosen by the driver behind DB_DSN:
 *
 *     inc/migrations/          PostgreSQL — local development
 *     inc/migrations/mysql/    MySQL / MariaDB — shared hosting
 *
 * They are deliberately separate files rather than one dialect-neutral set.
 * DDL is where the two databases differ most (identity columns, JSON, the
 * collation that makes an email lookup case-insensitive), and a lowest common
 * denominator schema would be worse on both than two honest ones. The
 * APPLICATION code is shared and driver-agnostic; only the schema forks.
 *
 * Each set has its own schema_migrations ledger, because each lives in its own
 * database. The same filename appearing in both is expected.
 *
 * On PostgreSQL each migration runs inside a transaction, so a failure halfway
 * leaves no partial schema behind. MySQL COMMITS IMPLICITLY on every DDL
 * statement and cannot offer that — a failure there can leave the schema
 * partly applied, and the runner says so rather than pretending otherwise.
 *
 * Applied files are recorded by name AND by checksum. Editing a migration that
 * has already run is the classic way to get two environments silently out of
 * sync; this refuses to continue instead. Fix forward with a new migration.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$statusOnly = in_array('--status', $argv, true);

try {
    $pdo = db();
} catch (DbUnavailable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$isMysql = db_driver() === 'mysql';
$dir     = __DIR__ . '/migrations' . ($isMysql ? '/mysql' : '');

echo "driver: " . db_driver() . "  ({$dir})\n";

// Bootstrap the ledger. Not itself a migration, because it is what tracks them.
// filename is capped at 191 characters on MySQL: an indexed VARCHAR key under
// utf8mb4 is limited to 767 bytes on older InnoDB row formats, and 191 * 4 is
// the largest that always fits. No migration file comes close.
$pdo->exec($isMysql
    ? 'CREATE TABLE IF NOT EXISTS schema_migrations (
           filename    VARCHAR(191) NOT NULL PRIMARY KEY,
           checksum    VARCHAR(64)  NOT NULL,
           applied_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    : 'CREATE TABLE IF NOT EXISTS schema_migrations (
           filename    text        PRIMARY KEY,
           checksum    text        NOT NULL,
           applied_at  timestamptz NOT NULL DEFAULT now()
       )'
);

/**
 * Split a migration into individual statements.
 *
 * PDO's MySQL driver does not enable multi-statement queries, so the whole file
 * cannot be handed to exec() the way PostgreSQL accepts it. Semicolons inside
 * string literals and comments are not statement boundaries, so this tracks
 * quoting rather than calling explode(';') and hoping.
 *
 * @return list<string>
 */
function split_sql_statements(string $sql): array
{
    $statements = [];
    $current    = '';
    $len        = strlen($sql);
    $inSingle   = false;
    $inDouble   = false;
    $inBacktick = false;

    for ($i = 0; $i < $len; $i++) {
        $c    = $sql[$i];
        $next = $sql[$i + 1] ?? '';

        if (!$inSingle && !$inDouble && !$inBacktick) {
            // Line comment: -- to end of line, and MySQL's # variant.
            if (($c === '-' && $next === '-') || $c === '#') {
                $nl = strpos($sql, "\n", $i);
                $i  = $nl === false ? $len : $nl;
                $current .= "\n";
                continue;
            }
            // Block comment.
            if ($c === '/' && $next === '*') {
                $end = strpos($sql, '*/', $i + 2);
                $i   = $end === false ? $len : $end + 1;
                continue;
            }
            if ($c === ';') {
                if (trim($current) !== '') {
                    $statements[] = trim($current);
                }
                $current = '';
                continue;
            }
        }

        // Backslash escapes the next character inside a string (MySQL default).
        if (($inSingle || $inDouble) && $c === '\\') {
            $current .= $c . $next;
            $i++;
            continue;
        }

        if ($c === "'" && !$inDouble && !$inBacktick) { $inSingle = !$inSingle; }
        elseif ($c === '"' && !$inSingle && !$inBacktick) { $inDouble = !$inDouble; }
        elseif ($c === '`' && !$inSingle && !$inDouble) { $inBacktick = !$inBacktick; }

        $current .= $c;
    }

    if (trim($current) !== '') {
        $statements[] = trim($current);
    }

    return $statements;
}

$files = glob($dir . '/*.sql') ?: [];
sort($files, SORT_STRING);

if (!$files) {
    fwrite(STDERR, "No migrations found in {$dir}\n");
    exit(1);
}

$applied = [];
foreach (all('SELECT filename, checksum FROM schema_migrations') as $row) {
    $applied[$row['filename']] = $row['checksum'];
}

$pending = [];
foreach ($files as $path) {
    $name = basename($path);
    $sum  = hash_file('sha256', $path);

    if (!isset($applied[$name])) {
        $pending[] = [$name, $path, $sum];
        continue;
    }

    if (!hash_equals($applied[$name], $sum)) {
        fwrite(STDERR,
            "REFUSING TO RUN: {$name} has changed since it was applied.\n"
            . "  A migration that has run is history and must not be edited — other\n"
            . "  environments already have the old version. Write a new migration\n"
            . "  that makes the change instead.\n");
        exit(1);
    }
}

if ($statusOnly) {
    echo "applied:\n";
    foreach (array_keys($applied) as $n) { echo "  {$n}\n"; }
    echo $applied ? '' : "  (none)\n";
    echo "pending:\n";
    foreach ($pending as [$n]) { echo "  {$n}\n"; }
    echo $pending ? '' : "  (none)\n";
    exit(0);
}

if (!$pending) {
    echo "up to date — nothing to apply\n";
    exit(0);
}

foreach ($pending as [$name, $path, $sum]) {
    $sql = file_get_contents($path);
    if ($sql === false || trim($sql) === '') {
        fwrite(STDERR, "empty or unreadable: {$name}\n");
        exit(1);
    }

    echo "applying {$name} ... ";

    if ($isMysql) {
        // No transaction: MySQL commits implicitly on every CREATE/ALTER, so
        // opening one would be theatre — and rollBack() on a transaction the
        // server has already closed raises its own error, hiding the real one.
        try {
            foreach (split_sql_statements($sql) as $statement) {
                $pdo->exec($statement);
            }
            q('INSERT INTO schema_migrations (filename, checksum) VALUES (?, ?)', [$name, $sum]);
            echo "ok\n";
        } catch (Throwable $e) {
            echo "FAILED\n";
            fwrite(STDERR, "  " . $e->getMessage() . "\n");
            fwrite(STDERR, "  MySQL cannot roll back DDL: the schema may be PARTLY applied and\n");
            fwrite(STDERR, "  {$name} is NOT recorded. Inspect the database before re-running —\n");
            fwrite(STDERR, "  on an empty database, dropping and recreating it is the clean fix.\n");
            exit(1);
        }
        continue;
    }

    $pdo->beginTransaction();
    try {
        $pdo->exec($sql);
        q('INSERT INTO schema_migrations (filename, checksum) VALUES (?, ?)', [$name, $sum]);
        $pdo->commit();
        echo "ok\n";
    } catch (Throwable $e) {
        $pdo->rollBack();
        echo "FAILED\n";
        fwrite(STDERR, "  " . $e->getMessage() . "\n");
        fwrite(STDERR, "  rolled back; no schema change was kept\n");
        exit(1);
    }
}

echo "done — " . count($pending) . " migration(s) applied\n";
