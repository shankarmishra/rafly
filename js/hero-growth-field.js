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

    /* ── Ambient Constellation Node Particles ────────────────────────── */
    const NODE_COUNT = 42;
    const nodes = [];
    for (let i = 0; i < NODE_COUNT; i++) {
        nodes.push({
            x: Math.random(),
            y: Math.random(),
            vx: (Math.random() - 0.5) * 0.0004,
            vy: (Math.random() - 0.5) * 0.0004,
            radius: 1.5 + Math.random() * 2.2,
            alpha: 0.25 + Math.random() * 0.50,
            phase: Math.random() * Math.PI * 2,
        });
    }

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

        /* 1. Fluid Aurora Mesh Background Orbs */
        const orb1X = cx + Math.sin(t * 0.25) * (w * 0.25) + pxCurr * 40;
        const orb1Y = cy + Math.cos(t * 0.20) * (h * 0.20) + pyCurr * 30;
        const orb1  = ctx.createRadialGradient(orb1X, orb1Y, 10, orb1X, orb1Y, w * 0.45);
        orb1.addColorStop(0.0, 'rgba(10, 99, 255, 0.14)');
        orb1.addColorStop(0.5, 'rgba(56, 189, 248, 0.06)');
        orb1.addColorStop(1.0, 'transparent');
        ctx.beginPath();
        ctx.arc(orb1X, orb1Y, w * 0.45, 0, Math.PI * 2);
        ctx.fillStyle = orb1;
        ctx.fill();

        const orb2X = cx - Math.cos(t * 0.22) * (w * 0.22) - pxCurr * 30;
        const orb2Y = cy - Math.sin(t * 0.28) * (h * 0.22) - pyCurr * 25;
        const orb2  = ctx.createRadialGradient(orb2X, orb2Y, 10, orb2X, orb2Y, w * 0.40);
        orb2.addColorStop(0.0, 'rgba(99, 102, 241, 0.12)');
        orb2.addColorStop(0.5, 'rgba(10, 99, 255, 0.05)');
        orb2.addColorStop(1.0, 'transparent');
        ctx.beginPath();
        ctx.arc(orb2X, orb2Y, w * 0.40, 0, Math.PI * 2);
        ctx.fillStyle = orb2;
        ctx.fill();

        /* 2. Expanding System Wave Shockwave */
        const pulsePhase = (t * 0.18) % 1.0;
        const pulseR     = pulsePhase * (Math.max(w, h) * 0.70);
        const pulseAlpha = (1.0 - pulsePhase) * 0.15;
        ctx.save();
        ctx.beginPath();
        ctx.arc(cx + pxCurr * 20, cy + pyCurr * 15, pulseR, 0, Math.PI * 2);
        ctx.strokeStyle = `rgba(10, 99, 255, ${pulseAlpha})`;
        ctx.lineWidth   = 1.2;
        ctx.stroke();
        ctx.restore();

        /* 3. Constellation Node Network & Interconnecting Beams */
        const mouseX = cx + pxCurr * w;
        const mouseY = cy + pyCurr * h;

        // Position nodes & calculate physical coordinates
        const physNodes = nodes.map((node) => {
            node.x += node.vx;
            node.y += node.vy;
            if (node.x < 0 || node.x > 1) node.vx *= -1;
            if (node.y < 0 || node.y > 1) node.vy *= -1;

            const px = node.x * w + Math.sin(t * 0.4 + node.phase) * 8 + pxCurr * 15;
            const py = node.y * h + Math.cos(t * 0.3 + node.phase) * 8 + pyCurr * 12;
            return { px, py, radius: node.radius, alpha: node.alpha };
        });

        // Interconnecting lines between close nodes
        const maxDist = 170;
        for (let i = 0; i < physNodes.length; i++) {
            for (let j = i + 1; j < physNodes.length; j++) {
                const n1 = physNodes[i];
                const n2 = physNodes[j];
                const dx = n1.px - n2.px;
                const dy = n1.py - n2.py;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < maxDist) {
                    const lineAlpha = (1.0 - dist / maxDist) * 0.18;
                    ctx.beginPath();
                    ctx.moveTo(n1.px, n1.py);
                    ctx.lineTo(n2.px, n2.py);
                    ctx.strokeStyle = `rgba(10, 99, 255, ${lineAlpha})`;
                    ctx.lineWidth   = 1.0;
                    ctx.stroke();
                }
            }
        }

        // Draw individual glowing nodes
        for (const n of physNodes) {
            ctx.save();
            ctx.beginPath();
            ctx.arc(n.px, n.py, n.radius, 0, Math.PI * 2);
            ctx.fillStyle   = '#ffffff';
            ctx.shadowBlur  = 10;
            ctx.shadowColor = 'rgba(10, 99, 255, 0.8)';
            ctx.globalAlpha = n.alpha;
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
