<?php
// public/taskboard.php — Entry point for the Kanban board
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config.php';           // DB + global settings
require_once __DIR__ . '/../src/controllers/taskboard.php';
?>
