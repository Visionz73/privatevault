<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/db.php';

// Enable error logging to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/event_errors.log');

requireLogin();
$userId = $_SESSION['user_id'];

// Function to log debug information
function logDebug($message, $data = null) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $message;
    if ($data !== null) {
        $log .= " - Data: " . print_r($data, true);
    }
    error_log($log);
}

logDebug("Event save process started", $_POST);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logDebug("No POST data received");
    die("Error: Form must be submitted via POST");
}

// Get form data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');
$startDate = trim($_POST['start_date'] ?? '');
$startTime = trim($_POST['start_time'] ?? '');
$endDate = trim($_POST['end_date'] ?? '');
$endTime = trim($_POST['end_time'] ?? '');
$assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
$assignedGroup = !empty($_POST['assigned_group']) ? (int)$_POST['assigned_group'] : null;
$color = trim($_POST['color'] ?? '#3498db');

// If assignedTo or assignedGroup is 0, set to null
if ($assignedTo === 0) $assignedTo = null;
if ($assignedGroup === 0) $assignedGroup = null;

logDebug("Processed form data", [
    'title' => $title,
    'startDate' => $startDate,
    'startTime' => $startTime,
    'endDate' => $endDate,
    'endTime' => $endTime,
    'assignedTo' => $assignedTo,
    'assignedGroup' => $assignedGroup
]);

// Basic validation
$errors = [];

if (empty($title)) $errors[] = "Title is required";
if (empty($startDate)) $errors[] = "Start date is required";
if (empty($startTime)) $errors[] = "Start time is required";
if (empty($endDate)) $errors[] = "End date is required";
if (empty($endTime)) $errors[] = "End time is required";

if (!empty($errors)) {
    logDebug("Validation errors", $errors);
    $_SESSION['event_form_errors'] = $errors;
    $_SESSION['event_form_data'] = $_POST;
    header("Location: add_event.php");
    exit;
}

// Format date and times for database
$startDateTime = $startDate . ' ' . $startTime . ':00';
$endDateTime = $endDate . ' ' . $endTime . ':00';

// Get database table structure to confirm columns
try {
    $tableCheck = $pdo->query("DESCRIBE events");
    $columns = $tableCheck->fetchAll(PDO::FETCH_COLUMN);
    logDebug("Table structure", $columns);
} catch (PDOException $e) {
    logDebug("Error checking table structure: " . $e->getMessage());
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Prepare statement with named parameters for better debugging
    $sql = "INSERT INTO events (
                title, description, location,
                start_datetime, end_datetime,
                created_by, assigned_to, assigned_group_id,
                color, created_at, updated_at
            ) VALUES (
                :title, :description, :location,
                :start_datetime, :end_datetime,
                :created_by, :assigned_to, :assigned_group_id,
                :color, NOW(), NOW()
            )";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind each parameter separately for better error tracking
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':start_datetime', $startDateTime);
    $stmt->bindParam(':end_datetime', $endDateTime);
    $stmt->bindParam(':created_by', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':assigned_to', $assignedTo, PDO::PARAM_INT);
    $stmt->bindParam(':assigned_group_id', $assignedGroup, PDO::PARAM_INT);
    $stmt->bindParam(':color', $color);
    
    logDebug("Executing SQL", [
        'sql' => $sql,
        'params' => [
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'start_datetime' => $startDateTime,
            'end_datetime' => $endDateTime,
            'created_by' => $userId,
            'assigned_to' => $assignedTo, 
            'assigned_group_id' => $assignedGroup,
            'color' => $color
        ]
    ]);
    
    // Execute the statement
    $result = $stmt->execute();
    
    if ($result) {
        $eventId = $pdo->lastInsertId();
        logDebug("Event created successfully with ID: $eventId");
        $pdo->commit();
        
        // Redirect with success message
        $_SESSION['event_success'] = "Event created successfully";
        header("Location: calendar.php?view=day&date=$startDate");
        exit;
    } else {
        // Rollback on failure
        $pdo->rollBack();
        $errorInfo = $stmt->errorInfo();
        logDebug("SQL execution failed", $errorInfo);
        
        $_SESSION['event_form_errors'] = ["Database error: " . $errorInfo[2]];
        $_SESSION['event_form_data'] = $_POST;
        header("Location: add_event.php");
        exit;
    }
} catch (PDOException $e) {
    // Rollback transaction on exception
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    logDebug("PDO Exception: " . $e->getMessage(), [
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    $_SESSION['event_form_errors'] = ["Database error: " . $e->getMessage()];
    $_SESSION['event_form_data'] = $_POST;
    header("Location: add_event.php");
    exit;
} catch (Exception $e) {
    // General exception handling
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    logDebug("General Exception: " . $e->getMessage());
    
    $_SESSION['event_form_errors'] = ["System error: " . $e->getMessage()];
    $_SESSION['event_form_data'] = $_POST;
    header("Location: add_event.php");
    exit;
}
