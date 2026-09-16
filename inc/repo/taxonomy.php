<?php
/**
 * Taxonomy Repository.
 *
 * Provides structured query-to-canonical resolution, taxonomy lookups, and search coverage reporting.
 */

/**
 * Returns full taxonomy data matrix across all 7 services.
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
 * Find matching primary service for a given technology or entity slug.
 */
function taxonomy_find_service(string $entityOrSlug): ?array
{
    $matrix = taxonomy_all();
    $clean = strtolower(trim($entityOrSlug));

    // Direct match against primary service slug
    if (isset($matrix[$clean])) {
        return $matrix[$clean];
    }

    // Match against sub_services or entities
    foreach ($matrix as $serviceSlug => $data) {
        $subServices = (array)($data['sub_services'] ?? []);
        $entities    = (array)($data['entities'] ?? []);

        if (in_array($clean, $subServices, true) || in_array($clean, $entities, true)) {
            return $data;
        }
    }

    return null;
}

/**
 * Universal Query → Canonical Page Resolver Engine.
 * Maps any search query to the SINGLE strongest authoritative canonical destination.
 * Prevents keyword cannibalization and thin doorway pages.
 *
 * @param string $query User search query or target topic
 * @return array{target_url: string, service_slug: string, intent_type: string, is_indexable: bool}
 */
