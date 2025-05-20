<?php
// Debug-Modus aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/db.php';

requireLogin();
$userId = $_SESSION['user_id'];

// Default values
$defaultDate = $_GET['date'] ?? date('Y-m-d');
$defaultStartTime = $_GET['time'] ?? date('H:00');
$defaultEndTime = date('H:i', strtotime('+1 hour', strtotime($defaultDate . ' ' . $defaultStartTime)));

// Get user groups for assignment
$stmtGroups = $pdo->prepare("
    SELECT g.id, g.name 
    FROM user_groups g
    JOIN user_group_members m ON g.id = m.group_id
    WHERE m.user_id = ?
");
$stmtGroups->execute([$userId]);
$userGroups = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);

// Get all users for assignment
$stmtUsers = $pdo->query("SELECT id, username FROM users ORDER BY username");
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$messageType = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log submission data
    error_log('Event submission data: ' . print_r($_POST, true));
    
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $startTime = trim($_POST['start_time'] ?? '');
    $endDate = trim($_POST['end_date'] ?? $startDate);
    $endTime = trim($_POST['end_time'] ?? date('H:i', strtotime('+1 hour', strtotime("$startDate $startTime"))));
    $assignedTo = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;
    $assignedGroup = !empty($_POST['assigned_group']) ? intval($_POST['assigned_group']) : null;
    $color = trim($_POST['color'] ?? '#3498db');
    
    // Validate required fields
    $errors = [];
    
    if (empty($title)) $errors[] = "Title is required";
    if (empty($startDate)) $errors[] = "Start date is required";
    if (empty($startTime)) $errors[] = "Start time is required";
    if (empty($endDate)) $errors[] = "End date is required";
    if (empty($endTime)) $errors[] = "End time is required";
    
    // Format dates for MySQL
    $startDateTime = $startDate . ' ' . $startTime . ':00';
    $endDateTime = $endDate . ' ' . $endTime . ':00';
    
    if (empty($errors)) {
        try {
            // Insert into database using direct values to minimize errors
            $stmt = $pdo->prepare("
                INSERT INTO events (
                    title, description, location, 
                    start_datetime, end_datetime, 
                    created_by, assigned_to, assigned_group_id, 
                    color, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, 
                    ?, ?, 
                    ?, ?, ?, 
                    ?, NOW(), NOW()
                )
            ");
            
            // Set null for empty fields
            if ($assignedTo === 0) $assignedTo = null;
            if ($assignedGroup === 0) $assignedGroup = null;
            
            $result = $stmt->execute([
                $title, $description, $location,
                $startDateTime, $endDateTime,
                $userId, $assignedTo, $assignedGroup,
                $color
            ]);
            
            if ($result) {
                // Redirect to calendar view
                header("Location: calendar.php?view=day&date=$startDate&event_added=1");
                exit;
            } else {
                $message = "Failed to save event. Database error.";
                $messageType = "danger";
                error_log("Database error on event save: " . print_r($stmt->errorInfo(), true));
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = "danger";
            error_log("PDO Exception on event save: " . $e->getMessage());
        }
    } else {
        $message = "Please fix the following errors: " . implode(", ", $errors);
        $messageType = "warning";
    }
}

$pageTitle = 'Add Event';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2><i class="fas fa-calendar-plus"></i> Add New Event</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?= $messageType ?>">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="" id="event-form">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title*</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date*</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                    value="<?= htmlspecialchars($_POST['start_date'] ?? $defaultDate) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time*</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" 
                                    value="<?= htmlspecialchars($_POST['start_time'] ?? $defaultStartTime) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date*</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                    value="<?= htmlspecialchars($_POST['end_date'] ?? $defaultDate) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time*</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" 
                                    value="<?= htmlspecialchars($_POST['end_time'] ?? $defaultEndTime) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label">Assign to User</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="0">None</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="assigned_group" class="form-label">Assign to Group</label>
                                <select class="form-select" id="assigned_group" name="assigned_group">
                                    <option value="0">None</option>
                                    <?php foreach ($userGroups as $group): ?>
                                        <option value="<?= $group['id'] ?>">
                                            <?= htmlspecialchars($group['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="color" class="form-label">Event Color</label>
                            <input type="color" class="form-control form-control-color" id="color" name="color" 
                                value="<?= htmlspecialchars($_POST['color'] ?? '#3498db') ?>" title="Choose event color">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="calendar.php?view=day&date=<?= urlencode($defaultDate) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Calendar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update end date when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').value = this.value;
    });
    
    // Auto-calculate end time (1 hour later) when start time changes
    document.getElementById('start_time').addEventListener('change', function() {
        const startTime = this.value;
        if (startTime) {
            // Get hours and minutes
            const [hours, minutes] = startTime.split(':').map(Number);
            
            // Add 1 hour
            let newHours = hours + 1;
            if (newHours > 23) newHours = 23;
            
            // Format back to HH:MM
            const newTime = `${newHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            document.getElementById('end_time').value = newTime;
        }
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
