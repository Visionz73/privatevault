<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../lib/db.php';

// CSRF Token Generation: Ensure a token is available for the form.
if (empty($_SESSION['csrf_token_register'])) {
    $_SESSION['csrf_token_register'] = bin2hex(random_bytes(32));
}
$csrf_token_register = $_SESSION['csrf_token_register']; // Make it available to the template

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token Validation: This should be the first check.
    if (!isset($_POST['csrf_token_register']) || !hash_equals($_SESSION['csrf_token_register'], $_POST['csrf_token_register'])) {
        $errors[] = "Invalid security token. Please try submitting the form again.";
        unset($_SESSION['csrf_token_register']);
    } else {
        // CSRF token is valid, proceed. Unset token after use.
        unset($_SESSION['csrf_token_register']);

        $email           = trim($_POST['email'] ?? '');
        $username        = trim($_POST['username'] ?? '');
        $password        = $_POST['password'] ?? ''; // Do not trim password
        $confirmPassword = $_POST['confirm_password'] ?? ''; // Do not trim password

        // Email Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Bitte gib eine gültige E-Mail-Adresse an.';
        }
        // Username Validation
        if (empty($username)) { 
            $errors[] = 'Bitte wähle einen Benutzernamen.';
        }
        // Password Validation
        if (strlen($password) < 8) {
            $errors[] = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Das Passwort muss mindestens einen Großbuchstaben enthalten.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Das Passwort muss mindestens einen Kleinbuchstaben enthalten.';
        }
        if (!preg_match('/[0-9]/', $password)) { 
            $errors[] = 'Das Passwort muss mindestens eine Ziffer enthalten.';
        }
        if (!preg_match('/[^A-Za-z0-9\s]/', $password)) { 
            $errors[] = 'Das Passwort muss mindestens ein Sonderzeichen enthalten.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwort und Bestätigung stimmen nicht überein.';
        }

        // Unique-Check for username/email, only if other validations passed
        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Benutzername oder E-Mail bereits vergeben.';
            }
        }

        // User anlegen (Create user), only if still no errors
        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, password_hash, email, role, created_at) VALUES (?, ?, ?, ?, NOW())'
            );
            try {
                $stmt->execute([$username, $hash, $email, 'member']);

                // Automatisches Einloggen (Auto-login)
                $_SESSION['user_id'] = $pdo->lastInsertId();
                session_regenerate_id(true); // Regenerate session ID to prevent fixation
                
                unset($_SESSION['csrf_token_register']); 

                header('Location: /dashboard.php');
                exit;
            } catch (PDOException $e) {
                error_log("Error inserting user: " . $e->getMessage()); 
                $errors[] = "An error occurred during registration. Please try again later."; 
            }
        }
    }
    
    if (!empty($errors) && empty($_SESSION['csrf_token_register'])) {
        $_SESSION['csrf_token_register'] = bin2hex(random_bytes(32));
    }
    $csrf_token_register = $_SESSION['csrf_token_register'] ?? bin2hex(random_bytes(32)); 
}

// Make errors available to template
if (!isset($errors)) {
    $errors = [];
}

require_once __DIR__ . '/../../templates/register_form.php';

?>
