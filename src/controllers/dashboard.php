<?php
// 1) Session starten / Login prüfen
session_start();
require_once __DIR__.'/../lib/auth.php';
requireLogin();
$userId = $_SESSION['user_id'];

// 2) DB-Verbindung
require_once __DIR__.'/../lib/db.php';

// 3) Tasks zählen
$stmtCount = $pdo->prepare("
  SELECT COUNT(*) 
    FROM tasks t
   WHERE t.is_done != 1
     AND t.assigned_to = ?
     AND t.created_by != ?
");
$stmtCount->execute([$userId, $userId]);
$openTaskCount = (int)$stmtCount->fetchColumn();

// 4) Tasks holen
$stmtTasks = $pdo->prepare("
  SELECT t.*,
         u_creator.username AS creator_name,
         u_assignee.username AS assignee_name
    FROM tasks t
    LEFT JOIN users u_creator ON u_creator.id = t.created_by
    LEFT JOIN users u_assignee ON u_assignee.id = t.assigned_to
   WHERE t.is_done != 1
     AND t.assigned_to = ?
     AND t.created_by != ?
   ORDER BY t.created_at DESC
");
$stmtTasks->execute([$userId, $userId]);
$tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

// 5) Dokumente laden (need this for the documents widget)
$stmt = $pdo->prepare(
    'SELECT title, upload_date 
     FROM documents 
     WHERE user_id = ? 
     AND is_deleted = 0
     ORDER BY upload_date DESC
     LIMIT 5'
);
$stmt->execute([$userId]);
$docs = $stmt->fetchAll();

$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM documents WHERE user_id = ? AND is_deleted = 0'
);
$stmt->execute([$userId]);
$docCount = (int)$stmt->fetchColumn();

// 6) Termine laden (need this for the events widget)
$stmt = $pdo->prepare("SELECT id, title, event_date FROM events WHERE created_by = ? ORDER BY event_date ASC");
$stmt->execute([$userId]);
$events = $stmt->fetchAll();

// 7) Get user data for greeting
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 8) Template rendern
require_once __DIR__.'/../../templates/dashboard.php';
?>