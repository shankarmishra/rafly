<?php
/**
 * RAFly Search-Intent Taxonomy & Canonical Query Mapping Engine.
 *
 * Provides structured Behind-The-Scenes Search Intent Taxonomy and Query-to-Page Map.
 * Used for internal linking, query classification, canonical audit, and zero-doorway verification.
 */

/**
 * Returns the search intent taxonomy structure.
 *
 * @return array<string, array<string, string>>
 */
function search_intent_taxonomy(): array
{
    return [
        'platforms' => [
            'android'        => 'Android OS (Kotlin / Java)',
            'ios'            => 'Apple iOS (Swift / SwiftUI)',
            'cross-platform' => 'Cross-Platform Mobile',
            'mobile'         => 'Mobile Application Core',
        ],
        'frameworks' => [
            'react-native'    => 'React Native (Meta)',
            'flutter'         => 'Flutter & Dart (Google)',
            'kotlin'          => 'Kotlin Native (Jetpack Compose)',
            'swift'           => 'Swift Native (SwiftUI / UIKit)',
        ],
        'backends' => [
            'python'         => 'Python (FastAPI / Django)',
            'nodejs'         => 'Node.js (Express / NestJS)',
            'firebase'       => 'Firebase & Firestore Real-Time',
            'rest-api'       => 'Typed REST / GraphQL APIs',
            'databases'      => 'PostgreSQL, MySQL, SQLite, Realm',
            'authentication' => 'Argon2id, JWT, OAuth2, Biometrics',
            'payments'       => 'Stripe, Apple Pay, Google Pay, Razorpay',
            'notifications'  => 'FCM & Apple APNs Push Router',
        ],
        'app_types' => [
            'business'   => 'Enterprise & Business Apps',
            'ecommerce'  => 'Mobile E-Commerce Storefronts',
            'fintech'    => 'Fintech & Payment Applications',
            'healthcare' => 'Healthcare & Telemedicine Apps',
            'education'  => 'EdTech & Learning Platforms',
            'booking'    => 'On-Demand & Booking Engines',
            'saas'       => 'SaaS Mobile Client Portals',
            'ai_apps'    => 'AI-Powered Mobile Assistants',
        ],
    ];
}

/**
 * Returns the canonical query-intent database mapping 30 representative queries
 * to their intent, canonical page, page type, entities, location, quality score, and indexability.
 *
 * @return array<int, array<string, mixed>>
 */
