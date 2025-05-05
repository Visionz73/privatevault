<?php
// src/controllers/admin.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();
requireRole(['admin']);

$success = '';
$errors = [];

// Rolle ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $role    = $_POST['role']    ?? '';

    if (in_array($role, ['admin','member','guest'], true) && is_numeric($user_id)) {
        $stmt = $pdo->prepare('UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$role, $user_id]);
        $success = 'Rolle erfolgreich aktualisiert.';
    } else {
        $errors[] = 'Ungültiger User oder Rolle.';
    }
}

// Alle User laden
$stmt  = $pdo->query('SELECT id, username, email, role, created_at FROM users ORDER BY id');
$users = $stmt->fetchAll();

// Template rendern
require_once __DIR__ . '/../../templates/admin.php';
