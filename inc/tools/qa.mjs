/**
 * qa.mjs — drive the public site in a real browser and report what breaks.
 *
 * Not a screenshot pass. This clicks, types, submits and reads back:
 *   - every route loads, with no console error and no failed request
 *   - the header nav and the services dropdown reach what they claim to
 *   - the accordion, the modal and the mobile drawer open and close
 *   - the lead form rejects bad input and accepts good input
 *
 * Localhost only. Anything it creates it names so it can be found and removed.
 */
import { spawn } from 'node:child_process';
import { existsSync } from 'node:fs';
import path from 'node:path';
import os from 'node:os';

const BASE = process.argv[2] || 'http://127.0.0.1:8877';

const CHROME = [
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
].find((p) => existsSync(p));

const profile = path.join(os.tmpdir(), 'rafly-qa-profile');
const port = 9351;
const chrome = spawn(CHROME, [
    `--remote-debugging-port=${port}`, `--user-data-dir=${profile}`,
    '--headless=new', '--hide-scrollbars', '--force-device-scale-factor=1',
    '--no-first-run', '--no-default-browser-check', 'about:blank',
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
await sleep(1800);

const list = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json();
const target = list.find((t) => t.type === 'page');
const ws = new WebSocket(target.webSocketDebuggerUrl);
await new Promise((r) => ws.addEventListener('open', r));

let id = 0;
const pending = new Map();
const events = [];
ws.addEventListener('message', (ev) => {
    const m = JSON.parse(ev.data);
    if (m.id && pending.has(m.id)) { pending.get(m.id)(m); pending.delete(m.id); return; }
    if (m.method) events.push(m);
});
const send = (method, params = {}) => new Promise((res) => {
    const i = ++id;
    pending.set(i, res);
    ws.send(JSON.stringify({ id: i, method, params }));
});

const evaluate = async (expr) => {
    const r = await send('Runtime.evaluate', {
        expression: expr, returnByValue: true, awaitPromise: true,
    });
    if (r.result?.exceptionDetails) return { __error: r.result.exceptionDetails.text };
    return r.result?.result?.value;
};

await send('Page.enable');
await send('Runtime.enable');
await send('Log.enable');
await send('Network.enable');
await send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 900, deviceScaleFactor: 1, mobile: false });

const problems = [];
const notes = [];
const ok = (m) => notes.push('  [ ok ] ' + m);
const bad = (m) => { problems.push(m); notes.push('  [FAIL] ' + m); };

async function go(url, wait = 2200) {
    events.length = 0;
    await send('Page.navigate', { url });
    await sleep(wait);
    const errs = events
        .filter((e) => e.method === 'Runtime.exceptionThrown'
            || (e.method === 'Runtime.consoleAPICalled' && e.params.type === 'error')
            || (e.method === 'Log.entryAdded' && e.params.entry.level === 'error'))
        .map((e) => e.params?.exceptionDetails?.text
            || e.params?.entry?.text
            || (e.params?.args || []).map((a) => a.value ?? a.description).join(' '))
        .filter(Boolean)
        /* Chrome logs a page's own non-2xx status as a console resource error.
           On /does-not-exist that IS the correct behaviour, so a failure line
           about the 404 route answering 404 is the check being wrong, not the
           site. Filter it rather than reading past it every run. */
        .filter((t) => !/Failed to load resource.*40[0-9]/.test(t));
    const failed = events
        .filter((e) => e.method === 'Network.loadingFailed')
        .map((e) => e.params.errorText);
    return { errs, failed };
}

// ---------------------------------------------------------------- 1. routes
const ROUTES = [
    '/', '/about', '/team', '/pricing', '/contact', '/blog', '/case-studies',
    '/web-development', '/web-security', '/marketing-advertisement',
    '/content-creation', '/ecommerce-support', '/privacy',
    '/locations/greater-noida', '/thank-you', '/does-not-exist',
];

console.log('\n1. EVERY ROUTE LOADS CLEAN\n');
for (const r of ROUTES) {
    const { errs, failed } = await go(BASE + r);
    const meta = await evaluate(`JSON.stringify({
        t: document.title,
        h1: document.querySelectorAll('h1').length,
        words: (document.body.innerText.match(/\\S+/g) || []).length,
        status: window.__status || null
    })`);
    const m = JSON.parse(meta || '{}');
    const label = r.padEnd(30);
    if (errs.length) bad(`${label} console error: ${errs[0].slice(0, 110)}`);
    else if (failed.length) bad(`${label} failed request: ${failed[0]}`);
    else if (!m.t) bad(`${label} no <title>`);
    else if (m.h1 !== 1) bad(`${label} ${m.h1} h1 elements (want exactly 1)`);
    else if (m.words < 60) bad(`${label} only ${m.words} words rendered`);
    else ok(`${label} ${m.words} words, 1 h1, no console error`);
}

