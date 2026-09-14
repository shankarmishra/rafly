<?php
require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',  'url' => '/'],
    ['name' => 'About', 'url' => '/about'],
];

$page = [
    'id'        => 'about',
    'title'     => 'About Studio | RAFly Digital Growth Partner',
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
        <div class="container hero-grid" style="display: grid; grid-template-columns: 50% 50%; gap: 2.5rem; align-items: center; width: 100%;">
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

            <!-- HERO VISUAL CONSOLE: RAFLY OPERATING SYSTEM -->
            <div class="code-console-window" style="background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(20px); border: 1px solid rgba(10, 99, 255, 0.18); border-radius: 24px; padding: 1.5rem; box-shadow: 0 20px 50px rgba(10, 99, 255, 0.08), 0 2px 10px rgba(5, 15, 51, 0.03); position: relative;">
                <!-- Window Topbar -->
                <div class="code-console-bar" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem; margin-bottom: 0.75rem; background: transparent;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #0a63ff; margin-left: 0.4rem; font-family: var(--font-mono, monospace); letter-spacing: 0.05em;">
                            RAFLY_CORE // SYSTEM_ARCHITECTURE
                        </span>
                    </div>
                    <span class="machined-badge machined-badge-green" style="font-size: 0.65rem; padding: 3px 10px; border-radius: 20px;">● ONLINE</span>
                </div>
                
                <!-- Main Lottie Stage -->
                <div style="display: flex; justify-content: center; align-items: center; padding: 0.5rem 0; width: 100%; min-height: 310px; background: radial-gradient(circle at 50% 50%, rgba(10, 99, 255, 0.06) 0%, rgba(2, 132, 199, 0.02) 50%, rgba(255, 255, 255, 0) 75%); border-radius: 16px; position: relative; overflow: hidden;">
                    <!-- Ambient Grid Backdrop -->
                    <div style="position: absolute; inset: 0; background-image: radial-gradient(rgba(10, 99, 255, 0.12) 1px, transparent 1px); background-size: 16px 16px; opacity: 0.4; pointer-events: none;"></div>

                    <!-- Floating Micro Badges -->
                    <div style="position: absolute; top: 12px; left: 14px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); border: 1px solid rgba(10, 99, 255, 0.2); padding: 4px 10px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; color: #0a63ff; font-family: var(--font-mono, monospace); box-shadow: 0 4px 12px rgba(10, 99, 255, 0.08); z-index: 2;">
                        ⚡ 100/100 Core Vitals
                    </div>
                    <div style="position: absolute; bottom: 12px; right: 14px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); border: 1px solid rgba(22, 163, 74, 0.2); padding: 4px 10px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; color: #16a34a; font-family: var(--font-mono, monospace); box-shadow: 0 4px 12px rgba(22, 163, 74, 0.08); z-index: 2;">
                        🛡️ Zero-Trust Active
                    </div>

                    <lottie-player
                        src="/assets/lottie/system-arch.json"
                        background="transparent"
                        speed="1"
                        style="width: 100%; max-width: 480px; height: 320px; transform: scale(1.05); z-index: 1;"
                        loop
                        autoplay
                        aria-hidden="true">
                    </lottie-player>
                </div>

                <!-- Telemetry HUD Status Cards Footer -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 0.75rem; padding-top: 0.85rem; border-top: 1px dashed #e2e8f0;">
                    <div style="background: #f8fafc; border: 1px solid rgba(10, 99, 255, 0.12); border-radius: 10px; padding: 0.6rem 0.5rem; text-align: center;">
                        <div style="font-size: 0.65rem; font-weight: 700; color: #64748b; font-family: var(--font-mono, monospace); text-transform: uppercase;">Engine</div>
                        <div style="font-size: 0.8rem; font-weight: 800; color: #050f33; margin-top: 2px;">99.9% Uptime</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid rgba(2, 132, 199, 0.12); border-radius: 10px; padding: 0.6rem 0.5rem; text-align: center;">
                        <div style="font-size: 0.65rem; font-weight: 700; color: #64748b; font-family: var(--font-mono, monospace); text-transform: uppercase;">Perimeter</div>
                        <div style="font-size: 0.8rem; font-weight: 800; color: #0284c7; margin-top: 2px;">Zero-Trust</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid rgba(22, 163, 74, 0.12); border-radius: 10px; padding: 0.6rem 0.5rem; text-align: center;">
                        <div style="font-size: 0.65rem; font-weight: 700; color: #64748b; font-family: var(--font-mono, monospace); text-transform: uppercase;">Edge Speed</div>
                        <div style="font-size: 0.8rem; font-weight: 800; color: #16a34a; margin-top: 2px;">&lt; 12ms Response</div>
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
    </section>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
