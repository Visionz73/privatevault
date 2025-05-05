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
       SET title = ?, description = ?, status = ?, updated_at = NOW()
     WHERE id = ? AND created_by = ?
  ');
  $res = $stmt->execute([$title, $description, $status, $id, $_SESSION['user_id']]);
} else {
  $stmt = $pdo->prepare('
    INSERT INTO tasks (title, description, status, created_by)
    VALUES (?, ?, ?, ?)
  ');
  $res = $stmt->execute([$title, $description, $status, $_SESSION['user_id']]);
}

echo json_encode(['success'=> (bool)$res]);
