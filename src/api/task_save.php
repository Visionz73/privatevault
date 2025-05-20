<?php
// src/api/task_save.php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';

$id          = $_POST['id'] ?? null;
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status      = $_POST['status'] ?? 'todo';
$recurrence_type = $_POST['recurrence_type'] ?? 'none';
$recurrence_interval = isset($_POST['recurrence_interval']) && $_POST['recurrence_interval'] !== '' ? (int)$_POST['recurrence_interval'] : null;
$recurrence_end_date = isset($_POST['recurrence_end_date']) && $_POST['recurrence_end_date'] !== '' ? $_POST['recurrence_end_date'] : null;

if ($title === '') {
  echo json_encode(['success'=>false,'error'=>'Titel erforderlich']);
  exit;
}

if (!in_array($status, ['todo','doing','done'], true)) {
  $status = 'todo';
}

if ($id) {
  $stmt = $pdo->prepare('
    UPDATE tasks
       SET title = ?, description = ?, status = ?, recurrence_type = ?, recurrence_interval = ?, recurrence_end_date = ?, updated_at = NOW()
     WHERE id = ? AND created_by = ?
  ');
  $res = $stmt->execute([$title, $description, $status, $recurrence_type, $recurrence_interval, $recurrence_end_date, $id, $_SESSION['user_id']]);
} else {
  $stmt = $pdo->prepare('
    INSERT INTO tasks (title, description, status, created_by, recurrence_type, recurrence_interval, recurrence_end_date)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ');
  $res = $stmt->execute([$title, $description, $status, $_SESSION['user_id'], $recurrence_type, $recurrence_interval, $recurrence_end_date]);
}

echo json_encode(['success'=> (bool)$res]);
