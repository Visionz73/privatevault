<?php
// src/controllers/logout.php
// Sichert, dass Session gestartet ist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Alle Session-Daten löschen
$_SESSION = [];
session_destroy();

// Zur Login-Seite weiterleiten
header('Location: login.php');
exit;
