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
   Dokumente mit Kategorien laden
-------------------------------------------------------------------*/
$stmt = $pdo->prepare(
    'SELECT d.title, d.filename, c.name as category
     FROM documents d
     JOIN document_categories c ON c.id = d.category_id
     WHERE d.user_id = ? 
     AND d.is_deleted = 0
     ORDER BY d.upload_date DESC
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

/* ------------------------------------------------------------------*/
require_once __DIR__ . '/../../templates/dashboard.php';

