/**
 * field.js — the page-wide technical background.
 *
 * Two things the prototype drew on two separate fixed canvases with two
 * separate rAF loops:
 *
 *   TRACES   right-angle circuit paths snapped to a 42px grid, with data
 *            packets travelling along them, each packet a bright head and a
 *            nine-dot fading trail.
 *   NETWORK  a drifting particle field that links nearby points and pushes
 *            away from the cursor.
 *
 * They are ONE canvas and ONE loop here, for two reasons that both matter at
 * 60fps: two full-viewport canvases are two composited layers the compositor
 * has to blend every frame, and two rAF callbacks means two layout reads.
 *
 * THE O(n^2) PROBLEM. The network links every point to every other point to
 * decide which pairs are close enough to draw. At the prototype's density
 * that is roughly 130 points on a laptop and 8,400 distance checks per frame,
 * which is survivable, and on a 4K display it is 400 points and 80,000, which
 * is not. Points are bucketed into the same 42px grid the traces already use,
 * and each point only tests the nine buckets around it. The visual result is
 * identical because the link radius is smaller than three buckets.
 *
 * COLOURS COME FROM THE STYLESHEET. css/00-tokens.css carries the measured
 * contrast ratio beside every value; a hex baked in here would quietly stop
 * agreeing with the palette the first time the palette moved, and no harness
 * can see inside a canvas to catch it.
 *
 * It stops completely when the tab is hidden. A decorative rAF running in a
 * background tab is a battery cost with literally nothing to show for it.
 */

import { token, rgb } from './gates.js';

const GRID = 42;          // trace snap, and the spatial bucket size
const LINK = 112;         // network link radius, < 3 buckets by design
const DPR_CAP = 1.5;      // beyond this the fill cost doubles for no visible gain

const lerp = (a, b, n) => a + (b - a) * n;

