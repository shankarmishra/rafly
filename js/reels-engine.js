/**
 * reels-engine.js — Cinematic 5-Phone Scroll-Driven Content Engine Motion
 *
 * IDEA → CONTENT → DISTRIBUTION → RESPONSE → CONVERSION
 *
 * SCROLL CHOREOGRAPHY (rAF-driven smooth progress from section scroll):
 *   0.00 - 0.22   PHASE 01 (Strategy): Hero phone settles in center, Badge 1 reveals, Phase 01 active.
 *   0.22 - 0.48   PHASE 02 (Scripting): Phone 02 (Left Story) fans out on curved 3D trajectory, Phase 02 active.
 *   0.45 - 0.70   PHASE 03 (Production): Phone 03 (Right Case Study) fans out to right, Phase 03 active.
 *   0.65 - 0.88   PHASE 04 (Distribution): Phone 04 (Rear-Left 4K Studio) emerges into depth, Phase 04 active.
 *   0.75 - 0.98   PHASE 05 (Conversion): Phone 05 (Rear-Right Ledger) emerges to right depth, Phase 05 active.
 *   0.98 - 1.00   HOLD: Complete 5-phone synchronized system holds before unpin release.
 *
 * Pure transform & opacity. Zero layout shifts. 100% GPU accelerated.
 */

const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
const lerp = (a, b, n) => a + (b - a) * n;
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
const easeInOutCubic = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

/**
 * 5-Phone Target Geometry in 3D Space (px, deg)
 * Slot 0: Phone 01 (Hero Center)
 * Slot 1: Phone 02 (Mid-Left Support - Story Funnel)
 * Slot 2: Phone 03 (Mid-Right Support - Case Study)
 * Slot 3: Phone 04 (Rear-Left - 4K Motion Studio)
 * Slot 4: Phone 05 (Rear-Right - Commerce Ledger)
 */
const PHONE_GEOMETRY = [
    { x: 0,    y: 0,   z: 60,   ry: 0,   rz: 0,   s: 1.00, opacity: 1.00, zIndex: 10 },
    { x: -160, y: 6,   z: -24,  ry: 13,  rz: 1.2, s: 0.88, opacity: 0.96, zIndex: 8  },
    { x: 160,  y: 6,   z: -24,  ry: -13, rz: -1.2, s: 0.88, opacity: 0.96, zIndex: 8  },
    { x: -275, y: -12, z: -85,  ry: 18,  rz: 2.2, s: 0.78, opacity: 0.84, zIndex: 6  },
    { x: 275,  y: 12,  z: -85,  ry: -18, rz: -2.2, s: 0.78, opacity: 0.84, zIndex: 6  },
];

const PHASE_STATUS_TEXTS = [
    '🔴 Live Reel &bull; 1080p 60FPS',
    '⚡ Story Funnel &bull; 3s Hook',
    '📊 Case Study &bull; 4.8x ROAS',
    '🎬 4K Motion &bull; Studio Polish',
    '🟢 Revenue Sync &bull; Attributed'
];

