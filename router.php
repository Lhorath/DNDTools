<?php
/**
 * Dev router for PHP's built-in server. Run from the project root, e.g.:
 *   php -S localhost:8080 router.php
 *
 * Mirrors .htaccess routing without Apache. Production: use Apache + .htaccess instead.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rawurldecode((string) $uri);

if ($path !== '/' && $path !== '' && file_exists(__DIR__ . $path)) {
    return false;
}

$trim = trim($path, '/');
$_GET['url'] = $trim === '' ? 'home' : $trim;

require __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
