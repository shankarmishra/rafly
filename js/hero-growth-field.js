/**
 * hero-growth-field.js — RAFly Premium Ecosystem Hero Controller (Light & Restrained)
 *
 * Responsibilities:
 *   • Cinematic staggered entrance timeline (0.0s -> 1.5s idle state)
 *   • Dynamic SVG Bezier connection ribbon recalculation on resize
 *   • Subconscious card floating motion (max ±3-4px, 6-7.2s periods)
 *   • Interactive local hover feedback (subtle card lift, light stream focus)
 *   • Damped 3D mouse spatial parallax (max 1-3px movement)
 *   • Subtle scroll choreography
 *   • Full prefers-reduced-motion support & 60fps performance optimization
 */

const lerp = (a, b, n) => a + (b - a) * n;
const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));

export function initHeroGrowthField(host) {
    if (!host) return () => {};

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isFine  = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    /* ── DOM Selectors ─────────────────────────────────────────────────── */
    const stage              = host.querySelector('[data-hero-stage]');
    const hub                = host.querySelector('[data-hero-hub]');
    const cards              = Array.from(host.querySelectorAll('[data-hero-card]'));
    const svgConnections     = host.querySelector('.rafly-hero__connections');
    const pathGroups         = Array.from(host.querySelectorAll('[data-path]'));
    const dots               = Array.from(host.querySelectorAll('[data-dot]'));
    const particlesContainer = host.querySelector('[data-hero-particles]');
    const grid               = host.querySelector('.rafly-hero__grid');
    const orbitBg            = host.querySelector('.rafly-hero__orbit-bg');

    if (!stage || !hub) return () => {};

    /* ── Card Motion Config (Subtle & Subconscious) ───────────────────── */
    const CARD_CONFIG = {
        web:       { ampY: 3.5, period: 6000, phase: 0,   color: '#0a63ff' },
        security:  { ampY: 4.5, period: 7000, phase: 1.2, color: '#0891b2' },
        marketing: { ampY: 3.0, period: 6500, phase: 2.4, color: '#6134c9' },
        content:   { ampY: 4.0, period: 7200, phase: 3.6, color: '#2563eb' },
        commerce:  { ampY: 3.5, period: 6300, phase: 4.8, color: '#0230c6' }
    };

    /* ── State ─────────────────────────────────────────────────────────── */
    let isVisible     = true;
    let rafId         = 0;
    let startTime     = performance.now();
    let hoveredCard   = null;
    let pxTarget      = 0, pyTarget = 0;
    let pxCurr        = 0, pyCurr   = 0;
    let scrollProgress= 0;

    /* ── 1. PARTICLES GENERATOR (REDUCED & RESTRAINED) ─────────────────── */
    function initParticles() {
        if (!particlesContainer || reduced) return;
        particlesContainer.innerHTML = '';
        const particleCount = window.innerWidth < 768 ? 10 : 22;

        for (let i = 0; i < particleCount; i++) {
            const p = document.createElement('span');
            p.className = 'rafly-particle';
            const size = (Math.random() * 2.5 + 2).toFixed(1);
            const posX = (Math.random() * 100).toFixed(1);
            const posY = (Math.random() * 100).toFixed(1);
            const dx   = ((Math.random() - 0.5) * 25).toFixed(1);
            const dy   = ((Math.random() - 0.5) * 35).toFixed(1);
            const dur  = (Math.random() * 6 + 7).toFixed(1);
            const delay= (Math.random() * 4).toFixed(1);

            p.style.width  = `${size}px`;
            p.style.height = `${size}px`;
            p.style.left   = `${posX}%`;
            p.style.top    = `${posY}%`;
            p.style.setProperty('--dx', `${dx}px`);
            p.style.setProperty('--dy', `${dy}px`);
            p.style.setProperty('--dur', `${dur}s`);
            p.style.animationDelay = `${delay}s`;

            particlesContainer.appendChild(p);
        }
    }

    /* ── 2. DYNAMIC SVG BEZIER PATH CALCULATOR ────────────────────────── */
    function updateSVGPaths() {
        if (!svgConnections || window.innerWidth <= 768) return;

        const heroRect = host.getBoundingClientRect();
        const hubRect  = hub.getBoundingClientRect();

        if (heroRect.width === 0 || hubRect.width === 0) return;

        // Scale factors to map viewport pixels to SVG viewBox (1440 x 900)
        const scaleX = 1440 / heroRect.width;
        const scaleY = 900 / heroRect.height;

        // Hub center & radius in SVG viewBox space
        const hubCX = (hubRect.left + hubRect.width / 2 - heroRect.left) * scaleX;
        const hubCY = (hubRect.top + hubRect.height / 2 - heroRect.top) * scaleY;
        const sphereEl = hub.querySelector('.rafly-hub__sphere');
        const sphereRect = sphereEl ? sphereEl.getBoundingClientRect() : hubRect;
        const hubR  = ((sphereRect.width / 2) || 80) * scaleX;

        cards.forEach((card) => {
            const key = card.dataset.heroCard;
            const pathGroup = pathGroups.find((g) => g.dataset.path === key);
            if (!pathGroup) return;

            const cardRect = card.getBoundingClientRect();
            let anchorX, anchorY, targetX, targetY;

            if (key === 'web') {
                anchorX = (cardRect.right - heroRect.left - 5) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX - hubR * 0.707;
                targetY = hubCY - hubR * 0.707;
            } else if (key === 'security') {
                anchorX = (cardRect.right - heroRect.left - 5) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX - hubR;
                targetY = hubCY;
            } else if (key === 'marketing') {
                anchorX = (cardRect.left - heroRect.left + 5) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX + hubR * 0.707;
                targetY = hubCY - hubR * 0.707;
            } else if (key === 'content') {
                anchorX = (cardRect.left - heroRect.left + 5) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX + hubR;
                targetY = hubCY;
            } else if (key === 'commerce') {
                anchorX = (cardRect.left + cardRect.width / 2 - heroRect.left) * scaleX;
                anchorY = (cardRect.top - heroRect.top + 5) * scaleY;
                targetX = hubCX;
                targetY = hubCY + hubR;
            }

            // Smooth Natural Bezier Curve Calculation
            let cp1X = anchorX + (targetX - anchorX) * 0.48;
            let cp1Y = anchorY;
            let cp2X = targetX - (targetX - anchorX) * 0.22;
            let cp2Y = targetY;

            if (key === 'commerce') {
                cp1X = anchorX;
                cp1Y = anchorY + (targetY - anchorY) * 0.5;
                cp2X = targetX;
                cp2Y = targetY + (targetY - anchorY) * 0.5;
            }

            const dPath = `M ${anchorX.toFixed(1)},${anchorY.toFixed(1)} C ${cp1X.toFixed(1)},${cp1Y.toFixed(1)} ${cp2X.toFixed(1)},${cp2Y.toFixed(1)} ${targetX.toFixed(1)},${targetY.toFixed(1)}`;

            const baseLine = pathGroup.querySelector('.rafly-path__base');
            const glowLine = pathGroup.querySelector('.rafly-path__glow');
            const coreLine = pathGroup.querySelector('.rafly-path__core');

            if (baseLine) baseLine.setAttribute('d', dPath);
            if (glowLine) glowLine.setAttribute('d', dPath);
            if (coreLine) coreLine.setAttribute('d', dPath);
        });
    }

    /* ── 3. STAGGERED ENTRANCE TIMELINE ───────────────────────────────── */
    function initEntrance() {
        setTimeout(() => {
            host.classList.add('rafly-hero--entered');
        }, 100);

        cards.forEach((card, idx) => {
            const delay = 350 + idx * 100;
            setTimeout(() => {
                card.style.transition = 'opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
            }, delay);
        });
    }

    /* ── 4. HOVER & INTERACTION HANDLERS (LOCAL & RESTRAINED) ───────── */
    function initInteractions() {
        cards.forEach((card) => {
            const key = card.dataset.heroCard;
            const pathGroup = pathGroups.find((g) => g.dataset.path === key);
            const dot = dots.find((d) => d.dataset.dot === key);

            card.addEventListener('mouseenter', () => {
                hoveredCard = key;
                if (pathGroup) pathGroup.classList.add('is-active');
                if (hub) hub.classList.add('is-pulsing');
                if (dot) {
                    dots.forEach((d) => d.classList.remove('is-active'));
                    dot.classList.add('is-active');
                }

                // Fire traveling energy packet along path
                if (pathGroup) {
                    const packet = pathGroup.querySelector('.rafly-path__packet');
                    const corePath = pathGroup.querySelector('.rafly-path__core');
                    if (packet && corePath && typeof corePath.getTotalLength === 'function') {
                        const totalLen = corePath.getTotalLength();
                        let pProgress = 0;
                        const animatePacket = () => {
                            pProgress += 0.045;
                            if (pProgress <= 1) {
                                const pt = corePath.getPointAtLength(pProgress * totalLen);
                                packet.setAttribute('cx', pt.x.toFixed(1));
                                packet.setAttribute('cy', pt.y.toFixed(1));
                                requestAnimationFrame(animatePacket);
                            }
                        };
                        animatePacket();
                    }
                }
            });

            card.addEventListener('mouseleave', () => {
                hoveredCard = null;
                if (pathGroup) pathGroup.classList.remove('is-active');
                if (hub) hub.classList.remove('is-pulsing');
            });
        });
    }

    /* ── 5. MOUSE PARALLAX TRACKING (RESTRAINED 1-3PX MAX) ───────────── */
    function onMouseMove(e) {
        if (!isFine || reduced) return;
        const w = window.innerWidth;
        const h = window.innerHeight;
        pxTarget = clamp((e.clientX / w) - 0.5, -0.5, 0.5);
        pyTarget = clamp((e.clientY / h) - 0.5, -0.5, 0.5);
    }

    if (isFine) {
        window.addEventListener('mousemove', onMouseMove, { passive: true });
    }

    /* ── 6. MAIN TICK & ANIMATION LOOP ────────────────────────────────── */
    function renderLoop(time) {
        if (!isVisible) {
            rafId = requestAnimationFrame(renderLoop);
            return;
        }

        const elapsed = time - startTime;

        // Damped Mouse Parallax Lerp
        pxCurr = lerp(pxCurr, pxTarget, 0.05);
        pyCurr = lerp(pyCurr, pyTarget, 0.05);

        // Apply Parallax Transforms on Desktop
        if (!reduced && window.innerWidth > 768) {
            if (grid) {
                grid.style.transform = `translate3d(${pxCurr * 2}px, ${pyCurr * 2}px, 0)`;
            }
            if (orbitBg) {
                orbitBg.style.transform = `translate3d(${pxCurr * 3}px, ${pyCurr * 3}px, 0)`;
            }
            if (hub) {
                hub.style.transform = `translate(-50%, -50%) translate3d(${pxCurr * 1}px, ${pyCurr * 1}px, 0)`;
            }

            // Subconscious Card Floating + Parallax
            cards.forEach((card) => {
                const key = card.dataset.heroCard;
                const cfg = CARD_CONFIG[key] || { ampY: 3.5, period: 6500, phase: 0 };
                
                // Continuous Sine Floating (3-4px max)
                const floatY = Math.sin((elapsed / cfg.period) * Math.PI * 2 + cfg.phase) * cfg.ampY;
                const isHovered = (hoveredCard === key);
                const hoverLift = isHovered ? -3 : 0;
                const hoverScale = isHovered ? 1.01 : 1;

                if (key === 'commerce') {
                    card.style.transform = `translateX(-50%) translate3d(${pxCurr * 3}px, ${floatY + hoverLift + pyCurr * 3}px, 0) scale(${hoverScale})`;
                } else {
                    card.style.transform = `translate3d(${pxCurr * 3}px, ${floatY + hoverLift + pyCurr * 3}px, 0) scale(${hoverScale})`;
                }
            });
            // Recalculate dynamic SVG Bezier connection anchors per frame to keep paths attached to floating cards
            updateSVGPaths();
        }

        rafId = requestAnimationFrame(renderLoop);
    }

    /* ── 7. SCROLL CHOREOGRAPHY ────────────────────────────────────────── */
    function onScroll() {
        if (!host) return;
        const rect = host.getBoundingClientRect();
        const vh = window.innerHeight;
        
        if (rect.bottom > 0 && rect.top < vh) {
            scrollProgress = clamp(-rect.top / vh, 0, 1);
            if (!reduced && window.innerWidth > 768) {
                const translateY = scrollProgress * -40;
                const scale = 1 - scrollProgress * 0.015;
                const opacity = 1 - scrollProgress * 0.10;

                stage.style.transform = `translate3d(0, ${translateY}px, 0) scale(${scale})`;
                stage.style.opacity = opacity.toFixed(2);
            }
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    /* ── 8. INTERSECTION OBSERVER LIFECYCLE ────────────────────────────── */
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            isVisible = e.isIntersecting;
        });
    }, { threshold: 0.05 });

    io.observe(host);

    /* ── 9. RESIZE HANDLER ────────────────────────────────────────────── */
    function onResize() {
        updateSVGPaths();
    }

    window.addEventListener('resize', onResize, { passive: true });

    /* ── INITIALIZATION ───────────────────────────────────────────────── */
    const isStaticMode = host.hasAttribute('data-hero-static');
    
    initParticles();
    initEntrance();
    initInteractions();
    
    setTimeout(updateSVGPaths, 50);
    setTimeout(updateSVGPaths, 300);

    if (isStaticMode) {
        host.classList.add('rafly-hero--entered');
        cards.forEach((card) => {
            card.style.opacity = '1';
        });
        updateSVGPaths();
    } else {
        rafId = requestAnimationFrame(renderLoop);
    }

    return () => {
        cancelAnimationFrame(rafId);
        window.removeEventListener('mousemove', onMouseMove);
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        io.disconnect();
    };
}
