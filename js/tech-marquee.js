/**
 * tech-marquee.js — Technology Stack Ecosystem Marquee & Info Card Hover
 */

export function initTechMarquee() {
    const marqueeSection = document.querySelector('[data-tech-marquee]');
    if (!marqueeSection) return;

    const track = marqueeSection.querySelector('.marquee-track');
    const infoCard = marqueeSection.querySelector('.tech-info-card');
    const cardTitle = marqueeSection.querySelector('.tech-card-title');
    const cardDesc = marqueeSection.querySelector('.tech-card-desc');

    const techInfoMap = {
        'PHP': 'Core server-side execution environment for high-speed dynamic applications.',
        'Laravel': 'Production web application framework engineered for robust architecture.',
        'JavaScript': 'ESNext interactive client-side application logic and micro-flows.',
        'MySQL': 'Relational data store optimized for sub-millisecond query execution.',
        'Shopify': 'Scalable e-commerce engine with custom Liquid & Hydrogen storefronts.',
        'WordPress': 'Custom head-less or monolithic CMS systems with zero plugin bloat.',
        'Google Ads': 'High-intent search acquisition campaigns with attributed conversion tracking.',
        'Analytics': 'Server-side GA4 & custom telemetry tracking customer lifecycle behavior.',
        'SSL': 'TLS 1.3 cryptographic transport security with strict HSTS security headers.',
        'WAF': 'Edge Web Application Firewall protecting against OWASP Top 10 vulnerabilities.'
    };

    const techItems = marqueeSection.querySelectorAll('.tech-item');

    techItems.forEach(item => {
        item.addEventListener('mouseenter', (e) => {
            const name = item.dataset.techName || item.textContent.trim();
            const desc = techInfoMap[name] || 'Enterprise infrastructure component.';

            if (cardTitle) cardTitle.textContent = name;
            if (cardDesc) cardDesc.textContent = desc;

            if (infoCard) {
                infoCard.classList.add('is-visible');
                const rect = item.getBoundingClientRect();
                const containerRect = marqueeSection.getBoundingClientRect();
                infoCard.style.left = `${rect.left - containerRect.left + rect.width / 2}px`;
            }
        });

        item.addEventListener('mouseleave', () => {
            if (infoCard) infoCard.classList.remove('is-visible');
        });
    });
}
