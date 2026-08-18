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
| `js/motion.js` | Counters, word splitting, marquee duplication, and the IntersectionObserver reveal fallback. |
| `js/ui.js` | Header, dropdowns, drawer, modals, accordions, back-to-top, toasts, scroll spy. |
| `js/forms.js` | Client-side validation, AJAX submission, CSRF rotation, anti-spam re-seeding. |
| `js/carousel.js` | The 3-D perspective carousel. |
| `js/scroll.js` | Active-step tracking for the sticky storytelling sections. |
| `js/gl.js` | WebGL2 point-cloud renderer — the delivery-flow object. |
| `js/stage3d.js` | The only file that touches three.js, and only if it is vendored. |
| `js/pixel.js` | The Meta Pixel bootstrap (the one third-party origin on the site). |

> **Technique references, not vendored code.** Open-source projects studied for
> *how* an effect is built and then re-implemented here from scratch:
> [Lenis](https://github.com/darkroomengineering/lenis) (MIT) for the
> smooth-scroll integration rules in `js/smooth.js`, and the classic CSS 3-D
> carousel (David DeSandro's *Intro to CSS 3D transforms*) for the ring geometry
> in `css/07-fx.css` and `js/carousel.js`. No source from either ships; nothing
> is loaded from a CDN.

**three.js IS vendored**, self-hosted so `script-src 'self'` needs no change:

| File | Version | Licence | Size |
|---|---|---|---|
| `vendor/three/three.module.min.js` | three.js r185.1 | MIT | 357 KB |
| `vendor/three/three.core.min.js` | three.js r185.1 | MIT | 376 KB |
| `vendor/three/RoomEnvironment.js` | three.js addon | MIT | 5 KB |

`js/stage3d.js` is the only file that touches it, and it dynamic-imports only
when a `[data-stage3d]` host comes within 300px of the viewport, WebGL2 exists,
motion is not reduced, and the connection is neither Save-Data nor 2G. Any
failure at all falls back silently to the designed still.

**Where it loads, and what that costs.** There are three hosts:

| Host | Where | Object |
|---|---|---|
| `data-stage3d="hero"` | the homepage hero | browser window, phone, analytics card, shield, parcel, `< />`, chat bubble, database stack, plus connecting nodes — scattered around the centred headline |
| `data-stage3d="head"` | seven secondary page heads | the same object, fewer parts, framed tighter |
| `data-stage3d="peace"` | the P.E.A.C.E. section | five stacked rings and a glass core |

The hero object took four attempts. A ring of flat tiles was right but belonged
elsewhere, and moved to `#capabilities`. A `js/gl.js` point cloud was rejected —
dots have no surface, so nothing reflects, so it cannot look manufactured. A
glass torus knot was rejected as generic, correctly: a knot is the default
object of every 3-D demo and said nothing about this company. What is there now
is built from the subject instead of from a geometry library — the things Rafly
actually sells, arranged around the words rather than behind them, so the
composition has an empty middle and the type needs no mask to stay readable.

Three gates decide whether any of it is fetched:

- **WebGL2 or nothing**, never under `prefers-reduced-motion`, never on
  Save-Data or an effectiveType at or below 2G.
- **Never on a phone.** `.hero-3d` is `display: none` below 760px and
  `.head-3d` below 560px. That is not merely hidden — an element with no layout
  box never intersects, so the IntersectionObserver never fires and the dynamic
  import is never reached. Measured: a 390px viewport requests **0** files from
  `vendor/three/`; a 1440px one requests 3.
- **Never during first paint.** The P.E.A.C.E. object is below the fold, so its
  observer is the whole gate. The hero and the page heads are above it, so they
  would otherwise land in the middle of the initial render; they wait for
  `load` and then an idle callback (1200ms backstop). Both cross-fade in over a
  designed still that is already on screen, so arriving a beat later costs
  nothing visible.

On a desktop that passes all three, the homepage is about **1.5 MB
uncompressed**, of which roughly 1 MB is JavaScript and most of that is
three.js. That is a real price and it is paid deliberately, for the first
impression, on the devices that can afford it.

**No HDRI was downloaded, deliberately.** A physical material is mostly a mirror,
and a mirror with nothing to reflect is flat grey however many lamps you add — so
the object needs an environment map. The usual answer is a 1k `.hdr` from Poly
Haven: two to three megabytes of float data, plus a loader, plus a licence row.
`RoomEnvironment` builds an equivalent studio lightbox out of emissive boxes at
runtime and `PMREMGenerator` prefilters it into the same cube map, for 5 KB and
no licence at all.

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

There are now twelve third-party photographs, all CC0, each listed below with
its photographer and source. Everything else visual is drawn in CSS, generated
by a tool in `inc/tools/`, or a screenshot of this site taken from this site.

**The capabilities ring is drawn, not photographed, and that took two attempts
to settle.** (It spent one pass in the hero before moving to its own section.) A CC0 search for digital-agency subjects returns a gas mask, a Twitch
logo and a Burger King sign — the free-licence pools are not deep enough to
curate thirteen coherent tiles from, and every near miss puts somebody else's
trademark in the first thing a visitor sees. Screenshots of our own pages failed
differently: this site is light and text-heavy, so a 760px square of it shrinks
to grey mush inside a 124px tile. The tiles are now an icon and a word on a
brand gradient — on-topic by construction, sharp at any pixel ratio, and neither
bytes nor licence.

| Asset | Source | Licence | Notes |
|---|---|---|---|
| `assets/mockups/laptop-screen.*`, `phone-screen.*` | Original | Owned by Rafly | Real captures of `/pricing` and `/blog`, taken by `inc/tools/capture-mockups.mjs`. They sit inside the hero's CSS-drawn device frames. Re-run the tool and they update with the design. |
| `uploads/seed-cover-*.png`, `uploads/seed-team-*.png` | Original | Owned by Rafly | Preview-seed art — brand gradient plates and monogram discs, generated by `inc/tools/build-seed-art.php`. Preview content only; see CONTENT-CHECKLIST.md. |
| Device frames, the branching figure, the P.E.A.C.E. orbit, the service marks, the WebGL still | Original | Owned by Rafly | Pure CSS and the icon sprite. No image files at all. |
| Delivery-flow object | Original | Owned by Rafly | WebGL2 point-cloud forms in `js/gl.js` (sphere, knot, helix, grid) |
| `favicon.svg` | Original | Owned by Rafly | **Stopgap** — a plain "R" monogram, NOT the real Rafly mark. See below. |

The previous build printed every picture as a two-colour halftone "plate" on a
cream ground. That treatment, its source photographs and the tool that made them
(`build-paper.php`) are all gone — the direction it belonged to was rejected.

### If photographs are added later

`assets/photos/` exists and is empty. Every slot that would hold a photograph
already has a designed fallback, so the site is complete without them:
`photo()` returns an empty string for a missing file, and the challenge cards,
the carousel cells, the blog covers and the About figure all fall back to brand
tints or drawn objects.

### The photographs

Every row below is generated by `node inc/tools/fetch-photos.mjs --list` from
`assets/photos/credits.json`, which that tool writes from the same API response
that chose the file — so the credit cannot drift from what was downloaded.

| File | Photographer | Source | Licence |
|---|---|---|---|
| `assets/photos/about-desk.jpg` | [Green Chameleon](https://stocksnap.io/author/6745) | [stocksnap](https://stocksnap.io/photo/writing-drawing-8Y0EDX4VP9) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/challenge-cables.jpg` | [roland](https://www.flickr.com/photos/35034347371@N01) | [flickr](https://www.flickr.com/photos/35034347371@N01/84136) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/challenge-hourglass.jpg` | [Negative Space](https://stocksnap.io/author/4440) | [stocksnap](https://stocksnap.io/photo/clock-time-72R81VRMM0) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/challenge-lock.jpg` | [Jennifer Bourn](https://jenniferbourn.com) | [wordpress](https://wordpress.org/photos/photo/93624f06d0/) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/service-content.jpg` | [Kristin Hardwick](https://www.kristinhardwick.com) | [stocksnap](https://stocksnap.io/photo/camera-tripod-LAYS8FQZWO) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/service-ecom.jpg` | U.S. Department of Agriculture | [rawpixel](https://www.rawpixel.com/image/3306152/free-photo-image-warehouse-logistics-delivery) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/service-marketing.jpg` | [Serpstat](https://serpstat.com/ru) | [stocksnap](https://stocksnap.io/photo/seo-ppc-9699Y6WKLD) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/service-security.jpg` | [D Coetzee](https://www.flickr.com/photos/29507259@N02) | [flickr](https://www.flickr.com/photos/29507259@N02/6271241131) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/service-web.jpg` | [Matthew Henry](https://stocksnap.io/author/200) | [stocksnap](https://stocksnap.io/photo/laptop-code-0KAT80KW5F) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/work-1.jpg` | [Wonderlane](https://www.flickr.com/photos/71401718@N00) | [flickr](https://www.flickr.com/photos/71401718@N00/5526839767) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/work-2.jpg` | [Artem Beliaikin](https://www.flickr.com/photos/157635012@N07) | [flickr](https://www.flickr.com/photos/157635012@N07/49174901528) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |
| `assets/photos/work-3.jpg` | [donterase](https://georgeyanakiev.com/donterase) | [stocksnap](https://stocksnap.io/photo/cafe-restaurant-HOHJK6B7TD) | [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/) |

**Source: Openverse** (`api.openverse.org`), queried with `license=cc0`.
Openverse is Creative Commons' own index, which is why it was chosen over
Unsplash and Pexels: both of those need an API key and neither is CC0 — their
licences permit site use but restrict redistributing the asset, a condition this
repository would then carry forever.

To change one: edit its query in `inc/tools/fetch-photos.mjs`, delete the file,
re-run the tool, then `php inc/tools/build-photos.php` for the WebP twins.

**Approved sources.** Photography: Pexels, Unsplash (read each licence — neither
is CC0, and both restrict redistributing the asset itself as a competing
service; site imagery is fine). 3D models: Poly Pizza, Poly Haven, Khronos
glTF-Sample-Assets, Quaternius, Kenney — all CC0. Textures/HDRI: Poly Haven,
ambientCG (CC0). Illustrations: unDraw, Storyset (check attribution terms).

**Do not use** anything CC-BY-NC, CC-BY-SA (viral for a commercial site), "free
for personal use", or with no stated licence. For a registered company pitching
clients on professionalism, an unlicensed asset is a genuine liability — and
"found on Google Images" is not a licence.

If an asset requires attribution (CC-BY), it must be credited visibly on the
site, not only in this file.

**No stock portraits as people.** Testimonial and team avatars are monogram
discs. The testimonials in the preview seed are anonymised samples, and putting
a stranger's face beside a sample quote presents an invented client as a real
one. Real photographs go in only when the real person supplies them.

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
comment beside it, and `inc/tools/shoot.mjs` re-measures the rendered pages on
every run. The one that most needs stating: `--orange #ff6b35` is **2.84:1 on
white** and is decorative only. Orange that carries an icon uses `--orange-mid`
(3.76:1) and orange that carries text uses `--orange-ink` (4.93:1). Swapping
them is the exact mistake that put 2.15:1 white-on-amber into a previous build.

---

See `CONTENT-CHECKLIST.md` at the repo root for the full launch handoff list.
