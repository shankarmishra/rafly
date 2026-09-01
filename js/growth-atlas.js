/**
 * growth-atlas.js — THE RAFly GROWTH ATLAS
 *
 * A world-class Gen-Z premium digital agency interactive experience.
 * Concept: One living digital map / architectural object transforming as visitor scrolls.
 * Five Capabilities: WEB → SECURITY → MARKETING → CONTENT → COMMERCE
 *
 * Core Features:
 * - Pinned sticky scroll storytelling with continuous geometry morphing
 * - Canvas 2D + 3D depth hybrid procedural rendering
 * - Architectural "Growth Core" with dynamic lighting, signals & tilt parallax
 * - Editorial word-by-word reveal & single active annotation updates
 * - Chapter navigation timeline sync with smooth jump
 * - Full reduced-motion & mobile responsive adaptivity
 */

const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const lerp  = (a, b, n)   => a + (b - a) * n;

const CHAPTERS = [
    {
        idx: '01',
        key: 'web',
        label: 'WEB',
        title: 'Build the experience.',
        desc: 'Fast, resilient digital experiences engineered around how people actually move.',
        accent: '#0a63ff',
        glow: 'rgba(10, 99, 255, 0.22)'
    },
    {
        idx: '02',
        key: 'security',
        label: 'SECURITY',
        title: 'Protect what you build.',
        desc: 'Security engineered into the foundation — not added after the fact.',
        accent: '#0230c6',
        glow: 'rgba(2, 48, 198, 0.22)'
    },
    {
        idx: '03',
        key: 'marketing',
        label: 'MARKETING',
        title: 'Create demand.',
        desc: 'Campaigns and creative systems built to turn attention into measurable growth.',
        accent: '#6134c9',
        glow: 'rgba(97, 52, 201, 0.22)'
    },
    {
        idx: '04',
        key: 'content',
        label: 'CONTENT',
        title: 'Shape the story.',
        desc: 'Strategy, scripts and visual content designed to make the brand impossible to ignore.',
        accent: '#2563eb',
        glow: 'rgba(37, 99, 235, 0.22)'
    },
    {
        idx: '05',
        key: 'commerce',
        label: 'COMMERCE',
        title: 'Turn intent into revenue.',
        desc: 'Stores and conversion systems engineered from discovery to checkout.',
        accent: '#0891b2',
        glow: 'rgba(8, 145, 178, 0.22)'
    }
];

