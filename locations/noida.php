<?php
/**
 * Noida Commercial & Tech Hub Location Page.
 * Dedicated regional page for tech parks, IT corridors, and enterprise brands in Noida.
 */

require __DIR__ . '/../inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',       'url' => '/'],
    ['name' => 'Locations',  'url' => '/locations/greater-noida'],
    ['name' => 'Noida',      'url' => '/locations/noida'],
];

$title = 'Web Development & Digital Systems in Noida | ' . SITE_NAME;
$desc  = 'RAFly delivers high-performance web development, security audits, and performance marketing for businesses, tech hubs, and commercial brands in Noida & Delhi NCR.';

$page = [
    'id'        => 'locations',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-location',
    'styles'    => ['home', 'about'],
    'canonical' => 'locations/noida',
    'schema'    => [
        schema_webpage('locations/noida', $title, $desc, 'WebPage'),
        schema_breadcrumbs($crumbs),
    ],
];

$services = array_values(services_all());

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/social-rail.php';
?>
<main id="main">
    <section class="section page-head">
        <?php require __DIR__ . '/../partials/head-object.php'; ?>
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split">
                <div>
                    <span class="tk-kicker">01 // NOIDA COMMERCIAL HUB</span>
                    <h1 class="display">Web Development &amp; <span class="soft">Digital Systems in Noida.</span></h1>
                </div>
                <p class="lead">
                    Serving enterprise hubs, IT corridors (Sector 62, 125, 132), and D2C commercial brands across Noida with dedicated web development, Web Application Firewalls, and performance marketing systems.
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot">
        <div class="container">
            <div class="split split-wide-l">
                <div>
                    <span class="tk-kicker">02 // REGIONAL CAPABILITIES</span>
                    <h2>Engineering for Noida's Fast-Growing Tech Ecosystem</h2>
                    <p style="margin-top:1rem; color: #475569; line-height: 1.6;">
                        Noida has emerged as a premier technology and commercial hub in Delhi NCR. Whether you are launching a modern web platform, hardening security for a live web application, or scaling paid media campaigns, RAFly provides a single accountable engineering team.
                    </p>
                    <div style="margin-top:2rem; display:flex; gap:1rem; flex-wrap:wrap;">
                        <a class="btn btn-pill" href="/contact">Start a Project in Noida &rarr;</a>
                        <a class="btn btn-pill-outline" href="/web-development">Explore Web Dev Services</a>
                    </div>
                </div>
                <div>
                    <div style="background:#ffffff; border:1px solid rgba(6,18,47,0.12); border-radius:16px; padding:1.5rem; box-shadow: 0 4px 16px rgba(6,18,47,0.04);">
                        <span style="font-family:var(--font-mono, monospace); font-size:0.75rem; font-weight:800; color:#0a63ff; letter-spacing:0.08em; display:block; margin-bottom:0.75rem;">NOIDA SERVICE MATRIX</span>
                        <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Sub-15ms Custom PHP &amp; Laravel Web Applications</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; WAF &amp; Vulnerability Reviews for Sector 62 Tech Firms</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Headless Shopify &amp; Payment Gateway Integrations</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Server-Side GTM &amp; Performance Advertising</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid Context -->
    <section class="section ground-2">
        <div class="container">
            <span class="tk-kicker">03 // CORE SERVICES AVAILABLE IN NOIDA</span>
            <h2 style="font-family:var(--font-display); font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight:800; color:#06122f; margin-top:0.35rem;">
                One Coordinated Team Across All Digital Touchpoints
            </h2>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1.25rem; margin-top:2rem;">
                <?php foreach ($services as $s): ?>
                <div style="background:#ffffff; border:1px solid rgba(6,18,47,0.12); border-radius:14px; padding:1.25rem; display:flex; flex-direction:column; justify-content:space-between; gap:1rem;">
                    <div>
                        <span style="font-family:var(--font-mono, monospace); font-size:0.7rem; font-weight:800; color:#0a63ff; text-transform:uppercase; letter-spacing:0.08em;"><?= e($s['badge']) ?></span>
                        <h3 style="font-family:var(--font-display); font-size:1.2rem; font-weight:800; color:#06122f; margin:6px 0 4px;"><?= e($s['title']) ?></h3>
                        <p style="font-size:0.88rem; color:#475569; margin:0; line-height:1.45;"><?= e($s['card']) ?></p>
                    </div>
                    <a href="/<?= e($s['slug']) ?>" style="font-family:var(--font-mono, monospace); font-size:0.78rem; font-weight:700; color:#0a63ff; text-decoration:none;">View Scope &rarr;</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lead Form Partial -->
    <section class="section close-editorial-section" id="start">
        <div class="container">
            <div class="ce-grid">
                <div class="ce-copy">
                    <span class="ce-kicker">04 // START CONVERSATION</span>
                    <h2 class="ce-title">Ready to build your digital system in Noida?</h2>
                    <p class="ce-lead">Tell us about your project requirements. We respond within one working day with a clear scope and roadmap.</p>
                </div>
                <div class="ce-form-wrap">
                    <?php require __DIR__ . '/../partials/lead-form.php'; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../partials/tail.php'; ?>
