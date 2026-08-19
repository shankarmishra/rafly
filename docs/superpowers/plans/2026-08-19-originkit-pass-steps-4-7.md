# OriginKit pass, steps 4–7 — implementation plan

> Continues `.claude/plans/keen-wondering-locket.md`. Steps 0–3 are committed as
> `e9152b1`. This file plans the four that are left, at the level of exact
> values, so each can be built and verified without re-deciding anything.

**Goal:** finish the OriginKit pass — the deck collapses into a coverflow, the
phones take the Appit arrangement, the five services become the glass bento
cards Naveen has praised at every round, and no section below the fold is bare
paper.

**Architecture:** no new files and no new dependencies. `js/decks.js` gains one
interpolation stage and one geometry table; `css/09-scenes.css` gains the bento
block and the phone shell details; `index.php` gains markup. Every effect keeps
its designed still form so the page is complete with scripts off.

**Tech stack:** PHP 8.3, vanilla ES modules, CSS custom properties. No build.

## Global constraints

Copied from the standing rules; every task below inherits them.

- **Palette is ours.** OriginKit gives layout and motion only. `--accent` is
  fill, `--accent-fg` is text; nothing in these steps may introduce a colour
  that is not already a token in `css/00-tokens.css`.
- **`css/09-scenes.css` may never define a ground token.** Grounds live in
  `css/08-ground.css`.
- **Light theme everywhere.** No dark band below the hero except the existing
  `#work` chapter and the phone screens.
- **Complete with no JS.** Every new arrangement has a CSS still form. Nothing
  waits on a script to become visible.
- **Reduced motion resolves to the fan, not the coverflow** — five legible
  cards beats five overlapping ones when nothing can move.
- **Budget gates:** `home/phone` ≤ 600 KB total and ≤ 80 KB JS; css ≤ 200 KB on
  every route. Run `inc/tools/deadcss.mjs` before ever raising a line.
- **Commits authored `shankarmishra`**, pushed to `officialRafly/dev.rafly`,
  branch `rebuild/logo-blue`.
- **Verification, every task**, from `Desktop\rafly` with `MSYS_NO_PATHCONV=1`
  exported: `shoot.mjs --no-shots`, `audit.mjs`, `seo-audit.mjs`.

---

## File structure

| File | Responsibility after these steps |
|---|---|
| `js/decks.js` | Both decks. Slot tables (`FAN`, `COVER`, `PHONE_FAN`), one rAF, one interpolation: stack → fan → coverflow. |
| `js/sheen.js` | **New, ~40 lines.** Writes `--mx`/`--my` on a card under the pointer. Nothing else. Gated on fine pointer + motion. |
| `css/09-scenes.css` | Scene geometry: deck, coverflow dim, phones, head field, **and the new `.svc-bento`**. |
| `css/pages/home.css` | Page-specific type and the sections that are not scenes. Loses the dead `.assembly-*` block. |
| `index.php` | Markup and the data arrays that drive it. |

---

## Task 4 — Deck: fan → coverflow

**Files:** modify `js/decks.js`, `css/09-scenes.css:470-511` and `:745-750`.

**Interfaces produced:** `makeDeck(section, host, geo, order, cover)` — `cover`
optional; when absent the deck stops at the fan. Geometry entries grow two
optional keys, `y` and `rz`, which task 5 uses.

**The stages**, read from the section's own rect as now:

```
0.00 – 0.02   tight stack, visible          (k starts at 0.07, unchanged)
0.02 – 0.55   opens to FAN                  per-card stagger, unchanged
0.55 – 0.90   collapses to COVER            new
0.90 – 1.00   hold
```

**COVER**, from OriginKit's measured controls (gap 8, sideways tilt 8°, card
tilt 12°, inactive opacity 60%) re-derived for a 430px card:

```js
const COVER = [
    { x: 0,    y: 0,   z: 150,  ry: 0,   rz: 0,   s: 1.02, dim: 1    },
    { x: -232, y: 10,  z: -170, ry: 52,  rz: 3,   s: 0.90, dim: 0.60 },
    { x: 232,  y: 10,  z: -170, ry: -52, rz: -3,  s: 0.90, dim: 0.60 },
    { x: -392, y: 22,  z: -340, ry: 60,  rz: 5,   s: 0.82, dim: 0.60 },
    { x: 392,  y: 22,  z: -340, ry: -60, rz: -5,  s: 0.82, dim: 0.60 },
];
```

