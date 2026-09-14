/**
 * topology-system.js — Interactive SVG Topology System & Chaos -> System Motion
 *
 * Drives:
 * 1. Multiple Services / One Partner SVG Node Interactivity
 * 2. Multi-Vendor Problem: Chaos -> System Scroll Animation (GSAP ScrollTrigger)
 * 3. Operating System Architecture Diagram
 */

export function initTopologySystem() {
    // 1. Multiple Services / One Partner Interaction
    const partnerContainer = document.querySelector('[data-partner-topology]');
    if (partnerContainer) {
        const nodes = partnerContainer.querySelectorAll('.topology-node');
        const paths = partnerContainer.querySelectorAll('.topology-path');
        const infoCards = partnerContainer.querySelectorAll('.topology-info-card');

        nodes.forEach(node => {
            node.addEventListener('mouseenter', () => {
                const target = node.dataset.nodeTarget;

                // Activate selected node
                nodes.forEach(n => {
                    const isActive = n === node;
                    n.classList.toggle('is-active', isActive);
                    n.classList.toggle('is-quiet', !isActive && n.dataset.nodeTarget !== 'core');
                });

                // Highlight connecting paths
                paths.forEach(path => {
                    const isConnected = path.dataset.connects === target;
                    path.classList.toggle('is-active', isConnected);
                    path.classList.toggle('is-quiet', !isConnected);
                });

                // Display info card
                infoCards.forEach(card => {
                    card.style.display = card.dataset.infoKey === target ? 'block' : 'none';
                });
            });

            node.addEventListener('mouseleave', () => {
                nodes.forEach(n => {
                    n.classList.remove('is-active', 'is-quiet');
                });
                paths.forEach(p => {
                    p.classList.remove('is-active', 'is-quiet');
                });
            });
        });
    }

    // 2. Chaos -> System Scroll Animation (GSAP ScrollTrigger)
    const chaosSection = document.querySelector('[data-chaos-system]');
    if (chaosSection && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        const chaosLines = chaosSection.querySelectorAll('.chaos-line');
        const systemLines = chaosSection.querySelectorAll('.system-line');
        const chaosNodes = chaosSection.querySelectorAll('.chaos-node');
        const systemCore = chaosSection.querySelector('.system-core');

        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: chaosSection,
                start: 'top 75%',
                end: 'bottom 25%',
                scrub: 0.8
            }
        });

        // Step 1: Fade / straighten chaos lines into orderly paths
        tl.to(chaosLines, {
            strokeDashoffset: 0,
            opacity: 0.25,
            stagger: 0.05,
            duration: 1
        })
        .to(chaosNodes, {
            scale: 0.8,
            opacity: 0.4,
            duration: 1
        }, '<')
        .to(systemLines, {
            strokeDashoffset: 0,
            opacity: 1,
            stagger: 0.1,
            duration: 1.2
        }, '-=0.5')
        .to(systemCore, {
            scale: 1.05,
            boxShadow: '0 0 40px rgba(10, 99, 255, 0.4)',
            duration: 0.8
        }, '-=0.4');
    }
}
