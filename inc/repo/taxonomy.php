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
    $matrix = taxonomy_all();

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

    // 2. Check for sub-services with dedicated canonical routes
    foreach ($matrix as $serviceSlug => $data) {
        $subServices = (array)($data['sub_services'] ?? []);
        foreach ($subServices as $subSlug) {
            $subTitle = str_replace('-', ' ', $subSlug);
            if (str_contains($q, $subSlug) || str_contains($q, $subTitle)) {
                return [
                    'target_url'   => '/services/' . $subSlug,
                    'service_slug' => $serviceSlug,
                    'intent_type'  => 'Commercial Sub-Service',
                    'is_indexable' => true
                ];
            }
        }
    }

    // 3. Match against tech entity keywords to map to main service hub
    foreach ($matrix as $serviceSlug => $data) {
        $entities = (array)($data['entities'] ?? []);
        foreach ($entities as $entity) {
            $entityTitle = str_replace('-', ' ', $entity);
            if (str_contains($q, $entity) || str_contains($q, $entityTitle)) {
                return [
                    'target_url'   => '/services/' . $serviceSlug,
                    'service_slug' => $serviceSlug,
                    'intent_type'  => 'Commercial Technology Search',
                    'is_indexable' => true
                ];
            }
        }
    }

    // 4. Default fallback to primary service hub or contact
    if (str_contains($q, 'security') || str_contains($q, 'audit')) {
        return ['target_url' => '/services/web-security', 'service_slug' => 'web-security', 'intent_type' => 'Commercial', 'is_indexable' => true];
    }
    if (str_contains($q, 'app') || str_contains($q, 'mobile') || str_contains($q, 'android') || str_contains($q, 'ios')) {
        return ['target_url' => '/services/app-development', 'service_slug' => 'app-development', 'intent_type' => 'Commercial', 'is_indexable' => true];
    }
    if (str_contains($q, 'ad') || str_contains($q, 'ppc') || str_contains($q, 'marketing')) {
        return ['target_url' => '/services/performance-marketing', 'service_slug' => 'performance-marketing', 'intent_type' => 'Commercial', 'is_indexable' => true];
    }
    if (str_contains($q, 'content') || str_contains($q, 'copy') || str_contains($q, 'reels')) {
        return ['target_url' => '/services/content-creation', 'service_slug' => 'content-creation', 'intent_type' => 'Commercial', 'is_indexable' => true];
    }
    if (str_contains($q, 'ecom') || str_contains($q, 'shop') || str_contains($q, 'store')) {
        return ['target_url' => '/services/ecommerce', 'service_slug' => 'ecommerce', 'intent_type' => 'Commercial', 'is_indexable' => true];
    }
    if (str_contains($q, 'lead') || str_contains($q, 'crm') || str_contains($q, 'automation')) {
        return ['target_url' => '/services/lead-automation', 'service_slug' => 'lead-automation', 'intent_type' => 'Commercial', 'is_indexable' => true];
    }

    return [
        'target_url'   => '/services/web-development',
        'service_slug' => 'web-development',
        'intent_type'  => 'General Commercial',
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
