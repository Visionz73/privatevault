<?php
// src/controllers/login.php
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Ungültige Zugangsdaten.';
    }
}

require __DIR__ . '/../../templates/login_form.php';
