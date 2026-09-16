<?php
/**
 * Service x Location Controller & Template.
 *
 * Handles programmatic combinations like:
 * /services/web-development/greater-noida
 * /services/web-development/noida
 * /services/web-development/delhi
 * /services/web-development/gurgaon
 *
 * Enforces Quality Engine threshold (Quality Score >= 75 for indexability,
 * canonicalization to parent hub for non-qualifying combinations).
 */

require __DIR__ . '/inc/bootstrap.php';

$serviceSlug  = (string)($_GET['service'] ?? '');
$locationSlug = (string)($_GET['location'] ?? '');

if ($serviceSlug === '' || $locationSlug === '') {
    require __DIR__ . '/404.php';
    exit;
}

$combo = service_location_find($serviceSlug, $locationSlug);
if ($combo === null) {
    require __DIR__ . '/404.php';
    exit;
}

$service  = $combo['service'];
$location = $combo['location'];

$crumbs = [
    ['name' => 'Home',            'url' => '/'],
    ['name' => 'Services',        'url' => '/#services'],
    ['name' => $service['title'], 'url' => service_url($service['slug'])],
    ['name' => $location['name'], 'url' => service_url($service['slug']) . '/' . $location['slug']],
];

$title = $combo['seo_title'];
$desc  = $combo['meta_desc'];

$page = [
    'id'        => 'services',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-service-location svc-' . $service['key'],
    'styles'    => ['home', 'home-scenes', 'service'],
    'module'    => 'home',
    'canonical' => ltrim($combo['canonical'], '/'),
    'noindex'   => !$combo['is_indexable'],
    'schema'    => [
        schema_service(
            $service['title'] . ' in ' . $location['name'],
            $desc,
            $combo['service_highlights'],
            schema_id('service-' . $service['slug'] . '-' . $location['slug'])
        ),
        schema_breadcrumbs($crumbs),
        schema_faq($combo['faqs']),
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">

    <!-- HERO SECTION -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 85vh; padding-block: 4rem; display: flex; align-items: center; position: relative;">
        <div class="container hero-grid">
            <div>
                <?= breadcrumbs($crumbs) ?>
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active"></span> <?= e(strtoupper($service['title'])) ?> // <?= e(strtoupper($location['name'])) ?>
                </div>
                <h1 style="font-size: clamp(2.4rem, 4vw, 3.6rem); font-weight: 800; color: #050f33; line-height: 1.1; letter-spacing: -0.02em; margin-bottom: 1rem;">
                    <?= e($service['title']) ?> <br><span style="color: #0a63ff;">in <?= e($location['name']) ?></span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    <?= e($combo['intro']) ?>
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake">Start Project Brief <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#details">Explore Specs <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- TELEMETRY STAGE -->
            <div class="svc-eco-stage">
                <div class="svc-eco-header">
                    <div class="svc-eco-title">
                        <?= icon($service['icon']) ?> <?= e($service['title']) ?>
                    </div>
                    <span class="svc-eco-badge"><?= e($location['name']) ?> NODE</span>
                </div>
                <div class="svc-eco-canvas" style="padding: 2rem;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="machined-card" style="margin: 0; background: #ffffff;">
                            <span class="telemetry-pill-mono" style="color: #0a63ff; font-weight: 700;">DELIVERY NODE</span>
                            <h4 style="font-size: 1.1rem; font-weight: 800; margin-block: 0.3rem; color: #050f33;"><?= e($location['display_title']) ?></h4>
                            <p style="font-size: 0.88rem; color: #475569; margin: 0;"><?= e($location['address']) ?></p>
                        </div>
                        <div class="machined-card" style="margin: 0; background: #ffffff;">
                            <span class="telemetry-pill-mono" style="color: #16a34a; font-weight: 700;">QUALITY THRESHOLD</span>
                            <h4 style="font-size: 1.1rem; font-weight: 800; margin-block: 0.3rem; color: #050f33;">Quality Score: <?= e($combo['quality_score']) ?> / 100</h4>
                            <p style="font-size: 0.88rem; color: #475569; margin: 0;">Verified service authority &amp; non-duplicate location relevance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICE & LOCATION DETAILS -->
    <section class="section band-soft" id="details">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">CAPABILITIES MATRIX</span>
                <h2>SERVICE CAPABILITIES &amp; LOCAL EXECUTION</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <?php foreach ((array)$service['highlights'] as $n => $highlight): ?>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color: #0a63ff; font-weight: 700;">SPEC 0<?= $n + 1 ?></span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-block: 0.5rem;"><?= e($highlight) ?></h3>
                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.6; margin: 0;">
                        Delivered for clients in <?= e($location['name']) ?> with full source code transfer and zero hidden technical debt.
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section class="section" style="padding-block: 5rem;">
        <div class="container" style="max-width: 860px;">
            <div class="sec-head sec-head-center" style="margin-bottom: 3rem;">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">FREQUENTLY ASKED QUESTIONS</span>
                <h2><?= e(strtoupper($service['title'])) ?> IN <?= e(strtoupper($location['name'])) ?> FAQ</h2>
            </div>
            <?php foreach ($combo['faqs'] as $faq): ?>
            <details class="loc-faq-item" style="margin-bottom: 1rem;" open>
                <summary class="loc-faq-head"><?= e($faq['q']) ?></summary>
                <div class="loc-faq-body"><?= e($faq['a']) ?></div>
            </details>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- INTAKE FORM -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// SERVICE INTAKE CONSOLE</div>
                <h2>START YOUR <?= e(strtoupper($service['title'])) ?> BRIEF FOR <?= e(strtoupper($location['name'])) ?></h2>
            </div>
            <?php $formId = 'svcLocLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
