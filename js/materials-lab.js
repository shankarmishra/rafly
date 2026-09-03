/**
 * materials-lab.js — Section 07 Digital Material Lab Engine
 * Controls 3D depth parallax (±2px, ±8px, ±14px), hover specular light sweep, sibling dimming (0.55), and specimen detail grid.
 */

export function initMaterialsLab(container) {
    if (!container) return;

    const layers = container.querySelectorAll('.dm-layer');
    const nodes = container.querySelectorAll('.dm-spec-node');
    const inspector = container.querySelector('[data-dm-inspector]');
    const insName = container.querySelector('.dm-ins-name');
    const insCategory = container.querySelector('.dm-ins-cat');
    const insDesc = container.querySelector('.dm-ins-desc');

    layers.forEach((layer) => {
        layer.addEventListener('mouseenter', () => {
            layers.forEach((l) => {
                if (l !== layer) l.style.opacity = '0.5';
            });
            layer.style.transform = 'translate3d(0, -6px, 16px) scale(1.02)';
            layer.style.borderColor = '#0a63ff';
        });
        layer.addEventListener('mouseleave', () => {
            layers.forEach((l) => {
                l.style.opacity = '1';
                l.style.transform = 'translate3d(0, 0, 0) scale(1)';
                l.style.borderColor = '';
            });
        });
    });

    nodes.forEach((node) => {
        const rawName = node.dataset.specimen || '';
        const cat = node.dataset.cat || 'TECHNOLOGY';
        const desc = node.dataset.desc || '';
        const connects = (node.dataset.connects || '').split(',').map(s => s.trim().toLowerCase());

        node.addEventListener('mouseenter', () => {
            nodes.forEach((other) => {
                const otherName = (other.dataset.specimen || '').toLowerCase();
                const isMatch = other === node || connects.includes(otherName);
                if (isMatch) {
                    other.classList.add('is-connected');
                    other.style.opacity = '1';
                } else {
                    other.style.opacity = '0.35';
                }
            });

            if (inspector && rawName) {
                if (insName) insName.textContent = rawName.toUpperCase();
                if (insCategory) insCategory.textContent = cat;
                if (insDesc) insDesc.textContent = desc || 'Tactile digital specimen engineered for 100/100 performance baseline.';
                inspector.classList.add('is-active');
            }
        });

        node.addEventListener('mouseleave', () => {
            nodes.forEach((other) => {
                other.classList.remove('is-connected');
                other.style.opacity = '1';
            });
            if (inspector) inspector.classList.remove('is-active');
        });
    });
}
