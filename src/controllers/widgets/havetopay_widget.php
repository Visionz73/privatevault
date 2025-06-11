<?php

// 1. Ensure the user is logged in
require_once __DIR__ . '/../../lib/auth.php';
requireLogin();

// 2. Get the current userId from the session
$userId = $_SESSION['user_id'];

// 3. Include the database connection
require_once __DIR__ . '/../../lib/db.php';

// 4. Initialize variables
$widgetTotalOwed = 0;
$widgetTotalOwing = 0;
$widgetNetBalance = 0;
$balances = ['others_owe' => [], 'user_owes' => []];
$recentExpenses = [];

try {
    // Check users table structure for display names
    $columnsResult = $pdo->query("DESCRIBE users");
    $userColumns = [];
    while ($column = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
        $userColumns[] = $column['Field'];
    }
    
    $hasFirstName = in_array('first_name', $userColumns);
    $hasLastName = in_array('last_name', $userColumns);

    // 5. Fetch the total amount others owe the current user
    $stmtOwed = $pdo->prepare("
        SELECT SUM(ep.share_amount) AS total_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        WHERE e.payer_id = :user_id 
        AND ep.user_id != :user_id 
        AND ep.is_settled = 0
    ");
    $stmtOwed->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtOwed->execute();
    $resultOwed = $stmtOwed->fetch(PDO::FETCH_ASSOC);
    if ($resultOwed && $resultOwed['total_owed']) {
        $widgetTotalOwed = (float)$resultOwed['total_owed'];
    }

    // 6. Fetch the total amount the current user owes others
    $stmtOwing = $pdo->prepare("
        SELECT SUM(ep.share_amount) AS total_owing
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        WHERE ep.user_id = :user_id 
        AND e.payer_id != :user_id 
        AND ep.is_settled = 0
    ");
    $stmtOwing->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtOwing->execute();
    $resultOwing = $stmtOwing->fetch(PDO::FETCH_ASSOC);
    if ($resultOwing && $resultOwing['total_owing']) {
        $widgetTotalOwing = (float)$resultOwing['total_owing'];
    }

    // 7. Calculate the net balance
    $widgetNetBalance = $widgetTotalOwed - $widgetTotalOwing;

    // Fetch detailed balances: what others owe current user
    $nameFields = $hasFirstName && $hasLastName ? 
        "u.first_name, u.last_name" : 
        "u.username as name";
        
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, $nameFields,
               SUM(ep.share_amount) as amount_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        JOIN users u ON ep.user_id = u.id
        WHERE e.payer_id = ? AND ep.user_id != ? AND ep.is_settled = 0
        GROUP BY u.id
        ORDER BY amount_owed DESC
    ");
    $stmt->execute([$userId, $userId]);
    $othersOwe = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($othersOwe as $balance) {
        $balance['display_name'] = $hasFirstName && $hasLastName && 
            !empty($balance['first_name']) && !empty($balance['last_name']) ? 
            $balance['first_name'] . ' ' . $balance['last_name'] : 
            $balance['username'];
        $balances['others_owe'][] = $balance;
    }
    
    // Fetch detailed balances: what current user owes others
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, $nameFields,
               SUM(ep.share_amount) as amount_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        JOIN users u ON e.payer_id = u.id
        WHERE ep.user_id = ? AND e.payer_id != ? AND ep.is_settled = 0
        GROUP BY u.id
        ORDER BY amount_owed DESC
    ");
    $stmt->execute([$userId, $userId]);
    $userOwes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($userOwes as $balance) {
        $balance['display_name'] = $hasFirstName && $hasLastName && 
            !empty($balance['first_name']) && !empty($balance['last_name']) ? 
            $balance['first_name'] . ' ' . $balance['last_name'] : 
            $balance['username'];
        $balances['user_owes'][] = $balance;
    }

    // Fetch recent expenses
    $userNameFields = $hasFirstName && $hasLastName ? 
        "u.first_name as payer_first_name, u.last_name as payer_last_name," : 
        "";
        
    $stmt = $pdo->prepare("
        SELECT e.id, e.title, e.amount, e.expense_date,
               u.username as payer_name,
               $userNameFields
               (SELECT COUNT(*) FROM expense_participants WHERE expense_id = e.id) as participant_count
        FROM expenses e
        JOIN users u ON e.payer_id = u.id
        WHERE e.payer_id = ? 
        OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?)
        ORDER BY e.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId, $userId]);
    $recentExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // 9. Include error handling and log any errors.
    error_log("Error in havetopay_widget.php: " . $e->getMessage());
    $widgetTotalOwed = 0;
    $widgetTotalOwing = 0;
    $widgetNetBalance = 0;
    $balances = ['others_owe' => [], 'user_owes' => []];
    $recentExpenses = [];
}

?>
