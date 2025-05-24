<?php
// This controller is included by public/change_password.php
// public/change_password.php handles session_start(), config.php, and auth.php (requireLogin())

$pageTitle = "Change Password"; // For use in templates/header.php

// Assumes templates/header.php and templates/footer.php exist for layout.
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/change_password.php';
require_once __DIR__ . '/../../templates/footer.php';
?>
