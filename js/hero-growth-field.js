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
    const cursorGlow         = host.querySelector('[data-hero-cursor-glow]');
    const model3d            = host.querySelector('[data-hero-model3d]');
    const leftCol            = host.querySelector('[data-hero-left]');

    if (!stage || !hub) return () => {};

    /* ── Card Motion Config (Subtle & Subconscious) ───────────────────── */
    const CARD_CONFIG = {
        web:       { ampY: 3.5, period: 6200, phase: 0,    color: '#1769ff' },
        security:  { ampY: 4.5, period: 7000, phase: 1.2,  color: '#22b8d6' },
        marketing: { ampY: 3.8, period: 6700, phase: 2.4,  color: '#7c5cff' },
        content:   { ampY: 4.2, period: 7400, phase: 3.6,  color: '#1769ff' },
        commerce:  { ampY: 3.5, period: 6400, phase: 4.8,  color: '#253c88' }
    };

    /* ── State ─────────────────────────────────────────────────────────── */
    let isVisible     = true;
    let rafId         = 0;
    let startTime     = performance.now();
    let hoveredCard   = null;
    let pxTarget      = 0, pyTarget = 0;
    let pxCurr        = 0, pyCurr   = 0;
    let scrollProgress= 0;

    /* ── 1. PARTICLES GENERATOR ────────────────────────────────────────── */
    function initParticles() {
        if (!particlesContainer || reduced) return;
        particlesContainer.innerHTML = '';
        const particleCount = window.innerWidth < 768 ? 12 : 28;

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
        const hubR  = ((sphereRect.width / 2) || 82) * scaleX;

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
        if (reduced) {
            host.classList.add('rafly-hero--entered');
            return;
        }

        setTimeout(() => {
            host.classList.add('rafly-hero--entered');
        }, 100);

        // Staggered reveal delays for left column elements per Section 31
        const revealEls = Array.from(host.querySelectorAll('[data-hero-reveal]'));
        const revealDelays = {
            'eyebrow': 300,
            'title-1': 400,
            'title-2': 500,
            'title-3': 600,
            'title-4': 700,
            'desc': 800,
            'cta': 900,
            'metrics': 1000
        };

        revealEls.forEach((el) => {
            const key = el.dataset.heroReveal;
            const delay = revealDelays[key] || 400;
            el.style.transitionDelay = `${delay}ms`;
        });

        // Staggered card node delays (950ms -> 1350ms)
        const cardDelays = {
            web: 950,
            security: 1050,
            marketing: 1150,
            content: 1250,
            commerce: 1350
        };

        cards.forEach((card) => {
            const key = card.dataset.heroCard;
            const delay = cardDelays[key] || 950;
            card.style.transitionDelay = `${delay}ms`;
        });

        if (model3d) {
            model3d.style.transitionDelay = '1800ms';
        }
    }

    /* ── 4. HOVER & INTERACTION HANDLERS ───────────────────────────────── */
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
                            pProgress += 0.055;
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

    /* ── 3D Model Waveform Morphing (Section 9) ── */
    function initWaveformMorphing() {
        if (!model3d || reduced) return;
        const mainWave = model3d.querySelector('.rafly-wave--primary');
        const fillWave = model3d.querySelector('.rafly-wave--primary-fill');
        if (!mainWave) return;

        const waveStates = [
            "M 0 70 Q 50 60 100 35 T 200 18 T 240 12",
            "M 0 65 Q 60 45 120 50 T 210 25 T 240 15",
            "M 0 75 Q 40 55 90 28 T 190 30 T 240 10",
            "M 0 60 Q 70 65 110 40 T 200 15 T 240 18"
        ];

        let stateIdx = 0;
        setInterval(() => {
            if (!isVisible) return;
            stateIdx = (stateIdx + 1) % waveStates.length;
            const strokeD = waveStates[stateIdx];
            const fillD = strokeD + " L 240 100 L 0 100 Z";
            mainWave.setAttribute('d', strokeD);
            if (fillWave) fillWave.setAttribute('d', fillD);
        }, 7500);
    }

    /* ── Continuous Data Flow Packet System (Section 19) ── */
    function initContinuousDataFlow() {
        if (reduced) return;

        const pathKeys = ['web', 'security', 'marketing', 'content', 'commerce'];

        function dispatchPacket() {
            if (!isVisible) {
                scheduleNext();
                return;
            }

            const key = pathKeys[Math.floor(Math.random() * pathKeys.length)];
            const pathGroup = pathGroups.find((g) => g.dataset.path === key);
            if (!pathGroup) {
                scheduleNext();
                return;
            }

            const packet = pathGroup.querySelector('.rafly-path__packet');
            const corePath = pathGroup.querySelector('.rafly-path__core');

            if (packet && corePath && typeof corePath.getTotalLength === 'function') {
                const totalLen = corePath.getTotalLength();
                let pProgress = 0;
                pathGroup.classList.add('is-active');

                const animatePacket = () => {
                    pProgress += 0.018; // smooth cubic duration ~2.2s
                    if (pProgress <= 1) {
                        const eased = pProgress < 0.5 
                            ? 2 * pProgress * pProgress 
                            : -1 + (4 - 2 * pProgress) * pProgress;
                        const pt = corePath.getPointAtLength(eased * totalLen);
                        packet.setAttribute('cx', pt.x.toFixed(1));
                        packet.setAttribute('cy', pt.y.toFixed(1));
                        requestAnimationFrame(animatePacket);
                    } else {
                        pathGroup.classList.remove('is-active');
                        if (hub) {
                            hub.classList.add('is-pulsing');
                            setTimeout(() => hub.classList.remove('is-pulsing'), 600);
                        }
                    }
                };
                animatePacket();
            }

            scheduleNext();
        }

        function scheduleNext() {
            const delay = Math.random() * 4000 + 3500; // 3.5s - 7.5s
            setTimeout(dispatchPacket, delay);
        }

        setTimeout(dispatchPacket, 2500);
    }

    /* ── 5. MOUSE PARALLAX & CURSOR GLOW TRACKING ─────────────────────── */
    function onMouseMove(e) {
        if (!isFine || reduced) return;
        const w = window.innerWidth;
        const h = window.innerHeight;
        pxTarget = clamp((e.clientX / w) - 0.5, -0.5, 0.5);
        pyTarget = clamp((e.clientY / h) - 0.5, -0.5, 0.5);

        if (cursorGlow) {
            cursorGlow.style.opacity = '1';
            cursorGlow.style.left = `${e.clientX}px`;
            cursorGlow.style.top = `${e.clientY}px`;
        }
    }

    if (isFine) {
        window.addEventListener('mousemove', onMouseMove, { passive: true });
        host.addEventListener('mouseleave', () => {
            if (cursorGlow) cursorGlow.style.opacity = '0';
        });
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

        // Apply Spatial Depth Parallax Transforms on Desktop (Section 13, 23, 24)
        if (!reduced && window.innerWidth > 768) {
            if (grid) {
                grid.style.transform = `translate3d(${pxCurr * 2}px, ${pyCurr * 2}px, 0)`;
            }
            if (hub) {
                // Hub depth 6-8px
                hub.style.transform = `translate(-50%, -50%) translate3d(${pxCurr * 7}px, ${pyCurr * 7}px, 0)`;
            }
            if (model3d) {
                // 3D Model depth 10-14px + float Y & subtle tilt (Section 8)
                const modelFloatY = Math.sin((elapsed / 6500) * Math.PI * 2) * 6;
                const modelRotY = -10 + Math.sin((elapsed / 7000) * Math.PI * 2) * 1.5;
                const modelRotX = 4 + Math.cos((elapsed / 6000) * Math.PI * 2) * 1;
                const vp = model3d.querySelector('.rafly-model3d__viewport');
                if (vp) {
                    vp.style.transform = `rotateY(${modelRotY + pxCurr * 6}deg) rotateX(${modelRotX - pyCurr * 4}deg) rotateZ(-1deg)`;
                }
                model3d.style.transform = `translateY(-50%) translate3d(${pxCurr * 11}px, ${modelFloatY + pyCurr * 11}px, 0)`;
            }

            // Subconscious Card Floating + Parallax (Section 16 & 23)
            cards.forEach((card) => {
                const key = card.dataset.heroCard;
                const cfg = CARD_CONFIG[key] || { ampY: 3.5, period: 6500, phase: 0 };
                
                // Continuous Organic Floating (3-5px max)
                const floatY = Math.sin((elapsed / cfg.period) * Math.PI * 2 + cfg.phase) * cfg.ampY;
                const isHovered = (hoveredCard === key);
                const hoverLift = isHovered ? -4 : 0;
                const hoverScale = isHovered ? 1.015 : 1;

                if (key === 'commerce') {
                    card.style.transform = `translateX(-50%) translate3d(${pxCurr * 6}px, ${floatY + hoverLift + pyCurr * 6}px, 0) scale(${hoverScale})`;
                } else {
                    card.style.transform = `translate3d(${pxCurr * 6}px, ${floatY + hoverLift + pyCurr * 6}px, 0) scale(${hoverScale})`;
                }
            });

            // Recalculate dynamic SVG Bezier connection anchors per frame
            updateSVGPaths();
        }

        rafId = requestAnimationFrame(renderLoop);
    }

    /* ── 7. SCROLL CHOREOGRAPHY (Section 33) ──────────────────────────── */
    function onScroll() {
        if (!host) return;
        const rect = host.getBoundingClientRect();
        const vh = window.innerHeight;
        
        if (rect.bottom > 0 && rect.top < vh) {
            scrollProgress = clamp(-rect.top / vh, 0, 1);
            if (!reduced && window.innerWidth > 768) {
                const translateY = scrollProgress * -35;
                const scale = 1 - scrollProgress * 0.015;
                const opacity = 1 - scrollProgress * 0.08;

                if (stage) {
                    stage.style.transform = `translate3d(0, ${translateY}px, 0) scale(${scale})`;
                    stage.style.opacity = opacity.toFixed(2);
                }
                if (leftCol) {
                    leftCol.style.transform = `translate3d(0, ${translateY * 0.6}px, 0)`;
                }
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
    initWaveformMorphing();
    initContinuousDataFlow();
    
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
