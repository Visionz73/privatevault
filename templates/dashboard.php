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
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    /* On mobile, add a top margin to main to push content below the fixed mobile navbar */
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }

    /* Dark theme widget styling */
    .widget-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: white;
      transition: all 0.3s ease;
    }
    .widget-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    /* Widget headers */
    .widget-header {
      color: white;
      font-weight: 600;
      font-size: 1.125rem;
    }
    .widget-header a {
      color: white !important;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .widget-header a:hover {
      color: rgba(255, 255, 255, 0.8) !important;
    }
    .widget-header svg {
      color: rgba(255, 255, 255, 0.7);
    }

    /* Widget content */
    .widget-description {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.875rem;
    }

    /* List items in widgets */
    .widget-list-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .widget-list-item:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(3px);
    }
    .widget-list-item:nth-child(even) {
      background: rgba(255, 255, 255, 0.03);
    }
    .widget-list-item:nth-child(even):hover {
      background: rgba(255, 255, 255, 0.08);
    }

    /* Task list specific styling */
    .task-title {
      color: white;
      font-weight: 500;
    }
    .task-description {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.875rem;
    }
    .task-meta {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.75rem;
    }
    .task-meta .font-medium {
      color: rgba(255, 255, 255, 0.7);
    }

    /* Status badges */
    .status-overdue {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .status-due {
      background: rgba(156, 163, 175, 0.2);
      color: rgba(255, 255, 255, 0.6);
      border: 1px solid rgba(156, 163, 175, 0.3);
    }
    .group-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
    }

    /* Buttons and controls */
    .widget-button {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .widget-button:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
    }

    /* Dropdown menus */
    .dropdown-menu {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    .dropdown-item {
      color: rgba(255, 255, 255, 0.9);
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }
    .dropdown-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }
    .dropdown-item.active {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    /* Forms in widgets */
    .widget-form input, .widget-form select, .widget-form textarea {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      color: white;
      padding: 0.75rem;
    }
    .widget-form input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    .widget-form input:focus, .widget-form select:focus, .widget-form textarea:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      outline: none;
    }

    /* Modal dark theme */
    .modal-content {
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .modal-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Greeting text */
    .greeting-text {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    /* Placeholder widgets */
    .placeholder-widget {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      color: rgba(255, 255, 255, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      font-size: 0.875rem;
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
    <h1 class="text-3xl font-bold greeting-text leading-tight">
      <?= $formattedDate ?><br>
      Guten <?= date('H')<12?'Morgen':(date('H')<18?'Tag':'Abend') ?>,
      <?= htmlspecialchars($user['first_name']??$user['username']) ?>
    </h1>

    <!-- Grid ------------------------------------------------------------->
    <div class="grid gap-8 auto-rows-min" style="grid-template-columns:repeat(auto-fill,minmax(340px,1fr));">

      <!-- Inbox Widget -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <a href="inbox.php" class="group inline-flex items-center widget-header">
            <h2 class="mr-1">Inbox</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <!-- Group Filter Dropdown -->
          <div class="relative">
            <button id="groupFilterBtn" class="widget-button text-sm flex items-center">
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
            <div id="groupFilterMenu" class="absolute right-0 mt-2 w-56 dropdown-menu hidden z-20">
              <a href="?filter=mine" class="block dropdown-item <?= $filterType==='mine' ? 'active' : '' ?>">
                Meine Aufgaben
              </a>
              <?php if (!empty($userGroups)): ?>
                <div class="border-t border-white/10 my-1"></div>
                <?php foreach($userGroups as $g): ?>
                  <a href="?filter=group&group_id=<?= $g['id'] ?>" 
                     class="block dropdown-item <?= ($filterType==='group' && $filterGroupId==$g['id']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($g['name']) ?>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <p class="widget-description mb-4"><?= $openTaskCount ?> abschließende Elemente</p>

        <ul class="flex-1 overflow-y-auto text-sm space-y-2">
          <?php if (!empty($tasks)): ?>
            <?php foreach($tasks as $idx => $t): ?>
              <li class="widget-list-item flex flex-col gap-2"
                  onclick="window.location.href='task_detail.php?id=<?= $t['id'] ?>'">
                <!-- Title and Due Date -->
                <div class="flex justify-between items-center">
                  <span class="task-title"><?= htmlspecialchars($t['title']) ?></span>
                  <?php if(isset($t['due_date']) && $t['due_date']): $over = strtotime($t['due_date']) < time(); ?>
                    <span class="<?= $over ? 'status-overdue' : 'status-due' ?> px-2 py-0.5 rounded-full text-xs whitespace-nowrap">
                      <?= $over ? 'Überfällig' : date('d.m.', strtotime($t['due_date'])) ?>
                    </span>
                  <?php endif; ?>
                </div>
                
                <!-- Description (short) -->
                <?php if(!empty($t['description'])): ?>
                  <p class="task-description line-clamp-1"><?= htmlspecialchars($t['description']) ?></p>
                <?php endif; ?>
                
                <!-- Creator and Assignee Info -->
                <div class="flex gap-4 task-meta">
                  <span>
                    <span class="font-medium">Von:</span> 
                    <?= htmlspecialchars($t['creator_name'] ?? 'Unbekannt') ?>
                  </span>
                  <span>
                    <span class="font-medium">Für:</span> 
                    <?php if ($t['assigned_group_id']): ?>
                      <span class="group-badge px-1 py-0.5 rounded-full">
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
            <li class="widget-list-item text-center task-meta">Keine offenen Aufgaben.</li>
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
      <article class="widget-card p-6 flex flex-col">
        <a href="profile.php?tab=documents" class="group inline-flex items-center mb-4 widget-header">
          <h2 class="mr-1">Dokumente</h2>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
        <p class="widget-description mb-4"><?= $docCount ?> Dateien</p>

        <ul class="flex-1 overflow-y-auto text-sm space-y-2">
          <?php if(!empty($docs)): ?>
            <?php foreach($docs as $idx=>$d): ?>
              <li class="widget-list-item">
                <span class="truncate block task-title"><?= htmlspecialchars($d['title'] ?? '') ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="widget-list-item text-center task-meta">Keine Dokumente vorhanden.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Meine Termine Widget – Updated for consistent formatting -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <a href="calendar.php" class="inline-flex items-center widget-header">
            Meine Termine
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
          <button id="showInlineEventForm" class="widget-button">
            +
          </button>
        </div>
        <p class="widget-description mb-4"><?= count($events) ?> Termine</p>
        <!-- Inline event creation form (initially hidden) -->
        <div id="inlineEventFormContainer" class="mb-4 hidden">
          <form id="inlineEventForm" class="space-y-2 widget-form">
            <input type="text" name="title" placeholder="Event Titel" required>
            <input type="date" name="date" required>
            <button type="submit" class="w-full widget-button">
              Termin erstellen
            </button>
          </form>
        </div>
        <ul id="dashboardEventList" class="flex-1 overflow-y-auto text-sm space-y-2">
          <?php if(!empty($events)): ?>
            <?php foreach($events as $evt): ?>
              <li class="widget-list-item flex justify-between items-center">
                <a href="calendar.php" class="truncate pr-2 flex-1 task-title"><?= htmlspecialchars($evt['title']) ?></a>
                <span class="task-meta text-xs"><?= date('d.m.Y', strtotime($evt['event_date'])) ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="widget-list-item text-center task-meta">Keine Termine gefunden.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Placeholder Cards --------------------------------------------->
      <?php foreach(['Recruiting','Abwesenheit','Org-Chart','Events'] as $name): ?>
        <article class="placeholder-widget">
          <?= $name ?>-Widget
        </article>
      <?php endforeach; ?>

      <!-- HaveToPay Widget -->
      <?php include __DIR__.'/widgets/havetopay_widget.php'; ?>
    </div><!-- /grid -->
  </main>
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Group filter dropdown
      const groupFilterBtn = document.getElementById('groupFilterBtn');
      const groupFilterMenu = document.getElementById('groupFilterMenu');
      
      if (groupFilterBtn && groupFilterMenu) {
        groupFilterBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          groupFilterMenu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', () => {
          groupFilterMenu.classList.add('hidden');
        });
      }
    });
    
    // Toggle inline event creation form
    document.getElementById('showInlineEventForm').addEventListener('click', function() {
      document.getElementById('inlineEventFormContainer').classList.toggle('hidden');
    });

    // Handle inline event form submission via AJAX
    document.getElementById('inlineEventForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const title = this.title.value.trim();
      const date = this.date.value;
      if(title && date){
        fetch('/create_event.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ title: title, date: date })
        })
        .then(response => response.json())
        .then(data => {
          if(data.success){
            // Create new list element for the event
            const newEvent = data.event;
            const li = document.createElement('li');
            li.className = "px-2 py-2 flex justify-between items-center";
            li.innerHTML = `<a href="calendar.php" class="truncate pr-2 flex-1">${newEvent.title}</a>
                             <span class="text-gray-400 text-xs">${new Date(newEvent.date).toLocaleDateString('de-DE')}</span>`;
            const eventList = document.getElementById('dashboardEventList');
            
            // If "Keine Termine gefunden." is present, remove it.
            if(eventList.childElementCount === 1 && eventList.firstElementChild.textContent.includes('Keine Termine')) {
              eventList.innerHTML = '';
            }
            eventList.appendChild(li);
            // Update count (force a reload or recalc count)
            // For simplicity, not auto-updating count here.
            this.reset();
            document.getElementById('inlineEventFormContainer').classList.add('hidden');
          } else {
            alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(() => alert('Fehler beim Erstellen des Termins.'));
      }
    });

    // Attach click events to task list items for mobile tap
    document.querySelectorAll('.task-item').forEach(item => {
      item.addEventListener('click', () => {
        const title = item.getAttribute('data-title');
        const due = item.getAttribute('data-due');
        document.getElementById('modalTaskTitle').textContent = title;
        document.getElementById('modalTaskDue').textContent = due ? 'Fällig: ' + due : '';
        document.getElementById('taskModal').classList.remove('hidden');
      });
    });

    // Close modal when clicking the close button
    document.getElementById('closeTaskModal').addEventListener('click', () => {
      document.getElementById('taskModal').classList.add('hidden');
    });

    // Optional: close modal when clicking on the background
    document.getElementById('taskModal').addEventListener('click', (e) => {
      if(e.target === document.getElementById('taskModal')){
        document.getElementById('taskModal').classList.add('hidden');
      }
    });

    // Subtask functionality
    document.querySelectorAll('.add-subtask-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const taskId = this.getAttribute('data-task-id');
        document.getElementById('modalTaskId').value = taskId;
        document.getElementById('subtaskModal').classList.remove('hidden');

        // Optional: Load existing subtasks via AJAX
        fetch('/get_subtasks.php?task_id=' + taskId)
          .then(response => response.json())
          .then(data => {
            const subtasksList = document.getElementById('subtasksList');
            subtasksList.innerHTML = ''; // Clear existing list
            data.subtasks.forEach(subtask => {
              const div = document.createElement('div');
              div.className = "flex justify-between items-center p-2 bg-gray-50 rounded-lg";
              div.innerHTML = `<span class="text-sm">${subtask.title}</span>
                               <button class="text-red-500 text-xs remove-subtask-btn" data-subtask-id="${subtask.id}">&times;</button>`;
              subtasksList.appendChild(div);
            });
          });
      });
    });

    document.querySelectorAll('.add-subtask-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const taskId = btn.getAttribute('data-task-id');
        document.getElementById('modalTaskId').value = taskId;
        const taskTitle = btn.closest('li').querySelector('.task-item span').textContent;
        document.getElementById('modalTaskTitle').textContent = 'Unteraufgaben für: ' + taskTitle;
        document.getElementById('subtaskModal').classList.remove('hidden');
      });
    });

    // Close subtask modal
    document.getElementById('closeSubtaskModal').addEventListener('click', () => {
      document.getElementById('subtaskModal').classList.add('hidden');
    });

    // Handle subtask form submission
    document.getElementById('subtaskForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const taskId = this.task_id.value;
      const subtaskTitle = this.subtask_title.value.trim();
      if(subtaskTitle){
        fetch('/add_subtask.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ task_id: taskId, subtask_title: subtaskTitle })
        })
        .then(response => response.json())
        .then(data => {
          if(data.success){
            // Add new subtask to the list
            const newSubtask = data.subtask;
            const div = document.createElement('div');
            div.className = "flex justify-between items-center p-2 bg-gray-50 rounded-lg";
            div.innerHTML = `<span class="text-sm">${newSubtask.title}</span>
                             <button class="text-red-500 text-xs remove-subtask-btn" data-subtask-id="${newSubtask.id}">&times;</button>`;
            document.getElementById('subtasksList').appendChild(div);
            this.reset();
          } else {
            alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(() => alert('Fehler beim Hinzufügen der Unteraufgabe.'));
      }
    });

    // Remove subtask
    document.getElementById('subtasksList').addEventListener('click', function(e) {
      if(e.target.classList.contains('remove-subtask-btn')){
        const subtaskId = e.target.getAttribute('data-subtask-id');
        fetch('/remove_subtask.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ subtask_id: subtaskId })
        })
        .then(response => response.json())
        .then(data => {
          if(data.success){
            e.target.closest('div').remove();
          } else {
            alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(() => alert('Fehler beim Entfernen der Unteraufgabe.'));
      }
    });
  </script>
</body>
</html>
