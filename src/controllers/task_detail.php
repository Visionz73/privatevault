<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$taskId) {
    header('Location: inbox.php');
    exit;
}

// Aufgabe laden inkl. Erstellername, Empfängername und Gruppenname
$stmt = $pdo->prepare("
    SELECT t.*, 
           creator.username as creator_name, 
           assignee.username as assignee_name,
           g.name as group_name
    FROM tasks t
    LEFT JOIN users creator ON creator.id = t.created_by
    LEFT JOIN users assignee ON assignee.id = t.assigned_to
    LEFT JOIN user_groups g ON g.id = t.assigned_group_id
    WHERE t.id = ?
");
$stmt->execute([$taskId]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: inbox.php');
    exit;
}

// Load subtasks
$subtasks = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM task_subtasks WHERE task_id = ? ORDER BY sort_order ASC, created_at ASC");
    $stmt->execute([$taskId]);
    $subtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Subtasks table might not exist yet, create it
    try {
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
    } catch (PDOException $createError) {
        // Log error but continue
        error_log("Could not create task_subtasks table: " . $createError->getMessage());
    }
}

// Calculate subtask progress
$totalSubtasks = count($subtasks);
$completedSubtasks = 0;
foreach ($subtasks as $subtask) {
    if ($subtask['is_completed']) {
        $completedSubtasks++;
    }
}
$subtaskProgress = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;

// Check if user can edit (creator or assignee or member of assigned group)
$canEdit = ($_SESSION['user_id'] == $task['created_by'] || $_SESSION['user_id'] == $task['assigned_to']);

// If assigned to group, check if user is member
if (!$canEdit && $task['assigned_group_id']) {
    $stmt = $pdo->prepare("SELECT 1 FROM user_group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$task['assigned_group_id'], $_SESSION['user_id']]);
    $canEdit = (bool)$stmt->fetch();
}

// Check if we're in edit mode
$editMode = isset($_GET['edit']) && $canEdit;

// POST-Anfragen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
    // Handle subtask operations
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_subtask':
                $subtaskTitle = trim($_POST['subtask_title'] ?? '');
                if (!empty($subtaskTitle)) {
                    $stmt = $pdo->prepare("INSERT INTO task_subtasks (task_id, title, sort_order) VALUES (?, ?, ?)");
                    $stmt->execute([$taskId, $subtaskTitle, $totalSubtasks]);
                }
                header("Location: task_detail.php?id=$taskId");
                exit;
                
            case 'toggle_subtask':
                $subtaskId = (int)($_POST['subtask_id'] ?? 0);
                $isCompleted = (int)($_POST['is_completed'] ?? 0);
                if ($subtaskId > 0) {
                    $stmt = $pdo->prepare("UPDATE task_subtasks SET is_completed = ? WHERE id = ? AND task_id = ?");
                    $stmt->execute([$isCompleted, $subtaskId, $taskId]);
                }
                header("Location: task_detail.php?id=$taskId");
                exit;
                
            case 'delete_subtask':
                $subtaskId = (int)($_POST['subtask_id'] ?? 0);
                if ($subtaskId > 0) {
                    $stmt = $pdo->prepare("DELETE FROM task_subtasks WHERE id = ? AND task_id = ?");
                    $stmt->execute([$subtaskId, $taskId]);
                }
                header("Location: task_detail.php?id=$taskId");
                exit;
        }
    }
    
    // Aufgabe aktualisieren
    $title = $_POST['title'] ?? $task['title'];
    $description = $_POST['description'] ?? $task['description'];
    $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $status = $_POST['status'] ?? $task['status'];
    $estimatedBudget = !empty($_POST['estimated_budget']) ? floatval($_POST['estimated_budget']) : null;
    $estimatedHours = !empty($_POST['estimated_hours']) ? floatval($_POST['estimated_hours']) : null;
    $category = !empty($_POST['category']) ? $_POST['category'] : null;
    $priority = $_POST['priority'] ?? $task['priority'];
    $tags = !empty($_POST['tags']) ? trim($_POST['tags']) : null;
    
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET title = ?, description = ?, due_date = ?, status = ?, 
            estimated_budget = ?, estimated_hours = ?, category = ?, 
            priority = ?, tags = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $title, $description, $dueDate, $status, 
        $estimatedBudget, $estimatedHours, $category, 
        $priority, $tags, $taskId
    ]);
    
    header("Location: task_detail.php?id=$taskId");
    exit;
}

require_once __DIR__ . '/../../templates/task_detail.php';
?>
