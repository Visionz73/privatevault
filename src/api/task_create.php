<?php
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'todo';
    $priority = $_POST['priority'] ?? 'medium';
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
    $assigned_group_id = !empty($_POST['assigned_group_id']) ? (int)$_POST['assigned_group_id'] : null;
    $category = $_POST['category'] ?? null;
    $estimated_budget = !empty($_POST['estimated_budget']) ? (float)$_POST['estimated_budget'] : null;
    $estimated_hours = !empty($_POST['estimated_hours']) ? (float)$_POST['estimated_hours'] : null;
    $tags = trim($_POST['tags'] ?? '');
    
    if (empty($title)) {
        echo json_encode(['success' => false, 'error' => 'Titel ist erforderlich']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO tasks (
            title, description, status, priority, due_date, 
            assigned_to, assigned_group_id, category, estimated_budget, 
            estimated_hours, tags, created_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([
        $title, $description, $status, $priority, $due_date,
        $assigned_to, $assigned_group_id, $category, $estimated_budget,
        $estimated_hours, $tags, $_SESSION['user_id']
    ]);
    
    if ($result) {
        $taskId = $pdo->lastInsertId();
        
        // Handle subtasks if provided
        if (!empty($_POST['subtasks'])) {
            $subtaskStmt = $pdo->prepare("
                INSERT INTO task_subtasks (task_id, title, is_completed, sort_order, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            foreach ($_POST['subtasks'] as $index => $subtask) {
                if (!empty($subtask['title'])) {
                    $subtaskStmt->execute([
                        $taskId,
                        trim($subtask['title']),
                        isset($subtask['is_completed']) ? (int)$subtask['is_completed'] : 0,
                        $index
                    ]);
                }
            }
        }
        
        echo json_encode(['success' => true, 'task_id' => $taskId]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen der Aufgabe']);
    }
    
} catch (PDOException $e) {
    error_log("Task creation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler']);
}
?>
