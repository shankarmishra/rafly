/**
 * contrast.mjs — measure WCAG contrast for candidate token values, BEFORE
 * they are written into css/00-tokens.css.
 *
 * inc/tools/shoot.mjs measures the RENDERED page, which is the authority and
 * always will be. But it can only measure colours that already exist in the
 * stylesheet, so by the time it speaks, a palette has already been written,
 * a hundred call sites already reference it, and a failure means a rewrite
 * rather than an edit. This tool answers the same question one step earlier:
 * is this hex safe on that ground, at that size, before anything reads it.
 *
 * The two are not redundant. This one knows the intended pairing and nothing
 * about the page; shoot.mjs knows the page and nothing about the intent. A
 * token can pass here and still fail there — an ancestor's opacity, a grain
 * overlay multiplying over the text, a gradient the harness cannot sample —
 * and every one of those is a real bug this tool is structurally blind to.
 *
 *     node inc/tools/contrast.mjs                 # the whole palette table
 *     node inc/tools/contrast.mjs '#0a63ff' '#f6f8fc'   # one pair
 *
 * Thresholds are WCAG 2.2 AA: 4.5:1 normal text, 3:1 large text (>=24px, or
 * >=18.66px bold) and non-text (borders, focus rings, icons carrying meaning).
 *
 * No dependencies.
 */

/* ------------------------------------------------------------------ maths */

/** '#rrggbb' -> [r, g, b], 0-255. Accepts '#rgb' too. */
function parse(hex) {
    let h = String(hex).trim().replace(/^#/, '');
    if (h.length === 3) {
        h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
    }
    if (!/^[0-9a-f]{6}$/i.test(h)) {
        throw new Error(`not a hex colour: ${hex}`);
    }
    return [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16));
}

/**
 * WCAG relative luminance. The 0.03928 branch is the sRGB transfer curve's
 * linear segment near black — dropping it (as a lot of hand-rolled versions
 * do) overstates the contrast of very dark colours, which is exactly the
 * region this palette's ink and chapter ground live in.
 */
