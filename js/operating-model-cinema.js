/**
 * operating-model-cinema.js — Section 08 Global Kinetic Operating Engine
 * Controls spatial SVG stream animations, satellite node telemetry, 3D card tilt, 
 * interactive view mode switcher, and GSAP scroll-triggered kinetic surges.
 */

export function initOperatingModelCinema(container) {
    if (!container) return;

    // 1. Interactive Satellite Node Telemetry Mapping
    const satelliteData = {
        web: {
            title: "WEB ARCHITECTURE CORE",
            tag: "INFRASTRUCTURE // V4.2",
            metric: "99+ CORE WEB VITALS",
            desc: "Custom headless & monolithic micro-architectures with sub-100ms LCP, instant SPA hydration, and zero layout shift.",
            status: "ACTIVE SYNC",
            color: "#0a63ff"
        },
        security: {
            title: "PERIMETER SECURITY ENGINE",
            tag: "HARDENING // DEFENSE",
            metric: "100% PERIMETER SHIELDED",
            desc: "Active header security policies, automated session sanitization, CSRF/XSS perimeter defenses, and encrypted key management.",
            status: "ZERO THREATS",
            color: "#10b981"
        },
        growth: {
            title: "GROWTH & ATTRIBUTION ENGINE",
            tag: "ANALYTICS // ROAS",
            metric: "HIGH-INTENT ROAS FUNNELS",
            desc: "Server-side event tracking, conversion API integration, real-time funnel telemetry, and campaign performance dashboards.",
            status: "OPTIMIZED",
            color: "#38bdf8"
        },
        content: {
            title: "BRAND & COPY MATRIX",
            tag: "EDITORIAL // VOICE",
            metric: "HIGH-CONVERTING COPY",
            desc: "Precision engineering copy, value proposition positioning, asset optimization, and conversion-focused storytelling.",
            status: "ENGAGED",
            color: "#8b5cf6"
        },
        commerce: {
            title: "COMMERCE & PAYMENTS CORE",
            tag: "STOREFRONT // GATEWAY",
            metric: "SUB-SECOND CHECKOUT",
            desc: "Stripe & gateway payout reconciliation, inventory sync pipelines, global tax calculation, and frictionless buyer journeys.",
            status: "SCALING",
            color: "#ec4899"
        }
    };

    // 2. Interactive SVG Node Hover & Telemetry Box Updates
    const satNodes = container.querySelectorAll('.s08-sat-node');
    const teleTitle = container.querySelector('[data-telemetry-title]');
    const teleTag = container.querySelector('[data-telemetry-tag]');
    const teleMetric = container.querySelector('[data-telemetry-metric]');
    const teleDesc = container.querySelector('[data-telemetry-desc]');
    const teleStatus = container.querySelector('[data-telemetry-status]');
    const corePulse = container.querySelector('.s08-svg-core-pulse');

    satNodes.forEach(node => {
        node.addEventListener('mouseenter', () => {
            const key = node.dataset.satKey;
            const data = satelliteData[key];
            if (!data) return;

            // Highlight node & active stream
            satNodes.forEach(n => n.classList.remove('is-active'));
            node.classList.add('is-active');

            // Highlight corresponding laser path
            const paths = container.querySelectorAll('.s08-laser-stream');
            paths.forEach(p => {
                if (p.dataset.pathKey === key) {
                    p.classList.add('is-highlighted');
                } else {
                    p.classList.remove('is-highlighted');
                }
            });

            // Update Telemetry Panel with smooth transition
            if (teleTitle) teleTitle.textContent = data.title;
            if (teleTag) teleTag.textContent = data.tag;
            if (teleMetric) teleMetric.textContent = data.metric;
            if (teleDesc) teleDesc.textContent = data.desc;
            if (teleStatus) {
                teleStatus.textContent = data.status;
                teleStatus.style.color = data.color;
            }

            // Surge core pulse
            if (corePulse) {
                corePulse.style.transform = 'scale(1.25)';
                corePulse.style.opacity = '0.9';
                setTimeout(() => {
                    corePulse.style.transform = 'scale(1)';
                    corePulse.style.opacity = '0.4';
                }, 400);
            }
        });

        node.addEventListener('mouseleave', () => {
            node.classList.remove('is-active');
            const paths = container.querySelectorAll('.s08-laser-stream');
            paths.forEach(p => p.classList.remove('is-highlighted'));
        });
    });

    // 3. View Mode Switcher (Architecture vs 15 Specs vs Matrix)
    const viewButtons = container.querySelectorAll('.s08-view-btn');
    const viewBlocks = container.querySelectorAll('.s08-view-block');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetView = btn.dataset.viewTarget;
            if (!targetView) return;

            viewButtons.forEach(b => {
                const isActive = b === btn;
                b.classList.toggle('is-active', isActive);
                b.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            viewBlocks.forEach(block => {
                if (block.dataset.viewId === targetView || targetView === 'all-views') {
                    block.style.display = 'block';
                    block.style.opacity = '0';
                    block.style.transform = 'translateY(12px)';
                    requestAnimationFrame(() => {
                        block.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        block.style.opacity = '1';
                        block.style.transform = 'translateY(0)';
                    });
                } else {
                    block.style.display = 'none';
                }
            });
        });
    });

    // 4. 3D Spatial Tilt Micro-Interaction for Boundary Cards
    const cards = container.querySelectorAll('.s08-bi-card');

    cards.forEach(card => {
        let isMoving = false;
        card.addEventListener('mousemove', (e) => {
            if (!isMoving) {
                requestAnimationFrame(() => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = ((y - centerY) / centerY) * -5;
                    const rotateY = ((x - centerX) / centerX) * 5;

                    card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(4px)`;
                    isMoving = false;
                });
                isMoving = true;
            }
        }, { passive: true });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) translateZ(0)';
        }, { passive: true });
    });

    // 5. GSAP ScrollTrigger Surge Reveal (if GSAP available)
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        const svgEngine = container.querySelector('.s08-kinetic-svg-hub');
        const siloPipeline = container.querySelector('.s08-silo-pipeline');

        if (svgEngine) {
            gsap.fromTo(svgEngine, 
                { scale: 0.92, opacity: 0.5 },
                {
                    scale: 1,
                    opacity: 1,
                    duration: 1.2,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: container,
                        start: "top 75%",
                        toggleActions: "play none none reverse"
                    }
                }
            );
        }

        if (siloPipeline) {
            const steps = siloPipeline.querySelectorAll('.s08-silo-step');
            gsap.fromTo(steps,
                { opacity: 0, x: -16 },
                {
                    opacity: 1,
                    x: 0,
                    stagger: 0.12,
                    duration: 0.6,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: siloPipeline,
                        start: "top 80%"
                    }
                }
            );
        }
    }
}
