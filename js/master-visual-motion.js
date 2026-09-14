/**
 * master-visual-motion.js — Master Motion & Visual Orchestrator for RAFLY
 *
 * Integrates all 12 Phases:
 * 1. Hero 3D Glass Platform Core
 * 2. Topology Node System (Multiple Services / One Partner)
 * 3. Chaos -> System Motion
 * 4. P.E.A.C.E. Framework Tracing Beam & How We Work Engineering Pipeline
 * 5. 5 Service Visual Metaphors (Web, Security, Marketing, Content, Commerce)
 * 6. Technology Stack Ecosystem Marquee
 * 7. Horizontal Scroll Case Studies
 * 8. FAQ Accordion & Pricing Micro-Interactions
 * 9. Final CTA Flowing SVG Lines & Spotlight
 */

import { initHeroGlassCore } from './hero-glass-core.js';
import { initTopologySystem } from './topology-system.js';
import { initPeacePipeline } from './peace-pipeline.js';
import { initServiceVisuals } from './service-visuals.js';
import { initTechMarquee } from './tech-marquee.js';
import { initCaseStudiesScroll } from './case-studies-scroll.js';

export function initMasterVisualMotion() {
    // 1. Hero 3D Core (if present)
    const heroGlassContainer = document.getElementById('hero-glass-core');
    if (heroGlassContainer) {
        initHeroGlassCore(heroGlassContainer);
    }

    // 2. Topology System & Chaos -> System
    initTopologySystem();

    // 3. P.E.A.C.E. & Engineering Pipeline
    initPeacePipeline();

    // 4. Service Visual Metaphors
    initServiceVisuals();

    // 5. Tech Stack Marquee
    initTechMarquee();

    // 6. Case Studies Horizontal Scroll
    initCaseStudiesScroll();

    // 7. FAQ Accordion Polish
    initFaqAccordion();

    // 8. Final CTA Spotlight Effect
    initCtaSpotlight();
}

function initFaqAccordion() {
    const faqItems = document.querySelectorAll('.faq-item, [data-faq-item]');
    faqItems.forEach(item => {
        const trigger = item.querySelector('.faq-q, [data-faq-trigger]');
        const answer = item.querySelector('.faq-a, [data-faq-answer]');
        const indicator = item.querySelector('.faq-icon, .faq-indicator');

        if (trigger && answer) {
            trigger.addEventListener('click', () => {
                const isOpen = item.classList.contains('is-open');

                // Close other items
                faqItems.forEach(other => {
                    if (other !== item) {
                        other.classList.remove('is-open');
                        const otherAns = other.querySelector('.faq-a, [data-faq-answer]');
                        const otherInd = other.querySelector('.faq-icon, .faq-indicator');
                        if (otherAns) otherAns.style.maxHeight = null;
                        if (otherInd) otherInd.textContent = '+';
                    }
                });

                item.classList.toggle('is-open', !isOpen);
                if (!isOpen) {
                    answer.style.maxHeight = `${answer.scrollHeight}px`;
                    if (indicator) indicator.textContent = '−';
                } else {
                    answer.style.maxHeight = null;
                    if (indicator) indicator.textContent = '+';
                }
            });
        }
    });
}

function initCtaSpotlight() {
    const ctaSection = document.querySelector('#start, .final-cta-section');
    if (!ctaSection) return;

    let isTicking = false;
    ctaSection.addEventListener('mousemove', (e) => {
        if (!isTicking) {
            requestAnimationFrame(() => {
                const rect = ctaSection.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                ctaSection.style.setProperty('--spotlight-x', `${x}px`);
                ctaSection.style.setProperty('--spotlight-y', `${y}px`);
                isTicking = false;
            });
            isTicking = true;
        }
    }, { passive: true });
}

// Auto-run if loaded as page module
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMasterVisualMotion);
} else {
    initMasterVisualMotion();
}