function luminance(hex) {
    const [r, g, b] = parse(hex).map((v) => {
        const c = v / 255;
        return c <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4;
    });
    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

/** Contrast ratio, always >= 1, order-independent. */
export function ratio(a, b) {
    const l1 = luminance(a);
    const l2 = luminance(b);
    return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
}

/* ------------------------------------------------------------- the palette */

/**
 * The candidate palette. This is the LOGO BLUE edition — sampled from
 * logo.png, replacing the machined-paper warm-grey set. Grounds first,
 * because everything else is measured against them.
 */
const GROUNDS = {
    'paper-01': '#f6f8fc',
    'paper-02': '#eef2fa',
    'paper-03': '#e3e9f6',
    'chapter':  '#050f33',
};

/**
 * Each entry: the token, its value, which grounds it must clear, and the
 * threshold it is held to. `min` is the honest part — a token declared at 3
 * is declaring "large text or non-text only", and that claim has to be kept
 * at every call site or the number here is decoration.
 */
const TOKENS = [
    // --- ink on paper -----------------------------------------------------
    { token: '--ink',         value: '#050f33', on: ['paper-01', 'paper-02', 'paper-03'], min: 4.5, note: 'display, headings' },
    { token: '--ink-2',       value: '#3a4468', on: ['paper-01', 'paper-02', 'paper-03'], min: 4.5, note: 'body' },
    { token: '--ink-3',       value: '#5b6384', on: ['paper-01', 'paper-02', 'paper-03'], min: 4.5, note: 'captions, eyebrows, metadata' },
    { token: '--ink-4',       value: '#767ea6', on: ['paper-01', 'paper-02', 'paper-03'], min: 3.0, note: 'LARGE TEXT ONLY' },

    // --- the accent -------------------------------------------------------
    { token: '--blue',        value: '#0a63ff', on: ['paper-01', 'paper-02', 'paper-03'], min: 3.0, note: 'fills and non-text only' },
    { token: '--blue-ink',    value: '#0b52d8', on: ['paper-01', 'paper-02', 'paper-03'], min: 4.5, note: 'the accent AS TEXT' },
    { token: '--blue-hover',  value: '#0842ad', on: ['paper-01', 'paper-02', 'paper-03'], min: 4.5, note: 'hovered link' },

    // --- white on the accent fills ---------------------------------------
    { token: 'white on --blue',      value: '#ffffff', onRaw: ['#0a63ff'], min: 4.5, note: 'CTA label' },
    { token: 'white on --blue-deep', value: '#ffffff', onRaw: ['#0230c6'], min: 4.5, note: 'gradient deep end' },
    { token: 'white on --pill',      value: '#ffffff', onRaw: ['#050f33'], min: 4.5, note: 'ink pill label' },

    // --- semantic ---------------------------------------------------------
    { token: '--success-ink', value: '#0e6f31', on: ['paper-01', 'paper-03'], min: 4.5, note: 'success AS TEXT' },

    /* The logo green is 2.43:1 on paper — it fails even the 3:1 NON-TEXT
       minimum, so it cannot be a tick, a status dot or a border on paper,
       let alone a word. It is a FILL: put ink on it, or put it on the dark
       chapter ground. Measured all three ways below so the boundary is a
       number rather than an intention. */
    { token: '--green (fill)',    value: '#1bba3a', on: ['paper-01'], min: 1.0, note: 'FAILS non-text on paper (2.43) — fill only, never a mark' },
    { token: '--ink on --green',  value: '#050f33', onRaw: ['#1bba3a'], min: 4.5, note: 'label on a green fill' },
    { token: '--green on chapter', value: '#1bba3a', on: ['chapter'], min: 4.5, note: 'green AS TEXT, in the dark only' },

    { token: '--danger',      value: '#c62828', on: ['paper-01', 'paper-03'], min: 4.5, note: 'error text' },
    { token: '--warning',     value: '#96560a', on: ['paper-01', 'paper-03'], min: 4.5, note: 'warning text' },
    { token: '--whatsapp',    value: '#128c4a', on: ['paper-01'],             min: 3.0, note: 'brand mark, non-text' },

    // --- on the chapter ground -------------------------------------------
    { token: '--on-chapter-1', value: '#f6f8fc', on: ['chapter'], min: 4.5, note: 'display, headings' },
    { token: '--on-chapter-2', value: '#c4cbe4', on: ['chapter'], min: 4.5, note: 'body' },
    { token: '--on-chapter-3', value: '#8a96c6', on: ['chapter'], min: 4.5, note: 'captions, metadata' },
    { token: '--accent-chapter', value: '#6ea0ff', on: ['chapter'], min: 4.5, note: 'the accent AS TEXT, in the dark' },

    // --- the five service accents, AS TEXT on paper ----------------------
    { token: '--svc-web',       value: '#0b52d8', on: ['paper-01', 'paper-03'], min: 4.5, note: 'web development' },
    { token: '--svc-security',  value: '#046070', on: ['paper-01', 'paper-03'], min: 4.5, note: 'web security' },
    { token: '--svc-marketing', value: '#8c2fbf', on: ['paper-01', 'paper-03'], min: 4.5, note: 'marketing — replaces the retired orange' },
    { token: '--svc-content',   value: '#6134c9', on: ['paper-01', 'paper-03'], min: 4.5, note: 'content creation' },
    { token: '--svc-ecom',      value: '#0d6b34', on: ['paper-01', 'paper-03'], min: 4.5, note: 'ecommerce support' },

    // --- non-text: borders, focus rings, hairlines ------------------------
    { token: '--focus (ring)',  value: '#0b52d8', on: ['paper-01', 'paper-03'], min: 3.0, note: 'focus ring, non-text minimum' },
    { token: '--line-strong',   value: '#cbd3e6', on: ['paper-01'],             min: 1.0, note: 'decorative hairline, no minimum' },
];

/* ------------------------------------------------------------------ report */

function fmt(n) {
    return n.toFixed(2).padStart(6);
}

function run() {
    const pair = process.argv.slice(2).filter((a) => !a.startsWith('--'));
    if (pair.length === 2) {
        const r = ratio(pair[0], pair[1]);
        console.log(`${pair[0]} on ${pair[1]}  ${r.toFixed(2)}:1`);
        console.log(`  normal text (4.5): ${r >= 4.5 ? 'PASS' : 'FAIL'}`);
        console.log(`  large / non-text (3.0): ${r >= 3.0 ? 'PASS' : 'FAIL'}`);
        return 0;
    }

    console.log('\nGrounds');
    for (const [name, hex] of Object.entries(GROUNDS)) {
        console.log(`  ${name.padEnd(10)} ${hex}`);
    }

    let failures = 0;
    console.log('\nToken                    value      min   ' +
        Object.keys(GROUNDS).map((g) => g.padStart(9)).join('') + '   note');
    console.log('-'.repeat(118));

    for (const t of TOKENS) {
        const cells = [];
        let worst = Infinity;

        if (t.onRaw) {
            for (const g of t.onRaw) {
                const r = ratio(t.value, g);
                worst = Math.min(worst, r);
                cells.push(fmt(r) + '   ');
            }
            while (cells.length < Object.keys(GROUNDS).length) cells.push('       -   ');
        } else {
            for (const g of Object.keys(GROUNDS)) {
                if (!t.on.includes(g)) {
                    cells.push('       -   ');
                    continue;
                }
                const r = ratio(t.value, GROUNDS[g]);
                worst = Math.min(worst, r);
                cells.push(fmt(r) + '   ');
            }
        }

        const ok = worst >= t.min;
        if (!ok) failures++;
        console.log(
            `${ok ? ' ' : '!'}${t.token.padEnd(23)} ${t.value}  ${String(t.min).padStart(4)}   ` +
            cells.join('') + `  ${t.note}`
        );
    }

    console.log('-'.repeat(118));
    console.log(failures === 0
        ? `\nAll ${TOKENS.length} candidates clear their stated minimum.\n`
        : `\n${failures} of ${TOKENS.length} candidates FAIL. Marked with ! above.\n`);
    return failures === 0 ? 0 : 1;
}

process.exit(run());
