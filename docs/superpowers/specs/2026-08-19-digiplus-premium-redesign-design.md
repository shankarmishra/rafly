# Digiplus-premium redesign — design

**Date:** 2026-08-19
**Status:** Approved by Naveen, ready for implementation planning.
**Builds on:** the light edition (`C:\Users\xshan\Desktop\rafly`, first commit `19ffbaa`), NOT the dark/paper
redesign in `dev.rafly` (that direction was rejected — see `[[paper-edition-direction]]` in project memory).

## Why

Naveen wants the site to look and move like the premium end of agency/SaaS web design — specifically
citing `https://digiplus.co.in/` as the quality bar, then asking to go further and match the best-in-class
tier of animated, light-themed, 3D-touched sites, not stop at digiplus. He wants the "exploring" effect
(scroll-driven reveal/motion) applied to every section of every page, not just the hero, plus a premium
texture treatment inside display text.

## Reference research

Screenshotted with Playwright (`chromium`) at 1440×900, multiple scroll depths, since neither `WebFetch`
nor the Chrome extension were available to inspect these live:

- **digiplus.co.in** (primary reference) — white ground, animated particle/network mesh drifting behind
  the hero headline, bold two-line headline in a purple→pink gradient fill, gradient-color keywords inside
  body headings, soft-shadow white cards, big gradient numeral stats, dashboard-mockup illustrations
  alternating left/right down the page.
- **stripe.com** — flowing gradient-ribbon mesh graphic behind the hero, and critically: hero paragraph
  text itself is gradient-masked (`background-clip: text` fading from dark to light) — this is the literal
  technique for "texture inside text."
- **cuberto.com** — minimal white hero with huge bold clean type, immediately followed by a full-bleed
  photographic 3D device-render (laptop + phone in a dark studio scene), case-study grid presented as 3D
  device-mockup frames.
- **apple.com/airpods-pro** — pure product-photography 3D hero with confident oversized type, minimal
  chrome, classic scroll-driven product storytelling.
- **basement.studio / lusion.co** (seen, deliberately NOT followed) — full dark WebGL scenes, wrong
  register for an IT-services company and heavier than this project's budget allows.
- **instrument.com** (seen, deliberately NOT followed) — exploded scattered-card playfulness, right for a
  creative agency, wrong for Rafly's positioning.

## Design

### 1. Color

Keep Rafly's existing orange identity (`--orange` family in `00-tokens.css`, already accessibility-measured
per-token). Do **not** switch to digiplus's purple — that would abandon the brand. Instead, treat orange the
way digiplus/Stripe treat their accent: as a **gradient** (orange → amber → rose) applied to headline
keywords, stat numerals, buttons, and the hero animation, rather than a flat swatch.

### 2. Typography — "texture inside text"

`background-clip: text` gradient fills on:
- H1/H2 headline keywords (digiplus's trick: one or two words in a heading carry the gradient, the rest
  stay solid ink color)
- Pull-quotes and big stat numerals
- **Never body copy** — a textured fill on paragraph text hurts legibility and fails contrast checks; this
  is a deliberate exclusion, not an oversight.

A light animated noise/grain texture may be layered under the gradient (very low opacity, decorative only)
for large numerals/hero words if it reads as premium rather than dirty — validate visually before keeping.

### 3. Motion — three systems

1. **Hero background**: an animated line/particle network canvas behind the headline (digiplus's signature),
   hand-rolled canvas 2D (or lightweight WebGL if it composes cleanly with system 3 below) — no new
   dependency for this piece specifically, consistent with the project's existing "zero dependencies"
   engines (`smooth.js`, `gl.js`, `pixel.js`).
2. **Site-wide scroll-reveal**: the light edition already ships a reveal vocabulary
   (`data-r="rise|lift|left|right|scale|blur|wipe|fade|group|lines|parallax"`, native
   `animation-timeline: view()` with an IntersectionObserver fallback). "Exploring effect top to bottom"
   means wiring this onto every section of every page, staggered, not just the hero and a few elements as
   today. No second reveal system — extend the existing one.
3. **Pinned "hero moments"**: 1–2 per page (homepage hero, and likely "how we work"/process section) where
   a 3D object visibly transforms as the section is scrolled through, Apple/Cuberto-style. Used sparingly —
   most sections get system 2 only, not a pinned moment, so the pinned moments keep their impact.

### 4. Real 3D objects

Naveen approved pulling in **three.js** (~150 KB raw), self-hosted in `vendor/three/three.module.js` so the
`script-src 'self'` CSP needs no change — same pattern already used for self-hosted fonts. This unlocks real
rotating 3D product/device renders for the hero and pinned moments, rather than CSS-3D approximations.
Watch total JS weight; the project's stated budget was ~34 KB gzipped before three.js, so three.js should be
lazy-loaded only on pages/sections that actually use it, not shipped globally.

### 5. Rollout order

Build and verify on the **homepage only** first. Screenshot it for real (Playwright, same tooling used for
this research, or the project's existing `shoot.mjs`-equivalent verification script) and get Naveen's eyes
on the actual running page before touching any interior page. Given the project's history — three prior
full redesigns rejected on sight, twice with "design bahut kharab, pura bhool" — do not extend this system
to the other 14 routes until the homepage is explicitly approved.

### Testing / verification

Reuse the project's existing dependency-free CDP verification pattern (`inc/tools/shoot.mjs` /
`inc/tools/audit.mjs` if present in this repo, or the equivalent Playwright pattern used for this design's
own reference research) — all routes touched must be screenshotted and checked for overflow, contrast, and
reduced-motion behavior before being called done. No visual claim gets reported without a fresh screenshot
backing it.

## Out of scope for this spec

- Interior pages (about, team, services ×5, pricing, case-studies, blog, contact, privacy) — follow-on work
  once the homepage direction is approved.
- The ~20 clean photographs and the no-JS invisible-element bug already tracked in project memory
  (`[[rafly-work-in-progress]]`) — unrelated to this redesign, not addressed here.
