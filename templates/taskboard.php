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
  SELECT t.*, 
         (SELECT COUNT(*) FROM task_subtasks WHERE task_id = t.id) as subtask_count,
         (SELECT COUNT(*) FROM task_subtasks WHERE task_id = t.id AND is_completed = 1) as completed_subtasks
    FROM tasks t
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

// Helper function for priority colors
function getPriorityColor($priority) {
    switch ($priority) {
        case 'urgent': return 'bg-red-500';
        case 'high': return 'bg-orange-500';
        case 'medium': return 'bg-yellow-500';
        case 'low': return 'bg-green-500';
        default: return 'bg-gray-500';
    }
}

// Helper function for category colors
function getCategoryColor($category) {
    switch ($category) {
        case 'development': return 'bg-blue-100 text-blue-800';
        case 'design': return 'bg-purple-100 text-purple-800';
        case 'marketing': return 'bg-pink-100 text-pink-800';
        case 'administration': return 'bg-gray-100 text-gray-800';
        case 'meeting': return 'bg-yellow-100 text-yellow-800';
        case 'research': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
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
    
    /* Enhanced task card styling */
    .task-card {
      transition: all 0.2s ease;
      position: relative;
    }
    .task-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .priority-indicator {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      border-radius: 4px 4px 0 0;
    }
    .subtask-progress {
      background: #e5e7eb;
      height: 4px;
      border-radius: 2px;
      overflow: hidden;
    }
    .subtask-progress-bar {
      height: 100%;
      background: #10b981;
      transition: width 0.3s ease;
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
        <div id="column-<?= $key ?>"
             class="task-column bg-gray-100/80 backdrop-blur-sm rounded-lg p-3 md:p-4 w-full md:w-1/3 shadow"
             ondragover="event.preventDefault()" 
             ondrop="dropTask(event, '<?= $key ?>')">
          <h2 class="text-lg font-semibold text-gray-800 mb-3"><?= $label ?> (<span id="count-<?= $key ?>"><?= count($tasksByStatus[$key] ?? []) ?></span>)</h2>
          <div id="tasks-<?= $key ?>" class="space-y-3 min-h-[50px]">
            <?php foreach (($tasksByStatus[$key] ?? []) as $task): ?>
              <div id="task-<?= $task['id'] ?>" 
                   class="task-card bg-white rounded-md shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow duration-150 relative"
                   draggable="true" 
                   ondragstart="dragStart(event, <?= $task['id'] ?>, '<?= $key ?>')"
                   onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
                
                <!-- Priority indicator -->
                <div class="priority-indicator <?= getPriorityColor($task['priority'] ?? 'medium') ?>"></div>
                
                <div class="pt-2">
                  <!-- Header with title and budget -->
                  <div class="flex justify-between items-start mb-2">
                    <h3 class="font-medium text-gray-900 flex-1"><?= htmlspecialchars($task['title']) ?></h3>
                    <?php if (!empty($task['estimated_budget'])): ?>
                      <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded ml-2">
                        €<?= number_format($task['estimated_budget'], 0) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Category badge -->
                  <?php if (!empty($task['category'])): ?>
                    <div class="mb-2">
                      <span class="inline-block px-2 py-1 text-xs rounded-full <?= getCategoryColor($task['category']) ?>">
                        <?= htmlspecialchars(ucfirst($task['category'])) ?>
                      </span>
                    </div>
                  <?php endif; ?>
                  
                  <!-- Description -->
                  <?php if (!empty($task['description'])): ?>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2"><?= htmlspecialchars($task['description']) ?></p>
                  <?php endif; ?>
                  
                  <!-- Subtasks progress -->
                  <?php if ($task['subtask_count'] > 0): ?>
                    <div class="mt-2">
                      <div class="flex justify-between items-center text-xs text-gray-500 mb-1">
                        <span>Unteraufgaben</span>
                        <span><?= $task['completed_subtasks'] ?>/<?= $task['subtask_count'] ?></span>
                      </div>
                      <div class="subtask-progress">
                        <div class="subtask-progress-bar" style="width: <?= $task['subtask_count'] > 0 ? ($task['completed_subtasks'] / $task['subtask_count'] * 100) : 0 ?>%"></div>
                      </div>
                    </div>
                  <?php endif; ?>
                  
                  <!-- Tags -->
                  <?php if (!empty($task['tags'])): ?>
                    <div class="mt-2">
                      <?php foreach (explode(',', $task['tags']) as $tag): ?>
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                          #<?= htmlspecialchars(trim($tag)) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                  
                  <!-- Footer with due date and hours -->
                  <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
                    <div class="flex items-center space-x-3">
                      <?php if (!empty($task['due_date'])): ?>
                        <span class="flex items-center">
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                          <?= date('d.m.Y', strtotime($task['due_date'])) ?>
                        </span>
                      <?php endif; ?>
                      
                      <?php if (!empty($task['estimated_hours'])): ?>
                        <span class="flex items-center">
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          <?= $task['estimated_hours'] ?>h
                        </span>
                      <?php endif; ?>
                    </div>
                    
                    <!-- Priority badge -->
                    <?php if ($task['priority'] === 'urgent' || $task['priority'] === 'high'): ?>
                      <span class="text-xs font-medium <?= $task['priority'] === 'urgent' ? 'text-red-600' : 'text-orange-600' ?>">
                        <?= $task['priority'] === 'urgent' ? 'DRINGEND' : 'HOCH' ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button onclick="openNewTaskModal('<?= $key ?>')" class="mt-3 w-full text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-100 p-2 rounded-md transition-colors duration-150">+ Aufgabe hinzufügen</button>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <script>
    // Drag and Drop Functionality
    let draggedTaskId = null;
    let sourceStatus = null;

    function dragStart(event, taskId, status) {
        draggedTaskId = taskId;
        sourceStatus = status;
        event.dataTransfer.setData('text/plain', taskId);
        event.target.classList.add('dragging');
    }

    function dragEnd(event) {
        event.target.classList.remove('dragging');
    }

    function dropTask(event, targetStatus) {
        event.preventDefault();
        if (!draggedTaskId || sourceStatus === targetStatus) return;

        const taskElement = document.getElementById(`task-${draggedTaskId}`);
        if (!taskElement) return;

        const formData = new FormData();
        formData.append('id', draggedTaskId);
        formData.append('status', targetStatus);

        fetch('/src/api/task_update.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error('Fehler beim Aktualisieren: ' + text); });
            }
            return response.text();
        })
        .then(data => {
            document.getElementById(`tasks-${targetStatus}`).appendChild(taskElement);
            updateColumnCounter(sourceStatus);
            updateColumnCounter(targetStatus);
            draggedTaskId = null;
            sourceStatus = null;
        })
        .catch(error => {
            console.error('Error updating task status:', error);
            alert(error.message);
        });
    }

    function updateColumnCounter(status) {
        const countElement = document.getElementById(`count-${status}`);
        const tasksContainer = document.getElementById(`tasks-${status}`);
        if (countElement && tasksContainer) {
            countElement.textContent = tasksContainer.children.length;
        }
    }

    // Initialisiere Spaltenzähler beim Laden der Seite
    document.addEventListener('DOMContentLoaded', () => {
        <?php foreach (array_keys($statuses) as $statusKey): ?>
        updateColumnCounter('<?= $statusKey ?>');
        <?php endforeach; ?>
    });

  </script>
</body>
</html>
