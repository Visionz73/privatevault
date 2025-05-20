<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();                  // alle Rollen dürfen lesen
requireRole(['admin','member']);  // Gäste ausgeschlossen

// Get current user ID
$userId = $_SESSION['user_id'];

// Filter mode: 'all' or 'user'
$filterMode = $_GET['filter'] ?? 'all';

/* ------------------------------------------------------------------
   Aufgaben nach Status gruppiert mit Filter-Option
-------------------------------------------------------------------*/
$columns = ['todo'=>[], 'doing'=>[], 'done'=>[]];

// Basis Query
$query = "
    SELECT t.id, t.title, t.description, t.created_by, t.assigned_to, t.due_date, t.status,
           uc.username AS creator_name, ua.username AS assignee_name
    FROM tasks t
    LEFT JOIN users uc ON t.created_by = uc.id
    LEFT JOIN users ua ON t.assigned_to = ua.id
";

// Filter anwenden - NUR zugewiesene Aufgaben anzeigen
if ($filterMode === 'user') {
    $query .= " WHERE t.assigned_to = ?";  // Nur zugewiesene Aufgaben 
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
} else {
    $stmt = $pdo->query($query);
}

// Gruppieren nach Status
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    // Fallback wenn Status nicht existiert
    $status = in_array($row['status'], array_keys($columns)) ? $row['status'] : 'todo';
    $columns[$status][] = $row;
}

/* ------------------------------------------------------------------
   Alle Users fürs Dropdown
-------------------------------------------------------------------*/
$allUsers = $pdo->query(
  'SELECT id, username FROM users ORDER BY username'
)->fetchAll();

/* ------------------------------------------------------------------*/
require_once __DIR__ . '/../../templates/taskboard.php';
