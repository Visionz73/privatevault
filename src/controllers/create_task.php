<?php
// src/controllers/create_task.php

// 1) DB und Auth aus src/lib laden
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// 2) Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();

$allUsers = [];
$allGroups = [];
$errors = [];
$success = '';

// 3) Nutzer für „assigned_to"-Dropdown
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading users: ' . $e->getMessage();
}

// 3b) Gruppen für "assigned_group"-Dropdown
try {
    $stmt = $pdo->query("SELECT g.id, g.name, COUNT(m.user_id) as member_count 
                         FROM user_groups g 
                         LEFT JOIN group_members m ON g.id = m.group_id 
                         GROUP BY g.id 
                         ORDER BY g.name");
    $allGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading groups: ' . $e->getMessage();
}

// 4) Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assignmentType = $_POST['assignment_type'] ?? 'user';
    $dueDate = $_POST['due_date'] ?? '';
    
    // Validate inputs
    if (empty($title)) {
        $errors[] = 'Titel ist erforderlich.';
    }
    
    if ($assignmentType === 'user' && empty($_POST['assigned_to'])) {
        $errors[] = 'Bitte wählen Sie einen Benutzer aus.';
    }
    
    if ($assignmentType === 'group' && empty($_POST['assigned_group'])) {
        $errors[] = 'Bitte wählen Sie eine Gruppe aus.';
    }
    
    // Process if no errors
    if (empty($errors)) {
        try {
            if ($assignmentType === 'user') {
                // Individual user assignment
                $assignedTo = $_POST['assigned_to'];
                
                $stmt = $pdo->prepare("
                    INSERT INTO tasks
                      (title, description, created_by, assigned_to, due_date, status, is_done)
                    VALUES
                      (?, ?, ?, ?, ?, 'todo', 0)
                ");
                
                $stmt->execute([$title, $description, $_SESSION['user_id'], $assignedTo, $dueDate]);
                $success = 'Aufgabe wurde erstellt und dem Benutzer zugewiesen.';
                
            } else {
                // Group assignment - create tasks for all group members
                $groupId = $_POST['assigned_group'];
                
                // Get all users in the group
                $stmt = $pdo->prepare("
                    SELECT user_id FROM user_group_members WHERE group_id = ?
                ");
                $stmt->execute([$groupId]);
                $groupMembers = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (empty($groupMembers)) {
                    $errors[] = 'Die ausgewählte Gruppe hat keine Mitglieder.';
                } else {
                    // Start transaction
                    $pdo->beginTransaction();
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO tasks
                          (title, description, created_by, assigned_to, due_date, status, is_done)
                        VALUES
                          (?, ?, ?, ?, ?, 'todo', 0)
                    ");
                    
                    foreach ($groupMembers as $memberId) {
                        $stmt->execute([$title, $description, $_SESSION['user_id'], $memberId, $dueDate]);
                    }
                    
                    $pdo->commit();
                    $success = 'Aufgabe wurde erstellt und allen Gruppenmitgliedern zugewiesen.';
                }
            }
            
            if (!empty($success)) {
                // Reset form after successful submission
                $_POST = [];
            }
            
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Datenbankfehler: ' . $e->getMessage();
        }
    }
}

// 5) Template rendern
require_once __DIR__ . '/../../templates/create_task.php';
