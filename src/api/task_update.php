<?php
// src/api/task_update.php - Enhanced API endpoint for updating tasks
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Nicht authentifiziert']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
    exit;
}

$taskId = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$taskId || !$status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fehlende Parameter']);
    exit;
}

// Validate status
$validStatuses = ['todo', 'doing', 'done'];
if (!in_array($status, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültiger Status']);
    exit;
}

try {
    // Check permissions
    $stmt = $pdo->prepare("
        SELECT t.id, t.title
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
        echo json_encode(['success' => false, 'error' => 'Keine Berechtigung für diese Aufgabe']);
        exit;
    }
    
    // Update the task
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = :status, 
            updated_at = NOW()
        WHERE id = :task_id
    ");
    
    $result = $stmt->execute([
        ':status' => $status,
        ':task_id' => $taskId
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Aufgabe erfolgreich aktualisiert',
            'task_id' => $taskId,
            'new_status' => $status,
            'task_title' => $task['title']
        ]);
    } else {
        throw new Exception('Fehler beim Speichern der Änderungen');
    }
    
} catch (PDOException $e) {
    error_log("Database error in task_update: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Datenbankfehler beim Aktualisieren der Aufgabe'
    ]);
} catch (Exception $e) {
    error_log("General error in task_update: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
