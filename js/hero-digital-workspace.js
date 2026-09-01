/**
 * hero-digital-workspace.js — THE RAFly DIGITAL WORKSPACE HERO
 *
 * A world-class physical-digital hero environment.
 * Combining: Apple product launch precision x high-end creative tech studio x Figma/Linear quality.
 *
 * Features:
 * - Multi-depth 3D architectural slab sculpture with physical materiality
 * - Multi-plane mouse pointer parallax with damped tilt
 * - Canvas 2D procedural generative engine (trajectory drawing, signal waves, layout grids)
 * - Periodic editorial light sweep across headline emphasis
 * - Scroll hand-off disassembling elements toward section 02
 * - High performance rAF loop gated by IntersectionObserver
 */

const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const lerp  = (a, b, n)   => a + (b - a) * n;

export function initHeroDigitalWorkspace(host) {
    if (!host) return () => {};

    const reduced  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = () => window.innerWidth <= 991;

    /* ─── DOM Querying ─────────────────────────────────────── */
    const canvas     = host.querySelector('[data-workspace-canvas]');
    const ctx        = canvas ? canvas.getContext('2d') : null;
    const slab       = host.querySelector('[data-workspace-slab]');
    const planes     = [...host.querySelectorAll('[data-depth]')];
    const lightSweep = host.querySelector('[data-light-sweep]');
    const headLines  = [...host.querySelectorAll('.dw-h-inner')];

    /* ─── Internal State ───────────────────────────────────── */
    let rafId       = 0;
    let running     = false;
    let time        = 0;
    let scrollP     = 0;
    let mouseX      = 0, mouseY = 0;
    let tgtMouseX   = 0, tgtMouseY = 0;
    let sweepTimer  = 0;

    /* Set Canvas Resolution */
    function resizeCanvas() {
        if (!canvas) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        canvas.width  = (rect.width || 640) * dpr;
        canvas.height = (rect.height || 640) * dpr;
    }

    if (canvas) {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });
    }

    /* ─── Entrance Animations ─────────────────────────────── */
    function animateEntrance() {
        host.classList.add('dw-hero--entered');
        
        // Trigger periodic light sweep on highlighted phrase
        if (lightSweep) {
            setInterval(() => {
                lightSweep.classList.remove('is-sweeping');
                void lightSweep.offsetWidth; // Reflow
                lightSweep.classList.add('is-sweeping');
            }, 8000);
        }
    }

    /* ─── Mouse Movement Parallax ─────────────────────────── */
    const onMove = (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        tgtMouseX = clamp((e.clientX - cx) / cx, -1, 1);
        tgtMouseY = clamp((e.clientY - cy) / cy, -1, 1);
    };

    if (!reduced && !isMobile()) {
        window.addEventListener('mousemove', onMove, { passive: true });
    }

    /* ─── Render Canvas Procedural Visual ─────────────────── */
    function renderWorkspaceVisual(w, h, t, sp) {
        if (!ctx) return;

        ctx.clearRect(0, 0, w, h);

        const cx = w / 2;
        const cy = h / 2;

        // Layer 1: Architectural Blueprint Grid
        ctx.save();
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.08)';
        ctx.lineWidth = 1;
        const gridStep = 45;
        const offsetX = (t * 8) % gridStep;
        const offsetY = (t * 6) % gridStep;

        for (let x = offsetX; x < w; x += gridStep) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, h);
            ctx.stroke();
        }
        for (let y = offsetY; y < h; y += gridStep) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(w, y);
            ctx.stroke();
        }
        ctx.restore();

        // Layer 2: Generative Flowing Trajectories (Blue & Cyan Paths)
        ctx.save();
        const waveCount = 3;
        for (let i = 0; i < waveCount; i++) {
            const alpha = 0.25 + i * 0.12;
            const grad = ctx.createLinearGradient(0, 0, w, h);
            grad.addColorStop(0, `rgba(10, 99, 255, ${alpha})`);
            grad.addColorStop(0.5, `rgba(8, 145, 178, ${alpha * 0.8})`);
            grad.addColorStop(1, `rgba(97, 52, 201, ${alpha * 0.5})`);

            ctx.strokeStyle = grad;
            ctx.lineWidth = 1.8 - i * 0.3;

            ctx.beginPath();
            for (let x = 40; x < w - 40; x += 8) {
                const normX = (x - cx) / (w / 2);
                const y = cy + Math.sin(normX * 3.5 + t * 1.8 + i * 1.2) * (30 + i * 16) * Math.exp(-normX * normX * 0.9);
                if (x === 40) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }
            ctx.stroke();
        }
        ctx.restore();

        // Layer 3: Dynamic Interface Wireframe Frames (Website & Component Blocks)
        ctx.save();
        const fw = Math.min(w * 0.52, 340);
        const fh = Math.min(h * 0.38, 220);
        const fx = cx - fw / 2 + Math.sin(t * 0.8) * 8;
        const fy = cy - fh / 2 + Math.cos(t * 0.6) * 6;

        ctx.fillStyle = 'rgba(10, 20, 45, 0.65)';
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.35)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        if (ctx.roundRect) ctx.roundRect(fx, fy, fw, fh, 12);
        else ctx.rect(fx, fy, fw, fh);
        ctx.fill();
        ctx.stroke();

        // Header bar dots
        ctx.fillStyle = 'rgba(10, 99, 255, 0.5)';
        ctx.beginPath(); ctx.arc(fx + 18, fy + 14, 3.5, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(fx + 30, fy + 14, 3.5, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(fx + 42, fy + 14, 3.5, 0, Math.PI * 2); ctx.fill();

        // Layout rows
        ctx.fillStyle = 'rgba(255, 255, 255, 0.04)';
        ctx.fillRect(fx + 14, fy + 32, fw - 28, 28);

        // Columns
        const cw = (fw - 40) / 3;
        for (let c = 0; c < 3; c++) {
            ctx.fillStyle = 'rgba(10, 99, 255, 0.12)';
            ctx.strokeStyle = 'rgba(10, 99, 255, 0.25)';
            ctx.beginPath();
            const ch = 70 + Math.sin(t * 2 + c) * 8;
            if (ctx.roundRect) ctx.roundRect(fx + 14 + c * (cw + 6), fy + 68, cw, ch, 6);
            else ctx.rect(fx + 14 + c * (cw + 6), fy + 68, cw, ch);
            ctx.fill();
            ctx.stroke();
        }
        ctx.restore();

        // Layer 4: Travelling Data Packets
        ctx.save();
        for (let p = 0; p < 5; p++) {
            const pathProgress = ((t * 0.4 + p * 0.2) % 1);
            const px = lerp(40, w - 40, pathProgress);
            const py = cy + Math.sin((px - cx) / (w / 2) * 3.5 + t * 1.8) * 30;

            ctx.fillStyle = '#ffffff';
            ctx.shadowColor = '#0a63ff';
            ctx.shadowBlur = 10;
            ctx.beginPath();
            ctx.arc(px, py, 3, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0;
        }
        ctx.restore();
    }

    /* ─── Render Frame ─────────────────────────────────────── */
    function renderFrame() {
        const r = host.getBoundingClientRect();
        const vh = window.innerHeight;
        scrollP = clamp(-r.top / (r.height || vh), 0, 1);

        time += 0.016;

        if (canvas && ctx) {
            renderWorkspaceVisual(canvas.width, canvas.height, time, scrollP);
        }

        // Multi-Depth Mouse Parallax & 3D Tilt (Desktop)
        if (!reduced && !isMobile()) {
            mouseX = lerp(mouseX, tgtMouseX, 0.06);
            mouseY = lerp(mouseY, tgtMouseY, 0.06);

            planes.forEach((plane) => {
                const depth = parseFloat(plane.dataset.depth || '0.5');
                const px = (mouseX * depth * 18).toFixed(1);
                const py = (mouseY * depth * 16).toFixed(1);
                plane.style.transform = `translate3d(${px}px, ${py}px, 0)`;
            });

            if (slab) {
                const tiltX = (-mouseY * 4.0 + scrollP * 8).toFixed(2);
                const tiltY = ( mouseX * 5.5).toFixed(2);
                const shiftY = (scrollP * 60).toFixed(1); // Scroll hand-off
                slab.style.transform = `translate3d(0, ${shiftY}px, 0) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
            }
        }

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
            animateEntrance();
            start();
        } else {
            stop();
        }
    }, { rootMargin: '10% 0px' });

    io.observe(host);

    document.addEventListener('visibilitychange', () => {
        document.hidden ? stop() : (running || start());
    });

    renderFrame();

    if (reduced) {
        animateEntrance();
    }

    return function destroy() {
        stop();
        io.disconnect();
        window.removeEventListener('mousemove', onMove);
        window.removeEventListener('resize', resizeCanvas);
    };
}
