<?php
/**
 * Turns a URL slug into a page model:
 *   template, title, h1, description, breadcrumbs, schema, plus type-specific vars.
 * Returns null for unknown URLs (404).
 */

function resolve_page(string $slug): ?array
{
    $slug = trim($slug, '/');
    if (!slug_exists($slug)) {
        return null;
    }

    $page = classify($slug);
    $page['slug'] = $slug;
    // /home2 duplicates the homepage, so it points search engines at "/".
    $page['canonical'] = abs_url($slug === 'home2' ? '' : $slug);
    $page['breadcrumbs'] ??= [['Home', '/'], [$page['h1'], null]];

    // Imported content from the live site overrides the generated body and meta.
    if ($imported = imported_content($slug)) {
        $page['imported_html'] = $imported['html'];
        foreach (['title', 'description', 'h1'] as $k) {
            if (!empty($imported['meta'][$k])) {
                $page[$k] = $imported['meta'][$k];
            }
        }
        $page['full_title'] = $imported['meta']['title'] ?? null;
    }
    return $page;
}

function classify(string $slug): array
{
    $brand = site('name');

    if ($slug === '' || $slug === 'home2') {
        return [
            'template' => 'home',
            'title' => 'Pest Control & Exterminator in NJ, NY & South Florida',
            'h1' => 'Effective Pest Control Solutions for Your Home & Business',
            'description' => "$brand is a family-owned, QualityPro-certified pest control company serving New Jersey, New York City and South Florida. Termites, rodents, bed bugs, wildlife and more.",
            'breadcrumbs' => [],
        ];
    }

    if ($slug === 'contact') {
        return ['template' => 'contact', 'title' => 'Contact Us', 'h1' => 'Contact Amco Pest Solutions',
            'description' => "Schedule an inspection or request a free estimate from $brand. Call or send us a message."];
    }
    if ($slug === 'services') {
        return ['template' => 'services', 'title' => 'Pest Control Services', 'h1' => 'Our Pest Control Services',
            'description' => "Termite, rodent, bed bug, wildlife, mosquito and general pest control services from $brand for homes and businesses."];
    }
    if ($slug === 'pest-library') {
        return ['template' => 'library-index', 'title' => 'Pest Library', 'h1' => 'Pest Library',
            'description' => 'Identify common pests in New Jersey, New York and Florida: what they look like, the problems they cause and how to get rid of them.'];
    }
    if ($slug === 'service-areas') {
        return ['template' => 'service-areas', 'title' => 'Service Areas', 'h1' => 'Areas We Serve',
            'description' => "$brand serves communities across New Jersey, the five boroughs of New York City and South Florida."];
    }
    if ($slug === 'news-and-updates') {
        return ['template' => 'blog-index', 'title' => 'News & Updates', 'h1' => 'Pest Control News & Updates',
            'description' => 'Pest prevention tips, seasonal alerts and company news from Amco Pest Solutions.'];
    }

    if (str_starts_with($slug, 'news-and-updates/')) {
        $post = substr($slug, strlen('news-and-updates/'));
        $title = humanize($post);
        return ['template' => 'blog-post', 'title' => $title, 'h1' => $title,
            'description' => "$title – pest control advice from the team at $brand.",
            'post_slug' => $post,
            'breadcrumbs' => [['Home', '/'], ['News & Updates', '/news-and-updates'], [$title, null]]];
    }

    if (isset(SERVICES[$slug])) {
        [$name, $blurb, $lib] = SERVICES[$slug];
        return ['template' => 'service', 'title' => "$name Services in NJ, NY & FL", 'h1' => $name,
            'description' => "$blurb Call $brand for a free inspection.",
            'service' => $slug, 'name' => $name, 'blurb' => $blurb, 'library' => $lib,
            'breadcrumbs' => [['Home', '/'], ['Services', '/services'], [$name, null]]];
    }
    if (isset(OTHER_SERVICES[$slug])) {
        [$name, $blurb] = OTHER_SERVICES[$slug];
        return ['template' => 'service', 'title' => $name, 'h1' => $name,
            'description' => "$blurb Serving New Jersey, New York City and South Florida.",
            'service' => $slug, 'name' => $name, 'blurb' => $blurb, 'library' => null,
            'breadcrumbs' => [['Home', '/'], ['Services', '/services'], [$name, null]]];
    }

    if (isset(CORE_PAGES[$slug])) {
        [$title, $h1, $desc] = CORE_PAGES[$slug];
        return ['template' => 'generic', 'title' => $title, 'h1' => $h1, 'description' => $desc];
    }

    if (isset(COUNTIES[$slug])) {
        [$name, $st] = COUNTIES[$slug];
        return ['template' => 'county', 'title' => "Pest Control in $name, $st", 'h1' => "Pest Control & Exterminator Services in $name",
            'description' => "Local pest control, termite, rodent, bed bug and wildlife services throughout $name, $st from $brand.",
            'county' => $slug, 'name' => $name, 'state' => $st,
            'breadcrumbs' => [['Home', '/'], ['Service Areas', '/service-areas'], [$name, null]]];
    }

    if (str_starts_with($slug, 'pest-library-')) {
        $key = substr($slug, strlen('pest-library-'));
        $name = PEST_NAMES[$key] ?? humanize($key);
        return ['template' => 'library', 'title' => "$name: Identification & Control", 'h1' => $name,
            'description' => "Learn how to identify $name, the risks they pose, how to prevent them and when to call a professional exterminator.",
            'pest' => $key, 'name' => $name,
            'breadcrumbs' => [['Home', '/'], ['Pest Library', '/pest-library'], [$name, null]]];
    }

    // /service-areas-{town}-nj-{service}
    if (preg_match('/^service-areas-(.+)-(nj|ny|fl)-(' . implode('|', array_keys(AREA_SERVICES)) . ')$/', $slug, $m)) {
        [$svcName, $svcSlug] = AREA_SERVICES[$m[3]];
        $town = town_name($m[1]);
        $st = strtoupper($m[2]);
        return ['template' => 'service-area', 'title' => "$svcName in $town, $st", 'h1' => "$svcName in $town, $st",
            'description' => "Need $svcName in $town, $st? $brand provides fast, effective, licensed service. Call for a free inspection.",
            'town' => $town, 'town_slug' => $m[1], 'state' => $st, 'service_name' => $svcName, 'service_slug' => $svcSlug, 'area_service' => $m[3],
            'breadcrumbs' => town_breadcrumbs($m[1], "$svcName in $town")];
    }

    // /pest-control-exterminator-of-{town}-{county}-county-nj (and one "pest-control-of-..." variant)
    if (preg_match('/^pest-control-(?:exterminator-)?of-(.+)-(ocean|monmouth|warren|hudson)-county-nj$/', $slug, $m)) {
        $town = town_name($m[1]);
        $county = $m[2] . '-county';
        return ['template' => 'city', 'title' => "Pest Control & Exterminator in $town, NJ", 'h1' => "Pest Control & Exterminator in $town, NJ",
            'description' => "Trusted pest control and exterminator services in $town, " . COUNTIES[$county][0] . ", NJ. Termites, rodents, bed bugs, wildlife and more.",
            'town' => $town, 'town_slug' => $m[1], 'state' => 'NJ', 'county' => $county,
            'breadcrumbs' => [['Home', '/'], ['Service Areas', '/service-areas'], [COUNTIES[$county][0], url($county)], [$town, null]]];
    }

    // /{town}-{nj|ny|fl}-pest-control
    if (preg_match('/^(.+)-(nj|ny|fl)-pest-control$/', $slug, $m)) {
        $town = town_name($m[1]);
        $st = strtoupper($m[2]);
        return ['template' => 'city', 'title' => "Pest Control in $town, $st", 'h1' => "Pest Control Services in $town, $st",
            'description' => "Professional pest control in $town, $st. $brand handles termites, rodents, bed bugs, ants, wildlife and more. Free inspections.",
            'town' => $town, 'town_slug' => $m[1], 'state' => $st, 'county' => TOWN_COUNTY[$m[1]] ?? null,
            'breadcrumbs' => town_breadcrumbs($m[1], $town)];
    }

    // /animal-control-near-you-for-{species}-removal[-in]-{town}  (plus a few irregular variants)
    if (preg_match('/^animal-c(?:on)?trol-near-you-for-(.+)$/', $slug, $m)) {
        return classify_animal($m[1], $slug);
    }

    // Anything else in the sitemap still gets a page.
    $title = humanize($slug);
    return ['template' => 'generic', 'title' => $title, 'h1' => $title, 'description' => "$title – $brand."];
}

