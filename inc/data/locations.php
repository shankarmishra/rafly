<?php
/**
 * GEOGRAPHIC TAXONOMY & REGIONAL DELIVERY DATA
 *
 * Single source of truth for RAFLY's physical HQ, on-site hubs,
 * regional delivery nodes, and global service availability.
 *
 * Hierarchy:
 * World -> Country -> State/Region -> City -> Locality -> Service
 */

return [
    // -----------------------------------------------------------------------
    // HQ HUB (PHYSICAL OFFICE & REGISTERED PRESENCE)
    // -----------------------------------------------------------------------
    'greater-noida' => [
        'slug'            => 'greater-noida',
        'name'            => 'Greater Noida West',
        'type'            => 'hq', // 'hq' | 'onsite_hub' | 'regional_node' | 'global_node'
        'country'         => 'India',
        'country_code'    => 'IN',
        'state'           => 'Uttar Pradesh',
        'region'          => 'Delhi NCR',
        'city'            => 'Greater Noida',
        'locality'        => 'Greater Noida West',
        'display_title'   => 'Greater Noida West HQ',
        'seo_title'       => 'Web Development & Digital Growth HQ in Greater Noida West | RAFLY',
        'meta_desc'       => 'RAFLY headquarters and systems lab in Greater Noida West (NX-One, Tech Zone IV). Web development, security, performance marketing, content creation, and ecommerce support.',
        'address'         => 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, Uttar Pradesh 201306',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 IST',
        'is_physical'     => true,
        'quality_score'   => 100, // Maximum score (Physical HQ)
        'is_indexable'    => true,
        'intro'           => 'RAFLY is registered and headquartered in Greater Noida West (NX-One, Tech Zone IV). Our engineering systems lab and creative operations run directly from this primary hub.',
        'highlights'      => [
            'Physical Registered HQ & Systems Lab',
            'Direct On-Site Architecture Consultations',
            'In-Person Client Intake & Strategy Briefings',
            'Full Stack Development & Security Operations'
        ],
        'local_context'   => 'Greater Noida West (Tech Zone IV) serves as the primary engineering node for RAFLY, powering digital growth for enterprises, D2C brands, and clinics across Delhi NCR and globally.',
        'nearby_slugs'    => ['noida', 'delhi', 'gurgaon'],
        'featured_services' => ['web-development', 'web-security', 'performance-marketing', 'content-creation', 'ecommerce', 'lead-automation']
    ],

    // -----------------------------------------------------------------------
    // ON-SITE HUBS (DELHI NCR METRO)
    // -----------------------------------------------------------------------
    'noida' => [
        'slug'            => 'noida',
        'name'            => 'Noida Tech Corridor',
        'type'            => 'onsite_hub',
        'country'         => 'India',
        'country_code'    => 'IN',
        'state'           => 'Uttar Pradesh',
        'region'          => 'Delhi NCR',
        'city'            => 'Noida',
        'locality'        => 'Sectors 62, 63 & Expressways',
        'display_title'   => 'Noida Sector Hub',
        'seo_title'       => 'Web Development & Digital Growth Services in Noida | RAFLY',
        'meta_desc'       => 'Dedicated engineering teams delivering rapid web development, web security, and lead automation for tech enterprises and startups across Noida Sectors 62, 63, and Expressways.',
        'address'         => 'On-Site Engineering Hub across Noida Sectors 62, 63, & Expressway Corridors',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 IST',
        'is_physical'     => false,
        'quality_score'   => 92,
        'is_indexable'    => true,
        'intro'           => 'Noida is a premier IT and software hub in North India. RAFLY provides rapid on-site technical consultations, sprint handovers, and web application architecture across Noida.',
        'highlights'      => [
            'Rapid On-Site Technical Consultations',
            'Same-Day Emergency Security Incident Triage',
            'Dedicated Sprint Handovers & Architecture Reviews',
            'Direct Integration with Local Business Workflows'
        ],
        'local_context'   => 'With thousands of tech startups, IT services firms, and D2C brands operating in Noida, RAFLY delivers high-speed web apps and hardened security perimeters with local availability.',
        'nearby_slugs'    => ['greater-noida', 'delhi', 'gurgaon'],
        'featured_services' => ['web-development', 'web-security', 'lead-automation', 'ecommerce']
    ],

    'delhi' => [
        'slug'            => 'delhi',
        'name'            => 'Delhi Enterprise Zone',
        'type'            => 'onsite_hub',
        'country'         => 'India',
        'country_code'    => 'IN',
        'state'           => 'Delhi',
        'region'          => 'Delhi NCR',
        'city'            => 'New Delhi',
        'locality'        => 'Central & South Delhi Enterprise Corridors',
        'display_title'   => 'Delhi Enterprise Hub',
        'seo_title'       => 'Web Development Agency & Digital Growth in Delhi | RAFLY',
        'meta_desc'       => 'High-touch technical consulting, custom web app development, performance marketing, and cyber security for enterprises and commercial brands in Delhi NCR.',
        'address'         => 'Executive Consultation Hub, Central & South Delhi NCR',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 IST',
        'is_physical'     => false,
        'quality_score'   => 90,
        'is_indexable'    => true,
        'intro'           => 'As the commercial capital of Northern India, Delhi demands high-performance digital systems. RAFLY delivers decoupled web architectures and growth intelligence for Delhi enterprises.',
        'highlights'      => [
            'Executive Stakeholder Alignment & Consultations',
            'High-Conversion Commercial Web Architectures',
            'Meta CAPI & GA4 Server-Side Analytics Integration',
            'Custom Lead Automation & WhatsApp Pipelines'
        ],
        'local_context'   => 'From Connaught Place to South Extension and Okhla Industrial Area, RAFLY partners with established B2B firms and retail brands across New Delhi.',
        'nearby_slugs'    => ['gurgaon', 'noida', 'greater-noida'],
        'featured_services' => ['web-development', 'performance-marketing', 'content-creation', 'web-security']
    ],

    'gurgaon' => [
        'slug'            => 'gurgaon',
        'name'            => 'Gurgaon Cyber City',
        'type'            => 'onsite_hub',
        'country'         => 'India',
        'country_code'    => 'IN',
        'state'           => 'Haryana',
        'region'          => 'Delhi NCR',
        'city'            => 'Gurugram',
        'locality'        => 'Cyber City & Golf Course Road',
        'display_title'   => 'Gurgaon Cyber Hub',
        'seo_title'       => 'Web Development & Performance Marketing in Gurgaon | RAFLY',
        'meta_desc'       => 'Enterprise web application development, security hardening, and performance acquisition for SaaS companies and corporate clients in Gurgaon Cyber City.',
        'address'         => 'Consulting Node, DLF Cyber City & Golf Course Road, Gurugram, Haryana',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 IST',
        'is_physical'     => false,
        'quality_score'   => 90,
        'is_indexable'    => true,
        'intro'           => 'Gurgaon Cyber City represents India\'s premier technology ecosystem. RAFLY provides zero-trust web architectures, custom SaaS client portals, and performance marketing to Gurgaon enterprises.',
        'highlights'      => [
            'SaaS & Product Team Custom API Engineering',
            'Zero-Trust Web Security & Penetration Audits',
            'Conversion Rate Optimization for High-Scale Funnels',
            'Executive Sprint Reviews & Technical Documentation'
        ],
        'local_context'   => 'Located along Golf Course Road and Cyber City, RAFLY delivers high-speed web apps and secure cloud data layers built to modern engineering standards.',
        'nearby_slugs'    => ['delhi', 'noida', 'greater-noida'],
        'featured_services' => ['web-development', 'web-security', 'performance-marketing', 'lead-automation']
    ],

    // -----------------------------------------------------------------------
    // REGIONAL / GLOBAL NODES (INDEXABLE ONLY WITH HIGH-QUALITY SPECIFIC CONTENT)
    // -----------------------------------------------------------------------
    'mumbai' => [
        'slug'            => 'mumbai',
        'name'            => 'Mumbai Commercial Hub',
        'type'            => 'regional_node',
        'country'         => 'India',
        'country_code'    => 'IN',
        'state'           => 'Maharashtra',
        'region'          => 'Western India',
        'city'            => 'Mumbai',
        'locality'        => 'BKC & South Mumbai',
        'display_title'   => 'Mumbai Regional Node',
        'seo_title'       => 'Web Development & E-Commerce Engineering in Mumbai | RAFLY',
        'meta_desc'       => 'Decoupled web development, Shopify e-commerce optimization, and performance marketing for brands and financial services in Mumbai.',
        'address'         => 'Remote Project Delivery & Consultation Node, Mumbai, Maharashtra',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 IST',
        'is_physical'     => false,
        'quality_score'   => 85,
        'is_indexable'    => true,
        'intro'           => 'Mumbai is India\'s financial capital and e-commerce epicenter. RAFLY provides custom storefront architectures, high-speed payment gateway integrations, and lead automation for Mumbai businesses.',
        'highlights'      => [
            'Custom E-Commerce Storefronts & Shopify Plus Optimization',
            'Financial Data Protection & Argon2id Security Hardening',
            'Sub-100ms LCP Conversion Funnels for D2C Brands',
            'Full Remote Git Deployment & CI/CD Pipelines'
        ],
        'local_context'   => 'Serving financial, fashion, and consumer brands across BKC, Lower Parel, and Andheri, RAFLY ensures sub-second load speeds and frictionless checkout workflows.',
        'nearby_slugs'    => ['delhi', 'gurgaon', 'greater-noida'],
        'featured_services' => ['ecommerce', 'web-development', 'app-development', 'performance-marketing', 'web-security']
    ],

    'bangalore' => [
        'slug'            => 'bangalore',
        'name'            => 'Bengaluru Tech Capital',
        'type'            => 'regional_node',
        'country'         => 'India',
        'country_code'    => 'IN',
        'state'           => 'Karnataka',
        'region'          => 'South India',
        'city'            => 'Bengaluru',
        'locality'        => 'Koramangala & Indiranagar Tech Corridor',
        'display_title'   => 'Bengaluru Regional Node',
        'seo_title'       => 'Web Application Development & API Architecture in Bengaluru | RAFLY',
        'meta_desc'       => 'Custom PHP 8.3 & React web application development, decoupled API engineering, and cyber security for tech startups and enterprises in Bengaluru.',
        'address'         => 'Remote Project Delivery & Consultation Node, Bengaluru, Karnataka',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 IST',
        'is_physical'     => false,
        'quality_score'   => 85,
        'is_indexable'    => true,
        'intro'           => 'Bengaluru is India\'s Silicon Valley. RAFLY builds bespoke web applications, decoupled API gateways, and scalable database layers for high-growth tech ventures in Bengaluru.',
        'highlights'      => [
            'Decoupled Microservices & Typed API Engineering',
            'React / Next.js High-Performance Frontend Stacks',
            'Redis Cache Layer & PostgreSQL Schema Optimization',
            'Async Git Workflow with Staging Preview Links'
        ],
        'local_context'   => 'Partnering with technology founders and product managers across Koramangala, HSR Layout, and Whitefield, RAFLY delivers clean code with zero technical debt.',
        'nearby_slugs'    => ['mumbai', 'delhi', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'web-security', 'lead-automation', 'content-creation']
    ],

    'dubai' => [
        'slug'            => 'dubai',
        'name'            => 'Dubai & UAE Global Node',
        'type'            => 'global_node',
        'country'         => 'United Arab Emirates',
        'country_code'    => 'AE',
        'state'           => 'Dubai',
        'region'          => 'Middle East & GCC',
        'city'            => 'Dubai',
        'locality'        => 'Downtown Dubai & DIFC',
        'display_title'   => 'Dubai & GCC Global Node',
        'seo_title'       => 'Web Development & Cyber Security Services in Dubai, UAE | RAFLY',
        'meta_desc'       => 'Global digital growth partner delivering bespoke web development, e-commerce storefronts, web security audits, and WhatsApp automation for businesses in Dubai and the GCC region.',
        'address'         => 'Global Remote Delivery Node, Dubai, United Arab Emirates',
        'phone'           => '+91 8796882212',
        'hours'           => 'Sun - Thu, 09:00 - 18:00 GST',
        'is_physical'     => false,
        'quality_score'   => 82,
        'is_indexable'    => true,
        'intro'           => 'Dubai is the innovation gateway of the Middle East. RAFLY delivers internationalized web applications, secure payment workflows, and automated WhatsApp lead intake for clients in Dubai and across the UAE.',
        'highlights'      => [
            'Internationalized Multi-Language Web Application Engineering',
            'High-Security Payment Gateway Integration (Stripe / Telr / Checkout.com)',
            'Instant 60-Second WhatsApp Business Lead Routing',
            'Global Edge CDN Asset Delivery via Cloudflare'
        ],
        'local_context'   => 'Supporting commercial ventures and luxury brands in DIFC, Business Bay, and Dubai Marina, RAFLY delivers enterprise-grade code ownership and zero-downtime releases.',
        'nearby_slugs'    => ['london', 'india', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'ecommerce', 'web-security', 'lead-automation']
    ],

    'london' => [
        'slug'            => 'london',
        'name'            => 'London & UK Global Node',
        'type'            => 'global_node',
        'country'         => 'United Kingdom',
        'country_code'    => 'GB',
        'state'           => 'England',
        'region'          => 'United Kingdom',
        'city'            => 'London',
        'locality'        => 'City of London & Tech City',
        'display_title'   => 'London & UK Global Node',
        'seo_title'       => 'Web Development Agency & E-Commerce Engineering in London | RAFLY',
        'meta_desc'       => 'Bespoke web development, GDPR-compliant cyber security, e-commerce optimization, and performance marketing for UK enterprises and scaling businesses.',
        'address'         => 'Global Remote Delivery Node, London, United Kingdom',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 17:00 BST',
        'is_physical'     => false,
        'quality_score'   => 82,
        'is_indexable'    => true,
        'intro'           => 'London is a global fintech and commercial center. RAFLY collaborates with UK organizations to deliver high-speed web apps, strict GDPR data compliance, and performance marketing.',
        'highlights'      => [
            'GDPR & UK Data Protection Act Compliant System Architectures',
            'High-Speed Decoupled Storefront Development',
            'Core Web Vitals Pass Guarantee (< 1.2s LCP)',
            'Transparent Async Slack & GitHub Project Operations'
        ],
        'local_context'   => 'Working with UK businesses across London, Manchester, and Birmingham, RAFLY offers dedicated full-stack development teams with full IP transfer.',
        'nearby_slugs'    => ['dubai', 'usa', 'greater-noida'],
        'featured_services' => ['web-development', 'ecommerce', 'web-security', 'performance-marketing']
    ],

    'usa' => [
        'slug'            => 'usa',
        'name'            => 'United States Global Node',
        'type'            => 'global_node',
        'country'         => 'United States',
        'country_code'    => 'US',
        'state'           => 'California & NY',
        'region'          => 'North America',
        'city'            => 'San Francisco & NYC',
        'district'        => 'Bay Area & Manhattan',
        'locality'        => 'Silicon Valley & East Coast',
        'postal_code'     => '94103',
        'display_title'   => 'USA & North America Global Node',
        'seo_title'       => 'Custom Web & Mobile App Development Services USA | RAFLY',
        'meta_desc'       => 'Decoupled web application development, iOS/Android app engineering, security hardening, and performance marketing for US companies.',
        'address'         => 'Global Remote Delivery Node, California & New York, USA',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 EST/PST',
        'is_physical'     => false,
        'quality_score'   => 82,
        'is_indexable'    => true,
        'intro'           => 'RAFLY provides remote web application development, mobile app engineering, and growth systems for US startups, SaaS platforms, and retail brands across North America.',
        'highlights'      => [
            'Bespoke PHP 8.3, React & Mobile App Engineering',
            'Sub-100ms Core Web Vitals Performance Audits',
            '100% Code & Intellectual Property Transfer',
            '24/7 Security Monitoring & Backup Verification'
        ],
        'local_context'   => 'Partnering with US teams across California, New York, and Texas, RAFLY delivers enterprise software quality with seamless async collaboration.',
        'nearby_slugs'    => ['canada', 'london', 'dubai', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'web-security', 'ecommerce', 'performance-marketing']
    ],

    'canada' => [
        'slug'            => 'canada',
        'name'            => 'Canada & Toronto Node',
        'type'            => 'global_node',
        'country'         => 'Canada',
        'country_code'    => 'CA',
        'state'           => 'Ontario',
        'region'          => 'North America',
        'city'            => 'Toronto',
        'district'        => 'Greater Toronto Area',
        'locality'        => 'Downtown Toronto & Financial District',
        'postal_code'     => 'M5H 2N2',
        'display_title'   => 'Canada & Toronto Global Node',
        'seo_title'       => 'Web Development & App Engineering Services in Canada | RAFLY',
        'meta_desc'       => 'High-performance web development, mobile app engineering, and digital growth services for Canadian technology companies and enterprises in Toronto and Vancouver.',
        'address'         => 'Global Remote Delivery Node, Toronto & Vancouver, Canada',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 EST',
        'is_physical'     => false,
        'quality_score'   => 80,
        'is_indexable'    => true,
        'intro'           => 'RAFLY collaborates with Canadian tech startups and enterprises in Toronto and Vancouver to deliver custom web apps, mobile solutions, and web security hardening.',
        'highlights'      => [
            'Enterprise Web & Mobile Application Development',
            'PipEDA Data Privacy & Security Hardening',
            'Sub-Second LCP Performance Optimization',
            'Transparent Async Project Operations'
        ],
        'local_context'   => 'Supporting Canadian tech hubs in Toronto and Vancouver with robust software engineering and security compliance.',
        'nearby_slugs'    => ['usa', 'london', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'web-security', 'ecommerce']
    ],

    'australia' => [
        'slug'            => 'australia',
        'name'            => 'Australia & Sydney Node',
        'type'            => 'global_node',
        'country'         => 'Australia',
        'country_code'    => 'AU',
        'state'           => 'New South Wales',
        'region'          => 'Oceania / APAC',
        'city'            => 'Sydney',
        'district'        => 'Sydney CBD',
        'locality'        => 'Barangaroo & Tech Central',
        'postal_code'     => '2000',
        'display_title'   => 'Australia & Sydney Global Node',
        'seo_title'       => 'Web Development & Mobile App Services Australia | RAFLY',
        'meta_desc'       => 'Decoupled web application development, iOS & Android app creation, and performance marketing for Australian businesses in Sydney and Melbourne.',
        'address'         => 'Global Remote Delivery Node, Sydney & Melbourne, Australia',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 17:00 AEST',
        'is_physical'     => false,
        'quality_score'   => 80,
        'is_indexable'    => true,
        'intro'           => 'RAFLY provides software engineering, mobile app development, and growth marketing for businesses in Sydney, Melbourne, and across Australia.',
        'highlights'      => [
            'Cross-Platform Mobile Apps (React Native & Flutter)',
            'High-Conversion E-Commerce & Shopify Engineering',
            '100% Code & Intellectual Property Ownership',
            'Cloudflare Edge CDN Asset Acceleration'
        ],
        'local_context'   => 'Partnering with Australian merchants and B2B services to deliver fast, secure digital architectures.',
        'nearby_slugs'    => ['singapore', 'dubai', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'ecommerce', 'performance-marketing']
    ],

    'singapore' => [
        'slug'            => 'singapore',
        'name'            => 'Singapore APAC Node',
        'type'            => 'global_node',
        'country'         => 'Singapore',
        'country_code'    => 'SG',
        'state'           => 'Singapore',
        'region'          => 'Southeast Asia / APAC',
        'city'            => 'Singapore',
        'district'        => 'Central Business District',
        'locality'        => 'Marina Bay & One-North Tech Hub',
        'postal_code'     => '018981',
        'display_title'   => 'Singapore APAC Global Node',
        'seo_title'       => 'Web & Mobile App Development Services in Singapore | RAFLY',
        'meta_desc'       => 'Enterprise web app engineering, mobile app development, and security hardening for financial technology and commerce brands in Singapore.',
        'address'         => 'Global Remote Delivery Node, Singapore CBD & One-North',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 18:00 SGT',
        'is_physical'     => false,
        'quality_score'   => 82,
        'is_indexable'    => true,
        'intro'           => 'Singapore is Southeast Asia\'s leading tech capital. RAFLY builds secure web applications, mobile apps, and automated lead intake workflows for Singapore enterprises.',
        'highlights'      => [
            'PDPA-Compliant Data Security & Session Hardening',
            'Fintech & Commerce Mobile App Development',
            'Sub-100ms API Gateway Performance',
            'Instant 60s Lead Automation Workflows'
        ],
        'local_context'   => 'Collaborating with Singapore startups and regional headquarters across Marina Bay and One-North.',
        'nearby_slugs'    => ['australia', 'dubai', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'web-security', 'lead-automation']
    ],

    'germany' => [
        'slug'            => 'germany',
        'name'            => 'Germany & Berlin Node',
        'type'            => 'global_node',
        'country'         => 'Germany',
        'country_code'    => 'DE',
        'state'           => 'Berlin & Bavaria',
        'region'          => 'Europe / EU',
        'city'            => 'Berlin & Munich',
        'district'        => 'Mitte & Munich Tech Corridor',
        'locality'        => 'Berlin Mitte & Munich Business District',
        'postal_code'     => '10117',
        'display_title'   => 'Germany & Europe Global Node',
        'seo_title'       => 'Web Development & App Engineering Services in Germany | RAFLY',
        'meta_desc'       => 'GDPR-compliant web development, mobile application creation, and security hardening for German enterprises and technology ventures in Berlin and Munich.',
        'address'         => 'Global Remote Delivery Node, Berlin & Munich, Germany',
        'phone'           => '+91 8796882212',
        'hours'           => 'Mon - Fri, 09:00 - 17:00 CET',
        'is_physical'     => false,
        'quality_score'   => 80,
        'is_indexable'    => true,
        'intro'           => 'RAFLY delivers software engineering, GDPR-compliant web systems, and mobile applications for companies in Berlin, Munich, and across Germany.',
        'highlights'      => [
            'Strict EU GDPR Compliance & Consent Logging',
            'Custom Web App & Mobile App Development',
            'Argon2id Password Hashing & SSL Pinning',
            'High-Precision Technical Documentation'
        ],
        'local_context'   => 'Serving European technology hubs with clean software architecture and zero technical debt.',
        'nearby_slugs'    => ['netherlands', 'london', 'greater-noida'],
        'featured_services' => ['web-development', 'app-development', 'web-security', 'content-creation']
    ]
];

