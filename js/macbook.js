/**
 * macbook.js — a laptop that opens as you scroll, with our own screen in it.
 *
 * Naveen sent Aceternity's MacbookScroll and asked for the same move with our
 * content rather than theirs. This is that: the lid is hinged at its bottom
 * edge and rotates up from shut to open across the section, while the whole
 * rig comes forward. Nothing else on the page does this — the deck fans, the
 * gallery slides, the phones sit still — so it earns its place by being a
 * different verb rather than a fourth arrangement of cards.
 *
 * TWO NUMBERS, WRITTEN AS CUSTOM PROPERTIES, AND THAT IS THE WHOLE MODULE:
 *
 *   --lid   degrees the lid is open. 0 is shut and lying on the base; 90 is
 *           upright. It rests just past upright because a real laptop does.
 *   --rig   0 to 1, how far the whole assembly has come forward. CSS turns it
 *           into scale and a little translate.
 *
 * Everything else — the perspective, the hinge origin, the screen, the
 * keyboard — is CSS, which is what lets the still form be exact: the
 * stylesheet declares its own --lid and --rig, so with no JavaScript the
 * laptop stands open at a designed angle instead of shut or missing.
 *
 * PROGRESS COMES FROM THE SECTION'S OWN RECT, like js/decks.js and
 * js/gallery.js. A module that reads window.scrollY and subtracts a constant
 * is a module that desyncs the first time a paragraph above it is edited.
 */

const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const lerp = (a, b, n) => a + (b - a) * n;
const easeOut = (t) => 1 - Math.pow(1 - t, 3);

export function initMacbook(root) {
    if (!root) return null;
    const section = root.closest('section');
    if (!section) return null;

    let eased = 0;
    let raf = 0, running = false;

    function frame() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        const raw = span > 0 ? clamp01(-r.top / span) : 1;
        eased = lerp(eased, raw, 0.09);

        /* The lid is done at 62% of the section, so the last third is the
           laptop OPEN and readable rather than still opening. A device that
           finishes its move exactly as it leaves the screen was never actually
           shown to anyone. */
        const open = easeOut(clamp01(eased / 0.62));

        /* Just past upright: 94 degrees, not 90. A lid at exactly 90 reads as
           a diagram; every real laptop leans back a little. */
        root.style.setProperty('--lid', String(open * 94));
        root.style.setProperty('--rig', String(easeOut(clamp01(eased / 0.8))));

        raf = requestAnimationFrame(frame);
    }

    const start = () => { if (!running) { running = true; raf = requestAnimationFrame(frame); } };
    const stop = () => { running = false; cancelAnimationFrame(raf); };

    const io = new IntersectionObserver(
        ([entry]) => (entry.isIntersecting ? start() : stop()),
        { rootMargin: '20% 0px' }
    );
    io.observe(section);

    const onVisibility = () => { document.hidden ? stop() : start(); };
    document.addEventListener('visibilitychange', onVisibility);

    root.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        root.classList.remove('is-live');
        root.style.removeProperty('--lid');
        root.style.removeProperty('--rig');
    };
}
