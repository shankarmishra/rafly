# Information Architecture & Indexation Policy — rafly.in

## URL architecture

Flat, single-segment clean URLs for every content page
(`/about`, `/pricing`, `/web-development`), with one deliberate exception:
`/locations/greater-noida` — a nested path chosen because it is real IA that
should extend the same way (`/locations/<slug>`) if a second real office is
ever opened, not a naming accident. Every clean URL is backed by a real `.php`
file, rewritten invisibly (`.htaccess` / `router.php`, kept in sync by hand,
documented as such at the top of `router.php`); every legacy `.php` URL 301s
to its clean form so bookmarks and existing backlinks transfer rather than
break. Trailing slashes now 301 to the slash-less canonical form (this
engagement's P0 fix).

## Indexation policy

Every route, explicit. `INDEX` = crawlable and indexed. `NOINDEX` = crawlable,
carries a `noindex` directive. `CANONICALIZED` = crawlable, but declares a
different URL as canonical. `REDIRECT` = 301s, never itself indexed.
`DISALLOWED` = blocked in `robots.txt`. `NOT PUBLIC` = requires
authentication or is a POST-only handler with no public GET.

| Route | Policy | Why |
|---|---|---|
| `/` | INDEX | Homepage. |
| `/about` | INDEX | |
| `/team` | INDEX | |
| `/pricing` | INDEX | |
| `/case-studies` | INDEX | |
| `/contact` | INDEX | |
| `/privacy` | INDEX | Legal page — indexed for transparency; low priority (0.3) in the sitemap. |
| `/blog` | INDEX | Unfiltered listing only. |
| `/blog?category=…`, `/blog?q=…` | CANONICALIZED → `/blog` + NOINDEX | Thin, near-duplicate filtered views of the same collection. Enforced in `blog.php`. |
| `/blog/{slug}` | INDEX | One entry per published article; `published_at`/`status` gate visibility, checked at request time so a future-dated draft cannot leak. |
| `/web-development`, `/web-security`, `/marketing-advertisement`, `/content-creation`, `/ecommerce-support` | INDEX | Each declares its own canonical explicitly (`service.php`) — without it, all five would self-canonicalise to whichever the crawler saw first. |
| `/locations/greater-noida` | INDEX | New this engagement. |
| `/thank-you` | NOINDEX | Session-gated one-shot confirmation; not `Disallow`'d (fixed this engagement — see `audit.md` finding #2), so the `noindex` tag is actually reachable and honoured. |
| `/submit` | NOT PUBLIC (POST only) | No renderable GET response; not `Disallow`'d for the same reason as `/thank-you`. |
| `/sitemap.xml` | Machine-readable, not a page | Served by `sitemap.php`, listing exactly the `INDEX` rows above. |
| `/robots.txt` | Machine-readable | |
| `/{indexnow-key}.txt` | NOT PUBLIC | 404s unconditionally until `INDEXNOW_KEY` is configured (`inc/config.local.php`) — see `analytics.md`. |
| `/admin/*`, `/admin-gate/*` | DISALLOWED + NOINDEX + login-gated | Three independent controls, none alone sufficient: `robots.txt` keeps well-behaved crawlers out, `X-Robots-Tag: noindex` (`inc/security.php`) covers anything that reaches it anyway, and the login gate is the actual security boundary — `robots.txt` is explicitly documented in-repo as a request, not a control. |
| `/inc/*`, `/partials/*`, `/private/*` | DISALLOWED, and 404 via `.htaccess`/`router.php` regardless | Application internals; never renderable pages. |
| `/vendor/*` | Allowed, not disallowed | Deliberately NOT blocked — holds self-hosted webfonts and the icon sprite every page renders from; blocking it is a rendering hazard, not a privacy gain. Existing, correct decision, left untouched. |

## Duplicate-URL risks, checked

| Risk | Status |
|---|---|
| Trailing slash (`/about` vs `/about/`) | **Fixed this engagement.** 301 in `.htaccess`, mirrored in `router.php`, and the canonical fallback in `head.php` normalises independently as a second line of defence. |
| `http` vs `https` | Already handled — unconditional 301 in `.htaccess`, pre-existing. |
| `www` vs bare domain | Already handled — bare domain is canonical, `www.` 301s, pre-existing. |
| Uppercase/lowercase path variants | Not enforced at the rewrite layer (Apache path matching is case-sensitive by default on Linux hosting, which Hostinger — the target host per `.htaccess`'s own header comment — runs). No internal link anywhere in the codebase produces an uppercase path, so this is a theoretical external-link risk only; flagged in `roadmap.md` P3. |
| Query-parameter duplicates | `service.php?service=…` and `blog-post.php?post=…` — legacy direct-file access — both 301 to their clean equivalent for a recognised value, and the clean route is what canonical/sitemap ever emit. Pre-existing, verified still correct. |
| `/blog` filter/search views | Explicitly `noindex` + canonicalised — see indexation table above. |

## Orphan-page check

Verified programmatically (`inc/tools/seo-audit.mjs`, section 5): every URL in
`sitemap.xml` is reachable via internal links from `/`, breadth-first. 19/19
pass on the current build. The check itself is now part of the permanent
regression suite — a future page added to the sitemap without a real internal
link pointing at it will fail CI-equivalent verification rather than silently
sitting undiscovered.

## Pagination & faceted navigation

`/blog` has one facet (`category`) and one free-text search (`q`), both
handled by the CANONICALIZED+NOINDEX rule above rather than a `rel=prev/next`
scheme — there is currently no true pagination (all published posts render on
one listing page). If the article count grows enough to need real pagination,
the existing `noindex`-the-filtered-view pattern extends cleanly: paginated
pages 2+ should canonical to the base `/blog` listing exactly as filtered
views already do, not emit `rel=next`/`rel=prev` (deprecated by Google,
per current Search Central guidance) or self-canonicalise per page.

## Reusable technical abstractions (Section 47 of the brief)

| Concept | Implementation |
|---|---|
| `PAGE_SEO` | `$page` array convention (`partials/head.php`) — every page sets `title`/`desc`/`canonical`/`schema`/`noindex` through one shape. |
| `STRUCTURED_DATA` | `inc/schema.php` — one generator function per node type, one `schema_render()` serializer. |
| `CANONICAL_MANAGER` | `$page['canonical']` + the normalised fallback in `head.php`. |
| `SITEMAP_GENERATOR` | `inc/sitemap.php`'s `sitemap_urls()`, rendered as XML by `sitemap.php` and read directly by `inc/tools/indexnow.php` — one source, two consumers, added this engagement. |
| `ROBOTS_GENERATOR` | Static `robots.txt` — deliberately not generated (a static file is simpler and sufficient for a site this size; generating it would be complexity with no present benefit). |
| `BREADCRUMB_SCHEMA` | `breadcrumbs()` (visible) and `schema_breadcrumbs()` (JSON-LD) in `inc/helpers.php`/`inc/schema.php`, built from the same `{name,url}` array so the two can never disagree. |
| `INTERNAL_LINK_ENGINE` | `inc/repo/links.php`, added this engagement. |
| `SEO_AUDITOR` / `SEO_TESTS` | `inc/tools/seo-audit.mjs`, added this engagement, alongside the pre-existing `inc/tools/audit.mjs`. |
| `SEARCH_ANALYTICS` | Not yet built — see `analytics.md`; requires real GSC/GA credentials this engagement does not have. |
