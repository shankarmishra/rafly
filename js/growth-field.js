/**
 * growth-field.js — The RAFly Growth Field
 *
 * THE GROWTH FIELD: One living architectural environment.
 * Five capability zones. BUILD → PROTECT → CREATE → CONVERT → COMPOUND
 *
 * Motion System:
 *  - Scroll-driven progressive zone assembly (staggered by capability threshold)
 *  - Kinetic oversized background typography gliding on scroll
 *  - Subtle 2–3° CSS 3D slab tilt parallax on cursor
 *  - Zone hover interactions: depth lift, dim siblings, reveal signal line
 *  - Security perimeter activation on scroll (30–45%)
 *  - Master electric blue pulse at section convergence (90–100%)
 *  - Capability rail auto-syncs to scroll position, clickable
 *
 * Performance:
 *  - All transforms are GPU-composited (transform/opacity only)
 *  - IntersectionObserver gates the rAF loop
 *  - Pauses on visibilitychange and outside viewport
 *  - Fully respects prefers-reduced-motion
 */

/* ─── Utilities ──────────────────────────────────────────── */
const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const lerp  = (a, b, n)   => a + (b - a) * n;
const ease  = (t)          => t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;

const ZONE_ORDER    = ['web', 'security', 'marketing', 'content', 'commerce'];
const ZONE_THRESHOLDS = { web: 0.15, security: 0.32, marketing: 0.48, content: 0.63, commerce: 0.78 };

