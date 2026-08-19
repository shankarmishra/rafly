# SEO Audit — rafly.in

Written 2026-08-18, against the working tree before/after this engagement. Every
finding below was verified by reading the actual code and, where practical, by
running the site (PHP built-in server, `router.php`) and its automated checks —
not inferred from convention.

## Starting position

This was **not** a bare site. Before this engagement, rafly.in already had:

- Canonical URL handling with an allow-listed `SITE_ORIGIN` host (`inc/config.php`)
- A JSON-LD `@graph` engine (`inc/schema.php`) — Organization, WebSite, Service,
  FAQPage, BreadcrumbList, Person, BlogPosting
- Clean URLs with 301s from every legacy `.php` path (`.htaccess` + `router.php`,
  kept in sync by hand and documented as such)
- A generated, DB-aware XML sitemap (`sitemap.php`)
- `X-Robots-Tag: noindex` on the admin panel (`inc/security.php`)
- A full security-header layer (CSP, HSTS, X-Frame-Options, etc.)
- A dependency-free CDP-driven audit harness (`inc/tools/audit.mjs`) checking
  keyboard access, reduced-motion compliance, per-route weight and no-JS
  readability — genuinely unusual rigor for a project this size
- Service pages with real depth: deliverables, process, tools, and a
  `boundaries` array stating what the business will **not** take on
- `is_placeholder` flags on case studies/testimonials, rendering a visible
  orange badge rather than presenting sample content as real

The gap was never "add basic SEO." It was: close a handful of real technical
defects, then build the three layers that genuinely didn't exist — **local/geo
targeting, the entity graph, and internal linking** — while respecting the
codebase's own defining constraint: it refuses to fabricate.

## Findings, by severity

### P0 — indexation-affecting

| # | Finding | Evidence | Resolution |
|---|---|---|---|
| 1 | Trailing-slash duplicates: `/about` and `/about/` both served 200 with no redirect, each self-canonicalising. Affected every clean route including all 5 services and every article. | `.htaccess:95-105` (pre-fix), `partials/head.php:67` (pre-fix) | 301 rule added to `.htaccess` and mirrored in `router.php`; canonical fallback in `head.php` now also strips a trailing slash defensively, so the two can never disagree even if a rule is later missed. |
| 2 | `/submit` and `/thank-you` were both `Disallow`'d in `robots.txt` **and** `noindex`'d — a page blocked from crawling never has its `noindex` tag read, so it can still surface as a bare URL. | `robots.txt` (pre-fix) | Removed from `Disallow`; `noindex` (already correct) is now the sole, sufficient control. |
| 3 | Homepage meta description and hero copy advertised "AI automation" as a bundled service, alongside the five real ones. `SERVICES` has five slugs; no such page exists. The site's own body copy already correctly hedges this ("actively building out AI automation") — only the two most search-visible strings overclaimed. | `index.php:132,182` (pre-fix) vs `inc/config.php` `SERVICES` | Removed from meta description and hero lead; the honest "actively building out" mentions elsewhere were left as-is. |
| 4 | **Real, unrelated bug found during testing**: `partials/header.php` read `$s['url']` on every `SOCIAL_LINKS` entry, but the array's key is `href`. Every social link in the mobile drawer rendered with an empty `href` on every page, and (in debug mode) emitted 5 PHP warnings per page. | `partials/header.php:101` (pre-fix) | One-line fix to `$s['href']`. |
| 5 | `pricing.php` skipped a heading level (`h1` → `h3`, no `h2`) — the three price-card titles landed directly under the page's own `h1`. Found by the new `seo-audit.mjs`, not by manual review. | `pricing.php` (pre-fix) | Added a `.visually-hidden` `h2` ("Bundle packages") above the price grid — no visual change, correct outline. |

### P1 — the three missing layers (see `architecture.md`, `entity-model.md`)

6. **Zero geo signal.** Despite a real registered address in Greater Noida
   West, UP, not one page, title, or heading mentioned Noida, Greater Noida,
   or Delhi NCR, and `schema_service()` hardcoded `areaServed` to `Country:
   India`. Built: `BUSINESS_GEO_*` constants, `schema_area_served()`, one real
   `/locations/greater-noida` page, natural geo mentions on About/Contact/every
   service page.
7. **Shallow entity graph.** Organization/WebSite were emitted sitewide but
   never cross-referenced the five Services; no `SearchAction`; About/Team
   hand-rolled near-duplicate WebPage nodes. Built: `makesOffer` linking
   Organization → Service by stable `@id`, `SearchAction` on the real `?q=`
   search, an extracted `schema_webpage()` helper, `@id`s on Person nodes so
   `schema_article()` references the same author entity `/team` does.
