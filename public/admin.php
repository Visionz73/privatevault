<?php
// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// public/admin.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controllers/admin.php';
