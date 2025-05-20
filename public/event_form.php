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

// Get any previously submitted form data and errors
$formData = $_SESSION['event_form_data'] ?? [];
$formError = $_SESSION['event_form_error'] ?? null;

// Clear session data after retrieving
unset($_SESSION['event_form_data'], $_SESSION['event_form_error']);

// Extract specific errors if they exist
$errors = $formError['errors'] ?? [];
$generalError = $formError['message'] ?? '';
$debugInfo = $formError['debug_info'] ?? [];

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
                    <?php if (!empty($generalError)): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= htmlspecialchars($generalError) ?>
                            
                            <?php if (DEBUG && !empty($debugInfo)): ?>
                                <hr>
                                <h5>Debug Information:</h5>
                                <pre><?= htmlspecialchars(print_r($debugInfo, true)) ?></pre>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="event_submit.php" id="event-form">
                        <!-- Include original GET parameters -->
                        <?php foreach ($_GET as $key => $value): ?>
                            <input type="hidden" name="_get_<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                        <?php endforeach; ?>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                    id="title" name="title" value="<?= htmlspecialchars($formData['title'] ?? '') ?>" required>
                                <?php if (isset($errors['title'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="<?= htmlspecialchars($formData['location'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date*</label>
                                <input type="date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" 
                                    id="start_date" name="start_date" value="<?= htmlspecialchars($formData['start_date'] ?? $defaultDate) ?>" required>
                                <?php if (isset($errors['start_date'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['start_date']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time*</label>
                                <input type="time" class="form-control <?= isset($errors['start_time']) ? 'is-invalid' : '' ?>" 
                                    id="start_time" name="start_time" value="<?= htmlspecialchars($formData['start_time'] ?? $defaultStartTime) ?>" required>
                                <?php if (isset($errors['start_time'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['start_time']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date*</label>
                                <input type="date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" 
                                    id="end_date" name="end_date" value="<?= htmlspecialchars($formData['end_date'] ?? $defaultDate) ?>" required>
                                <?php if (isset($errors['end_date'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['end_date']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time*</label>
                                <input type="time" class="form-control <?= isset($errors['end_time']) ? 'is-invalid' : '' ?>" 
                                    id="end_time" name="end_time" value="<?= htmlspecialchars($formData['end_time'] ?? $defaultEndTime) ?>" required>
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
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="color" class="form-label">Event Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" 
                                    value="<?= htmlspecialchars($formData['color'] ?? '#3498db') ?>" title="Choose event color">
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
