-- ---------------------------------------------------------------------------
-- 004 — core team members (MySQL / MariaDB)
--
-- Counterpart of ../004_team_members.sql.
--
-- One row per person on the public team page (team.php). The card is a native
-- <details>: collapsed it shows photo + name + role + brief, and the arrow
-- opens it onto the bio and the profile links.
--
-- brief and bio are separate columns on purpose. The closed state is the line
-- a visitor actually reads, so an editor has to be able to write it; deriving
-- it from the first 200 characters of bio would make the teaser a side effect
-- of however paragraph one happened to start.
--
-- The photo is a reference into the existing media library rather than a
-- filename column or a second upload path. admin/media.php is the only upload
-- pipeline in this codebase and it is deliberately strict — generated
-- filenames, sniffed MIME, a getimagesize() check, no SVG. A second, laxer
-- path just for headshots would undo all of it.
--
-- ON DELETE SET NULL, not CASCADE: removing an image must not remove the
-- person. The card falls back to the i-user icon.
--
-- Every VARCHAR width here equals the matching str_cut() cap in
-- admin/team.php. That pairing is the only thing standing between a long
-- paste and a silent MySQL truncation, so the two move together or not at all.
--
-- updated_at carries DEFAULT CURRENT_TIMESTAMP but deliberately NOT
-- ON UPDATE — the application sets it explicitly, and two mechanisms writing
-- one column is exactly how they drift. Same as testimonials.
-- ---------------------------------------------------------------------------

CREATE TABLE team_members (
    id             BIGINT        NOT NULL AUTO_INCREMENT PRIMARY KEY,

    name           VARCHAR(120)  NOT NULL DEFAULT '',
    role           VARCHAR(120)  NOT NULL DEFAULT '',

    brief          VARCHAR(200)  NOT NULL DEFAULT ''
                   COMMENT 'One line, shown while the card is collapsed.',
    bio            VARCHAR(2000) NOT NULL DEFAULT ''
                   COMMENT 'Full detail, revealed when the card is expanded.',

    -- Absolute http(s) only; a relative URL is meaningless for an off-site
    -- profile. Enforced in admin/team.php, which is the only writer.
    github_url     VARCHAR(300)  NOT NULL DEFAULT '',
    linkedin_url   VARCHAR(300)  NOT NULL DEFAULT '',

    photo_media_id BIGINT        NULL
                   COMMENT 'media.id of the headshot. NULL renders the i-user fallback.',

    sort_order     INT           NOT NULL DEFAULT 0,
    is_placeholder TINYINT(1)    NOT NULL DEFAULT 1
                   COMMENT 'Renders the orange PLACEHOLDER badge and withholds the Person JSON-LD node.',
    is_published   TINYINT(1)    NOT NULL DEFAULT 1,

    created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY team_members_photo_idx (photo_media_id),
    CONSTRAINT team_members_photo_fk FOREIGN KEY (photo_media_id)
        REFERENCES media (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'People rendered on team.php. Ordered by sort_order, then id.';
