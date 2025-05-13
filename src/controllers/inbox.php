<?php
// src/controllers/inbox.php

// 1) Session & Auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

// 2) Aktuellen User ermitteln
$userId = $_SESSION['user_id'];

// 3) Filter aus GET ziehen: 'all' oder eine User-ID (default: nur meine Tasks)
$filterAssignedTo = $_GET['assigned_to'] ?? $userId;

// 4) „Erledigt“-Markierung verarbeiten
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $stmt = $pdo->prepare(
        'UPDATE tasks
            SET status = "done"
          WHERE id = ? AND assigned_to = ?'
    );
    $stmt->execute([(int)$_GET['done'], $userId]);
    header('Location: inbox.php?assigned_to=' . urlencode($filterAssignedTo));
    exit;
}

// 5) Alle Nutzer für das Filter-Dropdown holen
$users = $pdo->query(
    'SELECT id, username
       FROM users
    ORDER BY username'
)->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 6) WHERE-Klausel je nach Filter aufbauen
$where  = ['t.status != "done"'];
$params = [];

if ($filterAssignedTo !== 'all') {
    $where[]  = 't.assigned_to = ?';
    $params[] = (int)$filterAssignedTo;
}

// 7) Tasks abfragen (inkl. Creator-Name)
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

// 8) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
