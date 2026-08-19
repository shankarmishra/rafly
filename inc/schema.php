<?php
/**
 * JSON-LD structured data.
 *
 * The site had none. For a company with a real street address, a phone number
 * and an eight-question FAQ, that is free rich-result eligibility left on the
 * table — the FAQ block in particular.
 *
 * Everything is emitted as one @graph so there is a single script tag per page
 * and the nodes can cross-reference each other by @id.
 */

/** Stable @id anchors, so nodes can reference one another rather than repeat. */
function schema_id(string $fragment): string
{
    return SITE_ORIGIN . '/#' . $fragment;
}

/**
 * The Organization / LocalBusiness node. Emitted on every page.
 *
 * ProfessionalService is a subtype of LocalBusiness, which is what earns the
 * address, opening hours and phone their meaning in a knowledge panel.
 */
function schema_organization(): array
{
    return [
        '@type' => ['Organization', 'ProfessionalService'],
        '@id'   => schema_id('organization'),
        'name'  => 'Rafly Digital Growth Private Limited',
        'alternateName' => SITE_NAME,
        'url'   => SITE_ORIGIN . '/',
        // The square mark, not the horizontal lockup: Google rejects an
        // Organization logo under 112px on either axis, and the lockup is only
        // 99px tall.
        'logo'  => [
            '@type' => 'ImageObject',
            'url'   => SITE_ORIGIN . '/' . asset('assets/icon-512.png'),
            'width'  => 512,
            'height' => 512,
        ],
        // Same asset already promised at 1200x630 for og:image — reused rather
        // than adding a second curated image just for this node.
        'image' => SITE_ORIGIN . '/' . asset('assets/og-cover.png'),
        'description' => 'One partner, one bundled package — web development, content creation, '
                       . 'digital marketing, web security and e-commerce support.',
        // Qualitative, not a fabricated number: packages are quoted per engagement
        // ("Chat for Pricing" on every tier), so "$$" signals mid-market rather
        // than claiming a specific figure the site never publishes.
        'priceRange' => '$$',
        'email'     => CONTACT_EMAIL,
        'telephone' => CONTACT_PHONE,
        'address'   => schema_postal_address(),
        // A Google Maps search on the same address string the footer and
        // schema_postal_address() already render — real and clickable, unlike
        // GeoCoordinates would be: nobody has surveyed a lat/long for the
        // office, and inventing one is exactly the "fabricated structured
        // data" this file exists to avoid.
        'hasMap' => 'https://maps.google.com/?q=' . rawurlencode(
            (string)setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306')
        ),
        // Ordered nearest first. The registered office is in Greater Noida,
        // itself part of the Delhi NCR metro, and delivery reaches across
        // India — three honest, real scopes rather than the single hardcoded
        // country this used to declare.
        'areaServed' => schema_area_served(),
        // Lightweight refs, not full nodes — each Service's full description,
        // FAQ and offerings live once, on its own page. This is what lets the
        // Organization (emitted on every page) point at all five without
        // repeating any of that.
        'makesOffer' => array_values(array_map(
            static fn(string $slug, array $svc): array => [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    '@id'   => schema_id('service-' . $slug),
                    'name'  => (string)$svc['title'],
                    'url'   => SITE_ORIGIN . '/' . $slug,
                ],
            ],
            array_keys(services_all()),
            services_all()
        )),
        'openingHoursSpecification' => [[
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens'     => '09:00',
            'closes'    => '18:00',
        ]],
        'sameAs' => array_map(static fn(array $s) => $s['href'], SOCIAL_LINKS),
    ];
}

/**
 * The areaServed list shared by the Organization node and every Service node
 * — one place, so the two can never disagree the way a hand-copied literal
 * in each would eventually drift.
 *
 * @return array<int,array<string,string>>
 */
function schema_area_served(): array
{
    return [
        ['@type' => 'City',    'name' => BUSINESS_GEO_LOCALITY],
        ['@type' => 'Place',   'name' => BUSINESS_GEO_REGION],
        ['@type' => 'Country', 'name' => BUSINESS_GEO_COUNTRY],
    ];
}

/**
 * The PostalAddress node, derived from the contact.address SETTING.
 *
 * This was hardcoded, and that was a real bug rather than a shortcut: editing
 * the address in the admin moved it on the contact page, the footer and the
 * map link, but left the structured data — the copy search engines actually
 * read — pointing at the old premises, with nothing anywhere to say so.
 *
 * The setting is one human display string and JSON-LD wants parts, so the
 * string is split on commas by a fixed convention, read from the END because
 * that is the part with a stable shape:
 *
 *     …street parts…, <locality>, <postcode>
 *
 * A trailing all-digit segment (5-8 chars, so a PIN or a ZIP+4) is taken as
 * the postcode; the segment before it as the locality; everything left as the
 * street. If the string does not fit that shape nothing is guessed — the whole
 * value goes in streetAddress, which is valid and honest, rather than
 * scattering it across fields it might not belong in.
 *
 * Region and country stay constant. They are not in the setting, and inventing
 * a parser for them from a free-text field would fail silently the first time
 * somebody typed a different state.
 */
