<?php
// src/controllers/inbox.php

// 1) Session und Auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

// 2) Aktuellen User und Filter auslesen
$userId     = $_SESSION['user_id'];
$filterUser = $_GET['user'] ?? $userId;  // 'all' oder eine User-ID

// 3) „Erledigt“-Button verarbeiten
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $stmt = $pdo->prepare(
        'UPDATE tasks
            SET status = "done"
          WHERE id = ? AND created_by = ?'
    );
    $stmt->execute([(int)$_GET['done'], $userId]);
    header('Location: inbox.php?user=' . urlencode($filterUser));
    exit;
}

// 4) Alle User fürs Dropdown laden
$users    = $pdo->query('SELECT id, username FROM users ORDER BY username')
                ->fetchAll(PDO::FETCH_ASSOC);

// 5) WHERE-Klausel anhand des Filters bauen
$where  = ['t.status != "done"'];
$params = [];

if ($filterUser !== 'all') {
    $where[]  = 't.created_by = ?';
    $params[] = (int)$filterUser;
}

// 6) Tasks abfragen
$sql = '
    SELECT t.*, u.username AS creator
      FROM tasks t
      JOIN users u ON u.id = t.created_by
     WHERE ' . implode(' AND ', $where) . '
  ORDER BY t.id DESC
';
$stmt  = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7) Template laden
require_once __DIR__ . '/../../templates/inbox.php';
