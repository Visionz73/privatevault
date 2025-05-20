<?php
// Ensure we have session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../../config.php';

// Create required tables if they don't exist
require_once __DIR__ . '/../../database/group_tables.php';

// Load the controller
require_once __DIR__ . '/../../src/controllers/admin/groups.php';
?>
