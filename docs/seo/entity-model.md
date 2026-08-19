# Entity Model — rafly.in

The site as a graph, not a page collection. Every node below is real JSON-LD
emitted by `inc/schema.php` and verified (this engagement) to parse and
cross-reference correctly via `node -e` inspection and `seo-audit.mjs`'s
per-page `@id` resolution check.

## Canonical identity

| Field | Value | Source |
|---|---|---|
| Legal name | Rafly Digital Growth Private Limited | `schema_organization()` |
| Brand name | Rafly | `SITE_NAME` |
| Domain | rafly.in | `SITE_DOMAIN` |
| Logo | 512×512 square mark (not the horizontal lockup — Google rejects an Organization logo under 112px on either axis) | `schema_organization()` |
| Address | Real, admin-editable (`contact.address` setting), split into `PostalAddress` parts by `schema_postal_address()` | `inc/schema.php` |
| Region/state | Uttar Pradesh (`BUSINESS_GEO_STATE`, this engagement) | `inc/config.php` |
| Locality served | Greater Noida → Delhi NCR → India, ordered nearest-first (this engagement) | `schema_area_served()` |
| Phone / email | Single source (`RAW_PHONE`), derived into display/WhatsApp/E.164 forms — cannot drift into three different numbers the way the pre-existing codebase's own comments describe it once did | `inc/config.php` |
| Social profiles | LinkedIn, Instagram, Facebook, WhatsApp, YouTube — `sameAs` | `SOCIAL_LINKS` |

## The graph

```
Organization (Rafly Digital Growth Private Limited)
 ├─ address        → PostalAddress
 ├─ areaServed     → [City: Greater Noida, Place: Delhi NCR, Country: India]
 ├─ hasMap         → Google Maps search on the real address
 ├─ makesOffer     → 5× Offer → Service (@id ref, not repeated)
 ├─ sameAs         → social profiles
 └─ referenced by  → every WebPage/AboutPage/Service/BlogPosting node, sitewide

WebSite
 ├─ publisher      → Organization (@id ref)
 └─ potentialAction → SearchAction (target: /blog?q={search_term_string})

Service × 5 (@id = #service-<slug>, stable, referenced by Organization.makesOffer)
 ├─ provider       → Organization (@id ref)
 ├─ areaServed     → same three-level list as Organization
 ├─ hasOfferCatalog → named offerings (where present)
 └─ own page: FAQPage, BreadcrumbList

BlogPosting × 5
 ├─ publisher      → Organization (@id ref)
 ├─ author         → Person (full node, same @id team.php renders for that
 │                    person) — falls back to Organization when no
 │                    author_team_id is set
 └─ mainEntityOfPage → the article's own URL

Person × N (team members, @id = #person-<id>)
 ├─ worksFor       → Organization (@id ref)
 └─ sameAs         → GitHub/LinkedIn, when supplied

AboutPage / WebPage × several (About, Team, the new location page)
 ├─ isPartOf       → WebSite (@id ref)
 └─ about          → Organization (@id ref)
```

## What changed this engagement, and why each change closes a real gap

| Addition | Problem it fixes |
|---|---|
| `areaServed` on Organization + every Service | Was hardcoded to `Country: India` on Service, absent entirely on Organization. A search engine building a local knowledge panel had no locality signal beyond the bare postal address string. |
| `hasMap` on Organization | No structured link from the entity to a map, despite the footer already rendering one for humans — the machine-readable version was missing. Uses the real address, not fabricated coordinates (`schema.php`'s own comment explains why `GeoCoordinates` was deliberately not added: nobody has surveyed a real lat/long for the office, and inventing one is exactly the kind of fabricated structured data this file exists to avoid). |
| `makesOffer` on Organization, `@id`s on Service | The five services existed as five separate, disconnected nodes. Nothing told a crawler "this Organization offers these five things" as a structured fact — it had to infer it from prose. |
| `SearchAction` on WebSite | The site has a real, working `?q=` search (`blog.php`) with no markup declaring it — a documented, supported feature left undeclared for no reason. |
| `schema_webpage()` extraction | About and Team each hand-rolled an identical WebPage shape inline. Two copies of the same shape are two places a future edit can make them disagree — this is the same "one accountable line" argument the site's own marketing copy makes about bundled services, applied to its own codebase. |
| `@id`s on Person, referenced (not duplicated) by BlogPosting.author | An article's byline used to be a bare `{name: "..."}` string with no connection to the actual Person node `/team` renders for that same individual — two representations of one person, unlinked. |

## What was deliberately NOT added

- **`Review` / `AggregateRating`** — no legitimate review corpus exists on the
  site today. Adding this schema without real, consented reviews is exactly
  the "fake reviews / fake ratings" pattern Section 8 of the brief prohibits.
- **`GeoCoordinates`** — see `hasMap` above. A real lat/long can be added the
  moment someone actually looks it up against the real address; guessing one
  from the postal address is a fabrication risk not worth the marginal
  knowledge-panel benefit.
- **A second Organization-like node** (e.g. a separate `LocalBusiness`) — the
  existing Organization node already carries `ProfessionalService` as a
  secondary `@type`, which is a `LocalBusiness` subtype. A second node
  describing the same entity would be the "contradictory entities" pattern
  Section 8 explicitly warns against; the existing node was extended in place
  instead.

## Relationships not yet structured (flagged, not built)

- **Organization → Article** is implicit today (via `publisher`), not an
  explicit `ItemList` of "everything this org has published." `blog.php`'s
  unfiltered listing already carries a `CollectionPage`/`ItemList` node for
  this — verified still correct, not duplicated here.
- **Case study → Service used** exists as a *visible* internal link (this
  engagement's `case-studies.php` tag-chip linking, see `architecture.md`'s
  `INTERNAL_LINK_ENGINE` entry) but is not yet mirrored in structured data,
  because case studies are currently `is_placeholder`-flagged sample content
  — adding a structured claim about which real service a fictional case
  study used would be a fabrication risk. Revisit once real case studies are
  published (see `roadmap.md`).
