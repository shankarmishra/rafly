-- ---------------------------------------------------------------------------
-- 003 — login throttling (MySQL / MariaDB)
--
-- Counterpart of ../003_login_attempts.sql.
--
-- rate_limit_ok() in inc/security.php is session-keyed, which is the right
-- trade for the public contact form: shared NAT means IP-keying would block a
-- whole office, and the honeypot plus CSRF already stop naive bots.
--
-- It is NOT sufficient for a login. An attacker guessing passwords controls
-- their own cookie jar and simply discards the session between attempts, so a
-- session-keyed counter never increments. Throttling has to persist somewhere
-- the client cannot influence.
--
-- Recorded per (identifier, ip) so that:
--   * many failures against one account lock that account's login path,
--     regardless of where they come from
--   * many failures from one address are caught even when the attacker rotates
--     usernames
--
-- The identifier index is prefixed to 191 characters: an indexed utf8mb4 column
-- is capped at 767 bytes on older InnoDB row formats, and no email approaches
-- that length anyway.
-- ---------------------------------------------------------------------------

CREATE TABLE login_attempts (
    id          BIGINT       NOT NULL AUTO_INCREMENT PRIMARY KEY,

    -- The submitted email, lowercased. Stored even when no such user exists —
    -- that is precisely the case worth rate limiting.
    identifier  VARCHAR(255) NOT NULL,
    ip          VARCHAR(45)  NOT NULL DEFAULT '',
    successful  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY login_attempts_identifier_idx (identifier(191), created_at DESC),
    KEY login_attempts_ip_idx         (ip, created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Login throttling. Prune rows older than the longest window; nothing reads them beyond that.';