export function initGrowthAtlas(host) {
    if (!host) return () => {};

    const reduced  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = () => window.innerWidth <= 991;

    /* ─── DOM Querying ─────────────────────────────────────── */
    const canvas      = host.querySelector('[data-atlas-canvas]');
    const ctx         = canvas ? canvas.getContext('2d') : null;
    const scene3d     = host.querySelector('[data-atlas-3d]');
    const growthCore  = host.querySelector('[data-growth-core]');
    const planes      = [...host.querySelectorAll('[data-depth]')];
    const navItems    = [...host.querySelectorAll('[data-chapter-nav]')];
    const cards       = [...host.querySelectorAll('[data-chapter]')];
    const headWords   = [...host.querySelectorAll('.atlas-w')];
    const payoffEl    = host.querySelector('[data-atlas-payoff]');

    /* ─── Internal State ───────────────────────────────────── */
    let rafId       = 0;
    let running     = false;
    let rawScrollP  = 0;
    let progress    = 0;  // Eased progress 0..1
    let mouseX      = 0, mouseY = 0;
    let tgtMouseX   = 0, tgtMouseY = 0;
    let currentChapterIdx = -1;
    let headlineRevealed = false;
    let time        = 0;

    /* Set Canvas Resolution */
    function resizeCanvas() {
        if (!canvas) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        canvas.width  = (rect.width || 800) * dpr;
        canvas.height = (rect.height || 600) * dpr;
    }

    if (canvas) {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });
    }

    /* ─── Editorial Headline Reveal ───────────────────────── */
    function revealHeadline() {
        if (headlineRevealed || reduced) return;
        headlineRevealed = true;
        headWords.forEach((w, i) => {
            w.style.opacity = '0';
            w.style.transform = 'translateY(18px)';
            w.style.filter = 'blur(8px)';
            w.style.transition = 'none';
            setTimeout(() => {
                w.style.transition = 'opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1), filter 0.6s cubic-bezier(0.16,1,0.3,1)';
                w.style.opacity = '1';
                w.style.transform = 'translateY(0)';
                w.style.filter = 'blur(0px)';
            }, 60 + i * 45);
        });
    }

    /* ─── Mouse Pointer Parallax Tracking ──────────────────── */
    const onMove = (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        tgtMouseX = clamp((e.clientX - cx) / cx, -1, 1);
        tgtMouseY = clamp((e.clientY - cy) / cy, -1, 1);
    };

    if (!reduced && !isMobile()) {
        window.addEventListener('mousemove', onMove, { passive: true });
    }

    /* ─── Nav Click Handler ────────────────────────────────── */
    navItems.forEach((btn) => {
        btn.addEventListener('click', () => {
            const idx = parseInt(btn.dataset.chapterNav, 10);
            if (isNaN(idx)) return;
            const targetP = idx * 0.18 + 0.05; // 0.05, 0.23, 0.41, 0.59, 0.77
            const hostRect = host.getBoundingClientRect();
            const scrollTotal = hostRect.height - window.innerHeight;
            if (scrollTotal > 0) {
                const targetScrollY = window.scrollY + hostRect.top + targetP * scrollTotal;
                window.scrollTo({ top: targetScrollY, behavior: 'smooth' });
            }
        });
    });

    /* ─── Render Chapter Canvas Visuals ────────────────────── */
    function renderProceduralVisual(w, h, p, t) {
        if (!ctx) return;

        ctx.clearRect(0, 0, w, h);

        const cx = w / 2;
        const cy = h / 2;

        const normP = clamp(p / 0.90, 0, 1);
        const floatStage = normP * 4;
        const activeIdx = clamp(Math.floor(floatStage), 0, 4);
        const stagePhase = floatStage - activeIdx;

        // Common Ambient Energy Grid
        ctx.lineWidth = 1;
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.07)';
        const gridGap = 40;
        for (let x = (t * 10) % gridGap; x < w; x += gridGap) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, h);
            ctx.stroke();
        }
        for (let y = (t * 8) % gridGap; y < h; y += gridGap) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(w, y);
            ctx.stroke();
        }

        // Draw Chapter-Specific Visual Layers
        switch (activeIdx) {
            case 0:
                drawWebChapter(ctx, w, h, cx, cy, stagePhase, t);
                break;
            case 1:
                drawSecurityChapter(ctx, w, h, cx, cy, stagePhase, t);
                break;
            case 2:
                drawMarketingChapter(ctx, w, h, cx, cy, stagePhase, t);
                break;
            case 3:
                drawContentChapter(ctx, w, h, cx, cy, stagePhase, t);
                break;
            case 4:
                drawCommerceChapter(ctx, w, h, cx, cy, stagePhase, t);
                break;
        }
    }

    /* ─── Chapter 01: WEB ──────────────────────────────────── */
    function drawWebChapter(ctx, w, h, cx, cy, phase, t) {
        ctx.save();
        const blue = '#0a63ff';

        const bw = Math.min(w * 0.55, 420);
        const bh = Math.min(h * 0.45, 260);
        const bx = cx - bw / 2;
        const by = cy - bh / 2 - 10;

        ctx.strokeStyle = 'rgba(10, 99, 255, 0.25)';
        ctx.setLineDash([4, 6]);
        ctx.strokeRect(bx - 20, by - 20, bw + 40, bh + 40);
        ctx.setLineDash([]);

        ctx.fillStyle = 'rgba(10, 20, 45, 0.7)';
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.4)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        if (ctx.roundRect) ctx.roundRect(bx, by, bw, bh, 12);
        else ctx.rect(bx, by, bw, bh);
        ctx.fill();
        ctx.stroke();

        ctx.fillStyle = 'rgba(10, 99, 255, 0.6)';
        ctx.beginPath(); ctx.arc(bx + 20, by + 16, 4, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(bx + 34, by + 16, 4, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(bx + 48, by + 16, 4, 0, Math.PI * 2); ctx.fill();

        ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
        ctx.fillRect(bx + 16, by + 36, bw - 32, 40);

        const colW = (bw - 48) / 3;
        for (let i = 0; i < 3; i++) {
            const cX = bx + 16 + i * (colW + 8);
            const cY = by + 86;
            ctx.fillStyle = 'rgba(10, 99, 255, 0.12)';
            ctx.strokeStyle = 'rgba(10, 99, 255, 0.3)';
            ctx.beginPath();
            const hVal = 90 * Math.min(1, Math.max(0, phase * 1.5 - i * 0.2));
            if (ctx.roundRect) ctx.roundRect(cX, cY, colW, hVal, 6);
            else ctx.rect(cX, cY, colW, hVal);
            ctx.fill();
            ctx.stroke();
        }

        const dotPos = (t * 120) % (bw * 2 + bh * 2);
        let dx = bx, dy = by;
        if (dotPos < bw) { dx += dotPos; }
        else if (dotPos < bw + bh) { dx += bw; dy += (dotPos - bw); }
        else if (dotPos < bw * 2 + bh) { dx += bw - (dotPos - bw - bh); dy += bh; }
        else { dy += bh - (dotPos - bw * 2 - bh); }

        ctx.shadowColor = blue;
        ctx.shadowBlur = 12;
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.arc(dx, dy, 3.5, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;

        ctx.restore();
    }

    /* ─── Chapter 02: SECURITY ─────────────────────────────── */
    function drawSecurityChapter(ctx, w, h, cx, cy, phase, t) {
        ctx.save();

        const r1 = 110 + Math.sin(t * 2) * 4;
        const r2 = 160 + Math.cos(t * 1.5) * 6;
        const r3 = 210 + Math.sin(t * 1.2) * 8;

        ctx.strokeStyle = 'rgba(2, 48, 198, 0.35)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.arc(cx, cy, r3, t * 0.8, t * 0.8 + Math.PI * 1.2);
        ctx.stroke();

        ctx.strokeStyle = 'rgba(92, 147, 255, 0.5)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(cx, cy, r2, -t * 1.2, -t * 1.2 + Math.PI * 1.4);
        ctx.stroke();

        ctx.strokeStyle = 'rgba(10, 99, 255, 0.4)';
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        for (let i = 0; i < 6; i++) {
            const angle = (i * Math.PI / 3) + t * 0.2;
            const x = cx + r1 * Math.cos(angle);
            const y = cy + r1 * Math.sin(angle);
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.closePath();
        ctx.stroke();

        for (let p = 0; p < 8; p++) {
            const ang = (p * Math.PI / 4) + t * 0.5;
            const dist = 240 - ((t * 80 + p * 30) % 130);
            const px = cx + dist * Math.cos(ang);
            const py = cy + dist * Math.sin(ang);

            ctx.fillStyle = dist < r1 + 10 ? 'rgba(92, 147, 255, 0.9)' : 'rgba(2, 48, 198, 0.4)';
            ctx.beginPath();
            ctx.arc(px, py, 2.5, 0, Math.PI * 2);
            ctx.fill();

            if (dist < r1 + 25) {
                ctx.strokeStyle = 'rgba(92, 147, 255, 0.3)';
                ctx.beginPath();
                ctx.moveTo(px, py);
                ctx.lineTo(cx, cy);
                ctx.stroke();
            }
        }

        ctx.restore();
    }

    /* ─── Chapter 03: MARKETING ────────────────────────────── */
    function drawMarketingChapter(ctx, w, h, cx, cy, phase, t) {
        ctx.save();

        const waveCount = 5;
        for (let i = 0; i < waveCount; i++) {
            const alpha = 0.15 + i * 0.08;
            ctx.strokeStyle = `rgba(97, 52, 201, ${alpha})`;
            ctx.lineWidth = 1.8 - i * 0.25;

            ctx.beginPath();
            for (let x = 0; x < w; x += 10) {
                const normX = (x - cx) / (w / 2);
                const y = cy + Math.sin(normX * 4 + t * 2 + i * 0.8) * (35 + i * 12) * Math.exp(-normX * normX * 0.8);
                if (x === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }
            ctx.stroke();
        }

        const nodes = [
            { x: cx - 180, y: cy - 70 },
            { x: cx + 200, y: cy - 50 },
            { x: cx - 140, y: cy + 90 },
            { x: cx + 160, y: cy + 80 }
        ];

        nodes.forEach((nd, i) => {
            const ripR = ((t * 40 + i * 20) % 50);
            const ripO = 1 - (ripR / 50);
            ctx.strokeStyle = `rgba(10, 99, 255, ${ripO * 0.5})`;
            ctx.beginPath();
            ctx.arc(nd.x, nd.y, ripR, 0, Math.PI * 2);
            ctx.stroke();

            ctx.fillStyle = '#6134c9';
            ctx.shadowColor = '#0a63ff';
            ctx.shadowBlur = 10;
            ctx.beginPath();
            ctx.arc(nd.x, nd.y, 4, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0;

            ctx.strokeStyle = 'rgba(97, 52, 201, 0.25)';
            ctx.beginPath();
            ctx.moveTo(nd.x, nd.y);
            ctx.lineTo(cx, cy);
            ctx.stroke();
        });

        ctx.restore();
    }

    /* ─── Chapter 04: CONTENT ──────────────────────────────── */
    function drawContentChapter(ctx, w, h, cx, cy, phase, t) {
        ctx.save();

        const planeCount = 4;
        for (let i = 0; i < planeCount; i++) {
            const offset = (i - 1.5) * 90;
            const px = cx + offset + Math.sin(t + i) * 12;
            const py = cy + (i % 2 === 0 ? -30 : 25) + Math.cos(t * 1.2 + i) * 10;
            const pw = 120;
            const ph = 150;

            ctx.fillStyle = 'rgba(15, 23, 42, 0.65)';
            ctx.strokeStyle = 'rgba(37, 99, 235, 0.4)';
            ctx.lineWidth = 1.2;

            ctx.beginPath();
            if (ctx.roundRect) ctx.roundRect(px - pw / 2, py - ph / 2, pw, ph, 8);
            else ctx.rect(px - pw / 2, py - ph / 2, pw, ph);
            ctx.fill();
            ctx.stroke();

            ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
            ctx.lineWidth = 1;
            const corner = 8;
            ctx.beginPath(); ctx.moveTo(px - pw / 2 - 4, py - ph / 2 + corner); ctx.lineTo(px - pw / 2 - 4, py - ph / 2 - 4); ctx.lineTo(px - pw / 2 + corner, py - ph / 2 - 4); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(px + pw / 2 + 4, py + ph / 2 - corner); ctx.lineTo(px + pw / 2 + 4, py + ph / 2 + 4); ctx.lineTo(px + pw / 2 - corner, py + ph / 2 + 4); ctx.stroke();
        }

        const waveX = cx - 180;
        const waveY = cy + 120;
        const barCount = 32;
        ctx.fillStyle = 'rgba(37, 99, 235, 0.6)';
        for (let b = 0; b < barCount; b++) {
            const bh = Math.abs(Math.sin(b * 0.4 + t * 4)) * 28 + 4;
            ctx.fillRect(waveX + b * 11, waveY - bh / 2, 4, bh);
        }

        ctx.restore();
    }

    /* ─── Chapter 05: COMMERCE ─────────────────────────────── */
    function drawCommerceChapter(ctx, w, h, cx, cy, phase, t) {
        ctx.save();

        const pathCount = 6;
        for (let i = 0; i < pathCount; i++) {
            const angle = (i * Math.PI * 2 / pathCount) + t * 0.15;
            const startR = 240;
            const sx = cx + startR * Math.cos(angle);
            const sy = cy + startR * Math.sin(angle);

            ctx.strokeStyle = 'rgba(8, 145, 178, 0.3)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(sx, sy);
            ctx.quadraticCurveTo(cx + Math.sin(angle + t) * 40, cy + Math.cos(angle + t) * 40, cx, cy);
            ctx.stroke();

            const pProg = ((t * 0.8 + i * 0.15) % 1);
            const px = lerp(sx, cx, pProg * pProg);
            const py = lerp(sy, cy, pProg * pProg);

            ctx.fillStyle = '#0891b2';
            ctx.shadowColor = '#0a63ff';
            ctx.shadowBlur = 12;
            ctx.beginPath();
            ctx.arc(px, py, 3.5, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0;
        }

        ctx.restore();
    }

    /* ─── Update UI Components ─────────────────────────────── */
    function updateUI() {
        const mobile = isMobile();

        const clampedP = clamp(progress, 0, 0.95);
        const floatStage = (clampedP / 0.95) * 4.99;
        const newChapterIdx = clamp(Math.floor(floatStage), 0, 4);

        if (newChapterIdx !== currentChapterIdx) {
            currentChapterIdx = newChapterIdx;

            navItems.forEach((btn, i) => {
                const match = i === currentChapterIdx;
                btn.classList.toggle('is-active', match);
                btn.setAttribute('aria-selected', match ? 'true' : 'false');
            });

            cards.forEach((card, i) => {
                const match = i === currentChapterIdx;
                card.classList.toggle('is-active', match);
            });

            const activeChapter = CHAPTERS[currentChapterIdx];
            if (activeChapter && host) {
                host.style.setProperty('--atlas-glow-color', activeChapter.glow);
                host.style.setProperty('--atlas-accent-color', activeChapter.accent);
            }
        }

        if (payoffEl) {
            const payoffActive = progress >= 0.90;
            payoffEl.classList.toggle('is-visible', payoffActive);
        }

        if (!reduced && !mobile && scene3d) {
            mouseX = lerp(mouseX, tgtMouseX, 0.06);
            mouseY = lerp(mouseY, tgtMouseY, 0.06);

            planes.forEach((plane) => {
                const d = parseFloat(plane.dataset.depth || '0.5');
                const px = (mouseX * d * 16).toFixed(1);
                const py = (mouseY * d * 14).toFixed(1);
                plane.style.transform = `translate3d(${px}px, ${py}px, 0)`;
            });

            if (growthCore) {
                const tiltX = (-mouseY * 4.5).toFixed(2);
                const tiltY = ( mouseX * 5.5).toFixed(2);
                growthCore.style.transform = `rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
            }
        }
    }

    /* ─── Main Render Loop ─────────────────────────────────── */
    function renderFrame() {
        const r = host.getBoundingClientRect();
        const scrollRange = r.height - window.innerHeight;

        if (scrollRange > 0) {
            const raw = clamp(-r.top / scrollRange, 0, 1);
            rawScrollP = raw;
            progress = lerp(progress, rawScrollP, 0.08);
        } else {
            progress = 1;
        }

        time += 0.016;

        if (canvas && ctx) {
            renderProceduralVisual(canvas.width, canvas.height, progress, time);
        }

        updateUI();

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
            revealHeadline();
            start();
        } else {
            stop();
        }
    }, { rootMargin: '20% 0px' });

    io.observe(host);

    document.addEventListener('visibilitychange', () => {
        document.hidden ? stop() : (running || start());
    });

    renderFrame();

    if (reduced) {
        cards.forEach((c, i) => c.classList.toggle('is-active', i === 0));
        navItems.forEach((n, i) => n.classList.toggle('is-active', i === 0));
        revealHeadline();
    }

    return function destroy() {
        stop();
        io.disconnect();
        window.removeEventListener('mousemove', onMove);
        window.removeEventListener('resize', resizeCanvas);
    };
}
