<?php
// public/create_task.php

// Fehlerberichterstattung aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!file_exists(__DIR__ . '/../config.php')) {
    die('Fehler: config.php nicht gefunden.');
}
require_once __DIR__ . '/../config.php';

if (!file_exists(__DIR__ . '/../src/controllers/create_task.php')) {
    die('Fehler: create_task.php (Controller) nicht gefunden.');
}
require_once __DIR__ . '/../src/controllers/create_task.php';
?>
