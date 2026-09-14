/**
 * service-visuals.js — 5 Distinct Visual Metaphors for RAFLY Services
 *
 * 1. Web Dev: Digital Application Architecture (UI -> Frontend -> API -> Database -> Infrastructure)
 * 2. Security: Security Perimeter (Internet -> WAF -> App -> Auth -> DB) + Intercepted Threat Particles
 * 3. Marketing: Growth Engine (Audience -> Campaign -> Traffic -> Conversion -> Insight) + Animated Chart
 * 4. Content: Message Engine (IDEA -> MESSAGE -> COPY -> CAMPAIGN -> CONTENT) + Typography Morph
 * 5. Commerce: Commerce Pipeline (Product -> Catalog -> Order -> Payment -> Reconciliation -> Reporting)
 */

export function initServiceVisuals() {
    // Service 01: Web Dev Architecture Data Packets
    const webDevSvg = document.querySelector('[data-service-visual="web"]');
    if (webDevSvg) {
        const packets = webDevSvg.querySelectorAll('.arch-packet');
        packets.forEach((packet, idx) => {
            packet.style.animationDelay = `${idx * 0.4}s`;
        });
    }

    // Service 02: Security Perimeter & Intercepted Threat Particles
    const securitySvg = document.querySelector('[data-service-visual="security"]');
    if (securitySvg) {
        const threatParticles = securitySvg.querySelectorAll('.threat-particle');
        const shieldPulse = securitySvg.querySelector('.shield-pulse-ring');

        threatParticles.forEach(particle => {
            particle.addEventListener('animationiteration', () => {
                if (shieldPulse) {
                    shieldPulse.classList.add('is-intercepting');
                    setTimeout(() => shieldPulse.classList.remove('is-intercepting'), 300);
                }
            });
        });
    }

    // Service 03: Growth Engine Chart Animation
    const marketingSvg = document.querySelector('[data-service-visual="marketing"]');
    if (marketingSvg && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        const chartPath = marketingSvg.querySelector('.growth-chart-line');
        if (chartPath) {
            const length = chartPath.getTotalLength ? chartPath.getTotalLength() : 400;
            gsap.set(chartPath, { strokeDasharray: length, strokeDashoffset: length });
            gsap.to(chartPath, {
                strokeDashoffset: 0,
                duration: 1.5,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: marketingSvg,
                    start: 'top 80%'
                }
            });
        }
    }

    // Service 04: Content Message Engine Typography Morph
    const contentVisual = document.querySelector('[data-service-visual="content"]');
    if (contentVisual) {
        const morphWords = contentVisual.querySelectorAll('.morph-word');
        let currentIdx = 0;

        if (morphWords.length > 1) {
            setInterval(() => {
                morphWords[currentIdx].classList.remove('is-active');
                currentIdx = (currentIdx + 1) % morphWords.length;
                morphWords[currentIdx].classList.add('is-active');
            }, 2400);
        }
    }

    // Service 05: Commerce Pipeline Order Packets
    const commerceSvg = document.querySelector('[data-service-visual="commerce"]');
    if (commerceSvg) {
        const orderFlows = commerceSvg.querySelectorAll('.commerce-packet');
        orderFlows.forEach((flow, i) => {
            flow.style.animationDelay = `${i * 0.6}s`;
        });
    }
}
