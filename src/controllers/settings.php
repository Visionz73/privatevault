<?php
// src/controllers/settings.php

// Session is started by public/settings.php (which includes config.php)
// auth.php is included by public/settings.php, so requireLogin() is called there.
// $pdo is available globally from config.php (via db.php or directly if public/settings.php includes it)

$pageTitle = "User Settings";

// CSRF Token Generation for settings form
// A new token is generated if one doesn't exist or if a POST request used/invalidated the previous one.
if (empty($_SESSION['csrf_token_settings'])) {
    $_SESSION['csrf_token_settings'] = bin2hex(random_bytes(32));
}
$csrf_token_settings = $_SESSION['csrf_token_settings'];

// Fetch current user data (needed for pre-filling form and for the navbar)
$user = getUser(); // From src/lib/auth.php, included via public/settings.php

if (!$user) {
    // This case should be rare due to requireLogin() in public/settings.php
    $_SESSION['error_message'] = "User data could not be retrieved. Please try logging in again.";
    header('Location: logout.php'); // Redirect to logout or login
    exit;
}

$currentDisplayName = $user['username'] ?? ''; 
$currentEmail = $user['email'] ?? '';

// Handle form submission for account information changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveAccountChanges'])) {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token_settings']) || !hash_equals($_SESSION['csrf_token_settings'], $_POST['csrf_token_settings'])) {
        $_SESSION['error_message'] = "Invalid security token. Please try submitting the form again.";
        unset($_SESSION['csrf_token_settings']); // Invalidate current token
        header('Location: settings.php');        // Redirect to refresh form with new token
        exit;
    }
    // Valid token, unset it for one-time use for this submission.
    // A new one will be generated on the next page load by the logic at the top.
    unset($_SESSION['csrf_token_settings']);

    $newDisplayName = trim($_POST['displayName'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $validationErrors = []; // Local array for this submission's field validation errors

    if (empty($newDisplayName)) {
        $validationErrors[] = "Display Name cannot be empty.";
    }
    if (empty($newEmail)) {
        $validationErrors[] = "Email cannot be empty.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $validationErrors[] = "Invalid email format.";
    }

    if (empty($validationErrors)) {
        try {
            // Check if email already exists for another user
            $stmtCheckEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmtCheckEmail->execute([$newEmail, $_SESSION['user_id']]);
            if ($stmtCheckEmail->fetch()) {
                $_SESSION['error_message'] = "This email address is already in use by another account.";
            } else {
                // Proceed with update
                $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                if ($updateStmt->execute([$newDisplayName, $newEmail, $_SESSION['user_id']])) {
                    $_SESSION['success_message'] = "Account information updated successfully.";
                    // Update the $user array for the navbar if it shows display name/username
                    if (isset($_SESSION['username'])) { // If navbar uses $_SESSION['username']
                        $_SESSION['username'] = $newDisplayName;
                    }
                    // Also update the local $user variable for the current page load if needed,
                    // though redirect makes this less critical for the form itself.
                    $user['username'] = $newDisplayName; 
                    $user['email'] = $newEmail;
                    $currentDisplayName = $newDisplayName; // Ensure current vars reflect change before potential re-render
                    $currentEmail = $newEmail;

                } else {
                    $_SESSION['error_message'] = "Failed to update account information. Please try again.";
                }
            }
        } catch (PDOException $e) {
            error_log("Error updating user settings: " . $e->getMessage());
            $_SESSION['error_message'] = "A database error occurred. Could not update account information.";
        }
    } else {
        $_SESSION['error_message'] = implode("<br>", $validationErrors);
    }
    
    // Redirect back to settings.php to show messages and get a fresh CSRF token for the form.
    header('Location: settings.php');
    exit;
}

// The template will be included after this controller logic.
// Variables available to templates/settings.php:
// $pageTitle, $csrf_token_settings, $currentDisplayName, $currentEmail, 
// $user (for navbar.php if it uses it directly from the scope of the including page)
// Session messages like $_SESSION['success_message'] or $_SESSION['error_message'] will be picked up by the template.
require_once __DIR__ . '/../../templates/settings.php';

?>
