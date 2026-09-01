/**
 * service-studio.js — THE RAFly SERVICE STUDIO
 *
 * One Large Cinematic Service Stage + Minimal Editorial Service Rail.
 * 01 WEB → 02 SECURITY → 03 MARKETING → 04 CONTENT → 05 COMMERCE
 *
 * Core Features:
 * - 700–1100ms cinematic morphing scene transitions
 * - Canvas 2D + Live vector geometry procedural engine
 * - 3D depth parallax & subtle tilt (max ±3°)
 * - Kinetic background ghost typography scroll drift
 * - 60fps performance gated by IntersectionObserver
 */

const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const lerp  = (a, b, n)   => a + (b - a) * n;
const ease  = (t)          => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;

const SERVICES = {
    web: {
        idx: '01',
        tag: '01 // WEB ARCHITECTURE',
        accent: '#0a63ff',
        glow: 'rgba(10, 99, 255, 0.18)'
    },
    security: {
        idx: '02',
        tag: '02 // PERIMETER SECURITY',
        accent: '#0d9488',
        glow: 'rgba(13, 148, 136, 0.18)'
    },
    marketing: {
        idx: '03',
        tag: '03 // DEMAND MATRIX',
        accent: '#7c3aed',
        glow: 'rgba(124, 58, 237, 0.18)'
    },
    content: {
        idx: '04',
        tag: '04 // KINETIC CONTENT',
        accent: '#d97706',
        glow: 'rgba(217, 119, 6, 0.18)'
    },
    commerce: {
        idx: '05',
        tag: '05 // CONVERSION PIPELINE',
        accent: '#0891b2',
        glow: 'rgba(8, 145, 178, 0.18)'
    }
};

