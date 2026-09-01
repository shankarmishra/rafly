/**
 * gallery.js — 3D Spatial Card Deck Engine & Editorial Experience
 *
 * WHAT WE BUILD // FIVE CORE CAPABILITIES // ONE CONNECTED SYSTEM
 *
 * Choreography:
 *   - Continuous scroll progress normalized to [0.00, 1.00]
 *   - Smooth lerp inertia for fluid 60FPS spatial motion
 *   - 3D perspective transformations (translateX, translateZ, rotateY, scale, blur, opacity)
 *   - Mouse-driven micro parallax on active card
 *   - Staggered word-by-word headline entrance
 *   - Integrated progress indicator & tab navigation
 */

const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const lerp = (a, b, n) => a + (b - a) * n;
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
const easeInOutCubic = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

export function initGallery(root) {
    if (!root) return null;

    const section = root.closest('section') || document.getElementById('build');
    if (!section) return null;

    const cards = [...root.querySelectorAll('.build-spatial-card, [data-slot]')];
    const navTabs = [...section.querySelectorAll('[data-build-tab]')];
    const counterEl = section.querySelector('[data-bpi-counter]');
    const progressBar = section.querySelector('[data-bpi-bar]');
    const stepCounter = section.querySelector('[data-build-step]');
    const headlineWords = [...section.querySelectorAll('[data-build-heading] .bh-word')];
    const bgGrid = section.querySelector('.build-bg-grid');
    const ambientOrb1 = section.querySelector('.build-ambient-orb-1');

    if (cards.length < 2) return null;
    const n = cards.length;

    let easedProgress = 0;
    let targetProgress = 0;
    let activeIdx = 0;
    let raf = 0;
    let running = false;
    let mouseX = 0, mouseY = 0;
    let targetMouseX = 0, targetMouseY = 0;
    let headlineRevealed = false;

    // --- 1. Headline Staggered Word Reveal ---
    function revealHeadline() {
        if (headlineRevealed) return;
        headlineRevealed = true;

        headlineWords.forEach((word, idx) => {
            word.style.opacity = '0';
            word.style.transform = 'translateY(28px)';
            word.style.filter = 'blur(6px)';

            setTimeout(() => {
                word.style.transition = 'transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.65s ease, filter 0.65s ease';
                word.style.opacity = '1';
                word.style.transform = 'translateY(0)';
                word.style.filter = 'blur(0)';
            }, 80 + idx * 45);
        });
    }

    // --- 2. Click Navigation ---
    function scrollToIndex(idx) {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        if (span <= 0) return;
        const top = r.top + window.scrollY;
        const targetRatio = clamp01(idx / (n - 1));
        window.scrollTo({
            top: top + span * targetRatio,
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
        });
    }

    cards.forEach((card, idx) => {
        card.addEventListener('click', () => scrollToIndex(idx));
    });

    navTabs.forEach((tab, idx) => {
        tab.addEventListener('click', () => scrollToIndex(idx));
    });

    // --- 3. Mouse Parallax on Desktop ---
    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;
    if (!isTouch) {
        window.addEventListener('mousemove', (e) => {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            targetMouseX = (e.clientX - cx) / cx;
            targetMouseY = (e.clientY - cy) / cy;
        }, { passive: true });
    }

    // --- 4. Spatial 3D Deck Placement & Geometry ---
    function render(p) {
        const isMobile = window.innerWidth <= 768;
        const isReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (isReduced || isMobile) {
            // Static or standard stacked presentation
            cards.forEach((c, i) => {
                c.classList.toggle('is-active', i === Math.round(p * (n - 1)));
            });
            return;
        }

        // Fractional position across the deck
        const fIdx = p * (n - 1);
        const currentActive = Math.min(n - 1, Math.max(0, Math.round(fIdx)));

        if (currentActive !== activeIdx) {
            activeIdx = currentActive;

            // Update tab highlights
            navTabs.forEach((tab, i) => {
                const isActive = i === activeIdx;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            // Update step badge & counter
            if (stepCounter) {
                stepCounter.textContent = `0${activeIdx + 1}`;
            }
            if (counterEl) {
                counterEl.textContent = `0${activeIdx + 1} / 0${n}`;
            }

            // Update active glow accent
            const activeAcc = cards[activeIdx]?.style.getPropertyValue('--card-acc') || 'var(--blue)';
            section.style.setProperty('--active-build-glow', activeAcc);
        }

        // Update progress bar width
        if (progressBar) {
            progressBar.style.width = `${Math.max(15, (p * 100))}%`;
        }

        // Background grid micro-parallax
        if (bgGrid) {
            bgGrid.style.transform = `translateY(${(p * 14).toFixed(1)}px)`;
        }

        // Interpolate mouse parallax
        mouseX = lerp(mouseX, targetMouseX, 0.08);
        mouseY = lerp(mouseY, targetMouseY, 0.08);

        // Position all 5 cards in 3D perspective space
        cards.forEach((card, i) => {
            const rel = i - fIdx;
            const az = Math.abs(rel);
            const isActive = az < 0.45;

            card.classList.toggle('is-active', isActive);

            // Compute spatial transforms
            // Desktop horizontal displacement: 52% of card width
            const xPercent = rel * 52;
            const zDistance = -az * 90;
            const yRotation = rel * -10;
            const cardScale = Math.max(0.74, 1 - az * 0.11);
            const cardOpacity = Math.max(0.25, 1 - az * 0.38);
            const cardBlur = Math.min(3.5, az * 1.6);
            const zIndexVal = Math.round(10 - az * 2);

            // Mouse parallax tilt for active/near-active cards
            const tiltY = isActive ? (mouseX * 2.5) : 0;
            const tiltX = isActive ? (mouseY * -1.8) : 0;

            card.style.transform = `translate3d(calc(-50% + ${xPercent.toFixed(1)}%), -50%, ${zDistance.toFixed(1)}px) rotateY(${(yRotation + tiltY).toFixed(2)}deg) rotateX(${tiltX.toFixed(2)}deg) scale(${cardScale.toFixed(3)})`;
            card.style.opacity = cardOpacity.toFixed(3);
            card.style.filter = cardBlur > 0.3 ? `blur(${cardBlur.toFixed(1)}px)` : 'none';
            card.style.zIndex = String(zIndexVal);
            card.style.pointerEvents = az < 1.1 ? 'auto' : 'none';
        });
    }

    // --- 5. Main rAF Scroll Loop ---
    function frame() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        const raw = span > 0 ? clamp01(-r.top / span) : 0;
        targetProgress = raw;

        easedProgress = lerp(easedProgress, targetProgress, 0.10);
        render(easedProgress);

        raf = requestAnimationFrame(frame);
    }

    const start = () => {
        if (!running) {
            running = true;
            raf = requestAnimationFrame(frame);
        }
    };

    const stop = () => {
        running = false;
        cancelAnimationFrame(raf);
    };

    // --- 6. Intersection Observer ---
    const io = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            revealHeadline();
            start();
        } else {
            stop();
        }
    }, { rootMargin: '20% 0px' });

    io.observe(section);

    const onVisibility = () => {
        if (document.hidden) stop();
        else start();
    };
    document.addEventListener('visibilitychange', onVisibility);

    // Initial render
    frame();
    root.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        root.classList.remove('is-live');
    };
}
