<?php
/**
 * Normalised Global Geographic Repository.
 *
 * Interfaces with inc/data/geo.php to provide country, state, city,
 * and locality query interfaces.
 */

function geo_data(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    return $cache = require __DIR__ . '/../data/geo.php';
}

function geo_countries(): array
{
    return geo_data()['countries'] ?? [];
}

function geo_country_find(string $slugOrCode): ?array
{
    $slugOrCode = strtolower(trim($slugOrCode));
    foreach (geo_countries() as $code => $country) {
        if (strtolower($code) === $slugOrCode || strtolower($country['slug']) === $slugOrCode) {
            return $country;
        }
    }
    return null;
}

function geo_states(): array
{
    return geo_data()['states'] ?? [];
}

function geo_cities(): array
{
    return geo_data()['cities'] ?? [];
}

function geo_city_find(string $slug): ?array
{
    $slug = strtolower(trim($slug));
    return geo_cities()[$slug] ?? null;
}

function geo_locality_find(string $citySlug, string $localitySlug): ?array
{
    $city = geo_city_find($citySlug);
    if (!$city || empty($city['localities'])) {
        return null;
    }
    $localitySlug = strtolower(trim($localitySlug));
    return $city['localities'][$localitySlug] ?? null;
}