**The dim is a custom property, never an inline opacity.** JS writes
`--slot-dim`; `.mock { opacity: var(--slot-dim, 1) }` reads it, and the
reduced-motion branch pins `--slot-dim: 1`. An inline `style.opacity` could not
be overridden by a media query.

**Honesty note to write into the file:** `inc/tools/shoot.mjs` skips an element
whose *own* computed opacity is under 0.5 — an ancestor at 0.6 is not counted,
so the dim will not be measured. The deck is `aria-hidden` and every string in
it is chrome (`rafly.in/app/web`, the service tag), so nothing legible is being
faded; the comment says so rather than letting a later reader assume the meter
cleared it.

- [ ] **Step 1:** add `COVER`; extend `FAN`/`PHONE_FAN` entries with `y: 0, rz: 0`.
- [ ] **Step 2:** `makeDeck` takes `cover`; `layout()` returns `cover: cover?.[slot]`.
- [ ] **Step 3:** split `spread` into `openT` (`eased/0.55`) and `coverT`
      (`(eased-0.55)/0.35`), both `easeInOut(clamp01(...))`; interpolate every
      channel between the live fan value and `COVER`; kill the bob and the
      `rotateX` lean by `(1 - coverT)` so the deck locks flat when it lands.
- [ ] **Step 4:** write `--slot-dim` per card; add `opacity: var(--slot-dim, 1)`
      and `transition: none` to `.mock`; pin it to 1 under reduced motion.
- [ ] **Step 5:** `initDecks()` passes `COVER` for `[data-deck]` and nothing for
      `[data-phones]`.
- [ ] **Step 6:** verify — `shoot.mjs`, `audit.mjs` (the still fan check at
      `deckStill` must still see ≥2 distinct x positions), visual peek at 40%,
      70% and 95% of `#services`.
- [ ] **Step 7:** commit.

---

## Task 5 — Phones: the Appit arrangement

**Files:** modify `js/decks.js` (`PHONE_FAN`), `css/09-scenes.css:637-706`.

Three differences from what is there, and they are the whole task:

1. **Centre forward and lower, sides back and higher.** Ours sit on one
   baseline, which is why they read as a filmstrip rather than as a group.
2. **Sides carry `rotateZ` as well as `rotateY`** — a real tilt, not a turn.
3. **The shells get a side button and a thinner bezel.**

```js
const PHONE_FAN = [
    { x: 0,    y: 30,  z: 120,  ry: 0,   rz: 0,  s: 1.00 },
    { x: -214, y: -44, z: -190, ry: 22,  rz: 5,  s: 0.86 },
    { x: 214,  y: -44, z: -190, ry: -22, rz: -5, s: 0.86 },
];
```

`y` and `rz` scale with `k` like every other channel, so the stack still opens
from flat rather than snapping into the arrangement.

**The shell.** `overflow: hidden` moves from `.phone` to `.phone-screen` (with
its own 26px radius) so the buttons can sit on the frame edge instead of being
clipped by it. Border 8px → 6px. Two pseudo-elements: `::after` a 3px × 56px
power button on the right at 22% from the top, `::before` a 3px × 34px volume
pair on the left. Both in `--ink-chapter-3`, the colour the bezel already uses.

- [ ] **Step 1:** `PHONE_FAN` with `y`/`rz`; static CSS fan gains
      `--slot-y`/`--slot-rz` at the same 0.34 factor so the no-JS form matches.
- [ ] **Step 2:** shell details — overflow move, bezel, buttons.
- [ ] **Step 3:** verify at 320px, 390px, 960px and 1440px; the `.phones` box is
      560px tall and the raised sides must not clip.
- [ ] **Step 4:** commit.

---

## Task 6 — The service bento

**Files:** modify `index.php:472-528`, `css/09-scenes.css` (new block),
delete `css/pages/home.css:158-195` (`.assembly-*`), new `js/sheen.js`,
modify `js/home.js`.

This is the thing Naveen has asked for at every round — *"wo best wala cards
accha h"*. The five services stop being a list of rows and become the glass
cards.

**Layout — six tiles, three columns, no hole.** The current two-column block
leaves the panel stranded beside a list; the bento fills:

```
row 1   [ Web — spans 2 ]            [ Security ]
row 2   [ Marketing ]  [ Content ]   [ Commerce ]
row 3   [ the panel — spans 3, horizontal, facts in a row ]
```

Below 960px it collapses to one column and the panel's `dl` stacks.

**The card.** Glass over the paper ground, and every part of it already has a
token:

