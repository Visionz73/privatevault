<?php
// src/api/task_update.php
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';

// Debugging: Log incoming requests
error_log("task_update.php called with: " . json_encode($_POST));

$id     = $_POST['id'] ?? null;
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['todo','doing','done'], true)) {
  http_response_code(400);
  echo "Error: Invalid parameters. ID: $id, Status: $status";
  exit;
}

try {
  $stmt = $pdo->prepare('
    UPDATE tasks 
       SET status = ?, updated_at = NOW() 
     WHERE id = ?
  ');
  $result = $stmt->execute([$status, $id]);
  
  if ($result) {
    http_response_code(200);
    echo "Status updated successfully";
  } else {
    http_response_code(500);
    echo "Database update failed";
  }
} catch (Exception $e) {
  http_response_code(500);
  echo "Error: " . $e->getMessage();
}
