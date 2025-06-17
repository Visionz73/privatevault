<?php
// templates/task_modal.php - Enhanced modal for creating/editing tasks
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Nicht authentifiziert";
    exit;
}

$task = null;
$defaultStatus = $_GET['status'] ?? 'todo';
$isNew = true;

// If task ID is provided, load existing task data
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    $isNew = false;
    
    try {
        $stmt = $pdo->prepare("
            SELECT t.*, u.username, g.name as group_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN user_groups g ON t.assigned_group_id = g.id
            WHERE t.id = ?
        ");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$task) {
            echo "Aufgabe nicht gefunden.";
            exit;
        }
        
        $defaultStatus = $task['status'];
    } catch (PDOException $e) {
        echo "Datenbankfehler: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

// Get all users for assignment
$allUsers = [];
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Just silently fail, we can handle empty user list in the UI
}

// Get all groups for assignment
$allGroups = [];
try {
    $stmt = $pdo->query("
        SELECT g.id, g.name, COUNT(m.user_id) as member_count 
        FROM user_groups g 
        LEFT JOIN user_group_members m ON g.id = m.group_id 
        GROUP BY g.id 
        ORDER BY g.name
    ");
    $allGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Just silently fail, we can handle empty group list in the UI
}

// Determine assignment type
$assignmentType = 'user';
if ($task) {
    if (!empty($task['assigned_group_id'])) {
        $assignmentType = 'group';
    }
}

// Load existing subtasks if editing
$subtasks = [];
if (!$isNew) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM task_subtasks WHERE task_id = ? ORDER BY sort_order ASC, created_at ASC");
        $stmt->execute([$taskId]);
        $subtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Subtasks table might not exist yet
    }
}
?>

