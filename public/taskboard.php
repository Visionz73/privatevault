<?php
// public/taskboard.php — Entry point for the Kanban board
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config.php';           // DB + global settings
require_once __DIR__ . '/../src/lib/auth.php';     // Authentication
requireLogin();
require_once __DIR__ . '/../src/controllers/taskboard.php';

// Get filter mode from query parameter, default to 'all'
$filterMode = $_GET['filter'] ?? 'all';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskBoard | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
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
    <?php require_once __DIR__ . '/../templates/navbar.php'; ?>
    
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
                <a href="?filter=group" class="px-4 py-2 rounded-lg text-sm font-medium <?= ($filterMode === 'group') ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' ?>">
                    Gruppenaufgaben
                </a>
                
                <!-- Create Task Button -->
                <button onclick="openNewTaskModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Neue Aufgabe
                </button>
            </div>
        </div>

        <!-- Columns Container -->
        <div class="flex space-x-4 columns-container overflow-x-auto pb-6">
            <div id="column-todo"
                 class="task-column bg-gray-100/80 backdrop-blur-sm rounded-lg p-3 md:p-4 w-full md:w-1/3 shadow"
                 ondragover="event.preventDefault()" 
                 ondrop="dropTask(event, 'todo')">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">To Do (<span id="count-todo"><?= count($todoTasks ?? []) ?></span>)</h2>
                <div id="tasks-todo" class="space-y-3 min-h-[50px]">
                    <?php foreach(($todoTasks ?? []) as $task): ?>
                    <div id="task-<?= $task['id'] ?>" 
                         class="bg-white rounded-md shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow duration-150"
                         draggable="true" 
                         ondragstart="dragStart(event, <?= $task['id'] ?>, 'todo')"
                         onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
                        <div class="task-priority-indicator w-full h-1 rounded-sm mb-2 <?= getPriorityClass($task) ?>"></div>
                        <h3 class="font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></h3>
                        <?php if (!empty($task['description'])): ?>
                        <p class="text-sm text-gray-600 mt-1 truncate"><?= htmlspecialchars($task['description']) ?></p>
                        <?php endif; ?>
                        <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                            <?php if (!empty($task['due_date'])): ?>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?= formatDate($task['due_date']) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($task['assigned_to'])): ?>
                            <span class="flex items-center" title="<?= htmlspecialchars(getUserName($task['assigned_to'])) ?>">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <?= htmlspecialchars(getUserInitials($task['assigned_to'])) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($task['assigned_group_id'])): ?>
                            <span class="flex items-center" title="<?= htmlspecialchars(getGroupName($task['assigned_group_id'])) ?>">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <?= htmlspecialchars(getGroupInitials($task['assigned_group_id'])) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button onclick="openNewTaskModal('todo')" class="mt-3 w-full text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-100 p-2 rounded-md transition-colors duration-150">
                    + Aufgabe hinzufügen
                </button>
            </div>
            
            <div id="column-doing"
                 class="task-column bg-gray-100/80 backdrop-blur-sm rounded-lg p-3 md:p-4 w-full md:w-1/3 shadow"
                 ondragover="event.preventDefault()" 
                 ondrop="dropTask(event, 'doing')">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">In Bearbeitung (<span id="count-doing"><?= count($inProgressTasks ?? []) ?></span>)</h2>
                <div id="tasks-doing" class="space-y-3 min-h-[50px]">
                    <?php foreach(($inProgressTasks ?? []) as $task): ?>
                    <div id="task-<?= $task['id'] ?>" 
                         class="bg-white rounded-md shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow duration-150"
                         draggable="true" 
                         ondragstart="dragStart(event, <?= $task['id'] ?>, 'doing')"
                         onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
                        <div class="task-priority-indicator w-full h-1 rounded-sm mb-2 <?= getPriorityClass($task) ?>"></div>
                        <h3 class="font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></h3>
                        <?php if (!empty($task['description'])): ?>
                        <p class="text-sm text-gray-600 mt-1 truncate"><?= htmlspecialchars($task['description']) ?></p>
                        <?php endif; ?>
                        <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                            <?php if (!empty($task['due_date'])): ?>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?= formatDate($task['due_date']) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($task['assigned_to'])): ?>
                            <span class="flex items-center" title="<?= htmlspecialchars(getUserName($task['assigned_to'])) ?>">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <?= htmlspecialchars(getUserInitials($task['assigned_to'])) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($task['assigned_group_id'])): ?>
                            <span class="flex items-center" title="<?= htmlspecialchars(getGroupName($task['assigned_group_id'])) ?>">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <?= htmlspecialchars(getGroupInitials($task['assigned_group_id'])) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button onclick="openNewTaskModal('doing')" class="mt-3 w-full text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-100 p-2 rounded-md transition-colors duration-150">
                    + Aufgabe hinzufügen
                </button>
            </div>
            
            <div id="column-done"
                 class="task-column bg-gray-100/80 backdrop-blur-sm rounded-lg p-3 md:p-4 w-full md:w-1/3 shadow"
                 ondragover="event.preventDefault()" 
                 ondrop="dropTask(event, 'done')">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Erledigt (<span id="count-done"><?= count($completedTasks ?? []) ?></span>)</h2>
                <div id="tasks-done" class="space-y-3 min-h-[50px]">
                    <?php foreach(($completedTasks ?? []) as $task): ?>
                    <div id="task-<?= $task['id'] ?>" 
                         class="bg-white rounded-md shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow duration-150"
                         draggable="true" 
                         ondragstart="dragStart(event, <?= $task['id'] ?>, 'done')"
                         onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
                        <div class="task-priority-indicator w-full h-1 rounded-sm mb-2 <?= getPriorityClass($task) ?>"></div>
                        <h3 class="font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></h3>
                        <?php if (!empty($task['description'])): ?>
                        <p class="text-sm text-gray-600 mt-1 truncate"><?= htmlspecialchars($task['description']) ?></p>
                        <?php endif; ?>
                        <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                            <?php if (!empty($task['due_date'])): ?>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?= formatDate($task['due_date']) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($task['assigned_to'])): ?>
                            <span class="flex items-center" title="<?= htmlspecialchars(getUserName($task['assigned_to'])) ?>">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <?= htmlspecialchars(getUserInitials($task['assigned_to'])) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($task['assigned_group_id'])): ?>
                            <span class="flex items-center" title="<?= htmlspecialchars(getGroupName($task['assigned_group_id'])) ?>">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <?= htmlspecialchars(getGroupInitials($task['assigned_group_id'])) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button onclick="openNewTaskModal('done')" class="mt-3 w-full text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-100 p-2 rounded-md transition-colors duration-150">
                    + Aufgabe hinzufügen
                </button>
            </div>
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
                // UI aktualisieren
                document.getElementById(`tasks-${targetStatus}`).appendChild(taskElement);
                updateColumnCounter(sourceStatus);
                updateColumnCounter(targetStatus);
                // Reset draggedTaskId and sourceStatus
                draggedTaskId = null;
                sourceStatus = null;
                taskElement.classList.remove('dragging');
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

        // Initialize column counters
        document.addEventListener('DOMContentLoaded', () => {
            updateColumnCounter('todo');
            updateColumnCounter('doing');
            updateColumnCounter('done');
        });
    </script>
</body>
</html>
