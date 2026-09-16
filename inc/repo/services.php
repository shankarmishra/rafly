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
                        'slug'        => $slug,
                        'title'       => (string)$row['title'],
                        'icon'        => (string)($row['icon']     ?? 'layers'),
                        'key'         => (string)($row['key_name'] ?? 'web'),
                        'tagline'     => (string)($row['tagline']  ?? ''),
                        'intro'       => (string)($row['intro']    ?? ''),
                        'card'        => (string)($row['card']     ?? ''),
                        'wide'        => false,
                        'scene'       => 'browser',
                        '_extra_data' => (string)($row['extra_data'] ?? ''),
                    ];
                }
                // Merge deep fields from seed, then override with custom DB extra_data if present
                $seed = require __DIR__ . '/../data/services.php';
                foreach ($out as $slug => &$svc) {
                    if (isset($seed[$slug])) {
                        $svc = array_merge($seed[$slug], $svc);
                    }
                    if (!empty($svc['_extra_data'])) {
                        $decoded = json_decode($svc['_extra_data'], true);
                        if (is_array($decoded)) {
                            $svc = array_merge($svc, $decoded);
                        }
                    }
                    $svc['slug'] = $slug;
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
    $all = services_all();
    if (isset($all[$slug])) {
        return $all[$slug];
    }
    $aliasMap = [
        'ecommerce'                  => 'ecommerce-support',
        'performance-marketing'      => 'marketing-advertisement',

        // Web Development Sub-Services
        'frontend-development'       => 'web-development',
        'backend-development'        => 'web-development',
        'api-development'            => 'web-development',
        'custom-web-apps'            => 'web-development',

        // Web Security Sub-Services
        'api-security'               => 'web-security',
        'security-audit'             => 'web-security',
        'vulnerability-assessment'   => 'web-security',

        // App Development Sub-Services
        'android-app-development'    => 'app-development',
        'ios-app-development'        => 'app-development',
        'react-native-development'   => 'app-development',
        'flutter-development'        => 'app-development',
        'cross-platform-app-dev'     => 'app-development',

        // Performance Marketing Sub-Services
        'google-ads-management'      => 'marketing-advertisement',
        'meta-ads-agency'            => 'marketing-advertisement',
        'conversion-rate-optimization' => 'marketing-advertisement',

        // Content Creation Sub-Services
        'short-form-video'           => 'content-creation',
        'reels-production'           => 'content-creation',
        'social-media-creative'      => 'content-creation',

        // E-Commerce Sub-Services
        'shopify-development'        => 'ecommerce-support',
        'woocommerce-development'    => 'ecommerce-support',
        'custom-ecommerce-apps'      => 'ecommerce-support',

        // Lead Automation Sub-Services
        'crm-lead-routing'           => 'lead-automation',
        'whatsapp-lead-automation'   => 'lead-automation',
        'email-workflow-automation'  => 'lead-automation',
    ];

    if (isset($aliasMap[$slug], $all[$aliasMap[$slug]])) {
        $svc = $all[$aliasMap[$slug]];
        // Customize sub-topic titles if requested directly
        $subTopicTitles = [
            'frontend-development'       => 'Frontend Web Development',
            'backend-development'        => 'Backend Web Engineering',
            'api-development'            => 'API & Micro-Services Architecture',
            'custom-web-apps'            => 'Custom Web Application Development',
            'api-security'               => 'API Security & Gateway Hardening',
            'security-audit'             => 'Web Application Security Audit',
            'vulnerability-assessment'   => 'Surface Vulnerability Assessment',
            'android-app-development'    => 'Android App Development',
            'ios-app-development'        => 'iOS App Development',
            'react-native-development'   => 'React Native App Development',
            'flutter-development'        => 'Flutter App Development',
            'cross-platform-app-dev'     => 'Cross-Platform App Development',
            'google-ads-management'      => 'Google Ads Campaign Management',
            'meta-ads-agency'            => 'Meta Ads & Paid Social Strategy',
            'conversion-rate-optimization' => 'Conversion Rate Optimization (CRO)',
            'short-form-video'           => 'Short-Form Video & Reels Scripting',
            'reels-production'           => 'Reels & Social Creative Production',
            'social-media-creative'      => 'Social Media Creative & Copy Packets',
            'shopify-development'        => 'Shopify Store Development',
            'woocommerce-development'    => 'WooCommerce Development & Optimization',
            'custom-ecommerce-apps'      => 'Custom E-Commerce Storefront Apps',
            'crm-lead-routing'           => 'CRM Pipeline Lead Routing',
            'whatsapp-lead-automation'   => 'WhatsApp Lead Automation Workflows',
            'email-workflow-automation'  => 'Email & Inbound Workflow Automation',
        ];

        if (isset($subTopicTitles[$slug])) {
            $svc['title'] = $subTopicTitles[$slug];
            $svc['sub_topic_slug'] = $slug;
        }
        return $svc;
    }
    return null;
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
