<?php
require_once __DIR__ . '/src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/src/lib/db.php';

$taskId = $_GET['id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    echo '<div class="text-red-400 p-4">Ungültige Aufgaben-ID</div>';
    exit;
}

// Task mit allen Details laden
$stmt = $pdo->prepare("
    SELECT t.*, 
           creator.username AS creator_name,
           assignee.username AS assignee_name,
           g.name AS group_name
    FROM tasks t
    LEFT JOIN users creator ON creator.id = t.created_by
    LEFT JOIN users assignee ON assignee.id = t.assigned_to
    LEFT JOIN user_groups g ON g.id = t.assigned_group_id
    WHERE t.id = ?
");
$stmt->execute([$taskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo '<div class="text-red-400 p-4">Aufgabe nicht gefunden</div>';
    exit;
}

// Status-Labels
$statusLabels = [
    'todo' => 'To Do',
    'doing' => 'In Bearbeitung', 
    'done' => 'Erledigt'
];

$statusColors = [
    'todo' => 'bg-blue-100 text-blue-700 border-blue-200',
    'doing' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
    'done' => 'bg-green-100 text-green-700 border-green-200'
];
?>

<div class="space-y-6">
    <!-- Task Header -->
    <div>
        <div class="flex items-center gap-3 mb-3">
            <span class="px-3 py-1 rounded-full text-sm font-medium border <?= $statusColors[$task['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200' ?>">
                <?= $statusLabels[$task['status']] ?? 'Unbekannt' ?>
            </span>
            <?php if ($task['priority']): ?>
                <span class="px-2 py-1 rounded text-xs font-medium <?= $task['priority'] === 'high' ? 'bg-red-100 text-red-700' : ($task['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') ?>">
                    <?= ucfirst($task['priority']) ?>
                </span>
            <?php endif; ?>
        </div>
        
        <h1 class="text-2xl font-bold text-white mb-3"><?= htmlspecialchars($task['title']) ?></h1>
        
        <?php if ($task['description']): ?>
            <div class="bg-white/5 rounded-lg p-4 mb-4">
                <h3 class="text-sm font-medium text-white/80 mb-2">Beschreibung</h3>
                <p class="text-white/70 whitespace-pre-line"><?= htmlspecialchars($task['description']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Task Meta Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white/5 rounded-lg p-4">
            <h3 class="text-sm font-medium text-white/80 mb-2">Ersteller</h3>
            <div class="flex items-center gap-2">
                <div class="user-avatar h-8 w-8 rounded-full flex items-center justify-center text-xs">
                    <?= htmlspecialchars(substr($task['creator_name'], 0, 2)) ?>
                </div>
                <span class="text-white"><?= htmlspecialchars($task['creator_name']) ?></span>
            </div>
        </div>

        <div class="bg-white/5 rounded-lg p-4">
            <h3 class="text-sm font-medium text-white/80 mb-2">Zugewiesen an</h3>
            <?php if ($task['assigned_group_id']): ?>
                <div class="flex items-center gap-2">
                    <div class="group-badge px-2 py-1 rounded-full text-xs">
                        Gruppe: <?= htmlspecialchars($task['group_name']) ?>
                    </div>
                </div>
            <?php elseif ($task['assigned_to']): ?>
                <div class="flex items-center gap-2">
                    <div class="user-avatar h-8 w-8 rounded-full flex items-center justify-center text-xs">
                        <?= htmlspecialchars(substr($task['assignee_name'], 0, 2)) ?>
                    </div>
                    <span class="text-white"><?= htmlspecialchars($task['assignee_name']) ?></span>
                </div>
            <?php else: ?>
                <span class="text-white/60">Nicht zugewiesen</span>
            <?php endif; ?>
        </div>

        <?php if ($task['due_date']): ?>
            <div class="bg-white/5 rounded-lg p-4">
                <h3 class="text-sm font-medium text-white/80 mb-2">Fälligkeitsdatum</h3>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-white"><?= date('d.m.Y', strtotime($task['due_date'])) ?></span>
                    <?php if (strtotime($task['due_date']) < time()): ?>
                        <span class="overdue-badge px-2 py-1 rounded-full text-xs ml-2">Überfällig</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white/5 rounded-lg p-4">
            <h3 class="text-sm font-medium text-white/80 mb-2">Erstellt am</h3>
            <span class="text-white"><?= date('d.m.Y H:i', strtotime($task['created_at'])) ?></span>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 pt-4 border-t border-white/10">
        <button onclick="editTask(<?= $task['id'] ?>)" class="done-button px-4 py-2 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Bearbeiten
        </button>
        
        <?php if ($task['status'] !== 'done'): ?>
            <button onclick="markAsDone(<?= $task['id'] ?>)" class="done-button px-4 py-2 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Als erledigt markieren
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
function editTask(taskId) {
    // Close the split screen and open edit modal
    closeTaskDetail();
    setTimeout(() => {
        openTaskModal(taskId);
    }, 300);
}

function markAsDone(taskId) {
    if (confirm('Aufgabe als erledigt markieren?')) {
        window.location.href = `/inbox.php?done=${taskId}`;
    }
}
</script>
