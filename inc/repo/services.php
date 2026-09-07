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

    /* --- DB branch: reads from `services` table when it exists and has rows --
       Falls through to the seed when:
         - the DB is unavailable (no creds, connection error)
         - the table does not exist yet (migration not run)
         - the table exists but is empty (nobody has seeded it yet)
       The last case is deliberate: an empty table means "migration ran but
       content hasn't been entered yet", not "no services", so the seed keeps
       the site running while content is being populated in the admin.
       -------------------------------------------------------------------- */
    if (db_available()) {
        try {
            $rows = all('SELECT * FROM services WHERE is_published = ? ORDER BY sort_order, id', [true]);
            if ($rows) {
                $out = [];
                foreach ($rows as $row) {
                    $slug = (string)$row['slug'];
                    $out[$slug] = [
                        'slug'       => $slug,
                        'title'      => (string)$row['title'],
                        'icon'       => (string)($row['icon']     ?? 'layers'),
                        'key'        => (string)($row['key_name'] ?? 'web'),
                        'tagline'    => (string)($row['tagline']  ?? ''),
                        'intro'      => (string)($row['intro']    ?? ''),
                        'card'       => (string)($row['card']     ?? ''),
                        'wide'       => false,
                        'scene'      => 'browser',
                        // Fields below are not stored in DB yet — they require
                        // the full seed data for service detail pages. Fall back
                        // to seed for any slug we recognise, so that the detail
                        // page still renders even before seed data is migrated.
                    ];
                }
                // Merge deep fields (faqs, process, deliverables …) from seed
                // for any slug that has a DB row but no extended data in the DB.
                $seed = require __DIR__ . '/../data/services.php';
                foreach ($out as $slug => &$svc) {
                    if (isset($seed[$slug])) {
                        $svc = array_merge($seed[$slug], $svc);
                        $svc['slug'] = $slug;
                    }
                }
                unset($svc);
                return $cache = $out;
            }
        } catch (\Throwable $e) {
            // Table does not exist yet — fall through to seed silently.
        }
    }

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
    if (!db_available()) {
        return 'seed';
    }
    try {
        $count = scalar('SELECT count(*) FROM services WHERE is_published = ?', [true]);
        return ((int)$count > 0) ? 'database' : 'seed';
    } catch (\Throwable $e) {
        return 'seed';   // table doesn't exist yet
    }
}
