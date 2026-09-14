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

$crumbs = [
    ['name' => 'Home',     'url' => '/'],
    ['name' => 'Services', 'url' => '/#services'],
    ['name' => $data['title'], 'url' => '/services/' . ($service === 'ecommerce-support' ? 'ecommerce' : $service)],
];

$page = [
    'id'        => 'services',
    'title'     => $data['title'] . ' | RAFly Digital Growth Partner',
    'desc'      => $data['intro'],
    'bodyClass' => 'page-service svc-' . $data['key'],
    'styles'    => ['home', 'home-scenes', 'service'],
    'module'    => 'home',
    'canonical' => 'services/' . ($service === 'ecommerce-support' ? 'ecommerce' : $service),
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO CUSTOM SVG: DIGITAL PRODUCT ENGINE -->
            <div class="code-console-window">
                <div class="code-console-bar">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #38bdf8;">DIGITAL PRODUCT ENGINE PIPELINE</span>
                    <span class="telemetry-pill-mono" style="background: rgba(16,185,129,0.15); color: #10b981; border-color: rgba(16,185,129,0.3);">LCP: 38ms</span>
                </div>
                <div style="padding: 1rem; background: #0f172a; display: flex; justify-content: center; align-items: center; width: 100%; height: 160px;">
                    <lottie-player
                        src="/assets/lottie/system-arch.json"
                        background="transparent"
                        speed="1"
                        style="width: 150px; height: 150px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 1.5rem;">
                    <svg viewBox="0 0 420 220" fill="none" style="width: 100%;">
                        <!-- Viewport frame -->
                        <rect x="10" y="10" width="400" height="200" rx="10" stroke="#0a63ff" stroke-width="1.5" stroke-dasharray="6 6" opacity="0.35" />
                        <!-- Browser bar -->
                        <rect x="20" y="20" width="380" height="24" rx="4" fill="#0a1746" stroke="#38bdf8" stroke-width="1" />
                        <circle cx="34" cy="32" r="3" fill="#ef4444" />
                        <circle cx="44" cy="32" r="3" fill="#eab308" />
                        <circle cx="54" cy="32" r="3" fill="#22c55e" />
                        <text x="70" y="35" font-family="monospace" font-size="9" fill="#94a3b8">https://api.rafly.in/v1/engine</text>
                        
                        <!-- Nodes & Flows -->
                        <path d="M 40,110 H 120 C 140,110 140,65 170,65 H 250 C 280,65 280,110 310,110 H 380" stroke="#0a63ff" stroke-width="2" stroke-linecap="round" />
                        <circle cx="40" cy="110" r="6" fill="#38bdf8" />
                        <circle cx="170" cy="65" r="6" fill="#10b981" />
                        <circle cx="250" cy="65" r="6" fill="#10b981" />
                        <circle cx="380" cy="110" r="6" fill="#38bdf8" />

                        <!-- Flow signal packets -->
                        <circle cx="120" cy="110" r="3" fill="#ffffff"><animate attributeName="cx" values="40;120;170;250;310;380" dur="3s" repeatCount="indefinite" /></circle>

                        <!-- Labels -->
                        <text x="40" y="135" font-family="monospace" font-size="9" fill="#94a3b8" text-anchor="middle">CLIENT</text>
                        <text x="120" y="135" font-family="monospace" font-size="9" fill="#94a3b8" text-anchor="middle">EDGE CDN</text>
                        <text x="210" y="52" font-family="monospace" font-size="9" fill="#38bdf8" text-anchor="middle" font-weight="bold">PHP 8.3 REST API</text>
                        <text x="310" y="135" font-family="monospace" font-size="9" fill="#94a3b8" text-anchor="middle">MYSQL VAULT</text>
                        <text x="380" y="135" font-family="monospace" font-size="9" fill="#10b981" text-anchor="middle">200 OK</text>

                        <!-- Code preview overlay -->
                        <rect x="120" y="150" width="180" height="45" rx="6" fill="#050f33" stroke="#10b981" stroke-width="1" />
                        <text x="130" y="167" font-family="monospace" font-size="8" fill="#10b981">HTTP/2 200 OK [38ms]</text>
                        <text x="130" y="182" font-family="monospace" font-size="8" fill="#94a3b8">Cache-Control: public, max-age=86400</text>
                    </svg>
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO CUSTOM SVG: DEFENSE PERIMETER -->
            <div class="code-console-window" style="border-color: rgba(220,38,38,0.4);">
                <div class="code-console-bar" style="background: #1e1b4b;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #fca5a5;">PERIMETER SHIELD ACTIVE GUARD</span>
                    <span class="telemetry-pill-mono" style="background: rgba(16,185,129,0.15); color: #10b981; border-color: rgba(16,185,129,0.3);">0 BREACH VECTORS</span>
                <div style="padding: 1rem; background: linear-gradient(155deg, #0f172a 0%, #1e1b4b 100%); display: flex; justify-content: center; align-items: center; width: 100%; height: 160px;">
                    <lottie-player
                        src="/assets/lottie/security-shield.json"
                        background="transparent"
                        speed="1"
                        style="width: 140px; height: 140px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 1.5rem; background: linear-gradient(155deg, #0f172a 0%, #1e1b4b 100%);">
                    <svg viewBox="0 0 420 200" fill="none" style="width: 100%;">
                        <circle cx="210" cy="100" r="85" stroke="#dc2626" stroke-width="1.5" stroke-dasharray="6 6" opacity="0.4" />
                        <circle cx="210" cy="100" r="55" stroke="#38bdf8" stroke-width="1.5" />
                        <circle cx="210" cy="100" r="25" fill="#dc2626" fill-opacity="0.2" stroke="#dc2626" stroke-width="2" />
                        <text x="210" y="104" font-family="sans-serif" font-weight="900" font-size="10" fill="#fff" text-anchor="middle">VAULT</text>

                        <!-- Threat vectors -->
                        <line x1="30" y1="100" x2="125" y2="100" stroke="#ef4444" stroke-width="2" stroke-dasharray="4 4" />
                        <polygon points="125,96 133,100 125,104" fill="#ef4444" />
                        <text x="60" y="90" font-family="monospace" font-size="8" fill="#fca5a5">BLOCKED ATTACK</text>

                        <!-- Laser scanning beam -->
                        <line x1="210" y1="15" x2="210" y2="45" stroke="#10b981" stroke-width="2" />
                        <text x="210" y="10" font-family="monospace" font-size="8" fill="#10b981" text-anchor="middle">TLS 1.3 SAFE</text>
                    </svg>
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO CUSTOM SVG: SIGNAL NETWORK -->
            <div class="code-console-window" style="border-color: rgba(22,163,74,0.4);">
                <div class="code-console-bar" style="background: #052e16;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #4ade80;">SIGNAL → CONVERSION BUS</span>
                    <span class="telemetry-pill-mono" style="background: rgba(74,222,128,0.15); color: #4ade80; border-color: rgba(74,222,128,0.3);">META CAPI SYNCED</span>
                </div>
                <div style="padding: 1rem; background: linear-gradient(155deg, #052e16 0%, #050f33 100%); display: flex; justify-content: center; align-items: center; width: 100%; height: 160px;">
                    <lottie-player
                        src="/assets/lottie/growth-chart.json"
                        background="transparent"
                        speed="1"
                        style="width: 150px; height: 150px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 1.5rem; background: linear-gradient(155deg, #052e16 0%, #050f33 100%);">
                    <svg viewBox="0 0 420 200" fill="none" style="width: 100%;">
                        <path d="M 30,100 C 100,30 200,170 300,100 T 390,100" stroke="#16a34a" stroke-width="2" stroke-linecap="round" fill="none" />
                        <circle cx="30" cy="100" r="5" fill="#4ade80" />
                        <circle cx="160" cy="120" r="5" fill="#4ade80" />
                        <circle cx="300" cy="100" r="5" fill="#4ade80" />
                        <circle cx="390" cy="100" r="6" fill="#16a34a" />

                        <!-- Animated particle along signal curve -->
                        <circle cx="30" cy="100" r="3" fill="#ffffff"><animate attributeName="cx" values="30;160;300;390" dur="2.5s" repeatCount="indefinite" /></circle>

                        <text x="30" y="125" font-family="monospace" font-size="8" fill="#94a3b8">AUDIENCE</text>
                        <text x="160" y="145" font-family="monospace" font-size="8" fill="#4ade80">INTENT SIGNAL</text>
                        <text x="300" y="125" font-family="monospace" font-size="8" fill="#94a3b8">CAMPAIGN</text>
                        <text x="390" y="125" font-family="monospace" font-size="8" fill="#16a34a">CONVERSION</text>
                    </svg>
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO CUSTOM SVG: NLE VIDEO TIMELINE -->
            <div class="code-console-window" style="border-color: rgba(147,51,234,0.4);">
                <div class="code-console-bar" style="background: #1e1b4b;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #c084fc;">NLE EDITING STUDIO TIMELINE</span>
                    <span class="telemetry-pill-mono" style="background: rgba(192,132,252,0.15); color: #c084fc; border-color: rgba(192,132,252,0.3);">4K HDR 120FPS</span>
                </div>
                <div style="padding: 1rem; background: linear-gradient(155deg, #1e1b4b 0%, #050f33 100%); display: flex; justify-content: center; align-items: center; width: 100%; height: 160px;">
                    <lottie-player
                        src="/assets/lottie/video-reel.json"
                        background="transparent"
                        speed="1"
                        style="width: 150px; height: 150px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 1.5rem; background: linear-gradient(155deg, #1e1b4b 0%, #050f33 100%);">
                    <svg viewBox="0 0 420 200" fill="none" style="width: 100%;">
                        <!-- Aspect frames: 9:16, 16:9, 1:1 -->
                        <rect x="20" y="30" width="45" height="80" rx="4" stroke="#c084fc" stroke-width="1.5" fill="none" />
                        <text x="42" y="125" font-family="monospace" font-size="8" fill="#c084fc" text-anchor="middle">9:16</text>

                        <rect x="80" y="45" width="90" height="50" rx="4" stroke="#38bdf8" stroke-width="1.5" fill="none" />
                        <text x="125" y="110" font-family="monospace" font-size="8" fill="#38bdf8" text-anchor="middle">16:9</text>

                        <rect x="185" y="40" width="60" height="60" rx="4" stroke="#10b981" stroke-width="1.5" fill="none" />
                        <text x="215" y="115" font-family="monospace" font-size="8" fill="#10b981" text-anchor="middle">1:1</text>

                        <!-- Timeline tracks -->
                        <rect x="260" y="30" width="140" height="15" rx="3" fill="#9333ea" fill-opacity="0.4" />
                        <rect x="260" y="50" width="140" height="15" rx="3" fill="#38bdf8" fill-opacity="0.4" />
                        <rect x="260" y="70" width="140" height="15" rx="3" fill="#10b981" fill-opacity="0.4" />

                        <!-- Playhead -->
                        <line x1="330" y1="20" x2="330" y2="100" stroke="#ef4444" stroke-width="2">
                            <animate attributeName="x1" values="260;400;260" dur="4s" repeatCount="indefinite" />
                            <animate attributeName="x2" values="260;400;260" dur="4s" repeatCount="indefinite" />
                        </line>

                        <!-- Audio waveform preview -->
                        <path d="M 20,160 Q 60,140 100,160 T 180,160 T 260,160 T 340,160 T 400,160" stroke="#9333ea" stroke-width="1.5" fill="none" />
                    </svg>
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO CUSTOM SVG: COMMERCE BUS -->
            <div class="code-console-window" style="border-color: rgba(2,132,199,0.4);">
                <div class="code-console-bar" style="background: #0c4a6e;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #38bdf8;">COMMERCE ORDER PIPELINE</span>
                    <span class="telemetry-pill-mono" style="background: rgba(56,189,248,0.15); color: #38bdf8; border-color: rgba(56,189,248,0.3);">STRIPE / RAZORPAY API</span>
                </div>
                <div style="padding: 1rem; background: linear-gradient(155deg, #0c4a6e 0%, #050f33 100%); display: flex; justify-content: center; align-items: center; width: 100%; height: 160px;">
                    <lottie-player
                        src="/assets/lottie/ecommerce.json"
                        background="transparent"
                        speed="1"
                        style="width: 150px; height: 150px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 1.5rem; background: linear-gradient(155deg, #0c4a6e 0%, #050f33 100%);">
                    <svg viewBox="0 0 420 200" fill="none" style="width: 100%;">
                        <path d="M 30,100 H 120 C 140,100 140,50 170,50 H 250 C 280,50 280,100 300,100 H 390" stroke="#0284c7" stroke-width="2" />
                        <circle cx="30" cy="100" r="6" fill="#38bdf8" />
                        <circle cx="170" cy="50" r="6" fill="#10b981" />
                        <circle cx="250" cy="50" r="6" fill="#10b981" />
                        <circle cx="390" cy="100" r="6" fill="#38bdf8" />

                        <!-- Animated order packet -->
                        <circle cx="30" cy="100" r="3" fill="#ffffff"><animate attributeName="cx" values="30;120;170;250;300;390" dur="3s" repeatCount="indefinite" /></circle>

                        <text x="30" y="125" font-family="monospace" font-size="8" fill="#94a3b8">STOREFRONT</text>
                        <text x="120" y="125" font-family="monospace" font-size="8" fill="#94a3b8">CART</text>
                        <text x="210" y="38" font-family="monospace" font-size="8" fill="#38bdf8" text-anchor="middle">PAYMENT GATEWAY BUS</text>
                        <text x="300" y="125" font-family="monospace" font-size="8" fill="#94a3b8">ORDER STATE</text>
                        <text x="390" y="125" font-family="monospace" font-size="8" fill="#10b981">FULFILLED</text>
                    </svg>
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO CUSTOM SVG: ROUTING NETWORK -->
            <div class="code-console-window" style="border-color: rgba(217,119,6,0.4);">
                <div class="code-console-bar" style="background: #451a03;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #fbbf24;">LEAD ROUTING ENGINE DECISION TREE</span>
                    <span class="telemetry-pill-mono" style="background: rgba(251,191,36,0.15); color: #fbbf24; border-color: rgba(251,191,36,0.3);">WHATSAPP API ACTIVE</span>
                </div>
                <div style="padding: 1.5rem; background: linear-gradient(155deg, #451a03 0%, #050f33 100%);">
                    <svg viewBox="0 0 420 200" fill="none" style="width: 100%;">
                        <!-- Multi-channel inputs -->
                        <circle cx="40" cy="50" r="5" fill="#38bdf8" />
                        <circle cx="40" cy="100" r="5" fill="#10b981" />
                        <circle cx="40" cy="150" r="5" fill="#d97706" />

                        <!-- Funnel lines to capture node -->
                        <line x1="45" y1="50" x2="140" y2="100" stroke="#d97706" stroke-width="1.5" />
                        <line x1="45" y1="100" x2="140" y2="100" stroke="#d97706" stroke-width="1.5" />
                        <line x1="45" y1="150" x2="140" y2="100" stroke="#d97706" stroke-width="1.5" />

                        <!-- Animated lead signals -->
                        <circle cx="45" cy="50" r="3" fill="#ffffff"><animate attributeName="cx" values="45;140" dur="2s" repeatCount="indefinite" /><animate attributeName="cy" values="50;100" dur="2s" repeatCount="indefinite" /></circle>

                        <rect x="140" y="80" width="70" height="40" rx="6" fill="#d97706" fill-opacity="0.2" stroke="#d97706" stroke-width="1.5" />
                        <text x="175" y="104" font-family="monospace" font-size="9" fill="#fff" text-anchor="middle" font-weight="bold">QUALIFY</text>

                        <!-- Branching outputs -->
                        <line x1="210" y1="100" x2="310" y2="50" stroke="#10b981" stroke-width="1.5" />
                        <line x1="210" y1="100" x2="310" y2="150" stroke="#ef4444" stroke-width="1.5" />

                        <rect x="310" y="30" width="80" height="40" rx="6" fill="#050f33" stroke="#10b981" stroke-width="1.5" />
                        <text x="350" y="54" font-family="monospace" font-size="8" fill="#10b981" text-anchor="middle">HOT → SALES</text>

                        <rect x="310" y="130" width="80" height="40" rx="6" fill="#050f33" stroke="#ef4444" stroke-width="1.5" />
                        <text x="350" y="154" font-family="monospace" font-size="8" fill="#fca5a5" text-anchor="middle">NURTURE</text>
                    </svg>
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

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
