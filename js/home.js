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

import { allowCanvas, allowShader, finePointer, whenIdle, token, rgb } from './gates.js';

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
    // The cards move on any pointer-capable device and cost nothing but
    // transforms, so they are gated on motion preference alone rather than on
    // the full canvas ladder.
    const cluster = hero.querySelector('[data-cluster]');
    if (cluster && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        import('./cluster.js')
            .then((m) => m.initCluster(cluster))
            .catch(() => {});
    }

    if (allowShader()) {
        const canvas = hero.querySelector('[data-aurora]');
        if (canvas) {
            whenIdle(() => {
                import('./aurora.js')
                    .then((m) => m.initAurora(canvas, hero, rgb(token('--paper', '#f6f8fc'))))
                    .catch(() => { /* the CSS gradient fallback is the same design, frozen */ });
            });
        }
    }
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
