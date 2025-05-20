<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();
$userId = $_SESSION['user_id'];
$user = getUser();

// Make sure calendar tables exist
require_once __DIR__ . '/../../database/calendar_tables.php';

// Get view parameters
$view = $_GET['view'] ?? 'month';  // month, week, day
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$day = isset($_GET['day']) ? (int)$_GET['day'] : (int)date('d');

// Validate and adjust dates
$currentDate = new DateTime();
$currentDate->setDate($year, $month, $day);
$currentDate->setTime(0, 0, 0);

$year = $currentDate->format('Y');
$month = $currentDate->format('m');
$day = $currentDate->format('d');

// Calculate date ranges for the view
$dateInfo = [];
if ($view === 'month') {
    // First day of the month
    $firstDay = new DateTime("$year-$month-01");
    // Last day of the month
    $lastDay = new DateTime("$year-$month-" . $firstDay->format('t'));
    
    // Adjust to include days from previous/next month to fill calendar grid
    $firstDayOfWeek = (int)$firstDay->format('N'); // 1 (Mon) through 7 (Sun)
    $startDate = clone $firstDay;
    $startDate->modify('-' . ($firstDayOfWeek - 1) . ' days');
    
    $lastDayOfWeek = (int)$lastDay->format('N');
    $endDate = clone $lastDay;
    $endDate->modify('+' . (7 - $lastDayOfWeek) . ' days');
    
    $dateInfo = [
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'currentMonth' => $month,
        'currentYear' => $year
    ];
} elseif ($view === 'week') {
    // First day of the week (Monday)
    $dayOfWeek = (int)$currentDate->format('N');
    $startDate = clone $currentDate;
    $startDate->modify('-' . ($dayOfWeek - 1) . ' days');
    
    // Last day of the week (Sunday)
    $endDate = clone $startDate;
    $endDate->modify('+6 days');
    
    $dateInfo = [
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'currentWeek' => $currentDate->format('W')
    ];
} else { // day view
    $dateInfo = [
        'currentDate' => $currentDate->format('Y-m-d')
    ];
}

// Load user's groups for filter dropdown
$stmtGroups = $pdo->prepare("
    SELECT g.id, g.name 
    FROM user_groups g
    JOIN user_group_members m ON g.id = m.group_id
    WHERE m.user_id = ?
    ORDER BY g.name
");
$stmtGroups->execute([$userId]);
$userGroups = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);

// Get all users for assignment dropdown
$stmtUsers = $pdo->query("SELECT id, username FROM users ORDER BY username");
$allUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Fetch events based on view
$params = [];
$whereClause = "";

if ($view === 'month' || $view === 'week') {
    $whereClause = "e.event_date BETWEEN ? AND ?";
    $params = [$dateInfo['startDate'], $dateInfo['endDate']];
} else { // day view
    $whereClause = "e.event_date = ?";
    $params = [$dateInfo['currentDate']];
}

// Base query to get events visible to current user
$stmt = $pdo->prepare("
    SELECT e.*, 
           creator.username AS creator_name,
           assignee.username AS assignee_name,
           g.name AS group_name
    FROM events e
    LEFT JOIN users creator ON creator.id = e.created_by
    LEFT JOIN users assignee ON assignee.id = e.assigned_to
    LEFT JOIN user_groups g ON g.id = e.assigned_group_id
    WHERE $whereClause AND (
        e.created_by = ? OR 
        e.assigned_to = ? OR
        e.assigned_group_id IN (
            SELECT group_id FROM user_group_members WHERE user_id = ?
        )
    )
    ORDER BY e.event_date, e.start_time
");

array_push($params, $userId, $userId, $userId);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group events by date for easier rendering
$eventsByDate = [];
foreach ($events as $event) {
    $date = $event['event_date'];
    if (!isset($eventsByDate[$date])) {
        $eventsByDate[$date] = [];
    }
    $eventsByDate[$date][] = $event;
}

// Handle form submission for new events
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_event') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $eventDate = $_POST['event_date'] ?? '';
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        $allDay = isset($_POST['all_day']) ? 1 : 0;
        $assignmentType = $_POST['assignment_type'] ?? 'none';
        $assignedTo = ($assignmentType === 'user') ? ($_POST['assigned_to'] ?? null) : null;
        $assignedGroupId = ($assignmentType === 'group') ? ($_POST['assigned_group_id'] ?? null) : null;
        $color = $_POST['color'] ?? '#4A90E2';
        
        // Validation
        if (empty($title)) {
            $errors[] = 'Der Titel ist erforderlich.';
        }
        if (empty($eventDate)) {
            $errors[] = 'Das Datum ist erforderlich.';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO events (
                        title, description, location, event_date, 
                        start_time, end_time, all_day, created_by, 
                        assigned_to, assigned_group_id, color
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $title, $description, $location, $eventDate,
                    $startTime, $endTime, $allDay, $userId,
                    $assignedTo, $assignedGroupId, $color
                ]);
                
                $success = 'Termin erfolgreich erstellt!';
                
                // Redirect to prevent form resubmission
                header("Location: /calendar.php?success=created&view=$view&year=$year&month=$month&day=$day");
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Datenbankfehler: ' . $e->getMessage();
            }
        }
    }
}

// Include template
require_once __DIR__ . '/../../templates/calendar.php';
