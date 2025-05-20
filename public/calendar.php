<?php
// Debug-Modus aktivieren, um den genauen Fehler zu sehen:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/db.php';

requireLogin();
$userId = $_SESSION['user_id'];

// Get view parameters
$view = $_GET['view'] ?? 'month';  // month, week, day
$date = $_GET['date'] ?? date('Y-m-d');
$timestamp = strtotime($date);
$year = date('Y', $timestamp);
$month = date('m', $timestamp);
$day = date('d', $timestamp);

// Get user groups for filtering
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

// Get selected filters
$filterType = $_GET['filter'] ?? 'all';  // all, mine, group
$filterGroupId = $_GET['group_id'] ?? null;

// Fetch events based on view and filters
$whereClauses = [];
$params = [];

// Filter by ownership/assignment
if ($filterType === 'mine') {
    $whereClauses[] = "(e.created_by = ? OR e.assigned_to = ?)";
    array_push($params, $userId, $userId);
} elseif ($filterType === 'group' && !empty($filterGroupId)) {
    $whereClauses[] = "e.assigned_group_id = ?";
    $params[] = $filterGroupId;
} else {
    // Show all events the user can see (created by user, assigned to user, or assigned to user's groups)
    $groupIds = array_column($userGroups, 'id');
    $groupPlaceholders = '';
    
    if (!empty($groupIds)) {
        $groupPlaceholders = implode(',', array_fill(0, count($groupIds), '?'));
        $whereClauses[] = "(e.created_by = ? OR e.assigned_to = ? OR e.assigned_group_id IN ($groupPlaceholders))";
        array_push($params, $userId, $userId);
        $params = array_merge($params, $groupIds);
    } else {
        $whereClauses[] = "(e.created_by = ? OR e.assigned_to = ?)";
        array_push($params, $userId, $userId);
    }
}

// Date range based on view
if ($view === 'month') {
    // Get first and last day of month
    $firstDay = date('Y-m-01', $timestamp);
    $lastDay = date('Y-m-t', $timestamp);
    
    // Get a few days from previous and next month to fill calendar
    $prevDays = date('w', strtotime($firstDay));
    $firstDay = date('Y-m-d', strtotime("-$prevDays days", strtotime($firstDay)));
    
    $lastDayOfWeek = date('w', strtotime($lastDay));
    $daysToAdd = 6 - $lastDayOfWeek;
    $lastDay = date('Y-m-d', strtotime("+$daysToAdd days", strtotime($lastDay)));
    
    $whereClauses[] = "DATE(e.start_datetime) BETWEEN ? AND ?";
    array_push($params, $firstDay, $lastDay);
} elseif ($view === 'week') {
    // Get first and last day of week
    $dayOfWeek = date('w', $timestamp);
    $firstDay = date('Y-m-d', strtotime("-$dayOfWeek days", $timestamp));
    $lastDay = date('Y-m-d', strtotime("+6 days", strtotime($firstDay)));
    
    $whereClauses[] = "DATE(e.start_datetime) BETWEEN ? AND ?";
    array_push($params, $firstDay, $lastDay);
} elseif ($view === 'day') {
    $whereClauses[] = "DATE(e.start_datetime) = ?";
    array_push($params, date('Y-m-d', $timestamp));
}

// Build and execute the query
$whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
$query = "
    SELECT e.*, 
           creator.username as creator_name,
           assignee.username as assignee_name,
           g.name as group_name
    FROM events e
    LEFT JOIN users creator ON creator.id = e.created_by
    LEFT JOIN users assignee ON assignee.id = e.assigned_to
    LEFT JOIN user_groups g ON g.id = e.assigned_group_id
    $whereClause
    ORDER BY e.start_datetime ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../templates/calendar.php';
