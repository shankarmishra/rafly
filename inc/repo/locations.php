<?php
/**
 * LocationRepository & Global Location Quality Engine.
 *
 * Provides access to geographic location data and evaluates
 * quality thresholds to prevent doorway pages or index bloat.
 */

/**
 * Get all configured locations keyed by slug.
 *
 * @return array<string, array<string, mixed>>
 */
function locations_all(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $locations = require __DIR__ . '/../data/locations.php';
    return $cache = $locations;
}

/**
 * Find a single location by slug.
 */
function location_find(string $slug): ?array
{
    $locations = locations_all();
    return $locations[$slug] ?? null;
}

/**
 * Quality scoring engine for location pages.
 * Returns an integer score (0-100).
 */
function location_quality_score(string $slug): int
{
    $loc = location_find($slug);
    if ($loc === null) {
        return 0;
    }

    $score = (int)($loc['quality_score'] ?? 50);

    // Boost physical HQ
    if (!empty($loc['is_physical'])) {
        $score += 20;
    }

    // Boost if rich local context and highlights exist
    if (!empty($loc['local_context']) && strlen((string)$loc['local_context']) > 80) {
        $score += 10;
    }

    if (!empty($loc['highlights']) && count((array)$loc['highlights']) >= 3) {
        $score += 10;
    }

    return min(100, $score);
}

/**
 * Service x Location combination lookup and Quality Engine evaluation.
 *
 * Ensures only legitimate combinations with sufficient quality are indexable.
 *
 * @return array<string, mixed>|null
 */
function service_location_find(string $serviceSlug, string $locationSlug): ?array
{
    $service = service_find($serviceSlug);
    $location = location_find($locationSlug);

    if ($service === null || $location === null) {
        return null;
    }

    $qualityScore = service_location_quality_score($serviceSlug, $locationSlug);
    $isIndexable = $qualityScore >= 75; // Quality threshold gate

    $serviceTitle = $service['title'];
    $locationName = $location['name'];
    $cityName = $location['city'] ?? $locationName;
    $countryName = $location['country'] ?? 'India';

    $canonicalUrl = $isIndexable
        ? site_path('/services/' . $service['slug'] . '/' . $location['slug'])
        : service_url($service['slug']); // Fallback to parent service hub if non-indexable

    return [
        'service'          => $service,
        'location'         => $location,
        'title'            => $serviceTitle . ' in ' . $locationName . ' | RAFLY',
        'seo_title'        => $serviceTitle . ' Services in ' . $locationName . ' (' . $countryName . ') | RAFLY',
        'meta_desc'        => 'Professional ' . strtolower($serviceTitle) . ' services in ' . $locationName . '. Delivering high-speed architectures, security hardening, and digital growth.',
        'canonical'        => $canonicalUrl,
        'quality_score'    => $qualityScore,
        'is_indexable'     => $isIndexable,
        'intro'            => 'RAFLY provides specialized ' . strtolower($serviceTitle) . ' services for businesses and enterprises in ' . $locationName . ' (' . $countryName . '). Delivered via our ' . ($location['is_physical'] ? 'registered office' : 'regional engineering node') . '.',
        'service_highlights' => $service['highlights'] ?? [],
        'location_highlights' => $location['highlights'] ?? [],
        'faqs'             => array_merge(
            [
                [
                    'q' => 'Do you deliver ' . $serviceTitle . ' in ' . $locationName . '?',
                    'a' => 'Yes. RAFLY delivers ' . strtolower($serviceTitle) . ' for clients in ' . $locationName . ' via ' . ($location['is_physical'] ? 'our registered office' : 'on-site consultations and remote engineering pipelines') . '.'
                ],
                [
                    'q' => 'How can a business in ' . $locationName . ' start a ' . $serviceTitle . ' project?',
                    'a' => 'Submit your project scope via our intake form or contact us directly on WhatsApp at +91 8796882212. Our engineering leads respond within 60 seconds.'
                ]
            ],
            $service['faqs'] ?? []
        )
    ];
}

/**
 * Quality scoring engine for Service x Location page combinations.
 */
function service_location_quality_score(string $serviceSlug, string $locationSlug): int
{
    $service = service_find($serviceSlug);
    $location = location_find($locationSlug);

    if ($service === null || $location === null) {
        return 0;
    }

    $baseScore = location_quality_score($locationSlug);

    // HQ and On-Site Hubs always pass quality threshold for all services
    if (in_array($location['type'] ?? '', ['hq', 'onsite_hub'], true)) {
        return max(85, $baseScore);
    }

    // Check if the service is featured in the location's explicit service list
    $featuredServices = (array)($location['featured_services'] ?? []);
    if (in_array($serviceSlug, $featuredServices, true)) {
        return max(78, $baseScore);
    }

    // Default for secondary global/regional combinations without specific custom text
    return 60; // Below threshold 75 -> non-indexable to avoid doorway/index bloat!
}

/**
 * Returns list of indexable location URLs for XML Sitemap.
 *
 * @return list<array{loc:string,mod:string,freq:string,pri:string}>
 */
function locations_sitemap_urls(): array
{
    $urls = [];
    $locations = locations_all();
    $services = services_all();

    // 1. Indexable location pages
    foreach ($locations as $slug => $loc) {
        if (!empty($loc['is_indexable']) && location_quality_score($slug) >= 75) {
            $urls[] = [
                'loc'  => 'locations/' . $slug,
                'freq' => 'monthly',
                'pri'  => !empty($loc['is_physical']) ? '0.9' : '0.7',
            ];

            // 2. Indexable service x location pages
            foreach ($services as $svcSlug => $svc) {
                if (service_location_quality_score($svcSlug, $slug) >= 75) {
                    $urls[] = [
                        'loc'  => 'services/' . service_url_slug($svcSlug) . '/' . $slug,
                        'freq' => 'monthly',
                        'pri'  => '0.7',
                    ];
                }
            }
        }
    }

    return $urls;
}

/** Helper to extract raw slug from alias */
function service_url_slug(string $slug): string
{
    $aliasMap = [
        'ecommerce-support'       => 'ecommerce',
        'marketing-advertisement' => 'performance-marketing',
    ];
    return $aliasMap[$slug] ?? $slug;
}
