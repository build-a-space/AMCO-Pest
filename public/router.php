<?php
// Router for PHP's built-in server:  php -S localhost:8000 -t public public/router.php
// Serves real files (CSS, JS, images) directly and sends everything else to index.php.
$file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_URI'] !== '/' && is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';
