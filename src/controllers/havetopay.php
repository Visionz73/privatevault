<?php
// Controller for the main HaveToPay dashboard view
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];

// Initialize variables
$errorMessage = '';
$successMessage = '';
$balances = ['others_owe' => [], 'user_owes' => []];
$totalOwed = 0;
$totalOwing = 0;
$netBalance = 0;
$recentExpenses = [];
$currentUser = [];
$allUsers = [];

try {
    // First, check users table structure
    $columnsResult = $pdo->query("DESCRIBE users");
    $userColumns = [];
    while ($column = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
        $userColumns[] = $column['Field'];
    }
    
    // Determine if name fields exist
    $hasFirstName = in_array('first_name', $userColumns);
    $hasLastName = in_array('last_name', $userColumns);
    
    // Make sure tables exist
    $tableCreationOutput = '';
    ob_start();
    require_once __DIR__ . '/../../database/havetopay_tables.php';
    $tableCreationOutput = ob_get_clean();

    // Process any success or error messages from table creation
    if (!empty($tableCreationOutput)) {
        if (strpos(strtolower($tableCreationOutput), 'error') !== false) {
            $errorMessage = "Error during table setup: " . htmlspecialchars($tableCreationOutput);
        } elseif (strpos($tableCreationOutput, 'successfully') !== false) {
            $successMessage = htmlspecialchars($tableCreationOutput);
        }
    }
    
    // Get current user data
    if ($hasFirstName && $hasLastName) {
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
    }
    $stmt->execute([$userId]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentUser) {
        throw new Exception("User data could not be retrieved.");
    }
    
    // Prepare user display name
    $currentUser['display_name'] = $hasFirstName && $hasLastName && 
        !empty($currentUser['first_name']) && !empty($currentUser['last_name']) ? 
        $currentUser['first_name'] . ' ' . $currentUser['last_name'] : 
        $currentUser['username'];
    
    // Get all users for expense participant selection
    if ($hasFirstName && $hasLastName) {
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users WHERE id != ? ORDER BY username");
    } else {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ? ORDER BY username");
    }
    $stmt->execute([$userId]);
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to all users
    foreach ($allUsers as &$user) {
        $user['display_name'] = $hasFirstName && $hasLastName && 
            !empty($user['first_name']) && !empty($user['last_name']) ? 
            $user['first_name'] . ' ' . $user['last_name'] : 
            $user['username'];
    }
    
    // Get balances: what others owe current user
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
    ");
    $stmt->execute([$userId, $userId]);
    $othersOwe = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to balances
    foreach ($othersOwe as $balance) {
        $balance['display_name'] = $hasFirstName && $hasLastName && 
            !empty($balance['first_name']) && !empty($balance['last_name']) ? 
            $balance['first_name'] . ' ' . $balance['last_name'] : 
            $balance['username'];
        $balances['others_owe'][] = $balance;
    }
    
    // Get balances: what current user owes others
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, $nameFields,
               SUM(ep.share_amount) as amount_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        JOIN users u ON e.payer_id = u.id
        WHERE ep.user_id = ? AND e.payer_id != ? AND ep.is_settled = 0
        GROUP BY u.id
    ");
    $stmt->execute([$userId, $userId]);
    $userOwes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to balances
    foreach ($userOwes as $balance) {
        $balance['display_name'] = $hasFirstName && $hasLastName && 
            !empty($balance['first_name']) && !empty($balance['last_name']) ? 
            $balance['first_name'] . ' ' . $balance['last_name'] : 
            $balance['username'];
        $balances['user_owes'][] = $balance;
    }
    
    // Get recent expenses involving the current user
    $userNameFields = $hasFirstName && $hasLastName ? 
        "u.first_name as payer_first_name, u.last_name as payer_last_name," : 
        "";
        
    $stmt = $pdo->prepare("
        SELECT e.*,
               u.username as payer_name,
               $userNameFields
               (SELECT COUNT(*) FROM expense_participants WHERE expense_id = e.id) as participant_count
        FROM expenses e
        JOIN users u ON e.payer_id = u.id
        WHERE e.payer_id = ? 
        OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?)
        ORDER BY e.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$userId, $userId]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to expenses
    foreach ($expenses as $expense) {
        $expense['payer_display_name'] = $hasFirstName && $hasLastName && 
            !empty($expense['payer_first_name']) && !empty($expense['payer_last_name']) ? 
            $expense['payer_first_name'] . ' ' . $expense['payer_last_name'] : 
            $expense['payer_name'];
        $recentExpenses[] = $expense;
    }
    
    // Calculate total balances
    foreach ($balances['others_owe'] as $balance) {
        $totalOwed += $balance['amount_owed'];
    }
    
    foreach ($balances['user_owes'] as $balance) {
        $totalOwing += $balance['amount_owed'];
    }
    
    $netBalance = $totalOwed - $totalOwing;
    
} catch (Exception $e) {
    $errorMessage = "An error occurred: " . $e->getMessage();
    error_log("HaveToPay error: " . $e->getMessage());
}

// Check for success message in query string (from add/edit operations)
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $successMessage = 'Expense added successfully';
            break;
        case 'settled':
            $successMessage = 'Payment marked as settled';
            break;
    }
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay.php';
?>