export function initGrowthField(host) {
    if (!host) return () => {};

    const reduced  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = () => window.innerWidth <= 768;

    /* ─── DOM ────────────────────────────────────────────── */
    const slab       = host.querySelector('[data-gf-slab]');
    const chassis    = slab?.querySelector('.gf-slab-chassis');
    const perimeter  = host.querySelector('[data-gf-perimeter]');
    const pulse      = host.querySelector('[data-gf-pulse]');
    const kineticEl  = host.querySelector('[data-gf-kinetic]');
    const lightSweep = host.querySelector('.gf-light-sweep');
    const zones      = [...host.querySelectorAll('[data-zone]')];
    const railTabs   = [...host.querySelectorAll('[data-gf-target]')];
    const signalLines= [...host.querySelectorAll('[data-signal]')];
    const headWords  = [...host.querySelectorAll('[data-gf-heading] .gfh-word')];

    /* ─── State ──────────────────────────────────────────── */
    let rafId       = 0;
    let running     = false;
    let progress    = 0;
    let easedProg   = 0;
    let mouseX      = 0, mouseY = 0;
    let tgtMouseX   = 0, tgtMouseY = 0;
    let activeZone  = 'web';
    let userPicked  = false;
    let pickTimer   = 0;
    let headlineDone= false;
    let pulseFired  = false;
    let perimActive = false;

    /* ─── Headline reveal ────────────────────────────────── */
    function revealHeadline() {
        if (headlineDone || reduced) return;
        headlineDone = true;
        headWords.forEach((w, i) => {
            w.style.opacity = '0';
            w.style.transform = 'translateY(22px)';
            w.style.transition = 'none';
            // Stagger each word
            setTimeout(() => {
                w.style.transition = 'opacity 0.65s cubic-bezier(0.16,1,0.3,1), transform 0.65s cubic-bezier(0.16,1,0.3,1)';
                w.style.opacity = '1';
                w.style.transform = 'translateY(0)';
            }, 80 + i * 55);
        });
    }

    /* ─── Cursor tracking ────────────────────────────────── */
    const onMove = (e) => {
        const cx = window.innerWidth  / 2;
        const cy = window.innerHeight / 2;
        tgtMouseX = (e.clientX - cx) / cx;  // -1 to +1
        tgtMouseY = (e.clientY - cy) / cy;
    };

    if (!reduced && !isMobile()) {
        window.addEventListener('mousemove', onMove, { passive: true });
    }

    /* ─── Rail interaction ───────────────────────────────── */
    function setActiveZone(key) {
        if (activeZone === key) return;
        activeZone = key;

        railTabs.forEach(btn => {
            const match = btn.dataset.gfTarget === key;
            btn.classList.toggle('is-active', match);
        });

        zones.forEach(z => {
            const match = z.dataset.zone === key;
            z.classList.toggle('is-dimmed', !match && !isMobile());
        });

        // Signal lines: activate matching
        signalLines.forEach(l => {
            const match = l.dataset.signal === key;
            l.classList.toggle('is-active', match);
        });
    }

    railTabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const key = btn.dataset.gfTarget;
            if (!key) return;
            userPicked = true;
            clearTimeout(pickTimer);
            setActiveZone(key);
            pickTimer = setTimeout(() => { userPicked = false; }, 5000);
        });
    });

    /* Zone hover */
    zones.forEach(z => {
        const key = z.dataset.zone;
        z.addEventListener('mouseenter', () => {
            userPicked = true;
            setActiveZone(key);
        });
        z.addEventListener('mouseleave', () => {
            userPicked = false;
            // Remove all dimming on leave
            zones.forEach(zz => zz.classList.remove('is-dimmed'));
        });
    });

    /* ─── Main render loop ───────────────────────────────── */
    function render() {
        const mobile = isMobile();

        // On mobile: just show all zones statically, nothing more
        if (mobile) {
            zones.forEach(z => {
                z.classList.add('is-visible');
                z.style.transform = '';
                z.style.opacity   = '';
                z.style.filter    = '';
            });
            if (!headlineDone) revealHeadline();
            return;
        }

        // Lerp mouse
        mouseX = lerp(mouseX, tgtMouseX, 0.055);
        mouseY = lerp(mouseY, tgtMouseY, 0.055);

        // Kinetic background typography — drifts leftward with scroll
        if (kineticEl) {
            const trackX = -progress * 280 + mouseX * 12;
            kineticEl.style.transform = `translateY(-50%) translateX(${trackX}px)`;
        }

        // Light sweep opacity on scroll (15–30%)
        if (lightSweep) {
            const sweepP = clamp((progress - 0.15) / 0.15, 0, 1);
            lightSweep.style.opacity = (sweepP * 0.75).toString();
            lightSweep.style.transform = `translateX(${sweepP * 20}%)`;
        }

        // Slab subtle 3D tilt (max 2.5°) + micro parallax
        if (chassis) {
            const tiltX = (-mouseY * 2.5).toFixed(2);
            const tiltY = ( mouseX * 2.2).toFixed(2);
            const shX   = (mouseX * 6).toFixed(1);
            const shY   = (mouseY * 4).toFixed(1);
            chassis.style.transform = `translate3d(${shX}px,${shY}px,0) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
        }

        // Progressive zone assembly
        ZONE_ORDER.forEach(key => {
            const z = zones.find(z => z.dataset.zone === key);
            if (!z) return;

            const thresh = ZONE_THRESHOLDS[key];
            const enterProgress = clamp((progress - (thresh - 0.12)) / 0.12, 0, 1);
            const eased = ease(enterProgress);

            if (enterProgress > 0.01) {
                z.classList.add('is-visible');
            }

            const transY = ((1 - eased) * 22).toFixed(1);
            const scl    = (0.95 + eased * 0.05).toFixed(3);
            const opa    = (0.2 + eased * 0.8).toFixed(2);

            // Don't override hover transforms
            if (!z.matches(':hover')) {
                z.style.transform = `translateY(${transY}px) scale(${scl})`;
                z.style.opacity   = opa;
            }
        });

        // Active zone by scroll (unless user picked)
        if (!userPicked) {
            let nextZone = 'web';
            if (progress >= ZONE_THRESHOLDS.commerce) nextZone = 'commerce';
            else if (progress >= ZONE_THRESHOLDS.content)   nextZone = 'content';
            else if (progress >= ZONE_THRESHOLDS.marketing) nextZone = 'marketing';
            else if (progress >= ZONE_THRESHOLDS.security)  nextZone = 'security';
            setActiveZone(nextZone);
        }

        // Security perimeter activation (30–45%)
        if (perimeter) {
            const perimP = clamp((progress - 0.30) / 0.15, 0, 1);
            if (perimP > 0.3 && !perimActive) {
                perimActive = true;
                perimeter.classList.add('is-active');
            } else if (perimP <= 0.1 && perimActive) {
                perimActive = false;
                perimeter.classList.remove('is-active');
            }
        }

        // Master pulse at 88–100%
        if (pulse && progress >= 0.88 && !pulseFired) {
            pulseFired = true;
            pulse.classList.add('is-pulsing');
            setTimeout(() => {
                pulse.classList.remove('is-pulsing');
            }, 1800);
        } else if (progress < 0.80) {
            pulseFired = false;
        }
    }

    /* ─── rAF scroll loop ────────────────────────────────── */
    function frame() {
        const r    = host.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        const raw  = span > 0 ? clamp(-r.top / span, 0, 1) : 0;
        easedProg  = lerp(easedProg, raw, 0.07);
        progress   = easedProg;

        render();
        rafId = requestAnimationFrame(frame);
    }

    const start = () => { if (!running) { running = true; rafId = requestAnimationFrame(frame); }};
    const stop  = () => { running = false; cancelAnimationFrame(rafId); };

    /* ─── IntersectionObserver gate ──────────────────────── */
    const io = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            revealHeadline();
            start();
        } else {
            stop();
        }
    }, { rootMargin: '25% 0px' });

    io.observe(host);

    document.addEventListener('visibilitychange', () => {
        document.hidden ? stop() : (running || start());
    });

    // Begin (first frame: render static baseline)
    frame();

    // On reduced motion: show everything immediately
    if (reduced) {
        zones.forEach(z => {
            z.classList.add('is-visible');
            z.style.opacity   = '1';
            z.style.transform = 'none';
        });
        signalLines.forEach(l => {
            l.style.opacity         = '0.35';
            l.style.strokeDashoffset = '0';
        });
        revealHeadline();
    }

    host.setAttribute('data-gf-live', '');

    return function destroy() {
        stop();
        io.disconnect();
        window.removeEventListener('mousemove', onMove);
        host.removeAttribute('data-gf-live');
    };
}
