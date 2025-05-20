<?php
// templates/taskboard.php
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/../src/lib/db.php';

// Status-Spalten und Labels
$statuses = [
  'todo'  => 'To&nbsp;Do',
  'doing' => 'In&nbsp;Bearbeitung',
  'done'  => 'Erledigt'
];

// Alle Tasks des Users laden
$stmt = $pdo->prepare('
  SELECT * 
    FROM tasks 
   WHERE created_by = ? 
ORDER BY created_at DESC
');
$stmt->execute([$_SESSION['user_id']]);
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nach Status gruppieren
$tasksByStatus = array_fill_keys(array_keys($statuses), []);
foreach ($all as $t) {
  $tasksByStatus[$t['status']][] = $t;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>TaskBoard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: Inter, sans-serif; }
    .task-column { min-height: 70vh; }
    .dragging { opacity: 0.5; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
      .columns-container { flex-direction: column; }
      .task-column { min-height: auto; margin-bottom: 1rem; width: 100% !important; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2]">
  <?php require_once __DIR__ . '/navbar.php'; ?>
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-6">
    
    <!-- Header with filter options -->
    <div class="flex flex-wrap justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Kanban Board</h1>
      
      <div class="flex space-x-2 mt-2 sm:mt-0">
        <!-- Filter Buttons -->
        <a href="?filter=all" class="px-4 py-2 rounded-lg text-sm font-medium <?= ($filterMode === 'all') ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' ?>">
          Alle Aufgaben
        </a>
        <a href="?filter=user" class="px-4 py-2 rounded-lg text-sm font-medium <?= ($filterMode === 'user') ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' ?>">
          Meine Aufgaben
        </a>
        
        <!-- Create Task Button -->
        <button onclick="openNewTaskModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium">
          + Neue Aufgabe
        </button>
      </div>
    </div>

    <!-- Columns Container -->
    <div class="flex space-x-4 columns-container overflow-x-auto pb-6">
      <?php foreach ($statuses as $key => $label): ?>
        <div class="task-column w-80 flex-shrink-0 bg-gray-50 rounded-xl shadow-sm p-3" data-status="<?= $key ?>">
          <div class="flex justify-between items-center p-2 mb-3">
            <h3 class="font-semibold text-gray-700"><?= $label ?> <span class="text-sm text-gray-500">(<?= count($tasksByStatus[$key]) ?>)</span></h3>
          </div>

          <!-- Task Container -->
          <div class="task-container min-h-[100px] space-y-3" ondragover="event.preventDefault()" ondrop="dropTask(event, '<?= $key ?>')">
            <?php foreach ($tasksByStatus[$key] as $task): ?>
              <div class="task-card bg-white rounded-lg shadow p-3 cursor-grab" 
                   draggable="true" 
                   ondragstart="dragStart(event, '<?= $task['id'] ?>', '<?= $key ?>')"
                   data-id="<?= $task['id'] ?>">
                
                <div class="flex justify-between items-start">
                  <h4 class="font-medium text-gray-800"><?= htmlspecialchars($task['title']) ?></h4>
                  
                  <!-- Due Date Badge -->
                  <?php if(!empty($task['due_date'])): 
                    $dueDate = strtotime($task['due_date']);
                    $isOverdue = $dueDate < time();
                  ?>
                    <span class="<?= $isOverdue ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' ?> px-2 py-0.5 rounded-full text-xs">
                      <?= date('d.m.Y', $dueDate) ?>
                    </span>
                  <?php endif; ?>
                </div>
                
                <?php if(!empty($task['description'])): ?>
                  <p class="mt-2 text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars($task['description']) ?></p>
                <?php endif; ?>
                
                <div class="mt-3 flex justify-between items-center">
                  <!-- Assignee info -->
                  <div class="text-xs text-gray-500">
                    <?php if(!empty($task['assignee_name'])): ?>
                      <span class="flex items-center">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 inline-flex items-center justify-center mr-1 text-[10px] font-bold">
                          <?= strtoupper(substr($task['assignee_name'], 0, 1)) ?>
                        </span>
                        <?= htmlspecialchars($task['assignee_name']) ?>
                      </span>
                    <?php else: ?>
                      <span>Nicht zugewiesen</span>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Edit button -->
                  <button onclick="event.stopPropagation(); openTaskModal(<?= $task['id'] ?>)" class="text-xs text-gray-500 hover:text-blue-500">
                    Details
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
            
            <?php if(count($tasksByStatus[$key]) === 0): ?>
              <div class="empty-placeholder text-center py-6 text-sm text-gray-400">
                Keine Aufgaben
              </div>
            <?php endif; ?>
            
            <!-- Add task button per column -->
            <button onclick="openNewTaskModal('<?= $key ?>')" class="w-full mt-2 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded text-center">
              + Aufgabe hinzufügen
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Task Modal (reuse existing) -->
  <div id="taskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div id="modalContent" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 m-4 max-h-[90vh] overflow-y-auto"></div>
  </div>

  <script>
    // Drag and Drop Functionality
    let draggedTaskId = null;
    let sourceStatus = null;

    function dragStart(event, taskId, status) {
        draggedTaskId = taskId;
        sourceStatus = status;
        event.target.classList.add('dragging');
        // Set data required for Firefox
        event.dataTransfer.setData('text/plain', taskId);
        event.dataTransfer.effectAllowed = 'move';
    }

    function dropTask(event, targetStatus) {
        event.preventDefault();
        const taskId = draggedTaskId || event.dataTransfer.getData('text/plain');
        
        if (!taskId) return;
        
        const taskCard = document.querySelector(`.task-card[data-id="${taskId}"]`);
        if (taskCard) {
            taskCard.classList.remove('dragging');
            
            if (sourceStatus !== targetStatus) {
                // Move in DOM
                const targetContainer = document.querySelector(`.task-column[data-status="${targetStatus}"] .task-container`);
                const addButton = targetContainer.querySelector('button:last-child');
                
                // Remove empty placeholder if it exists
                const emptyPlaceholder = targetContainer.querySelector('.empty-placeholder');
                if (emptyPlaceholder) emptyPlaceholder.remove();
                
                targetContainer.insertBefore(taskCard, addButton);
                
                // Update task data attribute
                taskCard.setAttribute('ondragstart', `dragStart(event, '${taskId}', '${targetStatus}')`);
                
                // Update counters
                updateColumnCounter(sourceStatus);
                updateColumnCounter(targetStatus);
                
                // Save to database with better error handling
                updateTaskStatus(taskId, targetStatus);
            }
        }
        
        draggedTaskId = null;
        sourceStatus = null;
    }

    function updateColumnCounter(status) {
        const column = document.querySelector(`.task-column[data-status="${status}"]`);
        if (!column) return;
        
        const counter = column.querySelector('h3 span');
        const taskCount = column.querySelectorAll('.task-card').length;
        
        if (counter) {
            counter.textContent = `(${taskCount})`;
        }
    }

    // New Task Modal
    function openNewTaskModal(defaultStatus = 'todo') {
        const modal = document.getElementById('taskModal');
        const modalContent = document.getElementById('modalContent');
        
        modalContent.innerHTML = `
          <div class="flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Neue Aufgabe</h2>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
              <input type="text" id="newTaskTitle" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Aufgabe Titel">
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
              <textarea id="newTaskDescription" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" rows="3" placeholder="Aufgabe Beschreibung"></textarea>
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Fälligkeitsdatum</label>
              <input type="date" id="newTaskDueDate" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select id="newTaskStatus" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <?php foreach ($statuses as $statusKey => $statusLabel): ?>
                  <option value="<?= $statusKey ?>" <?= ($statusKey === 'todo') ? 'selected' : '' ?>><?= $statusLabel ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="flex justify-end mt-4">
              <button onclick="saveNewTask()" class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold">
                Aufgabe erstellen
              </button>
            </div>
          </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Set default status
        document.getElementById('newTaskStatus').value = defaultStatus;
      }

      function saveNewTask() {
        const title = document.getElementById('newTaskTitle').value.trim();
        const description = document.getElementById('newTaskDescription').value.trim();
        const dueDate = document.getElementById('newTaskDueDate').value;
        const status = document.getElementById('newTaskStatus').value;
        
        if (!title) {
          alert('Bitte geben Sie einen Titel für die Aufgabe ein.');
          return;
        }
        
        // AJAX request to create new task
        fetch('/api/tasks', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ title, description, due_date: dueDate, status }),
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Close modal
            document.getElementById('taskModal').classList.add('hidden');
            
            // Reset form
            document.getElementById('newTaskTitle').value = '';
            document.getElementById('newTaskDescription').value = '';
            document.getElementById('newTaskDueDate').value = '';
            document.getElementById('newTaskStatus').value = 'todo';
            
            // Add new task to the correct column
            const newTask = data.task;
            const targetContainer = document.querySelector(`.task-column[data-status="${newTask.status}"] .task-container`);
            const addButton = targetContainer.querySelector('button:last-child');
            
            // Remove empty placeholder if it exists
            const emptyPlaceholder = targetContainer.querySelector('.empty-placeholder');