export function initServiceStudio(host) {
    if (!host) return () => {};

    const reduced  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = () => window.innerWidth <= 991;

    /* ─── DOM Querying ─────────────────────────────────────── */
    const canvas     = host.querySelector('[data-stage-canvas]');
    const ctx        = canvas ? canvas.getContext('2d') : null;
    const stageEl    = host.querySelector('[data-service-stage]');
    const railItems  = [...host.querySelectorAll('[data-service-target]')];
    const tagEl      = host.querySelector('[data-active-tag]');
    const ghostWords = [...host.querySelectorAll('.ss-ghost')];

    /* ─── State Variables ──────────────────────────────────── */
    let rafId          = 0;
    let running        = false;
    let activeKey      = 'web';
    let prevKey        = 'web';
    let morphProgress  = 1.0; // 0 (start transition) to 1 (complete)
    let transitionTime = 0;
    let time           = 0;
    let mouseX         = 0, mouseY = 0;
    let tgtMouseX      = 0, tgtMouseY = 0;

    /* Set Canvas Resolution */
    function resizeCanvas() {
        if (!canvas) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        canvas.width  = (rect.width || 740) * dpr;
        canvas.height = (rect.height || 520) * dpr;
    }

    if (canvas) {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });
    }

    /* ─── Mouse Movement Parallax Tracking ────────────────── */
    const onMove = (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        tgtMouseX = clamp((e.clientX - cx) / cx, -1, 1);
        tgtMouseY = clamp((e.clientY - cy) / cy, -1, 1);
    };

    if (!reduced && !isMobile()) {
        window.addEventListener('mousemove', onMove, { passive: true });
    }

    /* ─── Switch Service Handler ───────────────────────────── */
    function switchService(key) {
        if (key === activeKey) return;
        prevKey = activeKey;
        activeKey = key;
        morphProgress = 0.0;
        transitionTime = 0;

        // Sync Rail Items UI
        railItems.forEach((btn) => {
            const match = btn.dataset.serviceTarget === key;
            btn.classList.toggle('is-active', match);
            btn.setAttribute('aria-selected', match ? 'true' : 'false');
        });

        // Sync Active Tag Text
        if (tagEl && SERVICES[key]) {
            tagEl.textContent = SERVICES[key].tag;
        }

        // Sync Host Accent Color
        if (SERVICES[key]) {
            host.style.setProperty('--ss-accent-color', SERVICES[key].accent);
            host.style.setProperty('--ss-glow-color', SERVICES[key].glow);
        }
    }

    /* ─── Scroll-triggered Service Switching ───────── */
    const serviceKeys = ['web', 'security', 'marketing', 'content', 'commerce'];
    let userInteracted = false;

    function checkScrollService() {
        if (userInteracted) return;
        const rect = host.getBoundingClientRect();
        const vh = window.innerHeight || 800;
        if (rect.top > vh * 0.85 || rect.bottom < vh * 0.15) return;

        const total = Math.max(1, rect.height - vh * 0.3);
        const current = vh * 0.55 - rect.top;
        const progress = Math.max(0, Math.min(0.99, current / total));
        const idx = Math.floor(progress * serviceKeys.length);
        const autoKey = serviceKeys[idx];
        if (autoKey && autoKey !== activeKey) {
            switchService(autoKey);
        }
    }

    window.addEventListener('scroll', checkScrollService, { passive: true });

    /* Rail Click & Hover Listeners */
    railItems.forEach((btn) => {
        btn.addEventListener('click', () => {
            userInteracted = true;
            const target = btn.dataset.serviceTarget;
            if (target) switchService(target);
        });
        btn.addEventListener('mouseenter', () => {
            const target = btn.dataset.serviceTarget;
            if (target) switchService(target);
        });
    });

    /* ─── Procedural Canvas Rendering Engine ───────────────── */
    function renderStudioStage(w, h, t, pMorph, current, prev) {
        if (!ctx) return;

        ctx.clearRect(0, 0, w, h);

        const cx = w / 2;
        const cy = h / 2;

        // Background Technical Blueprint Grid
        ctx.save();
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.06)';
        ctx.lineWidth = 1;
        const gridGap = 40;
        for (let x = (t * 8) % gridGap; x < w; x += gridGap) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, h); ctx.stroke();
        }
        for (let y = (t * 6) % gridGap; y < h; y += gridGap) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(w, y); ctx.stroke();
        }
        ctx.restore();

        // Calculate Morph Blend Factor
        const blend = ease(clamp(pMorph, 0, 1));

        // Draw Previous Scene if Morphing
        if (blend < 1.0) {
            ctx.save();
            ctx.globalAlpha = 1.0 - blend;
            drawServiceScene(prev, ctx, w, h, cx, cy, t);
            ctx.restore();
        }

        // Draw Current Scene
        ctx.save();
        ctx.globalAlpha = blend;
        drawServiceScene(current, ctx, w, h, cx, cy, t);
        ctx.restore();
    }

    /* ─── Scene Specific Graphics ──────────────────────────── */
    function drawServiceScene(key, ctx, w, h, cx, cy, t) {
        switch (key) {
            case 'web':
                drawWebScene(ctx, w, h, cx, cy, t);
                break;
            case 'security':
                drawSecurityScene(ctx, w, h, cx, cy, t);
                break;
            case 'marketing':
                drawMarketingScene(ctx, w, h, cx, cy, t);
                break;
            case 'content':
                drawContentScene(ctx, w, h, cx, cy, t);
                break;
            case 'commerce':
                drawCommerceScene(ctx, w, h, cx, cy, t);
                break;
        }
    }

    /* 01 WEB SCENE */
    function drawWebScene(ctx, w, h, cx, cy, t) {
        const bw = Math.min(w * 0.76, 560);
        const bh = Math.min(h * 0.68, 350);
        const bx = cx - bw / 2;
        const by = cy - bh / 2;

        // Construction Grid Outline
        ctx.save();
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.22)';
        ctx.setLineDash([4, 6]);
        ctx.strokeRect(bx - 16, by - 16, bw + 32, bh + 32);
        ctx.setLineDash([]);

        // Browser Frame
        ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.4)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        if (ctx.roundRect) ctx.roundRect(bx, by, bw, bh, 14);
        else ctx.rect(bx, by, bw, bh);
        ctx.fill();
        ctx.stroke();

        // Browser Dots & URL Bar
        ctx.fillStyle = 'rgba(10, 99, 255, 0.6)';
        ctx.beginPath(); ctx.arc(bx + 20, by + 18, 4, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(bx + 34, by + 18, 4, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(bx + 48, by + 18, 4, 0, Math.PI * 2); ctx.fill();

        ctx.fillStyle = 'rgba(255, 255, 255, 0.08)';
        ctx.fillRect(bx + 70, by + 10, bw - 140, 16);

        // Responsive Viewport Wireframe
        ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
        ctx.fillRect(bx + 20, by + 42, bw - 40, 48);

        // 3 Layout Columns
        const colW = (bw - 60) / 3;
        for (let i = 0; i < 3; i++) {
            ctx.fillStyle = 'rgba(10, 99, 255, 0.14)';
            ctx.strokeStyle = 'rgba(10, 99, 255, 0.35)';
            ctx.beginPath();
            const hVal = 130 + Math.sin(t * 1.5 + i) * 10;
            if (ctx.roundRect) ctx.roundRect(bx + 20 + i * (colW + 10), by + 102, colW, hVal, 8);
            else ctx.rect(bx + 20 + i * (colW + 10), by + 102, colW, hVal);
            ctx.fill();
            ctx.stroke();
        }

        // Design Annotations
        ctx.fillStyle = 'rgba(10, 99, 255, 0.8)';
        ctx.font = '10px monospace';
        ctx.fillText('w: 100%  h: auto  |  60FPS', bx + 24, by + bh - 14);

        // Subtle Studio Cursor
        const curX = bx + bw * 0.65 + Math.sin(t * 1.2) * 30;
        const curY = by + bh * 0.45 + Math.cos(t * 1.5) * 20;
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.moveTo(curX, curY);
        ctx.lineTo(curX + 12, curY + 12);
        ctx.lineTo(curX + 5, curY + 14);
        ctx.closePath();
        ctx.fill();

        ctx.restore();
    }

    /* 02 SECURITY SCENE */
    function drawSecurityScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        const r1 = 120 + Math.sin(t * 2) * 6;
        const r2 = 180 + Math.cos(t * 1.5) * 8;
        const r3 = 240 + Math.sin(t * 1.2) * 10;

        // Concentric Scanning Arcs
        ctx.strokeStyle = 'rgba(13, 148, 136, 0.4)';
        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.arc(cx, cy, r3, t * 0.6, t * 0.6 + Math.PI * 1.2); ctx.stroke();

        ctx.strokeStyle = 'rgba(45, 212, 191, 0.6)';
        ctx.lineWidth = 2.5;
        ctx.beginPath(); ctx.arc(cx, cy, r2, -t * 1.2, -t * 1.2 + Math.PI * 1.4); ctx.stroke();

        // Central Shield Hexagon
        ctx.strokeStyle = 'rgba(13, 148, 136, 0.6)';
        ctx.lineWidth = 1.8;
        ctx.beginPath();
        for (let i = 0; i < 6; i++) {
            const ang = (i * Math.PI / 3) + t * 0.3;
            const x = cx + r1 * Math.cos(ang);
            const y = cy + r1 * Math.sin(ang);
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.closePath();
        ctx.stroke();

        // Encrypted Data Particles Intercept
        for (let p = 0; p < 10; p++) {
            const ang = (p * Math.PI / 5) + t * 0.4;
            const dist = 260 - ((t * 90 + p * 25) % 150);
            const px = cx + dist * Math.cos(ang);
            const py = cy + dist * Math.sin(ang);

            ctx.fillStyle = dist < r1 + 15 ? 'rgba(45, 212, 191, 0.9)' : 'rgba(13, 148, 136, 0.4)';
            ctx.beginPath(); ctx.arc(px, py, 3, 0, Math.PI * 2); ctx.fill();
        }

        ctx.restore();
    }

    /* 03 MARKETING SCENE */
    function drawMarketingScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Organic Sine Waves
        for (let i = 0; i < 5; i++) {
            const alpha = 0.2 + i * 0.1;
            ctx.strokeStyle = `rgba(124, 58, 237, ${alpha})`;
            ctx.lineWidth = 2 - i * 0.3;

            ctx.beginPath();
            for (let x = 60; x < w - 60; x += 10) {
                const normX = (x - cx) / (w / 2);
                const y = cy + Math.sin(normX * 3.5 + t * 2 + i * 0.7) * (40 + i * 14) * Math.exp(-normX * normX * 0.7);
                if (x === 60) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }
            ctx.stroke();
        }

        // Audience Nodes & Ripple Rings
        const nodes = [
            { x: cx - 200, y: cy - 80 },
            { x: cx + 220, y: cy - 60 },
            { x: cx - 150, y: cy + 100 },
            { x: cx + 180, y: cy + 90 }
        ];

        nodes.forEach((nd, i) => {
            const ripR = ((t * 45 + i * 20) % 55);
            ctx.strokeStyle = `rgba(124, 58, 237, ${1 - ripR / 55})`;
            ctx.beginPath(); ctx.arc(nd.x, nd.y, ripR, 0, Math.PI * 2); ctx.stroke();

            ctx.fillStyle = '#7c3aed';
            ctx.beginPath(); ctx.arc(nd.x, nd.y, 4.5, 0, Math.PI * 2); ctx.fill();
        });

        ctx.restore();
    }

    /* 04 CONTENT SCENE */
    function drawContentScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Kinetic Typography & Media Frame Planes
        for (let i = 0; i < 4; i++) {
            const px = cx + (i - 1.5) * 110 + Math.sin(t + i) * 14;
            const py = cy + (i % 2 === 0 ? -35 : 30) + Math.cos(t * 1.2 + i) * 12;
            const pw = 130;
            const ph = 160;

            ctx.fillStyle = 'rgba(15, 23, 42, 0.75)';
            ctx.strokeStyle = 'rgba(217, 119, 6, 0.45)';
            ctx.lineWidth = 1.4;

            ctx.beginPath();
            if (ctx.roundRect) ctx.roundRect(px - pw / 2, py - ph / 2, pw, ph, 10);
            else ctx.rect(px - pw / 2, py - ph / 2, pw, ph);
            ctx.fill();
            ctx.stroke();
        }

        // Sound Waveform Bar Matrix
        const waveX = cx - 210;
        const waveY = cy + 130;
        ctx.fillStyle = 'rgba(217, 119, 6, 0.7)';
        for (let b = 0; b < 36; b++) {
            const bh = Math.abs(Math.sin(b * 0.35 + t * 4)) * 32 + 6;
            ctx.fillRect(waveX + b * 12, waveY - bh / 2, 4.5, bh);
        }

        ctx.restore();
    }

    /* 05 COMMERCE SCENE */
    function drawCommerceScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        const paths = 6;
        for (let i = 0; i < paths; i++) {
            const ang = (i * Math.PI * 2 / paths) + t * 0.15;
            const startR = 260;
            const sx = cx + startR * Math.cos(ang);
            const sy = cy + startR * Math.sin(ang);

            ctx.strokeStyle = 'rgba(8, 145, 178, 0.35)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(sx, sy);
            ctx.quadraticCurveTo(cx + Math.sin(ang + t) * 50, cy + Math.cos(ang + t) * 50, cx, cy);
            ctx.stroke();

            const pProg = ((t * 0.8 + i * 0.15) % 1);
            const px = lerp(sx, cx, pProg * pProg);
            const py = lerp(sy, cy, pProg * pProg);

            ctx.fillStyle = '#0891b2';
            ctx.shadowColor = '#0891b2';
            ctx.shadowBlur = 10;
            ctx.beginPath(); ctx.arc(px, py, 4, 0, Math.PI * 2); ctx.fill();
            ctx.shadowBlur = 0;
        }

        ctx.restore();
    }

    /* ─── Main Render Loop ─────────────────────────────────── */
    function renderFrame() {
        time += 0.016;

        if (morphProgress < 1.0) {
            transitionTime += 0.016;
            morphProgress = Math.min(1.0, transitionTime / 0.85);
        }

        if (canvas && ctx) {
            renderStudioStage(canvas.width, canvas.height, time, morphProgress, activeKey, prevKey);
        }

        // 3D Parallax Tilt (Desktop)
        if (!reduced && !isMobile() && stageEl) {
            mouseX = lerp(mouseX, tgtMouseX, 0.06);
            mouseY = lerp(mouseY, tgtMouseY, 0.06);

            const tiltX = (-mouseY * 3.0).toFixed(2);
            const tiltY = ( mouseX * 3.0).toFixed(2);
            stageEl.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
        }

        // Ghost typography scroll drift
        ghostWords.forEach((word, i) => {
            const shiftX = (Math.sin(time * 0.5 + i) * 20).toFixed(1);
            word.style.transform = `translateX(${shiftX}px)`;
        });

        rafId = requestAnimationFrame(renderFrame);
    }

    const start = () => {
        if (!running) {
            running = true;
            rafId = requestAnimationFrame(renderFrame);
        }
    };

    const stop = () => {
        running = false;
        cancelAnimationFrame(rafId);
    };

    /* ─── IntersectionObserver Gate ────────────────────────── */
    const io = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            start();
        } else {
            stop();
        }
    }, { rootMargin: '15% 0px' });

    io.observe(host);

    document.addEventListener('visibilitychange', () => {
        document.hidden ? stop() : (running || start());
    });

    renderFrame();

    return function destroy() {
        stop();
        io.disconnect();
        window.removeEventListener('mousemove', onMove);
        window.removeEventListener('resize', resizeCanvas);
        window.removeEventListener('scroll', checkScrollService);
    };
}
