<?php
// src/controllers/inbox.php

require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
error_log("DEBUG: Inbox controller: user_id = " . $userId);

// 1) Welcher Filter? 'all' oder eine User-ID (Default: nur meine Tasks)
$filterAssignedTo = $_GET['assigned_to'] ?? $userId;
error_log("DEBUG: Filter assigned_to = " . $filterAssignedTo);

// 2) „Erledigt“-Button verarbeiten, dann zurück mit gleichem Filter
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

// 3) Liste aller User für Filter-Dropdown und Zuweisung
$users = $pdo->query(
  'SELECT id, username 
     FROM users 
  ORDER BY username'
)->fetchAll(PDO::FETCH_ASSOC);

// Für schnelles Nachschlagen im Template
$usersMap = array_column($users, 'username', 'id');

// 4) Tasks holen – je nach Filter
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

// Debug: Log the SQL parameters and number of tasks fetched
error_log("DEBUG: SQL params: " . print_r($params, true));
error_log("DEBUG: Number of tasks fetched: " . count($tasks));
error_log("DEBUG: Fetched tasks: " . print_r($tasks, true));

// 5) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
