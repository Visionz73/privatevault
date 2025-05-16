// src/controllers/create_task.php
<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Debug fallback: set a user_id if none exists
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$allUsers = [];
$errors = [];

// Load users to populate the "assigned_to" select field
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading users: ' . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("DEBUG: POST received in create_task");
    error_log("DEBUG: Title: " . ($_POST['title'] ?? 'not provided'));
    error_log("DEBUG: Description: " . ($_POST['description'] ?? 'not provided'));
    error_log("DEBUG: Assigned_to: " . ($_POST['assigned_to'] ?? 'not provided'));
    error_log("DEBUG: Due_date: " . ($_POST['due_date'] ?? 'not provided'));

    // Fallback: if no assigned_to provided, assign to current user
    $assignedTo = $_POST['assigned_to'] ?? $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO tasks 
                (title, description, assigned_to, due_date, status, user_id, created_by) 
            VALUES 
                (?, ?, ?, ?, 'open', ?, ?)
        ");
        $result = $stmt->execute([
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            $assignedTo,
            $_POST['due_date'] ?? null,
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);
        error_log("DEBUG: Insert result: " . var_export($result, true));
        error_log("DEBUG: Rows inserted: " . $stmt->rowCount());

        if ($result && $stmt->rowCount() > 0) {
            // Redirect to inbox to see the new task
            header('Location: /src/controllers/inbox.php');
            exit;
        } else {
            $errors[] = 'Task was not created. Check your input values.';
        }
    } catch (PDOException $e) {
        error_log("DEBUG: PDOException in create_task: " . $e->getMessage());
        $errors[] = 'Error creating task: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/../../templates/create_task.php';
