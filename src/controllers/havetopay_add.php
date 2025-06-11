<?php
// Controller for adding new shared expenses
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];
$errors = [];
$success = '';

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
    
    // Get all users for participant selection
    if ($hasFirstName && $hasLastName) {
        $userStmt = $pdo->prepare("
            SELECT id, username, first_name, last_name FROM users WHERE id != ? ORDER BY username
        ");
    } else {
        $userStmt = $pdo->prepare("
            SELECT id, username FROM users WHERE id != ? ORDER BY username
        ");
    }
    $userStmt->execute([$userId]);
    $allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display name to users
    foreach ($allUsers as &$user) {
        $user['display_name'] = $hasFirstName && $hasLastName && 
            !empty($user['first_name']) && !empty($user['last_name']) ? 
            $user['first_name'] . ' ' . $user['last_name'] : 
            $user['username'];
    }
    
    // Determine which groups table is in use
    $groupsTable = 'user_groups';
    $groupMembersTable = 'user_group_members';
    
    try {
        $pdo->query("SELECT 1 FROM $groupsTable LIMIT 1");
    } catch (Exception $e) {
        $groupsTable = 'groups';
        $groupMembersTable = 'group_members';
    }
    
    // Get user groups
    try {
        $groupStmt = $pdo->prepare("
            SELECT g.id, g.name
            FROM $groupsTable g
            JOIN $groupMembersTable m ON g.id = m.group_id
            WHERE m.user_id = ?
            ORDER BY g.name
        ");
        $groupStmt->execute([$userId]);
        $allGroups = $groupStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // If there's an error, provide empty groups array
        $allGroups = [];
        error_log('Error fetching groups: ' . $e->getMessage());
    }
    
    // Get expense categories
    try {
        $categoryStmt = $pdo->query("SELECT id, name, icon FROM expense_categories ORDER BY name");
        $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Provide default categories if table doesn't exist
        $categories = [
            ['id' => 1, 'name' => 'Food', 'icon' => 'fa-utensils'],
            ['id' => 2, 'name' => 'Transportation', 'icon' => 'fa-car'],
            ['id' => 9, 'name' => 'Other', 'icon' => 'fa-question-circle']
        ];
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
        $expenseCategory = $_POST['category'] ?? 'Other';
        $participants = $_POST['participants'] ?? [];
        $groupId = !empty($_POST['group_id']) ? intval($_POST['group_id']) : null;
        
        // Form validation
        if (empty($title)) {
            $errors[] = 'Title is required';
        }
        
        if ($amount <= 0) {
            $errors[] = 'Amount must be greater than zero';
        }
        
        if (empty($participants)) {
            $errors[] = 'Select at least one participant';
        }
        
        // Process if no errors
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();
                
                // Check if the expenses table has the right columns
                $hasGroupIdColumn = true;
                $hasCategoryColumn = true;
                
                try {
                    $columnsResult = $pdo->query("DESCRIBE expenses");
                    $expenseColumns = [];
                    while ($column = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
                        $expenseColumns[] = $column['Field'];
                    }
                    $hasGroupIdColumn = in_array('group_id', $expenseColumns);
                    $hasCategoryColumn = in_array('expense_category', $expenseColumns);
                } catch (Exception $e) {
                    $hasGroupIdColumn = false;
                    $hasCategoryColumn = false;
                }
                
                // Insert the expense with appropriate columns
                if ($hasGroupIdColumn && $hasCategoryColumn) {
                    $expenseStmt = $pdo->prepare("
                        INSERT INTO expenses (title, description, amount, payer_id, group_id, expense_date, expense_category)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $expenseStmt->execute([$title, $description, $amount, $userId, $groupId, $expenseDate, $expenseCategory]);
                } elseif ($hasGroupIdColumn) {
                    $expenseStmt = $pdo->prepare("
                        INSERT INTO expenses (title, description, amount, payer_id, group_id, expense_date)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $expenseStmt->execute([$title, $description, $amount, $userId, $groupId, $expenseDate]);
                } elseif ($hasCategoryColumn) {
                    $expenseStmt = $pdo->prepare("
                        INSERT INTO expenses (title, description, amount, payer_id, expense_date, expense_category)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $expenseStmt->execute([$title, $description, $amount, $userId, $expenseDate, $expenseCategory]);
                } else {
                    $expenseStmt = $pdo->prepare("
                        INSERT INTO expenses (title, description, amount, payer_id, expense_date)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $expenseStmt->execute([$title, $description, $amount, $userId, $expenseDate]);
                }
                
                $expenseId = $pdo->lastInsertId();
                
                // Calculate equal share
                $participantCount = count($participants);
                $equalShare = $amount / $participantCount;
                
                // Insert participant records
                $participantStmt = $pdo->prepare("
                    INSERT INTO expense_participants (expense_id, user_id, share_amount)
                    VALUES (?, ?, ?)
                ");
                
                foreach ($participants as $participantId) {
                    $participantStmt->execute([$expenseId, $participantId, $equalShare]);
                }
                
                $pdo->commit();
                $success = 'Expense added successfully';
                
                // Redirect to the main HaveToPay page
                header('Location: havetopay.php?success=added');
                exit;
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errors[] = 'Database error: ' . $e->getMessage();
                error_log('HaveToPay add expense error: ' . $e->getMessage());
            }
        }
    }
} catch (Exception $e) {
    $errors[] = 'An error occurred: ' . $e->getMessage();
    error_log('HaveToPay add page error: ' . $e->getMessage());
    
    // If data can't be loaded, provide empty arrays
    $allUsers = [];
    $allGroups = [];
    $categories = [];
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay_add.php';
?>
