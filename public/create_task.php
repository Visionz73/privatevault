<?php
// Fehlerberichterstattung aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug-Ausgabe
echo "Debug: create_task.php wird ausgeführt<br>";

// Debugging: Überprüfen, ob die Dateien existieren
if (!file_exists(__DIR__ . '/../config.php')) {
    die('Fehler: config.php nicht gefunden.');
}
if (!file_exists(__DIR__ . '/../src/controllers/create_task.php')) {
    die('Fehler: create_task.php (Controller) nicht gefunden.');
}

// Konfigurations- und Controller-Dateien laden
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controllers/create_task.php';
?>
