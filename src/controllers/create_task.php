<?php
// src/controllers/create_task.php

// 1) DB und Auth aus src/lib laden
require_once(__DIR__ . '/../../lib/db.php');
require_once __DIR__ . '/../lib/auth.php';

// 2) Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Debug-Fallback
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$allUsers = [];
$errors   = [];

// 3) Nutzer für „assigned_to“-Dropdown
try {
    $stmt     = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading users: ' . $e->getMessage();
}

// 4) Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignedTo = $_POST['assigned_to'] ?? $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO tasks
              (title, description, assigned_to, due_date, status, user_id, created_by)
            VALUES
              (?, ?, ?, ?, 'open', ?, ?)
        ");
        $stmt->execute([
            $_POST['title']       ?? '',
            $_POST['description'] ?? '',
            $assignedTo,
            $_POST['due_date']    ?? null,
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);

        if ($stmt->rowCount() > 0) {
            header('Location: /inbox.php');
            exit;
        } else {
            $errors[] = 'Task wurde nicht angelegt. Bitte Eingaben prüfen.';
        }
    } catch (PDOException $e) {
        $errors[] = 'Error creating task: ' . $e->getMessage();
    }
}

// 5) Template rendern
require_once __DIR__ . '/../../templates/create_task.php';
