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
    echo json_encode(['success' => false, 'error' => 'Nur POST-Requests erlaubt']);
    exit;
}

$task_id = $_POST['id'] ?? null;
$new_status = $_POST['status'] ?? null;

if (!$task_id || !$new_status) {
    echo json_encode(['success' => false, 'error' => 'Task ID und Status sind erforderlich']);
    exit;
}

// Validate status
$valid_statuses = ['todo', 'doing', 'done'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Ungültiger Status']);
    exit;
}

try {
    // Check if user has permission to update this task
    $stmt = $pdo->prepare("
        SELECT t.id, t.created_by, t.assigned_to, t.assigned_group_id
        FROM tasks t
        LEFT JOIN user_group_members ugm ON t.assigned_group_id = ugm.group_id
        WHERE t.id = ? AND (
            t.created_by = ? OR 
            t.assigned_to = ? OR 
            ugm.user_id = ?
        )
    ");
    $stmt->execute([$task_id, $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$task) {
        echo json_encode(['success' => false, 'error' => 'Aufgabe nicht gefunden oder keine Berechtigung']);
        exit;
    }
    
    // Update task status
    $stmt = $pdo->prepare("UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $task_id]);
    
    echo json_encode(['success' => true, 'message' => 'Status erfolgreich aktualisiert']);
    
} catch (PDOException $e) {
    error_log("Database error in task_update.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler']);
} catch (Exception $e) {
    error_log("Error in task_update: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Unbekannter Fehler']);
}
?>
