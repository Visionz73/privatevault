<?php
// public/index.php (Front Controller)
require __DIR__ . '/../config.php';
require __DIR__ . '/../routes.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove the base path handling for /privatevault
$route = $path ?: '/';

if (isset($routes[$route])) {
    require __DIR__ . '/../' . $routes[$route];
} else {
    http_response_code(404);
    echo 'Seite nicht gefunden';
}
