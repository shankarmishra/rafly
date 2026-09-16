<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',  'url' => '/'],
    ['name' => 'About', 'url' => '/about'],
];

$page = [
    'id'        => 'about',
    'title'     => 'About Us — Web Development & Security Agency | RAFLY',
    'desc'      => 'RAFly is an engineering and growth studio building web applications, hardening cyber perimeters, and scaling performance marketing.',
    'bodyClass' => 'page-about',
    'styles'    => ['home', 'home-scenes', 'about'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- 01 — HERO: THE STUDIO & RAFLY OPERATING SYSTEM (LIGHT THEME) -->
    <section class="section hero sig-hero blueprint-canvas" style="min-height: 85vh; display: flex; align-items: center; padding-block: 4rem; position: relative; background: #ffffff;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.25rem;">
                    <span class="glow-dot-active"></span> STUDIO OPERATING SYSTEM
                </div>
                <h1 style="font-size: clamp(2.2rem, 3.8vw, 3.4rem); font-weight: 850; color: #050f33; line-height: 1.1; margin-bottom: 1rem; letter-spacing: -0.03em;">
                    DIGITAL ENGINEERING + <span style="color: #0a63ff;">GROWTH STUDIO</span>
                </h1>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6; margin-bottom: 1.75rem; max-width: 520px;">
                    We operate at the intersection of technical software development, zero-trust security, and performance growth. One accountable team, zero agency proxy layers.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
                    <a class="btn btn-primary btn-lg" href="#intake">Work With The Studio <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#principles">Operating Principles <?= icon('arrow-down') ?></a>
                </div>
                
                <!-- Telemetry Row -->
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="telemetry-pill-mono" style="background: rgba(10, 99, 255, 0.08); color: #0b52d8; border: 1px solid rgba(10, 99, 255, 0.2);">
                        <?= icon('zap') ?> 100/100 Core Web Vitals
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.2);">
                        <?= icon('shield') ?> Zero-Trust Architecture
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(22, 163, 74, 0.08); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2);">
                        <?= icon('trending-up') ?> 100% Attributed ROI
                    </span>
                </div>
            </div>

            <!-- HERO VISUAL CONSOLE: RAFLY ECOSYSTEM ARCHITECTURE -->
            <div class="rafly-eco-stage">
                <!-- Header -->
                <div class="rafly-eco-header">
                    <div class="rafly-eco-title">
                        <?= icon('layers') ?> RAFly Studio Ecosystem
                    </div>
                    <span class="rafly-eco-title-badge">STUDIO OPERATING SYSTEM</span>
                </div>

                <!-- Canvas Visual Stage -->
                <div class="rafly-eco-canvas-container">
                    <!-- Background Grid -->
                    <div class="rafly-eco-grid-bg"></div>

                    <!-- Orbital Rings -->
                    <div class="rafly-eco-ring-1"></div>
                    <div class="rafly-eco-ring-2"></div>

                    <!-- Vector Connecting Lines -->
                    <svg class="rafly-eco-svg-lines" viewBox="0 0 460 330" fill="none">
                        <line x1="120" y1="50" x2="230" y2="165" stroke="#0a63ff" stroke-width="1.5" stroke-opacity="0.35" class="rafly-eco-pulse-line" />
                        <line x1="340" y1="50" x2="230" y2="165" stroke="#0284c7" stroke-width="1.5" stroke-opacity="0.35" class="rafly-eco-pulse-line" />
                        <line x1="120" y1="280" x2="230" y2="165" stroke="#16a34a" stroke-width="1.5" stroke-opacity="0.35" class="rafly-eco-pulse-line" />
                        <line x1="340" y1="280" x2="230" y2="165" stroke="#d97706" stroke-width="1.5" stroke-opacity="0.35" class="rafly-eco-pulse-line" />

                        <!-- Animated Light Pulses -->
                        <circle r="3.5" fill="#0a63ff"><animateMotion path="M120,50 L230,165" dur="3s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#0284c7"><animateMotion path="M340,50 L230,165" dur="3.5s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#16a34a"><animateMotion path="M120,280 L230,165" dur="4s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#d97706"><animateMotion path="M340,280 L230,165" dur="4.2s" repeatCount="indefinite" /></circle>
                    </svg>

                    <!-- Central Core Node -->
                    <div class="rafly-eco-core-hub">
                        <div class="rafly-eco-core-title">RAFLY</div>
                        <div class="rafly-eco-core-sub">ENGINE</div>
                    </div>

                    <!-- 4 Studio Pillars Nodes -->
                    <!-- Top Left: Digital Engineering -->
                    <div class="rafly-eco-pillar-card pillar-top-left">
                        <div class="rafly-eco-pillar-icon icon-blue">
                            <?= icon('code') ?>
                        </div>
                        <div class="rafly-eco-pillar-info">
                            <span class="rafly-eco-pillar-num">01 // ENGINEERING</span>
                            <span class="rafly-eco-pillar-label">Software &amp; Web</span>
                            <span class="rafly-eco-pillar-sub">Full-Stack Core</span>
                        </div>
                    </div>

                    <!-- Top Right: Zero-Trust Security -->
                    <div class="rafly-eco-pillar-card pillar-top-right">
                        <div class="rafly-eco-pillar-icon icon-cyan">
                            <?= icon('shield') ?>
                        </div>
                        <div class="rafly-eco-pillar-info">
                            <span class="rafly-eco-pillar-num">02 // SECURITY</span>
                            <span class="rafly-eco-pillar-label">Perimeter Defense</span>
                            <span class="rafly-eco-pillar-sub">Zero-Trust Protocol</span>
                        </div>
                    </div>

                    <!-- Bottom Left: Performance Growth -->
                    <div class="rafly-eco-pillar-card pillar-bot-left">
                        <div class="rafly-eco-pillar-icon icon-green">
                            <?= icon('trending-up') ?>
                        </div>
                        <div class="rafly-eco-pillar-info">
                            <span class="rafly-eco-pillar-num">03 // GROWTH</span>
                            <span class="rafly-eco-pillar-label">Acquisition Funnels</span>
                            <span class="rafly-eco-pillar-sub">Attributed ROI</span>
                        </div>
                    </div>

                    <!-- Bottom Right: Core Web Vitals & Automation -->
                    <div class="rafly-eco-pillar-card pillar-bot-right">
                        <div class="rafly-eco-pillar-icon icon-amber">
                            <?= icon('zap') ?>
                        </div>
                        <div class="rafly-eco-pillar-info">
                            <span class="rafly-eco-pillar-num">04 // PERFORMANCE</span>
                            <span class="rafly-eco-pillar-label">Core Web Vitals</span>
                            <span class="rafly-eco-pillar-sub">Edge Acceleration</span>
                        </div>
                    </div>
                </div>

                <!-- Footer: Studio Team & Accountability -->
                <div class="rafly-eco-footer-bar">
                    <div class="rafly-eco-footer-text">
                        <?= icon('user') ?> Senior Engineers &amp; Growth Specialists
                    </div>
                    <div class="rafly-eco-footer-badge">
                        ● Direct Engagement
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 WHY WE EXIST -->
    <section class="section" style="padding-block: 5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
        <div class="container container-narrow text-center">
            <div class="machined-badge machined-badge-cyan" style="margin-bottom: 0.8rem;">PURPOSE &amp; MISSION</div>
            <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">WHY WE EXIST</h2>
            <p class="lead" style="margin-top: 1rem; color: #475569; max-width: 680px; margin-inline: auto; line-height: 1.7;">
                Traditional agencies split web development, security, and marketing across disconnected vendors who rarely speak. RAFly exists to unify engineering, perimeter protection, and acquisition under one single, accountable scope.
            </p>
        </div>
    </section>

    <!-- 03 OPERATING PRINCIPLES -->
    <section class="section blueprint-canvas" id="principles" style="padding-block: 5rem; background: #f8fafc;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 3rem; text-align: center;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// OPERATING PRINCIPLES</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">HOW WE THINK &amp; BUILD</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">PRINCIPLE 01</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Code is the Contract</h3>
                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.6; margin: 0;">No generic pitch decks. Every architecture design is backed by executable PHP, SQL schemas, and verified metrics.</p>
                </div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.75rem;">PRINCIPLE 02</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Zero Account Managers</h3>
                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.6; margin: 0;">You talk directly with senior software engineers, security auditors, and growth specialists on Slack.</p>
                </div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-green" style="margin-bottom: 0.75rem;">PRINCIPLE 03</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">100% Written IP Ownership</h3>
                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.6; margin: 0;">Upon milestone signoff, all source code, design assets, and database schemas are transferred to you.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 04 WORK WITH THE STUDIO INTAKE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 2.5rem; text-align: center;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// STUDIO INTAKE</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">START AN ENGAGEMENT</h2>
            </div>
            <?php $formId = 'aboutLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
<?php require __DIR__ . '/partials/tail.php'; ?>