function schema_postal_address(): array
{
    $raw   = trim((string)setting('contact.address', ''));
    $parts = array_values(array_filter(array_map('trim', explode(',', $raw)), static fn($p) => $p !== ''));

    $postal   = '';
    $locality = '';

    $last = end($parts);
    if ($last !== false && preg_match('/^\d{5,8}$/', $last)) {
        $postal = array_pop($parts);
        if (count($parts) > 1) {
            $locality = array_pop($parts);
        }
    }

    $address = array_filter([
        '@type'           => 'PostalAddress',
        'streetAddress'   => implode(', ', $parts),
        'addressLocality' => $locality,
        'addressRegion'   => BUSINESS_GEO_STATE,
        'postalCode'      => $postal,
        'addressCountry'  => 'IN',
    ], static fn($v) => $v !== '');

    return $address;
}

/** The WebSite node, so the site name can be used in results. */
function schema_website(): array
{
    return [
        '@type'     => 'WebSite',
        '@id'       => schema_id('website'),
        'url'       => SITE_ORIGIN . '/',
        'name'      => SITE_NAME,
        'publisher' => ['@id' => schema_id('organization')],
        'inLanguage' => 'en-IN',
        // blog.php's ?q= search (inc/repo/content.php) is real and already
        // live — this just declares markup for a feature the site already
        // has, not a promise of one it doesn't.
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => SITE_ORIGIN . '/blog?q={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];
}

/**
 * The WebPage node shared by every simple content page (About, Team, a future
 * location page) — about.php and team.php used to each hand-roll this exact
 * shape, which is how they could drift: one node with an 'isPartOf' and
 * another without would be a real disagreement in the graph, not a stylistic
 * difference.
 *
 * $type lets a caller declare a more specific WebPage subtype (AboutPage is
 * the only one in use today) without needing a second function.
 */
function schema_webpage(string $slug, string $name, string $description, string $type = 'WebPage'): array
{
    return [
        '@type'       => $type,
        '@id'         => schema_id($slug),
        'url'         => SITE_ORIGIN . '/' . ltrim($slug, '/'),
        'name'        => $name,
        'description' => $description,
        'isPartOf'    => ['@id' => schema_id('website')],
        'about'       => ['@id' => schema_id('organization')],
    ];
}

/**
 * Breadcrumbs.
 * @param array<int,array{name:string,url:string}> $crumbs
 */
function schema_breadcrumbs(array $crumbs): array
{
    $items = [];
    foreach ($crumbs as $i => $c) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $c['name'],
            'item'     => SITE_ORIGIN . '/' . ltrim($c['url'], '/'),
        ];
    }

    return ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
}

/**
 * A CollectionPage/ItemList for a listing page (/insights, /case-studies) —
 * ties the individually-linked items together as one entity for crawlers,
 * the same way BreadcrumbList ties a page to its ancestors. Only name + url
 * per item: no numeric claims (ratings, prices) are fabricated here.
 *
 * @param array<int,array{name:string,url:string}> $items
 */
function schema_collection_list(string $name, string $url, array $items): array
{
    $listItems = [];
    foreach ($items as $i => $item) {
        $listItems[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'url'      => SITE_ORIGIN . '/' . ltrim($item['url'], '/'),
            'name'     => $item['name'],
        ];
    }

    return [
        '@type'    => 'CollectionPage',
        'name'     => $name,
        'url'      => SITE_ORIGIN . '/' . ltrim($url, '/'),
        'mainEntity' => [
            '@type'           => 'ItemList',
            'itemListElement' => $listItems,
        ],
    ];
}

/**
 * A single service offering.
 *
 * $id, when given, becomes this node's own @id — schema_organization()'s
 * makesOffer references services by this same id (schema_id('service-<slug>'))
 * without repeating the full node, so passing it is what lets the two halves
 * of the same Service actually resolve to one another in the graph.
 *
 * @param array<int,string> $offerings
 */
function schema_service(string $name, string $description, array $offerings = [], ?string $id = null): array
{
    $node = [
        '@type'       => 'Service',
        'name'        => $name,
        'description' => $description,
        'provider'    => ['@id' => schema_id('organization')],
        'areaServed'  => schema_area_served(),
    ];

    if ($id !== null) {
        $node['@id'] = $id;
    }

    if ($offerings) {
        $node['hasOfferCatalog'] = [
            '@type' => 'OfferCatalog',
            'name'  => $name,
            'itemListElement' => array_map(static fn(string $o) => [
                '@type' => 'Offer',
                'itemOffered' => ['@type' => 'Service', 'name' => $o],
            ], $offerings),
        ];
    }

    return $node;
}

