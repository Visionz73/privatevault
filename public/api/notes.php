<?php
// public/api/notes.php - API für das Notes-System
// Wichtig: Session muss vor include gestartet werden
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/api/notes.php';
?>
