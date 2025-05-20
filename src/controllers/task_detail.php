<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$taskId) {
    header('Location: inbox.php');
    exit;
}

// Aufgabe laden inkl. Erstellername und Empfängername
$stmt = $pdo->prepare("
    SELECT t.*, 
           creator.username as creator_name, 
           assignee.username as assignee_name
    FROM tasks t
    LEFT JOIN users creator ON creator.id = t.created_by
    LEFT JOIN users assignee ON assignee.id = t.assigned_to
    WHERE t.id = ?
");
$stmt->execute([$taskId]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: inbox.php');
    exit;
}

// Unteraufgaben laden
$stmt = $pdo->prepare("SELECT * FROM sub_tasks WHERE task_id = ? ORDER BY created_at");
$stmt->execute([$taskId]);
$subtasks = $stmt->fetchAll();

// Fortschritt berechnen (Prozent abgeschlossener Unteraufgaben)
$totalSubtasks = count($subtasks);
$completedSubtasks = 0;
foreach ($subtasks as $subtask) {
    if ($subtask['status'] === 'closed') {
        $completedSubtasks++;
    }
}
$progress = $totalSubtasks > 0 ? floor(($completedSubtasks / $totalSubtasks) * 100) : 0;

// Alle Benutzer für die Zuweisung laden
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
$users = $stmt->fetchAll();

// Benutzer hat Bearbeitungsrechte, wenn er Ersteller ist oder die Aufgabe ihm zugewiesen ist
$canEdit = ($_SESSION['user_id'] == $task['created_by'] || $_SESSION['user_id'] == $task['assigned_to']);

// POST-Anfragen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Unteraufgabe hinzufügen
    if (isset($_POST['add_subtask']) && !empty($_POST['subtask_title'])) {
        $stmt = $pdo->prepare("INSERT INTO sub_tasks (task_id, title, status) VALUES (?, ?, 'open')");
        $stmt->execute([$taskId, $_POST['subtask_title']]);
        header("Location: task_detail.php?id=$taskId");
        exit;
    }
    
    // Unteraufgabe Status ändern
    if (isset($_POST['toggle_subtask']) && isset($_POST['subtask_id'])) {
        $subtaskId = (int)$_POST['subtask_id'];
        $newStatus = $_POST['status'] === 'open' ? 'closed' : 'open';
        $stmt = $pdo->prepare("UPDATE sub_tasks SET status = ? WHERE id = ? AND task_id = ?");
        $stmt->execute([$newStatus, $subtaskId, $taskId]);
        header("Location: task_detail.php?id=$taskId");
        exit;
    }
    
    // Aufgabe aktualisieren
    if (isset($_POST['update_task']) && $canEdit) {
        $title = $_POST['title'] ?? $task['title'];
        $description = $_POST['description'] ?? $task['description'];
        $assigneeId = isset($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : $task['assigned_to'];
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, assigned_to = ?, due_date = ? WHERE id = ?");
        $stmt->execute([$title, $description, $assigneeId, $dueDate, $taskId]);
        
        header("Location: task_detail.php?id=$taskId");
        exit;
    }
    
    // Aufgabe als erledigt markieren
    if (isset($_POST['mark_done'])) {
        $stmt = $pdo->prepare("UPDATE tasks SET is_done = 1 WHERE id = ?");
        $stmt->execute([$taskId]);
        header('Location: inbox.php');
        exit;
    }
}

require_once __DIR__ . '/../../templates/task_detail.php';
?>
