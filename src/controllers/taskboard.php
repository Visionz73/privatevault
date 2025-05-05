<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();                  // alle Rollen dürfen lesen
requireRole(['admin','member']);  // Gäste ausgeschlossen

/* ------------------------------------------------------------------
   Aufgaben nach Status gruppiert
-------------------------------------------------------------------*/
$columns = ['todo'=>[], 'doing'=>[], 'done'=>[]];

$stmt = $pdo->query(
  'SELECT t.id, t.title, t.description, t.status, t.due_date,
          au.username  AS assignee
     FROM tasks t
LEFT JOIN users au ON au.id = t.assigned_to
 ORDER BY t.id DESC'
);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $columns[$row['status']][] = $row;
}

/* ------------------------------------------------------------------
   Alle Users fürs Dropdown
-------------------------------------------------------------------*/
$allUsers = $pdo->query(
  'SELECT id, username FROM users ORDER BY username'
)->fetchAll();

/* ------------------------------------------------------------------*/
require_once __DIR__ . '/../../templates/taskboard.php';
