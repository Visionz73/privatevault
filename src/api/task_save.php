<?php
// src/api/task_save.php - API endpoint for saving tasks
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
$assignedGroupId = ($assignmentType === 'group') ? ($_POST['assigned_group'] ?? null) : null;

// Recurrence data
$recurrenceType = $_POST['recurrence_type'] ?? 'none';
$recurrenceInterval = (!empty($_POST['recurrence_interval']) && $recurrenceType !== 'none') ? intval($_POST['recurrence_interval']) : null;
$recurrenceEndDate = (!empty($_POST['recurrence_end_date']) && $recurrenceType !== 'none') ? $_POST['recurrence_end_date'] : null;

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
                recurrence_type = :recurrence_type,
                recurrence_interval = :recurrence_interval,
                recurrence_end_date = :recurrence_end_date,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->bindParam(':id', $taskId);
        
    } else {
        // Create new task
        $stmt = $pdo->prepare("
            INSERT INTO tasks (
                title, description, created_by, due_date, 
                status, priority, is_done, assigned_to, assigned_group_id,
                recurrence_type, recurrence_interval, recurrence_end_date,
                created_at, updated_at, user_id
            ) VALUES (
                :title, :description, :created_by, :due_date,
                :status, :priority, :is_done, :assigned_to, :assigned_group_id,
                :recurrence_type, :recurrence_interval, :recurrence_end_date,
                NOW(), NOW(), :user_id
            )
        ");
        
        $stmt->bindParam(':created_by', $_SESSION['user_id']);
        $stmt->bindParam(':user_id', $_SESSION['user_id']); // This seems to be a duplicate column in your schema
    }
    
    // Bind common parameters
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $dueDate);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':is_done', $isDone);
    $stmt->bindParam(':assigned_to', $assignedTo);
    $stmt->bindParam(':assigned_group_id', $assignedGroupId);
    $stmt->bindParam(':recurrence_type', $recurrenceType);
    $stmt->bindParam(':recurrence_interval', $recurrenceInterval);
    $stmt->bindParam(':recurrence_end_date', $recurrenceEndDate);
    
    // Execute the statement
    $stmt->execute();
    
    // If creating new task, get the ID
    if (!$taskId) {
        $taskId = $pdo->lastInsertId();
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
