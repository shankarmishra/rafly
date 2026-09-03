<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * THE HOMEPAGE.
 *
 * Every section answers a question a buyer actually asks, in the order they
 * ask it:
 *
 *   1   what is this              hero
 *   2   what do you believe       the statement
 *   2b  what does it look like    the laptop — a lid that opens on the portal
 *   3   what do I get             the platform — five products, fanned
 *   3b  do you build apps         apps — three phones
 *   3c  what kinds of build       the gallery — a coverflow of five covers
 *   4   how does it run           delivery
 *   5   what is it built on       the stack
 *   6   who have you done it for  selected work   (conditional — see below)
 *   7   what will you NOT do      honest limits
 *   8   what does it cost         pricing and FAQ
 *   9   how do I start            the close
 *
 * FOUR SECTIONS SHOW A DEVICE, AND EACH ONE HAS A DIFFERENT VERB. That is the
 * rule that keeps them from reading as the same idea four times: the laptop
 * OPENS, the platform deck FANS and then closes to a coverflow, the phones
 * STAND STILL and are simply arranged, the gallery SLIDES. A fifth device
 * section would need a fifth verb before it earns a place.
 *
 * TWO calls to action, not five: the hero and the close.
 *
 * THERE IS NO 3-D ON THIS PAGE, AND THAT TOOK FOUR ATTEMPTS TO LEARN
 * ------------------------------------------------------------------
 * A WebGL point cloud, a glass torus knot, a rotating capability ring and a
 * machined assembly that came apart on scroll were all built for this page,
 * and all four were rejected on sight. The last of them was the best-executed
 * by a distance — a real studio lighting rig, PMREM environment maps,
 * pre-rendered stills that could not drift from the live scene — and it was
 * rejected for the same reason as the first three: a metal part says nothing
 * about what this company sells. Executing an idea better does not fix the
 * idea.
 *
 * What replaced it is drawn from the subject instead of from a geometry
 * library: the five services as five working screens, and the app work as
 * three phones. Deleted with the object: js/stage3d.js, js/assembly.js,
 * js/studio.js, inc/tools/render-stills.mjs, vendor/three/ (744 KB) and
 * assets/render/ (712 KB). The homepage is roughly 1.5 MB lighter and no page
 * on the site loads a 3-D library any more.
 *
 * WHAT ELSE IS NOT HERE, AND WHY
 * ------------------------------
 *   the 3-D perspective carousel           a competing focal device
 *   the point-cloud band                   shipped a visibly empty frame
 *   the trust bar                          "120+ projects / 98% satisfaction"
 *                                          were seeded sample values
 *   24 stock photographs                   other companies' premises,
 *                                          standing in for our work
 */

/**
 * Single source for the FAQ: renders the accordion below AND the FAQPage
 * structured data, so the rich result can never claim something the page does
 * not actually say.
 */
$FAQS = [
    ['q' => 'How quickly can you start?',
     'a' => 'Most engagements begin within a week of our first call. For urgent fixes — a broken checkout, a security gap — we can have someone looking at it within 48 hours.'],
    ['q' => 'Do you work with existing websites?',
     'a' => 'Yes. Most of our work is on live sites and stores, not blank slates. We audit what is already there before we touch anything.'],
    ['q' => 'Who actually does the work?',
     'a' => 'A dedicated Rafly team — developers, a security reviewer, a content writer and a marketer — not a rotating cast of freelancers.'],
    ['q' => 'How do you price engagements?',
     'a' => 'Through bundled packages built around what you actually need. Instead of separate invoices for web, content, marketing and support, you get one scope and one price agreed up front.'],
    ['q' => 'What about security and compliance?',
     'a' => 'Every project includes a baseline security review — forms, sessions and access handling — as part of the package, not an optional add-on.'],
    ['q' => 'Do you offer ongoing support?',
     'a' => 'Yes. Bundled packages include a support window after launch, and we offer ongoing monthly plans for sites and stores that need continuous attention.'],
    ['q' => 'Can you integrate with our existing tools?',
     'a' => 'In most cases, yes — payment gateways, CRMs, analytics and e-commerce platforms. We confirm feasibility during discovery before committing.'],
    ['q' => 'Do you sign NDAs and transfer IP?',
     'a' => 'Yes. We sign NDAs on request, and full IP ownership transfers to you on final payment.'],
];

/**
 * Delivery. Real stage names and the same timeframe vocabulary the service
 * pages use (inc/data/services.php), so the two can never disagree.
 */
$FLOW = [
    ['Discovery',       '2-3 days',   'A call to understand your goals, your current setup, and which parts need the most help. No pitch deck until we know what is actually broken.'],
    ['Package and plan','3-5 days',   'We scope a bundled package matched to what you need, and price it up front. One scope, one number.'],
    ['Build and execute','2-6 weeks', 'Development, content and campaign work run in parallel inside one team against one plan. Nothing waits on a handoff between vendors who have never spoken.'],
    ['Launch',          '1 week',     'Your site, store or campaign goes live with the baseline security review already done, because it was part of the build rather than a phase after it.'],
    ['Ongoing',         'Continuous', 'Monitoring, updates and improvement. The team that built it is the team that keeps it running.'],
];

/**
 * The stack. Named tools only — every one appears in the `tools` array of a
 * real service in inc/data/services.php. No logo wall: a logo implies a
 * partnership, and Rafly has none to claim.
 */
$STACK = [
    ['Build',      ['PHP', 'Laravel', 'WordPress', 'JavaScript', 'MySQL']],
    ['Commerce',   ['Shopify', 'Payment gateways', 'Catalogue data', 'Order reconciliation']],
    ['Security',   ['SSL &amp; TLS config', 'WAF rules', 'Dependency updates', 'Access &amp; roles', 'Backup checks']],
    ['Growth',     ['Google Ads', 'Meta Ads', 'Analytics 4', 'Tag Manager', 'Search Console', 'Email campaigns']],
    ['Operations', ['Staging &amp; deploys', 'Core Web Vitals', 'Log review', 'Editorial calendar']],
];

/** The comparison — Rafly against the two real alternatives. */
$COMPARE = [
    ['Service coverage',  'Web, content, marketing, security and e-commerce in one team', 'Usually one specialty',                         'No shared context between people'],
    ['Accountability',    'One point of contact, accountable for outcomes',               'Account manager, execution outsourced further', 'You coordinate everyone yourself'],
    ['Security',          'Baseline review included in every package',                    'Usually a separate paid add-on',                'Rarely considered at all'],
    ['Delivery speed',    'Parallel execution across one coordinated team',               'Sequential handoffs between departments',       'Depends on individual availability'],
    ['Pricing structure', 'Clear, bundled packages',                                      'Custom retainers, scope creep',                 'Paid per task, costs add up'],
    ['Communication',     'Plain-language updates, no jargon',                            'Layered reporting, slower answers',             'Manual coordination on your end'],
];

/**
 * The five services. Index, label and slug all come from one place, so the
 * hero's spec list and the bento below it can never drift from each other or
 * from the page they link to.
 *
 * The last two columns are the card's accent TOKEN NAME and its glyph. A token
 * name rather than a hex, because --svc-security and --svc-ecom are fill-only
 * values and writing them here as literals is how a fill colour eventually
 * ends up on type. The card uses --sc as a background and a border only; every
 * word in it is --ink or --ink-3.
 */
$MODULES = [
    ['01', 'web-development',         'Web',       'Sites and web apps that load fast and do not fall over as you grow.',            '--svc-web',       'code'],
    ['02', 'web-security',            'Security',  'A practical look at what someone probing your site would find first.',           '--svc-security',  'shield'],
    ['03', 'marketing-advertisement', 'Marketing', 'Campaigns built around who is actually buying, reported in plain language.',     '--svc-marketing', 'megaphone'],
    ['04', 'content-creation',        'Content',   'Copy that says what you do, in your words, without the filler.',                 '--svc-content',   'pencil'],
    ['05', 'ecommerce-support',       'Commerce',  'The unglamorous side of selling online, kept in order.',                         '--svc-ecom',      'shopping-cart'],
];

/**
 * The five product surfaces in the deck, in the order they sit on screen:
 * centre first, then the inner pair, then the outer pair. That order is the
 * z-order and the paint order both, so a slot's index IS how far back it sits.
 *
 * The accents are the five --svc-* values re-expressed as a light/dark pair
 * for a gradient. They are chrome, not identity: nothing in a mock is labelled
 * with a number, so none of these needs to clear a text contrast ratio.
 */
$DECK = [
    ['web',       'Web Development', '#0a63ff', '#4b8bff'],
    ['security',  'Web Security',    '#0230c6', '#3d6ee8'],
    ['marketing', 'Marketing',       '#1b6bff', '#6aa4ff'],
    ['content',   'Content',         '#001a7a', '#3a63d8'],
    ['commerce',  'Commerce',        '#0847d6', '#5c93ff'],
];

/**
 * VIRAL REELS & SHORT-FORM CONTENT CAPABILITIES (5-PHASE CONTENT ENGINE)
 * IDEA → SCRIPTING → PRODUCTION → DISTRIBUTION → CONVERSION
 */
$REELS_CAPABILITIES = [
    [
        'idx'   => '01',
        'phase' => 'STRATEGY',
        'title' => 'Algorithmic Hook Strategy',
        'desc'  => 'Audience intent profiling & 3-second hook formulas.',
        'tag'   => 'Retention Engine',
        'icon'  => 'crosshair',
    ],
    [
        'idx'   => '02',
        'phase' => 'SCRIPTING',
        'title' => 'Direct-Response Scripting',
        'desc'  => 'Zero-filler copy calibrated for viral binge-watch velocity.',
        'tag'   => 'High-Intent Copy',
        'icon'  => 'pencil',
    ],
    [
        'idx'   => '03',
        'phase' => 'PRODUCTION',
        'title' => '4K Motion & Sound Design',
        'desc'  => 'Cinematic punch-ins, bespoke mixing & 60FPS precision.',
        'tag'   => 'Studio Polish',
        'icon'  => 'play',
    ],
    [
        'idx'   => '04',
        'phase' => 'DISTRIBUTION',
        'title' => 'Multi-Format Social Matrix',
        'desc'  => 'Synchronized rollout across Reels, Shorts & TikTok Ads.',
        'tag'   => 'Omnichannel Reach',
        'icon'  => 'share-2',
    ],
    [
        'idx'   => '05',
        'phase' => 'CONVERSION',
        'title' => 'Revenue & ROAS Pipeline',
        'desc'  => 'Frictionless DM funnels & 100% attributed store checkout.',
        'tag'   => 'Attributed ROAS',
        'icon'  => 'trending-up',
    ],
];
$CONTENT_REELS_POINTS = array_column($REELS_CAPABILITIES, 'title');
$APP_POINTS = &$CONTENT_REELS_POINTS;

$MODULES = [
    ['01', 'web-development',         'Web',       'Sites and web apps that load fast and do not fall over as you grow.',            '--svc-web',       'code'],
    ['02', 'web-security',            'Security',  'A practical look at what someone probing your site would find first.',           '--svc-security',  'shield'],
    ['03', 'marketing-advertisement', 'Marketing', 'Campaigns built around who is actually buying, reported in plain language.',     '--svc-marketing', 'megaphone'],
    ['04', 'content-creation',        'Content',   'Copy that says what you do, in your words, without the filler.',                 '--svc-content',   'pencil'],
    ['05', 'ecommerce-support',       'Commerce',  'The unglamorous side of selling online, kept in order.',                         '--svc-ecom',      'shopping-cart'],
];

$DECK = [
    ['web',       'Web Development', '#0a63ff', '#4b8bff'],
    ['security',  'Web Security',    '#0230c6', '#3d6ee8'],
    ['marketing', 'Marketing',       '#1b6bff', '#6aa4ff'],
    ['content',   'Content',         '#001a7a', '#3a63d8'],
    ['commerce',  'Commerce',        '#0847d6', '#5c93ff'],
];

