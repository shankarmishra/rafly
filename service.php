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
    ['name' => 'Home',         'url' => '/'],
    ['name' => 'Services',     'url' => '/#services'],
    ['name' => $data['title'], 'url' => '/services/' . ($service === 'ecommerce-support' ? 'ecommerce' : $service)],
];

$relatedArticles    = related_articles_for_service($service, 3);
$relatedCaseStudies = related_case_studies_for_service($data['title'], 2);

$signals = $data['signals'] ?? [];
if (empty($signals) && !empty($data['diagnostics']) && is_array($data['diagnostics'])) {
    $signals = [];
    foreach ($data['diagnostics'] as $d) {
        if (is_array($d)) {
            $signals[] = $d['title'] . ': ' . $d['desc'];
        } else {
            $signals[] = (string)$d;
        }
    }
}

$deliverables = $data['deliverables'] ?? [];
if (empty($deliverables) && !empty($data['bento']) && is_array($data['bento'])) {
    $deliverables = [];
    if (isset($data['bento']['main']) && is_array($data['bento']['main'])) {
        $deliverables[] = [
            'icon'  => 'layers',
            'title' => $data['bento']['main']['title'],
            'desc'  => $data['bento']['main']['desc']
        ];
    }
    if (isset($data['bento']['medium']) && is_array($data['bento']['medium'])) {
        foreach ($data['bento']['medium'] as $m) {
            if (is_array($m)) {
                $deliverables[] = [
                    'icon'  => 'cpu',
                    'title' => $m['title'],
                    'desc'  => $m['desc']
                ];
            }
        }
    }
    if (isset($data['bento']['compact']) && is_array($data['bento']['compact'])) {
        foreach ($data['bento']['compact'] as $c) {
            if (is_array($c)) {
                $deliverables[] = [
                    'icon'  => 'check-circle',
                    'title' => $c['title'],
                    'desc'  => $c['desc']
                ];
            }
        }
    }
}

$points = $data['points'] ?? [
    'Direct communication with lead engineers',
    'Written delivery milestones and timeline commitments',
    'Zero hidden fees, transparent pricing structure',
    'Full source code and IP ownership transfer'
];

$bannerImages = [
    'web'       => '/assets/web_dev_hero_banner.webp',
    'security'  => '/assets/security_hero_banner.webp',
    'marketing' => '/assets/marketing_hero_banner.webp',
    'content'   => '/assets/content_hero_banner.webp',
    'ecom'      => '/assets/ecom_hero_banner.webp',
];

$telemetryChipsMap = [
    'web'       => ['⚡ 38ms LCP · 100/100 Vitals', 'PHP 8.3 / ESNext', 'Zero-Bloat Stack'],
    'security'  => ['🛡️ TLS 1.3 Shield · Active', 'Argon2id Auth Guard', '0 Threat Vectors'],
    'marketing' => ['📈 ROAS +312% · GA4 Server-Side', 'Meta CAPI Synced', 'Attributed Revenue'],
    'content'   => ['🎬 4K HDR · 120fps Timeline', 'ProRes 4444 XQ', 'Color Graded LUTs'],
    'ecom'      => ['🛒 Stripe Verified · Sub-10ms', '100% Inventory Synced', 'Global CDN Hub'],
];

$currentBanner = $bannerImages[$data['key']] ?? '/assets/web_dev_hero_banner.webp';
$currentChips  = $telemetryChipsMap[$data['key']] ?? ['⚡ 100/100 Vitals', 'Sub-second Load'];

