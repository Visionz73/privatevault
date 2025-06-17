<?php
// src/api/task_create.php - API endpoint for creating new tasks
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

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'todo';
$due_date = $_POST['due_date'] ?? null;
$priority = $_POST['priority'] ?? 'medium';
$category = $_POST['category'] ?? null;
$estimated_budget = $_POST['estimated_budget'] ?? null;
$estimated_hours = $_POST['estimated_hours'] ?? null;
$tags = $_POST['tags'] ?? null;
$assigned_to = $_POST['assigned_to'] ?? null;
$assigned_group_id = $_POST['assigned_group_id'] ?? null;

// Validation
if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Titel ist erforderlich']);
    exit;
}

// Validate status
$valid_statuses = ['todo', 'doing', 'done'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Ungültiger Status']);
    exit;
}

try {
    // Insert task
    $stmt = $pdo->prepare("
        INSERT INTO tasks (
            title, description, status, due_date, priority, category,
            estimated_budget, estimated_hours, tags, assigned_to, assigned_group_id,
            created_by, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([
        $title, $description, $status, $due_date, $priority, $category,
        $estimated_budget, $estimated_hours, $tags, $assigned_to, $assigned_group_id,
        $_SESSION['user_id']
    ]);
    
    $task_id = $pdo->lastInsertId();
    
    // Handle subtasks if provided
    if (isset($_POST['subtasks']) && is_array($_POST['subtasks'])) {
        $subtask_stmt = $pdo->prepare("
            INSERT INTO task_subtasks (task_id, title, is_completed, sort_order, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        foreach ($_POST['subtasks'] as $index => $subtask) {
            if (!empty($subtask['title'])) {
                $subtask_stmt->execute([
                    $task_id,
                    trim($subtask['title']),
                    isset($subtask['is_completed']) ? (int)$subtask['is_completed'] : 0,
                    $index
                ]);
            }
        }
    }
    
    echo json_encode(['success' => true, 'task_id' => $task_id]);
    
} catch (PDOException $e) {
    error_log("Database error in task_create.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler beim Erstellen der Aufgabe']);
} catch (Exception $e) {
    error_log("Error in task_create: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Unbekannter Fehler']);
}
?>
