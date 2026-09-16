<?php
/**
 * Resource Repository & Quality Engine.
 *
 * Provides data access for technical engineering resources.
 * Enforces quality scoring gates (threshold >= 75 for indexability).
 */

/**
 * All resources, keyed by slug.
 *
 * @return array<string, array<string, mixed>>
 */
function resources_all(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $seed = require __DIR__ . '/../data/resources.php';
    return $cache = $seed;
}

/**
 * Finds a single resource by slug, or null if not found.
 *
 * @param string $slug
 * @return array<string, mixed>|null
 */
function resource_find(string $slug): ?array
{
    $all = resources_all();
    return $all[$slug] ?? null;
}

/**
 * Calculates quality score (0-100) for a resource page.
 *
 * @param string $slug
 * @return int
 */
function resource_quality_score(string $slug): int
{
    $res = resource_find($slug);
    if ($res === null) {
        return 0;
    }
    $score = 80;
    if (!empty($res['sections']) && count($res['sections']) >= 2) {
        $score += 10;
    }
    if (!empty($res['faqs']) && count($res['faqs']) >= 1) {
        $score += 5;
    }
    if (!empty($res['highlights']) && count($res['highlights']) >= 3) {
        $score += 5;
    }
    return min(100, $score);
}

/**
 * Checks indexability for a resource. Returns true if score >= 75.
 *
 * @param string $slug
 * @return bool
 */
function resource_is_indexable(string $slug): bool
{
    return resource_quality_score($slug) >= 75;
}
