<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';

requireLogin();
$userId = $_SESSION['user_id'];
$user = getUser();

// Make sure calendar tables exist
require_once __DIR__ . '/../database/calendar_tables.php';

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$redirectParams = isset($_GET['redirect']) ? $_GET['redirect'] : 'view=month';
$success = '';
$errors = [];

// Fetch the event
$stmt = $pdo->prepare("
    SELECT e.*, 
           creator.username AS creator_name,
           assignee.username AS assignee_name,
           g.name AS group_name
    FROM events e
    LEFT JOIN users creator ON creator.id = e.created_by
    LEFT JOIN users assignee ON assignee.id = e.assigned_to
    LEFT JOIN user_groups g ON g.id = e.assigned_group_id
    WHERE e.id = ? AND (
        e.created_by = ? OR 
        e.assigned_to = ? OR
        e.assigned_group_id IN (
            SELECT group_id FROM user_group_members WHERE user_id = ?
        )
    )
");
$stmt->execute([$eventId, $userId, $userId, $userId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if event exists and user can edit it
if (!$event) {
    header('Location: /calendar.php?error=not_found');
    exit;
}

// Only the creator or an admin can edit events
$canEdit = ($userId == $event['created_by']) || isAdmin();
if (!$canEdit) {
    header('Location: /calendar.php?error=permission_denied');
    exit;
}

// Get all users for assignment dropdown
$stmtUsers = $pdo->query("SELECT id, username FROM users ORDER BY username");
$allUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Load user's groups for filter dropdown
$stmtGroups = $pdo->prepare("
    SELECT g.id, g.name 
    FROM user_groups g
    JOIN user_group_members m ON g.id = m.group_id
    WHERE m.user_id = ?
    ORDER BY g.name
");
$stmtGroups->execute([$userId]);
$userGroups = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $startTime = $_POST['start_time'] ?? null;
    $endTime = $_POST['end_time'] ?? null;
    $allDay = isset($_POST['all_day']) ? 1 : 0;
    $assignmentType = $_POST['assignment_type'] ?? 'none';
    $assignedTo = ($assignmentType === 'user') ? ($_POST['assigned_to'] ?? null) : null;
    $assignedGroupId = ($assignmentType === 'group') ? ($_POST['assigned_group_id'] ?? null) : null;
    $color = $_POST['color'] ?? '#4A90E2';
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Der Titel ist erforderlich.';
    }
    if (empty($eventDate)) {
        $errors[] = 'Das Datum ist erforderlich.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE events SET
                    title = ?, 
                    description = ?, 
                    location = ?, 
                    event_date = ?, 
                    start_time = ?, 
                    end_time = ?, 
                    all_day = ?,
                    assigned_to = ?, 
                    assigned_group_id = ?, 
                    color = ?
                WHERE id = ? AND created_by = ?
            ");
            
            $stmt->execute([
                $title, $description, $location, $eventDate,
                $startTime, $endTime, $allDay,
                $assignedTo, $assignedGroupId, $color,
                $eventId, $userId
            ]);
            
            $success = 'Termin erfolgreich aktualisiert!';
            
            // Redirect to calendar
            header('Location: /calendar.php?' . $redirectParams . '&success=updated');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Datenbankfehler: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Termin bearbeiten | Private Vault</title>
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
    
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: white;
    }
    
    .glass-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      color: white;
    }
    
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
  </style>
</head>
<body class="min-h-screen">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>
  
  <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-2xl mx-auto">
      <div class="mb-6">
        <a href="/calendar.php" class="text-white/70 flex items-center hover:text-white transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Zurück zum Kalender
        </a>
      </div>
    
      <h1 class="text-2xl font-bold text-white mb-6">Termin bearbeiten</h1>
      
      <?php if (!empty($errors)): ?>
        <div class="bg-red-500/20 border border-red-400/30 text-red-300 px-4 py-3 rounded-lg mb-6">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <form method="post" class="glass-card p-6 space-y-4">
        <div>
          <label for="title" class="block text-sm font-medium text-white/80 mb-1">Titel *</label>
          <input type="text" id="title" name="title" required 
                 value="<?= htmlspecialchars($event['title']) ?>"
                 class="w-full px-4 py-2 glass-input">
        </div>
        
        <div>
          <label for="description" class="block text-sm font-medium text-white/80 mb-1">Beschreibung</label>
          <textarea id="description" name="description" rows="3" 
                    class="w-full px-4 py-2 glass-input"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
        </div>
        
        <div>
          <label for="location" class="block text-sm font-medium text-white/80 mb-1">Ort</label>
          <input type="text" id="location" name="location" 
                 value="<?= htmlspecialchars($event['location'] ?? '') ?>"
                 class="w-full px-4 py-2 glass-input">
        </div>
        
        <div>
          <label for="event_date" class="block text-sm font-medium text-white/80 mb-1">Datum *</label>
          <input type="date" id="event_date" name="event_date" required 
                 value="<?= htmlspecialchars($event['event_date']) ?>"
                 class="w-full px-4 py-2 glass-input">
        </div>
        
        <div class="flex items-center mb-4">
          <input type="checkbox" id="all_day" name="all_day" 
                 <?= $event['all_day'] ? 'checked' : '' ?>
                 class="h-4 w-4 text-[#4A90E2] focus:ring-[#4A90E2]">
          <label for="all_day" class="ml-2 text-sm text-white/80">Ganztägiger Termin</label>
        </div>
        
        <div id="timeSelectionGroup" class="grid grid-cols-2 gap-4 <?= $event['all_day'] ? 'hidden' : '' ?>">
          <div>
            <label for="start_time" class="block text-sm font-medium text-white/80 mb-1">Startzeit</label>
            <input type="time" id="start_time" name="start_time" 
                   value="<?= $event['start_time'] ? substr($event['start_time'], 0, 5) : '' ?>"
                   class="w-full px-4 py-2 glass-input">
          </div>
          
          <div>
            <label for="end_time" class="block text-sm font-medium text-white/80 mb-1">Endzeit</label>
            <input type="time" id="end_time" name="end_time" 
                   value="<?= $event['end_time'] ? substr($event['end_time'], 0, 5) : '' ?>"
                   class="w-full px-4 py-2 glass-input">
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Zuweisung</label>
          <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
              <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="none" 
                       <?= (!$event['assigned_to'] && !$event['assigned_group_id']) ? 'checked' : '' ?>
                       onclick="toggleAssignmentType('none')"
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]">
                <span class="ml-2 text-sm">Keine</span>
              </label>
            </div>
            <div>
              <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="user"
                       <?= $event['assigned_to'] ? 'checked' : '' ?>
                       onclick="toggleAssignmentType('user')"
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]">
                <span class="ml-2 text-sm">Benutzer</span>
              </label>
            </div>
            <div>
              <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="group"
                       <?= $event['assigned_group_id'] ? 'checked' : '' ?>
                       onclick="toggleAssignmentType('group')"
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]">
                <span class="ml-2 text-sm">Gruppe</span>
              </label>
            </div>
          </div>
          
          <!-- User Assignment -->
          <div id="user_assignment" class="<?= $event['assigned_to'] ? '' : 'hidden' ?>">
            <label for="assigned_to" class="block text-sm font-medium text-white/80 mb-1">Benutzer auswählen</label>
            <select id="assigned_to" name="assigned_to" 
                    class="w-full px-4 py-2 glass-input">
              <option value="" disabled>Bitte auswählen...</option>
              <?php foreach ($allUsers as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $event['assigned_to'] == $u['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['username']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Group Assignment -->
          <div id="group_assignment" class="<?= $event['assigned_group_id'] ? '' : 'hidden' ?>">
            <label for="assigned_group_id" class="block text-sm font-medium text-white/80 mb-1">Gruppe auswählen</label>
            <select id="assigned_group_id" name="assigned_group_id"
                    class="w-full px-4 py-2 glass-input">
              <option value="" disabled>Bitte auswählen...</option>
              <?php foreach ($userGroups as $group): ?>
                <option value="<?= $group['id'] ?>" <?= $event['assigned_group_id'] == $group['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($group['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        
        <div>
          <label for="color" class="block text-sm font-medium text-white/80 mb-1">Farbe</label>
          <div class="flex space-x-2">
            <input type="color" id="color" name="color" value="<?= htmlspecialchars($event['color']) ?>"
                   class="h-8 w-8 rounded cursor-pointer">
            <input type="text" id="colorText" value="<?= htmlspecialchars($event['color']) ?>" disabled
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50">
          </div>
        </div>
        
        <div class="pt-4 flex justify-end space-x-3">
          <a href="/calendar.php" class="px-4 py-2 bg-white/10 border border-white/20 text-white rounded-lg hover:bg-white/15 transition-colors">
            Abbrechen
          </a>
          <button type="submit" 
                  class="px-4 py-2 bg-blue-600/80 text-white rounded-lg hover:bg-blue-600/90 transition-colors">
            Speichern
          </button>
        </div>
      </form>
    </div>
  </main>
  
  <script>
    // Toggle time selection based on all-day checkbox
    document.getElementById('all_day').addEventListener('change', function() {
      document.getElementById('timeSelectionGroup').classList.toggle('hidden', this.checked);
    });
    
    // Update color text when color is changed
    document.getElementById('color').addEventListener('input', function(e) {
      document.getElementById('colorText').value = e.target.value;
    });
    
    // Assignment type toggle
    function toggleAssignmentType(type) {
      document.getElementById('user_assignment').classList.toggle('hidden', type !== 'user');
      document.getElementById('group_assignment').classList.toggle('hidden', type !== 'group');
      
      // Update required attributes
      const userSelect = document.getElementById('assigned_to');
      const groupSelect = document.getElementById('assigned_group_id');
      
      userSelect.required = (type === 'user');
      groupSelect.required = (type === 'group');
    }
  </script>
</body>
</html>
