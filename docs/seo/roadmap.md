# SEO Roadmap — rafly.in

Scored, per the brief's own formula, as
`(impact × confidence × opportunity) ÷ cost` — qualitatively, since no
volume/traffic data exists yet to make this quantitative (see `analytics.md`).

## P0 — critical (done this engagement)

All items in this tier were either indexation-breaking or actively
contradictory (a control fighting another control). All are implemented and
verified — see `audit.md`'s verification section.

- Trailing-slash duplicate URLs
- `robots.txt` disallowing pages that also `noindex` (the tag was never
  reachable)
- Overclaiming metadata ("AI automation" advertised as live)
- `partials/header.php`'s broken social-link key (unrelated bug, found and
  fixed during testing)
- `pricing.php`'s heading-level skip

## P1 — high impact (done this engagement)

- Local/geo layer: `areaServed`, `hasMap`, `/locations/greater-noida`, geo
  copy on About/Contact/every service page
- Entity graph: `makesOffer`, `SearchAction`, `schema_webpage()`, `@id`s on
  Service/Person nodes
- Internal linking engine (`inc/repo/links.php`) across articles, services,
  case studies — including the category-slug bug fix that would have made it
  silently do nothing on a clean production database
- Responsive images (`srcset`/width variants), real alt text on service
  photos, image sitemap entries
- `js/gl.js` scoped to the one page that actually uses it
- IndexNow + Search Console/Bing verification support (inert until
  configured)
- Automated SEO regression suite (`inc/tools/seo-audit.mjs`)
- One new pillar article, sourced entirely from real service data

## P1 — high impact, NOT done, blocked on external prerequisites

| Item | Blocked on |
|---|---|
| Connect Google Search Console | Requires the site owner's Google account and DNS/file-upload or HTML-tag verification decision — `GOOGLE_SITE_VERIFICATION` is ready to receive the token the moment it exists. |
| Connect Bing Webmaster Tools | Same, `BING_SITE_VERIFICATION` ready. |
| Activate IndexNow for real | Requires generating and committing a real key to `inc/config.local.php` (git-ignored — a deliberate choice by the site owner, not something to commit on their behalf) and deploying it to production. |
| Wire IndexNow into the publish flow | `admin/posts.php` should call `inc/tools/indexnow.php` on publish/update. Deferred because it is an admin-panel behaviour change, not a pure SEO change, and should be reviewed alongside the key activation above. |
| Populate real pricing on `/pricing` | `bundles_all()`/`price_text` already supports it — this is an admin-panel data-entry decision (real numbers), not a code change. Flagged in `competitors.md` as the single clearest differentiation opportunity found: zero of the three researched competitors publish any pricing signal. |
| Publish real case studies | Genuinely blocked on client consent and real, attributable metrics — the site's own `is_placeholder` architecture exists specifically so this is never worked around with invented ones. |

## P2 — growth

- **Topical coverage gap**: `content-creation` and `marketing-advertisement`
  have no supporting article (see `content-strategy.md`). Needs a genuine,
  specific angle for each — not filler — before writing.
- **A real Google Analytics / GA4 decision** — a business call for the site
  owner, not implemented unilaterally (see `analytics.md`).
- **Deeper competitor technical audit**: this engagement's `competitors.md`
  pass covered service offering, pricing transparency, and proof claims for
  three real local competitors, but did not crawl their full technical SEO
  (schema depth, Core Web Vitals, backlink profile) — worth a dedicated pass
  once GSC/GA exist to measure Rafly's own position against.
- **A dashboard** (Section 33 of the brief) — deliberately deferred until
  there is real Search Console/GA data to show; building one against no data
  would mean either an empty shell or fabricated numbers.
- **`/case-studies`, once real content exists**: extend
  `related_case_studies_for_service()`'s tag-matching to structured data
  (`entity-model.md`'s deferred "Case study → Service used" relationship).

## P3 — optimization

- Uppercase/lowercase URL-path enforcement — theoretical risk only (no
  internal link produces one), see `architecture.md`.
- AVIF image variants — `.htaccess` already declares the MIME type; GD on
  this environment supports `imageavif()`; not generated this engagement in
  favour of the WebP width-variant work, which already delivers the bulk of
  the byte savings (WebP twins were already ~40-90% smaller than the
  original JPEGs before this engagement even began).
- Re-compress the handful of oversized master JPEGs
  (`service-content.jpg`, `service-ecom.jpg`, `work-3.jpg`) — lower priority
  now that their WebP twins (the format actually served to nearly every
  visitor) are already well-optimized; the JPEG is only served as a
  fallback for browsers without WebP support.
- The visible `rawpixel` watermark on `assets/photos/service-ecom.jpg` — a
  real, cosmetic finding from actually viewing the image during this
  engagement's alt-text work. Legally clear (CC0, per `assets/CREDITS.md`)
  but worth a replacement photo when convenient; not urgent enough to block
  this engagement on.

## Explicitly not on this roadmap

Per the plan this engagement executed against: multi-city location pages,
fabricated proof of any kind, `Review`/`AggregateRating` schema without a
real review corpus, programmatic page generation, removal of the 3D/motion
layer, and any promise about ranking outcomes. These are not deferred — they
are deliberately excluded, and the reasoning for each is in `audit.md` and
`entity-model.md`.
