<?php
require __DIR__ . '/inc/bootstrap.php';

// Slug is validated against SERVICES (inc/config.php), the same array that
// builds the nav dropdown and the footer, so the three can never drift apart.
//
// A missing or unrecognised slug is a real 404, not a silent fallback to
// web-development — the previous behaviour meant /service.php?service=anything
// served the web-development page at HTTP 200, a soft-404 that could get
// indexed as duplicate content.
$service = (string)($_GET['service'] ?? '');
if (!array_key_exists($service, SERVICES)) {
    require __DIR__ . '/404.php';
    exit;
}

/**
 * One array drives the whole page: hero, scope grid, process, tools, limits,
 * the FAQ accordion AND the FAQPage JSON-LD, plus the cross-links at the
 * bottom (which loop the same repository and skip the current slug).
 *
 * The five services come from ServiceRepository (inc/repo/services.php), not
 * from a page-local array. That array used to live in this file and was
 * duplicated, in reduced form, twice more — as SERVICES in inc/config.php and
 * as card copy in index.php. Three files, same five services, maintained
 * separately, which is how the homepage came to advertise a service called
 * "Automation" that the nav and this page both called "Content Creation".
 */
$data = service_find($service);

// service_find() returns null for a slug we do not offer. The guard above
// already rejected those against SERVICES, so reaching here with null means
// the two sources disagree — again a real 404 rather than a silent fallback.
if ($data === null) {
    require __DIR__ . '/404.php';
    exit;
}

$crumbs = [
    ['name' => 'Home',         'url' => '/'],
    ['name' => 'Services',     'url' => '/#services'],
    ['name' => $data['title'], 'url' => '/' . $service],
];

