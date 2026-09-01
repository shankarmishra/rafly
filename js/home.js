/**
 * home.js — the homepage's one entry point.
 *
 * It replaces stage3d.js as the module partials/tail.php loads. Everything
 * decorative on this page is reached from here and NOTHING is imported
 * statically: each effect is a dynamic import() behind its gate, so a phone,
 * a reduced-motion visitor and a Save-Data visitor never download a byte of
 * code they will not run.
 *
 * That last point is the whole design. The previous build gated with CSS in
 * one place and JS in another, and a renamed class shipped a hidden canvas
 * that still downloaded and still ran. A gate that decides AFTER the import
 * has already saved nothing.
 *
 *   TIER C  field.js    2D canvas. Traces, packets, dot network.
 *   TIER B  aurora.js   raw WebGL2, one fragment shader.
 *           cluster.js  DOM transforms only — cheapest thing here.
 *
 * Tier A (three.js, the dark chapter scene) is not wired yet; the chapter
 * renders its static form until it is, which is a complete section.
 *
 * Failure is always silent and always downward. A caught import, a refused
 * context, a shader that will not compile: the page keeps the designed still
 * form it already had on screen. No effect here carries meaning.
 */

import { allowCanvas, allowShader, finePointer, wideEnough, whenIdle, token, rgb } from './gates.js';

/* ------------------------------------------------------- the background */

if (allowCanvas()) {
    const canvas = document.getElementById('field');
    if (canvas) {
        whenIdle(() => {
            import('./field.js')
                .then((m) => m.initField(canvas))
                .catch(() => { /* the CSS dot texture underneath is the fallback */ });
        });
    }
}

/* ------------------------------------------------------------- the hero */

const hero = document.querySelector('[data-hero]');

if (hero) {
    import('./hero-growth-field.js')
        .then((m) => m.initHeroGrowthField(hero))
        .catch(() => {});
}

/* ------------------------------------------------ the manifesto / difference */

const manifesto = document.querySelector('[data-manifesto]');
if (manifesto) {
    import('./manifesto.js')
        .then((m) => m.initManifesto(manifesto))
        .catch(() => { /* Clean CSS fallback renders static presentation */ });
}

/* ------------------------------------------- THE RAFly SERVICE STUDIO */

const serviceStudio = document.querySelector('[data-service-studio]');
if (serviceStudio) {
    import('./service-studio.js')
        .then((m) => m.initServiceStudio(serviceStudio))
        .catch(() => { /* Clean static CSS presentation is fallback */ });
}

/* ------------------------------------------- THE RAFly BUILD MATRIX */

const buildMatrix = document.querySelector('[data-build-matrix]');
if (buildMatrix) {
    import('./build-matrix.js')
        .then((m) => m.initBuildMatrix(buildMatrix))
        .catch(() => { /* Clean static CSS presentation is fallback */ });
}

/* ------------------------------------------------------- the two decks */

/* The browser fan and the phones. Transforms only, so they are the cheapest
   thing on the page after the cluster and are gated on motion preference
   rather than on the full canvas ladder — a phone should still get the fan,
   it is the section's whole point. Under reduced motion the CSS lays both out
   as a static open fan, which is the same composition holding still. */
if ((document.querySelector('[data-deck]') || document.querySelector('[data-phones]'))
    && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    import('./decks.js')
        .then((m) => m.initDecks())
        .catch(() => { /* the CSS stack is a complete, readable fallback */ });
}

/* ---------------------------------------------------- honest limits tabs */

const limitsNav = document.querySelector('.limits-filter-nav');
const limitsSec = document.querySelector('.limits-section');

if (limitsNav) {
    const buttons = limitsNav.querySelectorAll('[data-filter]');
    const cards = document.querySelectorAll('.limit-card');

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;
            if (!filter) return;

            buttons.forEach((b) => {
                const isActive = b === btn;
                b.classList.toggle('is-active', isActive);
                b.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            // Update ambient background flare to match active service token
            const accentStyle = btn.style.getPropertyValue('--fp-accent');
            if (limitsSec && accentStyle) {
                limitsSec.style.setProperty('--active-svc-glow', accentStyle);
            }

            let visibleIdx = 0;
            cards.forEach((card) => {
                const match = card.dataset.category === filter;
                if (match) {
                    card.style.display = '';
                    card.classList.remove('is-hidden');
                    card.classList.remove('is-visible');
                    card.style.animationDelay = (visibleIdx * 0.07) + 's';
                    visibleIdx++;
                    // Trigger reflow to restart CSS animation
                    void card.offsetWidth;
                    card.classList.add('is-visible');
                } else {
                    card.style.display = 'none';
                    card.classList.add('is-hidden');
                    card.classList.remove('is-visible');
                }
            });
        });
    });
}

/* ------------------------------------------- viral reels & content engine */

const reelsEngineSection = document.querySelector('[data-reels-engine]');
if (reelsEngineSection) {
    import('./reels-engine.js')
        .then((m) => m.initReelsEngine(reelsEngineSection))
        .catch(() => { /* CSS fallback holds the assembled still form */ });
}

/* ----------------------------------------------------------- the gallery */

/* Transforms and two custom properties per card, so it is gated on motion
   preference rather than on the canvas ladder — a phone should get this, it is
   the section's whole point. Under reduced motion css/09-scenes.css lays the
   five cards out in the same coverflow with the transitions off. */
if (document.querySelector('[data-gallery]')
    && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    import('./gallery.js')
        .then((m) => m.initGallery(document.querySelector('[data-gallery]')))
        .catch(() => { /* the CSS coverflow is already the complete design */ });
}

/* ------------------------------------------------------- the bento sheen */

/* A highlight that follows the cursor is nothing without a cursor, so this is
   the one effect on the page gated on the POINTER rather than on the canvas
   ladder — a phone never fetches it. Reduced motion keeps it: the sheen is a
   hover state, not an animation, and the CSS turns off only the card's travel.
   Without this module --mx/--my resolve to 50% and hover is a centred glow. */
if (finePointer() && document.querySelector('[data-sheen]')) {
    whenIdle(() => {
        import('./sheen.js')
            .then((m) => m.initSheen())
            .catch(() => { /* the centred fallback glow is already the design */ });
    });
}

/* ------------------------------------------------ interaction polish */

/* Magnetic buttons and the custom cursor. The file gates itself again on
   pointer: fine and reduced motion — it has to, because it is also loadable
   from elsewhere — but it is gated HERE too so phones never fetch its 12 KB.
   perf-budget.json allows 80 KB of JS on the phone homepage and the tail is
   already 59 KB; this is the single largest thing that could push it over. */
if (allowCanvas()) {
    whenIdle(() => {
        import('./interactions.js').catch(() => {});
    }, 2200);
}
