<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title><?= htmlspecialchars($task['title']) ?> | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
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
                                    <input type="hidden" name="assignment_type" value="group">
                                    <input type="hidden" name="assigned_group" value="<?= $task['assigned_group_id'] ?>">
                                </div>
                            <?php else: ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Zugewiesen an</label>
                                    <select name="assigned_to" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>" 
                                                    <?= $user['id'] == $task['assigned_to'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($user['username']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="assignment_type" value="user">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fällig am</label>
                            <input type="date" name="due_date" 
                                   value="<?= $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : '' ?>" 
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div class="flex justify-between">
                            <input type="hidden" name="update_task" value="1">
                            <button type="submit" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#4A90E2]/90 transition">Speichern</button>
                            <button type="submit" name="mark_done" value="1" 
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Als erledigt markieren</button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Nur-Lese-Modus -->
                    <div class="p-6 md:p-8">
                        <h1 class="text-2xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($task['title']) ?></h1>
                        
                        <?php if (!empty($task['description'])): ?>
                            <p class="mb-6 text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <span class="text-sm text-gray-500 block">Ersteller</span>
                                <span class="font-medium"><?= htmlspecialchars($task['creator_name']) ?></span>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <span class="text-sm text-gray-500 block">Zugewiesen an</span>
                                <?php if ($task['assigned_group_id']): ?>
                                    <span class="inline-flex items-center">
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-lg text-sm font-medium">
                                            Gruppe: <?= htmlspecialchars($task['group_name']) ?>
                                        </span>
                                    </span>
                                <?php else: ?>
                                    <span class="font-medium"><?= htmlspecialchars($task['assignee_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($task['due_date']): ?>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <span class="text-sm text-gray-500 block">Fällig am</span>
                                    <?php 
                                    $dueDate = strtotime($task['due_date']);
                                    $isOverdue = $dueDate < time();
                                    $dueDateFormatted = date('d.m.Y', $dueDate);
                                    ?>
                                    <span class="font-medium <?= $isOverdue ? 'text-red-600' : '' ?>">
                                        <?= $dueDateFormatted ?>
                                        <?= $isOverdue ? ' (überfällig)' : '' ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <span class="text-sm text-gray-500 block">Erstellt am</span>
                                <span class="font-medium"><?= date('d.m.Y', strtotime($task['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <form method="post">
                            <button type="submit" name="mark_done" value="1" 
                                    class="w-full md:w-auto px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                Als erledigt markieren
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Unteraufgaben -->
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm p-6 md:p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Unteraufgaben</h2>
                
                <!-- Fortschrittsanzeige -->
                <div class="mb-6">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-[#4A90E2]">Fortschritt</span>
                        <span class="text-sm font-medium text-[#4A90E2]"><?= $progress ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-[#4A90E2] h-2.5 rounded-full transition-all duration-300" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
                
                <!-- Liste der Unteraufgaben -->
                <?php if (!empty($subtasks)): ?>
                    <ul class="space-y-2 mb-6">
                        <?php foreach ($subtasks as $subtask): ?>
                            <li class="flex items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition">
                                <form method="post" class="flex items-center w-full">
                                    <input type="hidden" name="subtask_id" value="<?= $subtask['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $subtask['status'] ?>">
                                    <input type="hidden" name="toggle_subtask" value="1">
                                    <button type="submit" class="mr-3 flex-shrink-0">
                                        <?php if ($subtask['status'] === 'closed'): ?>
                                            <div class="w-5 h-5 border-2 border-[#4A90E2] rounded flex items-center justify-center bg-[#4A90E2] transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-5 h-5 border-2 border-gray-300 rounded transition hover:border-[#4A90E2]"></div>
                                        <?php endif; ?>
                                    </button>
                                    <span class="<?= $subtask['status'] === 'closed' ? 'line-through text-gray-500' : 'text-gray-900' ?>">
                                        <?= htmlspecialchars($subtask['title']) ?>
                                    </span>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500 mb-6 bg-white p-4 rounded-lg text-center">Keine Unteraufgaben vorhanden.</p>
                <?php endif; ?>
                
                <!-- Neue Unteraufgabe hinzufügen -->
                <form method="post" class="flex space-x-2">
                    <input type="text" name="subtask_title" placeholder="Neue Unteraufgabe hinzufügen" 
                           class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]" required>
                    <input type="hidden" name="add_subtask" value="1">
                    <button type="submit" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#4A90E2]/90 transition">Hinzufügen</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
