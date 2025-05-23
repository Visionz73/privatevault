<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif; 
    }

    /* Custom gradient background - will be overridden by JS if custom colors are set */
    .custom-gradient-bg {
      background: linear-gradient(135deg, #f0f4ff 0%, #eef7ff 50%, #f9fdf2 100%);
    }
    
    /* Color picker tool styling */
    .gradient-customizer {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      padding: 15px;
      z-index: 1000;
      transition: all 0.3s ease;
      width: 320px;
      transform: translateY(calc(100% - 50px));
    }
    
    .gradient-customizer:hover,
    .gradient-customizer.active {
      transform: translateY(0);
    }
    
    .customizer-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      cursor: pointer;
    }
    
    .color-inputs {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
    }
    
    .color-input-group {
      flex: 1;
    }
    
    .color-input-group input[type="color"] {
      width: 100%;
      height: 40px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    
    .color-input-group label {
      display: block;
      font-size: 12px;
      margin-bottom: 5px;
      color: #666;
    }
    
    .customizer-actions {
      display: flex;
      justify-content: space-between;
    }
    
    .reset-btn {
      background: #f1f5f9;
      color: #334155;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    
    .save-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    /* On mobile, add a top margin to main to push content below the fixed mobile navbar */
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
      
      .gradient-customizer {
        bottom: 10px;
        right: 10px;
        left: 10px;
        width: auto;
      }
    }
  </style>
</head>
<body class="min-h-screen custom-gradient-bg flex flex-col">

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
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <a href="inbox.php" class="group inline-flex items-center">
            <h2 class="text-lg font-semibold mr-1">Inbox</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <!-- Group Filter Dropdown -->
          <div class="relative">
            <button id="groupFilterBtn" class="text-sm text-gray-600 flex items-center">
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
            <div id="groupFilterMenu" class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-20">
              <a href="?filter=mine" class="block px-4 py-2 hover:bg-gray-100 <?= $filterType==='mine' ? 'bg-gray-100' : '' ?>">
                Meine Aufgaben
              </a>
              <?php if (!empty($userGroups)): ?>
                <div class="border-t border-gray-200 my-1"></div>
                <?php foreach($userGroups as $g): ?>
                  <a href="?filter=group&group_id=<?= $g['id'] ?>" 
                     class="block px-4 py-2 hover:bg-gray-100 <?= ($filterType==='group' && $filterGroupId==$g['id']) ? 'bg-gray-100' : '' ?>">
                    <?= htmlspecialchars($g['name']) ?>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <p class="text-sm text-gray-500 mb-4"><?= $openTaskCount ?> abschließende Elemente</p>

        <ul class="flex-1 overflow-y-auto text-sm divide-y divide-gray-100">
          <?php if (!empty($tasks)): ?>
            <?php foreach($tasks as $idx => $t): ?>
              <li class="px-2 py-3 <?= $idx %2 ? 'bg-gray-50' : 'bg-white' ?> flex flex-col gap-2 hover:bg-gray-100 cursor-pointer"
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
                        Gruppe: <?= htmlspecialchars($t['group_name'] ?? 'Unbekannt') ?>
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

      <!-- Task Detail Modal -->
      <div id="taskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 m-4 max-w-lg w-full">
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

      <!-- Edit Task Modal -->
      <div id="editTaskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 m-4 max-w-lg w-full">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold">Aufgabe bearbeiten</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
          </div>
          <form id="editTaskForm" class="space-y-4">
            <input type="hidden" id="editTaskId" name="task_id">
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Ersteller</label>
              <select name="creator_id" id="editCreatorId" class="w-full px-3 py-2 border rounded-lg">
                <?php
                $users = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll();
                foreach($users as $user): ?>
                  <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Zugewiesen an</label>
              <select name="assignee_id" id="editAssigneeId" class="w-full px-3 py-2 border rounded-lg">
                <?php foreach($users as $user): ?>
                  <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="flex justify-end space-x-3">
              <button type="button" onclick="closeEditModal()" 
                      class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                Abbrechen
              </button>
              <button type="submit" 
                      class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Speichern
              </button>
            </div>
          </form>
        </div>
      </div>

      <script>
        document.querySelectorAll('.task-item').forEach(item => {
          item.addEventListener('click', () => {
            const modal = document.getElementById('taskModal');
            document.getElementById('modalTitle').textContent = item.dataset.title;
            document.getElementById('modalCreator').textContent = item.dataset.creator;
            document.getElementById('modalAssignee').textContent = item.dataset.assignee;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
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

        function openEditModal(taskId, title) {
          document.getElementById('editTaskId').value = taskId;
          document.getElementById('editTaskModal').classList.remove('hidden');
          document.getElementById('editTaskModal').classList.add('flex');
        }

        function closeEditModal() {
          document.getElementById('editTaskModal').classList.add('hidden');
          document.getElementById('editTaskModal').classList.remove('flex');
        }

        document.getElementById('editTaskForm').addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(this);
          
          fetch('/src/controllers/update_task.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            } else {
              alert('Fehler beim Aktualisieren der Aufgabe');
            }
          });
        });
      </script>

      <!-- Dokumente Widget -->
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex flex-col">
        <a href="profile.php?tab=documents" class="group inline-flex items-center mb-4">
          <h2 class="text-lg font-semibold mr-1">Dokumente</h2>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
        <p class="text-sm text-gray-500 mb-4"><?= $docCount ?> Dateien</p>

        <ul class="flex-1 overflow-y-auto text-sm divide-y divide-gray-100">
          <?php if(!empty($docs)): ?>
            <?php foreach($docs as $idx=>$d): ?>
              <li class="px-2 py-2 <?= $idx %2 ? 'bg-gray-50' : 'bg-white' ?>">
                <span class="truncate block"><?= htmlspecialchars($d['title'] ?? '') ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="px-2 py-2 text-gray-500">Keine Dokumente vorhanden.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Meine Termine Widget – Updated for consistent formatting -->
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <a href="calendar.php" class="inline-flex items-center text-lg font-semibold text-gray-900">
            Meine Termine
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
          <button id="showInlineEventForm" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg shadow-sm">
            +
          </button>
        </div>
        <p class="text-sm text-gray-500 mb-4"><?= count($events) ?> Termine</p>
        <!-- Inline event creation form (initially hidden) -->
        <div id="inlineEventFormContainer" class="mb-4 hidden">
          <form id="inlineEventForm" class="space-y-2">
            <input type="text" name="title" placeholder="Event Titel" class="w-full border border-gray-300 rounded p-2" required>
            <input type="date" name="date" class="w-full border border-gray-300 rounded p-2" required>
            <button type="submit" class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200">
              Termin erstellen
            </button>
          </form>
        </div>
        <ul id="dashboardEventList" class="flex-1 overflow-y-auto text-sm divide-y divide-gray-100">
          <?php if(!empty($events)): ?>
            <?php foreach($events as $evt): ?>
              <li class="px-2 py-2 flex justify-between items-center">
                <a href="calendar.php" class="truncate pr-2 flex-1"><?= htmlspecialchars($evt['title']) ?></a>
                <span class="text-gray-400 text-xs"><?= date('d.m.Y', strtotime($evt['event_date'])) ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="px-2 py-2 text-gray-500">Keine Termine gefunden.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Placeholder Cards --------------------------------------------->
      <?php foreach(['Recruiting','Abwesenheit','Org-Chart','Events'] as $name): ?>
        <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex items-center justify-center text-gray-400 text-sm">
          <?= $name ?>-Widget
        </article>
      <?php endforeach; ?>
    </div><!-- /grid -->
  </main>
  
  <!-- Gradient Customizer Tool -->
  <div class="gradient-customizer" id="gradientCustomizer">
    <div class="customizer-header" id="customizerHeader">
      <h3 class="font-semibold text-sm">Hintergrund anpassen</h3>
      <span class="toggle-icon">▲</span>
    </div>
    
    <div class="customizer-content">
      <div class="color-inputs">
        <div class="color-input-group">
          <label for="startColor">Start</label>
          <input type="color" id="startColor" value="#f0f4ff">
        </div>
        
        <div class="color-input-group">
          <label for="midColor">Mitte</label>
          <input type="color" id="midColor" value="#eef7ff">
        </div>
        
        <div class="color-input-group">
          <label for="endColor">Ende</label>
          <input type="color" id="endColor" value="#f9fdf2">
        </div>
      </div>
      
      <div class="customizer-actions">
        <button class="reset-btn" id="resetGradient">Zurücksetzen</button>
        <button class="save-btn" id="saveGradient">Speichern</button>
      </div>
    </div>
  </div>

  <script>
    // Gradient customizer functionality
    document.addEventListener('DOMContentLoaded', function() {
      const startColorInput = document.getElementById('startColor');
      const midColorInput = document.getElementById('midColor');
      const endColorInput = document.getElementById('endColor');
      const resetButton = document.getElementById('resetGradient');
      const saveButton = document.getElementById('saveGradient');
      const customizerHeader = document.getElementById('customizerHeader');
      const gradientCustomizer = document.getElementById('gradientCustomizer');
      
      // Default colors
      const defaultColors = {
        start: '#f0f4ff',
        mid: '#eef7ff',
        end: '#f9fdf2'
      };
      
      // Load saved colors from localStorage if available
      function loadSavedColors() {
        const savedColors = JSON.parse(localStorage.getItem('dashboardGradient') || '{}');
        
        if (savedColors.start && savedColors.mid && savedColors.end) {
          startColorInput.value = savedColors.start;
          midColorInput.value = savedColors.mid;
          endColorInput.value = savedColors.end;
          
          applyGradient(savedColors.start, savedColors.mid, savedColors.end);
        }
      }
      
      // Apply gradient to body
      function applyGradient(start, mid, end) {
        document.body.style.background = 
          `linear-gradient(135deg, ${start} 0%, ${mid} 50%, ${end} 100%)`;
      }
      
      // Update gradient on color input change
      [startColorInput, midColorInput, endColorInput].forEach(input => {
        input.addEventListener('input', () => {
          applyGradient(startColorInput.value, midColorInput.value, endColorInput.value);
        });
      });
      
      // Reset to default colors
      resetButton.addEventListener('click', () => {
        startColorInput.value = defaultColors.start;
        midColorInput.value = defaultColors.mid;
        endColorInput.value = defaultColors.end;
        
        applyGradient(defaultColors.start, defaultColors.mid, defaultColors.end);
        localStorage.removeItem('dashboardGradient');
      });
      
      // Save current gradient
      saveButton.addEventListener('click', () => {
        const colors = {
          start: startColorInput.value,
          mid: midColorInput.value,
          end: endColorInput.value
        };
        
        localStorage.setItem('dashboardGradient', JSON.stringify(colors));
        
        // Show brief confirmation
        saveButton.textContent = 'Gespeichert!';
        saveButton.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        
        setTimeout(() => {
          saveButton.textContent = 'Speichern';
          saveButton.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }, 2000);
      });
      
      // Toggle customizer panel
      customizerHeader.addEventListener('click', () => {
        gradientCustomizer.classList.toggle('active');
        const toggleIcon = customizerHeader.querySelector('.toggle-icon');
        toggleIcon.textContent = gradientCustomizer.classList.contains('active') ? '▼' : '▲';
      });
      
      // Load saved colors on page load
      loadSavedColors();
    });
  </script>
</body>
</html>
