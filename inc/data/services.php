<?php
/**
 * SEED DATA — Complete Art-Directed Service Visual Systems & Structured Data.
 *
 * Drives all 5 service detail pages:
 * 01 Web Development
 * 02 Web Security
 * 03 Marketing & Advertisement
 * 04 Content Creation
 * 05 E-Commerce Support
 */

return [
    'web-development' => [
        'title' => 'Web Development',
        'icon'  => 'code',
        'key'   => 'web',
        'wide'  => true,
        'badge' => 'SOFTWARE ARCHITECTURE ENGINE',
        'tagline' => 'Sites and web apps that load fast, read clearly, and do not fall over as you grow.',
        'intro' => 'We engineer custom, decoupled web systems and web applications designed for sub-second load times, clean information architecture, and long-term maintainability.',
        'highlights' => [
            'Bespoke Web Applications',
            'Decoupled API Architecture',
            'Core Web Vitals Optimization',
            'Zero-Bloat Frontend Stacks',
            'Full IP & Repository Transfer'
        ],
        'diagnostics' => [
            ['code' => 'ERR_LEGACY_STACK',  'title' => 'Unmaintained Legacy Codebase', 'desc' => 'Your site was built years ago on brittle plugins or outdated frameworks nobody wants to touch.'],
            ['code' => 'ERR_FRICTION_LOOP', 'title' => 'High Editorial Friction',     'desc' => 'Simple copy edits or new landing pages turn into week-long developer back-and-forth cycles.'],
            ['code' => 'ERR_MOBILE_BREAK',  'title' => 'Mobile Layout Failure',        'desc' => 'The site renders decently on laptops but falls apart on mobile viewports where most traffic arrives.'],
            ['code' => 'ERR_LATENCY_SPIKE', 'title' => 'Core Web Vitals Degradation', 'desc' => 'Slow LCP and bad interaction delays lose search engine rankings and lower conversion rates.']
        ],
        'bento' => [
            'main' => [
                'handle' => 'ARCHITECTURE CORE',
                'title'  => 'Decoupled Full-Stack Engineering',
                'desc'   => 'We separate client presentation layers from backend services, allowing custom web apps to handle high traffic spikes without latency penalties.',
                'specs'  => ['Semantic HTML5 & Modern CSS3', 'Server-Side Rendering & Hydration', 'Sub-100ms First Input Latency']
            ],
            'medium' => [
                ['handle' => 'BACKEND & APIS', 'title' => 'Typed API & Micro-Services', 'desc' => 'PHP 8.3 & Node backend endpoints with strict input validation, rate limiting, and async job queues.', 'specs' => ['Strict Input Validation', 'Argon2id Auth Guards', 'Async Background Queues']],
                ['handle' => 'DATA & EDGE',   'title' => 'Indexed Data & Edge Caching', 'desc' => 'Relational schemas in PostgreSQL or MySQL paired with Redis key-value caching and Cloudflare CDNs.', 'specs' => ['PostgreSQL Query Tuning', 'Redis Cache Layer', 'Edge CDN Asset Delivery']]
            ],
            'compact' => [
                ['handle' => 'PERFORMANCE', 'title' => 'Web Vitals Pass',  'desc' => 'Granular asset compression, web-font subsetting, and Brotli encoding.'],
                ['handle' => 'SECURITY',    'title' => 'Built-In Hardening','desc' => 'CSRF tokens, HSTS headers, and sanitized query parameters.'],
                ['handle' => 'HANDOVER',    'title' => 'Git & Code Ownership', 'desc' => 'Full IP transfer with clean staging environments and documentation.']
            ]
        ],
        'system_map' => [
            'eyebrow' => '// SYSTEM ARCHITECTURE MAP',
            'title'   => 'Living Software Architecture Engine',
            'desc'    => 'An interactive visualization of data packet flow between the client viewport and edge infrastructure.',
            'nodes'   => [
                ['id' => 'user',     'name' => 'USER SESSION',     'label' => '[CLIENT VIEWPORT]',   'icon' => 'monitor',  'role' => 'Responsive client interface rendering across mobile & desktop.', 'tech' => 'HTML5 / Modern JS'],
                ['id' => 'frontend', 'name' => 'FRONTEND ENGINE',  'label' => '[UI HYDRATION]',      'icon' => 'layers',   'role' => 'Optimized DOM rendering, semantic accessibility, and zero bloat.', 'tech' => 'SSR / CSS Tokens'],
                ['id' => 'api',      'name' => 'API GATEWAY',      'label' => '[ROUTING & AUTH]',    'icon' => 'cpu',      'role' => 'Sanitized REST/GraphQL endpoints with rate limiting & CORS.', 'tech' => 'PHP 8.3 Services'],
                ['id' => 'db',       'name' => 'DATABASE NODE',    'label' => '[INDEXED STORAGE]',   'icon' => 'database', 'role' => 'Relational database schemas with in-memory Redis caching.', 'tech' => 'PostgreSQL / Redis'],
                ['id' => 'edge',     'name' => 'EDGE CDN LAYER',   'label' => '[GLOBAL DISTRIBUTION]','icon' => 'globe',   'role' => 'Global static asset delivery and edge HTTP/3 transport.', 'tech' => 'Cloudflare Edge']
            ]
        ],
        'process' => [
            ['step' => '01', 'title' => 'DISCOVER', 'time' => '2-3 Days',  'desc' => 'We audit existing codebases, evaluate technical bottlenecks, and map data dependencies.'],
            ['step' => '02', 'title' => 'PLAN',     'time' => '3-5 Days',  'desc' => 'We establish page wireframes, API specs, database schemas, and scope in writing.'],
            ['step' => '03', 'title' => 'BUILD',    'time' => '2-6 Weeks', 'desc' => 'Iterative frontend & backend engineering with live staging previews and automated tests.'],
            ['step' => '04', 'title' => 'LAUNCH',   'time' => '1 Week',    'desc' => 'Production deployment, domain migration, baseline security check, and 30-day support.']
        ],
        'artifacts' => [
            ['type' => 'SPEC SHEET',    'title' => 'Technical Architecture Blueprint', 'desc' => 'Comprehensive data schemas, component tree, and API route documentation.', 'tag' => 'v2.4 Final Spec'],
            ['type' => 'CODE REPO',     'title' => 'Git Repository & Deployment Pipeline', 'desc' => 'Version-controlled codebase with staging preview links and CI/CD scripts.', 'tag' => 'Branch: main'],
            ['type' => 'AUDIT REPORT',  'title' => 'Core Web Vitals Verification',     'desc' => 'Lighthouse performance report verifying sub-100ms FID and 95+ score.', 'tag' => 'Score: 98/100']
        ],
        'tools' => [
            ['icon' => 'code',     'label' => 'PHP 8.3 &amp; Laravel',    'role' => 'Robust Backend Logic'],
            ['icon' => 'terminal', 'label' => 'JavaScript (ESNext)',      'role' => 'Client Micro-Interactions'],
            ['icon' => 'layers',   'label' => 'HTML5 / CSS3 Tokens',      'role' => 'Semantic Layout Systems'],
            ['icon' => 'database', 'label' => 'PostgreSQL / MySQL',        'role' => 'Relational Data Storage'],
            ['icon' => 'globe',    'label' => 'Cloudflare CDNs',          'role' => 'Edge Asset Delivery'],
            ['icon' => 'gauge',    'label' => 'Core Web Vitals',          'role' => 'Speed & Latency Audits']
        ],
        'outcomes' => [
            'A lightning-fast, custom website your team can update without filing developer tickets.',
            'Clean codebase with zero hidden technical debt or unnecessary plugin bloat.',
            'Full intellectual property ownership transferred directly upon project completion.',
            'Significant improvements in search engine visibility, mobile retention, and conversion.'
        ],
        'who_it_is_for' => [
            ['title' => 'Growing Companies',   'desc' => 'Businesses outgrowing off-the-shelf templates and requiring custom presentation.', 'fit' => 'Outgrown basic templates'],
            ['title' => 'SaaS Platforms',      'desc' => 'Product teams requiring client portals, custom dashboards, or bespoke customer workflows.', 'fit' => 'Custom API & DB requirements'],
            ['title' => 'High-Traffic Stores', 'desc' => 'Brands seeking high-speed conversion landing pages to lower ad bounce rates.', 'fit' => 'Speed & conversion focus']
        ],
        'boundaries' => [
            ['title' => 'Native iOS / Android Apps',  'desc' => 'We specialize in responsive browser applications. Native mobile app development is best handled by dedicated mobile studios.'],
            ['title' => 'Brand Design From Scratch', 'desc' => 'We implement existing brand guidelines cleanly. Full visual logo identity design is handled separately.'],
            ['title' => 'Legacy Mainframe Migration', 'desc' => 'We build modern web layers; migrating legacy COBOL/AS400 mainframes is outside our core focus.']
        ],
        'faqs' => [
            ['q' => 'Do you build custom websites or use pre-made templates?', 'a' => 'We build custom, bespoke websites tailored to your exact business requirements. We write clean, semantic code that avoids unnecessary framework bloat.'],
            ['q' => 'Who owns the code and intellectual property after launch?', 'a' => 'You do. Full IP ownership and source code access are transferred to your company upon project completion.'],
            ['q' => 'How long does a web development project take?', 'a' => 'Discovery takes 2-3 days, scoping takes 3-5 days, and development typically runs 2-6 weeks depending on project complexity.'],
            ['q' => 'How do you handle site performance and mobile responsiveness?', 'a' => 'Every layout is designed mobile-first and tested on real devices. We optimize assets, script execution, and caching to achieve high Core Web Vitals scores.']
        ]
    ],

    'web-security' => [
        'title' => 'Web Security',
        'icon'  => 'shield',
        'key'   => 'security',
        'wide'  => true,
        'badge' => 'DEFENSIVE PERIMETER SYSTEM',
        'tagline' => 'A practical look at what someone probing your site would find first — and what to fix before they do.',
        'intro' => 'We help businesses harden their digital presence through practical security audits, vulnerability mitigation, and baseline infrastructure hardening.',
        'highlights' => [
            'Surface Vulnerability Audits',
            'Form & API Input Sanitization',
            'HTTP Security Headers (CSP/HSTS)',
            'Session & Auth Hardening',
            'Encrypted Backup Resilience'
        ],
        'landing_links' => [
            [
                'title' => 'Emergency Security Response',
                'desc'  => 'Under active attack or experiencing a security breach? Access 24/7 urgent malware cleanup and site recovery.',
                'url'   => '/landing/security-emergency',
                'badge' => '24/7 EMERGENCY'
            ],
            [
                'title' => 'Free Technical & Security Audit',
                'desc'  => 'Get a free forensic audit of your site\'s surface vulnerabilities, SSL configuration, and performance bottlenecks.',
                'url'   => '/landing/website-audit',
                'badge' => 'FREE AUDIT'
            ]
        ],
        'diagnostics' => [
            ['code' => 'ERR_UNCHECKED_INPUT', 'title' => 'Unvalidated Form Inputs',   'desc' => 'Forms collect customer data without strict sanitization, risking SQLi or XSS injections.'],
            ['code' => 'ERR_STALE_DEP',       'title' => 'Outdated Dependencies',     'desc' => 'CMS plugins or libraries have gone unpatched for months with known CVE risks.'],
            ['code' => 'ERR_EXPOSED_HEADER',  'title' => 'Missing Security Headers',   'desc' => 'Missing CSP, HSTS, or anti-clickjacking headers leave browsers vulnerable to exploitation.'],
            ['code' => 'ERR_UNVERIFIED_BAK',  'title' => 'Unverified Backup Integrity', 'desc' => 'Backups are assumed to work but have never been tested in a real restoration dry-run.']
        ],
        'bento' => [
            'main' => [
                'handle' => 'PERIMETER CORE',
                'title'  => 'Layered Defense-in-Depth Architecture',
                'desc'   => 'Security is not a single tool—it is a continuous defense model protecting every layer from public DNS edge down to database queries.',
                'specs'  => ['Enforced TLS 1.3 Encryption', 'Strict Content Security Policy (CSP)', 'Sanitized Parameter Handling']
            ],
            'medium' => [
                ['handle' => 'INPUT HARMONY', 'title' => 'Form & API Hardening',      'desc' => 'Securing all entry endpoints against SQL injection, XSS vectors, parameter tampering, and CSRF attacks.', 'specs' => ['Strict Type Validation', 'Anti-CSRF Token Checks', 'Prepared SQL Queries']],
                ['handle' => 'AUTH & SESSIONS','title' => 'Authentication Security',  'desc' => 'Hardening login flows with rate-limiting, Argon2id password hashing, and secure session management.', 'specs' => ['Brute-Force Rate Limiting', 'Argon2id Hashing', 'Session Token Lifetimes']]
            ],
            'compact' => [
                ['handle' => 'DEPENDENCY', 'title' => 'CVE Dependency Hygiene', 'desc' => 'Auditing third-party libraries and CMS plugins for known vulnerabilities.'],
                ['handle' => 'HEADERS',    'title' => 'Transport Security',    'desc' => 'Configuring HSTS, Referrer-Policy, and X-Frame-Options.'],
                ['handle' => 'BACKUP',     'title' => 'Recovery Verification', 'desc' => 'Verifying off-site encrypted backup routines and recovery timing.']
            ]
        ],
        'system_map' => [
            'eyebrow' => '// DEFENSIVE PERIMETER MAP',
            'title'   => 'Living Security Perimeter System',
            'desc'    => 'An interactive visualization of defense layers inspecting incoming web requests.',
            'nodes'   => [
                ['id' => 'internet', 'name' => 'PUBLIC INTERNET',   'label' => '[PUBLIC WEB FLOW]',   'icon' => 'globe',    'role' => 'Incoming traffic filtration separating legitimate users from automated bots.', 'tech' => 'IPv4/IPv6 Filter'],
                ['id' => 'edge',     'name' => 'EDGE & TLS GATEWAY', 'label' => '[TLS 1.3 SHIELD]',    'icon' => 'lock',     'role' => 'Enforced HSTS, TLS 1.3 encryption, and geo-ip rate throttling.', 'tech' => 'Edge Certificate'],
                ['id' => 'waf',      'name' => 'WAF RULE ENGINE',    'label' => '[WAF ACTIVE]',       'icon' => 'shield',   'role' => 'Rules blocking SQLi, XSS vectors, and brute-force payloads.', 'tech' => 'WAF Inspection'],
                ['id' => 'app',      'name' => 'SESSION GUARD',      'label' => '[SESSION HARMONY]',  'icon' => 'cpu',      'role' => 'Sanitized inputs, strict session tokens, and Argon2id password hashing.', 'tech' => 'Strict Application'],
                ['id' => 'db',       'name' => 'DATABASE STORAGE',   'label' => '[HARDENED STORAGE]', 'icon' => 'database', 'role' => 'Prepared statements, least-privilege DB accounts, and encrypted backups.', 'tech' => 'Encrypted DB']
            ]
        ],
        'process' => [
            ['step' => '01', 'title' => 'DISCOVER', 'time' => '2-3 Days',  'desc' => 'We define exact target scope, establish rules of engagement, and execute NDAs.'],
            ['step' => '02', 'title' => 'PLAN',     'time' => '3-5 Days',  'desc' => 'Non-disruptive security inspection of headers, code patterns, and administrative endpoints.'],
            ['step' => '03', 'title' => 'BUILD',    'time' => '2-6 Weeks', 'desc' => 'Direct implementation of security fixes, header configs, and code hardening.'],
            ['step' => '04', 'title' => 'LAUNCH',   'time' => '1 Week',    'desc' => 'Verification pass to confirm all vulnerabilities are closed, followed by final report handover.']
        ],
        'artifacts' => [
            ['type' => 'AUDIT REPORT', 'title' => 'Surface Vulnerability Assessment', 'desc' => 'Prioritized breakdown of exposed endpoints, header configs, and risk severity.', 'tag' => 'Status: Audited'],
            ['type' => 'SECURITY LOG', 'title' => 'Header & WAF Configuration Spec',  'desc' => 'Exact CSP directives, HSTS setup, and rate-limiting rule definitions.', 'tag' => 'HSTS: Active'],
            ['type' => 'RECOVERY SOP', 'title' => 'Disaster Recovery & Backup Playbook','desc' => 'Step-by-step restoration procedure verified against off-site encrypted storage.', 'tag' => 'Verified Dry-Run']
        ],
        'tools' => [
            ['icon' => 'lock',     'label' => 'SSL &amp; TLS 1.3 Config', 'role' => 'Transport Encryption'],
            ['icon' => 'shield',   'label' => 'WAF &amp; Rule Engines',   'role' => 'Payload Filtering'],
            ['icon' => 'package',  'label' => 'Dependency CVE Scanners', 'role' => 'Vulnerability Checks'],
            ['icon' => 'users',    'label' => 'Argon2id &amp; Session Guards', 'role' => 'Authentication Hardening'],
            ['icon' => 'search',   'label' => 'HTTP Header Audits',       'role' => 'Browser Vector Defense'],
            ['icon' => 'database', 'label' => 'Encrypted Backup Systems', 'role' => 'Data Resilience']
        ],
        'outcomes' => [
            'A clear, prioritized breakdown of web security risks and actionable recommendations.',
            'Immediate mitigation of common attack vectors (XSS, SQLi, CSRF, Brute Force).',
            'Verified backup procedures to ensure rapid business continuity in any incident.',
            'Technical confidence that public web infrastructure adheres to modern security standards.'
        ],
        'who_it_is_for' => [
            ['title' => 'Data-Collecting Sites', 'desc' => 'Sites handling customer inquiries, lead capture, user registrations, or sensitive forms.', 'fit' => 'Active form pipelines'],
            ['title' => 'Client Portals',        'desc' => 'Platforms requiring secure user logins, payment integrations, and data privacy.', 'fit' => 'Authenticated customer portals'],
            ['title' => 'B2B Vendors',          'desc' => 'Companies needing to harden infrastructure ahead of client security reviews.', 'fit' => 'Vendor compliance prep']
        ],
        'boundaries' => [
            ['title' => 'Formal Penetration Testing', 'desc' => 'Certified CREST/OSCP penetration testing with formal compliance sign-offs requires specialized auditing firms.'],
            ['title' => 'Compliance Sign-Offs',       'desc' => 'We harden systems to best-practice standards, but we do not issue formal ISO 27001 or SOC 2 audit certificates.'],
            ['title' => 'Active Incident Response',   'desc' => 'If your server is undergoing an active DDoS attack right now, hosting security teams can intervene faster.']
        ],
        'faqs' => [
            ['q' => 'Will your security audit cause downtime or disrupt our site?', 'a' => 'No. All security audits and reviews are non-disruptive and conducted safely on live or staging environments without affecting user availability.'],
            ['q' => 'Do you implement the security fixes or only provide a report?', 'a' => 'We do both. We can directly implement the necessary code and header changes, or hand off the detailed technical report to your internal team.'],
            ['q' => 'How does RAFly approach technical security honesty?', 'a' => 'We avoid marketing hype like "100% hack-proof". We focus on realistic defense-in-depth, reducing attack surface area, and hardening high-risk vectors.'],
            ['q' => 'Is web security included with RAFly development packages?', 'a' => 'Yes. Baseline security (CSRF protection, sanitized inputs, secure sessions) is included with all web development builds. This standalone service provides deeper infrastructure hardening.']
        ]
    ],

    'marketing-advertisement' => [
        'title' => 'Marketing & Advertisement',
        'icon'  => 'trending-up',
        'key'   => 'marketing',
        'wide'  => false,
        'badge' => 'GROWTH INTELLIGENCE MATRIX',
        'tagline' => 'Campaigns built around who is actually buying, reported in plain language.',
        'intro' => 'We design structured digital marketing campaigns that align messaging, audience targeting, and analytics so your acquisition channels perform with clarity.',
        'highlights' => [
            'Paid Search & Social Strategy',
            'Audience Intent Profiling',
            'Landing Page Conversion Alignment',
            'GA4 & Server-Side Event Tracking',
            'Transparent Spend-to-Lead Reporting'
        ],
        'diagnostics' => [
            ['code' => 'ERR_BLIND_SPEND',   'title' => 'Unattributed Ad Spend',      'desc' => 'Money leaves your ad accounts monthly without clear visibility into which channel drives actual leads.'],
            ['code' => 'ERR_BOUNCE_DROPOFF', 'title' => 'Landing Page Disconnect',    'desc' => 'Ad messaging promises one thing while the destination page delivers generic copy, losing clicks.'],
            ['code' => 'ERR_VANITY_METRIC',  'title' => 'Vanity Metric Reports',       'desc' => 'Monthly reports focus on impressions and clicks rather than verified customer inquiries or sales.'],
            ['code' => 'ERR_BROKEN_TRACKING','title' => 'Unreliable Event Conversion','desc' => 'Google Analytics 4 is missing conversion goals or relying entirely on third-party cookies.']
        ],
        'bento' => [
            'main' => [
                'handle' => 'ACQUISITION CORE',
                'title'  => 'Connected Acquisition Funnels',
                'desc'   => 'Marketing is an interconnected funnel. We align every step—from initial search intent touchpoint to landing page checkout.',
                'specs'  => ['Commercial Search Intent Research', 'Targeted Search & Social Campaigns', 'Verified Conversion Attribution']
            ],
            'medium' => [
                ['handle' => 'AD COPY ALIGNMENT', 'title' => 'Ad & Landing Page Copy',     'desc' => 'Writing tight ad copy variations and matching landing page content so expectations agree.', 'specs' => ['Google & Meta Copy Variants', 'Dedicated Landing Messaging', 'Clear Call-to-Action Flows']],
                ['handle' => 'ANALYTICS MATRIX',  'title' => 'GA4 & Server-Side Tracking', 'desc' => 'Configuring Google Analytics 4 and Tag Manager events before ad spend starts so every lead is tracked.', 'specs' => ['Server-Side Tagging', 'Custom Lead Event Goals', 'Form & Click Attribution']]
            ],
            'compact' => [
                ['handle' => 'OPTIMIZATION', 'title' => 'Continuous Pruning',    'desc' => 'Pruning negative search queries and testing copy variants.'],
                ['handle' => 'REPORTING',    'title' => 'Plain-Language Reports','desc' => 'Monthly reports detailing spend, lead counts, and next steps.'],
                ['handle' => 'OWNERSHIP',    'title' => '100% Account Control',  'desc' => 'All campaigns built inside your company accounts.']
            ]
        ],
        'system_map' => [
            'eyebrow' => '// GROWTH INTELLIGENCE MAP',
            'title'   => 'Living Growth Intelligence Engine',
            'desc'    => 'An interactive visualization of customer intent flowing into acquisition channels.',
            'nodes'   => [
                ['id' => 'audience',   'name' => 'INTENT SEGMENTS',   'label' => '[AUDIENCE PROFILING]', 'icon' => 'users',      'role' => 'Identifying active search intent and target customer profiles.', 'tech' => 'Intent Keywords'],
                ['id' => 'campaign',   'name' => 'CHANNEL ARCHITECTURE','label' => '[PAID SEARCH & SOCIAL]','icon' => 'megaphone',  'role' => 'Structured Search & Social campaigns matching margins.', 'tech' => 'Google / Meta Ads'],
                ['id' => 'traffic',    'name' => 'QUALIFIED FLOW',    'label' => '[UTM ROUTING]',        'icon' => 'trending-up','role' => 'Routing targeted clicks directly to specialized landing pages.', 'tech' => 'Landing Funnels'],
                ['id' => 'engagement', 'name' => 'LANDING ENGINE',    'label' => '[CONVERSION UI]',      'icon' => 'layout',     'role' => 'Clear value proposition, proof elements, and low-friction forms.', 'tech' => 'High-Speed UI'],
                ['id' => 'retention',  'name' => 'ATTRIBUTION ACTIVE','label' => '[ANALYTICS MATRIX]',   'icon' => 'pie-chart',  'role' => 'Accurate event tracking and continuous campaign refinement.', 'tech' => 'GA4 / Tag Manager']
            ]
        ],
        'process' => [
            ['step' => '01', 'title' => 'DISCOVER', 'time' => '2-3 Days',  'desc' => 'We review your target margins, current ad account setups, and historical performance.'],
            ['step' => '02', 'title' => 'PLAN',     'time' => '3-5 Days',  'desc' => 'We map channel allocation, messaging angles, and tracking tags before spending budget.'],
            ['step' => '03', 'title' => 'BUILD',    'time' => '2-6 Weeks', 'desc' => 'Ad copy, landing pages, tracking tags, and account structures go live in a staged rollout.'],
            ['step' => '04', 'title' => 'LAUNCH',   'time' => 'Ongoing',   'desc' => 'Regular search query negative pruning, copy testing, and budget optimization.']
        ],
        'artifacts' => [
            ['type' => 'STRATEGY BRIEF','title' => 'Audience Intent & Keyword Architecture', 'desc' => 'Commercial search intent queries, negative terms, and campaign buckets.', 'tag' => 'Targeting: Active'],
            ['type' => 'COPY PACKET',   'title' => 'Ad Variants & Landing Page Matrix',     'desc' => 'Written search ads, social creative copy, and matching landing section text.', 'tag' => 'Copy Approved'],
            ['type' => 'GA4 SPEC',      'title' => 'Server-Side Analytics & Tag Spec',       'desc' => 'Configured GTM tags, GA4 custom conversion events, and UTM parameters.', 'tag' => 'Tracking Live']
        ],
        'tools' => [
            ['icon' => 'trending-up', 'label' => 'Google Search &amp; Display', 'role' => 'Intent Search Ads'],
            ['icon' => 'facebook',    'label' => 'Meta Ads Manager',         'role' => 'Paid Social Targeting'],
            ['icon' => 'pie-chart',   'label' => 'Google Analytics 4',       'role' => 'Conversion Attribution'],
            ['icon' => 'settings',    'label' => 'Google Tag Manager',       'role' => 'Server-Side Tagging'],
            ['icon' => 'search',      'label' => 'Google Search Console',    'role' => 'Organic Search Queries'],
            ['icon' => 'mail',        'label' => 'Email Marketing',          'role' => 'Retention Workflows']
        ],
        'outcomes' => [
            'Full visibility into campaign spend, qualified lead volume, and cost-per-lead.',
            'Complete ownership of all ad accounts, tags, and analytics assets.',
            'Ad campaigns aligned directly with dedicated, high-speed landing pages.',
            'Transparent reporting focused on business decisions rather than vanity metrics.'
        ],
        'who_it_is_for' => [
            ['title' => 'B2B Services',      'desc' => 'Companies seeking high-intent lead generation for consultative sales.', 'fit' => 'High lifetime customer value'],
            ['title' => 'Regional Brands',   'desc' => 'Businesses looking to dominate specific geographic search intent with targeted ads.', 'fit' => 'Geo-targeted acquisition'],
            ['title' => 'E-Commerce Sellers', 'desc' => 'Storefronts seeking structured paid search and social campaigns aligned with margins.', 'fit' => 'Direct acquisition funnels']
        ],
        'boundaries' => [
            ['title' => 'Guaranteed Rankings or Leads', 'desc' => 'Ad platforms are auction systems; nobody can honestly guarantee fixed lead costs or search positions.'],
            ['title' => 'Enterprise Media Buying Desks','desc' => 'Multi-million dollar national broadcast buying is best handled by traditional media buying desks.'],
            ['title' => 'Influencer Talent Management', 'desc' => 'We focus on search, social ads, and analytics rather than talent booking or PR outreach.']
        ],
        'faqs' => [
            ['q' => 'Who owns the ad accounts and data?', 'a' => 'You do. All campaigns are built directly inside your company’s Google Ads and Meta accounts so you retain all data and history.'],
            ['q' => 'Is ad spend included in RAFly’s service fee?', 'a' => 'No. Platform ad spend is paid directly to Google or Meta through your credit card; our fee covers strategy, copy, management, and analytics.'],
            ['q' => 'How quickly will we see results from paid campaigns?', 'a' => 'Paid search campaigns can generate traffic immediately upon launch. Initial conversion optimization and audience refinement typically stabilize over 2-4 weeks.'],
            ['q' => 'How does RAFly handle conversion tracking accuracy?', 'a' => 'We implement server-side tracking and GA4 custom events to ensure form submissions, calls, and sales are measured accurately without relying solely on third-party cookies.']
        ]
    ],

    'content-creation' => [
        'title' => 'Content Creation',
        'icon'  => 'pencil',
        'key'   => 'content',
        'wide'  => false,
        'badge' => 'CREATIVE PRODUCTION OS',
        'tagline' => 'Copy that says what you do, in your words, without the filler everyone skims past.',
        'intro' => 'We create articulate, search-aware website copy and brand content that presents your value proposition confidently and builds audience trust.',
        'highlights' => [
            'Website & Service Page Copy',
            'Search-Aware Editorial Architecture',
            'Social & Campaign Copy Packets',
            'Brand Voice & Messaging Frameworks',
            'Scannable Mobile Formatting'
        ],
        'diagnostics' => [
            ['code' => 'ERR_JARGIN_OVERLOAD', 'title' => 'Dense Agency Jargon',       'desc' => 'Your homepage takes three paragraphs of generic jargon to state what the business actually does.'],
            ['code' => 'ERR_INCONSISTENT_VOICE','title' => 'Inconsistent Brand Voice', 'desc' => 'Different pages sound like they were written by different people because they were.'],
            ['code' => 'ERR_UNWRITTEN_SCOPE', 'title' => 'Delayed Copywriting',        'desc' => 'You have been planning to update your service descriptions for a year but lack time to draft them.'],
            ['code' => 'ERR_MOBILE_DENSITY',  'title' => 'Unscannable Mobile Text',    'desc' => 'Visitors bounce because copy is dense, poorly formatted, or hard to read on phone screens.']
        ],
        'bento' => [
            'main' => [
                'handle' => 'EDITORIAL CORE',
                'title'  => 'Structured Brand Copywriting',
                'desc'   => 'Clear, concise website text structured to convey immediate value, establish credibility, and guide visitors toward conversion.',
                'specs'  => ['Homepage & Service Landing Copy', 'Structured H1/H2 Page Hierarchy', 'Scannable Mobile Card & Bullet Text']
            ],
            'medium' => [
                ['handle' => 'BRAND VOICE',     'title' => 'Brand Voice & Frameworks',  'desc' => 'Establishing a consistent brand voice, tone guidelines, and value propositions that set your business apart.', 'specs' => ['Voice & Tone Guidelines', 'Elevator Pitch Framing', 'Value Proposition Definition']],
                ['handle' => 'SEO ARCHITECTURE','title' => 'Search-Aware Structures',   'desc' => 'Writing informative copy that satisfies user intent and ranks naturally without keyword-stuffing.', 'specs' => ['Intent Keyword Integration', 'Meta Title & Desc Specs', 'Internal Linking Strategy']]
            ],
            'compact' => [
                ['handle' => 'CAMPAIGNS', 'title' => 'Ad & Social Copy Packets', 'desc' => 'Punchy ad text and social captions written to capture attention.'],
                ['handle' => 'PROOF',      'title' => 'Case Study Writing',       'desc' => 'Transforming client wins into structured problem-solution case studies.'],
                ['handle' => 'REVISIONS',  'title' => 'Two Complete Revision Rounds', 'desc' => 'Collaborative feedback cycles ensuring every sentence hits standards.']
            ]
        ],
        'system_map' => [
            'eyebrow' => '// EDITORIAL PRODUCTION MAP',
            'title'   => 'Living Creative Production OS',
            'desc'    => 'An interactive visualization of content moving from initial brief to published asset.',
            'nodes'   => [
                ['id' => 'idea',        'name' => 'CREATIVE BRIEF',    'label' => '[BRIEF DISCOVERY]',   'icon' => 'compass',   'role' => 'Extracting core business values, audience pain points, and product details.', 'tech' => 'Voice Brief'],
                ['id' => 'strategy',    'name' => 'CONTENT STRATEGY',  'label' => '[SEARCH & INTENT]',   'icon' => 'search',    'role' => 'Mapping search intent keywords, page hierarchies, and content structures.', 'tech' => 'Topic Mapping'],
                ['id' => 'creation',    'name' => 'DRAFTING ENGINE',   'label' => '[COPYWRITING ENGINE]','icon' => 'pencil',    'role' => 'Writing crisp H1/H2 headlines, punchy body copy, and persuasive CTAs.', 'tech' => 'Structured Copy'],
                ['id' => 'editing',     'name' => 'EDITORIAL APPROVED','label' => '[EDITORIAL REVIEW]',  'icon' => 'file-check','role' => 'Refining tone, checking factual clarity, and eliminating filler.', 'tech' => 'Proofing Pass'],
                ['id' => 'distribution','name' => 'DEPLOYMENT & SEO',  'label' => '[MULTI-CHANNEL DIST]', 'icon' => 'share-2',   'role' => 'Formatting content into clean HTML, adding meta attributes, and publishing.', 'tech' => 'HTML Delivery']
            ]
        ],
        'process' => [
            ['step' => '01', 'title' => 'DISCOVER', 'time' => '2-3 Days',  'desc' => 'We interview your team to capture your vocabulary, customer questions, and core values.'],
            ['step' => '02', 'title' => 'PLAN',     'time' => '3-5 Days',  'desc' => 'We map page structures, messaging angles, and heading hierarchies before writing.'],
            ['step' => '03', 'title' => 'BUILD',    'time' => '2-6 Weeks', 'desc' => 'Drafting copy in structured batches for client review and reaction.'],
            ['step' => '04', 'title' => 'LAUNCH',   'time' => '1 Week',    'desc' => 'Two rounds of client edits, final proofreading pass, and developer-ready HTML delivery.']
        ],
        'artifacts' => [
            ['type' => 'VOICE GUIDE',   'title' => 'Messaging Framework & Voice Spec', 'desc' => 'Documenting brand tone, core value statements, and key customer pain points.', 'tag' => 'Voice Approved'],
            ['type' => 'COPY DRAFT',    'title' => 'Website & Service Page Copy Drafts', 'desc' => 'Polished copy for homepages, service detail pages, and landing pages.', 'tag' => 'Drafts: Batch 1'],
            ['type' => 'HTML PACKET',   'title' => 'Formatted Semantic HTML Assets',   'desc' => 'Clean HTML-formatted copy ready for direct developer insertion into your CMS.', 'tag' => 'HTML Ready']
        ],
        'tools' => [
            ['icon' => 'pencil',  'label' => 'Brand Voice Guides',       'role' => 'Messaging Consistency'],
            ['icon' => 'search',    'label' => 'Search Intent Research',   'role' => 'Topic & Intent Mapping'],
            ['icon' => 'file-text', 'label' => 'Structured HTML Copy',     'role' => 'Semantic CMS Assets'],
            ['icon' => 'gauge',     'label' => 'Readability Audits',       'role' => 'Mobile Scannability'],
            ['icon' => 'history',   'label' => 'Editorial Calendars',      'role' => 'Content Workflow'],
            ['icon' => 'share-2',   'label' => 'Multi-channel Copy',       'role' => 'Campaign Asset Packets']
        ],
        'outcomes' => [
            'A clear, authoritative website voice that explains your value proposition in seconds.',
            'Consistent tone across all landing pages, service descriptions, and customer touchpoints.',
            'Search-optimized page structures that satisfy user intent without fluff.',
            'Higher engagement and lower bounce rates due to scannable, mobile-friendly formatting.'
        ],
        'who_it_is_for' => [
            ['title' => 'B2B Companies',     'desc' => 'Businesses needing clear, authoritative descriptions of complex technical services.', 'fit' => 'Technical positioning'],
            ['title' => 'Rebranding Sites',  'desc' => 'Companies undergoing website redesigns requiring unified tone across all pages.', 'fit' => 'Redesign unification'],
            ['title' => 'Founders',          'desc' => 'Founders who know their product deeply but need help articulating value.', 'fit' => 'Value articulation']
        ],
        'boundaries' => [
            ['title' => 'Video & Film Production', 'desc' => 'We write script treatments and storyboards; video shooting and editing require a dedicated production studio.'],
            ['title' => 'Technical API Docs',      'desc' => 'Developer documentation requires specialized technical writers embedded inside software engineering teams.'],
            ['title' => 'Language Translation',    'desc' => 'We write natively in English. Multi-lingual translation should be reviewed by native speakers in target regions.']
        ],
        'faqs' => [
            ['q' => 'How do you ensure the copy sounds authentic to our brand?', 'a' => 'Our discovery phase focuses on capturing your existing vocabulary, customer conversations, and core values so the copy sounds like an articulate extension of your team.'],
            ['q' => 'How many revision rounds are included?', 'a' => 'Two rounds of revisions are included in every scope. We deliver copy in batches so you can review early drafts before final polishing.'],
            ['q' => 'Do you write for search engines or human readers?', 'a' => 'We write for human readers first. Search engines prioritize high-quality, clear content that satisfies user intent—so good writing naturally performs well in search.'],
            ['q' => 'In what format is the copy delivered?', 'a' => 'We deliver copy as structured document files as well as clean, semantic HTML blocks that your web developer or CMS editor can paste directly.']
        ]
    ],

    'ecommerce-support' => [
        'title' => 'E-Commerce Support',
        'icon'  => 'shopping-cart',
        'key'   => 'ecom',
        'wide'  => false,
        'badge' => 'COMMERCE INFRASTRUCTURE ENGINE',
        'tagline' => 'The unglamorous side of selling online — listings, orders, reconciliation — kept in order.',
        'intro' => 'We assist online sellers with storefront structures, product catalog integrity, checkout optimization, and streamlined operational processes.',
        'highlights' => [
            'Catalog & Taxonomy Optimization',
            'Checkout Friction Reduction',
            'Order Workflow Documentation (SOPs)',
            'Payment Gateway Webhook Verification',
            'Sales & Payout Reconciliation'
        ],
        'diagnostics' => [
            ['code' => 'ERR_CATALOG_MESS',  'title' => 'Unstructured Product Catalog', 'desc' => 'The catalog has grown large and fragmented, making search and navigation confusing for buyers.'],
            ['code' => 'ERR_CHECKOUT_DROP', 'title' => 'Checkout Friction Drop-off',  'desc' => 'Checkout drop-off rates are high because of unnecessary form fields or mobile layout bugs.'],
            ['code' => 'ERR_RECON_DELAY',   'title' => 'Manual Reconciliation Lag',   'desc' => 'Month-end reconciliation between storefront sales, payment gateways, and accounts takes days.'],
            ['code' => 'ERR_UNWRITTEN_SOP', 'title' => 'Unwritten Order Operations',  'desc' => 'Order fulfillment and return procedures live inside one person\'s head rather than documented SOPs.']
        ],
        'bento' => [
            'main' => [
                'handle' => 'COMMERCE CORE',
                'title'  => 'Storefront & Catalog Architecture',
                'desc'   => 'Structuring product categories, custom attributes, tags, and search filters so buyers find items effortlessly on any device.',
                'specs'  => ['Category Taxonomy Standardization', 'Variant & Attribute Cleanup', 'Mobile Storefront Navigation']
            ],
            'medium' => [
                ['handle' => 'CHECKOUT OPTIM', 'title' => 'Checkout Friction Reduction',  'desc' => 'Auditing mobile and desktop checkout flows to eliminate unnecessary fields and boost completions.', 'specs' => ['Field Pruning & Guest Checkout', 'Mobile Wallet Setup (Apple/Google Pay)', 'Cart Abandonment Reduction']],
                ['handle' => 'PAYMENT SETUP',  'title' => 'Payment Gateway Verification', 'desc' => 'Assisting with gateway configuration, webhook verifications, currency options, and transaction security.', 'specs' => ['Stripe & PayPal Integration', 'Webhook Callback Verification', 'Fraud Prevention Rules']]
            ],
            'compact' => [
                ['handle' => 'WORKFLOWS', 'title' => 'Operational SOPs',     'desc' => 'Documenting order handling from purchase to dispatch.'],
                ['handle' => 'RECON',      'title' => 'Financial Payout Sync','desc' => 'Matching store sales records with payment gateway payouts.'],
                ['handle' => 'ANALYTICS',  'title' => 'GA4 E-Commerce Events','desc' => 'Configuring view_item, add_to_cart, and purchase event tracking.']
            ]
        ],
        'system_map' => [
            'eyebrow' => '// COMMERCE INFRASTRUCTURE MAP',
            'title'   => 'Living Commerce Infrastructure Engine',
            'desc'    => 'An interactive visualization of customer transactions flowing through storefront systems.',
            'nodes'   => [
                ['id' => 'customer',   'name' => 'SHOPPER SESSION',  'label' => '[SHOPPER FLOW]',      'icon' => 'users',        'role' => 'Fast product discovery, scannable listings, and responsive mobile UI.', 'tech' => 'Storefront UI'],
                ['id' => 'storefront', 'name' => 'CATALOG SYNC',     'label' => '[PRODUCT CATALOG]',   'icon' => 'package',      'role' => 'Organized product categories, structured SKUs, and rich image standards.', 'tech' => 'Shopify / Woo'],
                ['id' => 'cart',       'name' => 'CHECKOUT ENGINE',  'label' => '[CART & CHECKOUT]',   'icon' => 'shopping-cart','role' => 'Streamlined cart page, guest checkout options, and minimal friction.', 'tech' => 'Optimized Cart'],
                ['id' => 'payment',    'name' => 'GATEWAY VERIFIED', 'label' => '[PAYMENT GATEWAY]',   'icon' => 'credit-card',  'role' => 'Secure card processing, Apple/Google Pay support, and fraud rules.', 'tech' => 'Stripe / PayPal'],
                ['id' => 'order',      'name' => 'ORDER DISPATCH',   'label' => '[FULFILLMENT SYNC]',  'icon' => 'truck',        'role' => 'Automated order confirmation, inventory deduction, and dispatch updates.', 'tech' => 'Order Inventory']
            ]
        ],
        'process' => [
            ['step' => '01', 'title' => 'DISCOVER', 'time' => '2-3 Days',  'desc' => 'We audit your storefront, catalog structure, checkout metrics, and order bottlenecks.'],
            ['step' => '02', 'title' => 'PLAN',     'time' => '3-5 Days',  'desc' => 'We define catalog cleanup rules, checkout fixes, gateway tasks, and SOP requirements.'],
            ['step' => '03', 'title' => 'BUILD',    'time' => '2-6 Weeks', 'desc' => 'Executing catalog reorganization, checkout updates, and documentation while keeping store live.'],
            ['step' => '04', 'title' => 'LAUNCH',   'time' => '1 Week',    'desc' => 'Staff training on new SOPs, analytics verification, and optional ongoing monthly maintenance.']
        ],
        'artifacts' => [
            ['type' => 'TAXONOMY SPEC', 'title' => 'Catalog Structure & Attribute Spec', 'desc' => 'Reorganized product taxonomy, attribute maps, and listing guidelines.', 'tag' => 'Catalog Cleaned'],
            ['type' => 'UX AUDIT',      'title' => 'Checkout Friction Reduction Audit',  'desc' => 'Detailed review of checkout drop-off points with actionable UX fixes.', 'tag' => 'Checkout Optimized'],
            ['type' => 'SOP MANUAL',    'title' => 'Order Flow Operational SOP Manual',   'desc' => 'Documented operational steps from order placement to dispatch and returns.', 'tag' => 'SOPs Documented']
        ],
        'tools' => [
            ['icon' => 'shopping-cart', 'label' => 'Shopify &amp; Shopify Plus', 'role' => 'Storefront Platform'],
            ['icon' => 'layers',        'label' => 'WooCommerce',                'role' => 'WordPress Commerce'],
            ['icon' => 'credit-card',   'label' => 'Payment Gateways',           'role' => 'Transaction Processing'],
            ['icon' => 'pie-chart',     'label' => 'GA4 E-Commerce Analytics',   'role' => 'Funnel Conversion Tracking'],
            ['icon' => 'package',       'label' => 'Catalog Management',         'role' => 'SKU & Attribute Taxonomy'],
            ['icon' => 'database',      'label' => 'Inventory Records',          'role' => 'Payout Reconciliation']
        ],
        'outcomes' => [
            'A streamlined product catalog that shoppers can navigate easily on any device.',
            'Reduced checkout abandonment through streamlined payment and cart flows.',
            'Documented operational SOPs ensuring your team can process orders consistently.',
            'Faster month-end financial reconciliation with clear gateway payout mapping.'
        ],
        'who_it_is_for' => [
            ['title' => 'Shopify & Woo Stores', 'desc' => 'Online stores seeking to clean up catalog mess and improve mobile checkout rates.', 'fit' => 'Active storefront platforms'],
            ['title' => 'Scaling Merchants',    'desc' => 'Brands transitioning from manual order handling to documented operational systems.', 'fit' => 'Operational scaling'],
            ['title' => 'Multi-Channel Brands', 'desc' => 'Sellers operating across storefronts and marketplaces needing catalog sync clarity.', 'fit' => 'Multi-channel sync']
        ],
        'boundaries' => [
            ['title' => 'Warehousing & Physical Fulfillment', 'desc' => 'We build and optimize digital commerce systems; physical warehousing and shipping logistics are handled by 3PL partners.'],
            ['title' => 'Tax Filing & Formal Accounting',     'desc' => 'We organize store sales and payout data for clean export; formal tax filing should be reviewed by a certified accountant.'],
            ['title' => 'Customer Service Staffing',          'desc' => 'We document support workflows and templates; answering daily customer tickets is managed by your internal support team.']
        ],
        'faqs' => [
            ['q' => 'Which e-commerce platforms do you support?', 'a' => 'We specialize in Shopify and WooCommerce. We also support custom PHP/Node storefronts with custom payment integrations.'],
            ['q' => 'Will our store remain online while you perform updates?', 'a' => 'Yes. All catalog, theme, and checkout work is tested in staging environments first to ensure zero interruption to live sales.'],
            ['q' => 'Do you help set up local and international payment gateways?', 'a' => 'Yes. We assist with configuration and testing for major gateways including Stripe, PayPal, Razorpay, and regional providers.'],
            ['q' => 'How do you measure e-commerce performance improvements?', 'a' => 'We set up GA4 E-Commerce tracking to monitor conversion rates, checkout funnel drop-offs, average order value (AOV), and mobile purchase completions.']
        ]
    ],

    'lead-automation' => [
        'title' => 'Lead Automation',
        'icon'  => 'bot',
        'key'   => 'marketing',
        'wide'  => true,
        'badge' => '24/7 QUALIFICATION & RESPONSE SYSTEM',
        'tagline' => 'Engage inbound leads in under 60 seconds, filter qualified buyers, and sync directly with WhatsApp and CRM.',
        'intro' => 'We design automated lead intake and qualification pipelines that respond to website inquiries within 60 seconds, route qualified opportunities directly to sales on WhatsApp, and update your CRM pipeline automatically.',
        'highlights' => [
            '60-Second WhatsApp Auto-Acknowledgement',
            'Interactive Qualification Field Scoping',
            'Webhook-Driven CRM Pipeline Synchronization',
            'Zero Artificial Financial Quote Generation',
            'Sales Notification & Discovery Link Routing'
        ],
        'landing_links' => [
            [
                'title' => 'WhatsApp Lead Automation Workflows',
                'desc'  => 'Automate lead qualification, instant response workflows, and CRM routing via WhatsApp API integration.',
                'url'   => '/landing/whatsapp-automation',
                'badge' => 'SPECIALIZED WORKFLOW'
            ]
        ],
        'diagnostics' => [
            ['code' => 'ERR_SLOW_RESPONSE',     'title' => 'Slow Lead Response Times',   'desc' => 'Inquiries sit unread in email inboxes for 24+ hours while high-intent buyers move on to competitors.'],
            ['code' => 'ERR_UNQUALIFIED_LEADS', 'title' => 'Unqualified Call Overload', 'desc' => 'Sales reps spend hours on discovery calls with leads who lack required budget or scope fit.'],
            ['code' => 'ERR_DISCONNECTED_CRM',  'title' => 'Disconnected Lead Records', 'desc' => 'Leads submitted on website forms exist as isolated emails with no central CRM tracking or status history.'],
            ['code' => 'ERR_MANUAL_HANDOFF',    'title' => 'Manual Handoff Friction',   'desc' => 'Lack of instant WhatsApp routing means sales reps must manually copy contact info before reaching out.']
        ],
        'bento' => [
            'main' => [
                'handle' => 'AUTOMATION CORE',
                'title'  => '60-Second Lead Response & Qualification',
                'desc'   => 'Automated intake workflows that immediately validate submissions, send instant WhatsApp confirmation, and assign qualified leads to your team.',
                'specs'  => ['Instant 60s WhatsApp Notification', 'Automated Qualification Logic', 'Team OS CRM Webhook Sync']
            ],
            'medium' => [
                ['handle' => 'QUALIFICATION FLOW', 'title' => 'Budget & Scope Filtering',        'desc' => 'Structured multi-step form questions that capture budget bracket, project urgency, and service interest without manual sorting.', 'specs' => ['Budget & Urgency Fields', 'Lead Qualification Scoring', 'Human Proposal Hand-off']],
                ['handle' => 'CRM PIPELINE',       'title' => 'Automated CRM Synchronization',   'desc' => 'Every form submission instantly creates a structured lead and deal record inside your CRM pipeline with assigned ownership.', 'specs' => ['Team OS DB Integration', 'Real-time Sales Alerts', 'Stage History Tracking']]
            ],
            'compact' => [
                ['handle' => 'DISCOVERY',  'title' => 'Calendar Routing',       'desc' => 'Direct discovery call booking links sent automatically to qualified leads.'],
                ['handle' => 'COMPLIANCE', 'title' => 'Data Privacy & Consent', 'desc' => 'GDPR and Indian IT Act 2000 compliant consent logging on all forms.'],
                ['handle' => 'SAFEGUARDS', 'title' => 'Human Financial Ownership','desc' => 'Automation qualifies leads, but binding financial quotes are always issued by human proposals.']
            ]
        ],
        'system_map' => [
            'eyebrow' => '// LEAD AUTOMATION SYSTEM MAP',
            'title'   => 'Living Lead Qualification Engine',
            'desc'    => 'An interactive visualization of customer inquiries moving from web forms to qualified CRM deals.',
            'nodes'   => [
                ['id' => 'user',      'name' => 'WEBSITE VISITOR',  'label' => '[FORM SUBMISSION]',     'icon' => 'users',          'role' => 'Visitor completes multi-step qualification form with project scope & budget.', 'tech' => 'Web Form UI'],
                ['id' => 'webhook',   'name' => 'AUTOMATION ENGINE', 'label' => '[WEBHOOK VALIDATION]',  'icon' => 'cpu',            'role' => 'Sanitizes input, checks rate limits, and triggers parallel notification pipelines.', 'tech' => 'Server Webhook'],
                ['id' => 'crm',       'name' => 'TEAM OS CRM',       'label' => '[CRM DEAL ENTRY]',      'icon' => 'database',       'role' => 'Generates lead record, assigns pipeline stage (New Lead), and calculates score.', 'tech' => 'Team OS Pipeline'],
                ['id' => 'whatsapp',  'name' => 'WHATSAPP ROUTER',   'label' => '[INSTANT ACK & ALERT]', 'icon' => 'message-square', 'role' => 'Sends 60s automated WhatsApp confirmation to buyer and instant alert to sales team.', 'tech' => 'WhatsApp API'],
                ['id' => 'discovery', 'name' => 'DISCOVERY CALL',    'label' => '[QUALIFIED PIPELINE]',  'icon' => 'calendar',       'role' => 'Qualified prospect selects 15-minute discovery call time with senior engineer.', 'tech' => 'Calendar Routing']
            ]
        ],
        'process' => [
            ['step' => '01', 'title' => 'DISCOVER', 'time' => '2-3 Days',  'desc' => 'We map existing lead intake channels, CRM requirements, and sales team response SLAs.'],
            ['step' => '02', 'title' => 'PLAN',     'time' => '3-5 Days',  'desc' => 'We define qualification parameters, WhatsApp message templates, and webhook architecture.'],
            ['step' => '03', 'title' => 'BUILD',    'time' => '2-6 Weeks', 'desc' => 'Custom multi-step form, webhook handlers, CRM database sync, and WhatsApp routing implementation.'],
            ['step' => '04', 'title' => 'LAUNCH',   'time' => '1 Week',    'desc' => 'Live test end-to-end lead flow, verify webhook delivery, and train sales staff on lead management.']
        ],
        'artifacts' => [
            ['type' => 'FLOW SCHEMA',   'title' => 'Lead Qualification Diagram & Form Schema', 'desc' => 'Multi-step question logic, budget qualification brackets, and validation rules.', 'tag' => 'Schema Validated'],
            ['type' => 'WEBHOOK SPEC',  'title' => 'CRM Webhook & Pipeline Integration Spec', 'desc' => 'Payload mappings connecting web forms to Team OS CRM deal stages.', 'tag' => 'Webhook Live'],
            ['type' => 'SOP TEMPLATE',  'title' => 'WhatsApp Auto-Ack & Sales Outreach SOP',  'desc' => 'Standardized response templates, 60-second SLA rules, and discovery call script.', 'tag' => 'SOP Approved']
        ],
        'tools' => [
            ['icon' => 'message-square', 'label' => 'WhatsApp Business Routing', 'role' => 'Instant WhatsApp Outreach'],
            ['icon' => 'database',       'label' => 'Team OS CRM Integration',   'role' => 'Pipeline Deal Tracking'],
            ['icon' => 'pie-chart',      'label' => 'GA4 Lead Event Tracking',   'role' => 'LeadForm_Start & Complete'],
            ['icon' => 'shield',         'label' => 'Sanitizing & Anti-Spam',    'role' => 'Rate Limit & CSRF Guard'],
            ['icon' => 'calendar',       'label' => 'Discovery Call Scheduler',  'role' => '15-Min Meeting Booking'],
            ['icon' => 'mail',           'label' => 'Automated Email Auto-Ack',  'role' => 'Parallel Confirmation']
        ],
        'outcomes' => [
            '100% of qualified website inquiries acknowledged within 60 seconds.',
            'Sales team receives structured lead context on WhatsApp before the first discovery call.',
            'Zero lost leads with central CRM tracking and automated notification fallbacks.',
            'Higher discovery call conversion rates from self-identified, pre-qualified prospects.'
        ],
        'who_it_is_for' => [
            ['title' => 'Professional Services & Clinics', 'desc' => 'Businesses requiring fast lead intake and instant consultation booking.', 'fit' => 'High inbound inquiry volume'],
            ['title' => 'D2C & B2B Brands',                'desc' => 'Brands needing pre-sale qualification before scheduling consultative calls.', 'fit' => 'Consultative sales process'],
            ['title' => 'Growing Sales Teams',             'desc' => 'Teams moving away from messy email threads into automated CRM deal tracking.', 'fit' => 'CRM pipeline optimization']
        ],
        'boundaries' => [
            ['title' => 'Automated Binding Quotes',   'desc' => 'Automation qualifies leads and estimates scope, but binding financial quotes are always issued by human proposals.'],
            ['title' => 'Cold Unsolicited Spamming',  'desc' => 'We engineer inbound opt-in qualification systems; cold email harvesting or bulk unsolicited SMS spam is strictly excluded.'],
            ['title' => 'Call Center Outsourcing',    'desc' => 'Our system automates triage and routing; conducting technical discovery calls is managed by your internal team.']
        ],
        'faqs' => [
            ['q' => 'Does RAFly’s lead automation generate binding financial quotes automatically?', 'a' => 'No. Automation assists lead qualification and budget routing, but binding financial quotes and SOWs are always prepared and confirmed by human project leads.'],
            ['q' => 'How fast is the automated lead notification sent to the prospect?', 'a' => 'Automated acknowledgements and WhatsApp routing links trigger within 60 seconds of form submission.'],
            ['q' => 'Can website forms sync directly with our CRM system?', 'a' => 'Yes. Forms connect to RAFly Team OS CRM out of the box and can also post webhooks to external platforms like HubSpot, Pipedrive, or Salesforce.'],
            ['q' => 'How does the lead system prevent spam and fake submissions?', 'a' => 'Every form includes multi-layer anti-spam protection: hidden honeypots, mathematical challenge verification, rate-limiting, and strict input sanitization.']
        ]
    ]
];

