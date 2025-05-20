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
            <h3 class="font-semibold text-gray-700"><?= $label ?> <span class="text-sm text-gray-500">(<?= count($columns[$key]) ?>)</span></h3>
          </div>

          <!-- Task Container -->
          <div class="task-container min-h-[100px] space-y-3" ondragover="event.preventDefault()" ondrop="dropTask(event, '<?= $key ?>')">
            <?php foreach ($columns[$key] as $task): ?>
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
            
            <?php if(count($columns[$key]) === 0): ?>
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
        event.target.classList.add('dragging');
        draggedTaskId = taskId;
        sourceStatus = status;
    }

    function dropTask(event, targetStatus) {
        event.preventDefault();
        if (!draggedTaskId) return;
        
        const taskCard = document.querySelector(`.task-card[data-id="${draggedTaskId}"]`);
        if (taskCard) {
            taskCard.classList.remove('dragging');
            
            if (sourceStatus !== targetStatus) {
                // Move in DOM
                const targetContainer = document.querySelector(`.task-column[data-status="${targetStatus}"] .task-container`);
                const emptyPlaceholder = targetContainer.querySelector('.empty-placeholder');
                if (emptyPlaceholder) emptyPlaceholder.remove();
                
                // Insert before the "add task" button
                const addButton = targetContainer.querySelector('button:last-child');
                targetContainer.insertBefore(taskCard, addButton);
                
                // Update counters
                updateColumnCounter(sourceStatus);
                updateColumnCounter(targetStatus);
                
                // Update task data attribute for future operations
                taskCard.setAttribute('data-status', targetStatus);
                
                // IMPORTANT: Save to database
                updateTaskStatus(draggedTaskId, targetStatus);
            }
        }
        
        draggedTaskId = null;
        sourceStatus = null;
    }

    function updateColumnCounter(status) {
      const column = document.querySelector(`.task-column[data-status="${status}"]`);
      const counter = column.querySelector('h3 span');
      const taskCount = column.querySelectorAll('.task-card').length;
      counter.textContent = `(${taskCount})`;
    }

    function updateTaskStatus(taskId, status) {
        // Debug output
        console.log(`Updating task ${taskId} to status ${status}`);
        
        // Send to server
        fetch('/src/api/task_update.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${taskId}&status=${status}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            console.log('Status updated successfully');
        })
        .catch(error => {
            console.error('Error updating status:', error);
            alert('Fehler beim Aktualisieren des Status. Bitte aktualisieren Sie die Seite.');
        });
    }
    
    // Modal functionality
    function openTaskModal(id) {
      loadModalContent(`/templates/task_modal.php?id=${id}`);
    }
    
    function openNewTaskModal(defaultStatus = 'todo') {
      loadModalContent(`/templates/task_modal.php?status=${defaultStatus}`);
    }
    
    function loadModalContent(url) {
      fetch(url)
        .then(response => response.text())
        .then(html => {
          document.getElementById('modalContent').innerHTML = html;
          document.getElementById('taskModal').classList.remove('hidden');
          document.getElementById('taskModal').classList.add('flex');
        });
    }
    
    document.getElementById('taskModal').addEventListener('click', (e) => {
      if (e.target.id === 'taskModal') {
        e.target.classList.add('hidden');
        e.target.classList.remove('flex');
      }
    });
  </script>
</body>
</html>
