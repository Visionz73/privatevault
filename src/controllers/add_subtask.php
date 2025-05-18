<?php
session_start();
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $subtask_title = trim($_POST['subtask_title'] ?? '');
    
    if (!$task_id || !$subtask_title) {
        echo json_encode(['success' => false, 'error' => 'Fehlende Angaben.']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO sub_tasks (task_id, title) VALUES (?, ?)");
    if ($stmt->execute([$task_id, $subtask_title])) {
        $subtask_id = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'subtask' => ['id' => $subtask_id, 'title' => $subtask_title]]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Datenbankfehler.']);
    }
}
exit;
