<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Team', 'url' => '/team'],
];

$page = [
    'id'        => 'team',
    'title'     => 'Senior Practitioner Roster & Matrix | RAFly Digital Growth Partner',
    'desc'      => 'Senior practitioner team across 8 discipline verticals: System Architecture, UX/UI, Frontend, Backend, Security, Marketing, and Lead Operations.',
    'bodyClass' => 'page-team',
    'styles'    => ['home', 'home-scenes', 'team'],
    'module'    => 'home',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- 01 — HERO: PRACTITIONER ROSTER & MATRIX (LIGHT THEME) -->
    <section class="team-hero-light">
        <div class="container team-hero-grid">
            <div class="team-reveal">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.25rem;">
                    <span class="glow-dot-active"></span> SENIOR PRACTITIONER NETWORK // NO ACCOUNT MANAGERS
                </div>
                <h1 class="team-hero-title">
                    PRACTITIONER ROSTER &amp; <br><span>ENGINEERING MATRIX.</span>
                </h1>
                <p style="font-size: clamp(1rem, 1.3vw, 1.15rem); color: #475569; line-height: 1.6; max-width: 540px; margin-bottom: 2rem;">
                    Zero stock headshots, zero account manager proxy layers. You communicate directly with senior practitioners across 8 core discipline verticals.
                </p>

                <div style="display: flex; gap: 1.25rem; flex-wrap: wrap; margin-bottom: 2.5rem;">
                    <a class="btn btn-primary btn-lg" href="#roster" style="box-shadow: 0 10px 25px rgba(10, 99, 255, 0.3);">
                        Explore 8 Disciplines <?= icon('arrow-down') ?>
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="#intake">
                        Connect With Engineers <?= icon('arrow-up-right') ?>
                    </a>
                </div>

                <!-- Telemetry Row -->
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="telemetry-pill-mono" style="background: rgba(10, 99, 255, 0.08); color: #0b52d8; border: 1px solid rgba(10, 99, 255, 0.2);">
                        ⚡ Direct Engineer Communication
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.2);">
                        🎯 Senior-Level Execution
                    </span>
                    <span class="telemetry-pill-mono" style="background: rgba(22, 163, 74, 0.08); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2);">
                        🚫 Zero Stock Headshots
                    </span>
                </div>
            </div>

            <!-- HERO CUSTOM SVG: 8-NODE OCTAGON NETWORK MODEL (LIGHT THEME) -->
            <div class="team-console-box-light team-reveal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
                        <span style="font-family: var(--font-mono, monospace); font-size: 0.75rem; font-weight: 700; color: #0a63ff;">
                            ROSTER_MATRIX // 8_DISCIPLINE_CORE
                        </span>
                    </div>
                    <span class="machined-badge machined-badge-green" style="font-size: 0.65rem;">● SENIOR ENGINEERS</span>
                </div>
                <div style="padding: 0.5rem; position: relative;">
                    <svg viewBox="0 0 420 320" fill="none" style="width: 100%; height: auto;">
                        <defs>
                            <radialGradient id="hubGlowLight" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#0a63ff" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
                            </radialGradient>
                        </defs>

                        <!-- Outer Octagon Mesh Track Grid -->
                        <circle cx="210" cy="160" r="122" stroke="#e2e8f0" stroke-width="1.2"/>
                        <circle cx="210" cy="160" r="95" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 4"/>
                        <circle cx="210" cy="160" r="60" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="2 3"/>

                        <!-- Central Engine Hub -->
                        <circle cx="210" cy="160" r="42" fill="url(#hubGlowLight)"/>
                        <circle cx="210" cy="160" r="22" fill="#0a63ff" stroke="#0284c7" stroke-width="2.5"/>
                        <circle cx="210" cy="160" r="30" stroke="#0a63ff" stroke-width="1.5" stroke-dasharray="5 5" opacity="0.7">
                            <animateTransform attributeName="transform" type="rotate" from="0 210 160" to="360 210 160" dur="10s" repeatCount="indefinite" />
                        </circle>
                        <text x="210" y="164" font-family="monospace" font-weight="900" font-size="9" fill="#ffffff" text-anchor="middle">RAFLY</text>

                        <!-- 8 Node Lines Connecting to Center -->
                        <line x1="210" y1="38" x2="210" y2="160" stroke="#0a63ff" stroke-width="1.5" opacity="0.5"/>
                        <line x1="296" y1="74" x2="210" y2="160" stroke="#0284c7" stroke-width="1.5" opacity="0.5"/>
                        <line x1="332" y1="160" x2="210" y2="160" stroke="#9333ea" stroke-width="1.5" opacity="0.5"/>
                        <line x1="296" y1="246" x2="210" y2="160" stroke="#16a34a" stroke-width="1.5" opacity="0.5"/>
                        <line x1="210" y1="282" x2="210" y2="160" stroke="#d97706" stroke-width="1.5" opacity="0.5"/>
                        <line x1="124" y1="246" x2="210" y2="160" stroke="#dc2626" stroke-width="1.5" opacity="0.5"/>
                        <line x1="88" y1="160" x2="210" y2="160" stroke="#0a63ff" stroke-width="1.5" opacity="0.5"/>
                        <line x1="124" y1="74" x2="210" y2="160" stroke="#0284c7" stroke-width="1.5" opacity="0.5"/>

                        <!-- Data Signals traveling on paths -->
                        <circle r="3.5" fill="#0a63ff"><animateMotion path="M210,38 L210,160" dur="2s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#16a34a"><animateMotion path="M296,246 L210,160" dur="2.4s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#9333ea"><animateMotion path="M332,160 L210,160" dur="1.8s" repeatCount="indefinite" /></circle>
                        <circle r="3.5" fill="#dc2626"><animateMotion path="M124,246 L210,160" dur="2.2s" repeatCount="indefinite" /></circle>

                        <!-- 8 Discipline Orbit Nodes -->
                        <g transform="translate(210, 38)"><circle r="16" fill="#ffffff" stroke="#0a63ff" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#0a63ff" text-anchor="middle" font-weight="bold">STRAT</text></g>
                        <g transform="translate(296, 74)"><circle r="16" fill="#ffffff" stroke="#0284c7" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#0284c7" text-anchor="middle" font-weight="bold">ARCH</text></g>
                        <g transform="translate(332, 160)"><circle r="16" fill="#ffffff" stroke="#9333ea" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#9333ea" text-anchor="middle" font-weight="bold">UX/UI</text></g>
                        <g transform="translate(296, 246)"><circle r="16" fill="#ffffff" stroke="#16a34a" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#16a34a" text-anchor="middle" font-weight="bold">FRONT</text></g>
                        <g transform="translate(210, 282)"><circle r="16" fill="#ffffff" stroke="#d97706" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#d97706" text-anchor="middle" font-weight="bold">BACK</text></g>
                        <g transform="translate(124, 246)"><circle r="16" fill="#ffffff" stroke="#dc2626" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#dc2626" text-anchor="middle" font-weight="bold">SEC</text></g>
                        <g transform="translate(88, 160)"><circle r="16" fill="#ffffff" stroke="#0a63ff" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#0a63ff" text-anchor="middle" font-weight="bold">GROW</text></g>
                        <g transform="translate(124, 74)"><circle r="16" fill="#ffffff" stroke="#0284c7" stroke-width="2"/><text y="4" font-family="monospace" font-size="7.5" fill="#0284c7" text-anchor="middle" font-weight="bold">LEAD</text></g>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 — STATS ROW -->
    <section class="section" style="padding-block: 3.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
        <div class="container">
            <div class="grid grid-3 text-center team-reveal">
                <div>
                    <div style="font-family: var(--font-display); font-size: 2.8rem; font-weight: 900; color: #0a63ff;">8</div>
                    <div style="font-family: var(--font-mono, monospace); font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Practice Disciplines</div>
                </div>
                <div>
                    <div style="font-family: var(--font-display); font-size: 2.8rem; font-weight: 900; color: #16a34a;">100%</div>
                    <div style="font-family: var(--font-mono, monospace); font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Senior Practitioner Direct Access</div>
                </div>
                <div>
                    <div style="font-family: var(--font-display); font-size: 2.8rem; font-weight: 900; color: #9333ea;">0</div>
                    <div style="font-family: var(--font-mono, monospace); font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Stock Headshots Used</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 — 8 DISCIPLINE CARDS GRID -->
    <section class="section" id="roster" style="padding-block: 6rem; background: #ffffff;">
        <div class="container">
            <div class="text-center team-reveal" style="max-width: 680px; margin-inline: auto; margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// DISCIPLINE VERTICALS</div>
                <h2 style="font-size: clamp(2.2rem, 3.5vw, 3rem); font-weight: 800; color: #050f33; letter-spacing: -0.02em;">
                    8 PRACTITIONER DISCIPLINES
                </h2>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.6;">
                    Click any discipline card to connect directly with the lead practitioner or inspect technical toolkits.
                </p>
            </div>

            <div class="team-disciplines-grid">
                <!-- 01 DIGITAL STRATEGY -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #0a63ff;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        </div>
                        <span class="team-disc-num">DISCIPLINE [01]</span>
                        <h3 class="team-disc-title">Digital Strategy &amp; SOW</h3>
                        <p class="team-disc-desc">Milestone roadmap definition, written technical scope of work, budget allocation, and risk mitigation.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #0a63ff; font-weight: 700;">
                        Tools: Figma, Notion, SOW Vault &rarr;
                    </div>
                </div>

                <!-- 02 SYSTEM ARCHITECTURE -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #0284c7;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                        </div>
                        <span class="team-disc-num" style="color: #0284c7;">DISCIPLINE [02]</span>
                        <h3 class="team-disc-title">System Architecture</h3>
                        <p class="team-disc-desc">PHP 8.3 decoupled monolith design, MySQL database schema normalization, and sub-50ms execution targets.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #0284c7; font-weight: 700;">
                        Tools: PHP 8.3, MySQL, Redis &rarr;
                    </div>
                </div>

                <!-- 03 UX/UI DESIGN -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #9333ea;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path><path d="M2 2l7.586 7.586"></path><circle cx="11" cy="11" r="2"></circle></svg>
                        </div>
                        <span class="team-disc-num" style="color: #9333ea;">DISCIPLINE [03]</span>
                        <h3 class="team-disc-title">Bespoke UX/UI Design</h3>
                        <p class="team-disc-desc">Spatial depth wireframing, high-contrast light design tokens, 3D CSS perspectives, and responsive typography.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #9333ea; font-weight: 700;">
                        Tools: Figma, Space Grotesk, Inter &rarr;
                    </div>
                </div>

                <!-- 04 FRONTEND ENGINEERING -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #16a34a;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                        </div>
                        <span class="team-disc-num" style="color: #16a34a;">DISCIPLINE [04]</span>
                        <h3 class="team-disc-title">Frontend Engineering</h3>
                        <p class="team-disc-desc">Vanilla JS micro-interaction engines, SVG vector animations, zero framework bloat, and 100/100 Lighthouse score.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #16a34a; font-weight: 700;">
                        Tools: Vanilla JS, SVG, GSAP &rarr;
                    </div>
                </div>

                <!-- 05 BACKEND & SQL VAULT -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #d97706;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                        </div>
                        <span class="team-disc-num" style="color: #d97706;">DISCIPLINE [05]</span>
                        <h3 class="team-disc-title">Backend &amp; SQL Vault</h3>
                        <p class="team-disc-desc">Prepared PDO SQL query engines, Redis session caching, automated data cleaning, and REST/GraphQL APIs.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #d97706; font-weight: 700;">
                        Tools: PDO, Redis, JSON &rarr;
                    </div>
                </div>

                <!-- 06 CYBER SECURITY -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #dc2626;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <span class="team-disc-num" style="color: #dc2626;">DISCIPLINE [06]</span>
                        <h3 class="team-disc-title">Cyber Security Audit</h3>
                        <p class="team-disc-desc">Argon2id password hashing, WAF rate limits, double-submit CSRF cookie protection, and OWASP Top 10 compliance.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #dc2626; font-weight: 700;">
                        Tools: Argon2id, CSRF, CSP &rarr;
                    </div>
                </div>

                <!-- 07 PERFORMANCE MARKETING -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #0a63ff;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        </div>
                        <span class="team-disc-num">DISCIPLINE [07]</span>
                        <h3 class="team-disc-title">Growth &amp; Meta CAPI</h3>
                        <p class="team-disc-desc">Server-side Meta Conversions API integration, GA4 Measurement Protocol, and 100% attributed campaign ROI.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #0a63ff; font-weight: 700;">
                        Tools: Meta CAPI, GA4 MP &rarr;
                    </div>
                </div>

                <!-- 08 LEAD OPERATIONS -->
                <div class="team-disc-card-light team-reveal" style="--disc-accent: #0284c7;">
                    <div>
                        <div class="team-disc-icon-wrap-light">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        </div>
                        <span class="team-disc-num" style="color: #0284c7;">DISCIPLINE [08]</span>
                        <h3 class="team-disc-title">Lead Operations</h3>
                        <p class="team-disc-desc">WhatsApp Cloud API automated lead triage, instant sales alerts, webhook syncs, and automated intake forms.</p>
                    </div>
                    <div style="margin-top: 1rem; font-family: var(--font-mono, monospace); font-size: 0.75rem; color: #0284c7; font-weight: 700;">
                        Tools: WhatsApp API, Webhooks &rarr;
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 04 — COLLABORATION PROTOCOL CONSOLE -->
    <section class="section" style="padding-block: 6rem; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="container" style="max-width: 860px;">
            <div class="text-center team-reveal" style="margin-bottom: 3.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.75rem;">// OPERATIONAL PROTOCOL</div>
                <h2 style="font-size: clamp(2rem, 3vw, 2.75rem); font-weight: 800; color: #050f33;">
                    DIRECT PRACTITIONER COLLABORATION PROTOCOL
                </h2>
            </div>

            <div class="team-reveal" style="background: #050f33; color: #ffffff; border-radius: 18px; padding: 2rem; font-family: var(--font-mono, monospace); font-size: 0.9rem; line-height: 1.8; box-shadow: 0 15px 40px rgba(5,15,51,0.2);">
                <div style="color: #38bdf8; margin-bottom: 1rem; font-weight: bold;">> INIT_COLLABORATION_PROTOCOL</div>
                <div style="margin-bottom: 0.5rem;"><span style="color: #4ade80;">[✓]</span> CHANNEL_TYPE: Direct Slack / WhatsApp Channel</div>
                <div style="margin-bottom: 0.5rem;"><span style="color: #4ade80;">[✓]</span> PROXY_LAYERS: Zero Account Managers (Direct Practitioner Access)</div>
                <div style="margin-bottom: 0.5rem;"><span style="color: #4ade80;">[✓]</span> SPRINT_CADENCE: Weekly Staging Preview Demos</div>
                <div style="margin-bottom: 0.5rem;"><span style="color: #4ade80;">[✓]</span> SOW_GUARANTEE: 100% Written IP Ownership Handoff</div>
            </div>
        </div>
    </section>

    <!-- 05 — CONNECT WITH SENIOR PRACTITIONERS INTAKE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center team-reveal" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">// ROSTER INTAKE CONSOLE</div>
                <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #050f33;">CONNECT WITH OUR SENIOR PRACTITIONERS</h2>
            </div>
            <?php $formId = 'teamLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Discipline card click to scroll to intake console
    const discCards = document.querySelectorAll('.team-disc-card-light');
    discCards.forEach(card => {
        card.addEventListener('click', () => {
            const intakeSection = document.getElementById('intake');
            if (intakeSection) {
                intakeSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
</script>

<?php require __DIR__ . '/partials/tail.php'; ?>