/**
 * A named team member.
 *
 * sameAs is the point of this node. Two authoritative profile URLs are what let
 * a search engine resolve "who is this person" rather than guessing from a
 * name, and team_members already stores both — without this they do nothing
 * beyond rendering two buttons.
 *
 * Only call this for people who are NOT placeholders. A placeholder renders a
 * visible orange badge, but structured data has no equivalent: a Person node
 * asserts to a machine that someone exists, and nothing downstream shows the
 * caveat. team.php filters on that before calling in.
 *
 * @param array<string,mixed> $row A team_members row, joined to media.
 */
function schema_person(array $row): array
{
    $node = [
        '@type'    => 'Person',
        'name'     => (string)$row['name'],
        'jobTitle' => (string)$row['role'],
        'worksFor' => ['@id' => schema_id('organization')],
    ];

    // A stable @id, keyed on the team_members row id, is what lets
    // schema_article() reference this exact node as an article's author
    // instead of inlining a bare name that repeats — and could drift from —
    // this one.
    if (!empty($row['id'])) {
        $node['@id'] = schema_id('person-' . (int)$row['id']);
    }

    if (!empty($row['brief'])) {
        $node['description'] = (string)$row['brief'];
    }

    // Absolute, not site_path(): JSON-LD consumers are off-site. Same reason
    // schema_article() builds its image URL this way.
    if (!empty($row['photo'])) {
        $node['image'] = SITE_ORIGIN . '/uploads/' . rawurlencode((string)$row['photo']);
    }

    // Guaranteed absolute http(s) by the validation in admin/team.php.
    $sameAs = array_values(array_filter([
        (string)($row['github_url'] ?? ''),
        (string)($row['linkedin_url'] ?? ''),
    ], 'strlen'));

    if ($sameAs) {
        $node['sameAs'] = $sameAs;
    }

    return $node;
}

/**
 * FAQ rich result.
 * @param array<int,array{q:string,a:string}> $faqs
 */
function schema_faq(array $faqs): array
{
    return [
        '@type' => 'FAQPage',
        'mainEntity' => array_map(static fn(array $f) => [
            '@type' => 'Question',
            'name'  => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ], $faqs),
    ];
}

/**
 * A single article, for blog-post.php.
 *
 * datePublished/dateModified are emitted as ISO-8601 with an offset, which is
 * what Google expects — a bare 'Y-m-d H:i:s' is ambiguous about timezone and
 * gets ignored. Both come straight from the posts table.
 *
 * image and author are taken from the row when the join supplied them. The
 * structured data has to describe the page a reader actually gets: claiming
 * the generic og-cover for an article that visibly has its own cover, or
 * crediting the organisation for one that carries a person's byline, is the
 * kind of mismatch rich-result validation flags.
 *
 * @param array<string,mixed> $post A row from posts, optionally joined to
 *                                  media (cover) and team_members (author).
 */
function schema_article(array $post): array
{
    $url = SITE_ORIGIN . '/blog/' . rawurlencode((string)$post['slug']);

    $node = [
        '@type'            => 'BlogPosting',
        'headline'         => (string)$post['title'],
        'description'      => (string)($post['meta_desc'] ?: $post['excerpt']),
        'url'              => $url,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
        'publisher'        => ['@id' => schema_id('organization')],
        'image'            => !empty($post['cover'])
            ? SITE_ORIGIN . '/uploads/' . rawurlencode((string)$post['cover'])
            : SITE_ORIGIN . '/assets/og-cover.png',
    ];

    // An author is optional in the schema but a missing one is a Search Console
    // warning, so fall back to the organisation rather than omitting it.
    //
    // A full Person node (via schema_person(), same as team.php renders for
    // this exact row), not a bare name — it carries the same @id
    // schema_person() gives that person on /team, which is what lets a
    // consumer recognise this is the same entity rather than an unrelated
    // string that happens to match.
    $node['author'] = !empty($post['author_id']) && !empty($post['author_name'])
        ? schema_person([
            'id'    => $post['author_id'],
            'name'  => $post['author_name'],
            'role'  => $post['author_role'] ?? '',
            'photo' => $post['author_photo'] ?? null,
        ])
        : ['@id' => schema_id('organization')];

    foreach (['published_at' => 'datePublished', 'updated_at' => 'dateModified'] as $col => $prop) {
        if (!empty($post[$col])) {
            $node[$prop] = date(DATE_ATOM, strtotime((string)$post[$col]));
        }
    }

    return $node;
}

/**
 * Renders the graph as a script tag.
 *
 * JSON_UNESCAPED_SLASHES and _UNICODE keep URLs and any non-ASCII readable.
 * JSON_HEX_TAG is the important one: it escapes < and > so a stray "</script>"
 * inside any string cannot break out of the tag.
 *
 * @param array<int,array> $nodes
 */
function schema_render(array $nodes): string
{
    $graph = [
        '@context' => 'https://schema.org',
        '@graph'   => array_values(array_filter($nodes)),
    ];

    $json = json_encode(
        $graph,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
    );

    if ($json === false) {
        return '';
    }

    return '<script type="application/ld+json">' . $json . '</script>';
}
