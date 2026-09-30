<?php
/** <head> meta tags and schema.org JSON-LD. */

function page_title(array $page): string
{
    if (!empty($page['full_title'])) {
        return $page['full_title'];
    }
    $brand = site('name');
    return $page['slug'] === '' ? "$brand | {$page['title']}" : "{$page['title']} | $brand";
}

function meta_tags(array $page): string
{
    $title = page_title($page);
    $desc = mb_strimwidth($page['description'], 0, 160, '…');
    $og = abs_url(ltrim(site('default_og_image'), '/'));
    $robots = site('indexable') ? 'index, follow' : 'noindex, nofollow';
    if (!empty($page['status']) && $page['status'] === 404) {
        $robots = 'noindex';
    }

    $out  = '<title>' . e($title) . "</title>\n";
    $out .= '<meta name="description" content="' . e($desc) . "\">\n";
    $out .= '<meta name="robots" content="' . $robots . "\">\n";
    if (!empty($page['canonical'])) {
        $out .= '<link rel="canonical" href="' . e($page['canonical']) . "\">\n";
    }
    $out .= '<meta property="og:type" content="' . ($page['template'] === 'blog-post' ? 'article' : 'website') . "\">\n";
    $out .= '<meta property="og:site_name" content="' . e(site('name')) . "\">\n";
    $out .= '<meta property="og:title" content="' . e($title) . "\">\n";
    $out .= '<meta property="og:description" content="' . e($desc) . "\">\n";
    $out .= '<meta property="og:url" content="' . e($page['canonical'] ?? '') . "\">\n";
    $out .= '<meta property="og:image" content="' . e($og) . "\">\n";
    $out .= "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    return $out;
}

function local_business_schema(): array
{
    $a = site('address');
    return [
        '@type' => 'PestControlService',
        '@id' => abs_url('') . '#business',
        'name' => site('name'),
        'legalName' => site('legal_name'),
        'url' => abs_url(''),
        'telephone' => site('phone'),
        'email' => site('email'),
        'image' => abs_url(ltrim(site('default_og_image'), '/')),
        'logo' => abs_url(ltrim(site('logo'), '/')),
        'priceRange' => '$$',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $a['street'],
            'addressLocality' => $a['city'],
            'addressRegion' => $a['region'],
            'postalCode' => $a['postal'],
            'addressCountry' => $a['country'],
        ],
        'geo' => ['@type' => 'GeoCoordinates', 'latitude' => site('geo')['lat'], 'longitude' => site('geo')['lng']],
        'openingHours' => site('hours'),
        'areaServed' => [
            ['@type' => 'State', 'name' => 'New Jersey'],
            ['@type' => 'City', 'name' => 'New York City'],
            ['@type' => 'AdministrativeArea', 'name' => 'South Florida'],
        ],
        'sameAs' => array_values(site('social')),
        'contactPoint' => array_values(array_filter([
            ['@type' => 'ContactPoint', 'telephone' => site('phone_href'), 'contactType' => 'customer service', 'areaServed' => ['NJ', 'NY']],
            site('toll_free') ? ['@type' => 'ContactPoint', 'telephone' => tel_href(site('toll_free')), 'contactType' => 'customer service', 'contactOption' => 'TollFree'] : null,
            !empty(site('florida_office')['phone']) ? ['@type' => 'ContactPoint', 'telephone' => site('florida_office')['phone_href'], 'contactType' => 'customer service', 'areaServed' => 'FL'] : null,
        ])),
    ];
}

function schema_graph(array $page): string
{
    $graph = [];
    $graph[] = local_business_schema();
    $graph[] = [
        '@type' => 'WebSite',
        '@id' => abs_url('') . '#website',
        'url' => abs_url(''),
        'name' => site('name'),
        'publisher' => ['@id' => abs_url('') . '#business'],
    ];

    if (!empty($page['breadcrumbs'])) {
        $items = [];
        foreach ($page['breadcrumbs'] as $i => [$label, $href]) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $label,
                'item' => $href ? abs_url(ltrim($href, '/')) : $page['canonical']];
        }
        $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    switch ($page['template']) {
        case 'blog-post':
            $graph[] = ['@type' => 'BlogPosting', 'headline' => $page['h1'], 'description' => $page['description'],
                'mainEntityOfPage' => $page['canonical'], 'author' => ['@id' => abs_url('') . '#business'],
                'publisher' => ['@id' => abs_url('') . '#business']];
            break;
        case 'service':
        case 'service-area':
        case 'city':
        case 'animal':
            $area = $page['town'] ?? null;
            $graph[] = array_filter([
                '@type' => 'Service',
                'name' => $page['h1'],
                'description' => $page['description'],
                'provider' => ['@id' => abs_url('') . '#business'],
                'areaServed' => $area ? ['@type' => 'City', 'name' => $area . (isset($page['state']) ? ', ' . $page['state'] : '')] : null,
                'url' => $page['canonical'],
            ]);
            break;
    }

    if (!empty($page['faq'])) {
        $graph[] = ['@type' => 'FAQPage', 'mainEntity' => array_map(fn($qa) => [
            '@type' => 'Question', 'name' => $qa[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
        ], $page['faq'])];
    }

    $json = json_encode(['@context' => 'https://schema.org', '@graph' => $graph],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    return '<script type="application/ld+json">' . $json . '</script>';
}

/** Stand-alone FAQPage JSON-LD for FAQs rendered inside a template. */
function faq_schema(array $faq): string
{
    $json = json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($qa) => [
        '@type' => 'Question', 'name' => $qa[0],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
    ], $faq)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    return '<script type="application/ld+json">' . $json . '</script>';
}
