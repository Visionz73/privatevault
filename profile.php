<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include utilities first
require_once __DIR__ . '/src/lib/utils.php';

// Check if controller exists before including
$controllerPath = __DIR__ . '/src/controllers/profile.php';
if (!file_exists($controllerPath)) {
    die('Error: Profile controller not found at: ' . $controllerPath);
}

try {
    require_once $controllerPath;
} catch (Exception $e) {
    die('Error loading profile controller: ' . $e->getMessage());
}
