<?php
// src/api/task_save.php - Enhanced API endpoint for saving tasks
header('Content-Type: application/json');

// Required files
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Nicht authentifiziert']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
    exit;
}

// Get task data
$taskId = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
$status = $_POST['status'] ?? 'todo';
$priority = $_POST['priority'] ?? 'medium';
$isDone = ($status === 'done') ? 1 : 0;

// Assignment data
$assignmentType = $_POST['assignment_type'] ?? 'user';
$assignedTo = ($assignmentType === 'user') ? ($_POST['assigned_to'] ?? null) : null;
$assignedGroupId = ($assignmentType === 'group') ? ($_POST['assigned_group_id'] ?? null) : null;

// New enhanced fields
$estimatedBudget = !empty($_POST['estimated_budget']) ? floatval($_POST['estimated_budget']) : null;
$estimatedHours = !empty($_POST['estimated_hours']) ? floatval($_POST['estimated_hours']) : null;
$category = !empty($_POST['category']) ? $_POST['category'] : null;
$tags = !empty($_POST['tags']) ? trim($_POST['tags']) : null;

// Subtasks data
$subtasks = $_POST['subtasks'] ?? [];

// Validate required fields
if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Titel ist erforderlich']);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    if ($taskId) {
        // Update existing task
        $stmt = $pdo->prepare("
            UPDATE tasks 
            SET title = :title,
                description = :description,
                due_date = :due_date,
                status = :status,
                priority = :priority,
                is_done = :is_done,
                assigned_to = :assigned_to,
                assigned_group_id = :assigned_group_id,
                estimated_budget = :estimated_budget,
                estimated_hours = :estimated_hours,
                category = :category,
                tags = :tags,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':id' => $taskId,
            ':title' => $title,
            ':description' => $description,
            ':due_date' => $dueDate,
            ':status' => $status,
            ':priority' => $priority,
            ':is_done' => $isDone,
            ':assigned_to' => $assignedTo,
            ':assigned_group_id' => $assignedGroupId,
            ':estimated_budget' => $estimatedBudget,
            ':estimated_hours' => $estimatedHours,
            ':category' => $category,
            ':tags' => $tags
        ]);
        
    } else {
        // Create new task
        $stmt = $pdo->prepare("
            INSERT INTO tasks (
                title, description, created_by, due_date, 
                status, priority, is_done, assigned_to, assigned_group_id,
                estimated_budget, estimated_hours, category, tags,
                created_at, updated_at
            ) VALUES (
                :title, :description, :created_by, :due_date,
                :status, :priority, :is_done, :assigned_to, :assigned_group_id,
                :estimated_budget, :estimated_hours, :category, :tags,
                NOW(), NOW()
            )
        ");
        
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':created_by' => $_SESSION['user_id'],
            ':due_date' => $dueDate,
            ':status' => $status,
            ':priority' => $priority,
            ':is_done' => $isDone,
            ':assigned_to' => $assignedTo,
            ':assigned_group_id' => $assignedGroupId,
            ':estimated_budget' => $estimatedBudget,
            ':estimated_hours' => $estimatedHours,
            ':category' => $category,
            ':tags' => $tags
        ]);
        
        $taskId = $pdo->lastInsertId();
    }
    
    // Handle subtasks
    if (!empty($subtasks)) {
        // Create task_subtasks table if it doesn't exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS task_subtasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                task_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                is_completed TINYINT(1) DEFAULT 0,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
            )
        ");
        
        // Remove existing subtasks for update
        if ($taskId) {
            $stmt = $pdo->prepare("DELETE FROM task_subtasks WHERE task_id = ?");
            $stmt->execute([$taskId]);
        }
        
        // Insert/update subtasks
        $sortOrder = 0;
        foreach ($subtasks as $subtask) {
            if (!empty($subtask['title'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO task_subtasks (task_id, title, is_completed, sort_order)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $taskId,
                    trim($subtask['title']),
                    isset($subtask['is_completed']) ? intval($subtask['is_completed']) : 0,
                    $sortOrder++
                ]);
            }
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'task_id' => $taskId,
        'message' => 'Aufgabe gespeichert'
    ]);
    
} catch (PDOException $e) {
    // Roll back transaction on error
    $pdo->rollBack();
    
    echo json_encode([
        'success' => false,
        'error' => 'Datenbankfehler: ' . $e->getMessage()
    ]);
}
?>
