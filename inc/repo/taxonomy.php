<?php
/**
 * Taxonomy Repository.
 *
 * Provides ground-truth taxonomy lookups, query-to-canonical resolution, and search coverage reporting.
 * ONLY 'verified' entities participate in public SEO, sitemaps, and schema generation.
 */

/**
 * Returns full ground-truth taxonomy matrix across all 7 services.
 *
 * @return array<string, array<string, mixed>>
 */
function taxonomy_all(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $file = __DIR__ . '/../data/taxonomy.php';
    if (is_file($file)) {
        return $cache = require $file;
    }
    return $cache = [];
}

/**
 * Returns only verified entities for a primary service or across all services.
 *
 * @param string|null $serviceSlug Optional service slug filter
 * @return array<string, array<string, mixed>>
 */
function taxonomy_verified_entities(?string $serviceSlug = null): array
{
    $matrix = taxonomy_all();
    $out = [];

    if ($serviceSlug !== null && isset($matrix[$serviceSlug])) {
        $entities = (array)($matrix[$serviceSlug]['entities'] ?? []);
        foreach ($entities as $key => $meta) {
            if (($meta['status'] ?? '') === 'verified') {
                $out[$key] = $meta;
            }
        }
        return $out;
    }

    foreach ($matrix as $svc => $data) {
        $entities = (array)($data['entities'] ?? []);
        foreach ($entities as $key => $meta) {
            if (($meta['status'] ?? '') === 'verified') {
                $out[$svc][$key] = $meta;
            }
        }
    }

    return $out;
}

/**
 * Find matching primary service for a given entity slug, returning entity metadata and status.
 */
function taxonomy_find_entity(string $entitySlug): ?array
{
    $matrix = taxonomy_all();
    $clean = strtolower(trim($entitySlug));

    foreach ($matrix as $serviceSlug => $data) {
        $entities = (array)($data['entities'] ?? []);
        if (isset($entities[$clean])) {
            return array_merge($entities[$clean], [
                'service_slug' => $serviceSlug,
                'entity_key'   => $clean
            ]);
        }
    }

    return null;
}

/**
 * Universal Ground-Truth Query → Canonical Page Resolver Engine.
 * Maps any search query string deterministically to its single strongest canonical URL.
 * Excludes candidate and unsupported claims from generating commercial doorway pages.
 *
 * @param string $query User search query or target topic
 * @return array{target_url: string, service_slug: string, intent_type: string, is_indexable: bool, match_reason: string}
 */
