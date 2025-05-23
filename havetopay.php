<?php
/**
 * HaveToPay Module Entry Point
 * This file serves as the main entry point for the HaveToPay functionality
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include and execute the HaveToPay controller
require_once __DIR__ . '/src/controllers/havetopay.php';
?>
