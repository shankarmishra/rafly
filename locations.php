<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',      'url' => '/'],
    ['name' => 'Locations', 'url' => '/locations'],
];

$page = [
    'id'        => 'locations',
    'title'     => 'Regional Delivery Network | RAFly Digital Growth Partner',
    'desc'      => 'RAFly delivery network across Greater Noida West HQ, Noida, Delhi NCR, Gurgaon Cyber Hub, and remote global client delivery.',
    'bodyClass' => 'page-locations',
    'styles'    => ['home', 'home-scenes', 'locations'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- 01 — HERO: COMMAND CENTER & REGIONAL MESH NETWORK (LIGHT THEME) -->
    <section class="loc-hero-light">
        <div class="container loc-hero-grid">
            <div class="loc-reveal">
                <div class="machined-badge machined-badge-green" style="margin-bottom: 1.25rem;">
                    <span class="glow-dot-active"></span> REGIONAL MESH NETWORK // OPERATIONAL
                </div>
                <h1 class="loc-hero-title">
                    GLOBAL REACH. <br><span>LOCAL PRECISION.</span>
                </h1>
                <p class="loc-hero-desc">
                    Headquartered in Greater Noida West with active engineering nodes across Delhi NCR, Noida, Gurgaon Cyber Hub, and high-speed remote global delivery.
                </p>
                <div style="display: flex; gap: 1.25rem; flex-wrap: wrap; margin-bottom: 2.5rem;">
                    <a class="btn btn-primary btn-lg" href="#intake" style="box-shadow: 0 10px 25px rgba(10, 99, 255, 0.3);">
                        Start Project Brief <?= icon('arrow-up-right') ?>
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="#hubs">
                        Explore Hubs <?= icon('arrow-down') ?>
                    </a>
                </div>
                
                <!-- Telemetry Row -->
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="telemetry-pill-mono" style="background: rgba(10, 99, 255, 0.08); color: #0b52d8; border: 1px solid rgba(10, 99, 255, 0.2);">
                        📍 Greater Noida HQ
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.2);">
                        🏢 Delhi NCR On-Site
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(22, 163, 74, 0.08); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2);">
                        🌐 24/7 Global SOW
                    </span>
                </div>
            </div>

            <!-- HERO CUSTOM SVG: DETAILED HIGH-TECH SPATIAL RADAR MODEL -->
            <div class="loc-radar-console-light loc-reveal">
                <div class="loc-radar-bar-light">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #0a63ff; margin-left: 0.4rem; font-family: var(--font-mono, monospace);">
                            SPATIAL_RADAR // LAT: 28.5355° N | LON: 77.3910° E
                        </span>
                    </div>
                    <span class="machined-badge machined-badge-blue" style="font-size: 0.65rem; padding: 2px 8px;">● 4/4 NODES ACTIVE</span>
                </div>
                <div style="padding: 1.75rem; position: relative;">
                    <svg viewBox="0 0 460 270" fill="none" style="width: 100%; height: auto;">
                        <defs>
                            <radialGradient id="hqGlowLight" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
                            </radialGradient>
                            <linearGradient id="radarSweepGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.45"/>
                                <stop offset="100%" stop-color="#0a63ff" stop-opacity="0"/>
                            </linearGradient>
                        </defs>

                        <!-- Outer Latitude & Longitude Arc Grid -->
                        <circle cx="230" cy="135" r="120" stroke="#e2e8f0" stroke-width="1.2"/>
                        <circle cx="230" cy="135" r="95" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 4"/>
                        <circle cx="230" cy="135" r="65" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 3"/>
                        <line x1="90" y1="135" x2="370" y2="135" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 4"/>
                        <line x1="230" y1="10" x2="230" y2="260" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 4"/>

                        <!-- Animated Sweeping Radar Beam -->
                        <g>
                            <path d="M230,135 L345,135 A115,115 0 0,0 230,20 Z" fill="url(#radarSweepGrad)">
                                <animateTransform attributeName="transform" type="rotate" from="0 230 135" to="360 230 135" dur="8s" repeatCount="indefinite" />
                            </path>
                        </g>

                        <!-- Central HQ Node: Greater Noida West -->
                        <circle cx="230" cy="135" r="48" fill="url(#hqGlowLight)"/>
                        <circle cx="230" cy="135" r="16" fill="#0a63ff" stroke="#0284c7" stroke-width="2.5"/>
                        <circle cx="230" cy="135" r="32" stroke="#0a63ff" stroke-width="1.5" stroke-dasharray="6 6" opacity="0.7">
                            <animateTransform attributeName="transform" type="rotate" from="0 230 135" to="360 230 135" dur="12s" repeatCount="indefinite" />
                        </circle>
                        <text x="230" y="167" font-family="monospace" font-size="9" fill="#050f33" text-anchor="middle" font-weight="800">GREATER NOIDA HQ [PRIMARY]</text>
                        <text x="230" y="179" font-family="monospace" font-size="7.5" fill="#0a63ff" text-anchor="middle">LATENCY: 0.2ms</text>

                        <!-- Hub 1: Noida Tech Corridor -->
                        <line x1="100" y1="65" x2="230" y2="135" stroke="#0a63ff" stroke-width="1.8" stroke-dasharray="4 3" opacity="0.6"/>
                        <circle r="4" fill="#0284c7"><animateMotion path="M230,135 L100,65" dur="2.2s" repeatCount="indefinite" /></circle>
                        <rect x="80" y="50" width="40" height="30" rx="6" fill="#ffffff" stroke="#0284c7" stroke-width="1.8"/>
                        <text x="100" y="68" font-family="monospace" font-size="8.5" fill="#0284c7" text-anchor="middle" font-weight="bold">NOIDA</text>
                        <text x="100" y="40" font-family="monospace" font-size="7.5" fill="#0284c7" text-anchor="middle" font-weight="bold">HUB 01</text>

                        <!-- Hub 2: Delhi Enterprise Zone -->
                        <line x1="360" y1="65" x2="230" y2="135" stroke="#0a63ff" stroke-width="1.8" stroke-dasharray="4 3" opacity="0.6"/>
                        <circle r="4" fill="#0a63ff"><animateMotion path="M230,135 L360,65" dur="1.8s" repeatCount="indefinite" /></circle>
                        <rect x="340" y="50" width="40" height="30" rx="6" fill="#ffffff" stroke="#0a63ff" stroke-width="1.8"/>
                        <text x="360" y="68" font-family="monospace" font-size="8.5" fill="#0a63ff" text-anchor="middle" font-weight="bold">DELHI</text>
                        <text x="360" y="40" font-family="monospace" font-size="7.5" fill="#0a63ff" text-anchor="middle" font-weight="bold">HUB 02</text>

                        <!-- Hub 3: Gurgaon Cyber City -->
                        <line x1="100" y1="205" x2="230" y2="135" stroke="#0a63ff" stroke-width="1.8" stroke-dasharray="4 3" opacity="0.6"/>
                        <circle r="4" fill="#9333ea"><animateMotion path="M230,135 L100,205" dur="2.5s" repeatCount="indefinite" /></circle>
                        <rect x="75" y="190" width="50" height="30" rx="6" fill="#ffffff" stroke="#9333ea" stroke-width="1.8"/>
                        <text x="100" y="208" font-family="monospace" font-size="8.5" fill="#9333ea" text-anchor="middle" font-weight="bold">GURGAON</text>
                        <text x="100" y="232" font-family="monospace" font-size="7.5" fill="#9333ea" text-anchor="middle" font-weight="bold">HUB 03</text>

                        <!-- Hub 4: Remote Global Mesh -->
                        <line x1="360" y1="205" x2="230" y2="135" stroke="#0a63ff" stroke-width="1.8" stroke-dasharray="4 3" opacity="0.6"/>
                        <circle r="4" fill="#16a34a"><animateMotion path="M230,135 L360,205" dur="2s" repeatCount="indefinite" /></circle>
                        <rect x="335" y="190" width="50" height="30" rx="6" fill="#ffffff" stroke="#16a34a" stroke-width="1.8"/>
                        <text x="360" y="208" font-family="monospace" font-size="8.5" fill="#16a34a" text-anchor="middle" font-weight="bold">GLOBAL</text>
                        <text x="360" y="232" font-family="monospace" font-size="7.5" fill="#16a34a" text-anchor="middle" font-weight="bold">HUB 04</text>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 — REGIONAL HUBS DISPLAY WITH INTERACTIVE DETAILS -->
    <section class="section" id="hubs" style="padding-block: 6rem; background: #ffffff;">
        <div class="container">
            <div class="text-center loc-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// ACTIVE HUBS</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    FOUR OPERATIONAL HUBS
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    Click any hub card to inspect live location telemetries, contact routing, and engineering team availability.
                </p>
            </div>

            <div class="loc-hubs-grid">
                <!-- HUB 1 -->
                <div class="loc-hub-card-light loc-reveal" data-hub="1" style="--card-accent: #0a63ff;">
                    <div class="loc-hub-icon-box">
                        <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"></path></svg>
                    </div>
                    <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">HEADQUARTERS</span>
                    <h3 class="loc-hub-title">Greater Noida West HQ</h3>
                    <p class="loc-hub-address">
                        A523, T3, NX-One, Tech Zone IV, Greater Noida West, Uttar Pradesh 201306
                    </p>
                    <div class="loc-hub-tags">
                        <span class="loc-tag">Systems Lab</span>
                        <span class="loc-tag">Security Audit</span>
                        <span class="loc-tag">Media Studio</span>
                    </div>
                    <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <a href="/locations/greater-noida" style="font-family: var(--font-mono, monospace); font-size: 0.82rem; font-weight: 700; color: #0a63ff; text-decoration: none;">View Greater Noida HQ Page &rarr;</a>
                    </div>
                </div>

                <!-- HUB 2 -->
                <div class="loc-hub-card-light loc-reveal" data-hub="2" style="--card-accent: #0284c7;">
                    <div class="loc-hub-icon-box">
                        <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.75rem;">TECH CORRIDOR</span>
                    <h3 class="loc-hub-title">Noida Sector Hub</h3>
                    <p class="loc-hub-address">
                        Dedicated engineering teams providing rapid on-site consultation and sprint handovers across Noida Sectors 62, 63, & Expressways.
                    </p>
                    <div class="loc-hub-tags">
                        <span class="loc-tag">Rapid Sprints</span>
                        <span class="loc-tag">On-Site Scoping</span>
                    </div>
                    <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <a href="/locations/noida" style="font-family: var(--font-mono, monospace); font-size: 0.82rem; font-weight: 700; color: #0284c7; text-decoration: none;">View Noida Hub Page &rarr;</a>
                    </div>
                </div>

                <!-- HUB 3 -->
                <div class="loc-hub-card-light loc-reveal" data-hub="3" style="--card-accent: #9333ea;">
                    <div class="loc-hub-icon-box">
                        <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </div>
                    <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.75rem;">ENTERPRISE HUB</span>
                    <h3 class="loc-hub-title">Gurgaon &amp; Delhi NCR</h3>
                    <p class="loc-hub-address">
                        High-touch technical consulting, custom web app architectures, and executive stakeholder alignment across Cyber City & Delhi.
                    </p>
                    <div class="loc-hub-tags">
                        <span class="loc-tag">Enterprise SOW</span>
                        <span class="loc-tag">CAPI Integration</span>
                    </div>
                    <div style="margin-top: 1.25rem; display: flex; gap: 1rem; align-items: center;">
                        <a href="/locations/delhi" style="font-family: var(--font-mono, monospace); font-size: 0.82rem; font-weight: 700; color: #9333ea; text-decoration: none;">Delhi &rarr;</a>
                        <a href="/locations/gurgaon" style="font-family: var(--font-mono, monospace); font-size: 0.82rem; font-weight: 700; color: #9333ea; text-decoration: none;">Gurgaon &rarr;</a>
                    </div>
                </div>

                <!-- HUB 4 -->
                <div class="loc-hub-card-light loc-reveal" data-hub="4" style="--card-accent: #16a34a;">
                    <div class="loc-hub-icon-box">
                        <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <span class="machined-badge machined-badge-green" style="margin-bottom: 0.75rem;">GLOBAL ASYNC</span>
                    <h3 class="loc-hub-title">Remote Global Delivery</h3>
                    <p class="loc-hub-address">
                        Decoupled Git workflows, staging deployment preview environments, async Slack status updates, and milestone guarantees worldwide.
                    </p>
                    <div class="loc-hub-tags">
                        <span class="loc-tag">Git CI/CD</span>
                        <span class="loc-tag">Async Communication</span>
                    </div>
                    <div style="margin-top: 1.25rem; text-align: right;">
                        <span style="font-family: var(--font-mono, monospace); font-size: 0.78rem; font-weight: 700; color: #16a34a;">Click for Details &rarr;</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 — CLIENT COVERAGE ASYMMETRIC SECTION (LIGHT THEME) -->
    <section class="loc-coverage-section-light">
        <div class="container">
            <div class="loc-matrix-grid">
                <!-- Left Column: Key Telemetry Stats -->
                <div class="loc-reveal">
                    <div class="machined-badge machined-badge-cyan" style="margin-bottom: 1rem;">// PERFORMANCE TELEMETRY</div>
                    <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33; line-height: 1.15; margin-bottom: 1.5rem;">
                        ENGINEERED FOR SPEED AND RELIABILITY ACROSS ALL ZONES
                    </h2>
                    <p style="color: #475569; font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                        Our infrastructure guarantees top tier performance regardless of physical distance between client and server.
                    </p>

                    <div class="loc-stat-box-light">
                        <div class="loc-stat-number" style="color: #0a63ff;">38ms</div>
                        <div style="font-family: var(--font-mono, monospace); font-size: 0.82rem; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Average Server Response Time (TTFB)</div>
                    </div>

                    <div class="loc-stat-box-light">
                        <div class="loc-stat-number" style="color: #16a34a;">100/100</div>
                        <div style="font-family: var(--font-mono, monospace); font-size: 0.82rem; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Lighthouse Mobile Optimization Score</div>
                    </div>
                </div>

                <!-- Right Column: Segment Coverage Cards -->
                <div class="loc-reveal">
                    <div class="loc-coverage-card-light">
                        <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">LOCAL CLINICS & BUSINESSES</span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.4rem; color: #050f33;">High-Conversion Regional Portals</h3>
                        <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">
                            Hyper-targeted Local SEO, immediate WhatsApp/Call lead intake routing, and lightning fast initial load speeds.
                        </p>
                    </div>

                    <div class="loc-coverage-card-light" style="border-left-color: #16a34a;">
                        <span class="machined-badge machined-badge-green" style="margin-bottom: 0.5rem;">E-COMMERCE BRANDS</span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.4rem; color: #050f33;">Decoupled Store Architectures</h3>
                        <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">
                            Custom checkout pipelines, zero cart latency, server-side Meta CAPI integration, and inventory sync APIs.
                        </p>
                    </div>

                    <div class="loc-coverage-card-light" style="border-left-color: #9333ea;">
                        <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.5rem;">SAAS & ENTERPRISE</span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Zero-Trust Web Applications</h3>
                        <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.6;">
                            Monolith-to-API migrations, Argon2id security hardening, Redis session caching, and custom admin control panels.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 04 — NEW DETAILED SECTION: ON-SITE SLA & DEPLOYMENT PROTOCOL -->
    <section class="section" style="padding-block: 6rem; background: #ffffff;">
        <div class="container">
            <div class="text-center loc-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-green" style="margin-bottom: 0.75rem;">// REGIONAL PROTOCOL</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    ON-SITE &amp; REMOTE DEPLOYMENT TIMELINE
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    How we execute engagements seamlessly whether in-person across Delhi NCR or remotely worldwide.
                </p>
            </div>

            <div class="loc-process-grid">
                <div class="loc-process-step loc-reveal">
                    <span class="loc-step-badge">STAGE 01</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Technical Scope Review</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Initial intake, requirement extraction, database schema design, and SLA targets.</p>
                </div>
                <div class="loc-process-step loc-reveal">
                    <span class="loc-step-badge" style="color: #0284c7; background: rgba(2,132,199,0.08);">STAGE 02</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Architecture &amp; Security</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Monolith refactoring, zero-trust perimeter configuration, and Redis cache setup.</p>
                </div>
                <div class="loc-process-step loc-reveal">
                    <span class="loc-step-badge" style="color: #9333ea; background: rgba(147,51,234,0.08);">STAGE 03</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Staging Demo Review</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Live staging previews, written changelog verification, and stakeholder approval.</p>
                </div>
                <div class="loc-process-step loc-reveal">
                    <span class="loc-step-badge" style="color: #16a34a; background: rgba(22,163,74,0.08);">STAGE 04</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Production Launch</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Zero-downtime deployment, DNS migration, CAPI tracking verification, and IP handoff.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 05 — NEW DETAILED SECTION: REGIONAL FAQ ACCORDION -->
    <section class="section" style="padding-block: 6rem; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="container" style="max-width: 860px;">
            <div class="text-center loc-reveal" style="margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// REGIONAL FAQ</div>
                <h2 style="font-size: clamp(2rem, 3vw, 2.75rem); font-weight: 800; color: #050f33;">
                    LOCATION &amp; DELIVERY FREQUENTLY ASKED QUESTIONS
                </h2>
            </div>

            <details class="loc-faq-item loc-reveal" open>
                <summary class="loc-faq-head">Do you provide in-person consultation across Delhi NCR?</summary>
                <div class="loc-faq-body">
                    Yes. Our core engineering lab is located in Greater Noida West (NX-One). We schedule in-person architecture reviews, security audits, and project intake sessions across Noida, Gurgaon Cyber City, and Delhi NCR.
                </div>
            </details>

            <details class="loc-faq-item loc-reveal">
                <summary class="loc-faq-head">How do remote global engagements work?</summary>
                <div class="loc-faq-body">
                    Remote engagements run via dedicated Slack/WhatsApp channels, Git repository commits, live staging preview URLs, and written weekly milestone demos. All IP is transferred upon milestone sign-off.
                </div>
            </details>

            <details class="loc-faq-item loc-reveal">
                <summary class="loc-faq-head">What is your turnaround time for local business sites vs enterprise apps?</summary>
                <div class="loc-faq-body">
                    High-conversion local business portals typically launch in 7–14 days. Complex decoupled e-commerce or custom web applications take 3–6 weeks depending on SOW requirements.
                </div>
            </details>
        </div>
    </section>

    <!-- 06 — START A PROJECT INTAKE CONSOLE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center loc-reveal" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// LOCATION INTAKE CONSOLE</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">START A REGIONAL OR GLOBAL BRIEF</h2>
            </div>
            <?php $formId = 'locationLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
