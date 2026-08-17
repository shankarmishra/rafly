/* ==========================================================================
   fetch-photos.mjs — download the site's photography from Openverse.

       node inc/tools/fetch-photos.mjs              # fetch everything missing
       node inc/tools/fetch-photos.mjs --force      # re-fetch even if present
       node inc/tools/fetch-photos.mjs --only arc   # one slot group
       node inc/tools/fetch-photos.mjs --list       # print the credit table only

   WHY OPENVERSE AND NOT UNSPLASH OR PEXELS
   Both of those need an API key, and neither is CC0 — their licences permit
   site use but forbid redistributing the asset, which is a condition this repo
   would then have to carry forever. Openverse is Creative Commons' own index;
   querying it with license=cc0 returns only public-domain-dedicated work, and
   every record already carries the four things assets/CREDITS.md needs:
   creator, creator_url, license_url and the landing page. So the credit table
   is generated from the same response that chose the file, and cannot drift
   from what was actually downloaded.

   WHY 960w
   StockSnap (Openverse's main CC0 photo provider) serves exactly one size.
   That is not a limitation here: the widest slot on the site renders at about
   640 CSS px, so 960 covers it at 1.5x. build-photos.php then writes the WebP
   twins that inc/helpers.php photo() actually looks for.

   WHAT THIS TOOL WILL NOT DO
   It never downloads a portrait into a testimonial or team slot. Those avatars
   are monogram discs on purpose — the testimonials are anonymised sample rows,
   and a stranger's face beside a sample quote presents an invented client as a
   real one. Every slot below is decorative or illustrative, and none of them is
   captioned as Rafly's staff, premises or customers.
   ========================================================================== */

import fs from 'node:fs/promises';
import path from 'node:path';

const ROOT = path.resolve(import.meta.dirname, '../..');
const OUT  = path.join(ROOT, 'assets/photos');
const API  = 'https://api.openverse.org/v1/images/';
const UA   = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) rafly-asset-fetch/1.0';

const args  = process.argv.slice(2);
const force = args.includes('--force');
const listOnly = args.includes('--list');
const onlyIdx = args.indexOf('--only');
const only = onlyIdx !== -1 ? args[onlyIdx + 1] : null;

/* Every photograph the site can show, with the search that finds it.
   `group` exists so --only can refetch one band without touching the rest.
   `q` is deliberately concrete: vague queries return stock-clipart mush. */
const SLOTS = [
    // The hero arc used to be photographed. It is not any more: see the note
    // in inc/tools/capture-mockups.mjs and the $ARC block in index.php. Twelve
    // slots were removed from here rather than left commented out, so a future
    // run cannot quietly re-download images nothing renders.

    // Service carousel cells. photo() is called as service-{key}.jpg, and the
    // keys come from inc/data/services.php — web, security, marketing,
    // content, ecom. These names are load-bearing.
    { file: 'service-web',       group: 'service', q: 'laptop code website development', alt: 'A website being built on a laptop screen' },
    { file: 'service-security',  group: 'service', q: 'data center servers blue',        alt: 'Network cabling in a server rack' },
    { file: 'service-marketing', group: 'service', q: 'analytics chart dashboard',       alt: 'A marketing analytics dashboard' },
    { file: 'service-content',   group: 'service', q: 'video camera studio production',  alt: 'A camera set up for a studio shoot' },
    { file: 'service-ecom',      group: 'service', q: 'warehouse boxes shipping',        alt: 'Parcels ready for e-commerce delivery' },

    // Challenge cards.
    { file: 'challenge-1', group: 'challenge', q: 'business meeting discussion',  alt: 'A team meeting in progress' },
    { file: 'challenge-2', group: 'challenge', q: 'paperwork documents invoices', alt: 'Paperwork spread across a desk' },
    { file: 'challenge-3', group: 'challenge', q: 'whiteboard planning workshop',alt: 'A team planning at a whiteboard' },

    // The About figure and the work covers.
    { file: 'about-desk', group: 'about', q: 'office desk notebook coffee',   alt: 'A notebook and coffee on a working desk' },
    { file: 'work-1',     group: 'work',  q: 'empty modern office interior',  alt: '' },
    { file: 'work-2',     group: 'work',  q: 'retail store shop interior',     alt: '' },
    { file: 'work-3',     group: 'work',  q: 'cafe restaurant interior',      alt: '' },
];

/* Three passes, loosening as they go. The first asks for exactly what the slot
   wants; if that returns nothing the category and extension filters come off,
   and finally the query itself is cut to its first two words. license=cc0 is
   the one parameter that never relaxes — it is the whole reason this source was
   chosen over Unsplash. */
const PASSES = [
    q => `q=${encodeURIComponent(q)}&category=photograph&extension=jpg`,
    q => `q=${encodeURIComponent(q)}`,
    q => `q=${encodeURIComponent(q.split(' ').slice(0, 2).join(' '))}`,
];

async function api(q, pass, page = 1) {
    const url = `${API}?${PASSES[pass](q)}&license=cc0&page_size=20&page=${page}&mature=false`;
    const res = await fetch(url, { headers: { 'User-Agent': UA } });
    if (res.status === 404) return { results: [] };   // page past the end
    if (!res.ok) throw new Error(`Openverse ${res.status} for "${q}"`);
    return res.json();
}

/* One image per slot, and never the same image twice across slots — a ring of
   twelve tiles with a repeat in it is more obvious than any single weak pick. */
const taken = new Set();