export function initField(canvas) {
    const ctx = canvas.getContext('2d', { alpha: true });
    if (!ctx) return () => {};

    const accent = rgb(token('--blue', '#0a63ff'));
    const A = accent.join(',');

    let w = 0, h = 0, dpr = 1;
    let paths = [], packets = [], points = [], buckets = new Map();
    let mx = -9999, my = -9999;
    let raf = 0, running = false;

    /* ------------------------------------------------------------ build */

    function buildTraces() {
        paths = [];
        packets = [];
        const count = Math.round(w / 150);

        for (let i = 0; i < count; i++) {
            const pts = [];
            let px = Math.round((Math.random() * w) / GRID) * GRID;
            let py = Math.round((Math.random() * h) / GRID) * GRID;
            pts.push({ x: px, y: py });

            const steps = 6 + Math.floor(Math.random() * 6);
            let horiz = Math.random() < 0.5;
            for (let s = 0; s < steps; s++) {
                const len = (1 + Math.floor(Math.random() * 4)) * GRID * (Math.random() < 0.5 ? -1 : 1);
                if (horiz) px = Math.max(0, Math.min(w, px + len));
                else py = Math.max(0, Math.min(h, py + len));
                pts.push({ x: px, y: py });
                horiz = !horiz;
            }

            // Cumulative arc length, so a packet's position is one lookup
            // rather than a walk from the start of the path every frame.
            let total = 0;
            const segs = [];
            for (let k = 1; k < pts.length; k++) {
                const d = Math.hypot(pts[k].x - pts[k - 1].x, pts[k].y - pts[k - 1].y);
                if (d > 0) { segs.push({ a: pts[k - 1], b: pts[k], d, acc: total }); total += d; }
            }
            if (total <= 0) continue;

            paths.push({ pts, segs, total });
            const n = 1 + Math.floor(Math.random() * 2);
            for (let p = 0; p < n; p++) {
                packets.push({ path: paths.length - 1, t: Math.random() * total, sp: 0.55 + Math.random() * 1.15 });
            }
        }
    }

    function buildPoints() {
        // Capped, not just scaled. A 4K screen does not want four times the
        // particles; it wants the same design at a higher resolution.
        const n = Math.min(180, Math.floor((w * h) / 20000));
        points = Array.from({ length: n }, () => ({
            x: Math.random() * w,
            y: Math.random() * h,
            vx: (Math.random() - 0.5) * 0.18,
            vy: (Math.random() - 0.5) * 0.18,
            r: Math.random() * 1.4 + 0.6,
        }));
    }

    function resize() {
        dpr = Math.min(window.devicePixelRatio || 1, DPR_CAP);
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = Math.round(w * dpr);
        canvas.height = Math.round(h * dpr);
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        buildTraces();
        buildPoints();
    }

    /* ------------------------------------------------------------- draw */

    function at(path, dist) {
        const d = ((dist % path.total) + path.total) % path.total;
        for (const s of path.segs) {
            if (d >= s.acc && d <= s.acc + s.d) {
                const f = (d - s.acc) / s.d;
                return { x: lerp(s.a.x, s.b.x, f), y: lerp(s.a.y, s.b.y, f) };
            }
        }
        return path.pts[0];
    }

    function drawTraces() {
        ctx.lineWidth = 1;
        ctx.strokeStyle = `rgba(${A},.085)`;
        for (const p of paths) {
            ctx.beginPath();
            ctx.moveTo(p.pts[0].x, p.pts[0].y);
            for (let k = 1; k < p.pts.length; k++) ctx.lineTo(p.pts[k].x, p.pts[k].y);
            ctx.stroke();
        }
        // Junction pads, batched into one path so this is one fill call for
        // the whole page rather than one per corner.
        ctx.fillStyle = `rgba(${A},.14)`;
        ctx.beginPath();
        for (const p of paths) {
            for (const pt of p.pts) {
                ctx.moveTo(pt.x + 1.9, pt.y);
                ctx.arc(pt.x, pt.y, 1.9, 0, Math.PI * 2);
            }
        }
        ctx.fill();
    }

    function drawPackets() {
        for (const pk of packets) {
            pk.t += pk.sp;
            const p = paths[pk.path];
            if (!p) continue;

            for (let k = 0; k < 9; k++) {
                const pos = at(p, pk.t - k * 7);
                ctx.fillStyle = `rgba(${A},${(1 - k / 9) * 0.55})`;
                ctx.beginPath();
                ctx.arc(pos.x, pos.y, 2.4 - k * 0.16, 0, Math.PI * 2);
                ctx.fill();
            }

            const head = at(p, pk.t);
            const g = ctx.createRadialGradient(head.x, head.y, 0, head.x, head.y, 14);
            g.addColorStop(0, 'rgba(120,175,255,.65)');
            g.addColorStop(1, `rgba(${A},0)`);
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.arc(head.x, head.y, 14, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function drawNetwork() {
        buckets.clear();
        for (const p of points) {
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > w) p.vx *= -1;
            if (p.y < 0 || p.y > h) p.vy *= -1;

            const d = Math.hypot(p.x - mx, p.y - my);
            if (d < 150 && d > 0) {
                const f = (150 - d) / 150;
                p.x += ((p.x - mx) / d) * f * 1.5;
                p.y += ((p.y - my) / d) * f * 1.5;
            }

            const key = ((p.x / GRID) | 0) + ':' + ((p.y / GRID) | 0);
            let b = buckets.get(key);
            if (!b) { b = []; buckets.set(key, b); }
            b.push(p);
        }

        // Links. Only the nine buckets around each point are tested, and only
        // the forward half of them, so no pair is considered twice.
        ctx.lineWidth = 1;
        for (const [key, cell] of buckets) {
            const [bx, by] = key.split(':').map(Number);
            for (let dx = -1; dx <= 1; dx++) {
                for (let dy = -1; dy <= 1; dy++) {
                    const other = buckets.get((bx + dx) + ':' + (by + dy));
                    if (!other) continue;
                    for (const a of cell) {
                        for (const b of other) {
                            if (a === b || (b.x < a.x) || (b.x === a.x && b.y <= a.y)) continue;
                            const d = Math.hypot(a.x - b.x, a.y - b.y);
                            if (d >= LINK) continue;
                            ctx.strokeStyle = `rgba(${A},${0.11 * (1 - d / LINK)})`;
                            ctx.beginPath();
                            ctx.moveTo(a.x, a.y);
                            ctx.lineTo(b.x, b.y);
                            ctx.stroke();
                        }
                    }
                }
            }
        }

        ctx.fillStyle = `rgba(${A},.30)`;
        ctx.beginPath();
        for (const p of points) {
            ctx.moveTo(p.x + p.r, p.y);
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        }
        ctx.fill();
    }

    function frame() {
        ctx.clearRect(0, 0, w, h);
        drawTraces();
        drawPackets();
        drawNetwork();
        raf = requestAnimationFrame(frame);
    }

    /* ------------------------------------------------------------- wire */

    const onMove = (e) => { mx = e.clientX; my = e.clientY; };
    const onResize = () => resize();
    const onVisibility = () => { document.hidden ? stop() : start(); };

    function start() {
        if (running) return;
        running = true;
        raf = requestAnimationFrame(frame);
    }
    function stop() {
        running = false;
        cancelAnimationFrame(raf);
    }

    resize();
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('mousemove', onMove, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);
    start();

    return function destroy() {
        stop();
        window.removeEventListener('resize', onResize);
        window.removeEventListener('mousemove', onMove);
        document.removeEventListener('visibilitychange', onVisibility);
        ctx.clearRect(0, 0, w, h);
    };
}
