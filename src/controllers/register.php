<?php
// src/controllers/register.php
require_once __DIR__ . '/../lib/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fallbacks, damit kein Notice entsteht
    $email           = trim($_POST['email']           ?? '');
    $username        = trim($_POST['username']        ?? '');
    $password        = $_POST['password']             ?? '';
    $confirmPassword = $_POST['confirm_password']     ?? '';

    // Validierung
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Bitte gib eine gültige E-Mail-Adresse an.';
    }
    if ($username === '') {
        $errors[] = 'Bitte wähle einen Benutzernamen.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwort und Bestätigung stimmen nicht überein.';
    }

    // Unique-Check nur, wenn bisher keine Errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Benutzername oder E-Mail bereits vergeben.';
        }
    }

    // User anlegen
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, $hash, $email]);

        // Automatisches Einloggen
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header('Location: dashboard.php');
        exit;
    }
}

// Template rendern
require_once __DIR__ . '/../../templates/register_form.php';
