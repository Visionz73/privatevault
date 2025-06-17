<?php
// templates/taskboard.php - Modern Kanban Board
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/../src/lib/db.php';

// Get filter mode
$filterMode = $_GET['filter'] ?? 'all';

// Status configuration
$statuses = [
    'todo' => ['label' => 'To Do', 'color' => 'blue', 'icon' => 'fas fa-circle'],
    'doing' => ['label' => 'In Progress', 'color' => 'yellow', 'icon' => 'fas fa-clock'],
    'done' => ['label' => 'Done', 'color' => 'green', 'icon' => 'fas fa-check-circle']
];

// Build query based on filter
$baseWhere = '';
$params = [':user_id' => $_SESSION['user_id']];

switch ($filterMode) {
    case 'user':
        $baseWhere = 'WHERE t.assigned_to = :user_id';
        break;
    case 'group':
        $baseWhere = 'WHERE t.assigned_group_id IN (SELECT group_id FROM user_group_members WHERE user_id = :user_id)';
        break;
    default: // 'all'
        $baseWhere = 'WHERE (t.created_by = :user_id OR t.assigned_to = :user_id OR t.assigned_group_id IN (SELECT group_id FROM user_group_members WHERE user_id = :user_id))';
}

// Get all tasks with subtask counts
$stmt = $pdo->prepare("
    SELECT t.*, 
           u1.username as creator_name,
           u2.username as assignee_name,
           g.name as group_name,
           (SELECT COUNT(*) FROM task_subtasks WHERE task_id = t.id) as subtask_count,
           (SELECT COUNT(*) FROM task_subtasks WHERE task_id = t.id AND is_completed = 1) as completed_subtasks
    FROM tasks t
    LEFT JOIN users u1 ON t.created_by = u1.id
    LEFT JOIN users u2 ON t.assigned_to = u2.id
    LEFT JOIN user_groups g ON t.assigned_group_id = g.id
    {$baseWhere}
    ORDER BY t.created_at DESC
");
$stmt->execute($params);
$allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group tasks by status
$tasksByStatus = [];
foreach ($statuses as $status => $config) {
    $tasksByStatus[$status] = array_filter($allTasks, function($task) use ($status) {
        return $task['status'] === $status;
    });
}

// Helper functions
function getPriorityColor($priority) {
    switch ($priority) {
        case 'urgent': return 'bg-red-500';
        case 'high': return 'bg-orange-500';
        case 'medium': return 'bg-yellow-500';
        case 'low': return 'bg-green-500';
        default: return 'bg-gray-500';
    }
}

function getCategoryColor($category) {
    $colors = [
        'development' => 'bg-blue-100 text-blue-800',
        'design' => 'bg-purple-100 text-purple-800',
        'marketing' => 'bg-pink-100 text-pink-800',
        'administration' => 'bg-gray-100 text-gray-800',
        'meeting' => 'bg-yellow-100 text-yellow-800',
        'research' => 'bg-green-100 text-green-800'
    ];
    return $colors[$category] ?? 'bg-gray-100 text-gray-800';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Kanban Board Styles */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            min-height: calc(100vh - 200px);
        }
        
        .kanban-column {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .kanban-column.drag-over {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
            transform: scale(1.02);
        }
        
        /* Task Card Styles */
        .task-card {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: grab;
            position: relative;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .task-card.dragging {
            opacity: 0.7;
            transform: rotate(5deg) scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        
        .task-card:active {
            cursor: grabbing;
        }
        
        .priority-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 12px 12px 0 0;
        }
        
        /* Progress Bar */
        .progress-bar {
            background: #e5e7eb;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, #10b981, #059669);
            height: 100%;
            transition: width 0.3s ease;
        }
        
        /* Status Column Headers */
        .status-todo { border-top: 4px solid #3b82f6; }
        .status-doing { border-top: 4px solid #f59e0b; }
        .status-done { border-top: 4px solid #10b981; }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .kanban-board {
                grid-template-columns: 1fr;
            }
            
            main {
                margin-top: 4rem;
                padding: 1rem;
            }
        }
        
        /* Animation for new tasks */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .task-card.new-task {
            animation: slideIn 0.5s ease;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <?php require_once __DIR__ . '/navbar.php'; ?>
    
    <main class="ml-0 mt-16 md:ml-64 md:mt-0 p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Kanban Board</h1>
                <p class="text-gray-600">Verwalte deine Aufgaben visuell</p>
            </div>
            
            <div class="flex flex-wrap gap-3 mt-4 sm:mt-0">
                <!-- Filter Buttons -->
                <div class="flex bg-white rounded-lg shadow-sm border p-1">
                    <a href="?filter=all" class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($filterMode === 'all') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' ?>">
                        Alle
                    </a>
                    <a href="?filter=user" class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($filterMode === 'user') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' ?>">
                        Meine
                    </a>
                    <a href="?filter=group" class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($filterMode === 'group') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' ?>">
                        Gruppen
                    </a>
                </div>
                
                <button onclick="openTaskModal()" class="btn-primary px-6 py-2.5 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Neue Aufgabe
                </button>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="kanban-board">
            <?php foreach ($statuses as $status => $config): ?>
            <div class="kanban-column status-<?= $status ?>" 
                 id="column-<?= $status ?>"
                 ondragover="allowDrop(event)" 
                 ondrop="dropTask(event, '<?= $status ?>')"
                 ondragenter="dragEnter(event)"
                 ondragleave="dragLeave(event)">
                
                <!-- Column Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="<?= $config['icon'] ?> text-<?= $config['color'] ?>-500"></i>
                            <h3 class="text-lg font-semibold text-gray-900"><?= $config['label'] ?></h3>
                        </div>
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-2.5 py-1 rounded-full" 
                              id="count-<?= $status ?>">
                            <?= count($tasksByStatus[$status]) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Tasks Container -->
                <div class="p-4 min-h-96" id="tasks-<?= $status ?>">
                    <?php foreach ($tasksByStatus[$status] as $task): ?>
                    <div class="task-card" 
                         id="task-<?= $task['id'] ?>"
                         draggable="true"
                         ondragstart="dragStart(event, <?= $task['id'] ?>, '<?= $status ?>')"
                         ondragend="dragEnd(event)"
                         onclick="openTaskDetail(<?= $task['id'] ?>)">
                        
                        <!-- Priority Indicator -->
                        <div class="priority-indicator <?= getPriorityColor($task['priority'] ?? 'medium') ?>"></div>
                        
                        <div class="pt-2">
                            <!-- Task Header -->
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-semibold text-gray-900 flex-1 pr-2"><?= htmlspecialchars($task['title']) ?></h4>
                                <?php if (!empty($task['estimated_budget'])): ?>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                    €<?= number_format($task['estimated_budget'], 0) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Description -->
                            <?php if (!empty($task['description'])): ?>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars($task['description']) ?></p>
                            <?php endif; ?>
                            
                            <!-- Category -->
                            <?php if (!empty($task['category'])): ?>
                            <div class="mb-3">
                                <span class="inline-block px-2 py-1 text-xs rounded-full <?= getCategoryColor($task['category']) ?>">
                                    <?= htmlspecialchars(ucfirst($task['category'])) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Subtasks Progress -->
                            <?php if ($task['subtask_count'] > 0): ?>
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Unteraufgaben</span>
                                    <span><?= $task['completed_subtasks'] ?>/<?= $task['subtask_count'] ?></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $task['subtask_count'] > 0 ? ($task['completed_subtasks'] / $task['subtask_count'] * 100) : 0 ?>%"></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Tags -->
                            <?php if (!empty($task['tags'])): ?>
                            <div class="mb-3">
                                <?php foreach (array_slice(explode(',', $task['tags']), 0, 3) as $tag): ?>
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                                    #<?= htmlspecialchars(trim($tag)) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Footer -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <div class="flex items-center space-x-3 text-xs text-gray-500">
                                    <?php if (!empty($task['due_date'])): ?>
                                    <span class="flex items-center">
                                        <i class="far fa-calendar mr-1"></i>
                                        <?= date('d.m.Y', strtotime($task['due_date'])) ?>
                                    </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($task['estimated_hours'])): ?>
                                    <span class="flex items-center">
                                        <i class="far fa-clock mr-1"></i>
                                        <?= $task['estimated_hours'] ?>h
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Assignment -->
                                <div class="flex items-center">
                                    <?php if (!empty($task['assignee_name'])): ?>
                                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium"
                                         title="<?= htmlspecialchars($task['assignee_name']) ?>">
                                        <?= strtoupper(substr($task['assignee_name'], 0, 1)) ?>
                                    </div>
                                    <?php elseif (!empty($task['group_name'])): ?>
                                    <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs"
                                         title="<?= htmlspecialchars($task['group_name']) ?>">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Add Task Button -->
                    <button onclick="openTaskModal('<?= $status ?>')" 
                            class="w-full p-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:text-gray-700 hover:border-gray-400 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Aufgabe hinzufügen
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Neue Aufgabe</h2>
                    <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="modalContent">
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Drag and Drop Variables
        let draggedTask = null;
        let draggedFromColumn = null;
        
        // Drag Start
        function dragStart(event, taskId, fromStatus) {
            draggedTask = taskId;
            draggedFromColumn = fromStatus;
            event.target.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target.outerHTML);
        }
        
        // Drag End
        function dragEnd(event) {
            event.target.classList.remove('dragging');
        }
        
        // Allow Drop
        function allowDrop(event) {
            event.preventDefault();
        }
        
        // Drag Enter
        function dragEnter(event) {
            event.preventDefault();
            event.currentTarget.classList.add('drag-over');
        }
        
        // Drag Leave
        function dragLeave(event) {
            event.currentTarget.classList.remove('drag-over');
        }
        
        // Drop Task
        function dropTask(event, toStatus) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');
            
            if (!draggedTask || draggedFromColumn === toStatus) return;
            
            // Update task status via API
            const formData = new FormData();
            formData.append('id', draggedTask);
            formData.append('status', toStatus);
            
            fetch('/src/api/task_update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Move task element to new column
                    const taskElement = document.getElementById(`task-${draggedTask}`);
                    const targetContainer = document.getElementById(`tasks-${toStatus}`);
                    const addButton = targetContainer.querySelector('button');
                    
                    // Insert before the add button
                    targetContainer.insertBefore(taskElement, addButton);
                    
                    // Update counters
                    updateColumnCounter(draggedFromColumn);
                    updateColumnCounter(toStatus);
                    
                    // Add success animation
                    taskElement.classList.add('new-task');
                    setTimeout(() => taskElement.classList.remove('new-task'), 500);
                } else {
                    alert('Fehler beim Aktualisieren der Aufgabe: ' + (data.error || 'Unbekannter Fehler'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Netzwerkfehler beim Aktualisieren der Aufgabe');
            })
            .finally(() => {
                draggedTask = null;
                draggedFromColumn = null;
            });
        }
        
        // Update Column Counter
        function updateColumnCounter(status) {
            const counter = document.getElementById(`count-${status}`);
            const tasksContainer = document.getElementById(`tasks-${status}`);
            const taskCount = tasksContainer.querySelectorAll('.task-card').length;
            counter.textContent = taskCount;
        }
        
        // Open Task Modal
        function openTaskModal(status = 'todo') {
            const modal = document.getElementById('taskModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            modalContent.innerHTML = '<div class="animate-pulse"><div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div><div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div><div class="h-4 bg-gray-200 rounded w-5/6"></div></div>';
            
            fetch(`/templates/task_modal.php?status=${status}`)
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading modal:', error);
                    modalContent.innerHTML = '<p class="text-red-600">Fehler beim Laden des Formulars.</p>';
                });
        }
        
        // Close Task Modal
        function closeTaskModal() {
            document.getElementById('taskModal').classList.add('hidden');
        }
        
        // Open Task Detail
        function openTaskDetail(taskId) {
            window.location.href = `task_detail.php?id=${taskId}`;
        }
        
        // Close modal when clicking outside
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if (e.target === this) closeTaskModal();
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('taskModal').classList.contains('hidden')) {
                closeTaskModal();
            }
        });
    </script>
</body>
</html>
