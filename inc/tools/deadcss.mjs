/**
 * deadcss.mjs — report class selectors in css/ that no template renders.
 *
 * Deleting the 3-D layer left its stylesheets behind: .stage, .stage-sticky,
 * .stage-still, .assembly-* and the rest are still shipped on every page, and
 * the per-type CSS budget in inc/tools/perf-budget.json is now failing by a
 * few KB because of it. Raising the budget would have hidden that; this finds
 * it instead.
 *
 * It is a REPORT, not a rewriter. A class can be constructed in PHP or added
 * by JS, so anything it lists is a candidate for a human to check, never
 * something to delete automatically.
 *
 *   node inc/tools/deadcss.mjs
 */
import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';

const ROOT = process.argv[2] || 'C:/Users/xshan/Desktop/rafly';

async function* walk(dir, exts) {
    for (const e of await readdir(dir, { withFileTypes: true })) {
        if (e.name === 'node_modules' || e.name === '.git' || e.name === 'vendor') continue;
        const p = path.join(dir, e.name);
        if (e.isDirectory()) yield* walk(p, exts);
        else if (exts.some((x) => e.name.endsWith(x))) yield p;
    }
}

// Every class used anywhere a template, a script or a PHP string could put it.
const used = new Set();
for await (const f of walk(ROOT, ['.php', '.js', '.mjs', '.html'])) {
    if (f.includes(`${path.sep}css${path.sep}`)) continue;
    const src = await readFile(f, 'utf8');
    for (const m of src.matchAll(/class(?:List)?\s*=?\s*["'`]([^"'`]+)["'`]/g)) {
        for (const c of m[1].split(/\s+/)) if (c) used.add(c.replace(/^\./, ''));
    }
    // classList.add('x'), .contains('x'), a bare 'is-live' in a string
    for (const m of src.matchAll(/['"`]([a-z][a-z0-9-]{2,})['"`]/gi)) used.add(m[1]);
}

const declared = new Map();
for await (const f of walk(path.join(ROOT, 'css'), ['.css'])) {
    const src = await readFile(f, 'utf8');
    // Strip comments so a class named inside prose is not counted as declared.
    const code = src.replace(/\/\*[\s\S]*?\*\//g, '');
    for (const m of code.matchAll(/\.(-?[_a-zA-Z][\w-]*)/g)) {
        const c = m[1];
        if (!declared.has(c)) declared.set(c, new Set());
        declared.get(c).add(path.relative(ROOT, f));
    }
}

const dead = [...declared.entries()]
    .filter(([c]) => !used.has(c))
    .sort((a, b) => a[0].localeCompare(b[0]));

if (!dead.length) {
    console.log('\nNo unused class selectors found.\n');
} else {
    console.log(`\n${dead.length} class selector(s) no template or script mentions:\n`);
    for (const [c, files] of dead) {
        console.log(`  .${c.padEnd(28)} ${[...files].join(', ')}`);
    }
    console.log('\nCandidates only — check each before deleting.\n');
}
