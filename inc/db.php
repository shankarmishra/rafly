<?php
/**
 * Database access.
 *
 * Deliberately thin: a lazily-opened PDO handle and four query helpers. There
 * is no ORM and no Composer, matching the rest of the project — the whole site
 * is hand-written PHP with no build step, and a dependency tree would be the
 * single largest thing in it.
 *
 * Every query in the application goes through these helpers with bound
 * parameters. If you find yourself interpolating a value into SQL, that is a
 * bug: pass it as a parameter instead. The only things that may be
 * interpolated are identifiers the application itself controls (a column name
 * from a hardcoded whitelist), never anything derived from a request.
 */

/** Raised when the database is unreachable or unconfigured. */
class DbUnavailable extends RuntimeException {}

/**
 * The shared connection, opened on first use.
 *
 * Connecting lazily matters: most public pages will eventually read from the
 * database, but 404s, the sitemap and static-ish pages may not, and a dead
 * database should not take down a page that never needed it.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (DB_DSN === '') {
        throw new DbUnavailable(
            'No database configured. Create inc/config.local.php with DB_DSN, '
            . 'DB_USER and DB_PASS (see inc/config.php).'
        );
    }

    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Real prepared statements, not client-side interpolation. Emulation
            // would send the query and the data to the server as one string,
            // which is the mechanism SQL injection exploits.
            PDO::ATTR_EMULATE_PREPARES   => false,

            // Without this, every integer and boolean comes back as a string and
            // strict comparisons downstream silently fail.
            //
            // On MySQL this only holds together because emulation is off: with
            // native prepares the server sends typed values, so BIGINT and INT
            // arrive as PHP int and TINYINT(1) as 0/1. Every boolean in this
            // codebase is consumed for truthiness (`if ($row['is_published'])`),
            // never `=== true`, so int 0/1 and PostgreSQL's real bool behave
            // identically. Do not introduce a `=== true` on a database boolean.
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ]);
    } catch (PDOException $e) {
        // The message can contain the DSN, which carries the host and database
        // name. Log the detail, show the visitor nothing.
        error_log('DB connection failed: ' . $e->getMessage());
        throw new DbUnavailable('Database connection failed.', 0, $e);
    }

    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
        // utf8mb4 is the only encoding that holds a four-byte character; the
        // older "utf8" alias silently truncates emoji and several Indic
        // sequences. The DSN should carry charset=utf8mb4 as well — this is the
        // belt to that braces, and costs one round-trip on connect.
        $pdo->exec("SET NAMES 'utf8mb4'");

        // ANSI_QUOTES makes MySQL read "key" as an identifier rather than a
        // string literal, which is what PostgreSQL already does. The settings
        // table has a column named `key` — a reserved word in MySQL but not in
        // PostgreSQL — and this is what lets ONE spelling of that query work on
        // both. Appended to whatever the server already has rather than
        // replacing it, so the host's STRICT_TRANS_TABLES survives.
        //
        // Consequence: double quotes in SQL are identifiers, never strings.
        // Every string literal in this codebase is single-quoted already.
        $pdo->exec("SET SESSION sql_mode = CONCAT(@@sql_mode, ',ANSI_QUOTES')");

        // Align the database clock with PHP's.
        //
        // PostgreSQL's timestamptz returns an offset, so strtotime() reads it
        // unambiguously no matter which zone either side is in. MySQL DATETIME
        // has no offset: the app writes NOW() and reads the value back through
        // strtotime(), which resolves it in PHP's default zone. If the two
        // disagree, every displayed timestamp is silently wrong by that
        // difference — and a shared host's php.ini is not ours to control.
        //
        // A numeric offset rather than 'Asia/Kolkata' because named zones need
        // the mysql.time_zone tables loaded, which shared hosting often skips.
        // Offsets are always understood. India observes no DST; a zone that did
        // would need this recomputed per connection, which is what happens here
        // anyway since the offset is read fresh on every request.
        $pdo->exec("SET time_zone = '" . (new DateTimeImmutable('now'))->format('P') . "'");
    }

    return $pdo;
}

/**
 * Which SQL dialect is on the other end: 'pgsql' or 'mysql'.
 *
 * The site is developed on PostgreSQL and deployed to MySQL on shared hosting,
 * so a handful of statements genuinely differ. Everything that differs branches
 * on this, in one of the helpers below, and nowhere else — a call site that
 * writes its own `if (db_driver() === ...)` is a sign the helper is missing.
 */
