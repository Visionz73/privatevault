<?php
// src/controllers/change_password.php

// public/change_password.php handles:
// - session_start() (via config.php or auth.php)
// - config.php (for $pdo, though not used directly in this placeholder controller yet)
// - auth.php (for requireLogin() and getUser())

// requireLogin() is called in public/change_password.php, so user must be logged in.
// We need to fetch the user data for navbar.php, which expects a $user variable.
$user = getUser(); 

if (!$user) {
    // This case should be rare due to requireLogin() in public/change_password.php,
    // but as a fallback, redirect to login if user data somehow isn't available.
    // Setting an error message might be good if login page can display it.
    // For now, direct redirect.
    header('Location: login.php'); // Or a more generic error page/logout
    exit;
}

$pageTitle = "Change Password"; // For use in templates/change_password.php

// The template will be included after this controller logic.
// Variables available to templates/change_password.php:
// $pageTitle, $user (for navbar.php)
require_once __DIR__ . '/../../templates/change_password.php';
?>
