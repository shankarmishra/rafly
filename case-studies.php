<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',         'url' => '/'],
    ['name' => 'Case Studies', 'url' => '/case-studies'],
];

$page = [
    'id'        => 'work',
    'title'     => 'Case Studies & Technical Proof | RAFLY',
    'desc'      => 'Technical case studies demonstrating sub-50ms LCP engineering, zero-trust security hardening, and high-conversion web architectures.',
    'bodyClass' => 'page-work',
    'styles'    => ['home', 'home-scenes', 'work'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- Custom Pointer Ring for Case Studies -->
    <div class="work-custom-cursor" id="workCursor"></div>

    <!-- 01 — HERO: SELECTED WORK ARCHIVE (LIGHT THEME) -->
    <section class="work-hero-light">
        <div class="container work-hero-grid">
            <div class="work-reveal">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.25rem;">
                    <span class="glow-dot-active"></span> VERIFIED TECHNICAL CASE ARCHIVE
                </div>
                <h1 class="work-hero-title">
                    SYSTEMS THAT <br><span class="text-blue">DOMINATE.</span>
                </h1>
                <p style="font-size: clamp(1rem, 1.3vw, 1.15rem); color: #475569; line-height: 1.6; max-width: 520px; margin-bottom: 2rem;">
                    No superficial templates. Every case study below is a proof of sub-50ms web engineering, zero security failures, and measurable business growth.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="#archive" style="box-shadow: 0 10px 25px rgba(10, 99, 255, 0.3);">
                        Explore Case Studies <?= icon('arrow-down') ?>
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="#intake">
                        Submit Technical SOW <?= icon('arrow-up-right') ?>
                    </a>
                </div>
            </div>

            <!-- HERO CUSTOM SVG: SYSTEM ARCHITECTURE PIPELINE MODEL (LIGHT THEME) -->
            <div class="work-reveal" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(16px); border: 1px solid rgba(10, 99, 255, 0.2); border-radius: 20px; padding: 1.75rem; box-shadow: 0 15px 40px rgba(10, 99, 255, 0.08);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 700; color: #0a63ff;">
                            EXECUTION_PIPELINE // VERIFIED_METRICS
                        </span>
                    </div>
                    <span class="machined-badge machined-badge-green" style="font-size: 0.65rem;">● 100/100 LIGHTHOUSE</span>
                </div>
                <div style="display: flex; justify-content: center; align-items: center; padding: 0.5rem 0; width: 100%; height: 160px;">
                    <lottie-player
                        src="/assets/lottie/ad-reach.json"
                        background="transparent"
                        speed="1"
                        style="width: 100%; max-width: 260px; height: 160px;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>
                <div style="padding: 0.5rem;">
                    <svg viewBox="0 0 440 210" fill="none" style="width: 100%; height: auto;">
                        <defs>
                            <linearGradient id="pipeGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#ef4444"/>
                                <stop offset="100%" stop-color="#0a63ff"/>
                            </linearGradient>
                            <linearGradient id="pipeGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#0a63ff"/>
                                <stop offset="100%" stop-color="#16a34a"/>
                            </linearGradient>
                        </defs>

                        <!-- Pipeline Track Lines -->
                        <path d="M 65 65 L 175 65 L 285 65 L 375 65" stroke="url(#pipeGrad1)" stroke-width="2.5" stroke-dasharray="5 4"/>
                        <path d="M 65 145 L 175 145 L 285 145 L 375 145" stroke="url(#pipeGrad2)" stroke-width="2.5" stroke-dasharray="5 4"/>

                        <!-- Data Flow Packets -->
                        <circle r="4" fill="#0a63ff"><animateMotion path="M 65 65 L 375 65" dur="3s" repeatCount="indefinite" /></circle>
                        <circle r="4" fill="#16a34a"><animateMotion path="M 65 145 L 375 145" dur="2.4s" repeatCount="indefinite" /></circle>

                        <!-- Node 1: Ingestion & Audit -->
                        <rect x="20" y="40" width="90" height="50" rx="10" fill="#ffffff" stroke="#ef4444" stroke-width="1.8" />
                        <text x="65" y="63" font-family="monospace" font-size="8.5" fill="#dc2626" text-anchor="middle" font-weight="800">01. DIAGNOSTICS</text>
                        <text x="65" y="77" font-family="monospace" font-size="7" fill="#64748b" text-anchor="middle">LCP Audit &amp; Bottlenecks</text>

                        <!-- Node 2: Zero-Trust Security -->
                        <rect x="130" y="40" width="90" height="50" rx="10" fill="#ffffff" stroke="#0284c7" stroke-width="1.8" />
                        <text x="175" y="63" font-family="monospace" font-size="8.5" fill="#0284c7" text-anchor="middle" font-weight="800">02. ZERO-TRUST</text>
                        <text x="175" y="77" font-family="monospace" font-size="7" fill="#64748b" text-anchor="middle">WAF &amp; Schema Firewall</text>

                        <!-- Node 3: PHP 8.3 Architecture -->
                        <rect x="240" y="40" width="90" height="50" rx="10" fill="#ffffff" stroke="#0a63ff" stroke-width="1.8" />
                        <text x="285" y="63" font-family="monospace" font-size="8.5" fill="#0a63ff" text-anchor="middle" font-weight="800">03. ARCHITECTURE</text>
                        <text x="285" y="77" font-family="monospace" font-size="7" fill="#64748b" text-anchor="middle">Fast Engine &amp; Redis</text>

                        <!-- Node 4: Verified Delivery -->
                        <rect x="345" y="40" width="75" height="50" rx="10" fill="#f0fdf4" stroke="#16a34a" stroke-width="2" />
                        <text x="382.5" y="63" font-family="monospace" font-size="8.5" fill="#16a34a" text-anchor="middle" font-weight="800">04. RESULT</text>
                        <text x="382.5" y="77" font-family="monospace" font-size="7" fill="#15803d" text-anchor="middle">38ms LCP</text>

                        <!-- Lower Metric Callout Row -->
                        <rect x="50" y="125" width="105" height="40" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
                        <text x="102.5" y="142" font-family="monospace" font-size="8" fill="#050f33" text-anchor="middle" font-weight="bold">LATENCY SCORE</text>
                        <text x="102.5" y="154" font-family="monospace" font-size="7.5" fill="#0a63ff" text-anchor="middle">38ms (99th %ile)</text>

                        <rect x="175" y="125" width="105" height="40" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
                        <text x="227.5" y="142" font-family="monospace" font-size="8" fill="#050f33" text-anchor="middle" font-weight="bold">SECURITY FAILURES</text>
                        <text x="227.5" y="154" font-family="monospace" font-size="7.5" fill="#16a34a" text-anchor="middle">0 Failures Verified</text>

                        <rect x="300" y="125" width="95" height="40" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
                        <text x="347.5" y="142" font-family="monospace" font-size="8" fill="#050f33" text-anchor="middle" font-weight="bold">IP OWNERSHIP</text>
                        <text x="347.5" y="154" font-family="monospace" font-size="7.5" fill="#9333ea" text-anchor="middle">100% Transferred</text>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ANIMATED TICKER MARQUEE (LIGHT THEME) -->
        <div class="work-ticker-wrap-light">
            <div class="work-ticker-track">
                <div class="work-ticker-item-light"><span>⚡</span> SUB-50ms LCP RESPONSES</div>
                <div class="work-ticker-item-light"><span>🛡️</span> ZERO-TRUST SECURITY HARDENING</div>
                <div class="work-ticker-item-light"><span>📊</span> SERVER-SIDE GA4 &amp; META CAPI</div>
                <div class="work-ticker-item-light"><span>💬</span> INSTANT WHATSAPP LEAD TRIAGE</div>
                <div class="work-ticker-item-light"><span>⚡</span> SUB-50ms LCP RESPONSES</div>
                <div class="work-ticker-item-light"><span>🛡️</span> ZERO-TRUST SECURITY HARDENING</div>
                <div class="work-ticker-item-light"><span>📊</span> SERVER-SIDE GA4 &amp; META CAPI</div>
                <div class="work-ticker-item-light"><span>💬</span> INSTANT WHATSAPP LEAD TRIAGE</div>
            </div>
        </div>
    </section>

    <!-- 02 — ARCHIVE DISPLAY & CATEGORY FILTER -->
    <section class="section" id="archive" style="padding-block: 5rem; background: #ffffff;">
        <div class="container">
            <div class="text-center work-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 2rem;">
                <div class="machined-badge machined-badge-cyan" style="margin-bottom: 0.75rem;">// CASE ARCHIVE</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    FEATURED SYSTEM BUILDS
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    Explore real-world technical challenges, architectural strategies, and verified metrics.
                </p>
            </div>

            <!-- CATEGORY FILTER PILLS -->
            <div class="work-filter-bar work-reveal">
                <button class="work-filter-btn is-active" data-filter="all">All Engagements</button>
                <button class="work-filter-btn" data-filter="web">Web Platforms</button>
                <button class="work-filter-btn" data-filter="security">Security &amp; Audit</button>
                <button class="work-filter-btn" data-filter="growth">Growth &amp; Analytics</button>
                <button class="work-filter-btn" data-filter="commerce">Commerce &amp; Automation</button>
            </div>

            <!-- CASE STUDY CARDS LIST -->
            <div class="work-list">
                
                <!-- CASE 1: E-COMMERCE MODERNIZATION (WEB PLATFORM) -->
                <div class="work-card-light work-reveal" data-category="web commerce">
                    <div class="work-card-media-light">
                        <svg viewBox="0 0 320 220" fill="none" style="width: 100%; height: auto;">
                            <!-- Detailed Database & Cache Schematics SVG -->
                            <rect x="20" y="30" width="120" height="70" rx="8" fill="#0b1739" stroke="#0a63ff" stroke-width="1.8"/>
                            <text x="80" y="55" font-family="monospace" font-size="9" fill="#ffffff" text-anchor="middle" font-weight="bold">PHP 8.3 CORE ENGINE</text>
                            <text x="80" y="75" font-family="monospace" font-size="7.5" fill="#38bdf8" text-anchor="middle">OpCache + JIT Active</text>

                            <line x1="140" y1="65" x2="180" y2="65" stroke="#0a63ff" stroke-width="2" stroke-dasharray="3 3"/>
                            <circle r="3.5" fill="#38bdf8"><animateMotion path="M140,65 L180,65" dur="1.5s" repeatCount="indefinite"/></circle>

                            <rect x="180" y="30" width="120" height="70" rx="8" fill="#0b1739" stroke="#0284c7" stroke-width="1.8"/>
                            <text x="240" y="55" font-family="monospace" font-size="9" fill="#ffffff" text-anchor="middle" font-weight="bold">REDIS IN-MEMORY</text>
                            <text x="240" y="75" font-family="monospace" font-size="7.5" fill="#38bdf8" text-anchor="middle">0.4ms Session Cache</text>

                            <rect x="20" y="130" width="280" height="60" rx="8" fill="#0b1739" stroke="#16a34a" stroke-width="1.8"/>
                            <text x="160" y="155" font-family="monospace" font-size="9.5" fill="#ffffff" text-anchor="middle" font-weight="bold">MYSQL NORMALIZED VAULT</text>
                            <text x="160" y="172" font-family="monospace" font-size="8" fill="#4ade80" text-anchor="middle">LCP: 38ms (Verified 100/100)</text>
                        </svg>
                    </div>
                    <div class="work-card-body-light">
                        <div>
                            <div class="work-card-meta">
                                <span class="machined-badge machined-badge-blue">CASE ARCHIVE #01</span>
                                <span style="font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #16a34a; font-weight: 700;">● VERIFIED 38ms LCP</span>
                            </div>
                            <h3 class="work-card-title">E-Commerce Platform Modernization</h3>
                        </div>

                        <div class="work-quads-grid">
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #ef4444;">01. Problem</span>
                                <div class="work-quad-text">Legacy monolith suffering 4.2s LCP latency, cart abandonment, and mobile score under 40.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0284c7;">02. System</span>
                                <div class="work-quad-text">Decoupled PHP 8.3 core with Redis caching, SVG asset pipeline, and responsive layout.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0a63ff;">03. Build</span>
                                <div class="work-quad-text">Optimized SQL query vault, zero render-blocking CSS bundle, and instant prefetching.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #16a34a;">04. Result</span>
                                <div class="work-quad-text">38ms LCP, 100/100 Lighthouse Vitals, and 42% increase in checkout conversions.</div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div class="work-tech-tags">
                                <span class="work-tech-tag">PHP 8.3</span>
                                <span class="work-tech-tag">MySQL</span>
                                <span class="work-tech-tag">Redis</span>
                                <span class="work-tech-tag">Vanilla JS</span>
                            </div>
                            <a class="btn btn-outline-primary btn-sm" href="#intake">
                                Request Architecture SOW &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CASE 2: ZERO-TRUST SECURITY (SECURITY) -->
                <div class="work-card-light work-reveal" data-category="security">
                    <div class="work-card-media-light" style="background: linear-gradient(135deg, #070d1e 0%, #15092b 100%);">
                        <svg viewBox="0 0 320 220" fill="none" style="width: 100%; height: auto;">
                            <!-- Security Shield & Key Lock SVG -->
                            <path d="M160 30 L250 65 V130 C250 180 160 205 160 205 C160 205 70 180 70 130 V65 L160 30 Z" fill="#0b1739" stroke="#9333ea" stroke-width="2"/>
                            <circle cx="160" cy="110" r="24" stroke="#38bdf8" stroke-width="2" fill="none"/>
                            <rect x="154" y="110" width="12" height="24" rx="3" fill="#38bdf8"/>
                            <text x="160" y="165" font-family="monospace" font-size="8.5" fill="#a855f7" text-anchor="middle" font-weight="bold">ARGON2ID + WAF FIREWALL</text>
                            <text x="160" y="180" font-family="monospace" font-size="7.5" fill="#4ade80" text-anchor="middle">0 SECURITY FAILURES</text>
                        </svg>
                    </div>
                    <div class="work-card-body-light">
                        <div>
                            <div class="work-card-meta">
                                <span class="machined-badge machined-badge-red">CASE ARCHIVE #02</span>
                                <span style="font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #16a34a; font-weight: 700;">● ZERO BREACHES VERIFIED</span>
                            </div>
                            <h3 class="work-card-title">Zero-Trust Security Perimeter Hardening</h3>
                        </div>

                        <div class="work-quads-grid">
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #ef4444;">01. Problem</span>
                                <div class="work-quad-text">Vulnerability to brute-force credential stuffing and SQL injection vectors on legacy portal.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0284c7;">02. System</span>
                                <div class="work-quad-text">Argon2id password hashing, strict PDO bind values, and automated rate-limiting WAF.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0a63ff;">03. Build</span>
                                <div class="work-quad-text">Double-submit CSRF tokens, strict CSP headers, and encrypted HTTP-only session keys.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #16a34a;">04. Result</span>
                                <div class="work-quad-text">100% audit pass rate, 0 security incidents, and sub-10ms security check overhead.</div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div class="work-tech-tags">
                                <span class="work-tech-tag">Argon2id</span>
                                <span class="work-tech-tag">WAF Rules</span>
                                <span class="work-tech-tag">PDO Vault</span>
                                <span class="work-tech-tag">CSP v3</span>
                            </div>
                            <a class="btn btn-outline-primary btn-sm" href="#intake">
                                Request Security Audit &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CASE 3: SERVER-SIDE CAPI & GROWTH ENGINE (GROWTH) -->
                <div class="work-card-light work-reveal" data-category="growth">
                    <div class="work-card-media-light" style="background: linear-gradient(135deg, #051624 0%, #032d42 100%);">
                        <svg viewBox="0 0 320 220" fill="none" style="width: 100%; height: auto;">
                            <!-- Growth Signal Flow SVG -->
                            <rect x="20" y="40" width="110" height="60" rx="8" fill="#0b1739" stroke="#0284c7" stroke-width="1.8"/>
                            <text x="75" y="65" font-family="monospace" font-size="8.5" fill="#ffffff" text-anchor="middle" font-weight="bold">CLIENT PAYLOAD</text>
                            <text x="75" y="80" font-family="monospace" font-size="7" fill="#ef4444" text-anchor="middle">iOS 14.5+ Blocked</text>

                            <path d="M130 70 C 160 70, 160 140, 190 140" stroke="#16a34a" stroke-width="2" stroke-dasharray="3 3"/>
                            <circle r="3.5" fill="#16a34a"><animateMotion path="M130 70 C 160 70, 160 140, 190 140" dur="2s" repeatCount="indefinite"/></circle>

                            <rect x="190" y="110" width="110" height="60" rx="8" fill="#0b1739" stroke="#16a34a" stroke-width="1.8"/>
                            <text x="245" y="135" font-family="monospace" font-size="8.5" fill="#ffffff" text-anchor="middle" font-weight="bold">SERVER CAPI ENGINE</text>
                            <text x="245" y="150" font-family="monospace" font-size="7" fill="#4ade80" text-anchor="middle">100% Attributed ROI</text>
                        </svg>
                    </div>
                    <div class="work-card-body-light">
                        <div>
                            <div class="work-card-meta">
                                <span class="machined-badge machined-badge-purple">CASE ARCHIVE #03</span>
                                <span style="font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #16a34a; font-weight: 700;">● 100% ATTRIBUTION SYNC</span>
                            </div>
                            <h3 class="work-card-title">Server-Side CAPI &amp; Growth Attribution Engine</h3>
                        </div>

                        <div class="work-quads-grid">
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #ef4444;">01. Problem</span>
                                <div class="work-quad-text">35% loss in Meta ad campaign conversion tracking due to iOS 14.5+ privacy updates.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0284c7;">02. System</span>
                                <div class="work-quad-text">Direct server-to-server Graph API integration emitting hashed lead signals.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0a63ff;">03. Build</span>
                                <div class="work-quad-text">Asynchronous event queue dispatcher, GA4 Measurement Protocol, and SHA-256 hashing.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #16a34a;">04. Result</span>
                                <div class="work-quad-text">Full recovery of ad attribution, 28% lower Customer Acquisition Cost (CAC).</div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div class="work-tech-tags">
                                <span class="work-tech-tag">Meta CAPI</span>
                                <span class="work-tech-tag">GA4 MP</span>
                                <span class="work-tech-tag">SHA-256</span>
                                <span class="work-tech-tag">cURL Queue</span>
                            </div>
                            <a class="btn btn-outline-primary btn-sm" href="#intake">
                                Setup CAPI Tracking &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CASE 4: LEAD TRIAGE AUTOMATION (COMMERCE) -->
                <div class="work-card-light work-reveal" data-category="commerce">
                    <div class="work-card-media-light" style="background: linear-gradient(135deg, #021a14 0%, #06382a 100%);">
                        <svg viewBox="0 0 320 220" fill="none" style="width: 100%; height: auto;">
                            <!-- WhatsApp Lead Triage Flowchart SVG -->
                            <rect x="20" y="80" width="80" height="50" rx="8" fill="#0b1739" stroke="#16a34a" stroke-width="1.8"/>
                            <text x="60" y="103" font-family="monospace" font-size="8" fill="#ffffff" text-anchor="middle" font-weight="bold">LEAD FORM</text>
                            <text x="60" y="117" font-family="monospace" font-size="6.5" fill="#4ade80" text-anchor="middle">Instant Intake</text>

                            <line x1="100" y1="105" x2="150" y2="105" stroke="#16a34a" stroke-width="2"/>
                            <circle r="3.5" fill="#4ade80"><animateMotion path="M100,105 L150,105" dur="1.2s" repeatCount="indefinite"/></circle>

                            <rect x="150" y="80" width="150" height="50" rx="8" fill="#0b1739" stroke="#0284c7" stroke-width="1.8"/>
                            <text x="225" y="103" font-family="monospace" font-size="8" fill="#ffffff" text-anchor="middle" font-weight="bold">WHATSAPP CLOUD API</text>
                            <text x="225" y="117" font-family="monospace" font-size="6.5" fill="#38bdf8" text-anchor="middle">Automated Sales Routing</text>
                        </svg>
                    </div>
                    <div class="work-card-body-light">
                        <div>
                            <div class="work-card-meta">
                                <span class="machined-badge machined-badge-green">CASE ARCHIVE #04</span>
                                <span style="font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #16a34a; font-weight: 700;">● INSTANT 3-SEC RESPONSE</span>
                            </div>
                            <h3 class="work-card-title">WhatsApp Cloud API Lead Triage CRM</h3>
                        </div>

                        <div class="work-quads-grid">
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #ef4444;">01. Problem</span>
                                <div class="work-quad-text">High lead drop-off rate due to manual 4-hour email response delays.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0284c7;">02. System</span>
                                <div class="work-quad-text">WhatsApp Business Cloud API webhooks with instant automated lead qualification.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #0a63ff;">03. Build</span>
                                <div class="work-quad-text">PHP webhook receiver, automated template message dispatch, and sales team alerts.</div>
                            </div>
                            <div class="work-quad-box">
                                <span class="work-quad-label" style="color: #16a34a;">04. Result</span>
                                <div class="work-quad-text">Response time reduced from 4 hours to 3 seconds, 3.5x higher sales conversion.</div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div class="work-tech-tags">
                                <span class="work-tech-tag">WhatsApp API</span>
                                <span class="work-tech-tag">Webhooks</span>
                                <span class="work-tech-tag">CRM Sync</span>
                                <span class="work-tech-tag">JSON Parsers</span>
                            </div>
                            <a class="btn btn-outline-primary btn-sm" href="#intake">
                                Build Lead Automation &rarr;
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 03 — NEW DETAILED SECTION: TECHNICAL STACK & ARCHITECTURE PRINCIPLES -->
    <section class="section" style="padding-block: 6rem; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="container">
            <div class="text-center work-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// CORE TECH STACK</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    ENGINEERING ARCHITECTURE STANDARDS
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    The foundational standards behind every system we build.
                </p>
            </div>

            <div class="work-tech-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
                <div class="work-tech-card work-reveal" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">PHP 8.3 &amp; REDIS</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Decoupled Monoliths</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Sub-50ms execution speed, normalized SQL data vaults, and Redis session caching.</p>
                </div>
                <div class="work-tech-card work-reveal" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-red" style="margin-bottom: 0.75rem;">ZERO-TRUST SECURITY</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Argon2id &amp; WAF</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">PDO prepared queries, double-submit CSRF cookies, and automated IP rate limits.</p>
                </div>
                <div class="work-tech-card work-reveal" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.75rem;">GROWTH ATTRIBUTION</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">Server-Side Meta CAPI</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Bypassing client ad-blockers and iOS 14.5+ restrictions for 100% attributed ROI.</p>
                </div>
                <div class="work-tech-card work-reveal" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem 1.5rem; box-shadow: 0 4px 15px rgba(5,15,51,0.03);">
                    <span class="machined-badge machined-badge-green" style="margin-bottom: 0.75rem;">LEAD AUTOMATION</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #050f33; margin-bottom: 0.4rem;">WhatsApp Cloud API</h4>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.55;">Instant lead qualification triage, automated alerts, and CRM webhook sync.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 04 — BUILD SOMETHING WORTH SHOWING INTAKE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center work-reveal" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// WORK INTAKE CONSOLE</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">BUILD SOMETHING WORTH SHOWING</h2>
            </div>
            <?php $formId = 'workLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
