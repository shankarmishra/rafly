/**
 * materials-lab.js — Section 07 Digital Material Lab Engine
 * Controls 3D depth parallax (±2px, ±8px, ±14px), hover specular light sweep, sibling dimming (0.55), and specimen detail grid.
 */

export function initMaterialsLab(container) {
    if (!container) return;

    const items = container.querySelectorAll('.tk-item');
    const inspector = container.querySelector('[data-tk-inspector]');
    const inspectName = container.querySelector('.tk-inspect-name');
    const inspectCategory = container.querySelector('.tk-inspect-cat');
    const inspectMeta = container.querySelector('.tk-inspect-meta');

    const SPECIMENS = {
        'PHP': { cat: 'RUNTIME // SERVER ENGINE', meta: 'Sub-15ms server execution, zero framework overhead, ultra-fast memory footprint.' },
        'LARAVEL': { cat: 'FRAMEWORK // ARCHITECTURE', meta: 'Elegant MVC structure for enterprise database reconciliation and job queues.' },
        'WORDPRESS': { cat: 'CMS // HEADLESS DEPLOY', meta: 'Optimized custom PHP backend with hardened REST API endpoints.' },
        'JAVASCRIPT': { cat: 'CLIENT // HYDRATION', meta: 'Native ES6+ micro-interactions and asynchronous event pipelines.' },
        'MYSQL': { cat: 'DATABASE // PERSISTENCE', meta: 'High-throughput relational schema with indexed query caching.' },
        'SHOPIFY': { cat: 'COMMERCE // STOREFRONT', meta: 'Headless Liquid theme engine, friction-free checkout, API sync.' },
        'PAYMENT GATEWAYS': { cat: 'COMMERCE // FINANCIAL', meta: 'Direct PCI-DSS compliant Stripe & PayPal checkout reconciliation.' },
        'CATALOGUE DATA': { cat: 'COMMERCE // INVENTORY', meta: 'High-density product indexing with instant faceted search.' },
        'ORDER RECONCILIATION': { cat: 'COMMERCE // AUDIT', meta: 'Automated gateway payout balancing & multi-currency ledgering.' },
        'SSL & TLS CONFIG': { cat: 'SECURITY // ENCRYPTION', meta: 'Strict HSTS headers, TLS 1.3 protocol, AES-256 baseline cipher.' },
        'WAF RULES': { cat: 'SECURITY // DEFENSE', meta: 'Automated bot filtering, rate limiting, and SQLi protection shields.' },
        'DEPENDENCY UPDATES': { cat: 'SECURITY // HARDENING', meta: 'Continuous vulnerability patch auditing and zero-bloat package audits.' },
        'ACCESS & ROLES': { cat: 'SECURITY // AUTH', meta: 'Granular role-based access control and session token rotation.' },
        'BACKUP CHECKS': { cat: 'SECURITY // SNAPSHOT', meta: 'Encrypted offsite point-in-time snapshot recovery pipelines.' },
        'GOOGLE ADS': { cat: 'GROWTH // ACQUISITION', meta: 'High-intent search & shopping campaign funnels calibrated for ROAS.' },
        'META ADS': { cat: 'GROWTH // SOCIAL MATRIX', meta: 'Direct-response short-form video creative & conversion pixel sync.' },
        'ANALYTICS 4': { cat: 'GROWTH // ATTRIBUTION', meta: 'Server-side GTM event tracking & 100% attributed checkout reporting.' },
        'TAG MANAGER': { cat: 'GROWTH // CONTAINER', meta: 'Asynchronous zero-latency script injection and consent mode.' },
        'SEARCH CONSOLE': { cat: 'GROWTH // INDEXING', meta: 'Core Web Vitals indexing health & organic search intent tracking.' },
        'EMAIL CAMPAIGNS': { cat: 'GROWTH // RETENTION', meta: 'High-deliverability automated lifecycle copy flows.' },
        'STAGING & DEPLOYS': { cat: 'OPERATIONS // DEPLOYMENT', meta: 'Zero-downtime CI/CD deployment pipelines & staging isolation.' },
        'CORE WEB VITALS': { cat: 'OPERATIONS // PERFORMANCE', meta: '100/100 Lighthouse performance engineering & LCP optimization.' },
        'LOG REVIEW': { cat: 'OPERATIONS // TELEMETRY', meta: 'Continuous error log review & proactive bug mitigation.' },
        'EDITORIAL CALENDAR': { cat: 'OPERATIONS // CONTENT', meta: 'Synchronized multi-surface brand distribution timeline.' }
    };

    let mouseX = 0;
    let mouseY = 0;

    window.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
            mouseX = (e.clientX - rect.left - rect.width / 2) / (rect.width / 2);
            mouseY = (e.clientY - rect.top - rect.height / 2) / (rect.height / 2);
        }
    }, { passive: true });

    items.forEach((item, idx) => {
        const nameElem = item.querySelector('.tk-item-name');
        const rawName = nameElem ? nameElem.textContent.trim().toUpperCase() : '';

        // Depth tier assignment based on index
        const depthFactor = (idx % 3 === 0) ? 14 : (idx % 2 === 0 ? 8 : 2);
        item.style.transition = 'transform 0.4s cubic-bezier(.16,1,.3,1), opacity 0.3s cubic-bezier(.22,1,.36,1), filter 0.3s cubic-bezier(.22,1,.36,1)';

        item.addEventListener('mouseenter', () => {
            items.forEach((sibling) => {
                if (sibling !== item) {
                    sibling.style.opacity = '0.45';
                    sibling.style.filter = 'blur(1.5px)';
                    sibling.style.transform = 'translate3d(0, 0, 0) scale(0.97)';
                }
            });

            item.style.opacity = '1';
            item.style.filter = 'none';
            item.style.transform = `translate3d(0, -6px, 20px) scale(1.06) rotateZ(-0.5deg)`;
            item.classList.add('is-picked-up');

            if (inspector && rawName && SPECIMENS[rawName]) {
                const info = SPECIMENS[rawName];
                if (inspectName) inspectName.textContent = rawName;
                if (inspectCategory) inspectCategory.textContent = info.cat;
                if (inspectMeta) inspectMeta.textContent = info.meta;
                inspector.classList.add('is-active');
            }
        });

        item.addEventListener('mouseleave', () => {
            items.forEach((sibling) => {
                sibling.style.opacity = '1';
                sibling.style.filter = 'none';
                sibling.style.transform = 'translate3d(0, 0, 0) scale(1)';
            });
            item.classList.remove('is-picked-up');
            if (inspector) inspector.classList.remove('is-active');
        });
    });

    // Ambient 3D Parallax Damped Render Loop
    function updateParallax() {
        const rect = container.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
            items.forEach((item, idx) => {
                if (!item.classList.contains('is-picked-up')) {
                    const factor = (idx % 3 === 0) ? 12 : (idx % 2 === 0 ? 6 : 2);
                    const shiftX = mouseX * factor;
                    const shiftY = mouseY * factor;
                    item.style.transform = `translate3d(${shiftX}px, ${shiftY}px, 0)`;
                }
            });
        }
        requestAnimationFrame(updateParallax);
    }

    requestAnimationFrame(updateParallax);
}
