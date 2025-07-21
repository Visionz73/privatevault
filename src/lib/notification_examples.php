<?php
/**
 * Example integration for sending notifications from other parts of the application
 */

require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/NotificationManager.php';

// Example usage for sending notifications

// Initialize notification manager
$notificationManager = new NotificationManager($pdo);

// Example: Send task reminder
function sendTaskReminder($taskId, $userId) {
    global $notificationManager, $pdo;
    
    // Get task details
    $stmt = $pdo->prepare("SELECT title, due_date FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    $task = $stmt->fetch();
    
    if ($task) {
        $notificationManager->sendTaskReminder(
            $userId,
            $task['title'],
            $taskId,
            $task['due_date']
        );
    }
}

// Example: Send calendar event reminder
function sendCalendarReminder($eventId, $userId) {
    global $notificationManager, $pdo;
    
    // Get event details
    $stmt = $pdo->prepare("SELECT title, start_date FROM events WHERE id = ? AND user_id = ?");
    $stmt->execute([$eventId, $userId]);
    $event = $stmt->fetch();
    
    if ($event) {
        $notificationManager->sendCalendarReminder(
            $userId,
            $event['title'],
            $eventId,
            $event['start_date']
        );
    }
}

// Example: Send finance update
function sendFinanceUpdate($userId, $amount, $description) {
    global $notificationManager;
    
    $notificationManager->sendFinanceUpdate($userId, $amount, $description);
}

// Example: Send system notification
function sendSystemNotification($userId, $title, $message, $type = 'info') {
    global $notificationManager;
    
    $notificationManager->sendNotification($userId, $title, $message, $type);
}

// Example: Bulk notification to all users
function sendBulkNotification($title, $message, $type = 'info') {
    global $notificationManager, $pdo;
    
    // Get all user IDs
    $stmt = $pdo->query("SELECT id FROM users WHERE active = 1");
    $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $sent = $notificationManager->sendBulkNotification($userIds, $title, $message, $type);
    
    return $sent;
}

// Example cron job for sending scheduled reminders
function processScheduledNotifications() {
    global $pdo;
    
    // Get tasks due within next 24 hours
    $stmt = $pdo->query("
        SELECT t.id, t.title, t.due_date, t.user_id 
        FROM tasks t 
        WHERE t.due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
        AND t.status != 'completed'
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.user_id = t.user_id 
            AND n.type = 'task_reminder'
            AND JSON_EXTRACT(n.data, '$.taskId') = t.id
            AND n.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        )
    ");
    
    $tasks = $stmt->fetchAll();
    
    foreach ($tasks as $task) {
        sendTaskReminder($task['id'], $task['user_id']);
    }
    
    // Get calendar events starting within next 2 hours
    $stmt = $pdo->query("
        SELECT e.id, e.title, e.start_date, e.user_id 
        FROM events e 
        WHERE e.start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.user_id = e.user_id 
            AND n.type = 'calendar_event'
            AND JSON_EXTRACT(n.data, '$.eventId') = e.id
            AND n.created_at > DATE_SUB(NOW(), INTERVAL 2 HOUR)
        )
    ");
    
    $events = $stmt->fetchAll();
    
    foreach ($events as $event) {
        sendCalendarReminder($event['id'], $event['user_id']);
    }
}

// If this file is run directly, process scheduled notifications
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    processScheduledNotifications();
    echo "Scheduled notifications processed.\n";
}
?>
