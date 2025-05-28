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
    body { font-family: 'Inter', sans-serif; }
    /* On mobile, add a top margin to main to push content below the fixed mobile navbar */
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">

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

      <!-- HaveToPay Widget -->
      <?php include __DIR__.'/widgets/havetopay_widget.php'; ?>

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
