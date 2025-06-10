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
    @media (min-width: 769px) {
      main.main-content-area { 
        margin-left: 17rem !important;
        margin-top: 1rem;
        margin-right: 1rem;
        margin-bottom: 1rem;
      }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2]">
  <?php require_once __DIR__ . '/navbar.php'; ?>
  <main class="main-content-area ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-6">
    
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
                   class="bg-white rounded-md shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow duration-150"
                   draggable="true" 
                   ondragstart="dragStart(event, <?= $task['id'] ?>, '<?= $key ?>')"
                   onclick="openTaskModal(<?= $task['id'] ?>)">
                <h3 class="font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></h3>
                <?php if (!empty($task['description'])): ?>
                  <p class="text-sm text-gray-600 mt-1 truncate"><?= htmlspecialchars($task['description']) ?></p>
                <?php endif; ?>
                <?php if (!empty($task['due_date'])): ?>
                  <p class="text-xs text-gray-500 mt-2">Fällig: <?= date('d.m.Y', strtotime($task['due_date'])) ?></p>
                <?php endif; ?>
                 <?php if (($task['recurrence_type'] ?? 'none') !== 'none'): ?>
                    <p class="text-xs text-gray-500 mt-1">Wiederholung: 
                        <?php 
                        echo htmlspecialchars(ucfirst($task['recurrence_type']));
                        if (!empty($task['recurrence_interval'])) {
                            echo " (alle ".htmlspecialchars($task['recurrence_interval']).")";
                        }
                        if (!empty($task['recurrence_end_date'])) {
                            echo " bis ".date('d.m.Y', strtotime($task['recurrence_end_date']));
                        }
                        ?>
                    </p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <button onclick="openNewTaskModal('<?= $key ?>')" class="mt-3 w-full text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-100 p-2 rounded-md transition-colors duration-150">+ Aufgabe hinzufügen</button>
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
        event.dataTransfer.setData('text/plain', taskId); // Wichtig für Firefox
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

        // API-Aufruf zum Aktualisieren des Task-Status
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
            console.log(data); // Erfolgsmeldung vom Server
            // UI aktualisieren
            document.getElementById(`tasks-${targetStatus}`).appendChild(taskElement);
            updateColumnCounter(sourceStatus);
            updateColumnCounter(targetStatus);
            // Reset draggedTaskId and sourceStatus
            draggedTaskId = null;
            sourceStatus = null;
        })
        .catch(error => {
            console.error('Error updating task status:', error);
            alert(error.message);
            // Ggf. Task zurück in die ursprüngliche Spalte verschieben oder Fehler anzeigen
        });
    }

    function updateColumnCounter(status) {
        const countElement = document.getElementById(`count-${status}`);
        const tasksContainer = document.getElementById(`tasks-${status}`);
        if (countElement && tasksContainer) {
            countElement.textContent = tasksContainer.children.length;
        }
    }

    // New Task Modal
    function openNewTaskModal(defaultStatus = 'todo') {
        fetch('/templates/task_modal.php?status=' + defaultStatus)
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('taskModal').classList.remove('hidden');
                document.getElementById('taskModal').classList.add('flex');
                // Event-Listener für das neue Formular im Modal neu binden
                const taskForm = document.getElementById('taskForm');
                if(taskForm) {
                    taskForm.addEventListener('submit', handleTaskFormSubmit);
                }
            });
    }
    
    function openTaskModal(taskId) {
        fetch('/templates/task_modal.php?id=' + taskId)
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('taskModal').classList.remove('hidden');
                document.getElementById('taskModal').classList.add('flex');
                 // Event-Listener für das neue Formular im Modal neu binden
                const taskForm = document.getElementById('taskForm');
                if(taskForm) {
                    taskForm.addEventListener('submit', handleTaskFormSubmit);
                }
            });
    }

    function handleTaskFormSubmit(event) {
        event.preventDefault();
        const fd = new FormData(event.target);
        const recurrenceType = fd.get('recurrence_type');
        if (recurrenceType === 'none') {
            fd.delete('recurrence_interval');
            fd.delete('recurrence_end_date');
        }

        fetch('/src/api/task_save.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    location.reload(); // Einfachste Lösung: Seite neu laden, um Änderungen zu sehen
                } else {
                    alert(res.error || 'Fehler beim Speichern des Tasks.');
                }
            })
            .catch(error => {
                console.error('Error saving task:', error);
                alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
            });
    }

    // Close Modal
    document.getElementById('taskModal').addEventListener('click', function(event) {
        if (event.target === this) { // Klick auf den Hintergrund
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });

    // Initialisiere Spaltenzähler beim Laden der Seite
    document.addEventListener('DOMContentLoaded', () => {
        <?php foreach (array_keys($statuses) as $statusKey): ?>
        updateColumnCounter('<?= $statusKey ?>');
        <?php endforeach; ?>
    });

  </script>
</body>
</html>
