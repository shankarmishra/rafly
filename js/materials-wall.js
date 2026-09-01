/**
 * materials-wall.js — Physical Studio Materials Specimen Workbench for Section 07
 * Handles tactile hover deformation, Z-axis translate, scale variation, and specimen inspection.
 */

export function initMaterialsWall(container) {
    if (!container) return;

    const items = container.querySelectorAll('.tk-item');
    const inspector = container.querySelector('[data-tk-inspector]');
    const inspectName = container.querySelector('.tk-inspect-name');
    const inspectCategory = container.querySelector('.tk-inspect-cat');
    const inspectMeta = container.querySelector('.tk-inspect-meta');

    const SPECIMEN_DATA = {
        'PHP': { cat: 'BUILD ENGINE', meta: 'Server-side velocity, zero overhead, sub-15ms execution' },
        'LARAVEL': { cat: 'BUILD ENGINE', meta: 'Robust application framework for complex data structures' },
        'WORDPRESS': { cat: 'BUILD ENGINE', meta: 'Headless / optimized CMS deployment with custom PHP architecture' },
        'JAVASCRIPT': { cat: 'BUILD ENGINE', meta: 'Native ES6+ micro-interactions and asynchronous client hydration' },
        'MYSQL': { cat: 'BUILD ENGINE', meta: 'High-throughput relational data persistence' },
        'SHOPIFY': { cat: 'COMMERCE ENGINE', meta: 'Liquid customization, headless checkout, zero transaction friction' },
        'PAYMENT GATEWAYS': { cat: 'COMMERCE ENGINE', meta: 'Stripe, PayPal, Razorpay PCI-DSS compliant direct integrations' },
        'CATALOGUE DATA': { cat: 'COMMERCE ENGINE', meta: 'High-density product inventory indexing & search' },
        'ORDER RECONCILIATION': { cat: 'COMMERCE ENGINE', meta: 'Automated gateway transaction balancing' },
        'SSL & TLS CONFIG': { cat: 'SECURITY DEFENSE', meta: 'HSTS, TLS 1.3, AES-256 encryption baseline' },
        'WAF RULES': { cat: 'SECURITY DEFENSE', meta: 'Automated bot filtering, rate limiting & DDOS shielding' },
        'DEPENDENCY UPDATES': { cat: 'SECURITY DEFENSE', meta: 'Automated patch auditing & zero-vulnerability guarantee' },
        'ACCESS & ROLES': { cat: 'SECURITY DEFENSE', meta: 'Granular RBAC permission enforcement' },
        'BACKUP CHECKS': { cat: 'SECURITY DEFENSE', meta: 'Encrypted off-site snapshot retention' },
        'GOOGLE ADS': { cat: 'GROWTH ENGINE', meta: 'High-intent search & remarketing conversion funnels' },
        'META ADS': { cat: 'GROWTH ENGINE', meta: 'Direct-response short-form video creative & ROAS scaling' },
        'ANALYTICS 4': { cat: 'GROWTH ENGINE', meta: 'Server-side GTM event attribution & funnel tracking' },
        'TAG MANAGER': { cat: 'GROWTH ENGINE', meta: 'Zero-latency asynchronous script container' },
        'SEARCH CONSOLE': { cat: 'GROWTH ENGINE', meta: 'Organic indexing health & search intent optimization' },
        'EMAIL CAMPAIGNS': { cat: 'GROWTH ENGINE', meta: 'High-deliverability revenue retention flows' },
        'STAGING & DEPLOYS': { cat: 'OPERATIONS', meta: 'Zero-downtime CI/CD deployment pipelines' },
        'CORE WEB VITALS': { cat: 'OPERATIONS', meta: '100/100 Lighthouse performance engineering' },
        'LOG REVIEW': { cat: 'OPERATIONS', meta: 'Continuous telemetry & exception monitoring' },
        'EDITORIAL CALENDAR': { cat: 'OPERATIONS', meta: 'Synchronized multi-surface content schedule' }
    };

    items.forEach((item) => {
        const textElem = item.querySelector('.tk-item-name');
        const toolName = textElem ? textElem.textContent.trim().toUpperCase() : '';

        item.addEventListener('mouseenter', () => {
            // Recede siblings subtly
            items.forEach((sibling) => {
                if (sibling !== item) {
                    sibling.style.opacity = '0.35';
                    sibling.style.filter = 'blur(1px)';
                }
            });

            item.style.opacity = '1';
            item.style.filter = 'none';

            // Update Specimen Inspector if present
            if (inspector && toolName && SPECIMEN_DATA[toolName]) {
                const data = SPECIMEN_DATA[toolName];
                if (inspectName) inspectName.textContent = toolName;
                if (inspectCategory) inspectCategory.textContent = data.cat;
                if (inspectMeta) inspectMeta.textContent = data.meta;
                inspector.classList.add('is-active');
            }
        });

        item.addEventListener('mouseleave', () => {
            items.forEach((sibling) => {
                sibling.style.opacity = '1';
                sibling.style.filter = 'none';
            });
            if (inspector) inspector.classList.remove('is-active');
        });
    });
}
