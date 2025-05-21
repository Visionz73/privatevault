<?php
// Controller for the main HaveToPay dashboard view
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];

// Make sure tables exist
$tableMessage = '';
$errorMessage = '';
ob_start();
require_once __DIR__ . '/../../database/havetopay_tables.php';
$tableMessage = ob_get_clean();

// For success/error message handling
$successMessage = '';
if (!empty($tableMessage)) {
    if (strpos($tableMessage, 'successfully') !== false) {
        $successMessage = $tableMessage;
    } elseif (strpos($tableMessage, 'error') !== false) {
        $errorMessage = $tableMessage;
    }
}

// Check if required tables exist
$tablesExist = true;
try {
    $pdo->query("SELECT 1 FROM expenses LIMIT 1");
    $pdo->query("SELECT 1 FROM expense_participants LIMIT 1");
} catch (Exception $e) {
    $tablesExist = false;
    $errorMessage = "HaveToPay module requires database tables that don't exist. Please contact the administrator.";
}

// Only proceed with the rest of the controller if tables exist
if ($tablesExist) {
    // Get balances between current user and others
    function getBalances($pdo, $userId) {
        // This calculates what others owe the current user
        $othersOweQuery = "
            SELECT u.id, u.username, u.first_name, u.last_name, 
                   SUM(ep.share_amount) as amount_owed
            FROM expenses e
            JOIN expense_participants ep ON e.id = ep.expense_id
            JOIN users u ON ep.user_id = u.id
            WHERE e.payer_id = ? AND ep.user_id != ? AND ep.is_settled = 0
            GROUP BY u.id
        ";
        
        // This calculates what the current user owes others
        $userOwesQuery = "
            SELECT u.id, u.username, u.first_name, u.last_name, 
                   SUM(ep.share_amount) as amount_owed
            FROM expenses e
            JOIN expense_participants ep ON e.id = ep.expense_id
            JOIN users u ON e.payer_id = u.id
            WHERE ep.user_id = ? AND e.payer_id != ? AND ep.is_settled = 0
            GROUP BY u.id
        ";
        
        $balances = [
            'others_owe' => [],
            'user_owes' => []
        ];
        
        $stmt = $pdo->prepare($othersOweQuery);
        $stmt->execute([$userId, $userId]);
        $balances['others_owe'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare($userOwesQuery);
        $stmt->execute([$userId, $userId]);
        $balances['user_owes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $balances;
    }

    // Get recent expenses involving the current user
    function getRecentExpenses($pdo, $userId) {
        $query = "
            SELECT e.*, 
                   u.username as payer_name,
                   (SELECT COUNT(*) FROM expense_participants WHERE expense_id = e.id) as participant_count
            FROM expenses e
            JOIN users u ON e.payer_id = u.id
            WHERE e.payer_id = ? 
               OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?)
            ORDER BY e.created_at DESC
            LIMIT 10
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all users for expense participant selection
    $userQuery = "SELECT id, username, CONCAT(first_name, ' ', last_name) as full_name 
                  FROM users 
                  WHERE id != ? 
                  ORDER BY username";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->execute([$userId]);
    $allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get balances and recent expenses
    $balances = getBalances($pdo, $userId);
    $recentExpenses = getRecentExpenses($pdo, $userId);

    // Calculate total balance
    $totalOwed = 0;
    foreach ($balances['others_owe'] as $balance) {
        $totalOwed += $balance['amount_owed'];
    }

    $totalOwing = 0;
    foreach ($balances['user_owes'] as $balance) {
        $totalOwing += $balance['amount_owed'];
    }

    $netBalance = $totalOwed - $totalOwing;

    // Get current user data
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
} else {
    // If tables don't exist, we'll only show the error message in the template
}

// Template rendering - pass the success message to the template
require_once __DIR__ . '/../../templates/havetopay.php';