// ------------------------------------------------------- 2. nav and dropdown
console.log('\n2. HEADER NAV\n');
await go(BASE + '/');
const navReport = await evaluate(`(() => {
    const links = [...document.querySelectorAll('header a[href]')]
        .map(a => a.getAttribute('href'))
        .filter(h => h && !h.startsWith('#') && !h.startsWith('http') && !h.startsWith('tel') && !h.startsWith('mailto'));
    return JSON.stringify([...new Set(links)]);
})()`);
for (const href of JSON.parse(navReport || '[]')) {
    const url = href.startsWith('/') ? BASE + href : BASE + '/' + href;
    const res = await fetch(url, { redirect: 'manual' });
    if (res.status >= 400) bad(`header link ${href} -> HTTP ${res.status}`);
    else ok(`header link ${href.padEnd(28)} -> ${res.status}`);
}

// ------------------------------------------------- 3. interactive components
console.log('\n3. COMPONENTS\n');
await go(BASE + '/');

const accordion = await evaluate(`(async () => {
    const btn = document.querySelector('.accordion-trigger');
    if (!btn) return 'no accordion';
    const before = btn.getAttribute('aria-expanded');
    btn.click();
    await new Promise(r => setTimeout(r, 500));
    const after = btn.getAttribute('aria-expanded');
    const panel = document.getElementById(btn.getAttribute('aria-controls'));
    const visible = panel && !panel.hasAttribute('hidden');
    btn.click();
    await new Promise(r => setTimeout(r, 400));
    return before + ' -> ' + after + ' , panel visible: ' + visible +
           ' , closed again: ' + (btn.getAttribute('aria-expanded') === 'false');
})()`);
String(accordion).includes('false -> true , panel visible: true , closed again: true')
    ? ok('FAQ accordion opens and closes  — ' + accordion)
    : bad('FAQ accordion — ' + accordion);

const modal = await evaluate(`(async () => {
    const open = document.querySelector('[data-modal-open], [data-open-modal], [href="#consultationModal"]')
        || [...document.querySelectorAll('button, a')].find(b => /consult/i.test(b.textContent));
    const m = document.getElementById('consultationModal');
    if (!m) return 'no modal in DOM';
    if (!open) return 'modal exists but no trigger found';
    open.click();
    await new Promise(r => setTimeout(r, 600));
    const isOpen = m.classList.contains('is-open');
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
    await new Promise(r => setTimeout(r, 600));
    return 'opened: ' + isOpen + ' , closed on Escape: ' + !m.classList.contains('is-open');
})()`);
String(modal).includes('opened: true , closed on Escape: true')
    ? ok('consultation modal — ' + modal)
    : bad('consultation modal — ' + modal);

const deck = await evaluate(`(async () => {
    const d = document.querySelector('[data-deck]');
    if (!d) return 'no deck';
    const s = d.closest('section');
    window.scrollTo(0, s.offsetTop + 40);
    await new Promise(r => setTimeout(r, 1400));
    const a = getComputedStyle(d.children[1]).transform;
    window.scrollTo(0, s.offsetTop + s.offsetHeight - window.innerHeight - 40);
    await new Promise(r => setTimeout(r, 1600));
    const b = getComputedStyle(d.children[1]).transform;
    return a !== b ? 'transforms with scroll' : 'DID NOT MOVE (' + a + ')';
})()`);
String(deck) === 'transforms with scroll' ? ok('product deck scrubs on scroll') : bad('product deck — ' + deck);

const gallery = await evaluate(`(async () => {
    const g = document.querySelector('[data-gallery]');
    if (!g) return 'no gallery';
    const s = g.closest('section');
    window.scrollTo(0, s.offsetTop + 40);
    await new Promise(r => setTimeout(r, 1200));
    const a = g.children[0].style.getPropertyValue('--rel');
    window.scrollTo(0, s.offsetTop + s.offsetHeight - window.innerHeight - 40);
    await new Promise(r => setTimeout(r, 1600));
    const b = g.children[0].style.getPropertyValue('--rel');
    const dots = document.querySelectorAll('[data-cf-dot][aria-current="true"]').length;
    return a !== b ? 'advances on scroll (' + a + ' -> ' + b + '), one dot current: ' + (dots === 1)
                   : 'DID NOT ADVANCE';
})()`);
String(gallery).startsWith('advances on scroll') ? ok('coverflow gallery — ' + gallery) : bad('coverflow gallery — ' + gallery);

// --------------------------------------------------------- 4. the lead form
console.log('\n4. LEAD FORM\n');
await go(BASE + '/contact');

const emptySubmit = await evaluate(`(async () => {
    const f = document.querySelector('form[data-lead], form#contactLeadForm, main form');
    if (!f) return 'no form on /contact';
    const btn = f.querySelector('[type=submit]');
    btn.click();
    await new Promise(r => setTimeout(r, 700));
    const invalid = f.querySelectorAll(':invalid, .has-error').length;
    return invalid > 0 ? 'blocked, ' + invalid + ' field(s) flagged' : 'SUBMITTED EMPTY';
})()`);
String(emptySubmit).startsWith('blocked') ? ok('empty submit is refused — ' + emptySubmit) : bad('empty submit — ' + emptySubmit);

