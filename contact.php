<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',    'url' => '/'],
    ['name' => 'Contact', 'url' => '/contact'],
];

$page = [
    'id'        => 'contact',
    'title'     => 'Start A Conversation | RAFly Digital Growth Partner',
    'desc'      => 'Initiate a project brief with RAFly Digital Growth Partner. Scoped proposals delivered within 24 business hours.',
    'bodyClass' => 'page-contact',
    'styles'    => ['home', 'home-scenes', 'about'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- 01 — HERO: START A CONVERSATION & PROJECT INTAKE PIPELINE (LIGHT THEME) -->
    <section class="section hero sig-hero blueprint-canvas" style="min-height: 85vh; display: flex; align-items: center; padding-block: 4rem; position: relative; background: #ffffff;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.25rem;">
                    <span class="glow-dot-active"></span> DIRECT BUILD CHANNEL
                </div>
                <h1 style="font-size: clamp(2.2rem, 3.8vw, 3.4rem); font-weight: 850; color: #050f33; line-height: 1.1; margin-bottom: 1rem; letter-spacing: -0.03em;">
                    START A <span style="color: #0a63ff;">CONVERSATION</span>
                </h1>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6; margin-bottom: 1.75rem; max-width: 500px;">
                    Configure your project parameters below to receive a detailed milestone proposal, technical breakdown, and clear pricing within 24 business hours.
                </p>
                
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem; display: flex; flex-direction: column; gap: 0.6rem;">
                    <div style="font-family: var(--font-mono, monospace); font-size: 0.82rem; color: #050f33; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('map-pin') ?> HQ: A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306
                    </div>
                    <div style="font-family: var(--font-mono, monospace); font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('mail') ?> <?= e(CONTACT_EMAIL) ?> &nbsp;•&nbsp; <?= icon('phone') ?> <?= e(CONTACT_PHONE) ?>
                    </div>
                </div>

                <!-- Telemetry Row -->
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="telemetry-pill-mono" style="background: rgba(10, 99, 255, 0.08); color: #0b52d8; border: 1px solid rgba(10, 99, 255, 0.2);">
                        <?= icon('clock') ?> &lt; 24 Hrs Proposal SLA
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.2);">
                        <?= icon('shield') ?> Direct Senior Practitioner
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(22, 163, 74, 0.08); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2);">
                        <?= icon('check') ?> Written SOW Guarantee
                    </span>
                </div>
            </div>

            <!-- HERO CUSTOM SVG: INTAKE PROCESS PIPELINE (LIGHT THEME) -->
            <div class="code-console-window" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(16px); border: 1px solid rgba(10, 99, 255, 0.18); border-radius: 20px; padding: 1.5rem; box-shadow: 0 15px 40px rgba(10, 99, 255, 0.08);">
                <div class="code-console-bar" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #0a63ff; margin-left: 0.4rem; font-family: var(--font-mono, monospace);">
                            PROJECT_INTAKE // PROCESS
                        </span>
                    </div>
                    <span class="machined-badge machined-badge-green" style="font-size: 0.65rem; padding: 2px 8px;">● OPEN</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: center; padding: 0.5rem 0; width: 100%; height: 90px;">
                    <lottie-player
                        src="/assets/lottie/support.json"
                        background="transparent"
                        speed="1"
                        style="width: 90px; height: 90px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 1rem;">
                    <svg viewBox="0 0 420 200" fill="none" style="width: 100%;">
                        <rect x="20" y="80" width="70" height="40" rx="8" fill="#ffffff" stroke="#0284c7" stroke-width="1.8" />
                        <text x="55" y="104" font-family="monospace" font-size="8.5" fill="#0284c7" text-anchor="middle" font-weight="bold">01. BRIEF</text>

                        <line x1="90" y1="100" x2="120" y2="100" stroke="#0a63ff" stroke-width="2" stroke-dasharray="3 3"/>
                        <circle r="3.5" fill="#0a63ff"><animateMotion path="M90,100 L120,100" dur="1.2s" repeatCount="indefinite" /></circle>

                        <rect x="120" y="80" width="70" height="40" rx="8" fill="#ffffff" stroke="#0a63ff" stroke-width="1.8" />
                        <text x="155" y="104" font-family="monospace" font-size="8.5" fill="#0a63ff" text-anchor="middle" font-weight="bold">02. SCOPE</text>

                        <line x1="190" y1="100" x2="220" y2="100" stroke="#0a63ff" stroke-width="2" stroke-dasharray="3 3"/>
                        <circle r="3.5" fill="#0a63ff"><animateMotion path="M190,100 L220,100" dur="1.2s" repeatCount="indefinite" /></circle>

                        <rect x="220" y="80" width="70" height="40" rx="8" fill="#ffffff" stroke="#9333ea" stroke-width="1.8" />
                        <text x="255" y="104" font-family="monospace" font-size="8.5" fill="#9333ea" text-anchor="middle" font-weight="bold">03. SOW</text>

                        <line x1="290" y1="100" x2="320" y2="100" stroke="#16a34a" stroke-width="2"/>
                        <circle r="3.5" fill="#16a34a"><animateMotion path="M290,100 L320,100" dur="1.2s" repeatCount="indefinite" /></circle>

                        <rect x="320" y="80" width="80" height="40" rx="8" fill="#f0fdf4" stroke="#16a34a" stroke-width="2" />
                        <text x="360" y="104" font-family="monospace" font-size="8.5" fill="#16a34a" text-anchor="middle" font-weight="bold">04. SPRINT</text>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 WHAT ARE YOU BUILDING? -->
    <section class="section blueprint-canvas" style="padding-block: 5rem; background: #ffffff;">
        <div class="container container-narrow text-center">
            <div class="machined-badge machined-badge-cyan" style="margin-bottom: 0.8rem;">CORE DOMAINS</div>
            <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">WHAT ARE YOU BUILDING?</h2>
            <p class="lead" style="margin-top: 1rem; color: #475569; max-width: 600px; margin-inline: auto; line-height: 1.6;">
                Whether launching a new web app, hardening an active store, or scaling paid media, select your domain in the console below.
            </p>
        </div>
    </section>

    <!-- 03 INTAKE CONSOLE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 2.5rem; text-align: center;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// INTAKE CONSOLE</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">SUBMIT YOUR PROJECT PARAMETERS</h2>
            </div>
            <?php $formId = 'contactLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
