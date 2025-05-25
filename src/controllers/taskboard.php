<?php
// src/controllers/taskboard.php - Updated controller for the Kanban board

// Database connection
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/utils.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$filterMode = $_GET['filter'] ?? 'all';

// Tasks by Status
$todoTasks = [];
$inProgressTasks = [];
$completedTasks = [];

try {
    // Prepare base query - different query based on filter mode
    if ($filterMode === 'user') {
        // Show only tasks assigned directly to this user
        $baseQuery = "
            SELECT t.*, u.username, g.name as group_name 
            FROM tasks t 
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN user_groups g ON t.assigned_group_id = g.id
            WHERE t.assigned_to = :user_id
        ";
        $params = [':user_id' => $user_id];
    } else if ($filterMode === 'group') {
        // Show only tasks assigned to groups this user belongs to
        $baseQuery = "
            SELECT t.*, u.username, g.name as group_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN user_groups g ON t.assigned_group_id = g.id
            JOIN user_group_members m ON t.assigned_group_id = m.group_id AND m.user_id = :user_id
            WHERE t.assigned_group_id IS NOT NULL
        ";
        $params = [':user_id' => $user_id];
    } else {
        // Show all tasks: created by user, assigned to user, or in user's groups
        $baseQuery = "
            SELECT t.*, u.username, g.name as group_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN user_groups g ON t.assigned_group_id = g.id
            LEFT JOIN user_group_members m ON t.assigned_group_id = m.group_id AND m.user_id = :user_id
            WHERE t.created_by = :user_id OR t.assigned_to = :user_id OR m.user_id = :user_id
            GROUP BY t.id
        ";
        $params = [':user_id' => $user_id];
    }

    // Get To Do tasks
    $todoQuery = $baseQuery . " AND t.status = 'todo' ORDER BY t.due_date ASC";
    $stmt = $pdo->prepare($todoQuery);
    $stmt->execute($params);
    $todoTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get In Progress tasks
    $inProgressQuery = $baseQuery . " AND t.status = 'doing' ORDER BY t.due_date ASC";
    $stmt = $pdo->prepare($inProgressQuery);
    $stmt->execute($params);
    $inProgressTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get Completed tasks
    $completedQuery = $baseQuery . " AND t.status = 'done' ORDER BY t.due_date ASC";
    $stmt = $pdo->prepare($completedQuery);
    $stmt->execute($params);
    $completedTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database errors
    error_log("Database error in taskboard: " . $e->getMessage());
    // You could show an error message to the user
}

// Helper functions for displaying task data
function formatDate($date) {
    if (empty($date)) return '';
    return date('d.m.Y', strtotime($date));
}

function getPriorityClass($task) {
    if (isset($task['priority'])) {
        switch ($task['priority']) {
            case 'high': return 'bg-red-500';
            case 'medium': return 'bg-yellow-500';
            case 'low': return 'bg-green-500';
            default: return 'bg-blue-500';
        }
    }
    
    // Default priority indicator if not set
    return 'bg-blue-500';
}
?>
