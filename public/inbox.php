<?php
// public/inbox.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controllers/inbox.php';

// Nach dem Laden der Aufgaben (z.B. vor dem Rendern des Templates):
echo "<!-- Debug inbox.php: count(\$tasks) = " . count($tasks) . " -->";
?>
