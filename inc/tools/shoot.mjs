/**
 * shoot.mjs — render the site through the Chrome DevTools Protocol and report
 * what is actually on screen.
 *
 * This exists because the previous rebuilds were designed blind: CSS was read
 * and behaviour inferred, and three consecutive directions were rejected on
 * sight. It also replaces an earlier screenshot script that used
 * `chrome --headless --window-size=390,1600`, which does NOT emulate a phone —
 * <meta viewport> is never applied, so the page renders at desktop width and
 * squashes. That produced a confident, wrong "mobile is broken" report.
 * Emulation.setDeviceMetricsOverride is the difference.
 *
 * Usage:
 *   node inc/tools/shoot.mjs [--base http://127.0.0.1:8877] [--out DIR]
 *                            [--only /path,/other] [--no-shots]
 *
 * Output: one PNG per route per viewport, plus a table of overflow and
 * contrast findings. Exits non-zero if any hard failure is found, so it can
 * gate a commit.
 *
 * No dependencies — Node's built-in WebSocket drives CDP directly.
 */

import { spawn } from 'node:child_process';
import { mkdir, writeFile, rm } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import os from 'node:os';

/* ------------------------------------------------------------------ config */

const args = process.argv.slice(2);
const arg = (name, fallback) => {
    const i = args.indexOf(name);
    return i >= 0 && args[i + 1] ? args[i + 1] : fallback;
};
const has = (name) => args.includes(name);

const BASE = arg('--base', 'http://127.0.0.1:8877').replace(/\/$/, '');
const OUT = arg('--out', path.join(os.tmpdir(), 'rafly-shots'));
const SHOTS = !has('--no-shots');

const ROUTES = (arg('--only', '') || [
    '/',
    '/about',
    '/team',
    '/blog',
    '/pricing',
    '/case-studies',
    '/contact',
    '/privacy',
    '/web-development',
    '/web-security',
    '/marketing-advertisement',
    '/content-creation',
    '/ecommerce-support',
    '/thank-you',
    '/does-not-exist',
].join(',')).split(',').filter(Boolean);

const VIEWPORTS = [
    { name: 'desktop', width: 1440, height: 900, dpr: 1, mobile: false },
    { name: 'phone', width: 390, height: 844, dpr: 2, mobile: true },
];

const CHROME = [
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
    '/usr/bin/google-chrome',
    '/usr/bin/chromium',
].find((p) => existsSync(p));

if (!CHROME) {
    console.error('No Chrome binary found.');
    process.exit(2);
}

/* --------------------------------------------------------------- CDP client */

class CDP {
    constructor(ws) {
        this.ws = ws;
        this.id = 0;
        this.pending = new Map();
        this.pendingMethod = new Map();
        this.listeners = new Map();
        ws.addEventListener('message', (event) => {
            const msg = JSON.parse(event.data);
            if (msg.id && this.pending.has(msg.id)) {
                const { resolve, reject } = this.pending.get(msg.id);
                this.pending.delete(msg.id);
                msg.error ? reject(new Error(`${msg.error.message} (${this.pendingMethod.get(msg.id)})`)) : resolve(msg.result);
            } else if (msg.method) {
                (this.listeners.get(msg.method) || []).forEach((fn) => fn(msg.params));
            }
        });
    }

    send(method, params = {}) {
        const id = ++this.id;
        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.pendingMethod.set(id, method);
            this.ws.send(JSON.stringify({ id, method, params }));
            setTimeout(() => {
                if (this.pending.has(id)) {
                    this.pending.delete(id);
                    reject(new Error(`CDP timeout: ${method}`));
                }
            }, 30000);
        });
    }

    on(method, fn) {
        if (!this.listeners.has(method)) this.listeners.set(method, []);
        this.listeners.get(method).push(fn);
    }

    once(method) {
        return new Promise((resolve) => {
            const fn = (params) => {
                const list = this.listeners.get(method);
                list.splice(list.indexOf(fn), 1);
                resolve(params);
            };
            this.on(method, fn);
        });
    }
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

/* ------------------------------------------------- the in-page measurements */

