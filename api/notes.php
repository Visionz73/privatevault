<?php
// API Weiterleitung für notes.php
// Wichtig: Session muss vor include gestartet werden
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the actual API file
require_once __DIR__ . '/../src/api/notes.php';
?>
