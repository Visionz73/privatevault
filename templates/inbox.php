<!-- templates/inbox.php -->
<?php
// Aus dem Controller: $tasks, $users, $filterUser
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inbox | Private Vault</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-bg flex">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-64 flex-1 p-8">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Filter-Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-text">Inbox</h1>
        <div class="relative">
          <button id="filterBtn" class="px-4 py-2 bg-white border rounded shadow flex items-center">
            <?= $filterUser === 'all'
                  ? 'Alle Aufgaben'
                  : 'Meine Aufgaben' ?>
            <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="filterMenu" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow hidden">
            <a href="inbox.php?user=all"
               class="block px-4 py-2 hover:bg-gray-100 <?= $filterUser==='all'?'bg-gray-100':'' ?>">
              Alle Aufgaben
            </a>
            <a href="inbox.php?user=<?= htmlspecialchars($_SESSION['user_id']) ?>"
               class="block px-4 py-2 hover:bg-gray-100 <?= $filterUser===$_SESSION['user_id']?'bg-gray-100':'' ?>">
              Meine Aufgaben
            </a>
          </div>
        </div>
      </div>

      <!-- Task-Liste -->
      <?php if (empty($tasks)): ?>
        <div class="p-6 bg-card-bg rounded shadow text-center text-gray-600">
          Keine offenen Aufgaben gefunden.
        </div>
      <?php else: ?>
        <ul class="space-y-4">
          <?php foreach ($tasks as $t): ?>
            <li class="bg-card-bg rounded shadow p-4 flex justify-between items-center">
              <div>
                <h2 class="font-medium"><?= htmlspecialchars($t['title']) ?></h2>
                <?php if (!empty($t['description'])): ?>
                  <p class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
                <?php endif; ?>
                <p class="text-xs text-gray-500">
                  Erstellt von <?= htmlspecialchars($t['creator']) ?>,
                  <?= !empty($t['due_date'])
                       ? 'fällig am '.date('d.m.Y', strtotime($t['due_date']))
                       : '' ?>
                </p>
              </div>
              <form method="get" action="inbox.php">
                <input type="hidden" name="user" value="<?= urlencode($filterUser) ?>">
                <button name="done" value="<?= $t['id'] ?>"
                        class="px-3 py-1 bg-green-600 text-white rounded text-sm">
                  Erledigt
                </button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

    </div>
  </main>

  <script>
    // Filter-Dropdown
    document.getElementById('filterBtn').addEventListener('click', e => {
      e.stopPropagation();
      document.getElementById('filterMenu').classList.toggle('hidden');
    });
    document.addEventListener('click', () => {
      document.getElementById('filterMenu').classList.add('hidden');
    });
  </script>
</body>
</html>
