<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];

// Collect notification settings
$settings = [
    'email_finance' => isset($_POST['email_finance']) ? 1 : 0,
    'email_documents' => isset($_POST['email_documents']) ? 1 : 0,
    'email_security' => isset($_POST['email_security']) ? 1 : 0,
    'email_newsletter' => isset($_POST['email_newsletter']) ? 1 : 0,
    'push_finance' => isset($_POST['push_finance']) ? 1 : 0,
    'push_reminders' => isset($_POST['push_reminders']) ? 1 : 0,
    'push_security' => isset($_POST['push_security']) ? 1 : 0,
    'notification_frequency' => $_POST['frequency'] ?? 'immediate'
];

// Update user settings
$stmt = $pdo->prepare('UPDATE users SET notification_settings = ?, updated_at = NOW() WHERE id = ?');
$stmt->execute([json_encode($settings), $userId]);

$_SESSION['success'] = 'Benachrichtigungseinstellungen gespeichert.';
header('Location: /profile.php?tab=notifications');
exit;
