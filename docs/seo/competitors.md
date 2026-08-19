# Competitive Analysis — Delhi NCR web/digital agencies

Researched live (WebSearch + WebFetch) on 2026-08-18 against three real,
currently-trading competitors serving the same Greater Noida / Delhi NCR
market Rafly targets. This is not an exhaustive market map — it is enough
evidence to test whether Rafly's actual differentiators (bundled scope,
published boundaries, no fabricated proof) are real advantages or assumed
ones.

## Companies researched

| Company | Location claim | Services | Source |
|---|---|---|---|
| **Star Web Maker** | Noida/Greater Noida, 15+ years | Web design/dev, e-commerce, WordPress, mobile apps, digital marketing/SEO/PPC — sold both à la carte and bundled | [starwebmaker.com](https://www.starwebmaker.com/) |
| **Off & Digi Marketing Solutions** | Markets itself for Greater Noida; registered office is actually in Indirapuram, Ghaziabad | SEO, PPC, social, web/app dev, content, marketplace listings, CRM/automation | [offanddigi.com](https://offanddigi.com/digital-marketing-agency-greater-noida/) |
| **SkyApex Digital Agency LLP** | Gaur City, **Greater Noida West** — the same immediate locality as Rafly's own registered address, per public directory listings | Web dev (incl. e-commerce), SEO, performance marketing, content, graphic design, lead gen | [skyapex.in](https://skyapex.in/), [Justdial listing](https://www.justdial.com/Greater-Noida/Skyapex-Digital-Agency-LLP-Near-Chaar-Murti-Gaur-City-1/011PXX11-XX11-240528182009-N9M7_BZDET) |

SkyApex is the closest true local peer found — same micro-locality, founded
2022, ISO-certified, 4.7★ / 161 reviews on public directories.

## Evidence table

| Signal | Star Web Maker | Off & Digi | SkyApex | Rafly (today) | Gap / opportunity |
|---|---|---|---|---|---|
| **Published pricing** | None — "request a quote" | None — "cost depends on your goals" | None — "7-day free trial", pay-on-results framing | Indicative ranges on `/pricing` when an admin fills them in; currently unpopulated | **CONFIRMED opportunity.** Zero researched competitors publish any pricing signal. Populating real indicative ranges is a genuine, low-effort differentiator nobody else in this set offers. |
| **Published service boundaries ("what we don't do")** | Not found | Not found | Not found | Real `boundaries` array on every service page (native apps, formal pen-testing, compliance sign-off, etc.) | **CONFIRMED differentiator, already built.** None of the three researched competitors state a limit anywhere in reviewed content. A specific, verifiable limit is exactly the kind of claim AI answer engines cite — this gets amplified in Section C's work, not hidden. |
| **Named case studies with real metrics** | "6,438+ projects", generic client logos, no named studies | "1000+ clients", "3x growth" claims, no identifiable businesses | "200+ projects", "1000+ happy clients", aggregate only | Case-studies page exists but currently holds only `is_placeholder`-flagged sample content, visibly badged as such | **NOT yet a realized advantage.** All four sites (including Rafly) currently show no verifiable, named results. Rafly's architecture is the only one that visibly distinguishes real from placeholder — but the advantage only activates once real, consented case studies are published. Flagged in the roadmap. |
| **Explicit security service line** | Not a named service | Not a named service | Not a named service | Dedicated `/web-security` service page, own FAQ, own boundaries | **CONFIRMED gap in the competitor set.** None of the three name security as a distinct offering — Rafly is the only one of the four with a dedicated page for it. |
| **Bundled 5-service scope (web + security + marketing + content + e-commerce, one team)** | Bundles design+dev; marketing separate | Broad list but à la carte, no stated bundle | Markets "Done-For-You" packages but scoped to marketing/e-commerce, not security | The entire premise of `BUNDLES` — explicitly one team, one scope | Directionally similar to SkyApex's packaging language, but Rafly is the only one bundling security into the core offer rather than treating it as absent. |
| **Structured data / schema depth** | Not audited (out of scope for this pass) | Not audited | Title/meta not retrievable via fetch — likely thin | Organization+WebSite sitewide, Service/FAQ/Breadcrumb per page, cross-referenced `@id`s, `SearchAction`, `areaServed` at three geo levels (this engagement) | Directional only — a full technical crawl of competitor markup was out of scope for this pass. Flagged as a P2 follow-up in the roadmap if deeper SERP-feature competition is worth tracking. |
| **Local geo specificity** | Region-level ("Noida, Ghaziabad, Gurugram") | Address is Ghaziabad; markets Greater Noida | Genuinely local — Gaur City, Greater Noida West | Now: `/locations/greater-noida`, `areaServed` at Greater Noida / Delhi NCR / India, geo copy on About/Contact/every service | SkyApex is the one competitor with a real local-precision advantage Rafly did not have until this engagement. Parity now exists on the technical/content side; SkyApex's directory review volume (161 reviews) remains a real, unmatched trust signal — see roadmap. |

## What NOT to copy

- **Aggregate vanity numbers** ("1000+ clients", "6,438+ projects") with no
  verifiable source. This is exactly the pattern the codebase's
  `is_placeholder` architecture was built to avoid, and copying it would
  undo that.
- **"Pay only on results" framing** (SkyApex) without a defined, auditable
  measurement — this reads well but is a claim the site would have to
  actually operationalize and defend; not adopted here.
- **Directory-listing volume as the primary trust play.** SkyApex's 161
  reviews on Justdial are real and valuable, but building for a review
  platform Rafly doesn't control is a distinct initiative (see roadmap,
  90-day plan) — not a technical SEO change.

## Net assessment

Rafly's actual structural advantages over this competitor set — bundled
security, published limits, (once populated) transparent pricing — were
already present in the codebase's content model before this engagement; they
were simply not reinforced by the technical layer (geo signal, entity graph,
internal linking) that helps search engines and AI answer systems surface
them. That reinforcement is what this engagement built. The one advantage a
competitor (SkyApex) has that Rafly does not is accumulated third-party
review volume — a genuine, time-dependent asset no on-page change can
manufacture, and it's called out plainly in the roadmap rather than glossed
over.
