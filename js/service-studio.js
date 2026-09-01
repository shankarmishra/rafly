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

    /* ─── Canvas Helper Functions ───────────────────────────── */
    function drawRoundRect(ctx, x, y, w, h, r) {
        if (ctx.roundRect) {
            ctx.roundRect(x, y, w, h, r);
        } else {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.arcTo(x + w, y, x + w, y + r, r);
            ctx.lineTo(x + w, y + h - r);
            ctx.arcTo(x + w, y + h, x + w - r, y + h, r);
            ctx.lineTo(x + r, y + h);
            ctx.arcTo(x, y + h, x, y + h - r, r);
            ctx.lineTo(x, y + r);
            ctx.arcTo(x, y, x + r, y, r);
            ctx.closePath();
        }
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

    /* 01 WEB ARCHITECTURE SCENE */
    function drawWebScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Ambient Background Radial Glow
        const bgGlow = ctx.createRadialGradient(cx, cy, 20, cx, cy, 280);
        bgGlow.addColorStop(0, 'rgba(10, 99, 255, 0.16)');
        bgGlow.addColorStop(1, 'rgba(10, 99, 255, 0)');
        ctx.fillStyle = bgGlow;
        ctx.fillRect(0, 0, w, h);

        const bw = Math.min(w * 0.82, 600);
        const bh = Math.min(h * 0.72, 370);
        const bx = cx - bw / 2;
        const by = cy - bh / 2 - 10;

        // Alignment & Dimension Vector Guidelines
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.18)';
        ctx.lineWidth = 1;
        ctx.setLineDash([4, 6]);
        ctx.strokeRect(bx - 18, by - 18, bw + 36, bh + 36);
        ctx.setLineDash([]);

        // Technical Corner Ticks
        const tickS = 10;
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.5)';
        ctx.lineWidth = 1.5;
        [[bx - 18, by - 18], [bx + bw + 18, by - 18], [bx - 18, by + bh + 18], [bx + bw + 18, by + bh + 18]].forEach(([tx, ty], idx) => {
            ctx.beginPath();
            ctx.moveTo(tx, ty + (idx > 1 ? -tickS : tickS));
            ctx.lineTo(tx, ty);
            ctx.lineTo(tx + (idx % 2 === 1 ? -tickS : tickS), ty);
            ctx.stroke();
        });

        // Main Glass Browser Viewport Container
        const frameGrad = ctx.createLinearGradient(bx, by, bx, by + bh);
        frameGrad.addColorStop(0, 'rgba(15, 23, 42, 0.94)');
        frameGrad.addColorStop(1, 'rgba(10, 15, 30, 0.92)');

        ctx.fillStyle = frameGrad;
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.45)';
        ctx.lineWidth = 1.6;
        ctx.shadowColor = 'rgba(10, 99, 255, 0.2)';
        ctx.shadowBlur = 24;

        ctx.beginPath();
        drawRoundRect(ctx, bx, by, bw, bh, 14);
        ctx.fill();
        ctx.stroke();
        ctx.shadowBlur = 0;

        // Top Browser Header & Control Dots
        ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
        ctx.fillRect(bx, by, bw, 38);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
        ctx.beginPath(); ctx.moveTo(bx, by + 38); ctx.lineTo(bx + bw, by + 38); ctx.stroke();

        const dots = ['#ff5f56', '#ffbd2e', '#27c93f'];
        dots.forEach((c, i) => {
            ctx.fillStyle = c;
            ctx.beginPath(); ctx.arc(bx + 20 + i * 14, by + 19, 4.5, 0, Math.PI * 2); ctx.fill();
        });

        // URL Pill & Security Badge
        const urlW = bw * 0.45;
        const urlX = bx + (bw - urlW) / 2;
        ctx.fillStyle = 'rgba(255, 255, 255, 0.08)';
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.3)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        drawRoundRect(ctx, urlX, by + 10, urlW, 18, 9);
        ctx.fill(); ctx.stroke();

        ctx.fillStyle = '#10b981';
        ctx.beginPath(); ctx.arc(urlX + 14, by + 19, 3, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.font = '500 10px monospace';
        ctx.fillText('https://rafly.dev/architecture', urlX + 24, by + 22);

        // Header Action Telemetry Badge
        ctx.fillStyle = 'rgba(10, 99, 255, 0.85)';
        ctx.font = '600 10px monospace';
        ctx.fillText('⚡ 100/100 CWV', bx + bw - 110, by + 22);

        // Split Layout: Left Code Editor + Right Interactive Interface Wireframe
        const codeW = bw * 0.36;
        const codeX = bx + 16;
        const codeY = by + 52;
        const codeH = bh - 68;

        // Left Code Editor Panel
        ctx.fillStyle = 'rgba(8, 14, 26, 0.8)';
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.25)';
        ctx.beginPath();
        drawRoundRect(ctx, codeX, codeY, codeW, codeH, 8);
        ctx.fill(); ctx.stroke();

        // Code Syntax Lines
        const codeLines = [
            { t: 'const app = initUI({', c: '#38bdf8' },
            { t: '  mode: "60fps",', c: '#a7f3d0' },
            { t: '  cwv: 100,', c: '#fbbf24' },
            { t: '  layout: "fluid"', c: '#cbd5e1' },
            { t: '});', c: '#38bdf8' },
            { t: 'export default app;', c: '#f472b6' }
        ];
        codeLines.forEach((ln, i) => {
            const ly = codeY + 22 + i * 18;
            ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
            ctx.font = '9px monospace';
            ctx.fillText(String(i + 1).padStart(2, '0'), codeX + 10, ly);

            ctx.fillStyle = ln.c;
            ctx.font = '500 9.5px monospace';
            ctx.fillText(ln.t, codeX + 32, ly);
        });

        // Right Interface Viewport
        const viewX = codeX + codeW + 16;
        const viewW = bw - codeW - 48;
        const viewY = codeY;
        const viewH = codeH;

        ctx.fillStyle = 'rgba(15, 23, 42, 0.5)';
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.25)';
        ctx.beginPath();
        drawRoundRect(ctx, viewX, viewY, viewW, viewH, 8);
        ctx.fill(); ctx.stroke();

        // Hero Component Card in Viewport
        const card1H = 50;
        const cardGrad = ctx.createLinearGradient(viewX + 12, viewY + 12, viewX + viewW - 12, viewY + 12 + card1H);
        cardGrad.addColorStop(0, 'rgba(10, 99, 255, 0.3)');
        cardGrad.addColorStop(1, 'rgba(37, 99, 235, 0.15)');

        ctx.fillStyle = cardGrad;
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.4)';
        ctx.beginPath();
        drawRoundRect(ctx, viewX + 12, viewY + 12, viewW - 24, card1H, 6);
        ctx.fill(); ctx.stroke();

        // Micro CTA inside Hero Card
        ctx.fillStyle = '#0a63ff';
        ctx.beginPath();
        drawRoundRect(ctx, viewX + 24, viewY + 24, 70, 16, 4);
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.font = '600 8.5px sans-serif';
        ctx.fillText('DEPLOY NOW', viewX + 31, by + 86);

        // 2 Column Grid Cards in Viewport
        const colW = (viewW - 34) / 2;
        [0, 1].forEach((col) => {
            const cxPos = viewX + 12 + col * (colW + 10);
            const cyPos = viewY + 70;
            const cH = viewH - 82;

            ctx.fillStyle = 'rgba(255, 255, 255, 0.04)';
            ctx.strokeStyle = 'rgba(10, 99, 255, 0.2)';
            ctx.beginPath();
            drawRoundRect(ctx, cxPos, cyPos, colW, cH, 6);
            ctx.fill(); ctx.stroke();

            // Animated Wireframe Bar Graph inside Column
            const barCount = 5;
            for (let b = 0; b < barCount; b++) {
                const bh = Math.abs(Math.sin(t * 2 + col * 1.5 + b * 0.8)) * 36 + 12;
                ctx.fillStyle = col === 0 ? 'rgba(10, 99, 255, 0.6)' : 'rgba(56, 189, 248, 0.5)';
                ctx.fillRect(cxPos + 10 + b * 16, cyPos + cH - 14 - bh, 10, bh);
            }
        });

        // Dynamic Precision Studio Cursor with Trailing Particle Ray
        const curX = viewX + viewW * 0.55 + Math.sin(t * 1.4) * 45;
        const curY = viewY + viewH * 0.4 + Math.cos(t * 1.8) * 35;

        // Trailing glow path
        ctx.strokeStyle = 'rgba(56, 189, 248, 0.5)';
        ctx.lineWidth = 1.5;
        ctx.setLineDash([2, 4]);
        ctx.beginPath();
        ctx.moveTo(viewX + 20, viewY + 30);
        ctx.quadraticCurveTo(curX - 30, curY - 20, curX, curY);
        ctx.stroke();
        ctx.setLineDash([]);

        // Cursor
        ctx.shadowColor = '#0a63ff';
        ctx.shadowBlur = 12;
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.moveTo(curX, curY);
        ctx.lineTo(curX + 14, curY + 14);
        ctx.lineTo(curX + 6, curY + 15);
        ctx.lineTo(curX + 2, curY + 22);
        ctx.closePath();
        ctx.fill();
        ctx.shadowBlur = 0;

        ctx.restore();
    }

    /* 02 PERIMETER SECURITY SCENE */
    function drawSecurityScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Ambient Emerald/Teal Glow
        const bgGlow = ctx.createRadialGradient(cx, cy, 30, cx, cy, 270);
        bgGlow.addColorStop(0, 'rgba(13, 148, 136, 0.22)');
        bgGlow.addColorStop(1, 'rgba(13, 148, 136, 0)');
        ctx.fillStyle = bgGlow;
        ctx.fillRect(0, 0, w, h);

        const r1 = 110 + Math.sin(t * 1.8) * 5;
        const r2 = 165 + Math.cos(t * 1.4) * 7;
        const r3 = 225 + Math.sin(t * 1.1) * 9;

        // Rotating Telemetry Radar Rings with Degree Ticks
        [r1, r2, r3].forEach((r, idx) => {
            ctx.strokeStyle = idx === 1 ? 'rgba(45, 212, 191, 0.45)' : 'rgba(13, 148, 136, 0.25)';
            ctx.lineWidth = idx === 1 ? 1.8 : 1.2;
            ctx.beginPath();
            ctx.arc(cx, cy, r, 0, Math.PI * 2);
            ctx.stroke();

            // Degree Ticks
            const tickCount = 12;
            for (let k = 0; k < tickCount; k++) {
                const ang = (k * Math.PI * 2 / tickCount) + (idx % 2 === 0 ? t * 0.2 : -t * 0.15);
                const x1 = cx + (r - 4) * Math.cos(ang);
                const y1 = cy + (r - 4) * Math.sin(ang);
                const x2 = cx + (r + 4) * Math.cos(ang);
                const y2 = cy + (r + 4) * Math.sin(ang);
                ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke();
            }
        });

        // 360 Sweep Radar Scanner Beam
        const scanAng = t * 1.5;
        const sweepGrad = ctx.createConicGradient ? ctx.createConicGradient(scanAng, cx, cy) : null;
        if (sweepGrad) {
            sweepGrad.addColorStop(0, 'rgba(45, 212, 191, 0.35)');
            sweepGrad.addColorStop(0.15, 'rgba(13, 148, 136, 0)');
            sweepGrad.addColorStop(1, 'rgba(13, 148, 136, 0)');
            ctx.fillStyle = sweepGrad;
            ctx.beginPath(); ctx.arc(cx, cy, r3, 0, Math.PI * 2); ctx.fill();
        }

        // Central 3D Hexagonal Defense Core
        ctx.shadowColor = '#2dd4bf';
        ctx.shadowBlur = 18;
        ctx.strokeStyle = 'rgba(45, 212, 191, 0.85)';
        ctx.lineWidth = 2.2;
        ctx.beginPath();
        for (let i = 0; i < 6; i++) {
            const ang = (i * Math.PI / 3) + t * 0.25;
            const x = cx + (r1 - 25) * Math.cos(ang);
            const y = cy + (r1 - 25) * Math.sin(ang);
            i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        }
        ctx.closePath();
        ctx.stroke();
        ctx.shadowBlur = 0;

        // Core Glowing Emblem Pulse
        const coreGrad = ctx.createRadialGradient(cx, cy, 0, cx, cy, 35);
        coreGrad.addColorStop(0, '#2dd4bf');
        coreGrad.addColorStop(0.6, 'rgba(13, 148, 136, 0.6)');
        coreGrad.addColorStop(1, 'rgba(13, 148, 136, 0)');
        ctx.fillStyle = coreGrad;
        ctx.beginPath(); ctx.arc(cx, cy, 35, 0, Math.PI * 2); ctx.fill();

        // Lock Icon Vector in Center Core
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.arc(cx, cy - 4, 7, Math.PI, 0); ctx.stroke();
        ctx.fillStyle = '#ffffff';
        ctx.beginPath(); drawRoundRect(ctx, cx - 9, cy - 3, 18, 14, 3); ctx.fill();

        // Threat Packet Interception Animation
        for (let p = 0; p < 12; p++) {
            const ang = (p * Math.PI / 6) + t * 0.3;
            const rawDist = 270 - ((t * 110 + p * 28) % 180);
            const px = cx + rawDist * Math.cos(ang);
            const py = cy + rawDist * Math.sin(ang);

            const isIntercepted = rawDist < r1 + 15;
            ctx.fillStyle = isIntercepted ? '#2dd4bf' : 'rgba(239, 68, 68, 0.75)';
            ctx.shadowColor = isIntercepted ? '#2dd4bf' : '#ef4444';
            ctx.shadowBlur = 8;

            ctx.beginPath(); ctx.arc(px, py, isIntercepted ? 3.5 : 2.5, 0, Math.PI * 2); ctx.fill();
            ctx.shadowBlur = 0;

            if (isIntercepted) {
                ctx.strokeStyle = 'rgba(45, 212, 191, 0.4)';
                ctx.lineWidth = 1;
                ctx.beginPath(); ctx.arc(px, py, 8, 0, Math.PI * 2); ctx.stroke();
            }
        }

        // Telemetry Data Badges
        ctx.fillStyle = 'rgba(13, 148, 136, 0.9)';
        ctx.font = '700 11px monospace';
        ctx.fillText('SYS.SECURITY // PERIMETER HARDENED', cx - 110, cy - r3 - 12);

        ctx.fillStyle = 'rgba(45, 212, 191, 0.8)';
        ctx.font = '500 10px monospace';
        ctx.fillText('ENCRYPTION: AES-256-GCM  |  EXPOSURES: 0  |  TLS 1.3', cx - 140, cy + r3 + 24);

        ctx.restore();
    }

    /* 03 DEMAND MATRIX / MARKETING SCENE */
    function drawMarketingScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Ambient Violet Radial Aura
        const bgGlow = ctx.createRadialGradient(cx, cy, 30, cx, cy, 280);
        bgGlow.addColorStop(0, 'rgba(124, 58, 237, 0.18)');
        bgGlow.addColorStop(1, 'rgba(124, 58, 237, 0)');
        ctx.fillStyle = bgGlow;
        ctx.fillRect(0, 0, w, h);

        const chartW = Math.min(w * 0.8, 580);
        const chartH = 210;
        const chartX = cx - chartW / 2;
        const chartY = cy + 20;

        // Baseline Chart Axes & Grid Lines
        ctx.strokeStyle = 'rgba(124, 58, 237, 0.15)';
        ctx.lineWidth = 1;
        for (let g = 0; g <= 4; g++) {
            const gy = chartY - (g * chartH / 4);
            ctx.beginPath(); ctx.moveTo(chartX, gy); ctx.lineTo(chartX + chartW, gy); ctx.stroke();
        }

        // Primary Spline Curve 1 (Attributed Revenue / ROAS)
        const points1 = [];
        const segs = 60;
        for (let i = 0; i <= segs; i++) {
            const px = chartX + (i / segs) * chartW;
            const normX = i / segs;
            const wave = Math.sin(normX * Math.PI * 2.2 - t * 2) * 18;
            const growth = Math.pow(normX, 1.8) * chartH * 0.75;
            const py = chartY - growth + wave;
            points1.push({ x: px, y: py });
        }

        // Area Gradient Fill under Spline
        const areaGrad = ctx.createLinearGradient(0, chartY - chartH, 0, chartY);
        areaGrad.addColorStop(0, 'rgba(124, 58, 237, 0.42)');
        areaGrad.addColorStop(0.7, 'rgba(168, 85, 247, 0.1)');
        areaGrad.addColorStop(1, 'rgba(124, 58, 237, 0)');

        ctx.fillStyle = areaGrad;
        ctx.beginPath();
        ctx.moveTo(points1[0].x, chartY);
        points1.forEach((p) => ctx.lineTo(p.x, p.y));
        ctx.lineTo(points1[points1.length - 1].x, chartY);
        ctx.closePath();
        ctx.fill();

        // Stroke Spline Line
        ctx.shadowColor = '#8b5cf6';
        ctx.shadowBlur = 12;
        ctx.strokeStyle = '#8b5cf6';
        ctx.lineWidth = 3;
        ctx.beginPath();
        points1.forEach((p, idx) => idx === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y));
        ctx.stroke();
        ctx.shadowBlur = 0;

        // Secondary Baseline Projection Curve (Dashed)
        ctx.strokeStyle = 'rgba(192, 132, 252, 0.45)';
        ctx.lineWidth = 1.8;
        ctx.setLineDash([5, 5]);
        ctx.beginPath();
        for (let i = 0; i <= segs; i++) {
            const px = chartX + (i / segs) * chartW;
            const normX = i / segs;
            const py = chartY - normX * chartH * 0.38 + Math.cos(normX * 4 + t) * 10;
            i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        }
        ctx.stroke();
        ctx.setLineDash([]);

        // Interactive Target Acquisition Nodes
        const nodes = [
            { idx: 15, label: 'AWARENESS' },
            { idx: 35, label: 'CONSIDERATION' },
            { idx: 54, label: 'CONVERSION PEAK' }
        ];

        nodes.forEach((nd) => {
            const p = points1[nd.idx];
            if (!p) return;

            // Halo Rings
            const ripR = ((t * 40 + nd.idx * 10) % 45);
            ctx.strokeStyle = `rgba(168, 85, 247, ${1 - ripR / 45})`;
            ctx.lineWidth = 1.2;
            ctx.beginPath(); ctx.arc(p.x, p.y, ripR, 0, Math.PI * 2); ctx.stroke();

            // Core Node Dot
            ctx.shadowColor = '#c084fc';
            ctx.shadowBlur = 10;
            ctx.fillStyle = '#ffffff';
            ctx.beginPath(); ctx.arc(p.x, p.y, 5, 0, Math.PI * 2); ctx.fill();
            ctx.shadowBlur = 0;

            // Crosshair Guide Line on Apex Node
            if (nd.label.includes('PEAK')) {
                ctx.strokeStyle = 'rgba(192, 132, 252, 0.6)';
                ctx.lineWidth = 1;
                ctx.setLineDash([3, 3]);
                ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(p.x, chartY); ctx.stroke();
                ctx.setLineDash([]);

                // Peak Metric Tooltip Pill
                const pillX = p.x - 65;
                const pillY = p.y - 36;
                ctx.fillStyle = 'rgba(15, 23, 42, 0.9)';
                ctx.strokeStyle = 'rgba(168, 85, 247, 0.6)';
                ctx.lineWidth = 1;
                ctx.beginPath();
                drawRoundRect(ctx, pillX, pillY, 130, 24, 6);
                ctx.fill(); ctx.stroke();

                ctx.fillStyle = '#c084fc';
                ctx.font = '700 10px monospace';
                ctx.fillText('🔥 +340% ROAS (4.8x)', pillX + 10, pillY + 16);
            }
        });

        // Header Annotation
        ctx.fillStyle = 'rgba(168, 85, 247, 0.9)';
        ctx.font = '700 11px monospace';
        ctx.fillText('SYS.GROWTH // ACQUISITION TRAJECTORY & DEMAND MATRIX', chartX, chartY - chartH - 18);

        ctx.restore();
    }

    /* 04 KINETIC CONTENT SCENE (ULTRA-PREMIUM REDESIGN) */
    function drawContentScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Warm Gold/Amber Ambient Aura
        const bgGlow = ctx.createRadialGradient(cx, cy - 20, 20, cx, cy - 20, 290);
        bgGlow.addColorStop(0, 'rgba(217, 119, 6, 0.2)');
        bgGlow.addColorStop(1, 'rgba(217, 119, 6, 0)');
        ctx.fillStyle = bgGlow;
        ctx.fillRect(0, 0, w, h);

        // Technical Grid Guidelines
        ctx.strokeStyle = 'rgba(217, 119, 6, 0.15)';
        ctx.lineWidth = 1;
        ctx.setLineDash([4, 6]);
        ctx.strokeRect(cx - 270, cy - 180, 540, 360);
        ctx.setLineDash([]);

        // Floating Studio Asset Cards Stack (3 Z-Depth Layers)

        // 1. LAYER BACK LEFT: Editorial Copy Card
        const card1X = cx - 210 + Math.sin(t * 0.9) * 8;
        const card1Y = cy - 70 + Math.cos(t * 1.1) * 6;
        const card1W = 160;
        const card1H = 190;

        ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
        ctx.strokeStyle = 'rgba(217, 119, 6, 0.35)';
        ctx.lineWidth = 1.2;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
        ctx.shadowBlur = 16;
        ctx.beginPath();
        drawRoundRect(ctx, card1X, card1Y, card1W, card1H, 12);
        ctx.fill(); ctx.stroke();

        // Editorial Card Content Wireframe
        ctx.fillStyle = '#f59e0b';
        ctx.font = '700 8.5px monospace';
        ctx.fillText('EDITORIAL STORY', card1X + 14, card1Y + 22);

        ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
        ctx.fillRect(card1X + 14, card1Y + 34, card1W - 28, 6);
        ctx.fillRect(card1X + 14, card1Y + 46, card1W - 48, 5);

        for (let l = 0; l < 5; l++) {
            ctx.fillStyle = 'rgba(255, 255, 255, 0.25)';
            ctx.fillRect(card1X + 14, card1Y + 66 + l * 12, card1W - 28 - (l % 2) * 20, 4);
        }

        // Reading Time Pill
        ctx.fillStyle = 'rgba(217, 119, 6, 0.2)';
        ctx.beginPath(); drawRoundRect(ctx, card1X + 14, card1Y + card1H - 28, 70, 16, 8); ctx.fill();
        ctx.fillStyle = '#f59e0b';
        ctx.font = '600 8px monospace';
        ctx.fillText('3 MIN READ', card1X + 22, card1Y + card1H - 17);

        // 2. LAYER BACK RIGHT: Social Media Engagement Card
        const card2X = cx + 60 + Math.cos(t * 1.0) * 10;
        const card2Y = cy - 80 + Math.sin(t * 1.2) * 8;
        const card2W = 150;
        const card2H = 170;

        ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
        ctx.strokeStyle = 'rgba(245, 158, 11, 0.4)';
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        drawRoundRect(ctx, card2X, card2Y, card2W, card2H, 12);
        ctx.fill(); ctx.stroke();

        ctx.fillStyle = '#fbbf24';
        ctx.font = '700 8.5px monospace';
        ctx.fillText('KINETIC ENGAGEMENT', card2X + 14, card2Y + 22);

        // Metric Pill
        ctx.fillStyle = 'rgba(16, 185, 129, 0.2)';
        ctx.strokeStyle = 'rgba(16, 185, 129, 0.5)';
        ctx.beginPath(); drawRoundRect(ctx, card2X + 14, card2Y + 34, 100, 20, 6); ctx.fill(); ctx.stroke();
        ctx.fillStyle = '#34d399';
        ctx.font = '700 9px monospace';
        ctx.fillText('▲ +184% REACH', card2X + 22, card2Y + 47);

        // Heart Icon & Pulse Ring
        ctx.fillStyle = '#ef4444';
        ctx.beginPath(); ctx.arc(card2X + 30, card2Y + 84, 10, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
        ctx.fillRect(card2X + 48, card2Y + 80, 70, 8);

        // 3. LAYER CENTER FRONT: Rich 4K Video Player Surface
        const mainW = 240;
        const mainH = 185;
        const mainX = cx - mainW / 2 + Math.sin(t * 1.3) * 6;
        const mainY = cy - 75 + Math.sin(t * 1.5) * 5;

        // Card Glass Body
        const mainGrad = ctx.createLinearGradient(mainX, mainY, mainX, mainY + mainH);
        mainGrad.addColorStop(0, 'rgba(30, 41, 59, 0.96)');
        mainGrad.addColorStop(1, 'rgba(15, 23, 42, 0.98)');

        ctx.fillStyle = mainGrad;
        ctx.strokeStyle = 'rgba(245, 158, 11, 0.65)';
        ctx.lineWidth = 1.8;
        ctx.shadowColor = 'rgba(217, 119, 6, 0.35)';
        ctx.shadowBlur = 24;
        ctx.beginPath();
        drawRoundRect(ctx, mainX, mainY, mainW, mainH, 14);
        ctx.fill(); ctx.stroke();
        ctx.shadowBlur = 0;

        // Video Viewport Canvas Inside Main Card
        const vpX = mainX + 12;
        const vpY = mainY + 12;
        const vpW = mainW - 24;
        const vpH = 110;

        // Sunset/Cinematic Landscape Gradient Graphic inside Video Viewport
        const vpGrad = ctx.createLinearGradient(vpX, vpY, vpX + vpW, vpY + vpH);
        vpGrad.addColorStop(0, '#1e1b4b');
        vpGrad.addColorStop(0.5, '#431407');
        vpGrad.addColorStop(1, '#b45309');

        ctx.fillStyle = vpGrad;
        ctx.beginPath();
        drawRoundRect(ctx, vpX, vpY, vpW, vpH, 8);
        ctx.fill();

        // Mountain Vector Silhouettes inside Video Viewport
        ctx.fillStyle = 'rgba(15, 23, 42, 0.7)';
        ctx.beginPath();
        ctx.moveTo(vpX, vpY + vpH);
        ctx.lineTo(vpX + 45, vpY + vpH - 40);
        ctx.lineTo(vpX + 90, vpY + vpH - 15);
        ctx.lineTo(vpX + 140, vpY + vpH - 50);
        ctx.lineTo(vpX + vpW, vpY + vpH);
        ctx.closePath();
        ctx.fill();

        // Sun/Aura
        ctx.fillStyle = 'rgba(253, 230, 138, 0.85)';
        ctx.shadowColor = '#fde047';
        ctx.shadowBlur = 16;
        ctx.beginPath(); ctx.arc(vpX + vpW - 45, vpY + 32, 14, 0, Math.PI * 2); ctx.fill();
        ctx.shadowBlur = 0;

        // 4K Badge Pill top-left
        ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
        ctx.beginPath(); drawRoundRect(ctx, vpX + 8, vpY + 8, 70, 16, 4); ctx.fill();
        ctx.fillStyle = '#f59e0b';
        ctx.font = '700 8px monospace';
        ctx.fillText('● 4K 60FPS', vpX + 14, vpY + 19);

        // Circular Glowing Play Button in Center of Video
        ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
        ctx.shadowColor = '#ffffff';
        ctx.shadowBlur = 12;
        ctx.beginPath(); ctx.arc(vpX + vpW / 2, vpY + vpH / 2, 16, 0, Math.PI * 2); ctx.fill();
        ctx.shadowBlur = 0;

        // Play Triangle
        ctx.fillStyle = '#b45309';
        ctx.beginPath();
        ctx.moveTo(vpX + vpW / 2 - 4, vpY + vpH / 2 - 6);
        ctx.lineTo(vpX + vpW / 2 + 7, vpY + vpH / 2);
        ctx.lineTo(vpX + vpW / 2 - 4, vpY + vpH / 2 + 6);
        ctx.closePath();
        ctx.fill();

        // Video Progress Scrub Track & Timecode
        const scrubY = mainY + 134;
        ctx.fillStyle = 'rgba(255, 255, 255, 0.15)';
        ctx.fillRect(mainX + 12, scrubY, mainW - 24, 4);

        const progW = (mainW - 24) * 0.62;
        ctx.fillStyle = '#f59e0b';
        ctx.fillRect(mainX + 12, scrubY, progW, 4);

        // Scrub Cursor Dot
        ctx.shadowColor = '#f59e0b';
        ctx.shadowBlur = 8;
        ctx.beginPath(); ctx.arc(mainX + 12 + progW, scrubY + 2, 5, 0, Math.PI * 2); ctx.fill();
        ctx.shadowBlur = 0;

        ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.font = '600 9px monospace';
        ctx.fillText('01:42 / 03:15', mainX + 12, mainY + mainH - 12);
        ctx.fillText('HDR · STEREO', mainX + mainW - 82, mainY + mainH - 12);

        // 4. HYPER-DETAILED AUDIO EQUALIZER SPECTRUM MATRIX AT BOTTOM
        const waveX = cx - 230;
        const waveY = cy + 135;
        const barCount = 42;
        const barW = 4.5;
        const barGap = 6.2;

        for (let b = 0; b < barCount; b++) {
            const bx = waveX + b * barGap;
            const env = Math.sin((b / barCount) * Math.PI); // Envelope curve
            const bh = Math.abs(Math.sin(b * 0.35 + t * 4.5)) * 36 * env + 6;

            const barGrad = ctx.createLinearGradient(bx, waveY - bh / 2, bx, waveY + bh / 2);
            barGrad.addColorStop(0, '#f59e0b');
            barGrad.addColorStop(1, '#ea580c');

            ctx.fillStyle = barGrad;
            ctx.beginPath();
            drawRoundRect(ctx, bx, waveY - bh / 2, barW, bh, 2.2);
            ctx.fill();

            // Glowing Peak Tip Dots on Active Frequencies
            if (bh > 20) {
                ctx.fillStyle = '#fde047';
                ctx.shadowColor = '#fde047';
                ctx.shadowBlur = 6;
                ctx.beginPath(); ctx.arc(bx + barW / 2, waveY - bh / 2 - 4, 1.8, 0, Math.PI * 2); ctx.fill();
                ctx.shadowBlur = 0;
            }
        }

        // Equalizer Baseline & Frequency Tick Labels
        ctx.strokeStyle = 'rgba(245, 158, 11, 0.4)';
        ctx.lineWidth = 1;
        ctx.beginPath(); ctx.moveTo(waveX, waveY + 24); ctx.lineTo(waveX + barCount * barGap, waveY + 24); ctx.stroke();

        ctx.fillStyle = 'rgba(245, 158, 11, 0.85)';
        ctx.font = '600 9.5px monospace';
        ctx.fillText('AUDIO ENGINE // 24-BIT 48kHZ DYNAMIC SPECTRUM', waveX, waveY + 38);

        // Floating Kinetic Particle Sparks
        for (let sp = 0; sp < 14; sp++) {
            const spX = cx - 240 + ((sp * 38 + t * 40) % 480);
            const spY = cy + 40 - Math.sin(t * 2 + sp) * 90;
            const spAlpha = Math.sin((sp / 14) * Math.PI);

            ctx.fillStyle = `rgba(253, 224, 71, ${spAlpha * 0.7})`;
            ctx.beginPath(); ctx.arc(spX, spY, 1.8, 0, Math.PI * 2); ctx.fill();
        }

        ctx.restore();
    }

    /* 05 COMMERCE / CONVERSION PIPELINE SCENE */
    function drawCommerceScene(ctx, w, h, cx, cy, t) {
        ctx.save();

        // Ambient Cyan Radial Aura
        const bgGlow = ctx.createRadialGradient(cx, cy, 20, cx, cy, 280);
        bgGlow.addColorStop(0, 'rgba(8, 145, 178, 0.2)');
        bgGlow.addColorStop(1, 'rgba(8, 145, 178, 0)');
        ctx.fillStyle = bgGlow;
        ctx.fillRect(0, 0, w, h);

        // Pipeline Flow Nodes: CART -> CHECKOUT -> GATEWAY -> PAYOUT
        const hubs = [
            { label: '01 CART', x: cx - 210, y: cy - 70 },
            { label: '02 CHECKOUT', x: cx - 70, y: cy + 40 },
            { label: '03 GATEWAY', x: cx + 70, y: cy - 60 },
            { label: '04 RECONCILIATION', x: cx + 210, y: cy + 30 }
        ];

        // Draw Flow Conduit Curves connecting Hubs
        for (let i = 0; i < hubs.length - 1; i++) {
            const h1 = hubs[i];
            const h2 = hubs[i + 1];

            ctx.strokeStyle = 'rgba(8, 145, 178, 0.35)';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(h1.x, h1.y);
            ctx.bezierCurveTo(h1.x + 50, h1.y + 40, h2.x - 50, h2.y - 40, h2.x, h2.y);
            ctx.stroke();
        }

        // Accelerating Order Token Particles along Flow Conduits
        for (let p = 0; p < 16; p++) {
            const pProg = ((t * 0.75 + p * 0.065) % 1);
            const segIdx = Math.floor(pProg * 3);
            const segT = (pProg * 3) % 1;

            const h1 = hubs[segIdx];
            const h2 = hubs[segIdx + 1];
            if (h1 && h2) {
                const px = lerp(h1.x, h2.x, segT);
                const py = lerp(h1.y, h2.y, segT);

                ctx.fillStyle = '#06b6d4';
                ctx.shadowColor = '#06b6d4';
                ctx.shadowBlur = 10;
                ctx.beginPath(); ctx.arc(px, py, 4, 0, Math.PI * 2); ctx.fill();
                ctx.shadowBlur = 0;
            }
        }

        // Hub Node Circles & Labels
        hubs.forEach((hb) => {
            ctx.fillStyle = 'rgba(15, 23, 42, 0.9)';
            ctx.strokeStyle = 'rgba(6, 182, 212, 0.6)';
            ctx.lineWidth = 1.5;
            ctx.beginPath(); ctx.arc(hb.x, hb.y, 22, 0, Math.PI * 2); ctx.fill(); ctx.stroke();

            ctx.fillStyle = '#06b6d4';
            ctx.font = '700 9px monospace';
            ctx.fillText(hb.label, hb.x - 30, hb.y + 36);
        });

        // Floating 3D Checkout Card (Centerpiece)
        const cardW = 200;
        const cardH = 120;
        const cardX = cx - cardW / 2;
        const cardY = cy - 70 + Math.sin(t * 1.5) * 8;

        const cardGrad = ctx.createLinearGradient(cardX, cardY, cardX + cardW, cardY + cardH);
        cardGrad.addColorStop(0, 'rgba(15, 23, 42, 0.95)');
        cardGrad.addColorStop(1, 'rgba(8, 145, 178, 0.4)');

        ctx.fillStyle = cardGrad;
        ctx.strokeStyle = 'rgba(6, 182, 212, 0.7)';
        ctx.lineWidth = 1.8;
        ctx.shadowColor = 'rgba(8, 145, 178, 0.4)';
        ctx.shadowBlur = 24;
        ctx.beginPath(); drawRoundRect(ctx, cardX, cardY, cardW, cardH, 12); ctx.fill(); ctx.stroke();
        ctx.shadowBlur = 0;

        // Credit Card EMV Chip Vector
        ctx.fillStyle = '#fbbf24';
        ctx.beginPath(); drawRoundRect(ctx, cardX + 16, cardY + 20, 24, 18, 4); ctx.fill();

        // Contactless Wave Icon
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.6)';
        ctx.lineWidth = 1.5;
        for (let w = 0; w < 3; w++) {
            ctx.beginPath(); ctx.arc(cardX + 54, cardY + 29, 6 + w * 4, -Math.PI / 3, Math.PI / 3); ctx.stroke();
        }

        // Transaction Success Badge
        ctx.fillStyle = 'rgba(16, 185, 129, 0.2)';
        ctx.strokeStyle = '#34d399';
        ctx.lineWidth = 1;
        ctx.beginPath(); drawRoundRect(ctx, cardX + 16, cardY + cardH - 34, cardW - 32, 22, 6); ctx.fill(); ctx.stroke();

        ctx.fillStyle = '#34d399';
        ctx.font = '700 9.5px monospace';
        ctx.fillText('✓ APPROVED · $2,850.00', cardX + 26, cardY + cardH - 20);

        // Header Telemetry
        ctx.fillStyle = 'rgba(6, 182, 212, 0.9)';
        ctx.font = '700 11px monospace';
        ctx.fillText('SYS.COMMERCE // HIGH-VELOCITY TRANSACTIONS (24ms LATENCY)', cx - 180, cy + 130);

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
