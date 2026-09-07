<?php
require_once dirname(__DIR__) . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Services', 'url' => '/#services'],
    ['name' => 'Security Emergency Response', 'url' => '/landing/security-emergency'],
];

$page = [
    'id'        => 'landing-security-emergency',
    'title'     => 'Emergency Website Security & Malware Recovery | RAFly',
    'desc'      => 'Is your website hacked, defaced, or flagged by Google? RAFly provides 1-Hour Emergency Incident Triage to isolate, clean, and restore your site.',
    'bodyClass' => 'page-landing page-security-emergency',
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
    <section class="section hero sig-hero svc-clean-hero band-ink" style="min-height: clamp(480px, 60vh, 600px); padding-block: clamp(2.5rem, 4vh, 4rem); position: relative; overflow: hidden; background: #040b1e; color: #ffffff;">
        <div class="sig-env" aria-hidden="true" style="opacity: 0.8;">
            <div class="sig-env__grain"></div>
            <div class="sig-env__grid"></div>
        </div>

        <div class="container" style="position: relative; z-index: 2;">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split" style="align-items: flex-start; margin-top: 1rem;">
                <div style="max-width: 680px;">
                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1rem; display: inline-block;">PROTECT PILLAR — 24/7 EMERGENCY RESPONSE</span>
                    <h1 class="display" style="font-size: clamp(2.2rem, 4vw, 3.5rem); font-weight: 800; color: #ffffff !important; line-height: 1.1;">
                        Hacked Site or Google Blacklist? <span style="color: #f87171 !important;">1-Hour Incident Triage SLA.</span>
                    </h1>
                    <p class="lead" style="color: #94a3b8; font-size: 1.1rem; line-height: 1.6; margin-top: 1rem;">
                        Don't lose customers, search traffic, or ad revenue. We quarantine malicious scripts, clean infected databases, resubmit Google security reviews, and harden your site against future attacks.
                    </p>

                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 2rem;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #cbd5e1;">
                            <span class="pulse-dot-red"></span> <span>1-Hour Response SLA (24/7)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #cbd5e1;">
                            <?= icon('shield', 'text-accent') ?> <span>Google Blacklist Resubmission</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #cbd5e1;">
                            <?= icon('check', 'text-accent') ?> <span>WAF &amp; Header Hardening</span>
                        </div>
                    </div>
                </div>

                <!-- EMERGENCY LEAD FORM CARD -->
                <div style="width: 100%; max-width: 460px; background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--r-2xl); padding: 2rem; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin: 0;">Emergency Cleanup Request</h2>
                        <span class="pulse-dot-red"></span>
                    </div>
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.5rem;">Submit your details for immediate 1-hour incident triage.</p>

                    <form method="post" action="/submit" id="emergencyLeadForm">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="source_page" value="/landing/security-emergency">
                        <input type="hidden" name="service_slug" value="web-security">
                        <input type="hidden" name="service_interest" value="Protect (Security Emergency)">
                        <input type="hidden" name="budget_bracket" value="Emergency Response: ₹12,000 - ₹25,000">

                        <div style="margin-bottom: 1rem;">
                            <label for="sec_company_url" style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.3rem;">Compromised Site URL *</label>
                            <input id="sec_company_url" class="form-control" type="url" name="company_name" placeholder="https://yourwebsite.com" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; background: #1e293b; color: #fff; border: 1px solid #334155;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="sec_contact_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.3rem;">Your Name *</label>
                            <input id="sec_contact_name" class="form-control" type="text" name="contact_name" placeholder="John Doe" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; background: #1e293b; color: #fff; border: 1px solid #334155;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="sec_contact_email" style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.3rem;">Email Address *</label>
                            <input id="sec_contact_email" class="form-control" type="email" name="contact_email" placeholder="john@company.com" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; background: #1e293b; color: #fff; border: 1px solid #334155;">
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <label for="sec_contact_number" style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.3rem;">WhatsApp / Emergency Phone *</label>
                            <input id="sec_contact_number" class="form-control" type="tel" name="contact_number" placeholder="+91 98765 43210" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; background: #1e293b; color: #fff; border: 1px solid #334155;">
                        </div>

                        <!-- Anti-Bot Field -->
                        <div class="hp-field" aria-hidden="true" style="display:none;">
                            <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%; background: #ef4444; border-color: #ef4444;">
                            <span>Dispatch Emergency Incident Triage</span>
                            <?= icon('arrow-right') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- 3-STAGE RECOVERY ARCHITECTURE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">RECOVERY ARCHITECTURE</p>
                <h2>Three-stage <span class="soft">incident playbook</span></h2>
                <p class="lead">From initial malware quarantine to Google review resubmission and WAF hardening.</p>
            </div>

            <div class="grid grid-3" style="gap: 1.5rem; margin-top: 2rem;">
                <div class="card" style="padding: 2rem; background: #ffffff; border-top: 4px solid #ef4444; border-radius: var(--r-xl); box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #ef4444; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.8rem;">STAGE 01 // 1-HOUR SLA</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Quarantine &amp; Contain</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Immediate 1-hour triage response. We isolate infected file directories, freeze attacker backdoors, and restrict database write permissions.</p>
                </div>

                <div class="card" style="padding: 2rem; background: #ffffff; border-top: 4px solid #f59e0b; border-radius: var(--r-xl); box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.8rem;">STAGE 02 // SANITIZATION</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Clean &amp; Verify</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Core file verification against clean repositories, complete database malware sanitization, and verification of admin user tables.</p>
                </div>

                <div class="card" style="padding: 2rem; background: #ffffff; border-top: 4px solid #10b981; border-radius: var(--r-xl); box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 800; color: #10b981; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.8rem;">STAGE 03 // DEFENSE & RESUBMISSION</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;">Harden &amp; Monitor</h3>
                    <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">Configuring Web Application Firewall (WAF) rules, resubmitting Google Blacklist review requests, and 7-day post-incident monitoring.</p>
                </div>
            </div>

            <!-- HOTLINE STRIP -->
            <div style="margin-top: 3rem; padding: 2rem; background: #0f172a; color: #ffffff; border-radius: var(--r-2xl); display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
                <div>
                    <h3 style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin: 0 0 0.4rem 0;">Prefer Immediate Direct Contact?</h3>
                    <p style="font-size: 0.95rem; color: #94a3b8; margin: 0;">24/7 Security Emergency Hotline: <strong>+91 8796882212</strong></p>
                </div>
                <div>
                    <a class="btn btn-primary btn-lg" target="_blank" rel="noopener" href="<?= e(whatsapp_link('EMERGENCY: My website is hacked/flagged and I need urgent security assistance.')) ?>" style="background: #ef4444; border-color: #ef4444;">
                        <?= icon('whatsapp', 'icon-fill') ?> WhatsApp Security Hotline (24/7)
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Security SLA';
    $ctaTitle   = 'Response < 1 Hour. Resolution Target < 4 Hours.';
    $ctaText    = 'Do not let a security incident destroy customer trust or search rankings. Contact our incident triage team now.';
    require_once dirname(__DIR__) . '/partials/cta-band.php';
    ?>
</main>
<?php
require_once dirname(__DIR__) . '/partials/tail.php';
