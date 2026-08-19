# Asset credits & licences

Every third-party asset used on this site, with its source and licence.
**Only commercially-free licences are permitted here** (CC0 / MIT / ISC / OFL / Apache-2.0).
Nothing is hotlinked — everything is self-hosted, which is also why the site's
Content-Security-Policy can forbid all third-party origins.

Add a row here **when you download the asset**, not afterwards.

---

## Libraries

**None.** The site has zero third-party JavaScript. All behaviour is hand-
written and dependency-free:

| File | What it does |
|---|---|
| `js/smooth.js` | Inertial wheel scrolling. Stands down on `prefers-reduced-motion` and on coarse pointers. |
| `js/motion.js` | Counters, word splitting, and the IntersectionObserver reveal fallback. |
| `js/ui.js` | Header, dropdowns, drawer, modals, accordions, back-to-top, toasts, scroll spy. |
| `js/forms.js` | Client-side validation, AJAX submission, CSRF rotation, anti-spam re-seeding. |
| `js/scroll.js` | Active-step tracking for the sticky storytelling sections. |
| `js/assembly.js` | The signature object: geometry, materials, the exploded transform. Imports nothing. |
| `js/studio.js` | The lighting rig, the ground and the camera path. Imports nothing. |
| `js/stage3d.js` | The gates, the still hand-off and the scroll choreography. The only file that touches three.js. |
| `js/interactions.js` | Magnetic buttons and the custom cursor. Gated on `pointer: fine` and stands down under `prefers-reduced-motion`. |
| `js/pixel.js` | The Meta Pixel bootstrap (the one third-party origin on the site). |

Two files were **deleted** in the Machined Paper rebuild, not merely unhooked:
`js/gl.js` (a 959-line WebGL2 point-cloud renderer, whose one host element
shipped a visibly empty 400px frame in full-page capture) and `js/carousel.js`
(the 3-D perspective carousel). Both were competent and neither earned its
place once the page had one object instead of several.

`js/interactions.js` was deleted alongside them and has been **reinstated**. The
argument for removing it — that a firm asking a business owner to trust it with
their checkout does not need the pointer to behave unusually — lost to a direct
design decision: the approved direction calls for magnetic buttons and a custom
cursor, and both are in it. The file is recorded here rather than left untracked
so the reversal is on the record instead of being quietly contradicted by a file
sitting in the working tree.

