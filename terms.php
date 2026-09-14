<?php
require __DIR__ . '/inc/bootstrap.php';

$lastUpdated = 'September 11, 2026';

$crumbs = [
    ['name' => 'Home',  'url' => '/'],
    ['name' => 'Terms', 'url' => '/terms'],
];

$page = [
    'id'        => 'terms',
    'title'     => 'Service Agreement & Terms | RAFly Digital Growth Partner',
    'desc'      => 'Operating terms, delivery frameworks, intellectual property, and service agreements for RAFly engagements.',
    'bodyClass' => 'page-legal page-terms',
    'styles'    => ['home', 'home-scenes', 'legal'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">
    <!-- SECTION 01 — HERO HEADER -->
    <section class="section page-head blueprint-canvas" style="padding-block: 4rem 2rem;">
        <div class="container container-narrow">
            <?= breadcrumbs($crumbs) ?>
            <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.8rem; margin-top: 0.5rem;">
                <span class="glow-dot-active"></span> OPERATING FRAMEWORK // SOW TERMS
            </div>
            <h1 class="display" style="font-size: clamp(2.4rem, 4vw, 3.5rem); font-weight: 800; color: #050f33; margin-bottom: 0.5rem;">Service Agreement &amp; Operating Terms</h1>
            <p class="policy-updated" style="font-family: var(--font-mono); font-size: 0.82rem; color: #64748b;">Version 2.4 &nbsp;•&nbsp; Effective Date: <?= e($lastUpdated) ?></p>
        </div>
    </section>

    <!-- SECTION 02 — LEGAL CONTENT ARCHITECTURE WITH STICKY NAVIGATION -->
    <div class="container container-narrow policy-wrap" style="padding-block: 2rem 5rem;">
        <div class="machined-card policy-notice" style="margin-bottom: 2rem; background: #ffffff; border-left: 4px solid #0a63ff;">
            This agreement sets forth the operational standard, delivery commitments, and intellectual property transfer rules governing engagements between <strong>RAFly Digital Growth Partner</strong> ("RAFly", "Studio", "we") and our clients ("Client", "you").
        </div>

        <div class="machined-card policy-toc" data-r="rise" style="margin-bottom: 3rem; background: #ffffff;">
            <div class="machined-badge machined-badge-cyan" style="margin-bottom: 0.75rem;">DOCUMENT INDEX</div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: #050f33; margin-bottom: 1rem;">Document Index</h2>
            <ol style="line-height: 1.8; color: #475569; font-size: 0.95rem;">
                <li><a href="#engagement-scope" style="color: #0a63ff; font-weight: 600;">01. Engagement Scope &amp; Statements of Work</a></li>
                <li><a href="#delivery-milestones" style="color: #0a63ff; font-weight: 600;">02. Delivery Milestones &amp; Timelines</a></li>
                <li><a href="#ip-transfer" style="color: #0a63ff; font-weight: 600;">03. Intellectual Property &amp; Code Ownership</a></li>
                <li><a href="#security-sla" style="color: #0a63ff; font-weight: 600;">04. Security Standards &amp; SLA Terms</a></li>
                <li><a href="#payment-terms" style="color: #0a63ff; font-weight: 600;">05. Payment &amp; Commercial Structure</a></li>
                <li><a href="#confidentiality" style="color: #0a63ff; font-weight: 600;">06. Confidentiality &amp; Non-Disclosure</a></li>
                <li><a href="#warranty-limits" style="color: #0a63ff; font-weight: 600;">07. Warranties &amp; Liability Boundaries</a></li>
                <li><a href="#governing-law" style="color: #0a63ff; font-weight: 600;">08. Jurisdiction &amp; Dispute Resolution</a></li>
            </ol>
        </div>

        <section class="policy-section" data-r="rise" id="engagement-scope">
            <h2><?= icon('file-text') ?> 01. Engagement Scope &amp; Statements of Work</h2>
            <p>Every engineering or growth project initiated with RAFly is governed by a written Statement of Work (SOW) or project scope document. The SOW defines explicit deliverables, technical specifications, and boundary conditions prior to project initiation.</p>
        </section>

        <section class="policy-section" data-r="rise" id="delivery-milestones">
            <h2><?= icon('clock') ?> 02. Delivery Milestones &amp; Timelines</h2>
            <p>Projects progress through defined execution stages: Discovery, Architecture, Build, Verification, and Launch. Milestone sign-offs require written client approval before moving to subsequent phases.</p>
        </section>

        <section class="policy-section" data-r="rise" id="ip-transfer">
            <h2><?= icon('shield') ?> 03. Intellectual Property &amp; Code Ownership</h2>
            <p>Upon final settlement of project invoices, RAFly transfers 100% full intellectual property rights and code ownership to the Client for custom application source code, designs, and assets created specifically for the engagement.</p>
        </section>

        <section class="policy-section" data-r="rise" id="security-sla">
            <h2><?= icon('lock') ?> 04. Security Standards &amp; SLA Terms</h2>
            <p>All delivered systems follow zero-trust architectural hardening standards. Support and maintenance SLAs govern initial response and triage times based on selected retainer tier.</p>
        </section>

        <section class="policy-section" data-r="rise" id="payment-terms">
            <h2><?= icon('credit-card') ?> 05. Payment &amp; Commercial Structure</h2>
            <p>Engagement fees are milestone-based or structured as monthly retainers as specified in the active SOW. Invoices are due within 14 days of issuance unless otherwise agreed in writing.</p>
        </section>

        <section class="policy-section" data-r="rise" id="confidentiality">
            <h2><?= icon('eye-off') ?> 06. Confidentiality &amp; Non-Disclosure</h2>
            <p>Both parties agree to treat all non-public technical, commercial, and strategic information exchanged during the engagement as strict confidential material.</p>
        </section>

        <section class="policy-section" data-r="rise" id="warranty-limits">
            <h2><?= icon('alert-triangle') ?> 07. Warranties &amp; Liability Boundaries</h2>
            <p>Delivered software includes a standard 30-day post-launch warranty covering bug remediation against approved SOW specifications. Liability is capped at total fees paid for the specific SOW.</p>
        </section>

        <section class="policy-section" data-r="rise" id="governing-law">
            <h2><?= icon('map-pin') ?> 08. Jurisdiction &amp; Dispute Resolution</h2>
            <p>This agreement is governed by the laws of India, with exclusive jurisdiction in the courts of Delhi NCR (Noida / Greater Noida).</p>
        </section>
    </div>

    <?php
    $ctaEyebrow = 'READY TO INITIATE A PROJECT?';
    $ctaTitle   = 'Start a project brief with RAFly.';
    $ctaText    = 'Our intake team responds within 24 business hours with clear milestone proposals.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>

<?php require __DIR__ . "/partials/tail.php"; ?>
