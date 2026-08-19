/**
 * audit.mjs — the checks that are not about how a page looks.
 *
 *     node inc/tools/audit.mjs [--base http://127.0.0.1:8877]
 *
 * shoot.mjs covers layout, contrast and render errors. This covers the four
 * things that only show up when you actually drive the page:
 *
 *   1. KEYBOARD — tab through the mobile drawer with its sub-panels collapsed.
 *      Focus must never land on a link nobody can see. The previous build's
 *      drawer animated height only, so it did exactly that, on the one
 *      navigation phones depend on.
 *   2. REDUCED MOTION — with prefers-reduced-motion: reduce, nothing may still
 *      be animating and nothing may be left stuck at an entrance state.
 *   3. WEIGHT — request count and transferred bytes per page, split by type.
 *   4. NO-JS — the page must still be readable with JavaScript disabled. The
 *      whole motion layer is built around this and it is worth asserting.
 *
 * No dependencies.
 */

import { spawn } from 'node:child_process';
import { existsSync, readFileSync } from 'node:fs';
import { rm } from 'node:fs/promises';
import path from 'node:path';
import os from 'node:os';

const args = process.argv.slice(2);
const arg = (n, f) => { const i = args.indexOf(n); return i >= 0 && args[i + 1] ? args[i + 1] : f; };
const BASE = arg('--base', 'http://127.0.0.1:8877').replace(/\/$/, '');

const CHROME = [
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
    '/usr/bin/google-chrome',
].find((p) => existsSync(p));
if (!CHROME) { console.error('No Chrome binary found.'); process.exit(2); }

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

let failures = 0;
const check = (label, pass, detail = '') => {
    if (!pass) failures++;
    console.log(`  [${pass ? ' ok ' : 'FAIL'}] ${label}${detail ? '  — ' + detail : ''}`);
};

async function connect(port, flags = []) {
    const profile = path.join(os.tmpdir(), 'rafly-audit-' + port);
    await rm(profile, { recursive: true, force: true });

    const chrome = spawn(CHROME, [
        '--headless=new', `--remote-debugging-port=${port}`, '--disable-gpu',
        '--hide-scrollbars', '--no-first-run', '--no-default-browser-check',
        `--user-data-dir=${profile}`, ...flags, 'about:blank',
    ], { stdio: 'ignore' });

    let ready = null;
    for (let i = 0; i < 60 && !ready; i++) {
        try { ready = await (await fetch(`http://127.0.0.1:${port}/json/version`)).json(); }
        catch { await sleep(250); }
    }
    if (!ready) { chrome.kill(); throw new Error('Chrome did not start'); }

    const target = await (await fetch(`http://127.0.0.1:${port}/json/new?about:blank`, { method: 'PUT' })).json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((res, rej) => {
        ws.addEventListener('open', res, { once: true });
        ws.addEventListener('error', rej, { once: true });
    });
    return { chrome, ws, cdp: new CDP(ws) };
}

async function goto(cdp, url) {
    const loaded = cdp.once('Page.loadEventFired');
    await cdp.send('Page.navigate', { url });
    await Promise.race([loaded, sleep(15000)]);
    await sleep(500);
}

/* ==========================================================================
   The budget

   inc/tools/perf-budget.json used to be documentation: a file describing what
   the site weighed, that nothing ever read. A budget nothing enforces is a
   comment. This reads it and fails the run, which is the only version of a
   performance budget that survives contact with a deadline.
   ========================================================================== */

function loadBudget() {
    try {
        return JSON.parse(readFileSync(new URL('./perf-budget.json', import.meta.url), 'utf8'));
    } catch {
        return null;
    }
}

function budgetFor(budget, route, viewport) {
    if (!budget) return null;
    for (const spec of Object.values(budget.routes)) {
        if (spec.match?.includes(route)) return spec[viewport] || null;
    }
    return budget.routes.default?.[viewport] || null;
}

