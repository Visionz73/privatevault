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

// Default values
$defaultDate = $_GET['date'] ?? date('Y-m-d');
$defaultStartTime = $_GET['time'] ?? date('H:00');
$defaultEndTime = date('H:i', strtotime('+1 hour', strtotime($defaultDate . ' ' . $defaultStartTime)));

$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log submission data for debugging
    error_log('Event submission data: ' . print_r($_POST, true));
    
    // Validate inputs
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $startTime = trim($_POST['start_time'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    $endTime = trim($_POST['end_time'] ?? '');
    $assignedTo = intval($_POST['assigned_to'] ?? 0);
    $assignedGroup = intval($_POST['assigned_group'] ?? 0);
    $color = trim($_POST['color'] ?? '#3498db');
    
    // Validate required fields
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
        $errors['start_date'] = 'Valid start date is required (YYYY-MM-DD)';
    }
    
    if (empty($startTime) || !preg_match('/^\d{2}:\d{2}$/', $startTime)) {
        $errors['start_time'] = 'Valid start time is required (HH:MM)';
    }
    
    if (empty($endDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        $errors['end_date'] = 'Valid end date is required (YYYY-MM-DD)';
    }
    
    if (empty($endTime) || !preg_match('/^\d{2}:\d{2}$/', $endTime)) {
        $errors['end_time'] = 'Valid end time is required (HH:MM)';
    }
    
    // Check that end datetime is after start datetime
    $startDateTime = strtotime("$startDate $startTime");
    $endDateTime = strtotime("$endDate $endTime");
    
    if ($endDateTime <= $startDateTime) {
        $errors['end_time'] = 'End time must be after start time';
    }
    
    // If no errors, insert the event
    if (empty($errors)) {
        try {
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
            
            $result = $stmt->execute([
                $title, $description, $location, 
                "$startDate $startTime:00", "$endDate $endTime:00", 
                $userId, ($assignedTo > 0) ? $assignedTo : null, ($assignedGroup > 0) ? $assignedGroup : null, 
                $color
            ]);
            
            if ($result) {
                $eventId = $pdo->lastInsertId();
                $success = true;
                
                // Redirect back to calendar
                header("Location: calendar.php?view=day&date=$startDate&event_added=1");
                exit;
            } else {
                $errors['database'] = 'Failed to save event';
                error_log('Event insert statement failed: ' . print_r($stmt->errorInfo(), true));
            }
        } catch (PDOException $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
            error_log('PDO Exception during event creation: ' . $e->getMessage());
        }
    }
}

// Page title
$pageTitle = 'Create New Event';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>Create New Event</h2>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Event created successfully!
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors['database'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['database']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="" id="event-form">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                    id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                                <?php if (isset($errors['title'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date*</label>
                                <input type="date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" 
                                    id="start_date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? $defaultDate) ?>" required>
                                <?php if (isset($errors['start_date'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['start_date']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time*</label>
                                <input type="time" class="form-control <?= isset($errors['start_time']) ? 'is-invalid' : '' ?>" 
                                    id="start_time" name="start_time" value="<?= htmlspecialchars($_POST['start_time'] ?? $defaultStartTime) ?>" required>
                                <?php if (isset($errors['start_time'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['start_time']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date*</label>
                                <input type="date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" 
                                    id="end_date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? $defaultDate) ?>" required>
                                <?php if (isset($errors['end_date'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['end_date']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time*</label>
                                <input type="time" class="form-control <?= isset($errors['end_time']) ? 'is-invalid' : '' ?>" 
                                    id="end_time" name="end_time" value="<?= htmlspecialchars($_POST['end_time'] ?? $defaultEndTime) ?>" required>
                                <?php if (isset($errors['end_time'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['end_time']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label">Assign to User</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="0">None</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= (isset($_POST['assigned_to']) && $_POST['assigned_to'] == $user['id']) ? 'selected' : '' ?>>
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
                                        <option value="<?= $group['id'] ?>" <?= (isset($_POST['assigned_group']) && $_POST['assigned_group'] == $group['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($group['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="color" class="form-label">Event Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" 
                                    value="<?= htmlspecialchars($_POST['color'] ?? '#3498db') ?>" title="Choose event color">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="calendar.php?view=day&date=<?= urlencode($defaultDate) ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Event</button>
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
        if (document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
        }
    });
    
    // Form validation
    document.getElementById('event-form').addEventListener('submit', function(e) {
        const startDate = document.getElementById('start_date').value;
        const startTime = document.getElementById('start_time').value;
        const endDate = document.getElementById('end_date').value;
        const endTime = document.getElementById('end_time').value;
        
        const start = new Date(`${startDate}T${startTime}`);
        const end = new Date(`${endDate}T${endTime}`);
        
        if (end <= start) {
            e.preventDefault();
            alert('End time must be after start time');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
