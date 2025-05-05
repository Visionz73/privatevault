<?php
// src/api/task_assign_update.php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';

$id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$assigned_to = isset($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : 0;

if (!$id || !$assigned_to) {
  echo json_encode(['success'=>false,'error'=>'Ungültige Daten']);
  exit;
}

// Nur Ersteller darf neu zuweisen
$stmt = $pdo->prepare('
  UPDATE tasks
     SET assigned_to = ?, updated_at = NOW()
   WHERE id = ? AND created_by = ?
');
$res = $stmt->execute([$assigned_to, $id, $_SESSION['user_id']]);
echo json_encode(['success' => (bool)$res]);
