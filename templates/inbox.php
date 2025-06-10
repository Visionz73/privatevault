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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
    
    /* Modern search container */
    .search-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Filter dropdowns */
    .filter-dropdown {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      color: white;
      transition: all 0.3s ease;
      position: relative;
      z-index: 30;
    }
    .filter-dropdown:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
    }
    
    .filter-menu {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
      z-index: 40;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
    }
    .filter-menu.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .filter-menu a {
      color: rgba(255, 255, 255, 0.9);
      transition: all 0.2s ease;
    }
    .filter-menu a:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }
    .filter-menu a.active {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }
    
    /* Task cards */
    .task-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .task-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }
    
    /* Task content styling */
    .task-title {
      color: white;
      font-weight: 600;
    }
    .task-description {
      color: rgba(255, 255, 255, 0.7);
    }
    .task-meta {
      color: rgba(255, 255, 255, 0.6);
    }
    .task-meta .font-medium {
      color: rgba(255, 255, 255, 0.8);
    }
    
    /* Badges */
    .group-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
    }
    .overdue-badge {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    
    /* Search input */
    .search-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.75rem;
      color: white;
      transition: all 0.3s ease;
    }
    .search-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    .search-input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    
    /* Header styling */
    .header-text {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    
    /* User avatar */
    .user-avatar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      color: white;
      font-weight: 700;
      border: 2px solid rgba(255,255,255,0.3);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Done button */
    .done-button {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.8) 0%, rgba(21, 128, 61, 0.8) 100%);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }
    .done-button:hover {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.9) 0%, rgba(21, 128, 61, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }
    
    /* No tasks message */
    .no-tasks {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      color: rgba(255, 255, 255, 0.6);
    }
  </style>
