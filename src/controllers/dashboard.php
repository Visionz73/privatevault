// 1) Session starten / Login prüfen
session_start();
require_once __DIR__.'/../lib/auth.php';
requireLogin();
$userId = $_SESSION['user_id'];

// 2) DB-Verbindung
require_once __DIR__.'/../lib/db.php';

// 3) Tasks zählen (nur nach assigned_to, kein created_by-Filter)
$stmtCount = $pdo->prepare("
  SELECT COUNT(*)
    FROM tasks t
   WHERE t.assigned_to = ?
     AND t.is_done != 1
");
$stmtCount->execute([$userId]);
$openTaskCount = (int)$stmtCount->fetchColumn();

// 4) Tasks holen (nur nach assigned_to, kein created_by-Filter)
$stmtTasks = $pdo->prepare("
  SELECT t.*,
         uc.username  AS creator_name,
         ua.username  AS assignee_name
    FROM tasks t
    LEFT JOIN users uc ON uc.id = t.created_by
    LEFT JOIN users ua ON ua.id = t.assigned_to
   WHERE t.assigned_to = ?
     AND t.is_done != 1
   ORDER BY t.created_at DESC
");
$stmtTasks->execute([$userId]);
$tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

// 5) Template rendern
require_once __DIR__.'/../../templates/dashboard.php';