/**
 * Runs inside the page. Returns everything worth asserting on, so one round
 * trip covers layout, contrast and render errors.
 */
const PROBE = `(() => {
    const doc = document.documentElement;

    // 1. Horizontal overflow. This is the bug that actually shipped last time.
    const pageOverflow = doc.scrollWidth - doc.clientWidth;

    // 2. Which elements stick out past the viewport.
    //    Excluded: fixed/sticky chrome (a nav pill is not page content), and
    //    anything inside an ancestor that clips or scrolls horizontally — a
    //    marquee track is twice its container's width BY DESIGN, and reporting
    //    it makes the real findings impossible to see.
    const clipped = (el) => {
        let node = el.parentElement;
        while (node && node !== document.documentElement) {
            const ox = getComputedStyle(node).overflowX;
            if (ox === 'hidden' || ox === 'clip' || ox === 'auto' || ox === 'scroll') return true;
            node = node.parentElement;
        }
        return false;
    };

    const wide = [];
    const vw = doc.clientWidth;
    for (const el of document.body.querySelectorAll('*')) {
        const style = getComputedStyle(el);
        if (style.position === 'fixed' || style.position === 'sticky') continue;
        if (style.visibility === 'hidden' || style.display === 'none') continue;
        const rect = el.getBoundingClientRect();
        if (rect.width === 0) continue;
        if (clipped(el)) continue;
        if (rect.right > vw + 1 || rect.left < -1) {
            wide.push({
                sel: el.tagName.toLowerCase() + (el.className && typeof el.className === 'string'
                    ? '.' + el.className.trim().split(/\\s+/).slice(0, 2).join('.') : ''),
                left: Math.round(rect.left),
                right: Math.round(rect.right),
            });
        }
        if (wide.length > 12) break;
    }

    // 3. Contrast. Measured on the elements that actually render text, against
    //    the nearest ancestor that paints a background — eyeballing a token
    //    table is how #ff6b35 ends up as 3:1 body copy.
    const srgb = (c) => { c /= 255; return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4); };
    const lum = ([r, g, b]) => 0.2126 * srgb(r) + 0.7152 * srgb(g) + 0.0722 * srgb(b);
    /* TWO COMPUTED COLOUR FORMATS, AND THEY USE DIFFERENT SCALES.

       A plain declaration computes to rgb(5, 15, 51) — channels 0-255. But
       anything built with color-mix() computes to color(srgb 1 1 1 / 0.76) —
       channels 0-1. Reading the second with the first scale turns white into
       rgb(1, 1, 1), which is black.

       That is not hypothetical: it reported a white glass panel as near-black
       and failed three real elements on it at 1.12:1, 2.19:1 and 3.19:1 — a
       heading, a paragraph and an eyebrow that are all comfortably legible.
       An hour was spent looking for the CSS bug before the harness turned out
       to be the thing that was wrong. color-mix is now used throughout
       css/09-scenes.css, so this would have kept happening. */
    const parse = (v) => {
        const n = (String(v).match(/[\\d.]+/g) || []).slice(0, 4).map(Number);
        if (/^color\\(/.test(String(v).trim())) {
            return [n[0] * 255, n[1] * 255, n[2] * 255, n[3] ?? 1];
        }
        return n;
    };
    const ratio = (fg, bg) => {
        const a = lum(fg), b = lum(bg);
        return (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
    };
    /* The paper grain. It is an inline SVG noise tile at 0.038-0.10 opacity,
       and it is on the page ground, every panel and every large heading — so
       treating a "has a background-image" as "unmeasurable" made the harness
       give up on most of the site. It shifts luminance by well under a
       hundredth of a ratio point. Skip past it and measure the colour under
       it. Any OTHER image or gradient is still unmeasurable, which is the
       case this rule was written for. */
    const GRAIN = /^url\\("?data:image\\/svg\\+xml/;

    /* Returns the effective background, or null when it cannot be known.
       A gradient or a photograph behind the text means the real ratio depends
       on WHERE the text sits over it, which computed style cannot answer — so
       the honest result is "unverifiable", not "fail". Reporting a scrimmed
       label as a failure would train me to ignore this list.

       skipSelf is for background-clip:text, where the element's own
       background IS the letterform and the ground is its parent's. */
    const bgOf = (el, skipSelf) => {
        let node = skipSelf ? el.parentElement : el;
        while (node && node !== document.documentElement) {
            const style = getComputedStyle(node);
            const img = style.backgroundImage;
            if (img && img !== 'none' && !GRAIN.test(img)) return null;
            const c = parse(style.backgroundColor);
            if (c.length === 3 || (c[3] ?? 1) > 0.6) return c.slice(0, 3);
            node = node.parentElement;
        }
        return [255, 255, 255];
    };

    /* background-clip:text throws the 'color' property away and paints the
       letterforms with the background instead. Computed 'color' then reads
       rgba(0, 0, 0, 0), so measuring it naively reports black-on-anything —
       a SILENT PASS on the largest type on the page, which is worse than not
       checking. Every grained heading on this site hits that path.

       When all the colour stops in that background are the same value, the
       text has exactly one real colour and it is measurable. Pull it out and
       measure THAT. When they differ it is a genuine gradient and stays
       honestly unverifiable. */
    const clippedColor = (style) => {
        const clip = style.webkitBackgroundClip || style.backgroundClip;
        if (clip !== 'text') return null;
        const stops = (style.backgroundImage || '').match(/rgba?\\([^)]*\\)/g);
        if (!stops || !stops.length) return null;
        return stops.every((s) => s === stops[0]) ? parse(stops[0]).slice(0, 3) : null;
    };

    const contrast = [];
    const seen = new Set();
    let unverifiable = 0;
    for (const el of document.body.querySelectorAll('p, a, h1, h2, h3, h4, span, li, label, button, td, th, figcaption')) {
        const text = (el.textContent || '').trim();
        if (!text || el.children.length > 0) continue;
        const style = getComputedStyle(el);
        if (style.visibility === 'hidden' || style.display === 'none' || +style.opacity < 0.5) continue;
        const rect = el.getBoundingClientRect();
        if (rect.width < 4 || rect.height < 4) continue;

        const size = parseFloat(style.fontSize);
        const weight = +style.fontWeight || 400;
        const large = size >= 24 || (size >= 18.66 && weight >= 700);
        const need = large ? 3 : 4.5;

        const clipped = clippedColor(style);
        const bg = bgOf(el, clipped !== null);
        if (bg === null) { unverifiable++; continue; }

        const fg = clipped || parse(style.color).slice(0, 3);
        const r = ratio(fg, bg);
        if (r >= need) continue;

        const key = style.color + '|' + el.className + '|' + Math.round(size);
        if (seen.has(key)) continue;
        seen.add(key);

        contrast.push({
            sel: el.tagName.toLowerCase() + (typeof el.className === 'string' && el.className
                ? '.' + el.className.trim().split(/\\s+/)[0] : ''),
            text: text.slice(0, 40),
            color: style.color,
            size: Math.round(size),
            ratio: Math.round(r * 100) / 100,
            need,
        });
        if (contrast.length > 14) break;
    }

    // 4. Anything a template got wrong that still returned a 200.
    const body = document.body.innerText || '';
    const phpError = /(Fatal error|Parse error|Warning:|Notice:|Deprecated:|Uncaught \\w*Error)/.test(body);

    // 5. Images that failed to load.
    const brokenImages = [...document.images]
        .filter((img) => img.complete && img.naturalWidth === 0 && img.currentSrc)
        .map((img) => img.currentSrc.replace(location.origin, ''))
        .slice(0, 8);

    return {
        title: document.title,
        pageOverflow,
        scrollHeight: doc.scrollHeight,
        wide,
        contrast,
        unverifiable,
        phpError,
        brokenImages,
        h1Count: document.querySelectorAll('h1').length,
    };
})()`;

