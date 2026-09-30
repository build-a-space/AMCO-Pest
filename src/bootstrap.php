<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

// Never print PHP notices into pages (they break headers/cookies); log them instead.
ini_set('display_errors', getenv('APP_DEBUG') ? '1' : '0');
ini_set('log_errors', '1');

require ROOT . '/src/storage.php';
$GLOBALS['site'] = load_settings();

require ROOT . '/src/catalog.php';
require ROOT . '/src/admin/auth.php';
require ROOT . '/src/pages.php';
require ROOT . '/src/seo.php';

/** config/site.php defaults, overlaid with dashboard edits from storage/settings.json. */
function load_settings(): array
{
    $defaults = require ROOT . '/config/site.php';
    $saved = data_read('settings') ?? [];
    // Lists (hours) are replaced wholesale rather than merged index by index.
    foreach ($saved as $k => $v) {
        $defaults[$k] = (is_array($v) && is_array($defaults[$k] ?? null) && !array_is_list($v))
            ? array_replace($defaults[$k], $v) : $v;
    }
    return $defaults;
}

/** Persist dashboard edits (only the keys passed in). */
function save_settings(array $changes): void
{
    data_write('settings', array_replace(data_read('settings') ?? [], $changes));
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

/** Asset URL with a cache-busting version from the file's contents (mtimes are fixed on Vercel). */
function asset(string $path): string
{
    $file = ROOT . '/public/assets/' . ltrim($path, '/');
    $v = is_file($file) ? substr(md5_file($file), 0, 10) : '';
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

/*
 * No server-side sessions: serverless hosts (Vercel) run many short-lived
 * instances, so state lives in signed cookies instead.
 */
function is_https(): bool
{
    return ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function set_app_cookie(string $name, string $value, int $expires = 0): void
{
    setcookie($name, $value, ['expires' => $expires, 'path' => '/', 'secure' => is_https(), 'httponly' => true, 'samesite' => 'Lax']);
    $_COOKIE[$name] = $value;
}

/** Double-submit CSRF token: random value in a cookie that each form must echo back. */
function csrf_token(): string
{
    $t = (string) ($_COOKIE['amco_csrf'] ?? '');
    if (!preg_match('/^[a-f0-9]{32}$/', $t)) {
        $t = bin2hex(random_bytes(16));
        set_app_cookie('amco_csrf', $t);
    }
    return $t;
}

function csrf_valid(): bool
{
    $cookie = (string) ($_COOKIE['amco_csrf'] ?? '');
    return $cookie !== '' && hash_equals($cookie, (string) ($_POST['csrf'] ?? ''));
}

/** Tamper-proof token: base64(payload).hmac */
function sign_token(array $payload): string
{
    $body = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
    return $body . '.' . hash_hmac('sha256', $body, app_key());
}

function verify_token(string $token): ?array
{
    [$body, $mac] = array_pad(explode('.', $token, 2), 2, '');
    if ($body === '' || !hash_equals(hash_hmac('sha256', $body, app_key()), $mac)) {
        return null;
    }
    return json_decode((string) base64_decode(strtr($body, '-_', '+/')), true) ?: null;
}
