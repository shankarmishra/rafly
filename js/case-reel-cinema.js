/**
 * case-reel-cinema.js — Section 08 Case Study Reel Cinema Engine
 * Handles depth zoom transitions, clip-path reveals, image parallax (foreground ±4px, scroll ±20px), and scroll index progress.
 */

export function initCaseReelCinema(container) {
    if (!container) return;

    const cards = container.querySelectorAll('.ws-case-card');
    const indexBar = container.querySelector('.ws-index-fill');
    const indexText = container.querySelector('.ws-index-num');

    let activeIdx = 0;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-entering');
                entry.target.style.transition = 'all 0.8s cubic-bezier(.16,1,.3,1)';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translate3d(0, 0, 0) scale(1)';
                entry.target.style.filter = 'blur(0)';
            }
        });
    }, { threshold: 0.2 });

    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translate3d(0, 40px, 0) scale(0.95)';
        card.style.filter = 'blur(10px)';
        observer.observe(card);

        // Hover perspective micro-interaction
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left - rect.width / 2) / (rect.width / 2);
            const y = (e.clientY - rect.top - rect.height / 2) / (rect.height / 2);
            card.style.transform = `translate3d(${x * 6}px, ${y * 6 - 4}px, 12px) rotateX(${-y * 2}deg) rotateY(${x * 2}deg)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translate3d(0, 0, 0) rotateX(0) rotateY(0)';
        });
    });

    // Update case study scroll progress indicator
    window.addEventListener('scroll', () => {
        const rect = container.getBoundingClientRect();
        const winH = window.innerHeight;
        if (rect.top < winH && rect.bottom > 0) {
            const progress = Math.min(1, Math.max(0, (winH - rect.top) / (rect.height + winH)));
            const calculatedIdx = Math.min(cards.length - 1, Math.floor(progress * cards.length));
            
            if (calculatedIdx !== activeIdx) {
                activeIdx = calculatedIdx;
                if (indexText) indexText.textContent = `0${activeIdx + 1} / 0${cards.length}`;
            }
            if (indexBar) indexBar.style.width = (progress * 100) + '%';
        }
    }, { passive: true });
}
