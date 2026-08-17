<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * Single source for the FAQ: renders the accordion further down AND the
 * FAQPage structured data below. Keeping one array means the rich result can
 * never claim something the page does not actually say.
 *
 * Trimmed to 8 from an earlier 12 — a long FAQ signals an unclear offer.
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
 * The P.E.A.C.E. framework and the delivery flow. One array each — previously
 * five near-identical blocks of markup where only three strings differed, which
 * is how the fourth one ends up with a stale icon nobody notices.
 */
$PEACE = [
    ['P', 'compass',        'Planning',     'We map your goals, current setup, and where the gaps are — before any code, content, or campaign work begins.'],
    ['E', 'terminal',       'Execution',    'Our web, content, marketing, and security specialists build and ship inside one coordinated workflow.'],
    ['A', 'search',         'Analysis',     'We track traffic, engagement, and conversion data so decisions are based on evidence, not guesswork.'],
    ['C', 'message-circle', 'Consultation', 'Plain-language updates and recommendations — no technical jargon, no hiding behind reports.'],
    ['E', 'gauge',          'Enhancement',  'Ongoing improvements to your site, campaigns, and security posture as your business grows.'],
];

$FLOW = [
    ['Discovery',       '2-3 Days',  'A call to understand your goals, your current setup, and which parts of your growth need the most help. No pitch deck until we know what is actually broken.'],
    ['Package & Plan',  '3-5 Days',  'We scope a bundled package — web, content, marketing, security, e-commerce — matched to what you actually need, and price it up front. One scope, one number.'],
    ['Build & Execute', '2-6 Weeks', 'Development, content and campaign work run in parallel inside one coordinated team, against one plan. Nothing waits on a handoff between vendors who have never spoken.'],
    ['Launch',          '1 Week',    'Your site, store or campaign goes live with a baseline security review already done — forms, sessions and access handling — because it was part of the build, not a phase after it.'],
    ['Ongoing Support', 'Ongoing',   'Monitoring, updates and continuous improvement, so growth does not stall the day after launch. The same team that built it is the team that keeps it running.'],
];

/** The three problems the offer exists to solve. */
$CHALLENGES = [
    ['cables',    'Five vendors, no shared context',
     'Juggling separate vendors for your website, ads, content, automation and store — and none of them are talking to each other?'],
    ['hourglass', 'Launches that slip, every time',
     'Watching launches slip because the designer is waiting on the developer who is waiting on the marketer?'],
    ['lock',      'Security you have assumed, not checked',
     'Not sure if your site, checkout, and customer data are actually secure — or just assumed to be?'],
];

/** The comparison table — Rafly against the two real alternatives. */
$COMPARE = [
    ['Service coverage', 'Web, content, marketing, security & e-commerce in one team', 'Usually just one specialty', 'No shared context between people'],
    ['Accountability',   'One point of contact, accountable for outcomes',             'Account manager, execution outsourced further', 'You coordinate everyone yourself'],
    ['Security',         'Baseline review included in every package',                  'Usually a separate paid add-on', 'Rarely considered at all'],
    ['Delivery speed',   'Parallel execution across one coordinated team',             'Sequential handoffs between departments', 'Depends entirely on individual availability'],
    ['Pricing structure','Clear, bundled packages',                                    'Custom retainers, scope creep', 'Paid per task, costs add up'],
    ['Communication',    'Plain-language updates, no jargon',                          'Layered reporting, slower answers', 'Manual coordination on your end'],
];

/** What the work is actually built on. Named tools, no invented partnerships. */
$STACK = ['PHP &amp; Laravel', 'WordPress', 'Shopify', 'JavaScript', 'MySQL', 'Google Ads', 'Analytics 4', 'SSL &amp; WAF'];

/**
 * Homepage content that the admin owns: case studies and testimonials.
 *
 * Both are read here, before any output, so a slow or failed query cannot leave
 * a half-rendered page. db_available() never throws — with the database down
 * each section renders empty and the rest of the homepage is unaffected, which
 * matters because everything else on this page is static and still sells.
 */
/* array_values(): services_all() is keyed by SLUG, so an unguarded
   `foreach ($services as $i => $s)` hands back a string where the numbered
   markers need an integer. Re-indexing once here is safer than remembering it
   at every loop. */