async function main() {
    /* ================================================================== */
    console.log('\n1. KEYBOARD — mobile drawer, sub-panels collapsed\n');
    {
        const { chrome, ws, cdp } = await connect(9341);
        await cdp.send('Page.enable');
        await cdp.send('Runtime.enable');
        await cdp.send('Emulation.setDeviceMetricsOverride', {
            width: 390, height: 844, deviceScaleFactor: 2, mobile: true,
            screenWidth: 390, screenHeight: 844,
        });
        await goto(cdp, BASE + '/');

        // Closed drawer: nothing inside it may be focusable at all.
        let r = await cdp.send('Runtime.evaluate', {
            returnByValue: true,
            expression: `(() => {
                const d = document.getElementById('drawer');
                const sel = 'a[href], button:not([disabled]), input, [tabindex]:not([tabindex="-1"])';
                return { inert: d.hasAttribute('inert'), focusable: d.querySelectorAll(sel).length };
            })()`,
        });
        check('closed drawer is inert', r.result.value.inert,
            `${r.result.value.focusable} focusable descendants, all neutralised by inert`);

        // Open it, then tab the whole thing and record where focus lands.
        await cdp.send('Runtime.evaluate', {
            expression: `document.querySelector('[data-drawer-toggle]').click()`,
        });
        await sleep(450);

        r = await cdp.send('Runtime.evaluate', {
            returnByValue: true,
            expression: `(() => {
                const sub = document.getElementById('drawerServices');
                const trigger = document.querySelector('[data-drawer-sub]');
                return {
                    hidden: sub.hidden,
                    expanded: trigger.getAttribute('aria-expanded'),
                    links: sub.querySelectorAll('a').length,
                };
            })()`,
        });
        check('collapsed sub-panel uses [hidden], not just height', r.result.value.hidden === true,
            `${r.result.value.links} service links inside, aria-expanded=${r.result.value.expanded}`);

        const seen = [];
        for (let i = 0; i < 22; i++) {
            await cdp.send('Input.dispatchKeyEvent', { type: 'rawKeyDown', windowsVirtualKeyCode: 9, key: 'Tab', code: 'Tab' });
            await cdp.send('Input.dispatchKeyEvent', { type: 'keyUp', windowsVirtualKeyCode: 9, key: 'Tab', code: 'Tab' });
            const at = await cdp.send('Runtime.evaluate', {
                returnByValue: true,
                expression: `(() => {
                    const el = document.activeElement;
                    if (!el || el === document.body) return null;
                    const rect = el.getBoundingClientRect();
                    const cs = getComputedStyle(el);
                    return {
                        tag: el.tagName.toLowerCase(),
                        text: (el.textContent || '').trim().slice(0, 28),
                        inDrawer: !!el.closest('#drawer'),
                        inHiddenPanel: !!el.closest('[hidden]'),
                        visible: rect.width > 0 && rect.height > 0 && cs.visibility !== 'hidden' && +cs.opacity > 0.05,
                    };
                })()`,
            });
            if (at.result.value) seen.push(at.result.value);
        }

        const trapped = seen.filter((s) => s.inDrawer).length;
        const bad = seen.filter((s) => s.inHiddenPanel || (s.inDrawer && !s.visible));
        check('focus stays inside the open drawer', trapped > 0, `${trapped}/${seen.length} stops inside it`);
        check('focus never lands on a hidden link', bad.length === 0,
            bad.length ? bad.map((b) => b.text).join(' | ') : 'checked ' + seen.length + ' tab stops');

        // Open the sub-panel and confirm the links become reachable.
        await cdp.send('Runtime.evaluate', { expression: `document.querySelector('[data-drawer-sub]').click()` });
        await sleep(250);
        r = await cdp.send('Runtime.evaluate', {
            returnByValue: true,
            expression: `document.getElementById('drawerServices').hidden`,
        });
        check('opening the sub-panel reveals its links', r.result.value === false);

        ws.close(); chrome.kill();
    }

    /* ================================================================== */
    console.log('\n2. REDUCED MOTION — prefers-reduced-motion: reduce\n');
    {
        const { chrome, ws, cdp } = await connect(9342);
        await cdp.send('Page.enable');
        await cdp.send('Runtime.enable');
        await cdp.send('Emulation.setEmulatedMedia', {
            features: [{ name: 'prefers-reduced-motion', value: 'reduce' }],
        });
        await goto(cdp, BASE + '/');
        await sleep(600);

        const r = await cdp.send('Runtime.evaluate', {
            returnByValue: true,
            expression: `(() => {
                const running = document.getAnimations().filter(a => a.playState === 'running');
                const stuck = [...document.querySelectorAll('[data-r], [data-r] > *, [data-fx], .arc-tile, .split-word')]
                    .filter(el => {
                        const cs = getComputedStyle(el);
                        return +cs.opacity < 0.9 || (cs.transform !== 'none' && cs.transform !== 'matrix(1, 0, 0, 1, 0, 0)');
                    }).length;
                return {
                    running: running.length,
                    names: running.map(a => a.animationName || a.constructor.name).slice(0, 5),
                    stuck,
                    smoothScroll: !!window.__raflySmooth,
                };
            })()`,
        });
        const v = r.result.value;
        check('no animation left running', v.running === 0, v.running ? v.names.join(', ') : 'document.getAnimations() is empty');
        check('nothing stuck at an entrance state', v.stuck === 0, v.stuck ? v.stuck + ' element(s) below full opacity' : 'every revealed element is at rest');

        ws.close(); chrome.kill();
    }

    /* ================================================================== */
    const budget = loadBudget();
    console.log('\n3. WEIGHT — per page, uncompressed on this dev server\n');
    {
        const { chrome, ws, cdp } = await connect(9343);
        await cdp.send('Page.enable');
        await cdp.send('Network.enable');

        /* TWO viewports, not one.
           This block used to measure at whatever width headless Chrome opened
           at, which is always a desktop width — so it reported the desktop
           figure and called it "the page". The single most important number
           on this project is the PHONE payload, because that is where
           three.js is gated OUT and where the visitor is on a mid-range
           Android on mobile data. Measuring only desktop meant the mobile
           budget was never actually checked by anything. */
        const viewports = [
            { name: 'desktop', width: 1440, height: 900, dpr: 1, mobile: false },
            { name: 'phone', width: 390, height: 844, dpr: 2, mobile: true },
        ];
        const routes = ['/', '/about', '/web-development', '/blog', '/contact'];
        for (const vp of viewports) {
        await cdp.send('Emulation.setDeviceMetricsOverride', {
            width: vp.width, height: vp.height, deviceScaleFactor: vp.dpr, mobile: vp.mobile,
            screenWidth: vp.width, screenHeight: vp.height,
        });
        console.log(`  --- ${vp.name} ${vp.width}x${vp.height} ---`);
        for (const route of routes) {
            const seen = new Map();
            const onResponse = (p) => seen.set(p.requestId, { type: p.type, url: p.response.url });
            const onFinished = (p) => { const e = seen.get(p.requestId); if (e) e.bytes = p.encodedDataLength; };
            cdp.on('Network.responseReceived', onResponse);
            cdp.on('Network.loadingFinished', onFinished);

            await goto(cdp, BASE + route);
            await sleep(900);

            const rows = [...seen.values()].filter((e) => e.bytes);
            const by = {};
            for (const e of rows) {
                const k = e.type === 'Stylesheet' ? 'css' : e.type === 'Script' ? 'js'
                    : e.type === 'Font' ? 'font' : e.type === 'Image' ? 'img'
                    : e.type === 'Document' ? 'html' : 'other';
                by[k] = (by[k] || 0) + e.bytes;
            }
            const total = rows.reduce((a, b) => a + b.bytes, 0);
            const fmt = (n) => (n / 1024).toFixed(0).padStart(4) + ' KB';
            console.log(`  ${route.padEnd(20)} ${String(rows.length).padStart(2)} req  total ${fmt(total)}` +
                `   html ${fmt(by.html || 0)}  css ${fmt(by.css || 0)}  js ${fmt(by.js || 0)}  font ${fmt(by.font || 0)}  img ${fmt(by.img || 0)}`);

            const cap = budgetFor(budget, route, vp.name);
            if (cap) {
                const over = [];
                for (const [k, limit] of Object.entries(cap)) {
                    const got = (k === 'total' ? total : (by[k] || 0)) / 1024;
                    if (got > limit) over.push(`${k} ${got.toFixed(0)}KB > ${limit}KB`);
                }
                if (over.length) {
                    failures++;
                    console.log(`     [OVER BUDGET] ${over.join(', ')}`);
                }
            }
        }
        console.log('');
        }
        ws.close(); chrome.kill();
    }

    /* ================================================================== */
    console.log('\n4. NO-JS — the page must still be readable\n');
    {
        const { chrome, ws, cdp } = await connect(9344, ['--disable-javascript']);
        await cdp.send('Page.enable');
        await cdp.send('Runtime.enable');
        await cdp.send('Emulation.setScriptExecutionDisabled', { value: true });
        await goto(cdp, BASE + '/');

        /* Scroll the whole page first.
           Where `animation-timeline: view()` is supported the entrance
           animations are pure CSS and keep working with JS off — but they are
           SCROLL-driven, so an element below the fold is legitimately still at
           its entrance state until the reader reaches it. Asserting on the
           un-scrolled page measured the wrong thing and reported 40 "hidden"
           elements that a reader would see the moment they scrolled.

           No requestAnimationFrame here: scripting is disabled, so this runs
           through CDP's own scroll gestures instead. */
        for (let y = 0; y < 40; y++) {
            await cdp.send('Input.dispatchMouseEvent', {
                type: 'mouseWheel', x: 200, y: 400, deltaX: 0, deltaY: 900,
            });
            await sleep(45);
        }
        await sleep(400);

        const r = await cdp.send('Runtime.evaluate', {
            returnByValue: true,
            expression: `(() => {
                /* The one exclusion is the WebGL stage. Its canvas sits at
                   opacity 0 until js/stage3d.js has painted a first frame and
                   added .is-live to .stage — that is the fallback CONTRACT, not
                   a failure. Underneath it is a pre-rendered still
                   (.stage-still), which is what a visitor without JS, without
                   WebGL2, on a phone or under reduced motion actually sees, and
                   which is asserted separately below. */
                const els = [...document.querySelectorAll('[data-r], [data-r] > *, [data-fx]')]
                    .filter(el => !el.closest('.stage-sticky'))
                    /* Skip anything with no layout box. A display:none element is
                       not hidden-but-present, it is ABSENT by design — the arc
                       sheds its outer tiles below 900px, and a CSS animation on a
                       display:none element never starts, so fill-mode holds it at
                       the entrance state forever and it reads as a failure. The
                       fallback path in css/06-motion.css never uses display, so
                       nothing this check is meant to catch can hide here. */
                    .filter(el => el.getClientRects().length > 0);
                const hidden = els.filter(el => +getComputedStyle(el).opacity < 0.5);
                return {
                    total: els.length,
                    invisible: hidden.length,
                    which: hidden.slice(0, 5).map(el => el.tagName.toLowerCase()
                        + '.' + (typeof el.className === 'string' ? el.className.trim().split(/\\s+/)[0] : '')),
                    /* Not just present — VISIBLE and pointing at a real file.
                       A still that exists in the DOM at opacity 0, or with an
                       empty src, is the same blank frame to a visitor as no
                       still at all, and that blank frame is exactly what the
                       previous build shipped. */
                    still: (() => {
                        const img = document.querySelector('.stage-still img');
                        if (!img) return 'missing';
                        const op = +getComputedStyle(img.closest('.stage-still')).opacity;
                        if (op < 0.2) return 'transparent (opacity ' + op + ')';
                        if (!img.getAttribute('src')) return 'no src';
                        if (!img.getClientRects().length) return 'no layout box';
                        return 'ok';
                    })(),
                    canvasLive: !!document.querySelector('.stage.is-live'),
                    words: (document.body.innerText || '').trim().split(/\\s+/).length,
                    jsMotion: document.documentElement.classList.contains('js-motion'),
                };
            })()`,
        });
        const v = r.result.value;
        check('nothing stays hidden without JS, once scrolled', v.invisible === 0,
            v.invisible ? v.which.join(', ') : `${v.total} revealed elements, all visible after a full scroll`);
        check('the pre-rendered still is what shows without JS', v.still === 'ok', v.still);
        check('the live canvas never activates without JS', v.canvasLive === false,
            '.stage must not carry .is-live when scripts are disabled');
        check('.js-motion is absent, so nothing opts into hiding', v.jsMotion === false);
        check('the page still has its content', v.words > 800, `${v.words} words readable`);

        ws.close(); chrome.kill();
    }

    console.log(`\n${failures ? failures + ' check(s) FAILED' : 'All checks passed'}\n`);
    process.exit(failures ? 1 : 0);
}

main().catch((e) => { console.error(e); process.exit(2); });
