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
 * The app section, and it is deliberately CAPABILITY rather than claim. Not
 * "we shipped N apps" and not a store rating: those are numbers, and the site
 * does not publish a number it cannot back. These say what the work is.
 */
$APP_POINTS = [
    'iOS and Android, native or cross-platform',
    'Store submission, review responses, and the release after that',
    'One backend behind the app and the website, never two',
];

/**
 * THE GALLERY — what a client actually commissions, as five covers.
 *
 * Naveen sent OriginKit's coverflow component and asked for the same thing
 * with our own pictures. WE HAVE NO PICTURES, and that is a decision rather
 * than a gap: 24 stock photographs were deleted from this site because they
 * were other companies' premises standing in for our work, and swapping them
 * back in under a nicer component would be the same lie in a better frame.
 *
 * So every cover here is DRAWN — a gradient ground and an abstract of the
 * screen that kind of build produces. Nothing is fetched, nothing is a
 * photograph of somebody else's business, and the day there are screenshots of
 * real Rafly work they drop into the same slot with no other change.
 *
 * Columns: title, what it is, the two gradient stops, and which cover layout.
 * The stops are the service accents as light/dark pairs — fills, on artwork,
 * never text.
 */
$GALLERY = [
    ['Online stores',        'Catalogue, checkout, and the operations behind them.',        '#0d6b34', '#2fa35e', 'grid'],
    ['Booking sites',        'Enquiries and appointments that land where your team works.', '#0a63ff', '#5c93ff', 'rows'],
    ['Brand and content',    'The pages that say what you do, written in your words.',      '#6134c9', '#9a6ff0', 'copy'],
    ['Dashboards and apps',  'The internal screens that run the business day to day.',      '#0230c6', '#3d6ee8', 'chart'],
    ['Mobile apps',          'iOS and Android, on the same backend as the site.',           '#046070', '#0a97ad', 'phone'],
];

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
    <section class="section hero hero-scene" id="home" data-hero>
        <?php /* Painted by js/aurora.js — raw WebGL2, one fragment shader, one
                 triangle. Not three.js: loading 365 KB of scene graph, camera
                 and material system to draw a single full-screen quad is not
                 defensible. aria-hidden because it is the room's lighting. */ ?>
        <canvas class="hero-aurora" data-aurora aria-hidden="true"></canvas>

        <?php /* THE FLOOR. A real perspective grid receding to a horizon, which
                 is the one thing the aurora could not give the hero: depth.
                 Colour tells you the mood of a page; a converging grid tells
                 you there is a space behind the words.

                 IT IS CSS, NOT SHADER. rotateX on a plane of two repeating
                 gradients is a genuine 3-D projection done by the compositor,
                 and it costs no JavaScript, no WebGL and no bytes — so it is
                 there for a visitor with scripts off, with a dead GPU, and on
                 a phone, where the shader is gated away. The lines travel by
                 animating background-position, which is the same convergence
                 the reference does with geometry.

                 It is masked away from the left so the headline never sits on
                 a grid line, and everything in it is --blue as a FILL at low
                 alpha, never as a mark. */ ?>
        <div class="hero-floor" aria-hidden="true">
            <span class="hero-floor-plane"></span>
            <span class="hero-floor-nodes"></span>
        </div>
        <span class="hero-horizon" aria-hidden="true"></span>

        <div class="container">
            <div class="hero-grid">
                <div class="hero-copy">
                    <p class="meta-row" data-r="lift">
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

                    <p class="hero-lead" data-r="lift">
                        Web, security, marketing, content and commerce — built by one team,
                        on one plan, with one person accountable. Not five vendors who have
                        never spoken to each other.
                    </p>

                    <div class="hero-cta" data-r="lift">
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
                    <p class="eyebrow" data-r="lift">One platform</p>
                    <h2 data-r="lift">Five products.<br>One place they all live.</h2>
                    <p class="lead" data-r="lift">
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
<?php require __DIR__ . '/partials/deck-mock.php'; ?>
                    </div>
                    <span class="mock-tag"><?= e($label) ?></span>
                </div>
<?php endforeach; ?>
            </div>
        </div>

    </section>

    <?php /* The same five, as real links, in their OWN block outside the pinned
             section. Inside it they fought the sticky: .platform-sticky can
             only stick for as long as its PARENT has room left, so a list
             sharing that parent ate the deck pinned range from the bottom and
             left a screen of nothing where the deck had been.

             A BENTO, NOT A LIST. They were a column of rows beside a panel,
             which is a column of text beside an empty half-page and reads as
             the section having run out rather than having finished. Six tiles
             on three columns close it with nothing left over: the lead service
             across two, the other four filling the rest, and the panel across
             all three as the last word.

             The cards are the ones Naveen has picked out at every round — glass
             over the paper ground, a gradient border that arrives on hover, and
             a sheen that follows the cursor. The sheen needs js/sheen.js for
             the follow; without it --mx/--my resolve to the card's centre and
             hover is a centred glow, which is a designed state rather than a
             missing one. */ ?>
    <section class="section platform-list">
        <div class="tex-hatch" aria-hidden="true"></div>
        <div class="container svc-bento">
