<?php
// Ensure this controller is included by an entry point file in public/
// (e.g., public/settings.php which should handle session_start, config, and auth.php)

// $pdo is available globally from config.php
// session is started in public/settings.php
// requireLogin() is called in public/settings.php
// getUser() is available from src/lib/auth.php (included in public/settings.php)

$pageTitle = "Settings";
$errors = []; // To store validation errors

// Fetch current user data
$user = getUser(); // From src/lib/auth.php

if ($user) {
    // Assuming 'username' is used for display name and 'email' for email.
    // Adjust if your 'users' table schema has a specific 'display_name' column.
    $currentDisplayName = $user['username'] ?? ''; 
    $currentEmail = $user['email'] ?? '';
} else {
    // This case should ideally be prevented by requireLogin()
    // Or if getUser() returns null for a logged-in user (e.g., user deleted mid-session)
    $_SESSION['error_message'] = "User data could not be retrieved. Please try logging in again.";
    // Redirect to logout or login page might be more appropriate here
    header('Location: logout.php'); 
    exit;
}

// Handle form submission for account information changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveAccountChanges'])) {
    $newDisplayName = trim($_POST['displayName'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');

    // Validation
    if (empty($newDisplayName)) {
        $errors[] = "Display Name cannot be empty.";
    }
    // Add more validation for display name if needed (e.g., length, characters)

    if (empty($newEmail)) {
        $errors[] = "Email cannot be empty.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        try {
            // Check if email already exists for another user (optional but good practice)
            $stmtCheckEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmtCheckEmail->execute([$newEmail, $_SESSION['user_id']]);
            if ($stmtCheckEmail->fetch()) {
                $_SESSION['error_message'] = "This email address is already in use by another account.";
            } else {
                // Proceed with update
                // Assuming 'username' column for display name. Adjust if column is 'display_name'
                $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                if ($updateStmt->execute([$newDisplayName, $newEmail, $_SESSION['user_id']])) {
                    $_SESSION['success_message'] = "Account information updated successfully.";
                    // Optionally, update the session variable for username if it's used in the navbar directly
                    if (isset($_SESSION['username'])) {
                        $_SESSION['username'] = $newDisplayName;
                    }
                } else {
                    $_SESSION['error_message'] = "Failed to update account information. Please try again.";
                }
            }
        } catch (PDOException $e) {
            error_log("Error updating user settings: " . $e->getMessage()); // Log the actual error
            $_SESSION['error_message'] = "A database error occurred. Could not update account information.";
        }
    } else {
        $_SESSION['error_message'] = implode("<br>", $errors);
    }
    
    // Redirect back to settings.php to show messages and prevent form resubmission
    header('Location: settings.php');
    exit;
}

// These variables will be available in the included templates
require_once __DIR__ . '/../../templates/header.php'; 
require_once __DIR__ . '/../../templates/settings.php'; 
require_once __DIR__ . '/../../templates/footer.php'; 
?>
