<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/css/apple-ui.css">
  <style>
    /* On mobile, add a top margin to main to push content below the fixed mobile navbar */
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <?php require_once __DIR__.'/navbar.php'; ?>

  <!-- Use responsive margin: on small screens, remove left margin so content fills the screen -->
  <!-- Adjust main margin: on mobile use top margin to push content below the fixed top navbar; on desktop use left margin -->
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-8 space-y-10">

    <!-- Greeting --------------------------------------------------------->
    <?php
    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'de_DE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $formattedDate = $formatter->format(new DateTime());
    } else {
        $formattedDate = date('l, d. F'); // Fallback using date()
    }
    ?>
    <h1 class="text-3xl font-bold text-gray-900 leading-tight">
      <?= $formattedDate ?><br>
      Guten <?= date('H')<12?'Morgen':(date('H')<18?'Tag':'Abend') ?>,
      <?= htmlspecialchars($user['first_name']??$user['username']) ?>
    </h1>

    <!-- Grid ------------------------------------------------------------->
    <div class="grid gap-8 auto-rows-min" style="grid-template-columns:repeat(auto-fill,minmax(340px,1fr));">

      <!-- Inbox Widget -->
      <article class="glass-card flex flex-col overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-gray-200/30">
          <a href="inbox.php" class="group inline-flex items-center">
            <h2 class="text-lg font-semibold mr-1">Inbox</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <!-- Group Filter Dropdown -->
          <div class="relative">
            <button id="groupFilterBtn" class="text-sm text-gray-600 flex items-center glass-button">
              <?php if ($filterType === 'mine'): ?>
                Meine Aufgaben
              <?php else: ?>
                <?php 
                $groupName = "Gruppe";
                foreach ($userGroups as $g) {
                  if ($g['id'] == $filterGroupId) {
                    $groupName = $g['name'];
                    break;
                  }
                }
                ?>
                Gruppe: <?= htmlspecialchars($groupName) ?>
              <?php endif; ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div id="groupFilterMenu" class="absolute right-0 mt-2 w-56 glass-card hidden z-20">
              <a href="?filter=mine" class="block px-4 py-2 hover:bg-gray-100/70 <?= $filterType==='mine' ? 'bg-gray-100/70' : '' ?>">
                Meine Aufgaben
              </a>
              <?php if (!empty($userGroups)): ?>
                <div class="border-t border-gray-200/30 my-1"></div>
                <?php foreach($userGroups as $g): ?>
                  <a href="?filter=group&group_id=<?= $g['id'] ?>" 
                     class="block px-4 py-2 hover:bg-gray-100/70 <?= ($filterType==='group' && $filterGroupId==$g['id']) ? 'bg-gray-100/70' : '' ?>">
                    <?= htmlspecialchars($g['name']) ?>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <p class="text-sm text-gray-500 px-6 pt-4"><?= $openTaskCount ?> abschließende Elemente</p>

        <ul class="flex-1 overflow-y-auto text-sm divide-y divide-gray-100/20 px-6 py-4">
          <?php if (!empty($tasks)): ?>
            <?php foreach($tasks as $idx => $t): ?>
              <li class="px-2 py-3 <?= $idx %2 ? 'bg-gray-50/40' : 'bg-white/40' ?> rounded-lg my-1 flex flex-col gap-2 hover:bg-gray-100/50 cursor-pointer transition-all duration-200"
                  onclick="window.location.href='task_detail.php?id=<?= $t['id'] ?>'">
                <!-- Title and Due Date -->
                <div class="flex justify-between items-center">
                  <span class="font-medium"><?= htmlspecialchars($t['title']) ?></span>
                  <?php if(isset($t['due_date']) && $t['due_date']): $over = strtotime($t['due_date']) < time(); ?>
                    <span class="<?= $over ? 'bg-red-100 text-red-600' : 'text-gray-400' ?> px-2 py-0.5 rounded-full text-xs whitespace-nowrap">
                      <?= $over ? 'Überfällig' : date('d.m.', strtotime($t['due_date'])) ?>
                    </span>
                  <?php endif; ?>
                </div>
                
                <!-- Description (short) -->
                <?php if(!empty($t['description'])): ?>
                  <p class="text-sm text-gray-600 line-clamp-1"><?= htmlspecialchars($t['description']) ?></p>
                <?php endif; ?>
                
                <!-- Creator and Assignee Info -->
                <div class="flex gap-4 text-xs text-gray-500">
                  <span>
                    <span class="font-medium">Von:</span> 
                    <?= htmlspecialchars($t['creator_name'] ?? 'Unbekannt') ?>
                  </span>
                  <span>
                    <span class="font-medium">Für:</span> 
                    <?php if ($t['assigned_group_id']): ?>
                      <span class="bg-purple-100 text-purple-800 px-1 py-0.5 rounded-full">
                        Gruppe
                      </span>
                    <?php else: ?>
                      <?= htmlspecialchars($t['assignee_name'] ?? 'Nicht zugewiesen') ?>
                    <?php endif; ?>
                  </span>
                </div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="px-2 py-2 text-gray-500">Keine offenen Aufgaben.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- HaveToPay Widget -->
      <?php 
        // Include HaveToPay widget
        require_once __DIR__ . '/widgets/havetopay_widget.php';
      ?>

      <!-- Dokumente Widget -->
      <article class="glass-card flex flex-col overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-gray-200/30">
          <a href="profile.php?tab=documents" class="group inline-flex items-center">
            <h2 class="text-lg font-semibold mr-1">Dokumente</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          <a href="upload.php" class="glass-button flex items-center text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            Upload
          </a>
        </div>
        
        <div class="p-6">
          <p class="text-sm text-gray-600 mb-4">Kürzlich hochgeladene Dokumente</p>
          
          <div class="space-y-3">
            <?php for($i=0; $i<3; $i++): ?>
              <div class="flex items-center p-3 rounded-lg bg-white/40 hover:bg-white/60 transition-all">
                <div class="p-2 bg-blue-100 text-blue-600 rounded">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div class="ml-4 flex-1 truncate">
                  <h3 class="text-sm font-medium">Beispieldokument <?= $i+1 ?>.pdf</h3>
                  <p class="text-xs text-gray-500">Hochgeladen am <?= date('d.m.Y', time()-rand(0, 30)*86400) ?></p>
                </div>
                <a href="#" class="ml-2 text-gray-400 hover:text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                  </svg>
                </a>
              </div>
            <?php endfor; ?>
          </div>
          
          <a href="profile.php?tab=documents" class="mt-4 text-sm text-blue-600 hover:text-blue-800 hover:underline block text-center">
            Alle Dokumente anzeigen
          </a>
        </div>
      </article>
    </div><!-- /grid -->
  </main>

  <!-- Task Detail Modal -->
  <div id="taskModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="glass-card p-6 m-4 max-w-lg w-full">
      <div class="flex justify-between items-start mb-4">
        <h3 id="modalTitle" class="text-lg font-semibold"></h3>
        <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      <div id="modalContent" class="space-y-4">
        <p class="text-sm text-gray-600">
          <span class="font-medium">Ersteller:</span> <span id="modalCreator"></span>
        </p>
        <p class="text-sm text-gray-600">
          <span class="font-medium">Zugewiesen an:</span> <span id="modalAssignee"></span>
        </p>
      </div>
    </div>
  </div>

  <script>
    // Group Filter dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
      const groupFilterBtn = document.getElementById('groupFilterBtn');
      const groupFilterMenu = document.getElementById('groupFilterMenu');
      
      if (groupFilterBtn && groupFilterMenu) {
        groupFilterBtn.addEventListener('click', () => {
          groupFilterMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
          if (!e.target.closest('#groupFilterBtn') && !e.target.closest('#groupFilterMenu')) {
            groupFilterMenu.classList.add('hidden');
          }
        });
      }
      
      // Task modal functionality 
      document.querySelectorAll('[onclick*="task_detail.php"]').forEach(item => {
        const title = item.querySelector('.font-medium').textContent;
        const creator = item.querySelector('.font-medium + span').textContent.trim() || 'Unknown';
        const assignee = item.querySelector('.font-medium:nth-of-type(2) + span').textContent.trim() || 'Unassigned';
        
        item.addEventListener('click', (e) => {
          e.preventDefault();
          document.getElementById('modalTitle').textContent = title;
          document.getElementById('modalCreator').textContent = creator;
          document.getElementById('modalAssignee').textContent = assignee;
          document.getElementById('taskModal').classList.remove('hidden');
          document.getElementById('taskModal').classList.add('flex');
        });
      });
    });

    function closeTaskModal() {
      const modal = document.getElementById('taskModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    // Close on background click
    document.getElementById('taskModal').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) closeTaskModal();
    });
  </script>
</body>
</html>
