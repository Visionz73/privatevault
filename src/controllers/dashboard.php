<?php
// 1) Session prüfen / Login prüfen - ohne session_start() da diese bereits in config.php erfolgt
require_once __DIR__.'/../lib/auth.php';
requireLogin();
$userId = $_SESSION['user_id'];

// 2) DB-Verbindung
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

// Load user widget configuration
$stmt = $pdo->prepare("
    SELECT slot_position, widget_type, widget_config 
    FROM user_dashboard_widgets 
    WHERE user_id = ? AND is_active = 1
    ORDER BY slot_position
");
$stmt->execute([$userId]);
$userWidgetConfig = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create full widget configuration (16 slots)
$widgetSlots = [];
for ($i = 1; $i <= 16; $i++) {
    $found = false;
    foreach ($userWidgetConfig as $widget) {
        if ($widget['slot_position'] == $i) {
            $widgetSlots[$i] = $widget;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $widgetSlots[$i] = [
            'slot_position' => $i,
            'widget_type' => 'placeholder',
            'widget_config' => null
        ];
    }
}

// Available widgets list
$availableWidgets = [
    'inbox' => ['name' => 'Inbox', 'icon' => 'inbox'],
    'documents' => ['name' => 'Dokumente', 'icon' => 'folder'],
    'calendar' => ['name' => 'Kalender', 'icon' => 'calendar'],
    'havetopay' => ['name' => 'HaveToPay', 'icon' => 'money'],
    'placeholder' => ['name' => 'Leer', 'icon' => 'plus']
];

// Load widget data based on configuration
$widgetData = [];
foreach ($widgetSlots as $slot) {
    $widgetType = $slot['widget_type'];
    
    switch ($widgetType) {
        case 'inbox':
            // Load inbox data with existing logic
            $countParams = [];
            $countWhere = ["t.is_done != 1"];
            
            if ($filterType === 'group' && is_numeric($filterGroupId)) {
                $checkMembership = $pdo->prepare("SELECT COUNT(*) FROM user_group_members WHERE user_id = ? AND group_id = ?");
                $checkMembership->execute([$userId, $filterGroupId]);
                
                if ($checkMembership->fetchColumn() > 0) {
                    $countWhere[] = "t.assigned_group_id = ?";
                    $countParams[] = $filterGroupId;
                } else {
                    $countWhere[] = "t.assigned_to = ?";
                    $countParams[] = $userId;
                }
            } else {
                $countWhere[] = "t.assigned_to = ?";
                $countParams[] = $userId;
            }
            
            $countSql = "SELECT COUNT(*) FROM tasks t WHERE " . implode(' AND ', $countWhere);
            $stmtCount = $pdo->prepare($countSql);
            $stmtCount->execute($countParams);
            $widgetData['inbox_count'] = (int)$stmtCount->fetchColumn();
            
            $taskSql = "
              SELECT t.*,
                     u_creator.username AS creator_name,
                     u_assignee.username AS assignee_name,
                     g.name AS group_name
                FROM tasks t
                LEFT JOIN users u_creator ON u_creator.id = t.created_by
                LEFT JOIN users u_assignee ON u_assignee.id = t.assigned_to
                LEFT JOIN user_groups g ON g.id = t.assigned_group_id
               WHERE " . implode(' AND ', $countWhere) . "
               ORDER BY t.created_at DESC
               LIMIT 5
            ";
            $stmtTasks = $pdo->prepare($taskSql);
            $stmtTasks->execute($countParams);
            $widgetData['inbox_tasks'] = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'documents':
            $stmt = $pdo->prepare(
                'SELECT title, upload_date 
                 FROM documents 
                 WHERE user_id = ? 
                 AND is_deleted = 0
                 ORDER BY upload_date DESC
                 LIMIT 5'
            );
            $stmt->execute([$userId]);
            $widgetData['documents'] = $stmt->fetchAll();
            
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM documents WHERE user_id = ? AND is_deleted = 0'
            );
            $stmt->execute([$userId]);
            $widgetData['documents_count'] = (int)$stmt->fetchColumn();
            break;
            
        case 'calendar':
            $stmt = $pdo->prepare("SELECT id, title, event_date FROM events WHERE created_by = ? ORDER BY event_date ASC LIMIT 5");
            $stmt->execute([$userId]);
            $widgetData['events'] = $stmt->fetchAll();
            break;
            
        case 'havetopay':
            require_once __DIR__.'/widgets/havetopay_widget.php';
            $widgetData['havetopay'] = [
                'total_owed' => $widgetTotalOwed,
                'total_owing' => $widgetTotalOwing,
                'net_balance' => $widgetNetBalance,
                'balances' => $balances,
                'recent_expenses' => $recentExpenses
            ];
            break;
    }
}

// 8) Template rendern
require_once __DIR__.'/../../templates/dashboard.php';
?>