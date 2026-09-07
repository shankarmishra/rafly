/**
 * hero-growth-field.js — RAFly Growth Studio Controller
 *
 * Concept: "Growth Reactor / Growth Lens"
 * A premium physical sculpture rendered on Canvas2D.
 * Five flowing ribbons (Web, Security, Marketing, Content, Commerce)
 * converge into a central translucent crystalline core.
 *
 * Responsibilities:
 *   • Canvas2D reactor: crystalline core + 5 flowing ribbons + ambient particles
 *   • Damped mouse parallax (bg 0.5×, reactor 1.0×, annotations 1.8×)
 *   • 3D-style tilt via CSS perspective (max ±5°)
 *   • Kinetic typography: staggered mask reveal on load
 *   • Periodic light sweep on "Scale Smarter." every 7s
 *   • Idle auto-cycling through 5 capabilities + hover lock
 *   • Scroll choreography (reactor scale + ghost word drift)
 *   • Ghost background word parallax
 *   • IntersectionObserver lifecycle — pause when off-screen
 *   • Full prefers-reduced-motion support
 */

const lerp = (a, b, n) => a + (b - a) * n;
const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));

/** Bezier easing (same as CSS ease-out cubic-bezier(.16,1,.3,1)) */
const easeOut = (t) => 1 - Math.pow(1 - t, 3);

