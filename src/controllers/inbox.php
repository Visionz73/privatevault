<?php
// src/controllers/inbox.php

// 1) DB, Auth & Session
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();

// 2) Aktueller User & Filter
$userId          = $_SESSION['user_id'];
$filterAssignedTo = $_GET['assigned_to'] ?? $userId;

// 3) Aufgaben als „done“ markieren
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $upd = $pdo->prepare("
        UPDATE tasks
           SET status = 'done'
         WHERE id = ? AND assigned_to = ?
    ");
    $upd->execute([(int)$_GET['done'], $userId]);
    header('Location: /src/controllers/inbox.php?assigned_to=' . urlencode($filterAssignedTo));
    exit;
}

// 4) Alle Nutzer für den Filter-Dropdown
$users    = $pdo->query("SELECT id, username FROM users ORDER BY username")
                ->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 5) WHERE-Klausel bauen
$where  = ["t.status != 'done'"];
$params = [];

if ($filterAssignedTo !== 'all') {
    $where[]  = 't.assigned_to = ?';
    $params[] = (int)$filterAssignedTo;
}

// 6) Tasks laden
$sql = "
    SELECT t.*, u.username AS creator
      FROM tasks t
      JOIN users u ON u.id = t.created_by
     WHERE " . implode(' AND ', $where) . "
  ORDER BY t.id DESC
";
$stmt  = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
