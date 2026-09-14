<?php
require __DIR__ . '/inc/bootstrap.php';

// Slug validation against SERVICES (inc/config.php)
$service = (string)($_GET['service'] ?? '');
if (!array_key_exists($service, SERVICES)) {
    require __DIR__ . '/404.php';
    exit;
}

$data = service_find($service);
if ($data === null) {
    require __DIR__ . '/404.php';
    exit;
}

$serviceUrl = service_url($service);

$crumbs = [
    ['name' => 'Home',     'url' => '/'],
    ['name' => 'Services', 'url' => '/#services'],
    ['name' => $data['title'], 'url' => $serviceUrl],
];

$page = [
    'id'        => 'services',
    'title'     => $data['title'] . ' | RAFly Digital Growth Partner',
    'desc'      => $data['intro'],
    'bodyClass' => 'page-service svc-' . $data['key'],
    'styles'    => ['home', 'home-scenes', 'service'],
    'module'    => 'home',
    'canonical' => ltrim($serviceUrl, '/'),
    'schema'    => [
        schema_service($data['title'], $data['intro'], $data['highlights'], schema_id('service-' . $service)),
        schema_breadcrumbs($crumbs),
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';

$key = $data['key']; // 'web', 'security', 'marketing', 'content', 'ecom', 'automation'
?>
<main id="main">

<?php if ($key === 'web'): ?>
    <!-- =========================================================================
         01. WEB DEVELOPMENT — DIGITAL PRODUCT ENGINE (EXACTLY 8 SECTIONS)
         ========================================================================= -->
    <!-- 01 HERO / SYSTEM INTRODUCTION -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 88vh; padding-block: 4rem; position: relative; overflow: hidden; display: flex; align-items: center;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active"></span> DIGITAL PRODUCT ENGINE
                </div>
                <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.08; letter-spacing: -0.025em; margin-bottom: 1rem;">
                    WEB ENGINE &amp; <span style="color: #0a63ff;">APPLICATION ARCHITECTURE</span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    Custom PHP 8.3 REST APIs, React &amp; Next.js applications, and decoupled web engines engineered for sub-50ms server response times and 100/100 Core Web Vitals.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake">Start Web Build <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#topology">Explore Topology <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- HERO VISUAL CONSOLE: WEB DEVELOPMENT ENGINE -->
            <div class="svc-eco-stage svc-web-stage">
                <div class="svc-eco-header">
                    <div class="svc-eco-title">
                        <?= icon('code') ?> RAFly Web Engine
                    </div>
                    <span class="svc-eco-badge">APPLICATION ENGINE</span>
                </div>
                <div class="svc-eco-canvas">
                    <div class="svc-eco-grid-bg"></div>
                    <svg class="svc-eco-svg" viewBox="0 0 460 330" fill="none">
                        <line x1="120" y1="50" x2="230" y2="165" stroke="#0a63ff" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />
                        <line x1="340" y1="50" x2="230" y2="165" stroke="#0284c7" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />
                        <line x1="120" y1="280" x2="230" y2="165" stroke="#16a34a" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />
                        <line x1="340" y1="280" x2="230" y2="165" stroke="#d97706" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />

                        <circle r="3.5" fill="#0a63ff"><animateMotion path="M120,50 L230,165" dur="3s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#0284c7"><animateMotion path="M340,50 L230,165" dur="3.5s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#16a34a"><animateMotion path="M120,280 L230,165" dur="4s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#d97706"><animateMotion path="M340,280 L230,165" dur="4.2s" repeatCount="indefinite" /></circle>
                    </svg>

                    <div class="svc-eco-hub">
                        <div class="svc-eco-hub-title">WEB</div>
                        <div class="svc-eco-hub-sub">ENGINE</div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-left">
                        <div class="svc-node-icon icon-blue"><?= icon('layers') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">01 // FRONTEND</span>
                            <span class="svc-node-label">React &amp; Next.js</span>
                            <span class="svc-node-sub">Decoupled UI</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-right">
                        <div class="svc-node-icon icon-cyan"><?= icon('cpu') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">02 // BACKEND</span>
                            <span class="svc-node-label">PHP 8.3 REST API</span>
                            <span class="svc-node-sub">Sub-50ms Engine</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-left">
                        <div class="svc-node-icon icon-green"><?= icon('database') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">03 // VAULT</span>
                            <span class="svc-node-label">MySQL &amp; Redis</span>
                            <span class="svc-node-sub">Cached Queries</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-right">
                        <div class="svc-node-icon icon-amber"><?= icon('zap') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">04 // CDN EDGE</span>
                            <span class="svc-node-label">Core Web Vitals</span>
                            <span class="svc-node-sub">Sub-50ms LCP</span>
                        </div>
                    </div>
                </div>
                <div class="svc-eco-footer">
                    <div class="svc-eco-footer-text"><?= icon('code') ?> PHP 8.3 • React • Next.js • MySQL</div>
                    <div class="svc-eco-footer-badge" style="background:rgba(10,99,255,0.08); color:#0a63ff; border:1px solid rgba(10,99,255,0.2);">
                        ● Full-Stack Core
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 FRONTEND SYSTEM -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">FRONTEND ARCHITECTURE</span>
                <h2>EXPLODED PRODUCT INTERFACE SYSTEM</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#0a63ff; font-weight:700;">MODULE 01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Enterprise Portals</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Decoupled single-page applications built with React and Next.js, utilizing server-side rendering for instant indexability.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#0a63ff; font-weight:700;">MODULE 02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Subscription Dashboards</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Real-time metric telemetry, customer account authorization guards, subscription billing APIs, and interactive data grids.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#0a63ff; font-weight:700;">MODULE 03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Internal Operations Tools</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Custom operational portals, administrative management panels, automated worker queues, and database inspection consoles.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 APPLICATION ARCHITECTURE -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">BACKEND CONTROLLERS</span>
                <h2>PHP 8.3 &amp; DECOUPLED BACKEND ARCHITECTURE</h2>
            </div>
            <div class="code-console-window" style="padding: 1.75rem;">
                <div style="font-size: 0.85rem; line-height: 1.8;">
                    <div style="color:#10b981;">&lt;?php declare(strict_types=1);</div>
                    <div><span style="color:#0a63ff;">namespace</span> Rafly\Engine\Controllers;</div>
                    <div><span style="color:#0a63ff;">final class</span> <span style="color:#38bdf8;">ApplicationController</span> {</div>
                    <div>&nbsp;&nbsp;<span style="color:#0a63ff;">public function</span> <span style="color:#10b981;">dispatch</span>(<span style="color:#38bdf8;">Request</span> $request): <span style="color:#38bdf8;">Response</span> {</div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#64748b;">// Sub-50ms response dispatch pipeline</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#0a63ff;">return</span> <span style="color:#0a63ff;">new</span> <span style="color:#38bdf8;">JsonResponse</span>([<span style="color:#10b981">'status'</span> =&gt; <span style="color:#10b981">'200_OK'</span>, <span style="color:#10b981">'lcp'</span> =&gt; <span style="color:#10b981">'38ms'</span>, <span style="color:#10b981">'cache'</span> =&gt; <span style="color:#10b981">'HIT'</span>]);</div>
                    <div>&nbsp;&nbsp;}</div>
                    <div>}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 04 API + DATA FLOW -->
    <section class="section band-soft" id="topology">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">TOPOLOGY</span>
                <h2>DECOUPLED DATA FLOW &amp; SQL VAULT</h2>
            </div>
            <div class="grid grid-2" style="gap:1.5rem;">
                <div class="machined-card">
                    <strong style="color:#0a63ff; font-family:var(--font-mono); font-size:0.8rem;">DATA LAYER 01</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Normalized SQL Schema</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">B-Tree indexed MySQL tables, PDO prepared statements, foreign key enforcement, and ACID transaction compliance.</p>
                </div>
                <div class="machined-card">
                    <strong style="color:#0a63ff; font-family:var(--font-mono); font-size:0.8rem;">DATA LAYER 02</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Redis In-Memory Caching</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Sub-millisecond session state management, key-value query result caching, and rate limiting buckets.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 05 PERFORMANCE + ACCESSIBILITY -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">TELEMETRY GAUGES</span>
                <h2>PERFORMANCE &amp; ACCESSIBILITY METRICS</h2>
            </div>
            <div class="grid grid-4" style="gap:1.25rem; text-align:center;">
                <div class="machined-card">
                    <strong style="font-size:2.2rem; color:#0a63ff; display:block; font-weight:800; font-family:var(--font-display);">100/100</strong>
                    <span class="telemetry-pill-mono" style="margin-top:0.4rem; display:inline-block;">LIGHTHOUSE VITAL</span>
                </div>
                <div class="machined-card">
                    <strong style="font-size:2.2rem; color:#10b981; display:block; font-weight:800; font-family:var(--font-display);">38ms</strong>
                    <span class="telemetry-pill-mono" style="margin-top:0.4rem; display:inline-block;">LCP RESPONSE</span>
                </div>
                <div class="machined-card">
                    <strong style="font-size:2.2rem; color:#38bdf8; display:block; font-weight:800; font-family:var(--font-display);">WCAG 2.1</strong>
                    <span class="telemetry-pill-mono" style="margin-top:0.4rem; display:inline-block;">ARIA ACCESSIBLE</span>
                </div>
                <div class="machined-card">
                    <strong style="font-size:2.2rem; color:#9333ea; display:block; font-weight:800; font-family:var(--font-display);">PSR-12</strong>
                    <span class="telemetry-pill-mono" style="margin-top:0.4rem; display:inline-block;">CODE COMPLIANT</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 06 QA / TESTING / DEPLOYMENT -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">CI/CD DEPLOYMENT</span>
                <h2>AUTOMATED QA &amp; DEPLOYMENT PIPELINE</h2>
            </div>
            <div class="machined-card" style="font-family:var(--font-mono); font-size:0.88rem; display:flex; flex-direction:column; gap:0.8rem;">
                <div><strong style="color:#0a63ff; font-weight:800;">STAGE 1:</strong> Automated Unit &amp; Integration Testing (<span style="color:#38bdf8;">PHPUnit / Jest</span>)</div>
                <div><strong style="color:#0a63ff; font-weight:800;">STAGE 2:</strong> Static Security &amp; Type Analysis (<span style="color:#38bdf8;">PHPStan Level 9 / Psalm</span>)</div>
                <div><strong style="color:#0a63ff; font-weight:800;">STAGE 3:</strong> Staging Build &amp; Visual Regression Verification (<span style="color:#38bdf8;">Playwright / Lighthouse</span>)</div>
                <div><strong style="color:#0a63ff; font-weight:800;">STAGE 4:</strong> Zero-Downtime Atomic Deployment (<span style="color:#10b981;">Git / SSH Symlink Release</span>)</div>
            </div>
        </div>
    </section>

    <!-- 07 BUILD SCENARIOS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">VERIFIED PROOF</span>
                <h2>TECHNICAL BUILD SCENARIOS</h2>
            </div>
            <div class="grid grid-3" style="gap:1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-blue" style="margin-bottom:0.5rem;">SCENARIO #01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Legacy Monolith Modernization</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Refactored legacy portal into a decoupled PHP 8.3 REST API with Redis caching, cutting average page load times from 3.2s to 38ms under heavy traffic.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-blue" style="margin-bottom:0.5rem;">SCENARIO #02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">SaaS Customer Billing Portal</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Engineered React customer portal connected to Stripe webhooks, handling real-time usage metrics and automated subscription tier upgrades.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-blue" style="margin-bottom:0.5rem;">SCENARIO #03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Operations Control Console</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Built custom administrative dashboard with role-based access controls, worker queue monitoring, and sub-100ms SQL query execution.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 08 START A PROJECT INTAKE -->
    <section class="section band-soft" id="intake">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">WEB INTAKE CONSOLE</span>
                <h2>START A WEB DEVELOPMENT BRIEF</h2>
            </div>
            <?php $formId = 'webLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

<?php elseif ($key === 'security'): ?>
    <!-- =========================================================================
         02. WEB SECURITY — DIGITAL DEFENSE PERIMETER (EXACTLY 8 SECTIONS)
         ========================================================================= -->
    <!-- 01 SECURITY COMMAND CENTER HERO -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 88vh; padding-block: 4rem; display: flex; align-items: center; position: relative;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-red" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active" style="color:#dc2626;"></span> DEFENSE PERIMETER COMMAND CENTER
                </div>
                <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.08; letter-spacing: -0.025em; margin-bottom: 1rem;">
                    CYBER SECURITY &amp; <span style="color: #dc2626;">PERIMETER HARDENING</span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    Zero-trust application architecture, vulnerability audits, WAF inspection rules, Argon2id authentication guards, and emergency breach recovery.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake" style="background:#dc2626; border-color:#dc2626;">Secure The Project <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#audit">Audit Surface <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- HERO VISUAL CONSOLE: WEB SECURITY STAGE -->
            <div class="svc-eco-stage svc-sec-stage">
                <div class="svc-eco-header">
                    <div class="svc-eco-title">
                        <?= icon('shield') ?> RAFly Cyber Shield
                    </div>
                    <span class="svc-eco-badge">DEFENSE PERIMETER</span>
                </div>
                <div class="svc-eco-canvas">
                    <div class="svc-eco-grid-bg"></div>
                    <svg class="svc-eco-svg" viewBox="0 0 460 330" fill="none">
                        <!-- Concentric Shield Rings -->
                        <circle cx="230" cy="165" r="140" stroke="#dc2626" stroke-width="1.5" stroke-dasharray="6 6" stroke-opacity="0.3" class="svc-eco-dash-pulse" />
                        <circle cx="230" cy="165" r="95" stroke="#0284c7" stroke-width="1.5" stroke-opacity="0.35" />

                        <!-- Connecting Laser Paths -->
                        <line x1="120" y1="50" x2="230" y2="165" stroke="#dc2626" stroke-width="1.5" stroke-opacity="0.4" />
                        <line x1="340" y1="50" x2="230" y2="165" stroke="#0284c7" stroke-width="1.5" stroke-opacity="0.4" />
                        <line x1="120" y1="280" x2="230" y2="165" stroke="#16a34a" stroke-width="1.5" stroke-opacity="0.4" />
                        <line x1="340" y1="280" x2="230" y2="165" stroke="#d97706" stroke-width="1.5" stroke-opacity="0.4" />

                        <!-- Animated Intercept Pulses -->
                        <circle r="3.5" fill="#dc2626"><animateMotion path="M120,50 L230,165" dur="2.5s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#0284c7"><animateMotion path="M340,50 L230,165" dur="3s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#16a34a"><animateMotion path="M120,280 L230,165" dur="3.8s" repeatCount="indefinite" /></circle>
                    </svg>

                    <!-- Central Core Hub -->
                    <div class="svc-eco-hub">
                        <div class="svc-eco-hub-title">VAULT</div>
                        <div class="svc-eco-hub-sub">ZERO-TRUST</div>
                    </div>

                    <!-- 4 Security Pillar Nodes -->
                    <div class="svc-eco-node-card svc-node-top-left">
                        <div class="svc-node-icon" style="background:rgba(220,38,38,0.08); color:#dc2626;"><?= icon('shield') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">01 // WAF PERIMETER</span>
                            <span class="svc-node-label">Threat Interceptor</span>
                            <span class="svc-node-sub">Blocked XSS / SQLi</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-right">
                        <div class="svc-node-icon icon-cyan"><?= icon('lock') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">02 // AUTH VAULT</span>
                            <span class="svc-node-label">Argon2id &amp; TOTP</span>
                            <span class="svc-node-sub">Session Guards</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-left">
                        <div class="svc-node-icon icon-green"><?= icon('check-circle') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">03 // TLS ENCRYPTION</span>
                            <span class="svc-node-label">HTTPS TLS 1.3</span>
                            <span class="svc-node-sub">AES-256-GCM</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-right">
                        <div class="svc-node-icon icon-amber"><?= icon('activity') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">04 // AUDIT LOOP</span>
                            <span class="svc-node-label">Penetration Test</span>
                            <span class="svc-node-sub">4-Stage Patching</span>
                        </div>
                    </div>
                </div>
                <div class="svc-eco-footer">
                    <div class="svc-eco-footer-text"><?= icon('shield') ?> WAF Rules • Argon2id • TLS 1.3 • Audit</div>
                    <div class="svc-eco-footer-badge" style="background:rgba(220,38,38,0.08); color:#dc2626; border:1px solid rgba(220,38,38,0.2);">
                        ● Perimeter Guard
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 ATTACK SURFACE -->
    <section class="section band-soft" id="audit">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">THREAT MAP</span>
                <h2>ATTACK SURFACE &amp; VULNERABILITY MATRIX</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card" style="border-color: rgba(220,38,38,0.2);">
                    <span class="telemetry-pill-mono" style="color:#dc2626; font-weight:700;">RISK 01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Broken Auth &amp; Session Leak</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;"><strong style="color:#dc2626;">CONTROL:</strong> Argon2id password hashing, HttpOnly cookie flags, TOTP MFA, and single-use CSRF token rotation.</p>
                </div>
                <div class="machined-card" style="border-color: rgba(220,38,38,0.2);">
                    <span class="telemetry-pill-mono" style="color:#dc2626; font-weight:700;">RISK 02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">SQL &amp; Command Injection</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;"><strong style="color:#dc2626;">CONTROL:</strong> PDO prepared statements, strict parameter type-casting, and database user privilege minimization.</p>
                </div>
                <div class="machined-card" style="border-color: rgba(220,38,38,0.2);">
                    <span class="telemetry-pill-mono" style="color:#dc2626; font-weight:700;">RISK 03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Cross-Site Scripting (XSS)</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;"><strong style="color:#dc2626;">CONTROL:</strong> Contextual output HTML escaping, strict Content-Security-Policy headers, and input sanitization.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 AUTH + IDENTITY -->
    <section class="section">
        <div class="container container-narrow">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">IDENTITY GUARDS</span>
                <h2>AUTHENTICATION &amp; IDENTITY HARDENING</h2>
                <p class="lead" style="margin-top:1rem; color:#475569;">
                    Implementation of TOTP multi-factor authentication, Argon2id password hashing, single-use CSRF tokens, and zero-trust session revocation across every endpoint.
                </p>
            </div>
        </div>
    </section>

    <!-- 04 APPLICATION HARDENING -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">DEFENSIVE LOOP</span>
                <h2>4-STAGE SECURITY HARDENING LOOP</h2>
            </div>
            <div class="code-console-window" style="padding:1.5rem; display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; text-align:center;">
                <div><span style="color:#ef4444; font-weight:800; font-size:1.2rem;">01</span><br><span style="font-size:0.8rem; font-weight:700;">DETECT VULNERABILITY</span></div>
                <div><span style="color:#eab308; font-weight:800; font-size:1.2rem;">02</span><br><span style="font-size:0.8rem; font-weight:700;">REMEDIATE &amp; PATCH</span></div>
                <div><span style="color:#38bdf8; font-weight:800; font-size:1.2rem;">03</span><br><span style="font-size:0.8rem; font-weight:700;">PENETRATION TEST</span></div>
                <div><span style="color:#10b981; font-weight:800; font-size:1.2rem;">04</span><br><span style="font-size:0.8rem; font-weight:700;">VERIFY PROTECTION</span></div>
            </div>
        </div>
    </section>

    <!-- 05 DATA PROTECTION -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">VAULT PROTECTION</span>
                <h2>DATA ENCRYPTION &amp; VAULT SHIELDING</h2>
            </div>
            <div class="grid grid-2" style="gap:1.5rem;">
                <div class="machined-card">
                    <strong style="color:#dc2626; font-family:var(--font-mono); font-size:0.8rem;">DATA AT REST</strong>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">AES-256 Storage Encryption</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Encrypted database backups, restricted file system permissions, and isolated environment secret vaults.</p>
                </div>
                <div class="machined-card">
                    <strong style="color:#dc2626; font-family:var(--font-mono); font-size:0.8rem;">DATA IN TRANSIT</strong>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">TLS 1.3 Transport Security</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Strict TLS 1.3 transport encryption, HSTS response headers, and Perfect Forward Secrecy key rotation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 06 MONITOR + INCIDENT RESPONSE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">TELEMETRY STREAM</span>
                <h2>24/7 INCIDENT RESPONSE &amp; TELEMETRY STREAM</h2>
            </div>
            <div class="machined-card" style="font-family:var(--font-mono); font-size:0.88rem; line-height:1.6;">
                Emergency Incident SLA: <strong style="color:#dc2626;">&lt; 1 hour triage response</strong> for active security events. Real-time anomaly detection, automated IP blocking, and immutable audit logs.
            </div>
        </div>
    </section>

    <!-- 07 SECURITY SCENARIOS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">VERIFIED PROOF</span>
                <h2>REALISTIC SECURITY SCENARIOS</h2>
            </div>
            <div class="grid grid-3" style="gap:1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-red" style="margin-bottom:0.5rem;">SECURITY #01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Emergency Breach Containment</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Contained session leak attempt within 45 minutes, patched input parameters, and deployed automated WAF IP block rules.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-red" style="margin-bottom:0.5rem;">SECURITY #02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">API Authentication Hardening</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Migrated legacy API token system to Argon2id hashed keys with rate limiting buckets, stopping credential stuffing attacks.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-red" style="margin-bottom:0.5rem;">SECURITY #03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Infrastructure Penetration Audit</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Audited public-facing endpoints, identified 4 header misconfigurations, and implemented strict Content-Security-Policy rules.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 08 SECURE THE PROJECT INTAKE -->
    <section class="section band-soft" id="intake">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-red" style="margin-bottom: 0.5rem;">SECURITY INTAKE CONSOLE</span>
                <h2>SECURE YOUR PROJECT WITH RAFLY</h2>
            </div>
            <?php $formId = 'securityLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

<?php elseif ($key === 'marketing'): ?>
    <!-- =========================================================================
         03. PERFORMANCE MARKETING — SIGNAL CONVERSION NETWORK (EXACTLY 8 SECTIONS)
         ========================================================================= -->
    <!-- 01 SIGNAL ENGINE HERO -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 88vh; padding-block: 4rem; display: flex; align-items: center; position: relative;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-green" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active" style="color:#16a34a;"></span> SIGNAL → DEMAND → CONVERSION ENGINE
                </div>
                <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.08; letter-spacing: -0.025em; margin-bottom: 1rem;">
                    PERFORMANCE MARKETING &amp; <span style="color: #16a34a;">GROWTH TELEMETRY</span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    Server-side GA4 &amp; Meta CAPI signal tracking, intent-driven ad campaigns, friction-free conversion funnels, and attribution accuracy.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake" style="background:#16a34a; border-color:#16a34a;">Grow With RAFly <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#intent">Audience Intent <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- HERO VISUAL CONSOLE: PERFORMANCE MARKETING STAGE -->
            <div class="svc-eco-stage svc-mkt-stage">
                <div class="svc-eco-header">
                    <div class="svc-eco-title">
                        <?= icon('trending-up') ?> RAFly Growth Engine
                    </div>
                    <span class="svc-eco-badge">ACQUISITION FUNNEL</span>
                </div>
                <div class="svc-eco-canvas">
                    <div class="svc-eco-grid-bg"></div>
                    <svg class="svc-eco-svg" viewBox="0 0 460 330" fill="none">
                        <line x1="120" y1="50" x2="230" y2="165" stroke="#16a34a" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />
                        <line x1="340" y1="50" x2="230" y2="165" stroke="#0a63ff" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />
                        <line x1="120" y1="280" x2="230" y2="165" stroke="#0284c7" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />
                        <line x1="340" y1="280" x2="230" y2="165" stroke="#d97706" stroke-width="1.5" stroke-opacity="0.35" class="svc-eco-dash-pulse" />

                        <circle r="3.5" fill="#16a34a"><animateMotion path="M120,50 L230,165" dur="2.8s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#0a63ff"><animateMotion path="M340,50 L230,165" dur="3.2s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#0284c7"><animateMotion path="M120,280 L230,165" dur="3.6s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#d97706"><animateMotion path="M340,280 L230,165" dur="4s" repeatCount="indefinite" /></circle>
                    </svg>

                    <div class="svc-eco-hub">
                        <div class="svc-eco-hub-title">ROI</div>
                        <div class="svc-eco-hub-sub">ENGINE</div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-left">
                        <div class="svc-node-icon icon-green"><?= icon('trending-up') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">01 // TRAFFIC INGEST</span>
                            <span class="svc-node-label">Meta &amp; Google Ads</span>
                            <span class="svc-node-sub">High-Intent Traffic</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-right">
                        <div class="svc-node-icon icon-blue"><?= icon('activity') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">02 // CAPI SYNC</span>
                            <span class="svc-node-label">Server GA4 &amp; CAPI</span>
                            <span class="svc-node-sub">100% Attribution</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-left">
                        <div class="svc-node-icon icon-cyan"><?= icon('layers') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">03 // FUNNEL CONSOLE</span>
                            <span class="svc-node-label">Frictionless Intake</span>
                            <span class="svc-node-sub">Conversion Lift</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-right">
                        <div class="svc-node-icon icon-amber"><?= icon('zap') ?></div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">04 // ROAS OPTIMIZER</span>
                            <span class="svc-node-label">Creative Varianting</span>
                            <span class="svc-node-sub">Campaign Scale</span>
                        </div>
                    </div>
                </div>
                <div class="svc-eco-footer">
                    <div class="svc-eco-footer-text"><?= icon('trending-up') ?> Meta CAPI • GA4 Telemetry • Funnels • ROAS</div>
                    <div class="svc-eco-footer-badge" style="background:rgba(22,163,74,0.08); color:#16a34a; border:1px solid rgba(22,163,74,0.2);">
                        ● Attributed ROI
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 AUDIENCE & INTENT -->
    <section class="section band-soft" id="intent">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">INTENT MATRIX</span>
                <h2>AUDIENCE INTENT STAGES</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#16a34a; font-weight:700;">STAGE 01. DISCOVERY</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">High-Retention Hooks</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Capturing audience attention across Meta Ads, Google Search, and LinkedIn with targeted problem statements.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#16a34a; font-weight:700;">STAGE 02. CONSIDERATION</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Editorial Proof Content</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Technical scenario breakdowns, architectural comparison grids, and verified client deliverables.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#16a34a; font-weight:700;">STAGE 03. PURCHASE</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Frictionless Intake</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">High-converting landing page consoles, sub-50ms page load speeds, and 3-step intake consoles.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 CREATIVE SYSTEM -->
    <section class="section">
        <div class="container container-narrow">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">CREATIVE VARIANTS</span>
                <h2>BRANCHING CREATIVE VARIANT ARCHITECTURE</h2>
                <p class="lead" style="margin-top:1rem; color:#475569;">
                    Systematic testing of visual angles, hook scripts, and ad formats to identify top-performing campaign variants without budget waste.
                </p>
            </div>
        </div>
    </section>

    <!-- 04 CAMPAIGN ARCHITECTURE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">OPERATING BLUEPRINT</span>
                <h2>CAMPAIGN OPERATING ARCHITECTURE</h2>
            </div>
            <div class="code-console-window" style="padding:1.5rem; text-align:center; background:#052e16;">
                AD PLATFORM → CAMPAIGN → CREATIVE VARIANT → LANDING PAGE CONSOLE → GA4 CAPI → CRM ATTRIBUTION
            </div>
        </div>
    </section>

    <!-- 05 CONVERSION SYSTEM -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">POST-CLICK JOURNEY</span>
                <h2>CONVERSION EXPERIENCE &amp; FRICTION REMOVAL</h2>
            </div>
            <div class="grid grid-2" style="gap:1.5rem;">
                <div class="machined-card">
                    <strong style="color:#16a34a; font-family:var(--font-mono); font-size:0.8rem;">PAGE SPEED OPTIMIZATION</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Sub-50ms LCP Response</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Sub-50ms server response times eliminate bounce rate spikes on high-intent paid ad traffic.</p>
                </div>
                <div class="machined-card">
                    <strong style="color:#16a34a; font-family:var(--font-mono); font-size:0.8rem;">FORM CONVERSION CONSOLE</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Multi-Step Intake Console</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Multi-step intake consoles increase submission completion rates while capturing rich project context.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 06 MEASUREMENT LOOP -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">ANALYTICS LOOP</span>
                <h2>SERVER-SIDE CAPI &amp; MEASUREMENT LOOP</h2>
            </div>
            <div class="machined-card" style="font-family:var(--font-mono); font-size:0.88rem; line-height:1.6;">
                Server-side Meta Conversions API (CAPI) &amp; GA4 event telemetry for 100% accurate lead attribution and first-party data capture.
            </div>
        </div>
    </section>

    <!-- 07 CAMPAIGN SCENARIOS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">VERIFIED PROOF</span>
                <h2>CAMPAIGN BUILD SCENARIOS</h2>
            </div>
            <div class="grid grid-3" style="gap:1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-green" style="margin-bottom:0.5rem;">MARKETING #01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">B2B Lead Acquisition Funnel</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Implemented server-side CAPI tracking and redesigned landing page intake form, eliminating 35% unallocated lead drop-offs.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-green" style="margin-bottom:0.5rem;">MARKETING #02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">High-Intent Search Campaign</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Structured Google Search campaign hierarchy with exact-match negative keyword filters and targeted landing page consoles.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-green" style="margin-bottom:0.5rem;">MARKETING #03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">E-Commerce Retargeting Engine</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Deployed dynamic catalog ads linked to custom cart drawer events, recovering abandoned checkout sessions.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 08 GROW WITH RAFLY INTAKE -->
    <section class="section band-soft" id="intake">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">MARKETING INTAKE CONSOLE</span>
                <h2>GROW WITH RAFLY</h2>
            </div>
            <?php $formId = 'marketingLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

<?php elseif ($key === 'content'): ?>
    <!-- =========================================================================
         04. CONTENT CREATION — EDITORIAL PRODUCTION PIPELINE (EXACTLY 8 SECTIONS)
         ========================================================================= -->
    <!-- 01 CONTENT STUDIO HERO -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 88vh; padding-block: 4rem; display: flex; align-items: center; position: relative;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-purple" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active" style="color:#9333ea;"></span> EDITORIAL PRODUCTION NLE STUDIO
                </div>
                <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.08; letter-spacing: -0.025em; margin-bottom: 1rem;">
                    CONTENT STUDIO &amp; <span style="color: #9333ea;">VIDEO PIPELINE</span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    Short-form Reels, 4K product shooting, NLE video editing timelines, sound design, and multi-format asset distribution engines.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake" style="background:#9333ea; border-color:#9333ea;">Create With RAFly <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#formats">Format System <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- HERO CUSTOM ECOSYSTEM STAGE: CONTENT EDITORIAL PRODUCTION PIPELINE -->
            <div class="svc-eco-stage svc-cnt-stage" style="width:100%; max-width:540px; justify-self:center;">
                <div class="svc-eco-header">
                    <div>
                        <div class="svc-eco-title">EDITORIAL PRODUCTION STAGE</div>
                        <div class="svc-eco-sub">SHORT-FORM REELS, 4K SHOOTING &amp; NLE TIMELINES</div>
                    </div>
                    <div class="svc-eco-badge">
                        <span class="glow-dot-active" style="background:#9333ea; box-shadow: 0 0 8px #9333ea;"></span> 4K HDR 120FPS
                    </div>
                </div>

                <div class="svc-eco-canvas" style="position:relative; width:100%; height:340px;">
                    <!-- Floating Pillar Cards (4 Nodes) -->
                    <div class="svc-eco-node-card svc-node-top-left">
                        <div class="svc-node-icon" style="background: rgba(147,51,234,0.1); color:#9333ea;">
                            <?= icon('video') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">FORMAT 9:16</span>
                            <span class="svc-node-label">Short-Form Hook</span>
                            <span class="svc-node-sub">Pattern Interrupt</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-right">
                        <div class="svc-node-icon" style="background: rgba(147,51,234,0.1); color:#9333ea;">
                            <?= icon('film') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">NLE TIMELINE</span>
                            <span class="svc-node-label">DaVinci Master</span>
                            <span class="svc-node-sub">ProRes 4444 LUTs</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-left">
                        <div class="svc-node-icon" style="background: rgba(147,51,234,0.1); color:#9333ea;">
                            <?= icon('disc') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">AUDIO MASTER</span>
                            <span class="svc-node-label">Multi-Track SFX</span>
                            <span class="svc-node-sub">Voice Leveling</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-right">
                        <div class="svc-node-icon" style="background: rgba(147,51,234,0.1); color:#9333ea;">
                            <?= icon('share-2') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">DISTRIBUTION</span>
                            <span class="svc-node-label">Multi-Frame Export</span>
                            <span class="svc-node-sub">Insta / TikTok / Ads</span>
                        </div>
                    </div>

                    <!-- Central Core Hub -->
                    <div class="svc-eco-hub" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:104px; height:104px; border-radius:50%; z-index:3; text-align:center; color:#ffffff; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="svc-eco-hub-title" style="color:#ffffff;">NLE STUDIO</span>
                        <span class="svc-eco-hub-sub" style="color:#c084fc;">CORE ENGINE</span>
                    </div>

                    <!-- Vector Laser Mesh -->
                    <svg viewBox="0 0 520 340" fill="none" style="position:absolute; inset:0; width:100%; height:100%; pointer-events:none; z-index:1;">
                        <defs>
                            <linearGradient id="cnt-grad-1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#9333ea" stop-opacity="0.6" />
                                <stop offset="100%" stop-color="#c084fc" stop-opacity="0.1" />
                            </linearGradient>
                        </defs>

                        <!-- Node Connect Vectors -->
                        <path id="cnt-path-tl" d="M 120,50 L 260,170" stroke="url(#cnt-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="cnt-path-tr" d="M 400,50 L 260,170" stroke="url(#cnt-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="cnt-path-bl" d="M 120,290 L 260,170" stroke="url(#cnt-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="cnt-path-br" d="M 400,290 L 260,170" stroke="url(#cnt-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />

                        <!-- Animated Video Signal Pulse -->
                        <circle r="3" fill="#c084fc"><animateMotion dur="2.4s" repeatCount="indefinite"><mpath href="#cnt-path-tl"/></animateMotion></circle>
                        <circle r="3" fill="#c084fc"><animateMotion dur="2.8s" repeatCount="indefinite"><mpath href="#cnt-path-tr"/></animateMotion></circle>
                        <circle r="3" fill="#c084fc"><animateMotion dur="2.2s" repeatCount="indefinite"><mpath href="#cnt-path-bl"/></animateMotion></circle>
                        <circle r="3" fill="#c084fc"><animateMotion dur="2.6s" repeatCount="indefinite"><mpath href="#cnt-path-br"/></animateMotion></circle>

                        <!-- Video Timeline Track Visualizer -->
                        <rect x="180" y="115" width="160" height="8" rx="2" fill="#9333ea" fill-opacity="0.3" />
                        <rect x="180" y="217" width="160" height="8" rx="2" fill="#38bdf8" fill-opacity="0.3" />

                        <!-- Audio Waveform Line -->
                        <path d="M 190,221 Q 210,213 230,221 T 270,221 T 310,221 T 330,221" stroke="#c084fc" stroke-width="1.5" fill="none" />
                    </svg>
                </div>

                <div class="svc-eco-footer">
                    <div class="svc-eco-footer-text">
                        <?= icon('film') ?> 4K HDR Studio Capture → DaVinci Master → Multi-Channel
                    </div>
                    <div class="svc-eco-footer-badge" style="background: rgba(147,51,234,0.1); color:#9333ea;">
                        PRORES 4444
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 STORY ARCHITECTURE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">NARRATIVE FLOW</span>
                <h2>STORY &amp; HOOK ARCHITECTURE</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#9333ea; font-weight:700;">STAGE 01. THE HOOK</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">First 3 Seconds</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Visual pattern interruption, high-contrast motion text, and immediate problem statement to stop the scroll.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#9333ea; font-weight:700;">STAGE 02. NARRATIVE</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Core Value Story</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Concise storytelling, dynamic motion graphics overlays, multi-track sound effects, and product feature highlights.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#9333ea; font-weight:700;">STAGE 03. CALL TO ACTION</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Action Trigger</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Clear conversion prompt directing traffic to landing page intake consoles and booking links.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 FORMAT SYSTEM -->
    <section class="section" id="formats">
        <div class="container container-narrow">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">MULTI-FORMAT</span>
                <h2>SIX VISUAL CONTENT FORMATS</h2>
                <p class="lead" style="margin-top:1rem; color:#475569;">
                    Instagram Reels (9:16), YouTube Shorts (9:16), 4K Widescreen (16:9), Feed Visuals (1:1), Product Video Cuts &amp; High-Retention Performance Ads.
                </p>
            </div>
        </div>
    </section>

    <!-- 04 PRODUCTION PIPELINE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">TIMELINE</span>
                <h2>PRODUCTION TIMELINE &amp; WORKFLOW</h2>
            </div>
            <div class="code-console-window" style="padding:1.5rem; text-align:center; background:#1e1b4b;">
                PRE-PROD → SCRIPTING → 4K SHOOTING → NLE EDITING → SOUND DESIGN → MOTION GRAPHICS → EXPORT
            </div>
        </div>
    </section>

    <!-- 05 EDITING SYSTEM -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">NLE TIMELINE</span>
                <h2>ADVANCED NLE EDITING &amp; MOTION GRAPHICS</h2>
            </div>
            <div class="grid grid-2" style="gap:1.5rem;">
                <div class="machined-card">
                    <strong style="color:#9333ea; font-family:var(--font-mono); font-size:0.8rem;">COLOR GRADING</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">ProRes 4444 Color Mastering</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Custom studio LUTs, DaVinci Resolve color mastering, and HDR 10-bit export curves.</p>
                </div>
                <div class="machined-card">
                    <strong style="color:#9333ea; font-family:var(--font-mono); font-size:0.8rem;">SOUND DESIGN</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Multi-Track Audio Mixing</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Multi-track voiceover leveling, background score ducking, and punchy impact SFX design.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 06 DISTRIBUTION -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">MULTI-CHANNEL</span>
                <h2>MULTI-FRAME DISTRIBUTION ENGINE</h2>
            </div>
            <div class="machined-card" style="font-family:var(--font-mono); font-size:0.88rem; line-height:1.6;">
                Master video cut rendered into platform-specific aspect ratios (9:16, 16:9, 1:1) and frame rates for Instagram, TikTok, YouTube &amp; Meta Ads.
            </div>
        </div>
    </section>

    <!-- 07 CONTENT SCENARIOS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">VERIFIED PROOF</span>
                <h2>PRODUCTION SCENARIOS</h2>
            </div>
            <div class="grid grid-3" style="gap:1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-purple" style="margin-bottom:0.5rem;">CONTENT #01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Short-Form Product Launch</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Produced 12 multi-angle short-form Reels with motion graphics overlays, achieving 84% average 3-second hook retention.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-purple" style="margin-bottom:0.5rem;">CONTENT #02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Founder Story Brand Video</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Filmed 4K studio interview with B-roll overlays and motion typography, establishing founder authority across social channels.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-purple" style="margin-bottom:0.5rem;">CONTENT #03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">SaaS Feature Explainer Cut</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Created animated screen recording walkthrough with voiceover and UI callout graphics, driving landing page conversion lifts.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 08 CREATE WITH RAFLY INTAKE -->
    <section class="section band-soft" id="intake">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">CONTENT INTAKE CONSOLE</span>
                <h2>CREATE WITH RAFLY</h2>
            </div>
            <?php $formId = 'contentLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

<?php elseif ($key === 'ecom'): ?>
    <!-- =========================================================================
         05. E-COMMERCE — COMMERCE OPERATING SYSTEM (EXACTLY 8 SECTIONS)
         ========================================================================= -->
    <!-- 01 COMMERCE ENGINE HERO -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 88vh; padding-block: 4rem; display: flex; align-items: center; position: relative;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-cyan" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active" style="color:#0284c7;"></span> COMMERCE OPERATING SYSTEM
                </div>
                <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.08; letter-spacing: -0.025em; margin-bottom: 1rem;">
                    E-COMMERCE ENGINE &amp; <span style="color: #0284c7;">STORE ARCHITECTURE</span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    Custom Shopify storefronts, headless commerce API buses, friction-free checkout flows, and 6-state order operations engines.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake" style="background:#0284c7; border-color:#0284c7;">Build Store With RAFly <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#checkout">Checkout System <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- HERO CUSTOM ECOSYSTEM STAGE: COMMERCE ORDER PIPELINE ENGINE -->
            <div class="svc-eco-stage svc-ecm-stage" style="width:100%; max-width:540px; justify-self:center;">
                <div class="svc-eco-header">
                    <div>
                        <div class="svc-eco-title">COMMERCE ORDER PIPELINE</div>
                        <div class="svc-eco-sub">SHOPIFY STOREFRONT &amp; HEADLESS API BUS</div>
                    </div>
                    <div class="svc-eco-badge">
                        <span class="glow-dot-active" style="background:#0284c7; box-shadow: 0 0 8px #0284c7;"></span> STRIPE / RAZORPAY API
                    </div>
                </div>

                <div class="svc-eco-canvas" style="position:relative; width:100%; height:340px;">
                    <!-- Floating Pillar Cards (4 Nodes) -->
                    <div class="svc-eco-node-card svc-node-top-left">
                        <div class="svc-node-icon" style="background: rgba(2,132,199,0.1); color:#0284c7;">
                            <?= icon('shopping-bag') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">STOREFRONT</span>
                            <span class="svc-node-label">Product Discovery</span>
                            <span class="svc-node-sub">Fast Grid &amp; Swatches</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-right">
                        <div class="svc-node-icon" style="background: rgba(2,132,199,0.1); color:#0284c7;">
                            <?= icon('shopping-cart') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">CART DRAWER</span>
                            <span class="svc-node-label">Dynamic Ajax Cart</span>
                            <span class="svc-node-sub">Cross-Sell Engine</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-left">
                        <div class="svc-node-icon" style="background: rgba(2,132,199,0.1); color:#0284c7;">
                            <?= icon('credit-card') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">CHECKOUT BUS</span>
                            <span class="svc-node-label">Fast Checkout</span>
                            <span class="svc-node-sub">Address Autocomplete</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-right">
                        <div class="svc-node-icon" style="background: rgba(2,132,199,0.1); color:#0284c7;">
                            <?= icon('box') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">FULFILLMENT</span>
                            <span class="svc-node-label">6-State Order System</span>
                            <span class="svc-node-sub">Logistics Webhooks</span>
                        </div>
                    </div>

                    <!-- Central Core Hub -->
                    <div class="svc-eco-hub" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:104px; height:104px; border-radius:50%; z-index:3; text-align:center; color:#ffffff; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="svc-eco-hub-title" style="color:#ffffff;">COMMERCE</span>
                        <span class="svc-eco-hub-sub" style="color:#38bdf8;">API BUS</span>
                    </div>

                    <!-- Vector Laser Mesh -->
                    <svg viewBox="0 0 520 340" fill="none" style="position:absolute; inset:0; width:100%; height:100%; pointer-events:none; z-index:1;">
                        <defs>
                            <linearGradient id="ecm-grad-1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0284c7" stop-opacity="0.6" />
                                <stop offset="100%" stop-color="#38bdf8" stop-opacity="0.1" />
                            </linearGradient>
                        </defs>

                        <!-- Node Connect Vectors -->
                        <path id="ecm-path-tl" d="M 120,50 L 260,170" stroke="url(#ecm-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="ecm-path-tr" d="M 400,50 L 260,170" stroke="url(#ecm-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="ecm-path-bl" d="M 120,290 L 260,170" stroke="url(#ecm-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="ecm-path-br" d="M 400,290 L 260,170" stroke="url(#ecm-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />

                        <!-- Animated Order Flow Packets -->
                        <circle r="3" fill="#38bdf8"><animateMotion dur="2.2s" repeatCount="indefinite"><mpath href="#ecm-path-tl"/></animateMotion></circle>
                        <circle r="3" fill="#38bdf8"><animateMotion dur="2.6s" repeatCount="indefinite"><mpath href="#ecm-path-tr"/></animateMotion></circle>
                        <circle r="3" fill="#38bdf8"><animateMotion dur="2.0s" repeatCount="indefinite"><mpath href="#ecm-path-bl"/></animateMotion></circle>
                        <circle r="3" fill="#38bdf8"><animateMotion dur="2.4s" repeatCount="indefinite"><mpath href="#ecm-path-br"/></animateMotion></circle>

                        <!-- Storefront Bus Ring -->
                        <circle cx="260" cy="170" r="72" stroke="#38bdf8" stroke-width="1" stroke-dasharray="6,6" opacity="0.4" />
                    </svg>
                </div>

                <div class="svc-eco-footer">
                    <div class="svc-eco-footer-text">
                        <?= icon('shopping-bag') ?> Storefront Cart → Gateway API → Webhook Sync → Order State
                    </div>
                    <div class="svc-eco-footer-badge" style="background: rgba(2,132,199,0.1); color:#0284c7;">
                        SUB-50MS API
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 STOREFRONT EXPERIENCE -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">STOREFRONT</span>
                <h2>STOREFRONT EXPERIENCE MODULES</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#0284c7; font-weight:700;">MODULE 01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Product Discovery</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Fast grid filtering, instant search autocomplete, variant swatch selectors, and high-res media viewports.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#0284c7; font-weight:700;">MODULE 02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Dynamic Cart Drawer</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Instant quantity updates, free shipping progress indicators, and cross-sell recommendation chips.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#0284c7; font-weight:700;">MODULE 03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Optimized Checkout</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Friction-free single-page checkout flow with address autocomplete and instant payment gateway validation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 HEADLESS ARCHITECTURE -->
    <section class="section">
        <div class="container container-narrow">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">HEADLESS API</span>
                <h2>DECOUPLED HEADLESS COMMERCE API</h2>
                <p class="lead" style="margin-top:1rem; color:#475569;">
                    Separating storefront UI from backend order systems via REST/GraphQL API connectors for maximum speed, security, and design freedom.
                </p>
            </div>
        </div>
    </section>

    <!-- 04 CHECKOUT SYSTEM -->
    <section class="section band-soft" id="checkout">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">FRICTION MAP</span>
                <h2>CHECKOUT FRICTION REMOVAL SYSTEM</h2>
            </div>
            <div class="code-console-window" style="padding:1.5rem; text-align:center; background:#0c4a6e;">
                CART → ADDRESS AUTOCOMPLETE → LOGISTICS SHIPPING API → PAYMENT GATEWAY → INSTANT CONFIRMATION
            </div>
        </div>
    </section>

    <!-- 05 ORDER OPERATIONS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">ORDER PIPELINE</span>
                <h2>6-STATE ORDER OPERATIONS PIPELINE</h2>
            </div>
            <div class="grid grid-3" style="gap:1.25rem; text-align:center;">
                <div class="machined-card"><strong style="color:#0284c7; font-family:var(--font-mono);">01. PLACED</strong><p style="font-size:0.8rem; color:#64748b; margin-top:0.3rem;">Order Received</p></div>
                <div class="machined-card"><strong style="color:#0284c7; font-family:var(--font-mono);">02. CONFIRMED</strong><p style="font-size:0.8rem; color:#64748b; margin-top:0.3rem;">Payment Verified</p></div>
                <div class="machined-card"><strong style="color:#0284c7; font-family:var(--font-mono);">03. PAID</strong><p style="font-size:0.8rem; color:#64748b; margin-top:0.3rem;">Funds Settled</p></div>
                <div class="machined-card"><strong style="color:#0284c7; font-family:var(--font-mono);">04. PROCESSING</strong><p style="font-size:0.8rem; color:#64748b; margin-top:0.3rem;">Warehouse Allocated</p></div>
                <div class="machined-card"><strong style="color:#0284c7; font-family:var(--font-mono);">05. SHIPPED</strong><p style="font-size:0.8rem; color:#64748b; margin-top:0.3rem;">Tracking Assigned</p></div>
                <div class="machined-card"><strong style="color:#10b981; font-family:var(--font-mono);">06. DELIVERED</strong><p style="font-size:0.8rem; color:#10b981; margin-top:0.3rem;">Customer Handoff</p></div>
            </div>
        </div>
    </section>

    <!-- 06 INTEGRATION LAYER -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">INTEGRATIONS</span>
                <h2>THIRD-PARTY INTEGRATION MATRIX</h2>
            </div>
            <div class="machined-card" style="font-family:var(--font-mono); font-size:0.88rem; line-height:1.6;">
                Payment Gateways (Stripe, Razorpay, PayU), ERP Systems, Logistics Webhooks (Shiprocket, Delhivery) &amp; Automated Inventory Sync APIs.
            </div>
        </div>
    </section>

    <!-- 07 COMMERCE SCENARIOS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">VERIFIED PROOF</span>
                <h2>COMMERCE BUILD SCENARIOS</h2>
            </div>
            <div class="grid grid-3" style="gap:1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-cyan" style="margin-bottom:0.5rem;">COMMERCE #01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Shopify Storefront &amp; Cart Rebuild</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Rebuilt storefront theme and integrated Ajax cart drawer, reducing checkout completion time from 48s to 12s.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-cyan" style="margin-bottom:0.5rem;">COMMERCE #02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Headless Storefront Migration</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Migrated legacy monolithic store to Next.js storefront with Shopify Storefront API, achieving 100/100 Lighthouse performance.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-cyan" style="margin-bottom:0.5rem;">COMMERCE #03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Multi-Currency Payment API</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Integrated multi-currency Stripe and Razorpay gateways with geo-IP location detection and localized checkout flows.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 08 BUILD THE STORE INTAKE -->
    <section class="section band-soft" id="intake">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">COMMERCE INTAKE CONSOLE</span>
                <h2>BUILD THE STORE WITH RAFLY</h2>
            </div>
            <?php $formId = 'ecomLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

<?php elseif ($key === 'automation'): ?>
    <!-- =========================================================================
         06. LEAD AUTOMATION — LEAD ROUTING NETWORK (EXACTLY 8 SECTIONS)
         ========================================================================= -->
    <!-- 01 LEAD OPERATIONS HERO -->
    <section class="section hero sig-hero svc-hero-section blueprint-canvas" style="min-height: 88vh; padding-block: 4rem; display: flex; align-items: center;">
        <div class="container hero-grid">
            <div>
                <div class="machined-badge machined-badge-amber" style="margin-bottom: 1.2rem;">
                    <span class="glow-dot-active" style="color:#d97706;"></span> LEAD OPERATIONS ROUTING ENGINE
                </div>
                <h1 style="font-size: clamp(2.4rem, 4.2vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.08; letter-spacing: -0.025em; margin-bottom: 1rem;">
                    LEAD AUTOMATION &amp; <span style="color: #d97706;">CRM ROUTING</span>
                </h1>
                <p style="font-size: 1.05rem; color: #334155; line-height: 1.65; margin-bottom: 1.75rem; max-width: 520px;">
                    Multi-channel lead capture, automated qualification decision trees, WhatsApp API routing, and real-time CRM state synchronization.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#intake" style="background:#d97706; border-color:#d97706;">Automate Pipeline <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="#qualification">Decision Trees <?= icon('arrow-down') ?></a>
                </div>
            </div>

            <!-- HERO CUSTOM ECOSYSTEM STAGE: LEAD OPERATIONS ROUTING ENGINE -->
            <div class="svc-eco-stage svc-aut-stage" style="width:100%; max-width:540px; justify-self:center;">
                <div class="svc-eco-header">
                    <div>
                        <div class="svc-eco-title">LEAD OPERATIONS ROUTING ENGINE</div>
                        <div class="svc-eco-sub">MULTI-CHANNEL INTAKE &amp; CRM TRIAGE</div>
                    </div>
                    <div class="svc-eco-badge">
                        <span class="glow-dot-active" style="background:#d97706; box-shadow: 0 0 8px #d97706;"></span> WHATSAPP API ACTIVE
                    </div>
                </div>

                <div class="svc-eco-canvas" style="position:relative; width:100%; height:340px;">
                    <!-- Floating Pillar Cards (4 Nodes) -->
                    <div class="svc-eco-node-card svc-node-top-left">
                        <div class="svc-node-icon" style="background: rgba(217,119,6,0.1); color:#d97706;">
                            <?= icon('inbox') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">INTAKE LAYER</span>
                            <span class="svc-node-label">Multi-Channel Ingest</span>
                            <span class="svc-node-sub">Web / Ads / Webhooks</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-top-right">
                        <div class="svc-node-icon" style="background: rgba(217,119,6,0.1); color:#d97706;">
                            <?= icon('git-branch') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">DECISION TREE</span>
                            <span class="svc-node-label">Qualification Engine</span>
                            <span class="svc-node-sub">Rules &amp; Lead Scoring</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-left">
                        <div class="svc-node-icon" style="background: rgba(217,119,6,0.1); color:#d97706;">
                            <?= icon('message-square') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">WHATSAPP API</span>
                            <span class="svc-node-label">Direct Messaging</span>
                            <span class="svc-node-sub">Instant Triage Bot</span>
                        </div>
                    </div>

                    <div class="svc-eco-node-card svc-node-bot-right">
                        <div class="svc-node-icon" style="background: rgba(217,119,6,0.1); color:#d97706;">
                            <?= icon('database') ?>
                        </div>
                        <div class="svc-node-info">
                            <span class="svc-node-tag">CRM STATE</span>
                            <span class="svc-node-label">Real-Time Sync</span>
                            <span class="svc-node-sub">HubSpot / Salesforce</span>
                        </div>
                    </div>

                    <!-- Central Core Hub -->
                    <div class="svc-eco-hub" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:104px; height:104px; border-radius:50%; z-index:3; text-align:center; color:#ffffff; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="svc-eco-hub-title" style="color:#ffffff;">ROUTING</span>
                        <span class="svc-eco-hub-sub" style="color:#fbbf24;">TRIAGE CORE</span>
                    </div>

                    <!-- Vector Laser Mesh -->
                    <svg viewBox="0 0 520 340" fill="none" style="position:absolute; inset:0; width:100%; height:100%; pointer-events:none; z-index:1;">
                        <defs>
                            <linearGradient id="aut-grad-1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#d97706" stop-opacity="0.6" />
                                <stop offset="100%" stop-color="#fbbf24" stop-opacity="0.1" />
                            </linearGradient>
                        </defs>

                        <!-- Node Connect Vectors -->
                        <path id="aut-path-tl" d="M 120,50 L 260,170" stroke="url(#aut-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="aut-path-tr" d="M 400,50 L 260,170" stroke="url(#aut-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="aut-path-bl" d="M 120,290 L 260,170" stroke="url(#aut-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />
                        <path id="aut-path-br" d="M 400,290 L 260,170" stroke="url(#aut-grad-1)" stroke-width="1.5" stroke-dasharray="4,4" />

                        <!-- Animated Lead Ingestion Signals -->
                        <circle r="3" fill="#fbbf24"><animateMotion dur="2.1s" repeatCount="indefinite"><mpath href="#aut-path-tl"/></animateMotion></circle>
                        <circle r="3" fill="#fbbf24"><animateMotion dur="2.5s" repeatCount="indefinite"><mpath href="#aut-path-tr"/></animateMotion></circle>
                        <circle r="3" fill="#fbbf24"><animateMotion dur="1.9s" repeatCount="indefinite"><mpath href="#aut-path-bl"/></animateMotion></circle>
                        <circle r="3" fill="#fbbf24"><animateMotion dur="2.7s" repeatCount="indefinite"><mpath href="#aut-path-br"/></animateMotion></circle>

                        <!-- Triage Decision Rays -->
                        <line x1="260" y1="170" x2="260" y2="110" stroke="#10b981" stroke-width="1.5" stroke-dasharray="3,3" />
                        <text x="260" y="102" font-family="monospace" font-size="7" fill="#10b981" text-anchor="middle">QUALIFIED LEAD</text>
                    </svg>
                </div>

                <div class="svc-eco-footer">
                    <div class="svc-eco-footer-text">
                        <?= icon('git-branch') ?> Multi-Channel Ingest → Qualification Rules → WhatsApp Router → CRM
                    </div>
                    <div class="svc-eco-footer-badge" style="background: rgba(217,119,6,0.1); color:#d97706;">
                        ZERO LEAD LOSS
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 CAPTURE LAYER -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">UNIFIED INBOX</span>
                <h2>MULTI-CHANNEL INTAKE CAPTURE</h2>
            </div>
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#d97706; font-weight:700;">CHANNEL 01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Website Intake Consoles</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">AJAX 3-step intake consoles with real-time field validation and scope context capture.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#d97706; font-weight:700;">CHANNEL 02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">WhatsApp Business API</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Direct message routing, automated greeting triggers, and instant lead notification webhooks.</p>
                </div>
                <div class="machined-card">
                    <span class="telemetry-pill-mono" style="color:#d97706; font-weight:700;">CHANNEL 03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.5rem;">Paid Ad Lead Webhooks</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Instant lead ingestion from Meta lead forms and Google Ads webhook integrations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 QUALIFICATION ENGINE -->
    <section class="section" id="qualification">
        <div class="container container-narrow">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">DECISION TREE</span>
                <h2>QUALIFICATION ENGINE &amp; TRIAGE NODES</h2>
                <p class="lead" style="margin-top:1rem; color:#475569;">
                    Automated evaluation of budget bracket, service interest, and urgency to route high-intent leads directly to senior engineers.
                </p>
            </div>
        </div>
    </section>

    <!-- 04 ROUTING LOGIC -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">DECISION MATRIX</span>
                <h2>ROUTING LOGIC MATRIX</h2>
            </div>
            <div class="code-console-window" style="padding:1.5rem; text-align:center; background:#451a03;">
                INTAKE SIGNAL → BUDGET EVALUATION → SERVICE TAGGING → DISPATCH SLA (&lt; 24 HRS)
            </div>
        </div>
    </section>

    <!-- 05 FOLLOW-UP ENGINE -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">ENGAGEMENT</span>
                <h2>STAGED FOLLOW-UP &amp; SLA ENGAGEMENT</h2>
            </div>
            <div class="grid grid-2" style="gap:1.5rem;">
                <div class="machined-card">
                    <strong style="color:#d97706; font-family:var(--font-mono); font-size:0.8rem;">AUTOMATED ACKNOWLEDGMENT</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Instant Project Receipt</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Instant email and WhatsApp confirmation containing a unique project reference ID and scope summary.</p>
                </div>
                <div class="machined-card">
                    <strong style="color:#d97706; font-family:var(--font-mono); font-size:0.8rem;">HUMAN TRIAGE HANDOFF</strong>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Engineering Lead Review</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">Senior engineer reviews technical scope notes and returns a detailed milestone proposal within 24 hours.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 06 CRM DATA FLOW -->
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">CRM DATA STATE</span>
                <h2>ANIMATED CRM DATA STATE MOVEMENT</h2>
            </div>
            <div class="machined-card" style="font-family:var(--font-mono); font-size:0.88rem; line-height:1.6;">
                NEW INTAKE → CONTACTED → QUALIFIED → PROPOSAL SENT → WON / NURTURE
            </div>
        </div>
    </section>

    <!-- 07 AUTOMATION SCENARIOS -->
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">VERIFIED PROOF</span>
                <h2>AUTOMATION SCENARIOS</h2>
            </div>
            <div class="grid grid-3" style="gap:1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-amber" style="margin-bottom:0.5rem;">AUTOMATION #01</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Multi-Channel Lead Triage</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Connected website intake console to WhatsApp API and CRM webhooks, accelerating lead triage acknowledgment from 14 hours to &lt;5 minutes.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-amber" style="margin-bottom:0.5rem;">AUTOMATION #02</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">Automated Lead Scoring Engine</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Built lead scoring logic based on budget scale, project timeline, and domain fit, instantly flagging enterprise opportunities.
                    </p>
                </div>
                <div class="machined-card">
                    <span class="machined-badge machined-badge-amber" style="margin-bottom:0.5rem;">AUTOMATION #03</span>
                    <h3 style="font-size:1.2rem; font-weight:800; color:#050f33; margin-block:0.4rem;">WhatsApp Triage Bot</h3>
                    <p style="font-size:0.9rem; color:#475569; line-height:1.6; margin:0;">
                        Deployed WhatsApp interactive menu bot to collect preliminary project requirements before handing off to discovery engineers.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 08 AUTOMATE THE PIPELINE INTAKE -->
    <section class="section band-soft" id="intake">
        <div class="container">
            <div class="sec-head sec-head-center">
                <span class="machined-badge machined-badge-amber" style="margin-bottom: 0.5rem;">AUTOMATION INTAKE CONSOLE</span>
                <h2>AUTOMATE THE PIPELINE WITH RAFLY</h2>
            </div>
            <?php $formId = 'automationLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

<?php endif; ?>

<?php if (!empty($data['landing_links'])): ?>
    <section class="section" style="padding-block: 4rem; background: #f8fafc;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 2rem;">
                <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.5rem;">SPECIALIZED WORKFLOWS</span>
                <h2 style="font-size: 1.8rem; font-weight: 800; color: #050f33;">DEDICATED SERVICE CONSOLES</h2>
            </div>
            <div class="grid grid-<?= count($data['landing_links']) > 1 ? '2' : '1' ?>" style="gap: 1.5rem; max-width: 900px; margin-inline: auto;">
                <?php foreach ($data['landing_links'] as $ll): ?>
                <a href="<?= e($ll['url']) ?>" class="machined-card" style="display: block; text-decoration: none; padding: 1.75rem;">
                    <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;"><?= e($ll['badge']) ?></span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.5rem;"><?= e($ll['title']) ?></h3>
                    <p style="font-size: 0.95rem; color: #475569; line-height: 1.55; margin: 0;"><?= e($ll['desc']) ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/partials/tail.php'; ?>
