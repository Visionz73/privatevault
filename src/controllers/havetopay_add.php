<?php
// Controller for adding new shared expenses
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];
$errors = [];
$success = '';

// Get all users for participant selection
$userStmt = $pdo->prepare("
    SELECT id, username, CONCAT(first_name, ' ', last_name) as full_name 
    FROM users 
    WHERE id != ? 
    ORDER BY username
");
$userStmt->execute([$userId]);
$allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $currency = $_POST['currency'] ?? 'EUR';
    $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
    $participants = $_POST['participants'] ?? [];
    $splitMethod = $_POST['split_method'] ?? 'equal';
    $customAmounts = $_POST['custom_amounts'] ?? [];
    
    // Form validation
    if (empty($title)) {
        $errors[] = 'Titel ist erforderlich';
    }
    
    if ($amount <= 0) {
        $errors[] = 'Betrag muss größer als Null sein';
    }
    
    if (empty($participants)) {
        $errors[] = 'Wähle mindestens einen Teilnehmer aus';
    }
    
    // Process if no errors
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert the expense
            $expenseStmt = $pdo->prepare("
                INSERT INTO expenses (title, description, amount, currency, payer_id, expense_date)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $expenseStmt->execute([$title, $description, $amount, $currency, $userId, $expenseDate]);
            $expenseId = $pdo->lastInsertId();
            
            // Calculate shares based on split method
            $shares = [];
            
            if ($splitMethod === 'equal') {
                // Include the payer in the participant count for equal splitting
                $participantCount = count($participants) + 1;
                $equalShare = $amount / $participantCount;
                
                // Add shares for each participant (excluding the payer)
                foreach ($participants as $participantId) {
                    $shares[$participantId] = $equalShare;
                }
                
            } else if ($splitMethod === 'custom') {
                // Custom splitting - use provided amounts
                $totalCustomAmount = 0;
                foreach ($participants as $participantId) {
                    $customAmount = floatval($customAmounts[$participantId] ?? 0);
                    $shares[$participantId] = $customAmount;
                    $totalCustomAmount += $customAmount;
                }
                
                // Validate total matches expense amount
                if (abs($totalCustomAmount - $amount) > 0.01) {
                    throw new Exception('Die Summe der individuellen Anteile stimmt nicht mit dem Gesamtbetrag überein');
                }
            }
            
            // Insert participant records
            $participantStmt = $pdo->prepare("
                INSERT INTO expense_participants (expense_id, user_id, share_amount)
                VALUES (?, ?, ?)
            ");
            
            foreach ($shares as $participantId => $shareAmount) {
                $participantStmt->execute([$expenseId, $participantId, $shareAmount]);
            }
            
            $pdo->commit();
            $success = 'Ausgabe erfolgreich hinzugefügt';
            
            // Redirect to the main HaveToPay page
            header('Location: /havetopay.php?success=added');
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Fehler: ' . $e->getMessage();
        }
    }
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay_add.php';
