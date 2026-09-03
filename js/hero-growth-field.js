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
        const w = rect.width  || 640;
        const h = rect.height || 640;
        canvas.width  = w * DPR;
        canvas.height = h * DPR;
        ctx.scale(DPR, DPR);
        // Store logical size
        canvas._w = w;
        canvas._h = h;
    }
    resizeCanvas();
    new ResizeObserver(resizeCanvas).observe(canvas);

    /* ── Ribbon definitions ─────────────────────────────────────────────
       Each ribbon is described as a set of cubic bezier control points
       relative to the canvas centre (cx, cy).
       The ribbons animate by modulating control points with time.
    */
    function getRibbons(cx, cy, r, t) {
        const s = r * 0.72; // ribbon spread
        return [
            /* 01 WEB — structured, geometric, top-left */
            {
                key: 'web',
                pts: [
                    cx - s * 0.92, cy - s * 0.78,
                    cx - s * 0.42 + Math.sin(t * 0.4) * 8, cy - s * 0.38 + Math.cos(t * 0.35) * 6,
                    cx - s * 0.18 + Math.sin(t * 0.3) * 5, cy - s * 0.15,
                    cx, cy,
                ],
                width: 2.2,
                opacity: 0.72,
            },
            /* 02 SECURITY — protective arc, left side */
            {
                key: 'security',
                pts: [
                    cx - s * 1.02, cy - s * 0.05,
                    cx - s * 0.62 + Math.sin(t * 0.28) * 10, cy - s * 0.22 + Math.cos(t * 0.32) * 8,
                    cx - s * 0.28, cy - s * 0.08,
                    cx, cy,
                ],
                width: 2.8,
                opacity: 0.80,
            },
            /* 03 MARKETING — radiating energy, top-right */
            {
                key: 'marketing',
                pts: [
                    cx + s * 0.92, cy - s * 0.78,
                    cx + s * 0.42 + Math.cos(t * 0.38) * 8, cy - s * 0.38 + Math.sin(t * 0.42) * 6,
                    cx + s * 0.18, cy - s * 0.15 + Math.cos(t * 0.28) * 4,
                    cx, cy,
                ],
                width: 2.2,
                opacity: 0.72,
            },
            /* 04 CONTENT — organic flow, right side */
            {
                key: 'content',
                pts: [
                    cx + s * 1.02, cy + s * 0.12,
                    cx + s * 0.58 + Math.cos(t * 0.35) * 12, cy + s * 0.32 + Math.sin(t * 0.28) * 10,
                    cx + s * 0.26, cy + s * 0.18 + Math.sin(t * 0.45) * 5,
                    cx, cy,
                ],
                width: 3.0,
                opacity: 0.85,
            },
            /* 05 COMMERCE — converging, bottom */
            {
                key: 'commerce',
                pts: [
                    cx, cy + s * 1.0,
                    cx + Math.sin(t * 0.30) * 14, cy + s * 0.58 + Math.cos(t * 0.25) * 8,
                    cx + Math.sin(t * 0.42) * 6,  cy + s * 0.28,
                    cx, cy,
                ],
                width: 2.6,
                opacity: 0.78,
            },
        ];
    }

    /* Ribbon gradient factory */
    function ribbonGradient(pts, color, alpha) {
        const [x0, y0, , , , , x3, y3] = pts;
        const grad = ctx.createLinearGradient(x0, y0, x3, y3);
        const { h, s, l } = color;
        grad.addColorStop(0.0,  `hsla(${h},${s}%,${l + 14}%,0)`);
        grad.addColorStop(0.35, `hsla(${h},${s}%,${l + 8}%, ${alpha * 0.55})`);
        grad.addColorStop(0.70, `hsla(${h},${s}%,${l}%,     ${alpha * 0.90})`);
        grad.addColorStop(1.0,  `hsla(${h},${s}%,${l}%,     ${alpha * 1.00})`);
        return grad;
    }

    /* ── Draw frame ─────────────────────────────────────────────────────── */
    function draw(ts) {
        const t  = (ts - t0) / 1000; // seconds since start
        const w  = canvas._w || 640;
        const h  = canvas._h || 640;
        const cx = w / 2;
        const cy = h / 2;
        const r  = Math.min(w, h) * 0.42; // radius of the reactor field

        ctx.clearRect(0, 0, w, h);

        // Active capability color
        const activeColor = activeCap ? CAP_COLORS[activeCap] : { h: 220, s: 100, l: 52 };

        /* ── Luminous Outer Radial Atmosphere ─────────────────────────── */
        const atmo = ctx.createRadialGradient(cx, cy, r * 0.25, cx, cy, r * 1.35);
        atmo.addColorStop(0.0, `hsla(${activeColor.h}, 100%, 65%, ${isSyncing ? 0.28 : 0.16})`);
        atmo.addColorStop(0.4, `hsla(${activeColor.h}, 85%, 55%, ${isSyncing ? 0.10 : 0.05})`);
        atmo.addColorStop(0.8, `hsla(210, 90%, 60%, 0.02)`);
        atmo.addColorStop(1.0, 'transparent');
        ctx.beginPath();
        ctx.arc(cx, cy, r * 1.35, 0, Math.PI * 2);
        ctx.fillStyle = atmo;
        ctx.fill();

        /* ── Five Energy Connection Beams ────────────────────────────── */
        const ribbons = getRibbons(cx, cy, r, t);
        for (const rib of ribbons) {
            const isCap    = activeCap === rib.key;
            const isDimmed = activeCap && !isCap && !isSyncing;
            const alpha    = isSyncing ? rib.opacity * 1.0 : (isDimmed ? rib.opacity * 0.28 : rib.opacity * (isCap ? 1.0 : 0.75));
            const lWidth   = isSyncing ? rib.width * 1.6 : (isDimmed ? rib.width * 0.7 : rib.width * (isCap ? 1.85 : 1.2));

            const color  = CAP_COLORS[rib.key];
            const [x0, y0, x1, y1, x2, y2, x3, y3] = rib.pts;

            // Ambient outer glow pass
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x0, y0);
            ctx.bezierCurveTo(x1, y1, x2, y2, x3, y3);
            ctx.strokeStyle = ribbonGradient(rib.pts, color, alpha * 0.45);
            ctx.lineWidth   = lWidth * (isCap || isSyncing ? 5.2 : 3.8);
            ctx.lineCap     = 'round';
            ctx.stroke();

            // Luminous core energy path
            ctx.beginPath();
            ctx.moveTo(x0, y0);
            ctx.bezierCurveTo(x1, y1, x2, y2, x3, y3);
            ctx.strokeStyle = ribbonGradient(rib.pts, color, alpha);
            ctx.lineWidth   = lWidth;
            ctx.lineCap     = 'round';
            ctx.stroke();

            // Connection node endpoints (luminous target rings)
            if (!isDimmed || isSyncing) {
                ctx.beginPath();
                ctx.arc(x0, y0, isCap ? 4.5 : 3.0, 0, Math.PI * 2);
                ctx.fillStyle   = `hsla(${color.h},${color.s}%,80%,${isCap ? 1.0 : 0.7})`;
                ctx.shadowBlur  = isCap ? 12 : 6;
                ctx.shadowColor = `hsla(${color.h},${color.s}%,${color.l}%,0.9)`;
                ctx.fill();
            }
            ctx.restore();

            // Travelling light energy particle with soft glowing trail
            if (!isDimmed || isSyncing) {
                const speedMult = (isCap || isSyncing) ? 0.72 : 0.48;
                const phase     = ((t * speedMult) + CAPS.indexOf(rib.key) * 0.20) % 1.0;
                const tp        = easeOut(phase);

                // Sample particle head position via deCasteljau
                const ax = lerp(x0, x1, tp), ay = lerp(y0, y1, tp);
                const bx = lerp(x1, x2, tp), by = lerp(y1, y2, tp);
                const cx2= lerp(x2, x3, tp), cy2= lerp(y2, y3, tp);
                const dx = lerp(ax, bx, tp),  dy = lerp(ay, by, tp);
                const ex = lerp(bx, cx2,tp),  ey = lerp(by, cy2,tp);
                const fx = lerp(dx, ex, tp),  fy = lerp(dy, ey, tp);

                // Particle tail (slightly behind tp)
                const tpTail = Math.max(0, tp - 0.05);
                const tax = lerp(x0, x1, tpTail), tay = lerp(y0, y1, tpTail);
                const tbx = lerp(x1, x2, tpTail), tby = lerp(y1, y2, tpTail);
                const tcx2= lerp(x2, x3, tpTail), tcy2= lerp(y2, y3, tpTail);
                const tdx = lerp(tax, tbx, tpTail), tdy = lerp(tay, tby, tpTail);
                const tex = lerp(tbx, tcx2, tpTail), tey = lerp(tby, tcy2, tpTail);
                const tfx = lerp(tdx, tex, tpTail), tfy = lerp(tdy, tey, tpTail);

                ctx.save();
                // Glowing tail line
                ctx.beginPath();
                ctx.moveTo(tfx, tfy);
                ctx.lineTo(fx, fy);
                ctx.strokeStyle = `hsla(${color.h},${color.s}%,75%,${isCap || isSyncing ? 0.85 : 0.45})`;
                ctx.lineWidth   = isCap || isSyncing ? 3.2 : 2.0;
                ctx.lineCap     = 'round';
                ctx.stroke();

                // Particle head
                const dotR = (isCap || isSyncing) ? 4.8 : 3.0;
                ctx.beginPath();
                ctx.arc(fx, fy, dotR, 0, Math.PI * 2);
                ctx.fillStyle   = '#ffffff';
                ctx.shadowBlur  = isCap || isSyncing ? 18 : 8;
                ctx.shadowColor = `hsla(${color.h},${color.s}%,70%,0.95)`;
                ctx.fill();
                ctx.restore();
            }
        }

        /* ── Central AI System Core ───────────────────────────────────── */
        const breathe = (1 + Math.sin(t * 0.60) * 0.04) * (isSyncing ? 1.06 : 1.0);
        const coreR   = r * 0.23 * breathe;

        // Luminous atmosphere halo
        const halo = ctx.createRadialGradient(cx, cy, coreR * 0.6, cx, cy, coreR * (isSyncing ? 2.6 : 2.2));
        halo.addColorStop(0.0, `hsla(${activeColor.h}, 100%, 65%, ${isSyncing ? 0.45 : 0.28})`);
        halo.addColorStop(0.5, `hsla(${activeColor.h}, 90%, 55%, ${isSyncing ? 0.18 : 0.09})`);
        halo.addColorStop(1.0, 'transparent');
        ctx.beginPath();
        ctx.arc(cx, cy, coreR * (isSyncing ? 2.6 : 2.2), 0, Math.PI * 2);
        ctx.fillStyle = halo;
        ctx.fill();

        // 1. Outer rotating blueprint ring (clockwise)
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(t * 0.04);
        ctx.beginPath();
        ctx.arc(0, 0, coreR * 1.85, 0, Math.PI * 2);
        ctx.strokeStyle = `hsla(${activeColor.h}, 100%, 70%, ${isSyncing ? 0.55 : 0.32})`;
        ctx.lineWidth   = 1.0;
        ctx.setLineDash([6, 10]);
        ctx.stroke();

        // Crosshair tick marks
        for (let i = 0; i < 4; i++) {
            const ta = (i / 4) * Math.PI * 2;
            const xA = Math.cos(ta) * (coreR * 1.76);
            const yA = Math.sin(ta) * (coreR * 1.76);
            const xB = Math.cos(ta) * (coreR * 1.94);
            const yB = Math.sin(ta) * (coreR * 1.94);
            ctx.beginPath();
            ctx.moveTo(xA, yA);
            ctx.lineTo(xB, yB);
            ctx.strokeStyle = `hsla(${activeColor.h}, 100%, 75%, ${isSyncing ? 0.75 : 0.45})`;
            ctx.lineWidth   = 1.2;
            ctx.stroke();
        }
        ctx.restore();

        // 2. Middle precision ring (counter-clockwise)
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(-t * 0.075);
        ctx.beginPath();
        ctx.arc(0, 0, coreR * 1.55, 0, Math.PI * 2);
        ctx.strokeStyle = `hsla(${activeColor.h}, 100%, 65%, ${isSyncing ? 0.60 : 0.38})`;
        ctx.lineWidth   = 1.1;
        ctx.setLineDash([3, 6]);
        ctx.stroke();

        // 8 node markers at 45 degree intervals
        for (let i = 0; i < 8; i++) {
            const ta = (i / 8) * Math.PI * 2;
            const px = Math.cos(ta) * (coreR * 1.55);
            const py = Math.sin(ta) * (coreR * 1.55);
            ctx.beginPath();
            ctx.arc(px, py, 1.8, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
        }
        ctx.restore();

        // 3. Inner rotating ring (clockwise fast)
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(t * 0.11);
        ctx.beginPath();
        ctx.arc(0, 0, coreR * 1.28, 0, Math.PI * 2);
        ctx.strokeStyle = `hsla(${activeColor.h}, 100%, 80%, ${isSyncing ? 0.70 : 0.42})`;
        ctx.lineWidth   = 1.2;
        ctx.setLineDash([12, 18]);
        ctx.stroke();
        ctx.restore();

        // Luminous Frosted Glass Core Disc
        const disc = ctx.createRadialGradient(cx - coreR * 0.3, cy - coreR * 0.3, 0, cx, cy, coreR);
        disc.addColorStop(0.0, 'rgba(255, 255, 255, 0.99)');
        disc.addColorStop(0.4, 'rgba(235, 245, 255, 0.94)');
        disc.addColorStop(1.0, 'rgba(195, 222, 255, 0.82)');
        ctx.save();
        ctx.beginPath();
        ctx.arc(cx, cy, coreR, 0, Math.PI * 2);
        ctx.fillStyle = disc;
        ctx.shadowBlur  = isSyncing ? 48 : 36;
        ctx.shadowColor = `hsla(${activeColor.h}, 100%, 60%, ${isSyncing ? 0.65 : 0.45})`;
        ctx.fill();

        // Inner specular edge ring
        ctx.beginPath();
        ctx.arc(cx, cy, coreR, 0, Math.PI * 2);
        ctx.strokeStyle = `hsla(${activeColor.h}, 100%, 85%, 0.75)`;
        ctx.lineWidth   = 1.6;
        ctx.stroke();
        ctx.restore();

        // Double Faceted Prism Core (Hexagon + Diamond)
        ctx.save();
        const rot = t * 0.09;
        const pR  = coreR * 0.55;
        ctx.beginPath();
        for (let i = 0; i < 6; i++) {
            const a = rot + (i / 6) * Math.PI * 2;
            const px = cx + Math.cos(a) * pR;
            const py = cy + Math.sin(a) * pR;
            i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        }
        ctx.closePath();
        const prism = ctx.createLinearGradient(cx - pR, cy - pR, cx + pR, cy + pR);
        prism.addColorStop(0.0, `hsla(${activeColor.h}, 100%, 55%, 0.82)`);
        prism.addColorStop(1.0, `hsla(${activeColor.h}, 100%, 75%, 0.92)`);
        ctx.fillStyle   = prism;
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth   = 1.3;
        ctx.fill();
        ctx.stroke();

        // Inner Rotating Diamond
        const rotInner = -t * 0.14;
        const pR2 = pR * 0.55;
        ctx.beginPath();
        for (let i = 0; i < 4; i++) {
            const a = rotInner + (i / 4) * Math.PI * 2;
            const px = cx + Math.cos(a) * pR2;
            const py = cy + Math.sin(a) * pR2;
            i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        }
        ctx.closePath();
        ctx.fillStyle   = 'rgba(255, 255, 255, 0.92)';
        ctx.strokeStyle = `hsla(${activeColor.h}, 100%, 65%, 0.9)`;
        ctx.lineWidth   = 1.1;
        ctx.fill();
        ctx.stroke();

        // Core AI Nucleus Point
        ctx.beginPath();
        ctx.arc(cx, cy, pR2 * 0.35, 0, Math.PI * 2);
        ctx.fillStyle   = '#ffffff';
        ctx.shadowBlur  = isSyncing ? 26 : 16;
        ctx.shadowColor = '#ffffff';
        ctx.fill();
        ctx.restore();

        /* ── Ambient Orbiting Micro-Particles ───────────────────────────── */
        if (!reduced) {
            const pCount = 16;
            for (let i = 0; i < pCount; i++) {
                const seed = i * 137.508; // golden angle
                const a    = (seed * 0.0174533) + t * 0.07;
                const dist = r * (0.45 + 0.55 * ((Math.sin(seed * 0.0837) + 1) / 2));
                const px   = cx + Math.cos(a) * dist;
                const py   = cy + Math.sin(a) * dist * 0.82;
                const sz   = 1.2 + 1.5 * ((Math.sin(seed * 0.113 + t * 0.26) + 1) / 2);
                const al   = 0.25 + 0.30 * ((Math.sin(seed * 0.077 + t * 0.22) + 1) / 2);
                ctx.save();
                ctx.beginPath();
                ctx.arc(px, py, sz, 0, Math.PI * 2);
                ctx.fillStyle   = `hsla(215, 100%, 70%, ${al})`;
                ctx.shadowBlur  = 8;
                ctx.shadowColor = 'rgba(10, 99, 255, 0.55)';
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
