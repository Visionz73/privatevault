<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

// Ensure user is logged in
requireLogin();

// Include the enhanced notes template
require_once __DIR__ . '/../templates/enhanced_notes.php';
?>
