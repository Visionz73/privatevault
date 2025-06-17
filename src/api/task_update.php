<?php
// src/api/task_update.php - Enhanced API endpoint for updating tasks
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Nicht authentifiziert']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Nur POST erlaubt']);
    exit;
}

try {
    $taskId = $_POST['id'] ?? null;
    $newStatus = $_POST['status'] ?? null;
    
    if (!$taskId) {
        echo json_encode(['success' => false, 'error' => 'Task ID fehlt']);
        exit;
    }
    
    // Check if user has permission to update this task
    $stmt = $pdo->prepare("
        SELECT * FROM tasks t
        LEFT JOIN user_group_members ugm ON t.assigned_group_id = ugm.group_id
        WHERE t.id = ? AND (
            t.created_by = ? OR 
            t.assigned_to = ? OR 
            ugm.user_id = ?
        )
    ");
    $stmt->execute([$taskId, $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $task = $stmt->fetch();
    
    if (!$task) {
        echo json_encode(['success' => false, 'error' => 'Task nicht gefunden oder keine Berechtigung']);
        exit;
    }
    
    // Simple status update
    if ($newStatus && in_array($newStatus, ['todo', 'doing', 'done'])) {
        $stmt = $pdo->prepare("UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newStatus, $taskId]);
        
        echo json_encode(['success' => true, 'message' => 'Status aktualisiert']);
        exit;
    }
    
    // Full task update
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? null;
    $priority = $_POST['priority'] ?? 'medium';
    $category = $_POST['category'] ?? '';
    $estimated_budget = $_POST['estimated_budget'] ?? null;
    $estimated_hours = $_POST['estimated_hours'] ?? null;
    $tags = $_POST['tags'] ?? '';
    $assigned_to = $_POST['assigned_to'] ?? null;
    $assigned_group_id = $_POST['assigned_group_id'] ?? null;
    $assignment_type = $_POST['assignment_type'] ?? 'user';
    
    if (empty($title)) {
        echo json_encode(['success' => false, 'error' => 'Titel ist erforderlich']);
        exit;
    }
    
    // Clear assignment based on type
    if ($assignment_type === 'user') {
        $assigned_group_id = null;
    } else {
        $assigned_to = null;
    }
    
    // Convert empty strings to null for database
    $due_date = !empty($due_date) ? $due_date : null;
    $estimated_budget = !empty($estimated_budget) ? (float)$estimated_budget : null;
    $estimated_hours = !empty($estimated_hours) ? (float)$estimated_hours : null;
    $assigned_to = !empty($assigned_to) ? (int)$assigned_to : null;
    $assigned_group_id = !empty($assigned_group_id) ? (int)$assigned_group_id : null;
    $category = !empty($category) ? $category : null;
    $tags = !empty($tags) ? $tags : null;
    
    $stmt = $pdo->prepare("
        UPDATE tasks SET 
            title = ?, description = ?, due_date = ?, priority = ?, category = ?,
            estimated_budget = ?, estimated_hours = ?, tags = ?,
            assigned_to = ?, assigned_group_id = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $result = $stmt->execute([
        $title, $description, $due_date, $priority, $category,
        $estimated_budget, $estimated_hours, $tags,
        $assigned_to, $assigned_group_id, $taskId
    ]);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Fehler beim Aktualisieren']);
        exit;
    }
    
    // Handle subtasks if provided
    if (isset($_POST['subtasks']) && is_array($_POST['subtasks'])) {
        // Delete existing subtasks
        $stmt = $pdo->prepare("DELETE FROM task_subtasks WHERE task_id = ?");
        $stmt->execute([$taskId]);
        
        // Insert new subtasks
        $stmt = $pdo->prepare("
            INSERT INTO task_subtasks (task_id, title, is_completed, sort_order, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        foreach ($_POST['subtasks'] as $index => $subtask) {
            if (!empty($subtask['title'])) {
                $isCompleted = isset($subtask['is_completed']) ? (int)$subtask['is_completed'] : 0;
                $stmt->execute([$taskId, $subtask['title'], $isCompleted, $index]);
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Task erfolgreich aktualisiert']);
    
} catch (PDOException $e) {
    error_log("Database error in task_update: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler']);
} catch (Exception $e) {
    error_log("Error in task_update: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Unbekannter Fehler']);
}
?>