- ground `color-mix(in srgb, #fff 76%, transparent)`, `backdrop-filter: blur(14px)`
- **hover gradient border** — `::before` inset 0, `padding: 1px`,
  `background: linear-gradient(135deg, var(--sc), transparent 60%)`,
  `mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0)`,
  `mask-composite: exclude`, opacity 0 → 1 on hover/focus-within
- **cursor sheen** — `::after`, `radial-gradient(240px circle at var(--mx) var(--my), color-mix(in srgb, var(--sc) 18%, transparent), transparent 60%)`, opacity 0 → 1
- **scroll drift** — `data-r="lift"`, which already exists
- a service glyph tile in `--sc`, the index in mono, name at `--fs-h4`, blurb at
  `--fs-sm`/`--ink-3`, and a `link-arrow` that slides 4px on hover

`--sc` per card comes from the existing service tokens, mapped by slug:
`web-development → --svc-web`, `web-security → --svc-security`,
`marketing-advertisement → --svc-marketing`, `content-creation → --svc-content`,
`ecommerce-support → --svc-ecom`. **No new colour.**

**`js/sheen.js`** — the only new file, and it does exactly one thing:

```js
export function initSheen(sel = '[data-sheen]') {
    for (const el of document.querySelectorAll(sel)) {
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${e.clientX - r.left}px`);
            el.style.setProperty('--my', `${e.clientY - r.top}px`);
        });
    }
}
```

Gated in `js/home.js` on `finePointer()` and motion preference, imported
dynamically like everything else. With no JS `--mx`/`--my` fall back to `50%`
and the sheen is a centred glow on hover — still a designed state.

**Contrast.** `--svc-security` (#046070) and `--svc-ecom` (#0d6b34) are fill
tokens. They appear here only as a glyph tile background and a border gradient,
never as text. Card text stays `--ink` / `--ink-3` on white glass. Run
`contrast.mjs` before writing any value that ends up on type.

- [ ] **Step 1:** `$MODULES` gains a fifth column, the token name.
- [ ] **Step 2:** markup — `.svc-bento` with five `.svc-card` and the panel.
- [ ] **Step 3:** CSS block; delete `.assembly-*` from `css/pages/home.css`.
- [ ] **Step 4:** `js/sheen.js` + the gate in `js/home.js`.
- [ ] **Step 5:** verify — `shoot.mjs` (glass card text is the exact thing the
      `color-mix` parse fix was written for), keyboard focus ring on every card,
      `deadcss.mjs` for anything the deletion orphaned.
- [ ] **Step 6:** commit.

---

## Task 7 — Textures, reveals, and the marquee

**Files:** modify `index.php` §04–§09, `css/09-scenes.css` (marquee strip only —
`.marquee` itself already exists in `css/03-components.css:605` and
`initMarquees()` in `js/motion.js:172`, both unused to date).

| Section | Gets |
|---|---|
| §02 → §03 seam | **The marquee.** One strip of the five service names plus "Mobile apps", "One scope", "One invoice", separated by a diamond. Hover-paused, reversed on the second row. Already built; this is the first page that uses it. |
| §04 delivery | `.tex-hatch`, one orb bottom-left, `data-r="rise"` already on the steps |
| §05 stack | `.tex-grid .tex-mask-c` — it is the workspace section and the plotting grid is what that texture is for |
| §07 limits | orb top-right; `data-r="group"` on `.limits`, `data-r="lift"` on `.compare-wrap` |
| §08 pricing | `.tex-hatch` + orb behind the FAQ |
| §09 close | `.tex-grid` + one orb behind the form |

Every decorated section needs `overflow: hidden; isolation: isolate` or the orbs
bleed into the section above. That is one shared rule, `.has-tex`, not six.

- [ ] **Step 1:** `.has-tex` rule + the marquee strip markup and its wrapper CSS.
- [ ] **Step 2:** texture layers on §04, §05, §07, §08, §09.
- [ ] **Step 3:** reveals on §07's rows.
- [ ] **Step 4:** verify — full harness, plus `audit.mjs` reduced-motion (the
      marquee must stop; `css/06-motion.css:489` already does this).
- [ ] **Step 5:** budget check. If css crosses 200 KB, run `deadcss.mjs` and
      delete before raising the line — the rule that produced the 7 KB saving
      last time.
- [ ] **Step 6:** commit and push.

---

## What is deliberately not here

- **Part B, the client portal.** Unchanged, still 9–13 weeks, still starts after
  the public site.
- **A second reveal system.** `data-r` and `data-fx` both exist and both are
  used; these steps add call sites to the existing ones and no eighth variant.
- **Any 3-D library.** `vendor/three/` is deleted and the audit's job is to
  catch anything that re-adds it.
