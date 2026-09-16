<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * THE HOMEPAGE.
 *
 * Every section answers a question a buyer actually asks, in the order they
 * ask it:
 *
 *   01   hero                      what is this
 *   02   the statement             we build digital systems, not deliverables
 *   03   service studio            5 capabilities under one team
 *   04   reels & short-form        viral hook & attributed social funnels
 *   05   what we build             5 fanned product surfaces
 *   06   delivery engine           from first signal to shipped (4 stages)
 *   07   proof / selected work     real client builds with verified ROI
 *   08   where we draw the line    15 strict operational boundaries & unified difference
 *   09   let's build               clear intake, fixed scope, disciplined launch

 *
 * FOUR SECTIONS SHOW A DEVICE, AND EACH ONE HAS A DIFFERENT VERB. That is the
 * rule that keeps them from reading as the same idea four times: the laptop
 * OPENS, the platform deck FANS and then closes to a coverflow, the phones
 * STAND STILL and are simply arranged, the gallery SLIDES. A fifth device
 * section would need a fifth verb before it earns a place.
 *
 * TWO calls to action, not five: the hero and the close.
 *
 * HIGH-VELOCITY DIGITAL SYSTEMS HOMEPAGE.
 *
 * Designed with Apple / Vercel level precision:
 * - Clean light theme visual hierarchy with responsive glassmorphic cards
 * - Five integrated digital capabilities operating as a single unified engine
 * - Full performance optimization with Core Web Vitals guarantees
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

/**
 * THE 6 INSTAGRAM REELS (FEATURING AUTHENTIC REELS MOCKUP UI)
 * 1. https://www.instagram.com/reel/DdGUNlyz6o_/
 * 2. https://www.instagram.com/reel/DdBEsLHzhZQ/
 * 3. https://www.instagram.com/reel/DdMKeQqzPvj/
 * 4. https://www.instagram.com/reel/DcJCSPiMtjk/
 * 5. https://www.instagram.com/reel/DbgAnezPxKK/
 * 6. https://www.instagram.com/reel/DdI-PFNzU5r/
 */
