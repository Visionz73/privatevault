<?php

// 1. Ensure the user is logged in
require_once __DIR__ . '/../../lib/auth.php';
requireLogin();

// 2. Get the current userId from the session
$userId = $_SESSION['user_id'];

// 3. Include the database connection
require_once __DIR__ . '/../../lib/db.php';

// 4. Initialize variables
$widgetTotalOwed = 0;
$widgetTotalOwing = 0;
$widgetNetBalance = 0;

try {
    // 5. Fetch the total amount others owe the current user
    $stmtOwed = $pdo->prepare("
        SELECT SUM(ep.share_amount) AS total_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        WHERE e.payer_id = :user_id 
        AND ep.user_id != :user_id 
        AND ep.is_settled = 0
    ");
    $stmtOwed->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtOwed->execute();
    $resultOwed = $stmtOwed->fetch(PDO::FETCH_ASSOC);
    if ($resultOwed && $resultOwed['total_owed']) {
        $widgetTotalOwed = (float)$resultOwed['total_owed'];
    }

    // 6. Fetch the total amount the current user owes others
    $stmtOwing = $pdo->prepare("
        SELECT SUM(ep.share_amount) AS total_owing
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        WHERE ep.user_id = :user_id 
        AND e.payer_id != :user_id 
        AND ep.is_settled = 0
    ");
    $stmtOwing->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtOwing->execute();
    $resultOwing = $stmtOwing->fetch(PDO::FETCH_ASSOC);
    if ($resultOwing && $resultOwing['total_owing']) {
        $widgetTotalOwing = (float)$resultOwing['total_owing'];
    }

    // 7. Calculate the net balance
    $widgetNetBalance = $widgetTotalOwed - $widgetTotalOwing;

} catch (PDOException $e) {
    // 9. Include error handling and log any errors.
    // If an error occurs, set default values (0) for the variables.
    error_log("Error in havetopay_widget.php: " . $e->getMessage());
    $widgetTotalOwed = 0;
    $widgetTotalOwing = 0;
    $widgetNetBalance = 0;
}

// 8. The script should make these variables available for inclusion in another PHP script.
// This is implicitly done as the script will be included, and variables will be in scope.

?>
