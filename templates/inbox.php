<?php
// Vom Controller bereitgestellt:
//   $tasks           — Array der Task-Datensätze
//   $users           — Array aller User (id, username)
//   $usersMap        — Map id ⇒ username
//   $filterAssignedTo— 'all' oder User-ID
//   $userGroups      — Array der Gruppen des Nutzers
//   $filterGroupId   — 'all' oder Group-ID
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aufgaben | Private Vault</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    body{font-family:'Inter',sans-serif}
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header mit Filter-Buttons -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-text">Inbox</h1>
        <div class="flex flex-wrap gap-3">
          <!-- Benutzer-Filter -->
          <div class="relative z-20">
            <button id="userFilterBtn"
                    class="flex items-center px-4 py-2 bg-white border border-border rounded-lg shadow hover:bg-gray-50 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <?php if ($filterAssignedTo === 'all'): ?>
                Alle Benutzer
              <?php else: ?>
                Benutzer: <?= htmlspecialchars($usersMap[$filterAssignedTo] ?? 'Unbekannt') ?>
              <?php endif; ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div id="userFilterMenu" class="absolute right-0 mt-2 w-60 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-20 max-h-80 overflow-y-auto">
              <a href="/inbox.php?assigned_to=all&group_id=<?= $filterGroupId ?>"
                 class="block px-4 py-2 hover:bg-gray-100 <?= $filterAssignedTo==='all'?'bg-gray-100':'' ?>">
                Alle Benutzer
              </a>
              <div class="border-t border-gray-200 my-1"></div>
              <?php foreach($users as $u): ?>
                <a href="/inbox.php?assigned_to=<?= $u['id'] ?>&group_id=<?= $filterGroupId ?>"
                   class="block px-4 py-2 hover:bg-gray-100 <?= ((string)$filterAssignedTo)===(string)$u['id']?'bg-gray-100':'' ?>">
                  <?= htmlspecialchars($u['username']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
          
          <!-- Gruppen-Filter -->
          <div class="relative z-20">
            <button id="groupFilterBtn"
                    class="flex items-center px-4 py-2 bg-white border border-border rounded-lg shadow hover:bg-gray-50 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <?php if ($filterGroupId === 'all'): ?>
                Alle Gruppen
              <?php else: ?>
                <?php 
                $groupName = "Unbekannt";
                foreach ($userGroups as $g) {
                  if ($g['id'] == $filterGroupId) {
                    $groupName = $g['name'];
                    break;
                  }
                }
                ?>
                Gruppe: <?= htmlspecialchars($groupName) ?>
              <?php endif; ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div id="groupFilterMenu" class="absolute right-0 mt-2 w-60 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-20 max-h-80 overflow-y-auto">
              <a href="/inbox.php?assigned_to=<?= $filterAssignedTo ?>&group_id=all"
                 class="block px-4 py-2 hover:bg-gray-100 <?= $filterGroupId==='all'?'bg-gray-100':'' ?>">
                Alle Gruppen
              </a>
              <div class="border-t border-gray-200 my-1"></div>
              <?php if (empty($userGroups)): ?>
                <span class="block px-4 py-2 text-gray-500">Keine Gruppen gefunden</span>
              <?php else: ?>
                <?php foreach($userGroups as $g): ?>
                  <a href="/inbox.php?assigned_to=<?= $filterAssignedTo ?>&group_id=<?= $g['id'] ?>"
                     class="block px-4 py-2 hover:bg-gray-100 <?= ((string)$filterGroupId)===(string)$g['id']?'bg-gray-100':'' ?>">
                    <?= htmlspecialchars($g['name']) ?>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
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
                  <input type="hidden" name="group_id" value="<?= htmlspecialchars($filterGroupId) ?>">
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
      // Benutzer-Filter-Dropdown toggeln
      const userBtn  = document.getElementById('userFilterBtn');
      const userMenu = document.getElementById('userFilterMenu');
      
      userBtn.addEventListener('click', e => {
        e.stopPropagation();
        userMenu.classList.toggle('hidden');
        // Hide group menu when user menu is shown
        document.getElementById('groupFilterMenu').classList.add('hidden');
      });
      
      // Gruppen-Filter-Dropdown toggeln
      const groupBtn = document.getElementById('groupFilterBtn');
      const groupMenu = document.getElementById('groupFilterMenu');
      
      groupBtn.addEventListener('click', e => {
        e.stopPropagation();
        groupMenu.classList.toggle('hidden');
        // Hide user menu when group menu is shown
        userMenu.classList.add('hidden');
      });
      
      document.addEventListener('click', () => {
        userMenu.classList.add('hidden');
        groupMenu.classList.add('hidden');
      });

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