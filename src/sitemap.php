<?php
// XML sitemap generated from data/urls.txt, so it always matches the routes.
header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach (all_slugs() as $slug) {
    $priority = $slug === '' ? '1.0' : '0.75';
    echo "  <url><loc>" . e(abs_url($slug)) . "</loc><priority>$priority</priority>"
        . ($slug === '' ? '<changefreq>daily</changefreq>' : '') . "</url>\n";
}
echo "</urlset>\n";
