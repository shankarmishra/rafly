/**
 * sheen.js — the cursor position, as two custom properties.
 *
 * That is the entire module. It writes --mx and --my on the element under the
 * pointer; css/09-scenes.css decides what to do with them, which right now is
 * one radial gradient on .svc-card.
 *
 * WHY IT IS A SEPARATE FILE AND NOT A LINE IN interactions.js: it is gated
 * differently. A sheen that follows a cursor is meaningless without a cursor,
 * so this is fetched only behind finePointer() — a phone downloads none of it,
 * and js/home.js is where that decision is visible.
 *
 * Both properties fall back to 50% in the stylesheet, so with this module
 * never loading the hover state is a centred glow instead of nothing. There is
 * no failure mode here that shows a blank card.
 *
 * One listener per card, passive, and it writes nothing but two custom
 * properties — no layout is read inside the handler except the card's own
 * rect, which is read per event rather than cached because a card can move
 * (the deck above it is 150vh of sticky) and a stale rect puts the highlight
 * somewhere the cursor is not.
 */

export function initSheen(selector = '[data-sheen]') {
    const cards = document.querySelectorAll(selector);
    if (!cards.length) return () => {};

    const kill = [];

    for (const el of cards) {
        const onMove = (e) => {
            const r = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${e.clientX - r.left}px`);
            el.style.setProperty('--my', `${e.clientY - r.top}px`);
        };

        /* Reset on leave. Without it the gradient is frozen wherever the
           pointer left the card, and the next hover starts from a stale
           position — a highlight that jumps before it follows. */
        const onLeave = () => {
            el.style.removeProperty('--mx');
            el.style.removeProperty('--my');
        };

        el.addEventListener('pointermove', onMove, { passive: true });
        el.addEventListener('pointerleave', onLeave, { passive: true });

        kill.push(() => {
            el.removeEventListener('pointermove', onMove);
            el.removeEventListener('pointerleave', onLeave);
            onLeave();
        });
    }

    return () => kill.forEach((fn) => fn());
}