function classify_animal(string $rest, string $slug): array
{
    $brand = site('name');
    $species = null;
    // Longest species key first so "eastern-screech-owls" wins over "eastern-screech-owl".
    $keys = array_keys(SPECIES);
    usort($keys, fn($a, $b) => strlen($b) <=> strlen($a));
    foreach ($keys as $k) {
        if (str_starts_with($rest, $k)) {
            $species = $k;
            break;
        }
    }
    [$name, $group] = SPECIES[$species] ?? [humanize($rest), 'mammal'];
    $tail = $species ? substr($rest, strlen($species)) : '';
    $tail = preg_replace('/^-removal/', '', $tail);
    $tail = preg_replace('/^-in-/', '-', $tail);
    $townSlug = ltrim($tail, '-');
    $town = $townSlug !== '' ? town_name($townSlug) : null;

    $where = $town ? " in $town, NJ" : ' Near You';
    $h1 = "$name Removal$where";
    return ['template' => 'animal', 'title' => "$name Removal & Animal Control$where", 'h1' => $h1,
        'description' => "Humane, licensed $name removal" . ($town ? " in $town, NJ" : ' in New Jersey') . ". $brand handles wildlife inspection, removal, exclusion and cleanup. Call today.",
        'species' => $species, 'species_name' => $name, 'group' => $group, 'town' => $town, 'town_slug' => $townSlug,
        'breadcrumbs' => [['Home', '/'], ['Wildlife Control', '/wildlife-control'], [$h1, null]]];
}

function town_name(string $slug): string
{
    return TOWN_NAMES[$slug] ?? humanize($slug);
}

function town_breadcrumbs(string $townSlug, string $last): array
{
    $crumbs = [['Home', '/'], ['Service Areas', '/service-areas']];
    if (($county = TOWN_COUNTY[$townSlug] ?? null) && slug_exists($county)) {
        $crumbs[] = [COUNTIES[$county][0], url($county)];
    }
    $crumbs[] = [$last, null];
    return $crumbs;
}

/** Group every URL by page type – used by hub pages and the HTML sitemap. */
function slugs_by_type(): array
{
    static $groups = null;
    if ($groups !== null) {
        return $groups;
    }
    $groups = [];
    foreach (all_slugs() as $slug) {
        $p = classify($slug);
        $groups[$p['template']][] = ['slug' => $slug] + $p;
    }
    return $groups;
}

/** City pages (all flavours) keyed by county hub slug. */
function towns_by_county(): array
{
    $out = [];
    foreach (slugs_by_type()['city'] ?? [] as $p) {
        $county = $p['county'] ?? null;
        $key = $county ?: strtolower($p['state']);
        $out[$key][] = $p;
    }
    foreach ($out as &$list) {
        usort($list, fn($a, $b) => strcmp($a['town'], $b['town']));
    }
    return $out;
}
