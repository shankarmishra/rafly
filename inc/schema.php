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
        'address'   => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'A523, T3, NX-One, Tech Zone IV',
            'addressLocality' => 'Greater Noida West',
            'addressRegion'   => 'Uttar Pradesh',
            'postalCode'      => '201306',
            'addressCountry'  => 'IN',
        ],
        'openingHoursSpecification' => [[
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens'     => '09:00',
            'closes'    => '18:00',
        ]],
        'sameAs' => array_map(static fn(array $s) => $s['href'], SOCIAL_LINKS),
    ];
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
 * @param array<int,string> $offerings
 */
function schema_service(string $name, string $description, array $offerings = []): array
{
    $node = [
        '@type'       => 'Service',
        'name'        => $name,
        'description' => $description,
        'provider'    => ['@id' => schema_id('organization')],
        'areaServed'  => ['@type' => 'Country', 'name' => 'India'],
    ];

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
    $node['author'] = !empty($post['author_name'])
        ? ['@type' => 'Person', 'name' => (string)$post['author_name']]
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
