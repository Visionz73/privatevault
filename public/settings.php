<?php
session_start();
require_once __DIR__ . '/../config.php'; 
require_once __DIR__ . '/../src/lib/auth.php';

// Ensure user is logged in
requireLogin(); 

// Delegate to a controller
require_once __DIR__ . '/../src/controllers/settings.php'; 
?>
