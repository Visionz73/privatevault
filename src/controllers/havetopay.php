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
$filteredExpenses = [];
$currentUser = [];
$allUsers = [];
$allGroups = [];

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
    
    // Silently ensure tables exist without showing messages
    ob_start();
    require_once __DIR__ . '/../../database/havetopay_tables.php';
    ob_end_clean(); // Discard any output from table creation
    
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
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users ORDER BY username");
    } else {
        $stmt = $pdo->prepare("SELECT id, username FROM users ORDER BY username");
    }
    $stmt->execute();
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to all users
    foreach ($allUsers as &$user) {
        $user['display_name'] = $hasFirstName && $hasLastName && 
            !empty($user['first_name']) && !empty($user['last_name']) ? 
            $user['first_name'] . ' ' . $user['last_name'] : 
            $user['username'];
    }
    
    // Get all groups for filter dropdown
    try {
        // Determine which groups table exists
        $groupsTable = 'user_groups';
        try {
            $pdo->query("SELECT 1 FROM user_groups LIMIT 1");
        } catch (Exception $e) {
            $groupsTable = 'groups';
        }
        
        $stmt = $pdo->prepare("SELECT id, name FROM $groupsTable ORDER BY name");
        $stmt->execute();
        $allGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $allGroups = [];
        error_log('Error fetching groups for filter: ' . $e->getMessage());
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
    
    // Get filter parameters
    $statusFilter = $_GET['status'] ?? '';
    $userFilter = $_GET['user'] ?? '';
    $groupFilter = $_GET['group'] ?? '';

    // Get filtered expenses with settlement status
    $whereConditions = [];
    $params = [$userId, $userId];
    
    // Build WHERE clause based on filters
    if ($statusFilter) {
        switch ($statusFilter) {
            case 'pending':
                $whereConditions[] = "settlement_stats.settlement_status = 'pending'";
                break;
            case 'settled':
                $whereConditions[] = "settlement_stats.settlement_status = 'fully_settled'";
                break;
            case 'partially_settled':
                $whereConditions[] = "settlement_stats.settlement_status = 'partially_settled'";
                break;
        }
    }
    
    if ($userFilter) {
        if ($userFilter === 'me') {
            $whereConditions[] = "e.payer_id = ?";
            $params[] = $userId;
        } elseif ($userFilter === 'involved') {
            // Show expenses where user is either payer or participant
            $whereConditions[] = "(e.payer_id = ? OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?))";
            $params[] = $userId;
            $params[] = $userId;
        } else {
            // Specific user filter
            $whereConditions[] = "(e.payer_id = ? OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?))";
            $params[] = $userFilter;
            $params[] = $userFilter;
        }
    }
    
    if ($groupFilter) {
        if ($groupFilter === 'no_group') {
            $whereConditions[] = "e.group_id IS NULL";
        } else {
            $whereConditions[] = "e.group_id = ?";
            $params[] = $groupFilter;
        }
    }
    
    $whereClause = !empty($whereConditions) ? 'AND ' . implode(' AND ', $whereConditions) : '';
    
    // Base query condition (user must be involved in the expense)
    $baseCondition = "(e.payer_id = ? OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?))";
    
    // Get expenses with settlement statistics
    $userNameFields = $hasFirstName && $hasLastName ? 
        "u.first_name as payer_first_name, u.last_name as payer_last_name," : 
        "";
        
    $stmt = $pdo->prepare("
        SELECT e.*,
               u.username as payer_name,
               $userNameFields
               g.name as group_name,
               settlement_stats.participant_count,
               settlement_stats.settled_count,
               settlement_stats.settlement_status
        FROM expenses e
        JOIN users u ON e.payer_id = u.id
        LEFT JOIN $groupsTable g ON e.group_id = g.id
        JOIN (
            SELECT 
                ep.expense_id,
                COUNT(*) as participant_count,
                SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) as settled_count,
                CASE 
                    WHEN COUNT(*) = SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) THEN 'fully_settled'
                    WHEN SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) > 0 THEN 'partially_settled'
                    ELSE 'pending'
                END as settlement_status
            FROM expense_participants ep
            GROUP BY ep.expense_id
        ) settlement_stats ON e.id = settlement_stats.expense_id
        WHERE $baseCondition
        $whereClause
        ORDER BY e.created_at DESC
        LIMIT 50
    ");
    $stmt->execute($params);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to expenses
    foreach ($expenses as $expense) {
        $expense['payer_display_name'] = $hasFirstName && $hasLastName && 
            !empty($expense['payer_first_name']) && !empty($expense['payer_last_name']) ? 
            $expense['payer_first_name'] . ' ' . $expense['payer_last_name'] : 
            $expense['payer_name'];
        $filteredExpenses[] = $expense;
    }
    
    // If no filters applied, show only non-fully-settled expenses by default
    if (empty($statusFilter) && empty($userFilter) && empty($groupFilter)) {
        $filteredExpenses = []; // Reset the array
        $stmt = $pdo->prepare("
            SELECT e.*,
                   u.username as payer_name,
                   $userNameFields
                   g.name as group_name,
                   settlement_stats.participant_count,
                   settlement_stats.settled_count,
                   settlement_stats.settlement_status
            FROM expenses e
            JOIN users u ON e.payer_id = u.id
            LEFT JOIN $groupsTable g ON e.group_id = g.id
            JOIN (
                SELECT 
                    ep.expense_id,
                    COUNT(*) as participant_count,
                    SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) as settled_count,
                    CASE 
                        WHEN COUNT(*) = SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) THEN 'fully_settled'
                        WHEN SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) > 0 THEN 'partially_settled'
                        ELSE 'pending'
                    END as settlement_status
                FROM expense_participants ep
                GROUP BY ep.expense_id
            ) settlement_stats ON e.id = settlement_stats.expense_id
            WHERE $baseCondition
            AND settlement_stats.settlement_status != 'fully_settled'
            ORDER BY e.created_at DESC
            LIMIT 15
        ");
        $stmt->execute([$userId, $userId]);
        $recentExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add display names to recent expenses
        foreach ($recentExpenses as $expense) {
            $expense['payer_display_name'] = $hasFirstName && $hasLastName && 
                !empty($expense['payer_first_name']) && !empty($expense['payer_last_name']) ? 
                $expense['payer_first_name'] . ' ' . $expense['payer_last_name'] : 
                $expense['payer_name'];
            $filteredExpenses[] = $expense;
        }
    }
    
    // Auto-update settlement status when all participants have paid
    $autoSettleStmt = $pdo->prepare("
        UPDATE expenses e
        JOIN (
            SELECT 
                ep.expense_id,
                COUNT(*) as total_participants,
                SUM(CASE WHEN ep.is_settled = 1 THEN 1 ELSE 0 END) as settled_participants
            FROM expense_participants ep
            GROUP BY ep.expense_id
            HAVING total_participants = settled_participants
        ) settlement_check ON e.id = settlement_check.expense_id
        SET e.updated_at = NOW()
        WHERE e.id = settlement_check.expense_id
    ");
    $autoSettleStmt->execute();
    
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
        case 'deleted':
            $successMessage = 'Expense deleted successfully';
            break;
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_expense') {
    $expenseId = intval($_POST['expense_id'] ?? 0);
    
    if ($expenseId > 0) {
        try {
            // Check if user owns this expense or is an admin
            $checkStmt = $pdo->prepare("SELECT payer_id FROM expenses WHERE id = ?");
            $checkStmt->execute([$expenseId]);
            $expense = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($expense && ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false))) {
                $pdo->beginTransaction();
                
                // Delete participants first (due to foreign key constraints)
                $deleteParticipantsStmt = $pdo->prepare("DELETE FROM expense_participants WHERE expense_id = ?");
                $deleteParticipantsStmt->execute([$expenseId]);
                
                // Delete the expense
                $deleteExpenseStmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
                $deleteExpenseStmt->execute([$expenseId]);
                
                $pdo->commit();
                $successMessage = 'Expense deleted successfully';
                
                // Redirect to avoid resubmission
                header('Location: havetopay.php?success=deleted');
                exit;
            } else {
                $errorMessage = 'You do not have permission to delete this expense';
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errorMessage = 'Error deleting expense: ' . $e->getMessage();
            error_log('HaveToPay delete error: ' . $e->getMessage());
        }
    }
}

// Use existing navbar.php instead of header/footer
require_once __DIR__ . '/../../templates/navbar.php';
require_once __DIR__ . '/../../templates/havetopay.php';
?>
