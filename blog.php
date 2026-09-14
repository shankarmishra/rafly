<?php
require __DIR__ . '/inc/bootstrap.php';

$page = [
    'id'        => 'blog',
    'title'     => 'Editorial Intelligence Archive | RAFly Digital Growth Partner',
    'desc'      => 'Technical insights, web application engineering, cyber security hardening protocols, and performance marketing strategies.',
    'bodyClass' => 'page-blog',
    'styles'    => ['home', 'home-scenes', 'blog'],
    'module'    => 'home',
];

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Blog', 'url' => '/blog'],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">

    <!-- HERO HEADER: EDITORIAL INTELLIGENCE ARCHIVE -->
    <section class="section hero sig-hero blueprint-canvas" style="min-height: 70vh; display: flex; align-items: center; padding-block: 4rem;">
        <div class="container container-narrow text-center">
            <div class="machined-badge machined-badge-purple" style="margin-bottom: 1rem;">
                <span class="glow-dot-active"></span> EDITORIAL INTELLIGENCE ARCHIVE
            </div>
            <h1 style="font-size: clamp(2.5rem, 4.5vw, 3.8rem); font-weight: 800; color: #050f33; line-height: 1.1; margin-bottom: 1rem; letter-spacing: -0.02em;">
                TECHNICAL INSIGHTS &amp; <span style="color: #0a63ff;">ENGINEERING ARTICLES</span>
            </h1>
            <p class="lead" style="max-width: 620px; margin-inline: auto; font-size: 1.08rem; color: #475569; line-height: 1.6; margin-bottom: 1.75rem;">
                Architectural breakdowns, zero-trust security reviews, conversion telemetry analysis, and digital growth protocols.
            </p>
            
            <div style="display: flex; justify-content: center; align-items: center; margin-block: 1rem; width: 100%; height: 160px;">
                <lottie-player
                    src="/assets/lottie/editorial.json"
                    background="transparent"
                    speed="1"
                    style="width: 100%; max-width: 260px; height: 160px;"
                    loop
                    autoplay
                    aria-hidden="true">
                </lottie-player>
            </div>
            <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
                <span class="telemetry-pill-mono">📚 40+ Technical Articles</span>
                <span class="telemetry-pill-mono">⚙️ PHP 8.3 &amp; Redis Specs</span>
                <span class="telemetry-pill-mono">🔒 Zero-Trust Audits</span>
            </div>
        </div>
    </section>

    <!-- FEATURED ARTICLE & TOPIC INDEX -->
    <section class="section" style="padding-block: 5rem; background: #ffffff;">
        <div class="container">
            <div class="sec-head-split" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.4rem;">FEATURED PUBLICATION</div>
                    <h2 style="font-size: 2rem; font-weight: 800; color: #050f33; margin: 0;">EDITORIAL SELECTION</h2>
                </div>
                <div class="topic-index-chips" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <span class="machined-badge machined-badge-blue">ALL ARTICLES</span>
                    <span class="machined-badge machined-badge-cyan">WEB DEVELOPMENT</span>
                    <span class="machined-badge machined-badge-red">SECURITY AUDITS</span>
                    <span class="machined-badge machined-badge-green">GROWTH TELEMETRY</span>
                </div>
            </div>

            <!-- FEATURED ARTICLE CARD -->
            <div class="machined-card" style="padding: 2.5rem; margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <span class="machined-badge machined-badge-blue">FEATURED READ &bull; 8 MIN READ</span>
                    <span class="telemetry-pill-mono">SEPTEMBER 2026</span>
                </div>
                <h3 style="font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 800; color: #050f33; margin-bottom: 1rem; line-height: 1.25;">
                    Why Single-Page Applications Fail Without Edge Caching: A PHP 8.3 &amp; Redis Architecture Study
                </h3>
                <p style="font-size: 1.05rem; color: #475569; line-height: 1.65; margin-bottom: 1.75rem; max-width: 850px;">
                    An in-depth technical analysis comparing traditional SPA client-rendering overhead against server-rendered PHP 8.3 decoupled caching. How sub-50ms LCP directly impacts conversion funnel metrics.
                </p>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <span class="telemetry-pill-mono">BY RAFLY CORE ENGINEERING TEAM</span>
                    <a class="btn btn-primary" href="/blog">Read Article <?= icon('arrow-right') ?></a>
                </div>
            </div>

            <!-- ARTICLE GRID -->
            <div class="grid grid-3" style="gap: 1.5rem;">
                <div class="machined-card">
                    <span class="machined-badge machined-badge-red" style="margin-bottom: 0.75rem;">SECURITY &bull; 5 MIN READ</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.6rem; line-height: 1.35;">Zero-Trust Password Hashing with Argon2id</h3>
                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.55; margin-bottom: 1.25rem;">Migrating legacy MD5/Bcrypt authentication tables to memory-hard Argon2id parameters.</p>
                    <a href="/blog" style="font-family: var(--font-mono); font-size: 0.8rem; color: #0a63ff; font-weight: 700;">Read Article →</a>
                </div>

                <div class="machined-card">
                    <span class="machined-badge machined-badge-green" style="margin-bottom: 0.75rem;">GROWTH &bull; 6 MIN READ</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.6rem; line-height: 1.35;">Server-Side Meta CAPI vs Client-Side Pixel</h3>
                    <p style="font-size: 0.88rem; color: #475569; line-height: 1.55; margin-bottom: 1.25rem;">How iOS 14.5+ ad blocking degrades attribution and how server webhooks restore signal accuracy.</p>
                    <a href="/blog" style="font-family: var(--font-mono); font-size: 0.8rem; color: #0a63ff; font-weight: 700;">Read Article →</a>
                </div>

                <div class="machined-card">
                    <span class="machined-badge machined-badge-purple" style="margin-bottom: 0.75rem;">CONTENT &bull; 4 MIN READ</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #050f33; margin-bottom: 0.6rem; line-height: 1.35;">Optimizing 9:16 Video Pipelines for Retention</h3>
                    <p style="font-size: 0.88rem; color: #475569; line-height: 1.55; margin-bottom: 1.25rem;">A visual breakdown of NLE editing timelines, sound design triggers, and 3-second hook structures.</p>
                    <a href="/blog" style="font-family: var(--font-mono); font-size: 0.8rem; color: #0a63ff; font-weight: 700;">Read Article →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER / INTAKE CONSOLE -->
    <section class="section blueprint-canvas" id="intake" style="padding-block: 5rem;">
        <div class="container">
            <div class="sec-head sec-head-center" style="margin-bottom: 2.5rem;">
                <div class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">EDITORIAL SUBSCRIPTION &amp; INTAKE</div>
                <h2 style="font-size: 2.2rem; font-weight: 800; color: #050f33;">SUBSCRIBE TO TECHNICAL BRIEFINGS OR INITIATE A PROJECT</h2>
            </div>
            <?php $formId = 'blogLeadForm'; require __DIR__ . '/partials/lead-form.php'; ?>
        </div>
    </section>

</main>

<?php require __DIR__ . '/partials/tail.php'; ?>
