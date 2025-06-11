<?php
// Controller for viewing expense details
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];

$expenseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = '';
$errors = [];

if (!$expenseId) {
    header('Location: havetopay.php?error=no_expense');
    exit;
}

// Check users table structure
$columnsResult = $pdo->query("DESCRIBE users");
$userColumns = [];
while ($column = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
    $userColumns[] = $column['Field'];
}

// Determine if name fields exist
$hasFirstName = in_array('first_name', $userColumns);
$hasLastName = in_array('last_name', $userColumns);

// Get the expense details with appropriate name fields
$nameFields = $hasFirstName && $hasLastName ? 
    "CONCAT(u.first_name, ' ', u.last_name) as payer_full_name" : 
    "u.username as payer_full_name";

$expenseStmt = $pdo->prepare("
    SELECT e.*, u.username as payer_name, $nameFields
    FROM expenses e
    JOIN users u ON e.payer_id = u.id
    WHERE e.id = ?
");
$expenseStmt->execute([$expenseId]);
$expense = $expenseStmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) {
    header('Location: havetopay.php?error=expense_not_found');
    exit;
}

// Check if user is related to this expense
$accessStmt = $pdo->prepare("
    SELECT COUNT(*) FROM expense_participants 
    WHERE expense_id = ? AND user_id = ?
");
$accessStmt->execute([$expenseId, $userId]);
$userHasAccess = ($accessStmt->fetchColumn() > 0) || ($expense['payer_id'] == $userId);

if (!$userHasAccess) {
    header('Location: havetopay.php?error=permission_denied');
    exit;
}

// Get participants with appropriate name fields
$participantNameFields = $hasFirstName && $hasLastName ? 
    "CONCAT(u.first_name, ' ', u.last_name) as full_name" : 
    "u.username as full_name";

$participantsStmt = $pdo->prepare("
    SELECT ep.*, 
           u.username, $participantNameFields
    FROM expense_participants ep
    JOIN users u ON ep.user_id = u.id
    WHERE ep.expense_id = ?
    ORDER BY ep.is_settled, u.username
");
$participantsStmt->execute([$expenseId]);
$participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle settling debts and deleting expenses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'settle' && isset($_POST['participant_id'])) {
        $participantId = (int)$_POST['participant_id'];
        
        // Check if the current user is either the payer or the participant
        $checkStmt = $pdo->prepare("
            SELECT ep.id, e.payer_id, ep.user_id 
            FROM expense_participants ep
            JOIN expenses e ON ep.expense_id = e.id
            WHERE ep.id = ? AND ep.expense_id = ?
        ");
        $checkStmt->execute([$participantId, $expenseId]);
        $participant = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participant && ($userId == $participant['payer_id'] || $userId == $participant['user_id'])) {
            // Update the participant record to mark as settled
            $settleStmt = $pdo->prepare("
                UPDATE expense_participants
                SET is_settled = 1, settled_at = NOW()
                WHERE id = ?
            ");
            
            if ($settleStmt->execute([$participantId])) {
                $success = 'Zahlung erfolgreich als beglichen markiert';
                
                // Refresh participant data
                $participantsStmt->execute([$expenseId]);
                $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $errors[] = 'Fehler beim Aktualisieren des Zahlungsstatus';
            }
        } else {
            $errors[] = 'Keine Berechtigung, diese Zahlung als beglichen zu markieren';
        }
    } elseif ($_POST['action'] === 'delete_expense') {
        // Check if the current user is the payer or an admin
        if ($userId == $expense['payer_id'] || ($_SESSION['is_admin'] ?? false)) {
            try {
                $pdo->beginTransaction();
                
                // Delete participants first (due to foreign key constraints)
                $deleteParticipantsStmt = $pdo->prepare("DELETE FROM expense_participants WHERE expense_id = ?");
                $deleteParticipantsStmt->execute([$expenseId]);
                
                // Delete the expense
                $deleteExpenseStmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
                $deleteExpenseStmt->execute([$expenseId]);
                
                $pdo->commit();
                
                // Redirect to main page with success message
                header('Location: havetopay.php?success=deleted');
                exit;
                
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = 'Fehler beim Löschen der Ausgabe: ' . $e->getMessage();
                error_log('HaveToPay delete error: ' . $e->getMessage());
            }
        } else {
            $errors[] = 'Du hast keine Berechtigung, diese Ausgabe zu löschen';
        }
    } elseif ($_POST['action'] === 'remove_participant' && isset($_POST['participant_id'])) {
        $participantId = (int)$_POST['participant_id'];
        
        // Check if the current user is the payer or the participant being removed
        $checkStmt = $pdo->prepare("
            SELECT ep.id, e.payer_id, ep.user_id, ep.share_amount 
            FROM expense_participants ep
            JOIN expenses e ON ep.expense_id = e.id
            WHERE ep.id = ? AND ep.expense_id = ?
        ");
        $checkStmt->execute([$participantId, $expenseId]);
        $participant = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participant && ($userId == $participant['payer_id'] || $userId == $participant['user_id'])) {
            try {
                $pdo->beginTransaction();
                
                // Remove the participant
                $removeStmt = $pdo->prepare("DELETE FROM expense_participants WHERE id = ?");
                $removeStmt->execute([$participantId]);
                
                // Recalculate shares for remaining participants
                $remainingStmt = $pdo->prepare("SELECT COUNT(*) FROM expense_participants WHERE expense_id = ?");
                $remainingStmt->execute([$expenseId]);
                $remainingCount = $remainingStmt->fetchColumn();
                
                if ($remainingCount > 0) {
                    $newShare = $expense['amount'] / $remainingCount;
                    $updateShareStmt = $pdo->prepare("UPDATE expense_participants SET share_amount = ? WHERE expense_id = ?");
                    $updateShareStmt->execute([$newShare, $expenseId]);
                }
                
                $pdo->commit();
                $success = 'Teilnehmer erfolgreich entfernt';
                
                // Refresh participant data
                $participantsStmt->execute([$expenseId]);
                $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = 'Fehler beim Entfernen des Teilnehmers: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'Keine Berechtigung, diesen Teilnehmer zu entfernen';
        }
    }
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay_detail.php';
