/**
 * close-cinema.js — Section 12 Culminating Closing Environment Engine
 * Handles film-ending atmospheric calm, form field focus expansion & surface illumination, magnetic submit button, and submission state.
 */

export function initCloseCinema(container) {
    if (!container) return;

    const form = container.querySelector('form');
    const inputs = container.querySelectorAll('input, select, textarea');
    const submitBtn = container.querySelector('button[type="submit"], .btn-submit');

    // Form field surface illumination and underline expansion
    inputs.forEach((input) => {
        const wrap = input.closest('.form-group') || input.parentElement;

        input.addEventListener('focus', () => {
            if (wrap) wrap.classList.add('is-focused');
            input.style.transition = 'all 0.3s cubic-bezier(.16,1,.3,1)';
            input.style.borderColor = '#0a63ff';
            input.style.boxShadow = '0 0 16px rgba(10, 99, 255, 0.15)';
        });

        input.addEventListener('blur', () => {
            if (wrap) wrap.classList.remove('is-focused');
            if (!input.value) {
                input.style.borderColor = '';
                input.style.boxShadow = '';
            }
        });
    });

    // Magnetic submit CTA button micro-interaction
    if (submitBtn) {
        submitBtn.addEventListener('mousemove', (e) => {
            const rect = submitBtn.getBoundingClientRect();
            const x = (e.clientX - rect.left - rect.width / 2) * 0.25;
            const y = (e.clientY - rect.top - rect.height / 2) * 0.25;
            submitBtn.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        });

        submitBtn.addEventListener('mouseleave', () => {
            submitBtn.style.transform = 'translate3d(0, 0, 0)';
        });

        submitBtn.addEventListener('mousedown', () => {
            submitBtn.style.transform = 'translate3d(0, 2px, 0) scale(0.98)';
        });
    }

    // Atmospheric calm observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                document.body.classList.add('is-close-active');
            } else {
                document.body.classList.remove('is-close-active');
            }
        });
    }, { threshold: 0.3 });

    observer.observe(container);
}
