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

$userId = $_SESSION['user_id'];

// Als erledigt markierte Tasks ausblenden und nur eigene zugewiesene laden
$stmt = $pdo->prepare("
  SELECT t.*, 
         uc.username AS creator_name, 
         ua.username AS assignee_name
    FROM tasks t
    LEFT JOIN users uc ON uc.id = t.created_by
    LEFT JOIN users ua ON ua.id = t.assigned_to
   WHERE t.assigned_to = ?
     AND t.is_done != 1
   ORDER BY t.created_at DESC
");
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Für Dropdown (optional, wenn noch gebraucht)
$users    = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll();
$usersMap = array_column($users, 'username', 'id');

// 8) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
