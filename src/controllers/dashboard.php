<?php
// 1) Session prüfen / Login prüfen - ohne session_start() da diese bereits in config.php erfolgt
require_once __DIR__.'/../lib/auth.php';
requireLogin();
$userId = $_SESSION['user_id'];

require_once __DIR__.'/../lib/db.php';

// Gruppen des Benutzers laden
$stmtGroups = $pdo->prepare("
  SELECT g.id, g.name 
  FROM user_groups g
  JOIN user_group_members m ON g.id = m.group_id
  WHERE m.user_id = ?
  ORDER BY g.name
");
$stmtGroups->execute([$userId]);
$userGroups = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);

// Filterparameter (Standard: Nur eigene Aufgaben)
$filterType = $_GET['filter'] ?? 'mine';
$filterGroupId = $_GET['group_id'] ?? null;

// 3) Tasks zählen - basierend auf Filter
$countParams = []; // Initialize as empty array
$countWhere = ["t.is_done != 1"];

if ($filterType === 'group' && is_numeric($filterGroupId)) {
    // Sicherstellen, dass der Benutzer dieser Gruppe angehört
    $checkMembership = $pdo->prepare("SELECT COUNT(*) FROM user_group_members WHERE user_id = ? AND group_id = ?");
    $checkMembership->execute([$userId, $filterGroupId]);
    
    if ($checkMembership->fetchColumn() > 0) {
        $countWhere[] = "t.assigned_group_id = ?";
        $countParams[] = $filterGroupId;
    } else {
        // Wenn kein Mitglied, auf eigene Aufgaben zurückfallen
        $countWhere[] = "t.assigned_to = ?";
        $countParams[] = $userId;
    }
} else {
    // Standardfall: Eigene Aufgaben
    $countWhere[] = "t.assigned_to = ?";
    $countParams[] = $userId;
}

$countSql = "SELECT COUNT(*) FROM tasks t WHERE " . implode(' AND ', $countWhere);
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($countParams);
$openTaskCount = (int)$stmtCount->fetchColumn();

// 4) Tasks holen - mit denselben Filtern
$taskParams = $countParams; // This will now correctly match the parameter count
$taskWhere = $countWhere;

$taskSql = "
  SELECT t.*,
         u_creator.username AS creator_name,
         u_assignee.username AS assignee_name,
         g.name AS group_name
    FROM tasks t
    LEFT JOIN users u_creator ON u_creator.id = t.created_by
    LEFT JOIN users u_assignee ON u_assignee.id = t.assigned_to
    LEFT JOIN user_groups g ON g.id = t.assigned_group_id
   WHERE " . implode(' AND ', $taskWhere) . "
   ORDER BY t.created_at DESC
   LIMIT 5
";
$stmtTasks = $pdo->prepare($taskSql);
$stmtTasks->execute($taskParams);
$tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

// 5) Dokumente laden (need this for the documents widget)
$stmt = $pdo->prepare(
    'SELECT title, upload_date 
     FROM documents 
     WHERE user_id = ? 
     AND is_deleted = 0
     ORDER BY upload_date DESC
     LIMIT 5'
);
$stmt->execute([$userId]);
$docs = $stmt->fetchAll();

$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM documents WHERE user_id = ? AND is_deleted = 0'
);
$stmt->execute([$userId]);
$docCount = (int)$stmt->fetchColumn();

// 6) Termine laden (need this for the events widget)
$stmt = $pdo->prepare("SELECT id, title, event_date FROM events WHERE created_by = ? ORDER BY event_date ASC");
$stmt->execute([$userId]);
$events = $stmt->fetchAll();

// 7) Get user data for greeting
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Load HaveToPay widget data
require_once __DIR__.'/widgets/havetopay_widget.php';

// Get upcoming events for calendar short
$stmt = $pdo->prepare("
    SELECT e.*, u.username AS creator_name
    FROM events e
    LEFT JOIN users u ON u.id = e.created_by
    WHERE (e.assigned_to = ? OR e.created_by = ?)
    AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
    LIMIT 5
");
$stmt->execute([$userId, $userId]);
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent documents for documents short
$stmt = $pdo->prepare("
    SELECT id, filename, upload_date, category
    FROM documents 
    WHERE user_id = ? 
    ORDER BY upload_date DESC 
    LIMIT 5
");
$stmt->execute([$userId]);
$recentDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get completed tasks count for stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND is_done = 1");
$stmt->execute([$userId]);
$completedTasksCount = $stmt->fetchColumn();

// 8) Template rendern
require_once __DIR__.'/../../templates/dashboard.php';
?>