export function initReelsEngine(section) {
    if (!section) return null;

    const stage = section.querySelector('[data-reels-stage]');
    if (!stage) return null;

    const heroPhone = stage.querySelector('.phone-hero-center');
    const leftPhone = stage.querySelector('.phone-mid-left');
    const rightPhone = stage.querySelector('.phone-mid-right');
    const rearLeftPhone = stage.querySelector('.phone-rear-left');
    const rearRightPhone = stage.querySelector('.phone-rear-right');

    const phones = [heroPhone, leftPhone, rightPhone, rearLeftPhone, rearRightPhone].filter(Boolean);
    const badges = [...section.querySelectorAll('.reels-proof-badge')];
    const navTabs = [...section.querySelectorAll('[data-reels-nav] .rpn-tab')];
    const serviceCards = [...section.querySelectorAll('[data-reels-rail] .reels-service-card')];
    const stepCounter = section.querySelector('[data-reels-step]');
    const ambientAura = section.querySelector('.reels-ambient-aura');
    const diStatus = section.querySelector('[data-di-status]');
    const likeBtn = section.querySelector('[data-reel-like]');
    const likeCount = section.querySelector('[data-like-count]');
    const videos = [...section.querySelectorAll('video')];

    let eased = 0;
    let raf = 0;
    let running = false;
    let currentStep = -1;

    // --- 1. Video Autoplay Initializer ---
    videos.forEach((v) => {
        v.muted = true;
        v.playsInline = true;
        v.setAttribute('muted', '');
        v.setAttribute('playsinline', '');
        v.setAttribute('loop', '');
        v.play().catch(() => {});
    });

    // --- 2. Interactive Reel Like Button ---
    if (likeBtn && likeCount) {
        let isLiked = true;
        likeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            isLiked = !isLiked;
            const icon = likeBtn.querySelector('.rau-icon') || likeBtn.querySelector('.ra-icon');
            if (icon) icon.classList.toggle('is-liked', isLiked);
            likeCount.textContent = isLiked ? '48.9K' : '48.8K';
        });
    }

    // --- 3. Click-to-Scrub Navigation ---
    const phaseScrollTargets = [0.10, 0.35, 0.58, 0.78, 0.92];

    const scrollToPhase = (phaseIdx) => {
        const targetRatio = phaseScrollTargets[phaseIdx] ?? 0.5;
        const secTop = section.getBoundingClientRect().top + window.scrollY;
        const scrollDistance = (section.offsetHeight - window.innerHeight) * targetRatio;
        window.scrollTo({
            top: secTop + scrollDistance,
            behavior: 'smooth'
        });
    };

    navTabs.forEach((tab, idx) => {
        tab.addEventListener('click', () => scrollToPhase(idx));
    });

    serviceCards.forEach((card, idx) => {
        card.addEventListener('click', () => scrollToPhase(idx));
    });

    // --- 4. Update Step & Active Highlights ---
    function updateStep(stepIndex) {
        if (stepIndex === currentStep) return;
        currentStep = stepIndex;

        if (stepCounter) {
            stepCounter.textContent = `0${Math.min(5, Math.max(1, stepIndex + 1))}`;
        }

        navTabs.forEach((tab, i) => {
            const isActive = i === stepIndex;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        serviceCards.forEach((card, i) => {
            card.classList.toggle('is-active', i === stepIndex);
        });

        if (diStatus && PHASE_STATUS_TEXTS[stepIndex]) {
            diStatus.innerHTML = PHASE_STATUS_TEXTS[stepIndex];
        }
    }

    // --- 5. Render Scroll Animation Frame ---
    function render(p) {
        // p is normalized scroll progress 0.0 -> 1.0
        const isReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const isMobile = window.innerWidth <= 900;

        if (isReduced) {
            phones.forEach((phone, idx) => {
                const g = PHONE_GEOMETRY[idx];
                if (!phone || !g) return;
                phone.style.transform = `translate3d(${g.x * 0.8}px, ${g.y}px, ${g.z}px) rotateY(${g.ry}deg) scale(${g.s})`;
                phone.style.opacity = String(g.opacity);
                phone.style.zIndex = String(g.zIndex);
            });
            updateStep(0);
            return;
        }

        // --- Ambient Aura Scale & Glow
        if (ambientAura) {
            const auraScale = lerp(0.9, 1.2, easeOutCubic(p));
            const auraOpacity = lerp(0.45, 0.75, easeOutCubic(p));
            ambientAura.style.transform = `translate(-50%, -50%) scale(${auraScale})`;
            ambientAura.style.opacity = String(auraOpacity);
        }

        // --- Determine Active Step (0 -> 4)
        let activeStep = 0;
        if (p >= 0.82) activeStep = 4;
        else if (p >= 0.62) activeStep = 3;
        else if (p >= 0.42) activeStep = 2;
        else if (p >= 0.20) activeStep = 1;
        else activeStep = 0;
        updateStep(activeStep);

        const scaleMod = isMobile ? 0.75 : 1.0;
        const xMod = isMobile ? 0.6 : 1.0;

        // --- Phone 01: Hero Center (Progress 0.00 -> 0.22)
        const p1 = easeInOutCubic(clamp01((p - 0.01) / 0.21));
        if (heroPhone) {
            const y1 = lerp(15, 0, p1);
            const s1 = lerp(0.94, 1.00, p1);
            const op1 = lerp(0.5, 1.00, p1);
            const z1 = lerp(10, 60, p1);
            heroPhone.style.transform = `translate3d(0px, ${y1}px, ${z1}px) rotateY(0deg) scale(${s1})`;
            heroPhone.style.opacity = String(op1);
            heroPhone.style.zIndex = '10';
        }

        // --- Phone 02: Left Support (Progress 0.20 -> 0.50)
        const p2 = easeInOutCubic(clamp01((p - 0.18) / 0.30));
        if (leftPhone) {
            const g2 = PHONE_GEOMETRY[1];
            const x2 = lerp(0, g2.x * xMod, p2);
            const y2 = lerp(10, g2.y, p2);
            const z2 = lerp(-60, g2.z, p2);
            const ry2 = lerp(0, g2.ry, p2);
            const rz2 = lerp(0, g2.rz, p2);
            const s2 = lerp(0.80, g2.s * scaleMod, p2);
            const op2 = lerp(0, g2.opacity, p2);

            leftPhone.style.transform = `translate3d(${x2}px, ${y2}px, ${z2}px) rotateY(${ry2}deg) rotateZ(${rz2}deg) scale(${s2})`;
            leftPhone.style.opacity = String(op2);
            leftPhone.style.zIndex = String(g2.zIndex);
            leftPhone.style.pointerEvents = p2 > 0.6 ? 'auto' : 'none';
        }

        // --- Phone 03: Right Support (Progress 0.38 -> 0.70)
        const p3 = easeInOutCubic(clamp01((p - 0.35) / 0.32));
        if (rightPhone) {
            const g3 = PHONE_GEOMETRY[2];
            const x3 = lerp(0, g3.x * xMod, p3);
            const y3 = lerp(10, g3.y, p3);
            const z3 = lerp(-60, g3.z, p3);
            const ry3 = lerp(0, g3.ry, p3);
            const rz3 = lerp(0, g3.rz, p3);
            const s3 = lerp(0.80, g3.s * scaleMod, p3);
            const op3 = lerp(0, g3.opacity, p3);

            rightPhone.style.transform = `translate3d(${x3}px, ${y3}px, ${z3}px) rotateY(${ry3}deg) rotateZ(${rz3}deg) scale(${s3})`;
            rightPhone.style.opacity = String(op3);
            rightPhone.style.zIndex = String(g3.zIndex);
            rightPhone.style.pointerEvents = p3 > 0.6 ? 'auto' : 'none';
        }

        // --- Phone 04: Rear-Left Depth (Progress 0.58 -> 0.86)
        const p4 = easeInOutCubic(clamp01((p - 0.55) / 0.28));
        if (rearLeftPhone) {
            const g4 = PHONE_GEOMETRY[3];
            const x4 = lerp(0, g4.x * xMod, p4);
            const y4 = lerp(12, g4.y, p4);
            const z4 = lerp(-110, g4.z, p4);
            const ry4 = lerp(0, g4.ry, p4);
            const rz4 = lerp(0, g4.rz, p4);
            const s4 = lerp(0.72, g4.s * scaleMod, p4);
            const op4 = lerp(0, g4.opacity, p4);

            rearLeftPhone.style.transform = `translate3d(${x4}px, ${y4}px, ${z4}px) rotateY(${ry4}deg) rotateZ(${rz4}deg) scale(${s4})`;
            rearLeftPhone.style.opacity = String(op4);
            rearLeftPhone.style.zIndex = String(g4.zIndex);
            rearLeftPhone.style.pointerEvents = p4 > 0.6 ? 'auto' : 'none';
        }

        // --- Phone 05: Rear-Right Depth (Progress 0.68 -> 0.94)
        const p5 = easeInOutCubic(clamp01((p - 0.65) / 0.26));
        if (rearRightPhone) {
            const g5 = PHONE_GEOMETRY[4];
            const x5 = lerp(0, g5.x * xMod, p5);
            const y5 = lerp(12, g5.y, p5);
            const z5 = lerp(-110, g5.z, p5);
            const ry5 = lerp(0, g5.ry, p5);
            const rz5 = lerp(0, g5.rz, p5);
            const s5 = lerp(0.72, g5.s * scaleMod, p5);
            const op5 = lerp(0, g5.opacity, p5);

            rearRightPhone.style.transform = `translate3d(${x5}px, ${y5}px, ${z5}px) rotateY(${ry5}deg) rotateZ(${rz5}deg) scale(${s5})`;
            rearRightPhone.style.opacity = String(op5);
            rearRightPhone.style.zIndex = String(g5.zIndex);
            rearRightPhone.style.pointerEvents = p5 > 0.6 ? 'auto' : 'none';
        }

        // --- Floating Proof Badges Sequential Reveal
        badges.forEach((badge, idx) => {
            const triggers = [0.16, 0.38, 0.60, 0.80];
            const t = triggers[idx] ?? 0.5;
            const lp = easeOutCubic(clamp01((p - t) / 0.14));
            const yOff = lerp(16, 0, lp);
            badge.style.transform = `translate3d(0, ${yOff}px, 0)`;
            badge.style.opacity = String(lp);
            badge.style.pointerEvents = lp > 0.7 ? 'auto' : 'none';
        });
    }

    // --- 6. rAF Animation Loop ---
    function frame() {
        const r = section.getBoundingClientRect();
        const span = r.height - window.innerHeight;
        const raw = span > 0 ? clamp01(-r.top / span) : 1;
        eased = lerp(eased, raw, 0.09);

        render(eased);
        raf = requestAnimationFrame(frame);
    }

    const start = () => {
        if (!running) {
            running = true;
            raf = requestAnimationFrame(frame);
        }
    };

    const stop = () => {
        running = false;
        cancelAnimationFrame(raf);
    };

    // --- 7. IntersectionObserver for Frame Budget Preservation ---
    const io = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                start();
                videos.forEach((v) => v.play().catch(() => {}));
            } else {
                stop();
                videos.forEach((v) => v.pause());
            }
        },
        { rootMargin: '20% 0px' }
    );
    io.observe(section);

    const onVisibility = () => {
        if (document.hidden) stop();
        else start();
    };
    document.addEventListener('visibilitychange', onVisibility);

    section.classList.add('is-live');

    return function destroy() {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        section.classList.remove('is-live');
        phones.forEach((p) => {
            if (p) {
                p.style.transform = '';
                p.style.opacity = '';
            }
        });
    };
}
