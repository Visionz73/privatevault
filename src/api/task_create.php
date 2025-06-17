<?php
// src/api/task_create.php - API endpoint for creating new tasks
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Nicht authentifiziert']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Nur POST erlaubt']);
    exit;
}

try {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? null;
    $priority = $_POST['priority'] ?? 'medium';
    $category = $_POST['category'] ?? '';
    $estimated_budget = $_POST['estimated_budget'] ?? null;
    $estimated_hours = $_POST['estimated_hours'] ?? null;
    $tags = $_POST['tags'] ?? '';
    $status = $_POST['status'] ?? 'todo';
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
        INSERT INTO tasks (
            title, description, due_date, priority, category, status,
            estimated_budget, estimated_hours, tags,
            assigned_to, assigned_group_id, created_by, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $result = $stmt->execute([
        $title, $description, $due_date, $priority, $category, $status,
        $estimated_budget, $estimated_hours, $tags,
        $assigned_to, $assigned_group_id, $_SESSION['user_id']
    ]);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen']);
        exit;
    }
    
    $taskId = $pdo->lastInsertId();
    
    // Handle subtasks if provided
    if (isset($_POST['subtasks']) && is_array($_POST['subtasks'])) {
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
    
    echo json_encode(['success' => true, 'message' => 'Task erfolgreich erstellt', 'task_id' => $taskId]);
    
} catch (PDOException $e) {
    error_log("Database error in task_create: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler']);
} catch (Exception $e) {
    error_log("Error in task_create: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Unbekannter Fehler']);
}
?>
