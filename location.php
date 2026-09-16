<?php
/**
 * Dynamic Location Page Controller & Template.
 *
 * Renders verified location hub pages (HQ, On-Site Hubs, Regional/Global Nodes)
 * with strict Quality Engine validation to prevent doorway pages or thin index bloat.
 */

require __DIR__ . '/inc/bootstrap.php';

$slug = (string)($_GET['slug'] ?? '');
if ($slug === '') {
    require __DIR__ . '/404.php';
    exit;
}

$loc = location_find($slug);
if ($loc === null) {
    require __DIR__ . '/404.php';
    exit;
}

$qualityScore = location_quality_score($slug);
$isIndexable = !empty($loc['is_indexable']) && $qualityScore >= 75;

$crumbs = [
    ['name' => 'Home',      'url' => '/'],
    ['name' => 'Locations', 'url' => '/locations'],
    ['name' => $loc['name'], 'url' => '/locations/' . $slug],
];

$title = $loc['seo_title'] ?? ($loc['name'] . ' Regional Hub | RAFLY');
$desc  = $loc['meta_desc'] ?? ($loc['intro'] ?? '');

$page = [
    'id'        => 'locations',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-location loc-' . $slug,
    'styles'    => ['home', 'home-scenes', 'locations'],
    'module'    => 'home',
    'canonical' => 'locations/' . $slug,
    'noindex'   => !$isIndexable,
    'schema'    => [
        schema_webpage('locations/' . $slug, $title, $desc, 'WebPage'),
        schema_breadcrumbs($crumbs),
    ],
];

$services = array_values(services_all());
$addr     = $loc['address'] ?? setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306');
$hours    = $loc['hours'] ?? 'Mon - Fri, 09:00 - 18:00 IST';
$phone    = $loc['phone'] ?? CONTACT_PHONE;

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">

    <!-- HERO SECTION -->
    <section class="section page-head">
        <?php require __DIR__ . '/partials/head-object.php'; ?>
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split">
                <div>
                    <div class="machined-badge machined-badge-blue" style="margin-bottom: 1rem;">
                        <span class="glow-dot-active"></span> <?= e(strtoupper($loc['type'] ?? 'REGIONAL NODE')) ?> // OPERATIONAL
                    </div>
                    <h1 class="display">Digital Growth &amp; Engineering <span class="soft">in <?= e($loc['name']) ?>.</span></h1>
                </div>
                <div>
                    <p class="lead" style="margin-bottom: 1.5rem;">
                        <?= e($loc['intro']) ?>
                    </p>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="telemetry-pill-mono" style="background: rgba(10, 99, 255, 0.08); color: #0b52d8; border: 1px solid rgba(10, 99, 255, 0.2);">
                            📍 Region: <?= e($loc['region'] ?? 'Global') ?>
                        </span>
                        <span class="telemetry-pill-mono" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.2);">
                            🌐 Country: <?= e($loc['country']) ?>
                        </span>
                        <span class="telemetry-pill-mono" style="background: rgba(22, 163, 74, 0.08); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2);">
                            ⚡ SLA Response: &lt; 60s
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NODE DETAILS & LOCAL CONTEXT -->
    <section class="section-bot">
        <div class="container">
            <div class="split split-wide-l">
                <div>
                    <p class="eyebrow">Node Intelligence</p>
                    <h2>Delivery &amp; Engagement Model</h2>
                    <div class="deflist contact-details" style="margin-top:1.5rem">
                        <div class="deflist-row">
                            <?= icon('map-pin') ?>
                            <span><span class="deflist-label">Location / Corridor</span>
                                <span class="deflist-value">
                                    <a href="https://maps.google.com/?q=<?= rawurlencode($addr) ?>" target="_blank" rel="noopener"><?= e($addr) ?></a>
                                </span>
                            </span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('hourglass') ?>
                            <span><span class="deflist-label">Operating Hours</span><span class="deflist-value"><?= e($hours) ?></span></span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('phone') ?>
                            <span><span class="deflist-label">Contact SLA</span>
                                <span class="deflist-value"><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a></span>
                            </span>
                        </div>
                    </div>
                    <p style="margin-top:2rem">
                        <a class="btn btn-pill" href="#intake">Start Brief in <?= e($loc['name']) ?> <?= icon('arrow-right') ?></a>
                    </p>
                </div>
                <div>
                    <p class="eyebrow">Regional Context</p>
                    <h2>Engineered for <?= e($loc['name']) ?> <span class="soft">businesses</span></h2>
                    <p class="lead" style="margin-bottom: 1.5rem;">
                        <?= e($loc['local_context']) ?>
                    </p>
                    <?php if (!empty($loc['highlights'])): ?>
                    <ul class="check-list" style="display: grid; gap: 0.75rem; margin-top: 1rem;">
                        <?php foreach ((array)$loc['highlights'] as $highlight): ?>
                        <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; color: #334155;">
                            <?= icon('check-circle', 'text-blue', 'Verified') ?>
                            <span><?= e($highlight) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- LINKED SERVICES -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Services in <?= e($loc['name']) ?></p>
                    <h2>Topical Services &amp; Solutions</h2>
                </div>
                <p class="lead">Every service is delivered to enterprise standards with full IP transfer and clean maintenance.</p>
            </div>

            <ol class="rail" data-r="group">
                <?php foreach ($services as $n => $svc): ?>
                    <li class="rail-item">
                        <span class="rail-num"><?= str_pad((string)($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="icon-box"><?= icon($svc['icon']) ?></span>
                        <span class="rail-body">
                            <span class="rail-title"><?= e($svc['title']) ?> in <?= e($loc['name']) ?></span>
                            <span class="rail-text"><?= e($svc['card']) ?></span>
                        </span>
                        <span class="rail-go"><?= icon('arrow-right') ?></span>
                        <a class="card-link" href="<?= e(service_url($svc['slug']) . '/' . $slug) ?>" aria-label="<?= e($svc['title']) ?> in <?= e($loc['name']) ?>"></a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- INTAKE FORM CONSOLE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// INTAKE CONSOLE</div>
                <h2>START A PROJECT BRIEF FOR <?= e(strtoupper($loc['name'])) ?></h2>
            </div>
            <?php $formId = 'locationDetailLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
