<?php
/**
 * ServiceRepository.
 *
 * The ONLY way a template should learn about a service. Templates call
 * services_all() / service_find() / services_labels() and receive plain arrays;
 * they do not know, and must not care, whether those arrays came from a
 * database, a seed file, or a cache.
 *
 * Today the source is inc/data/services.php, a literal array — there is no
 * `services` table yet. That is stated plainly rather than disguised behind a
 * fake query, because a fake query is a lie that costs someone an afternoon
 * later. services_source() reports which source answered, so the admin can show
 * the truth instead of implying the content is already editable.
 *
 * WHEN THE TABLE ARRIVES, this is the entire integration:
 *   - add a `services` table + admin CRUD
 *   - add the db_available() branch marked below
 *   - delete nothing else
 * Every template keeps working, because none of them reference the seed.
 *
 * This is the same shape bundles_all() (inc/helpers.php) already uses: read the
 * database when it answers, fall back to the constant when it does not, and
 * return one shape either way.
 */

/**
 * Every service, in presentation order, keyed by slug.
 *
 * @return array<string, array<string, mixed>>
 */
function services_all(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    /* --- DB branch goes here when a `services` table exists ---------------
       if (db_available()) {
           $rows = all('SELECT ... FROM services WHERE is_published ORDER BY sort_order, id');
           if ($rows) { return $cache = services_shape($rows); }
       }
       Falling through to the seed on an empty result is deliberate: an empty
       table means "nobody has migrated the content yet", not "this business
       offers no services". bundles_all() makes the same call.
       -------------------------------------------------------------------- */

    $seed = require __DIR__ . '/../data/services.php';

    // Normalise so a template can rely on every key existing, whatever the
    // source. A missing presentation key must never fatal a page.
    $out = [];
    foreach ($seed as $slug => $svc) {
        $out[$slug] = $svc + [
            'slug'  => $slug,
            'title' => $slug,
            'icon'  => 'layers',
            'key'   => 'web',
            'wide'  => false,
            'scene' => 'browser',
            'card'  => '',
        ];
        $out[$slug]['slug'] = $slug;   // seed must never override its own key
    }

    return $cache = $out;
}

/**
 * One service, or null when the slug is not one we offer.
 *
 * Null rather than a fallback service: service.php turns this into a real 404.
 * Serving the web-development page for /anything at HTTP 200 is a soft-404 that
 * gets indexed as duplicate content.
 */
function service_find(string $slug): ?array
{
    return services_all()[$slug] ?? null;
}

/**
 * slug => display label, for the nav dropdown, the footer and ?service=
 * validation.
 *
 * inc/config.php's SERVICES const is kept as the no-database, no-seed floor
 * (partials/header.php runs before this repository is guaranteed loaded on
 * some error paths), but this is what templates should call.
 *
 * @return array<string, string>
 */
function services_labels(): array
{
    $out = [];
    foreach (services_all() as $slug => $svc) {
        $out[$slug] = (string)$svc['title'];
    }
    return $out ?: SERVICES;
}

/**
 * Which source answered: 'database' | 'seed'.
 * Rendered in the admin so nobody has to read code to find out whether editing
 * a service is possible yet.
 */
function services_source(): string
{
    return 'seed';   // flip to 'database' inside the DB branch above
}
