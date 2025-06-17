<?php
// src/api/task_create.php - API endpoint for creating new tasks
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';

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

// Get form data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'todo';
$priority = $_POST['priority'] ?? 'medium';
$category = $_POST['category'] ?? '';
$due_date = $_POST['due_date'] ?? null;
$estimated_budget = !empty($_POST['estimated_budget']) ? floatval($_POST['estimated_budget']) : null;
$estimated_hours = !empty($_POST['estimated_hours']) ? floatval($_POST['estimated_hours']) : null;
$tags = trim($_POST['tags'] ?? '');
$assignment_type = $_POST['assignment_type'] ?? 'user';
$assigned_to = ($assignment_type === 'user' && !empty($_POST['assigned_to'])) ? intval($_POST['assigned_to']) : null;
$assigned_group_id = ($assignment_type === 'group' && !empty($_POST['assigned_group_id'])) ? intval($_POST['assigned_group_id']) : null;

// Validation
if (empty($title)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Titel ist erforderlich']);
    exit;
}

if (!in_array($status, ['todo', 'doing', 'done'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültiger Status']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Insert main task
    $stmt = $pdo->prepare("
        INSERT INTO tasks (
            title, description, status, priority, category, due_date,
            estimated_budget, estimated_hours, tags, assigned_to, assigned_group_id,
            created_by, created_at, updated_at
        ) VALUES (
            :title, :description, :status, :priority, :category, :due_date,
            :estimated_budget, :estimated_hours, :tags, :assigned_to, :assigned_group_id,
            :created_by, NOW(), NOW()
        )
    ");
    
    $result = $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':status' => $status,
        ':priority' => $priority,
        ':category' => $category,
        ':due_date' => $due_date ?: null,
        ':estimated_budget' => $estimated_budget,
        ':estimated_hours' => $estimated_hours,
        ':tags' => $tags,
        ':assigned_to' => $assigned_to,
        ':assigned_group_id' => $assigned_group_id,
        ':created_by' => $_SESSION['user_id']
    ]);
    
    if (!$result) {
        throw new Exception('Fehler beim Erstellen der Aufgabe');
    }
    
    $taskId = $pdo->lastInsertId();
    
    // Handle subtasks
    if (!empty($_POST['subtasks']) && is_array($_POST['subtasks'])) {
        $stmt = $pdo->prepare("
            INSERT INTO task_subtasks (task_id, title, is_completed, sort_order, created_at)
            VALUES (:task_id, :title, :is_completed, :sort_order, NOW())
        ");
        
        foreach ($_POST['subtasks'] as $index => $subtask) {
            if (!empty($subtask['title'])) {
                $stmt->execute([
                    ':task_id' => $taskId,
                    ':title' => trim($subtask['title']),
                    ':is_completed' => intval($subtask['is_completed'] ?? 0),
                    ':sort_order' => $index
                ]);
            }
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Aufgabe erfolgreich erstellt',
        'task_id' => $taskId
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Database error in task_create: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Datenbankfehler beim Erstellen der Aufgabe'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("General error in task_create: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
