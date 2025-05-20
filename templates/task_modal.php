<?php
// templates/task_modal.php - Modal for creating/editing tasks
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
?>

<h3 class="text-xl font-semibold text-gray-800 mb-4"><?= $isNew ? 'Neue Aufgabe' : 'Aufgabe bearbeiten' ?></h3>

<form id="taskForm" class="space-y-4">
    <?php if (!$isNew): ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']) ?>">
    <?php endif; ?>
    
    <input type="hidden" name="status" value="<?= htmlspecialchars($defaultStatus) ?>">
    
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
        </select>
    </div>
    
    <!-- Assignment Section -->
    <div class="border-t border-gray-200 pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Zuweisung</label>
        
        <div class="flex space-x-4 mb-3">
            <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="user" class="h-4 w-4 text-blue-600"
                       <?= $assignmentType === 'user' ? 'checked' : '' ?>
                       onclick="toggleAssignmentType('user')">
                <span class="ml-2 text-sm text-gray-700">Benutzer</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="group" class="h-4 w-4 text-blue-600"
                       <?= $assignmentType === 'group' ? 'checked' : '' ?>
                       onclick="toggleAssignmentType('group')">
                <span class="ml-2 text-sm text-gray-700">Gruppe</span>
            </label>
        </div>
        
        <div id="user_assignment" class="<?= $assignmentType === 'group' ? 'hidden' : '' ?>">
            <select id="assigned_to" name="assigned_to" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Benutzer auswählen --</option>
                <?php foreach ($allUsers as $user): ?>
                <option value="<?= $user['id'] ?>" 
                        <?= ($task && $task['assigned_to'] == $user['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['username']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="group_assignment" class="<?= $assignmentType === 'user' ? 'hidden' : '' ?>">
            <select id="assigned_group" name="assigned_group" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Gruppe auswählen --</option>
                <?php foreach ($allGroups as $group): ?>
                <option value="<?= $group['id'] ?>" 
                        <?= ($task && $task['assigned_group_id'] == $group['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['name']) ?> (<?= $group['member_count'] ?> Mitglieder)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <!-- Recurrence Options -->
    <div class="border-t border-gray-200 pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Wiederholung</label>
        <select id="recurrence_type" name="recurrence_type" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                onchange="toggleRecurrenceOptions()">
            <option value="none" <?= (!$task || $task['recurrence_type'] === 'none') ? 'selected' : '' ?>>Keine</option>
            <option value="daily" <?= ($task && $task['recurrence_type'] === 'daily') ? 'selected' : '' ?>>Täglich</option>
            <option value="weekly" <?= ($task && $task['recurrence_type'] === 'weekly') ? 'selected' : '' ?>>Wöchentlich</option>
            <option value="monthly" <?= ($task && $task['recurrence_type'] === 'monthly') ? 'selected' : '' ?>>Monatlich</option>
        </select>
        
        <div id="recurrence_options" class="mt-3 space-y-3 <?= (!$task || $task['recurrence_type'] === 'none') ? 'hidden' : '' ?>">
            <div>
                <label for="recurrence_interval" class="block text-sm font-medium text-gray-700 mb-1">Intervall</label>
                <input type="number" id="recurrence_interval" name="recurrence_interval" min="1" max="365"
                       value="<?= $task && isset($task['recurrence_interval']) ? $task['recurrence_interval'] : '1' ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="recurrence_end_date" class="block text-sm font-medium text-gray-700 mb-1">Enddatum (optional)</label>
                <input type="date" id="recurrence_end_date" name="recurrence_end_date"
                       value="<?= $task && isset($task['recurrence_end_date']) ? date('Y-m-d', strtotime($task['recurrence_end_date'])) : '' ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        <button type="button" onclick="closeModal()" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none">
            Abbrechen
        </button>
        <button type="submit" 
                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none">
            <?= $isNew ? 'Erstellen' : 'Speichern' ?>
        </button>
    </div>
</form>

<script>
    function toggleAssignmentType(type) {
        if (type === 'user') {
            document.getElementById('user_assignment').classList.remove('hidden');
            document.getElementById('group_assignment').classList.add('hidden');
        } else {
            document.getElementById('user_assignment').classList.add('hidden');
            document.getElementById('group_assignment').classList.remove('hidden');
        }
    }
    
    function toggleRecurrenceOptions() {
        const recurrenceType = document.getElementById('recurrence_type').value;
        const recurrenceOptions = document.getElementById('recurrence_options');
        
        if (recurrenceType === 'none') {
            recurrenceOptions.classList.add('hidden');
        } else {
            recurrenceOptions.classList.remove('hidden');
        }
    }
    
    function closeModal() {
        document.getElementById('taskModal').classList.add('hidden');
        document.getElementById('taskModal').classList.remove('flex');
    }
</script>
