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

<div class="task-detail-form">
  <div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-semibold text-white"><?= $isNew ? 'Neue Aufgabe' : 'Aufgabe bearbeiten' ?></h3>
    <button type="button" data-action="close-modal" 
            class="text-white/60 hover:text-white transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  <form id="taskForm" class="space-y-4">
    <?php if (!$isNew): ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']) ?>">
    <?php endif; ?>
    
    <input type="hidden" name="status" value="<?= htmlspecialchars($defaultStatus) ?>">
    
    <div>
      <label for="title" class="block text-sm font-medium text-white/90 mb-2">Titel *</label>
      <input type="text" id="title" name="title" required 
             value="<?= $task ? htmlspecialchars($task['title']) : '' ?>" 
             class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/15">
    </div>
    
    <div>
      <label for="description" class="block text-sm font-medium text-white/90 mb-2">Beschreibung</label>
      <textarea id="description" name="description" rows="4" 
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/15"><?= $task ? htmlspecialchars($task['description']) : '' ?></textarea>
    </div>
    
    <div>
      <label for="due_date" class="block text-sm font-medium text-white/90 mb-2">Fällig am</label>
      <input type="date" id="due_date" name="due_date" 
             value="<?= $task && $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : '' ?>" 
             class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/15">
    </div>
    
    <div>
      <label for="priority" class="block text-sm font-medium text-white/90 mb-2">Priorität</label>
      <select id="priority" name="priority" 
              class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/15">
        <option value="low" <?= ($task && $task['priority'] === 'low') ? 'selected' : '' ?>>Niedrig</option>
        <option value="medium" <?= ($task && $task['priority'] === 'medium') || !$task ? 'selected' : '' ?>>Mittel</option>
        <option value="high" <?= ($task && $task['priority'] === 'high') ? 'selected' : '' ?>>Hoch</option>
      </select>
    </div>
    
    <!-- Assignment Section -->
    <div class="border-t border-white/10 pt-4">
      <h4 class="text-sm font-medium text-white/90 mb-3">Zuweisung</h4>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="inline-flex items-center">
            <input type="radio" name="assignment_type" value="user" checked 
                   onclick="toggleAssignmentType('user')"
                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            <span class="ml-2 text-white/90">Einzelner Benutzer</span>
          </label>
        </div>
        <div>
          <label class="inline-flex items-center">
            <input type="radio" name="assignment_type" value="group"
                   onclick="toggleAssignmentType('group')"
                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            <span class="ml-2 text-white/90">Benutzergruppe</span>
          </label>
        </div>
      </div>
      
      <!-- User Assignment -->
      <div id="user_assignment" class="mb-4">
        <label for="assigned_to" class="block text-sm font-medium text-white/90 mb-2">Benutzer auswählen</label>
        <select id="assigned_to" name="assigned_to" 
                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/15">
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
        <label for="assigned_group_id" class="block text-sm font-medium text-white/90 mb-2">Gruppe auswählen</label>
        <select id="assigned_group_id" name="assigned_group_id"
                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/15">
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
                class="px-4 py-2 bg-white/10 border border-white/20 text-white rounded-lg hover:bg-white/20 transition-colors">
          Abbrechen
        </button>
        <button type="submit" 
                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all">
          Speichern
        </button>
      </div>
    </div>
  </form>
</div>

<script>
  function toggleAssignmentType(type) {
    document.getElementById('user_assignment').classList.toggle('hidden', type !== 'user');
    document.getElementById('group_assignment').classList.toggle('hidden', type !== 'group');
    
    // Update required attributes
    document.getElementById('assigned_to').required = (type === 'user');
    document.getElementById('assigned_group_id').required = (type === 'group');
  }
</script>