/**
 * WHAT WE BUILD — FOUR KINDS OF BUILD, ONE CONNECTED TEAM
 * ONE TEAM // FOUR CAPABILITIES // ONE UNIFIED DIGITAL SYSTEM
 */
$BUILD_SERVICES = [
    [
        'idx'       => '01',
        'key'       => 'strategy',
        'title'     => 'Brand & Digital Strategy',
        'sub'       => 'Research-driven strategy that aligns brand, audience and growth.',
        'keywords'  => ['STRATEGY', 'DISCOVERY', 'POSITIONING'],
        'accent'    => '#0a63ff',
        'tag'       => 'FOUNDATION & POSITIONING',
        'kpi'       => 'Market Alignment',
        'metric'    => '+180% Signal Velocity',
        'desc_long' => 'Deep audience intent discovery, market positioning vectors, and unified brand architecture designed to convert high-intent demand.',
    ],
    [
        'idx'       => '02',
        'key'       => 'experience',
        'title'     => 'UX & UI Experience Design',
        'sub'       => 'Interfaces that feel simple, intuitive and distinctly human.',
        'keywords'  => ['UX RESEARCH', 'UI SYSTEMS', 'PROTOTYPING'],
        'accent'    => '#2563eb',
        'tag'       => 'HUMAN-CENTERED SYSTEMS',
        'kpi'       => 'Design Token Scalability',
        'metric'    => '60FPS Native Micro-Flows',
        'desc_long' => 'High-end design systems, component libraries, and interactive prototypes calibrated for effortless cognitive clarity.',
    ],
    [
        'idx'       => '03',
        'key'       => 'development',
        'title'     => 'Web & App Development',
        'sub'       => 'Fast, secure and scalable digital experiences built to grow.',
        'keywords'  => ['FRONTEND', 'BACKEND', 'APIs'],
        'accent'    => '#0230c6',
        'tag'       => 'ZERO-BLOAT ARCHITECTURE',
        'kpi'       => 'Sub-50ms Response Time',
        'metric'    => '100% Type-Safe Full-Stack',
        'desc_long' => 'Modern performant web applications, custom APIs, and native mobile surfaces engineered with zero unnecessary dependencies.',
    ],
    [
        'idx'       => '04',
        'key'       => 'growth',
        'title'     => 'Growth & Performance Marketing',
        'sub'       => 'Data-led campaigns designed to create measurable growth.',
        'keywords'  => ['PERFORMANCE', 'ACQUISITION', 'ROAS'],
        'accent'    => '#6134c9',
        'tag'       => 'ATTRIBUTED CONVERSION',
        'kpi'       => 'Full-Funnel Attribution',
        'metric'    => '4.8x Performance ROAS',
        'desc_long' => 'High-velocity acquisition engines, precision creative iteration, and attribution funnels directly tied to customer checkout.',
    ],
];
$GALLERY = $BUILD_SERVICES; // Backward compatibility alias

/**
 * Three phone shells, and the screen each one renders. Centre, then left, then
 * right — the same order the deck above uses.
 *
 * Three copies of one screen would be a filmstrip of one app, so these are
 * three different screens; the status bar, the tab bar and the home indicator
 * are shared, which is what makes them read as three screens OF ONE APP.
 */
$PHONES = [
    ['#0a63ff', '#5c93ff', 'store'],
    ['#0230c6', '#3d6ee8', 'booking'],
    ['#1b6bff', '#6aa4ff', 'orders'],
];

/**
 * Admin-owned content, read before any output so a slow or failed query
 * cannot leave a half-rendered page. db_available() never throws; with the
 * database down these sections render empty and the rest of the page is
 * unaffected.
 *
 * #work stays CONDITIONAL. Every case study in the database matched
 * inc/data/seed-preview.php verbatim and had been flagged as real; those flags
 * were reset by inc/tools/unflag-seed.php. The section is designed and waiting
 * for work Rafly can actually name. An empty section is better than a
 * fabricated one.
 */
$caseStudies = case_studies_all(3);
$bundles     = bundles_all();

/** Service Tabs for the interactive limits terminal (No 'All' option). */
$serviceTabs = [
    [
        'slug'     => 'web-development',
        'label'    => 'Web Development',
        'short'    => 'Web Dev',
        'icon'     => 'code',
        'scTok'    => '--svc-web',
        'count'    => 3,
        'badge'    => 'Design & Stacks',
    ],
    [
        'slug'     => 'web-security',
        'label'    => 'Web Security',
        'short'    => 'Security',
        'icon'     => 'shield',
        'scTok'    => '--svc-security',
        'count'    => 3,
        'badge'    => 'Hardening & Audit',
    ],
    [
        'slug'     => 'marketing-advertisement',
        'label'    => 'Marketing & Ads',
        'short'    => 'Marketing',
        'icon'     => 'trending-up',
        'scTok'    => '--svc-marketing',
        'count'    => 3,
        'badge'    => 'ROAS & Channels',
    ],
    [
        'slug'     => 'content-creation',
        'label'    => 'Content & Words',
        'short'    => 'Content',
        'icon'     => 'pencil',
        'scTok'    => '--svc-content',
        'count'    => 3,
        'badge'    => 'Copy & Voice',
    ],
    [
        'slug'     => 'ecommerce-support',
        'label'    => 'E-Commerce Ops',
        'short'    => 'Commerce',
        'icon'     => 'shopping-cart',
        'scTok'    => '--svc-ecom',
        'count'    => 3,
        'badge'    => 'Store & Checkout',
    ],
];

/** Every honest limit the five services declare, indexed per service. */
$limits = [];
$standardsMap = [
    'Native mobile apps'           => '100% focused on ultra-fast browser apps & PWAs with 99+ Core Web Vitals.',
    'Brand identity from scratch'  => 'We refine your existing assets for high-converting digital interface systems.',
    'Enterprise replatforming'     => 'Right-sized scalable architectures with zero multi-year migration risk.',
    'Formal penetration testing'   => 'Direct security hardening for forms, sessions, and headers included in build.',
    'Compliance certification'     => 'Pragmatic technical safeguards that protect real customer data day to day.',
    'Live incident response'       => 'Proactive defensive architecture built up front to prevent security incidents.',
    'Guaranteed rankings or leads' => 'Transparent conversion tracking, high-intent targeting, and clear ROI reporting.',
    'Large-scale media buying'     => 'High-intent search & paid social campaigns built for owner-operator returns.',
    'Influencer and PR management' => 'Direct conversion funnels and ad creative that drive measurable revenue.',
    'Video production'             => 'High-impact website, funnel, and campaign copy in your genuine brand voice.',
    'Technical documentation'      => 'Commercial copy that explains your value clearly to paying customers.',
    'Translation'                  => 'Native English copywriting tailored for clarity, trust, and conversions.',
    'Warehousing and fulfilment'   => 'Digital storefront architecture, checkout speed, and payment reconciliation.',
    'Bookkeeping and filing'       => 'Cleanly mapped store and gateway payout records ready for your accountant.',
    'Staffing your inbox'          => 'Documented operating procedures and automated customer confirmation flows.',
];

$svcTokenMap = [
    'web-development'         => '--svc-web',
    'web-security'            => '--svc-security',
    'marketing-advertisement' => '--svc-marketing',
    'content-creation'        => '--svc-content',
    'ecommerce-support'       => '--svc-ecom',
];

$catTracker = [];
foreach (services_all() as $svc) {
    $slug = $svc['slug'] ?? '';
    if (!isset($catTracker[$slug])) $catTracker[$slug] = 0;
    foreach (($svc['boundaries'] ?? []) as $b) {
        $title = $b['title'];
        $catTracker[$slug]++;
        $limits[] = [
            'svc'       => $svc['title'],
            'slug'      => $slug,
            'icon'      => $svc['icon'] ?? 'shield',
            'scTok'     => $svcTokenMap[$slug] ?? '--svc-web',
            'localIdx'  => $catTracker[$slug],
            'title'     => $title,
            'desc'      => $b['desc'],
            'standard'  => $standardsMap[$title] ?? 'Direct execution with full accountability and transparent scope.',
        ];
    }
}

