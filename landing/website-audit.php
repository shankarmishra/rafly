<?php
require_once dirname(__DIR__) . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Free Website Audit', 'url' => '/landing/website-audit'],
];

$page = [
    'id'        => 'landing-website-audit',
    'title'     => 'Free 5-Point Website Speed & Security Audit | RAFly',
    'desc'      => 'Get a free, technical 5-point performance, mobile UX, security surface, and technical SEO assessment of your website within 10 minutes.',
    'bodyClass' => 'page-landing page-website-audit',
    'styles'    => ['home', 'home-scenes', 'service'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require_once dirname(__DIR__) . '/partials/head.php';
require_once dirname(__DIR__) . '/partials/header.php';
require_once dirname(__DIR__) . '/partials/social-rail.php';
?>
<main id="main">
    <!-- HERO SECTION -->
    <section class="section hero sig-hero svc-clean-hero" style="min-height: clamp(480px, 60vh, 600px); padding-block: clamp(2.5rem, 4vh, 4rem); position: relative; overflow: hidden;">
        <div class="sig-env" aria-hidden="true" style="opacity: 0.7;">
            <div class="sig-env__grain"></div>
            <div class="sig-env__grid"></div>
            <div class="sig-env__dots"></div>
        </div>

        <div class="container" style="position: relative; z-index: 2;">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split" style="align-items: flex-start; margin-top: 1rem;">
                <div style="max-width: 680px;">
                    <span class="badge badge-soft-blue" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1rem; display: inline-block;">FREE TECHNICAL AUDIT</span>
                    <h1 class="display" style="font-size: clamp(2.2rem, 4vw, 3.5rem); font-weight: 800; color: #06122f; line-height: 1.1;">
                        Get Your Free 5-Point <span class="soft" style="background: linear-gradient(135deg, #0a63ff 0%, #00f2fe 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Speed & Security Audit</span>
                    </h1>
                    <p class="lead" style="color: #475569; font-size: 1.1rem; line-height: 1.6; margin-top: 1rem;">
                        Identify unseen speed bottlenecks, mobile layout breaks, and security vulnerabilities costing you rankings and leads. Delivered within 10 minutes.
                    </p>

                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 2rem;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?= icon('check', 'text-accent') ?> <span>Zero Cost &amp; No Obligation</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?= icon('check', 'text-accent') ?> <span>100% Confidential Report</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?= icon('check', 'text-accent') ?> <span>Actionable Engineer Fixes</span>
                        </div>
                    </div>
                </div>

                <!-- AUDIT FORM CARD -->
                <div style="width: 100%; max-width: 460px; background: #ffffff; border: 1px solid rgba(10,99,255,0.2); border-radius: var(--r-2xl); padding: 2rem; box-shadow: 0 20px 50px rgba(10,99,255,0.1);">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #06122f; margin-bottom: 0.5rem;">Request Your 5-Point Audit</h2>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">Fill out the fields below and our engineering team will inspect your live site.</p>

                    <form method="post" action="/submit" id="auditLeadForm">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="source_page" value="/landing/website-audit">
                        <input type="hidden" name="service_slug" value="web-security">
                        <input type="hidden" name="service_interest" value="Protect (Security & Performance)">
                        <input type="hidden" name="budget_bracket" value="Audit Request">

                        <div style="margin-bottom: 1rem;">
                            <label for="audit_company_url" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">Website URL *</label>
                            <input id="audit_company_url" class="form-control" type="url" name="company_name" placeholder="https://yourwebsite.com" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="audit_contact_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">Your Name *</label>
                            <input id="audit_contact_name" class="form-control" type="text" name="contact_name" placeholder="John Doe" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="audit_contact_email" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">Work Email *</label>
                            <input id="audit_contact_email" class="form-control" type="email" name="contact_email" placeholder="john@company.com" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <label for="audit_contact_number" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">WhatsApp / Phone Number *</label>
                            <input id="audit_contact_number" class="form-control" type="tel" name="contact_number" placeholder="+91 98765 43210" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <!-- Anti-Bot Field -->
                        <div class="hp-field" aria-hidden="true" style="display:none;">
                            <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%;">
                            <span>Generate My Free Audit Report</span>
                            <?= icon('arrow-right') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- 5-POINT AUDIT BREAKDOWN SECTION -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">WHAT WE INSPECT</p>
                <h2>Five diagnostic vectors <span class="soft">in every report</span></h2>
                <p class="lead">We do not send generic automated PDFs. Every audit evaluates five real technical metrics.</p>
            </div>

            <div class="grid grid-3" style="gap: 1.5rem; margin-top: 2rem;">
                <div class="card card-hover" style="padding: 1.8rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">POINT 01</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Core Web Vitals &amp; LCP Speed</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Measuring Largest Contentful Paint (LCP), First Input Delay (FID), and script execution bottlenecks.</p>
                </div>

                <div class="card card-hover" style="padding: 1.8rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">POINT 02</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Mobile Viewport &amp; Touch Layout</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Inspecting touch target padding, horizontal overflow bugs, and responsive font scaling across phone viewports.</p>
                </div>

                <div class="card card-hover" style="padding: 1.8rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">POINT 03</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Security Surface &amp; Headers</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Checking HTTP security headers (CSP, HSTS, X-Frame-Options), SSL configuration, and form injection risks.</p>
                </div>

                <div class="card card-hover" style="padding: 1.8rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">POINT 04</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Technical SEO &amp; Indexing</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Verifying canonical link structures, meta title/description tags, XML sitemap validity, and Schema.org markup.</p>
                </div>

                <div class="card card-hover" style="padding: 1.8rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">POINT 05</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Conversion Friction &amp; Forms</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Evaluating call-to-action visibility, form submission errors, and lead qualification routing.</p>
                </div>

                <div class="card card-hover" style="padding: 1.8rem; background: linear-gradient(135deg, #0a63ff 0%, #0230c6 100%); color: #ffffff; border-radius: var(--r-xl);">
                    <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #ffffff; background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">FAST SLA</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 0.5rem;">Need Instant Feedback?</h3>
                    <p style="font-size: 0.92rem; color: rgba(255,255,255,0.85); margin: 0 0 1.2rem 0; line-height: 1.6;">Message our lead engineers directly on WhatsApp for an immediate quick audit.</p>
                    <a class="btn btn-white btn-sm" target="_blank" rel="noopener" href="<?= e(whatsapp_link('Hi Rafly, I would like a quick speed and security audit of my website.')) ?>">
                        <?= icon('whatsapp', 'icon-fill') ?> Chat on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Ready for clarity?';
    $ctaTitle   = 'Let\'s inspect your website today.';
    $ctaText    = 'Enter your site details above or reach out on WhatsApp to get your 5-point report.';
    require_once dirname(__DIR__) . '/partials/cta-band.php';
    ?>
</main>
<?php
require_once dirname(__DIR__) . '/partials/tail.php';