$services     = array_values(services_all());
$trustStats   = metrics_trust_bar();
$caseStudies  = case_studies_all(3);
$testimonials = testimonials_all(3);
$bundles      = bundles_all();

/** Deterministic tint per service, so the carousel reads as a set. */
$svcTint = ['web' => 't-deep', 'security' => 't-ink', 'marketing' => 't-orange', 'content' => 't-blue', 'ecom' => 't-tint'];

/**
 * The hero arc: thirteen tiles on a 240-degree ring, n running -6..+6 so the
 * bottom of the circle stays clear for the call to action.
 *
 * Each row is [icon, label, tint]. The tiles are DRAWN, not photographed, and
 * that is a decision with two reasons behind it.
 *
 * Photography failed twice here. A CC0 search for digital-agency subjects
 * returns a gas mask, a Twitch logo and a Burger King sign — the free-licence
 * pools are not deep enough to curate a coherent ring, and every near miss puts
 * somebody else's trademark in our hero. Screenshots of our own pages failed
 * differently: this site is light and text-heavy, so a 760px square of it
 * shrinks to grey mush in a 124px tile.
 *
 * A drawn tile has neither problem. It is on-topic by construction, it is sharp
 * at any size and any pixel ratio, it costs no bytes and no licence, and it
 * recolours with the tokens. The ring says what we do, in twelve words.
 */
$ARC = [
    ['code',          'Web',        't-deep'],
    ['shield',        'Security',   't-ink'],
    ['megaphone',     'Marketing',  't-orange'],
    ['pencil',        'Content',    't-blue'],
    ['shopping-cart', 'Commerce',   't-tint'],
    ['search',        'SEO',        't-ink'],
    ['gauge',         'Speed',      't-blue'],
    ['bot',           'Automation','t-deep'],
    ['pie-chart',     'Analytics',  't-orange'],
    ['layers',        'Design',     't-tint'],
    ['database',      'Hosting',    't-ink'],
    ['message-circle','Support',    't-blue'],
    ['rocket',        'Launch',     't-deep'],
];

