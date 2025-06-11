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
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Inbox | Private Vault</title>
  <link rel="stylesheet" href="/privatevault/css/main.css">
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>
  
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <!-- ...existing code... -->
  </main>
</body>
</html>
