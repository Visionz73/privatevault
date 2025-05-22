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
        SELECT id, username FROM users WHERE id != ? ORDER BY username
    ");
    $userStmt->execute([$userId]);
    $allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? ''); // Read title
        $description = trim($_POST['description'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $currency = $_POST['currency'] ?? 'EUR';
        $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
        $participants = $_POST['participants'] ?? [];
        
        // Form validation
        if (empty($title)) { // Validate title
            $errors[] = 'Title is required';
        }
        if (empty($description) && !empty($title) && $title === ($description = trim($_POST['description'] ?? ''))) {
            // If description was initially empty but title was provided,
            // and they happen to be the same (e.g. if description was auto-filled from title by browser),
            // we can consider description optional or set it to title.
            // For now, let's make description optional if title is present.
            // Or, you might decide description is also required.
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
                    INSERT INTO expenses (title, description, amount, payer_id, expense_date)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $expenseStmt->execute([$title, $description, $amount, $userId, $expenseDate]);
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
    $errors[] = 'An error occurred. Please try again later.';
    error_log('HaveToPay add page error: ' . $e->getMessage());
    
    // If users can't be loaded, provide empty array
    $allUsers = [];
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay_add.php';
