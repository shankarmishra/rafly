<?php
/**
 * RAFLY GROUND-TRUTH TAXONOMY MATRIX — Categorized Entities across All 7 Primary Services.
 *
 * Statuses:
 * - 'verified': Codebase/service backed capability. Used in public SEO, sitemaps, and schema.
 * - 'candidate': Plausible/requested, but unverified in code. Internal tracking only; excluded from public commercial pages.
 * - 'unsupported': Bounded out of scope by RAFLY. Strictly excluded from SEO.
 */

return [
    'web-development' => [
        'title' => 'Web Development',
        'slug'  => 'web-development',
        'sub_services' => ['frontend-development', 'backend-development', 'api-development', 'custom-web-apps'],
        'entities' => [
            'php'                    => ['status' => 'verified',   'label' => 'PHP 8.3'],
            'laravel'                => ['status' => 'verified',   'label' => 'Laravel Framework'],
            'javascript'             => ['status' => 'verified',   'label' => 'JavaScript (ESNext)'],
            'typescript'             => ['status' => 'verified',   'label' => 'TypeScript'],
            'postgresql'             => ['status' => 'verified',   'label' => 'PostgreSQL'],
            'mysql'                  => ['status' => 'verified',   'label' => 'MySQL'],
            'redis'                  => ['status' => 'verified',   'label' => 'Redis Key-Value Caching'],
            'cloudflare'             => ['status' => 'verified',   'label' => 'Cloudflare Edge CDN'],
            'rest-api'               => ['status' => 'verified',   'label' => 'REST APIs'],
            'decoupled-architecture' => ['status' => 'verified',   'label' => 'Decoupled Architecture'],
            'core-web-vitals'        => ['status' => 'verified',   'label' => 'Core Web Vitals Optimization'],
            'html5-css3'             => ['status' => 'verified',   'label' => 'HTML5 / CSS3 Tokens'],
            'react'                  => ['status' => 'candidate',  'label' => 'React Frontend Framework'],
            'nextjs'                 => ['status' => 'candidate',  'label' => 'Next.js Framework'],
            'nodejs'                 => ['status' => 'candidate',  'label' => 'Node.js Runtime'],
            'express'                => ['status' => 'candidate',  'label' => 'Express.js Framework'],
            'python'                 => ['status' => 'candidate',  'label' => 'Python Backend'],
            'django'                 => ['status' => 'candidate',  'label' => 'Django Framework'],
            'mongodb'                => ['status' => 'candidate',  'label' => 'MongoDB Database'],
            'graphql'                => ['status' => 'candidate',  'label' => 'GraphQL Endpoints'],
            'aws'                    => ['status' => 'candidate',  'label' => 'AWS Cloud Infrastructure'],
            'serverless'             => ['status' => 'candidate',  'label' => 'Serverless Architecture'],
            'cobol-mainframe'        => ['status' => 'unsupported','label' => 'Legacy COBOL Mainframes'],
        ]
    ],

    'web-security' => [
        'title' => 'Web Security',
        'slug'  => 'web-security',
        'sub_services' => ['api-security', 'security-audit', 'vulnerability-assessment'],
        'entities' => [
            'ssl-tls'                => ['status' => 'verified',   'label' => 'TLS 1.3 Encryption'],
            'security-headers'       => ['status' => 'verified',   'label' => 'HTTP Security Headers (CSP/HSTS)'],
            'argon2id'               => ['status' => 'verified',   'label' => 'Argon2id Password Hashing'],
            'input-sanitization'     => ['status' => 'verified',   'label' => 'Form & API Input Sanitization'],
            'xss-protection'         => ['status' => 'verified',   'label' => 'Cross-Site Scripting (XSS) Defense'],
            'sqli-defense'           => ['status' => 'verified',   'label' => 'SQL Injection Mitigation'],
            'csrf-guard'             => ['status' => 'verified',   'label' => 'Anti-CSRF Token Guards'],
            'encrypted-backups'      => ['status' => 'verified',   'label' => 'Encrypted Backup Recovery'],
            'malware-cleanup'        => ['status' => 'verified',   'label' => 'Emergency Malware Cleanup'],
            'surface-audit'          => ['status' => 'verified',   'label' => 'Surface Vulnerability Assessment'],
            'cloudflare-waf'         => ['status' => 'candidate',  'label' => 'Cloudflare WAF Rules'],
            'cloud-security'         => ['status' => 'candidate',  'label' => 'Cloud Infrastructure Hardening'],
            'crest-pen-testing'      => ['status' => 'unsupported','label' => 'Formal CREST Penetration Testing'],
            'iso27001-audit'         => ['status' => 'unsupported','label' => 'Formal ISO 27001 Certification'],
            'ddos-mitigation'        => ['status' => 'unsupported','label' => 'Origin DDoS Mitigation'],
        ]
    ],

    'app-development' => [
        'title' => 'App Development',
        'slug'  => 'app-development',
        'sub_services' => ['android-app-development', 'ios-app-development', 'react-native-development', 'flutter-development', 'cross-platform-app-dev'],
        'entities' => [
            'react-native'           => ['status' => 'verified',   'label' => 'React Native Framework'],
            'flutter'                => ['status' => 'verified',   'label' => 'Flutter & Dart Engine'],
            'swift'                  => ['status' => 'verified',   'label' => 'Native iOS (Swift)'],
            'kotlin'                 => ['status' => 'verified',   'label' => 'Native Android (Kotlin)'],
            'firebase'               => ['status' => 'verified',   'label' => 'Firebase Cloud Backend'],
            'push-notifications'     => ['status' => 'verified',   'label' => 'FCM & APNs Push Notifications'],
            'biometrics'             => ['status' => 'verified',   'label' => 'Biometric Auth (FaceID/TouchID)'],
            'app-store-publishing'   => ['status' => 'verified',   'label' => 'App Store & Google Play Submission'],
            'mobile-vault'           => ['status' => 'verified',   'label' => 'Mobile Security Vault & SSL Pinning'],
            'expo'                   => ['status' => 'candidate',  'label' => 'Expo Toolchain'],
            'swiftui'                => ['status' => 'candidate',  'label' => 'SwiftUI UI Framework'],
            'jetpack-compose'        => ['status' => 'candidate',  'label' => 'Jetpack Compose UI'],
            'realm-sqlite'           => ['status' => 'candidate',  'label' => 'Realm & SQLite Mobile Cache'],
            'unreal-engine-3d'       => ['status' => 'unsupported','label' => '3D Gaming Engine Development'],
            'invasive-tracking-sdk'  => ['status' => 'unsupported','label' => 'Invasive Data Tracking SDKs'],
        ]
    ],

    'performance-marketing' => [
        'title' => 'Performance Marketing',
        'slug'  => 'performance-marketing',
        'sub_services' => ['google-ads-management', 'meta-ads-agency', 'conversion-rate-optimization'],
        'entities' => [
            'google-ads'             => ['status' => 'verified',   'label' => 'Google Search & Display Ads'],
            'meta-ads'               => ['status' => 'verified',   'label' => 'Meta Ads Manager (Facebook & Instagram)'],
            'ga4'                    => ['status' => 'verified',   'label' => 'Google Analytics 4 (GA4)'],
            'gtm'                    => ['status' => 'verified',   'label' => 'Google Tag Manager (GTM)'],
            'utm-attribution'        => ['status' => 'verified',   'label' => 'UTM Campaign Attribution'],
            'negative-query-pruning' => ['status' => 'verified',   'label' => 'Negative Search Query Pruning'],
            'server-side-tagging'    => ['status' => 'verified',   'label' => 'Server-Side Tagging'],
            'search-console'         => ['status' => 'verified',   'label' => 'Google Search Console Query Mining'],
            'linkedin-ads'           => ['status' => 'candidate',  'label' => 'LinkedIn B2B Advertising'],
            'tiktok-ads'             => ['status' => 'candidate',  'label' => 'TikTok Ads Manager'],
            'guaranteed-1-ranking'   => ['status' => 'unsupported','label' => 'Guaranteed #1 Search Rankings'],
            'tv-broadcast-buying'    => ['status' => 'unsupported','label' => 'National TV Broadcast Buying'],
            'influencer-talent-mgmt' => ['status' => 'unsupported','label' => 'Influencer Talent Management'],
        ]
    ],

    'content-creation' => [
        'title' => 'Content Creation',
        'slug'  => 'content-creation',
        'sub_services' => ['short-form-video', 'reels-production', 'social-media-creative'],
        'entities' => [
            'website-copywriting'    => ['status' => 'verified',   'label' => 'Website & Service Page Copywriting'],
            'editorial-architecture' => ['status' => 'verified',   'label' => 'Search-Aware Editorial Architecture'],
            'brand-voice-guide'      => ['status' => 'verified',   'label' => 'Brand Voice & Messaging Frameworks'],
            'scannable-formatting'   => ['status' => 'verified',   'label' => 'Scannable Mobile Text Formatting'],
            'ad-copy-packets'        => ['status' => 'verified',   'label' => 'Ad & Social Copy Packets'],
            'reels-scriptwriting'    => ['status' => 'verified',   'label' => 'Short-Form Video & Reels Scripting'],
            'semantic-html-copy'     => ['status' => 'verified',   'label' => 'Semantic HTML5 Content Assets'],
            'ai-assisted-copy'       => ['status' => 'candidate',  'label' => 'AI-Assisted Content Drafting'],
            'motion-graphics-assets' => ['status' => 'candidate',  'label' => 'Motion Graphics & Visual Assets'],
            'on-location-film-crew'  => ['status' => 'unsupported','label' => 'On-Location Film Crew Video Shoots'],
            'api-developer-docs'     => ['status' => 'unsupported','label' => 'Technical API Developer Documentation'],
            'multilingual-translation'=>['status' => 'unsupported','label' => 'Multi-Lingual Language Translation'],
        ]
    ],

    'ecommerce' => [
        'title' => 'E-Commerce',
        'slug'  => 'ecommerce',
        'sub_services' => ['shopify-development', 'woocommerce-development', 'custom-ecommerce-apps'],
        'entities' => [
            'shopify'                => ['status' => 'verified',   'label' => 'Shopify Store Development'],
            'woocommerce'            => ['status' => 'verified',   'label' => 'WooCommerce Development'],
            'custom-storefront'      => ['status' => 'verified',   'label' => 'Custom PHP/Node Storefronts'],
            'stripe'                 => ['status' => 'verified',   'label' => 'Stripe Gateway Integration'],
            'paypal'                 => ['status' => 'verified',   'label' => 'PayPal Webhooks & Processing'],
            'razorpay'               => ['status' => 'verified',   'label' => 'Razorpay Payment Setup'],
            'catalog-taxonomy'       => ['status' => 'verified',   'label' => 'Product Catalog & Attribute Taxonomy'],
            'checkout-optimization'  => ['status' => 'verified',   'label' => 'Mobile Checkout Friction Reduction'],
            'ga4-ecommerce-events'   => ['status' => 'verified',   'label' => 'GA4 E-Commerce Event Tracking'],
            'shopify-plus'           => ['status' => 'candidate',  'label' => 'Shopify Plus Enterprise'],
            'bigcommerce'            => ['status' => 'candidate',  'label' => 'BigCommerce Storefronts'],
            'physical-warehousing'   => ['status' => 'unsupported','label' => 'Physical Warehousing & 3PL Logistics'],
            'tax-cpa-filing'         => ['status' => 'unsupported','label' => 'Tax Filing & Formal CPA Accounting'],
            'call-center-staffing'   => ['status' => 'unsupported','label' => 'Daily Call Center Customer Support'],
        ]
    ],

    'lead-automation' => [
        'title' => 'Lead Automation',
        'slug'  => 'lead-automation',
        'sub_services' => ['crm-lead-routing', 'whatsapp-lead-automation', 'email-workflow-automation'],
        'entities' => [
            'whatsapp-api'           => ['status' => 'verified',   'label' => 'WhatsApp Business API Integration'],
            'team-os-crm'            => ['status' => 'verified',   'label' => 'Team OS CRM Pipeline Sync'],
            '60-second-response'     => ['status' => 'verified',   'label' => '60-Second Lead Response Engine'],
            'qualification-form'     => ['status' => 'verified',   'label' => 'Interactive Budget & Scope Filtering'],
            'discovery-scheduler'    => ['status' => 'verified',   'label' => 'Discovery Call Scheduler Routing'],
            'anti-spam-honeypot'     => ['status' => 'verified',   'label' => 'Anti-Spam Honeypot & Rate Guards'],
            'webhook-sync'           => ['status' => 'verified',   'label' => 'Webhook Payload Pipeline Sync'],
            'hubspot-webhook'        => ['status' => 'candidate',  'label' => 'HubSpot CRM Webhooks'],
            'salesforce-sync'        => ['status' => 'candidate',  'label' => 'Salesforce Integration'],
            'automated-binding-quote'=> ['status' => 'unsupported','label' => 'Automated Binding Financial Quotes'],
            'cold-unsolicited-spam'  => ['status' => 'unsupported','label' => 'Cold Email / SMS Spamming'],
            'outbound-telemarketing' => ['status' => 'unsupported','label' => 'Outbound Call Center Telemarketing'],
        ]
    ]
];
