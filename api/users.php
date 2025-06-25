<?php
// API Weiterleitung für users.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/api/users.php';
?>
