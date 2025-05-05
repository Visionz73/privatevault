<?php
// src/api/task_update.php
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';

$id     = $_POST['id'] ?? null;
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['todo','doing','done'], true)) {
  http_response_code(400);
  exit;
}

$stmt = $pdo->prepare('
  UPDATE tasks 
     SET status = ?, updated_at = NOW() 
   WHERE id = ? AND created_by = ?
');
$stmt->execute([$status, $id, $_SESSION['user_id']]);
