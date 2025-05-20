<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';

requireLogin();
$userId = $_SESSION['user_id'];

// Make sure calendar tables exist
require_once __DIR__ . '/../database/calendar_tables.php';

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$view = $_GET['view'] ?? 'month';
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$day = $_GET['day'] ?? date('d');

// Check if the event exists and if user has permission to delete it
$stmt = $pdo->prepare("
    SELECT created_by 
    FROM events 
    WHERE id = ?
");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Only the creator or an admin can delete events
$canDelete = $event && (($userId == $event['created_by']) || isAdmin());

if (!$canDelete) {
    header("Location: /calendar.php?view=$view&year=$year&month=$month&day=$day&error=permission_denied");
    exit;
}

// Delete the event
try {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    header("Location: /calendar.php?view=$view&year=$year&month=$month&day=$day&success=deleted");
} catch (PDOException $e) {
    header("Location: /calendar.php?view=$view&year=$year&month=$month&day=$day&error=" . urlencode("Fehler: " . $e->getMessage()));
}
exit;