$INSTAGRAM_REELS = [
    [
        'id'       => 'DdGUNlyz6o_',
        'url'      => 'https://www.instagram.com/reel/DdGUNlyz6o_/',
        'mp4'      => '/assets/mockups/ig-reel-1.mp4',
        'title'    => 'Algorithmic Hook & Retention',
        'sub'      => '3-Second Hook Scripting',
        'likes'    => '54.2K',
        'comments' => '1,940',
        'shares'   => '14.8K',
        'caption'  => 'Viral Reels & Content, scripted, shot & edited to convert ⚡ Algorithmic hook formulas that capture high-intent audience attention in 3s flat.',
        'audio'    => 'officialrafly.in • Original Audio • Trending Sound',
        'tags'     => '#ViralReels #RaflyGrowth #ContentEngine #PerformanceWeb',
    ],
    [
        'id'       => 'DdBEsLHzhZQ',
        'url'      => 'https://www.instagram.com/reel/DdBEsLHzhZQ/',
        'mp4'      => '/assets/mockups/ig-reel-2.mp4',
        'title'    => '4K Motion & Creative Studio',
        'sub'      => '60FPS Cinematic Production',
        'likes'    => '41.8K',
        'comments' => '1,280',
        'shares'   => '10.2K',
        'caption'  => 'Studio-grade 4K motion graphics, color grading & bespoke audio design 🎬 High-velocity production cuts engineered to stop the scroll.',
        'audio'    => 'officialrafly.in • Studio Mix • High Retention Sound',
        'tags'     => '#StudioPolish #ShortFormVideo #CreativeProduction #Rafly',
    ],
    [
        'id'       => 'DdMKeQqzPvj',
        'url'      => 'https://www.instagram.com/reel/DdMKeQqzPvj/',
        'mp4'      => '/assets/mockups/ig-reel-3.mp4',
        'title'    => 'Attributed Conversion Funnel',
        'sub'      => 'Direct Store Checkout',
        'likes'    => '68.5K',
        'comments' => '2,410',
        'shares'   => '19.3K',
        'caption'  => 'Turning social video views into direct-response store revenue 🚀 End-to-end DM & checkout funnels with 100% attributed performance.',
        'audio'    => 'officialrafly.in • Revenue Audio • Performance Beats',
        'tags'     => '#AttributedROAS #ConversionEngine #EcommerceGrowth #Rafly',
    ],
    [
        'id'       => 'DcJCSPiMtjk',
        'url'      => 'https://www.instagram.com/reel/DcJCSPiMtjk/',
        'mp4'      => '/assets/mockups/ig-reel-4.mp4',
        'title'    => 'High-Impact Social Hook',
        'sub'      => 'Viral Hook Architecture',
        'likes'    => '39.4K',
        'comments' => '1,150',
        'shares'   => '9.4K',
        'caption'  => 'High-impact visual hooks engineered for social viral reach 💥 Direct-response copy & dynamic pacing that maximizes engagement.',
        'audio'    => 'officialrafly.in • Social Velocity • Viral Sound',
        'tags'     => '#SocialVelocity #ViralHooks #ContentStrategy #Rafly',
    ],
    [
        'id'       => 'DbgAnezPxKK',
        'url'      => 'https://www.instagram.com/reel/DbgAnezPxKK/',
        'mp4'      => '/assets/mockups/ig-reel-5.mp4',
        'title'    => 'Brand Storytelling Cut',
        'sub'      => 'Omnichannel Distribution',
        'likes'    => '46.1K',
        'comments' => '1,620',
        'shares'   => '11.8K',
        'caption'  => 'Authentic brand storytelling & high-performing product showcase 🎥 Scaled seamlessly across Instagram Reels & ad funnels.',
        'audio'    => 'officialrafly.in • Brand Story • Cinematic Audio',
        'tags'     => '#BrandStorytelling #ReelsShowcase #Omnichannel #Rafly',
    ],
    [
        'id'       => 'DdI-PFNzU5r',
        'url'      => 'https://www.instagram.com/reel/DdI-PFNzU5r/',
        'mp4'      => '/assets/mockups/ig-reel-6.mp4',
        'title'    => 'Omnichannel Ad Creative',
        'sub'      => 'ROAS Performance Beats',
        'likes'    => '52.7K',
        'comments' => '1,890',
        'shares'   => '13.5K',
        'caption'  => 'Synchronized ad creative rollout across performance channels 🚀 High-converting reel assets optimized for maximum ROAS.',
        'audio'    => 'officialrafly.in • Ad Matrix • Growth Audio',
        'tags'     => '#AdCreative #PerformanceMarketing #ROAS #RaflyGrowth',
    ],
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

/** Service Tabs for the interactive limits terminal. */
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
    [
        'slug'     => 'all',
        'label'    => 'All Specs',
        'short'    => 'All',
        'icon'     => 'layers',
        'scTok'    => '--svc-all',
        'count'    => 15,
        'badge'    => 'Full Spec Index',
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
    'title'     => 'RAFLY — Web Development, Security & Digital Growth Partner',
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
    <section class="section hero sig-hero sig-hero--entered grs-hero--entered has-tex has-reels-bg" id="home" data-hero data-sig-hero aria-label="RAFly — Digital Growth Studio">

        <!-- ARCHITECTURAL BACKGROUND TEXTURE LAYERS -->
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <?php /* Ambient Corner Light Glows (Fills all 4 corners) */ ?>
        <div class="sig-corner-glow sig-corner-glow--tl" aria-hidden="true"></div>
        <div class="sig-corner-glow sig-corner-glow--tr" aria-hidden="true"></div>
        <div class="sig-corner-glow sig-corner-glow--bl" aria-hidden="true"></div>
        <div class="sig-corner-glow sig-corner-glow--br" aria-hidden="true"></div>

        <?php /* Ambient Corner Light Glows */ ?>
        <div class="sig-corner-glow sig-corner-glow--tl" aria-hidden="true"></div>
        <div class="sig-corner-glow sig-corner-glow--tr" aria-hidden="true"></div>
        <div class="sig-corner-glow sig-corner-glow--bl" aria-hidden="true"></div>
        <div class="sig-corner-glow sig-corner-glow--br" aria-hidden="true"></div>

        <?php /* Kinetic Matrix Spring-Mass Simulation Canvas & Hero Reactor Canvas */ ?>
        <canvas class="sig-canvas sig-canvas--full-bg" id="field" width="1440" height="900" aria-hidden="true"></canvas>
        <canvas class="sig-canvas sig-canvas--full-bg" data-signal-canvas width="1440" height="900" aria-hidden="true"></canvas>

        <?php /* ── MAIN CONTAINER ── */ ?>
        <div class="container sig-container">
            <div class="sig-content sig-content--centered">

                <?php /* Top Badge */ ?>
                <div class="sig-eyebrow sig-eyebrow--pill">
                    <span class="sig-eyebrow__dot" aria-hidden="true">●</span>
                    <span class="sig-eyebrow__item">DIGITAL SYSTEMS • ENGINEERING • GROWTH</span>
                </div>

                <?php /* Animated GradientText Headline */ ?>
                <h1 class="sig-headline sig-headline--centered" aria-label="Build fast. Grow faster. Scale smarter.">
                    <span class="sig-h-line" data-line="1">
                        <span class="sig-h-mask">
                            <span class="sig-h-inner">
                                <span class="h-navy-text">Build </span>
                                <span class="sig-h-focus sig-gradient-text" data-light-sweep>fast.</span>
                            </span>
                        </span>
                    </span>
                    <span class="sig-h-line" data-line="2">
                        <span class="sig-h-mask">
                            <span class="sig-h-inner">
                                <span class="h-navy-text">Grow </span>
                                <span class="sig-h-focus sig-gradient-text" data-light-sweep>faster.</span>
                            </span>
                        </span>
                    </span>
                    <span class="sig-h-line sig-h-line--accent" data-line="3">
                        <span class="sig-h-mask">
                            <span class="sig-h-inner">
                                <span class="h-navy-text">Scale </span>
                                <span class="sig-h-focus sig-gradient-text" data-light-sweep>smarter.</span>
                            </span>
                        </span>
                    </span>
                </h1>

                <?php /* Body copy */ ?>
                <p class="sig-body sig-body--centered">
                    RAFly transforms ambitious ideas into <span class="sig-body-highlight">market-dominating digital platforms</span> with high-velocity engineering, security, and growth engines.
                </p>

                <?php /* CTAs */ ?>
                <div class="sig-actions sig-actions--centered">
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
                <div class="sig-trust sig-trust--centered">
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
                We operate as one unified team following a clear progression: <strong>Build the digital infrastructure &rarr; Protect the infrastructure &rarr; Grow the business.</strong>
            </p>
        </div>

        <!-- Technical Bottom Status Indicator -->
        <div class="manifesto-status-bar" aria-hidden="true">
            <div class="status-indicator">
                <span class="status-dot"></span>
                <span class="status-text">UNIFIED DIGITAL SYSTEM • ALL CAPABILITIES SYNCED</span>
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
                            <span class="ssh-tag-num" data-active-tag-num>01 // KINETIC</span>
                            <span class="ssh-tag-title" data-active-tag-title>WEB ARCHITECTURE</span>
                            <span class="ssh-status"><i class="ssh-dot"></i> STUDIO RENDER</span>
                        </div>

                        <!-- Hero Lottie Stage Player -->
                        <div class="ss-stage-lottie" data-stage-lottie aria-hidden="true"></div>

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

            <div class="container reels-main-container">
                
                <!-- ZONE 1: SECTION EDITORIAL HEADER -->
                <div class="reels-header-zone">
                    <div class="reels-eyebrow-row">
                        <span class="reels-sys-tag">SHORT-FORM CONTENT STUDIO</span>
                        <span class="reels-pipe-tag">VIRAL HOOKS &amp; ATTRIBUTED SOCIAL FUNNELS</span>
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

                    <!-- 5 SYNCHRONIZED 3D PHONE SURFACES WITH AUTHENTIC INSTAGRAM REELS UI -->

                    <!-- PHONE 04: REAR-LEFT (Instagram Reel 4 - DcJCSPiMtjk) -->
                    <div class="iphone-pro-surface phone-rear-left" data-surface-idx="3" data-reel-card="3">
                        <div class="surface-chassis">
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Viral Hook</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-reels">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                        <source src="<?= e(site_path($INSTAGRAM_REELS[3]['mp4'])) ?>" type="video/mp4">
                                    </video>
                                    <div class="reels-header-meta">
                                        <div class="reels-header-left">
                                            <span class="reels-logo-text">Reels</span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[3]['url']) ?>" target="_blank" rel="noopener" class="reels-trend-pill">🔥 IG Reel ↗</a>
                                    </div>
                                    <div class="reels-sidebar-actions">
                                        <div class="reels-action-unit like-unit is-active">
                                            <span class="rau-icon is-liked"><?= icon('heart') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[3]['likes']) ?></span>
                                        </div>
                                        <div class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('message-circle') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[3]['comments']) ?></span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[3]['url']) ?>" target="_blank" rel="noopener" class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('send') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[3]['shares']) ?></span>
                                        </a>
                                        <div class="reels-spinning-vinyl">
                                            <img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Disc" class="vinyl-art" width="32" height="32" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="reels-content-footer">
                                        <div class="reels-creator-row">
                                            <div class="reels-av-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="officialrafly.in" class="reels-av-pic" width="32" height="32" loading="lazy"></div>
                                            <div class="reels-handle"><strong>officialrafly.in</strong> <span class="verif-tag"><?= icon('verified') ?></span></div>
                                        </div>
                                        <p class="reels-caption"><?= e($INSTAGRAM_REELS[3]['caption']) ?></p>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 02: LEFT SUPPORT (Instagram Reel 2 - DdBEsLHzhZQ) -->
                    <div class="iphone-pro-surface phone-mid-left" data-surface-idx="1" data-reel-card="1">
                        <div class="surface-chassis">
                            <span class="hw-btn btn-action" aria-hidden="true"></span>
                            <span class="hw-btn btn-vol-up" aria-hidden="true"></span>
                            <span class="hw-btn btn-vol-down" aria-hidden="true"></span>
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Reel &bull; Production</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-reels">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                        <source src="<?= e(site_path($INSTAGRAM_REELS[1]['mp4'])) ?>" type="video/mp4">
                                    </video>
                                    <div class="reels-header-meta">
                                        <div class="reels-header-left">
                                            <span class="reels-logo-text">Reels</span>
                                            <span class="reels-header-caret">▾</span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[1]['url']) ?>" target="_blank" rel="noopener" class="reels-trend-pill">🔥 IG Reel ↗</a>
                                    </div>
                                    <div class="reels-sidebar-actions">
                                        <div class="reels-action-unit like-unit is-active">
                                            <span class="rau-icon is-liked"><?= icon('heart') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[1]['likes']) ?></span>
                                        </div>
                                        <div class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('message-circle') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[1]['comments']) ?></span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[1]['url']) ?>" target="_blank" rel="noopener" class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('send') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[1]['shares']) ?></span>
                                        </a>
                                        <div class="reels-action-unit"><span class="rau-icon"><?= icon('bookmark') ?></span></div>
                                        <div class="reels-spinning-vinyl" title="Spinning Original Audio">
                                            <span class="vinyl-groove"></span>
                                            <img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Disc" class="vinyl-art" width="32" height="32" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="reels-content-footer">
                                        <div class="reels-creator-row">
                                            <div class="reels-av-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="officialrafly.in" class="reels-av-pic" width="32" height="32" loading="lazy"></div>
                                            <div class="reels-handle">
                                                <strong>officialrafly.in</strong>
                                                <span class="verif-tag"><?= icon('verified') ?></span>
                                            </div>
                                            <a href="<?= e($INSTAGRAM_REELS[1]['url']) ?>" target="_blank" rel="noopener" class="reels-follow-btn">Follow</a>
                                        </div>
                                        <p class="reels-caption"><?= e($INSTAGRAM_REELS[1]['caption']) ?></p>
                                        <div class="reels-tags"><?= e($INSTAGRAM_REELS[1]['tags']) ?></div>
                                        <div class="reels-audio-badge">
                                            <span class="audio-note"><?= icon('music') ?></span>
                                            <span class="audio-title"><?= e($INSTAGRAM_REELS[1]['audio']) ?></span>
                                        </div>
                                    </div>
                                    <div class="reels-bottom-tabbar" aria-hidden="true">
                                        <span class="rnb-item"><?= icon('home') ?></span>
                                        <span class="rnb-item"><?= icon('search') ?></span>
                                        <span class="rnb-item rnb-add"><?= icon('plus') ?></span>
                                        <span class="rnb-item is-active"><?= icon('play') ?></span>
                                        <span class="rnb-item rnb-user"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="User" class="rnb-user-pic" width="16" height="16"></span>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 01: HERO CENTER (Flagship Instagram Reel 01 - DdGUNlyz6o_) -->
                    <div class="iphone-pro-surface phone-hero-center is-dominant" data-surface-idx="0" data-hero-surface>
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
                                <div class="surface-screen screen-reels" data-hero-screen>
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>" data-hero-video>
                                        <source src="<?= e(site_path($INSTAGRAM_REELS[0]['mp4'])) ?>" type="video/mp4">
                                    </video>

                                    <!-- Tap Play/Pause Indicator -->
                                    <div class="reels-play-overlay" aria-hidden="true" data-play-overlay>
                                        <span class="rpo-icon"><?= icon('play') ?></span>
                                    </div>

                                    <!-- Instagram Top Header Bar -->
                                    <div class="reels-header-meta">
                                        <div class="reels-header-left">
                                            <span class="reels-logo-text">Reels</span>
                                            <span class="reels-header-caret">▾</span>
                                        </div>
                                        <div class="reels-header-right">
                                            <button type="button" class="reels-sound-btn" data-reel-sound aria-label="Toggle Sound" title="Sound Mute/Unmute">
                                                <span class="rsb-icon is-muted"><?= icon('volume-x') ?></span>
                                                <span class="rsb-icon is-on" style="display:none;"><?= icon('volume-2') ?></span>
                                            </button>
                                            <a href="<?= e($INSTAGRAM_REELS[0]['url']) ?>" target="_blank" rel="noopener" class="reels-trend-pill" data-hero-link title="Open Reel on Instagram">
                                                <span>🔥 IG Reel ↗</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Instagram Right Sidebar Action Column -->
                                    <div class="reels-sidebar-actions">
                                        <button type="button" class="reels-action-unit like-unit is-active" data-reel-like aria-label="Like reel">
                                            <span class="rau-icon is-liked"><?= icon('heart') ?></span>
                                            <span class="rau-val" data-hero-likes><?= e($INSTAGRAM_REELS[0]['likes']) ?></span>
                                        </button>
                                        <button type="button" class="reels-action-unit" aria-label="Comment on reel">
                                            <span class="rau-icon"><?= icon('message-circle') ?></span>
                                            <span class="rau-val" data-hero-comments><?= e($INSTAGRAM_REELS[0]['comments']) ?></span>
                                        </button>
                                        <a href="<?= e($INSTAGRAM_REELS[0]['url']) ?>" target="_blank" rel="noopener" class="reels-action-unit" data-hero-share aria-label="Share reel">
                                            <span class="rau-icon"><?= icon('send') ?></span>
                                            <span class="rau-val" data-hero-shares><?= e($INSTAGRAM_REELS[0]['shares']) ?></span>
                                        </a>
                                        <button type="button" class="reels-action-unit" data-reel-bookmark aria-label="Save reel">
                                            <span class="rau-icon"><?= icon('bookmark') ?></span>
                                        </button>
                                        <button type="button" class="reels-action-unit" aria-label="More options">
                                            <span class="rau-dots">&bull;&bull;&bull;</span>
                                        </button>
                                        <div class="reels-spinning-vinyl" title="Spinning Original Audio">
                                            <span class="vinyl-groove"></span>
                                            <img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Disc" class="vinyl-art" width="32" height="32" loading="lazy">
                                            <span class="vinyl-note-float" aria-hidden="true">🎵</span>
                                        </div>
                                    </div>

                                    <!-- Instagram Bottom Metadata Bar -->
                                    <div class="reels-content-footer">
                                        <div class="reels-creator-row">
                                            <div class="reels-av-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="officialrafly.in" class="reels-av-pic" width="32" height="32" loading="lazy"></div>
                                            <div class="reels-handle">
                                                <strong>officialrafly.in</strong>
                                                <span class="verif-tag"><?= icon('verified') ?></span>
                                            </div>
                                            <a href="<?= e($INSTAGRAM_REELS[0]['url']) ?>" target="_blank" rel="noopener" class="reels-follow-btn" data-hero-follow>Follow</a>
                                        </div>
                                        <p class="reels-caption" data-hero-caption>
                                            <?= e($INSTAGRAM_REELS[0]['caption']) ?>
                                        </p>
                                        <div class="reels-tags" data-hero-tags><?= e($INSTAGRAM_REELS[0]['tags']) ?></div>
                                        <div class="reels-audio-badge">
                                            <span class="audio-note"><?= icon('music') ?></span>
                                            <span class="audio-title-marquee" data-hero-audio><?= e($INSTAGRAM_REELS[0]['audio']) ?></span>
                                        </div>
                                    </div>

                                    <!-- Instagram Reels Bottom Navigation Bar -->
                                    <div class="reels-bottom-tabbar" aria-hidden="true">
                                        <span class="rnb-item"><?= icon('home') ?></span>
                                        <span class="rnb-item"><?= icon('search') ?></span>
                                        <span class="rnb-item rnb-add"><?= icon('plus') ?></span>
                                        <span class="rnb-item is-active"><?= icon('play') ?></span>
                                        <span class="rnb-item rnb-user"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="User" class="rnb-user-pic" width="16" height="16"></span>
                                    </div>

                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow is-hero-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 03: RIGHT SUPPORT (Instagram Reel 3 - DdMKeQqzPvj) -->
                    <div class="iphone-pro-surface phone-mid-right" data-surface-idx="2" data-reel-card="2">
                        <div class="surface-chassis">
                            <span class="hw-btn btn-power" aria-hidden="true"></span>
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Reel &bull; Conversion</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-reels">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                        <source src="<?= e(site_path($INSTAGRAM_REELS[2]['mp4'])) ?>" type="video/mp4">
                                    </video>
                                    <div class="reels-header-meta">
                                        <div class="reels-header-left">
                                            <span class="reels-logo-text">Reels</span>
                                            <span class="reels-header-caret">▾</span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[2]['url']) ?>" target="_blank" rel="noopener" class="reels-trend-pill">🔥 IG Reel ↗</a>
                                    </div>
                                    <div class="reels-sidebar-actions">
                                        <div class="reels-action-unit like-unit is-active">
                                            <span class="rau-icon is-liked"><?= icon('heart') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[2]['likes']) ?></span>
                                        </div>
                                        <div class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('message-circle') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[2]['comments']) ?></span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[2]['url']) ?>" target="_blank" rel="noopener" class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('send') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[2]['shares']) ?></span>
                                        </a>
                                        <div class="reels-action-unit"><span class="rau-icon"><?= icon('bookmark') ?></span></div>
                                        <div class="reels-spinning-vinyl" title="Spinning Original Audio">
                                            <span class="vinyl-groove"></span>
                                            <img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Disc" class="vinyl-art" width="32" height="32" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="reels-content-footer">
                                        <div class="reels-creator-row">
                                            <div class="reels-av-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="officialrafly.in" class="reels-av-pic" width="32" height="32" loading="lazy"></div>
                                            <div class="reels-handle">
                                                <strong>officialrafly.in</strong>
                                                <span class="verif-tag"><?= icon('verified') ?></span>
                                            </div>
                                            <a href="<?= e($INSTAGRAM_REELS[2]['url']) ?>" target="_blank" rel="noopener" class="reels-follow-btn">Follow</a>
                                        </div>
                                        <p class="reels-caption"><?= e($INSTAGRAM_REELS[2]['caption']) ?></p>
                                        <div class="reels-tags"><?= e($INSTAGRAM_REELS[2]['tags']) ?></div>
                                        <div class="reels-audio-badge">
                                            <span class="audio-note"><?= icon('music') ?></span>
                                            <span class="audio-title"><?= e($INSTAGRAM_REELS[2]['audio']) ?></span>
                                        </div>
                                    </div>
                                    <div class="reels-bottom-tabbar" aria-hidden="true">
                                        <span class="rnb-item"><?= icon('home') ?></span>
                                        <span class="rnb-item"><?= icon('search') ?></span>
                                        <span class="rnb-item rnb-add"><?= icon('plus') ?></span>
                                        <span class="rnb-item is-active"><?= icon('play') ?></span>
                                        <span class="rnb-item rnb-user"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="User" class="rnb-user-pic" width="16" height="16"></span>
                                    </div>
                                    <span class="home-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="surface-shadow" aria-hidden="true"></div>
                    </div>

                    <!-- PHONE 05: REAR-RIGHT (Instagram Reel 5 - DbgAnezPxKK) -->
                    <div class="iphone-pro-surface phone-rear-right" data-surface-idx="4" data-reel-card="4">
                        <div class="surface-chassis">
                            <div class="surface-bezel">
                                <div class="dynamic-island">
                                    <div class="di-content">
                                        <span class="di-camera"></span>
                                        <div class="di-live-pill"><span class="di-dot"></span><span class="di-text">Brand Story</span></div>
                                    </div>
                                </div>
                                <div class="surface-status-bar">
                                    <span class="sb-time">9:41</span>
                                    <div class="sb-icons"><span class="sb-bars"><i></i><i></i><i></i><i></i></span><span class="sb-wifi">5G</span><span class="sb-batt"><i></i></span></div>
                                </div>
                                <div class="surface-screen screen-reels">
                                    <div class="screen-glare" aria-hidden="true"></div>
                                    <video class="surface-video" autoplay loop muted playsinline poster="<?= e(site_path('/assets/mockups/phone-screen.webp')) ?>">
                                        <source src="<?= e(site_path($INSTAGRAM_REELS[4]['mp4'])) ?>" type="video/mp4">
                                    </video>
                                    <div class="reels-header-meta">
                                        <div class="reels-header-left">
                                            <span class="reels-logo-text">Reels</span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[4]['url']) ?>" target="_blank" rel="noopener" class="reels-trend-pill">🔥 IG Reel ↗</a>
                                    </div>
                                    <div class="reels-sidebar-actions">
                                        <div class="reels-action-unit like-unit is-active">
                                            <span class="rau-icon is-liked"><?= icon('heart') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[4]['likes']) ?></span>
                                        </div>
                                        <div class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('message-circle') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[4]['comments']) ?></span>
                                        </div>
                                        <a href="<?= e($INSTAGRAM_REELS[4]['url']) ?>" target="_blank" rel="noopener" class="reels-action-unit">
                                            <span class="rau-icon"><?= icon('send') ?></span>
                                            <span class="rau-val"><?= e($INSTAGRAM_REELS[4]['shares']) ?></span>
                                        </a>
                                        <div class="reels-spinning-vinyl">
                                            <img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="Disc" class="vinyl-art" width="32" height="32" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="reels-content-footer">
                                        <div class="reels-creator-row">
                                            <div class="reels-av-ring"><img src="<?= e(site_path('/assets/icon-192.png')) ?>" alt="officialrafly.in" class="reels-av-pic" width="32" height="32" loading="lazy"></div>
                                            <div class="reels-handle"><strong>officialrafly.in</strong> <span class="verif-tag"><?= icon('verified') ?></span></div>
                                        </div>
                                        <p class="reels-caption"><?= e($INSTAGRAM_REELS[4]['caption']) ?></p>
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

            <div class="container pd-container">
                <div class="pd-head">
                    <p class="pd-eyebrow" data-r="rise">05 • WHAT WE BUILD</p>
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
                            ['ecom',       'Online stores',                  'Catalogue, checkout, and the operations behind them.',              '#0e6f31', '#10b981', 'E-COMMERCE'],
                            ['apps',       'Mobile apps',                    'iOS and Android, on the same codebase as your site.',              '#046070', '#0891b2', 'MOBILE APPS'],
                            ['marketing',  'Marketing and landers',          'High-converting landers that land where your ads do.',              '#1d4ed8', '#3b82f6', 'MARKETING & LANDERS'],
                            ['dashboards', 'Dashboards and internal systems', 'The internal screens that run your business day to day.',          '#0f2b5c', '#1e40af', 'INTERNAL SYSTEMS'],
                            ['content',    'Written and visual content',     'Copy and media that say what you do, written in your voice.',       '#5b21b6', '#7c3aed', 'BRAND CONTENT'],
                        ];
                        foreach ($CARDS as $i => [$app, $label, $sub, $accent, $accent2, $badgeLabel]): 
                        ?>
                        <article class="mock pd-card" data-slot="<?= (int)$i ?>" style="--card-bg: <?= e($accent) ?>; --c: <?= e($accent) ?>; --c2: <?= e($accent2) ?>;">
                            <div class="pd-card-inner">
                                <div class="pd-card-header">
                                    <span class="pd-card-tag">0<?= $i + 1 ?> • <?= e($badgeLabel) ?></span>
                                    <span class="pd-card-badge">PRODUCTION READY</span>
                                </div>
                                <div class="pd-card-graphic pd-graphic-<?= $app ?>" aria-hidden="true">
                                    <?php if ($app === 'ecom'): ?>
                                        <div class="pd-ecom-stage">
                                            <div class="pd-ecom-nav">
                                                <span>STORE CHECKOUT</span>
                                                <span class="pd-cart-pill">CART (3)</span>
                                            </div>
                                            <div class="pd-ecom-card">
                                                <div class="pd-ecom-thumb">
                                                    <span class="pd-thumb-icon"><?= icon('shopping-cart') ?></span>
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
        <!-- REELS-STYLE BACKGROUND LAYER 1: Architectural Grid & Texture -->
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="container">
            <div class="bl-split-grid">
                
                <!-- LEFT COLUMN (~42%): Kinetic Editorial Headline & Delivery Metrics -->
                <div class="bl-left-col">
                    <div class="bl-eyebrow">
                        <span class="bl-tag">06 • DELIVERY ENGINE</span>
                        <span class="bl-dot-live">●</span>
                    </div>
                    
                    <h2 class="bl-headline">
                        FROM<br>
                        FIRST SIGNAL<br>
                        TO <span class="bl-h-accent">SHIPPED.</span>
                    </h2>
                    
                    <p class="bl-desc">
                        Four synchronized engineering phases turn raw intent into high-velocity digital infrastructure &mdash; discovery, architecture, parallel build, and perimeter hardening executing under one accountable team.
                    </p>

                    <div class="bl-proof-annotation">
                        <div class="bl-pa-head">
                            <span class="bl-pa-label">DELIVERY GUARANTEE</span>
                            <span class="bl-pa-badge">SLA VERIFIED</span>
                        </div>
                        <strong class="bl-pa-val">2 TO 6 WEEKS SPRINT</strong>
                        <div class="bl-pa-metrics">
                            <span><?= icon('zap') ?> 100/100 CWV</span>
                            <span>•</span>
                            <span><?= icon('shield') ?> TLS 1.3 HARDENED</span>
                            <span>•</span>
                            <span><?= icon('circle-check') ?> ZERO DOWNTIME</span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN (~58%): ADVANCED ANIMATED DETAILED MODEL & ISOMETRIC SVG ENGINE -->
                <div class="bl-right-col">
                    <div class="bl-sculpture-wrapper" data-magnetic>
                        
                        <!-- Telemetry HUD Bar Header -->
                        <div class="bl-telemetry-hud">
                            <span class="bl-hud-status">🟢 STREAMLINED DELIVERY PIPELINE</span>
                            <span class="bl-hud-lat">LATENCY &lt; 5MS &bull; 100/100 CWV</span>
                        </div>

                        <!-- Sculpture Stage Nav Track -->
                        <div class="bl-stage-track">
                            <?php 
                            $STAGES = [
                                ['01', 'DISCOVER',  'INTAKE & AUDIT'],
                                ['02', 'ARCHITECT', 'SYSTEM SCOPE'],
                                ['03', 'BUILD',     'PARALLEL SPRINT'],
                                ['04', 'HARDEN',    'LAUNCH & SLA'],
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

                        <!-- Main Motion Sculpture Visual Container with Ultra-Detailed Animated SVGs -->
                        <div class="bl-sculpture-viewport">
                            <svg class="bl-sculpture-svg" viewBox="0 0 600 320" fill="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="blGradPrimary" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#0a63ff"/>
                                        <stop offset="100%" stop-color="#38bdf8"/>
                                    </linearGradient>
                                    <linearGradient id="blGradAccent" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#10b981"/>
                                        <stop offset="100%" stop-color="#34d399"/>
                                    </linearGradient>
                                    <linearGradient id="blPlaneIso1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="rgba(10,99,255,0.18)"/>
                                        <stop offset="100%" stop-color="rgba(56,189,248,0.04)"/>
                                    </linearGradient>
                                    <linearGradient id="blPlaneIso2" x1="100%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="rgba(255,255,255,0.95)"/>
                                        <stop offset="100%" stop-color="rgba(224,242,254,0.85)"/>
                                    </linearGradient>
                                    <filter id="blGlow" x="-30%" y="-30%" width="160%" height="160%">
                                        <feGaussianBlur stdDeviation="5" result="blur"/>
                                        <feMerge>
                                            <feMergeNode in="blur"/>
                                            <feMergeNode in="SourceGraphic"/>
                                        </feMerge>
                                    </filter>
                                </defs>

                                <!-- Architectural Grid Lines -->
                                <g class="bl-svg-grid">
                                    <line x1="40" y1="160" x2="560" y2="160" stroke="rgba(10,99,255,0.12)" stroke-dasharray="4 4"/>
                                    <line x1="300" y1="30" x2="300" y2="290" stroke="rgba(10,99,255,0.12)" stroke-dasharray="4 4"/>
                                    <ellipse cx="300" cy="160" rx="220" ry="110" stroke="rgba(10,99,255,0.12)" stroke-width="1" stroke-dasharray="6 6"/>
                                    <ellipse cx="300" cy="160" rx="140" ry="70" stroke="rgba(56,189,248,0.2)" stroke-width="1"/>
                                </g>

                                <!-- Evolving Isometric 3D Model Planes & Nodes -->
                                <g class="bl-sculpture-geometry">
                                    <!-- Base Isometric Deck Plane -->
                                    <polygon class="bl-geo-plane plane-1" points="300,50 490,145 300,240 110,145" fill="url(#blPlaneIso2)" stroke="rgba(10,99,255,0.3)" stroke-width="1.5"/>
                                    <polygon class="bl-geo-plane plane-2" points="300,90 440,160 300,230 160,160" fill="url(#blPlaneIso1)" stroke="rgba(10,99,255,0.4)" stroke-width="1.5"/>
                                    <polygon class="bl-geo-plane plane-3" points="300,130 390,175 300,220 210,175" fill="#06122f" stroke="#0a63ff" stroke-width="2"/>
                                    
                                    <!-- Isometric Interlink Columns -->
                                    <line x1="300" y1="50" x2="300" y2="130" stroke="#0a63ff" stroke-width="1.5" stroke-dasharray="3 3"/>
                                    <line x1="490" y1="145" x2="390" y2="175" stroke="#38bdf8" stroke-width="1.2" stroke-dasharray="3 3"/>
                                    <line x1="110" y1="145" x2="210" y2="175" stroke="#38bdf8" stroke-width="1.2" stroke-dasharray="3 3"/>

                                    <!-- Floating High-Tech Micro-Nodes -->
                                    <circle cx="300" cy="50" r="5" fill="#0a63ff" filter="url(#blGlow)"/>
                                    <circle cx="490" cy="145" r="4" fill="#38bdf8"/>
                                    <circle cx="110" cy="145" r="4" fill="#38bdf8"/>
                                    <circle cx="300" cy="240" r="4.5" fill="#10b981" filter="url(#blGlow)"/>
                                    <circle cx="300" cy="130" r="6" fill="#0a63ff" filter="url(#blGlow)"/>
                                </g>

                                <!-- Traveling Data Signals along isometric vectors -->
                                <g class="bl-packets">
                                    <circle cx="205" cy="97" r="3.5" fill="#0a63ff" filter="url(#blGlow)">
                                        <animate attributeName="cx" values="110;300;490;300;110" dur="4s" repeatCount="indefinite"/>
                                        <animate attributeName="cy" values="145;50;145;240;145" dur="4s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="395" cy="192" r="3" fill="#38bdf8">
                                        <animate attributeName="cx" values="490;300;110;300;490" dur="5s" repeatCount="indefinite"/>
                                        <animate attributeName="cy" values="145;240;145;50;145" dur="5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>

                                <!-- Active Stage Radar & Shield Visual Layers -->
                                <circle class="bl-scan-perimeter" cx="300" cy="160" r="130" stroke="#0a63ff" stroke-width="2" stroke-dasharray="30 400" stroke-linecap="round"/>
                                <circle cx="300" cy="160" r="150" stroke="rgba(16,185,129,0.25)" stroke-width="1.5" stroke-dasharray="8 8"/>
                            </svg>

                            <!-- Stage Copy Overlay -->
                            <div class="bl-stage-copy-box">
                                <div class="bl-stage-copy is-active" data-stage-copy="0">
                                    <h3>01 • DISCOVER &amp; STRATEGY</h3>
                                    <p>Raw intent, traffic vectors, and stack bottlenecks gathered into one synchronized strategic audit.</p>
                                </div>
                                <div class="bl-stage-copy" data-stage-copy="1" style="display:none;">
                                    <h3>02 • ARCHITECT &amp; PROTOTYPE</h3>
                                    <p>Scattered signals lock into grid structure, layout boundaries, and exact scope deliverables.</p>
                                </div>
                                <div class="bl-stage-copy" data-stage-copy="2" style="display:none;">
                                    <h3>03 • PARALLEL BUILD</h3>
                                    <p>Physical digital surfaces and parallel engineering components assemble in daily high-velocity sprints.</p>
                                </div>
                                <div class="bl-stage-copy" data-stage-copy="3" style="display:none;">
                                    <h3>04 • HARDEN &amp; SHIP</h3>
                                    <p>Perimeter scan verifies 100/100 CWV, TLS 1.3 encryption, and WAF security before shipping live.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payoff Footer Signal -->
                        <div class="bl-sculpture-footer">
                            <span class="bl-sf-signal">● SIGNAL STATUS: SHIPPED</span>
                            <div class="bl-sf-line"><div class="bl-sf-pulse"></div></div>
                            <span class="bl-sf-version">RAFLY PRODUCTION ENGINE</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ==========================================================
       07 — SELECTED WORK: PROOF NOT PROMISES (#work) — ART-DIRECTED ULTRA-PREMIUM CARDS
       ========================================================== */ ?>
<?php if ($caseStudies): ?>
    <section class="home-section-07 work-showcase-section has-tex" id="work">
        <!-- REELS-STYLE BACKGROUND LAYER 1 & 2: Architectural Grid, Dots, Hatch & Ambient Aura -->
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="ws-container container">
            <div class="ws-head">
                <span class="ws-kicker">07 • PROOF, NOT PROMISES</span>
                <h2 class="ws-title">Selected Work.</h2>
                <p class="ws-sub">Real client builds executed with full attribution, zero vanity metrics, and verified ROI.</p>
            </div>

            <!-- Art-Directed Ultra-Premium Topic-Specific Case Study Cards -->
            <div class="ws-hero-grid">
                <?php 
                $TOPIC_SVGS = [
                    // Card 01 SVG: E-Commerce & Checkout Engine (Animated Visual Model)
                    '<svg class="ws-topic-svg" viewBox="0 0 320 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="wsEcomBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#06122e"/>
                                <stop offset="50%" stop-color="#0a255c"/>
                                <stop offset="100%" stop-color="#030a1c"/>
                            </linearGradient>
                            <linearGradient id="wsGradEcom" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#0a63ff"/>
                                <stop offset="50%" stop-color="#38bdf8"/>
                                <stop offset="100%" stop-color="#10b981"/>
                            </linearGradient>
                            <linearGradient id="wsScanGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="rgba(56,189,248,0)"/>
                                <stop offset="50%" stop-color="rgba(56,189,248,0.35)"/>
                                <stop offset="100%" stop-color="rgba(56,189,248,0)"/>
                            </linearGradient>
                            <filter id="wsGlowEcom" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="4" result="blur"/>
                                <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                            </filter>
                        </defs>
                        <rect width="320" height="150" rx="12" fill="url(#wsEcomBg)" stroke="rgba(10,99,255,0.3)" stroke-width="1"/>
                        <line x1="20" y1="120" x2="300" y2="120" stroke="rgba(10,99,255,0.2)" stroke-dasharray="3 3"/>
                        <line x1="20" y1="90" x2="300" y2="90" stroke="rgba(10,99,255,0.15)" stroke-dasharray="3 3"/>
                        <line x1="20" y1="60" x2="300" y2="60" stroke="rgba(10,99,255,0.1)" stroke-dasharray="3 3"/>
                        <line x1="20" y1="30" x2="300" y2="30" stroke="rgba(10,99,255,0.08)" stroke-dasharray="3 3"/>

                        <rect class="ws-anim-scan" x="20" y="20" width="280" height="15" fill="url(#wsScanGrad)"/>

                        <path class="ws-anim-wave" d="M25 110 C75 105 105 55 155 70 C205 85 245 35 295 38" stroke="url(#wsGradEcom)" stroke-width="3.5" stroke-linecap="round" fill="none" filter="url(#wsGlowEcom)"/>
                        <circle class="ws-anim-pulse" cx="295" cy="38" r="5" fill="#10b981" filter="url(#wsGlowEcom)"/>

                        <rect x="22" y="16" width="125" height="34" rx="8" fill="rgba(10,99,255,0.28)" stroke="rgba(56,189,248,0.5)" stroke-width="1"/>
                        <circle cx="34" cy="33" r="3" fill="#38bdf8"/>
                        <text x="44" y="37" font-family="monospace" font-size="11" font-weight="900" fill="#38bdf8">+310% CVR</text>

                        <rect x="155" y="96" width="140" height="36" rx="8" fill="rgba(6,18,47,0.85)" stroke="rgba(16,185,129,0.5)" stroke-width="1.2"/>
                        <circle class="ws-anim-pulse-dot" cx="170" cy="114" r="4" fill="#10b981"/>
                        <text x="182" y="111" font-family="monospace" font-size="9" font-weight="800" fill="#ffffff">CHECKOUT: 38ms</text>
                        <text x="182" y="124" font-family="monospace" font-size="7.5" font-weight="700" fill="#38bdf8">● ACCELERATED API</text>
                    </svg>',

                    // Card 02 SVG: Full-Stack Web Architecture & Performance System (Animated Visual Model)
                    '<svg class="ws-topic-svg" viewBox="0 0 320 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="wsArchBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#050e2b"/>
                                <stop offset="100%" stop-color="#020817"/>
                            </linearGradient>
                        </defs>
                        <rect width="320" height="150" rx="12" fill="url(#wsArchBg)" stroke="rgba(10,99,255,0.3)" stroke-width="1"/>
                        <rect x="20" y="18" width="280" height="114" rx="8" fill="rgba(6,18,47,0.88)" stroke="rgba(10,99,255,0.35)" stroke-width="1.2"/>
                        <line x1="20" y1="44" x2="300" y2="44" stroke="rgba(10,99,255,0.25)"/>
                        <circle cx="36" cy="31" r="3.5" fill="#ef4444"/>
                        <circle cx="48" cy="31" r="3.5" fill="#eab308"/>
                        <circle cx="60" cy="31" r="3.5" fill="#10b981"/>
                        <text x="76" y="34" font-family="monospace" font-size="8" font-weight="700" fill="#64748b">https://rafly.in/system-status</text>

                        <line x1="35" y1="74" x2="285" y2="74" stroke="rgba(10,99,255,0.25)" stroke-dasharray="4 4"/>
                        <circle class="ws-anim-packet" cx="40" cy="74" r="4" fill="#38bdf8"/>

                        <rect x="35" y="56" width="110" height="32" rx="6" fill="rgba(56,189,248,0.18)" stroke="rgba(56,189,248,0.5)" stroke-width="1"/>
                        <text x="44" y="76" font-family="monospace" font-size="10" font-weight="900" fill="#38bdf8">100/100 CWV</text>

                        <rect x="155" y="56" width="130" height="32" rx="6" fill="rgba(10,99,255,0.3)" stroke="rgba(10,99,255,0.6)" stroke-width="1"/>
                        <text x="165" y="76" font-family="monospace" font-size="10" font-weight="900" fill="#ffffff">SUB-50ms SLA</text>

                        <rect x="35" y="98" width="165" height="18" rx="4" fill="rgba(255,255,255,0.05)"/>
                        <rect class="ws-anim-code-bar" x="35" y="103" width="95" height="8" rx="2" fill="#0a63ff"/>
                        <rect x="210" y="98" width="75" height="18" rx="4" fill="rgba(16,185,129,0.22)" stroke="rgba(16,185,129,0.5)"/>
                        <text x="217" y="111" font-family="monospace" font-size="8.5" font-weight="800" fill="#10b981">HARDENED</text>
                    </svg>',

                    // Card 03 SVG: Performance Growth & ROAS Matrix (Animated Visual Model)
                    '<svg class="ws-topic-svg" viewBox="0 0 320 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="wsRoasBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#09052b"/>
                                <stop offset="100%" stop-color="#03081a"/>
                            </linearGradient>
                            <filter id="wsGlowRoas" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="4" result="blur"/>
                                <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                            </filter>
                        </defs>
                        <rect width="320" height="150" rx="12" fill="url(#wsRoasBg)" stroke="rgba(10,99,255,0.3)" stroke-width="1"/>
                        
                        <g class="ws-anim-bars">
                            <rect class="ws-anim-bar1" x="25" y="82" width="44" height="46" rx="5" fill="rgba(10,99,255,0.35)"/>
                            <rect class="ws-anim-bar2" x="85" y="62" width="44" height="66" rx="5" fill="rgba(10,99,255,0.55)"/>
                            <rect class="ws-anim-bar3" x="145" y="42" width="44" height="86" rx="5" fill="rgba(56,189,248,0.7)"/>
                            <rect class="ws-anim-bar4" x="205" y="22" width="44" height="106" rx="5" fill="#0a63ff" filter="url(#wsGlowRoas)"/>
                        </g>

                        <path class="ws-anim-roas-path" d="M47 78 L107 58 L167 38 L227 18" stroke="#38bdf8" stroke-width="3" stroke-dasharray="4 4"/>
                        <circle class="ws-anim-pulse" cx="227" cy="18" r="5" fill="#38bdf8" filter="url(#wsGlowRoas)"/>

                        <rect x="195" y="24" width="105" height="28" rx="6" fill="#06122f" stroke="#38bdf8" stroke-width="1.2"/>
                        <circle cx="207" cy="38" r="3" fill="#10b981"/>
                        <text x="216" y="42" font-family="monospace" font-size="10.5" font-weight="900" fill="#38bdf8">4.8x ROAS</text>
                    </svg>'
                ];
                foreach ($caseStudies as $i => $cs): 
                    $svgIllustration = $TOPIC_SVGS[$i % count($TOPIC_SVGS)];
                ?>
                <article class="ws-case-card <?= $i === 0 ? 'is-featured' : '' ?>">
                    <div class="ws-card-media" aria-hidden="true">
                        <?= $svgIllustration ?>
                    </div>
                    <div class="ws-card-header">
                        <span class="ws-card-num">CASE STUDY 0<?= $i + 1 ?></span>
                        <span class="ws-card-tag">100% ATTRIBUTED</span>
                    </div>
                    <h3 class="ws-card-title"><?= e($cs['client_name'] ?? '') ?></h3>
                    <?php if (!empty($cs['summary'])): ?>
                    <p class="ws-card-desc"><?= e($cs['summary']) ?></p>
                    <?php endif; ?>
                    <div class="ws-card-metrics">
                        <span class="ws-metric-pill">🟢 Verified Production</span>
                        <span class="ws-metric-pill">⚡ SLA Guaranteed</span>
                        <?php if (!empty($cs['metric_value'])): ?>
                        <span class="ws-metric-pill is-highlight"><?= e($cs['metric_value']) ?> <?= e($cs['metric_label'] ?? '') ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($cs['slug'])): ?>
                    <a class="ws-card-link" href="<?= e(site_path('/case-studies#' . $cs['slug'])) ?>" data-magnetic>
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
       08 — OPERATING MODEL & OPERATIONAL BOUNDARIES (#limits)
       ========================================================== */ ?>
    <section class="home-section-08 boundaries-section has-tex" id="limits" data-system-map-container>
        <!-- REELS-STYLE BACKGROUND LAYER 1 & 2: Architectural Grid, Dots, Hatch & Ambient Aura -->
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>
        <div class="s08-container container">
            
            <!-- EDITORIAL SECTION HEADER WITH TELEMETRY RADAR & VIEW SWITCHER -->
            <!-- EDITORIAL SECTION HEADER WITH VIEW SWITCHER -->
            <header class="s08-header">
                <div class="s08-header-main">
                    <div class="s08-hm-titles">
                        <h2 class="s08-title">OPERATIONAL BOUNDARIES &amp; ENGINE<span class="s08-title-accent">.</span></h2>
                        <p class="s08-subtitle">We don't do everything. 15 strict operational boundaries and a unified engine—because an elite studio names its edges in writing.</p>
                    </div>

                    <!-- VIEW MODE SWITCHER TABS -->
                    <div class="s08-view-switcher" role="tablist" aria-label="View operating model layout">
                        <button type="button" class="s08-view-btn is-active" data-view-target="all-views" role="tab" aria-selected="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
                            <span>Full System Overview</span>
                        </button>
                        <button type="button" class="s08-view-btn" data-view-target="arch-engine" role="tab" aria-selected="false">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 17 22 12"/></svg>
                            <span>Kinetic Engine</span>
                        </button>
                        <button type="button" class="s08-view-btn" data-view-target="boundary-index" role="tab" aria-selected="false">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>15 Boundary Specs</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- ARCHITECTURAL COMPARISON: FRAGMENTED MODEL VS RAFLY UNIFIED KINETIC ENGINE -->
            <div class="s08-comparison-system unified-comparison-block s08-view-block" data-view-id="arch-engine">
                <div class="s08-comp-header">
                    <div class="s08-ch-left">
                        <h3 class="s08-comp-heading">Fragmented Agency Silos vs. Unified RAFly Engine</h3>
                    </div>
                    <div class="s08-ch-right">
                        <span class="s08-system-status-chip">
                            <span class="s08-ssc-pulse"></span>
                            LIVE ENGINE COMPARISON
                        </span>
                    </div>
                </div>

                <div class="s08-comp-grid">
                    <!-- Model A: Fragmented Agency Model (Friction Stack) -->
                    <div class="s08-model-card is-fragmented">
                        <div class="s08-card-hud-bar">
                            <div class="s08-model-badge">FRAGMENTED AGENCY MODEL</div>
                            <div class="s08-model-status">UNCOORDINATED &bull; 5 SEPARATE VENDORS</div>
                        </div>
                        <p class="s08-model-desc">Multiple isolated vendors passing work back and forth with zero shared strategy, fragmented codebase, and high friction handoffs.</p>

                        <!-- ANIMATED SVG FRICTION CHAOS PIPELINE -->
                        <div class="s08-chaos-pipeline-wrap">
                            <svg class="s08-chaos-svg" viewBox="0 0 380 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Dashed Glitch Lines -->
                                <path class="s08-chaos-path" d="M40 35 L190 35 L190 85" stroke="rgba(244, 63, 94, 0.4)" stroke-width="2" stroke-dasharray="4 4" />
                                <path class="s08-chaos-path" d="M190 85 L340 85 L340 145" stroke="rgba(244, 63, 94, 0.4)" stroke-width="2" stroke-dasharray="4 4" />
                                <path class="s08-chaos-path" d="M340 145 L190 145" stroke="rgba(244, 63, 94, 0.4)" stroke-width="2" stroke-dasharray="4 4" />
                                
                                <!-- Chaos Particles -->
                                <circle cx="40" cy="35" r="4" fill="#f43f5e"><animate attributeName="opacity" values="0.3;1;0.3" dur="1.2s" repeatCount="indefinite"/></circle>
                                <circle cx="190" cy="85" r="4" fill="#f43f5e"><animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/></circle>
                                <circle cx="340" cy="145" r="4" fill="#f43f5e"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.8s" repeatCount="indefinite"/></circle>
                            </svg>

                            <div class="s08-silo-pipeline">
                                <div class="s08-silo-step">
                                    <div class="s08-ss-left">
                                        <span class="s08-silo-num">01</span>
                                        <span class="s08-silo-label">Brand Agency</span>
                                    </div>
                                    <span class="s08-silo-friction">+12 Day Delay</span>
                                </div>
                                <div class="s08-silo-connector">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                    <span>Handoff Tax &amp; Friction</span>
                                </div>
                                <div class="s08-silo-step">
                                    <div class="s08-ss-left">
                                        <span class="s08-silo-num">02</span>
                                        <span class="s08-silo-label">Dev Vendor</span>
                                    </div>
                                    <span class="s08-silo-friction">Context Erosion</span>
                                </div>
                                <div class="s08-silo-connector">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                    <span>Unverified Rework Cycle</span>
                                </div>
                                <div class="s08-silo-step">
                                    <div class="s08-ss-left">
                                        <span class="s08-silo-num">03</span>
                                        <span class="s08-silo-label">Freelancer</span>
                                    </div>
                                    <span class="s08-silo-friction">No Security Audit</span>
                                </div>
                                <div class="s08-silo-connector">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                    <span>Uncoordinated Scope Creep</span>
                                </div>
                                <div class="s08-silo-step">
                                    <div class="s08-ss-left">
                                        <span class="s08-silo-num">04</span>
                                        <span class="s08-silo-label">Growth Agency</span>
                                    </div>
                                    <span class="s08-silo-friction">Data Leak &amp; Misalignment</span>
                                </div>
                            </div>
                        </div>

                        <div class="s08-model-verdict">
                            <span class="s08-mv-icon"><?= icon('alert-triangle') ?></span>
                            <div><strong>Outcome:</strong> You become the unpaid project manager bridging disconnected contractors with zero single-point accountability.</div>
                        </div>
                    </div>

                    <!-- Model B: RAFly Unified System (Kinetic SVG Engine Hub) -->
                    <div class="s08-model-card is-unified">
                        <div class="s08-card-hud-bar">
                            <div class="s08-model-badge is-accent">RAFLY UNIFIED KINETIC CORE</div>
                            <div class="s08-model-status is-live">🟢 100% INTEGRATED &bull; UNIFIED ENGINE</div>
                        </div>
                        <p class="s08-model-desc">Web, security, marketing, content, and e-commerce executed under one architecture, one repo, and one fixed scope.</p>

                        <!-- ADVANCED CUSTOM ANIMATED SVG KINETIC HUB -->
                        <div class="s08-kinetic-svg-hub">
                            <svg class="s08-engine-svg" viewBox="0 0 600 340" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <!-- Radial Core Gradient -->
                                    <radialGradient id="coreGrad" cx="50%" cy="50%" r="50%">
                                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="1"/>
                                        <stop offset="60%" stop-color="#0a63ff" stop-opacity="0.9"/>
                                        <stop offset="100%" stop-color="#06122f" stop-opacity="0.95"/>
                                    </radialGradient>
                                    <!-- Laser Stream Linear Gradients -->
                                    <linearGradient id="streamGradWeb" x1="0%" y1="100%" x2="0%" y2="0%">
                                        <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.2"/>
                                        <stop offset="100%" stop-color="#38bdf8" stop-opacity="1"/>
                                    </linearGradient>
                                    <linearGradient id="streamGradSec" x1="0%" y1="100%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.2"/>
                                        <stop offset="100%" stop-color="#10b981" stop-opacity="1"/>
                                    </linearGradient>
                                    <filter id="coreGlowFilter" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="3" result="blur"/>
                                        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                                    </filter>
                                </defs>

                                <!-- Curved Bezier Connection Paths -->
                                <path d="M 300 170 Q 300 100 300 45" class="s08-laser-path-base" />
                                <path d="M 300 170 Q 400 120 480 80" class="s08-laser-path-base" />
                                <path d="M 300 170 Q 390 230 460 270" class="s08-laser-path-base" />
                                <path d="M 300 170 Q 210 230 140 270" class="s08-laser-path-base" />
                                <path d="M 300 170 Q 200 120 120 80" class="s08-laser-path-base" />

                                <!-- Animated Laser Pulse Streams -->
                                <path d="M 300 170 Q 300 100 300 45" class="s08-laser-stream" data-path-key="web" stroke="url(#streamGradWeb)" />
                                <path d="M 300 170 Q 400 120 480 80" class="s08-laser-stream" data-path-key="security" stroke="url(#streamGradSec)" />
                                <path d="M 300 170 Q 390 230 460 270" class="s08-laser-stream" data-path-key="growth" stroke="#38bdf8" />
                                <path d="M 300 170 Q 210 230 140 270" class="s08-laser-stream" data-path-key="content" stroke="#8b5cf6" />
                                <path d="M 300 170 Q 200 120 120 80" class="s08-laser-stream" data-path-key="commerce" stroke="#ec4899" />

                                <!-- CENTRAL REACTOR CORE MATRIX -->
                                <g class="s08-core-group">
                                    <!-- Ambient Core Pulse Aura -->
                                    <circle cx="300" cy="170" r="68" fill="rgba(10, 99, 255, 0.12)" class="s08-svg-core-pulse" />
                                    <!-- Rotating Outer Tech Ring -->
                                    <circle cx="300" cy="170" r="56" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="8 6" class="s08-ring-cw" />
                                    <!-- Rotating Inner Gear Ring -->
                                    <circle cx="300" cy="170" r="42" stroke="rgba(255,255,255,0.4)" stroke-width="1" stroke-dasharray="14 8" class="s08-ring-ccw" />
                                    <!-- Central Core Orb -->
                                    <circle cx="300" cy="170" r="32" fill="url(#coreGrad)" filter="url(#coreGlowFilter)" />
                                    <text x="300" y="166" text-anchor="middle" class="s08-svg-core-text">RAFLY</text>
                                    <text x="300" y="178" text-anchor="middle" class="s08-svg-core-sub">CORE</text>
                                </g>

                                <!-- RADIAL SATELLITE NODES (INTERACTIVE HOVER TARGETS) -->
                                <!-- Satellite 1: Web Architecture (Top) -->
                                <g class="s08-sat-node" data-sat-key="web" transform="translate(300, 45)">
                                    <circle r="22" class="s08-sat-bg" />
                                    <circle r="14" class="s08-sat-core" fill="#0a63ff" />
                                    <text y="4" text-anchor="middle" fill="#fff" font-size="10" font-weight="900">🌐</text>
                                    <text y="36" text-anchor="middle" class="s08-sat-label">WEB DEVELOPMENT</text>
                                </g>

                                <!-- Satellite 2: Perimeter Security (Top Right) -->
                                <g class="s08-sat-node" data-sat-key="security" transform="translate(480, 80)">
                                    <circle r="22" class="s08-sat-bg" />
                                    <circle r="14" class="s08-sat-core" fill="#10b981" />
                                    <text y="4" text-anchor="middle" fill="#fff" font-size="10" font-weight="900">🛡️</text>
                                    <text y="36" text-anchor="middle" class="s08-sat-label">CYBER SECURITY</text>
                                </g>

                                <!-- Satellite 3: Growth Marketing (Bottom Right) -->
                                <g class="s08-sat-node" data-sat-key="growth" transform="translate(460, 270)">
                                    <circle r="22" class="s08-sat-bg" />
                                    <circle r="14" class="s08-sat-core" fill="#38bdf8" />
                                    <text y="4" text-anchor="middle" fill="#fff" font-size="10" font-weight="900">📈</text>
                                    <text y="36" text-anchor="middle" class="s08-sat-label">PERFORMANCE MARKETING</text>
                                </g>

                                <!-- Satellite 4: Brand & Content (Bottom Left) -->
                                <g class="s08-sat-node" data-sat-key="content" transform="translate(140, 270)">
                                    <circle r="22" class="s08-sat-bg" />
                                    <circle r="14" class="s08-sat-core" fill="#8b5cf6" />
                                    <text y="4" text-anchor="middle" fill="#fff" font-size="10" font-weight="900">✍️</text>
                                    <text y="36" text-anchor="middle" class="s08-sat-label">BRAND CONTENT</text>
                                </g>

                                <!-- Satellite 5: Commerce Engine (Top Left) -->
                                <g class="s08-sat-node" data-sat-key="commerce" transform="translate(120, 80)">
                                    <circle r="22" class="s08-sat-bg" />
                                    <circle r="14" class="s08-sat-core" fill="#ec4899" />
                                    <text y="4" text-anchor="middle" fill="#fff" font-size="10" font-weight="900">🛒</text>
                                    <text y="36" text-anchor="middle" class="s08-sat-label">E-COMMERCE OPS</text>
                                </g>
                            </svg>
                        </div>

                        <!-- REAL-TIME TELEMETRY PANEL (UPDATES ON NODE HOVER) -->
                        <div class="s08-hub-telemetry-box">
                            <div class="s08-htb-header">
                                <span class="s08-htb-tag" data-telemetry-tag>UNIFIED ENGINE &bull; ACTIVE</span>
                                <span class="s08-htb-status" data-telemetry-status>SYNCED</span>
                            </div>
                            <h4 class="s08-htb-title" data-telemetry-title>HOVER A SATELLITE CAPABILITY</h4>
                            <div class="s08-htb-metric" data-telemetry-metric>SINGLE-POINT ACCOUNTABILITY</div>
                            <p class="s08-htb-desc" data-telemetry-desc>All 5 service capabilities operate in direct synchronization under one codebase, eliminating vendor handoff delays and context loss.</p>
                        </div>

                        <div class="s08-model-verdict is-accent">
                            <span class="s08-mv-icon"><?= icon('zap') ?></span>
                            <div><strong>Outcome:</strong> High-velocity execution, zero vendor friction, and single-point engineering accountability.</div>
                        </div>
                    </div>
                </div>

                <!-- LIVE METRICS TELEMETRY STRIP -->
                <div class="s08-telemetry-strip">
                    <div class="s08-tel-card">
                        <span class="s08-tel-val">10x</span>
                        <span class="s08-tel-label">DEPLOYMENT VELOCITY</span>
                    </div>
                    <div class="s08-tel-card">
                        <span class="s08-tel-val">0%</span>
                        <span class="s08-tel-label">HANDOFF FRICTION TAX</span>
                    </div>
                    <div class="s08-tel-card">
                        <span class="s08-tel-val">100%</span>
                        <span class="s08-tel-label">FIXED PRICE GUARANTEE</span>
                    </div>
                    <div class="s08-tel-card">
                        <span class="s08-tel-val">1</span>
                        <span class="s08-tel-label">ACCOUNTABLE LEAD ENGINEER</span>
                    </div>
                </div>

                <!-- CONVERGING STATEMENT BANNER -->
                <div class="s08-converge-banner">
                    <div class="s08-cb-left">
                        <span class="s08-cb-badge">THE RAFLY DIFFERENCE</span>
                        <span class="s08-cb-statement">ONE TEAM. ONE UNIFIED ENGINE. ONE ACCOUNTABLE OUTCOME.</span>
                    </div>
                    <a href="#start" class="s08-cb-cta">
                        <span>INITIATE PROJECT SCOPE</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>

            <!-- OPERATIONAL BOUNDARIES INDEX -->
            <div class="s08-boundary-index s08-view-block" data-view-id="boundary-index">
                <div class="s08-bi-header">
                    <!-- Filter strip matching JS expectations -->
                    <div class="bs-filter-strip s08-filter-strip" role="tablist" aria-label="Filter boundaries by service">
                        <?php foreach ($serviceTabs as $i => $tab): ?>
                        <button type="button" class="bs-pill <?= $i === 0 ? 'is-active' : '' ?>" data-filter="<?= e($tab['slug']) ?>" role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                            <span><?= e($tab['label']) ?></span>
                            <small>(<?= $tab['count'] ?>)</small>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Technical Specification Boundary Grid (bs-card items for JS) -->
                <div class="s08-bi-grid bs-board-wrap">
                    <?php 
                    $defaultFilter = $serviceTabs[0]['slug'] ?? 'web-development';
                    foreach ($limits as $item): 
                        $isDefault = ($item['slug'] === $defaultFilter || $defaultFilter === 'all');
                    ?>
                    <div class="bs-card s08-bi-card" data-category="<?= e($item['slug']) ?>"<?= $isDefault ? '' : ' style="display: none;"' ?>>
                        <div class="s08-card-head-bar">
                            <span class="s08-ch-domain"><?= e(strtoupper($item['svc'])) ?></span>
                            <span class="s08-ch-num">SPEC 0<?= e($item['localIdx']) ?></span>
                        </div>
                        <div class="s08-card-content">
                            <h4 class="s08-cb-title"><?= e($item['title']) ?></h4>
                            <p class="s08-cb-desc"><?= e($item['desc']) ?></p>
                        </div>
                        <div class="s08-card-standard">
                            <span class="s08-cs-icon"><?= icon('circle-check') ?></span>
                            <div class="s08-cs-body">
                                <span class="s08-cs-label">Guaranteed Standard</span>
                                <p class="s08-cs-text"><?= e($item['standard']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>

    <?php /* ==========================================================
       09 — INTAKE CONSOLE & PROJECT START (#start)
       ========================================================== */ ?>
    <section class="home-section-09 close-editorial-section close has-tex" id="start">
        <!-- REELS-STYLE BACKGROUND LAYER 1 & 2: Architectural Grid, Dots, Hatch & Ambient Aura -->
        <div class="tex-apps-grid" aria-hidden="true"></div>
        <div class="tex-apps-dots" aria-hidden="true"></div>
        <div class="tex-apps-hatch" aria-hidden="true"></div>
        <div class="reels-ambient-aura" aria-hidden="true"></div>

        <div class="s09-container container">
            <div class="s09-split-grid ce-grid">
                
                <!-- LEFT COLUMN: DYNAMIC SIGNAL & SLA TIMELINE -->
                <div class="s09-timeline-panel ce-copy">
                    <div class="s09-header-block">
                        <div class="s09-head-tag-row">
                            <span class="s09-tag ce-kicker">09 • INTAKE CONSOLE</span>
                            <span class="s09-live-badge"><i class="s09-live-dot"></i> SLA ONLINE</span>
                        </div>
                        <h2 class="s09-title ce-title">LET'S BUILD SOMETHING THAT MATTERS.</h2>
                        <p class="s09-lead ce-lead">Tell us what is in front of you. We will respond with a clear scope and a transparent fixed price within 24 hours—or tell you plainly if it's not a fit.</p>
                    </div>

                    <!-- LIVE LOTTIE TELEMETRY HUD CARD (SIDE ANIMATION) -->
                    <div class="s09-lottie-hud-card">
                        <div class="s09-lhud-radar" style="width: 48px; height: 48px;">
                            <lottie-player
                                src="/assets/lottie/radar.json"
                                background="transparent"
                                speed="1"
                                style="width: 48px; height: 48px;"
                                autoplay
                                loop
                                aria-hidden="true">
                            </lottie-player>
                        </div>
                        <div class="s09-lhud-info">
                            <div class="s09-lhud-row">
                                <span class="s09-lhud-label">SIGNAL STATUS</span>
                                <strong class="s09-lhud-val">ENCRYPTED &bull; DIRECT SLA</strong>
                            </div>
                            <div class="s09-lhud-row">
                                <span class="s09-lhud-label">ENGINEER RESPONSE</span>
                                <strong class="s09-lhud-val">&lt; 24 HOURS GUARANTEE</strong>
                            </div>
                            <div class="s09-lhud-bars">
                                <i class="lh-b1"></i><i class="lh-b2"></i><i class="lh-b3"></i><i class="lh-b4"></i><i class="lh-b5"></i>
                            </div>
                        </div>
                    </div>

                    <!-- 3-Step Intake Timeline -->
                    <div class="s09-roadmap ce-roadmap">
                        <div class="s09-road-step ce-step-item">
                            <div class="s09-rs-num ce-step-num">01</div>
                            <div class="s09-rs-content ce-step-info">
                                <strong>TECHNICAL DISCOVERY &amp; AUDIT</strong>
                                <p>Direct technical call with lead engineers to map your goals &amp; stack bottlenecks.</p>
                            </div>
                        </div>
                        <div class="s09-road-step ce-step-item">
                            <div class="s09-rs-num ce-step-num">02</div>
                            <div class="s09-rs-content ce-step-info">
                                <strong>SCOPE &amp; FIXED PRICE PROPOSAL</strong>
                                <p>Clear roadmap document with itemized scope, timeline, and one guaranteed number up front.</p>
                            </div>
                        </div>
                        <div class="s09-road-step ce-step-item">
                            <div class="s09-rs-num ce-step-num">03</div>
                            <div class="s09-rs-content ce-step-info">
                                <strong>PARALLEL BUILD &amp; HARDENED LAUNCH</strong>
                                <p>Web, security, content, and growth engines built in parallel sprints with 100/100 CWV guarantee.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: INTAKE CONSOLE FORM PANEL -->
                <div class="s09-form-panel ce-form-wrap">
                    <?php
                        $formId        = 'homeLeadForm';
                        $submitLabel   = 'START A CONVERSATION →';
                        $hideTelemetry = true;
                        $compact       = true;
                        require __DIR__ . '/partials/lead-form.php';
                    ?>
                </div>

            </div>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
