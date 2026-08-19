/**
 * decks.js — the two scroll-driven mockup decks.
 *
 * THE BROWSER FAN (five product screens) and THE PHONES (three shells) are
 * the same mechanism at two scales, so they are one file and one rAF: read
 * the section's own progress through the viewport, ease it, and place every
 * card on an arc.
 *
 * THE BROWSER DECK HAS THREE STATES, NOT TWO. Naveen was asked whether he
 * wanted the wide fan or the overlapping coverflow and answered "dono — scroll
 * pe fan se coverflow", so the section is choreographed as one move through
 * both:
 *
 *     0.00 - 0.02   a tight stack, already visible
 *     0.02 - 0.55   opens to FAN      — five separate, legible screens
 *     0.55 - 0.90   closes to COVER   — overlapping, sides turned in and dimmed
 *     0.90 - 1.00   holds
 *
 * The fan is the section's argument (five products) and the coverflow is its
 * conclusion (one place they all live), which is why the order is that way
 * round and not the reverse.
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
 * Everything is transform plus one custom property. No layout, no paint, no
 * reflow.
 */

const lerp = (a, b, n) => a + (b - a) * n;
const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

/**
 * Slot geometry: where each card sits when the fan is fully open.
 * x/y/z in px, ry/rz in degrees, s a scale. Index 0 is the centre card and is
 * nearest the viewer; the pairs step back and turn in to face it.
 */
const FAN = [
    { x: 0,    y: 0, z: 60,   ry: 0,   rz: 0, s: 1.00 },
    { x: -250, y: 0, z: -150, ry: 15,  rz: 0, s: 0.90 },
    { x: 250,  y: 0, z: -150, ry: -15, rz: 0, s: 0.90 },
    { x: -448, y: 0, z: -350, ry: 24,  rz: 0, s: 0.80 },
    { x: 448,  y: 0, z: -350, ry: -24, rz: 0, s: 0.80 },
];

/**
 * Where the fan collapses to. Derived from OriginKit's coverflow controls —
 * gap 8, sideways tilt 8 degrees, card tilt 12 degrees, inactive opacity 60% —
 * re-proportioned for a 430px card, because those numbers were authored
 * against a different card size and copying them literally would have produced
 * a deck with a hole in the middle.
 *
 * What is actually taken from the reference is the SHAPE: the centre card
 * comes forward and stays square to the viewer, the rest overlap it, turn hard
 * toward it, and drop to 60% so the eye has one place to land.
 *
 * ON THE DIM AND THE CONTRAST HARNESS. inc/tools/shoot.mjs skips an element
 * whose OWN computed opacity is below 0.5; an ancestor at 0.6 is not counted,
 * so this dim will not appear in its report. That is stated here rather than
 * left for a later reader to assume the meter cleared it. It is acceptable
 * because the deck is aria-hidden and every string inside a mock is chrome —
 * "rafly.in/app/web" and the service tag — with no information in it. If real
 * copy is ever put in a mock, this dim has to be re-argued.
 */
const COVER = [
    { x: 0,    y: 0,  z: 150,  ry: 0,   rz: 0,  s: 1.02, dim: 1.00 },
    { x: -232, y: 10, z: -170, ry: 52,  rz: 3,  s: 0.90, dim: 0.60 },
    { x: 232,  y: 10, z: -170, ry: -52, rz: -3, s: 0.90, dim: 0.60 },
    { x: -392, y: 22, z: -340, ry: 60,  rz: 5,  s: 0.82, dim: 0.60 },
    { x: 392,  y: 22, z: -340, ry: -60, rz: -5, s: 0.82, dim: 0.60 },
];

/**
 * The phones, in the Appit arrangement: the CENTRE shell forward and LOW, the
 * side shells back, HIGH, turned in and tilted. Ours previously sat on one
 * baseline at one height, which is the difference between a group of objects
 * and a filmstrip — three phones in a row read as a contact sheet no matter
 * how much perspective is put behind them.
 *
 * rz is what makes it a tilt rather than a turn. rotateY alone swings a phone
 * like a door; a few degrees of rotateZ with it is a phone held in a hand.
 */
const PHONE_FAN = [
    { x: 0,    y: 30,  z: 120,  ry: 0,   rz: 0,  s: 1.00 },
    { x: -248, y: -44, z: -190, ry: 20,  rz: 5,  s: 0.86 },
    { x: 248,  y: -44, z: -190, ry: -20, rz: -5, s: 0.86 },
];

