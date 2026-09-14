/**
 * peace-pipeline.js — P.E.A.C.E. Framework Tracing Beam & How We Work Pipeline
 *
 * Uses GSAP ScrollTrigger to progressively draw SVG tracing paths,
 * activate nodes, update status indicators, and reveal stage details on scroll.
 */

export function initPeacePipeline() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    // 1. P.E.A.C.E. Framework Tracing Beam
    const peaceSection = document.querySelector('[data-peace-framework]');
    if (peaceSection) {
        const tracingBeam = peaceSection.querySelector('.peace-tracing-beam');
        const nodes = peaceSection.querySelectorAll('.peace-node');
        const cards = peaceSection.querySelectorAll('.peace-card');

        if (tracingBeam) {
            const pathLength = tracingBeam.getTotalLength ? tracingBeam.getTotalLength() : 1000;
            gsap.set(tracingBeam, {
                strokeDasharray: pathLength,
                strokeDashoffset: pathLength
            });

            const peaceTl = gsap.timeline({
                scrollTrigger: {
                    trigger: peaceSection,
                    start: 'top 70%',
                    end: 'bottom 30%',
                    scrub: 0.5
                }
            });

            peaceTl.to(tracingBeam, {
                strokeDashoffset: 0,
                ease: 'none'
            });

            // Activate individual nodes as beam reaches them
            nodes.forEach((node, idx) => {
                ScrollTrigger.create({
                    trigger: node,
                    start: 'top 75%',
                    end: 'bottom 25%',
                    onEnter: () => {
                        nodes.forEach((n, i) => n.classList.toggle('is-active', i === idx));
                        cards.forEach((c, i) => c.classList.toggle('is-active', i === idx));
                    },
                    onLeaveBack: () => {
                        if (idx > 0) {
                            nodes.forEach((n, i) => n.classList.toggle('is-active', i === idx - 1));
                            cards.forEach((c, i) => c.classList.toggle('is-active', i === idx - 1));
                        }
                    }
                });
            });
        }
    }

    // 2. How We Work Engineering Pipeline
    const pipelineSection = document.querySelector('[data-engineering-pipeline]');
    if (pipelineSection) {
        const pipelinePath = pipelineSection.querySelector('.pipeline-svg-path');
        const stages = pipelineSection.querySelectorAll('.pipeline-stage-node');

        if (pipelinePath) {
            const length = pipelinePath.getTotalLength ? pipelinePath.getTotalLength() : 800;
            gsap.set(pipelinePath, {
                strokeDasharray: length,
                strokeDashoffset: length
            });

            gsap.to(pipelinePath, {
                strokeDashoffset: 0,
                ease: 'none',
                scrollTrigger: {
                    trigger: pipelineSection,
                    start: 'top 80%',
                    end: 'bottom 20%',
                    scrub: 0.6
                }
            });

            stages.forEach((stage) => {
                gsap.fromTo(stage, 
                    { opacity: 0.4, y: 20 },
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        scrollTrigger: {
                            trigger: stage,
                            start: 'top 85%',
                            toggleActions: 'play none none reverse'
                        }
                    }
                );
            });
        }
    }
}
