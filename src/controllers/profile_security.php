<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'Passwörter stimmen nicht überein.';
        header('Location: /profile.php?tab=security');
        exit;
    }
    
    if (strlen($newPassword) < 8) {
        $_SESSION['error'] = 'Passwort muss mindestens 8 Zeichen lang sein.';
        header('Location: /profile.php?tab=security');
        exit;
    }
    
    // Verify current password
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!password_verify($currentPassword, $user['password_hash'])) {
        $_SESSION['error'] = 'Aktuelles Passwort ist falsch.';
        header('Location: /profile.php?tab=security');
        exit;
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$hashedPassword, $userId]);
    
    $_SESSION['success'] = 'Passwort erfolgreich geändert.';
}

header('Location: /profile.php?tab=security');
exit;
