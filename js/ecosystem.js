/**
 * ecosystem.js — The RAFly System: Five Capabilities, One Living Growth Surface
 *
 * Art Direction: Apple × Linear × Stripe × Vercel × Futuristic Editorial
 *
 * Motion & Interaction System:
 *   - Scroll-driven progressive capability assembly (0% -> 100%)
 *   - Oversized kinetic typography gliding background track
 *   - Multilayer 3D cursor parallax with damped lerp physics
 *   - Interactive horizontal capability rail synchronized with the growth surface
 *   - Synchronized master electric-blue energy pulse at 90-100% convergence
 *   - Editorial word-by-word headline reveal
 *   - Full prefers-reduced-motion and mobile vertical adaptiveness
 */

const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const lerp = (a, b, n) => a + (b - a) * n;

export function initEcosystem(host) {
    if (!host) return () => {};

    const section = host.closest('section') || document.getElementById('services');
    if (!section) return () => {};

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;

    // DOM Elements
    const surfaceChassis = section.querySelector('.gs-outer-chassis');
    const energyPulse = section.querySelector('[data-growth-pulse]');
    const kineticTrack = section.querySelector('[data-kinetic-track]');
    const panels = [...section.querySelectorAll('[data-capability]')];
    const railItems = [...section.querySelectorAll('[data-rail-target]')];
    const headlineWords = [...section.querySelectorAll('[data-growth-heading] .gh-word')];
    const radialGlow = section.querySelector('.growth-radial-glow');

    const capabilities = ['web', 'security', 'marketing', 'content', 'commerce'];
    const thresholds = {
        web: 0.15,
        security: 0.35,
        marketing: 0.50,
        content: 0.65,
        commerce: 0.80
    };

    let easedProgress = 0;
    let targetProgress = 0;
    let mouseX = 0, mouseY = 0;
    let targetMouseX = 0, targetMouseY = 0;
    let activeCap = 'web';
    let userOverrideCap = null;
    let overrideTimer = 0;
    let headlineRevealed = false;
    let pulseFired = false;
    let running = false;
    let rafId = 0;

    // --- 1. Editorial Headline Word Reveal ---
    function revealHeadline() {
        if (headlineRevealed) return;
        headlineRevealed = true;

        headlineWords.forEach((word, idx) => {
            word.style.opacity = '0';
            word.style.transform = 'translateY(24px)';
            word.style.filter = 'blur(6px)';

            setTimeout(() => {
                word.style.transition = 'transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.65s ease, filter 0.65s ease';
                word.style.opacity = '1';
                word.style.transform = 'translateY(0)';
                word.style.filter = 'blur(0)';
            }, 60 + idx * 50);
        });
    }

    // --- 2. 3D Cursor Parallax Tracking ---
    if (!isTouch && !reduced) {
        window.addEventListener('mousemove', (e) => {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            targetMouseX = (e.clientX - cx) / cx;
            targetMouseY = (e.clientY - cy) / cy;
        }, { passive: true });
    }

    // --- 3. Capability Rail & Panel Interactions ---
    function setActiveCapability(capKey) {
        if (!capKey || activeCap === capKey) return;
        activeCap = capKey;

        // Update rail buttons
        railItems.forEach((btn) => {
            const match = btn.dataset.railTarget === capKey;
            btn.classList.toggle('is-active', match);
            btn.setAttribute('aria-selected', match ? 'true' : 'false');
        });

        // Update panels focus
        panels.forEach((p) => {
            const match = p.dataset.capability === capKey;
            p.classList.toggle('is-active-focus', match);
        });

        // Update ambient glow accent
        const targetPanel = panels.find((p) => p.dataset.capability === capKey);
        if (targetPanel && radialGlow) {
            const acc = targetPanel.style.getPropertyValue('--cap-acc') || '#0a63ff';
            section.style.setProperty('--active-growth-glow', acc);
        }
    }

    railItems.forEach((btn) => {
        btn.addEventListener('click', () => {
            const cap = btn.dataset.railTarget;
            if (!cap) return;
            userOverrideCap = cap;
            setActiveCapability(cap);

            clearTimeout(overrideTimer);
            overrideTimer = setTimeout(() => {
                userOverrideCap = null;
            }, 4000);
        });
    });

    panels.forEach((panel) => {
        const cap = panel.dataset.capability;
        if (!cap) return;

        panel.addEventListener('mouseenter', () => {
            userOverrideCap = cap;
            setActiveCapability(cap);
        });

        panel.addEventListener('mouseleave', () => {
            userOverrideCap = null;
        });
    });

    // --- 4. Main Render Choreography ---
    function render(p) {
        const isMobile = window.innerWidth <= 768;

        if (reduced || isMobile) {
            panels.forEach((p) => {
                p.style.opacity = '1';
                p.style.transform = 'none';
            });
            return;
        }

        // Interpolate mouse coordinates
        mouseX = lerp(mouseX, targetMouseX, 0.06);
        mouseY = lerp(mouseY, targetMouseY, 0.06);

        // Kinetic Background Typography Glide
        if (kineticTrack) {
            const trackShift = -p * 220;
            const trackParallax = mouseX * 8;
            kineticTrack.style.transform = `translateY(-50%) translate3d(${trackShift + trackParallax}px, 0, 0)`;
        }

        // Surface Chassis 3D Tilt Parallax (Max 3-4 degrees)
        if (surfaceChassis) {
            const rotY = (mouseX * 3.8).toFixed(2);
            const rotX = (-mouseY * 3.2).toFixed(2);
            const shiftX = (mouseX * 10).toFixed(1);
            const shiftY = (mouseY * 8).toFixed(1);
            const masterZ = p >= 0.88 ? 20 : 0;
            surfaceChassis.style.transform = `translate3d(${shiftX}px, ${shiftY}px, ${masterZ}px) rotateY(${rotY}deg) rotateX(${rotX}deg)`;
        }

        // Determine current active capability by scroll percentage unless user clicked
        if (!userOverrideCap) {
            if (p < 0.35) setActiveCapability('web');
            else if (p < 0.50) setActiveCapability('security');
            else if (p < 0.65) setActiveCapability('marketing');
            else if (p < 0.80) setActiveCapability('content');
            else setActiveCapability('commerce');
        }

        // Progressive Assembly for the 5 Capability Panels
        panels.forEach((panel) => {
            const key = panel.dataset.capability;
            const thresh = thresholds[key] || 0.2;
            const isHovered = activeCap === key;

            // Assembly Progress (0.0 to 1.0)
            const enterProgress = clamp01((p - (thresh - 0.15)) / 0.15);
            const easeEnter = Math.sin((enterProgress * Math.PI) / 2);

            // 3D Translation & Opacity
            const transY = ((1 - easeEnter) * 28).toFixed(1);
            const transZ = isHovered ? 16 : Math.round(easeEnter * 8);
            const scale = (0.94 + easeEnter * 0.06 * (isHovered ? 1.02 : 1)).toFixed(3);
            const opacity = (0.2 + easeEnter * 0.8).toFixed(2);

            panel.style.transform = `translate3d(0, ${transY}px, ${transZ}px) scale(${scale})`;
            panel.style.opacity = opacity;
        });

        // 90–100% Synchronized Energy Pulse Trigger
        if (p >= 0.88 && !pulseFired && energyPulse) {
            pulseFired = true;
            energyPulse.classList.add('is-pulsing');
            setTimeout(() => {
                energyPulse.classList.remove('is-pulsing');
            }, 1500);
        } else if (p < 0.80) {
            pulseFired = false;
        }
    }

    // --- 5. Main rAF Scroll Loop ---
    function frame() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        const raw = span > 0 ? clamp01(-r.top / span) : 0;
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

    // Initial run
    frame();
    host.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        host.classList.remove('is-live');
    };
}