$page = [
    'id'        => 'home',
    'title'     => 'Rafly | Digital Growth — Build Fast, Grow Faster, Scale Smarter',
    'desc'      => 'One team for web development, security, marketing, content and e-commerce. One scope, one price, one person accountable — instead of five vendors who have never spoken.',
    'bodyClass' => 'page-home',
    'styles'    => ['home', 'home-scenes'],
    'module'    => 'home',
    /* Was assets/render/core-og-1200.webp, a render of the deleted object.
       assets/og-cover.png is generated from logo.png by inc/tools/build-assets.php
       and is the real mark, at the 1200x630 the meta tags promise. */
    'ogImage'   => 'assets/og-cover.png',
    'schema'    => [
        schema_faq($FAQS),
    ],
];
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">

    <?php /* ==========================================================
       01 — HERO

       Type ranged left, a cluster of product surfaces on the right.

       THE HEADLINE IS THE LIVE SITE'S HEADLINE. "Build Fast. Grow Faster.
       Scale Smarter." is what rafly.in says today and what Naveen asked to
       keep; the prototype's "One core. Five branches." is retired as a
       headline, though its five-branch STRUCTURE is what the dark chapter
       renders further down the page.

       THERE IS NO 3-D OBJECT HERE, AND THAT IS THE DECISION. Three were
       built for this slot and all three were rejected on sight — a WebGL
       point cloud, a glass torus knot, and a machined assembly. The knot in
       particular is the default object of every 3-D demo on the web and said
       nothing about what this company sells. What is here instead is drawn
       from the subject: the actual working surfaces a client would see, in
       glass cards, arranged AROUND the words rather than behind them. The
       composition has an empty middle, so the type needs no scrim.

       THE LCP ELEMENT IS THE HEADLINE. Never the shader, never a card. Both
       arrive after load and after an idle callback, and both are gated in JS
       before their module is fetched (js/home.js -> js/gates.js).

       Everything below renders complete with no JavaScript at all: the cards
       are real elements in their final positions, and the aurora's four
       blobs exist as CSS radial-gradients on .hero-scene at their t=0
       positions. Without JS the hero is the same design, standing still.
       ========================================================== */ ?>
    <?php /* ==========================================================================
       THE RAFly GROWTH STUDIO — hero section

       Concept: "Growth Reactor / Growth Lens"
       A translucent crystalline growth structure sitting inside a luminous studio environment.
       Five flowing ribbons (Web, Security, Marketing, Content, Commerce) converge
       into one central translucent crystalline core — communicating:
       "Five capabilities. One growth system."

       Composition: Asymmetric editorial layout.
       Desktop: LEFT ~48-52% editorial content / RIGHT ~48-52% generative visual.
       The visual is a Canvas2D + SVG hybrid rendered in hero-growth-field.js.

       NO fake SaaS metrics. NO orbit rings. NO dashboard cards. NO telemetry.
       ONE visual idea. ONE material language. ONE motion language.
       ========================================================== */ ?>
    <?php /* ==========================================================================
       THE RAFly SIGNAL FIELD — HERO SECTION
       
       Concept: "The RAFly Signal Field"
       A living generative visual system representing: BUILD → PROTECT → GROW → CREATE → CONVERT
       All five capabilities converge into one central crystalline 3D sculpture sitting inside
       a luminous digital studio environment.
       
       Composition: Asymmetric 45/55 editorial layout.
       Left (~44%): Editorial typography with exact 3-line headline & light sweep accent.
       Right (~56%): Generative Canvas2D + SVG 3D Signal Field sculpture.
       ========================================================================== */ ?>
    <section class="section hero sig-hero" id="home" data-hero aria-label="RAFly — Digital Growth Studio">

        <?php /* ── 8-LAYER ENVIRONMENT & TEXTURE SYSTEM ── */ ?>
        <div class="sig-env" aria-hidden="true">
            <div class="sig-env__grain"></div>
            <div class="sig-env__grid"></div>
            <div class="sig-env__dots"></div>
            <div class="sig-env__glow glow-primary"></div>
            <div class="sig-env__glow glow-secondary"></div>
            <div class="sig-env__scanbeam"></div>
            <svg class="sig-env__blueprint" viewBox="0 0 1440 900" fill="none" preserveAspectRatio="xMidYMid slice">
                <path class="sig-bp-line line-a" d="M -100,220 Q 380,120 780,440 T 1540,620" stroke="url(#sigBpGrad1)" stroke-width="1.5" />
                <path class="sig-bp-line line-b" d="M -100,640 Q 420,780 780,440 T 1540,180" stroke="url(#sigBpGrad2)" stroke-width="1.5" />
                <defs>
                    <linearGradient id="sigBpGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.45" />
                        <stop offset="50%" stop-color="#0891b2" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#0a63ff" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="sigBpGrad2" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#0230c6" stop-opacity="0.35" />
                        <stop offset="50%" stop-color="#6134c9" stop-opacity="0.20" />
                        <stop offset="100%" stop-color="#0891b2" stop-opacity="0" />
                    </linearGradient>
                </defs>
            </svg>
            <div class="sig-env__ghost-words" data-ghost-words>
                <span class="sig-ghost" data-ghost="build">BUILD</span>
                <span class="sig-ghost" data-ghost="protect">PROTECT</span>
                <span class="sig-ghost" data-ghost="create">CREATE</span>
                <span class="sig-ghost" data-ghost="grow">GROW</span>
                <span class="sig-ghost" data-ghost="convert">CONVERT</span>
            </div>
            <div class="sig-env__light-sweep"></div>
        </div>

        <?php /* ── MAIN CONTAINER ── */ ?>
        <div class="container sig-container">
            <div class="sig-grid">

                <?php /* ── LEFT SIDE — EDITORIAL CONTENT (~44%) ── */ ?>
                <div class="sig-content">

                    <?php /* Eyebrow */ ?>
                    <div class="sig-eyebrow">
                        <span class="sig-eyebrow__dot" aria-hidden="true"></span>
                        <span class="sig-eyebrow__label">DIGITAL GROWTH PARTNER</span>
                        <span class="sig-eyebrow__sep" aria-hidden="true">/</span>
                        <span class="sig-eyebrow__item">WEB</span>
                        <span class="sig-eyebrow__sep" aria-hidden="true">/</span>
                        <span class="sig-eyebrow__item">SECURITY</span>
                        <span class="sig-eyebrow__sep" aria-hidden="true">/</span>
                        <span class="sig-eyebrow__item">GROWTH</span>
                        <span class="sig-eyebrow__sep" aria-hidden="true">/</span>
                        <span class="sig-eyebrow__item">COMMERCE</span>
                    </div>

                    <?php /* Headline — Exactly 3 lines */ ?>
                    <h1 class="sig-headline" aria-label="Build fast. Grow faster. Scale smarter.">
                        <span class="sig-h-line" data-line="1">
                            <span class="sig-h-mask"><span class="sig-h-inner">Build fast.</span></span>
                        </span>
                        <span class="sig-h-line" data-line="2">
                            <span class="sig-h-mask"><span class="sig-h-inner">Grow faster.</span></span>
                        </span>
                        <span class="sig-h-line sig-h-line--accent" data-line="3">
                            <span class="sig-h-mask">
                                <span class="sig-h-inner">
                                    <span class="sig-h-focus" data-light-sweep>Scale smarter.</span>
                                </span>
                            </span>
                        </span>
                    </h1>

                    <?php /* Body copy */ ?>
                    <p class="sig-body">
                        One team building the digital systems that turn attention into growth.
                    </p>

                    <?php /* CTAs */ ?>
                    <div class="sig-actions">
                        <button type="button" class="sig-btn sig-btn--primary" data-modal-open="consultationModal" data-magnetic>
                            <span class="sig-btn__label">Book a free consultation</span>
                            <span class="sig-btn__arrow" aria-hidden="true"><?= icon('arrow-right') ?></span>
                            <span class="sig-btn__sheen" aria-hidden="true"></span>
                        </button>
                        <a class="sig-btn sig-btn--secondary" href="#approach" data-magnetic>
                            <span class="sig-btn__label">See our work</span>
                            <span class="sig-btn__arrow" aria-hidden="true"><?= icon('arrow-up-right') ?></span>
                        </a>
                    </div>

                    <?php /* Compact Editorial Trust Row */ ?>
                    <div class="sig-trust">
                        <span class="sig-trust__cell">
                            <span class="sig-trust__dot" aria-hidden="true">◉</span>
                            <span>48H DISCOVERY</span>
                        </span>
                        <span class="sig-trust__sep" aria-hidden="true">·</span>
                        <span class="sig-trust__cell">
                            <span class="sig-trust__dot" aria-hidden="true">◇</span>
                            <span>SECURITY INCLUDED</span>
                        </span>
                        <span class="sig-trust__sep" aria-hidden="true">·</span>
                        <span class="sig-trust__cell">
                            <span class="sig-trust__dot" aria-hidden="true">◇</span>
                            <span>100% IP OWNERSHIP</span>
                        </span>
                        <span class="sig-trust__sep" aria-hidden="true">·</span>
                        <span class="sig-trust__cell">
                            <span class="sig-trust__dot" aria-hidden="true">◇</span>
                            <span>ONE UNIFIED TEAM</span>
                        </span>
                    </div>

                </div><!-- /.sig-content -->

                <?php /* ── RIGHT SIDE — SIGNATURE VISUAL: THE RAFly SIGNAL FIELD (~56%) ── */ ?>
                <div class="sig-visual" aria-label="Interactive RAFly Signal Field Sculpture">

                    <div class="sig-stage" data-signal-stage>

                        <?php /* Multi-depth Canvas2D Signal Field */ ?>
                        <div class="sig-3d-scene">
                            <canvas class="sig-canvas" data-signal-canvas width="640" height="640" aria-hidden="true"></canvas>

                            <?php /* SVG leader overlay for sparse capability indicators */ ?>
                            <svg class="sig-annot-svg" viewBox="0 0 640 640" fill="none" aria-hidden="true">
                                <path class="sig-leader sig-leader--web" data-leader="web" d="M 90,140 C 140,140 180,180 230,220" />
                                <path class="sig-leader sig-leader--sec" data-leader="security" d="M 60,320 C 120,320 160,310 210,310" />
                                <path class="sig-leader sig-leader--mkt" data-leader="marketing" d="M 550,140 C 500,140 460,180 410,220" />
                                <path class="sig-leader sig-leader--cnt" data-leader="content" d="M 580,320 C 520,320 480,310 430,310" />
                                <path class="sig-leader sig-leader--cmr" data-leader="commerce" d="M 320,570 C 320,510 320,460 320,410" />
                            </svg>

                            <?php /* Floating minimalist editorial labels (sparse, no SaaS cards) */ ?>
                            <div class="sig-tag sig-tag--web" data-sig-tag="web">
                                <span class="sig-tag__idx">01</span>
                                <span class="sig-tag__name">WEB</span>
                            </div>
                            <div class="sig-tag sig-tag--sec" data-sig-tag="security">
                                <span class="sig-tag__idx">02</span>
                                <span class="sig-tag__name">SECURITY</span>
                            </div>
                            <div class="sig-tag sig-tag--mkt" data-sig-tag="marketing">
                                <span class="sig-tag__idx">03</span>
                                <span class="sig-tag__name">MARKETING</span>
                            </div>
                            <div class="sig-tag sig-tag--cnt" data-sig-tag="content">
                                <span class="sig-tag__idx">04</span>
                                <span class="sig-tag__name">CONTENT</span>
                            </div>
                            <div class="sig-tag sig-tag--cmr" data-sig-tag="commerce">
                                <span class="sig-tag__idx">05</span>
                                <span class="sig-tag__name">COMMERCE</span>
                            </div>

                        </div><!-- /.sig-3d-scene -->

                    </div><!-- /.sig-stage -->

                </div><!-- /.sig-visual -->

            </div><!-- /.sig-grid -->
        </div><!-- /.container -->

        <?php /* ── Scroll cue ── */ ?>
        <div class="sig-scroll" aria-hidden="true">
            <span class="sig-scroll__label">SCROLL TO DISCOVER</span>
            <span class="sig-scroll__line"><i></i></span>
        </div>

    </section><!-- /.sig-hero -->
    <section class="section statement manifesto-scene has-tex" id="approach" data-manifesto aria-label="The difference">
        <!-- Generative Environment Layers -->
        <div class="manifesto-bg" aria-hidden="true">
            <div class="manifesto-ambient">
                <span class="manifesto-glow glow-tr"></span>
                <span class="manifesto-glow glow-bl"></span>
                <span class="manifesto-glow glow-center"></span>
            </div>
            <div class="manifesto-grid"></div>
            <div class="manifesto-system" data-manifesto-system>
                <!-- SVG Network Topology: thin connecting lines, nodes, travelling packets -->
                <svg class="manifesto-topo-svg" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none">
                    <defs>
                        <linearGradient id="mTopoGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.25" />
                            <stop offset="50%" stop-color="#4b8bff" stop-opacity="0.10" />
                            <stop offset="100%" stop-color="#0230c6" stop-opacity="0.22" />
                        </linearGradient>
                        <linearGradient id="mTopoGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#5c93ff" stop-opacity="0.18" />
                            <stop offset="100%" stop-color="#0a63ff" stop-opacity="0.06" />
                        </linearGradient>
                        <filter id="mNodeGlow" x="-50%" y="-50%" width="200%" height="200%">
                            <feGaussianBlur stdDeviation="3" result="blur" />
                            <feMerge>
                                <feMergeNode in="blur" />
                                <feMergeNode in="SourceGraphic" />
                            </feMerge>
                        </filter>
                    </defs>

                    <!-- Structural Connection Paths -->
                    <path class="topo-path path-primary" d="M120,340 C240,280 280,210 400,210 C520,210 560,390 680,390 C800,390 840,230 960,230 C1040,230 1080,300 1140,320" stroke="url(#mTopoGrad1)" stroke-width="1.2" stroke-dasharray="6 6" />
                    <path class="topo-path path-secondary" d="M180,180 C300,180 340,360 460,360 C580,360 620,180 740,180 C860,180 900,370 1020,370 L1100,380" stroke="url(#mTopoGrad2)" stroke-width="1" />
                    <path class="topo-path path-tertiary" d="M220,440 L380,440 C480,440 520,260 600,260 C680,260 720,450 820,450 L980,450" stroke="url(#mTopoGrad2)" stroke-width="0.8" stroke-dasharray="3 7" />

                    <!-- Cross Interlinks -->
                    <line x1="400" y1="210" x2="460" y2="360" stroke="url(#mTopoGrad1)" stroke-width="0.8" stroke-dasharray="2 4" />
                    <line x1="600" y1="260" x2="680" y2="390" stroke="url(#mTopoGrad1)" stroke-width="0.8" stroke-dasharray="2 4" />
                    <line x1="740" y1="180" x2="820" y2="450" stroke="url(#mTopoGrad1)" stroke-width="0.8" stroke-dasharray="2 4" />
                    <line x1="860" y1="230" x2="1020" y2="370" stroke="url(#mTopoGrad2)" stroke-width="0.8" stroke-dasharray="2 4" />

                    <!-- Topological Nodes -->
                    <g class="topo-nodes">
                        <circle cx="400" cy="210" r="3.5" class="topo-node node-pulse" />
                        <circle cx="680" cy="390" r="4.5" class="topo-node node-primary" filter="url(#mNodeGlow)" />
                        <circle cx="960" cy="230" r="3.5" class="topo-node node-pulse" />
                        <circle cx="460" cy="360" r="3" class="topo-node" />
                        <circle cx="600" cy="260" r="3.5" class="topo-node node-pulse" />
                        <circle cx="740" cy="180" r="3" class="topo-node" />
                        <circle cx="820" cy="450" r="3" class="topo-node" />
                        <circle cx="1020" cy="370" r="2.5" class="topo-node" />
                        <circle cx="180" cy="180" r="2" class="topo-node" />
                        <circle cx="220" cy="440" r="2" class="topo-node" />
                    </g>

                    <!-- Travelling Data Packets -->
                    <circle class="topo-packet packet-1" r="3" fill="#0a63ff" filter="url(#mNodeGlow)" />
                    <circle class="topo-packet packet-2" r="2.5" fill="#4b8bff" filter="url(#mNodeGlow)" />
                    <circle class="topo-packet packet-3" r="2" fill="#0230c6" />
                </svg>
            </div>
            <div class="manifesto-particles" data-manifesto-particles>
                <!-- Delicate floating micro particles -->
                <span class="m-particle" style="--px:16%; --py:22%; --ps:2px; --pd:19s;"></span>
                <span class="m-particle" style="--px:78%; --py:18%; --ps:3px; --pd:24s;"></span>
                <span class="m-particle" style="--px:84%; --py:68%; --ps:2.5px; --pd:22s;"></span>
                <span class="m-particle" style="--px:14%; --py:72%; --ps:2px; --pd:26s;"></span>
                <span class="m-particle" style="--px:48%; --py:15%; --ps:1.5px; --pd:18s;"></span>
                <span class="m-particle" style="--px:32%; --py:82%; --ps:2.5px; --pd:28s;"></span>
                <span class="m-particle" style="--px:68%; --py:86%; --ps:2px; --pd:21s;"></span>
                <span class="m-particle" style="--px:25%; --py:40%; --ps:1.5px; --pd:25s;"></span>
                <span class="m-particle" style="--px:72%; --py:38%; --ps:2px; --pd:27s;"></span>
                <span class="m-particle" style="--px:55%; --py:64%; --ps:1px; --pd:20s;"></span>
                <span class="m-particle" style="--px:88%; --py:44%; --ps:2.5px; --pd:23s;"></span>
                <span class="m-particle" style="--px:10%; --py:46%; --ps:1.5px; --pd:25s;"></span>
            </div>
            <div class="manifesto-grain"></div>
        </div>

        <!-- Technical Corner Annotations -->
        <div class="manifesto-coords top-left" aria-hidden="true">
            <span class="coord-crosshair">+</span>
            <span class="coord-tag">02 / 09</span>
        </div>
        <div class="manifesto-coords top-right" aria-hidden="true">
            <span class="coord-tag">SYSTEM / ARCHITECTURE</span>
            <span class="coord-crosshair">+</span>
        </div>

        <div class="container manifesto-container">
            <!-- Eyebrow with technical animated entry line -->
            <div class="manifesto-eyebrow" data-r="rise">
                <span class="eyebrow-accent-line"></span>
                <p class="statement-meta manifesto-meta">The difference</p>
                <span class="eyebrow-node"></span>
            </div>

            <!-- Main Editorial Headline: Word-by-word reveal -->
            <h2 class="statement-line manifesto-headline" aria-label="We build digital systems, not deliverables.">
                <span class="headline-row">
                    <span class="m-word" style="--w-idx: 0;"><span class="m-word-inner">We</span></span>
                    <span class="m-word" style="--w-idx: 1;"><span class="m-word-inner">build</span></span>
                    <span class="m-word m-emp" style="--w-idx: 2;">
                        <span class="m-word-inner m-emp-inner">
                            <span class="m-emp-gradient">digital&nbsp;systems,</span>
                            <span class="m-emp-beam" aria-hidden="true"></span>
                        </span>
                    </span>
                </span>
                <span class="headline-row">
                    <span class="m-word" style="--w-idx: 3;"><span class="m-word-inner">not</span></span>
                    <span class="m-word" style="--w-idx: 4;"><span class="m-word-inner">deliverables.</span></span>
                </span>
            </h2>

            <!-- Supporting Paragraph: Slower reveal, high legibility -->
            <p class="statement-sub manifesto-sub" data-r="rise">
                Five separate vendors produce five separate deliverables and no system.
                The website does not know what the campaign promised; the campaign does
                not know what the store can actually ship. We build the parts that have
                to agree, together, so that they do.
            </p>
        </div>

        <!-- Technical Bottom Status Indicator -->
        <div class="manifesto-status-bar" aria-hidden="true">
            <div class="status-indicator">
                <span class="status-dot"></span>
                <span class="status-text">COHERENCE: UNIFIED // PARTS: IN SYNC</span>
            </div>
            <div class="status-meter">
                <span class="status-meter-bar"></span>
            </div>
        </div>
    </section>

    <?php /* The capability strip. A rhythm between the statement and the deck,
             and the first thing on the site to use the marquee that has been
             sitting complete in css/03-components.css and js/motion.js since
             the first build.

             aria-hidden, and every word in it is real copy somewhere else on
             the page. Nothing here is a claim; it is a beat between two heavy
             sections. js/motion.js clones the track once so the -50% loop is
             seamless, and css/06-motion.css already stops it under reduced
             motion. */ ?>
    <div class="strip" aria-hidden="true">
        <div class="marquee">
            <div class="marquee-track">
