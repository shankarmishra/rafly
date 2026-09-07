/**
 * service-engine.js — THE RAFly SERVICE CANVAS & INTERACTIVE VISUAL SYSTEM
 *
 * Dedicated Canvas2D & SVG engine driving high-fps interactive visual systems
 * for all 5 service detail pages (/web-development, /web-security,
 * /marketing-advertisement, /content-creation, /ecommerce-support).
 *
 * Performance-gated by IntersectionObserver & prefers-reduced-motion.
 */

const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const lerp  = (a, b, n)   => a + (b - a) * n;

export function initServiceEngine(host) {
    if (!host) return () => {};

    const serviceKey = host.getAttribute('data-service-key') || 'web';
    const canvas = host.querySelector('[data-service-canvas]');
    const ctx = canvas ? canvas.getContext('2d') : null;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let rafId = 0;
    let running = false;
    let time = 0;
    let mouseX = 0, mouseY = 0;
    let tgtMouseX = 0, tgtMouseY = 0;

    // Resizing canvas to crisp resolution
    function resizeCanvas() {
        if (!canvas) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        canvas.width = (rect.width || 480) * dpr;
        canvas.height = (rect.height || 480) * dpr;
    }

    if (canvas) {
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });
    }

    // Parallax tracking
    const onMove = (e) => {
        const rect = host.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        tgtMouseX = clamp((e.clientX - cx) / (rect.width / 2), -1, 1);
        tgtMouseY = clamp((e.clientY - cy) / (rect.height / 2), -1, 1);
    };

    if (!reduced) {
        host.addEventListener('mousemove', onMove, { passive: true });
    }

    // Dynamic Render Engine per Service Key
    function render(w, h, t) {
        if (!ctx) return;
        ctx.clearRect(0, 0, w, h);
        ctx.shadowBlur = 0;

        const cx = w / 2;
        const cy = h / 2;

        mouseX = lerp(mouseX, tgtMouseX, 0.05);
        mouseY = lerp(mouseY, tgtMouseY, 0.05);

        ctx.save();
        ctx.translate(cx + mouseX * 12, cy + mouseY * 12);

        if (serviceKey === 'web') {
            drawWebArchitectureEngine(ctx, w, h, t);
        } else if (serviceKey === 'security') {
            drawSecurityPerimeterEngine(ctx, w, h, t);
        } else if (serviceKey === 'marketing') {
            drawMarketingIntelligenceEngine(ctx, w, h, t);
        } else if (serviceKey === 'content') {
            drawContentProductionEngine(ctx, w, h, t);
        } else if (serviceKey === 'ecom') {
            drawCommerceInfrastructureEngine(ctx, w, h, t);
        }

        ctx.restore();
    }

    /* =========================================================================
       1. WEB DEVELOPMENT ENGINE — Living Web Architecture Machine
       USER → UI → FRONTEND → API → DATABASE → EDGE
       ========================================================================= */
    function drawWebArchitectureEngine(ctx, w, h, t) {
        const radius = Math.min(w, h) * 0.36;

        // Concentric blueprint rings
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.15)';
        ctx.lineWidth = 1.5;
        ctx.setLineDash([4, 6]);
        ctx.beginPath(); ctx.arc(0, 0, radius, 0, Math.PI * 2); ctx.stroke();
        ctx.beginPath(); ctx.arc(0, 0, radius * 0.65, 0, Math.PI * 2); ctx.stroke();
        ctx.setLineDash([]);

        // Core Node Hub - Clean Crisp Rings
        ctx.strokeStyle = 'rgba(10, 99, 255, 0.28)';
        ctx.lineWidth = 1.5;
        ctx.beginPath(); ctx.arc(0, 0, radius * 0.45, 0, Math.PI * 2); ctx.stroke();

        // Data Packets along 6 Orbital Paths
        const nodes = [
            { label: 'USER', angle: 0 },
            { label: 'UI', angle: Math.PI / 3 },
            { label: 'FRONTEND', angle: (2 * Math.PI) / 3 },
            { label: 'API', angle: Math.PI },
            { label: 'DATABASE', angle: (4 * Math.PI) / 3 },
            { label: 'EDGE CDN', angle: (5 * Math.PI) / 3 }
        ];

        nodes.forEach((node, i) => {
            const nx = Math.cos(node.angle + t * 0.15) * radius;
            const ny = Math.sin(node.angle + t * 0.15) * radius;

            // Connecting vector line to center
            ctx.strokeStyle = 'rgba(10, 99, 255, 0.22)';
            ctx.lineWidth = 1.2;
            ctx.beginPath(); ctx.moveTo(0, 0); ctx.lineTo(nx, ny); ctx.stroke();

            // Moving data packet along line
            const progress = (t * 0.8 + i * 0.3) % 1;
            const px = nx * progress;
            const py = ny * progress;

            ctx.fillStyle = '#38bdf8';
            ctx.beginPath(); ctx.arc(px, py, 4, 0, Math.PI * 2); ctx.fill();

            // Outer Node Dot
            ctx.fillStyle = '#0a63ff';
            ctx.beginPath(); ctx.arc(nx, ny, 7, 0, Math.PI * 2); ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
        });

        // Center Pulsing Core
        const corePulse = 14 + Math.sin(t * 3) * 3;
        ctx.fillStyle = '#0a63ff';
        ctx.beginPath(); ctx.arc(0, 0, corePulse, 0, Math.PI * 2); ctx.fill();
    }

    /* =========================================================================
       2. WEB SECURITY ENGINE — Defensive Cyber Perimeter & Radar
       INTERNET → EDGE → TLS → WAF → APPLICATION → DATA
       ========================================================================= */
    function drawSecurityPerimeterEngine(ctx, w, h, t) {
        const radius = Math.min(w, h) * 0.38;

        // Radar Scanning Sweep
        const sweepAngle = t * 1.5;
        const sweepGrad = ctx.createConicGradient(sweepAngle, 0, 0);
        sweepGrad.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        sweepGrad.addColorStop(0.15, 'rgba(16, 185, 129, 0.05)');
        sweepGrad.addColorStop(0.3, 'rgba(16, 185, 129, 0)');
        ctx.fillStyle = sweepGrad;
        ctx.beginPath(); ctx.arc(0, 0, radius, 0, Math.PI * 2); ctx.fill();

        // Protective Concentric Shield Rings
        for (let r = 1; r <= 3; r++) {
            ctx.strokeStyle = `rgba(16, 185, 129, ${0.15 * r})`;
            ctx.lineWidth = 1.5;
            ctx.beginPath(); ctx.arc(0, 0, (radius / 3) * r, 0, Math.PI * 2); ctx.stroke();
        }

        // Threat Particles Deflection Animation
        for (let p = 0; p < 8; p++) {
            const pAngle = (p * Math.PI) / 4 + Math.sin(t + p) * 0.2;
            const dist = radius * (0.4 + 0.6 * ((t * 0.4 + p * 0.2) % 1));
            const px = Math.cos(pAngle) * dist;
            const py = Math.sin(pAngle) * dist;

            ctx.fillStyle = dist > radius * 0.75 ? 'rgba(239, 68, 68, 0.8)' : 'rgba(16, 185, 129, 0.9)';
            ctx.beginPath(); ctx.arc(px, py, 3.5, 0, Math.PI * 2); ctx.fill();
        }

        // Central Shield Core
        ctx.fillStyle = '#047857';
        ctx.beginPath(); ctx.arc(0, 0, 18, 0, Math.PI * 2); ctx.fill();
    }

    /* =========================================================================
       3. MARKETING & ADVERTISING ENGINE — Growth Intelligence Matrix
       AUDIENCE → SIGNAL → CAMPAIGN → TRAFFIC → CONVERSION → RETENTION
       ========================================================================= */
    function drawMarketingIntelligenceEngine(ctx, w, h, t) {
        const radius = Math.min(w, h) * 0.38;

        // Dynamic Growth Curves (Attribution Wave)
        ctx.strokeStyle = 'rgba(56, 189, 248, 0.4)';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        for (let x = -radius; x <= radius; x += 10) {
            const y = Math.sin((x / radius) * 4 + t * 2) * 28 + (x / radius) * 15;
            if (x === -radius) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.stroke();

        // Particle Clusters along conversion path
        for (let i = 0; i < 12; i++) {
            const frac = (i / 12 + t * 0.1) % 1;
            const px = -radius + frac * (radius * 2);
            const py = Math.sin((px / radius) * 4 + t * 2) * 28 + (px / radius) * 15;

            ctx.fillStyle = '#0284c7';
            ctx.beginPath(); ctx.arc(px, py, 4.5, 0, Math.PI * 2); ctx.fill();
        }

        // Outer Funnel Nodes
        for (let k = 0; k < 5; k++) {
            const angle = (k * Math.PI * 2) / 5;
            const kx = Math.cos(angle) * (radius * 0.85);
            const ky = Math.sin(angle) * (radius * 0.85);

            ctx.strokeStyle = 'rgba(10, 99, 255, 0.3)';
            ctx.lineWidth = 1.2;
            ctx.beginPath(); ctx.moveTo(0, 0); ctx.lineTo(kx, ky); ctx.stroke();

            ctx.fillStyle = '#0a63ff';
            ctx.beginPath(); ctx.arc(kx, ky, 6, 0, Math.PI * 2); ctx.fill();
        }
    }

    /* =========================================================================
       4. CONTENT CREATION ENGINE — Creative Production OS
       IDEA → RESEARCH → SCRIPT → DESIGN → EDIT → PUBLISH → DISTRIBUTE
       ========================================================================= */
    function drawContentProductionEngine(ctx, w, h, t) {
        const radius = Math.min(w, h) * 0.36;

        // Floating Document Sheet Frames
        for (let layer = 3; layer >= 1; layer--) {
            const lScale = 1 - layer * 0.12;
            const lAngle = (layer * 0.15) + Math.sin(t * 0.5 + layer) * 0.08;
            const sw = radius * 1.3 * lScale;
            const sh = radius * 1.6 * lScale;

            ctx.save();
            ctx.rotate(lAngle);
            ctx.fillStyle = `rgba(255, 255, 255, ${0.15 * (4 - layer)})`;
            ctx.strokeStyle = `rgba(10, 99, 255, ${0.25 * (4 - layer)})`;
            ctx.lineWidth = 1.5;
            ctx.fillRect(-sw / 2, -sh / 2, sw, sh);
            ctx.strokeRect(-sw / 2, -sh / 2, sw, sh);

            // Floating Text Fragment Lines inside Document
            ctx.fillStyle = `rgba(10, 99, 255, ${0.3 * (4 - layer)})`;
            for (let line = 0; line < 4; line++) {
                ctx.fillRect(-sw * 0.35, -sh * 0.3 + line * 16, sw * (0.5 + Math.sin(line + t) * 0.2), 3);
            }
            ctx.restore();
        }

        // Editorial Cursor Animation
        const cursorX = Math.cos(t * 1.8) * (radius * 0.5);
        const cursorY = Math.sin(t * 1.8) * (radius * 0.5);
        ctx.fillStyle = '#0a63ff';
        ctx.beginPath(); ctx.arc(cursorX, cursorY, 5, 0, Math.PI * 2); ctx.fill();
    }

    /* =========================================================================
       5. E-COMMERCE SUPPORT ENGINE — Commerce Infrastructure Model
       SHOPPER → PRODUCT → CART → CHECKOUT → PAYMENT → ORDER → DELIVERY
       ========================================================================= */
    function drawCommerceInfrastructureEngine(ctx, w, h, t) {
        const radius = Math.min(w, h) * 0.38;

        // Circular Commerce Trajectory Ring
        ctx.strokeStyle = 'rgba(8, 145, 178, 0.25)';
        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.arc(0, 0, radius, 0, Math.PI * 2); ctx.stroke();

        // 5 Cart/Order Nodes on Trajectory
        const commerceNodes = ['SHOPPER', 'PRODUCT', 'CART', 'PAYMENT', 'DISPATCH'];
        commerceNodes.forEach((node, idx) => {
            const angle = (idx * Math.PI * 2) / 5 + t * 0.2;
            const nx = Math.cos(angle) * radius;
            const ny = Math.sin(angle) * radius;

            ctx.fillStyle = '#0891b2';
            ctx.beginPath(); ctx.arc(nx, ny, 7, 0, Math.PI * 2); ctx.fill();

            // Connection line between consecutive nodes
            const nextAngle = ((idx + 1) * Math.PI * 2) / 5 + t * 0.2;
            const nnx = Math.cos(nextAngle) * radius;
            const nny = Math.sin(nextAngle) * radius;

            ctx.strokeStyle = 'rgba(6, 182, 212, 0.4)';
            ctx.lineWidth = 1.5;
            ctx.beginPath(); ctx.moveTo(nx, ny); ctx.lineTo(nnx, nny); ctx.stroke();
        });

        // Central Checkout Core Pulse
        const coreR = 16 + Math.sin(t * 2.5) * 4;
        ctx.fillStyle = '#0891b2';
        ctx.beginPath(); ctx.arc(0, 0, coreR, 0, Math.PI * 2); ctx.fill();
    }

    // Render loop
    function renderFrame() {
        time += 0.016;
        if (canvas && ctx) {
            render(canvas.width, canvas.height, time);
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

    // IntersectionObserver Gate for 60fps performance
    const io = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) start();
        else stop();
    }, { rootMargin: '15% 0px' });

    io.observe(host);

    renderFrame();

    return function destroy() {
        stop();
        io.disconnect();
        if (!reduced) {
            host.removeEventListener('mousemove', onMove);
        }
        window.removeEventListener('resize', resizeCanvas);
    };
}
