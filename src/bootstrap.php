<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

$GLOBALS['site'] = require ROOT . '/config/site.php';

require ROOT . '/src/catalog.php';
require ROOT . '/src/pages.php';
require ROOT . '/src/seo.php';

function site(string $key = null)
{
    return $key === null ? $GLOBALS['site'] : ($GLOBALS['site'][$key] ?? null);
}

/** HTML-escape. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Site-relative URL for a slug ("/" for home). */
function url(string $slug): string
{
    $slug = trim($slug, '/');
    return $slug === '' ? '/' : '/' . $slug;
}

function abs_url(string $slug): string
{
    return rtrim(site('base_url'), '/') . url($slug);
}

/** Asset URL with a cache-busting version from the file's mtime. */
function asset(string $path): string
{
    $file = ROOT . '/public/assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : 0;
    return '/assets/' . ltrim($path, '/') . ($v ? '?v=' . $v : '');
}

/** "red-bank" -> "Red Bank" */
function humanize(string $slug): string
{
    $small = ['of', 'and', 'the', 'by', 'in', 'or', 'to', 'a', 'for'];
    $words = explode('-', $slug);
    foreach ($words as $i => $w) {
        $words[$i] = ($i > 0 && in_array($w, $small, true)) ? $w : ucfirst($w);
    }
    $out = implode(' ', $words);
    // Slugs like "new-jersey-s-guide" come from apostrophes that were stripped.
    $out = preg_replace('/ S\b/', "'s", $out);
    $out = preg_replace("/\b(Don|Doesn|Isn|Can|Won|What|That|Here) T\b/", "$1't", $out);
    return str_replace(['Nj', 'Ny', 'Diy', 'Sds'], ['NJ', 'NY', 'DIY', 'SDS'], $out);
}

/** Render a template with variables; returns the HTML. */
function render(string $template, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();
    include ROOT . '/templates/' . $template . '.php';
    return (string) ob_get_clean();
}

function partial(string $name, array $vars = []): void
{
    echo render('partials/' . $name, $vars);
}

/** All URL slugs from data/urls.txt, de-duplicated, in sitemap order. */
function all_slugs(): array
{
    static $slugs = null;
    if ($slugs !== null) {
        return $slugs;
    }
    $lines = file(ROOT . '/data/urls.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $slugs = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $slugs[trim($line, '/')] = true;
    }
    return $slugs = array_keys($slugs);
}

function slug_exists(string $slug): bool
{
    static $set = null;
    $set ??= array_flip(all_slugs());
    return isset($set[trim($slug, '/')]);
}

/**
 * Imported page content (written by scripts/crawl.mjs) lives in content/pages.
 * Returns ['html' => ..., 'meta' => [...]] or null when nothing was imported yet.
 */
function imported_content(string $slug): ?array
{
    $key = $slug === '' ? 'index' : str_replace('/', '__', $slug);
    $html = ROOT . '/content/pages/' . $key . '.html';
    if (!is_file($html)) {
        return null;
    }
    $metaFile = ROOT . '/content/pages/' . $key . '.json';
    $meta = is_file($metaFile) ? (json_decode((string) file_get_contents($metaFile), true) ?: []) : [];
    return ['html' => (string) file_get_contents($html), 'meta' => $meta];
}

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}
