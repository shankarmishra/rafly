<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * THE HOMEPAGE — ten sections.
 *
 * Every section answers a question a buyer actually asks, in the order they
 * ask it:
 *
 *   1   what is this              hero
 *   2   what do you believe       the statement
 *   3   what do I get             the platform — five products, fanned
 *   3b  do you build apps         apps — three phones
 *   4   how does it run           delivery
 *   5   what is it built on       the stack
 *   6   who have you done it for  selected work   (conditional — see below)
 *   7   what will you NOT do      honest limits
 *   8   what does it cost         pricing and FAQ
 *   9   how do I start            the close
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
 * The five modules of the object, in stack order, matched to the five real
 * services. Index, label and slug all come from one place, so a label on the
 * 3-D scene can never drift from the page it links to.
 */
$MODULES = [
    ['01', 'web-development',         'Web',       'Sites and web apps that load fast and do not fall over as you grow.'],
    ['02', 'web-security',            'Security',  'A practical look at what someone probing your site would find first.'],
    ['03', 'marketing-advertisement', 'Marketing', 'Campaigns built around who is actually buying, reported in plain language.'],
    ['04', 'content-creation',        'Content',   'Copy that says what you do, in your words, without the filler.'],
    ['05', 'ecommerce-support',       'Commerce',  'The unglamorous side of selling online, kept in order.'],
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
 * The app section, and it is deliberately CAPABILITY rather than claim. Not
 * "we shipped N apps" and not a store rating: those are numbers, and the site
 * does not publish a number it cannot back. These say what the work is.
 */
$APP_POINTS = [
    'iOS and Android, native or cross-platform',
    'Store submission, review responses, and the release after that',
    'One backend behind the app and the website, never two',
];

/** Three phone shells. Centre, then left, then right. */
$PHONES = [
    ['#0a63ff', '#5c93ff'],
    ['#0230c6', '#3d6ee8'],
    ['#1b6bff', '#6aa4ff'],
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

/** Every honest limit the five services declare, in one list. */
$limits = [];
foreach (services_all() as $svc) {
    foreach (($svc['boundaries'] ?? []) as $b) {
        $limits[] = [$svc['title'], $b['title'], $b['desc']];
    }
}

$page = [
    'id'        => 'home',
    'title'     => 'Rafly | Digital Growth — Build Fast, Grow Faster, Scale Smarter',
    'desc'      => 'One team for web development, security, marketing, content and e-commerce. One scope, one price, one person accountable — instead of five vendors who have never spoken.',
    'bodyClass' => 'page-home',
    'styles'    => ['home'],
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
    <section class="section hero hero-scene" id="home" data-hero>
        <?php /* Painted by js/aurora.js — raw WebGL2, one fragment shader, one
                 triangle. Not three.js: loading 365 KB of scene graph, camera
                 and material system to draw a single full-screen quad is not
                 defensible. aria-hidden because it is the room's lighting. */ ?>
        <canvas class="hero-aurora" data-aurora aria-hidden="true"></canvas>

        <div class="container">
            <div class="hero-grid">
                <div class="hero-copy">
                    <p class="meta-row" data-fx="fade">
                        <span><b>Built for</b> owner-operated businesses</span>
                        <span><b>Model</b> one scope, one price</span>
                    </p>

                    <?php /* Each line is masked and slides up from behind its own
                             edge, which reads as typeset rather than animated. The
                             mask boxes are inert without JS — css/09-scenes.css
                             resolves them to translateY(0) under .no-js. */ ?>
                    <h1 class="hero-title is-in">
                        <span class="line-mask"><span>Build Fast.</span></span>
                        <span class="line-mask"><span>Grow Faster.</span></span>
                        <span class="line-mask"><span class="grad-word">Scale Smarter.</span></span>
                    </h1>

                    <p class="hero-lead" data-fx="fade">
                        Web, security, marketing, content and commerce — built by one team,
                        on one plan, with one person accountable. Not five vendors who have
                        never spoken to each other.
                    </p>

                    <div class="hero-cta" data-fx="fade">
                        <a class="btn btn-pill" href="#start" data-magnetic>Book a free consultation</a>
                        <a class="btn btn-line" href="#delivery" data-magnetic>See how delivery runs</a>
                    </div>

                    <dl class="spec">
<?php foreach ($MODULES as [$idx, $slug, $label, $blurb]): ?>
                        <div class="spec-row">
                            <dt><?= e($idx) ?></dt>
                            <dd><a href="<?= e(site_path('/' . $slug)) ?>"><?= e($label) ?></a></dd>
                        </div>
<?php endforeach; ?>
                    </dl>
                </div>

                <?php /* THE CARDS ARE DECORATIVE AND SAY SO. Every number in them
                         is shape, not data — bar heights, a ring percentage, a
                         status word. None of it is presented as a measurement of
                         anything, none of it is a client claim, and the whole
                         cluster is aria-hidden so a screen reader is never read a
                         chart that does not exist. That is the same rule
                         inc/repo/metrics.php enforces for the trust bar: a number
                         is either backed or it is not shown as fact.

                         data-depth is the z translation js/cluster.js gives each
                         card, and it also scales that card's parallax travel, so
                         the nearer cards move further. */ ?>
                <div class="cluster" data-cluster aria-hidden="true">
                    <div class="fcard" data-depth="0" style="width:290px; left:34px; top:6px;">
                        <div class="fcard-head">
                            <div>
                                <div class="fcard-meta">Delivery</div>
                                <div class="fcard-title">This week</div>
                            </div>
                            <div class="fcard-pill">On track</div>
                        </div>
                        <div class="fcard-row">
                            <div class="fcard-av"></div>
                            <div style="flex:1">
                                <div class="fcard-line" style="width:74%; margin-bottom:6px"></div>
                                <div class="fcard-line" style="width:46%"></div>
                            </div>
                        </div>
                        <div class="fcard-row">
                            <div class="fcard-av is-green"></div>
                            <div style="flex:1">
                                <div class="fcard-line" style="width:62%; margin-bottom:6px"></div>
                                <div class="fcard-line" style="width:38%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="fcard" data-depth="-90" style="width:216px; left:0; top:236px;">
                        <div class="fcard-head">
                            <div class="fcard-title">Traffic</div>
                            <div class="fcard-meta">30d</div>
                        </div>
                        <div class="fcard-bars">
                            <i style="height:34%"></i><i style="height:52%"></i><i style="height:41%"></i>
                            <i style="height:68%"></i><i style="height:57%"></i><i style="height:83%"></i>
                            <i style="height:72%"></i>
                        </div>
                    </div>

                    <div class="fcard" data-depth="70" style="width:200px; left:246px; top:300px;">
                        <div class="fcard-head">
                            <div class="fcard-title">Security</div>
                            <div class="fcard-meta">Scan</div>
                        </div>
                        <div style="display:flex; align-items:center; gap:14px;">
                            <div class="fcard-ring" style="--p:86%"></div>
                            <div style="flex:1">
                                <div class="fcard-line" style="width:82%; margin-bottom:7px"></div>
                                <div class="fcard-line" style="width:56%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="fcard" data-depth="-40" style="width:172px; left:118px; top:436px;">
                        <div class="fcard-head">
                            <div class="fcard-title">Uptime</div>
                            <div class="fcard-pill">Healthy</div>
                        </div>
                        <div class="fcard-line" style="width:100%; height:5px; margin-bottom:8px"></div>
                        <div class="fcard-strip">
<?php for ($i = 0; $i < 14; $i++): ?>
                            <i<?= $i === 9 ? ' class="is-warn"' : '' ?>></i>
<?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-cue" aria-hidden="true">
            <span class="meta">Scroll</span>
            <span class="rail"><i></i></span>
        </div>
    </section>

    <?php /* ==========================================================
       02 — THE STATEMENT

       A rest. One sentence, no object, no cards, no motion beyond the line
       reveal. Opaque paper, which is what hides the stage behind it.
       ========================================================== */ ?>
    <section class="section statement grain" id="approach">
        <div class="container">
            <p class="statement-meta">The difference</p>
            <p class="statement-line" data-r="lines">
                We build digital&nbsp;systems,<br>not deliverables.
            </p>
            <p class="statement-sub">
                Five separate vendors produce five separate deliverables and no system.
                The website does not know what the campaign promised; the campaign does
                not know what the store can actually ship. We build the parts that have
                to agree, together, so that they do.
            </p>
        </div>
    </section>

    <?php /* ==========================================================
       03 — THE PLATFORM

       Five product surfaces, fanned. This REPLACES a WebGL assembly that came
       apart as you scrolled: a machined object drawn with three.js, 755 KB of
       library plus 712 KB of pre-rendered stills, and it was rejected on sight
       along with the two 3-D objects before it. The objection was never the
       execution. It was that a metal part says nothing about what this company
       sells.

       These do. Each mock is one of the five services as a working screen, and
       the fan opens on scroll: a tight stack when the section arrives,
       spreading to centre-plus-two-plus-two by the time you are through it.
       The depth is real perspective rather than a drop shadow — the outer pair
       sit 350px further back and turn 24 degrees to face the middle.

       THEY ARE CHROME, NOT CLAIMS. Every bar, ring and KPI block inside is a
       shape with no number on it. Nothing here reports a metric, because
       nothing here measured one — the same rule inc/repo/metrics.php enforces
       for the trust bar. The whole deck is aria-hidden.

       With no JS the section is a normal-height block: the deck renders as a
       readable stack and the list below carries the same five services as real
       links, which is what a crawler and a screen reader use either way.
       ========================================================== */ ?>
    <section class="section platform" id="services">
        <div class="tex-grid tex-mask-c" aria-hidden="true"></div>
        <div class="orb" aria-hidden="true" style="width:760px; height:760px; left:-14%; top:6%;
             background:radial-gradient(circle, color-mix(in srgb, var(--blue) 16%, transparent), transparent 66%);"></div>
        <div class="orb" aria-hidden="true" style="width:700px; height:700px; right:-12%; bottom:0;
             background:radial-gradient(circle, color-mix(in srgb, var(--blue-deep) 14%, transparent), transparent 66%);"></div>

        <div class="platform-sticky">
            <div class="container">
                <div class="platform-head">
                    <p class="eyebrow" data-fx="fade">One platform</p>
                    <h2 data-fx="fade">Five products.<br>One place they all live.</h2>
                    <p class="lead" data-fx="fade">
                        Every service ships its own working surface &mdash; and they read as one
                        system, not five logins stitched together.
                    </p>
                </div>
            </div>

            <div class="deck" data-deck aria-hidden="true">
<?php foreach ($DECK as $i => [$app, $label, $accent, $accent2]): ?>
                <div class="mock" data-slot="<?= (int)$i ?>" style="--c: <?= e($accent) ?>; --c2: <?= e($accent2) ?>;">
                    <div class="mock-bar">
                        <i></i><i></i><i></i>
                        <span>rafly.in/app/<?= e($app) ?></span>
                    </div>
                    <div class="mock-app">
                        <div class="mock-side">
                            <span class="mock-logo"></span>
                            <b class="is-on"></b><b></b><b></b><b></b>
                        </div>
                        <div class="mock-body">
                            <div class="mock-top"><span class="h"></span><span class="c"></span></div>
                            <div class="mock-kpis">
                                <div class="mock-kpi"><u></u><s style="background:var(--c)"></s></div>
                                <div class="mock-kpi"><u></u><s style="background:var(--c2)"></s></div>
                                <div class="mock-kpi"><u></u><s></s></div>
                            </div>
                            <div class="mock-chart">
<?php foreach ([42, 66, 54, 78, 61, 86, 72, 94] as $bar): ?>
                                <i style="height:<?= (int)$bar ?>%"></i>
<?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <span class="mock-tag"><?= e($label) ?></span>
                </div>
<?php endforeach; ?>
            </div>
        </div>

    </section>

    <?php /* The same five, as real links, in their OWN block outside the
             pinned section. Inside it they fought the sticky: .platform-sticky
             can only stick for as long as its parent has room left, so a list
             sharing that parent ate the deck's pinned range from the bottom
             and left a screen of nothing where the deck used to be. Out here
             the deck gets the full 180vh and the list gets its own space. */ ?>
    <section class="section platform-list">
        <div class="container">
            <ol class="assembly-list">
<?php foreach ($MODULES as [$idx, $slug, $label, $blurb]): ?>
                <li class="assembly-item" data-fx="in-up">
                    <a href="<?= e(site_path('/' . $slug)) ?>">
                        <span class="assembly-idx"><?= e($idx) ?></span>
                        <span class="assembly-name"><?= e($label) ?></span>
                        <span class="assembly-desc"><?= e($blurb) ?></span>
                    </a>
                </li>
<?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php /* ==========================================================
       03b — APPS

       Rafly builds mobile apps, and nothing on the page said so.

       ON PAPER, NOT ON INK. The prototype put this section on the dark ground.
       Naveen asked for the opposite ("neeche bhi light theme hoga same"), and
       he is right: a dark band three quarters of the way down read as a
       different site rather than as the next paragraph. Depth without a dark
       ground comes from the same place the deck above gets it — perspective, a
       real turn toward the middle, and a lit top edge on each shell. The
       SCREENS stay dark, because a phone screen is dark, and that is also what
       keeps them legible against paper.
       ========================================================== */ ?>
    <section class="section apps" id="apps">
        <div class="tex-hatch" aria-hidden="true"></div>
        <div class="orb" aria-hidden="true" style="width:900px; height:900px; left:50%; top:46%;
             transform:translate(-50%,-50%);
             background:radial-gradient(circle, color-mix(in srgb, var(--blue) 18%, transparent), transparent 64%);"></div>

        <div class="apps-sticky">
            <div class="container apps-grid">
                <div class="apps-copy">
                    <p class="eyebrow" data-fx="fade">On the phone</p>
                    <h2 data-fx="fade">Mobile apps, built by<br>the same team.</h2>
                    <p class="lead" data-fx="fade">
                        Native and cross-platform builds, store submission, and the releases
                        after it &mdash; from the people already running your site, so the app
                        and the web are never two versions of the truth.
                    </p>
                    <ul class="apps-feat">
<?php foreach ($APP_POINTS as $point): ?>
                        <li data-fx="in-up"><span><?= icon('check') ?></span><?= e($point) ?></li>
<?php endforeach; ?>
                    </ul>
                    <p class="apps-cta">
                        <a class="btn btn-line" href="<?= e(site_path('/contact')) ?>" data-magnetic>Talk about an app build</a>
                    </p>
                </div>

                <div class="phones" data-phones aria-hidden="true">
<?php foreach ($PHONES as $i => [$accent, $accent2]): ?>
                    <div class="phone" data-slot="<?= (int)$i ?>" style="--c: <?= e($accent) ?>; --c2: <?= e($accent2) ?>;">
                        <span class="phone-notch"></span>
                        <div class="phone-screen">
                            <span class="ttl"></span>
                            <span class="sub"></span>
                            <div class="phone-hero"></div>
                            <div class="phone-list">
                                <div class="it"><u></u><b></b></div>
                                <div class="it"><u class="is-dim"></u><b style="width:70%"></b></div>
                                <div class="it"><u class="is-dim"></u><b style="width:52%"></b></div>
                            </div>
                            <div class="phone-nav"><i class="on"></i><i></i><i></i><i></i></div>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       04 — DELIVERY

       Calm on purpose, straight after the expensive section. A hairline
       spine, real stage names, real durations. No 3-D, no WebGL, no
       particles.
       ========================================================== */ ?>
    <section class="section ground-2 grain" id="delivery">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Delivery</p>
                <h2>How the work <span class="soft">actually runs</span></h2>
            </div>

            <ol class="flow">
<?php foreach ($FLOW as $i => [$name, $when, $desc]): ?>
                <li class="flow-step" data-r="rise">
                    <span class="flow-idx"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="flow-body">
                        <h3><?= e($name) ?></h3>
                        <p><?= e($desc) ?></p>
                    </div>
                    <span class="flow-when"><?= e($when) ?></span>
                </li>
<?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php /* ==========================================================
       05 — THE STACK

       Typographic, and deliberately static. Nearly zero animation: after the
       exploded sequence and before the dark chapter, this is a rest.
       ========================================================== */ ?>
    <section class="section ground-3" id="stack">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Built on</p>
                <h2>Named tools, <span class="soft">no logo wall</span></h2>
            </div>

            <div class="stack">
<?php foreach ($STACK as [$group, $items]): ?>
                <div class="stack-col">
                    <h3 class="stack-label"><?= e($group) ?></h3>
                    <ul>
<?php foreach ($items as $item): ?>
                        <li><?= $item ?></li>
<?php endforeach; ?>
                    </ul>
                </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       06 — SELECTED WORK — THE ONE DARK CHAPTER

       Conditional. Renders only when there is real, published,
       non-placeholder work to show. Nothing here is generated to fill space.
       ========================================================== */ ?>
<?php if ($caseStudies): ?>
    <section class="section ground-chapter grain seam-top seam-bottom" id="work">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Selected work</p>
                <h2>What it looked like <span class="soft">in practice</span></h2>
            </div>

            <div class="work-grid" data-r="group">
<?php foreach ($caseStudies as $i => $cs): ?>
                <article class="work-card">
                    <span class="work-idx"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= e($cs['client_name'] ?? '') ?></h3>
<?php if (!empty($cs['summary'])): ?>
                    <p><?= e($cs['summary']) ?></p>
<?php endif; ?>
<?php if (!empty($cs['slug'])): ?>
                    <a class="link-arrow" href="<?= e(site_path('/case-studies#' . $cs['slug'])) ?>">Read the detail</a>
<?php endif; ?>
                </article>
<?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

    <?php /* ==========================================================
       07 — HONEST LIMITS

       The strongest section on the page, and the one no competitor
       publishes. Every line is lifted from the `boundaries` array of a real
       service, so what the homepage refuses and what the service page
       refuses are the same file.
       ========================================================== */ ?>
    <section class="section" id="limits">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Honest limits</p>
                <h2>What we will <span class="soft">not</span> take on</h2>
                <p class="lead">
                    Fifteen of them, in writing, before you ask. A studio that will not
                    name its edges has not found them yet.
                </p>
            </div>

            <div class="limits">
<?php foreach ($limits as [$svc, $title, $desc]): ?>
                <div class="limit">
                    <p class="limit-svc"><?= e($svc) ?></p>
                    <h3><?= e($title) ?></h3>
                    <p><?= e($desc) ?></p>
                </div>
<?php endforeach; ?>
            </div>

            <div class="compare-wrap">
                <table class="compare">
                    <caption class="sr-only">Rafly compared with a traditional agency and with hiring separate freelancers</caption>
                    <thead>
                        <tr>
                            <th scope="col"><span class="sr-only">Aspect</span></th>
                            <th scope="col">Rafly</th>
                            <th scope="col">Traditional agency</th>
                            <th scope="col">Separate freelancers</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($COMPARE as [$aspect, $us, $agency, $free]): ?>
                        <tr>
                            <th scope="row"><?= e($aspect) ?></th>
                            <td class="is-us"><?= e($us) ?></td>
                            <td><?= e($agency) ?></td>
                            <td><?= e($free) ?></td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       08 — PRICING AND FAQ
       ========================================================== */ ?>
    <section class="section ground-2 grain" id="pricing">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Bundles</p>
                <h2>Three bundles. <span class="soft">One scope, one price.</span></h2>
                <p class="lead">
                    Every package is scoped in a free consultation first — you get a
                    number before any work starts, not an hourly rate that moves.
                </p>
            </div>

            <div class="grid grid-3" data-r="group">
<?php foreach ($bundles as $i => $t): require __DIR__ . '/partials/price-card.php'; endforeach; ?>
            </div>

            <div class="faq" id="faq">
                <div class="faq-head">
                    <h3>Before you get in touch</h3>
                    <p>
                        Anything not covered here,
                        <a href="<?= e(whatsapp_link('Hi Rafly, I have a question.')) ?>" target="_blank" rel="noopener">ask on WhatsApp</a>
                        — a person replies within one working day.
                    </p>
                </div>

                <div class="accordion" data-accordion="single">
<?php foreach ($FAQS as $i => $f): ?>
                    <div class="accordion-item">
                        <button class="accordion-trigger" type="button"
                                id="faq-t-<?= $i ?>" aria-expanded="false" aria-controls="faq-p-<?= $i ?>">
                            <span><?= e($f['q']) ?></span>
                            <span class="accordion-icon" aria-hidden="true"></span>
                        </button>
                        <div class="accordion-panel" id="faq-p-<?= $i ?>" role="region" aria-labelledby="faq-t-<?= $i ?>" hidden>
                            <p><?= e($f['a']) ?></p>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       09 — THE CLOSE

       The second and last call to action on the page. The object returns,
       assembled and at rest — the same render as the hero, because that is
       exactly what it is: the thing, put back together.
       ========================================================== */ ?>
    <section class="section close" id="start">
        <div class="container">
            <div class="close-grid">
                <div class="close-copy">
                    <p class="eyebrow">Start here</p>
                    <h2>Let's build <span class="soft">what's next.</span></h2>
                    <p class="lead">
                        Tell us what is in front of you. We will come back with a scope and
                        a number, or tell you plainly that it is not a job for us.
                    </p>
                    <p class="meta-row">
                        <span><b>First step</b> a call, not a pitch deck</span>
                        <span><b>Reply</b> within one working day</span>
                    </p>

                    <?php /* The rendered object that used to sit here went with the
                             rest of the 3-D layer. Nothing replaces it: this is a
                             form, and the quietest thing that can be beside a form
                             is the ground it is already standing on. */ ?>
                </div>

                <div class="close-form">
<?php
    $formId      = 'homeLeadForm';
    $submitLabel = 'Send requirements';
    require __DIR__ . '/partials/lead-form.php';
?>
                </div>
            </div>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
