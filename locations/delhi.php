<?php
/**
 * Delhi NCR Location Page.
 * Dedicated regional page for commercial enterprises, brands, and retail networks in Delhi NCR.
 */

require __DIR__ . '/../inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',       'url' => '/'],
    ['name' => 'Locations',  'url' => '/locations/greater-noida'],
    ['name' => 'Delhi NCR',  'url' => '/locations/delhi'],
];

$title = 'Web Development & Digital Systems in Delhi NCR | ' . SITE_NAME;
$desc  = 'RAFly provides custom web development, security reviews, content creation, and digital marketing systems for commercial enterprises across Delhi NCR.';

$page = [
    'id'        => 'locations',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-location',
    'styles'    => ['home', 'about'],
    'canonical' => 'locations/delhi',
    'schema'    => [
        schema_webpage('locations/delhi', $title, $desc, 'WebPage'),
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
                    <span class="tk-kicker">01 // DELHI NCR METRO</span>
                    <h1 class="display">Web Development &amp; <span class="soft">Growth Systems in Delhi NCR.</span></h1>
                </div>
                <p class="lead">
                    Delivering fast web applications, enterprise security audits, and high-ROI digital marketing for commercial brands, corporate headquarters, and retail businesses across Delhi NCR.
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot">
        <div class="container">
            <div class="split split-wide-l">
                <div>
                    <span class="tk-kicker">02 // CAPITAL MARKET SOLUTIONS</span>
                    <h2>Engineered for High-Velocity Delhi NCR Businesses</h2>
                    <p style="margin-top:1rem; color: #475569; line-height: 1.6;">
                        Delhi NCR demands fast loading speeds, bulletproof application security, and conversion-focused web user experiences. RAFly replaces fragmented multi-agency teams with one unified engineering partner.
                    </p>
                    <div style="margin-top:2rem; display:flex; gap:1rem; flex-wrap:wrap;">
                        <a class="btn btn-pill" href="/contact">Start a Project in Delhi NCR &rarr;</a>
                        <a class="btn btn-pill-outline" href="/pricing">View Transparent Pricing</a>
                    </div>
                </div>
                <div>
                    <div style="background:#ffffff; border:1px solid rgba(6,18,47,0.12); border-radius:16px; padding:1.5rem; box-shadow: 0 4px 16px rgba(6,18,47,0.04);">
                        <span style="font-family:var(--font-mono, monospace); font-size:0.75rem; font-weight:800; color:#0a63ff; letter-spacing:0.08em; display:block; margin-bottom:0.75rem;">DELHI NCR ENGAGEMENT SPECS</span>
                        <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Full-Stack Custom PHP &amp; Laravel Web Portals</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Vulnerability Assessments &amp; WAF Defense Systems</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Technical Copywriting &amp; Content Architecture</li>
                            <li style="font-size:0.9rem; color:#1e293b;">&check; Performance Paid Media &amp; Server-Side GTM Tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid Context -->
    <section class="section ground-2">
        <div class="container">
            <span class="tk-kicker">03 // BUNDLED CAPABILITIES FOR DELHI NCR</span>
            <h2 style="font-family:var(--font-display); font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight:800; color:#06122f; margin-top:0.35rem;">
                Five Digital Capabilities, One Accountable Team
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
                    <h2 class="ce-title">Build your system in Delhi NCR</h2>
                    <p class="ce-lead">Get in touch with our engineering team for a clear proposal within 24 hours.</p>
                </div>
                <div class="ce-form-wrap">
                    <?php require __DIR__ . '/../partials/lead-form.php'; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../partials/tail.php'; ?>
