<?php
// src/controllers/inbox.php

// 1) DB und Auth laden
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// 2) Session & Login-Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();

// 3) User & Filter
$userId           = $_SESSION['user_id'];
$filterAssignedTo = $_GET['assigned_to'] ?? $userId;

// 4) „Done“-Flag setzen
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $upd = $pdo->prepare("
        UPDATE tasks
           SET status = 'done'
         WHERE id = ? AND assigned_to = ?
    ");
    $upd->execute([(int)$_GET['done'], $userId]);
    header('Location: /inbox.php?assigned_to=' . urlencode($filterAssignedTo));
    exit;
}

// 5) Nutzer für Filter-Dropdown
$users    = $pdo->query("SELECT id, username FROM users ORDER BY username")
                ->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 6) WHERE-Klausel bauen
$where  = ["t.status != 'done'"];
$params = [];
if ($filterAssignedTo !== 'all') {
    $where[]  = 't.assigned_to = ?';
    $params[] = (int)$filterAssignedTo;
}

// 7) Tasks holen
$sql = "SELECT t.*, u.username AS creator
FROM tasks t
JOIN users u ON u.id = t.created_by
WHERE " . implode(' AND ', $where) . "
ORDER BY t.id DESC";

$stmt  = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 8) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
