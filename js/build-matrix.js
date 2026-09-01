/**
 * js/build-matrix.js — THE RAFly BUILD MATRIX Controller
 *
 * Internal Concept: "THE RAFly BUILD MATRIX"
 * Core Message: FOUR CAPABILITIES. ONE TEAM BEHIND THEM.
 *
 * Responsibilities:
 *   • Canvas2D Render Engine for 4 distinct architectural build artifacts:
 *       01 Strategy: Strategic positioning map & signal vectors
 *       02 UX/UI: Responsive interface layout frames & guides
 *       03 Web & App: Multi-viewport architectural code & browser structure
 *       04 Growth: Performance acquisition trajectories & audience nodes
 *   • Mode Transition Timeline (dissolve -> particles -> assemble -> lock)
 *   • Damped Mouse Parallax (pitch ±2.5°, yaw ±4°)
 *   • Keyboard & Touch Accessible Index Tabs
 *   • IntersectionObserver Lifecycle & Reduced Motion support
 */

const lerp = (a, b, n) => a + (b - a) * n;

export function initBuildMatrix(host) {
    if (!host) return () => {};

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── DOM REFS ── */
    const canvas  = host.querySelector('[data-bm-canvas]');
    const stage   = host.querySelector('[data-bm-stage]');
    const navItems= Array.from(host.querySelectorAll('[data-bm-target]'));
    const codeTag = host.querySelector('[data-bm-code]');
    const stepTag = host.querySelector('[data-bm-step]');

    if (!canvas) return () => {};
    const ctx = canvas.getContext('2d');
    if (!ctx) return () => {};

    /* ── CONFIG & STATE ── */
    const MODES = {
        strategy: { code: 'SYS.STRATEGY // 01', step: '01 / 04', color: { h: 220, s: 100, l: 52 } },
        uxui:     { code: 'SYS.UXUI // 02',     step: '02 / 04', color: { h: 260, s: 78,  l: 58 } },
        webdev:   { code: 'SYS.WEBDEV // 03',   step: '03 / 04', color: { h: 215, s: 100, l: 48 } },
        growth:   { code: 'SYS.GROWTH // 04',   step: '04 / 04', color: { h: 280, s: 75,  l: 55 } },
    };

    let activeMode = 'strategy';
    let targetMode = 'strategy';
    let transitionProgress = 1.0; // 0 = start transition, 1 = complete
    let isTransitioning = false;
    let isVisible = true;
    let raf = 0;
    let t0 = performance.now();
    let lastTs = performance.now();

    // Pointer lerp
    let pxTarget = 0, pyTarget = 0;
    let pxCurr = 0, pyCurr = 0;

    /* ── CANVAS DPI RESIZE ── */
    const DPR = Math.min(window.devicePixelRatio || 1, 2);
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        const w = rect.width  || 1180;
        const h = rect.height || 580;
        canvas.width  = w * DPR;
        canvas.height = h * DPR;
        ctx.scale(DPR, DPR);
        canvas._w = w;
        canvas._h = h;
    }
    resizeCanvas();
    new ResizeObserver(resizeCanvas).observe(canvas);

    /* ── MODE SWITCHING & SCROLL SYNC ── */
    const modeKeys = ['strategy', 'uxui', 'webdev', 'growth'];
    let userClicked = false;

    function setMode(modeKey) {
        if (!MODES[modeKey] || modeKey === targetMode) return;
        targetMode = modeKey;
        transitionProgress = 0;
        isTransitioning = true;

        navItems.forEach((btn) => {
            const isMatch = btn.dataset.bmTarget === modeKey;
            btn.classList.toggle('is-active', isMatch);
            btn.setAttribute('aria-selected', isMatch ? 'true' : 'false');
        });

        if (codeTag) codeTag.textContent = MODES[modeKey].code;
        if (stepTag) stepTag.textContent = MODES[modeKey].step;
    }

    function checkScrollMode() {
        if (userClicked) return;
        const rect = host.getBoundingClientRect();
        const vh = window.innerHeight || 800;
        if (rect.top > vh * 0.85 || rect.bottom < vh * 0.15) return;

        const total = Math.max(1, rect.height - vh * 0.4);
        const current = vh * 0.6 - rect.top;
        const progress = Math.max(0, Math.min(0.99, current / total));
        const modeIdx = Math.floor(progress * modeKeys.length);
        const autoMode = modeKeys[modeIdx];
        if (autoMode && autoMode !== targetMode) {
            setMode(autoMode);
        }
    }

    window.addEventListener('scroll', checkScrollMode, { passive: true });

    navItems.forEach((btn) => {
        btn.addEventListener('click', () => {
            userClicked = true;
            setMode(btn.dataset.bmTarget);
        });
        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                userClicked = true;
                setMode(btn.dataset.bmTarget);
            }
        });
    });

    /* ── RENDERERS FOR EACH CAPABILITY ── */

    // 01 STRATEGY MODE
    function renderStrategy(cx, cy, scale, opacity, t) {
        const mode = MODES.strategy;
        ctx.save();
        ctx.globalAlpha = opacity;

        // Concentric positioning target rings
        const radii = [180, 130, 80, 30];
        radii.forEach((r, i) => {
            ctx.beginPath();
            ctx.arc(cx, cy, r * scale, 0, Math.PI * 2);
            ctx.strokeStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,${0.12 + i * 0.05})`;
            ctx.lineWidth = 1.2;
            if (i % 2 === 1) ctx.setLineDash([4, 6]);
            ctx.stroke();
            ctx.setLineDash([]);
        });

        // Market nodes
        const nodes = [
            { label: 'TARGET',    angle: 0.2 + t * 0.1,  dist: 160 },
            { label: 'AUDIENCE',  angle: 1.5 - t * 0.08, dist: 120 },
            { label: 'POSITION',  angle: 3.1 + t * 0.12, dist: 80  },
            { label: 'GROWTH',    angle: 4.6 - t * 0.09, dist: 140 },
            { label: 'VELOCITY',  angle: 5.8 + t * 0.07, dist: 110 },
        ];

        // Signal vector lines connecting nodes
        ctx.beginPath();
        nodes.forEach((n, i) => {
            const nx = cx + Math.cos(n.angle) * n.dist * scale;
            const ny = cy + Math.sin(n.angle) * n.dist * scale;
            i === 0 ? ctx.moveTo(nx, ny) : ctx.lineTo(nx, ny);
        });
        ctx.closePath();
        ctx.strokeStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,0.35)`;
        ctx.lineWidth = 1.4;
        ctx.stroke();

        // Draw nodes & labels
        nodes.forEach((n) => {
            const nx = cx + Math.cos(n.angle) * n.dist * scale;
            const ny = cy + Math.sin(n.angle) * n.dist * scale;

            ctx.beginPath();
            ctx.arc(nx, ny, 4.5, 0, Math.PI * 2);
            ctx.fillStyle = `hsl(${mode.color.h},${mode.color.s}%,${mode.color.l}%)`;
            ctx.shadowBlur = 10;
            ctx.shadowColor = `hsl(${mode.color.h},${mode.color.s}%,${mode.color.l}%)`;
            ctx.fill();

            ctx.font = '600 10px monospace';
            ctx.fillStyle = '#050f33';
            ctx.fillText(n.label, nx + 10, ny + 4);
        });

        ctx.restore();
    }

    // 02 UX/UI MODE
    function renderUXUI(cx, cy, scale, opacity, t) {
        const mode = MODES.uxui;
        ctx.save();
        ctx.globalAlpha = opacity;

        const w = 480 * scale;
        const h = 300 * scale;
        const x = cx - w / 2;
        const y = cy - h / 2;

        // Viewport Frame
        ctx.beginPath();
        ctx.roundRect(x, y, w, h, 12);
        ctx.strokeStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,0.4)`;
        ctx.lineWidth = 1.6;
        ctx.fillStyle = 'rgba(255,255,255,0.7)';
        ctx.fill();
        ctx.stroke();

        // UI Header Bar
        ctx.beginPath();
        ctx.roundRect(x + 16, y + 16, w - 32, 36, 6);
        ctx.fillStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,0.10)`;
        ctx.fill();

        // UI Grid Cards (2 columns)
        const cardW = (w - 48) / 2;
        [0, 1].forEach((col) => {
            const cxPos = x + 16 + col * (cardW + 16);
            ctx.beginPath();
            ctx.roundRect(cxPos, y + 68, cardW, h - 84, 8);
            ctx.fillStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,0.05)`;
            ctx.strokeStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,0.2)`;
            ctx.fill();
            ctx.stroke();
        });

        // Interactive cursor simulation
        const curX = cx + Math.sin(t * 1.2) * 120 * scale;
        const curY = cy + Math.cos(t * 0.9) * 80 * scale;
        ctx.beginPath();
        ctx.moveTo(curX, curY);
        ctx.lineTo(curX + 12, curY + 16);
        ctx.lineTo(curX + 5, curY + 16);
        ctx.lineTo(curX, curY + 22);
        ctx.closePath();
        ctx.fillStyle = '#050f33';
        ctx.fill();

        ctx.restore();
    }

    // 03 WEB DEV MODE
    function renderWebDev(cx, cy, scale, opacity, t) {
        const mode = MODES.webdev;
        ctx.save();
        ctx.globalAlpha = opacity;

        const w = 520 * scale;
        const h = 320 * scale;
        const x = cx - w / 2;
        const y = cy - h / 2;

        // Browser Window Frame
        ctx.beginPath();
        ctx.roundRect(x, y, w, h, 10);
        ctx.fillStyle = '#ffffff';
        ctx.strokeStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,0.35)`;
        ctx.lineWidth = 1.4;
        ctx.fill();
        ctx.stroke();

        // Window Control Dots
        [0, 1, 2].forEach((i) => {
            ctx.beginPath();
            ctx.arc(x + 18 + i * 14, y + 18, 4, 0, Math.PI * 2);
            ctx.fillStyle = i === 0 ? '#ff5f56' : i === 1 ? '#ffbd2e' : '#27c93f';
            ctx.fill();
        });

        // Code Structural Block Lines
        const lineCount = 8;
        for (let i = 0; i < lineCount; i++) {
            const ly = y + 50 + i * 30;
            const lw = (120 + Math.sin(i * 1.5 + t * 2) * 80) * scale;
            ctx.beginPath();
            ctx.roundRect(x + 24, ly, lw, 12, 4);
            ctx.fillStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,${0.15 + (i % 3) * 0.1})`;
            ctx.fill();
        }

        // Mobile Breakpoint Overlay
        const mw = 140 * scale;
        const mh = 220 * scale;
        const mx = x + w - mw - 20;
        const my = y + h - mh - 20;
        ctx.beginPath();
        ctx.roundRect(mx, my, mw, mh, 12);
        ctx.fillStyle = 'rgba(246,248,252,0.95)';
        ctx.strokeStyle = `hsl(${mode.color.h},${mode.color.s}%,${mode.color.l}%)`;
        ctx.lineWidth = 1.5;
        ctx.fill();
        ctx.stroke();

        ctx.restore();
    }

    // 04 GROWTH MODE
    function renderGrowth(cx, cy, scale, opacity, t) {
        const mode = MODES.growth;
        ctx.save();
        ctx.globalAlpha = opacity;

        const w = 500 * scale;
        const h = 280 * scale;
        const x = cx - w / 2;
        const y = cy - h / 2;

        // Trajectory Bezier Curve
        ctx.beginPath();
        ctx.moveTo(x, y + h * 0.85);
        ctx.bezierCurveTo(
            x + w * 0.3, y + h * 0.80,
            x + w * 0.5, y + h * 0.20,
            x + w, y + h * 0.10
        );
        ctx.strokeStyle = `hsl(${mode.color.h},${mode.color.s}%,${mode.color.l}%)`;
        ctx.lineWidth = 3;
        ctx.shadowBlur = 14;
        ctx.shadowColor = `hsl(${mode.color.h},${mode.color.s}%,${mode.color.l}%)`;
        ctx.stroke();

        // Expanding Acquisition Signal Rings
        const ringT = (t * 0.6) % 1.0;
        const ringR = ringT * 120 * scale;
        ctx.beginPath();
        ctx.arc(x + w * 0.7, y + h * 0.35, ringR, 0, Math.PI * 2);
        ctx.strokeStyle = `hsla(${mode.color.h},${mode.color.s}%,${mode.color.l}%,${1 - ringT})`;
        ctx.lineWidth = 1.5;
        ctx.stroke();

        ctx.restore();
    }

        const renderMap = {
            strategy: renderStrategy,
            uxui:     renderUXUI,
            webdev:   renderWebDev,
            growth:   renderGrowth,
        };

        if (isTransitioning) {
            // Render outgoing activeMode
            if (renderMap[activeMode]) {
                renderMap[activeMode](cx, cy, 1 - transitionProgress * 0.1, 1 - transitionProgress, t);
            }
            // Render incoming targetMode
            if (renderMap[targetMode]) {
                renderMap[targetMode](cx, cy, 0.9 + transitionProgress * 0.1, transitionProgress, t);
            }
        } else {
            if (renderMap[activeMode]) {
                renderMap[activeMode](cx, cy, 1.0, 1.0, t);
            }
        }
    }

    /* ── ANIMATION LOOP ── */
    function tick(ts) {
        if (!isVisible) return;
        raf = requestAnimationFrame(tick);

        const dt = Math.min((ts - lastTs) / 1000, 0.05);
        lastTs = ts;

        // Damped pointer lerp for Stage Tilt
        const damp = 1 - Math.exp(-10 * dt);
        pxCurr = lerp(pxCurr, pxTarget, damp);
        pyCurr = lerp(pyCurr, pyTarget, damp);

        if (stage && !reduced) {
            const rx = -pyCurr * 2.5; // Pitch (max ±2.5 deg)
            const ry =  pxCurr * 4.0; // Yaw (max ±4.0 deg)
            stage.style.transform = `rotateX(${rx.toFixed(2)}deg) rotateY(${ry.toFixed(2)}deg)`;
        }

        if (!reduced) draw(ts);
    }

    /* ── POINTER & OBSERVER ── */
    function onPointerMove(e) {
        const rect = host.getBoundingClientRect();
        if (rect.width && rect.height) {
            pxTarget = (e.clientX - rect.left) / rect.width - 0.5;
            pyTarget = (e.clientY - rect.top) / rect.height - 0.5;
        }
    }
    host.addEventListener('pointermove', onPointerMove);

    const observer = new IntersectionObserver((entries) => {
        const ent = entries[0];
        isVisible = ent.isIntersecting;
        if (isVisible && !raf) {
            lastTs = performance.now();
            raf = requestAnimationFrame(tick);
        }
    }, { threshold: 0.1 });
    observer.observe(host);

    return () => {
        cancelAnimationFrame(raf);
        observer.disconnect();
        host.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('scroll', checkScrollMode);
    };
}
