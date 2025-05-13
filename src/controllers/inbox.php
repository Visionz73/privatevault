<?php
// src/controllers/inbox.php

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
// Determine filter: either 'all' or a specific user ID (default: only my tasks)
$filterAssignedTo = $_GET['assigned_to'] ?? $userId;

// Process "done" button if clicked
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $stmt = $pdo->prepare(
        'UPDATE tasks
            SET status = "done"
          WHERE id = ? AND assigned_to = ?'
    );
    $stmt->execute([ (int)$_GET['done'], $userId ]);
    header('Location: inbox.php?assigned_to=' . urlencode($filterAssignedTo));
    exit;
}

// 3) Retrieve list of all users for the filter dropdown
$users = $pdo->query(
  'SELECT id, username 
     FROM users 
  ORDER BY username'
)->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 4) Retrieve tasks based on filter
$where  = ['t.status != "done"'];
$params = [];
if ($filterAssignedTo !== 'all') {
    $where[]  = 't.assigned_to = ?';
    $params[] = (int)$filterAssignedTo;
}
$sql = '
  SELECT t.*, u.username AS creator
    FROM tasks t
    JOIN users u ON u.id = t.created_by
   WHERE ' . implode(' AND ', $where) . '
ORDER BY t.id DESC
';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// You may optionally log the fetched tasks for debugging
error_log("DEBUG: Filter assigned_to = " . $filterAssignedTo);
error_log("DEBUG: Number of tasks fetched = " . count($tasks));

// 5) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
?>
