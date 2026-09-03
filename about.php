<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',  'url' => '/'],
    ['name' => 'About', 'url' => '/about'],
];

$page = [
    'id'        => 'about',
    'title'     => 'About Rafly | Digital Growth',
    'desc'      => 'We build efficient digital systems and growth strategies for modern businesses — one partner across web, content, marketing, security and e-commerce.',
    'bodyClass' => 'page-about',
    'styles'    => ['home', 'about'],
    'schema'    => [
        schema_webpage(
            'about',
            'About Rafly | Digital Growth',
            'We build efficient digital systems and growth strategies for modern businesses — one partner across web, content, marketing, security and e-commerce.',
            'AboutPage'
        ),
        schema_breadcrumbs($crumbs),
    ],
];

$services = array_values(services_all());

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <section class="section page-head">
        <?php require __DIR__ . '/partials/head-object.php'; ?>
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">About Rafly</p>
                    <h1 class="display">We build digital systems, <span class="soft">not deliverables.</span></h1>
                </div>
                <div>
                    <p class="lead">
                        Rafly is a digital growth partner based in <?= e(BUSINESS_GEO_LOCALITY) ?>,
                        <?= e(BUSINESS_GEO_REGION) ?>, that bundles web development, content
                        creation, digital marketing, web security, and e-commerce support into one
                        coordinated team &mdash; so growing businesses get one accountable point of
                        contact instead of five separate vendors.
                    </p>
                    <p style="margin-top:1.5rem">
                        <a class="btn btn-pill" href="/case-studies">See our work <?= icon('arrow-right') ?></a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php /* ===================== THE BRANCHING FIGURE =====================
       Rafly as one core with five branches: the company narrative, drawn.
       The branch labels are the five services from the repository, so this
       figure and the nav and the five detail pages can never disagree about
       what the company does. It replaces a licensed stock photograph of
       strangers around a table — a picture that said nothing about Rafly and
       could have been on any agency site on earth. */ ?>
    <section class="section-bot">
        <div class="container">
            <div class="split split-wide-r">
                <div class="branch-figure" aria-hidden="true" data-fx="drift" style="--depth: 30px;">
                    <span class="branch-core"><?= icon('layers') ?></span>
                    <span class="branch-hud-tag tag-top">[SYSTEM INTEGRATION]</span>
                    <span class="branch-hud-tag tag-bot">[5 CORE BRANCHES ACTIVE]</span>
                    <ol class="branch-list">
                        <?php foreach ($services as $n => $svc): ?>
                            <li class="branch-item" style="--a:<?= -90 + $n * 72 ?>deg">
                                <span class="branch-dot"><?= icon($svc['icon']) ?></span>
                                <span class="branch-label"><?= e($svc['title']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>

                <div data-fx="in-right" style="--travel: 12%;">
                    <p class="eyebrow">Who we are</p>
                    <h2>One core. <span class="soft">Five branches.</span></h2>
                    <p class="lead">
                        Rafly combines technical execution with business understanding. Instead of
                        hiring separate people for your website, content, marketing, security, and
                        store operations, you work with one team that already shares the same plan.
                    </p>
                    <ul class="list-check" style="margin-top:2rem">
                        <li><?= icon('check') ?><span>Dedicated, bundled support for growing businesses</span></li>
                        <li><?= icon('check') ?><span>Reliable web development and security guidance</span></li>
                        <li><?= icon('check') ?><span>Strategy-led marketing, content, and e-commerce support</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php /* ======================= MISSION & VISION ======================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">Our mission and vision</p>
                <h2>Bundled growth, <span class="soft">not fragmented vendors</span></h2>
                <p class="lead">Two commitments that shape how every package is scoped and delivered.</p>
            </div>

            <div class="grid grid-2 mv-grid" data-r="group">
                <article class="mv-card">
                    <span class="icon-box icon-box-lg"><?= icon('compass') ?></span>
                    <h3>Our mission</h3>
                    <p>To give growing businesses one accountable team for every part of their digital presence &mdash; website, content, marketing, security, and store operations &mdash; bundled into clear packages instead of scattered contracts. We focus on dependable delivery, transparent pricing, and outcomes you can actually measure.</p>
                </article>
                <article class="mv-card is-vision">
                    <span class="icon-box icon-box-lg icon-box-ink"><?= icon('rocket') ?></span>
                    <h3>Our vision</h3>
                    <p>To become the go-to bundled growth partner for small and mid-sized businesses &mdash; the team you call once, instead of five different vendors, whenever something digital needs to get done.</p>
                </article>
            </div>
        </div>
    </section>

    <?php /* =========================== WHAT WE DO ==========================
       From the repository, not a sixth hand-written list. This section once
       carried six cards written separately from every other list of services on
       the site, and one of them — "Operational Support" — was not a service
       Rafly offers anywhere else; it existed only here. services_all() renders
       it now: five services, the same five, in the same order, with the same
       titles as the nav, the footer, the homepage and the detail pages. */ ?>
    <section class="section">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">What we do</p>
                    <h2>Five services, <span class="soft">one team</span></h2>
                </div>
                <p class="lead">Every one of these is available on its own &mdash; and every one is designed to be bundled with the others.</p>
            </div>

            <ol class="rail" data-r="group">
                <?php foreach ($services as $n => $svc): ?>
                    <li class="rail-item">
                        <span class="rail-num"><?= str_pad((string)($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="icon-box"><?= icon($svc['icon']) ?></span>
                        <span class="rail-body">
                            <span class="rail-title"><?= e($svc['title']) ?></span>
                            <span class="rail-text"><?= e($svc['card']) ?></span>
                        </span>
                        <span class="rail-go"><?= icon('arrow-right') ?></span>
                        <a class="card-link" href="/<?= e($svc['slug']) ?>" aria-label="<?= e($svc['title']) ?>"></a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php /* ========================== OUR APPROACH ========================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="split split-wide-l">
                <div data-fx="in-left" style="--travel: 12%;">
                    <p class="eyebrow">Our approach</p>
                    <h2>Structured, <span class="soft">and boring on purpose</span></h2>
                    <p class="lead">
                        We work with a practical, structured method that aligns technology, content,
                        marketing, and growth objectives across one bundled package. Every solution
                        is shaped around clarity, delivery reliability, and business value.
                    </p>
                    <ul class="list-check" style="margin-top:2rem">
                        <li><?= icon('check') ?><span>Understand goals before implementation</span></li>
                        <li><?= icon('check') ?><span>Build systems that are scalable and maintainable</span></li>
                        <li><?= icon('check') ?><span>Focus on measurable business impact, not just deliverables</span></li>
                    </ul>
                    <p style="margin-top:2rem"><a class="link-arrow" href="/#method">See the P.E.A.C.E. framework <?= icon('arrow-right') ?></a></p>
                </div>

                <div class="peace-orbit" aria-hidden="true" data-fx="drift" style="--depth: 26px;">
                    <span class="peace-orbit-core">P.E.A.C.E.</span>
                    <ol class="peace-orbit-ring">
                        <li style="--a:-90deg"><span>P</span></li>
                        <li style="--a:-18deg"><span>E</span></li>
                        <li style="--a:54deg"><span>A</span></li>
                        <li style="--a:126deg"><span>C</span></li>
                        <li style="--a:198deg"><span>E</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Ready to work with us?';
    $ctaTitle   = 'Let\'s build a stronger digital foundation.';
    $ctaText    = 'Tell us what you need and we will help you move forward with a clear plan and practical execution.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
