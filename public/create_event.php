<?php
// Include the event creation controller from the src folder:
require_once __DIR__ . '/../src/controllers/create_event.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';

// Make sure calendar tables exist
require_once __DIR__ . '/../database/calendar_tables.php';

try {
    // Check authentication
    requireLogin();
    $userId = $_SESSION['user_id'];
    
    // Extract data
    $title = trim($_POST['title'] ?? '');
    $eventDate = $_POST['date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    
    // Basic validation
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    if (empty($eventDate)) {
        throw new Exception('Date is required');
    }
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO events (title, event_date, description, created_by) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$title, $eventDate, $description, $userId]);
    $eventId = $pdo->lastInsertId();
    
    // Get the username of the creator
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $username = $stmt->fetchColumn();
    
    // Return success response with event data
    echo json_encode([
        'success' => true,
        'event' => [
            'id' => $eventId,
            'title' => $title,
            'date' => $eventDate,
            'description' => $description,
            'creator' => $username
        ]
    ]);
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
