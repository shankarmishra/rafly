/**
 * seo-audit.mjs — the SEO-specific checks inc/tools/audit.mjs does not cover.
 *
 *     node inc/tools/seo-audit.mjs [--base http://127.0.0.1:8899]
 *
 * audit.mjs already owns keyboard/motion/no-JS/per-route weight, driven
 * through a real Chrome via CDP, because those genuinely need a browser.
 * Everything below is plain server-rendered HTML — this site's whole "no-JS
 * still works" design already proves that — so this crawls with plain
 * fetch() and a few regexes. No dependencies, no headless browser, same
 * check()/exit-code contract as audit.mjs.
 *
 * The crawl set is /sitemap.xml itself: every URL this script checks is
 * exactly every URL the site claims is canonical and indexable. That is also
 * what makes this a genuine regression test — sitemap.php and this script
 * read inc/sitemap.php's sitemap_urls() list of routes the same way in
 * spirit (this reads it rendered as XML, over HTTP, the way a search engine
 * actually would), so a route that quietly stops being indexable, or a page
 * that quietly breaks, shows up here without anyone having to remember to
 * check it by hand.
 */

const args = process.argv.slice(2);
const arg = (n, f) => { const i = args.indexOf(n); return i >= 0 && args[i + 1] ? args[i + 1] : f; };
const BASE = arg('--base', 'http://127.0.0.1:8899').replace(/\/$/, '');

let failures = 0;
const check = (label, pass, detail = '') => {
    if (!pass) failures++;
    console.log(`  [${pass ? ' ok ' : 'FAIL'}] ${label}${detail ? '  — ' + detail : ''}`);
};

/** GET a URL, following NO redirects, returning {status, location, body, headers}. */
async function fetchRaw(url) {
    const res = await fetch(url, { redirect: 'manual' });
    const body = res.status < 300 || res.status >= 400 ? await res.text() : '';
    return {
        status: res.status,
        location: res.headers.get('location'),
        body,
        contentType: res.headers.get('content-type') || '',
    };
}

function tag(body, re) {
    const m = body.match(re);
    return m ? m[1].trim() : null;
}

function decodeEntities(s) {
    return s
        .replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"').replace(/&#0?39;/g, "'").replace(/&mdash;/g, '—');
}

function extractHeadings(body) {
    const out = [];
    const re = /<h([1-6])\b[^>]*>/gi;
    let m;
    while ((m = re.exec(body))) out.push(Number(m[1]));
    return out;
}

function extractImages(body) {
    const out = [];
    const re = /<img\b([^>]*)>/gi;
    let m;
    while ((m = re.exec(body))) {
        const attrs = m[1];
        out.push({
            hasAlt: /\balt\s*=/.test(attrs),
            hasWidth: /\bwidth\s*=/.test(attrs),
            hasHeight: /\bheight\s*=/.test(attrs),
            src: (attrs.match(/\bsrc\s*=\s*"([^"]*)"/) || [, ''])[1],
        });
    }
    return out;
}

function extractInternalLinks(body, origin) {
    const out = new Set();
    const re = /<a\b[^>]*\shref\s*=\s*"([^"]*)"/gi;
    let m;
    while ((m = re.exec(body))) {
        let href = decodeEntities(m[1]);
        if (href.startsWith(origin)) href = href.slice(origin.length) || '/';
        if (!href.startsWith('/')) continue;                 // external, mailto:, tel:, #anchor
        if (href.startsWith('//')) continue;                  // protocol-relative external
        out.add(href.split('#')[0] || '/');
    }
    return out;
}

function extractJsonLd(body) {
    const out = [];
    const re = /<script type="application\/ld\+json">([\s\S]*?)<\/script>/g;
    let m;
    while ((m = re.exec(body))) {
        try { out.push(JSON.parse(m[1])); } catch { out.push(null); }
    }
    return out;
}

/** Every @id this graph DEFINES, and every @id-only reference it MAKES. */
function collectIds(node, defined, referenced) {
    if (Array.isArray(node)) { node.forEach((n) => collectIds(n, defined, referenced)); return; }
    if (node === null || typeof node !== 'object') return;

    const keys = Object.keys(node);
    if (node['@id'] && keys.length === 1) {
        referenced.add(node['@id']);   // a bare {"@id": "..."} IS a reference
    } else if (node['@id']) {
        defined.add(node['@id']);      // an @id alongside other keys IS a definition
    }
    for (const k of keys) {
        if (k === '@id') continue;
        collectIds(node[k], defined, referenced);
    }
}

