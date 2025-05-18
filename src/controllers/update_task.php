<?php
session_start();
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $creator_id = $_POST['creator_id'] ?? null;
    $assignee_id = $_POST['assignee_id'] ?? null;

    if (!$task_id || !$creator_id || !$assignee_id) {
        echo json_encode(['success' => false, 'error' => 'Fehlende Daten']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE tasks 
            SET creator_id = ?, assignee_id = ?
            WHERE id = ?
        ");
        
        $success = $stmt->execute([$creator_id, $assignee_id, $task_id]);
        
        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Datenbankfehler']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage']);
exit;