/**
 * Paint order, so the centre card is drawn last and therefore on top without
 * any z-index bookkeeping. The DOM order is centre-first because that is the
 * order the markup reads in; this maps DOM index to visual slot.
 */
function layout(host, geo, order, cover) {
    const cards = [...host.children];
    return cards.map((el, i) => {
        const slot = order[i] ?? i;
        el.style.zIndex = String(10 - Math.abs(geo[slot].z / 100));
        return { el, geo: geo[slot], cover: cover ? cover[slot] : null, i: slot };
    });
}

function makeDeck(section, host, geo, order, cover) {
    if (!section || !host || !host.children.length) return null;

    const items = layout(host, geo, order, cover);
    let eased = 0;
    let raf = 0, running = false;

    function frame() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;

        // A section shorter than the viewport has no scroll range of its own;
        // treat it as fully open rather than dividing by zero.
        const raw = span > 0 ? clamp01(-r.top / span) : 1;
        eased = lerp(eased, raw, 0.09);

        // Two overlapping stages. The fan is complete at 55% and the collapse
        // runs from there to 90%, which leaves a tenth of the range as a hold
        // on the finished arrangement before the sticky releases. Earlier this
        // was a single stage completing at 78% and the last fifth was a deck
        // standing still — correct as choreography, wrong as layout.
        const openT  = easeInOut(clamp01((eased - 0.02) / 0.53));
        const coverT = cover ? easeInOut(clamp01((eased - 0.55) / 0.35)) : 0;

        for (const { el, geo: g, cover: c, i } of items) {
            const stagger = clamp01((openT - i * 0.05) / (1 - i * 0.05));
            const e = easeInOut(stagger);

            // 0.07, not 0: the deck opens FROM a visible tight stack.
            const k = 0.07 + 0.93 * e;

            // Where this card is in the fan RIGHT NOW — the collapse
            // interpolates away from the live fan value, not from the fan's
            // finished value, so the two stages can overlap without a jump if
            // the section is ever shortened.
            const fx  = g.x * k;
            const fy  = lerp(26, 0, e) + g.y * k - i * 4 * (1 - e);
            const fz  = g.z * k;
            const fry = g.ry * k;
            const frz = g.rz * k;
            const fs  = lerp(0.86, g.s, e);

            // The idle bob and the forward lean both die out as the deck locks
            // into the coverflow. A settled arrangement that is still breathing
            // reads as unfinished.
            const bob = Math.sin(eased * 3 + i) * 9 * (1 - coverT);

            const x  = c ? lerp(fx,  c.x,  coverT) : fx;
            const y  = (c ? lerp(fy,  c.y,  coverT) : fy) + bob;
            const z  = c ? lerp(fz,  c.z,  coverT) : fz;
            const ry = c ? lerp(fry, c.ry, coverT) : fry;
            const rz = c ? lerp(frz, c.rz, coverT) : frz;
            const s  = c ? lerp(fs,  c.s,  coverT) : fs;

            el.style.transform =
                `translate3d(${x}px, ${y}px, ${z}px) ` +
                `rotateY(${ry}deg) rotateZ(${rz}deg) ` +
                `rotateX(${(1 - e) * 6 * (1 - coverT)}deg) ` +
                `scale(${s})`;

            /* A CUSTOM PROPERTY, NOT style.opacity. The dim has to be
               overridable by a media query — css/09-scenes.css pins
               --slot-dim to 1 under prefers-reduced-motion — and an inline
               opacity would win against any stylesheet rule that tried. */
            if (c) el.style.setProperty('--slot-dim', String(lerp(1, c.dim, coverT)));
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
        host.classList.remove('is-live');
        for (const { el } of items) {
            el.style.transform = '';
            el.style.removeProperty('--slot-dim');
        }
    };
}

export function initDecks() {
    const kill = [];

    const deck = document.querySelector('[data-deck]');
    if (deck) {
        // Centre, then the inner pair, then the outer pair — the DOM order is
        // the reading order of the five services, this is where each lands.
        const d = makeDeck(deck.closest('section'), deck, FAN, [0, 1, 2, 3, 4], COVER);
        if (d) kill.push(d);
    }

    /* The phones get no collapse. Three shells overlapping would hide two of
       them, and unlike the browser deck there is no argument being made about
       consolidation here — it is one app shown from three angles. */
    const phones = document.querySelector('[data-phones]');
    if (phones) {
        const p = makeDeck(phones.closest('section'), phones, PHONE_FAN, [0, 1, 2], null);
        if (p) kill.push(p);
    }

    return () => kill.forEach((fn) => fn());
}
