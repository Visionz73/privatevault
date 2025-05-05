<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();
$userId = $_SESSION['user_id'];

/* ------------------------------------------------------------------
   letzte 5 Aufgaben, die MIR zugewiesen und NICHT erledigt sind
-------------------------------------------------------------------*/
$stmt = $pdo->prepare(
  'SELECT id, title, due_date
     FROM tasks
    WHERE assigned_to = ?
      AND status      != "done"
 ORDER BY id DESC           -- neueste oben
    LIMIT 5'
);
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

$stmt = $pdo->prepare(
  'SELECT COUNT(*) FROM tasks
    WHERE assigned_to = ?
      AND status      != "done"'
);
$stmt->execute([$userId]);
$openTaskCount = (int)$stmt->fetchColumn();

/* ------------------------------------------------------------------
   Dokumente (unverändert)
-------------------------------------------------------------------*/
$stmt = $pdo->prepare(
  'SELECT title
     FROM documents
    WHERE user_id = ? AND is_deleted = 0
 ORDER BY upload_date DESC
    LIMIT 5'
);
$stmt->execute([$userId]);
$docs      = $stmt->fetchAll();

$stmt = $pdo->prepare(
  'SELECT COUNT(*) FROM documents
   WHERE user_id = ? AND is_deleted = 0'
);
$stmt->execute([$userId]);
$docCount = (int)$stmt->fetchColumn();

/* ------------------------------------------------------------------*/
require_once __DIR__ . '/../../templates/dashboard.php';
