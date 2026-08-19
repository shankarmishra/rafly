/**
 * render-stills.mjs — render the signature object to still images, offline.
 *
 * WHY THIS EXISTS
 * ---------------
 * The still is not a fallback. It is the primary asset:
 *
 *   - it is what every phone sees (WebGL never loads below 761px);
 *   - it is what every visitor sees for the first second on desktop, before
 *     the live scene is allowed to load;
 *   - it is what a visitor with WebGL disabled, reduced motion, Save-Data or
 *     a dead GPU driver sees permanently;
 *   - it is the LCP-adjacent image, so it is the one that has to be good.
 *
 * The quality gate in the brief — "export the hero as a still, look at it,
 * ask whether it would work as a printed poster" — is only answerable if the
 * still actually exists as a file. This produces it.
 *
 * HOW
 * ---
 * It drives real Chrome over the DevTools Protocol, exactly the way
 * inc/tools/shoot.mjs already does, with no dependencies. It navigates to the
 * dev server so the page has a real same-origin URL, then replaces the
 * document with a render harness that imports the SAME js/assembly.js and
 * js/studio.js the live site uses. There is no second copy of the scene.
 *
 * Encoding is done by Chrome: canvas.toDataURL('image/webp', q) produces the
 * final WebP with its alpha channel intact, so no image library is needed and
 * nothing is added to the repo's zero-dependency posture.
 *
 * Usage:
 *   node inc/tools/render-stills.mjs [--base http://127.0.0.1:8899]
 *                                    [--out assets/render] [--only hero]
 *                                    [--quality 0.86] [--keep]
 */

