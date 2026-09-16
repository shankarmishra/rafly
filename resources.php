<?php
require __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/repo/resources.php';

$resources = resources_all();

$crumbs = [
    ['name' => 'Home',      'url' => '/'],
    ['name' => 'Resources', 'url' => '/resources'],
];

$page = [
    'id'        => 'resources',
    'title'     => 'Technical Engineering Resources & Architecture Guides | RAFly Digital Growth',
    'desc'      => 'In-depth engineering resources, cross-platform mobile frameworks breakdowns, backend API architecture guides, and technical security blueprints from RAFly.',
    'bodyClass' => 'page-resources',
    'styles'    => ['home', 'service'],
    'module'    => 'home',
    'canonical' => 'resources',
    'schema'    => [
        schema_breadcrumbs($crumbs),
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <section class="section hero sig-hero blueprint-canvas" style="padding-block: 4rem; position: relative;">
        <div class="container" style="max-width: 1100px;">
            <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.2rem;">
                <span class="glow-dot-active"></span> RAFly KNOWLEDGE ARCHITECTURE
            </div>
            <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.1; letter-spacing: -0.025em; margin-bottom: 1rem;">
                TECHNICAL ENGINEERING <span style="color: #0a63ff;">RESOURCES &amp; GUIDES</span>
            </h1>
            <p style="font-size: 1.1rem; color: #334155; line-height: 1.65; max-width: 680px; margin-bottom: 2.5rem;">
                In-depth technical blueprints, mobile application architectures, framework benchmarks, and backend performance insights written for software engineers, product leaders, and technical decision makers.
            </p>

            <div class="grid grid-3" style="gap: 1.75rem;">
                <?php foreach ($resources as $slug => $res): ?>
                    <div class="machined-card" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                        <div>
                            <span class="telemetry-pill-mono" style="color: #0a63ff; font-weight: 700; display: inline-block; margin-bottom: 0.6rem;"><?= e($res['badge']) ?></span>
                            <h2 style="font-size: 1.25rem; font-weight: 800; color: #050f33; line-height: 1.3; margin-bottom: 0.75rem;">
                                <a href="/resources/<?= e($slug) ?>" style="color: inherit; text-decoration: none;"><?= e($res['title']) ?></a>
                            </h2>
                            <p style="font-size: 0.92rem; color: #475569; line-height: 1.6; margin-bottom: 1.25rem;">
                                <?= e(str_trunc($res['intro'], 140)) ?>
                            </p>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: #64748b; font-family: var(--font-mono); margin-bottom: 1rem;">
                                <?= e($res['read_time']) ?> &middot; <?= e($res['category']) ?>
                            </div>
                            <a class="link-arrow" href="/resources/<?= e($slug) ?>" style="font-weight: 700; color: #0a63ff;">
                                Read Architecture Guide <?= icon('arrow-right') ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="section band-soft">
        <div class="container" style="text-align: center; max-width: 760px;">
            <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">ENGINEERING CONSULTATION</span>
            <h2 style="font-size: 2.2rem; font-weight: 800; color: #050f33; margin-bottom: 1rem;">NEED CUSTOM SOFTWARE ARCHITECTURE?</h2>
            <p style="font-size: 1.05rem; color: #475569; line-height: 1.6; margin-bottom: 2rem;">
                Speak directly with senior engineers to plan your mobile app, web platform, or backend infrastructure.
            </p>
            <a class="btn btn-primary btn-lg" href="/contact">Schedule Technical Call <?= icon('arrow-up-right') ?></a>
        </div>
    </section>
</main>
<?php
require __DIR__ . '/partials/footer.php';
