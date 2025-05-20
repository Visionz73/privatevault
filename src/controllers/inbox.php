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
$userId = $_SESSION['user_id'];
$filterAssignedTo = $_GET['assigned_to'] ?? 'all';

// 4) „Done"-Flag setzen
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    // Aktualisiere die Aufgabe als erledigt, indem is_done auf 1 gesetzt wird
    $upd = $pdo->prepare("UPDATE tasks SET is_done = 1 WHERE id = ? AND assigned_to = ?");
    $upd->execute([(int)$_GET['done'], $userId]);
    header('Location: /inbox.php?assigned_to=' . urlencode($filterAssignedTo));
    exit;
}

// 5) Nutzer für Filter-Dropdown
$users = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 6) WHERE-Klausel bauen basierend auf Filter
$where = ["t.is_done != 1"];
$params = [];

// Nur bestimmte zugewiesene Aufgaben oder alle Aufgaben
if ($filterAssignedTo !== 'all' && is_numeric($filterAssignedTo)) {
    $where[] = "t.assigned_to = ?";
    $params[] = (int)$filterAssignedTo;
}

// 7) Tasks holen
$sql = "SELECT t.*, 
               creator.username AS creator_name,
               assignee.username AS assignee_name
        FROM tasks t
        LEFT JOIN users creator ON creator.id = t.created_by
        LEFT JOIN users assignee ON assignee.id = t.assigned_to
        WHERE " . implode(' AND ', $where) . "
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 8) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