</head>
<body class="min-h-screen flex">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header mit Suche -->
      <div class="flex flex-col gap-4">
        <h1 class="text-3xl font-bold header-text">Inbox</h1>
        
        <!-- Suchcontainer -->
        <div class="search-container p-4">
          <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
            <!-- Suchfeld -->
            <div class="flex-1 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input 
                type="text" 
                id="taskSearch" 
                placeholder="Aufgaben durchsuchen..." 
                class="search-input w-full pl-10 pr-4 py-3"
              >
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex gap-3">
              <!-- Benutzer-Filter -->
              <div class="relative">
                <button id="userFilterBtn" class="filter-dropdown px-4 py-3 flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  <span>
                    <?php if ($filterAssignedTo === 'all'): ?>
                      Alle Benutzer
                    <?php else: ?>
                      <?= htmlspecialchars($usersMap[$filterAssignedTo] ?? 'Unbekannt') ?>
                    <?php endif; ?>
                  </span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div id="userFilterMenu" class="filter-menu absolute right-0 mt-2 w-60 p-2 max-h-80 overflow-y-auto">
                  <a href="/inbox.php?assigned_to=all&group_id=<?= $filterGroupId ?>"
                     class="block px-4 py-2 rounded-lg text-sm <?= $filterAssignedTo==='all'?'active':'' ?>">
                    Alle Benutzer
                  </a>
                  <div class="border-t border-white/10 my-2"></div>
                  <?php foreach($users as $u): ?>
                    <a href="/inbox.php?assigned_to=<?= $u['id'] ?>&group_id=<?= $filterGroupId ?>"
                       class="block px-4 py-2 rounded-lg text-sm <?= ((string)$filterAssignedTo)===(string)$u['id']?'active':'' ?>">
                      <?= htmlspecialchars($u['username']) ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
              
              <!-- Gruppen-Filter -->
              <div class="relative">
                <button id="groupFilterBtn" class="filter-dropdown px-4 py-3 flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <span>
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
                      <?= htmlspecialchars($groupName) ?>
                    <?php endif; ?>
                  </span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div id="groupFilterMenu" class="filter-menu absolute right-0 mt-2 w-60 p-2 max-h-80 overflow-y-auto">
                  <a href="/inbox.php?assigned_to=<?= $filterAssignedTo ?>&group_id=all"
                     class="block px-4 py-2 rounded-lg text-sm <?= $filterGroupId==='all'?'active':'' ?>">
                    Alle Gruppen
                  </a>
                  <div class="border-t border-white/10 my-2"></div>
                  <?php if (empty($userGroups)): ?>
                    <span class="block px-4 py-2 text-sm text-white/50">Keine Gruppen gefunden</span>
                  <?php else: ?>
                    <?php foreach($userGroups as $g): ?>
                      <a href="/inbox.php?assigned_to=<?= $filterAssignedTo ?>&group_id=<?= $g['id'] ?>"
                         class="block px-4 py-2 rounded-lg text-sm <?= ((string)$filterGroupId)===(string)$g['id']?'active':'' ?>">
                        <?= htmlspecialchars($g['name']) ?>
                      </a>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Aufgabenliste -->
      <?php if(empty($tasks)): ?>
        <div class="no-tasks p-8 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-lg">Keine Aufgaben gefunden.</p>
          <p class="text-sm mt-2">Probieren Sie andere Filter oder erstellen Sie eine neue Aufgabe.</p>
        </div>
      <?php else: ?>
        <div id="taskList" class="space-y-4">
          <?php foreach($tasks as $t): ?>
            <div class="task-card p-5 relative task-item" 
                 data-title="<?= htmlspecialchars($t['title'] ?? '') ?>"
                 data-description="<?= htmlspecialchars($t['description'] ?? '') ?>"
                 data-creator="<?= htmlspecialchars($t['creator_name'] ?? '') ?>"
                 data-assignee="<?= htmlspecialchars($t['assignee_name'] ?? '') ?>">
              <div class="flex items-start gap-4">
                <!-- User Avatar -->
                <div class="user-avatar h-12 w-12 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0">
                  <?= htmlspecialchars(substr($t['creator_name'] ?? '', 0, 2)) ?>
                </div>
                
                <!-- Task Content -->
                <div class="flex-1 cursor-pointer" onclick="openDetailModal(<?= $t['id'] ?>)">
                  <h2 class="task-title text-lg font-medium mb-2"><?= htmlspecialchars($t['title'] ?? '') ?></h2>
                  
                  <?php if(!empty($t['description'])): ?>
                    <p class="task-description text-sm mb-3 line-clamp-2"><?= htmlspecialchars($t['description']) ?></p>
                  <?php endif; ?>
                  
                  <div class="flex flex-wrap gap-4 text-sm task-meta">
                    <span class="flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      <span class="font-medium">Von:</span> 
                      <?= htmlspecialchars($t['creator_name'] ?? 'Unbekannt') ?>
                    </span>
                    
                    <span class="flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                      <span class="font-medium">Für:</span> 
                      <?php if ($t['assigned_group_id']): ?>
                        <span class="group-badge px-2 py-1 rounded-full text-xs font-medium">
                          Gruppe: <?= htmlspecialchars($t['group_name'] ?? 'Unbekannt') ?>
                        </span>
                      <?php else: ?>
                        <?= htmlspecialchars($t['assignee_name'] ?? 'Nicht zugewiesen') ?>
                      <?php endif; ?>
                    </span>
                    
                    <?php if(!empty($t['due_date'])): ?>
                      <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">Fällig:</span>
                        <?= date('d.m.Y', strtotime($t['due_date'])) ?>
                      </span>
                    <?php endif; ?>
                    
                    <?php if(isset($t['due_date']) && strtotime($t['due_date']) < time()): ?>
                      <span class="overdue-badge px-2 py-1 rounded-full text-xs font-medium">Überfällig</span>
                    <?php endif; ?>
                  </div>
                </div>
                
                <!-- Done button -->
                <form method="get" class="flex-shrink-0">
                  <input type="hidden" name="done" value="<?= $t['id'] ?>">
                  <input type="hidden" name="assigned_to" value="<?= htmlspecialchars($filterAssignedTo) ?>">
                  <input type="hidden" name="group_id" value="<?= htmlspecialchars($filterGroupId) ?>">
                  <button type="submit" class="done-button px-4 py-2 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Erledigt
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </main>

  <!-- Detail-Modal -->
  <div id="detailModal" class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div id="detailContent" class="task-card w-full max-w-lg p-6 relative mx-4"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Search functionality
      const searchInput = document.getElementById('taskSearch');
      const taskItems = document.querySelectorAll('.task-item');
      
      searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        
        taskItems.forEach(item => {
          const title = item.dataset.title.toLowerCase();
          const description = item.dataset.description.toLowerCase();
          const creator = item.dataset.creator.toLowerCase();
          const assignee = item.dataset.assignee.toLowerCase();
          
          const matches = title.includes(searchTerm) || 
                         description.includes(searchTerm) || 
                         creator.includes(searchTerm) || 
                         assignee.includes(searchTerm);
          
          item.style.display = matches ? 'block' : 'none';
        });
      });
      
      // Filter dropdowns
      const userBtn = document.getElementById('userFilterBtn');
      const userMenu = document.getElementById('userFilterMenu');
      const groupBtn = document.getElementById('groupFilterBtn');
      const groupMenu = document.getElementById('groupFilterMenu');
      
      userBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userMenu.classList.toggle('show');
        groupMenu.classList.remove('show');
      });
      
      groupBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        groupMenu.classList.toggle('show');
        userMenu.classList.remove('show');
      });
      
      document.addEventListener('click', () => {
        userMenu.classList.remove('show');
        groupMenu.classList.remove('show');
      });

      // Modal functionality
      window.openDetailModal = async (id) => {
        try {
          const res = await fetch('/task_detail_modal.php?id=' + id);
          const html = await res.text();
          document.getElementById('detailContent').innerHTML = html;
          document.getElementById('detailModal').classList.remove('hidden');
          
          const closeBtn = document.querySelector('[data-action="close-modal"]');
          if (closeBtn) {
            closeBtn.addEventListener('click', () => {
              document.getElementById('detailModal').classList.add('hidden');
            });
          }
        } catch (error) {
          console.error('Error loading task details:', error);
        }
      };
      
      // Close modal on background click
      document.getElementById('detailModal').addEventListener('click', (e) => {
        if (e.target.id === 'detailModal') {
          e.target.classList.add('hidden');
        }
      });
    });
  </script>
</body>
</html>