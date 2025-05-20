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

// Get any form errors or data from session
$formErrors = $_SESSION['event_form_errors'] ?? [];
$formData = $_SESSION['event_form_data'] ?? [];
$successMessage = $_SESSION['event_success'] ?? '';

// Clear session data
unset($_SESSION['event_form_errors'], $_SESSION['event_form_data'], $_SESSION['event_success']);

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
                    <?php if (!empty($formErrors)): ?>
                        <div class="alert alert-danger">
                            <strong>Error!</strong>
                            <ul class="mb-0">
                                <?php foreach ($formErrors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($successMessage) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form with explicit action to save_event.php -->
                    <form method="post" action="save_event.php" id="event-form">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title*</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                value="<?= htmlspecialchars($formData['title'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                value="<?= htmlspecialchars($formData['location'] ?? '') ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date*</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                    value="<?= htmlspecialchars($formData['start_date'] ?? $defaultDate) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time*</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" 
                                    value="<?= htmlspecialchars($formData['start_time'] ?? $defaultStartTime) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date*</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                    value="<?= htmlspecialchars($formData['end_date'] ?? $defaultDate) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time*</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" 
                                    value="<?= htmlspecialchars($formData['end_time'] ?? $defaultEndTime) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label">Assign to User</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="0">None</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= (isset($formData['assigned_to']) && $formData['assigned_to'] == $user['id']) ? 'selected' : '' ?>>
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
                                        <option value="<?= $group['id'] ?>" <?= (isset($formData['assigned_group']) && $formData['assigned_group'] == $group['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($group['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="color" class="form-label">Event Color</label>
                            <input type="color" class="form-control form-control-color" id="color" name="color" 
                                value="<?= htmlspecialchars($formData['color'] ?? '#3498db') ?>" title="Choose event color">
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
            
            <!-- Debug information in development mode -->
            <?php if (defined('DEBUG') && DEBUG): ?>
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    Debug Information
                </div>
                <div class="card-body">
                    <p>To troubleshoot the event saving issue, check the following:</p>
                    <ul>
                        <li>Verify the database table structure matches the query (run: <code>DESCRIBE events</code>)</li>
                        <li>Check error logs at <code>/logs/event_errors.log</code></li>
                        <li>Ensure proper database permissions are set</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
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
