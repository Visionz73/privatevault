<?php
// Controller for the main HaveToPay dashboard view
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];

// Initialize variables to prevent undefined variable errors
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
    // Make sure tables exist
    $tableCreationOutput = ''; // Renamed for clarity
    ob_start();
    require_once __DIR__ . '/../../database/havetopay_tables.php';
    $tableCreationOutput = ob_get_clean();

    // For success/error message handling from table creation
    if (!empty($tableCreationOutput)) {
        if (strpos(strtolower($tableCreationOutput), 'error') !== false || strpos(strtolower($tableCreationOutput), 'fail') !== false) {
            // If table creation script itself outputted an error, prioritize this message.
            $errorMessage = "Error during HaveToPay table setup: " . htmlspecialchars($tableCreationOutput);
        } elseif (strpos($tableCreationOutput, 'successfully') !== false) {
            $successMessage = htmlspecialchars($tableCreationOutput);
        }
    }
    
    // Only proceed if no critical error from table setup
    if (empty($errorMessage)) {
        // First, get the structure of the users table to determine available columns
        $userColumns = [];
        $columnsQuery = $pdo->query("SHOW COLUMNS FROM users");
        while ($column = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
            $userColumns[] = $column['Field'];
        }

        // Define display name fields based on what's available in the database
        $nameFields = '';
        if (in_array('name', $userColumns)) {
            $nameFields = 'u.name';
        } elseif (in_array('fullname', $userColumns)) {
            $nameFields = 'u.fullname';
        } elseif (in_array('first_name', $userColumns) && in_array('last_name', $userColumns)) {
            $nameFields = 'u.first_name, u.last_name';
        } elseif (in_array('display_name', $userColumns)) {
            $nameFields = 'u.display_name';
        } else {
            // Fallback to username if no name fields are available
            $nameFields = 'u.username AS name';
        }

        // Get balances between current user and others
        function getBalances($pdo, $userId, $nameFields) {
            // This calculates what others owe the current user
            $othersOweQuery = "
                SELECT u.id, u.username, $nameFields,
                       SUM(ep.share_amount) as amount_owed
                FROM expenses e
                JOIN expense_participants ep ON e.id = ep.expense_id
                JOIN users u ON ep.user_id = u.id
                WHERE e.payer_id = ? AND ep.user_id != ? AND ep.is_settled = 0
                GROUP BY u.id
            ";
            
            // This calculates what the current user owes others
            $userOwesQuery = "
                SELECT u.id, u.username, $nameFields,
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
            $othersOwe = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process results to ensure consistent format regardless of name fields
            foreach ($othersOwe as $balance) {
                $displayName = getDisplayName($balance);
                $balance['display_name'] = $displayName;
                $balances['others_owe'][] = $balance;
            }
            
            $stmt = $pdo->prepare($userOwesQuery);
            $stmt->execute([$userId, $userId]);
            $userOwes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process results to ensure consistent format regardless of name fields
            foreach ($userOwes as $balance) {
                $displayName = getDisplayName($balance);
                $balance['display_name'] = $displayName;
                $balances['user_owes'][] = $balance;
            }
            
            return $balances;
        }

        // Helper function to get display name from various field combinations
        function getDisplayName($userData) {
            if (isset($userData['first_name']) && isset($userData['last_name'])) {
                return $userData['first_name'] . ' ' . $userData['last_name'];
            } elseif (isset($userData['name'])) {
                return $userData['name'];
            } elseif (isset($userData['fullname'])) {
                return $userData['fullname'];
            } elseif (isset($userData['display_name'])) {
                return $userData['display_name'];
            } else {
                return $userData['username'];
            }
        }

        // Get recent expenses involving the current user
        function getRecentExpenses($pdo, $userId, $nameFields) {
            $query = "
                SELECT e.*,  -- e.* will include title
                    u.username as payer_name,
                    $nameFields,
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
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process results to ensure consistent format
            foreach ($expenses as &$expense) {
                $expense['payer_display_name'] = getDisplayName($expense);
            }
            
            return $expenses;
        }

        try {
            // Get all users for expense participant selection
            $userQuery = "SELECT id, username, $nameFields FROM users WHERE id != ? ORDER BY username";
            $userStmt = $pdo->prepare($userQuery);
            $userStmt->execute([$userId]);
            $allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);

            // Add display names to all users
            foreach ($allUsers as &$user) {
                $user['display_name'] = getDisplayName($user);
            }

            // Get balances and recent expenses
            $balances = getBalances($pdo, $userId, $nameFields);
            $recentExpenses = getRecentExpenses($pdo, $userId, $nameFields);

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
            $userStmt = $pdo->prepare("SELECT *, $nameFields FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
            $currentUser['display_name'] = getDisplayName($currentUser);

        } catch (PDOException $e) {
            // If we encounter a database error, use fallback data
            // This error message will be shown if $errorMessage was not already set by table creation failure
            if (empty($errorMessage)) {
                $errorMessage = "We encountered an issue retrieving your data. Using sample data for now.";
            }
            error_log("HaveToPay database error: " . $e->getMessage());
            
            // Setup fallback demo data
            $allUsers = [
                ['id' => 2, 'username' => 'user1', 'display_name' => 'Sample User 1'],
                ['id' => 3, 'username' => 'user2', 'display_name' => 'Sample User 2'],
            ];
            
            $balances = [
                'others_owe' => [
                    ['id' => 2, 'username' => 'user1', 'display_name' => 'Sample User 1', 'amount_owed' => 25.50],
                ],
                'user_owes' => [
                    ['id' => 3, 'username' => 'user2', 'display_name' => 'Sample User 2', 'amount_owed' => 10.25],
                ]
            ];
            
            $recentExpenses = [
                [
                    'id' => 1,
                    'title' => 'Sample Expense Title', // Added title for consistency with template
                    'description' => 'Sample Lunch',
                    'amount' => 30.75,
                    'expense_date' => date('Y-m-d'),
                    'payer_name' => $_SESSION['username'] ?? 'sample_payer',
                    'payer_display_name' => $_SESSION['username'] ?? 'Sample Payer',
                    'participant_count' => 3
                ]
            ];
            
            $totalOwed = 25.50;
            $totalOwing = 10.25;
            $netBalance = $totalOwed - $totalOwing;
            
            $currentUser = [
                'id' => $userId,
                'username' => $_SESSION['username'] ?? 'Unknown User', // Safer fallback
                'display_name' => $_SESSION['username'] ?? 'Unknown User' // Safer fallback
            ];
        }
    } // End of if (empty($errorMessage)) from table setup
} catch (Exception $e) {
    // Catch-all for any other errors
    // This error message will be shown if not already set
    if (empty($errorMessage)) {
        $errorMessage = "An unexpected error occurred. Please try again later.";
    }
    error_log("HaveToPay unexpected error: " . $e->getMessage());
    
    // Setup basic fallback data if not already set by PDOException fallback
    if (empty($allUsers) && empty($recentExpenses)) { // Check if fallback data was already populated
        $allUsers = [];
        $balances = ['others_owe' => [], 'user_owes' => []];
        $recentExpenses = [];
        $totalOwed = 0;
        $totalOwing = 0;
        $netBalance = 0;
        $currentUser = ['username' => $_SESSION['username'] ?? 'Unknown User', 'display_name' => $_SESSION['username'] ?? 'Unknown User'];
    }
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay.php';
?>
