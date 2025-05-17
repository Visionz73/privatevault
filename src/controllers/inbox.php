<?php
// Debug-Modus: Alle Fehler anzeigen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $upd = $pdo->prepare("UPDATE tasks SET status = 'done' WHERE id = ? AND assigned_to = ?");
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
<<<<<<< HEAD
<<<<<<< HEAD
$sql   = "
    SELECT t.*, u.username AS creator
      FROM tasks t
      JOIN users u ON u.id = t.created_by
     WHERE " . implode(' AND ', $where) . "
  ORDER BY t.id DESC
<<<<<<< HEAD
';
<<<<<<< HEAD
<<<<<<< HEAD
$stmt = $pdo->prepare("
    SELECT
        id,
        title,
        is_done,
        created_at          AS due_date   -- oder due_date, falls Spalte existiert
    FROM tasks
    WHERE user_id = :uid
      AND is_done = 0
    ORDER BY created_at DESC
");
$stmt->execute([':uid' => $userId]);
$tasks = $stmt->fetchAll();
=======
$stmt  = $pdo->prepare($sql);
=======
$stmt = $pdo->prepare($sql);
>>>>>>> dd2066b8ba16da0b3016375319f0ac4eb4b3daf8
=======
";
=======
$sql = <<<SQL
SELECT t.*, u.username AS creator
=======
$sql = "SELECT t.*, u.username AS creator
>>>>>>> 8c1ab45d57a53431f39b55defeeec21568aea470
FROM tasks t
JOIN users u ON u.id = t.created_by
WHERE " . implode(' AND ', $where) . "
ORDER BY t.id DESC";
<<<<<<< HEAD

>>>>>>> 6b00b442aca23b8811f8b225ce36c1b8fe2628fa
=======
>>>>>>> d4627d3017a75c5f7220b1e9e953f8fedb3ef570
$stmt  = $pdo->prepare($sql);
>>>>>>> 2afea5308a465b63f73d8d8c9d0f91d1ed822722
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
>>>>>>> 4ae26bae264f4177682599ef410dc87fdcba950d

// 8) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