function taxonomy_resolve_canonical(string $query): array
{
    $q = strtolower(trim($query));

    // 1. Specialized High-Intent Landing Pages
    if (preg_match('/\b(malware|emergency security|hacked)\b/i', $q)) {
        return [
            'target_url'   => '/landing/security-emergency',
            'service_slug' => 'web-security',
            'intent_type'  => 'Transactional Emergency',
            'is_indexable' => true,
            'match_reason' => 'Emergency malware response landing page'
        ];
    }
    if (preg_match('/\b(free website security audit|free audit|forensic audit)\b/i', $q) || str_contains($q, 'free audit')) {
        return [
            'target_url'   => '/landing/website-audit',
            'service_slug' => 'web-security',
            'intent_type'  => 'Commercial Lead',
            'is_indexable' => true,
            'match_reason' => 'Technical security & vitals website audit landing page'
        ];
    }
    if (str_contains($q, 'whatsapp') && (str_contains($q, 'lead') || str_contains($q, 'automation') || str_contains($q, 'api'))) {
        return [
            'target_url'   => '/landing/whatsapp-automation',
            'service_slug' => 'lead-automation',
            'intent_type'  => 'Commercial Lead',
            'is_indexable' => true,
            'match_reason' => 'WhatsApp lead qualification & routing landing page'
        ];
    }

    // 2. High-Precision Verified Sub-Service & Entity Mapping (Evaluated in strict order with word boundaries)
    $verifiedMap = [
        // App Development (Verified)
        'react native'          => ['url' => '/services/react-native-development', 'svc' => 'app-development', 'reason' => 'Verified React Native sub-service'],
        'flutter'               => ['url' => '/services/flutter-development', 'svc' => 'app-development', 'reason' => 'Verified Flutter sub-service'],
        'ios'                   => ['url' => '/services/ios-app-development', 'svc' => 'app-development', 'reason' => 'Verified native iOS sub-service'],
        'swift'                 => ['url' => '/services/ios-app-development', 'svc' => 'app-development', 'reason' => 'Verified Swift iOS binding'],
        'android'               => ['url' => '/services/android-app-development', 'svc' => 'app-development', 'reason' => 'Verified native Android sub-service'],
        'kotlin'                => ['url' => '/services/android-app-development', 'svc' => 'app-development', 'reason' => 'Verified Kotlin Android binding'],
        'cross platform'        => ['url' => '/services/cross-platform-app-dev', 'svc' => 'app-development', 'reason' => 'Verified cross-platform sub-service'],
        'mobile app'            => ['url' => '/services/app-development', 'svc' => 'app-development', 'reason' => 'Verified mobile app capability'],
        'push notification'     => ['url' => '/services/app-development', 'svc' => 'app-development', 'reason' => 'Verified push notification engine'],
        'fcm'                   => ['url' => '/services/app-development', 'svc' => 'app-development', 'reason' => 'Verified FCM push engine'],
        'apns'                  => ['url' => '/services/app-development', 'svc' => 'app-development', 'reason' => 'Verified APNs push engine'],
        'firebase'              => ['url' => '/services/app-development', 'svc' => 'app-development', 'reason' => 'Verified mobile Firebase sync'],

        // Performance Marketing (Verified)
        'google ads'            => ['url' => '/services/google-ads-management', 'svc' => 'performance-marketing', 'reason' => 'Verified Google Ads sub-service'],
        'meta ads'              => ['url' => '/services/meta-ads-agency', 'svc' => 'performance-marketing', 'reason' => 'Verified Meta Ads sub-service'],
        'facebook ads'          => ['url' => '/services/meta-ads-agency', 'svc' => 'performance-marketing', 'reason' => 'Verified Meta Paid Social sub-service'],
        'conversion rate'       => ['url' => '/services/conversion-rate-optimization', 'svc' => 'performance-marketing', 'reason' => 'Verified CRO sub-service'],
        'cro'                   => ['url' => '/services/conversion-rate-optimization', 'svc' => 'performance-marketing', 'reason' => 'Verified CRO sub-service'],
        'landing page'          => ['url' => '/services/conversion-rate-optimization', 'svc' => 'performance-marketing', 'reason' => 'Verified landing page CRO alignment'],
        'ga4'                   => ['url' => '/services/performance-marketing', 'svc' => 'performance-marketing', 'reason' => 'Verified GA4 analytics'],
        'gtm'                   => ['url' => '/services/performance-marketing', 'svc' => 'performance-marketing', 'reason' => 'Verified GTM server-side tagging'],
        'paid search'           => ['url' => '/services/performance-marketing', 'svc' => 'performance-marketing', 'reason' => 'Verified paid search campaign'],
        'utm'                   => ['url' => '/services/performance-marketing', 'svc' => 'performance-marketing', 'reason' => 'Verified UTM attribution'],
        'negative search'       => ['url' => '/services/performance-marketing', 'svc' => 'performance-marketing', 'reason' => 'Verified negative query pruning'],

        // Content Creation (Verified)
        'reels'                 => ['url' => '/services/short-form-video', 'svc' => 'content-creation', 'reason' => 'Verified short-form Reels sub-service'],
        'short form'            => ['url' => '/services/short-form-video', 'svc' => 'content-creation', 'reason' => 'Verified short-form video sub-service'],
        'social media creative' => ['url' => '/services/social-media-creative', 'svc' => 'content-creation', 'reason' => 'Verified social creative sub-service'],
        'copywriting'           => ['url' => '/services/content-creation', 'svc' => 'content-creation', 'reason' => 'Verified copywriting capability'],
        'brand voice'           => ['url' => '/services/content-creation', 'svc' => 'content-creation', 'reason' => 'Verified brand voice framework'],
        'editorial'             => ['url' => '/services/content-creation', 'svc' => 'content-creation', 'reason' => 'Verified editorial architecture'],

        // E-Commerce (Verified)
        'shopify'               => ['url' => '/services/shopify-development', 'svc' => 'ecommerce', 'reason' => 'Verified Shopify storefront sub-service'],
        'woocommerce'           => ['url' => '/services/woocommerce-development', 'svc' => 'ecommerce', 'reason' => 'Verified WooCommerce sub-service'],
        'custom ecommerce'      => ['url' => '/services/custom-ecommerce-apps', 'svc' => 'ecommerce', 'reason' => 'Verified custom e-commerce app sub-service'],
        'stripe'                => ['url' => '/services/ecommerce', 'svc' => 'ecommerce', 'reason' => 'Verified Stripe payment gateway'],
        'paypal'                => ['url' => '/services/ecommerce', 'svc' => 'ecommerce', 'reason' => 'Verified PayPal gateway'],

        // Lead Automation (Verified)
        'crm'                   => ['url' => '/services/crm-lead-routing', 'svc' => 'lead-automation', 'reason' => 'Verified CRM lead routing sub-service'],
        'email workflow'        => ['url' => '/services/email-workflow-automation', 'svc' => 'lead-automation', 'reason' => 'Verified email workflow sub-service'],
        '60 second'             => ['url' => '/services/lead-automation', 'svc' => 'lead-automation', 'reason' => 'Verified 60s response engine'],

        // Web Security (Verified)
        'api security'          => ['url' => '/services/api-security', 'svc' => 'web-security', 'reason' => 'Verified API security sub-service'],
        'vulnerability'         => ['url' => '/services/vulnerability-assessment', 'svc' => 'web-security', 'reason' => 'Verified vulnerability assessment sub-service'],
        'security audit'        => ['url' => '/services/security-audit', 'svc' => 'web-security', 'reason' => 'Verified security audit sub-service'],
        'xss'                   => ['url' => '/services/web-security', 'svc' => 'web-security', 'reason' => 'Verified XSS defense'],
        'sql injection'         => ['url' => '/services/web-security', 'svc' => 'web-security', 'reason' => 'Verified SQLi defense'],
        'argon2id'              => ['url' => '/services/web-security', 'svc' => 'web-security', 'reason' => 'Verified Argon2id auth security'],
        'encrypted backup'      => ['url' => '/services/web-security', 'svc' => 'web-security', 'reason' => 'Verified encrypted backup recovery'],

        // Web Development (Verified)
        'frontend'              => ['url' => '/services/frontend-development', 'svc' => 'web-development', 'reason' => 'Verified frontend engineering sub-service'],
        'backend'               => ['url' => '/services/backend-development', 'svc' => 'web-development', 'reason' => 'Verified backend engineering sub-service'],
        'api development'       => ['url' => '/services/api-development', 'svc' => 'web-development', 'reason' => 'Verified API development sub-service'],
        'custom web app'        => ['url' => '/services/custom-web-apps', 'svc' => 'web-development', 'reason' => 'Verified custom web app sub-service'],
    ];

    foreach ($verifiedMap as $phrase => $info) {
        $pattern = '/\b' . preg_quote($phrase, '/') . '\b/i';
        if (preg_match($pattern, $q)) {
            return [
                'target_url'   => $info['url'],
                'service_slug' => $info['svc'],
                'intent_type'  => 'Commercial Verified Intent',
                'is_indexable' => true,
                'match_reason' => $info['reason']
            ];
        }
    }

    // 3. Fallback to Primary Service Hubs (with word boundaries)
    if (preg_match('/\b(security|audit|owasp|pentest)\b/i', $q)) {
        return ['target_url' => '/services/web-security', 'service_slug' => 'web-security', 'intent_type' => 'Commercial Security Search', 'is_indexable' => true, 'match_reason' => 'Web Security Primary Hub'];
    }
    if (preg_match('/\b(app|mobile)\b/i', $q)) {
        return ['target_url' => '/services/app-development', 'service_slug' => 'app-development', 'intent_type' => 'Commercial App Search', 'is_indexable' => true, 'match_reason' => 'App Development Primary Hub'];
    }
    if (preg_match('/\b(ad|ads|ppc|marketing|campaign)\b/i', $q)) {
        return ['target_url' => '/services/performance-marketing', 'service_slug' => 'performance-marketing', 'intent_type' => 'Commercial Marketing Search', 'is_indexable' => true, 'match_reason' => 'Performance Marketing Primary Hub'];
    }
    if (preg_match('/\b(content|copy|script|blog)\b/i', $q)) {
        return ['target_url' => '/services/content-creation', 'service_slug' => 'content-creation', 'intent_type' => 'Commercial Content Search', 'is_indexable' => true, 'match_reason' => 'Content Creation Primary Hub'];
    }
    if (preg_match('/\b(ecom|ecommerce|shop|store|cart)\b/i', $q)) {
        return ['target_url' => '/services/ecommerce', 'service_slug' => 'ecommerce', 'intent_type' => 'Commercial E-Commerce Search', 'is_indexable' => true, 'match_reason' => 'E-Commerce Primary Hub'];
    }
    if (preg_match('/\b(lead|automation|form|inquiry)\b/i', $q)) {
        return ['target_url' => '/services/lead-automation', 'service_slug' => 'lead-automation', 'intent_type' => 'Commercial Lead Automation Search', 'is_indexable' => true, 'match_reason' => 'Lead Automation Primary Hub'];
    }

    return [
        'target_url'   => '/services/web-development',
        'service_slug' => 'web-development',
        'intent_type'  => 'General Web Development Search',
        'is_indexable' => true,
        'match_reason' => 'Web Development Primary Hub Fallback'
    ];
}