function db_driver(): string
{
    static $driver = null;
    return $driver ??= (string)db()->getAttribute(PDO::ATTR_DRIVER_NAME);
}

// ---------------------------------------------------------------------------
// Dialect bridges
//
// Each returns a SQL fragment, chosen by driver. They take no caller data and
// interpolate none — the values are compile-time constants, so splicing their
// output into a query is not a parameterisation hole. Anything derived from a
// request still goes through a bound placeholder, without exception.
// ---------------------------------------------------------------------------

/**
 * Insert a row and return its new id.
 *
 * PostgreSQL can return the id from the INSERT itself; MySQL cannot, and needs
 * lastInsertId() immediately afterwards on the same connection. Pass the INSERT
 * WITHOUT a RETURNING clause and let this add it where it belongs.
 *
 * The MySQL path is only safe because the two calls share one connection (db()
 * is a singleton) and nothing runs between them. Do not "optimise" this into a
 * separate query later.
 *
 * @param array<string|int, mixed> $params
 */
function insert_returning_id(string $sql, array $params = []): int
{
    if (db_driver() === 'pgsql') {
        return (int)scalar(rtrim(rtrim($sql), ';') . ' RETURNING id', $params);
    }

    q($sql, $params);
    return (int)db()->lastInsertId();
}

/**
 * Insert a row, silently doing nothing if it collides with a unique key.
 *
 * Used where re-running something must be a no-op rather than an error, e.g.
 * granting a role a user already holds.
 *
 * @param array<string|int, mixed> $params
 */
function insert_ignore(string $sql, array $params = []): void
{
    if (db_driver() === 'pgsql') {
        q(rtrim(rtrim($sql), ';') . ' ON CONFLICT DO NOTHING', $params);
        return;
    }

    // INSERT IGNORE downgrades more than duplicate-key to a warning (bad dates,
    // truncation). Every use here inserts values the application has already
    // validated, so there is nothing else left for it to swallow.
    $ignored = preg_replace('/^(\s*)INSERT\s+INTO\b/i', '$1INSERT IGNORE INTO', $sql, 1, $count);

    if ($count !== 1 || $ignored === null) {
        throw new InvalidArgumentException('insert_ignore() expects a statement beginning with INSERT INTO.');
    }

    q($ignored, $params);
}

/** Case-insensitive LIKE operator: MySQL's default collations already are. */
function sql_ilike(): string
{
    return db_driver() === 'pgsql' ? 'ILIKE' : 'LIKE';
}

/**
 * A timestamp $seconds in the future, taking the count as a BOUND parameter.
 * The placeholder it contains must be supplied in the usual left-to-right order.
 */
function sql_now_plus_secs(): string
{
    return db_driver() === 'pgsql'
        ? 'now() + make_interval(secs => ?)'
        : 'DATE_ADD(NOW(), INTERVAL ? SECOND)';
}

/** As above, in the past. */
function sql_now_minus_secs(): string
{
    return db_driver() === 'pgsql'
        ? 'now() - make_interval(secs => ?)'
        : 'DATE_SUB(NOW(), INTERVAL ? SECOND)';
}

/**
 * A timestamp $days in the past, as a literal.
 *
 * $days is cast to int before it reaches the SQL, so this cannot carry a value
 * from a request even if someone later passes one.
 */
function sql_now_minus_days(int $days): string
{
    return db_driver() === 'pgsql'
        ? "now() - interval '{$days} days'"
        : "DATE_SUB(NOW(), INTERVAL {$days} DAY)";
}

/**
 * Concatenate a column across a group, in a defined order.
 *
 * $expr and $orderBy are SQL identifiers the application controls — never pass
 * anything derived from a request.
 */
