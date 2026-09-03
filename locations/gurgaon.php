<?php
/**
 * Gurgaon Tech & Corporate Hub Location Page.
 * Dedicated regional page for tech startups, corporate headquarters, and scale-ups in Gurgaon (Gurugram).
 */

require __DIR__ . '/../inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',       'url' => '/'],
    ['name' => 'Locations',  'url' => '/locations/greater-noida'],
    ['name' => 'Gurgaon',    'url' => '/locations/gurgaon'],
];

$title = 'Web Development & Tech Systems in Gurgaon | ' . SITE_NAME;
$desc  = 'RAFly builds high-performance web applications, security infrastructures, and performance marketing platforms for tech startups and corporate hubs in Gurgaon.';

$page = [
    'id'        => 'locations',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-location',
    'styles'    => ['home', 'about'],
    'canonical' => 'locations/gurgaon',
    'schema'    => [
        schema_webpage('locations/gurgaon', $title, $desc, 'WebPage'),
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
                    <span class="tk-kicker">01 // GURGAON TECH HUB</span>
                    <h1 class="display">Web Development &amp; <span class="soft">Scale-Up Systems in Gurgaon.</span></h1>
                </div>
                <p class="lead">
                    Providing high-availability web applications, automated security reviews, and growth infrastructure for tech startups and enterprise headquarters across Gurgaon Cyber City and Golf Course Road.
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot">
        <div class="container">
            <div class="split split-wide-l">
                <div>
                    <span class="tk-kicker">02 // ENTERPRISE TECH CAPABILITIES</span>
                    <h2>High-Performance Engineering for Gurgaon Scale-Ups</h2>
                    <p style="margin-top:1rem; color: #475569; line-height: 1.6;">
                        Fast-growing companies in Gurgaon need scalable web architectures, 100/100 Core Web Vitals, and strict data security protocols. RAFly provides a dedicated engineering team with fixed scopes and guaranteed SLAs.
                    </p>
                    <div style="margin-top:2rem; display:flex; gap:1rem; flex-wrap:wrap;">
                        <a class="btn btn-pill" href="/contact">Start a Project in Gurgaon &rarr;</a>
                        <a class="btn btn-pill-outline" href="/web-security">View Web Security Services</a>
                    </div>
                </div>
                <div>
                    <div style="background:#ffffff; border:1px solid rgba(6,18,47,0.12); border-radius:16px; padding:1.5rem; box-shadow: 0 4px 16px rgba(6,18,47,0.04);">
                        <span style="font-family:var(--font-mono, monospace); font-size:0.75rem; font-weight:800; color:#0a63ff; letter-spacing:0.08em; display:block; margin-bottom:0.75rem;">GURGAON SERVICE SPECS</span>
                        <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Enterprise MVC Architecture &amp; Custom Portals</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; TLS 1.3, HSTS &amp; WAF Defense Protocols</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Headless Shopify &amp; PCI-DSS Payment Integration</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Performance Paid Media &amp; Analytics Tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid Context -->
    <section class="section ground-2">
        <div class="container">
            <span class="tk-kicker">03 // CORE SERVICES AVAILABLE IN GURGAON</span>
            <h2 style="font-family:var(--font-display); font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight:800; color:#06122f; margin-top:0.35rem;">
                Full Digital Systems Stack Under One Partner
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
                    <span class="ce-kicker">04 // DIRECT BUILD CHANNEL</span>
                    <h2 class="ce-title">Build your system in Gurgaon</h2>
                    <p class="ce-lead">Reach out to our engineering team for a detailed proposal and scope breakdown.</p>
                </div>
                <div class="ce-form-wrap">
                    <?php require __DIR__ . '/../partials/lead-form.php'; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../partials/tail.php'; ?>
