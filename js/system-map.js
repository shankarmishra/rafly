/**
 * system-map.js — System Map Controller for Section 09 & 10
 * Controls Section 09 boundary laser travel and Section 10 fragmented-to-unified system convergence.
 */

export function initSystemMap(container) {
    if (!container) return;

    // Section 09 Boundary Line Travel
    const boundaryLine = container.querySelector('.bs-laser-line');
    const boundaryCards = container.querySelectorAll('.bs-card');

    // Section 10 System Map Nodes
    const ucContainer = document.querySelector('.unified-comparison-block');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-animated');
            }
        });
    }, { threshold: 0.15 });

    if (ucContainer) observer.observe(ucContainer);
    boundaryCards.forEach(card => observer.observe(card));

    // Scroll listener for smooth laser line height
    window.addEventListener('scroll', () => {
        if (!boundaryLine) return;
        const rect = container.getBoundingClientRect();
        const winH = window.innerHeight;
        if (rect.top < winH && rect.bottom > 0) {
            const progress = Math.min(1, Math.max(0, (winH - rect.top) / (rect.height + winH)));
            boundaryLine.style.height = (progress * 100) + '%';
        }
    }, { passive: true });
}
