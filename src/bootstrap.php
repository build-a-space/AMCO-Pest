<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

$GLOBALS['site'] = load_settings();

require ROOT . '/src/catalog.php';
require ROOT . '/src/admin/auth.php';
require ROOT . '/src/pages.php';
require ROOT . '/src/seo.php';

/** config/site.php defaults, overlaid with dashboard edits from storage/settings.json. */
function load_settings(): array
{
    $defaults = require ROOT . '/config/site.php';
    $file = ROOT . '/storage/settings.json';
    $saved = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
    // Lists (hours) are replaced wholesale rather than merged index by index.
    foreach ($saved as $k => $v) {
        $defaults[$k] = (is_array($v) && is_array($defaults[$k] ?? null) && !array_is_list($v))
            ? array_replace($defaults[$k], $v) : $v;
    }
    return $defaults;
}

/** Persist dashboard edits (only the keys passed in) to storage/settings.json. */
function save_settings(array $changes): void
{
    $file = ROOT . '/storage/settings.json';
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0775, true);
    }
    $saved = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
    $saved = array_replace($saved, $changes);
    file_put_contents($file, json_encode($saved, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    $GLOBALS['site'] = load_settings();
}

/** "(732) 681-8283" -> "+17326818283" for tel: links. */
function tel_href(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (strlen($digits) === 10) {
        $digits = '1' . $digits;
    }
    return '+' . $digits;
}

function site(?string $key = null)
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

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    session_name('amco_sid');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function csrf_token(): string
{
    start_session();
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}

function csrf_valid(): bool
{
    start_session();
    return hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''));
}
