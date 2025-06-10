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
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- Header mit Suche und Filter -->
      <div class="flex flex-col gap-6">
        <h1 class="text-3xl font-bold page-header">Inbox</h1>
        
        <!-- Search and Filter Container -->
        <div class="search-container p-6">
          <!-- Search Bar -->
          <div class="relative mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="searchInput" placeholder="Aufgaben durchsuchen..." 
                   class="search-input w-full">
          </div>

          <!-- Filter Buttons -->
          <div class="flex flex-wrap gap-3">
            <!-- User Filter -->
            <div class="relative">
              <button id="userFilterBtn" class="filter-container text-sm flex items-center px-4 py-2">
                <span class="mr-2">Benutzer: Alle</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div id="userFilterMenu" class="absolute top-full left-0 mt-2 w-56 filter-dropdown hidden z-20">
                <a href="?assigned_to=all" class="block filter-item px-4 py-2">Alle Benutzer</a>
                <!-- Add more filter options -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Split Screen Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[calc(100vh-20rem)]">
        
        <!-- Left Side: Task List -->
        <div class="widget-card p-6 overflow-hidden flex flex-col">
          <h2 class="widget-header mb-4">Aufgaben</h2>
          
          <div id="tasksContainer" class="flex-1 overflow-y-auto">
            <?php if(empty($tasks)): ?>
              <div class="no-results">
                <p>Keine Aufgaben gefunden.</p>
              </div>
            <?php else: ?>
              <ul class="space-y-3" id="tasksList">
                <?php foreach($tasks as $task): ?>
                  <li class="task-item widget-list-item cursor-pointer" 
                      data-task-id="<?= $task['id'] ?>"
                      onclick="loadTaskDetail(<?= $task['id'] ?>)">
                    <div class="flex justify-between items-start mb-2">
                      <h3 class="task-title text-base font-medium"><?= htmlspecialchars($task['title']) ?></h3>
                      <?php if($task['due_date']): ?>
                        <?php $isOverdue = strtotime($task['due_date']) < time(); ?>
                        <span class="<?= $isOverdue ? 'status-overdue' : 'status-due' ?> px-2 py-1 rounded-full text-xs whitespace-nowrap">
                          <?= $isOverdue ? 'Überfällig' : date('d.m.', strtotime($task['due_date'])) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    
                    <?php if($task['description']): ?>
                      <p class="task-description mb-2 line-clamp-2"><?= htmlspecialchars($task['description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="flex justify-between items-center task-meta">
                      <span>
                        <span class="font-medium">Von:</span> 
                        <?= htmlspecialchars($task['creator_name'] ?? 'Unbekannt') ?>
                      </span>
                      
                      <?php if($task['assigned_group_id']): ?>
                        <span class="group-badge px-2 py-1 rounded-full text-xs">
                          <?= htmlspecialchars($task['group_name'] ?? 'Gruppe') ?>
                        </span>
                      <?php else: ?>
                        <span class="text-xs">
                          <?= htmlspecialchars($task['assignee_name'] ?? 'Nicht zugewiesen') ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>

        <!-- Right Side: Task Detail/Edit Form -->
        <div class="widget-card p-6 overflow-hidden flex flex-col">
          <div id="taskDetailContainer" class="flex-1 overflow-y-auto">
            <!-- Placeholder when no task is selected -->
            <div id="noTaskSelected" class="flex items-center justify-center h-full text-center">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-white/60">Wählen Sie eine Aufgabe aus, um Details anzuzeigen</p>
              </div>
            </div>
            
            <!-- Task detail content will be loaded here -->
            <div id="taskDetailContent" class="hidden">
              <!-- Content loaded via AJAX -->
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <script>
    let currentTaskId = null;

    document.addEventListener('DOMContentLoaded', () => {
      // Search functionality
      const searchInput = document.getElementById('searchInput');
      const tasksList = document.getElementById('tasksList');
      
      if (searchInput && tasksList) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase().trim();
          const taskItems = tasksList.querySelectorAll('.task-item');
          let visibleCount = 0;
          
          taskItems.forEach(item => {
            const title = item.querySelector('.task-title').textContent.toLowerCase();
            const description = item.querySelector('.task-description')?.textContent.toLowerCase() || '';
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
              item.style.display = 'block';
              visibleCount++;
            } else {
              item.style.display = 'none';
            }
          });
          
          // Show no results message if nothing found
          if (visibleCount === 0 && searchTerm !== '') {
            // Handle no results display
          }
        });
      }

      // Filter dropdown functionality
      const userBtn = document.getElementById('userFilterBtn');
      const userMenu = document.getElementById('userFilterMenu');
      
      if (userBtn && userMenu) {
        userBtn.addEventListener('click', e => {
          e.stopPropagation();
          userMenu.classList.toggle('hidden');
        });
      }
      
      document.addEventListener('click', () => {
        if (userMenu) userMenu.classList.add('hidden');
      });
    });

    // Load task detail function
    async function loadTaskDetail(taskId) {
      if (currentTaskId === taskId) return; // Already loaded
      
      currentTaskId = taskId;
      
      // Highlight selected task
      document.querySelectorAll('.task-item').forEach(item => {
        item.classList.remove('bg-white/20', 'border-white/30');
      });
      
      const selectedTask = document.querySelector(`[data-task-id="${taskId}"]`);
      if (selectedTask) {
        selectedTask.classList.add('bg-white/20', 'border-white/30');
      }
      
      // Show loading state
      const noTaskSelected = document.getElementById('noTaskSelected');
      const taskDetailContent = document.getElementById('taskDetailContent');
      
      noTaskSelected.classList.add('hidden');
      taskDetailContent.classList.remove('hidden');
      taskDetailContent.innerHTML = `
        <div class="flex items-center justify-center h-full">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-4"></div>
            <p class="text-white/60">Lade Aufgabendetails...</p>
          </div>
        </div>
      `;
      
      try {
        const response = await fetch(`/templates/task_modal.php?id=${taskId}`);
        const html = await response.text();
        
        taskDetailContent.innerHTML = html;
        
        // Initialize form event listeners
        const taskForm = document.getElementById('taskForm');
        if (taskForm) {
          taskForm.addEventListener('submit', handleTaskFormSubmit);
        }
        
        // Add close button handler
        const closeButtons = taskDetailContent.querySelectorAll('[data-action="close-modal"]');
        closeButtons.forEach(btn => {
          btn.addEventListener('click', () => {
            currentTaskId = null;
            taskDetailContent.classList.add('hidden');
            noTaskSelected.classList.remove('hidden');
            
            // Remove selection highlight
            document.querySelectorAll('.task-item').forEach(item => {
              item.classList.remove('bg-white/20', 'border-white/30');
            });
          });
        });
        
      } catch (error) {
        console.error('Error loading task details:', error);
        taskDetailContent.innerHTML = `
          <div class="text-center text-red-400">
            <p>Fehler beim Laden der Aufgabendetails</p>
            <button onclick="loadTaskDetail(${taskId})" class="mt-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20">
              Erneut versuchen
            </button>
          </div>
        `;
      }
    }

    async function handleTaskFormSubmit(event) {
      event.preventDefault();
      const formData = new FormData(event.target);
      
      try {
        const response = await fetch('/src/api/task_save.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Reload the page to show updated task list
          location.reload();
        } else {
          alert(result.error || 'Fehler beim Speichern der Aufgabe.');
        }
      } catch (error) {
        console.error('Error saving task:', error);
        alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
      }
    }
  </script>
</body>
</html>