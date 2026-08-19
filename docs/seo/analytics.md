# Search Analytics & Bing/IndexNow Strategy — rafly.in

## What exists today

| System | Status |
|---|---|
| Google Analytics / GA4 | Not found anywhere in the codebase. |
| Google Search Console | Not connected — no verification tag present before this engagement. |
| Bing Webmaster Tools | Not connected. |
| Meta Pixel | **Present and working** — admin-editable (`analytics.meta_pixel_id` setting), constant fallback, placed early in `<head>` per Meta's own placement guidance, suppressed on `noindex` pages. Pre-existing, correct, left untouched. |
| IndexNow | Built this engagement, inert until configured. |

Search performance data (impressions, clicks, CTR, average position, indexed
vs excluded pages, Core Web Vitals field data) **requires Google Search
Console history that does not exist yet** — this is the single biggest
"remaining, blocked on external data" item in the whole engagement, called
out explicitly rather than worked around with an estimate.

## What was built this engagement

### Verification tags (inert until configured)

`inc/config.php`:
```php
GOOGLE_SITE_VERIFICATION   // default ''
BING_SITE_VERIFICATION     // default ''
```
`partials/head.php` emits the corresponding `<meta>` tag only when non-empty.
**To activate:** get the verification string from Search Console / Bing
Webmaster Tools ("HTML tag" method, not the DNS or file-upload method — those
need no code change), paste it into `inc/config.local.php`
(git-ignored, same override point `META_PIXEL_ID` already uses), deploy.

### IndexNow

`INDEXNOW_KEY` (default `''`) in `inc/config.php`. Until set:

- `indexnow-key.php` 404s every request — there is no key to prove ownership
  of, so nothing claims to be that proof.
- `inc/tools/indexnow.php` refuses to run at all (exit 1, clear message).

**To activate:**

1. Generate a key (e.g. `php -r "echo bin2hex(random_bytes(16));"`).
2. Set `INDEXNOW_KEY` in `inc/config.local.php`.
3. Confirm `https://rafly.in/<key>.txt` serves the key as plain text.
4. Run `php inc/tools/indexnow.php --dry-run` to see exactly what would be
   submitted, then without the flag to actually submit.
5. Wire it into the publish flow — call it from `admin/posts.php` after a
   post is published/updated, so new articles get submitted automatically
   rather than waiting for a manual run. **Not wired in automatically by
   this engagement** — the tool exists and is verified working (dry-run
   tested against the real sitemap), but hooking it into the admin's publish
   action is an admin-panel change outside this engagement's SEO scope, and
   is listed in the roadmap.

The tool only ever submits URLs present in `inc/sitemap.php`'s
`sitemap_urls()` — the exact same list `sitemap.xml` renders — so it cannot
submit a `noindex`, redirected, or non-canonical URL by construction, and it
rate-limits itself (won't resubmit the same URL within 24h without `--force`).

### Core Web Vitals

No field data (real user CWV) exists without Search Console/CrUX history.
Lab data was captured this engagement via `inc/tools/audit.mjs`'s WEIGHT
section (uncompressed per-route byte counts, dev server) and the new
`inc/tools/perf-budget.json` codifies those as a regression budget — see
`inc/tools/perf-budget.json`'s own `_methodology` note for why the budget
reflects the actual measured baseline rather than an abstract target. This is
a **lab proxy**, not field CWV; a genuine LCP/INP/CLS reading needs either
real Search Console CrUX data (needs traffic + time) or a one-off Lighthouse/
PageSpeed Insights run against the live production domain (not the local dev
server this engagement had access to).

## Metrics to track once Search Console is connected

Per the brief's own "success metrics" list — restated here as the tracking
plan, not fabricated numbers:

- Indexed page count (compare against the 19 URLs `sitemap.xml` currently
  declares — a large gap either direction is itself a finding).
- Impressions, clicks, CTR, average position — sitewide and per the Tier 1
  query set in `keywords.md`.
- Excluded-page reasons (Search Console's own report) — the fastest way to
  catch a future accidental `noindex` or crawl block before it costs
  ranking, and exactly what `seo-audit.mjs`'s regression suite is meant to
  prevent between now and then.
- Core Web Vitals field data, per the three route classes
  `perf-budget.json` defines (home / blog / default).
- Non-branded vs branded query split — a rough proxy for whether the geo/
  entity work in this engagement is reaching new searchers rather than only
  people who already know the brand name.
- Organic conversions — the site's own lead form already tags
  `source_page`/`service_slug` on every submission (`lead_context_fields()`,
  pre-existing); cross-referencing that against Search Console landing pages
  is possible once both exist, without adding new tracking.

## What this engagement deliberately did not add

- **Google Analytics / GA4** — adding a new, previously-absent analytics
  platform is a meaningful business/privacy decision (a new script,
  new data processor, a privacy-policy update) outside an SEO engagement's
  remit to decide unilaterally. Flagged for the site owner, not implemented.
- **Any GSC/GA API integration for a "dashboard"** — Section 33 of the brief
  asks for an SEO health dashboard; building one against data sources that
  do not yet exist would mean either faking the data or shipping an empty
  shell. The `roadmap.md` P1 entry is to connect Search Console first, and
  revisit a dashboard once there is real data to show.