/* ------------------------------------------------------------------ runner */

async function main() {
    await mkdir(OUT, { recursive: true });

    const profile = path.join(os.tmpdir(), 'rafly-chrome-profile');
    await rm(profile, { recursive: true, force: true });

    const chrome = spawn(CHROME, [
        '--headless=new',
        '--remote-debugging-port=9333',
        '--disable-gpu',
        '--hide-scrollbars',
        '--no-first-run',
        '--no-default-browser-check',
        '--disable-extensions',
        '--force-color-profile=srgb',
        `--user-data-dir=${profile}`,
        'about:blank',
    ], { stdio: 'ignore' });

    // Wait for the debugging endpoint rather than sleeping a fixed amount.
    let version = null;
    for (let i = 0; i < 60 && !version; i++) {
        try {
            version = await (await fetch('http://127.0.0.1:9333/json/version')).json();
        } catch { await sleep(250); }
    }
    if (!version) { chrome.kill(); throw new Error('Chrome did not expose a debugging port'); }

    const target = await (await fetch('http://127.0.0.1:9333/json/new?about:blank', { method: 'PUT' })).json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((resolve, reject) => {
        ws.addEventListener('open', resolve, { once: true });
        ws.addEventListener('error', reject, { once: true });
    });

    const cdp = new CDP(ws);
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');
    await cdp.send('Console.enable');

    const consoleErrors = [];
    cdp.on('Runtime.exceptionThrown', (p) => {
        consoleErrors.push(p.exceptionDetails?.exception?.description || p.exceptionDetails?.text || 'exception');
    });
    cdp.on('Runtime.consoleAPICalled', (p) => {
        if (p.type === 'error') {
            consoleErrors.push(p.args.map((a) => a.value ?? a.description ?? '').join(' '));
        }
    });

    const report = [];
    let failures = 0;

    for (const vp of VIEWPORTS) {
        await cdp.send('Emulation.setDeviceMetricsOverride', {
            width: vp.width,
            height: vp.height,
            deviceScaleFactor: vp.dpr,
            mobile: vp.mobile,
            screenWidth: vp.width,
            screenHeight: vp.height,
        });
        if (vp.mobile) {
            await cdp.send('Emulation.setTouchEmulationEnabled', { enabled: true, maxTouchPoints: 5 });
            await cdp.send('Emulation.setUserAgentOverride', {
                userAgent: 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Mobile Safari/537.36',
            });
        } else {
            await cdp.send('Emulation.setTouchEmulationEnabled', { enabled: false });
            await cdp.send('Emulation.setUserAgentOverride', { userAgent: version['User-Agent'] });
        }

        for (const route of ROUTES) {
            consoleErrors.length = 0;
            const url = BASE + route;

            const loaded = cdp.once('Page.loadEventFired');
            const nav = await cdp.send('Page.navigate', { url });
            await Promise.race([loaded, sleep(15000)]);
            await sleep(500);   // fonts, then one settle frame

            // Scroll the whole page so every lazy image and reveal has fired,
            // then return to the top before the screenshot.
            await cdp.send('Runtime.evaluate', {
                expression: `(async () => {
                    const step = innerHeight * 0.9;
                    for (let y = 0; y < document.documentElement.scrollHeight; y += step) {
                        scrollTo(0, y); await new Promise(r => setTimeout(r, 60));
                    }
                    scrollTo(0, 0); await new Promise(r => setTimeout(r, 250));
                })()`,
                awaitPromise: true,
            });

            /* Force every lazy image to load, and wait until they have decoded.

               captureBeyondViewport renders the whole document, but it does NOT
               make the browser fetch an image that loading="lazy" has never
               brought near the viewport. The scroll pass above triggers most of
               them; anything that was still in flight, or that the scroll
               stepped straight past, photographs as an empty frame. That is
               indistinguishable from a broken layout in a screenshot, and it
               silently exempted every below-the-fold image from the broken-image
               check — the harness was reviewing a page nobody will ever see. */
            await cdp.send('Runtime.evaluate', {
                expression: `(async () => {
                    document.querySelectorAll('img[loading="lazy"]').forEach(i => { i.loading = 'eager'; });
                    const imgs = Array.from(document.images);
                    await Promise.all(imgs.map(i => (i.decode ? i.decode() : Promise.resolve()).catch(() => {})));
                })()`,
                awaitPromise: true,
            });
            await sleep(250);

            /* Freeze every entrance animation at its FINISHED state before
               measuring or shooting.

               captureBeyondViewport renders the whole document, but a
               scroll-driven animation resolves against the real scroll
               position — so everything below the fold sits at the START of its
               range and photographs as blank. Freezing also matters for the
               probe: an element at opacity 0 is skipped by the contrast check,
               so without this most of the page is never measured at all. */
            await cdp.send('Runtime.evaluate', {
                expression: `(() => {
                    const s = document.createElement('style');
                    s.textContent = '[data-r],[data-r] > *,[data-fx],.arc-tile,.split-word,.st-step{animation:none!important;transition:none!important;opacity:1!important;transform:none!important;filter:none!important;clip-path:none!important}';
                    document.head.appendChild(s);
                })()`,
            });
            await sleep(120);

            const probe = await cdp.send('Runtime.evaluate', { expression: PROBE, returnByValue: true });
            const result = probe.result.value || {};
            const status = nav.errorText ? 0 : 200;

            const slug = route === '/' ? 'home' : route.replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '');

            if (SHOTS) {
                const shot = await cdp.send('Page.captureScreenshot', {
                    format: 'png',
                    captureBeyondViewport: true,
                    optimizeForSpeed: false,
                });
                await writeFile(path.join(OUT, `${slug}.${vp.name}.png`), Buffer.from(shot.data, 'base64'));
            }

            const problems = [];
            if (result.phpError) problems.push('PHP ERROR IN BODY');
            if (result.pageOverflow > 1) problems.push(`H-OVERFLOW ${result.pageOverflow}px`);
            if (result.wide?.length) problems.push(`${result.wide.length} element(s) past the edge`);
            if (result.contrast?.length) problems.push(`${result.contrast.length} contrast fail(s)`);
            if (result.brokenImages?.length) problems.push(`${result.brokenImages.length} broken image(s)`);
            if (result.h1Count !== 1) problems.push(`h1 count = ${result.h1Count}`);
            if (consoleErrors.length) problems.push(`${consoleErrors.length} console error(s)`);

            if (problems.length) failures++;

            report.push({
                route, viewport: vp.name, status,
                height: result.scrollHeight,
                title: result.title,
                problems,
                detail: {
                    wide: result.wide,
                    contrast: result.contrast,
                    brokenImages: result.brokenImages,
                    consoleErrors: consoleErrors.slice(0, 6),
                },
            });

            const mark = problems.length ? 'FAIL' : ' ok ';
            const note = result.unverifiable ? `  (${result.unverifiable} on a gradient, not checkable)` : '';
            console.log(`[${mark}] ${vp.name.padEnd(7)} ${route.padEnd(26)} ${String(result.scrollHeight).padStart(6)}px  ${problems.join(' | ')}${note}`);
        }
    }

    /* ------------------------------------------------------------ detail */
    const withDetail = report.filter((r) => r.problems.length);
    if (withDetail.length) {
        console.log('\n--- detail ---');
        for (const r of withDetail) {
            console.log(`\n${r.viewport} ${r.route}`);
            r.detail.wide?.forEach((w) => console.log(`   overflow  ${w.sel}  left=${w.left} right=${w.right}`));
            r.detail.contrast?.forEach((c) => console.log(`   contrast  ${c.ratio}:1 (needs ${c.need}) ${c.sel} ${c.size}px ${c.color} — "${c.text}"`));
            r.detail.brokenImages?.forEach((i) => console.log(`   image404  ${i}`));
            r.detail.consoleErrors?.forEach((e) => console.log(`   console   ${String(e).slice(0, 160)}`));
        }
    }

    await writeFile(path.join(OUT, 'report.json'), JSON.stringify(report, null, 2));
    console.log(`\n${report.length} renders, ${failures} with problems. Output: ${OUT}`);

    ws.close();
    chrome.kill();
    process.exit(failures ? 1 : 0);
}

main().catch((err) => {
    console.error(err);
    process.exit(2);
});
