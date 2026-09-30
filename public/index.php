<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($path);

// Trailing slashes and index.php collapse to the canonical URL.
if ($path !== '/' && (str_ends_with($path, '/') || str_ends_with($path, '/index.php'))) {
    $clean = preg_replace('#/(index\.php)?$#', '', $path) ?: '/';
    header('Location: ' . $clean, true, 301);
    exit;
}

$slug = trim($path, '/');

// Hidden admin dashboard (not linked or listed anywhere public).
if ('/' . $slug === ADMIN_PATH) {
    require ROOT . '/src/admin/dashboard.php';
    exit;
}

// Maintenance switch: visitors get a 503 "back soon" page; signed-in admins see the site.
if (!site('site_online') && !is_admin()) {
    http_response_code(503);
    header('Retry-After: 3600');
    header('X-Robots-Tag: noindex');
    echo render('offline');
    exit;
}

// Generated files.
if ($slug === 'sitemap.xml') {
    require ROOT . '/src/sitemap.php';
    exit;
}
if ($slug === 'robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    if (site('indexable')) {
        echo "User-agent: *\nAllow: /\n\nSitemap: " . abs_url('sitemap.xml') . "\n";
    } else {
        echo "# Staging build – not indexable. Set SITE_INDEXABLE=true at launch.\nUser-agent: *\nDisallow: /\n";
    }
    exit;
}

// Contact form submissions.
if ($slug === 'contact' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require ROOT . '/src/contact-handler.php';
    exit;
}

$page = resolve_page($slug);

if ($page === null) {
    http_response_code(404);
    $page = ['template' => '404', 'slug' => $slug, 'status' => 404, 'title' => 'Page Not Found',
        'h1' => 'Page Not Found', 'description' => 'The page you are looking for could not be found.',
        'canonical' => null, 'breadcrumbs' => []];
}

header('Content-Type: text/html; charset=utf-8');
echo render('layout', ['page' => $page]);
