# Executive Report — Search Visibility Engineering, rafly.in

2026-08-18. Full working detail in the rest of `docs/seo/`; this is the
synthesis.

## Current state

rafly.in started this engagement with an unusually strong technical
foundation for a site its size — real canonical handling, a JSON-LD graph,
clean URLs with proper 301s, a generated sitemap, security headers, and a
CDP-driven regression harness already checking keyboard access, motion
compliance and per-route weight. It also started with a hard, real
constraint worth stating plainly: the codebase refuses to fabricate — no
invented case studies, no invented metrics, no invented testimonials. That
constraint shaped every decision in this engagement, not just the content
pass.

What was actually missing was narrower and more specific than "SEO": zero
local/geo signal despite a real registered office, a shallow entity graph
that never connected the five services to the organization or to each other,
zero internal linking between articles/services/case studies, and no
automated way to catch a future regression in any of it. Two contradictory
technical controls existed (`robots.txt` blocking a page that also declared
`noindex` — the tag was never reachable) and one page had a real
accessibility/structure defect (a skipped heading level). One unrelated,
previously-unknown bug was also found and fixed: every social link in the
mobile drawer, on every page, was broken.

## Competitive position

Three real, currently-trading Delhi NCR/Greater Noida competitors were
researched live (`docs/seo/competitors.md`). None publish pricing. None
publish what they don't do. None show a named case study with a verifiable
metric — all use aggregate, unattributable numbers ("1000+ clients"). None
name web security as a distinct service line. Rafly's existing content model
already had the material for all four differentiators; none of it was
reinforced by the technical layer search engines and AI answer systems
actually read. That reinforcement is what this engagement built. The one
real competitive gap found — a local peer with 161 accumulated third-party
reviews — is a genuine, time-dependent asset no code change manufactures,
and is called out as such rather than glossed over.

## Biggest opportunities

1. **Local commercial-investigation content** — the one substantive content
   gap this engagement found and filled: no page anywhere addressed "how do
   I choose a partner in this market," despite it being the natural
   commercial-investigation query for the business's actual positioning.
2. **Populating real pricing** — the architecture already supports it
   (`bundles_all()`/`price_text`); zero researched competitors publish any
   pricing signal at all. This is a data-entry decision, not a code change,
   and is the single highest-leverage item left undone.
3. **Real case studies** — genuinely blocked on client consent, not on
   anything technical. The `is_placeholder` architecture means the moment
   real ones exist, they slot in with zero further engineering.

## Implemented

Full detail in `audit.md`; summarized:

