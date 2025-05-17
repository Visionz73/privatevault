<?php
// public/inbox.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controllers/inbox.php';

$due_date = $_GET['due_date'] ?? ''; // Default-Wert, falls 'due_date' nicht gesetzt ist
?>
