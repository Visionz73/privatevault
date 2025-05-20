<?php
// Debug-Modus aktivieren, um den genauen Fehler zu sehen:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect all controls to the calendar controller
require_once __DIR__ . '/../src/controllers/calendar.php';
?>
