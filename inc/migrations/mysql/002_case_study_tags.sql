-- ---------------------------------------------------------------------------
-- 002 — case study tags (MySQL / MariaDB)
--
-- Counterpart of ../002_case_study_tags.sql.
--
-- The work cards on index.php render a tag list per case study ("Web
-- Development", "SEO", "Content") that 001 missed. Added here rather than by
-- editing 001, which has already been applied — see the checksum guard in
-- inc/migrate.php.
--
-- Stored as a delimited string rather than an array or a join table. The admin
-- edits these as one comma-separated field, the site only ever renders them in
-- order, and nothing queries or filters by an individual tag. A join table
-- would be three more queries for no capability anyone has asked for.
--
-- VARCHAR rather than TEXT because the column carries a DEFAULT, which the TEXT
-- family cannot on every supported server version. The COMMENT is inline here;
-- MySQL has no separate COMMENT ON COLUMN statement.
-- ---------------------------------------------------------------------------

ALTER TABLE case_studies
    ADD COLUMN tags VARCHAR(255) NOT NULL DEFAULT ''
    COMMENT 'Comma-separated display tags, in render order. Split on "," and trim.';