function taxonomy_resolve_canonical(string $query): array
{
    $q = strtolower(trim($query));

    // 1. Check for specific high-value specialized landing pages
    if (str_contains($q, 'malware') || str_contains($q, 'emergency security') || str_contains($q, 'hacked')) {
        return [
            'target_url'   => '/landing/security-emergency',
            'service_slug' => 'web-security',
            'intent_type'  => 'Transactional Emergency',
            'is_indexable' => true
        ];
    }
    if (str_contains($q, 'website audit') || str_contains($q, 'free audit') || str_contains($q, 'forensic audit')) {
        return [
            'target_url'   => '/landing/website-audit',
            'service_slug' => 'web-security',
            'intent_type'  => 'Commercial Lead',
            'is_indexable' => true
        ];
    }
    if (str_contains($q, 'whatsapp') && (str_contains($q, 'lead') || str_contains($q, 'automation') || str_contains($q, 'api'))) {
        return [
            'target_url'   => '/landing/whatsapp-automation',
            'service_slug' => 'lead-automation',
            'intent_type'  => 'Commercial Lead',
            'is_indexable' => true
        ];
    }

    // 2. High-precision sub-service phrase matching
    $phraseMap = [
        'react native'          => ['url' => '/services/react-native-development', 'svc' => 'app-development'],
        'flutter'               => ['url' => '/services/flutter-development', 'svc' => 'app-development'],
        'ios'                   => ['url' => '/services/ios-app-development', 'svc' => 'app-development'],
        'swift'                 => ['url' => '/services/ios-app-development', 'svc' => 'app-development'],
        'android'               => ['url' => '/services/android-app-development', 'svc' => 'app-development'],
        'kotlin'                => ['url' => '/services/android-app-development', 'svc' => 'app-development'],
        'cross platform'        => ['url' => '/services/cross-platform-app-dev', 'svc' => 'app-development'],
        'google ads'            => ['url' => '/services/google-ads-management', 'svc' => 'performance-marketing'],
        'meta ads'              => ['url' => '/services/meta-ads-agency', 'svc' => 'performance-marketing'],
        'facebook ads'          => ['url' => '/services/meta-ads-agency', 'svc' => 'performance-marketing'],
        'conversion rate'       => ['url' => '/services/conversion-rate-optimization', 'svc' => 'performance-marketing'],
        'reels'                 => ['url' => '/services/short-form-video', 'svc' => 'content-creation'],
        'short form'            => ['url' => '/services/short-form-video', 'svc' => 'content-creation'],
        'social media creative' => ['url' => '/services/social-media-creative', 'svc' => 'content-creation'],
        'shopify'               => ['url' => '/services/shopify-development', 'svc' => 'ecommerce'],
        'woocommerce'           => ['url' => '/services/woocommerce-development', 'svc' => 'ecommerce'],
        'custom ecommerce'      => ['url' => '/services/custom-ecommerce-apps', 'svc' => 'ecommerce'],
        'crm'                   => ['url' => '/services/crm-lead-routing', 'svc' => 'lead-automation'],
        'email workflow'        => ['url' => '/services/email-workflow-automation', 'svc' => 'lead-automation'],
        'api security'          => ['url' => '/services/api-security', 'svc' => 'web-security'],
        'vulnerability'         => ['url' => '/services/vulnerability-assessment', 'svc' => 'web-security'],
        'frontend'              => ['url' => '/services/frontend-development', 'svc' => 'web-development'],
        'backend'               => ['url' => '/services/backend-development', 'svc' => 'web-development'],
    ];

    foreach ($phraseMap as $phrase => $info) {
        if (str_contains($q, $phrase)) {
            return [
                'target_url'   => $info['url'],
                'service_slug' => $info['svc'],
                'intent_type'  => 'Commercial Sub-Service Intent',
                'is_indexable' => true
            ];
        }
    }

    // 3. Fallback to primary service hubs
    if (str_contains($q, 'security') || str_contains($q, 'audit') || str_contains($q, 'owasp')) {
        return ['target_url' => '/services/web-security', 'service_slug' => 'web-security', 'intent_type' => 'Commercial Security Search', 'is_indexable' => true];
    }
    if (str_contains($q, 'app') || str_contains($q, 'mobile')) {
        return ['target_url' => '/services/app-development', 'service_slug' => 'app-development', 'intent_type' => 'Commercial App Search', 'is_indexable' => true];
    }
    if (str_contains($q, 'ad') || str_contains($q, 'ppc') || str_contains($q, 'marketing')) {
        return ['target_url' => '/services/performance-marketing', 'service_slug' => 'performance-marketing', 'intent_type' => 'Commercial Marketing Search', 'is_indexable' => true];
    }
    if (str_contains($q, 'content') || str_contains($q, 'copy') || str_contains($q, 'script')) {
        return ['target_url' => '/services/content-creation', 'service_slug' => 'content-creation', 'intent_type' => 'Commercial Content Search', 'is_indexable' => true];
    }
    if (str_contains($q, 'ecom') || str_contains($q, 'shop') || str_contains($q, 'store')) {
        return ['target_url' => '/services/ecommerce', 'service_slug' => 'ecommerce', 'intent_type' => 'Commercial E-Commerce Search', 'is_indexable' => true];
    }
    if (str_contains($q, 'lead') || str_contains($q, 'automation') || str_contains($q, 'form')) {
        return ['target_url' => '/services/lead-automation', 'service_slug' => 'lead-automation', 'intent_type' => 'Commercial Lead Automation Search', 'is_indexable' => true];
    }

    return [
        'target_url'   => '/services/web-development',
        'service_slug' => 'web-development',
        'intent_type'  => 'General Web Development Search',
        'is_indexable' => true
    ];
}

/**
 * Generate Search Coverage Report summarizing all 7 services.
 *
 * @return array<string, mixed>
 */
function taxonomy_search_coverage_report(): array
{
    $matrix = taxonomy_all();
    $report = [
        'total_services' => count($matrix),
        'services' => []
    ];

    foreach ($matrix as $slug => $data) {
        $report['services'][$slug] = [
            'title'               => $data['title'],
            'platforms_count'     => count($data['platforms'] ?? []),
            'frameworks_count'    => count($data['frameworks'] ?? []),
            'languages_count'     => count($data['languages'] ?? []),
            'capabilities_count'  => count($data['capabilities'] ?? []),
            'use_cases_count'     => count($data['use_cases'] ?? []),
            'industries_count'    => count($data['industries'] ?? []),
            'sub_services_count'  => count($data['sub_services'] ?? []),
            'entities_count'      => count($data['entities'] ?? []),
            'canonical_hub'       => '/services/' . $slug
        ];
    }

    return $report;
}
