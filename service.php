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

// inc/repo/links.php — the inverse of blog-post.php's related-service link,
// and case studies whose admin-entered tags name this service. Both degrade
// to [] with no database, same contract as everything else on this page.
$relatedArticles    = related_articles_for_service($service, 3);
$relatedCaseStudies = related_case_studies_for_service($data['title'], 2);

$page = [
    'id'        => 'services',
    'title'     => $data['title'] . ' | Rafly Digital Growth',
    'desc'      => $data['intro'],
    'bodyClass' => 'page-service svc-' . $data['key'],
    'styles'    => ['home', 'service'],

    // This page's content varies by slug, so the canonical must carry it.
    // Without it all five service pages self-canonicalise to the same URL
    // and four of the five get dropped from the index.
    'canonical' => $service,

    'schema'    => [
        // Same @id schema_organization()'s makesOffer references this service
        // by, so the two resolve to one Service entity instead of two.
        schema_service($data['title'], $data['intro'], $data['highlights'], schema_id('service-' . $service)),

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
                    <p class="svc-note">
                        Rafly is based in <?= e(BUSINESS_GEO_LOCALITY) ?>, <?= e(BUSINESS_GEO_REGION) ?>,
                        and works with businesses there and across <?= e(BUSINESS_GEO_COUNTRY) ?> &mdash;
                        delivery is remote-first, so location is rarely a blocker.
                    </p>
                </div>

                <?php /* THE SERVICE MARK.
                   A drawn object in the service's own accent rather than a
                   licensed photograph. A photo of people at a laptop is the
                   single most generic-agency element a service page can carry,
                   and it said nothing about THIS service that it did not also
                   say about the other four. If a real photograph of Rafly's own
                   work exists later, it drops into the same frame. */ ?>
                <div class="svc-mark svc-mark-<?= e($data['key']) ?>" aria-hidden="true" data-fx="drift" style="--depth: 24px;">
                    <?php if ($data['key'] === 'web'): ?>
                        <div class="svc-node-model web-model">
                            <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                            <span class="svc-hud-node node-fe">[FRONTEND]</span>
                            <span class="svc-hud-node node-api">[API GATEWAY]</span>
                            <span class="svc-hud-node node-db">[DATABASE]</span>
                            <span class="svc-hud-node node-edge">[EDGE CDN]</span>
                            <span class="svc-mark-ring"></span>
                            <span class="svc-mark-ring is-2"></span>
                        </div>
                    <?php elseif ($data['key'] === 'security'): ?>
                        <div class="svc-node-model sec-model">
                            <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                            <span class="svc-hud-node node-waf">[WAF ACTIVE]</span>
                            <span class="svc-hud-node node-tls">[TLS 1.3 SHIELD]</span>
                            <span class="svc-hud-node node-scan">[ZERO THREATS]</span>
                            <span class="svc-mark-ring ring-shield"></span>
                            <span class="svc-mark-ring is-2 ring-shield-outer"></span>
                        </div>
                    <?php elseif ($data['key'] === 'marketing'): ?>
                        <div class="svc-node-model mkt-model">
                            <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                            <span class="svc-hud-node node-roi">[ROI 3.4X]</span>
                            <span class="svc-hud-node node-ctr">[CTR +140%]</span>
                            <span class="svc-hud-node node-conv">[CONVERSIONS]</span>
                            <span class="svc-mark-ring ring-pulse"></span>
                            <span class="svc-mark-ring is-2"></span>
                        </div>
                    <?php elseif ($data['key'] === 'content'): ?>
                        <div class="svc-node-model cnt-model">
                            <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                            <span class="svc-hud-node node-pipe">[STRATEGY]</span>
                            <span class="svc-hud-node node-edit">[EDITORIAL]</span>
                            <span class="svc-hud-node node-dist">[DISTRIBUTION]</span>
                            <span class="svc-mark-ring"></span>
                            <span class="svc-mark-ring is-2"></span>
                        </div>
                    <?php elseif ($data['key'] === 'ecom'): ?>
                        <div class="svc-node-model ecom-model">
                            <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                            <span class="svc-hud-node node-store">[STOREFRONT]</span>
                            <span class="svc-hud-node node-pay">[GATEWAY]</span>
                            <span class="svc-hud-node node-order">[ORDER ENGINE]</span>
                            <span class="svc-mark-ring"></span>
                            <span class="svc-mark-ring is-2"></span>
                        </div>
                    <?php else: ?>
                        <span class="svc-mark-icon"><?= icon($data['icon']) ?></span>
                        <span class="svc-mark-ring"></span>
                        <span class="svc-mark-ring is-2"></span>
                    <?php endif; ?>
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
                        <span class="icon-box icon-box-note"><?= icon('alert-triangle') ?></span>
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
                    <h2><?= e($data['title']) ?>, <span class="mark">answered</span></h2>
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

    <?php /* ==================== 8b. FURTHER READING =====================
       Articles whose category maps to this service (inc/repo/links.php) and
       case studies whose tags name it. Either list may be empty — with no
       database, both are — so the whole section is skipped rather than
       rendering an empty head. */ ?>
    <?php if ($relatedArticles || $relatedCaseStudies): ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Further reading</p>
                    <h2>More on <span class="soft"><?= e($data['title']) ?></span></h2>
                </div>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($relatedArticles as $a): ?>
                    <article class="card card-hover">
                        <div class="card-body card-body-sm">
                            <span class="badge badge-soft">Article</span>
                            <h3 class="card-title"><?= e((string)$a['title']) ?></h3>
                            <p class="card-text"><?= e(str_cut((string)$a['excerpt'], 110)) ?></p>
                            <div class="card-foot">
                                <span class="blog-meta"><?= (int)$a['read_minutes'] ?> min read</span>
                                <?= icon('arrow-up-right') ?>
                            </div>
                        </div>
                        <a class="card-link" href="<?= e(site_path('/blog/' . rawurlencode((string)$a['slug']))) ?>" aria-label="<?= e((string)$a['title']) ?>"></a>
                    </article>
                <?php endforeach; ?>
                <?php foreach ($relatedCaseStudies as $cs): ?>
                    <article class="card card-hover">
                        <div class="card-body card-body-sm">
                            <span class="badge badge-soft">Case study</span>
                            <h3 class="card-title"><?= e((string)$cs['client_name']) ?></h3>
<?php if (trim((string)$cs['metric_value']) !== '' && trim((string)$cs['metric_label']) !== ''): ?>
                            <p class="card-text"><strong><?= e((string)$cs['metric_value']) ?></strong> &mdash; <?= e((string)$cs['metric_label']) ?></p>
<?php endif; ?>
                            <div class="card-foot">
                                <span class="link-arrow">Read the write-up <?= icon('arrow-right') ?></span>
                            </div>
                        </div>
                        <a class="card-link" href="/case-studies#case-study-<?= (int)$cs['index'] ?>" aria-label="<?= e((string)$cs['client_name']) ?> case study"></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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
