<?php
// src/controllers/create_task.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Fallback for debugging (remove or replace in production)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = ['id' => 1];
}

$allUsers = [];
$success = '';
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

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, assigned_to, due_date, status, user_id, created_by) VALUES (?, ?, ?, ?, 'open', ?, ?)");
        $result = $stmt->execute([
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            $_POST['assigned_to'] ?? null,
            $_POST['due_date'] ?? null,
            $_SESSION['user']['id'],
            $_SESSION['user']['id']
        ]);
        error_log("DEBUG: Insert result: " . var_export($result, true));
        error_log("DEBUG: Rows inserted: " . $stmt->rowCount());
        if ($result && $stmt->rowCount() > 0) {
            $success = 'Task created successfully.';
            header('Location: /dashboard.php');
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
?>