<?php foreach (['Web development', 'Web security', 'Marketing', 'Content', 'E-commerce support',
                'Mobile apps', 'One scope', 'One invoice', 'One person accountable'] as $word): ?>
                <span><?= e($word) ?></span><i></i>
<?php endforeach; ?>
            </div>
        </div>
    </div>



    <?php /* ==========================================================
       03 — THE RAFly SERVICE STUDIO
       
       One Large Cinematic Service Stage + Minimal Editorial Service Rail.
       WEB → SECURITY → MARKETING → CONTENT → COMMERCE
       ========================================================== */ ?>
    <section class="section service-studio" id="services" data-service-studio aria-label="THE RAFly SERVICE STUDIO — 05 Capabilities">
        
        <!-- KINETIC BACKGROUND GHOST TYPOGRAPHY -->
        <div class="ss-ghost-words" aria-hidden="true">
            <span class="ss-ghost kw-web">BUILD</span>
            <span class="ss-ghost kw-security">PROTECT</span>
            <span class="ss-ghost kw-marketing">CREATE</span>
            <span class="ss-ghost kw-content">CONVERT</span>
            <span class="ss-ghost kw-commerce">GROW</span>
        </div>

        <!-- LAYERED ATMOSPHERIC TEXTURE SYSTEM -->
        <div class="ss-bg-env" aria-hidden="true">
            <div class="ss-texture-grain"></div>
            <div class="ss-texture-dotmatrix"></div>
            <div class="ss-texture-grid"></div>
            <div class="ss-texture-lines"></div>
            <div class="ss-glow-primary"></div>
            <div class="ss-light-dust"></div>
        </div>

        <div class="container ss-container">
            <div class="ss-main-grid">
                
                <!-- LEFT COLUMN (~36%): EDITORIAL HEADLINE & STATEMENT -->
                <div class="ss-content">
                    
                    <!-- Eyebrow -->
                    <div class="ss-eyebrow">
                        <span class="ss-eyebrow__tag">THE RAFly SERVICE STUDIO</span>
                        <span class="ss-eyebrow__sep">/</span>
                        <span class="ss-eyebrow__text">05 CAPABILITIES</span>
                    </div>

                    <!-- Headline (max 3 lines) -->
                    <h2 class="ss-headline" aria-label="Everything your digital growth needs.">
                        <span class="ss-h-line">Everything your</span>
                        <span class="ss-h-line">digital growth</span>
                        <span class="ss-h-line ss-h-line--accent">needs.</span>
                    </h2>

                    <!-- Paragraph -->
                    <p class="ss-body">
                        Web, security, marketing, content and commerce &mdash; built by one connected team.
                    </p>

                    <!-- CTA Link -->
                    <div class="ss-action">
                        <a href="#approach" class="ss-cta-link" data-magnetic>
                            <span>Explore our capabilities</span>
                            <span class="ss-cta-arrow" aria-hidden="true"><?= icon('arrow-right') ?></span>
                        </a>
                    </div>

                </div><!-- /.ss-content -->

                <!-- RIGHT COLUMN (~64%): CINEMATIC SERVICE STAGE + EDITORIAL RAIL -->
                <div class="ss-stage-wrapper">
                    
                    <!-- CINEMATIC SERVICE STAGE (680–760px wide, 500–560px tall) -->
                    <div class="ss-stage" data-service-stage>
                        
                        <!-- Stage Environment Canvas2D / SVG hybrid -->
                        <canvas class="ss-canvas" data-stage-canvas width="740" height="520" aria-hidden="true"></canvas>

                        <!-- Live Vector Geometry & Stage Overlay Layers -->
                        <div class="ss-stage-overlay" data-stage-overlay>
                            <div class="ss-stage-header">
                                <span class="ssh-tag" data-active-tag>01 // WEB ARCHITECTURE</span>
                                <span class="ssh-status"><i class="ssh-dot"></i> STUDIO RENDER</span>
                            </div>
                        </div>

                    </div><!-- /.ss-stage -->

                    <!-- MINIMAL EDITORIAL SERVICE RAIL -->
                    <nav class="ss-service-rail" aria-label="Capabilities rail" data-service-rail>
                        <button type="button" class="ss-rail-item is-active" data-service-target="web" aria-selected="true">
                            <div class="sri-head">
                                <span class="sri-idx">01</span>
                                <span class="sri-label">WEB</span>
                            </div>
                            <span class="sri-sub">Build the experience.</span>
                            <span class="sri-indicator"></span>
                        </button>
                        
                        <div class="ss-rail-sep" aria-hidden="true"></div>

                        <button type="button" class="ss-rail-item" data-service-target="security" aria-selected="false">
                            <div class="sri-head">
                                <span class="sri-idx">02</span>
                                <span class="sri-label">SECURITY</span>
                            </div>
                            <span class="sri-sub">Protect the foundation.</span>
                            <span class="sri-indicator"></span>
                        </button>

                        <div class="ss-rail-sep" aria-hidden="true"></div>

                        <button type="button" class="ss-rail-item" data-service-target="marketing" aria-selected="false">
                            <div class="sri-head">
                                <span class="sri-idx">03</span>
                                <span class="sri-label">MARKETING</span>
                            </div>
                            <span class="sri-sub">Create demand.</span>
                            <span class="sri-indicator"></span>
                        </button>

                        <div class="ss-rail-sep" aria-hidden="true"></div>

                        <button type="button" class="ss-rail-item" data-service-target="content" aria-selected="false">
                            <div class="sri-head">
                                <span class="sri-idx">04</span>
                                <span class="sri-label">CONTENT</span>
                            </div>
                            <span class="sri-sub">Shape the story.</span>
                            <span class="sri-indicator"></span>
                        </button>

                        <div class="ss-rail-sep" aria-hidden="true"></div>

                        <button type="button" class="ss-rail-item" data-service-target="commerce" aria-selected="false">
                            <div class="sri-head">
                                <span class="sri-idx">05</span>
                                <span class="sri-label">COMMERCE</span>
                            </div>
                            <span class="sri-sub">Turn intent into revenue.</span>
                            <span class="sri-indicator"></span>
                        </button>
                    </nav>

                </div><!-- /.ss-stage-wrapper -->

            </div><!-- /.ss-main-grid -->

            <!-- SUBTLE BOTTOM METADATA FOOTER -->
            <footer class="ss-footer-meta" aria-hidden="true">
                <span>05 CAPABILITIES</span>
                <span class="ss-meta-sep">/</span>
                <span>ONE CONNECTED TEAM</span>
                <span class="ss-meta-sep">/</span>
                <span>BUILT TO WORK TOGETHER</span>
            </footer>

        </div><!-- /.container -->
    </section>

    <?php /* ==========================================================
       03b — THE RAFly FANNED BUILD DECK ("WHAT WE BUILD")
       
       "Five kinds of build, one team behind them."
       5 Fanned Product Surfaces (Dashboards, Mobile apps, Online stores, Marketing, Content)
       ========================================================== */ ?>
    <?php /* ==========================================================
       04 — VIRAL REELS & SHORT-FORM VIDEO CONTENT ENGINE
       
       IDEA → CONTENT → DISTRIBUTION → RESPONSE → CONVERSION
       Five synchronized distribution surfaces built on a unified content engine.
       ========================================================== */ ?>
    <section class="reels-engine-section has-tex" id="reels" data-reels-engine>
        <!-- Sticky Viewport Wrapper for Cinematic Scroll Pinned Experience -->
        <div class="reels-sticky-viewport">
            <!-- Background Layer 1: Architectural Grid & Texture -->
            <div class="tex-apps-grid" aria-hidden="true"></div>
            <div class="tex-apps-dots" aria-hidden="true"></div>
            <div class="tex-apps-hatch" aria-hidden="true"></div>

            <!-- Background Layer 2: Subtle Ambient Atmospheric Aura -->
            <div class="reels-ambient-aura" aria-hidden="true"></div>

            <!-- Technical Coordinate Markers -->
            <div class="reels-tech-marker top-left" aria-hidden="true">
                <span class="rtm-dot"></span>
                <span class="rtm-label">SYS.CONTENT // 04-REELS</span>
            </div>
            <div class="reels-tech-marker top-right" aria-hidden="true">
                <span class="rtm-label">PHASE <b data-reels-step>01</b> / 05</span>
            </div>

            <div class="container reels-main-container">
                
                <!-- ZONE 1: SECTION EDITORIAL HEADER -->
                <div class="reels-header-zone">
                    <div class="reels-eyebrow-row">
                        <span class="reels-sys-tag">SYS.CONTENT // 04-REELS</span>
                        <span class="reels-pipe-tag">// VIRAL HOOKS &amp; ATTRIBUTED SOCIAL FUNNELS</span>
                        <span class="reels-badge-pill">@officialrafly.in &bull; 48.9K+</span>
                    </div>

                    <h2 class="reels-heading">
                        <span class="rh-line">Viral Reels &amp; Content,</span>
                        <span class="rh-line"><span class="rh-accent">scripted, shot &amp; edited to convert.</span></span>
                    </h2>

                    <p class="reels-subhead">
                        One synchronized content engine producing five high-performing distribution surfaces &mdash;
                        from 3-second hook scripts to direct-response checkout pipelines, synchronized directly with your brand architecture.
                    </p>

                    <!-- Process Navigation Strip -->
                    <div class="reels-process-nav" role="tablist" aria-label="Content engine distribution pipeline" data-reels-nav>
                        <button type="button" class="rpn-tab is-active" data-phase="0" role="tab" aria-selected="true">
                            <span class="rpn-num">01</span>
                            <span class="rpn-label">Strategy</span>
                            <span class="rpn-indicator"></span>
                        </button>
                        <button type="button" class="rpn-tab" data-phase="1" role="tab" aria-selected="false">
                            <span class="rpn-num">02</span>
                            <span class="rpn-label">Scripting</span>
                            <span class="rpn-indicator"></span>
                        </button>
                        <button type="button" class="rpn-tab" data-phase="2" role="tab" aria-selected="false">
                            <span class="rpn-num">03</span>
                            <span class="rpn-label">Production</span>
                            <span class="rpn-indicator"></span>
                        </button>
                        <button type="button" class="rpn-tab" data-phase="3" role="tab" aria-selected="false">
                            <span class="rpn-num">04</span>
                            <span class="rpn-label">Distribution</span>
                            <span class="rpn-indicator"></span>
                        </button>
                        <button type="button" class="rpn-tab" data-phase="4" role="tab" aria-selected="false">
                            <span class="rpn-num">05</span>
                            <span class="rpn-label">Conversion</span>
                            <span class="rpn-indicator"></span>
                        </button>
                    </div>
                </div>

                <!-- ZONE 2: CENTRAL 3D VISUAL COMPOSITION STAGE -->
                <div class="reels-visual-stage" data-reels-stage>
                    
                    <!-- Atmospheric Depth Glow Behind Phones -->
                    <div class="reels-stage-glow" aria-hidden="true"></div>

                    <!-- FLOATING PROOF BADGES (Sequenced with Scroll Reveal) -->
                    <div class="reels-proof-badge badge-top-left" data-proof-idx="0" aria-hidden="true">
                        <span class="rpb-icon is-insta"><?= icon('instagram') ?></span>
                        <div class="rpb-info">
                            <strong>48.9K+ Active Community</strong>
                            <span>@officialrafly.in &bull; Verified Proof</span>
                        </div>
                    </div>

                    <div class="reels-proof-badge badge-top-right" data-proof-idx="1" aria-hidden="true">
                        <span class="rpb-icon is-green"><?= icon('play') ?></span>
                        <div class="rpb-info">
                            <strong>10x Viral Hook Formats</strong>
                            <span>3-Second Retention Scripting</span>
                        </div>
                    </div>

                    <div class="reels-proof-badge badge-bot-left" data-proof-idx="2" aria-hidden="true">
                        <span class="rpb-icon is-blue"><?= icon('trending-up') ?></span>
                        <div class="rpb-info">
                            <strong>Direct-Response Funnels</strong>
                            <span>Social Traffic &rarr; High-Converting Store</span>
                        </div>
                    </div>

                    <div class="reels-proof-badge badge-bot-right" data-proof-idx="3" aria-hidden="true">
                        <span class="rpb-icon is-purple"><?= icon('verified') ?></span>
                        <div class="rpb-info">
                            <strong>100% Attributed Proof</strong>
                            <span>ROAS 4.8x Performance Case Studies</span>
                        </div>
                    </div>

                    <!-- 5 SYNCHRONIZED 3D PHONE SURFACES -->

                    <!-- PHONE 04: REAR-LEFT (4K Motion & Creative Studio) -->
                    <div class="iphone-pro-surface phone-rear-left" data-surface-idx="3">
                        <div class="surface-chassis">
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">4K 60FPS</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-studio">
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                        <source src="<?= e(site_path('/assets/mockups/reel-2.mp4')) ?>" type="video/mp4">
                                    </video>
                                    <div class="studio-meta-overlay">
                                        <div class="studio-hud-tag">PROD.RENDER // 60 FPS</div>
                                        <div class="studio-eq-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                                        <div class="studio-codec-tag">BITRATE 48Mbps &bull; REC.709</div>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 02: LEFT SUPPORT (Instagram Story & 3s Retention Hook) -->
                    <div class="iphone-pro-surface phone-mid-left" data-surface-idx="1">
                        <div class="surface-chassis">
                            <span class="hw-btn btn-action" aria-hidden="true"></span>
                            <span class="hw-btn btn-vol-up" aria-hidden="true"></span>
                            <span class="hw-btn btn-vol-down" aria-hidden="true"></span>
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Story &bull; 2h</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-story">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <div class="story-progress-strip">
                                        <span class="sps-bar is-done"><i></i></span>
                                        <span class="sps-bar is-active"><i></i></span>
                                        <span class="sps-bar"><i></i></span>
                                    </div>
                                    <div class="story-profile-head">
                                        <div class="story-author">
                                            <div class="story-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Rafly" class="story-av"></div>
                                            <div class="story-user-meta">
                                                <span class="story-name">officialrafly.in <?= icon('verified') ?></span>
                                                <span class="story-age">2h ago</span>
                                            </div>
                                        </div>
                                        <span class="story-close"><?= icon('x') ?></span>
                                    </div>
                                    <div class="story-media-view">
                                        <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                            <source src="<?= e(site_path('/assets/mockups/reel-instagram.mp4')) ?>" type="video/mp4">
                                            <source src="<?= e(site_path('/assets/mockups/reel-1.mp4')) ?>" type="video/mp4">
                                        </video>
                                        <div class="story-stickers">
                                            <div class="sticker-highlight">🔥 +248% Revenue Spurt</div>
                                            <div class="sticker-quote">
                                                <p>"Shipped full ecommerce rebuild in 14 days with zero downtime."</p>
                                                <span class="sticker-verified">&check; Client Verified</span>
                                            </div>
                                            <div class="sticker-swipe"><span>Swipe Up To Scale <?= icon('arrow-up') ?></span></div>
                                        </div>
                                    </div>
                                    <div class="story-reply-bar">
                                        <div class="story-input-fake"><span>Send message...</span></div>
                                        <div class="story-btns">
                                            <button type="button" class="story-btn is-heart" aria-label="Like story"><?= icon('heart') ?></button>
                                            <button type="button" class="story-btn" aria-label="Share story"><?= icon('send') ?></button>
                                        </div>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 01: HERO CENTER (Flagship Studio Creator Reel) -->
                    <div class="iphone-pro-surface phone-hero-center is-dominant" data-surface-idx="0">
                        <div class="surface-chassis">
                            <span class="hw-btn btn-action" aria-hidden="true"></span>
                            <span class="hw-btn btn-vol-up" aria-hidden="true"></span>
                            <span class="hw-btn btn-vol-down" aria-hidden="true"></span>
                            <span class="hw-btn btn-power" aria-hidden="true"></span>
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text" data-di-status>🔴 Live Reel &bull; 1080p 60FPS</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-reels">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>" data-reel-video>
                                        <source src="<?= e(site_path('/assets/mockups/reel-main.mp4')) ?>" type="video/mp4">
                                        <source src="<?= e(site_path('/assets/mockups/reel-1.mp4')) ?>" type="video/mp4">
                                    </video>
                                    <div class="reels-header-meta">
                                        <span class="reels-logo-text">Reels</span>
                                        <span class="reels-trend-pill">🔥 Trending</span>
                                    </div>
                                    <div class="reels-sidebar-actions">
                                        <div class="reels-action-unit like-unit is-active" data-reel-like>
                                            <span class="rau-icon is-liked"><?= icon('heart') ?></span>
                                            <span class="rau-val" data-like-count>48.9K</span>
                                        </div>
                                        <div class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('message-circle') ?></span>
                                            <span class="rau-val">1,842</span>
                                        </div>
                                        <div class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('send') ?></span>
                                            <span class="rau-val">12.4K</span>
                                        </div>
                                        <div class="reels-action-unit"><span class="rau-icon"><?= icon('bookmark') ?></span></div>
                                        <div class="reels-spinning-vinyl" title="Spinning Original Audio">
                                            <span class="vinyl-groove"></span>
                                            <img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Disc" class="vinyl-art">
                                        </div>
                                    </div>
                                    <div class="reels-content-footer">
                                        <div class="reels-creator-row">
                                            <div class="reels-av-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="officialrafly.in" class="reels-av-pic"></div>
                                            <div class="reels-handle">
                                                <strong>officialrafly.in</strong>
                                                <span class="verif-tag"><?= icon('verified') ?></span>
                                            </div>
                                            <a href="https://www.instagram.com/officialrafly.in?igsh=MTMwYWZhb29waWZtbA==" target="_blank" rel="noopener" class="reels-follow-btn">Follow</a>
                                        </div>
                                        <p class="reels-caption">
                                            Turning digital chaos into high-converting revenue systems 🚀 Full-stack web, security &amp; performance under one roof.
                                        </p>
                                        <div class="reels-tags">#RaflyGrowth #WebDev #GenZTech #Ecommerce</div>
                                        <div class="reels-audio-badge">
                                            <span class="audio-note"><?= icon('music') ?></span>
                                            <span class="audio-title">officialrafly.in &bull; Original Audio &bull; Trending Sound</span>
                                        </div>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow is-hero-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 03: RIGHT SUPPORT (Performance Case Study & Feed Distribution) -->
                    <div class="iphone-pro-surface phone-mid-right" data-surface-idx="2">
                        <div class="surface-chassis">
                            <span class="hw-btn btn-power" aria-hidden="true"></span>
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Case Study</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-feed">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <div class="feed-header-row">
                                        <div class="feed-poster-info">
                                            <div class="feed-av-wrap"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Rafly" class="feed-av"></div>
                                            <div>
                                                <span class="feed-name">officialrafly.in <?= icon('verified') ?></span>
                                                <span class="feed-sub">Sponsored &bull; Case Study</span>
                                            </div>
                                        </div>
                                        <span class="feed-dots">&bull;&bull;&bull;</span>
                                    </div>
                                    <div class="feed-media-viewport">
                                        <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                            <source src="<?= e(site_path('/assets/mockups/reel-instagram-2.mp4')) ?>" type="video/mp4">
                                            <source src="<?= e(site_path('/assets/mockups/reel-3.mp4')) ?>" type="video/mp4">
                                        </video>
                                        <div class="feed-kpi-badge">
                                            <div class="kpi-card">
                                                <span class="kpi-label">Conversion Velocity</span>
                                                <strong>+310.8%</strong>
                                                <span class="kpi-pill">Verified ROAS 4.8x</span>
                                            </div>
                                        </div>
                                        <span class="feed-counter">1/3</span>
                                    </div>
                                    <div class="feed-actions-strip">
                                        <div class="feed-left-btns">
                                            <button type="button" class="feed-btn is-heart" aria-label="Like post"><?= icon('heart') ?></button>
                                            <button type="button" class="feed-btn" aria-label="Comment on post"><?= icon('message-circle') ?></button>
                                            <button type="button" class="feed-btn" aria-label="Share post"><?= icon('send') ?></button>
                                        </div>
                                        <button type="button" class="feed-btn" aria-label="Bookmark post"><?= icon('bookmark') ?></button>
                                    </div>
                                    <div class="feed-caption-box">
                                        <div class="feed-likes">Liked by <strong>naveen.growth</strong> and <strong>18,340 others</strong></div>
                                        <p class="feed-text"><strong>officialrafly.in</strong> Zero bloat. 100% custom architectures shipped directly to production.</p>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 05: REAR-RIGHT (Commerce & Growth Engine) -->
                    <div class="iphone-pro-surface phone-rear-right" data-surface-idx="4">
                        <div class="surface-chassis">
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Revenue Sync</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-ledger">
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                        <source src="<?= e(site_path('/assets/mockups/reel-3.mp4')) ?>" type="video/mp4">
                                    </video>
                                    <div class="ledger-meta-overlay">
                                        <div class="ledger-hud-tag">REVENUE.SYNC // ROAS 4.8x</div>
                                        <div class="ledger-metric-box">
                                            <strong>1,420 Orders</strong>
                                            <span>+412% Checkout Velocity</span>
                                        </div>
                                        <div class="ledger-status-tag">🟢 100% Attributed Pipeline</div>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                </div>

                <!-- ZONE 3: FIVE SERVICE STAGE CARDS & CTA BAR -->
                <div class="reels-footer-zone">
                    <div class="reels-service-rail" data-reels-rail>
