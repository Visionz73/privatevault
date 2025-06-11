<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title><?= htmlspecialchars($task['title']) ?> | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/privatevault/css/main.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media (max-width: 768px) {
            main { margin-top: 3.5rem; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
    <?php require_once __DIR__.'/navbar.php'; ?>
    
    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-3xl mx-auto">
            <!-- Zurück-Button -->
            <div class="mb-6">
                <a href="inbox.php" class="text-[#4A90E2] flex items-center hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Zurück zur Inbox
                </a>
            </div>
            
            <!-- Task Status Badge -->
            <div class="mb-4">
                <?php 
                $statusColors = [
                    'todo' => 'bg-blue-100 text-blue-700',
                    'doing' => 'bg-yellow-100 text-yellow-700',
                    'done' => 'bg-green-100 text-green-700'
                ];
                $statusLabels = [
                    'todo' => 'To Do',
                    'doing' => 'In Bearbeitung',
                    'done' => 'Erledigt'
                ];
                $statusColor = $statusColors[$task['status']] ?? 'bg-gray-100 text-gray-700';
                $statusLabel = $statusLabels[$task['status']] ?? 'Unbekannt';
                ?>
                <span class="inline-block <?= $statusColor ?> rounded-full px-3 py-1 text-sm font-semibold">
                    <?= $statusLabel ?>
                </span>
            </div>
            
            <!-- Aufgaben-Details Card -->
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm mb-6">
                <?php if ($canEdit): ?>
                    <!-- Bearbeitungsmodus -->
                    <form method="post" class="p-6 md:p-8">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" 
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                            <textarea name="description" rows="4" 
                                      class="w-full px-4 py-2 border border-gray-200 rounded-lg"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ersteller</label>
                                <input type="text" value="<?= htmlspecialchars($task['creator_name']) ?>" 
                                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg" readonly>
                            </div>
                            <?php if ($task['assigned_group_id']): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Zugewiesen an Gruppe</label>
                                    <input type="text" value="<?= htmlspecialchars($task['group_name']) ?>" 
                                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg" readonly>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fällig am</label>
                                <input type="date" name="due_date" value="<?= $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : '' ?>" 
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                                    <option value="todo" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>To Do</option>
                                    <option value="doing" <?= $task['status'] === 'doing' ? 'selected' : '' ?>>In Bearbeitung</option>
                                    <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Erledigt</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="window.location.href='inbox.php'" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Abbrechen
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Änderungen speichern
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- View Mode -->
                    <div class="p-6 md:p-8">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($task['title']) ?></h2>
                            <?php if ($task['description']): ?>
                                <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($task['description']) ?></p>
                            <?php else: ?>
                                <p class="text-gray-500 italic">Keine Beschreibung vorhanden</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Erstellt von</h3>
                                <p class="font-medium"><?= htmlspecialchars($task['creator_name']) ?></p>
                            </div>
                            
                            <?php if ($task['assigned_to']): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Zugewiesen an</h3>
                                    <p class="font-medium"><?= htmlspecialchars($task['assignee_name']) ?></p>
                                </div>
                            <?php elseif ($task['assigned_group_id']): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Zugewiesen an Gruppe</h3>
                                    <p class="font-medium"><?= htmlspecialchars($task['group_name']) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($task['due_date']): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Fällig am</h3>
                                    <p class="font-medium"><?= date('d.m.Y', strtotime($task['due_date'])) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                                <span class="inline-block <?= $statusColor ?> rounded-full px-3 py-1 text-sm font-semibold">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($canEdit): ?>
                            <div class="flex justify-end">
                                <a href="?id=<?= $task['id'] ?>&edit=1" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
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
