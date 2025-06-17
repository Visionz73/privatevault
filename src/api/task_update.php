<?php
// src/api/task_update.php - API endpoint for updating task status
header('Content-Type: application/json');

// Required files
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';

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
$status = $_POST['status'] ?? null;

if (!$taskId || !$status) {
    http_response_code(400);
    echo json_encode(['error' => 'Fehlende Parameter']);
    exit;
}

// Validate status
$validStatuses = ['todo', 'doing', 'done'];
if (!in_array($status, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ungültiger Status']);
    exit;
}

try {
    // Check if the user has permission to update this task
    $stmt = $pdo->prepare("
        SELECT t.id 
        FROM tasks t
        LEFT JOIN user_group_members m ON t.assigned_group_id = m.group_id
        WHERE t.id = :task_id 
          AND (t.created_by = :user_id 
               OR t.assigned_to = :user_id
               OR m.user_id = :user_id)
        LIMIT 1
    ");
    
    $stmt->execute([
        ':task_id' => $taskId,
        ':user_id' => $_SESSION['user_id']
    ]);
    
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$task) {
        http_response_code(403);
        echo json_encode(['error' => 'Keine Berechtigung']);
        exit;
    }
    
    // Update the task status
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = :status, 
            updated_at = NOW(),
            is_done = :is_done
        WHERE id = :task_id
    ");
    
    $stmt->execute([
        ':status' => $status,
        ':task_id' => $taskId,
        ':is_done' => ($status === 'done' ? 1 : 0)
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Aufgabenstatus aktualisiert'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Datenbankfehler',
        'details' => $e->getMessage() 
    ]);
}
?>