8. **No internal linking between content types.** Articles never linked to
   services; services never linked to articles or case studies. Built:
   `inc/repo/links.php` — category-to-service resolution (with a real bug fix,
   see below), rendered on `blog-post.php`, `service.php`, and
   `case-studies.php`.
9. **Images invisible to search.** `photo()` emitted one fixed-width image, no
   `srcset`; several real content photos had `alt=""`. Built: width-variant
   generation in `build-photos.php`, `srcset`/`sizes` in `photo()`, real alt
   text on the five service photos (verified by viewing each image), image
   sitemap entries.
10. **No Bing/IndexNow path.** Built, entirely inert until a key is set:
    `indexnow-key.php`, `inc/tools/indexnow.php`, verification meta tags in
    `head.php`.

### A real bug found only by testing against production data

`inc/repo/links.php`'s category→service map was written against
`inc/data/seed-preview.php`'s sample category slugs (`security`,
`e-commerce`). Running the actual production content pipeline
(`inc/tools/seed-posts.php`) against a real database revealed that a clean
production deploy never produces those slugs — a one-time migration backfill
(`inc/migrations/007_blog.sql`) generates categories from each post's `tag`
column instead, producing `web-security`, not `security`. On this local dev
database (which had both seeders run against it at different times) the bug
was invisible; on a genuinely fresh production database it would have
silently linked nothing. Fixed in two places: the map now keys on the
tag-derived slugs a real deploy actually produces, and `seed-posts.php` itself
now creates and links categories on every run — closing the gap where the
one-time migration backfill would otherwise leave every article added *after*
launch permanently uncategorised.

## What was NOT changed, and why

- **The 3D/WebGL layer** — gated, has designed fallbacks, verified by
  `audit.mjs`'s own no-JS/reduced-motion checks. `js/gl.js` was scoped to load
  only on the homepage (it was shipping unconditionally on every page for a
  `<canvas>` element that only exists on one), but nothing about the layer
  itself was removed.
- **`pricing.php`'s two `<h1>` elements**, flagged in initial planning —
  on inspection these are mutually-exclusive `if`/`else` branches; only one
  ever renders. Not a real bug.
- **Case study / testimonial placeholder content** — left exactly as-is.
  Fabricating case studies to fill out image/rich-result opportunities would
  violate the one rule this codebase treats as non-negotiable.

## Verification performed

- `php -l` on every changed file.
- Full route smoke test (status codes, redirect targets) across every clean
  URL, trailing-slash variant, legacy `.php` path, and the new location page.
- `node inc/tools/audit.mjs` — the pre-existing keyboard/motion/no-JS/weight
  suite — run before and after; **zero regressions**, confirmed on the final
  pass.
- `node inc/tools/seo-audit.mjs` (new) — 19 sitemap URLs, unique titles/
  descriptions, self-referential canonicals, exactly-one-`h1`, no heading
  skips, JSON-LD parses with every `@id` reference resolved, every `<img>` has
  `alt`+dimensions, zero broken internal links, zero redirect chains, zero
  orphan pages. All green on the final run.
- JSON-LD manually inspected on `/`, `/web-development`, `/locations/
  greater-noida`, `/about`, `/team` — confirmed `areaServed`, `hasMap`,
  `makesOffer`, `SearchAction` all present and correctly cross-referencing.
- `inc/tools/seed-posts.php` run against the real local MariaDB instance —
  confirmed insert, category creation, and linkage end-to-end, not just
  against the design-preview fallback.
- `sitemap.xml` validated as well-formed XML via `DOMDocument`; 19 `<url>`
  entries (up from 13), 14 `<image:image>` entries.

## Before / after, measured

| Metric | Before | After |
|---|---|---|
| Indexable URLs in sitemap | 13 | 19 (+1 location page, +1 real article, image entries) |
| Trailing-slash duplicate URLs | Every clean route | 0 |
| Pages with `areaServed` beyond "India" | 0 | 6 (Organization sitewide + 5 services) |
| `@id` cross-references in JSON-LD | 0 | 6 (5 services + author) |
| Internal links: articles → services | 0 | Every categorised article |
| Internal links: services → articles/case studies | 0 | Every service with a match |
| Real content photos with descriptive alt | 0 of 5 | 5 of 5 |
| Images with responsive `srcset` | 0 | 12 photos, 3 widths each where source allows |
| `js/gl.js` shipped on | Every page (14) | 1 page (homepage only) |
| Automated SEO regression checks | 0 | 13, across 5 categories |
| Real published articles | 4 | 5 |
