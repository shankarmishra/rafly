/**
 * decks.js — the two scroll-driven mockup decks.
 *
 * THE BROWSER FAN (five product screens) and THE PHONES (three shells) are
 * the same mechanism at two scales, so they are one file and one rAF: read
 * the section's own progress through the viewport, ease it, and place every
 * card on an arc from a tight stack to an open fan.
 *
 * PROGRESS IS READ FROM THE SECTION, NOT FROM window.scrollY. A section that
 * knows its own bounding box works wherever it is put on the page and keeps
 * working when something above it changes height. Reading a global scroll
 * offset and subtracting a hardcoded start is how a deck silently desyncs the
 * first time a paragraph is edited above it.
 *
 * THE STACK IS VISIBLE AT PROGRESS ZERO. This is the one thing the prototype
 * got wrong and it is worth stating: its opacity was min(1, e * 1.6), so at
 * e = 0 the whole deck was invisible and the top third of a 320vh section was
 * dead space. Here the deck starts as a real, tight, legible stack — k begins
 * at 0.07, not 0 — and fans open from there. Nothing is ever blank.
 *
 * Everything is transform only. No layout, no paint, no reflow.
 */

const lerp = (a, b, n) => a + (b - a) * n;
const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

/**
 * Slot geometry: where each card sits when the fan is fully open.
 * x/z in px, ry in degrees, s a scale. Index 0 is the centre card and is
 * nearest the viewer; the pairs step back and turn in to face it.
 */
const FAN = [
    { x: 0,    z: 60,   ry: 0,   s: 1.00 },
    { x: -250, z: -150, ry: 15,  s: 0.90 },
    { x: 250,  z: -150, ry: -15, s: 0.90 },
    { x: -448, z: -350, ry: 24,  s: 0.80 },
    { x: 448,  z: -350, ry: -24, s: 0.80 },
];

const PHONE_FAN = [
    { x: 0,    z: 80,   ry: 0,   s: 1.00 },
    { x: -206, z: -160, ry: 19,  s: 0.88 },
    { x: 206,  z: -160, ry: -19, s: 0.88 },
];

/**
 * Paint order, so the centre card is drawn last and therefore on top without
 * any z-index bookkeeping. The DOM order is centre-first because that is the
 * order the markup reads in; this maps DOM index to visual slot.
 */
function layout(host, geo, order) {
    const cards = [...host.children];
    return cards.map((el, i) => {
        const slot = order[i] ?? i;
        el.style.zIndex = String(10 - Math.abs(geo[slot].z / 100));
        return { el, geo: geo[slot], i: slot };
    });
}

function makeDeck(section, host, geo, order) {
    if (!section || !host || !host.children.length) return null;

    const items = layout(host, geo, order);
    let eased = 0;
    let raf = 0, running = false;

    function frame() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;

        // A section shorter than the viewport has no scroll range of its own;
        // treat it as fully open rather than dividing by zero.
        const raw = span > 0 ? clamp01(-r.top / span) : 1;
        eased = lerp(eased, raw, 0.09);

        // The fan completes at 65% of the section, so the last third is the
        // open deck holding still and being looked at rather than still moving.
        const spread = easeInOut(clamp01(eased / 0.65));

        for (const { el, geo: g, i } of items) {
            const stagger = clamp01((spread - i * 0.05) / (1 - i * 0.05));
            const e = easeInOut(stagger);

            // 0.07, not 0: the deck opens FROM a visible tight stack.
            const k = 0.07 + 0.93 * e;
            const bob = Math.sin(eased * 3 + i) * 9;
            const y = lerp(26, 0, e) + bob - i * 4 * (1 - e);

            el.style.transform =
                `translate3d(${g.x * k}px, ${y}px, ${g.z * k}px) ` +
                `rotateY(${g.ry * k}deg) rotateX(${(1 - e) * 6}deg) ` +
                `scale(${lerp(0.86, g.s, e)})`;
        }

        raf = requestAnimationFrame(frame);
    }

    const start = () => { if (!running) { running = true; raf = requestAnimationFrame(frame); } };
    const stop = () => { running = false; cancelAnimationFrame(raf); };

    /* Only run while the section is anywhere near the viewport. A deck five
       screens below the fold does not need a frame budget. */
    const io = new IntersectionObserver(
        ([entry]) => (entry.isIntersecting ? start() : stop()),
        { rootMargin: '20% 0px' }
    );
    io.observe(section);

    const onVisibility = () => { document.hidden ? stop() : start(); };
    document.addEventListener('visibilitychange', onVisibility);

    host.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        document.removeEventListener('visibilitychange', onVisibility);
        host.classList.remove('is-live');
        for (const { el } of items) el.style.transform = '';
    };
}

export function initDecks() {
    const kill = [];

    const deck = document.querySelector('[data-deck]');
    if (deck) {
        // Centre, then the inner pair, then the outer pair — the DOM order is
        // the reading order of the five services, this is where each lands.
        const d = makeDeck(deck.closest('section'), deck, FAN, [0, 1, 2, 3, 4]);
        if (d) kill.push(d);
    }

    const phones = document.querySelector('[data-phones]');
    if (phones) {
        const p = makeDeck(phones.closest('section'), phones, PHONE_FAN, [0, 1, 2]);
        if (p) kill.push(p);
    }

    return () => kill.forEach((fn) => fn());
}
