/**
 * capture-mockups.mjs — fills the hero's device screens with the real site.
 *
 *     node inc/tools/capture-mockups.mjs [--base http://127.0.0.1:8877]
 *
 * The device frames in the hero are drawn in CSS (css/07-fx.css). What goes
 * INSIDE them is a genuine screenshot of this site, captured here at exactly
 * the aspect ratio the frame expects.
 *
 * That choice is deliberate. The obvious alternative is a licensed stock
 * mockup or an invented app UI, and both are worse: one costs a licence and
 * shows somebody else's product, the other is a picture of software that does
 * not exist. A screenshot of the actual pages is free, honest, exactly on
 * brand by construction, and it updates the day the design does.
 *
 * The captured pages are NOT the homepage — that would put the hero inside its
 * own mockup, recursively. The laptop shows /pricing and the phone shows
 * /blog, which also means the hero quietly advertises two more pages.
 *
 * No dependencies: Node's built-in WebSocket drives the DevTools Protocol.
 */

import { spawn } from 'node:child_process';
import { writeFile, mkdir, rm } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import os from 'node:os';

const args = process.argv.slice(2);
const arg = (n, f) => { const i = args.indexOf(n); return i >= 0 && args[i + 1] ? args[i + 1] : f; };

const BASE = arg('--base', 'http://127.0.0.1:8877').replace(/\/$/, '');
const OUT = path.resolve(import.meta.dirname, '../../assets/mockups');

const CHROME = [
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
    '/usr/bin/google-chrome',
].find((p) => existsSync(p));

if (!CHROME) { console.error('No Chrome binary found.'); process.exit(2); }

/**
 * Each capture matches the CSS aspect-ratio of its frame exactly, so the image
 * fills the screen with no crop and no letterbox:
 *   .device-laptop .device-screen  aspect-ratio: 16 / 10
 *   .device-phone  .device-screen  aspect-ratio: 9 / 19.5
 */
const SHOTS = [
    { name: 'laptop-screen', url: '/pricing', width: 1440, height: 900, dpr: 1, mobile: false },
    { name: 'phone-screen',  url: '/blog',    width: 390,  height: 845, dpr: 1, mobile: true  },
];

/* Written as WebP with a JPEG twin, which is what inc/helpers.php photo()
   expects: it emits a <picture> with the .webp as a <source> and the .jpg as
   the <img>, so a browser without WebP still gets a picture.

   Both are capped at their DISPLAYED size. The first version of this captured
   the phone at 3x device pixel ratio and produced a 4.2 MB PNG for a frame
   that renders about 200px wide — five megabytes of hero for two thumbnails. */
const FORMATS = [
    { ext: 'webp', format: 'webp', quality: 82 },
    { ext: 'jpg',  format: 'jpeg', quality: 84 },
];

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

class CDP {
    constructor(ws) {
        this.ws = ws; this.id = 0; this.pending = new Map(); this.listeners = new Map();
        ws.addEventListener('message', (e) => {
            const m = JSON.parse(e.data);
            if (m.id && this.pending.has(m.id)) {
                const { resolve, reject } = this.pending.get(m.id);
                this.pending.delete(m.id);
                m.error ? reject(new Error(m.error.message)) : resolve(m.result);
            } else if (m.method) {
                (this.listeners.get(m.method) || []).forEach((fn) => fn(m.params));
            }
        });
    }
    send(method, params = {}) {
        const id = ++this.id;
        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.ws.send(JSON.stringify({ id, method, params }));
            setTimeout(() => { if (this.pending.has(id)) { this.pending.delete(id); reject(new Error('timeout ' + method)); } }, 30000);
        });
    }
    on(method, fn) {
        if (!this.listeners.has(method)) this.listeners.set(method, []);
        this.listeners.get(method).push(fn);
    }
    once(method) {
        return new Promise((resolve) => {
            const fn = (p) => { const l = this.listeners.get(method); l.splice(l.indexOf(fn), 1); resolve(p); };
            this.on(method, fn);
        });
    }
}

async function main() {
    await mkdir(OUT, { recursive: true });

    const profile = path.join(os.tmpdir(), 'rafly-mockup-profile');
    await rm(profile, { recursive: true, force: true });

    const chrome = spawn(CHROME, [
        '--headless=new', '--remote-debugging-port=9335', '--disable-gpu',
        '--hide-scrollbars', '--no-first-run', '--no-default-browser-check',
        '--force-color-profile=srgb', `--user-data-dir=${profile}`, 'about:blank',
    ], { stdio: 'ignore' });

    let ok = null;
    for (let i = 0; i < 60 && !ok; i++) {
        try { ok = await (await fetch('http://127.0.0.1:9335/json/version')).json(); }
        catch { await sleep(250); }
    }
    if (!ok) { chrome.kill(); throw new Error('Chrome did not expose a debugging port'); }

    const target = await (await fetch('http://127.0.0.1:9335/json/new?about:blank', { method: 'PUT' })).json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((res, rej) => {
        ws.addEventListener('open', res, { once: true });
        ws.addEventListener('error', rej, { once: true });
    });

    const cdp = new CDP(ws);
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');

    for (const shot of SHOTS) {
        await cdp.send('Emulation.setDeviceMetricsOverride', {
            width: shot.width, height: shot.height,
            deviceScaleFactor: shot.dpr, mobile: shot.mobile,
            screenWidth: shot.width, screenHeight: shot.height,
        });
        await cdp.send('Emulation.setTouchEmulationEnabled', { enabled: shot.mobile, maxTouchPoints: shot.mobile ? 5 : 1 });

        const loaded = cdp.once('Page.loadEventFired');
        await cdp.send('Page.navigate', { url: BASE + shot.url });
        await Promise.race([loaded, sleep(15000)]);
        await sleep(700);

        /* Entrance animations are scroll-driven, so anything below the fold in
           the capture would photograph mid-reveal. Freeze them finished, and
           drop the floating chrome that only makes sense on a live page. */
        await cdp.send('Runtime.evaluate', {
            expression: `(() => {
                const s = document.createElement('style');
                s.textContent = \`
                    [data-r],[data-r] > *,.split-word{animation:none!important;transition:none!important;
                        opacity:1!important;transform:none!important;filter:none!important;clip-path:none!important}
                    .social-rail,.to-top,.sticky-cta,.scroll-progress,.toast-stack{display:none!important}
                    .site-header{position:absolute!important}
                \`;
                document.head.appendChild(s);
                scrollTo(0, 0);
            })()`,
        });
        await sleep(200);

        // clip, not captureBeyondViewport: exactly one screen of the page, at
        // exactly the frame's aspect ratio.
        for (const fmt of FORMATS) {
            const shotData = await cdp.send('Page.captureScreenshot', {
                format: fmt.format,
                quality: fmt.quality,
                clip: { x: 0, y: 0, width: shot.width, height: shot.height, scale: shot.dpr },
                captureBeyondViewport: false,
                optimizeForSpeed: false,
            });

            const buf = Buffer.from(shotData.data, 'base64');
            await writeFile(path.join(OUT, `${shot.name}.${fmt.ext}`), buf);
            console.log(`${(shot.name + '.' + fmt.ext).padEnd(22)} ${shot.url.padEnd(10)} ${shot.width}x${shot.height}  ${(buf.length / 1024).toFixed(0)} KB`);
        }
    }

    ws.close();
    chrome.kill();
    console.log(`\nWritten to ${OUT}`);
}

main().catch((e) => { console.error(e); process.exit(2); });
