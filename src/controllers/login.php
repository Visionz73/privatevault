<?php
// src/controllers/login.php
require __DIR__ . '/../lib/db.php'; // Includes config.php, which should handle session_start() or auth.php will.
require __DIR__ . '/../lib/auth.php'; // auth.php also ensures session_start() if not already active.

// CSRF Token Generation
if (empty($_SESSION['csrf_token_login'])) {
    $_SESSION['csrf_token_login'] = bin2hex(random_bytes(32));
}
$csrf_token_login = $_SESSION['csrf_token_login']; // Make it available to the template

$error = ''; // Existing error variable used by the template

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token Validation: This should be the first check.
    if (!isset($_POST['csrf_token_login']) || !hash_equals($_SESSION['csrf_token_login'], $_POST['csrf_token_login'])) {
        $error = "Invalid security token. Please try submitting the form again.";
        unset($_SESSION['csrf_token_login']);
    } else {
        // CSRF token is valid, proceed with login logic.
        unset($_SESSION['csrf_token_login']);

        $username = trim($_POST['username'] ?? ''); 
        $password = $_POST['password'] ?? '';     

        // Explicit validation for empty fields (already present from previous iteration, confirmed here)
        if (empty($username)) { // Checks if username is empty after trim
            $error = 'Username is required.';
        } elseif (empty($password)) { // Checks if password is empty
            $error = 'Password is required.';
        } else {
            // Proceed with database lookup only if fields are not empty
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                session_regenerate_id(true); 
                
                unset($_SESSION['csrf_token_login']); 

                header('Location: /dashboard.php');
                exit;
            } else {
                // This error message will be set if username/password are incorrect,
                // or if username was found but password was empty (though caught above).
                // For true empty field errors, the messages above are more specific.
                $error = 'Ungültige Zugangsdaten.';
            }
        }
    }
    
    // If there was an error (CSRF or login failure), ensure a new token is available for the re-displayed form.
    if (!empty($error) && empty($_SESSION['csrf_token_login'])) {
        $_SESSION['csrf_token_login'] = bin2hex(random_bytes(32));
    }
    $csrf_token_login = $_SESSION['csrf_token_login'] ?? bin2hex(random_bytes(32)); 
}

require __DIR__ . '/../../templates/login_form.php';

?>
