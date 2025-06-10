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
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }

    /* Search container styling */
    .search-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    /* Filter dropdown styling */
    .filter-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }
    .filter-container:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .filter-dropdown {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .filter-item {
      color: rgba(255, 255, 255, 0.9);
      transition: all 0.3s ease;
    }
    .filter-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }
    .filter-item.active {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    /* Task card styling */
    .task-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .task-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    /* Task content styling */
    .task-title {
      color: white;
      font-weight: 600;
      font-size: 1rem;
    }
    .task-description {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.875rem;
    }
    .task-meta {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.75rem;
    }
    .task-meta .font-medium {
      color: rgba(255, 255, 255, 0.8);
    }

    /* Avatar styling */
    .task-avatar {
      width: 2.5rem;
      height: 2.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 0.875rem;
      border: 2px solid rgba(255,255,255,0.3);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Status badges */
    .status-overdue {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .group-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
    }

    /* Action button styling */
    .action-button {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.8) 0%, rgba(21, 128, 61, 0.8) 100%);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      font-size: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .action-button:hover {
      background: linear-gradient(135deg, rgba(34, 197, 94, 1) 0%, rgba(21, 128, 61, 1) 100%);
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }

    /* Search input styling */
    .search-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      transition: all 0.3s ease;
    }
    .search-input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    .search-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }

    /* No results styling */
    .no-results {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      color: rgba(255, 255, 255, 0.6);
      text-align: center;
      padding: 3rem;
    }

    /* Header styling */
    .page-header {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>
<body class="min-h-screen flex">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header mit Suche und Filter -->
      <div class="flex flex-col gap-6">
        <h1 class="text-3xl font-bold page-header">Inbox</h1>
        
        <!-- Search and Filter Container -->
        <div class="search-container p-6">
          <!-- Search Bar -->
          <div class="relative mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="searchInput" placeholder="Aufgaben durchsuchen..." 
                   class="w-full search-input">
          </div>

          <!-- Filter Buttons -->
          <div class="flex flex-wrap gap-3">
            <!-- Benutzer-Filter -->
            <div class="relative z-20">
              <button id="userFilterBtn" class="filter-container flex items-center px-4 py-2 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="mr-2">
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
              <div id="userFilterMenu" class="absolute right-0 mt-2 w-60 filter-dropdown hidden z-20 max-h-80 overflow-y-auto p-2">
                <a href="/inbox.php?assigned_to=all&group_id=<?= $filterGroupId ?>"
                   class="filter-item block px-4 py-2 rounded-md <?= $filterAssignedTo==='all'?'active':'' ?>">
                  Alle Benutzer
                </a>
                <div class="border-t border-white/10 my-2"></div>
                <?php foreach($users as $u): ?>
                  <a href="/inbox.php?assigned_to=<?= $u['id'] ?>&group_id=<?= $filterGroupId ?>"
                     class="filter-item block px-4 py-2 rounded-md <?= ((string)$filterAssignedTo)===(string)$u['id']?'active':'' ?>">
                    <?= htmlspecialchars($u['username']) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Gruppen-Filter -->
            <div class="relative z-20">
              <button id="groupFilterBtn" class="filter-container flex items-center px-4 py-2 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="mr-2">
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
              <div id="groupFilterMenu" class="absolute right-0 mt-2 w-60 filter-dropdown hidden z-20 max-h-80 overflow-y-auto p-2">
                <a href="/inbox.php?assigned_to=<?= $filterAssignedTo ?>&group_id=all"
                   class="filter-item block px-4 py-2 rounded-md <?= $filterGroupId==='all'?'active':'' ?>">
                  Alle Gruppen
                </a>
                <div class="border-t border-white/10 my-2"></div>
                <?php if (empty($userGroups)): ?>
                  <span class="block px-4 py-2 text-white/50">Keine Gruppen gefunden</span>
                <?php else: ?>
                  <?php foreach($userGroups as $g): ?>
                    <a href="/inbox.php?assigned_to=<?= $filterAssignedTo ?>&group_id=<?= $g['id'] ?>"
                       class="filter-item block px-4 py-2 rounded-md <?= ((string)$filterGroupId)===(string)$g['id']?'active':'' ?>">
                      <?= htmlspecialchars($g['name']) ?>
                    </a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Aufgabenliste -->
      <div id="tasksContainer">
        <?php if(empty($tasks)): ?>
          <div class="no-results">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <h3 class="text-lg font-medium mb-2">Keine Aufgaben gefunden</h3>
            <p>Versuchen Sie andere Filter oder erstellen Sie eine neue Aufgabe.</p>
          </div>
        <?php else: ?>
          <ul class="space-y-4" id="tasksList">
            <?php foreach($tasks as $t): ?>
              <li class="task-card p-6 task-item" 
                  data-title="<?= htmlspecialchars($t['title'] ?? '') ?>"
                  data-description="<?= htmlspecialchars($t['description'] ?? '') ?>"
                  data-creator="<?= htmlspecialchars($t['creator_name'] ?? '') ?>"
                  data-assignee="<?= htmlspecialchars($t['assignee_name'] ?? '') ?>"
                  onclick="openDetailModal(<?= $t['id'] ?>)">
                <div class="flex items-start gap-4">
                  <div class="task-avatar">
                    <?= htmlspecialchars(substr($t['creator_name'] ?? '', 0, 2)) ?>
                  </div>
                  <div class="flex-1">
                    <div class="flex justify-between items-start mb-3">
                      <h2 class="task-title"><?= htmlspecialchars($t['title'] ?? '') ?></h2>
                      <?php if(isset($t['due_date']) && strtotime($t['due_date']) < time()): ?>
                        <span class="status-overdue px-2 py-1 rounded-full text-xs font-medium">Überfällig</span>
                      <?php endif; ?>
                    </div>
                    
                    <?php if(!empty($t['description'])): ?>
                      <p class="task-description mb-3 line-clamp-2"><?= htmlspecialchars($t['description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="flex flex-wrap gap-4 task-meta">
                      <span>
                        <span class="font-medium">Von:</span> 
                        <?= htmlspecialchars($t['creator_name'] ?? 'Unbekannt') ?>
                      </span>
                      <span>
                        <span class="font-medium">Für:</span> 
                        <?php if ($t['assigned_group_id']): ?>
                          <span class="group-badge px-2 py-1 rounded-full text-xs ml-1">
                            <?= htmlspecialchars($t['group_name'] ?? 'Unbekannt') ?>
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
                    </div>
                  </div>
                  
                  <!-- Done button -->
                  <form method="get" class="flex-shrink-0" onclick="event.stopPropagation();">
                    <input type="hidden" name="done" value="<?= $t['id'] ?>">
                    <input type="hidden" name="assigned_to" value="<?= htmlspecialchars($filterAssignedTo) ?>">
                    <input type="hidden" name="group_id" value="<?= htmlspecialchars($filterGroupId) ?>">
                    <button type="submit" class="action-button">
                      Erledigt
                    </button>
                  </form>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Detail-Modal -->
  <div id="detailModal" class="fixed inset-0 hidden bg-black/50 flex items-center justify-center z-50">
    <div id="detailContent" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Search functionality
      const searchInput = document.getElementById('searchInput');
      const tasksList = document.getElementById('tasksList');
      const tasksContainer = document.getElementById('tasksContainer');
      
      if (searchInput && tasksList) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase().trim();
          const taskItems = tasksList.querySelectorAll('.task-item');
          let visibleCount = 0;
          
          taskItems.forEach(item => {
            const title = item.dataset.title.toLowerCase();
            const description = item.dataset.description.toLowerCase();
            const creator = item.dataset.creator.toLowerCase();
            const assignee = item.dataset.assignee.toLowerCase();
            
            const matches = title.includes(searchTerm) || 
                          description.includes(searchTerm) || 
                          creator.includes(searchTerm) || 
                          assignee.includes(searchTerm);
            
            if (matches || searchTerm === '') {
              item.style.display = '';
              visibleCount++;
            } else {
              item.style.display = 'none';
            }
          });
          
          // Show no results message if nothing found
          if (visibleCount === 0 && searchTerm !== '') {
            if (!document.getElementById('noSearchResults')) {
              const noResults = document.createElement('div');
              noResults.id = 'noSearchResults';
              noResults.className = 'no-results';
              noResults.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="text-lg font-medium mb-2">Keine Ergebnisse gefunden</h3>
                <p>Keine Aufgaben entsprechen Ihrer Suche nach "<strong>${searchTerm}</strong>".</p>
              `;
              tasksContainer.appendChild(noResults);
            }
            tasksList.style.display = 'none';
          } else {
            const noResults = document.getElementById('noSearchResults');
            if (noResults) {
              noResults.remove();
            }
            tasksList.style.display = '';
          }
        });
      }

      // Filter dropdown functionality
      const userBtn = document.getElementById('userFilterBtn');
      const userMenu = document.getElementById('userFilterMenu');
      const groupBtn = document.getElementById('groupFilterBtn');
      const groupMenu = document.getElementById('groupFilterMenu');
      
      if (userBtn && userMenu) {
        userBtn.addEventListener('click', e => {
          e.stopPropagation();
          userMenu.classList.toggle('hidden');
          if (groupMenu) groupMenu.classList.add('hidden');
        });
      }
      
      if (groupBtn && groupMenu) {
        groupBtn.addEventListener('click', e => {
          e.stopPropagation();
          groupMenu.classList.toggle('hidden');
          if (userMenu) userMenu.classList.add('hidden');
        });
      }
      
      document.addEventListener('click', () => {
        if (userMenu) userMenu.classList.add('hidden');
        if (groupMenu) groupMenu.classList.add('hidden');
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
      document.getElementById('detailModal').addEventListener('click', e => {
        if (e.target.id === 'detailModal') {
          e.target.classList.add('hidden');
        }
      });
    });
  </script>
</body>
</html>