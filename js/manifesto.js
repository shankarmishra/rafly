/**
 * manifesto.js — The Difference / Manifesto interactive scene
 *
 * Art Direction: Apple editorial × high-end digital systems studio.
 *
 * Responsibilities:
 *   • Dynamic scroll-triggered word-by-word reveal (triggers on actual scroll into view)
 *   • Scroll-depth interpolation: status meter, ambient light drift, topology parallax
 *   • Subtle pointer parallax on desktop
 *   • Resets cleanly when scrolled away so the reveal re-engages on return
 *   • High-performance compositor transforms (60fps)
 *   • Respects prefers-reduced-motion
 */

const lerp = (a, b, n) => a + (b - a) * n;

export function initManifesto(host) {
    if (!host) return () => {};

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    // Elements
    const ambientEl = host.querySelector('.manifesto-ambient');
    const systemEl = host.querySelector('[data-manifesto-system]');
    const particlesEl = host.querySelector('[data-manifesto-particles]');
    const meterBar = host.querySelector('.status-meter-bar');
    const headline = host.querySelector('.manifesto-headline');
    const empGradient = host.querySelector('.m-emp-gradient');

    if (reduced) {
        host.classList.add('is-in', 'is-revealed');
        host.style.setProperty('--m-scroll', '1');
        if (meterBar) meterBar.style.width = '100%';
        return () => {};
    }

    let isVisible = false;
    let isRevealed = false;
    let rafId = 0;
    let running = false;

    // Pointer coordinates
    let targetMx = 0, targetMy = 0;
    let currMx = 0, currMy = 0;

    // Scroll progress (0 to 1 across viewport traversal)
    let targetScrollP = 0;
    let currScrollP = 0;

    function calculateScroll() {
        const rect = host.getBoundingClientRect();
        const vh = window.innerHeight || 800;

        // Progress from 0 (top entering bottom) to 1 (bottom leaving top)
        const total = rect.height + vh;
        const current = vh - rect.top;
        const progress = Math.max(0, Math.min(1, current / total));
        targetScrollP = progress;

        // Target headline element position specifically for trigger calculation
        const headlineEl = headline || host.querySelector('.manifesto-headline');
        const hRect = headlineEl ? headlineEl.getBoundingClientRect() : rect;

        // Trigger reveal ONLY when the headline is actually scrolled into view
        // (headline top enters upper 70% of viewport and is still above bottom 10%)
        const inViewTrigger = hRect.top < vh * 0.70 && hRect.bottom > vh * 0.10;

        if (inViewTrigger && !isRevealed) {
            isRevealed = true;
            host.classList.add('is-revealed');
        } else if (hRect.top > vh * 0.88 || rect.bottom < 0) {
            // Reset cleanly when scrolled back up above headline or completely past section
            if (isRevealed) {
                isRevealed = false;
                host.classList.remove('is-revealed');
            }
        }
    }

    function onPointerMove(e) {
        if (!isVisible || !isFinePointer) return;
        const rect = host.getBoundingClientRect();
        if (e.clientY < rect.top - 80 || e.clientY > rect.bottom + 80) return;

        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;

        targetMx = Math.max(-0.5, Math.min(0.5, x));
        targetMy = Math.max(-0.5, Math.min(0.5, y));
    }

    function onPointerLeave() {
        targetMx = 0;
        targetMy = 0;
    }

    function frame() {
        if (!running) return;

        // Smooth damping
        currMx = lerp(currMx, targetMx, 0.05);
        currMy = lerp(currMy, targetMy, 0.05);
        currScrollP = lerp(currScrollP, targetScrollP, 0.09);

        host.style.setProperty('--m-scroll', currScrollP.toFixed(4));

        // Ambient glow parallax
        if (ambientEl) {
            const gx = (currMx * 32).toFixed(2);
            const gy = (currMy * 24 + (currScrollP - 0.5) * -16).toFixed(2);
            ambientEl.style.transform = `translate3d(${gx}px, ${gy}px, 0)`;
        }

        // System Network topology parallax
        if (systemEl) {
            const sx = (currMx * 12).toFixed(2);
            const sy = (currMy * 9 + (currScrollP - 0.5) * -28).toFixed(2);
            systemEl.style.transform = `translate3d(${sx}px, ${sy}px, 0)`;
        }

        // Micro particles parallax
        if (particlesEl) {
            const px = (currMx * 18).toFixed(2);
            const py = (currMy * 14 + (currScrollP - 0.5) * -38).toFixed(2);
            particlesEl.style.transform = `translate3d(${px}px, ${py}px, 0)`;
        }

        // Headline slight spatial lift
        if (headline) {
            const hScale = (0.98 + currScrollP * 0.02).toFixed(4);
            const hTy = ((currScrollP - 0.5) * -8).toFixed(2);
            headline.style.transform = `translate3d(0, ${hTy}px, 0) scale(${hScale})`;
        }

        // Living gradient sweep sync with scroll
        if (empGradient) {
            const bgPos = (currScrollP * 120).toFixed(1);
            empGradient.style.backgroundPosition = `${bgPos}% 50%`;
        }

        // Status meter bar width tracking
        if (meterBar) {
            const meterPct = Math.min(100, Math.max(6, currScrollP * 100));
            meterBar.style.width = `${meterPct.toFixed(1)}%`;
        }

        rafId = requestAnimationFrame(frame);
    }

    function start() {
        if (!running) {
            running = true;
            rafId = requestAnimationFrame(frame);
        }
    }

    function stop() {
        running = false;
        cancelAnimationFrame(rafId);
    }

    // Viewport Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            isVisible = entry.isIntersecting;
            if (entry.isIntersecting) {
                host.classList.add('is-in');
                calculateScroll();
                start();
            } else {
                stop();
            }
        });
    }, { threshold: [0, 0.1, 0.25, 0.5, 0.75, 1.0], rootMargin: '40px 0px' });

    observer.observe(host);

    // Scroll listener
    const onScroll = () => {
        if (isVisible) calculateScroll();
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('mousemove', onPointerMove, { passive: true });
    host.addEventListener('mouseleave', onPointerLeave, { passive: true });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stop();
        else if (isVisible) start();
    });

    calculateScroll();

    return function destroy() {
        stop();
        observer.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('mousemove', onPointerMove);
        host.removeEventListener('mouseleave', onPointerLeave);
        host.classList.remove('is-in', 'is-revealed');
    };
}
