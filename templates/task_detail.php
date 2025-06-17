<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title><?= htmlspecialchars($task['title']) ?> | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            min-height: 100vh;
        }
        @media (max-width: 768px) {
            main { margin-top: 4rem; }
        }
        
        /* Task detail card */
        .task-detail-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        /* Form inputs */
        .task-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            color: white;
            transition: all 0.3s ease;
        }
        .task-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        .task-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Subtask progress bar */
        .subtask-progress {
            background: rgba(255, 255, 255, 0.1);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .subtask-progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: width 0.3s ease;
        }
        
        /* Status badges */
        .status-badge {
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-todo {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .status-doing {
            background: rgba(251, 191, 36, 0.2);
            color: #fde047;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
        .status-done {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        /* Text styling */
        .text-primary {
            color: white;
        }
        .text-secondary {
            color: rgba(255, 255, 255, 0.7);
        }
        .text-muted {
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Back button */
        .back-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }
        .back-link:hover {
            color: white;
            text-decoration: none;
        }
        
        /* Subtask items */
        .subtask-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .subtask-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .subtask-completed {
            opacity: 0.6;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__.'/navbar.php'; ?>
    
    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Zurück-Button -->
            <div class="mb-6">
                <a href="inbox.php" class="back-link flex items-center hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Zurück zur Inbox
                </a>
            </div>
            
            <!-- Task Status Badge -->
            <div class="mb-4">
                <?php 
                $statusClass = 'status-todo';
                $statusLabel = 'To Do';
                switch($task['status']) {
                    case 'doing':
                        $statusClass = 'status-doing';
                        $statusLabel = 'In Bearbeitung';
                        break;
                    case 'done':
                        $statusClass = 'status-done';
                        $statusLabel = 'Erledigt';
                        break;
                }
                ?>
                <span class="status-badge <?= $statusClass ?>">
                    <?= $statusLabel ?>
                </span>
            </div>
            
            <!-- Aufgaben-Details Card -->
            <div class="task-detail-card mb-6">
                <?php if ($editMode): ?>
                    <!-- Bearbeitungsmodus -->
                    <form method="post" class="p-6 md:p-8">
                        <!-- Basic Task Info -->
                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Titel</label>
                                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" 
                                       class="task-input w-full px-4 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Beschreibung</label>
                                <textarea name="description" rows="4" 
                                          class="task-input w-full px-4 py-2"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Grid for task properties -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Fällig am</label>
                                <input type="date" name="due_date" value="<?= $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : '' ?>" 
                                       class="task-input w-full px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Status</label>
                                <select name="status" class="task-input w-full px-4 py-2">
                                    <option value="todo" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>To Do</option>
                                    <option value="doing" <?= $task['status'] === 'doing' ? 'selected' : '' ?>>In Bearbeitung</option>
                                    <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Erledigt</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Priorität</label>
                                <select name="priority" class="task-input w-full px-4 py-2">
                                    <option value="low" <?= ($task['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Niedrig</option>
                                    <option value="medium" <?= ($task['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Mittel</option>
                                    <option value="high" <?= ($task['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Hoch</option>
                                    <option value="urgent" <?= ($task['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Dringend</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Kategorie</label>
                                <select name="category" class="task-input w-full px-4 py-2">
                                    <option value="">Keine Kategorie</option>
                                    <option value="development" <?= ($task['category'] ?? '') === 'development' ? 'selected' : '' ?>>Entwicklung</option>
                                    <option value="design" <?= ($task['category'] ?? '') === 'design' ? 'selected' : '' ?>>Design</option>
                                    <option value="marketing" <?= ($task['category'] ?? '') === 'marketing' ? 'selected' : '' ?>>Marketing</option>
                                    <option value="administration" <?= ($task['category'] ?? '') === 'administration' ? 'selected' : '' ?>>Administration</option>
                                    <option value="meeting" <?= ($task['category'] ?? '') === 'meeting' ? 'selected' : '' ?>>Meeting</option>
                                    <option value="research" <?= ($task['category'] ?? '') === 'research' ? 'selected' : '' ?>>Recherche</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Budget Section -->
                        <div class="border-t border-white/10 pt-6 mb-6">
                            <h3 class="text-lg font-medium text-primary mb-4">Budget & Aufwand</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-primary mb-1">Geschätztes Budget (€)</label>
                                    <input type="number" name="estimated_budget" step="0.01" min="0"
                                           value="<?= htmlspecialchars($task['estimated_budget'] ?? '') ?>" 
                                           class="task-input w-full px-4 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-primary mb-1">Geschätzte Stunden</label>
                                    <input type="number" name="estimated_hours" step="0.5" min="0"
                                           value="<?= htmlspecialchars($task['estimated_hours'] ?? '') ?>" 
                                           class="task-input w-full px-4 py-2">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-primary mb-1">Tags</label>
                            <input type="text" name="tags" 
                                   value="<?= htmlspecialchars($task['tags'] ?? '') ?>"
                                   placeholder="Tags durch Komma getrennt..."
                                   class="task-input w-full px-4 py-2">
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'" 
                                    class="btn-secondary px-4 py-2">
                                Abbrechen
                            </button>
                            <button type="submit" class="btn-primary px-4 py-2">
                                Änderungen speichern
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- View Mode -->
                    <div class="p-6 md:p-8">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-primary mb-1"><?= htmlspecialchars($task['title']) ?></h2>
                            <?php if ($task['description']): ?>
                                <p class="text-secondary whitespace-pre-line"><?= htmlspecialchars($task['description']) ?></p>
                            <?php else: ?>
                                <p class="text-muted italic">Keine Beschreibung vorhanden</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Enhanced Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-sm font-medium text-muted mb-1">Erstellt von</h3>
                                <p class="font-medium text-secondary"><?= htmlspecialchars($task['creator_name']) ?></p>
                            </div>
                            
                            <?php if ($task['assigned_to']): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-muted mb-1">Zugewiesen an</h3>
                                    <p class="font-medium text-secondary"><?= htmlspecialchars($task['assignee_name']) ?></p>
                                </div>
                            <?php elseif ($task['assigned_group_id']): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-muted mb-1">Zugewiesen an Gruppe</h3>
                                    <p class="font-medium text-secondary"><?= htmlspecialchars($task['group_name']) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($task['due_date']): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-muted mb-1">Fällig am</h3>
                                    <p class="font-medium text-secondary"><?= date('d.m.Y', strtotime($task['due_date'])) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h3 class="text-sm font-medium text-muted mb-1">Status</h3>
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($task['priority'])): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-muted mb-1">Priorität</h3>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        <?php
                                        switch($task['priority']) {
                                            case 'urgent': echo 'bg-red-100 text-red-800'; break;
                                            case 'high': echo 'bg-orange-100 text-orange-800'; break;
                                            case 'medium': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'low': echo 'bg-green-100 text-green-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= htmlspecialchars(ucfirst($task['priority'])) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($task['category'])): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-muted mb-1">Kategorie</h3>
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        <?= htmlspecialchars(ucfirst($task['category'])) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Budget & Time Information -->
                        <?php if (!empty($task['estimated_budget']) || !empty($task['estimated_hours'])): ?>
                            <div class="border-t border-white/10 pt-6 mb-6">
                                <h3 class="text-lg font-medium text-primary mb-4">Budget & Aufwand</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <?php if (!empty($task['estimated_budget'])): ?>
                                        <div>
                                            <h3 class="text-sm font-medium text-muted mb-1">Geschätztes Budget</h3>
                                            <p class="text-2xl font-bold text-green-400">€<?= number_format($task['estimated_budget'], 2) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($task['estimated_hours'])): ?>
                                        <div>
                                            <h3 class="text-sm font-medium text-muted mb-1">Geschätzte Stunden</h3>
                                            <p class="text-2xl font-bold text-blue-400"><?= $task['estimated_hours'] ?>h</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Tags if available -->
                        <?php if (!empty($task['tags'])): ?>
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-muted mb-2">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach (explode(',', $task['tags']) as $tag): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                            #<?= htmlspecialchars(trim($tag)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Quick Actions -->
                        <div class="border-t border-white/10 pt-6">
                            <h3 class="text-sm font-medium text-muted mb-3">Schnellaktionen</h3>
                            <div class="flex flex-wrap gap-3">
                                <?php if ($task['status'] !== 'done'): ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="btn-primary px-4 py-2 text-sm">
                                            Als erledigt markieren
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($task['status'] === 'todo'): ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="status" value="doing">
                                        <button type="submit" class="btn-secondary px-4 py-2 text-sm">
                                            In Bearbeitung
                                        </button>
                                    </form>
                                <?php elseif ($task['status'] === 'doing'): ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="status" value="todo">
                                        <button type="submit" class="btn-secondary px-4 py-2 text-sm">
                                            Zurück zu To-Do
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="taskboard.php" class="btn-secondary px-4 py-2 text-sm inline-block">
                                    Zum Kanban Board
                                </a>
                            </div>
                        </div>
                        
                        <?php if ($canEdit): ?>
                            <div class="flex justify-end mt-6 pt-4 border-t border-white/10">
                                <a href="?id=<?= $task['id'] ?>&edit=1" class="btn-primary px-4 py-2 inline-block">
                                    Bearbeiten
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Subtasks / To-Do List Section -->
            <div class="task-detail-card">
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-primary">To-Do Liste</h3>
                        <?php if ($totalSubtasks > 0): ?>
                            <span class="text-sm text-muted">
                                <?= $completedSubtasks ?>/<?= $totalSubtasks ?> erledigt (<?= $subtaskProgress ?>%)
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Progress Bar -->
                    <?php if ($totalSubtasks > 0): ?>
                        <div class="mb-6">
                            <div class="subtask-progress">
                                <div class="subtask-progress-bar" style="width: <?= $subtaskProgress ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Subtask List -->
                    <div class="space-y-3 mb-6">
                        <?php if (empty($subtasks)): ?>
                            <p class="text-muted text-center py-8">Noch keine Unteraufgaben vorhanden.</p>
                        <?php else: ?>
                            <?php foreach ($subtasks as $subtask): ?>
                                <div class="subtask-item p-4 flex items-center gap-4 <?= $subtask['is_completed'] ? 'subtask-completed' : '' ?>">
                                    <?php if ($canEdit): ?>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="toggle_subtask">
                                            <input type="hidden" name="subtask_id" value="<?= $subtask['id'] ?>">
                                            <input type="hidden" name="is_completed" value="<?= $subtask['is_completed'] ? '0' : '1' ?>">
                                            <button type="submit" class="w-5 h-5 rounded border-2 border-white/30 flex items-center justify-center hover:border-white/50 transition-colors">
                                                <?php if ($subtask['is_completed']): ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="w-5 h-5 rounded border-2 border-white/30 flex items-center justify-center">
                                            <?php if ($subtask['is_completed']): ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <span class="flex-1 <?= $subtask['is_completed'] ? 'line-through text-muted' : 'text-secondary' ?>">
                                        <?= htmlspecialchars($subtask['title']) ?>
                                    </span>
                                    
                                    <?php if ($canEdit): ?>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="delete_subtask">
                                            <input type="hidden" name="subtask_id" value="<?= $subtask['id'] ?>">
                                            <button type="submit" class="text-red-400 hover:text-red-300 transition-colors" 
                                                    onclick="return confirm('Unteraufgabe löschen?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Add New Subtask -->
                    <?php if ($canEdit): ?>
                        <form method="post" class="flex gap-3">
                            <input type="hidden" name="action" value="add_subtask">
                            <input type="text" name="subtask_title" required 
                                   placeholder="Neue Unteraufgabe hinzufügen..."
                                   class="task-input flex-1 px-4 py-2">
                            <button type="submit" class="btn-primary px-4 py-2 whitespace-nowrap">
                                Hinzufügen
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
