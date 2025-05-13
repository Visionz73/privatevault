<?php
// Fehlerberichterstattung aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!file_exists(__DIR__ . '/../config.php')) {
    die("Error: config.php not found at " . __DIR__ . '/../config.php');
}
if (!file_exists(__DIR__ . '/../src/controllers/create_task.php')) {
    die("Error: Controller not found at " . __DIR__ . '/../src/controllers/create_task.php');
}

// Konfigurations- und Controller-Dateien laden
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controllers/create_task.php';
?>