function search_intent_map(): array
{
    return [
        [
            'query'            => 'app development company',
            'intent'           => 'commercial',
            'canonical_page'   => '/services/app-development',
            'page_type'        => 'service-hub',
            'primary_entity'   => 'App Development',
            'secondary_entities' => ['Mobile App Engine', 'iOS', 'Android', 'React Native'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 96,
        ],
        [
            'query'            => 'mobile app development company',
            'intent'           => 'commercial',
            'canonical_page'   => '/services/app-development',
            'page_type'        => 'service-hub',
            'primary_entity'   => 'App Development',
            'secondary_entities' => ['Cross-Platform', 'Flutter', 'Swift'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 96,
        ],
        [
            'query'            => 'android app development company',
            'intent'           => 'commercial',
            'canonical_page'   => '/services/app-development',
            'page_type'        => 'service-hub',
            'primary_entity'   => 'Android App Development',
            'secondary_entities' => ['Kotlin', 'Google Play Store', 'Jetpack Compose'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 95,
        ],
        [
            'query'            => 'react native development company',
            'intent'           => 'commercial',
            'canonical_page'   => '/services/app-development',
            'page_type'        => 'service-hub',
            'primary_entity'   => 'React Native App Development',
            'secondary_entities' => ['Cross-Platform', 'JavaScript', 'Expo'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 95,
        ],
        [
            'query'            => 'flutter app development agency',
            'intent'           => 'commercial',
            'canonical_page'   => '/services/app-development',
            'page_type'        => 'service-hub',
            'primary_entity'   => 'Flutter App Development',
            'secondary_entities' => ['Dart', 'Skia Impeller', 'iOS & Android'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 95,
        ],
        [
            'query'            => 'ios app developer agency',
            'intent'           => 'commercial',
            'canonical_page'   => '/services/app-development',
            'page_type'        => 'service-hub',
            'primary_entity'   => 'iOS App Development',
            'secondary_entities' => ['Swift', 'SwiftUI', 'Apple App Store'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 95,
        ],
        [
            'query'            => 'react native vs flutter',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/react-native-vs-flutter',
            'page_type'        => 'resource',
            'primary_entity'   => 'React Native vs Flutter',
            'secondary_entities' => ['Architecture Comparison', 'Performance Benchmarks'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 92,
        ],
        [
            'query'            => 'react native app development guide',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/react-native-app-development',
            'page_type'        => 'resource',
            'primary_entity'   => 'React Native Architecture',
            'secondary_entities' => ['Hermes Engine', 'Bridge & Fabric', 'Native Modules'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 94,
        ],
        [
            'query'            => 'flutter app development guide',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/flutter-app-development',
            'page_type'        => 'resource',
            'primary_entity'   => 'Flutter Architecture',
            'secondary_entities' => ['Dart Compiler', 'Impeller Engine', 'Platform Channels'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 94,
        ],
        [
            'query'            => 'android app development architecture',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/android-app-development',
            'page_type'        => 'resource',
            'primary_entity'   => 'Android Architecture',
            'secondary_entities' => ['Kotlin Coroutines', 'Jetpack Compose', 'Room DB'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 93,
        ],
        [
            'query'            => 'ios app development swift guide',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/ios-app-development',
            'page_type'        => 'resource',
            'primary_entity'   => 'iOS Architecture',
            'secondary_entities' => ['SwiftUI', 'Combine Framework', 'CoreData'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 93,
        ],
        [
            'query'            => 'mobile app architecture best practices',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/mobile-app-architecture',
            'page_type'        => 'resource',
            'primary_entity'   => 'Mobile App Architecture',
            'secondary_entities' => ['Clean Architecture', 'Offline Sync', 'SSL Pinning'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 95,
        ],
        [
            'query'            => 'python backend for mobile app',
            'intent'           => 'informational',
            'canonical_page'   => '/resources/python-mobile-app-backend',
            'page_type'        => 'resource',
            'primary_entity'   => 'Python Mobile Backend',
            'secondary_entities' => ['FastAPI', 'PostgreSQL', 'JWT Authentication'],
            'location'         => null,
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 91,
        ],
        [
            'query'            => 'app development company in Greater Noida',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/greater-noida',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development Greater Noida HQ',
            'secondary_entities' => ['Tech Zone IV', 'Greater Noida West', 'Delhi NCR'],
            'location'         => 'greater-noida',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 92,
        ],
        [
            'query'            => 'app development company in Noida',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/noida',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development Noida Hub',
            'secondary_entities' => ['Sector 62', 'Noida Expressway', 'IT Corridor'],
            'location'         => 'noida',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 88,
        ],
        [
            'query'            => 'mobile app developers in Delhi',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/delhi',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development Delhi',
            'secondary_entities' => ['Connaught Place', 'South Delhi', 'Okhla'],
            'location'         => 'delhi',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 86,
        ],
        [
            'query'            => 'app development company in Gurgaon',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/gurgaon',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development Gurgaon',
            'secondary_entities' => ['Cyber City', 'DLF Phase 3', 'Golf Course Road'],
            'location'         => 'gurgaon',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 86,
        ],
        [
            'query'            => 'app development company in Mumbai',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/mumbai',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development Mumbai',
            'secondary_entities' => ['BKC', 'Andheri East', 'Financial Hub'],
            'location'         => 'mumbai',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 84,
        ],
        [
            'query'            => 'app development company in Dubai',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/dubai',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development Dubai',
            'secondary_entities' => ['Business Bay', 'DIFC', 'UAE Enterprise'],
            'location'         => 'dubai',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 84,
        ],
        [
            'query'            => 'app development company in London',
            'intent'           => 'local-commercial',
            'canonical_page'   => '/services/app-development/london',
            'page_type'        => 'service-location',
            'primary_entity'   => 'App Development London',
            'secondary_entities' => ['Tech City', 'Canary Wharf', 'UK Hub'],
            'location'         => 'london',
            'status'           => 'active',
            'indexability'     => 'index',
            'quality_score'    => 84,
        ],
    ];
}

/**
 * Classifies a search query into its canonical target page and intent type.
 *
 * @param string $query
 * @return array<string, mixed>
 */
function classify_query_intent(string $query): array
{
    $normalized = strtolower(trim($query));
    foreach (search_intent_map() as $item) {
        if (strtolower($item['query']) === $normalized) {
            return $item;
        }
    }

    // Dynamic heuristic classification
    if (str_contains($normalized, 'vs') || str_starts_with($normalized, 'what is') || str_starts_with($normalized, 'how ')) {
        return [
            'query'          => $query,
            'intent'         => 'informational',
            'canonical_page' => '/resources',
            'page_type'      => 'resource',
            'indexability'   => 'index',
            'quality_score'  => 80,
        ];
    }

    if (str_contains($normalized, 'company') || str_contains($normalized, 'developer') || str_contains($normalized, 'agency')) {
        return [
            'query'          => $query,
            'intent'         => 'commercial',
            'canonical_page' => '/services/app-development',
            'page_type'      => 'service-hub',
            'indexability'   => 'index',
            'quality_score'  => 90,
        ];
    }

    return [
        'query'          => $query,
        'intent'         => 'commercial',
        'canonical_page' => '/services/app-development',
        'page_type'      => 'service-hub',
        'indexability'   => 'index',
        'quality_score'  => 85,
    ];
}