async function main() {
    console.log(`\nSEO audit — ${BASE}\n`);

    /* ================================================================== */
    console.log('1. SITEMAP — fetch and parse\n');
    const smRes = await fetchRaw(BASE + '/sitemap.xml');
    check('sitemap.xml returns 200', smRes.status === 200, `got ${smRes.status}`);
    check('sitemap.xml is XML', smRes.contentType.includes('xml'), smRes.contentType);

    const locs = [...smRes.body.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => decodeEntities(m[1]));
    // <image:loc> also matches <loc>, so split them back out: an <image:image>
    // block's <loc> is never a page URL to crawl as one.
    const imageLocs = new Set(
        [...smRes.body.matchAll(/<image:image>[\s\S]*?<\/image:image>/g)]
            .flatMap((b) => [...b[0].matchAll(/<image:loc>([^<]+)<\/image:loc>/g)].map((m) => decodeEntities(m[1])))
    );
    const pageUrls = locs.filter((l) => !imageLocs.has(l));
    check('sitemap lists at least one page', pageUrls.length > 0, `${pageUrls.length} URL(s)`);

    const origin = pageUrls.length ? new URL(pageUrls[0]).origin : BASE;

    /* ================================================================== */
    console.log('\n2. IMAGE URLS — every <image:loc> actually resolves\n');
    let brokenImages = 0;
    for (const loc of imageLocs) {
        const r = await fetchRaw(loc);
        if (r.status !== 200) { brokenImages++; console.log(`  ! ${r.status} ${loc}`); }
    }
    check('every sitemap image resolves', brokenImages === 0, `${brokenImages}/${imageLocs.size} broken`);

    /* ================================================================== */
    console.log('\n3. PER-PAGE CHECKS\n');

    const titles = new Map();       // title -> [urls]
    const descriptions = new Map(); // description -> [urls]
    const pageLinks = new Map();    // url -> Set(internal links found on it)
    const globalLinkCache = new Map(); // url -> {status, location}
    let jsonLdFailures = 0;
    let h1Failures = 0;
    let headingSkipFailures = 0;
    let canonicalFailures = 0;
    let robotsFailures = 0;
    let altFailures = 0;
    let dimFailures = 0;

    for (const url of pageUrls) {
        const res = await fetchRaw(url);
        if (res.status !== 200) {
            check(`${url} returns 200`, false, `got ${res.status}`);
            continue;
        }
        const body = res.body;

        const title = decodeEntities(tag(body, /<title>([^<]*)<\/title>/i) || '');
        const desc = decodeEntities(tag(body, /<meta name="description" content="([^"]*)"/i) || '');
        const canonical = tag(body, /<link rel="canonical" href="([^"]*)"/i);
        const robots = tag(body, /<meta name="robots" content="([^"]*)"/i) || '';

        if (!title) { check(`${url} has a <title>`, false); }
        else titles.set(title, [...(titles.get(title) || []), url]);

        if (!desc) { check(`${url} has a meta description`, false); }
        else descriptions.set(desc, [...(descriptions.get(desc) || []), url]);

        if (!canonical || canonical !== url) {
            canonicalFailures++;
            console.log(`  ! canonical mismatch on ${url}: got "${canonical}"`);
        }

        if (robots.includes('noindex')) {
            robotsFailures++;
            console.log(`  ! ${url} is in sitemap.xml but sends noindex`);
        }

        const headings = extractHeadings(body);
        const h1Count = headings.filter((h) => h === 1).length;
        if (h1Count !== 1) { h1Failures++; console.log(`  ! ${url} has ${h1Count} <h1> element(s), expected 1`); }

        for (let i = 1; i < headings.length; i++) {
            if (headings[i] - headings[i - 1] > 1) {
                headingSkipFailures++;
                console.log(`  ! ${url} skips a heading level: h${headings[i - 1]} -> h${headings[i]}`);
                break;
            }
        }

        const graphs = extractJsonLd(body);
        if (graphs.length === 0) {
            jsonLdFailures++;
            console.log(`  ! ${url} has no JSON-LD at all`);
        }
        for (const g of graphs) {
            if (g === null) { jsonLdFailures++; console.log(`  ! ${url} has invalid JSON-LD`); continue; }
            const defined = new Set(), referenced = new Set();
            collectIds(g['@graph'] || g, defined, referenced);
            for (const ref of referenced) {
                if (!defined.has(ref)) {
                    jsonLdFailures++;
                    console.log(`  ! ${url} JSON-LD references ${ref}, never defined in the same graph`);
                }
            }
        }

        const images = extractImages(body);
        const noAlt = images.filter((i) => !i.hasAlt).length;
        const noDim = images.filter((i) => !i.hasWidth || !i.hasHeight).length;
        if (noAlt > 0) { altFailures += noAlt; console.log(`  ! ${url}: ${noAlt} <img> with no alt attribute at all`); }
        if (noDim > 0) { dimFailures += noDim; console.log(`  ! ${url}: ${noDim} <img> missing width/height`); }

        pageLinks.set(url, extractInternalLinks(body, origin));
    }

    check('every page has a unique <title>', [...titles.values()].every((v) => v.length === 1),
        [...titles.entries()].filter(([, v]) => v.length > 1).map(([t, v]) => `"${t}" on ${v.join(', ')}`).join('; '));
    check('every page has a unique meta description', [...descriptions.values()].every((v) => v.length === 1),
        [...descriptions.entries()].filter(([, v]) => v.length > 1).map(([, v]) => v.join(', ')).join('; '));
    check('every canonical is absolute and self-referential', canonicalFailures === 0, `${canonicalFailures} mismatch(es)`);
    check('nothing in sitemap.xml sends noindex', robotsFailures === 0, `${robotsFailures} page(s)`);
    check('every page has exactly one <h1>', h1Failures === 0, `${h1Failures} page(s)`);
    check('no page skips a heading level', headingSkipFailures === 0, `${headingSkipFailures} page(s)`);
    check('JSON-LD parses and every @id reference resolves', jsonLdFailures === 0, `${jsonLdFailures} issue(s)`);
    check('every <img> has an alt attribute', altFailures === 0, `${altFailures} image(s)`);
    check('every <img> has width and height', dimFailures === 0, `${dimFailures} image(s)`);

    /* ================================================================== */
    console.log('\n4. INTERNAL LINKS — broken links and redirect chains\n');

    const allLinks = new Set();
    for (const set of pageLinks.values()) for (const l of set) allLinks.add(l);

    let broken = 0, chains = 0;
    for (const link of allLinks) {
        const full = origin + link;
        let r = globalLinkCache.get(full);
        if (!r) { r = await fetchRaw(full); globalLinkCache.set(full, r); }

        if (r.status >= 400) { broken++; console.log(`  ! ${r.status} ${link}`); continue; }
        if (r.status >= 300) {
            const dest = r.location || '';
            const destFull = dest.startsWith('http') ? dest : origin + dest;
            let r2 = globalLinkCache.get(destFull);
            if (!r2) { r2 = await fetchRaw(destFull); globalLinkCache.set(destFull, r2); }
            if (r2.status >= 300 && r2.status < 400) {
                chains++;
                console.log(`  ! redirect chain: ${link} -> ${dest} -> (another redirect)`);
            }
        }
    }
    check('no broken internal links', broken === 0, `${broken}/${allLinks.size} checked`);
    check('no redirect chains among internal links', chains === 0, `${chains} chain(s)`);

    /* ================================================================== */
    console.log('\n5. ORPHAN PAGES — every sitemap URL reachable by internal links\n');

    const reachable = new Set(['/']);
    const queue = ['/'];
    while (queue.length) {
        const current = queue.shift();
        for (const link of pageLinks.get(origin + current) || pageLinks.get(current) || []) {
            if (!reachable.has(link)) { reachable.add(link); queue.push(link); }
        }
    }
    const sitemapPaths = pageUrls.map((u) => new URL(u).pathname || '/');
    const orphans = sitemapPaths.filter((p) => !reachable.has(p));
    check('no orphan pages (every sitemap URL reachable from /)', orphans.length === 0,
        orphans.length ? orphans.join(', ') : `${sitemapPaths.length} URL(s) all reachable`);

    console.log(`\n${failures ? failures + ' check(s) FAILED' : 'All checks passed'}\n`);
    process.exit(failures ? 1 : 0);
}

main().catch((e) => { console.error(e); process.exit(2); });
