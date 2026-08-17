-- ---------------------------------------------------------------------------
-- 008 — repair legacy relative links inside article bodies
--
-- A data fix, not a schema change, and in the migration ledger for the reason
-- the ledger exists: it has to happen exactly once per environment, and there
-- is no other mechanism here that guarantees that. A CLI tool would work too,
-- right up until nobody remembers to run it on production.
--
-- THE BUG
-- The seeded article bodies were written with relative links —
-- service.php?service=web-security, index.php#pricing — from a time when
-- articles were reachable at insight.php?post=slug, where "service.php" and
-- the document sat in the same directory. Clean URLs moved articles to
-- /insights/{slug} and then /blog/{slug}, and a relative href resolves against
-- the DIRECTORY of the current URL. So every one of these resolved to
-- /blog/service.php?service=… and returned 404.
--
-- Eleven dead links across all four published articles, every one of them
-- pointing at a service page — which is to say, every in-body link that was
-- doing conversion work. They were equally dead at /insights/, so this is not
-- fallout from the rename; the rename is just when it got measured.
--
-- WHY ABSOLUTE PATHS
-- Rewriting to /web-development rather than ../web-development makes the link
-- independent of where the article is served from, so the next URL change
-- cannot silently break them again.
--
-- updated_at is deliberately NOT touched. This is a link repair, not an
-- editorial revision, and dateModified that churns on maintenance is exactly
-- what teaches a crawler to ignore the field — the same reasoning the posts
-- table already applies to published_at.
--
-- Idempotent: the patterns no longer exist after the first run, so re-running
-- is a no-op. inc/tools/seed-posts.php was corrected in the same change, so a
-- freshly seeded database never has them to begin with.
-- ---------------------------------------------------------------------------

UPDATE posts SET body =
    replace(
    replace(
    replace(
    replace(
    replace(
    replace(
    replace(
    replace(body,
        'href="service.php?service=web-development"',         'href="/web-development"'),
        'href="service.php?service=web-security"',            'href="/web-security"'),
        'href="service.php?service=marketing-advertisement"', 'href="/marketing-advertisement"'),
        'href="service.php?service=content-creation"',        'href="/content-creation"'),
        'href="service.php?service=ecommerce-support"',       'href="/ecommerce-support"'),
        'href="index.php#pricing"',                           'href="/#pricing"'),
        'href="index.php#contact"',                           'href="/#contact"'),
        'href="insights.php"',                                'href="/blog"')
 WHERE body LIKE '%href="service.php%'
    OR body LIKE '%href="index.php%'
    OR body LIKE '%href="insights.php%';
