<?php
// src/controllers/create_task.php

// 1) DB und Auth laden (relativ zu diesem Skript)
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// 2) Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3) Fallback für Benutzer-ID (Debug / lokal)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$allUsers = [];
$errors   = [];

// 4) Alle Nutzer für das „assigned_to“-Dropdown laden
try {
    $stmt     = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading users: ' . $e->getMessage();
}

// 5) Formular-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Wenn kein Empfänger gewählt wurde: auf mich selbst setzen
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
            // Zur Inbox weiterleiten
            header('Location: /src/controllers/inbox.php');
            exit;
        } else {
            $errors[] = 'Task wurde nicht angelegt. Bitte Eingaben prüfen.';
        }
    } catch (PDOException $e) {
        $errors[] = 'Error creating task: ' . $e->getMessage();
    }
}

// 6) Template rendern
require_once __DIR__ . '/../../templates/create_task.php';