/**
 * Generate Ground-Truth Search Coverage & Entity Audit Report.
 *
 * @return array<string, mixed>
 */
function taxonomy_search_coverage_report(): array
{
    $matrix = taxonomy_all();
    $report = [
        'total_services' => count($matrix),
        'total_entities' => 0,
        'verified_entities_count' => 0,
        'candidate_entities_count' => 0,
        'unsupported_entities_count' => 0,
        'services' => []
    ];

    foreach ($matrix as $slug => $data) {
        $entities = (array)($data['entities'] ?? []);
        $vCount = 0;
        $cCount = 0;
        $uCount = 0;

        foreach ($entities as $meta) {
            $status = $meta['status'] ?? 'candidate';
            if ($status === 'verified') {
                $vCount++;
            } elseif ($status === 'candidate') {
                $cCount++;
            } elseif ($status === 'unsupported') {
                $uCount++;
            }
        }

        $report['total_entities'] += count($entities);
        $report['verified_entities_count'] += $vCount;
        $report['candidate_entities_count'] += $cCount;
        $report['unsupported_entities_count'] += $uCount;

        $report['services'][$slug] = [
            'title'               => $data['title'],
            'total_entities'      => count($entities),
            'verified_count'      => $vCount,
            'candidate_count'     => $cCount,
            'unsupported_count'   => $uCount,
            'sub_services_count'  => count($data['sub_services'] ?? []),
            'canonical_hub'       => '/services/' . $slug
        ];
    }

    return $report;
}
