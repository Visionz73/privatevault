<?php
// Controller for the main HaveToPay dashboard view
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];

// DISABLED FOR TESTING: Make sure tables exist
// Comment out the database table checks for now
/*
$tableMessage = '';
ob_start();
require_once __DIR__ . '/../../database/havetopay_tables.php';
$tableMessage = ob_get_clean();

// For success message handling
$successMessage = '';
if (!empty($tableMessage) && strpos($tableMessage, 'successfully') !== false) {
    $successMessage = $tableMessage;
}
*/

// Set some default values for testing
$successMessage = 'Testing mode: SQL operations disabled';
$errorMessage = '';
$tablesExist = true; // Assume tables exist for testing

// DISABLED FOR TESTING: Get balances between current user and others
/*
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
*/

// TEST DATA - Use this instead of database queries for testing
// Mock current user
$currentUser = [
    'id' => $userId,
    'username' => $_SESSION['username'] ?? 'testuser',
    'first_name' => 'Test',
    'last_name' => 'User'
];

// Mock balances
$balances = [
    'others_owe' => [
        ['id' => 1, 'username' => 'alice', 'first_name' => 'Alice', 'last_name' => 'Smith', 'amount_owed' => 25.50],
        ['id' => 2, 'username' => 'bob', 'first_name' => 'Bob', 'last_name' => 'Jones', 'amount_owed' => 15.00],
    ],
    'user_owes' => [
        ['id' => 3, 'username' => 'carol', 'first_name' => 'Carol', 'last_name' => 'Taylor', 'amount_owed' => 10.75],
    ],
];

// Mock recent expenses
$recentExpenses = [
    [
        'id' => 1, 
        'description' => 'Lunch', 
        'amount' => 30.00, 
        'payer_id' => $userId, 
        'expense_date' => date('Y-m-d'),
        'payer_name' => $currentUser['username'],
        'participant_count' => 3
    ],
    [
        'id' => 2, 
        'description' => 'Movie tickets', 
        'amount' => 25.50, 
        'payer_id' => 1, 
        'expense_date' => date('Y-m-d', strtotime('-1 day')),
        'payer_name' => 'alice',
        'participant_count' => 2
    ],
];

// Mock all users
$allUsers = [
    ['id' => 1, 'username' => 'alice', 'full_name' => 'Alice Smith'],
    ['id' => 2, 'username' => 'bob', 'full_name' => 'Bob Jones'],
    ['id' => 3, 'username' => 'carol', 'full_name' => 'Carol Taylor'],
];

// Calculate total balance from mock data
$totalOwed = 40.50; // sum of others_owe
$totalOwing = 10.75; // sum of user_owes
$netBalance = $totalOwed - $totalOwing;

// Template rendering
require_once __DIR__ . '/../../templates/havetopay.php';
?>
