/**
 * build-loop-cinema.js — Section 06 Build Loop Cinematic Engine
 * Transforms geometry dynamically across DISCOVER → ARCHITECT → BUILD → HARDEN.
 * Handles SVG line drawing, coordinate HUD updates, stage dissolution, and magnetic hover micro-interactions.
 */

export function initBuildLoopCinema(container) {
    if (!container) return;

    const tabs = container.querySelectorAll('.bl-table-tab, .bl-tab');
    const panels = container.querySelectorAll('[data-stage-panel]');
    const svgTrace = container.querySelector('.bl-svg-trace');
    const coordVal = container.querySelector('.bl-hud-coord, .bl-coord-val');

    let currentStage = 0;

    function setStage(idx) {
        if (idx === currentStage && container.classList.contains('is-initialized')) return;
        currentStage = idx;
        container.classList.add('is-initialized');

        // Staggered tab states
        tabs.forEach((tab, tIdx) => {
            const isActive = tIdx === idx;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            if (isActive) {
                tab.style.transform = 'translate3d(0, -3px, 0)';
            } else {
                tab.style.transform = 'translate3d(0, 0, 0)';
            }
        });

        // Dissolve & animate stage panels smoothly without instant popping
        panels.forEach((panel, pIdx) => {
            const isMatch = pIdx === idx;
            if (isMatch) {
                panel.style.display = 'grid';
                panel.style.opacity = '0';
                panel.style.transform = 'translate3d(0, 20px, 0) scale(0.98)';
                panel.style.filter = 'blur(8px)';
                
                requestAnimationFrame(() => {
                    panel.style.transition = 'all 0.5s cubic-bezier(.16,1,.3,1)';
                    panel.style.opacity = '1';
                    panel.style.transform = 'translate3d(0, 0, 0) scale(1)';
                    panel.style.filter = 'blur(0)';
                });
            } else {
                panel.style.transition = 'all 0.3s cubic-bezier(.22,1,.36,1)';
                panel.style.opacity = '0';
                panel.style.transform = 'translate3d(0, -15px, 0) scale(0.97)';
                panel.style.filter = 'blur(4px)';
                setTimeout(() => {
                    if (panel.dataset.stagePanel !== String(currentStage)) {
                        panel.style.display = 'none';
                    }
                }, 300);
            }
        });

        // Coordinate HUD text interpolation
        if (coordVal) {
            const telemetry = [
                'SYS.DISCOVER // INTENT_NODES: ACTIVE | COORD: [142, 890] | FREQ: 60Hz',
                'SYS.ARCHITECT // TOKEN_BOUNDARIES: LOCKED | COORD: [420, 1240] | FREQ: 60Hz',
                'SYS.BUILD_ENGINE // SPRINT_VELOCITY: SUB-15MS | COORD: [890, 2100] | FREQ: 60Hz',
                'SYS.HARDENED_PROD // SECURITY: AES-256 | CWV: 100/100 | COORD: [1280, 2890] | FREQ: 60Hz'
            ];
            coordVal.textContent = telemetry[idx] || telemetry[0];
        }

        // SVG path trace animation
        if (svgTrace) {
            const pathLength = svgTrace.getTotalLength ? svgTrace.getTotalLength() : 800;
            const progress = (idx + 1) / 4;
            svgTrace.style.strokeDasharray = pathLength;
            svgTrace.style.transition = 'stroke-dashoffset 0.75s cubic-bezier(.16,1,.3,1)';
            svgTrace.style.strokeDashoffset = pathLength * (1 - progress);
        }
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const idx = parseInt(tab.dataset.stage, 10);
            setStage(idx);
        });

        tab.addEventListener('mouseenter', () => {
            const num = tab.querySelector('.bl-tab-num');
            if (num) num.style.transform = 'translate3d(0, -2px, 0) scale(1.1)';
        });
        tab.addEventListener('mouseleave', () => {
            const num = tab.querySelector('.bl-tab-num');
            if (num) num.style.transform = 'translate3d(0, 0, 0) scale(1)';
        });
    });

    // Auto initialize stage 0
    setStage(0);

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
