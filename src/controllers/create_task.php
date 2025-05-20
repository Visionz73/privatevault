<?php
// src/controllers/create_task.php

// 1) DB und Auth aus src/lib laden
require_once __DIR__ . '/../lib/db.php';
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
$allGroups = [];
$errors   = [];

// 3) Nutzer für „assigned_to“-Dropdown
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("
        SELECT g.id, g.name, COUNT(gm.user_id) as member_count 
        FROM user_groups g
        LEFT JOIN user_group_members gm ON g.id = gm.group_id
        GROUP BY g.id
        ORDER BY g.name
    ");
    $allGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading users or groups: ' . $e->getMessage();
}

// 4) Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prüfen, ob der Array-Key "due_date" gesetzt ist und einen Default-Wert zuweisen
    $due_date = $_POST['due_date'] ?? '';

    // Prüfen, ob wir einen Benutzer oder eine Gruppe zuweisen
    $assignmentType = $_POST['assignment_type'] ?? 'user';
    $assignedTo = null;
    $assignedGroupId = null;
    
    if ($assignmentType === 'user') {
        $assignedTo = $_POST['assigned_to'] ?? $_SESSION['user_id'];
        if (empty($assignedTo)) {
            $errors[] = 'Bitte wählen Sie einen Benutzer aus.';
        }
    } else {
        $assignedGroupId = $_POST['assigned_group'] ?? null;
        if (empty($assignedGroupId)) {
            $errors[] = 'Bitte wählen Sie eine Gruppe aus.';
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO tasks
                  (title, description, assigned_to, assigned_group_id, due_date, status, created_by)
                VALUES
                  (?, ?, ?, ?, ?, 'todo', ?)
            ");
            $stmt->execute([
                $_POST['title']       ?? '',
                $_POST['description'] ?? '',
                $assignedTo,
                $assignedGroupId,
                $due_date,
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
}

// 5) Template rendern
require_once __DIR__ . '/../../templates/create_task.php';
