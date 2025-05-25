<?php
session_start();
require_once __DIR__ . '/../config.php'; 
require_once __DIR__ . '/../src/lib/auth.php'; 

requireLogin(); // Ensure user is logged in

// Delegate to the controller
require_once __DIR__ . '/../src/controllers/security.php'; 
?>
