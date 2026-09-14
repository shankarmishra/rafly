/**
 * case-studies-scroll.js — Horizontal Scroll Case Studies Experience (GSAP ScrollTrigger)
 */

export function initCaseStudiesScroll() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const workSection = document.querySelector('.work-showcase-section');
    if (!workSection) return;

    const grid = workSection.querySelector('.ws-hero-grid');
    if (!grid) return;

    // Only apply horizontal scroll on desktop viewports (> 991px)
    const isDesktop = window.innerWidth >= 992;
    const cards = grid.querySelectorAll('.ws-case-card');

    if (isDesktop && cards.length > 1) {
        gsap.registerPlugin(ScrollTrigger);

        const totalWidth = grid.scrollWidth - grid.clientWidth;

        if (totalWidth > 50) {
            gsap.to(grid, {
                x: -totalWidth,
                ease: 'none',
                scrollTrigger: {
                    trigger: workSection,
                    pin: true,
                    scrub: 0.8,
                    end: () => `+=${totalWidth + 300}`,
                    invalidateOnRefresh: true
                }
            });
        }

        // Card 3D Depth tilt on mousemove
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                gsap.to(card, {
                    rotationY: x * 8,
                    rotationX: -y * 8,
                    transformPerspective: 1000,
                    ease: 'power1.out',
                    duration: 0.4
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    rotationY: 0,
                    rotationX: 0,
                    ease: 'power2.out',
                    duration: 0.6
                });
            });
        });
    }
}
