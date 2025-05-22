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
    // Get all users for participant selection
    $userStmt = $pdo->prepare("
        SELECT id, username, first_name, last_name FROM users WHERE id != ? ORDER BY username
    ");
    $userStmt->execute([$userId]);
    $allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display name to users
    foreach ($allUsers as &$user) {
        $user['display_name'] = !empty($user['first_name']) && !empty($user['last_name']) ? 
            $user['first_name'] . ' ' . $user['last_name'] : $user['username'];
    }
    
    // Get user groups
    $groupStmt = $pdo->prepare("
        SELECT g.id, g.name
        FROM user_groups g
        JOIN user_group_members m ON g.id = m.group_id
        WHERE m.user_id = ?
        ORDER BY g.name
    ");
    $groupStmt->execute([$userId]);
    $allGroups = $groupStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get expense categories
    $categoryStmt = $pdo->query("SELECT id, name, icon FROM expense_categories ORDER BY name");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

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
                
                // Insert the expense
                $expenseStmt = $pdo->prepare("
                    INSERT INTO expenses (title, description, amount, payer_id, group_id, expense_date, expense_category)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $expenseStmt->execute([$title, $description, $amount, $userId, $groupId, $expenseDate, $expenseCategory]);
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