<div class="max-h-[90vh] overflow-y-auto">
    <h3 class="text-xl font-semibold text-gray-800 mb-4"><?= $isNew ? 'Neue Aufgabe' : 'Aufgabe bearbeiten' ?></h3>

    <form id="taskForm" class="space-y-6">
        <?php if (!$isNew): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']) ?>">
        <?php endif; ?>
        
        <input type="hidden" name="status" value="<?= htmlspecialchars($defaultStatus) ?>">
        
        <!-- Basic Task Information -->
        <div class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                <input type="text" id="title" name="title" required 
                       value="<?= $task ? htmlspecialchars($task['title']) : '' ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                <textarea id="description" name="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $task ? htmlspecialchars($task['description']) : '' ?></textarea>
            </div>
        </div>

        <!-- Task Settings -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fällig am</label>
                <input type="date" id="due_date" name="due_date" 
                       value="<?= $task && $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : '' ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priorität</label>
                <select id="priority" name="priority" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="low" <?= ($task && $task['priority'] === 'low') ? 'selected' : '' ?>>Niedrig</option>
                    <option value="medium" <?= ($task && $task['priority'] === 'medium') || !$task ? 'selected' : '' ?>>Mittel</option>
                    <option value="high" <?= ($task && $task['priority'] === 'high') ? 'selected' : '' ?>>Hoch</option>
                    <option value="urgent" <?= ($task && $task['priority'] === 'urgent') ? 'selected' : '' ?>>Dringend</option>
                </select>
            </div>
        </div>

        <!-- Budget Planning -->
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Budget & Aufwand
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="estimated_budget" class="block text-sm font-medium text-gray-700 mb-1">Geschätztes Budget (€)</label>
                    <input type="number" id="estimated_budget" name="estimated_budget" step="0.01" min="0"
                           value="<?= $task ? htmlspecialchars($task['estimated_budget'] ?? '') : '' ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-1">Geschätzte Stunden</label>
                    <input type="number" id="estimated_hours" name="estimated_hours" step="0.5" min="0"
                           value="<?= $task ? htmlspecialchars($task['estimated_hours'] ?? '') : '' ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategorie</label>
                    <select id="category" name="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Keine Kategorie</option>
                        <option value="development" <?= ($task && $task['category'] === 'development') ? 'selected' : '' ?>>Entwicklung</option>
                        <option value="design" <?= ($task && $task['category'] === 'design') ? 'selected' : '' ?>>Design</option>
                        <option value="marketing" <?= ($task && $task['category'] === 'marketing') ? 'selected' : '' ?>>Marketing</option>
                        <option value="administration" <?= ($task && $task['category'] === 'administration') ? 'selected' : '' ?>>Administration</option>
                        <option value="meeting" <?= ($task && $task['category'] === 'meeting') ? 'selected' : '' ?>>Meeting</option>
                        <option value="research" <?= ($task && $task['category'] === 'research') ? 'selected' : '' ?>>Recherche</option>
                        <option value="other" <?= ($task && $task['category'] === 'other') ? 'selected' : '' ?>>Sonstiges</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Subtasks / Checklist -->
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                To-Do Liste / Unteraufgaben
            </h4>
            
            <div id="subtasksList" class="space-y-2 mb-3">
                <?php foreach ($subtasks as $index => $subtask): ?>
                <div class="flex items-center space-x-2 subtask-item" data-index="<?= $index ?>">
                    <input type="checkbox" class="subtask-checkbox" 
                           <?= $subtask['is_completed'] ? 'checked' : '' ?>
                           data-subtask-id="<?= $subtask['id'] ?>">
                    <input type="text" name="subtasks[<?= $index ?>][title]" 
                           value="<?= htmlspecialchars($subtask['title']) ?>"
                           class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
                           placeholder="Unteraufgabe...">
                    <input type="hidden" name="subtasks[<?= $index ?>][id]" value="<?= $subtask['id'] ?>">
                    <input type="hidden" name="subtasks[<?= $index ?>][is_completed]" value="<?= $subtask['is_completed'] ? '1' : '0' ?>">
                    <button type="button" class="text-red-500 hover:text-red-700 remove-subtask" aria-label="Entfernen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" id="addSubtask" class="w-full px-3 py-2 border border-dashed border-gray-300 rounded-md text-sm text-gray-600 hover:border-gray-400 hover:text-gray-700">
                + Unteraufgabe hinzufügen
            </button>
        </div>

        <!-- Assignment Section -->
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Zuweisung</h4>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="inline-flex items-center">
                        <input type="radio" name="assignment_type" value="user" <?= $assignmentType === 'user' ? 'checked' : '' ?>
                               onclick="toggleAssignmentType('user')"
                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2">Einzelner Benutzer</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="radio" name="assignment_type" value="group" <?= $assignmentType === 'group' ? 'checked' : '' ?>
                               onclick="toggleAssignmentType('group')"
                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2">Benutzergruppe</span>
                    </label>
                </div>
            </div
            
            <!-- User Assignment -->
            <div id="user_assignment" class="mb-4 <?= $assignmentType !== 'user' ? 'hidden' : '' ?>">
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Benutzer auswählen</label>
                <select id="assigned_to" name="assigned_to" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Nicht zugewiesen</option>
                    <?php foreach ($allUsers ?? [] as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= isset($task) && $task['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Group Assignment -->
            <div id="group_assignment" class="mb-4 <?= $assignmentType !== 'group' ? 'hidden' : '' ?>">
                <label for="assigned_group_id" class="block text-sm font-medium text-gray-700 mb-1">Gruppe auswählen</label>
                <select id="assigned_group_id" name="assigned_group_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Nicht zugewiesen</option>
                    <?php foreach ($allGroups ?? [] as $group): ?>
                        <option value="<?= $group['id'] ?>" <?= isset($task) && $task['assigned_group_id'] == $group['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($group['name']) ?> (<?= $group['member_count'] ?> Mitglieder)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Tags -->
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Tags</h4>
            <input type="text" id="tags" name="tags" 
                   value="<?= $task ? htmlspecialchars($task['tags'] ?? '') : '' ?>"
                   placeholder="Tags durch Komma getrennt..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">z.B. wichtig, kunde, deadline</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
            <button type="button" onclick="closeTaskModal()" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Abbrechen
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <?= $isNew ? 'Erstellen' : 'Speichern' ?>
            </button>
        </div>
    </form>
</div>

<script>
let subtaskCounter = <?= count($subtasks) ?>;

function toggleAssignmentType(type) {
    document.getElementById('user_assignment').classList.toggle('hidden', type !== 'user');
    document.getElementById('group_assignment').classList.toggle('hidden', type !== 'group');
}

// Subtask management
document.getElementById('addSubtask').addEventListener('click', function() {
    const subtasksList = document.getElementById('subtasksList');
    const newSubtask = document.createElement('div');
    newSubtask.className = 'flex items-center space-x-2 subtask-item';
    newSubtask.setAttribute('data-index', subtaskCounter);
    
    newSubtask.innerHTML = `
        <input type="checkbox" class="subtask-checkbox">
        <input type="text" name="subtasks[${subtaskCounter}][title]" 
               class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
               placeholder="Unteraufgabe...">
        <input type="hidden" name="subtasks[${subtaskCounter}][is_completed]" value="0">
        <button type="button" class="text-red-500 hover:text-red-700 remove-subtask" aria-label="Entfernen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    subtasksList.appendChild(newSubtask);
    subtaskCounter++;
    
    // Focus on the new input
    newSubtask.querySelector('input[type="text"]').focus();
});

// Handle subtask checkbox changes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('subtask-checkbox')) {
        const hiddenInput = e.target.parentElement.querySelector('input[type="hidden"]');
        hiddenInput.value = e.target.checked ? '1' : '0';
    }
});

// Handle subtask removal
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-subtask')) {
        e.target.closest('.subtask-item').remove();
    }
});

// Initialize assignment type toggle
document.addEventListener('DOMContentLoaded', function() {
    const checkedAssignmentType = document.querySelector('input[name="assignment_type"]:checked');
    if (checkedAssignmentType) {
        toggleAssignmentType(checkedAssignmentType.value);
    }
});

// Handle form submission
document.getElementById('taskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isEdit = formData.get('id');
    const url = isEdit ? "/src/api/task_update.php" : "/src/api/task_create.php";
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeTaskModal();
            // Reload the page to show the new/updated task
            window.location.reload();
        } else {
            alert(data.error || 'Es ist ein Fehler aufgetreten');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Es ist ein Fehler aufgetreten');
    });
});
</script>