<?php foreach ($MODULES as [$idx, $slug, $label, $blurb, $token, $glyph]): ?>
            <a class="svc-card" data-sheen data-r="lift"
               href="<?= e(site_path('/' . $slug)) ?>"
               style="--sc: var(<?= e($token) ?>);">
                <span class="svc-card-top">
                    <span class="svc-card-glyph"><?= icon($glyph) ?></span>
                    <span class="svc-card-idx"><?= e($idx) ?></span>
                </span>
                <h3 class="svc-card-name"><?= e($label) ?></h3>
                <p class="svc-card-desc"><?= e($blurb) ?></p>
                <span class="svc-card-go">Explore <?= icon('arrow-up-right') ?></span>
            </a>
<?php endforeach; ?>

            <aside class="platform-panel" data-r="lift">
                <p class="eyebrow">What one platform means</p>
                <h3>Five surfaces, one account, one bill.</h3>

                <?php /* Structural, not claimed. The count comes from
                         services_all(), the same array the list beside it is
                         rendered from, so this number cannot drift from the
                         page it sits on and cannot be wrong. Everything else
                         here is a property of how the work is organised — not
                         a metric, not a result, and not something a client
                         would have to take on trust. */ ?>
                <dl class="platform-facts">
                    <div>
                        <dt>Services</dt>
                        <dd><?= (int)count($MODULES) ?>, under one scope</dd>
                    </div>
                    <div>
                        <dt>Contact</dt>
                        <dd>One person, accountable</dd>
                    </div>
                    <div>
                        <dt>Security review</dt>
                        <dd>In every package</dd>
                    </div>
                    <div>
                        <dt>Ownership</dt>
                        <dd>Full IP, transferred on final payment</dd>
                    </div>
                </dl>

                <p class="platform-panel-foot">
                    Add a service later and it joins the same scope and the same
                    invoice &mdash; there is no second onboarding, and no second
                    team to bring up to speed.
                </p>

                <a class="btn btn-line" href="<?= e(site_path('/pricing')) ?>" data-magnetic>
                    See what a package includes <?= icon('arrow-up-right') ?>
                </a>
            </aside>
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
            <?php /* CENTRED, not copy-left and phones-right. Three phones on
                     the right of a paragraph read as an illustration OF the
                     paragraph; the same three in the middle of the section are
                     the subject of it. It also matches the platform section
                     directly above, which is the composition this page has
                     settled on for anything that is one object being shown. */ ?>
            <div class="container">
                <div class="apps-head">
                    <p class="eyebrow" data-r="lift">On the phone</p>
                    <h2 data-r="lift">Mobile apps, built by the same team.</h2>
                    <p class="lead" data-r="lift">
                        Native and cross-platform builds, store submission, and the releases
                        after it &mdash; from the people already running your site, so the app
                        and the web are never two versions of the truth.
                    </p>
                </div>
            </div>

            <div class="phones" data-phones aria-hidden="true">
<?php foreach ($PHONES as $i => [$accent, $accent2, $variant]): ?>
                <div class="phone" data-slot="<?= (int)$i ?>" style="--c: <?= e($accent) ?>; --c2: <?= e($accent2) ?>;">
                    <span class="phone-notch"></span>
<?php require __DIR__ . '/partials/phone-screen.php'; ?>
                </div>
<?php endforeach; ?>
            </div>

            <div class="container">
                <ul class="apps-feat">
<?php foreach ($APP_POINTS as $point): ?>
                    <li data-r="lift"><span><?= icon('check') ?></span><?= e($point) ?></li>