function sql_group_concat(string $expr, string $separator, string $orderBy): string
{
    $sep = "'" . str_replace("'", "''", $separator) . "'";

    return db_driver() === 'pgsql'
        ? "string_agg({$expr}, {$sep} ORDER BY {$orderBy})"
        : "GROUP_CONCAT({$expr} ORDER BY {$orderBy} SEPARATOR {$sep})";
}

/** True if the database is configured and reachable. Never throws. */
function db_available(): bool
{
    static $ok = null;
    if ($ok !== null) {
        return $ok;
    }
    try {
        db();
        return $ok = true;
    } catch (Throwable) {
        return $ok = false;
    }
}

/**
 * Run a statement and return it. Use for INSERT/UPDATE/DELETE, or when you
 * want the statement object itself.
 *
 * Parameters are bound individually rather than passed to execute(), because
 * execute() casts every value to string — and PHP's false casts to '', which
 * PostgreSQL rejects for a boolean column with "invalid input syntax". Binding
 * with an explicit type keeps booleans and nulls intact.
 *
 * @param array<string|int, mixed> $params
 */
function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);

    foreach ($params as $key => $value) {
        // Positional placeholders are 1-indexed; named ones are passed through.
        $param = is_int($key) ? $key + 1 : $key;

        $type = match (true) {
            is_bool($value) => PDO::PARAM_BOOL,
            is_int($value)  => PDO::PARAM_INT,
            is_null($value) => PDO::PARAM_NULL,
            default         => PDO::PARAM_STR,
        };

        $st->bindValue($param, $value, $type);
    }

    $st->execute();
    return $st;
}

/**
 * First matching row, or null.
 *
 * @param array<string|int, mixed> $params
 * @return array<string, mixed>|null
 */
function one(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row === false ? null : $row;
}

/**
 * All matching rows.
 *
 * @param array<string|int, mixed> $params
 * @return list<array<string, mixed>>
 */
function all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

/**
 * Single scalar from the first column of the first row, or null.
 *
 * @param array<string|int, mixed> $params
 */
function scalar(string $sql, array $params = []): mixed
{
    $v = q($sql, $params)->fetchColumn();
    return $v === false ? null : $v;
}

/**
 * A single value from the settings table, or $default if it is missing.
 *
 * The whole table is fetched once and cached for the request. It is a dozen
 * short rows, and the alternative — a query per lookup — meant the homepage
 * alone issued six round-trips to render four numbers.
 *
 * Returns $default rather than throwing when the database is down, so a
 * settings-driven value degrades to its hardcoded fallback instead of taking
 * down the page.
 */
function setting(string $key, ?string $default = null): ?string
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        if (db_available()) {
            try {
                // "key" is quoted because it is a reserved word in MySQL. The
                // ANSI_QUOTES mode set on connect makes this spelling mean the
                // same thing on both drivers.
                foreach (all('SELECT "key", value FROM settings') as $row) {
                    $cache[(string)$row['key']] = (string)$row['value'];
                }
            } catch (Throwable $e) {
                error_log('settings load failed: ' . $e->getMessage());
            }
        }
    }

    if (!array_key_exists($key, $cache) && function_exists('seed_preview_setting')) {
        // Design preview only: inc/repo/seed.php answers when there is no DB
        // and PREVIEW_SEED is on; otherwise this is null and the default wins.
        $seeded = seed_preview_setting($key);
        if ($seeded !== null) {
            return $seeded;
        }
    }

    return $cache[$key] ?? $default;
}

/**
 * Run $fn inside a transaction, rolling back if it throws.
 *
 * Nested calls join the outer transaction rather than opening a second one,
 * which PDO does not support and which would otherwise commit early.
 */
function tx(callable $fn): mixed
{
    $pdo = db();

    if ($pdo->inTransaction()) {
        return $fn($pdo);
    }

    $pdo->beginTransaction();
    try {
        $result = $fn($pdo);
        $pdo->commit();
        return $result;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
