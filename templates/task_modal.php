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
        <h4 class="text-sm font-medium text-gray-700 mb-2">Zuweisung</h4>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="inline-flex items-center">
                    <input type="radio" name="assignment_type" value="user" checked 
                           onclick="toggleAssignmentType('user')"
                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2">Einzelner Benutzer</span>
                </label>
            </div>
            <div>
                <label class="inline-flex items-center">
                    <input type="radio" name="assignment_type" value="group"
                           onclick="toggleAssignmentType('group')"
                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2">Benutzergruppe</span>
                </label>
            </div>
        </div>
        
        <!-- User Assignment -->
        <div id="user_assignment" class="mb-4">
            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Benutzer auswählen</label>
            <select id="assigned_to" name="assigned_to" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Bitte auswählen...</option>
                <?php foreach ($allUsers ?? [] as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= isset($task) && $task['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Group Assignment -->
        <div id="group_assignment" class="mb-4 hidden">
            <label for="assigned_group_id" class="block text-sm font-medium text-gray-700 mb-1">Gruppe auswählen</label>
            <select id="assigned_group_id" name="assigned_group_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Bitte auswählen...</option>
                <?php foreach ($allGroups ?? [] as $group): ?>
                    <option value="<?= $group['id'] ?>" <?= isset($task) && $task['assigned_group_id'] == $group['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($group['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-6">
            <button type="button" data-action="close-modal" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Abbrechen
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Speichern
            </button>
        </div>
    </div>
</form>

<script>
    function toggleAssignmentType(type) {
        document.getElementById('user_assignment').classList.toggle('hidden', type !== 'user');
        document.getElementById('group_assignment').classList.toggle('hidden', type !== 'group');
        
        // Update required attributes
        document.getElementById('assigned_to').required = (type === 'user');
        document.getElementById('assigned_group_id').required = (type === 'group');
    }
</script>
