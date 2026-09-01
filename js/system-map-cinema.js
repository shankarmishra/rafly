/**
 * system-map-cinema.js — Section 09 Boundary Laser & Section 10 Living System Map Engine
 * Controls Section 09 spatial laser growth, boundary scan sweeps, and Section 10 node breathing & signal routing.
 */

export function initSystemMapCinema(container) {
    if (!container) return;

    // Section 09 Boundary Laser Line & Scan Sweeps
    const laserLine = container.querySelector('.bs-laser-line');
    const boundaryCards = container.querySelectorAll('.bs-card');

    boundaryCards.forEach((card) => {
        card.addEventListener('mouseenter', () => {
            const scan = document.createElement('div');
            scan.className = 'bs-card-scan-sweep';
            card.appendChild(scan);
            setTimeout(() => scan.remove(), 600);
        });
    });

    // Section 10 Living System Map Node Breathing & Signal Pulses
    const oldNodes = container.querySelectorAll('.uc-node');
    const raflyCore = container.querySelector('.uc-u-core');
    const signalPath = container.querySelector('.uc-signal-path');

    let isCollapsed = false;

    window.addEventListener('scroll', () => {
        const rect = container.getBoundingClientRect();
        const winH = window.innerHeight;

        if (rect.top < winH && rect.bottom > 0) {
            // Section 09 Laser height scaling
            if (laserLine) {
                const progress = Math.min(1, Math.max(0, (winH * 0.8 - rect.top) / (rect.height * 0.6)));
                laserLine.style.height = (progress * 100) + '%';
            }

            // Section 10 System Map Collapse / Convergence logic
            const ucBlock = container.querySelector('.unified-comparison-block');
            if (ucBlock) {
                const ucRect = ucBlock.getBoundingClientRect();
                if (ucRect.top < winH * 0.65 && !isCollapsed) {
                    isCollapsed = true;
                    oldNodes.forEach((node, i) => {
                        node.style.transition = `all 0.6s cubic-bezier(.16,1,.3,1) ${i * 0.08}s`;
                        node.style.opacity = '0.4';
                        node.style.transform = 'translate3d(-10px, 0, 0) scale(0.96)';
                        node.style.filter = 'grayscale(0.8)';
                    });

                    if (raflyCore) {
                        raflyCore.style.transition = 'all 0.8s cubic-bezier(.16,1,.3,1) 0.3s';
                        raflyCore.style.transform = 'scale(1.04)';
                        raflyCore.style.boxShadow = '0 24px 64px rgba(10, 99, 255, 0.35)';
                    }
                } else if (ucRect.top >= winH * 0.65 && isCollapsed) {
                    isCollapsed = false;
                    oldNodes.forEach((node) => {
                        node.style.opacity = '1';
                        node.style.transform = 'translate3d(0, 0, 0) scale(1)';
                        node.style.filter = 'none';
                    });
                    if (raflyCore) {
                        raflyCore.style.transform = 'scale(1)';
                        raflyCore.style.boxShadow = 'none';
                    }
                }
            }
        }
    }, { passive: true });
}
