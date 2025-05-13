<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();                  // alle Rollen dürfen lesen
requireRole(['admin','member']);  // Gäste ausgeschlossen

/* ------------------------------------------------------------------
   Aufgaben nach Status gruppiert
-------------------------------------------------------------------*/
$columns = ['todo'=>[], 'doing'=>[], 'done'=>[]];

$query = "SELECT t.id, t.title, t.description, t.assigned_to, t.due_date, t.status FROM tasks t"; // Füge t.status wieder hinzu
$result = $pdo->query($query);
foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
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

if (!isset($someVariable)) {
    die('Fehler: Variable $someVariable ist nicht definiert.');
}