<?php endforeach; ?>
                </ul>
                <p class="apps-cta">
                    <a class="btn btn-line" href="<?= e(site_path('/contact')) ?>" data-magnetic>Talk about an app build</a>
                </p>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       03c — THE GALLERY

       OriginKit's coverflow, at its own numbers — Naveen sent the component
       source, so the geometry in css/09-scenes.css is lifted value for value
       rather than eyeballed: 1600 perspective, 240px of travel and 240px of
       depth per step, 12 degrees of turn, 8 of lean, 0.16 of scale, a 40%
       veil on everything that is not the centre.

       WHAT CHANGED IS THE PICTURES AND THE INPUT. The reference ships
       photographs and advances on a timer; this draws its own covers and
       advances on scroll depth, because a section is not a carousel and a
       carousel that runs whether or not anyone is looking at it is a thing to
       ignore rather than to read.

       Clicking a card does NOT set an index. It scrolls the page to the depth
       at which that card is centred, so the scroll position stays the only
       thing that decides what is active and the two can never disagree. The
       dots underneath do the same and are the keyboard path.

       Without JavaScript every card is laid out exactly where it would be with
       the first one active — the stylesheet sets the same custom properties
       js/gallery.js writes. It is the same design, holding still.
       ========================================================== */ ?>
    <section class="section gallery" id="build">
        <div class="tex-hatch" aria-hidden="true"></div>

        <div class="gallery-sticky">
            <div class="container">
                <div class="gallery-head">
                    <p class="eyebrow" data-r="lift">What we build</p>
                    <h2 data-r="lift">Five kinds of build,<br>one team behind them.</h2>
                    <p class="lead" data-r="lift">
                        Most work is one of these five. Scroll through them &mdash; or pick one,
                        and the deck comes to it.
                    </p>
                </div>
            </div>

            <div class="cf">
                <div class="cf-track" data-gallery>
<?php foreach ($GALLERY as $i => [$title, $what, $c1, $c2, $art]): ?>
                    <div class="cf-card" data-slot="<?= (int)$i ?>" style="--c: <?= e($c1) ?>; --c2: <?= e($c2) ?>;">
                        <span class="cf-art" aria-hidden="true">
                            <span class="cf-sheet is-<?= e($art) ?>">
                                <i></i><i></i><i class="is-dim"></i><i></i><i class="is-dim"></i><i></i>
                            </span>
                        </span>
                        <span class="cf-card-label">
                            <b><?= e($title) ?></b>
                            <span><?= e($what) ?></span>
                        </span>
                    </div>
<?php endforeach; ?>
                </div>
            </div>

            <?php /* Real buttons, and the only part of this a keyboard needs.
                     They move the PAGE, which is the same thing a click on a
                     card does — one input, one source of truth. */ ?>
            <div class="cf-dots">
<?php foreach ($GALLERY as $i => [$title]): ?>
                <button class="cf-dot" type="button" data-cf-dot
                        aria-current="<?= $i === 0 ? 'true' : 'false' ?>">
                    <span class="visually-hidden">Show <?= e($title) ?></span>
                </button>
<?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       04 — DELIVERY

       Calm on purpose, straight after the expensive section. A hairline
       spine, real stage names, real durations. No 3-D, no WebGL, no
       particles.
       ========================================================== */ ?>
    <section class="section ground-2 grain has-tex" id="delivery">
        <div class="tex-hatch" aria-hidden="true"></div>
        <div class="orb" aria-hidden="true" style="width:620px; height:620px; left:-10%; bottom:-14%;
             background:radial-gradient(circle, color-mix(in srgb, var(--blue) 13%, transparent), transparent 66%);"></div>
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
    <section class="section ground-3 has-tex" id="stack">
        <div class="tex-grid tex-mask-c" aria-hidden="true"></div>
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
    <section class="section has-tex" id="limits">
        <div class="tex-hatch" aria-hidden="true"></div>
        <div class="orb" aria-hidden="true" style="width:700px; height:700px; right:-12%; top:-8%;
             background:radial-gradient(circle, color-mix(in srgb, var(--blue-deep) 12%, transparent), transparent 66%);"></div>
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Honest limits</p>
                <h2>What we will <span class="soft">not</span> take on</h2>
                <p class="lead">
                    Fifteen of them, in writing, before you ask. A studio that will not
                    name its edges has not found them yet.
                </p>
            </div>

            <div class="limits" data-r="group">
<?php foreach ($limits as [$svc, $title, $desc]): ?>
                <div class="limit">
                    <p class="limit-svc"><?= e($svc) ?></p>
                    <h3><?= e($title) ?></h3>
                    <p><?= e($desc) ?></p>
                </div>
<?php endforeach; ?>
            </div>

            <div class="compare-wrap" data-r="lift">
                <table class="compare">
                    <caption class="visually-hidden">Rafly compared with a traditional agency and with hiring separate freelancers</caption>
                    <thead>
                        <tr>
                            <th scope="col"><span class="visually-hidden">Aspect</span></th>
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
    <section class="section ground-2 grain has-tex" id="pricing">
        <div class="tex-hatch" aria-hidden="true"></div>
        <div class="orb" aria-hidden="true" style="width:760px; height:760px; left:-14%; top:34%;
             background:radial-gradient(circle, color-mix(in srgb, var(--blue) 12%, transparent), transparent 66%);"></div>
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
    <section class="section close has-tex" id="start">
        <div class="tex-grid tex-mask-c" aria-hidden="true"></div>
        <div class="orb" aria-hidden="true" style="width:820px; height:820px; right:-16%; bottom:-24%;
             background:radial-gradient(circle, color-mix(in srgb, var(--blue) 15%, transparent), transparent 64%);"></div>
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