export function initHeroGrowthField(host) {
    if (!host) return () => {};

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isFine  = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    /* ── DOM refs ──────────────────────────────────────────────────────── */
    const canvas     = host.querySelector('[data-signal-canvas], [data-reactor-canvas]');
    const reactor    = host.querySelector('[data-signal-stage], [data-reactor]');
    const focusWord  = host.querySelector('[data-light-sweep], [data-focus-word]');
    const annots     = Array.from(host.querySelectorAll('[data-sig-tag], [data-annot]'));
    const leaders    = Array.from(host.querySelectorAll('[data-leader]'));
    const ghostWords = Array.from(host.querySelectorAll('[data-ghost]'));
    const glow       = host.querySelector('.sig-env__glow, .grs-env__glow');

    /* ── Capability config ─────────────────────────────────────────────── */
    const CAPS = ['web', 'security', 'marketing', 'content', 'commerce'];
    const CAP_COLORS = {
        web:       { h: 220, s: 100, l: 52 },
        security:  { h: 166, s: 78,  l: 38 },
        marketing: { h: 260, s: 78,  l: 55 },
        content:   { h: 200, s: 85,  l: 46 },
        commerce:  { h: 220, s: 100, l: 42 },
    };

    /* ── Logo Mark Asset Preload ────────────────────────────────────────── */
    let logoMarkImg = null;
    if (typeof Image !== 'undefined') {
        logoMarkImg = new Image();
        logoMarkImg.src = '/assets/logo-mark.png';
    }

    /* ── State ─────────────────────────────────────────────────────────── */
    let activeCap      = null;
    let hoverLocked    = false;
    let cycleIdx       = 0;
    let cycleTimer     = 0;
    let sweepTimer     = 0;
    let syncTimer      = 0;
    let isSyncing      = false;
    let raf            = 0;
    let isVisible      = true;
    let t0             = performance.now();

    // Pointer (normalised -0.5 … +0.5)
    let pxTarget = 0, pyTarget = 0;
    let pxCurr   = 0, pyCurr   = 0;

    // Scroll
    let scrollProgress = 0; // 0–1

    /* ── Entrance ──────────────────────────────────────────────────────── */
    host.classList.add('sig-hero--entered');
    host.classList.add('grs-hero--entered');

    /* ── Canvas setup ──────────────────────────────────────────────────── */
    if (!canvas) return () => {};
    const ctx = canvas.getContext('2d');
    if (!ctx) return () => {};

    const DPR = Math.min(window.devicePixelRatio || 1, 2);
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        const w = Math.floor(rect.width  || 640);
        const h = Math.floor(rect.height || 640);
        canvas.width  = Math.floor(w * DPR);
        canvas.height = Math.floor(h * DPR);
        // Store logical size
        canvas._w = w;
        canvas._h = h;
    }
    resizeCanvas();
    new ResizeObserver(resizeCanvas).observe(canvas);

    /* ── Kinetic Matrix (Spring-Mass Lattice Grid) Setup ───────────── */
    const COLS = 18;
    const ROWS = 12;
    const grid = [];

    for (let r = 0; r < ROWS; r++) {
        grid[r] = [];
        for (let c = 0; c < COLS; c++) {
            grid[r][c] = {
                xr: (c + 0.5) / COLS, // equilibrium ratio X (0..1)
                yr: (r + 0.5) / ROWS, // equilibrium ratio Y (0..1)
                x: 0,  // current physical X
                y: 0,  // current physical Y
                vx: 0,
                vy: 0,
            };
        }
    }

    let isGridInit = false;

    /* ── Draw frame ─────────────────────────────────────────────────────── */
    function draw(ts) {
        const t  = (ts - t0) / 1000; // seconds since start
        const w  = canvas._w || 1440;
        const h  = canvas._h || 900;
        const cx = w / 2;
        const cy = h / 2;

        // Reset transform to physical DPR pixels cleanly each frame
        ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
        ctx.clearRect(0, 0, w, h);
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';

        // Initialize grid physical coordinates on first frame or window resize
        if (!isGridInit || canvas._prevW !== w || canvas._prevH !== h) {
            canvas._prevW = w;
            canvas._prevH = h;
            isGridInit = true;
            for (let r = 0; r < ROWS; r++) {
                for (let c = 0; c < COLS; c++) {
                    const n = grid[r][c];
                    n.x = n.xr * w;
                    n.y = n.yr * h;
                    n.vx = 0;
                    n.vy = 0;
                }
            }
        }

        /* 1. Physics Step — Hooke's Law Spring Restoring + Cursor Force Field */
        const mouseX = cx + pxCurr * (w * 0.9);
        const mouseY = cy + pyCurr * (h * 0.9);
        const mouseRadius = 220;

        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                const n = grid[r][c];
                const baseX = n.xr * w;
                const baseY = n.yr * h;

                // Hooke's Law spring force pulling back to equilibrium
                const springFx = (baseX - n.x) * 0.075;
                const springFy = (baseY - n.y) * 0.075;

                // Cursor repulsion force field
                const dx = n.x - mouseX;
                const dy = n.y - mouseY;
                const dist = Math.sqrt(dx * dx + dy * dy);

                let repelFx = 0, repelFy = 0;
                if (dist < mouseRadius && dist > 0.1) {
                    const factor = Math.pow(1 - dist / mouseRadius, 2) * 18.0;
                    repelFx = (dx / dist) * factor;
                    repelFy = (dy / dist) * factor;
                }

                // Update velocity with spring force + cursor push + damping
                n.vx = (n.vx + springFx + repelFx) * 0.83;
                n.vy = (n.vy + springFy + repelFy) * 0.83;

                // Integrate position
                n.x += n.vx;
                n.y += n.vy;
            }
        }

        /* 2. Render Triangulated Geometric Mesh Facets & Facet Highlights */
        for (let r = 0; r < ROWS - 1; r++) {
            for (let c = 0; c < COLS - 1; c++) {
                const n1 = grid[r][c];
                const n2 = grid[r + 1][c];
                const n3 = grid[r + 1][c + 1];
                const n4 = grid[r][c + 1];

                const centerDist = Math.hypot((n1.x + n3.x) / 2 - mouseX, (n1.y + n3.y) / 2 - mouseY);
                if (centerDist < mouseRadius * 1.2) {
                    const alpha = Math.max(0, (1 - centerDist / (mouseRadius * 1.2)) * 0.08);

                    // Triangle 1 Facet Fill
                    ctx.beginPath();
                    ctx.moveTo(n1.x, n1.y);
                    ctx.lineTo(n2.x, n2.y);
                    ctx.lineTo(n3.x, n3.y);
                    ctx.closePath();
                    ctx.fillStyle = `rgba(10, 99, 255, ${alpha})`;
                    ctx.fill();

                    // Triangle 2 Facet Fill
                    ctx.beginPath();
                    ctx.moveTo(n1.x, n1.y);
                    ctx.lineTo(n3.x, n3.y);
                    ctx.lineTo(n4.x, n4.y);
                    ctx.closePath();
                    ctx.fillStyle = `rgba(56, 189, 248, ${alpha * 0.75})`;
                    ctx.fill();
                }
            }
        }

        /* 3. Render Triangulated Mesh Spring Lines (Horizontal, Vertical, & Diagonal) */
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                const n1 = grid[r][c];

                // Horizontal Spring Line
                if (c < COLS - 1) {
                    const n2 = grid[r][c + 1];
                    const strain = Math.abs(n1.x - n1.xr * w) + Math.abs(n1.y - n1.yr * h);
                    const lineAlpha = 0.14 + Math.min(strain * 0.03, 0.45);
                    ctx.beginPath();
                    ctx.moveTo(n1.x, n1.y);
                    ctx.lineTo(n2.x, n2.y);
                    ctx.strokeStyle = `rgba(10, 99, 255, ${lineAlpha})`;
                    ctx.lineWidth   = 1.0 + Math.min(strain * 0.02, 1.2);
                    ctx.stroke();
                }

                // Vertical Spring Line
                if (r < ROWS - 1) {
                    const n2 = grid[r + 1][c];
                    const strain = Math.abs(n1.x - n1.xr * w) + Math.abs(n1.y - n1.yr * h);
                    const lineAlpha = 0.14 + Math.min(strain * 0.03, 0.45);
                    ctx.beginPath();
                    ctx.moveTo(n1.x, n1.y);
                    ctx.lineTo(n2.x, n2.y);
                    ctx.strokeStyle = `rgba(10, 99, 255, ${lineAlpha})`;
                    ctx.lineWidth   = 1.0 + Math.min(strain * 0.02, 1.2);
                    ctx.stroke();
                }

                // Diagonal Triangle Spring Line (Top-Left to Bottom-Right)
                if (r < ROWS - 1 && c < COLS - 1) {
                    const n2 = grid[r + 1][c + 1];
                    const strain = Math.abs(n1.x - n1.xr * w) + Math.abs(n1.y - n1.yr * h);
                    const lineAlpha = 0.10 + Math.min(strain * 0.025, 0.40);
                    ctx.beginPath();
                    ctx.moveTo(n1.x, n1.y);
                    ctx.lineTo(n2.x, n2.y);
                    ctx.strokeStyle = `rgba(56, 189, 248, ${lineAlpha})`;
                    ctx.lineWidth   = 0.9 + Math.min(strain * 0.015, 1.0);
                    ctx.stroke();
                }
            }
        }

        /* 4. Render Mass Node Dots & Synaptic Data Pulses */
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                const n = grid[r][c];
                const dx = n.x - n.xr * w;
                const dy = n.y - n.yr * h;
                const strain = Math.sqrt(dx * dx + dy * dy);

                ctx.save();
                ctx.beginPath();
                const dotR = 2.0 + Math.min(strain * 0.08, 2.5);
                ctx.arc(n.x, n.y, dotR, 0, Math.PI * 2);

                if (strain > 8) {
                    ctx.fillStyle   = '#0a63ff';
                    ctx.shadowBlur  = 12;
                    ctx.shadowColor = 'rgba(10, 99, 255, 0.9)';
                } else {
                    ctx.fillStyle   = 'rgba(5, 15, 51, 0.38)';
                }
                ctx.fill();
                ctx.restore();
            }
        }

        /* 5. Traveling Synaptic Data Packets & Floating Geometric Triangles */
        const packetCount = 6;
        for (let p = 0; p < packetCount; p++) {
            const pRow = (p * 2 + 1) % (ROWS - 1);
            const phase = ((t * 0.30) + (p * 0.18)) % 1.0;
            const pColFloat = phase * (COLS - 1);
            const cIdx = Math.floor(pColFloat);
            const subRatio = pColFloat - cIdx;

            if (cIdx < COLS - 1) {
                const nodeA = grid[pRow][cIdx];
                const nodeB = grid[pRow + 1][cIdx + 1]; // Move along diagonal triangle spring!
                const px = lerp(nodeA.x, nodeB.x, subRatio);
                const py = lerp(nodeA.y, nodeB.y, subRatio);

                // Draw kinetic triangle particle at packet position
                ctx.save();
                ctx.translate(px, py);
                ctx.rotate(t * 1.5 + p);
                ctx.beginPath();
                const size = 6.0;
                ctx.moveTo(0, -size);
                ctx.lineTo(size * 0.866, size * 0.5);
                ctx.lineTo(-size * 0.866, size * 0.5);
                ctx.closePath();
                ctx.fillStyle   = 'rgba(10, 99, 255, 0.95)';
                ctx.shadowBlur  = 12;
                ctx.shadowColor = '#38bdf8';
                ctx.fill();
                ctx.restore();
            }
        }
    }

    let lastTs = performance.now();

    /* ── RAF loop ───────────────────────────────────────────────────────── */
    function tick(ts) {
        if (!isVisible) return;
        raf = requestAnimationFrame(tick);

        const dt = Math.min((ts - lastTs) / 1000, 0.05); // Cap dt at 50ms to prevent jumps
        lastTs = ts;

        // Ultra-smooth frame-rate independent pointer lerp
        const damp = 1 - Math.exp(-12 * dt);
        pxCurr = lerp(pxCurr, pxTarget, damp);
        pyCurr = lerp(pyCurr, pyTarget, damp);

        // Reactor parallax + tilt (max 4-5 deg)
        if (reactor && !reduced) {
            const tx = pxCurr * 16;
            const ty = pyCurr * 12 - scrollProgress * 20;
            const rx = -pyCurr * 5;  // tilt X (pitch)
            const ry =  pxCurr * 5;  // tilt Y (yaw)
            const scale = 1 - scrollProgress * 0.05;

            reactor.style.transform =
                `translate3d(${tx.toFixed(2)}px, ${ty.toFixed(2)}px, 0)` +
                ` rotateX(${rx.toFixed(2)}deg) rotateY(${ry.toFixed(2)}deg)` +
                ` scale(${scale.toFixed(3)})`;

            // Glow follows subtly
            if (glow) {
                glow.style.transform = `translateY(-50%) translate(${(pxCurr * 5).toFixed(2)}px, ${(pyCurr * 4).toFixed(2)}px)`;
            }
        }

        // Annotation parallax
        if (!reduced) {
            annots.forEach((a, i) => {
                const ax = pxCurr * (18 + i * 1.2);
                const ay = pyCurr * (14 + i * 1.0);
                a.style.setProperty('--ax', `${ax.toFixed(2)}px`);
                a.style.setProperty('--ay', `${ay.toFixed(2)}px`);
            });
            // Ghost words slow drift
            ghostWords.forEach((g, i) => {
                const sign = i % 2 === 0 ? 1 : -1;
                const gx   = pxCurr * (6 + i * 2) * sign
                            + scrollProgress * (24 + i * 8) * sign;
                g.style.setProperty('--ghost-x', `${gx.toFixed(2)}px`);
            });
        }

        // Idle auto-cycle
        cycleTimer += dt;
        if (!hoverLocked && cycleTimer > 3.2) {
            cycleTimer = 0;
            cycleIdx   = (cycleIdx + 1) % CAPS.length;
            setActiveCap(CAPS[cycleIdx]);
        }

        // Periodic signature synchronization pulse (every ~5.5s for 750ms)
        syncTimer += dt;
        if (syncTimer > 5.5) {
            if (syncTimer > 6.25) {
                syncTimer = 0;
                isSyncing = false;
            } else {
                isSyncing = true;
            }
        }

        // Sweep timer
        sweepTimer += dt;
        if (sweepTimer > 7 && focusWord) {
            sweepTimer = 0;
            triggerSweep();
        }

        if (!reduced) draw(ts);
    }

    /* ── Capability activation ──────────────────────────────────────────── */
    function setActiveCap(cap) {
        activeCap = cap;
        annots.forEach((a) => {
            const k = a.dataset.sigTag || a.dataset.annot;
            a.classList.toggle('is-active',  k === cap);
            a.classList.toggle('is-dimmed',  cap && k !== cap);
        });
        leaders.forEach((l) => {
            const k = l.dataset.leader;
            l.classList.toggle('is-active',  k === cap);
            l.classList.toggle('is-visible',  true);
        });
    }

    /* ── Light sweep ────────────────────────────────────────────────────── */
    function triggerSweep() {
        if (!focusWord) return;
        focusWord.classList.remove('is-sweeping');
        void focusWord.offsetWidth;
        focusWord.classList.add('is-sweeping');
    }
    setTimeout(triggerSweep, 800);

    /* ── Annotation hover ───────────────────────────────────────────────── */
    annots.forEach((a) => {
        a.addEventListener('mouseenter', () => {
            hoverLocked = true;
            cycleTimer  = 0;
            setActiveCap(a.dataset.sigTag || a.dataset.annot);
        });
        a.addEventListener('focus', () => {
            hoverLocked = true;
            cycleTimer  = 0;
            setActiveCap(a.dataset.sigTag || a.dataset.annot);
        });
        a.addEventListener('mouseleave', () => {
            hoverLocked = false;
        });
        a.addEventListener('blur', () => {
            hoverLocked = false;
        });
    });

    /* ── Mouse tracking ─────────────────────────────────────────────────── */
    if (isFine) {
        document.addEventListener('mousemove', (e) => {
            const rect = host.getBoundingClientRect();
            pxTarget = clamp((e.clientX - rect.left) / rect.width  - 0.5, -0.5, 0.5);
            pyTarget = clamp((e.clientY - rect.top)  / rect.height - 0.5, -0.5, 0.5);
        }, { passive: true });
    }

    /* ── Scroll ─────────────────────────────────────────────────────────── */
    function onScroll() {
        const rect = host.getBoundingClientRect();
        const h    = rect.height || window.innerHeight;
        scrollProgress = clamp(-rect.top / h, 0, 1);
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ── IntersectionObserver ───────────────────────────────────────────── */
    const io = new IntersectionObserver((entries) => {
        isVisible = entries[0].isIntersecting;
        if (isVisible) { t0 = performance.now() - (t0 || 0); raf = requestAnimationFrame(tick); }
        else cancelAnimationFrame(raf);
    }, { threshold: 0.05 });
    io.observe(host);

    /* ── Reduced motion static fallback ─────────────────────────────────── */
    if (reduced) {
        host.classList.add('grs-hero--entered');
        annots.forEach((a) => a.classList.add('is-visible'));
        leaders.forEach((l) => l.classList.add('is-visible'));
        // Draw one static frame
        requestAnimationFrame((ts) => { t0 = ts; draw(ts); });
        return () => {};
    }

    /* ── Start ──────────────────────────────────────────────────────────── */
    raf = requestAnimationFrame(tick);
    setActiveCap(CAPS[0]);

    return function destroy() {
        cancelAnimationFrame(raf);
        io.disconnect();
        window.removeEventListener('scroll', onScroll);
    };
}
