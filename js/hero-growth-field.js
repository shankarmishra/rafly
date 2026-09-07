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

    /* ── Card Motion Config (Restrained 2px max float) ─────────────────── */
    const CARD_CONFIG = {
        web:       { ampY: 2.0, period: 8200,  phase: 0 },
        security:  { ampY: 2.0, period: 9400,  phase: 1.2 },
        marketing: { ampY: 2.0, period: 10100, phase: 2.4 },
        content:   { ampY: 2.0, period: 8800,  phase: 3.6 },
        commerce:  { ampY: 2.0, period: 11200, phase: 4.8 }
    };

    /* ── State ─────────────────────────────────────────────────────────── */
    let isVisible     = true;
    let rafId         = 0;
    let startTime     = performance.now();
    let hoveredCard   = null;
    let pxTarget      = 0, pyTarget = 0;
    let pxCurr        = 0, pyCurr   = 0;
    let scrollProgress= 0;

    /* ── 1. PARTICLES GENERATOR (Subtle Ambient Nodes) ───────────────── */
    function initParticles() {
        if (!particlesContainer || reduced) return;
        particlesContainer.innerHTML = '';
        const particleCount = window.innerWidth < 768 ? 6 : 10;

        for (let i = 0; i < particleCount; i++) {
            const p = document.createElement('span');
            p.className = 'rafly-particle';
            const size = (Math.random() * 1.5 + 2).toFixed(1);
            const posX = (Math.random() * 80 + 10).toFixed(1);
            const posY = (Math.random() * 80 + 10).toFixed(1);
            const dx   = ((Math.random() - 0.5) * 15).toFixed(1);
            const dy   = ((Math.random() - 0.5) * 20).toFixed(1);
            const dur  = (Math.random() * 5 + 8).toFixed(1);
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

    /* ── 2. DYNAMIC SVG BEZIER PATH CALCULATOR (Precision Anchoring) ──── */
    function updateSVGPaths() {
        if (!svgConnections || window.innerWidth <= 768) return;

        const heroRect = host.getBoundingClientRect();
        const hubRect  = hub.getBoundingClientRect();

        if (heroRect.width === 0 || hubRect.width === 0) return;

        // Map viewport pixels to SVG viewBox (1440 x 900)
        const scaleX = 1440 / heroRect.width;
        const scaleY = 900 / heroRect.height;

        // Hub center & perimeter radius (~82.5px in SVG space)
        const hubCX = (hubRect.left + hubRect.width / 2 - heroRect.left) * scaleX;
        const hubCY = (hubRect.top + hubRect.height / 2 - heroRect.top) * scaleY;
        const sphereEl = hub.querySelector('.rafly-hub__sphere');
        const sphereRect = sphereEl ? sphereEl.getBoundingClientRect() : hubRect;
        const hubR  = ((sphereRect.width / 2) || 82.5) * scaleX;

        cards.forEach((card) => {
            const key = card.dataset.heroCard;
            const pathGroup = pathGroups.find((g) => g.dataset.path === key);
            if (!pathGroup) return;

            const cardRect = card.getBoundingClientRect();
            let anchorX, anchorY, targetX, targetY;
            let cp1X, cp1Y, cp2X, cp2Y;

            if (key === 'web') {
                // 01 WEB (Top-Left of hub): socket on right edge center -> hub top-left perimeter
                anchorX = (cardRect.right - heroRect.left) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX - hubR * 0.707;
                targetY = hubCY - hubR * 0.707;

                cp1X = anchorX + (targetX - anchorX) * 0.5;
                cp1Y = anchorY;
                cp2X = targetX;
                cp2Y = targetY - (targetY - anchorY) * 0.5;

            } else if (key === 'security') {
                // 02 SECURITY (Left-Middle of hub): socket on right edge center -> hub left perimeter
                anchorX = (cardRect.right - heroRect.left) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX - hubR;
                targetY = hubCY;

                cp1X = anchorX + (targetX - anchorX) * 0.5;
                cp1Y = anchorY;
                cp2X = targetX - (targetX - anchorX) * 0.15;
                cp2Y = targetY;

            } else if (key === 'marketing') {
                // 03 MARKETING (Top-Right of hub): socket on left edge center -> hub top-right perimeter
                anchorX = (cardRect.left - heroRect.left) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX + hubR * 0.707;
                targetY = hubCY - hubR * 0.707;

                cp1X = anchorX + (targetX - anchorX) * 0.5;
                cp1Y = anchorY;
                cp2X = targetX;
                cp2Y = targetY - (targetY - anchorY) * 0.5;

            } else if (key === 'content') {
                // 04 CONTENT (Right-Middle of hub): socket on left edge center -> hub right perimeter
                anchorX = (cardRect.left - heroRect.left) * scaleX;
                anchorY = (cardRect.top + cardRect.height / 2 - heroRect.top) * scaleY;
                targetX = hubCX + hubR;
                targetY = hubCY;

                cp1X = anchorX + (targetX - anchorX) * 0.5;
                cp1Y = anchorY;
                cp2X = targetX + (targetX - anchorX) * 0.15;
                cp2Y = targetY;

            } else if (key === 'commerce') {
                // 05 COMMERCE (Bottom-Center under hub): socket on top-center -> hub bottom perimeter
                anchorX = (cardRect.left + cardRect.width / 2 - heroRect.left) * scaleX;
                anchorY = (cardRect.top - heroRect.top) * scaleY;
                targetX = hubCX;
                targetY = hubCY + hubR;

                cp1X = anchorX;
                cp1Y = anchorY + (targetY - anchorY) * 0.5;
                cp2X = targetX;
                cp2Y = targetY + (targetY - anchorY) * 0.2;
            }

            const dPath = `M ${anchorX.toFixed(1)},${anchorY.toFixed(1)} C ${cp1X.toFixed(1)},${cp1Y.toFixed(1)} ${cp2X.toFixed(1)},${cp2Y.toFixed(1)} ${targetX.toFixed(1)},${targetY.toFixed(1)}`;

            const baseLine = pathGroup.querySelector('.rafly-path__base');
            const glowLine = pathGroup.querySelector('.rafly-path__glow');
            const coreLine = pathGroup.querySelector('.rafly-path__core');
            const packet   = pathGroup.querySelector('.rafly-path__packet');

            if (baseLine) baseLine.setAttribute('d', dPath);
            if (glowLine) glowLine.setAttribute('d', dPath);
            if (coreLine) coreLine.setAttribute('d', dPath);
            if (packet && !pathGroup.classList.contains('is-active')) {
                packet.setAttribute('cx', anchorX.toFixed(1));
                packet.setAttribute('cy', anchorY.toFixed(1));
            }
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

        // Staggered reveal delays per Section 22
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

        // Staggered card delays (650ms - 900ms)
        const cardDelays = {
            web: 650,
            security: 720,
            marketing: 790,
            content: 840,
            commerce: 900
        };

        cards.forEach((card) => {
            const key = card.dataset.heroCard;
            const delay = cardDelays[key] || 650;
            card.style.transitionDelay = `${delay}ms`;
        });

        if (model3d) {
            model3d.style.transitionDelay = '850ms';
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

                // Fire traveling energy packet on hover
                if (pathGroup) {
                    const packet = pathGroup.querySelector('.rafly-path__packet');
                    const corePath = pathGroup.querySelector('.rafly-path__core');
                    if (packet && corePath && typeof corePath.getTotalLength === 'function') {
                        const totalLen = corePath.getTotalLength();
                        let pProgress = 0;
                        const animatePacket = () => {
                            pProgress += 0.05;
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

    /* ── Single Clean Waveform Slow Morphing (8-12s loop) ────────────── */
    function initWaveformMorphing() {
        if (!model3d || reduced) return;
        const mainWave = model3d.querySelector('.rafly-wave--primary');
        const fillWave = model3d.querySelector('.rafly-wave--primary-fill');
        if (!mainWave) return;

        const waveStates = [
            "M 0 70 Q 60 58 120 42 T 240 16",
            "M 0 66 Q 70 48 130 52 T 240 22",
            "M 0 74 Q 50 62 110 36 T 240 12"
        ];

        let stateIdx = 0;
        setInterval(() => {
            if (!isVisible) return;
            stateIdx = (stateIdx + 1) % waveStates.length;
            const strokeD = waveStates[stateIdx];
            const fillD = strokeD + " L 240 100 L 0 100 Z";
            mainWave.setAttribute('d', strokeD);
            if (fillWave) fillWave.setAttribute('d', fillD);
        }, 9000);
    }

    /* ── Continuous Data Flow Packet System (4.5s - 8s random interval) ── */
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
                    pProgress += 0.018; // smooth duration ~2.2s
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
            const delay = Math.random() * 3500 + 4500; // 4.5s - 8.0s
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

    /* ── 6. MAIN TICK & ANIMATION LOOP (Subconscious Restrained Motion) ── */
    function renderLoop(time) {
        if (!isVisible) {
            rafId = requestAnimationFrame(renderLoop);
            return;
        }

        const elapsed = time - startTime;

        // Damped Mouse Parallax Lerp (0.05)
        pxCurr = lerp(pxCurr, pxTarget, 0.05);
        pyCurr = lerp(pyCurr, pyTarget, 0.05);

        // Apply Spatial Depth Parallax Transforms on Desktop (Section 20)
        if (!reduced && window.innerWidth > 768) {
            if (grid) {
                // Grid parallax max 2px
                grid.style.transform = `translate3d(${pxCurr * 2}px, ${pyCurr * 2}px, 0)`;
            }
            if (hub) {
                // Hub parallax max 4px
                hub.style.transform = `translate(-50%, -50%) translate3d(${pxCurr * 4}px, ${pyCurr * 4}px, 0)`;
            }
            if (model3d) {
                // Analytics panel max 7px parallax + subtle glass rotation (-6deg Y, 2deg X)
                const modelFloatY = Math.sin((elapsed / 8000) * Math.PI * 2) * 3;
                const vp = model3d.querySelector('.rafly-model3d__viewport');
                if (vp) {
                    vp.style.transform = `rotateY(${-6 + pxCurr * 4}deg) rotateX(${2 - pyCurr * 3}deg)`;
                }
                model3d.style.transform = `translateY(-50%) translate3d(${pxCurr * 7}px, ${modelFloatY + pyCurr * 7}px, 0)`;
            }

            // Subconscious Card Floating (2px max amplitude) + 5px Card Parallax
            cards.forEach((card) => {
                const key = card.dataset.heroCard;
                const cfg = CARD_CONFIG[key] || { ampY: 2.0, period: 8500, phase: 0 };
                
                const floatY = Math.sin((elapsed / cfg.period) * Math.PI * 2 + cfg.phase) * cfg.ampY;
                const isHovered = (hoveredCard === key);
                const hoverLift = isHovered ? -3 : 0;

                if (key === 'commerce') {
                    card.style.transform = `translateX(-50%) translate3d(${pxCurr * 5}px, ${floatY + hoverLift + pyCurr * 5}px, 0)`;
                } else {
                    card.style.transform = `translate3d(${pxCurr * 5}px, ${floatY + hoverLift + pyCurr * 5}px, 0)`;
                }
            });

            // Recalculate dynamic SVG Bezier connection anchors per frame
            updateSVGPaths();
        }

        rafId = requestAnimationFrame(renderLoop);
    }

    /* ── 7. SCROLL CHOREOGRAPHY (Restrained max -15px translateY) ───── */
    function onScroll() {
        if (!host) return;
        const rect = host.getBoundingClientRect();
        const vh = window.innerHeight;
        
        if (rect.bottom > 0 && rect.top < vh) {
            scrollProgress = clamp(-rect.top / vh, 0, 1);
            if (!reduced && window.innerWidth > 768) {
                const translateY = scrollProgress * -15;
                const scale = 1 - scrollProgress * 0.005;

                if (stage) {
                    stage.style.transform = `translate3d(0, ${translateY}px, 0) scale(${scale})`;
                }
                if (leftCol) {
                    leftCol.style.transform = `translate3d(0, ${translateY * 0.5}px, 0)`;
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
