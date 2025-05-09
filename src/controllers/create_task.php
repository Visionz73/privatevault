<?php
// src/controllers/create_task.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();
requireRole(['admin']);          // nur Admins dürfen Tasks erstellen

$errors  = [];
$success = '';

// Alle Nutzer für das Dropdown
$allUsers = $pdo->query(
  'SELECT id, username FROM users ORDER BY username'
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_to = $_POST['assigned_to'] ?? '';
    $due_date    = $_POST['due_date']    ?? null;

    if ($title === '' || $assigned_to === '') {
        $errors[] = 'Titel und Empfänger sind Pflichtfelder.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
          'INSERT INTO tasks (title, description, assigned_to, created_by, due_date)
           VALUES (?,?,?,?,?)'
        );
        $stmt->execute([
          $title,
          $description,
          $assigned_to,
          $_SESSION["user_id"],
          $due_date
        ]);
        $success = 'Aufgabe wurde erstellt.';
        $_POST = [];               // Formular leeren
    }
}

require_once __DIR__ . '/../../templates/create_task.php';
