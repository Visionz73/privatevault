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

// Log request data
error_log('Event submission started. POST data: ' . json_encode($_POST));

// Initialize response
$response = [
    'success' => false,
    'message' => 'Unknown error occurred',
    'errors' => [],
    'debug_info' => []
];

try {
    // Validate required fields
    $requiredFields = ['title', 'start_date', 'start_time', 'end_date', 'end_time'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $response['errors'][$field] = "Field $field is required";
        }
    }
    
    // If we have validation errors, return early
    if (!empty($response['errors'])) {
        $response['message'] = 'Validation failed';
        $response['debug_info'][] = 'Form validation failed';
        echo json_encode($response);
        exit;
    }
    
    // Format and sanitize data
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $startDate = trim($_POST['start_date']);
    $startTime = trim($_POST['start_time']);
    $endDate = trim($_POST['end_date']);
    $endTime = trim($_POST['end_time']);
    $color = trim($_POST['color'] ?? '#3498db');
    
    // Handle assignments
    $assignedTo = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;
    if ($assignedTo === 0) $assignedTo = null;
    
    $assignedGroup = !empty($_POST['assigned_group']) ? intval($_POST['assigned_group']) : null;
    if ($assignedGroup === 0) $assignedGroup = null;
    
    // Format dates for MySQL
    $startDateTime = date('Y-m-d H:i:s', strtotime("$startDate $startTime"));
    $endDateTime = date('Y-m-d H:i:s', strtotime("$endDate $endTime"));
    
    // Debug info
    $response['debug_info'][] = [
        'startDateTime' => $startDateTime,
        'endDateTime' => $endDateTime,
        'assignedTo' => $assignedTo,
        'assignedGroup' => $assignedGroup
    ];
    
    // Insert into database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
        INSERT INTO events (
            title, description, location, 
            start_datetime, end_datetime, 
            created_by, assigned_to, assigned_group_id, 
            color, created_at, updated_at
        ) VALUES (
            :title, :description, :location, 
            :start_datetime, :end_datetime, 
            :created_by, :assigned_to, :assigned_group_id, 
            :color, NOW(), NOW()
        )
    ");
    
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':start_datetime', $startDateTime);
    $stmt->bindParam(':end_datetime', $endDateTime);
    $stmt->bindParam(':created_by', $userId);
    $stmt->bindParam(':assigned_to', $assignedTo, PDO::PARAM_INT);
    $stmt->bindParam(':assigned_group_id', $assignedGroup, PDO::PARAM_INT);
    $stmt->bindParam(':color', $color);
    
    $result = $stmt->execute();
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Event created successfully';
        $response['event_id'] = $pdo->lastInsertId();
        error_log('Event created successfully with ID: ' . $response['event_id']);
    } else {
        $response['message'] = 'Database insert failed';
        $response['debug_info'][] = 'Execute returned false';
        error_log('Event creation failed: Execute returned false');
    }
    
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    $response['debug_info'][] = [
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ];
    error_log('PDO Exception during event creation: ' . $e->getMessage());
    error_log('SQL State: ' . $e->getCode());
    error_log('Trace: ' . $e->getTraceAsString());
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
    $response['debug_info'][] = [
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ];
    error_log('Exception during event creation: ' . $e->getMessage());
    error_log('Trace: ' . $e->getTraceAsString());
}

// Return JSON response if AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Otherwise, redirect with status
if ($response['success']) {
    header("Location: calendar.php?view=day&date=$startDate&event_added=1");
} else {
    // Save error info in session and redirect to form
    $_SESSION['event_form_error'] = $response;
    $_SESSION['event_form_data'] = $_POST;
    header("Location: event_form.php?" . http_build_query($_GET));
}
exit;