$page = [
    'id'        => 'home',
    'title'     => 'Rafly | Digital Growth — Build Fast, Grow Faster, Scale Smarter',
    'desc'      => 'One partner, one bundled package — web development, AI automation, content creation, digital marketing, web security and e-commerce support, working together instead of stitched together from multiple vendors.',
    'bodyClass' => 'page-home',
    'styles'    => ['home'],
    'module'    => 'stage3d',
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
       HERO — the headline inside a ring of work.

       Three layers: a soft bloom on the paper, the arc of tiles, and the
       words. The arc is placed entirely in CSS (one transform per tile, see
       css/07-fx.css section 2) and deals itself out from the centre of the
       headline on load.

       The device mockups used to live here. They moved to the app stage
       below, where they have room to arrive from the two sides and be the
       point of their own section rather than a detail in someone else's.
       ========================================================== */ ?>
    <section class="section hero" id="home">
        <div class="container">
            <div class="hero-stage">
                <div class="hero-glow" aria-hidden="true"></div>

                <div class="arc" aria-hidden="true" data-fx="spin" style="--turn: 7deg;">
                    <?php foreach ($ARC as $i => [$ico, $label, $tint]):
                        $n    = $i - 6;              // -6 .. +6, so the ring is centred
                        $out  = abs($n);             // steps from the top of the arc
                        $tier = $out >= 5 ? ' is-wide-only' : ($out >= 3 ? ' is-tablet-up' : '');
                    ?>
                        <div class="arc-cell<?= $tier ?>" style="--n: <?= $n ?>;">
                            <?php /* --i drives the deal delay, counted outward from the
                               centre tile so the ring opens from the middle rather than
                               unrolling from one end. */ ?>
                            <span class="arc-tile <?= e($tint) ?>" style="--i: <?= $out ?>;">
                                <?php /* Counter-rotated as one unit. The tile stays tangent
                                   to the ring — that is what makes it read as a ring rather
                                   than a bent row — but its contents come back upright, or
                                   the labels on the flanks are printed sideways. */ ?>
                                <span class="arc-inner">
                                    <?= icon($ico, 'arc-ico') ?>
                                    <span class="arc-label"><?= e($label) ?></span>
                                </span>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="hero-head">
                    <p class="eyebrow">One partner &middot; Every service</p>
                    <h1 class="display" data-r="lines">
                        <span>Build fast.</span>
                        <span>Grow faster.</span>
                        <span><u class="ul-draw is-orange">Scale smarter.</u></span>
                    </h1>
                    <p class="lead hero-lead">
                        Web development, AI automation, content, marketing, security and e-commerce
                        support &mdash; one team, one bundled package, instead of five vendors who
                        have never met.
                    </p>
                    <div class="cluster cluster-4 hero-actions">
                        <button type="button" class="btn btn-pill btn-lg" data-modal-open="consultationModal">
                            Get a free quote <?= icon('arrow-up-right') ?>
                        </button>
                        <a class="btn btn-line btn-lg" href="#services">See what we ship</a>
                    </div>
                </div>

                <?php /* The arc only covers 240 degrees, so the bottom of the stage
                   is empty by construction. The cue is what belongs there. A link
                   rather than an ornament, so the keyboard gets the shortcut too,
                   and a sibling of .hero-head rather than a child so it anchors to
                   the stage instead of to the bottom of the buttons. */ ?>
                <a class="hero-cue" href="#challenges">
                    <span>Scroll</span>
                    <span class="hero-cue-rail" aria-hidden="true"></span>
                </a>
            </div>
        </div>
    </section>

    <?php /* ========================= MARQUEE ========================= */ ?>
    <section class="section-sm hero-strip">
        <div class="container">
            <p class="tag hero-strip-label">Five services &middot; one package &middot; one invoice</p>
        </div>
        <div class="marquee" aria-hidden="true">
            <div class="marquee-track">
                <?php foreach ($services as $s): ?>
                    <span class="marquee-item"><?= icon($s['icon']) ?> <?= e($s['title']) ?></span>
                    <span class="marquee-dot"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ======================== TRUST BAR ========================= */ ?>
    <section class="section-sm trust-bar">
        <div class="container">
            <div class="grid grid-4 trust-grid" data-r="group">
                <?php foreach ($trustStats as $stat): ?>
                    <?php /* The REAL number ships in the markup, not the "0" the
                       count-up starts from. A visitor whose JS is blocked, slow or
                       broken by an extension must never read "0 Projects Delivered" —
                       not merely un-animated but actively false. runCounter's first
                       frame writes ~0, so the count-up still starts from zero. */ ?>
                    <div class="stat">
                        <span class="stat-value"
                              data-count="<?= (int)$stat['value'] ?>"<?= $stat['suffix'] !== '' ? ' data-suffix="' . e($stat['suffix']) . '"' : '' ?>><?= (int)$stat['value'] . e($stat['suffix']) ?></span>
                        <span class="stat-label"><?= e($stat['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ======================== CHALLENGES ======================== */ ?>
    <section class="section band-soft" id="challenges">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Are you facing these challenges?</p>
                    <h2>Still managing growth through multiple vendors?</h2>
                </div>
                <p class="lead">Endless handoffs, mismatched timelines, and no single team accountable for the outcome &mdash; sound familiar?</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($CHALLENGES as $i => [$img, $title, $copy]): ?>
                    <article class="card card-hover challenge-card">
                        <div class="card-media">
                            <?= photo("assets/photos/challenge-{$img}.jpg", '') ?>
                        </div>
                        <div class="card-body">
                            <span class="numeral challenge-num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                            <h3 class="card-title"><?= e($title) ?></h3>
                            <p class="card-text"><?= e($copy) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ========================= WHO WE ARE ======================== */ ?>
    <section class="section" id="about">
        <div class="container">
            <div class="split split-wide-r">
                <div class="figure figure-tall glow-blue" data-r="left">
                    <?= photo('assets/photos/about-desk.jpg', 'A Rafly working desk — laptop, notes and a phone') ?>
                    <span class="badge badge-ink figure-badge">Since day one, one team</span>
                </div>

                <div data-r="right">
                    <p class="eyebrow">Who we are</p>
                    <h2>One team instead of <span class="soft">multiple vendors</span></h2>
                    <p class="lead">
                        Rafly is a digital growth partner built around one idea: your website,
                        content, marketing, security, and store operations shouldn't come from
                        five different places.
                    </p>
                    <p>
                        We package web development, content creation, digital marketing, web
                        security, and e-commerce support into one coordinated team &mdash; and
                        we're actively building out AI automation and app development
                        capabilities to round out the offering.
                    </p>

                    <div class="grid grid-3 mini-stats">
                        <div class="stat"><span class="stat-value">5</span><span class="stat-label">Services bundled</span></div>
                        <div class="stat"><span class="stat-value">1</span><span class="stat-label">Point of contact</span></div>
                        <div class="stat"><span class="stat-value">0</span><span class="stat-label">Handoff gaps</span></div>
                    </div>

                    <p class="cluster" style="margin-top:2rem">
                        <a class="btn btn-pill" href="/about">More about Rafly <?= icon('arrow-right') ?></a>
                        <a class="link-arrow" href="/team">Meet the team <?= icon('arrow-right') ?></a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php /* ================== SERVICES — 3D CAROUSEL ================== */ ?>
    <section class="section" id="services">
        <div class="container container-wide">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">What we do</p>
                <h2>Five services. <span class="soft">One package.</span></h2>
                <p class="lead">Everything below is delivered by the same team, under one point of contact &mdash; not stitched together from five separate invoices.</p>
            </div>

            <div class="carousel" data-r="lift">
                <div class="carousel-scene">
                    <div class="carousel-ring">
                        <?php foreach ($services as $i => $s): ?>
                            <article class="carousel-cell <?= e($svcTint[$s['key']] ?? '') ?>">
                                <?= photo("assets/photos/service-{$s['key']}.jpg", '') ?>
                                <div class="carousel-cell-label">
                                    <span class="tag"><?= e($s['badge']) ?></span>
                                    <h3><?= e($s['title']) ?></h3>
                                    <a class="link-arrow carousel-cell-link" href="/<?= e($s['slug']) ?>">
                                        Explore <?= icon('arrow-right') ?>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="carousel-controls">
                    <button type="button" class="carousel-nav" data-carousel="prev" aria-label="Previous service"><?= icon('chevron-right') ?></button>
                    <div class="carousel-dots"></div>
                    <button type="button" class="carousel-nav" data-carousel="next" aria-label="Next service"><?= icon('chevron-right') ?></button>
                </div>
                <p class="visually-hidden" data-carousel-status aria-live="polite"></p>
            </div>

            <div class="steps-row svc-index" data-r="group">
                <?php foreach ($services as $i => $s): ?>
                    <a class="step-item svc-index-item" href="/<?= e($s['slug']) ?>">
                        <span class="step-num">+<?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="step-title"><?= e($s['title']) ?></span>
                        <span class="step-text"><?= e($s['card']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <p class="services-note">
                We're also actively building out AI automation and app development capabilities
                &mdash; <button type="button" class="link-inline" data-modal-open="consultationModal">reach out</button>
                if you'd like to be among the first to try them.
            </p>
        </div>
    </section>

    <?php /* ========================= APP STAGE =========================
       The laptop and the phone arrive from the two sides and settle
       overlapping in the middle, with a panel of live numbers rising in
       front. All three are placed with the `translate` property and moved
       with `transform`, which is what lets the arrival be animated without
       the resting composition depending on the animation having run — see the
       note at css/07-fx.css section 2b.

       The screens are real captures of /pricing and /blog taken by
       inc/tools/capture-mockups.mjs, so they cost nothing, need no licence,
       and update with the design the next time the tool is run.
       ========================================================== */ ?>
    <section class="section app-show" id="work-on-every-screen">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">Built once &middot; Right everywhere</p>
                <h2>Your business, <span class="ul-mark is-gold" data-r="mark">on every screen</span></h2>
                <p class="lead">
                    Every site we ship is designed on the phone first and proven on the
                    desktop &mdash; the same content, the same speed, the same checkout,
                    whichever screen your customer happens to be holding.
                </p>
            </div>

            <div class="app-stage">
                <div class="app-glow" aria-hidden="true"></div>

                <div class="device device-laptop" data-fx="in-left" style="--travel: 30%; --turn: 4deg;">
                    <div class="device-lid">
                        <div class="device-screen">
                            <?= photo('assets/mockups/laptop-screen.jpg', 'The Rafly pricing page open on a laptop', ['loading' => 'eager']) ?>
                        </div>
                    </div>
                    <div class="device-base"></div>
                </div>

                <div class="device device-phone" data-fx="in-right" style="--travel: 46%; --turn: 6deg;">
                    <div class="device-body">
                        <div class="device-screen">
                            <?= photo('assets/mockups/phone-screen.jpg', 'The Rafly blog open on a phone', ['loading' => 'eager']) ?>
                        </div>
                    </div>
                </div>

                <div class="app-panel" data-fx="in-up" style="--travel: 54px;">
                    <p class="app-panel-label">Largest Contentful Paint</p>
                    <p class="app-panel-value">1.2s</p>
                    <p class="app-panel-bar"><span style="--w: 88%;"></span></p>
                    <p class="app-panel-foot">Core Web Vitals &mdash; passing on mobile</p>
                </div>
            </div>

            <div class="steps-row app-points" data-r="group">
                <div class="step-item">
                    <span class="step-num">+01</span>
                    <h3 class="step-title">One codebase</h3>
                    <p class="step-text">No separate mobile site to fall out of date, and no second bill to keep it alive.</p>
                </div>
                <div class="step-item">
                    <span class="step-num">+02</span>
                    <h3 class="step-title">Real devices</h3>
                    <p class="step-text">Tested on the phones your customers actually own, not only in a browser window resized by hand.</p>
                </div>
                <div class="step-item">
                    <span class="step-num">+03</span>
                    <h3 class="step-title">Measured, not claimed</h3>
                    <p class="step-text">Every build is scored against Core Web Vitals before it ships, and the numbers go in your report.</p>
                </div>
            </div>
        </div>
    </section>

    <?php /* ============ P.E.A.C.E. — sticky, with the 3-D object ============ */ ?>
    <section class="section band-soft" id="method">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Our baseline methodology</p>
                    <h2>The P.E.A.C.E. framework <span class="soft">behind every package</span></h2>
                </div>
                <p class="lead">One coordinated method behind every bundled package we deliver &mdash; not five different vendors running five different processes.</p>
            </div>

            <div class="st-scroller">
                <div class="st-stage">
                    <?php
                    /* The 3-D object. js/stage3d.js loads three.js only when this
                       section is close to the viewport, WebGL2 exists, and motion
                       is not reduced. The still underneath is a designed object in
                       its own right, so a device that cannot run it still gets a
                       finished page rather than an empty box. */
                    ?>
                    <div class="three-stage" data-stage3d="peace" data-steps="5">
                        <div class="three-stage-still">
                            <svg viewBox="0 0 240 240" role="img" aria-label="A layered object representing the five stages of the P.E.A.C.E. framework">
                                <defs>
                                    <linearGradient id="pg1" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0" stop-color="#1652e0"/><stop offset="1" stop-color="#0d47a1"/>
                                    </linearGradient>
                                </defs>
                                <g fill="none" stroke-width="2">
                                    <ellipse cx="120" cy="168" rx="86" ry="30" fill="url(#pg1)" opacity=".16"/>
                                    <ellipse cx="120" cy="146" rx="72" ry="25" fill="url(#pg1)" opacity=".28"/>
                                    <ellipse cx="120" cy="124" rx="58" ry="20" fill="url(#pg1)" opacity=".45"/>
                                    <ellipse cx="120" cy="102" rx="44" ry="15" fill="url(#pg1)" opacity=".68"/>
                                    <ellipse cx="120" cy="80"  rx="30" ry="10" fill="url(#pg1)"/>
                                    <circle cx="120" cy="52" r="9" fill="#ff6b35"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="st-steps has-rail">
                    <?php foreach ($PEACE as $i => [$letter, $ico, $title, $copy]): ?>
                        <div class="st-step" data-letter="<?= e($letter) ?>">
                            <span class="st-step-num"><?= (int)($i + 1) ?></span>
                            <h3 class="st-step-title"><span class="peace-letter"><?= e($letter) ?></span> <?= e($title) ?></h3>
                            <p class="st-step-text"><?= e($copy) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ============= DELIVERY FLOW — the dark rhythm break ============= */ ?>
    <section class="section band-ink band-round-t" id="process">
        <div class="container">
            <div class="split split-wide-r">
                <div data-r="left">
                    <p class="eyebrow on-dark">How we work</p>
                    <h2>A process built for <span class="soft">predictable delivery</span></h2>
                    <p class="lead">No mystery timelines. You always know exactly which stage your package is in &mdash; and so do we.</p>
                    <p class="cluster" style="margin-top:2rem">
                        <button type="button" class="btn btn-white" data-modal-open="consultationModal">
                            Start with discovery <?= icon('arrow-right') ?>
                        </button>
                    </p>
                </div>

                <div class="gl-host" data-gl-host data-r="right">
                    <div class="gl-still" aria-hidden="true"></div>
                    <canvas data-gl="sphere,knot,helix,grid" data-mode="hero" data-density="0.9"
                            data-col-a="#1652e0" data-col-b="#0d47a1" data-col-hot="#ff6b35"
                            aria-hidden="true"></canvas>
                </div>
            </div>

            <div class="steps-row flow-row" data-r="group">
                <?php foreach ($FLOW as $i => [$title, $when, $copy]): ?>
                    <div class="step-item">
                        <span class="step-num">+<?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="step-title"><?= e($title) ?></span>
                        <span class="step-time"><?= e($when) ?></span>
                        <p class="step-text"><?= e($copy) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ========================= WHY CHOOSE ======================== */ ?>
    <section class="section" id="why">
        <div class="container">
            <div class="split split-wide-l">
                <div data-r="left">
                    <p class="eyebrow">Why choose</p>
                    <h2>Rafly for your <span class="soft">business growth?</span></h2>
                    <p class="lead">
                        Managing web development, marketing, content, security, and e-commerce
                        operations separately slows growth down. Rafly bundles them into a single
                        package with one point of contact, so you spend less time coordinating
                        vendors and more time running your business.
                    </p>
                    <ul class="list-check why-list">
                        <li><?= icon('check') ?><span>Faster turnaround timelines</span></li>
                        <li><?= icon('check') ?><span>One team, one point of contact</span></li>
                        <li><?= icon('check') ?><span>Professional, hands-on delivery</span></li>
                        <li><?= icon('check') ?><span>Proven, repeatable process</span></li>
                        <li><?= icon('check') ?><span>Transparent bundled packages</span></li>
                    </ul>
                    <p style="margin-top:2rem"><a class="btn btn-pill" href="/pricing">See the packages <?= icon('arrow-right') ?></a></p>
                </div>

                <div class="panel delivery-panel" data-r="right">
                    <p class="tag">One engagement</p>
                    <p class="delivery-panel-title">Everything below is delivered by the same team, under one point of contact.</p>
                    <ul class="plain delivery-list">
                        <?php foreach ($services as $s): ?>
                            <li>
                                <span class="icon-box"><?= icon($s['icon']) ?></span>
                                <span>
                                    <strong><?= e($s['title']) ?></strong>
                                    <span class="delivery-list-sub"><?= e($s['badge']) ?></span>
                                </span>
                                <?= icon('check', 'delivery-tick') ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="delivery-foot"><?= count($services) ?> services &middot; one team &middot; one invoice</p>
                </div>
            </div>

            <div class="stack-strip" data-r="rise">
                <p class="tag">Built on</p>
                <div class="cluster">
                    <?php foreach ($STACK as $tool): ?>
                        <span class="chip"><?= $tool ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ======================== SELECTED WORK ======================= */
    /* The section exists only when there is something real to put in it.
       case_studies_all() filters out placeholders in SQL, so a card that
       reaches this loop describes work that was actually done. */
    if ($caseStudies): ?>
    <section class="section band-soft" id="work">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Selected work</p>
                    <h2>Results, not just <span class="soft">deliverables</span></h2>
                </div>
                <div>
                    <p class="lead">A snapshot of what bundled delivery looks like in practice.</p>
                    <a class="link-arrow" href="/case-studies">See all case studies <?= icon('arrow-right') ?></a>
                </div>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($caseStudies as $c): ?>
                    <article class="card card-hover work-card">
                        <div class="card-body">
                            <span class="badge badge-soft"><?= e($c['sector']) ?></span>
                            <p class="work-metric"><?= e($c['metric_value']) ?></p>
                            <p class="work-metric-label"><?= e($c['metric_label']) ?></p>
                            <p class="card-text"><?= e(str_trunc((string)$c['problem'], 140)) ?></p>
                            <div class="card-foot">
                                <span class="tag"><?= e($c['client_name']) ?></span>
                                <?= icon('arrow-up-right') ?>
                            </div>
                        </div>
                        <a class="card-link" href="/case-studies" aria-label="Read the <?= e($c['client_name']) ?> case study"></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* =========================== PRICING ========================== */ ?>
    <section class="section" id="pricing">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">Bundle packages</p>
                <h2>One package, <span class="soft">every service included</span></h2>
                <p class="lead">Pick a starting point &mdash; every package is tailored to you during a free consultation.</p>
            </div>

            <div class="grid grid-3 price-grid" data-r="group">
                <?php foreach ($bundles as $i => $t) { require __DIR__ . '/partials/price-card.php'; } ?>
            </div>

            <p class="price-note">
                Every package is scoped in a free consultation before anything is agreed.
                Nothing starts until the scope and the number are both in writing.
            </p>
        </div>
    </section>

    <?php /* ========================= COMPARISON ========================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">Why growing businesses choose Rafly</p>
                <h2>Rafly vs. agencies <span class="soft">vs. freelancers</span></h2>
                <p class="lead">Compare how a bundled Rafly package stacks up against hiring separately.</p>
            </div>

            <div class="table-wrap" data-r="rise">
                <table class="table-compare">
                    <caption class="visually-hidden">A comparison of Rafly, traditional agencies and freelancers across six criteria</caption>
                    <thead>
                        <tr>
                            <th scope="col"><span class="visually-hidden">Criterion</span></th>
                            <th scope="col" class="is-us">Rafly</th>
                            <th scope="col">Traditional agencies</th>
                            <th scope="col">Freelancers &amp; DIY tools</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($COMPARE as [$label, $us, $agency, $free]): ?>
                            <tr>
                                <th scope="row"><?= e($label) ?></th>
                                <td class="is-us"><?= icon('circle-check', 'yes') ?> <?= e($us) ?></td>
                                <td><?= icon('alert-triangle', 'no') ?> <?= e($agency) ?></td>
                                <td><?= icon('circle-x', 'no') ?> <?= e($free) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php /* ======================== TESTIMONIALS ========================
       An earlier version of this section opened with a "video testimonial": a
       play button over an empty black box with no video behind it. A control
       that plays nothing costs more trust than it earns, so the format is a
       written quote.

       testimonials_all() applies both gates in SQL — not a seeded example, and
       the client has recorded consent to be named — so a card that reaches this
       loop is a quote somebody actually said and agreed to have published. */
    if ($testimonials): ?>
    <section class="section" id="stories">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">What our clients say</p>
                    <h2>Real stories, <span class="soft">real success</span></h2>
                </div>
                <p class="lead">Businesses that replaced a handful of scattered vendors with one bundled Rafly package.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($testimonials as $i => $t): ?>
                    <figure class="quote">
                        <?php /* Five filled stars would be a rating nobody recorded.
                           The quote is the evidence; the stars mark that a rating
                           exists at all only once one is actually stored. */ ?>
                        <blockquote class="quote-text">&ldquo;<?= e($t['quote']) ?>&rdquo;</blockquote>
                        <figcaption class="person">
                            <span class="avatar avatar-t<?= (int)(($i % 6) + 1) ?>" aria-hidden="true"><?= e(mb_substr((string)$t['author_name'], 0, 1)) ?></span>
                            <span>
                                <span class="person-name"><?= e($t['author_name']) ?></span><br>
                                <span class="person-role"><?= e($t['author_role']) ?><?= $t['author_company'] ? ', ' . e($t['author_company']) : '' ?></span>
                            </span>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ============================ FAQ ============================= */ ?>
    <section class="section band-soft" id="faq">
        <div class="container">
            <div class="faq-card" data-r="rise">
                <div class="faq-aside">
                    <p class="eyebrow">FAQ</p>
                    <h2>Got a question <span class="scribble">for Rafly?<svg viewBox="0 0 300 24" aria-hidden="true" preserveAspectRatio="none"><path d="M4 17C58 6 120 4 178 8c38 3 74 6 118 3"/></svg></span></h2>
                    <p class="muted">If there is something you want to ask that is not answered here, ask us directly &mdash; a person replies, not a form letter.</p>

                    <div class="faq-aside-actions">
                        <a class="btn btn-pill" href="#contact">Ask us directly <?= icon('arrow-right') ?></a>
                        <a class="link-arrow" target="_blank" rel="noopener"
                           href="<?= e(whatsapp_link('Hi Rafly, I have a question about your bundled packages.')) ?>">
                            <?= icon('whatsapp', 'icon-fill') ?> WhatsApp
                        </a>
                    </div>
                </div>

                <div class="faq-list">
                    <div class="accordion" data-accordion="single">
                        <?php foreach ($FAQS as $i => $f): ?>
                            <div class="accordion-item">
                                <button type="button" class="accordion-trigger" aria-expanded="false" aria-controls="faq-panel-<?= $i ?>" id="faq-trigger-<?= $i ?>">
                                    <span><?= e($f['q']) ?></span>
                                    <span class="accordion-icon" aria-hidden="true"><?= icon('chevron-down') ?></span>
                                </button>
                                <div class="accordion-panel" id="faq-panel-<?= $i ?>" role="region" aria-labelledby="faq-trigger-<?= $i ?>">
                                    <div><p><?= e($f['a']) ?></p></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php /* ==================== CONTACT — the blue band ==================
       The full-bleed colour band from the reference, doing real work rather
       than repeating a CTA the page has already made three times. */ ?>
    <section class="section band-ink" id="contact">
        <div class="container">
            <div class="split split-wide-l split-top">
                <div data-r="left">
                    <p class="eyebrow on-dark">Ready to stop juggling vendors?</p>
                    <h2>Tell us what's <span class="soft">slowing you down</span></h2>
                    <p class="lead">
                        One team, one package, one point of contact &mdash; for your website,
                        content, marketing, security, and e-commerce operations. Tell us what is
                        in the way and we'll put together a package that covers it.
                    </p>

                    <ul class="list-check contact-assurances">
                        <li><?= icon('check') ?><span>A person replies within one working day</span></li>
                        <li><?= icon('check') ?><span>No obligation, and no sales sequence</span></li>
                        <li><?= icon('check') ?><span>NDA on request; full IP transfers to you</span></li>
                    </ul>

                    <div class="deflist contact-details">
                        <?php $addr = setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306'); ?>
                        <div class="deflist-row">
                            <?= icon('map-pin') ?>
                            <span><span class="deflist-label">Office</span><span class="deflist-value"><?= e($addr) ?></span></span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('mail') ?>
                            <span><span class="deflist-label">Email</span><a class="deflist-value" href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a></span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('phone') ?>
                            <span><span class="deflist-label">Phone</span><a class="deflist-value" href="tel:<?= e(preg_replace('/[^0-9+]/', '', CONTACT_PHONE)) ?>"><?= e(CONTACT_PHONE) ?></a></span>
                        </div>
                        <?php $hours = setting('contact.hours', 'Mon - Fri, 09:00 - 18:00 IST'); if ($hours !== ''): ?>
                        <div class="deflist-row">
                            <?= icon('hourglass') ?>
                            <span><span class="deflist-label">Hours</span><span class="deflist-value"><?= e($hours) ?></span></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="contact-card" data-r="right">
                    <h3 class="contact-card-title">Start a conversation</h3>
                    <?php
                    $formId      = 'homeLeadForm';
                    $submitLabel = 'Submit requirements';
                    require __DIR__ . '/partials/lead-form.php';
                    ?>
                </div>
            </div>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
