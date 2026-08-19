# Keyword Strategy & Intent Map — rafly.in

No paid keyword-research tool (Ahrefs/Semrush/GSC history) was available for
this engagement, so **no search-volume figures are presented anywhere below**
— inventing them would be exactly the kind of confirmed-vs-speculation
confusion Section 61 of the brief warns against. What follows is intent
classification and URL mapping, which can be verified by reading the query
and the page; volume prioritization is explicitly listed as a P0 gap in
`roadmap.md` (real GSC/Search Console data is a prerequisite for it).

## Tier 1 — highest business value, already has a target page

| Query pattern | Intent | Target URL | Status |
|---|---|---|---|
| web development company Greater Noida / Noida / Delhi NCR | Commercial, local | `/web-development`, `/locations/greater-noida` | Live |
| digital marketing agency Greater Noida | Commercial, local | `/marketing-advertisement`, `/locations/greater-noida` | Live |
| website security audit / review India | Commercial | `/web-security` | Live |
| bundled web development package | Commercial | `/pricing`, `/#pricing` | Live |
| rafly / rafly digital growth | Navigational, branded | `/` | Live |
| how to choose a web development company Delhi NCR | Commercial investigation | `/blog/choosing-a-web-development-partner-delhi-ncr` | Live (new, this engagement) |
| bundled packages vs freelancers | Commercial investigation | `/blog/bundled-packages-vs-freelancers` | Live |
| small business website security basics | Informational | `/blog/small-business-website-security-basics` | Live |
| e-commerce listing optimization before ads | Informational | `/blog/clean-product-listings-before-ads` | Live |

## Tier 2 — real page exists, intent match is partial

| Query pattern | Intent | Target URL | Gap |
|---|---|---|---|
| content creation agency Delhi NCR | Commercial | `/content-creation` | No supporting article — see `content-strategy.md`'s topic map. |
| e-commerce support / store operations agency | Commercial | `/ecommerce-support` | Covered indirectly by the listings article; no dedicated pillar. |
| web development company case studies | Commercial investigation | `/case-studies` | Page exists but currently holds only placeholder-flagged content — the query has nowhere real to land yet. |
| AI automation for small business India | Informational | `/blog/ai-automation-small-business-workflows` | Article exists and is honest about the service NOT being offered yet — correct behaviour, but means this query cannot convert today, only inform. |

## Tier 3 — long-tail, no dedicated page (by design — see "Explicitly NOT doing")

| Query pattern | Intent | Notes |
|---|---|---|
| web development company Noida sector [X] | Local, narrow | Would require a second location page with no real second office behind it — the doorway-page pattern this engagement deliberately avoided. Covered instead by `/locations/greater-noida`'s honest "we travel across Delhi NCR" framing. |
| [competitor name] alternative | Comparison | No comparison page built — a fair, factual comparison against a NAMED competitor risks reading as disparagement without their side of the story, and none of the researched competitors (`competitors.md`) publish enough verifiable detail to compare against fairly. |
| freelance web developer vs agency | Informational | Substantially covered by the existing bundled-vs-freelancer article; a second page would cannibalize it. |

## Keyword → URL cannibalization check

Performed by reading every page's `<title>`/`<h1>`/meta description
(`seo-audit.mjs`'s own per-page pass) and cross-checking against this map.

- **No cannibalization found.** Each of the five service pages, the pricing
  page, and every article target a distinct primary phrase; blog category
  filter views are `noindex`+canonicalised to `/blog` specifically so a
  filtered listing view can never compete with the unfiltered one or an
  individual article for the same query.
- **One near-miss avoided by design**: the new pillar article
  ("choosing a web development partner") and `/web-development` both touch
  process/scope/ownership. The article targets *commercial-investigation*
  intent (comparing options before choosing), `/web-development` targets
  *commercial* intent (the offer itself) — different SERP shapes, and the
  article explicitly links down to the service page rather than restating
  its content.

## Intent classification reference

| Intent | Definition used | Example |
|---|---|---|
| Navigational | Looking for this specific site | "rafly digital growth" |
| Informational | Looking to learn, not yet buying | "website security basics" |
| Commercial investigation | Comparing options before choosing | "how to choose a web development partner" |
| Commercial | Ready to evaluate a specific offer | "web development company Greater Noida" |
| Transactional | Ready to act now | "get a quote website Greater Noida" — served by every service page's CTA and `/contact`, not a distinct landing page (the CTA density on commercial pages already covers this; a separate transactional page would just be a thinner copy of the service page). |
| Local | Geography is the qualifying term | "…in Greater Noida", "…Delhi NCR" |
| Branded | Company name present | "rafly" |

## Priority framework applied

Per the brief's own formula (`impact × confidence × opportunity ÷ cost`), the
work in this engagement was sequenced:

1. **P0 fixes** (trailing slashes, robots contradiction, the header.php bug) —
   near-zero cost, directly protects existing indexation. Done first.
2. **Geo + entity graph** — moderate cost, high confidence (schema.org
   `areaServed`/`SearchAction`/`makesOffer` are documented, stable APIs), high
   impact for local commercial queries. Done second.
3. **Internal linking** — moderate cost, high confidence, compounds with
   everything else (an article that links to a service page makes that
   service page's ranking work harder). Done third.
4. **One new pillar article** — highest cost per unit (real writing, fact-
   checked against the codebase's own service data), reserved for the single
   highest-value gap (`choosing a partner`, local + commercial-investigation)
   rather than spread across several thinner pieces.

Full P0–P3 roadmap, including what still depends on external data, is in
`roadmap.md`.
