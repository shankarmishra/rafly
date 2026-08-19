/**
 * gallery.js — the coverflow, driven by the page's own scroll.
 *
 * Naveen sent OriginKit's component source and asked for the same thing here,
 * "scroll se animated". The reference advances on click and on an autoplay
 * timer; this advances on scroll depth, which is a better fit for a page
 * section than a carousel that runs whether or not anyone is looking at it.
 *
 * ONE SOURCE OF TRUTH, AND IT IS THE SCROLL POSITION. The obvious way to add
 * click-to-centre is a second piece of state, and then two things own "which
 * card is active" and they disagree the moment you click and then scroll. So a
 * click does not set the index — it SCROLLS THE PAGE to the depth at which
 * that card is centred, and the scroll handler does what it always does. The
 * dots work the same way. Nothing here can desync, because there is only one
 * number.
 *
 * ALL THE GEOMETRY IS IN CSS. This writes two custom properties per card and
 * nothing else: --rel, the signed offset from the centre, and --az, its
 * absolute value. css/09-scenes.css turns those into the translate, the two
 * rotations, the scale and the veil, at OriginKit's own numbers. That split is
 * what lets the still form be real rather than approximate: the stylesheet
 * sets --rel per data-slot, so with no JavaScript the coverflow is laid out
 * exactly as it would be with the first card active.
 *
 * The wrap is shortest-path — with five cards, index 4 sits at rel -1 rather
 * than +4, so the far card slides in from the near side instead of travelling
 * across the whole deck.
 */

const clamp = (v, a, b) => (v < a ? a : v > b ? b : v);

export function initGallery(root) {
    if (!root) return null;

    const section = root.closest('section');
    if (!section) return null;

    const cards = [...root.querySelectorAll('[data-slot]')];
    const dots = [...section.querySelectorAll('[data-cf-dot]')];
    if (cards.length < 2) return null;

    const n = cards.length;
    let active = -1;
    let raf = 0, running = false;

    function place(next) {
        if (next === active) return;
        active = next;

        for (let i = 0; i < n; i++) {
            // Shortest way round the loop, so nothing ever travels the long
            // way across the deck to get to the position next door.
            let rel = i - active;
            if (rel > n / 2) rel -= n;
            if (rel < -n / 2) rel += n;

            cards[i].style.setProperty('--rel', String(rel));
            cards[i].style.setProperty('--az', String(Math.abs(rel)));
        }

        for (let i = 0; i < dots.length; i++) {
            dots[i].setAttribute('aria-current', i === active ? 'true' : 'false');
        }
    }

    /* Scroll depth -> index. The first and last cards get half a step of range
       each so the deck arrives already showing card one and leaves still
       showing card five, rather than snapping at both ends. */
    function indexFromScroll() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        if (span <= 0) return 0;
        const p = clamp(-r.top / span, 0, 1);
        return clamp(Math.round(p * (n - 1)), 0, n - 1);
    }

    function frame() {
        place(indexFromScroll());
        raf = requestAnimationFrame(frame);
    }

    const start = () => { if (!running) { running = true; raf = requestAnimationFrame(frame); } };
    const stop = () => { running = false; cancelAnimationFrame(raf); };

    /* The inverse of indexFromScroll: where the page has to be for card i to
       be the centre one. Used by every click and every dot, so a click can
       never put the deck somewhere the scroll position does not agree with. */
    function scrollToIndex(i) {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        if (span <= 0) return;
        const top = r.top + window.scrollY;
        window.scrollTo({
            top: top + span * (clamp(i, 0, n - 1) / (n - 1)),
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
        });
    }

    const onCard = (e) => {
        const card = e.currentTarget;
        scrollToIndex(cards.indexOf(card));
    };
    cards.forEach((c) => c.addEventListener('click', onCard));

    const onDot = (e) => scrollToIndex(dots.indexOf(e.currentTarget));
    dots.forEach((d) => d.addEventListener('click', onDot));

    const io = new IntersectionObserver(
        ([entry]) => (entry.isIntersecting ? start() : stop()),
        { rootMargin: '20% 0px' }
    );
    io.observe(section);

    const onVisibility = () => { document.hidden ? stop() : start(); };
    document.addEventListener('visibilitychange', onVisibility);

    place(indexFromScroll());
    root.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        cards.forEach((c) => c.removeEventListener('click', onCard));
        dots.forEach((d) => d.removeEventListener('click', onDot));
        root.classList.remove('is-live');
        cards.forEach((c) => {
            c.style.removeProperty('--rel');
            c.style.removeProperty('--az');
        });
    };
}
