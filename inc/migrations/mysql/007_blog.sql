-- ---------------------------------------------------------------------------
-- 007 — blog: categories, cover images and a credited author (MySQL / MariaDB)
--
-- Counterpart of ../007_blog.sql. The reasoning lives there; only the dialect
-- differences are annotated here.
--
-- Every VARCHAR width equals the matching str_cut() cap in admin/posts.php and
-- admin/categories.php. That pairing is the only thing standing between a long
-- paste and a silent MySQL truncation, so the two move together or not at all.
--
-- created_at carries DEFAULT CURRENT_TIMESTAMP but deliberately no ON UPDATE,
-- matching every other table here — the application writes timestamps, and two
-- mechanisms writing one column is exactly how they drift.
-- ---------------------------------------------------------------------------

CREATE TABLE categories (
    id          BIGINT       NOT NULL AUTO_INCREMENT PRIMARY KEY,

    slug        VARCHAR(120) NOT NULL UNIQUE,
    name        VARCHAR(120) NOT NULL DEFAULT '',

    description VARCHAR(400) NOT NULL DEFAULT ''
                COMMENT 'Intro copy and meta description for the filtered listing page.',

    sort_order  INT          NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Topic vocabulary for posts. Ordered by sort_order, then name.';

CREATE TABLE post_categories (
    post_id     BIGINT NOT NULL,
    category_id BIGINT NOT NULL,

    PRIMARY KEY (post_id, category_id),
    KEY post_categories_category_idx (category_id),

    CONSTRAINT post_categories_post_fk FOREIGN KEY (post_id)
        REFERENCES posts (id) ON DELETE CASCADE,
    CONSTRAINT post_categories_category_fk FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE posts
    ADD COLUMN cover_media_id BIGINT NULL
        COMMENT 'media.id of the cover image. NULL renders the card without a media block.',
    ADD COLUMN author_team_id BIGINT NULL
        COMMENT 'team_members.id of the credited author — an editorial choice, unlike author_id.',
    ADD KEY posts_cover_idx  (cover_media_id),
    ADD KEY posts_author_team_idx (author_team_id),
    ADD CONSTRAINT posts_cover_fk FOREIGN KEY (cover_media_id)
        REFERENCES media (id) ON DELETE SET NULL,
    ADD CONSTRAINT posts_author_team_fk FOREIGN KEY (author_team_id)
        REFERENCES team_members (id) ON DELETE SET NULL;

-- --- Backfill --------------------------------------------------------------
-- REGEXP_REPLACE with the identical [^a-z0-9]+ pattern the PostgreSQL file
-- uses, so both dialects produce byte-identical slugs. This matters more than
-- it looks: a slug is a URL, and two databases disagreeing about it means the
-- same article answers to a different address depending on where it is hosted.
--
-- An earlier draft used nested REPLACE() over space and slash on the grounds
-- that the existing tags are plain words. They are not quite — one of them is
-- "What's Next", and that spelling produced the slug what's-next, with an
-- apostrophe in a query string. Available since MariaDB 10.0.5 and MySQL 8.0;
-- DEPLOY.md pins MariaDB 12.3.
--
-- INSERT IGNORE rather than ON CONFLICT: the same intent, spelled the way this
-- dialect spells it, and the same pairing inc/db.php draws in insert_ignore().

INSERT IGNORE INTO categories (slug, name)
SELECT DISTINCT
       TRIM(BOTH '-' FROM REGEXP_REPLACE(LOWER(TRIM(tag)), '[^a-z0-9]+', '-')),
       TRIM(tag)
  FROM posts
 WHERE TRIM(tag) <> '';

INSERT IGNORE INTO post_categories (post_id, category_id)
SELECT p.id, c.id
  FROM posts p
  JOIN categories c
    ON c.slug = TRIM(BOTH '-' FROM REGEXP_REPLACE(LOWER(TRIM(p.tag)), '[^a-z0-9]+', '-'))
 WHERE TRIM(p.tag) <> '';