$page = [
    'id'        => 'services',
    'title'     => $data['title'] . ' | RAFly Digital Growth',
    'desc'      => $data['intro'],
    'bodyClass' => 'page-service svc-' . $data['key'],
    'styles'    => ['home', 'service'],
    'module'    => 'home',
    'canonical' => 'services/' . ($service === 'ecommerce-support' ? 'ecommerce' : $service),

    'schema'    => [
        schema_service($data['title'], $data['intro'], $data['highlights'], schema_id('service-' . $service)),
        schema_faq($data['faqs']),
        schema_breadcrumbs($crumbs),
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <?php /* ============================== 1. HERO ============================= */ ?>
    <section class="section hero sig-hero svc-clean-hero" style="min-height: clamp(540px, 75vh, 680px); padding-block: clamp(2.5rem, 4vh, 4rem); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
        
        <?php /* ── STATIC HOMEPAGE BACKGROUND TEXTURE SYSTEM (NO DISTRACTING ANIMATION) ── */ ?>
        <div class="sig-env" aria-hidden="true" style="opacity: 0.75;">
            <div class="sig-env__grain"></div>
            <div class="sig-env__grid"></div>
            <div class="sig-env__dots"></div>
            <div class="sig-env__scanbeam" style="opacity: 0.15;"></div>
            <svg class="sig-env__blueprint" viewBox="0 0 1440 900" fill="none" preserveAspectRatio="xMidYMid slice" style="opacity: 0.4;">
                <path class="sig-bp-line line-a" d="M -100,220 Q 380,120 780,440 T 1540,620" stroke="url(#sigBpGrad1)" stroke-width="1.5" />
                <path class="sig-bp-line line-b" d="M -100,640 Q 420,780 780,440 T 1540,180" stroke="url(#sigBpGrad2)" stroke-width="1.5" />
                <defs>
                    <linearGradient id="sigBpGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.35" />
                        <stop offset="50%" stop-color="#0891b2" stop-opacity="0.20" />
                        <stop offset="100%" stop-color="#0a63ff" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="sigBpGrad2" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#0230c6" stop-opacity="0.30" />
                        <stop offset="50%" stop-color="#6134c9" stop-opacity="0.15" />
                        <stop offset="100%" stop-color="#0891b2" stop-opacity="0" />
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <div class="container hero-grid" style="position: relative; z-index: 2; width: 100%;">
            <div class="hero-content svc-hero">
                <p class="eyebrow" data-r="fade">
                    <span class="pulse-dot-cyan"></span>
                    UNIFIED ENGINE // <?= e($data['badge']) ?> v2.4
                </p>

                <h1 class="svc-title" data-r="rise">
                    <?= e($data['title']) ?> <span class="grad-word rafly-underline">
                        <?= $data['key'] === 'web' ? 'Engineering' : ($data['key'] === 'security' ? 'Perimeter' : ($data['key'] === 'marketing' ? 'Intelligence' : ($data['key'] === 'content' ? 'Studio' : 'Infrastructure'))) ?>
                    </span>
                </h1>

                <p class="svc-tagline" data-r="rise" data-r-delay="1">
                    <?= e($data['tagline']) ?>
                </p>

                <p class="svc-intro" data-r="rise" data-r-delay="2">
                    <?= e($data['intro']) ?>
                </p>

                <div class="chips svc-pills" data-r="rise" data-r-delay="3">
                    <?php foreach ($data['highlights'] as $highlight): ?>
                        <span class="chip chip-sm">
                            <span class="chip-dot"></span>
                            <?= e($highlight) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <div class="hero-actions svc-actions" data-r="rise" data-r-delay="4">
                    <a class="btn btn-primary btn-lg" href="/contact">
                        <span>Book a free consultation</span>
                        <?= icon('arrow-up-right') ?>
                    </a>
                    <a class="btn btn-secondary btn-lg" href="#diagnostics">
                        <span>System diagnostics</span>
                        <?= icon('arrow-down') ?>
                    </a>
                </div>

                <p class="svc-note" data-r="fade" data-r-delay="5">
                    // RAFly UNIFIED ENGINE · 28.5355° N, 77.3910° E
                </p>
            </div>

            <div class="hero-visual" data-r="scale" data-service-host data-service-key="<?= e($data['key']) ?>">
                
                <!-- VISUAL MODE TAB SWITCHER -->
                <div class="svc-visual-mode-bar">
                    <button type="button" class="svc-visual-tab active" data-tab="matrix">
                        <span>🌐 Architecture Matrix</span>
                    </button>
                    <button type="button" class="svc-visual-tab" data-tab="ide">
                        <span>💻 Live IDE Studio</span>
                    </button>
                    <button type="button" class="svc-visual-tab" data-tab="vitals">
                        <span>📊 Vitals Telemetry</span>
                    </button>
                </div>

                <!-- VIEW 1: RAFly ARCHITECTURE MATRIX -->
                <div class="svc-visual-pane active" id="pane-matrix">
                    <div class="hero-product-card svc-arch-matrix" style="position: relative; overflow: hidden; background: linear-gradient(145deg, #050f33 0%, #0a1746 100%); border: 1px solid rgba(10, 99, 255, 0.3); border-radius: var(--r-2xl); padding: 1.75rem; box-shadow: 0 20px 50px rgba(5, 15, 51, 0.3); color: #fff;">
                        <!-- SVG Grid & System Nodes -->
                        <div class="matrix-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 0.875rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="pulse-dot-cyan"></span>
                                <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 700; color: #38bdf8; letter-spacing: 0.08em; text-transform: uppercase;">RAFly SYSTEM ARCHITECTURE // <?= e($data['key']) ?></span>
                            </div>
                            <span style="font-family: var(--font-mono, monospace); font-size: 0.7rem; color: #10b981; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 3px 10px; border-radius: 999px; font-weight: 600;">ACTIVE PIPELINE</span>
                        </div>

                        <div class="matrix-viewport" style="position: relative; min-height: 240px; background: rgba(5, 15, 51, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--r-lg); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                            <svg class="matrix-lines-svg" viewBox="0 0 500 160" fill="none" style="position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; opacity: 0.35;">
                                <path d="M 40,80 L 160,80 C 200,80 220,40 260,40 L 360,40 C 400,40 420,80 460,80" stroke="#0a63ff" stroke-width="2" stroke-dasharray="4 4" />
                                <path d="M 40,80 L 160,80 C 200,80 220,120 260,120 L 360,120 C 400,120 420,80 460,80" stroke="#0891b2" stroke-width="1.5" />
                                <circle cx="40" cy="80" r="4" fill="#0a63ff" />
                                <circle cx="260" cy="40" r="4" fill="#38bdf8" />
                                <circle cx="260" cy="120" r="4" fill="#10b981" />
                                <circle cx="460" cy="80" r="5" fill="#0a63ff" />
                            </svg>

                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; position: relative; z-index: 2;">
                                <div style="background: rgba(10, 23, 70, 0.8); border: 1px solid rgba(10, 99, 255, 0.25); border-radius: var(--r); padding: 1rem; backdrop-filter: blur(8px);">
                                    <span style="font-family: var(--font-mono, monospace); font-size: 0.68rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">STAGE 01</span>
                                    <strong style="font-size: 0.95rem; color: #fff; display: block; margin-bottom: 0.25rem;">Core Surface</strong>
                                    <span style="font-size: 0.78rem; color: #cbd5e1; line-height: 1.4; display: block;">Optimized asset delivery &amp; edge caching</span>
                                </div>
                                <div style="background: rgba(10, 23, 70, 0.8); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: var(--r); padding: 1rem; backdrop-filter: blur(8px);">
                                    <span style="font-family: var(--font-mono, monospace); font-size: 0.68rem; color: #38bdf8; display: block; margin-bottom: 0.25rem;">STAGE 02</span>
                                    <strong style="font-size: 0.95rem; color: #fff; display: block; margin-bottom: 0.25rem;">Security &amp; Logic</strong>
                                    <span style="font-size: 0.78rem; color: #cbd5e1; line-height: 1.4; display: block;">Session guard &amp; zero-trust data pipeline</span>
                                </div>
                                <div style="background: rgba(10, 23, 70, 0.8); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: var(--r); padding: 1rem; backdrop-filter: blur(8px);">
                                    <span style="font-family: var(--font-mono, monospace); font-size: 0.68rem; color: #10b981; display: block; margin-bottom: 0.25rem;">STAGE 03</span>
                                    <strong style="font-size: 0.95rem; color: #fff; display: block; margin-bottom: 0.25rem;">Conversion SLA</strong>
                                    <span style="font-size: 0.78rem; color: #cbd5e1; line-height: 1.4; display: block;">Attributed lead routing &amp; instant response</span>
                                </div>
                            </div>
                        </div>

                        <div class="matrix-chips-footer" style="display: flex; flex-wrap: wrap; gap: 0.6rem; margin-top: 1.25rem;">
                            <?php foreach ($currentChips as $chip): ?>
                                <span class="banner-telemetry-chip" style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 600; color: #e2e8f0; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); padding: 5px 12px; border-radius: 8px; backdrop-filter: blur(6px);"><?= e($chip) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- VIEW 2: LIVE IDE & CODE STUDIO WITH INTERACTIVE FILE TABS -->
                <div class="svc-visual-pane" id="pane-ide" style="display: none;">
                    <div class="hero-product-card svc-ide-studio">
                        <div class="studio-header">
                            <div class="window-dots"><span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span></div>
                            <div class="window-tabs">
                                <button type="button" class="ide-tab active" data-file="app">⚡ app.ts</button>
                                <button type="button" class="ide-tab" data-file="api">🐘 api.php</button>
                                <button type="button" class="ide-tab" data-file="db">🗄️ schema.sql</button>
                            </div>
                            <div class="window-badge">PHP 8.3 / ESNext</div>
                        </div>
                        <div class="studio-body">
                            <div class="code-file-content" id="file-app">
                                <div class="code-line"><span class="c-purple">import</span> { <span class="c-blue">createDecoupledApp</span> } <span class="c-purple">from</span> <span class="c-green">'@rafly/core'</span>;</div>
                                <div class="code-line"><span class="c-purple">const</span> app = <span class="c-blue">createDecoupledApp</span>({ <span class="c-orange">target</span>: <span class="c-green">'#root'</span> });</div>
                                <div class="code-line"><span class="c-comment">// Hydrating server-rendered edge cache...</span></div>
                                <div class="code-line"><span class="c-purple">await</span> app.<span class="c-blue">mount</span>(); <span class="c-cyan">// LCP: 38ms</span></div>
                            </div>
                            <div class="code-file-content" id="file-api" style="display: none;">
                                <div class="code-line"><span class="c-purple">&lt;?php</span></div>
                                <div class="code-line"><span class="c-purple">declare</span>(strict_types=1);</div>
                                <div class="code-line"><span class="c-purple">class</span> <span class="c-blue">ApiService</span> {</div>
                                <div class="code-line">&nbsp;&nbsp;<span class="c-purple">public function</span> <span class="c-blue">handle</span>(): <span class="c-green">Response</span> { <span class="c-cyan">/* 200 OK */</span> }</div>
                                <div class="code-line">}</div>
                            </div>
                            <div class="code-file-content" id="file-db" style="display: none;">
                                <div class="code-line"><span class="c-purple">CREATE TABLE</span> <span class="c-blue">sessions</span> (</div>
                                <div class="code-line">&nbsp;&nbsp;<span class="c-orange">id</span> <span class="c-purple">BIGINT PRIMARY KEY</span>,</div>
                                <div class="code-line">&nbsp;&nbsp;<span class="c-orange">token</span> <span class="c-purple">VARCHAR(255) NOT NULL</span></div>
                                <div class="code-line">); <span class="c-comment">-- B-Tree Indexed</span></div>
                            </div>
                            <div class="studio-footer">
                                <span class="vitals-chip"><span class="chip-pulse"></span> ⚡ 38ms LCP · 100/100 Vitals</span>
                                <span class="vitals-chip"><span class="chip-pulse green"></span> REST API: 200 OK</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 3: REAL-TIME VITALS TELEMETRY GAUGES -->
                <div class="svc-visual-pane" id="pane-vitals" style="display: none;">
                    <div class="hero-product-card vitals-telemetry-card" style="padding: 1.5rem; background: rgba(15, 23, 42, 0.92); color: #fff; border-radius: var(--r-2xl); border: 1px solid rgba(56, 189, 248, 0.3);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 0.8rem;">
                            <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 700; color: #38bdf8;">TELEMETRY / PERFORMANCE MATRIX</span>
                            <span style="font-family: var(--font-mono, monospace); font-size: 0.7rem; color: #10b981; background: rgba(16, 185, 129, 0.12); padding: 3px 10px; border-radius: 6px;">100/100 LIGHTHOUSE</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; padding: 1rem; text-align: center;">
                                <span style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; font-family: var(--font-mono, monospace);">38ms</span>
                                <span style="display: block; font-size: 0.75rem; color: #94a3b8; margin-top: 4px; font-family: var(--font-mono, monospace);">LCP (Largest Contentful Paint)</span>
                            </div>
                            <div style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 1rem; text-align: center;">
                                <span style="font-size: 1.8rem; font-weight: 800; color: #10b981; font-family: var(--font-mono, monospace);">12ms</span>
                                <span style="display: block; font-size: 0.75rem; color: #94a3b8; margin-top: 4px; font-family: var(--font-mono, monospace);">FID (First Input Delay)</span>
                            </div>
                            <div style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(168, 85, 247, 0.2); border-radius: 12px; padding: 1rem; text-align: center;">
                                <span style="font-size: 1.8rem; font-weight: 800; color: #c084fc; font-family: var(--font-mono, monospace);">0.00</span>
                                <span style="display: block; font-size: 0.75rem; color: #94a3b8; margin-top: 4px; font-family: var(--font-mono, monospace);">CLS (Cumulative Layout Shift)</span>
                            </div>
                            <div style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; padding: 1rem; text-align: center;">
                                <span style="font-size: 1.8rem; font-weight: 800; color: #fbbf24; font-family: var(--font-mono, monospace);">99.8%</span>
                                <span style="display: block; font-size: 0.75rem; color: #94a3b8; margin-top: 4px; font-family: var(--font-mono, monospace);">Edge CDN Hit Ratio</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ========================= 2. SOUNDS FAMILIAR ======================= */ ?>
    <section class="section band-soft" id="diagnostics">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">SYSTEM DIAGNOSTICS // SIGNALS</p>
                    <h2>The situations <span class="soft">people call us about</span></h2>
                </div>
                <p class="lead">If more than one of these diagnostic traces lands, this is the exact service page you need.</p>
            </div>

            <div class="grid grid-4" data-r="group">
                <?php if (!empty($data['diagnostics'])): ?>
                    <?php foreach ($data['diagnostics'] as $diag): ?>
                        <div class="signal-card">
                            <span class="step-num" style="font-family: var(--font-mono, monospace); font-size: 0.72rem; font-weight: 700; color: #0a63ff; background: rgba(10, 99, 255, 0.08); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 0.8rem;"><?= e($diag['code'] ?? '+01') ?></span>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;"><?= e($diag['title']) ?></h3>
                            <p style="font-size: 0.9rem; color: #475569; margin: 0; line-height: 1.6;"><?= e($diag['desc']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php /* ========================= 1b. SERVICE AT A GLANCE ========================= */ ?>
    <section class="section" style="padding-block: 1.5rem 1rem;">
        <div class="container">
            <div class="card" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #cbd5e1; border-radius: var(--r-xl, 16px); padding: 1.5rem 2rem; box-shadow: 0 4px 16px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                    <span class="pulse-dot-cyan"></span>
                    <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 700; color: #0a63ff; letter-spacing: 0.08em; text-transform: uppercase;">AT A GLANCE // <?= e($data['title']) ?></span>
                </div>
                <p style="font-size: 0.95rem; color: #334155; line-height: 1.6; margin: 0;">
                    <?= e($data['intro']) ?> Executed directly by RAFly's engineering team with written milestone commitments, transparent pricing, and full intellectual property transfer upon project completion.
                </p>
            </div>
        </div>
    </section>

    <?php /* ========================= 3. INTERACTIVE SYSTEM MAP ======================= */ ?>
    <?php if (!empty($data['system_map'])): ?>
    <section class="section section-blueprint" id="system-map">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow on-dark"><?= e($data['system_map']['eyebrow']) ?></p>
                <h2><?= e($data['system_map']['title']) ?></h2>
                <p class="lead muted" style="color: #94a3b8;"><?= e($data['system_map']['desc']) ?></p>
            </div>

            <div class="system-diagram-container">
                <svg class="system-pipeline-svg" viewBox="0 0 1000 60" fill="none" preserveAspectRatio="none">
                    <path d="M 60,30 L 940,30" stroke="rgba(56, 189, 248, 0.2)" stroke-width="3" stroke-dasharray="6 6" />
                    <path class="animated-packet-path" d="M 60,30 L 940,30" stroke="url(#sysPipeGrad)" stroke-width="4" stroke-linecap="round" />
                    <defs>
                        <linearGradient id="sysPipeGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#0a63ff" />
                            <stop offset="50%" stop-color="#38bdf8" />
                            <stop offset="100%" stop-color="#10b981" />
                        </linearGradient>
                    </defs>
                </svg>

                <div class="system-diagram-grid">
                    <?php foreach ($data['system_map']['nodes'] as $idx => $node): ?>
                        <div class="system-node-card <?= $idx === 0 ? 'is-active' : '' ?>" data-node-id="<?= e($node['id']) ?>">
                            <div class="node-header">
                                <span class="node-icon"><?= icon($node['icon']) ?></span>
                                <span class="node-label"><?= e($node['label']) ?></span>
                            </div>
                            <h3 class="node-title"><?= e($node['name']) ?></h3>
                            <p class="node-role"><?= e($node['role']) ?></p>
                            <div class="node-tech">
                                <span class="chip chip-sm"><?= e($node['tech']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ========================= 4. TARGET FIT ======================= */ ?>
    <?php if (!empty($data['who_it_is_for'])): ?>
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">TARGET FIT</p>
                <h2>Who this service <span class="soft">is engineered for</span></h2>
                <p class="lead">Built specifically for organizations where digital performance directly drives growth.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($data['who_it_is_for'] as $target): ?>
                    <div class="card card-hover fit-card">
                        <div class="card-body">
                            <span class="badge-soft-blue"><?= e($target['fit']) ?></span>
                            <h3 class="card-title" style="margin-top: 1rem; color: #06122f; font-weight: 700;"><?= e($target['title']) ?></h3>
                            <p class="card-text" style="color: #475569; margin-top: 0.5rem; line-height: 1.6;"><?= e($target['desc']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ========================= 5. WHAT'S INCLUDED (BENTO) ======================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">WHAT'S INCLUDED</p>
                <h2>The scope, <span class="soft">written down</span></h2>
                <p class="lead">Every engagement is scoped in writing before it starts. This is what that scope usually covers.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php if (isset($data['bento']['main'])): ?>
                    <article class="card card-hover bento-card-main" style="grid-column: span 3 / span 3; background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%); border: 1px solid rgba(10, 99, 255, 0.2); box-shadow: 0 12px 32px rgba(10, 99, 255, 0.06);">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <span class="icon-box" style="background: rgba(10, 99, 255, 0.1); color: #0a63ff; border-radius: 10px; padding: 10px; display: grid; place-items: center;"><?= icon('layers') ?></span>
                                <span class="badge badge-soft-blue"><?= e($data['bento']['main']['handle'] ?? 'CORE FEATURE') ?></span>
                            </div>
                            <h3 class="card-title" style="font-size: 1.4rem; font-weight: 800; color: #06122f;"><?= e($data['bento']['main']['title']) ?></h3>
                            <p class="card-text" style="font-size: 1rem; color: #475569; line-height: 1.6; margin-top: 0.5rem;"><?= e($data['bento']['main']['desc']) ?></p>
                            <?php if (!empty($data['bento']['main']['specs'])): ?>
                                <ul style="margin-top: 1.2rem; display: flex; flex-wrap: wrap; gap: 0.8rem; list-style: none; padding: 0;">
                                    <?php foreach ($data['bento']['main']['specs'] as $spec): ?>
                                        <li style="font-family: var(--font-mono, monospace); font-size: 0.78rem; font-weight: 700; color: #0a63ff; background: rgba(10, 99, 255, 0.06); padding: 5px 12px; border-radius: 6px; border: 1px solid rgba(10, 99, 255, 0.15);">✓ <?= e($spec) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if (isset($data['bento']['medium']) && is_array($data['bento']['medium'])): ?>
                    <?php foreach ($data['bento']['medium'] as $m): ?>
                        <article class="card card-hover">
                            <div class="card-body">
                                <span class="icon-box"><?= icon('cpu') ?></span>
                                <h3 class="card-title" style="font-weight: 700; margin-top: 0.8rem;"><?= e($m['title']) ?></h3>
                                <p class="card-text" style="color: #475569; line-height: 1.6;"><?= e($m['desc']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($data['bento']['compact']) && is_array($data['bento']['compact'])): ?>
                    <?php foreach ($data['bento']['compact'] as $c): ?>
                        <article class="card card-hover">
                            <div class="card-body">
                                <span class="icon-box"><?= icon('check-circle') ?></span>
                                <h3 class="card-title" style="font-weight: 700; margin-top: 0.8rem;"><?= e($c['title']) ?></h3>
                                <p class="card-text" style="color: #475569; line-height: 1.6;"><?= e($c['desc']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php /* ========================= 6. DELIVERABLES & ARTIFACTS ======================= */ ?>
    <?php if (!empty($data['artifacts'])): ?>
    <section class="section">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">DELIVERABLES &amp; HANDOFF</p>
                    <h2>Concrete outputs <span class="soft">you receive at launch</span></h2>
                </div>
                <p class="lead">Every engagement leaves behind documented code, specs, and verifiable audit reports.</p>
            </div>

            <div class="artifact-stack">
                <?php foreach ($data['artifacts'] as $art): ?>
                    <div class="artifact-sheet">
                        <div class="artifact-header">
                            <span class="artifact-type">// <?= e($art['type']) ?></span>
                            <span class="artifact-tag"><?= e($art['tag']) ?></span>
                        </div>
                        <h3 class="artifact-title"><?= e($art['title']) ?></h3>
                        <p class="artifact-desc"><?= e($art['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ======================= 6b. SPECIALIZED SOLUTIONS ===================== */ ?>
    <?php if (!empty($data['landing_links'])): ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">SPECIALIZED WORKFLOWS</p>
                <h2>Dedicated <span class="soft">Action Solutions</span></h2>
                <p class="lead">Accelerated action plans and targeted tools for specific operational requirements.</p>
            </div>
            <div class="grid grid-2" data-r="group">
                <?php foreach ($data['landing_links'] as $ll): ?>
                    <div class="card card-hover" style="background: #ffffff; border: 1px solid rgba(10, 99, 255, 0.2); padding: 1.8rem; border-radius: var(--r-xl, 16px); position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span class="badge badge-soft-blue" style="font-weight: 700; font-size: 0.75rem;"><?= e($ll['badge']) ?></span>
                            <span style="color: #0a63ff; font-weight: 700;"><?= icon('arrow-right') ?></span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #06122f; margin-bottom: 0.5rem;"><?= e($ll['title']) ?></h3>
                        <p style="font-size: 0.95rem; color: #475569; line-height: 1.6; margin-bottom: 1.2rem;"><?= e($ll['desc']) ?></p>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e($ll['url']) ?>">Access Solution <?= icon('arrow-right') ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* =========================== 7. WHAT CHANGES =================------- */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="split split-wide-l split-top">
                <div data-fx="in-left" style="--travel: 12%;">
                    <p class="eyebrow">WHAT CHANGES</p>
                    <h2>What you should <span class="soft">notice afterwards</span></h2>
                    <p class="lead">Not a promise about numbers &mdash; we do not make those. These are the practical differences the work is meant to produce.</p>
                    <ul class="list-check" style="margin-top:2rem">
                        <?php foreach ($data['outcomes'] as $outcome): ?>
                            <li style="display: flex; gap: 0.8rem; margin-bottom: 0.8rem; font-size: 1rem; color: #1e293b; line-height: 1.5;"><?= icon('check') ?><span><?= e($outcome) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="panel panel-line" data-fx="in-right" style="--travel: 16%; --turn: 3deg;">
                    <h3>How we approach it</h3>
                    <p class="muted">Every engagement is shaped around clarity, delivery reliability, and being straight with you about trade-offs.</p>
                    <ul class="list-check" style="margin-top:1.5rem">
                        <?php foreach ($points as $point): ?>
                            <li style="display: flex; gap: 0.8rem; margin-bottom: 0.8rem; font-size: 0.95rem; color: #334155;"><?= icon('check') ?><span><?= e($point) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php /* ===================== 8. HOW THE ENGAGEMENT RUNS ==================== */ ?>
    <section class="section band-ink band-round-t">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow on-dark">How it runs</p>
                <h2>Four stages, <span class="soft">no mystery timelines</span></h2>
                <p class="lead">The same delivery process behind every Rafly package, applied to this service.</p>
            </div>

            <div class="steps-row" data-r="group">
                <?php foreach ($data['process'] as $i => $step): ?>
                    <div class="step-item">
                        <span class="step-num">+<?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="step-title"><?= e($step['title']) ?></span>
                        <span class="step-time"><?= e($step['time']) ?></span>
                        <p class="step-text"><?= e($step['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ===================== 8b. SERVICE LEVEL COMMITMENT (SLA) ==================== */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="card" style="padding: 2.5rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: var(--r-xl, 16px); box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                <div class="sec-head-split" style="margin-bottom: 1.5rem;">
                    <div>
                        <span class="badge badge-soft-blue" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; display: inline-block;">SUPPORT &amp; RESPONSE SLA</span>
                        <h2 style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0;">Standardized Service Level Commitment</h2>
                    </div>
                    <div style="max-width: 440px;">
                        <p style="font-size: 0.95rem; color: #475569; line-height: 1.6; margin: 0;">
                            <strong>Core SLA Rule (Response ≠ Resolution):</strong> Initial Response &amp; Triage SLA governs how quickly our team acknowledges and begins technical diagnosis. Target resolution times are operational benchmarks dependent on technical issue complexity.
                        </p>
                    </div>
                </div>

                <div class="grid grid-4" style="gap: 1.2rem; margin-top: 1.5rem;">
                    <div style="padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.4rem;">Standard Support</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 0.3rem;">Response &lt; 24 business hrs</div>
                        <p style="font-size: 0.82rem; color: #64748b; margin: 0;">Triage &lt; 12 business hrs | Target resolution 48 hrs</p>
                    </div>

                    <div style="padding: 1.2rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #2563eb; text-transform: uppercase; margin-bottom: 0.4rem;">Growth Retainer</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #1e40af; margin-bottom: 0.3rem;">Response &lt; 8 business hrs</div>
                        <p style="font-size: 0.82rem; color: #3b82f6; margin: 0;">Triage &lt; 4 business hrs | Target resolution 24 hrs</p>
                    </div>

                    <div style="padding: 1.2rem; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 10px;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #7c3aed; text-transform: uppercase; margin-bottom: 0.4rem;">Enterprise SLA</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #5b21b6; margin-bottom: 0.3rem;">Response &lt; 2 business hrs</div>
                        <p style="font-size: 0.82rem; color: #6d28d9; margin: 0;">Triage &lt; 1 business hr | Target resolution 8 hrs</p>
                    </div>

                    <div style="padding: 1.2rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #dc2626; text-transform: uppercase; margin-bottom: 0.4rem;">Emergency Incident (24/7)</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #991b1b; margin-bottom: 0.3rem;">Response &lt; 1 hour (24/7)</div>
                        <p style="font-size: 0.82rem; color: #ef4444; margin: 0;">Hotline Triage &lt; 1 hour | Target resolution 4 hrs</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php /* ========================== 9. TOOLS AND STACK ======================= */ ?>
    <section class="section">
        <div class="container">
            <div class="sec-head sec-head-center">
                <p class="eyebrow">Stack &amp; tooling</p>
                <h2>What we use <span class="soft">to deliver this</span></h2>
                <p class="lead">Industry-standard tools, tuned for speed, safety, and handoff clarity.</p>
            </div>

            <div class="chips chips-center" data-r="fade">
                <?php foreach ($data['tools'] as $tool): ?>
                    <span class="chip" style="font-size: 0.9rem; padding: 0.6rem 1.2rem; gap: 8px;"><?= icon($tool['icon']) ?><?= $tool['label'] ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ========================== 10. HONEST LIMITS ========================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Honest limits</p>
                    <h2>Where we would <span class="soft">point you elsewhere</span></h2>
                </div>
                <p class="lead">We would rather tell you now than three weeks into a project that was never a good fit.</p>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($data['boundaries'] as $limit): ?>
                    <div class="limit-card" style="background: #ffffff; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--r-xl); padding: 1.5rem;">
                        <span class="icon-box icon-box-note" style="color: #ef4444; margin-bottom: 0.8rem;"><?= icon('alert-triangle') ?></span>
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #06122f; margin-bottom: 0.5rem;"><?= e($limit['title']) ?></h3>
                        <p style="font-size: 0.92rem; color: #475569; line-height: 1.6; margin: 0;"><?= e($limit['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ============================== 11. FAQ ============================== */ ?>
    <section class="section">
        <div class="container">
            <div class="faq-card" data-r="rise">
                <div class="faq-aside">
                    <p class="eyebrow">FAQ</p>
                    <h2><?= e($data['title']) ?>, <span class="mark">answered</span></h2>
                    <p class="muted">Something else on your mind? A person replies, usually the same working day.</p>
                    <div class="faq-aside-actions">
                        <a class="btn btn-pill" href="/contact">Ask us directly <?= icon('arrow-right') ?></a>
                    </div>
                </div>
                <div class="faq-list">
                    <div class="accordion" data-accordion="single">
                        <?php foreach ($data['faqs'] as $i => $faq): ?>
                            <div class="accordion-item">
                                <button type="button" class="accordion-trigger" aria-expanded="false" aria-controls="svc-faq-<?= $i ?>" id="svc-faq-t-<?= $i ?>">
                                    <span style="font-weight: 700; color: #06122f; font-size: 1.05rem;"><?= e($faq['q']) ?></span>
                                    <span class="accordion-icon" aria-hidden="true"><?= icon('chevron-down') ?></span>
                                </button>
                                <div class="accordion-panel" id="svc-faq-<?= $i ?>" role="region" aria-labelledby="svc-faq-t-<?= $i ?>">
                                    <div><p style="font-size: 0.95rem; color: #475569; line-height: 1.65;"><?= e($faq['a']) ?></p></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php /* ==================== 12. FURTHER READING ===================== */ ?>
    <?php if ($relatedArticles || $relatedCaseStudies): ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Further reading</p>
                    <h2>More on <span class="soft"><?= e($data['title']) ?></span></h2>
                </div>
            </div>

            <div class="grid grid-3" data-r="group">
                <?php foreach ($relatedArticles as $a): ?>
                    <article class="card card-hover">
                        <div class="card-body card-body-sm">
                            <span class="badge badge-soft">Article</span>
                            <h3 class="card-title"><?= e((string)$a['title']) ?></h3>
                            <p class="card-text"><?= e(str_cut((string)$a['excerpt'], 110)) ?></p>
                            <div class="card-foot">
                                <span class="blog-meta"><?= (int)$a['read_minutes'] ?> min read</span>
                                <?= icon('arrow-up-right') ?>
                            </div>
                        </div>
                        <a class="card-link" href="<?= e(site_path('/blog/' . rawurlencode((string)$a['slug']))) ?>" aria-label="<?= e((string)$a['title']) ?>"></a>
                    </article>
                <?php endforeach; ?>
                <?php foreach ($relatedCaseStudies as $cs): ?>
                    <article class="card card-hover">
                        <div class="card-body card-body-sm">
                            <span class="badge badge-soft">Case study</span>
                            <h3 class="card-title"><?= e((string)$cs['client_name']) ?></h3>
<?php if (trim((string)$cs['metric_value']) !== '' && trim((string)$cs['metric_label']) !== ''): ?>
                            <p class="card-text"><strong><?= e((string)$cs['metric_value']) ?></strong> &mdash; <?= e((string)$cs['metric_label']) ?></p>
<?php endif; ?>
                            <div class="card-foot">
                                <span class="link-arrow">Read the write-up <?= icon('arrow-right') ?></span>
                            </div>
                        </div>
                        <a class="card-link" href="/case-studies#case-study-<?= (int)$cs['index'] ?>" aria-label="<?= e((string)$cs['client_name']) ?> case study"></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ========================== 13. OTHER SERVICES ======================= */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Also from Rafly</p>
                    <h2>The other four</h2>
                </div>
                <p class="lead">Each works on its own. They work better bundled, which is the whole point.</p>
            </div>

            <div class="grid grid-4" data-r="group">
                <?php foreach (services_all() as $slug => $other): ?>
                    <?php if ($slug === $service) { continue; } ?>
                    <article class="card card-hover svc-other">
                        <div class="card-body card-body-sm">
                            <span class="icon-box"><?= icon($other['icon']) ?></span>
                            <h3 class="card-title"><?= e($other['title']) ?></h3>
                            <p class="card-text"><?= e($other['tagline']) ?></p>
                            <div class="card-foot">
                                <span class="link-arrow">Explore <?= icon('arrow-right') ?></span>
                            </div>
                        </div>
                        <a class="card-link" href="<?= e(service_url($slug)) ?>" aria-label="<?= e($other['title']) ?>"></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Next step';
    $ctaTitle   = "Let's scope your " . $data['title'] . ' work.';
    $ctaText    = 'Tell us what is slowing you down. We will come back with a scope, a timeline, and a straight answer about whether we are the right people for it.';
    $ctaButton  = 'Book a free consultation';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Visual Mode Tab Switcher Logic
    const visualTabs = document.querySelectorAll('.svc-visual-tab');
    visualTabs.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            visualTabs.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const targetTab = btn.getAttribute('data-tab');
            
            document.querySelectorAll('.svc-visual-pane').forEach(pane => {
                pane.style.display = 'none';
            });
            const activePane = document.getElementById('pane-' + targetTab);
            if (activePane) {
                activePane.style.display = 'block';
            }
        });
    });

    // 2. IDE File Tab Switcher Logic
    const ideTabs = document.querySelectorAll('.ide-tab');
    ideTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            ideTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const targetFile = tab.getAttribute('data-file');
            
            document.querySelectorAll('.code-file-content').forEach(file => {
                file.style.display = 'none';
            });
            const activeFile = document.getElementById('file-' + targetFile);
            if (activeFile) {
                activeFile.style.display = 'block';
            }
        });
    });

    // 3. System Architecture Node Click Logic
    const nodeCards = document.querySelectorAll('.system-node-card');
    nodeCards.forEach(card => {
        card.addEventListener('click', () => {
            nodeCards.forEach(c => c.classList.remove('is-active'));
            card.classList.add('is-active');
        });
    });
});
</script>

<?php require __DIR__ . '/partials/tail.php'; ?>