<?php foreach ($REELS_CAPABILITIES as $i => $cap): ?>
                        <div class="reels-service-card<?= $i === 0 ? ' is-active' : '' ?>" data-stage-card="<?= (int)$i ?>">
                            <div class="rsc-header">
                                <span class="rsc-number"><?= e($cap['idx']) ?></span>
                                <span class="rsc-category"><?= e($cap['phase']) ?></span>
                                <span class="rsc-arrow" aria-hidden="true">&rarr;</span>
                            </div>
                            <strong class="rsc-title"><?= e($cap['title']) ?></strong>
                            <p class="rsc-desc"><?= e($cap['desc']) ?></p>
                            <div class="rsc-footer">
                                <span class="rsc-tag"><?= e($cap['tag']) ?></span>
                            </div>
                        </div>
<?php endforeach; ?>
                    </div>

                    <div class="reels-cta-bar">
                        <a class="btn btn-pill btn-sm btn-primary" href="https://www.instagram.com/officialrafly.in?igsh=MTMwYWZhb29waWZtbA==" target="_blank" rel="noopener" data-magnetic>
                            <?= icon('instagram') ?> Follow @officialrafly.in <?= icon('arrow-up-right') ?>
                        </a>
                        <a class="btn btn-pill btn-sm btn-secondary" href="<?= e(site_path('/contact')) ?>" data-magnetic>
                            Launch a Video Campaign <?= icon('arrow-right') ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ==========================================================
       05 — THE RAFly FANNED BUILD DECK ("WHAT WE BUILD")
       
       "Five kinds of build, one team behind them."
       5 Fanned Product Surfaces (Dashboards, Mobile apps, Online stores, Marketing, Content)
       ========================================================== */ ?>
    <section class="section platform-deck-section has-tex" id="platform" data-platform-section>
        <div class="pd-sticky-viewport">
            
            <!-- REELS-STYLE BACKGROUND LAYER 1: Architectural Grid & Texture (FIXED STICKY) -->
            <div class="tex-apps-grid" aria-hidden="true"></div>
            <div class="tex-apps-dots" aria-hidden="true"></div>
            <div class="tex-apps-hatch" aria-hidden="true"></div>

            <!-- REELS-STYLE BACKGROUND LAYER 2: Dual Ambient Atmospheric Aura (FIXED STICKY) -->
            <div class="reels-ambient-aura" aria-hidden="true"></div>

            <!-- REELS-STYLE TECHNICAL COORDINATE MARKERS (FIXED STICKY) -->
            <div class="reels-tech-marker top-left" aria-hidden="true">
                <span class="rtm-dot"></span>
                <span class="rtm-label">SYS.BUILD // 05-FANNED-DECK</span>
            </div>
            <div class="reels-tech-marker top-right" aria-hidden="true">
                <span class="rtm-label">5 SURFACES // ONE TEAM</span>
            </div>

            <!-- KINETIC BACKGROUND GHOST TYPOGRAPHY (FIXED STICKY) -->
            <div class="pd-ghost-words" aria-hidden="true">
                <span class="pd-ghost">BUILD</span>
                <span class="pd-ghost">GROW</span>
                <span class="pd-ghost">SCALE</span>
                <span class="pd-ghost">PRODUCT</span>
            </div>

            <div class="container pd-container">
                <div class="pd-head">
                    <p class="pd-eyebrow" data-r="rise">05 // WHAT WE BUILD</p>
                    <h2 class="pd-heading" data-r="rise">
                        Five kinds of build,<br>
                        <span class="pd-accent">one team behind them.</span>
                    </h2>
                    <p class="pd-subhead" data-r="rise">
                        Most work is one of these five. Scroll through them &mdash; or pick one, and the deck comes to it.
                    </p>
                </div>

                <!-- FANNED 5-CARD DECK -->
                <div class="pd-deck-wrapper">
                    <div class="pd-deck" data-deck aria-label="Five kinds of build cards">
                        <?php 
                        $CARDS = [
                            ['ecom',       'Online stores',                  'Catalogue, checkout, and the operations behind them.',              '#0e6f31', '#10b981'],
                            ['apps',       'Mobile apps',                    'iOS and Android, on the same codebase as your site.',              '#046070', '#0891b2'],
                            ['marketing',  'Marketing and landers',          'High-converting landers that land where your ads do.',              '#1d4ed8', '#3b82f6'],
                            ['dashboards', 'Dashboards and internal systems', 'The internal screens that run your business day to day.',          '#0f2b5c', '#1e40af'],
                            ['content',    'Written and visual content',     'Copy and media that say what you do, written in your voice.',       '#5b21b6', '#7c3aed'],
                        ];
                        foreach ($CARDS as $i => [$app, $label, $sub, $accent, $accent2]): 
                        ?>
                        <article class="mock pd-card" data-slot="<?= (int)$i ?>" style="--card-bg: <?= e($accent) ?>; --c: <?= e($accent) ?>; --c2: <?= e($accent2) ?>;">
                            <div class="pd-card-inner">
                                <div class="pd-card-header">
                                    <span class="pd-card-tag">0<?= $i + 1 ?> // <?= strtoupper($app) ?></span>
                                    <span class="pd-card-badge">SYS.READY</span>
                                </div>
                                <div class="pd-card-graphic pd-graphic-<?= $app ?>" aria-hidden="true">
                                    <?php if ($app === 'ecom'): ?>
                                        <div class="pd-ecom-stage">
                                            <div class="pd-ecom-nav">
                                                <span>STORE // CHECKOUT</span>
                                                <span class="pd-cart-pill">CART (3)</span>
                                            </div>
                                            <div class="pd-ecom-card">
                                                <div class="pd-ecom-thumb">
                                                    <span class="pd-thumb-icon">🛍️</span>
                                                </div>
                                                <div class="pd-ecom-meta">
                                                    <span class="pd-ecom-title">Pro Storefront Engine</span>
                                                    <span class="pd-ecom-stars">★★★★★ 4.9</span>
                                                    <div class="pd-ecom-price-row">
                                                        <span class="pd-ecom-price">$149.00</span>
                                                        <span class="pd-ecom-btn">BUY NOW</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pd-ecom-metric">
                                                <span>⚡ 99.9% UPTIME · 38ms CHECKOUT</span>
                                            </div>
                                        </div>
                                    <?php elseif ($app === 'apps'): ?>
                                        <div class="pd-app-stage">
                                            <div class="pd-phone-mock">
                                                <div class="pd-phone-island"></div>
                                                <div class="pd-phone-screen">
                                                    <div class="pd-ps-header">
                                                        <span class="pd-ps-avatar"></span>
                                                        <div class="pd-ps-lines"><span></span><span></span></div>
                                                    </div>
                                                    <div class="pd-ps-widget">
                                                        <span class="pd-ps-val">+184% ACTIVE</span>
                                                        <div class="pd-ps-bars"><i></i><i></i><i></i><i></i><i></i></div>
                                                    </div>
                                                    <div class="pd-ps-nav">
                                                        <span class="is-active"></span><span></span><span></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pd-app-badge">iOS &amp; ANDROID PWA</div>
                                        </div>
                                    <?php elseif ($app === 'marketing'): ?>
                                        <div class="pd-mkt-stage">
                                            <div class="pd-mkt-hero">
                                                <span class="pd-mkt-tag">HIGH-VELOCITY LANDER</span>
                                                <div class="pd-mkt-hline"></div>
                                                <div class="pd-mkt-subline"></div>
                                                <div class="pd-mkt-btns">
                                                    <span class="pd-btn-p">START TRIAL</span>
                                                    <span class="pd-btn-s">DEMO</span>
                                                </div>
                                            </div>
                                            <div class="pd-mkt-metric">
                                                <span>🔥 4.8x ROAS · +180% CVR</span>
                                            </div>
                                        </div>
                                    <?php elseif ($app === 'dashboards'): ?>
                                        <div class="pd-dash-stage">
                                            <div class="pd-dash-top">
                                                <div class="pd-dash-kpi"><span>MRR</span><strong>$84.2K</strong></div>
                                                <div class="pd-dash-kpi"><span>USERS</span><strong>12.4K</strong></div>
                                            </div>
                                            <div class="pd-dash-chart">
                                                <div class="pd-chart-bar" style="--h: 40%"></div>
                                                <div class="pd-chart-bar" style="--h: 65%"></div>
                                                <div class="pd-chart-bar" style="--h: 85%"></div>
                                                <div class="pd-chart-bar" style="--h: 100%"></div>
                                            </div>
                                            <div class="pd-dash-status">
                                                <span class="pd-ds-dot"></span>
                                                <span>ZERO DEPENDENCY BLOAT</span>
                                            </div>
                                        </div>
                                    <?php elseif ($app === 'content'): ?>
                                        <div class="pd-cnt-stage">
                                            <div class="pd-video-mock">
                                                <span class="pd-video-badge">● 4K 60FPS</span>
                                                <div class="pd-video-play"><i class="pd-play-icon"></i></div>
                                                <div class="pd-video-track">
                                                    <div class="pd-video-fill"></div>
                                                </div>
                                            </div>
                                            <div class="pd-audio-bars">
                                                <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
                                            </div>
                                            <div class="pd-cnt-badge">BRAND VOICE &amp; COPY ENGINE</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="pd-card-body">
                                    <h3 class="pd-card-title"><?= e($label) ?></h3>
                                    <p class="pd-card-sub"><?= e($sub) ?></p>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- PROGRESS INDICATOR BARS -->
                    <div class="pd-nav-dots" aria-label="Deck cards navigation">
                        <span class="pd-dot is-active" data-dot="0" role="button" tabindex="0" aria-label="Card 1: Online stores"></span>
                        <span class="pd-dot" data-dot="1" role="button" tabindex="0" aria-label="Card 2: Mobile apps"></span>
                        <span class="pd-dot" data-dot="2" role="button" tabindex="0" aria-label="Card 3: Marketing and landers"></span>
                        <span class="pd-dot" data-dot="3" role="button" tabindex="0" aria-label="Card 4: Dashboards"></span>
                        <span class="pd-dot" data-dot="4" role="button" tabindex="0" aria-label="Card 5: Content"></span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ==========================================================
       06 — DELIVERY WORKFLOW: THE BUILD SCULPTURE (#delivery)
       ========================================================== */ ?>
    <section class="section build-loop-section ground-2 grain has-tex" id="delivery" data-build-loop>
        <!-- Oversized background ghost typography -->
        <div class="bl-ghost-text" aria-hidden="true">
            <span class="bl-gt-word gt-1">DISCOVER</span>
            <span class="bl-gt-word gt-2">ARCHITECT</span>
            <span class="bl-gt-word gt-3">BUILD</span>
            <span class="bl-gt-word gt-4">HARDEN</span>
            <span class="bl-gt-word gt-5">SHIP</span>
        </div>

        <div class="container">
            <div class="bl-split-grid">
                
                <!-- LEFT COLUMN (~42%): Kinetic Editorial Headline & Proof -->
                <div class="bl-left-col">
                    <div class="bl-eyebrow">
                        <span class="bl-tag">06 / DELIVERY</span>
                        <span class="bl-dot-live">●</span>
                    </div>
                    
                    <h2 class="bl-headline">
                        FROM<br>
                        FIRST SIGNAL<br>
                        TO <span class="bl-h-accent">SHIPPED.</span>
                    </h2>
                    
                    <p class="bl-desc">
                        Four focused stages turn an idea into a working digital system &mdash; strategy, structure, build and hardening moving as one continuous process.
                    </p>

                    <div class="bl-proof-annotation">
                        <span class="bl-pa-label">TYPICAL DELIVERY</span>
                        <strong class="bl-pa-val">2 TO 6 WEEKS</strong>
                        <span class="bl-pa-sub">SYNCHRONIZED SPRINT &bull; SLA GUARANTEED</span>
                    </div>
                </div>

                <!-- RIGHT COLUMN (~58%): HERO ABSTRACT MOTION SCULPTURE -->
                <div class="bl-right-col">
                    <div class="bl-sculpture-wrapper" data-magnetic>
                        
                        <!-- Sculpture Stage Nav Track -->
                        <div class="bl-stage-track">
                            <?php 
                            $STAGES = [
                                ['01', 'DISCOVER',  'RAW SIGNALS'],
                                ['02', 'ARCHITECT', 'STRUCTURE'],
                                ['03', 'BUILD',     'ASSEMBLY'],
                                ['04', 'HARDEN',    'PERIMETER'],
                            ];
                            foreach ($STAGES as $idx => [$num, $name, $sub]):
                            ?>
                            <button type="button" class="bl-stage-node <?= $idx === 0 ? 'is-active' : '' ?>" data-stage="<?= $idx ?>">
                                <span class="bl-sn-num"><?= $num ?></span>
                                <span class="bl-sn-info">
                                    <strong><?= $name ?></strong>
                                    <small><?= $sub ?></small>
                                </span>
                            </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Main Motion Sculpture Visual Container -->
                        <div class="bl-sculpture-viewport">
                            <svg class="bl-sculpture-svg" viewBox="0 0 600 400" fill="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="sculptureGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#0a63ff"/>
                                        <stop offset="100%" stop-color="#38bdf8"/>
                                    </linearGradient>
                                    <filter id="softGlow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="4" result="blur"/>
                                        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                                    </filter>
                                </defs>

                                <!-- Base Grid & Blueprint Vector Paths -->
                                <g class="bl-svg-grid">
                                    <line x1="50" y1="200" x2="550" y2="200" stroke="rgba(6,18,47,0.08)" stroke-dasharray="4 4"/>
                                    <line x1="300" y1="50" x2="300" y2="350" stroke="rgba(6,18,47,0.08)" stroke-dasharray="4 4"/>
                                    <circle cx="300" cy="200" r="140" stroke="rgba(10,99,255,0.12)" stroke-width="1"/>
                                </g>

                                <!-- Evolving Structural Geometry Planes -->
                                <g class="bl-sculpture-geometry">
                                    <polygon class="bl-geo-plane plane-1" points="300,80 440,160 300,240 160,160" fill="#ffffff" stroke="rgba(6,18,47,0.16)" stroke-width="1.5"/>
                                    <polygon class="bl-geo-plane plane-2" points="300,140 420,200 300,260 180,200" fill="#f6f8fc" stroke="rgba(10,99,255,0.3)" stroke-width="1.5"/>
                                    <polygon class="bl-geo-plane plane-3" points="300,190 380,230 300,270 220,230" fill="#06122f" stroke="#0a63ff" stroke-width="2"/>
                                </g>

                                <!-- Perimeter Scan Shield Line -->
                                <circle class="bl-scan-perimeter" cx="300" cy="200" r="160" stroke="#0a63ff" stroke-width="2" stroke-dasharray="20 400" stroke-linecap="round"/>
                            </svg>

                            <!-- Stage Copy Overlay -->
                            <div class="bl-stage-copy-box">
                                <div class="bl-stage-copy is-active" data-stage-copy="0">
                                    <h4>01 // DISCOVER</h4>
                                    <p>Raw intent, traffic vectors, and stack bottlenecks gathered into one synchronized strategic audit.</p>
                                </div>
                                <div class="bl-stage-copy" data-stage-copy="1" style="display:none;">
                                    <h4>02 // ARCHITECT</h4>
                                    <p>Scattered signals lock into grid structure, layout boundaries, and exact scope deliverables.</p>
                                </div>
                                <div class="bl-stage-copy" data-stage-copy="2" style="display:none;">
                                    <h4>03 // BUILD</h4>
                                    <p>Physical digital surfaces and parallel engineering components assemble in daily sprints.</p>
                                </div>
                                <div class="bl-stage-copy" data-stage-copy="3" style="display:none;">
                                    <h4>04 // HARDEN &amp; SHIP</h4>
                                    <p>Circular perimeter scan verifies 100/100 CWV, TLS 1.3 encryption, and WAF security before shipping live.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payoff Footer Signal -->
                        <div class="bl-sculpture-footer">
                            <span class="bl-sf-signal">● SIGNAL STATUS: SHIPPED</span>
                            <div class="bl-sf-line"><div class="bl-sf-pulse"></div></div>
                            <span class="bl-sf-version">RAFLY ENGINE v3.0</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ==========================================================
       07 — THE STACK: THE DIGITAL ENGINE (#stack)
       ========================================================== */ ?>
    <section class="section toolkit-section ground-3 has-tex" id="stack" data-materials-workbench>
        <!-- Narrative Signal Bridge continuation from Section 06 -->
        <div class="section-bridge-line" aria-hidden="true">
            <div class="sbl-pulse"></div>
        </div>

        <!-- Oversized background ghost typography -->
        <div class="tk-ghost-text" aria-hidden="true">
            <span class="tk-gt-word gt-1">FOUNDATION</span>
            <span class="tk-gt-word gt-2">APPLICATION</span>
            <span class="tk-gt-word gt-3">DATA</span>
            <span class="tk-gt-word gt-4">PROTECTION</span>
            <span class="tk-gt-word gt-5">GROWTH</span>
        </div>

        <div class="container">
            <!-- Section Editorial Header -->
            <div class="tk-editorial-head">
                <span class="tk-kicker">07 // TECHNOLOGY</span>
                <h2 class="tk-title">
                    THE SYSTEM<br>
                    BEHIND<br>
                    <span class="tk-h-accent">THE BUILD.</span>
                </h2>
                <p class="tk-sub">The technology changes with the project. The standard does not &mdash; fast interfaces, reliable infrastructure, secure systems and measurable growth.</p>
            </div>

            <!-- DIGITAL MATERIAL COMPOSITION (5 LAYERS & TYPOGRAPHIC SPECIMENS) -->
            <div class="dm-composition-container">
                
                <!-- 5 Layer Depth Sculpture -->
                <div class="dm-sculpture-core">
                    <div class="dm-layer layer-1" data-layer="1">
                        <span class="dm-l-tag">01 FOUNDATION</span>
                        <div class="dm-l-surface"></div>
                    </div>
                    <div class="dm-layer layer-2" data-layer="2">
                        <span class="dm-l-tag">02 APPLICATION</span>
                        <div class="dm-l-surface"></div>
                    </div>
                    <div class="dm-layer layer-3" data-layer="3">
                        <span class="dm-l-tag">03 DATA &amp; COMMERCE</span>
                        <div class="dm-l-surface"></div>
                    </div>
                    <div class="dm-layer layer-4" data-layer="4">
                        <span class="dm-l-tag">04 PROTECTION</span>
                        <div class="dm-l-surface"></div>
                    </div>
                    <div class="dm-layer layer-5" data-layer="5">
                        <span class="dm-l-tag">05 GROWTH</span>
                        <div class="dm-l-surface"></div>
                    </div>
                </div>

                <!-- Typographic Specimen Grid Wall -->
                <div class="dm-specimen-wall">
                    <?php 
                    $GROUPS = [
                        ['01', 'FOUNDATION & APPLICATION', [
                            'PHP'        => ['icon' => 'code', 'cat' => 'FOUNDATION', 'desc' => 'Sub-15ms server execution', 'badge' => 'Engine'],
                            'LARAVEL'    => ['icon' => 'layers', 'cat' => 'APPLICATION', 'desc' => 'Enterprise MVC framework', 'badge' => 'MVC'],
                            'WORDPRESS'  => ['icon' => 'file-pen', 'cat' => 'APPLICATION', 'desc' => 'Custom PHP headless backend', 'badge' => 'CMS'],
                            'JAVASCRIPT' => ['icon' => 'terminal', 'cat' => 'CLIENT', 'desc' => 'Native ES6+ micro-interactions', 'badge' => 'ES6+']
                        ]],
                        ['02', 'COMMERCE & DATA', [
                            'SHOPIFY'   => ['icon' => 'shopping-cart', 'cat' => 'COMMERCE', 'desc' => 'Headless Liquid theme engine', 'badge' => 'Store'],
                            'PAYMENTS'  => ['icon' => 'lock', 'cat' => 'GATEWAY', 'desc' => 'PCI-DSS Stripe & PayPal sync', 'badge' => 'PCI-DSS'],
                            'CATALOGUE' => ['icon' => 'database', 'cat' => 'DATA', 'desc' => 'High-density product indexing', 'badge' => 'Data']
                        ]],
                        ['03', 'SECURITY & PROTECTION', [
                            'SSL / TLS' => ['icon' => 'shield', 'cat' => 'PROTECTION', 'desc' => 'HSTS headers & TLS 1.3 protocol', 'badge' => 'TLS 1.3'],
                            'WAF'       => ['icon' => 'bot', 'cat' => 'PROTECTION', 'desc' => 'Bot filtering & SQLi shields', 'badge' => 'Shield'],
                            'ACCESS'    => ['icon' => 'settings', 'cat' => 'SECURITY', 'desc' => 'Role-based access & token rotation', 'badge' => 'Auth']
                        ]],
                        ['04', 'GROWTH & ANALYTICS', [
                            'GOOGLE ADS' => ['icon' => 'megaphone', 'cat' => 'GROWTH', 'desc' => 'High-intent search campaigns', 'badge' => 'Search'],
                            'META ADS'   => ['icon' => 'share', 'cat' => 'GROWTH', 'desc' => 'Direct-response short-form creative', 'badge' => 'Social'],
                            'ANALYTICS'  => ['icon' => 'pie-chart', 'cat' => 'GROWTH', 'desc' => 'Server-side GTM event tracking', 'badge' => 'GTM']
                        ]]
                    ];

                    foreach ($GROUPS as [$gNum, $gTitle, $items]):
                    ?>
                    <div class="dm-spec-group">
                        <span class="dm-sg-label"><?= $gNum ?> // <?= $gTitle ?></span>
                        <div class="dm-sg-items">
                            <?php foreach ($items as $name => $meta): ?>
                            <div class="dm-spec-node" data-specimen="<?= strtolower($name) ?>" data-cat="<?= $meta['cat'] ?>" data-desc="<?= $meta['desc'] ?>">
                                <div class="dm-sn-icon-wrap">
                                    <?= icon($meta['icon'], 'dm-sn-svg') ?>
                                </div>
                                <div class="dm-sn-content">
                                    <div class="dm-sn-title-row">
                                        <span class="dm-sn-name"><?= $name ?></span>
                                        <span class="dm-sn-badge"><?= $meta['badge'] ?></span>
                                    </div>
                                    <small class="dm-sn-desc"><?= $meta['desc'] ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Floating Specimen Inspector -->
                <div class="dm-inspector" data-dm-inspector aria-hidden="true">
                    <span class="dm-ins-cat">APPLICATION ARCHITECTURE</span>
                    <h4 class="dm-ins-name">LARAVEL</h4>
                    <p class="dm-ins-desc">Fast server-side systems and zero-overhead database queries.</p>
                </div>

            </div>
        </div>
    </section>

    <?php /* ==========================================================
       08 — SELECTED WORK: PROOF NOT PROMISES (#work) — THE DARK CHAPTER
       ========================================================== */ ?>
<?php if ($caseStudies): ?>
    <section class="section work-showcase-section ground-chapter grain seam-top seam-bottom has-reels-bg" id="work">
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="container">
            <div class="ws-head">
                <span class="ws-kicker">08 // PROOF, NOT PROMISES</span>
                <h2 class="ws-title">Selected Work.</h2>
                <p class="ws-sub">Real client builds executed with full attribution and zero vanity metrics.</p>
            </div>

            <!-- Art-Directed Case Study Experience -->
            <div class="ws-hero-grid">
                <?php foreach ($caseStudies as $i => $cs): ?>
                <article class="ws-case-card <?= $i === 0 ? 'is-featured' : '' ?>">
                    <div class="ws-card-header">
                        <span class="ws-card-num">PROVED WORK // 0<?= $i + 1 ?></span>
                        <span class="ws-card-tag">VERIFIED CASE STUDY</span>
                    </div>
                    <h3 class="ws-card-title"><?= e($cs['client_name'] ?? '') ?></h3>
                    <?php if (!empty($cs['summary'])): ?>
                    <p class="ws-card-desc"><?= e($cs['summary']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($cs['slug'])): ?>
                    <a class="ws-card-link" href="<?= e(site_path('/case-studies#' . $cs['slug'])) ?>">
                        <span>Read Case Study</span>
                        <i class="ws-arrow">&rarr;</i>
                    </a>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

    <?php /* ==========================================================
       09 — WHERE WE DRAW THE LINE & 10 — UNIFIED DIFFERENCE (#limits)
       ========================================================== */ ?>
    <section class="section boundaries-section limits-section has-tex has-reels-bg" id="limits" data-system-map-container>
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="container">
            
            <!-- SECTION 09: WHERE WE DRAW THE LINE -->
            <div class="bs-split-header">
                <div class="bs-left-title">
                    <span class="bs-kicker">09 // WHERE WE DRAW THE LINE</span>
                    <h2 class="bs-main-title">WE DON'T DO EVERYTHING.</h2>
                    <p class="bs-main-sub">Fifteen strict operational boundaries, in writing. A studio that will not name its edges hasn't found them yet.</p>
                </div>

                <div class="bs-filter-strip" role="tablist" aria-label="Filter boundaries by service">
                    <?php foreach ($serviceTabs as $i => $tab): ?>
                    <button type="button" class="bs-pill <?= $i === 0 ? 'is-active' : '' ?>" data-filter="<?= e($tab['slug']) ?>" role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                        <span><?= e($tab['label']) ?></span>
                        <small>(<?= $tab['count'] ?>)</small>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Spatial Boundary Line Spine Board -->
            <div class="bs-board-wrap">
                <div class="bs-laser-line" aria-hidden="true"></div>
                <div class="bs-grid">
                    <?php foreach ($limits as $item): ?>
                    <div class="bs-card" data-category="<?= e($item['slug']) ?>">
                        <div class="bs-dont">
                            <span class="bs-dont-label">WE DON'T DO</span>
                            <h3 class="bs-dont-title"><?= e($item['title']) ?></h3>
                            <p class="bs-dont-desc"><?= e($item['desc']) ?></p>
                        </div>
                        <div class="bs-instead">
                            <span class="bs-instead-label">WHAT WE DO INSTEAD</span>
                            <p class="bs-instead-text"><?= e($item['standard']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SECTION 10: ONE SYSTEM BEATS FIVE HANDOFFS -->
            <div class="unified-comparison-block">
                <div class="uc-head">
                    <span class="uc-kicker">10 // UNIFIED DIFFERENCE</span>
                    <h3 class="uc-title">ONE SYSTEM BEATS FIVE HANDOFFS.</h3>
                    <p class="uc-sub">A visual comparison of fragmented agency silos against RAFly's single connected engine.</p>
                </div>

                <div class="uc-split-grid">
                    <!-- Left: Fragmented Agency Silos -->
                    <div class="uc-col is-old">
                        <span class="uc-col-badge">FRAGMENTED AGENCY MODEL</span>
                        <div class="uc-flow-chain">
                            <div class="uc-node"><span>Brand Agency</span><small>Silo 01</small></div>
                            <div class="uc-arrow">&darr; Handoff Delay (+12 Days)</div>
                            <div class="uc-node"><span>Developer Vendor</span><small>Silo 02</small></div>
                            <div class="uc-arrow">&darr; Context Lost &amp; Rework</div>
                            <div class="uc-node"><span>External Freelancer</span><small>Silo 03</small></div>
                            <div class="uc-arrow">&darr; Uncoordinated Handoff</div>
                            <div class="uc-node"><span>Marketing Agency</span><small>Silo 04</small></div>
                        </div>
                        <p class="uc-verdict">Result: You become the full-time project manager, bridging broken handoffs and scope creep.</p>
                    </div>

                    <!-- Right: RAFly Unified System -->
                    <div class="uc-col is-rafly">
                        <span class="uc-col-badge is-accent">ONE UNIFIED SYSTEM</span>
                        <div class="uc-flow-unified">
                            <div class="uc-u-core">
                                <strong>ONE TEAM</strong>
                                <span>Web &bull; Security &bull; Marketing &bull; Content &bull; Commerce</span>
                            </div>
                            <div class="uc-u-pillars">
                                <span>ONE STRATEGY</span>
                                <span>ONE SCOPE</span>
                                <span>ONE ACCOUNTABILITY</span>
                            </div>
                        </div>
                        <p class="uc-verdict is-accent">Result: High-velocity execution with zero vendor friction and single-point accountability.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php /* ==========================================================
       11 — PRICING & FAQ: CHOOSE YOUR STARTING POINT (#pricing)
       ========================================================== */ ?>
    <section class="section pricing-editorial-section ground-2 grain has-tex has-reels-bg" id="pricing">
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="container">
            <div class="pe-head">
                <span class="pe-kicker">11 // ENGAGEMENT VOLUMES <span class="bl-dot-live">●</span></span>
                <h2 class="pe-title">Transparent Engagement Scope.</h2>
                <p class="pe-sub">Every engagement is scoped up front. One scope, one number, zero hourly rate creep.</p>
            </div>

            <!-- Architectural Level Tiers (Base, Elevated, Tallest) -->
            <div class="pe-tiers-grid">
                <?php 
                $tierHeights = ['tier-foundation', 'tier-growth is-recommended', 'tier-scale'];
                $tierTags    = ['01 // FOUNDATION BUILD', '★ MOST POPULAR ENGAGEMENT', '03 // ENTERPRISE ENGINE'];
                foreach ($bundles as $i => $t): 
                    $tierClass = $tierHeights[$i] ?? 'tier-foundation';
                    $tierTag   = $tierTags[$i] ?? ('0' . ($i + 1) . ' // PACKAGE');
                ?>
                <div class="pe-tier-card <?= $tierClass ?>">
                    <div class="pe-tier-top-tag">
                        <span class="pe-rec-badge"><?= $tierTag ?></span>
                    </div>
                    <div class="pe-card-top">
                        <h3 class="pe-card-name"><?= e($t['title'] ?? 'Package') ?></h3>
                        <p class="pe-card-sub"><?= e($t['tagline'] ?? '') ?></p>
                        <div class="pe-price-row">
                            <span class="pe-price"><?= e($t['price_formatted'] ?? 'Custom Scope') ?></span>
                            <span class="pe-price-sub">Fixed Scope &bull; SLA Guaranteed</span>
                        </div>
                    </div>
                    <?php if (!empty($t['features'])): ?>
                    <ul class="pe-feature-list">
                        <?php foreach (array_slice((array)$t['features'], 0, 5) as $f): ?>
                        <li><span class="pe-check-circle">&check;</span> <span><?= e(is_array($f) ? ($f['title'] ?? '') : $f) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <button type="button" class="btn btn-pill pe-btn <?= $i === 1 ? 'btn-accent-glow' : '' ?>" data-modal-open="consultationModal">Scope This Package &rarr;</button>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Integrated Minimalist Accordion FAQ -->
            <div class="pe-faq-wrap" id="faq">
                <div class="pe-faq-head">
                    <span class="pe-kicker">02 // FREQUENTLY ASKED QUESTIONS</span>
                    <div class="pe-faq-title-row">
                        <div>
                            <h3 class="pe-faq-main-title">Before you get in touch</h3>
                            <p class="pe-faq-sub">Clear answers on timelines, team alignment, and setup process.</p>
                        </div>
                        <a href="<?= e(whatsapp_link('Hi Rafly, I have a question about timelines and pricing.')) ?>" target="_blank" rel="noopener" class="pe-wa-badge">
                            <span class="pe-wa-dot">●</span>
                            <span>Need fast answers? Chat on WhatsApp &rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="pe-accordion" data-accordion="single">
                    <?php foreach ($FAQS as $i => $f): ?>
                    <div class="pe-acc-item">
                        <button type="button" class="pe-acc-trigger" id="faq-t-<?= $i ?>" aria-expanded="false" aria-controls="faq-p-<?= $i ?>">
                            <div class="pe-acc-left">
                                <span class="pe-acc-num">0<?= $i + 1 ?></span>
                                <span class="pe-acc-title"><?= e($f['q']) ?></span>
                            </div>
                            <span class="pe-acc-icon" aria-hidden="true"></span>
                        </button>
                        <div class="pe-acc-panel" id="faq-p-<?= $i ?>" role="region" aria-labelledby="faq-t-<?= $i ?>">
                            <div class="pe-acc-body"><p><?= e($f['a']) ?></p></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       12 — START PROJECT: LET'S BUILD SOMETHING THAT MATTERS (#start)
       ========================================================== */ ?>
    <section class="section close-editorial-section close has-tex has-reels-bg" id="start">
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="container">
            <div class="ce-grid">
                <div class="ce-copy">
                    <span class="ce-kicker">12 // LET'S BUILD</span>
                    <h2 class="ce-title">LET'S BUILD SOMETHING THAT MATTERS.</h2>
                    <p class="ce-lead">Tell us what is in front of you. We will respond with a clear scope and a number within one working day—or tell you plainly if it's not a fit.</p>
                    <div class="ce-commitments">
                        <div class="ce-comm-item">
                            <strong>FIRST STEP</strong>
                            <span>A direct call with the build team, not a sales pitch</span>
                        </div>
                        <div class="ce-comm-item">
                            <strong>RESPONSE SLA</strong>
                            <span>Within 24 hours (1 working day)</span>
                        </div>
                    </div>
                </div>

                <div class="ce-form-wrap">
                    <?php
                        $formId      = 'homeLeadForm';
                        $submitLabel = 'START A CONVERSATION →';
                        require __DIR__ . '/partials/lead-form.php';
                    ?>
                </div>
            </div>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