function pick(results) {
    for (const r of results) {
        if (taken.has(r.id)) continue;
        if (!r.url || !/^https:/.test(r.url)) continue;
        if (r.license !== 'cc0' && r.license !== 'pdm') continue;   // belt and braces
        return r;
    }
    return null;
}

/* The declared filetype is not reliable — Openverse reports what the provider
   claims, and several stocksnap records typed "jpg" arrive as PNG. Sniffing the
   magic number is the only way to name the file correctly, and naming matters:
   build-photos.php dispatches on the extension, and a PNG read as a JPEG is a
   hard failure rather than a degraded image. A byte length under 8 KB is the
   other tell — several CDNs answer a hotlink block with HTTP 200 and a tiny
   placeholder image. */
function sniff(buf) {
    if (buf[0] === 0xff && buf[1] === 0xd8) return 'jpg';
    if (buf[0] === 0x89 && buf[1] === 0x50 && buf[2] === 0x4e && buf[3] === 0x47) return 'png';
    return null;
}

async function download(url, destNoExt) {
    const res = await fetch(url, { headers: { 'User-Agent': UA } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const buf = Buffer.from(await res.arrayBuffer());
    const ext = sniff(buf);
    if (!ext) throw new Error(`not an image (${buf.length} bytes)`);
    if (buf.length < 8000) throw new Error(`placeholder, ${buf.length} bytes`);
    const dest = `${destNoExt}.${ext}`;
    await fs.writeFile(dest, buf);
    return { size: buf.length, ext, dest };
}

async function main() {
    await fs.mkdir(OUT, { recursive: true });

    const creditsPath = path.join(OUT, 'credits.json');
    let credits = {};
    try { credits = JSON.parse(await fs.readFile(creditsPath, 'utf8')); } catch { /* first run */ }
    for (const c of Object.values(credits)) taken.add(c.id);

    if (listOnly) { printTable(credits); return; }

    const wanted = SLOTS.filter(s => !only || s.group === only);
    let got = 0, skipped = 0, failed = 0, bytes = 0;

    for (const slot of wanted) {
        const destNoExt = path.join(OUT, slot.file);
        const have = await Promise.all(['jpg', 'png'].map(
            e => fs.access(`${destNoExt}.${e}`).then(() => true, () => false)
        )).then(r => r.some(Boolean));
        if (have && !force) { skipped++; continue; }

        /* Walk passes x pages until something downloads. A record can pass
           pick() and still fail to fetch — dead CDN links are common in an
           aggregated index — so the download is inside the search loop rather
           than after it, and a failure moves to the next candidate instead of
           abandoning the slot. */
        let saved = null, chosen = null;
        for (let pass = 0; pass < PASSES.length && !saved; pass++) {
            for (let page = 1; page <= 2 && !saved; page++) {
                let results;
                try { results = (await api(slot.q, pass, page)).results || []; }
                catch { break; }

                for (const cand of results) {
                    if (taken.has(cand.id)) continue;
                    if (!cand.url || !/^https:/.test(cand.url)) continue;
                    if (cand.license !== 'cc0' && cand.license !== 'pdm') continue;
                    try {
                        saved = await download(cand.url, destNoExt);
                        chosen = cand;
                        break;
                    } catch { /* next candidate */ }
                }
            }
        }
        if (!saved) { console.error(`  ! ${slot.file}: nothing usable for "${slot.q}"`); failed++; continue; }

        {
            const size = saved.size;
            taken.add(chosen.id);
            bytes += size;
            got++;
            credits[slot.file + '.' + saved.ext] = {
                id: chosen.id,
                title: chosen.title || '',
                creator: chosen.creator || 'Unknown',
                creator_url: chosen.creator_url || '',
                source: chosen.source || '',
                landing: chosen.foreign_landing_url || '',
                license: (chosen.license || '').toUpperCase(),
                license_url: chosen.license_url || '',
                alt: slot.alt,
            };
            console.log(`  ${(slot.file + '.' + saved.ext).padEnd(22)} ${(size / 1024).toFixed(0).padStart(5)} KB  ${chosen.creator} — ${chosen.title}`);
        }
    }

    /* Drop credit rows whose file is gone. A refetch can land a different
       extension, and a ghost row crediting a file the site never serves is
       exactly the kind of drift this generated table exists to prevent. */
    for (const key of Object.keys(credits)) {
        const stillThere = await fs.access(path.join(OUT, key)).then(() => true, () => false);
        if (!stillThere) delete credits[key];
    }
    await fs.writeFile(creditsPath, JSON.stringify(credits, null, 2) + '\n');

    console.log(`\n${got} downloaded, ${skipped} already present, ${failed} failed. ${(bytes / 1024 / 1024).toFixed(2)} MB.`);
    console.log(`Credits written to assets/photos/credits.json — run with --list for the CREDITS.md table.`);
    if (failed) process.exitCode = 1;
}

function printTable(credits) {
    const rows = Object.entries(credits).sort(([a], [b]) => a.localeCompare(b));
    console.log('| File | Photographer | Source | Licence |');
    console.log('|---|---|---|---|');
    for (const [file, c] of rows) {
        const who = c.creator_url ? `[${c.creator}](${c.creator_url})` : c.creator;
        const src = c.landing ? `[${c.source}](${c.landing})` : c.source;
        const lic = c.license_url ? `[${c.license} 1.0](${c.license_url})` : c.license;
        console.log(`| \`assets/photos/${file}\` | ${who} | ${src} | ${lic} |`);
    }
}

main().catch(err => { console.error(err); process.exit(2); });
