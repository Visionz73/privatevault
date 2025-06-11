<?php
// src/api/task_update.php - API endpoint for updating task status
header('Content-Type: application/json');

// Required files
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht authentifiziert']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Methode nicht erlaubt']);
    exit;
}

// Get task ID and new status
$taskId = $_POST['id'] ?? null;
$newStatus = $_POST['status'] ?? null;

if (!$taskId || !$newStatus) {
    http_response_code(400);
    echo json_encode(['error' => 'Fehlende Parameter']);
    exit;
}

// Validate status
$validStatuses = ['todo', 'doing', 'done'];
if (!in_array($newStatus, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ungültiger Status']);
    exit;
}

try {
    // Check if the user has permission to update this task
    $checkStmt = $pdo->prepare("
        SELECT id FROM tasks 
        WHERE id = ? AND (
            created_by = ? OR 
            assigned_to = ? OR 
            assigned_group_id IN (
                SELECT group_id FROM user_group_members WHERE user_id = ?
            )
        )
    ");
    $checkStmt->execute([$taskId, $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    
    if (!$checkStmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Keine Berechtigung für diese Aufgabe']);
        exit;
    }
    
    // Update the task status
    $updateStmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $updateStmt->execute([$newStatus, $taskId]);
    
    echo json_encode(['success' => true, 'message' => 'Status erfolgreich aktualisiert']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
