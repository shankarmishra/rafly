/**
 * cluster.js — the hero's floating glass cards.
 *
 * WHAT THIS REPLACED, AND WHY IT MATTERS. Three 3-D objects were built for
 * this slot and all three were rejected on sight: a point cloud, a glass
 * torus knot, and a machined assembly. The knot in particular is the default
 * object of every WebGL demo, and it said nothing about what this company
 * sells. What is here now is a cluster of the actual product surfaces — a
 * delivery status, a traffic chart, a security scan, an uptime strip —
 * arranged around the headline rather than behind it, so the composition has
 * an empty middle and the type needs no scrim to stay readable.
 *
 * THE MARKUP LIVES IN THE PAGE, NOT IN THIS FILE. index.php renders the cards
 * as real elements. This module only moves them. That is the difference
 * between a hero that degrades to a designed still and one that degrades to
 * an empty div: with no JS the cards are simply there, static, in their
 * final positions.
 *
 * Three motions compose, in this order:
 *   float     a slow sine per card, phase-offset, so the group breathes
 *   parallax  the pointer, damped, with depth-proportional travel
 *   drift     scroll, so the cluster leaves the viewport slower than the copy
 *
 * All three are pure transform. Nothing here triggers layout.
 */

const lerp = (a, b, n) => a + (b - a) * n;

export function initCluster(host) {
    const cards = [...host.querySelectorAll('[data-depth]')];
    if (!cards.length) return () => {};

    let mx = 0, my = 0, cx = 0, cy = 0;
    let scrollTarget = window.scrollY, scrollEased = window.scrollY;
    let raf = 0, running = false;
    const t0 = performance.now();

    const onMove = (e) => {
        mx = e.clientX / window.innerWidth - 0.5;
        my = e.clientY / window.innerHeight - 0.5;
    };
    const onScroll = () => { scrollTarget = window.scrollY; };

    function frame(now) {
        const t = ((now || performance.now()) - t0) / 1000;

        scrollEased = lerp(scrollEased, scrollTarget, 0.08);
        cx = lerp(cx, mx, 0.05);
        cy = lerp(cy, my, 0.05);

        cards.forEach((el, i) => {
            const d = +el.dataset.depth || 0;
            const float = Math.sin(t * 0.62 + i * 1.25) * 11;
            const px = cx * (26 + d * 0.16);
            const py = cy * (20 + d * 0.10);
            const drift = scrollEased * (0.05 + i * 0.018);
            el.style.transform =
                `translate3d(${px}px, ${float + py + drift}px, ${d}px) ` +
                `rotateY(${cx * 7}deg) rotateX(${-cy * 6}deg)`;
        });

        raf = requestAnimationFrame(frame);
    }

    const start = () => { if (!running) { running = true; raf = requestAnimationFrame(frame); } };
    const stop = () => { running = false; cancelAnimationFrame(raf); };
    const onVisibility = () => { document.hidden ? stop() : start(); };

    window.addEventListener('mousemove', onMove, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);

    // The cards are already in place and visible; this only adds the motion,
    // and the staggered entrance that makes the group feel assembled rather
    // than pasted.
    host.classList.add('is-live');
    cards.forEach((el, i) => { el.style.transitionDelay = (i * 130) + 'ms'; });

    start();

    return function destroy() {
        stop();
        window.removeEventListener('mousemove', onMove);
        window.removeEventListener('scroll', onScroll);
        document.removeEventListener('visibilitychange', onVisibility);
        host.classList.remove('is-live');
        cards.forEach((el) => { el.style.transform = ''; });
    };
}
