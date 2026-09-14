<?php
// lottie-test.php — Bulletproof Local Self-Hosted Lottie Animations Showcase
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAFly — 15 Premium Local Lottie Animations Suite</title>
    <!-- Core Lottie Web Engine (Airbnb / Bodymovin) -->
    <script src="js/vendor/lottie.min.js"></script>
    <style>
        :root {
            --bg: #070a12;
            --card-bg: #0f172a;
            --border: rgba(6, 182, 212, 0.3);
            --cyan: #06b6d4;
            --blue: #3b82f6;
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 2.5rem 1rem;
            line-height: 1.6;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        header h1 {
            font-size: 2.4rem;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        header p {
            color: var(--cyan);
            font-weight: 600;
            font-size: 1.15rem;
            max-width: 750px;
            margin: 0 auto;
        }

        .category-title {
            font-size: 1.5rem;
            color: #ffffff;
            margin: 3rem 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--cyan);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6);
            transition: transform 0.25s ease, border-color 0.25s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: var(--cyan);
        }

        .card-tag {
            align-self: flex-start;
            background: rgba(6, 182, 212, 0.15);
            color: var(--cyan);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .player-box {
            width: 100%;
            height: 220px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #020617;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
        }

        .lottie-target {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .lottie-target svg {
            width: 100% !important;
            height: 100% !important;
            max-height: 200px;
        }

        .card h3 {
            font-size: 1.2rem;
            color: #ffffff;
            margin-bottom: 0.4rem;
            text-align: center;
        }

        .card p {
            font-size: 0.88rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 1rem;
            min-height: 42px;
        }

        .local-path {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            width: 100%;
            font-family: monospace;
            font-size: 0.78rem;
            color: #38bdf8;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>🚀 RAFly — 15 Local Self-Hosted Lottie Animations</h1>
        <p>100% Offline & Same-Origin Local Asset Suite • Zero CDN 403 Errors</p>
    </header>

    <!-- SECTION 1: WEB DEVELOPMENT -->
    <h2 class="category-title">💻 1. Web Development & Architecture</h2>
    <div class="grid">
        <div class="card">
            <span class="card-tag">Web Dev #1 • Server Cloud Build</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/web-build.json"></div></div>
            <h3>Cloud Server & App Build</h3>
            <p>Full-stack web application development and cloud server rendering.</p>
            <div class="local-path">/assets/lottie/web-build.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Web Dev #2 • System Architecture</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/system-arch.json"></div></div>
            <h3>System Architecture Pipeline</h3>
            <p>Isometric web architecture engine and database pipeline model.</p>
            <div class="local-path">/assets/lottie/system-arch.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Web Dev #3 • Responsive Coding</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/code-render.json"></div></div>
            <h3>Web Studio Code Render</h3>
            <p>Responsive web app layout coding and modern frontend rendering.</p>
            <div class="local-path">/assets/lottie/code-render.json</div>
        </div>
    </div>

    <!-- SECTION 2: WEB SECURITY -->
    <h2 class="category-title">🔒 2. Web Security & Threat Surface</h2>
    <div class="grid">
        <div class="card">
            <span class="card-tag">Security #1 • Cyber Security Scan</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/security-shield.json"></div></div>
            <h3>Cyber Shield & WAF Scan</h3>
            <p>WAF firewall scanning, penetration testing and security audit pulse.</p>
            <div class="local-path">/assets/lottie/security-shield.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Security #2 • Encrypted Lock Shield</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/lock-shield.json"></div></div>
            <h3>Encrypted Security Lock</h3>
            <p>Data encryption, SSL session security and zero-trust verification.</p>
            <div class="local-path">/assets/lottie/lock-shield.json</div>
        </div>
    </div>

    <!-- SECTION 3: PERFORMANCE MARKETING -->
    <h2 class="category-title">📈 3. Performance Marketing & Ads ROI</h2>
    <div class="grid">
        <div class="card">
            <span class="card-tag">Marketing #1 • Growth Analytics Chart</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/growth-chart.json"></div></div>
            <h3>ROAS Analytics Dashboard</h3>
            <p>Ad campaign growth chart, conversion analytics, and ROAS matrix.</p>
            <div class="local-path">/assets/lottie/growth-chart.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Marketing #2 • Target Campaign Scale</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/ad-reach.json"></div></div>
            <h3>Target Ad Audience Reach</h3>
            <p>Meta & Google ads targeting, user funnel scale, and ad optimization.</p>
            <div class="local-path">/assets/lottie/ad-reach.json</div>
        </div>
    </div>

    <!-- SECTION 4: CONTENT CREATION -->
    <h2 class="category-title">🎬 4. Content Creation & Video Reels</h2>
    <div class="grid">
        <div class="card">
            <span class="card-tag">Content #1 • Video Reel Production</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/video-reel.json"></div></div>
            <h3>Video Reel Production Engine</h3>
            <p>Viral reel production, motion design editing, and content studio.</p>
            <div class="local-path">/assets/lottie/video-reel.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Content #2 • Creative Design Media</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/editorial.json"></div></div>
            <h3>Editorial Media & Copywriting</h3>
            <p>Brand design, editorial copywriting, and social media creative graphics.</p>
            <div class="local-path">/assets/lottie/editorial.json</div>
        </div>
    </div>

    <!-- SECTION 5: E-COMMERCE -->
    <h2 class="category-title">🛒 5. E-Commerce & Store Support</h2>
    <div class="grid">
        <div class="card">
            <span class="card-tag">Ecom #1 • Checkout & Cart Architecture</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/ecommerce.json"></div></div>
            <h3>Checkout & Store Architecture</h3>
            <p>E-commerce checkout reconciliation, inventory & payment gateway flow.</p>
            <div class="local-path">/assets/lottie/ecommerce.json</div>
        </div>
    </div>

    <!-- SECTION 6: UI MICRO-INTERACTIONS -->
    <h2 class="category-title">🎯 6. UI Micro-Interactions, Cursor & Loading Spinners</h2>
    <div class="grid">
        <div class="card">
            <span class="card-tag">Micro #1 • HUD Telemetry Radar</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/radar.json"></div></div>
            <h3>Telemetry Signal Radar</h3>
            <p>Continuous scanning radar pulse for live SLA telemetry HUD card.</p>
            <div class="local-path">/assets/lottie/radar.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Micro #2 • Cyber Loading Ring</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/loader.json"></div></div>
            <h3>Cyber Futuristic Loader</h3>
            <p>Loading spinner animation for page transitions and AJAX requests.</p>
            <div class="local-path">/assets/lottie/loader.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Micro #3 • Mouse Pointer Pulse</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/cursor.json"></div></div>
            <h3>Mouse Cursor Interactive Glow</h3>
            <p>Animated mouse cursor click pulse and interactive user hover feedback.</p>
            <div class="local-path">/assets/lottie/cursor.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Micro #4 • Step Verification Check</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/checkmark.json"></div></div>
            <h3>Verification Step Checkmark</h3>
            <p>Animated checkmark for contact form brief verification step.</p>
            <div class="local-path">/assets/lottie/checkmark.json</div>
        </div>
        <div class="card">
            <span class="card-tag">Micro #5 • 24/7 SLA Support</span>
            <div class="player-box"><div class="lottie-target" data-path="/assets/lottie/support.json"></div></div>
            <h3>Client Portal & 24/7 SLA Support</h3>
            <p>Communication & live customer support response animation.</p>
            <div class="local-path">/assets/lottie/support.json</div>
        </div>
    </div>

    <footer style="margin-top: 4rem; text-align: center; color: var(--text-muted); border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem;">
        <p>⚡ RAFly Motion Suite • Self-Hosted Local Lottie Asset Fleet</p>
    </footer>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const targets = document.querySelectorAll('.lottie-target');
    targets.forEach(el => {
        const path = el.getAttribute('data-path');
        if (path && window.lottie) {
            window.lottie.loadAnimation({
                container: el,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: path
            });
        }
    });
});
</script>

</body>
</html>
