// src/controllers/inbox.php
<?php
// 1) Session & Auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/auth.php';
requireLogin();

// 2) Aktueller User
$userId = $_SESSION['user_id'];

// 3) Filter: 'all' oder nur meine Tasks
$filterAssignedTo = $_GET['assigned_to'] ?? $userId;

// 4) Als „done“ markieren
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $upd = $pdo->prepare(
        'UPDATE tasks SET status = "done"
         WHERE id = ? AND assigned_to = ?'
    );
    $upd->execute([(int)$_GET['done'], $userId]);
    header('Location: /inbox.php?assigned_to=' . urlencode($filterAssignedTo));
    exit;
}

// 5) Nutzer für Filter-Dropdown
$users    = $pdo->query('SELECT id, username FROM users ORDER BY username')
                ->fetchAll(PDO::FETCH_ASSOC);
$usersMap = array_column($users, 'username', 'id');

// 6) WHERE-Klausel
$where  = ['t.status != "done"'];
$params = [];

if ($filterAssignedTo !== 'all') {
    $where[]  = 't.assigned_to = ?';
    $params[] = (int)$filterAssignedTo;
}

// 7) Tasks holen
$sql = '
    SELECT t.*, u.username AS creator
      FROM tasks t
      JOIN users u ON u.id = t.created_by
     WHERE ' . implode(' AND ', $where) . '
  ORDER BY t.id DESC
';
<<<<<<< HEAD
<<<<<<< HEAD
$stmt = $pdo->prepare("
    SELECT
        id,
        title,
        is_done,
        created_at          AS due_date   -- oder due_date, falls Spalte existiert
    FROM tasks
    WHERE user_id = :uid
      AND is_done = 0
    ORDER BY created_at DESC
");
$stmt->execute([':uid' => $userId]);
$tasks = $stmt->fetchAll();
=======
$stmt  = $pdo->prepare($sql);
=======
$stmt = $pdo->prepare($sql);
>>>>>>> dd2066b8ba16da0b3016375319f0ac4eb4b3daf8
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
>>>>>>> 4ae26bae264f4177682599ef410dc87fdcba950d

// 8) Template rendern
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/inbox.php';
