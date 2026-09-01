/**
 * build-system.js — "Five ways we build. One system behind them."
 *
 * Cinematic Scroll-Driven Progressive Assembly:
 *   - 0-15%: Section entry, headline reveal, canvas scale (0.96 -> 1.0)
 *   - 15-30%: 01 / WEB — Build the experience
 *   - 30-45%: 02 / SECURITY — Protect the foundation
 *   - 45-60%: 03 / MARKETING — Create demand
 *   - 60-75%: 04 / CONTENT — Shape the story
 *   - 75-90%: 05 / COMMERCE — Turn intent into revenue
 *   - 90-100%: Synchronized payoff — ONE SYSTEM. BUILT TO COMPOUND.
 *
 * Micro-interactions:
 *   - Damped multi-depth pointer parallax
 *   - Interactive rail click to scrub directly to layer
 *   - IntersectionObserver pause when off-screen
 *   - Full support for prefers-reduced-motion
 */

const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const lerp = (a, b, n) => a + (b - a) * n;

export function initBuildSystem(host) {
    if (!host) return () => {};

    const section = host.closest('section') || host;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;

    // DOM Elements
    const canvas = section.querySelector('[data-bs-canvas]');
    const layerWeb = section.querySelector('.bs-layer-web');
    const layerSecurity = section.querySelector('.bs-layer-security');
    const layerMarketing = section.querySelector('.bs-layer-marketing');
    const layerContent = section.querySelector('.bs-layer-content');
    const layerCommerce = section.querySelector('.bs-layer-commerce');
    const layerCompound = section.querySelector('.bs-layer-compound');
    const railItems = [...section.querySelectorAll('.bs-rail-item')];
    const statusText = section.querySelector('[data-bs-status]');
    const headlineInners = [...section.querySelectorAll('.bs-h-inner')];
    const bgGrid = section.querySelector('.bs-bg-grid');
    const bgGlow = section.querySelector('.bs-bg-glow');

    const layerMap = [
        { key: 'web', threshold: 0.15, elem: layerWeb, name: '01 // WEB' },
        { key: 'security', threshold: 0.30, elem: layerSecurity, name: '02 // SECURITY' },
        { key: 'marketing', threshold: 0.45, elem: layerMarketing, name: '03 // MARKETING' },
        { key: 'content', threshold: 0.60, elem: layerContent, name: '04 // CONTENT' },
        { key: 'commerce', threshold: 0.75, elem: layerCommerce, name: '05 // COMMERCE' }
    ];

    let easedProgress = 0;
    let targetProgress = 0;
    let mouseX = 0, mouseY = 0;
    let targetMouseX = 0, targetMouseY = 0;
    let currentActiveIdx = -1;
    let headlineRevealed = false;
    let running = false;
    let rafId = 0;

    // --- 1. Headline Mask Reveal ---
    function revealHeadline() {
        if (headlineRevealed) return;
        headlineRevealed = true;
        headlineInners.forEach((el, idx) => {
            setTimeout(() => {
                el.style.transform = 'translateY(0)';
                el.style.opacity = '1';
            }, 80 + idx * 120);
        });
    }

    // --- 2. 3D Pointer Parallax ---
    if (!isTouch && !reduced) {
        window.addEventListener('mousemove', (e) => {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            targetMouseX = (e.clientX - cx) / cx;
            targetMouseY = (e.clientY - cy) / cy;
        }, { passive: true });
    }

    // --- 3. Click Navigation Rail ---
    railItems.forEach((btn, idx) => {
        btn.addEventListener('click', () => {
            const rect = section.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const span = rect.height - window.innerHeight;
            const targetP = 0.20 + idx * 0.15;
            const destY = scrollTop + rect.top + span * targetP;

            window.scrollTo({
                top: destY,
                behavior: 'smooth'
            });
        });
    });

    // --- 4. Render Choreography ---
    function render(p) {
        const isMobile = window.innerWidth <= 768;

        if (reduced || isMobile) {
            // Simplified active states under reduced motion or mobile
            if (canvas) {
                canvas.style.transform = 'none';
                canvas.style.opacity = '1';
            }
            layerMap.forEach((l) => {
                if (l.elem) l.elem.classList.add('is-active');
            });
            if (layerCompound) layerCompound.classList.add('is-active');
            return;
        }

        // Pointer damping
        mouseX = lerp(mouseX, targetMouseX, 0.05);
        mouseY = lerp(mouseY, targetMouseY, 0.05);

        // Background subtle displacement
        if (bgGrid) {
            bgGrid.style.transform = `translate3d(${(mouseX * 6).toFixed(1)}px, ${(mouseY * 6).toFixed(1)}px, 0)`;
        }
        if (bgGlow) {
            bgGlow.style.transform = `translate3d(${(mouseX * -10).toFixed(1)}px, ${(mouseY * -10).toFixed(1)}px, 0)`;
        }

        // Canvas Scale & Entry (0 - 15%)
        const entryFactor = clamp01(p / 0.15);
        const canvasScale = (0.96 + entryFactor * 0.04).toFixed(3);
        const canvasOpacity = (0.2 + entryFactor * 0.8).toFixed(2);
        const tiltX = (-mouseY * 3.5).toFixed(1);
        const tiltY = (mouseX * 4.5).toFixed(1);

        if (canvas) {
            canvas.style.transform = `scale(${canvasScale}) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
            canvas.style.opacity = canvasOpacity;
        }

        // Active Layer Progressive Assembly
        let activeIdx = 0;
        if (p >= 0.90) activeIdx = 5; // Synchronized state
        else if (p >= 0.75) activeIdx = 4;
        else if (p >= 0.60) activeIdx = 3;
        else if (p >= 0.45) activeIdx = 2;
        else if (p >= 0.30) activeIdx = 1;
        else if (p >= 0.15) activeIdx = 0;

        // Update layers presence
        layerMap.forEach((l, idx) => {
            if (!l.elem) return;
            const isVisible = p >= l.threshold;
            const isCurrentlyActive = idx === activeIdx && activeIdx < 5;

            l.elem.classList.toggle('is-visible', isVisible);
            l.elem.classList.toggle('is-active', isCurrentlyActive);
            l.elem.classList.toggle('is-base', idx < activeIdx);
        });

        // Payoff Layer (90% -> 100%)
        if (layerCompound) {
            const isCompounding = p >= 0.90;
            layerCompound.classList.toggle('is-active', isCompounding);
            if (canvas) {
                canvas.classList.toggle('is-synchronized', isCompounding);
            }
        }

        // Update Navigation Rail
        if (activeIdx !== currentActiveIdx) {
            currentActiveIdx = activeIdx;
            railItems.forEach((btn, idx) => {
                btn.classList.toggle('is-active', idx === activeIdx || (activeIdx === 5 && idx === 4));
            });

            if (statusText) {
                if (activeIdx === 5) {
                    statusText.textContent = 'SYNCHRONIZED // 100%';
                    statusText.style.color = '#10b981';
                } else {
                    statusText.textContent = layerMap[activeIdx] ? layerMap[activeIdx].name : '01 // WEB';
                    statusText.style.color = '';
                }
            }
        }
    }

    // --- 5. Main rAF Scroll Loop ---
    function frame() {
        const rect = section.getBoundingClientRect();
        const span = rect.height - window.innerHeight;
        const raw = span > 0 ? clamp01(-rect.top / span) : 0;
        targetProgress = raw;

        easedProgress = lerp(easedProgress, targetProgress, 0.08);
        render(easedProgress);

        rafId = requestAnimationFrame(frame);
    }

    const start = () => {
        if (!running) {
            running = true;
            rafId = requestAnimationFrame(frame);
        }
    };

    const stop = () => {
        running = false;
        cancelAnimationFrame(rafId);
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

    // Initial frame
    frame();
    host.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        host.classList.remove('is-live');
    };
}
