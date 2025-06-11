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
            main { margin-top: 3.5rem; }
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
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.8) 0%, rgba(37, 99, 235, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(37, 99, 235, 0.9) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
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
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__.'/navbar.php'; ?>
    
    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-3xl mx-auto">
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
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-primary mb-1">Titel</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" 
                                   class="task-input w-full px-4 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-primary mb-1">Beschreibung</label>
                            <textarea name="description" rows="4" 
                                      class="task-input w-full px-4 py-2"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1">Ersteller</label>
                                <input type="text" value="<?= htmlspecialchars($task['creator_name']) ?>" 
                                       class="task-input w-full px-4 py-2 opacity-50" readonly>
                            </div>
                            <?php if ($task['assigned_group_id']): ?>
                                <div>
                                    <label class="block text-sm font-medium text-primary mb-1">Zugewiesen an Gruppe</label>
                                    <input type="text" value="<?= htmlspecialchars($task['group_name']) ?>" 
                                           class="task-input w-full px-4 py-2 opacity-50" readonly>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                        </div>
                        
                        <?php if ($canEdit): ?>
                            <div class="flex justify-end">
                                <a href="?id=<?= $task['id'] ?>&edit=1" class="btn-primary px-4 py-2 inline-block">
                                    Bearbeiten
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