import { spawn } from 'node:child_process';
import { mkdir, writeFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import os from 'node:os';

const args = process.argv.slice(2);
const arg = (n, d) => { const i = args.indexOf(n); return i >= 0 && args[i + 1] ? args[i + 1] : d; };
const has = (n) => args.includes(n);

const BASE = arg('--base', 'http://127.0.0.1:8899').replace(/\/$/, '');
const OUT = path.resolve(arg('--out', 'assets/render'));
const QUALITY = Number(arg('--quality', '0.86'));
const ONLY = arg('--only', '');

const CHROME = [
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
    '/usr/bin/google-chrome',
    '/usr/bin/chromium',
].find((p) => existsSync(p));

if (!CHROME) { console.error('No Chrome binary found.'); process.exit(2); }

/* ==========================================================================
   What gets rendered

   explode  0 assembled .. 1 fully open
   cam      position along the seven-key camera path in js/studio.js
   w/h      CSS pixels; dpr multiplies them for the actual render
   ========================================================================== */

const SHOTS = [
    /* The hero. Three widths, because this one is served to every screen and
       a phone has no business downloading a 1400px render. */
    { name: 'core-hero-1400', explode: 0, cam: 0.00, w: 1400, h: 1560, dpr: 1.6 },
    { name: 'core-hero-900',  explode: 0, cam: 0.00, w: 900,  h: 1000, dpr: 1.7 },
    { name: 'core-hero-560',  explode: 0, cam: 0.00, w: 560,  h: 640,  dpr: 2.0 },

    /* The exploded architecture — the still behind the services section when
       WebGL is not available, and the desktop poster frame before it loads. */
    { name: 'core-open-1400', explode: 1, cam: 0.72, w: 1400, h: 1560, dpr: 1.6 },
    { name: 'core-open-900',  explode: 1, cam: 0.72, w: 900,  h: 1000, dpr: 1.7 },

    /* The phone sequence. Three states, not five: the story is
       assembled -> opening -> open, and five near-identical frames cost four
       times the bytes to say the same thing. Labels are DOM, drawn over the
       third frame, so they stay selectable and translatable. */
    { name: 'core-seq-1-640', explode: 0.00, cam: 0.00, w: 640, h: 760, dpr: 1.8 },
    { name: 'core-seq-2-640', explode: 0.45, cam: 0.34, w: 640, h: 760, dpr: 1.8 },
    { name: 'core-seq-3-640', explode: 1.00, cam: 0.72, w: 640, h: 860, dpr: 1.8 },

    /* The open-graph card wants the object too, and at a fixed 1200x630 it
       cannot be a crop of anything above. */
    { name: 'core-og-1200', explode: 0.22, cam: 0.14, w: 1200, h: 630, dpr: 1 },
];

/* ==========================================================================
   The render harness — runs inside the page
   ========================================================================== */

const HARNESS = `<!doctype html><html><head><meta charset="utf-8">
<style>html,body{margin:0;background:transparent}canvas{display:block}</style>
</head><body><script type="module">
import * as THREE from '/vendor/three/three.module.min.js';
import { buildAssembly, setExplode } from '/js/assembly.js';
import { buildStudio, applyCamera } from '/js/studio.js';

const canvas = document.createElement('canvas');
document.body.appendChild(canvas);

const renderer = new THREE.WebGLRenderer({
    canvas, antialias: true, alpha: true,
    preserveDrawingBuffer: true, powerPreference: 'high-performance',
});
renderer.setClearAlpha(0);

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(28, 1, 0.1, 100);
const studio = buildStudio(THREE, renderer, scene, { quality: 'still' });
const rig = buildAssembly(THREE);
scene.add(rig.root);

window.__shoot = (spec) => {
    const W = Math.round(spec.w * spec.dpr), H = Math.round(spec.h * spec.dpr);
    renderer.setPixelRatio(1);
    renderer.setSize(W, H, false);
    setExplode(rig, spec.explode);
    studio.setExplode(spec.explode);
    applyCamera(THREE, camera, spec.cam, W / H);
    // Render twice: the first pass warms shader compilation and the shadow
    // map, and a cold first frame is how a "sometimes the shadow is missing"
    // bug gets baked into a committed asset.
    renderer.render(scene, camera);
    renderer.render(scene, camera);
    return canvas.toDataURL('image/webp', spec.quality);
};
window.__ready = true;
<\/script></body></html>`;

/* ==========================================================================
   CDP
   ========================================================================== */

class CDP {
    constructor(ws) {
        this.ws = ws; this.id = 0; this.pending = new Map(); this.method = new Map();
        ws.addEventListener('message', (e) => {
            const m = JSON.parse(e.data);
            if (m.id && this.pending.has(m.id)) {
                const { resolve, reject } = this.pending.get(m.id);
                this.pending.delete(m.id);
                m.error ? reject(new Error(`${m.error.message} (${this.method.get(m.id)})`)) : resolve(m.result);
            }
        });
    }
    send(method, params = {}) {
        const id = ++this.id;
        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.method.set(id, method);
            this.ws.send(JSON.stringify({ id, method, params }));
            setTimeout(() => {
                if (this.pending.has(id)) { this.pending.delete(id); reject(new Error(`CDP timeout: ${method}`)); }
            }, 60000);
        });
    }
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function main() {
    await mkdir(OUT, { recursive: true });

    const profile = path.join(os.tmpdir(), 'rafly-render-profile');
    const chrome = spawn(CHROME, [
        '--headless=new',
        '--remote-debugging-port=9333',
        `--user-data-dir=${profile}`,
        '--no-first-run', '--no-default-browser-check',
        /* Headless Chrome has no GPU. SwiftShader is a real, conformant
           software GL implementation — slower, pixel-identical for our
           purposes, and it is what makes an unattended render possible. */
        '--use-gl=angle', '--use-angle=swiftshader',
        '--enable-unsafe-swiftshader',
        '--hide-scrollbars', '--mute-audio',
    ], { stdio: 'ignore' });

    let target = null;
    for (let i = 0; i < 60 && !target; i++) {
        await sleep(250);
        try {
            const list = await (await fetch('http://127.0.0.1:9333/json/list')).json();
            target = list.find((t) => t.type === 'page');
        } catch { /* not up yet */ }
    }
    if (!target) { chrome.kill(); throw new Error('Chrome did not expose a debugging target.'); }

    const ws = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((r) => ws.addEventListener('open', r));
    const cdp = new CDP(ws);
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');

    const errors = [];
    ws.addEventListener('message', (e) => {
        const m = JSON.parse(e.data);
        if (m.method === 'Runtime.exceptionThrown') {
            errors.push(m.params.exceptionDetails.exception?.description
                || m.params.exceptionDetails.text);
        }
    });

    /* Navigate for the origin, then swap the document. This is what lets the
       harness import /js/assembly.js by absolute path without the file ever
       being reachable in production — inc/ is 404'd by .htaccess. */
    await cdp.send('Page.navigate', { url: `${BASE}/robots.txt` });
    await sleep(600);
    const { frameTree } = await cdp.send('Page.getFrameTree');
    await cdp.send('Page.setDocumentContent', { frameId: frameTree.frame.id, html: HARNESS });

    let ready = false;
    for (let i = 0; i < 80 && !ready; i++) {
        await sleep(250);
        const r = await cdp.send('Runtime.evaluate', { expression: 'window.__ready === true' });
        ready = r.result.value === true;
    }
    if (!ready) {
        chrome.kill();
        throw new Error(`Harness never became ready.${errors.length ? '\n  ' + errors.join('\n  ') : ''}`);
    }

    const shots = ONLY ? SHOTS.filter((s) => s.name.includes(ONLY)) : SHOTS;
    let total = 0;
    console.log(`\n  Rendering ${shots.length} still(s) -> ${path.relative(process.cwd(), OUT)}\n`);

    for (const s of shots) {
        const spec = JSON.stringify({ ...s, quality: QUALITY });
        const r = await cdp.send('Runtime.evaluate', {
            expression: `window.__shoot(${spec})`,
            returnByValue: true, awaitPromise: false,
        });
        const url = r.result.value;
        if (typeof url !== 'string' || !url.startsWith('data:image/webp')) {
            console.error(`  FAIL  ${s.name}  — canvas returned ${String(url).slice(0, 40)}`);
            continue;
        }
        const buf = Buffer.from(url.split(',')[1], 'base64');
        await writeFile(path.join(OUT, `${s.name}.webp`), buf);
        total += buf.length;
        const px = `${Math.round(s.w * s.dpr)}x${Math.round(s.h * s.dpr)}`;
        console.log(`  ok    ${s.name.padEnd(18)} ${px.padStart(11)}  ${(buf.length / 1024).toFixed(1).padStart(7)} KB`);
    }

    console.log(`\n  total ${(total / 1024).toFixed(1)} KB across ${shots.length} file(s)\n`);
    if (errors.length) console.log('  page errors:\n    ' + errors.join('\n    ') + '\n');

    ws.close();
    if (!has('--keep')) chrome.kill();
}

main().catch((e) => { console.error('\n  ' + e.message + '\n'); process.exit(1); });
