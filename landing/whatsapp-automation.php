<?php
require_once dirname(__DIR__) . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Services', 'url' => '/#services'],
    ['name' => '24/7 Lead Automation', 'url' => '/landing/whatsapp-automation'],
];

$page = [
    'id'        => 'landing-whatsapp-automation',
    'title'     => '24/7 Automated Lead Qualification & WhatsApp Systems | RAFly',
    'desc'      => 'Never lose a lead to slow response times. RAFly builds 24/7 lead qualification and automated WhatsApp workflows that engage prospects within 60 seconds.',
    'bodyClass' => 'page-landing page-whatsapp-automation',
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
        <div class="sig-env" aria-hidden="true" style="opacity: 0.75;">
            <div class="sig-env__grain"></div>
            <div class="sig-env__grid"></div>
        </div>

        <div class="container" style="position: relative; z-index: 2;">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split" style="align-items: flex-start; margin-top: 1rem;">
                <div style="max-width: 680px;">
                    <span class="badge badge-soft-blue" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1rem; display: inline-block;">GROW PILLAR — LEAD AUTOMATION ENGINE</span>
                    <h1 class="display" style="font-size: clamp(2.2rem, 4vw, 3.5rem); font-weight: 800; color: #06122f; line-height: 1.1;">
                        24/7 Lead Response <span class="soft" style="background: linear-gradient(135deg, #0a63ff 0%, #0891b2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">&amp; Qualification Engine</span>
                    </h1>
                    <p class="lead" style="color: #475569; font-size: 1.1rem; line-height: 1.6; margin-top: 1rem;">
                        Engage web prospects within 60 seconds. Our automated workflows score lead intent, filter budget fit, and route high-priority inquiries directly to your sales team on WhatsApp.
                    </p>

                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 2rem;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?= icon('check', 'text-accent') ?> <span>Sub-60s Response Benchmark</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?= icon('check', 'text-accent') ?> <span>Webhook CRM Sync (HubSpot / Zoho)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?= icon('check', 'text-accent') ?> <span>Calendar Booking Automation</span>
                        </div>
                    </div>
                </div>

                <!-- LEAD AUTOMATION FORM CARD -->
                <div style="width: 100%; max-width: 460px; background: #ffffff; border: 1px solid rgba(10,99,255,0.2); border-radius: var(--r-2xl); padding: 2rem; box-shadow: 0 20px 50px rgba(10,99,255,0.1);">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #06122f; margin-bottom: 0.5rem;">Automate Your Lead Pipeline</h2>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">Request a 15-minute WhatsApp automation demo &amp; setup blueprint.</p>

                    <form method="post" action="/submit" id="automationLeadForm">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="source_page" value="/landing/whatsapp-automation">
                        <input type="hidden" name="service_slug" value="lead-automation">
                        <input type="hidden" name="service_interest" value="Grow (Lead Automation & WhatsApp)">
                        <input type="hidden" name="budget_bracket" value="Starter: ₹25,000 - ₹60,000">

                        <div style="margin-bottom: 1rem;">
                            <label for="wa_company_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">Company Name *</label>
                            <input id="wa_company_name" class="form-control" type="text" name="company_name" placeholder="Acme Services Pvt Ltd" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="wa_contact_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">Your Name *</label>
                            <input id="wa_contact_name" class="form-control" type="text" name="contact_name" placeholder="John Doe" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="wa_contact_email" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">Email Address *</label>
                            <input id="wa_contact_email" class="form-control" type="email" name="contact_email" placeholder="john@company.com" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <label for="wa_contact_number" style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.3rem;">WhatsApp / Phone Number *</label>
                            <input id="wa_contact_number" class="form-control" type="tel" name="contact_number" placeholder="+91 98765 43210" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </div>

                        <!-- Anti-Bot Field -->
                        <div class="hp-field" aria-hidden="true" style="display:none;">
                            <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%;">
                            <span>Request Automation Strategy Call</span>
                            <?= icon('arrow-right') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- 3-PART AUTOMATION ARCHITECTURE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">AUTOMATION WORKFLOW</p>
                <h2>Three steps to <span class="soft">zero lost leads</span></h2>
                <p class="lead">How RAFly's automated qualification system processes every prospect instantly.</p>
            </div>

            <div class="grid grid-3" style="gap: 1.5rem; margin-top: 2rem;">
                <div class="card card-hover" style="padding: 2rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">PHASE 01 // 60s RESPONSE</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">60-Second Auto-Ack</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Instant automated WhatsApp and Email acknowledgment sent the second a visitor submits a form. Prospects never wait or look for competitors.</p>
                </div>

                <div class="card card-hover" style="padding: 2rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">PHASE 02 // QUALIFICATION</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Intent &amp; Budget Scoring</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Interactive WhatsApp qualification prompts score lead fit based on project budget, timeline, and decision-maker authority.</p>
                </div>

                <div class="card card-hover" style="padding: 2rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.12); border-radius: var(--r-xl);">
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #0a63ff; background: rgba(10,99,255,0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 1rem;">PHASE 03 // ROUTING &amp; CALENDAR</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">CRM &amp; Discovery Sync</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">High-intent leads are automatically synced to Team OS CRM deal stages and sent a direct 15-minute discovery call booking link.</p>
                </div>
            </div>

            <!-- DEMO PREVIEW CARD -->
            <div style="margin-top: 3rem; padding: 2.2rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.2); border-radius: var(--r-2xl); box-shadow: 0 10px 30px rgba(10,99,255,0.06); display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap;">
                <div style="max-width: 620px;">
                    <span class="badge badge-soft-blue" style="font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; display: inline-block;">WHATSAPP WORKFLOW DEMO</span>
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: #06122f; margin: 0 0 0.4rem 0;">Test the WhatsApp Automation Engine Live</h3>
                    <p style="font-size: 0.95rem; color: #475569; margin: 0; line-height: 1.6;">
                        Click below to chat with RAFly's automated WhatsApp system directly on your phone and experience the 60-second qualification flow.
                    </p>
                </div>
                <div>
                    <a class="btn btn-primary btn-lg" target="_blank" rel="noopener" href="<?= e(whatsapp_link('Hi Rafly! I want to test the live 24/7 WhatsApp Lead Qualification demo.')) ?>">
                        <?= icon('whatsapp', 'icon-fill') ?> Test WhatsApp Demo Live
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Ready to automate?';
    $ctaTitle   = 'Turn website traffic into qualified sales calls.';
    $ctaText    = 'Let our team build and configure your custom 24/7 WhatsApp & CRM lead qualification pipeline.';
    require_once dirname(__DIR__) . '/partials/cta-band.php';
    ?>
</main>
<?php
require_once dirname(__DIR__) . '/partials/tail.php';
