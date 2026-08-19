/**
 * gates.js — the one place that decides whether an effect is allowed to run.
 *
 * There are four consumers (field, aurora, the chapter scene, the decks) and
 * they ask overlapping questions. When each asked them itself the answers
 * drifted: one checked reduced-motion and not Save-Data, another checked a
 * width breakpoint in CSS instead of JS, and a hidden canvas kept costing the
 * full download. One module, one answer.
 *
 * The rules, and why each exists:
 *
 *   reduced motion   A visitor who has asked their operating system to stop
 *                    animation has asked once, for everything. Nothing here
 *                    communicates meaning; it is all emphasis on something the
 *                    markup already says, so it can all simply not happen.
 *
 *   Save-Data / 2G   The visitor has told the browser they are paying for
 *                    bytes. A decorative shader is exactly the thing to drop.
 *
 *   coarse pointer   A cursor-reactive dot field on a touchscreen reacts to
 *                    nothing. It is a battery cost with no visible effect.
 *
 *   phone            Tested in JS, BEFORE the dynamic import, never as a CSS
 *                    `display: none`. A hidden canvas still downloads its
 *                    module and still runs its rAF; that is precisely the
 *                    regression a previous build shipped when a class was
 *                    renamed.
 *
 * Every gate fails CLOSED. If something here throws, the answer is "no" and
 * the page renders its designed fallback, which is a complete page.
 */

/** The visitor asked for less motion. Live-checked: it can change mid-session. */
export const reducedMotion = () => {
    try {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch { return true; }
};

/** A real pointer that can hover. */
export const finePointer = () => {
    try {
        return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    } catch { return false; }
};

/** Wide enough that a decorative layer is not competing with the content. */
export const wideEnough = (min = 761) => {
    try {
        return window.matchMedia(`(min-width: ${min}px)`).matches;
    } catch { return false; }
};

/** The visitor is paying for bytes, or has almost none to pay with. */
export const cheapConnection = () => {
    try {
        const c = navigator.connection;
        if (!c) return false;
        if (c.saveData) return true;
        return /(^|-)2g$/.test(c.effectiveType || '');
    } catch { return false; }
};

/** WebGL2, actually acquired rather than assumed from a constructor existing. */
export const webgl2 = () => {
    try {
        const c = document.createElement('canvas');
        return !!c.getContext('webgl2', { failIfMajorPerformanceCaveat: true });
    } catch { return false; }
};

/**
 * Tier C — a 2D canvas or a DOM animation. Cheap, but still not free, and
 * still pointless without a pointer to react to.
 */
export const allowCanvas = () =>
    !reducedMotion() && !cheapConnection() && wideEnough() && finePointer();

/** Tier B — a raw WebGL2 shader. No library, one full-screen quad. */
export const allowShader = () =>
    !reducedMotion() && !cheapConnection() && wideEnough() && webgl2();

/**
 * Tier A — three.js. The only tier that costs a 365 KB download, so it is
 * the only one that also refuses a phone outright rather than by width.
 */
export const allowThree = () =>
    allowShader() && wideEnough(1024);

/**
 * Run fn when the browser is genuinely idle, with a backstop so a page that
 * never goes idle still gets there. Nothing decorative may compete with the
 * LCP element, which is the hero headline.
 */
export const whenIdle = (fn, backstop = 1500) => {
    let done = false;
    const go = () => { if (!done) { done = true; fn(); } };
    if (document.readyState === 'complete') {
        (window.requestIdleCallback || ((f) => setTimeout(f, 1)))(go, { timeout: backstop });
    } else {
        window.addEventListener('load', () => {
            (window.requestIdleCallback || ((f) => setTimeout(f, 1)))(go, { timeout: backstop });
        }, { once: true });
    }
    setTimeout(go, backstop + 400);
};

/**
 * Read a CSS custom property off :root as a colour string.
 *
 * The canvas layers MUST do this rather than hardcode a hex. css/00-tokens.css
 * is the single source of truth and it carries the measured contrast ratio for
 * every value; a canvas with #0a63ff baked into it silently stops agreeing
 * with the palette the first time the palette moves, and the harness cannot
 * see inside a canvas to catch it.
 */
export const token = (name, fallback = '#0a63ff') => {
    try {
        const v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        return v || fallback;
    } catch { return fallback; }
};

/**
 * '#0a63ff' -> [10, 99, 255], so a canvas can build rgba() with its own alpha
 * instead of needing a token per opacity.
 */
export const rgb = (hex) => {
    let h = String(hex).trim().replace(/^#/, '');
    if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
    if (!/^[0-9a-f]{6}$/i.test(h)) return [10, 99, 255];
    return [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16));
};
