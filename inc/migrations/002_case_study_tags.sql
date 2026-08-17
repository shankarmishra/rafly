-- ---------------------------------------------------------------------------
-- 002 — case study tags
--
-- The work cards on index.php render a tag list per case study ("Web
-- Development", "SEO", "Content") that 001 missed. Added here rather than by
-- editing 001, which has already been applied — see the checksum guard in
-- inc/migrate.php.
--
-- Stored as a delimited string rather than text[] or a join table. The admin
-- edits these as one comma-separated field, the site only ever renders them in
-- order, and nothing queries or filters by an individual tag. A join table
-- would be three more queries for no capability anyone has asked for; text[]
-- would tie the column to Postgres for the same non-benefit.
-- ---------------------------------------------------------------------------

ALTER TABLE case_studies
    ADD COLUMN tags text NOT NULL DEFAULT '';

COMMENT ON COLUMN case_studies.tags IS
    'Comma-separated display tags, in render order. Split on "," and trim.';
