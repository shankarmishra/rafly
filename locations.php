<?php
/**
 * Regional Locations & Service Hubs Overview Page.
 * Central hub for RAFly's regional operations across Delhi NCR (Greater Noida, Noida, Delhi, Gurgaon).
 */

require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',      'url' => '/'],
    ['name' => 'Locations', 'url' => '/locations'],
];

$title = 'Locations & Regional Service Hubs | RAFly Digital Growth';
$desc  = 'RAFly provides custom web development, web security, performance marketing, and e-commerce support across Greater Noida, Noida, Delhi, Gurgaon, and Delhi NCR.';

$page = [
    'id'        => 'locations',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-locations-hub',
    'styles'    => ['home', 'home-scenes', 'about'],
    'module'    => 'home',
    'canonical' => 'locations',
    'schema'    => [
        schema_webpage('locations', $title, $desc, 'WebPage'),
        schema_breadcrumbs($crumbs),
    ],
];

$locations = [
    [
        'slug'  => 'greater-noida',
        'name'  => 'Greater Noida',
        'tag'   => 'REGISTERED HEADQUARTERS',
        'desc'  => 'Our primary registered office and core engineering lab in Tech Zone IV, Greater Noida West.',
        'addr'  => 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306',
    ],
    [
        'slug'  => 'noida',
        'name'  => 'Noida',
        'tag'   => 'COMMERCIAL & TECH CORRIDOR',
        'desc'  => 'Serving enterprise tech parks, Sector 62/125/132 IT hubs, and commercial D2C brands across Noida.',
        'addr'  => 'Regional Coverage Across Noida IT & Commercial Sectors',
    ],
    [
        'slug'  => 'delhi',
        'name'  => 'Delhi',
        'tag'   => 'CAPITAL ENTERPRISE REGION',
        'desc'  => 'Engineering high-security web platforms, WAF defense, and performance advertising for Delhi enterprises.',
        'addr'  => 'Regional Coverage Across Central, South & North Delhi',
    ],
    [
        'slug'  => 'gurgaon',
        'name'  => 'Gurgaon (Gurugram)',
        'tag'   => 'CYBER CITY TECH HUB',
        'desc'  => 'Scaling high-concurrency e-commerce storefronts, server-side analytics, and growth engines in Gurgaon.',
        'addr'  => 'Regional Coverage Across Cyber City & Golf Course Road',
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
                    <p class="eyebrow">REGIONAL SERVICE HUBS</p>
                    <h1 class="display">Engineering presence <span class="soft">across Delhi NCR.</span></h1>
                </div>
                <p class="lead">
                    Headquartered in Greater Noida West, RAFly delivers single-team web development, cyber security, performance marketing, and e-commerce support for growth businesses across Greater Noida, Noida, Delhi, and Gurgaon.
                </p>
            </div>
        </div>
    </section>

    <!-- REGIONAL HUBS GRID -->
    <section class="section-bot">
        <div class="container">
            <div class="grid grid-2" style="gap: 1.5rem;" data-r="group">
                <?php foreach ($locations as $loc): ?>
                    <article class="card card-hover" style="background: #ffffff; border: 1px solid rgba(6,18,47,0.12); border-radius: var(--r-xl, 16px); padding: 1.8rem; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <span class="badge badge-soft-blue" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.06em; margin-bottom: 0.8rem; display: inline-block;"><?= e($loc['tag']) ?></span>
                            <h2 style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: #06122f; margin: 0 0 0.5rem 0;"><?= e($loc['name']) ?></h2>
                            <p style="font-size: 0.95rem; color: #475569; line-height: 1.6; margin-bottom: 1rem;"><?= e($loc['desc']) ?></p>
                            <p style="font-size: 0.85rem; color: #64748b; font-family: var(--font-mono, monospace); margin: 0;">📍 <?= e($loc['addr']) ?></p>
                        </div>
                        <div style="margin-top: 1.5rem;">
                            <a class="btn btn-pill-outline btn-sm" href="/locations/<?= e($loc['slug']) ?>">
                                Explore <?= e($loc['name']) ?> Hub &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SERVICES CROSS-LINKING -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">SERVICES DELIVERED REGIONALLY</p>
                    <h2>Five core capabilities, <span class="soft">one engineering team</span></h2>
                </div>
                <p class="lead">Every service is delivered directly by RAFly engineers with full source code and asset ownership.</p>
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
                        <a class="card-link" href="<?= e(service_url($svc['slug'])) ?>" aria-label="<?= e($svc['title']) ?>"></a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Regional Growth Partner';
    $ctaTitle   = 'Ready to build your digital system in Delhi NCR?';
    $ctaText    = 'Tell us about your project requirements. We respond within one working day with a clear scope and roadmap.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
