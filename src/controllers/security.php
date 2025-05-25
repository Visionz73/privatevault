<?php
// This controller is included by public/security.php
// public/security.php handles session_start(), config.php, and auth.php (requireLogin(), getUser())

// $user variable is needed by templates/navbar.php
// auth.php (which defines getUser()) is included by public/security.php
$user = getUser(); 

if (!$user) {
    // This case should be rare due to requireLogin() in public/security.php
    // Redirect to login if user data somehow isn't available.
    // Consider adding a session error message here if your login page displays them.
    // $_SESSION['error_message'] = "User session invalid. Please log in again.";
    header('Location: login.php'); 
    exit;
}

$pageTitle = "Security Settings"; 

// Load the main template
require_once __DIR__ . '/../../templates/security.php'; 
?>
