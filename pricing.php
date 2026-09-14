<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',    'url' => '/'],
    ['name' => 'Pricing', 'url' => '/pricing'],
];

$page = [
    'id'        => 'pricing',
    'title'     => '7-Dimension Complexity Framework & Pricing | RAFly',
    'desc'      => 'Transparent 7-dimension complexity evaluation model for web engineering, security audits, and performance marketing systems.',
    'bodyClass' => 'page-pricing',
    'styles'    => ['home', 'home-scenes', 'pricing'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- 01 — HERO: THE PROJECT COMPLEXITY MODEL (LIGHT THEME) -->
    <section class="price-hero-light">
        <div class="container price-hero-grid">
            <div class="price-reveal">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.25rem;">
                    <span class="glow-dot-active"></span> NO GENERIC PACKAGES // WRITTEN SOW GUARANTEE
                </div>
                <h1 class="price-hero-title">
                    PROJECT COMPLEXITY <br><span>EVALUATION MODEL.</span>
                </h1>
                <p style="font-size: clamp(1rem, 1.3vw, 1.15rem); color: #475569; line-height: 1.6; max-width: 540px; margin-bottom: 2rem;">
                    We evaluate every project across 7 precise technical dimensions to provide a fixed milestone scope with 100% written IP ownership transfer.
                </p>
                
                <div style="display: flex; gap: 1.25rem; flex-wrap: wrap; margin-bottom: 2.5rem;">
                    <a class="btn btn-primary btn-lg" href="#calc" style="box-shadow: 0 10px 25px rgba(10, 99, 255, 0.3);">
                        Calculate Complexity <?= icon('arrow-down') ?>
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="#intake">
                        Submit SOW Brief <?= icon('arrow-up-right') ?>
                    </a>
                </div>

                <!-- Trust Badges Row -->
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="telemetry-pill-mono" style="background: rgba(10, 99, 255, 0.08); color: #0b52d8; border: 1px solid rgba(10, 99, 255, 0.2);">
                        📄 Written Scope of Work
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.2);">
                        🔒 Fixed Milestone Payments
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(22, 163, 74, 0.08); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2);">
                        🛡️ 100% IP Transfer
                    </span>
                </div>
            </div>

            <!-- HERO HEPTAGON RADAR SVG (LIGHT THEME) -->
            <div class="price-radar-box-light price-reveal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: #0a63ff; display: inline-block;"></span>
                        <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 700; color: #0a63ff;">
                            COMPLEXITY_RADAR // 7D MATRIX
                        </span>
                    </div>
                    <span class="machined-badge machined-badge-cyan" id="heroScoreBadge" style="font-size: 0.65rem;">SCORE: 65/100</span>
                </div>
                <div style="display: flex; justify-content: center; align-items: center; padding: 0.5rem 0; width: 100%; height: 120px;">
                    <lottie-player
                        src="/assets/lottie/radar.json"
                        background="transparent"
                        speed="1"
                        style="width: 90px; height: 90px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 0.5rem; position: relative;">
                    <svg viewBox="0 0 400 330" fill="none" style="width: 100%; height: auto;">
                        <defs>
                            <radialGradient id="polyGlowLight" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
                            </radialGradient>
                        </defs>

                        <!-- Outer Target Circles & Coordinate Grid -->
                        <circle cx="200" cy="165" r="115" stroke="#cbd5e1" stroke-width="1.2"/>
                        <circle cx="200" cy="165" r="80" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 4"/>
                        <circle cx="200" cy="165" r="45" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 3"/>
                        <circle cx="200" cy="165" r="30" fill="url(#polyGlowLight)"/>

                        <!-- Tier 1: Starter Scope Polygon -->
                        <polygon points="200,110 240,128 250,172 222,210 178,210 150,172 160,128" stroke="#0284c7" stroke-width="1.2" stroke-dasharray="3 3" fill="rgba(2, 132, 199, 0.05)"/>
                        
                        <!-- Tier 2: Growth Build Polygon (Active Focus) -->
                        <polygon points="200,75 268,102 284,172 236,236 164,236 116,172 132,102" stroke="#0a63ff" stroke-width="2.2" fill="rgba(10, 99, 255, 0.12)"/>

                        <!-- Tier 3: Enterprise Outer Polygon -->
                        <polygon points="200,50 285,85 305,175 245,255 155,255 95,175 115,85" stroke="#cbd5e1" stroke-width="1.2" fill="none"/>
                        
                        <!-- Axis Lines to Vertices -->
                        <line x1="200" y1="165" x2="200" y2="50" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>
                        <line x1="200" y1="165" x2="285" y2="85" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>
                        <line x1="200" y1="165" x2="305" y2="175" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>
                        <line x1="200" y1="165" x2="245" y2="255" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>
                        <line x1="200" y1="165" x2="155" y2="255" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>
                        <line x1="200" y1="165" x2="95" y2="175" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>
                        <line x1="200" y1="165" x2="115" y2="85" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 2"/>

                        <!-- Vertex Nodes & Labels -->
                        <circle cx="200" cy="50" r="5" fill="#0284c7"/>
                        <text x="200" y="38" font-family="monospace" font-size="8.5" fill="#0284c7" text-anchor="middle" font-weight="bold">01. SCOPE SCALE</text>

                        <circle cx="285" cy="85" r="5" fill="#0a63ff"/>
                        <text x="296" y="80" font-family="monospace" font-size="8.5" fill="#0a63ff" font-weight="bold">02. ARCHITECTURE</text>

                        <circle cx="305" cy="175" r="5" fill="#9333ea"/>
                        <text x="315" y="180" font-family="monospace" font-size="8.5" fill="#9333ea" font-weight="bold">03. INTEGRATIONS</text>

                        <circle cx="245" cy="255" r="5" fill="#dc2626"/>
                        <text x="255" y="272" font-family="monospace" font-size="8.5" fill="#dc2626" font-weight="bold">04. SECURITY HARDENING</text>

                        <circle cx="155" cy="255" r="5" fill="#d97706"/>
                        <text x="145" y="272" font-family="monospace" font-size="8.5" fill="#d97706" text-anchor="end" font-weight="bold">05. CONTENT/MEDIA</text>

                        <circle cx="95" cy="175" r="5" fill="#16a34a"/>
                        <text x="85" y="180" font-family="monospace" font-size="8.5" fill="#16a34a" text-anchor="end" font-weight="bold">06. GROWTH TRACKING</text>

                        <circle cx="115" cy="85" r="5" fill="#0284c7"/>
                        <text x="104" y="80" font-family="monospace" font-size="8.5" fill="#0284c7" text-anchor="end" font-weight="bold">07. SLA TIER</text>

                        <!-- Central Radar Sweep Line -->
                        <line x1="200" y1="165" x2="200" y2="50" stroke="#0a63ff" stroke-width="2" opacity="0.8">
                            <animateTransform attributeName="transform" type="rotate" from="0 200 165" to="360 200 165" dur="9s" repeatCount="indefinite"/>
                        </line>

                        <!-- Center Core Metric Readout -->
                        <circle cx="200" cy="165" r="14" fill="#ffffff" stroke="#0a63ff" stroke-width="2"/>
                        <text x="200" y="168" font-family="monospace" font-size="7.5" fill="#050f33" text-anchor="middle" font-weight="bold">7D</text>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 — LIVE INTERACTIVE COMPLEXITY CALCULATOR WIDGET -->
    <section class="section" id="calc" style="padding-block: 5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
        <div class="container" style="max-width: 960px;">
            <div class="text-center price-reveal" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// INTERACTIVE TOOL</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.75rem); font-weight: 800; color: #050f33;">
                    ESTIMATE YOUR PROJECT COMPLEXITY SCORE
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    Select the requirements below to live-estimate your 7D Complexity Score and recommended engagement tier.
                </p>
            </div>

            <div class="price-reveal" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(5,15,51,0.04);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer;">
                        <input type="checkbox" class="calc-check" data-score="15" checked style="width: 18px; height: 18px; accent-color: #0a63ff;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #050f33;">Custom Bespoke UI Design</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Figma + CSS 3D Animations</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer;">
                        <input type="checkbox" class="calc-check" data-score="20" checked style="width: 18px; height: 18px; accent-color: #0a63ff;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #050f33;">PHP 8.3 &amp; Redis Vault</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Sub-50ms Backend Execution</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer;">
                        <input type="checkbox" class="calc-check" data-score="15" checked style="width: 18px; height: 18px; accent-color: #0a63ff;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #050f33;">Server-Side Meta CAPI &amp; GA4</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Growth Attribution Pipeline</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer;">
                        <input type="checkbox" class="calc-check" data-score="15" style="width: 18px; height: 18px; accent-color: #0a63ff;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #050f33;">Zero-Trust Argon2id Security</div>
                            <div style="font-size: 0.8rem; color: #64748b;">WAF + Prepared PDO Vault</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer;">
                        <input type="checkbox" class="calc-check" data-score="20" style="width: 18px; height: 18px; accent-color: #0a63ff;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #050f33;">WhatsApp API Lead Triage</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Instant CRM Webhook Routing</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer;">
                        <input type="checkbox" class="calc-check" data-score="15" style="width: 18px; height: 18px; accent-color: #0a63ff;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #050f33;">Dedicated SLA Support</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Weekly Sprints &amp; Direct Slack</div>
                        </div>
                    </label>
                </div>

                <!-- Calculator Output Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; border: 1px solid #0a63ff; border-radius: 14px; padding: 1.25rem 1.75rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 700;">ESTIMATED COMPLEXITY SCORE</div>
                        <div style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 900; color: #0a63ff;" id="calcScoreText">50 / 100</div>
                    </div>
                    <div>
                        <div style="font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 700;">RECOMMENDED ENGAGEMENT TIER</div>
                        <div style="font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: #050f33;" id="calcTierText">Growth Build</div>
                    </div>
                    <a class="btn btn-primary" href="#intake" style="box-shadow: 0 8px 20px rgba(10,99,255,0.25);">
                        Submit This Scope &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 — THE 7 DIMENSIONS OF COMPLEXITY ACCORDION -->
    <section class="section" style="padding-block: 6rem; background: #ffffff;">
        <div class="container">
            <div class="text-center price-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-cyan" style="margin-bottom: 0.75rem;">// EVALUATION MATRIX</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    THE 7 DIMENSIONS OF COMPLEXITY
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    Click any dimension to explore the technical criteria we evaluate during project intake.
                </p>
            </div>

            <div class="price-accordion-list">
                
                <!-- DIMENSION 01 -->
                <details class="price-dimension-item-light price-reveal" open>
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#01</span>
                            <h3 class="price-dim-title">Scope &amp; Functional Scale</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        We evaluate total unique page templates, custom dynamic workflows, database relation counts, user role levels, and interactive state complexity.
                    </div>
                </details>

                <!-- DIMENSION 02 -->
                <details class="price-dimension-item-light price-reveal">
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#02</span>
                            <h3 class="price-dim-title">System Architecture &amp; Performance</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        Sub-50ms execution targets, PHP 8.3 decoupled monolith structuring, Redis caching strategies, and Lighthouse 100/100 mobile optimization.
                    </div>
                </details>

                <!-- DIMENSION 03 -->
                <details class="price-dimension-item-light price-reveal">
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#03</span>
                            <h3 class="price-dim-title">Third-Party &amp; API Integrations</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        Payment gateway checkouts, CRM webhooks, WhatsApp Cloud API automation, and external inventory management syncs.
                    </div>
                </details>

                <!-- DIMENSION 04 -->
                <details class="price-dimension-item-light price-reveal">
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#04</span>
                            <h3 class="price-dim-title">Security Hardening &amp; Zero-Trust</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        Argon2id password encryption, prepared PDO SQL vaults, double-submit CSRF cookie protection, and strict Content Security Policies.
                    </div>
                </details>

                <!-- DIMENSION 05 -->
                <details class="price-dimension-item-light price-reveal">
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#05</span>
                            <h3 class="price-dim-title">Content &amp; Media Asset Pipeline</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        SVG vector animations, responsive WebP/AVIF media optimization, copy structuring, and localized multi-language setups.
                    </div>
                </details>

                <!-- DIMENSION 06 -->
                <details class="price-dimension-item-light price-reveal">
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#06</span>
                            <h3 class="price-dim-title">Growth &amp; Attribution Analytics</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        Server-side Meta CAPI integration, GA4 Measurement Protocol, conversion tracking setups, and custom campaign event routing.
                    </div>
                </details>

                <!-- DIMENSION 07 -->
                <details class="price-dimension-item-light price-reveal">
                    <summary class="price-dimension-head">
                        <div style="display: flex; align-items: center;">
                            <span class="price-dim-num">#07</span>
                            <h3 class="price-dim-title">Support &amp; SLA Tier</h3>
                        </div>
                        <span class="price-dim-arrow">&darr;</span>
                    </summary>
                    <div class="price-dimension-body">
                        Post-launch maintenance guarantees, weekly milestone sprint cadences, direct Slack/WhatsApp channels, and zero downtime server monitoring.
                    </div>
                </details>

            </div>
        </div>
    </section>

    <!-- 04 — ENGAGEMENT TIERS GRID -->
    <section class="section" style="padding-block: 6rem; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="container">
            <div class="text-center price-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// ENGAGEMENT MODELS</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    THREE ENGAGEMENT TIERS
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    Structured by complexity, delivered with written SOW milestone guarantees.
                </p>
            </div>

            <div class="price-tiers-grid">
                <!-- TIER 1: STARTER -->
                <div class="price-tier-card-light price-reveal">
                    <div>
                        <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">SIMPLE COMPLEXITY</span>
                        <h3 class="price-tier-name">Starter Scope</h3>
                        <p class="price-tier-scope">Ideal for targeted landing portals, local business sites, or single high-converting lead funnels.</p>
                        <ul class="price-tier-features">
                            <li><?= icon('check') ?> 1–3 Custom Page Types</li>
                            <li><?= icon('check') ?> Sub-50ms Execution Speed</li>
                            <li><?= icon('check') ?> High-Conversion Lead Forms</li>
                            <li><?= icon('check') ?> Basic SEO &amp; Schema Data</li>
                            <li><?= icon('check') ?> 100% Written IP Transfer</li>
                        </ul>
                    </div>
                    <a class="btn btn-outline-primary btn-block" href="#intake">Select Starter Scope &rarr;</a>
                </div>

                <!-- TIER 2: GROWTH BUILD (RECOMMENDED) -->
                <div class="price-tier-card-light is-recommended price-reveal">
                    <div>
                        <span class="machined-badge machined-badge-cyan" style="margin-bottom: 0.75rem;">MEDIUM-HIGH COMPLEXITY</span>
                        <h3 class="price-tier-name">Growth Build</h3>
                        <p class="price-tier-scope">Comprehensive web platforms, e-commerce architectures, or growth attribution tracking engines.</p>
                        <ul class="price-tier-features">
                            <li><?= icon('check') ?> Up to 8 Bespoke Page Types</li>
                            <li><?= icon('check') ?> PHP 8.3 &amp; Redis Sessions</li>
                            <li><?= icon('check') ?> Server-Side Meta CAPI &amp; GA4</li>
                            <li><?= icon('check') ?> WhatsApp API Lead Triage</li>
                            <li><?= icon('check') ?> Zero-Trust WAF Hardening</li>
                            <li><?= icon('check') ?> 100% Written IP Transfer</li>
                        </ul>
                    </div>
                    <a class="btn btn-primary btn-block" href="#intake" style="box-shadow: 0 10px 25px rgba(10, 99, 255, 0.3);">Select Growth Build &rarr;</a>
                </div>

                <!-- TIER 3: ENTERPRISE SYSTEM -->
                <div class="price-tier-card-light price-reveal">
                    <div>
                        <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.75rem;">MAXIMUM COMPLEXITY</span>
                        <h3 class="price-tier-name">Enterprise System</h3>
                        <p class="price-tier-scope">Complex web applications, multi-system migrations, custom admin portals, and dedicated SLA support.</p>
                        <ul class="price-tier-features">
                            <li><?= icon('check') ?> Unlimited Custom Scopes</li>
                            <li><?= icon('check') ?> Monolith-to-API Refactoring</li>
                            <li><?= icon('check') ?> Custom Admin Control Panels</li>
                            <li><?= icon('check') ?> 24/7 Dedicated SLA Monitoring</li>
                            <li><?= icon('check') ?> Weekly Sprint Reviews</li>
                            <li><?= icon('check') ?> 100% Written IP Transfer</li>
                        </ul>
                    </div>
                    <a class="btn btn-outline-primary btn-block" href="#intake">Select Enterprise &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 05 — SUBMIT COMPLEXITY SOW BRIEF INTAKE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center price-reveal" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// PRICING INTAKE CONSOLE</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">SUBMIT YOUR SCOPE OF WORK BRIEF</h2>
            </div>
            <?php $formId = 'pricingLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
