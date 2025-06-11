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
    // Aufgabe aktualisieren
    $title = $_POST['title'] ?? $task['title'];
    $description = $_POST['description'] ?? $task['description'];
    $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $status = $_POST['status'] ?? $task['status'];
    
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $description, $dueDate, $status, $taskId]);
    
    header("Location: task_detail.php?id=$taskId");
    exit;
}

require_once __DIR__ . '/../../templates/task_detail.php';
?>
