/**
 * build-loop-cinema.js — Section 06 Build Loop Cinematic Engine
 * Transforms geometry dynamically across DISCOVER → ARCHITECT → BUILD → HARDEN.
 * Handles SVG line drawing, coordinate HUD updates, stage dissolution, and magnetic hover micro-interactions.
 */

export function initBuildLoopCinema(container) {
    if (!container) return;

    const nodes = container.querySelectorAll('.bl-stage-node');
    const copies = container.querySelectorAll('[data-stage-copy]');
    const scanPerimeter = container.querySelector('.bl-scan-perimeter');

    let currentStage = 0;

    function setStage(idx) {
        if (idx === currentStage && container.classList.contains('is-initialized')) return;
        currentStage = idx;
        container.classList.add('is-initialized');

        nodes.forEach((node, nIdx) => {
            const isActive = nIdx === idx;
            node.classList.toggle('is-active', isActive);
            node.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        copies.forEach((copy, cIdx) => {
            const isMatch = cIdx === idx;
            if (isMatch) {
                copy.style.display = 'block';
                copy.style.opacity = '0';
                copy.style.transform = 'translate3d(0, 10px, 0)';
                requestAnimationFrame(() => {
                    copy.style.transition = 'all 0.35s cubic-bezier(.16,1,.3,1)';
                    copy.style.opacity = '1';
                    copy.style.transform = 'translate3d(0, 0, 0)';
                });
            } else {
                copy.style.display = 'none';
            }
        });

        if (scanPerimeter) {
            scanPerimeter.style.transition = 'stroke-dashoffset 0.6s cubic-bezier(.16,1,.3,1)';
            scanPerimeter.style.strokeDashoffset = String(400 - (idx + 1) * 100);
        }
    }

    nodes.forEach((node) => {
        node.addEventListener('click', () => {
            const idx = parseInt(node.dataset.stage, 10);
            setStage(idx);
        });
    });

    setStage(0);
}

    // Scroll progress listener for stage activation
    window.addEventListener('scroll', () => {
        const rect = container.getBoundingClientRect();
        const winH = window.innerHeight;
        if (rect.top < winH * 0.7 && rect.bottom > winH * 0.3) {
            const ratio = Math.min(0.99, Math.max(0, (winH * 0.7 - rect.top) / rect.height));
            const calculatedStage = Math.floor(ratio * 4);
            if (calculatedStage !== currentStage && calculatedStage >= 0 && calculatedStage < 4) {
                setStage(calculatedStage);
            }
        }
    }, { passive: true });
}
