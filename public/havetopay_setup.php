<?php
// Script to initialize HaveToPay module
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

// Only allow admins to access this page
requireLogin();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: index.php');
    exit;
}

$messages = [];

// Create tables
ob_start();
require_once __DIR__ . '/../database/havetopay_tables.php';
$tableMessage = ob_get_clean();
if (!empty($tableMessage)) {
    $messages[] = $tableMessage;
}

// Add test data if requested
if (isset($_GET['add_test_data']) && $_GET['add_test_data'] == '1') {
    try {
        // Get some user IDs
        $userQuery = $pdo->query("SELECT id, username FROM users LIMIT 5");
        $users = $userQuery->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) < 2) {
            throw new Exception("Need at least 2 users to create test data");
        }
        
        $pdo->beginTransaction();
        
        // Sample expense data
        $sampleExpenses = [
            [
                'title' => 'Team Lunch',
                'description' => 'Lunch at the Italian restaurant with the team.',
                'amount' => 45.50,
                'payer_id' => $users[0]['id'],
                'expense_date' => date('Y-m-d', strtotime('-5 days'))
            ],
            [
                'title' => 'Movie Night Tickets',
                'description' => 'Tickets for the new action movie and some snacks.',
                'amount' => 32.75,
                'payer_id' => $users[1]['id'],
                'expense_date' => date('Y-m-d', strtotime('-2 days'))
            ],
            [
                'title' => 'Weekly Groceries',
                'description' => 'Standard weekly grocery shopping.',
                'amount' => 67.20,
                'payer_id' => $users[0]['id'],
                'expense_date' => date('Y-m-d', strtotime('-1 day'))
            ]
        ];
        
        $expenseStmt = $pdo->prepare("
            INSERT INTO expenses (title, description, amount, payer_id, expense_date)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $participantStmt = $pdo->prepare("
            INSERT INTO expense_participants (expense_id, user_id, share_amount)
            VALUES (?, ?, ?)
        ");
        
        foreach ($sampleExpenses as $expense) {
            $expenseStmt->execute([
                $expense['title'],
                $expense['description'],
                $expense['amount'],
                $expense['payer_id'],
                $expense['expense_date']
            ]);
            
            $expenseId = $pdo->lastInsertId();
            
            // Add participants (excluding payer)
            $participantCount = 0;
            foreach ($users as $user) {
                if ($user['id'] != $expense['payer_id'] && $participantCount < 3) {
                    // Split amount evenly
                    $shareAmount = round($expense['amount'] / 3, 2); // Assuming 3 participants including payer
                    $participantStmt->execute([$expenseId, $user['id'], $shareAmount]);
                    $participantCount++;
                }
            }
        }
        
        $pdo->commit();
        $messages[] = "Test data added successfully: " . count($sampleExpenses) . " expenses created.";
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $messages[] = "Error adding test data: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HaveToPay Setup</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>HaveToPay Module Setup</h1>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>Setup Results</h2>
            </div>
            <div class="card-body">
                <?php if (empty($messages)): ?>
                    <p class="text-muted">No changes were made. The module appears to be already set up.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($messages as $message): ?>
                            <li class="list-group-item">
                                <?= htmlspecialchars($message) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>Actions</h2>
            </div>
            <div class="card-body">
                <a href="havetopay_setup.php?add_test_data=1" class="btn btn-primary">Add Test Data</a>
                <a href="view_tables.php" class="btn btn-secondary">View Tables</a>
                <a href="havetopay.php" class="btn btn-success">Go to HaveToPay</a>
                <a href="index.php" class="btn btn-link">Return to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