$page = [
    'id'        => 'services',
    'title'     => $data['title'] . ' | Rafly Digital Growth',
    'desc'      => $data['intro'],
    'bodyClass' => 'page-service svc-' . $data['key'],
    'styles'    => ['home', 'service'],
    'module'    => 'stage3d',

    // This page's content varies by slug, so the canonical must carry it.
    // Without it all five service pages self-canonicalise to the same URL
    // and four of the five get dropped from the index.
    'canonical' => $service,

    'schema'    => [
        schema_service($data['title'], $data['intro'], $data['highlights']),

        // Same array that renders the accordion below, so the rich result can
        // never claim an answer the visible page does not give.
        schema_faq($data['faqs']),
        schema_breadcrumbs($crumbs),
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">

    <?php /* ============================== 1. HERO ============================= */ ?>
    <section class="section page-head svc-hero">
        <?php require __DIR__ . '/partials/head-object.php'; ?>
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="split split-wide-l split-top">
                <div>
                    <p class="eyebrow"><?= e($data['badge']) ?></p>
                    <h1 class="display svc-title"><?= e($data['title']) ?></h1>
                    <p class="lead svc-tagline"><?= e($data['tagline']) ?></p>
                    <p class="svc-intro"><?= e($data['intro']) ?></p>

                    <div class="cluster svc-pills">
                        <?php foreach ($data['highlights'] as $highlight): ?>
                            <span class="chip"><?= e($highlight) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="cluster cluster-4 svc-actions">
                        <button type="button" class="btn btn-pill btn-lg" data-modal-open="consultationModal">
                            Book a free consultation <?= icon('arrow-up-right') ?>
                        </button>
                        <a class="btn btn-line btn-lg" href="/contact">Send requirements</a>
                    </div>

                    <p class="svc-note">Available as a standalone engagement, or bundled with our other services into a single package.</p>
                </div>

                <?php /* THE SERVICE MARK.
                   A drawn object in the service's own accent rather than a
                   licensed photograph. A photo of people at a laptop is the
                   single most generic-agency element a service page can carry,
                   and it said nothing about THIS service that it did not also
                   say about the other four. If a real photograph of Rafly's own
                   work exists later, it drops into the same frame. */ ?>
                <div class="svc-mark" aria-hidden="true" data-fx="drift" style="--depth: 24px;">
                    <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                    <span class="svc-mark-ring"></span>
                    <span class="svc-mark-ring is-2"></span>
                </div>
            </div>
        </div>
    </section>

    <?php /* ========================= 2. SOUNDS FAMILIAR ======================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Sounds familiar?</p>
                    <h2>The situations <span class="soft">people call us about</span></h2>
                </div>
                <p class="lead">If more than one of these lands, this is the right page.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($data['signals'] as $i => $signal): ?>
                    <div class="signal-card">
                        <span class="step-num">+<?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <p><?= e($signal) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ========================= 3. WHAT'S INCLUDED ======================= */ ?>
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">What's included</p>
                <h2>The scope, <span class="soft">written down</span></h2>
                <p class="lead">Every engagement is scoped in writing before it starts. This is what that scope usually covers.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($data['deliverables'] as $item): ?>
                    <article class="card card-hover">
                        <div class="card-body">
                            <span class="icon-box"><?= icon($item['icon']) ?></span>
                            <h3 class="card-title"><?= e($item['title']) ?></h3>
                            <p class="card-text"><?= e($item['desc']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* =========================== 4. WHAT CHANGES ======================== */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="split split-wide-l split-top">
                <div data-fx="in-left" style="--travel: 12%;">
                    <p class="eyebrow">What changes</p>
                    <h2>What you should <span class="soft">notice afterwards</span></h2>
                    <p class="lead">Not a promise about numbers &mdash; we do not make those. These are the practical differences the work is meant to produce.</p>
                    <ul class="list-check" style="margin-top:2rem">
                        <?php foreach ($data['outcomes'] as $outcome): ?>
                            <li><?= icon('check') ?><span><?= e($outcome) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="panel panel-line" data-fx="in-right" style="--travel: 16%; --turn: 3deg;">
                    <h3>How we approach it</h3>
                    <p class="muted">Every engagement is shaped around clarity, delivery reliability, and being straight with you about trade-offs.</p>
                    <ul class="list-check" style="margin-top:1.5rem">
                        <?php foreach ($data['points'] as $point): ?>
                            <li><?= icon('check') ?><span><?= e($point) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php /* ===================== 5. HOW THE ENGAGEMENT RUNS ==================== */ ?>
    <section class="section band-ink band-round-t">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow on-dark">How it runs</p>
                <h2>Four stages, <span class="soft">no mystery timelines</span></h2>
                <p class="lead">The same delivery process behind every Rafly package, applied to this service.</p>
            </div>

            <div class="steps-row" data-r="group">
                <?php foreach ($data['process'] as $i => $step): ?>
                    <div class="step-item">
                        <span class="step-num">+<?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="step-title"><?= e($step['title']) ?></span>
                        <span class="step-time"><?= e($step['time']) ?></span>
                        <p class="step-text"><?= e($step['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ========================= 6. WHAT WE WORK WITH ====================== */ ?>
    <section class="section-sm svc-tools">
        <div class="container">
            <p class="tag">What we work with</p>
            <div class="cluster" data-r="rise">
                <?php foreach ($data['tools'] as $tool): ?>
                    <span class="chip"><?= icon($tool['icon']) ?><?= $tool['label'] ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ========================== 7. HONEST LIMITS ========================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Honest limits</p>
                    <h2>Where we would <span class="soft">point you elsewhere</span></h2>
                </div>
                <p class="lead">We would rather tell you now than three weeks into a project that was never a good fit.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($data['boundaries'] as $limit): ?>
                    <div class="limit-card">
                        <span class="icon-box icon-box-orange"><?= icon('alert-triangle') ?></span>
                        <h3><?= e($limit['title']) ?></h3>
                        <p><?= e($limit['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ============================== 8. FAQ ==============================
       Rendered from the same array that feeds the FAQPage JSON-LD. */ ?>
    <section class="section">
        <div class="container">
            <div class="faq-card" data-r="rise">
                <div class="faq-aside">
                    <p class="eyebrow">FAQ</p>
                    <h2><?= e($data['title']) ?>, <span class="scribble">answered<svg viewBox="0 0 300 24" aria-hidden="true" preserveAspectRatio="none"><path d="M4 17C58 6 120 4 178 8c38 3 74 6 118 3"/></svg></span></h2>
                    <p class="muted">Something else on your mind? A person replies, usually the same working day.</p>
                    <div class="faq-aside-actions">
                        <a class="btn btn-pill" href="/contact">Ask us directly <?= icon('arrow-right') ?></a>
                    </div>
                </div>
                <div class="faq-list">
                    <div class="accordion" data-accordion="single">
                        <?php foreach ($data['faqs'] as $i => $faq): ?>
                            <div class="accordion-item">
                                <button type="button" class="accordion-trigger" aria-expanded="false" aria-controls="svc-faq-<?= $i ?>" id="svc-faq-t-<?= $i ?>">
                                    <span><?= e($faq['q']) ?></span>
                                    <span class="accordion-icon" aria-hidden="true"><?= icon('chevron-down') ?></span>
                                </button>
                                <div class="accordion-panel" id="svc-faq-<?= $i ?>" role="region" aria-labelledby="svc-faq-t-<?= $i ?>">
                                    <div><p><?= e($faq['a']) ?></p></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php /* ========================== 9. OTHER SERVICES =======================
       From the same repository this page is built on. This once read a
       page-local $serviceData that inc/repo/services.php replaced, so every
       service page printed two PHP warnings into its own footer and rendered an
       empty grid under "The other four". */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Also from Rafly</p>
                    <h2>The other four</h2>
                </div>
                <p class="lead">Each works on its own. They work better bundled, which is the whole point.</p>
            </div>

            <div class="grid grid-4" data-r="group">
                <?php foreach (services_all() as $slug => $other): ?>
                    <?php if ($slug === $service) { continue; } ?>
                    <article class="card card-hover svc-other">
                        <div class="card-body card-body-sm">
                            <span class="icon-box"><?= icon($other['icon']) ?></span>
                            <h3 class="card-title"><?= e($other['title']) ?></h3>
                            <p class="card-text"><?= e($other['tagline']) ?></p>
                            <div class="card-foot">
                                <span class="link-arrow">Explore <?= icon('arrow-right') ?></span>
                            </div>
                        </div>
                        <a class="card-link" href="/<?= e($slug) ?>" aria-label="<?= e($other['title']) ?>"></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Next step';
    $ctaTitle   = "Let's scope your " . $data['title'] . ' work.';
    $ctaText    = 'Tell us what is slowing you down. We will come back with a scope, a timeline, and a straight answer about whether we are the right people for it.';
    $ctaButton  = 'Book a free consultation';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
