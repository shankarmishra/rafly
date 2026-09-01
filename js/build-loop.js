/**
 * build-loop.js — Architectural Construction Field Controller for Section 06
 * Transforms SVG/Canvas geometry from SKETCH → STRUCTURE → PRODUCT on scroll/tab interaction.
 */

export function initBuildLoop(container) {
    if (!container) return;

    const tabs = container.querySelectorAll('.bl-tab');
    const panels = container.querySelectorAll('[data-stage-panel]');
    const svgPath = container.querySelector('.bl-svg-trace');
    const coordDisplay = container.querySelector('.bl-coord-val');

    function activateStage(idx) {
        tabs.forEach((tab) => {
            const isActive = tab.dataset.stage === String(idx);
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach((panel) => {
            const isMatch = panel.dataset.stagePanel === String(idx);
            panel.style.display = isMatch ? 'grid' : 'none';
            if (isMatch) {
                panel.classList.remove('is-active');
                void panel.offsetWidth; // Trigger reflow
                panel.classList.add('is-active');
            }
        });

        // Update coordinate HUD
        if (coordDisplay) {
            const coords = [
                'x: 0142 | y: 0890 | MODE: DISCOVER',
                'x: 0420 | y: 1240 | MODE: ARCHITECT',
                'x: 0890 | y: 2100 | MODE: BUILD_ENGINE',
                'x: 1280 | y: 2890 | MODE: HARDENED_PROD'
            ];
            coordDisplay.textContent = coords[idx] || coords[0];
        }

        // Animate SVG path trace
        if (svgPath) {
            const progress = (idx + 1) / 4;
            const totalLen = svgPath.getTotalLength ? svgPath.getTotalLength() : 600;
            svgPath.style.strokeDasharray = totalLen;
            svgPath.style.strokeDashoffset = totalLen * (1 - progress);
        }
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const idx = parseInt(tab.dataset.stage, 10);
            activateStage(idx);
        });
    });

    // IntersectionObserver to auto-activate initial stage smoothly
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                container.classList.add('is-in-view');
            }
        });
    }, { threshold: 0.2 });

    observer.observe(container);
}
