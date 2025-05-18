<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();
$userId = $_SESSION['user_id'];

/* ------------------------------------------------------------------
   Alle Aufgaben, die MIR zugewiesen und NICHT erledigt sind
   (d.h. nur Aufgaben, die noch erledigt werden müssen)
-------------------------------------------------------------------*/
$stmt = $pdo->prepare(
    'SELECT id, title, created_at
       FROM tasks
      WHERE assigned_to = ?
        AND is_done != 1
   ORDER BY id DESC'
);
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

$stmt = $pdo->prepare(
    'SELECT COUNT(*)
       FROM tasks
      WHERE assigned_to = ?
        AND is_done != 1'
);
$stmt->execute([$userId]);
$openTaskCount = (int)$stmt->fetchColumn();

/* ------------------------------------------------------------------
   Dokumente laden
-------------------------------------------------------------------*/
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

/* ------------------------------------------------------------------
   Termine (alle Termine, die noch erstellt wurden)
-------------------------------------------------------------------*/
$stmt = $pdo->prepare("SELECT id, title, event_date FROM events WHERE created_by = ? ORDER BY event_date ASC");
$stmt->execute([$userId]);
$events = $stmt->fetchAll();

// Load tasks
$stmt = $pdo->prepare("
    SELECT t.*, u.username as creator_name, u2.username as assignee_name
    FROM tasks t 
    LEFT JOIN users u ON t.creator_id = u.id
    LEFT JOIN users u2 ON t.assignee_id = u2.id
    WHERE t.is_deleted = 0
    ORDER BY t.created_at DESC
");
$stmt->execute();
$tasks = $stmt->fetchAll();

// Set filtered tasks same as tasks initially
$filteredTasks = $tasks;

// Count open tasks
$openTaskCount = count($tasks);

/* ------------------------------------------------------------------*/
require_once __DIR__ . '/../../templates/dashboard.php';

