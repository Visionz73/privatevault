<?php
// Vom Controller bereitgestellt:
//   $tasks           — Array der Task-Datensätze
//   $users           — Array aller User (id, username)
//   $usersMap        — Map id ⇒ username
//   $filterAssignedTo— 'all' oder User-ID
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

      <!-- Header mit Filter-Button -->
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-text">Inbox</h1>
        <div class="relative z-10">
          <button id="filterBtn"
                  class="flex items-center px-4 py-2 bg-white border border-border rounded-lg shadow hover:bg-gray-50 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 12h12M3 20h6"/>
            </svg>
            <?php if ($filterAssignedTo === 'all'): ?>
              Alle Aufgaben
            <?php else: ?>
              Aufgaben für: <?= htmlspecialchars($usersMap[$filterAssignedTo] ?? 'Unbekannt') ?>
            <?php endif; ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="filterMenu" class="absolute right-0 mt-2 w-60 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-20 max-h-80 overflow-y-auto">
            <a href="/inbox.php?assigned_to=all"
               class="block px-4 py-2 hover:bg-gray-100 <?= $filterAssignedTo==='all'?'bg-gray-100':'' ?>">
              Alle Aufgaben
            </a>
            <div class="border-t border-gray-200 my-1"></div>
            <?php foreach($users as $u): ?>
              <a href="/inbox.php?assigned_to=<?= $u['id'] ?>"
                 class="block px-4 py-2 hover:bg-gray-100 <?= ((string)$filterAssignedTo)===(string)$u['id']?'bg-gray-100':'' ?>">
                <?= htmlspecialchars($u['username']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Aufgabenliste -->
      <?php if(empty($tasks)): ?>
        <div class="p-6 bg-white/60 backdrop-blur-sm rounded-xl shadow-sm text-center text-gray-500">
          Keine Aufgaben gefunden.
        </div>
      <?php else: ?>
        <ul class="space-y-4">
          <?php foreach($tasks as $t): ?>
            <li class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-5 relative">
              <div class="flex items-start gap-4">
                <div class="h-10 w-10 rounded-full bg-[#4A90E2]/10 flex items-center justify-center font-semibold text-[#4A90E2] uppercase">
                  <?= htmlspecialchars(substr($t['creator_name'] ?? '', 0, 2)) ?>
                </div>
                <div class="flex-1 cursor-pointer" onclick="openDetailModal(<?= $t['id'] ?>)">
                  <h2 class="text-base font-medium text-gray-900 mb-1"><?= htmlspecialchars($t['title'] ?? '') ?></h2>
                  <?php if(!empty($t['description'])): ?>
                    <p class="text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars($t['description']) ?></p>
                  <?php endif; ?>
                  <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2">
                    <span>
                      <span class="font-medium">Von:</span> 
                      <?= htmlspecialchars($t['creator_name'] ?? 'Unbekannt') ?>
                    </span>
                    <span>
                      <span class="font-medium">Für:</span> 
                      <?php if ($t['assigned_group_id']): ?>
                        <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full text-xs">
                          Gruppe: <?= htmlspecialchars($t['group_name'] ?? 'Unbekannt') ?>
                        </span>
                      <?php else: ?>
                        <?= htmlspecialchars($t['assignee_name'] ?? 'Nicht zugewiesen') ?>
                      <?php endif; ?>
                    </span>
                    <?php if(!empty($t['due_date'])): ?>
                      <span>
                        <span class="font-medium">Fällig:</span>
                        <?= date('d.m.Y', strtotime($t['due_date'])) ?>
                      </span>
                    <?php endif; ?>
                    <?php if(isset($t['due_date']) && strtotime($t['due_date']) < time()): ?>
                      <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full">Überfällig</span>
                    <?php endif; ?>
                  </div>
                </div>
                <!-- Done button -->
                <form method="get" class="absolute top-2 right-2">
                  <input type="hidden" name="done" value="<?= $t['id'] ?>">
                  <input type="hidden" name="assigned_to" value="<?= htmlspecialchars($filterAssignedTo) ?>">
                  <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs">
                    Erledigt
                  </button>
                </form>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

    </div>
  </main>

  <!-- Detail-Modal -->
  <div id="detailModal" class="fixed inset-0 hidden bg-black/50 flex items-center justify-center z-50">
    <div id="detailContent" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Filter-Dropdown toggeln
      const btn  = document.getElementById('filterBtn');
      const menu = document.getElementById('filterMenu');
      btn.addEventListener('click', e => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
      });
      document.addEventListener('click', () => menu.classList.add('hidden'));

      // Modal laden
      window.openDetailModal = async (id) => {
        const res  = await fetch('/task_detail_modal.php?id=' + id);
        const html = await res.text();
        document.getElementById('detailContent').innerHTML = html;
        document.getElementById('detailModal').classList.remove('hidden');
        document.querySelector('[data-action="close-modal"]')
                ?.addEventListener('click', ()=> document.getElementById('detailModal').classList.add('hidden'));
      };
      // Klick außerhalb schließt Modal
      document.getElementById('detailModal')
              .addEventListener('click', e => { if(e.target.id==='detailModal') e.target.classList.add('hidden'); });
    });
  </script>
</body>
</html>