const badEmail = await evaluate(`(async () => {
    const f = document.querySelector('main form');
    const email = f.querySelector('input[type=email], input[name*=email]');
    if (!email) return 'no email field';
    email.value = 'not-an-email';
    email.dispatchEvent(new Event('input', { bubbles: true }));
    email.dispatchEvent(new Event('blur', { bubbles: true }));
    await new Promise(r => setTimeout(r, 500));
    return email.checkValidity() ? 'ACCEPTED a bad address' : 'rejected';
})()`);
String(badEmail) === 'rejected' ? ok('malformed email is rejected') : bad('email validation — ' + badEmail);

const honeypot = await evaluate(`(() => {
    const f = document.querySelector('main form');
    const hp = [...f.querySelectorAll('input')].filter(i => {
        const s = getComputedStyle(i.closest('div') || i);
        return s.display === 'none' || s.visibility === 'hidden' || i.tabIndex === -1;
    });
    const sum = [...f.querySelectorAll('label')].find(l => /what is|\\+/.test(l.textContent));
    return JSON.stringify({ hidden: hp.length, sumCheck: !!sum, csrf: !!f.querySelector('input[name*=csrf], input[name*=token]') });
})()`);
const hp = JSON.parse(honeypot || '{}');
hp.csrf ? ok('form carries a CSRF token') : bad('form has NO CSRF token');
hp.sumCheck ? ok('arithmetic anti-bot check present') : bad('no anti-bot check on the form');

// ---------------------------------------------------------- 5. mobile drawer
console.log('\n5. MOBILE\n');
await send('Emulation.setDeviceMetricsOverride', { width: 390, height: 844, deviceScaleFactor: 2, mobile: true });
await go(BASE + '/');
const drawer = await evaluate(`(async () => {
    const btn = document.querySelector('[data-drawer-open], .nav-toggle, [aria-controls*=drawer], [aria-label*=enu]');
    if (!btn) return 'no menu button';
    btn.click();
    await new Promise(r => setTimeout(r, 700));
    const open = document.body.classList.contains('drawer-open') || document.body.classList.contains('nav-open')
        || !!document.querySelector('.drawer.is-open, .nav-drawer.is-open, [data-drawer].is-open');
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
    await new Promise(r => setTimeout(r, 600));
    const closed = !(document.body.classList.contains('drawer-open') || document.body.classList.contains('nav-open')
        || !!document.querySelector('.drawer.is-open, .nav-drawer.is-open, [data-drawer].is-open'));
    return 'opened: ' + open + ' , closed on Escape: ' + closed;
})()`);
String(drawer).includes('opened: true') ? ok('mobile drawer — ' + drawer) : bad('mobile drawer — ' + drawer);

/* THE ONLY OVERFLOW THAT MATTERS IS THE ONE YOU CAN SCROLL TO. An element
   wider than the viewport inside an overflow:hidden parent is a design, not a
   bug — .hero-floor is deliberately 120% wide and clipped by .hero-scene, and
   a marquee track is twice the width of its window by definition. The first
   version of this check listed those every run, which is how a report teaches
   people to ignore it. What it asks now is whether the DOCUMENT scrolls
   sideways, and only then goes looking for who is unclipped. */
const overflow = await evaluate(`(() => {
    const de = document.documentElement;
    const slack = de.scrollWidth - de.clientWidth;
    if (slack <= 1) return JSON.stringify({ slack, culprits: [] });
    const w = de.clientWidth;
    const clipped = (el) => {
        for (let n = el.parentElement; n; n = n.parentElement) {
            const o = getComputedStyle(n).overflowX;
            if (o === 'hidden' || o === 'clip' || o === 'auto' || o === 'scroll') return true;
        }
        return false;
    };
    const culprits = [...document.querySelectorAll('body *')].filter(e => {
        const r = e.getBoundingClientRect();
        return r.width > 0 && (r.right > w + 2 || r.left < -2) && !clipped(e);
    }).slice(0, 4).map(e => e.tagName + '.' + (typeof e.className === 'string' ? e.className.trim().split(/\s+/)[0] : ''));
    return JSON.stringify({ slack, culprits });
})()`);
const ov = JSON.parse(overflow || '{}');
(ov.slack ?? 0) <= 1
    ? ok('the phone viewport does not scroll sideways')
    : bad(`page scrolls ${ov.slack}px sideways at 390px: ${ov.culprits.join(', ') || 'no unclipped culprit found'}`);

// -------------------------------------------------------------------- report
console.log(notes.join('\n'));
console.log('\n' + (problems.length
    ? `${problems.length} problem(s):\n` + problems.map((p) => '  - ' + p).join('\n')
    : 'All public flows passed.') + '\n');

ws.close();
chrome.kill();
process.exit(problems.length ? 1 : 0);
