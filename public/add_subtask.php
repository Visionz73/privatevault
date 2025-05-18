<?php
session_start();
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/../src/lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $subtask_title = trim($_POST['subtask_title'] ?? '');
    if (!$task_id || $subtask_title === '') {
        header("Location: task_sheet.php?id=" . $task_id);
        exit;
    }
    // Insert the new sub-task; adjust table/column names as necessary.
    $stmt = $pdo->prepare("INSERT INTO sub_tasks (task_id, title, is_completed, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->execute([$task_id, $subtask_title]);
}
header("Location: task_sheet.php?id=" . $task_id);
exit;
?>