- **Technical**: trailing-slash redirect (both `.htaccess` and `router.php`,
  plus a defensive canonical fallback), `robots.txt` contradiction removed,
  overclaiming metadata corrected, a real unrelated bug fixed
  (`header.php`'s social links), a real heading-hierarchy defect fixed
  (`pricing.php`).
- **Local/geo**: `BUSINESS_GEO_*` constants, three-level `areaServed` on
  Organization and every Service, `hasMap`, one real `/locations/
  greater-noida` page, natural geo copy on About/Contact/every service page.
- **Entity graph**: `makesOffer` linking Organization to all five Services by
  stable `@id`, a real `SearchAction` for the site's existing search feature,
  an extracted `schema_webpage()` helper removing duplicated markup, `@id`s
  on Person nodes so an article's byline and `/team`'s listing resolve to the
  same entity.
- **Internal linking**: a new `inc/repo/links.php`, wired into
  `blog-post.php`, `service.php`, and `case-studies.php` — plus a real bug
  found and fixed where the category-matching logic would have silently done
  nothing on a genuinely clean production database.
- **Images**: responsive `srcset` width variants generated and wired through
  `photo()`, real (visually-verified) alt text on every service photo, image
  sitemap entries.
- **Performance**: `js/gl.js` scoped to the one page it actually affects,
  removing ~44KB of dead script execution from every other page; a
  performance budget file calibrated against measured reality rather than an
  abstract target.
- **Search tooling**: IndexNow submission tool and key-serving endpoint,
  Search Console/Bing verification tag support — all inert until the site
  owner supplies real credentials, by design.
- **Content**: one new, fully-sourced pillar article, published through the
  real production content pipeline (not the preview-only fallback) —
  including a fix to that pipeline so future articles reliably get
  categorised, which is what makes the internal-linking work above actually
  function going forward.
- **Regression protection**: a new `inc/tools/seo-audit.mjs`, mirroring the
  existing `audit.mjs`'s zero-dependency, `check()`/exit-code contract,
  covering titles, descriptions, canonicals, heading structure, JSON-LD
  integrity, image attributes, broken links, redirect chains, and orphan
  pages.

## Validated

- `php -l` on every changed file.
- Every route smoke-tested for status code and redirect target, including
  every trailing-slash variant and the new location page.
- `inc/tools/audit.mjs` (pre-existing suite) — passed before and after every
  round of changes; zero regressions on the final run.
- `inc/tools/seo-audit.mjs` (new suite) — 19/19 sitemap URLs pass every
  check on the final run, including one real defect it caught
  (`pricing.php`'s heading skip) that manual review had missed.
- JSON-LD manually inspected across five representative pages, confirming
  the new fields resolve exactly as designed, `@id` references included.
- `inc/tools/seed-posts.php` run against the real local development
  database — the new article and its category linkage verified end-to-end,
  not just against the design-preview fallback.
- `sitemap.xml` validated as well-formed XML; every `<loc>` and
  `<image:loc>` confirmed reachable.

## Remaining

Explicitly blocked on data or decisions outside this engagement's reach —
detailed in `roadmap.md` and `analytics.md`:

- Google Search Console / Bing Webmaster Tools connection (needs the site
  owner's account)
- Real IndexNow key generation and activation (needs a production deploy
  decision)
- Populating real pricing (needs the business's actual numbers)
- Real case studies (needs client consent)
- A Google Analytics / GA4 decision (a business/privacy call, not made
  unilaterally)
- Content for `content-creation` and `marketing-advertisement` (needs a
  genuine angle, not filler, per this engagement's own content-quality
  standard)

## 30-day plan

1. Connect Google Search Console and Bing Webmaster Tools (paste the
   verification token; both slots are ready).
2. Generate and activate a real IndexNow key; submit the current sitemap
   once.
3. Deploy this engagement's changes to production; re-run
   `inc/tools/seo-audit.mjs --base https://rafly.in` against the live site
   as the first real-world check.
4. Populate real indicative pricing on `/pricing`.

## 90-day plan

1. Wire IndexNow submission into `admin/posts.php`'s publish action.
2. Publish the first real, consented case study; extend the internal-linking
   and structured-data work to reference it once it exists.
3. Make the Google Analytics / GA4 decision and implement if approved.
4. Write the `content-creation` and `marketing-advertisement` articles once
   a genuine angle for each is identified (not before).
5. First real Search Console data review against the Tier 1 query set in
   `keywords.md` — this is when priorities can start being set by actual
   impressions/clicks data rather than qualitative judgment.

## 6-month plan

1. Build the SEO health dashboard (Section 33 of the original brief) —
   deferred until there is real Search Console/GA data to populate it with.
2. Revisit `competitors.md` with a deeper technical crawl (schema, CWV,
   backlink profile) now that Rafly has comparable data of its own to set
   against it.
3. Evaluate whether accumulated real case studies and reviews justify
   `Review`/`AggregateRating` schema — only once a real, legitimate corpus
   exists.
4. Reassess the location-page strategy if the business ever opens a second
   real office — the `/locations/<slug>` pattern is built to extend, not
   duplicate into doorway pages.

## Success metrics

Tracked in full detail once Search Console exists (`analytics.md`):
indexed page count, impressions, clicks, CTR, average position (sitewide and
per the Tier 1 query set), Core Web Vitals field data, non-branded vs
branded query split, organic conversions (cross-referenced against the
site's existing lead-form attribution), and referring domains. No numeric
target is stated for any of these — this report does not promise a ranking
outcome, in keeping with the engagement's own operating constraint.
