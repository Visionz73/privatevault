<?php
// Debug-Modus: Alle Fehler anzeigen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1) DB und Auth laden
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// 2) Session & Login-Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();

// 3) User & Filter
$userId = $_SESSION['user_id'];
$filterAssignedTo = $_GET['assigned_to'] ?? 'all';
$filterGroupId = $_GET['group_id'] ?? 'all'; // New group filter parameter

// 4) „Done"-Flag setzen
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    // Aktualisiere die Aufgabe als erledigt, indem is_done auf 1 gesetzt wird
    $upd = $pdo->prepare("UPDATE tasks SET is_done = 1 WHERE id = ? AND assigned_to = ?");
    $upd->execute([(int)$_GET['done'], $userId]);
    header('Location: /inbox.php?assigned_to=' . urlencode($filterAssignedTo) . '&group_id=' . urlencode($filterGroupId));
    exit;
}

// 5) Nutzer für Filter-Dropdown
$users = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 5b) Gruppen für Filter-Dropdown laden
$stmtUserGroups = $pdo->prepare("
    SELECT g.id, g.name
    FROM user_groups g
    INNER JOIN user_group_members m ON g.id = m.group_id
    WHERE m.user_id = ?
    ORDER BY g.name
");
$stmtUserGroups->execute([$userId]);
$userGroups = $stmtUserGroups->fetchAll(PDO::FETCH_ASSOC);

// 6) WHERE-Klausel bauen basierend auf Filter
$where = ["t.is_done != 1"];
$params = [];

// Get user's group memberships
$stmtGroups = $pdo->prepare("
    SELECT group_id FROM user_group_members WHERE user_id = ?
");
$stmtGroups->execute([$userId]);
$userGroupIds = $stmtGroups->fetchAll(PDO::FETCH_COLUMN);

// Filter by specific group if requested
if ($filterGroupId !== 'all' && is_numeric($filterGroupId)) {
    // Check if user belongs to this group
    if (in_array($filterGroupId, $userGroupIds)) {
        $where[] = "t.assigned_group_id = ?";
        $params[] = (int)$filterGroupId;
    } else {
        // If user doesn't belong to the group, show nothing
        $where[] = "1 = 0";
    }
} 
// Only for specific assignee or all tasks
else if ($filterAssignedTo !== 'all' && is_numeric($filterAssignedTo)) {
    $where[] = "t.assigned_to = ?";
    $params[] = (int)$filterAssignedTo;
} else {
    // Tasks either directly assigned to the user OR to a group they belong to
    $groupCondition = '';
    if (!empty($userGroupIds)) {
        $placeholders = implode(',', array_fill(0, count($userGroupIds), '?'));
        $groupCondition = " OR t.assigned_group_id IN ($placeholders)";
        $params = array_merge($params, $userGroupIds);
    }
    
    $where[] = "(t.assigned_to = ?$groupCondition)";
    $params[] = (int)$userId;
}

// 7) Tasks holen
$sql = "SELECT t.*, 
              creator.username AS creator_name,
              assignee.username AS assignee_name,
              g.name AS group_name
        FROM tasks t
        LEFT JOIN users creator ON creator.id = t.created_by
        LEFT JOIN users assignee ON assignee.id = t.assigned_to
        LEFT JOIN user_groups g ON g.id = t.assigned_group_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 8) Template rendern
require_once __DIR__ . '/../../templates/inbox.php';