> **Technique references, not vendored code.** Open-source projects studied for
> *how* an effect is built and then re-implemented here from scratch:
> [Lenis](https://github.com/darkroomengineering/lenis) (MIT) for the
> smooth-scroll integration rules in `js/smooth.js`, and the classic CSS 3-D
> carousel (David DeSandro's *Intro to CSS 3D transforms*) for the ring geometry
> that survives in `css/07-fx.css` after `js/carousel.js` itself was deleted. No
> source from either ships; nothing is loaded from a CDN.

**three.js IS vendored**, self-hosted so `script-src 'self'` needs no change:

| File | Version | Licence | Size |
|---|---|---|---|
| `vendor/three/three.module.min.js` | three.js r185.1 | MIT | 357 KB |
| `vendor/three/three.core.min.js` | three.js r185.1 | MIT | 376 KB |
| `vendor/three/RoomEnvironment.js` | three.js addon | MIT | 5 KB |

**One host, on one page.** `[data-stage]` in `index.php` — the homepage hero and
the exploded assembly, which are the same scene. Nothing else on the site loads
three.js at all. It used to load on seven more pages, for a decorative object
behind the page header; `partials/head-object.php` is now deliberately empty and
records why.

`vendor/three/RoomEnvironment.js` was **deleted**. `js/studio.js` builds its own
studio instead — a key softbox upper left, a broad fill opposite at about a
quarter strength, a narrow rim behind, a wide overhead and a warm bounce card
standing in for the page itself — as emissive rectangles in a small scene, which
`PMREMGenerator` then prefilters into the environment map. RoomEnvironment is a
generic room, and a generic room lighting a subject on a light ground is exactly
what made the previous object resolve to flat grey plastic.

**No HDRI was downloaded, and no RectAreaLight was vendored.** A physical
material is mostly a mirror, and a mirror with nothing to reflect is flat grey
however many lamps you add. The usual answers are a 1k `.hdr` from Poly Haven
(two to three megabytes of float data, a loader and a licence row) or three.js's
`RectAreaLight`, which renders nothing without `RectAreaLightUniformsLib` —
roughly 150 KB of look-up-table data from the examples tree. An emissive
rectangle inside a PMREM'd scene *is* a softbox: it gives area-lit diffuse
shaping and a correctly shaped reflection in the brushed aluminium, which is the
part you can actually see, for zero additional bytes.

Four gates decide whether any of it is fetched:

- **WebGL2 or nothing**, never under `prefers-reduced-motion`, never on
  Save-Data or an effectiveType at or below 2G.
- **Never on a phone.** `js/stage3d.js` tests `matchMedia('(min-width: 761px)')`
  explicitly and returns before the dynamic `import()`. It is deliberately NOT a
  CSS `display: none`: a hidden canvas still costs the full download, and that
  is precisely the regression the previous build shipped when a class got
  renamed. Measured by `inc/tools/audit.mjs` — a 390px viewport requests **0**
  files from `vendor/three/`, and the phone homepage totals 480 KB uncompressed
  against a 600 KB budget.
- **Never during first paint.** It waits for `load` and then an idle callback,
  with a 1500ms backstop. The LCP element is the hero headline; the canvas must
  never compete with it.
- **Never as the visible asset.** The canvas cross-fades in over a pre-rendered
  still already on screen showing the identical frame, so there is nothing to
  see at the moment of the swap. Any failure — a caught import, a lost context —
  removes `.is-live` and the still comes straight back.

On a desktop that passes all four, the homepage is about **1.54 MB
uncompressed**, of which roughly 1 MB is JavaScript and most of that is three.js.
Over the wire, with `mod_deflate` / `mod_brotli` from `.htaccess`, that is closer
to 400 KB. It is a real price, paid deliberately, for one first impression, on
the devices that can afford it.

## Fonts

| Family | Source | Licence | Notes |
|---|---|---|---|
| Space Grotesk | https://fonts.google.com/specimen/Space+Grotesk | SIL OFL 1.1 | Display. Variable, latin subset — `vendor/fonts/space-grotesk-var.woff2` (22 KB) |
| Inter | https://fonts.google.com/specimen/Inter | SIL OFL 1.1 | Body. Variable, latin subset — `vendor/fonts/inter-var.woff2` (48 KB) |

Both downloaded from Google Fonts and self-hosted. No requests are made to
`fonts.googleapis.com` or `fonts.gstatic.com` at runtime.

## Icons

| Set | Source | Licence | Notes |
|---|---|---|---|
| Lucide | https://lucide.dev | ISC | All UI icons in `vendor/icons/sprite.svg` (`#i-*`, stroked) |
| Simple Icons | https://simpleicons.org | CC0 1.0 | Brand marks only — LinkedIn, Instagram, Facebook, WhatsApp, YouTube (filled) |

These replaced Font Awesome, which was the site's only external dependency and
carried attribution requirements.

## Imagery

**There are no photographs on this site.**

That is a decision, not a gap. The build this replaces carried twenty-four CC0
stock photographs — a conference room, a server rack, a warehouse, a cafe, a
clothes rail — 6.1 MB, and they were the strongest images on the page. Every one
of them showed some other company's premises, sitting under headings beginning
with "Our", and five were declared in `sitemap.xml` with rich captions and
submitted to Google Images under Rafly's own URLs. A reader assumes those are
Rafly's clients. They were a search result.

`assets/photos/` and `inc/tools/fetch-photos.mjs`'s output are gone. `photo()`
still returns an empty string for a missing file, so every slot that referenced
one degrades to its designed fallback rather than to a broken image.

### What replaced them

| Asset | Source | Licence | Notes |
|---|---|---|---|
| `assets/render/core-hero-{560,900,1400}.webp` | Original | Owned by Rafly | The signature object, assembled. Rendered offline by `node inc/tools/render-stills.mjs` from the same `js/assembly.js` + `js/studio.js` the live scene uses. |
| `assets/render/core-open-{900,1400}.webp` | Original | Owned by Rafly | The same object, exploded. Shown wherever the live scene never arrives. |
| `assets/render/core-seq-{1,2,3}-640.webp` | Original | Owned by Rafly | The phone sequence: assembled, opening, open. This is the mobile experience, designed rather than degraded. |
| `assets/render/core-og-1200.webp` | Original | Owned by Rafly | The homepage `og:image`, at the 1200x630 the meta tags promise. |
| `uploads/seed-cover-*.png`, `uploads/seed-team-*.png` | Original | Owned by Rafly | Preview-seed art — gradient plates and monogram discs from `inc/tools/build-seed-art.php`. Preview content only. |
| Service marks, the delivery spine, the comparison table | Original | Owned by Rafly | Pure CSS and the icon sprite. No image files at all. |
| `favicon.svg` | Original | Owned by Rafly | **Stopgap** — a drawn "R" monogram, NOT the real Rafly mark. See below. |

Every render carries `alt=""`. They illustrate the proposition; they do not
document anything, and a decorative image with an invented caption is the same
dishonesty as a stock photograph with one. `SITEMAP_SERVICE_IMAGES` in
`inc/sitemap.php` is empty for the same reason, and its comment says so.

The renders are regenerated, not hand-retouched:

```
php -S 127.0.0.1:8899 router.php
node inc/tools/render-stills.mjs --base http://127.0.0.1:8899
```

The tool drives headless Chrome over the DevTools Protocol, imports the live
scene modules, and lets Chrome itself encode the WebP through
`canvas.toDataURL`. No image library, no build step, and no second copy of the
scene that could drift from the one visitors see.

### If photographs are added later

Real ones only: Rafly's own team, its own premises, or a client's store that
Rafly actually built and that the client has agreed to have shown. Those go in
`assets/photos/`, credited in the table above, and `SITEMAP_SERVICE_IMAGES`
becomes non-empty again — nothing downstream has to change.

**No stock portraits as people.** Testimonial and team avatars are monogram
discs. Putting a stranger's face beside a sample quote presents an invented
client as a real one.

**Approved sources**, if third-party assets are ever needed. Photography: Pexels,
Unsplash — read each licence, neither is CC0 and both restrict redistributing
the asset itself. 3D: Poly Pizza, Poly Haven, Khronos glTF-Sample-Assets,
Quaternius, Kenney (all CC0). Textures/HDRI: Poly Haven, ambientCG (CC0).
Illustrations: unDraw, Storyset (check attribution terms).

**Do not use** anything CC-BY-NC, CC-BY-SA (viral for a commercial site), "free
for personal use", or with no stated licence. For a registered company pitching
clients on professionalism, an unlicensed asset is a genuine liability — and
"found on Google Images" is not a licence. If an asset requires attribution
(CC-BY), it must be credited visibly on the site, not only in this file.

---

## Preview seed (sample content)

The public site hides every placeholder row (`is_placeholder`). To *see* the
design populated there is a preview seed: `inc/data/seed-preview.php`
(anonymised sample team, case studies, quotes, categories; the four articles in
`inc/data/seed-posts.php`). It renders only when `PREVIEW_SEED` is `true` in
`inc/config.local.php` **and** no database is reachable (`inc/repo/seed.php`),
and `inc/tools/seed-preview.php --yes` can write it into a preview database. It
must be off / removed for launch — see CONTENT-CHECKLIST.md.

Still outstanding:

- `assets/favicon.svg` — the one remaining stopgap. It is a drawn "R" monogram,
  not the real mark. Every other icon is resampled from the real artwork, so
  this file is the odd one out; a proper SVG export of the mark would retire it.

---

## Generated brand assets

`logo.png` in the project root is the **master artwork** (1408x736, 176 KB) and
is not served to visitors. Everything in `assets/` below is derived from it by
cropping and resampling (`inc/tools/build-assets.php`) — no redrawing, no
tracing, so these are the real mark rather than an approximation.

| File | Size | Used by |
|---|---|---|
| `logo.png` | 320x99, 21 KB | header and drawer |
| `logo-reversed.png` | 97x30, 19 KB | footer |
| `og-cover.png` | 1200x630, 64 KB | `og:image` / `twitter:image` |
| `apple-touch-icon.png` | 180x180, 12 KB | iOS home screen (opaque white — iOS composites transparency on black) |
| `favicon-32.png` | 32x32, 1.4 KB | SVG-favicon fallback |
| `icon-192.png` / `icon-512.png` | 11 KB / 50 KB | PWA manifest, and `Organization.logo` in JSON-LD |

Two constraints drove the formats: no social-share crawler accepts SVG for
`og:image`, and iOS ignores an SVG `apple-touch-icon` — so those had to be PNG
regardless of preference. The 512 is also what JSON-LD points at, because Google
rejects an `Organization.logo` under 112px on either axis and the horizontal
lockup is only 99px tall.

---

## Colour

Every colour in `css/00-tokens.css` carries its **measured** WCAG ratio in a
comment beside it, computed against the real ground it sits on, and
`inc/tools/shoot.mjs` re-measures the rendered pages on every run. That harness
is not ceremony: it caught `--ink-4` at 2.92:1 against the 3:1 large-text
minimum during this rebuild, on a value that had been reasoned about and written
down as safe.

**One accent.** The orange ladder (`--orange` 2.84:1 decorative, `--orange-mid`
3.76:1, `--orange-ink` 4.93:1) and the gold used for star ratings are retired —
removed, not aliased, with every call site migrated, so the retirement cannot
quietly undo itself. Three accents is no accent: the call to action could never
be the loudest thing on a screen where two other colours were already shouting.

**The rule most likely to be broken later** is now a mirror pair rather than a
ladder:

| Token | On paper | In the dark chapter |
|---|---|---|
| `--blue` `#1b4fd8` | **6.15:1** — text, links, CTA | 2.68:1 — fills only |
| `--accent-chapter` `#6ea0ff` | 2.39:1 — decoration only | **6.90:1** — text, links |

Each is dangerous exactly where the other is safe, and a component that
hardcodes either will look completely correct on the ground it was written on.
Components read `--accent-fg`, which `css/08-ground.css` remaps per ground.

---

See `CONTENT-CHECKLIST.md` at the repo root for the full launch handoff list.
