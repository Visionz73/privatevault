<?php
session_start();
require_once __DIR__ . '/../config.php'; 
require_once __DIR__ . '/../src/lib/auth.php';

requireLogin(); // Ensure user is logged in (using the correct function from auth.php)

// Delegate to the controller
require_once __DIR__ . '/../src/controllers/change_password.php'; 
?>
