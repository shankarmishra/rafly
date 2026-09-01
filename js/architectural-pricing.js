/**
 * architectural-pricing.js — Section 11 Architectural Level Tiers & FAQ Engine
 * Handles level tier shadow shifts, animated perimeter stroke, physical FAQ accordion neighbor compression.
 */

export function initArchitecturalPricing(container) {
    if (!container) return;

    const tierCards = container.querySelectorAll('.pe-tier-card');
    const faqItems = container.querySelectorAll('.pe-acc-item');

    // Cursor movement shifts shadow angles & internal light for level tiers
    container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        const offsetX = (e.clientX - rect.left - rect.width / 2) / (rect.width / 2);
        const offsetY = (e.clientY - rect.top - rect.height / 2) / (rect.height / 2);

        tierCards.forEach((card) => {
            const isRec = card.classList.contains('is-recommended');
            const shadowX = offsetX * (isRec ? 16 : 8);
            const shadowY = offsetY * (isRec ? 20 : 10) + (isRec ? 24 : 12);
            card.style.boxShadow = `${shadowX}px ${shadowY}px ${isRec ? 48 : 28}px rgba(5, 15, 51, ${isRec ? 0.2 : 0.08})`;
        });
    });

    // Physical FAQ Accordion behavior with neighbor compression
    faqItems.forEach((item) => {
        const trigger = item.querySelector('.pe-acc-trigger');
        if (!trigger) return;

        trigger.addEventListener('click', () => {
            const isOpen = item.classList.contains('is-active');

            faqItems.forEach((other) => {
                other.classList.remove('is-active');
                other.style.transform = 'scale(1)';
                const otherBtn = other.querySelector('.pe-acc-trigger');
                if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                item.classList.add('is-active');
                trigger.setAttribute('aria-expanded', 'true');
                
                // Neighboring items slightly compress for physical feel
                faqItems.forEach((other) => {
                    if (other !== item) {
                        other.style.transform = 'scale(0.99)';
                    }
                });
            }
        });
    });
}
