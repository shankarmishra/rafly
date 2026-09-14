/**
 * close-cinema.js — Section 09 Culminating Closing Environment Engine
 * Handles film-ending atmospheric calm, form field focus expansion & surface illumination,
 * magnetic submit button, Lottie radar signal acceleration, and smart domain selector.
 */

export function initCloseCinema(container) {
    if (!container) return;

    const form = container.querySelector('form');
    const inputs = container.querySelectorAll('input, select, textarea');
    const submitBtn = container.querySelector('button[type="submit"], .btn-submit');
    const radarScanline = container.querySelector('.s09-lhud-scanline');
    const hudStatus = container.querySelector('.s09-lhud-val');

    // Form field surface illumination and active radar acceleration
    inputs.forEach((input) => {
        const wrap = input.closest('.field') || input.parentElement;

        input.addEventListener('focus', () => {
            if (wrap) wrap.classList.add('is-focused');
            input.style.transition = 'all 0.3s cubic-bezier(.16,1,.3,1)';
            input.style.borderColor = '#0a63ff';
            input.style.boxShadow = '0 0 16px rgba(10, 99, 255, 0.2)';
            
            // Accelerate radar scanning micro-animation on focus
            if (radarScanline) {
                radarScanline.style.animationDuration = '1.2s';
            }
            if (hudStatus) {
                hudStatus.textContent = 'ACTIVE INPUT // REAL-TIME SLA';
            }
        });

        input.addEventListener('blur', () => {
            if (wrap) wrap.classList.remove('is-focused');
            if (!input.value) {
                input.style.borderColor = '';
                input.style.boxShadow = '';
            }
            if (radarScanline) {
                radarScanline.style.animationDuration = '3s';
            }
            if (hudStatus) {
                hudStatus.textContent = 'ENCRYPTED • DIRECT SLA';
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

    // Smart Domain Chip selector interaction
    const chips = container.querySelectorAll('.ce-chip');
    const scopeMap = {
        'web-development': '01_build',
        'web-security': '02_protect',
        'marketing-advertisement': '03_grow',
        'content-creation': '04_content',
        'ecommerce-support': '05_ecommerce'
    };

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('is-active'));
            chip.classList.add('is-active');
            const scope = chip.dataset.scope;
            const targetPillar = scopeMap[scope] || scope;
            
            // Map scope to lead form inputs
            const serviceSelect = container.querySelector('input[name="service_interest"], select[name="service_interest"]');
            if (serviceSelect) {
                serviceSelect.value = targetPillar;
            }

            // Sync with interactive service cards inside lead-form
            const svcCards = container.querySelectorAll('.intake-svc-card');
            svcCards.forEach((card) => {
                const cardVal = card.dataset.val;
                if (cardVal === targetPillar) {
                    svcCards.forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                }
            });
        });
    });

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


