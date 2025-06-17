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
    
    // Get all users for participant selection (INCLUDING current user)
    if ($hasFirstName && $hasLastName) {
        $userStmt = $pdo->prepare("
            SELECT id, username, first_name, last_name FROM users ORDER BY username
        ");
    } else {
        $userStmt = $pdo->prepare("
            SELECT id, username FROM users ORDER BY username
        ");
    }
    $userStmt->execute();
    $allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display name to all users (including current user)
    $allUsersIncludingMe = [];
    foreach ($allUsers as $user) {
        $user['display_name'] = $hasFirstName && $hasLastName && 
            !empty($user['first_name']) && !empty($user['last_name']) ? 
            $user['first_name'] . ' ' . $user['last_name'] : 
            $user['username'];
        $allUsersIncludingMe[] = $user;
    }
    
    // Separate current user from others for reference
    $allUsers = array_filter($allUsersIncludingMe, function($user) use ($userId) {
        return $user['id'] != $userId;
    });
    
    // Determine which groups table is in use
    $groupsTable = 'user_groups';
    $groupMembersTable = 'user_group_members';
    
    try {
        $pdo->query("SELECT 1 FROM $groupsTable LIMIT 1");
    } catch (Exception $e) {
        $groupsTable = 'groups';
        $groupMembersTable = 'group_members';
    }
    
    // Get user groups with member information
    try {
        $groupStmt = $pdo->prepare("
            SELECT g.id, g.name, g.description,
                   COUNT(DISTINCT m.user_id) as member_count
            FROM $groupsTable g
            JOIN $groupMembersTable m ON g.id = m.group_id
            WHERE m.user_id = ? OR g.id IN (
                SELECT group_id FROM $groupMembersTable WHERE user_id = ?
            )
            GROUP BY g.id, g.name, g.description
            ORDER BY g.name
        ");
        $groupStmt->execute([$userId, $userId]);
        $groups = $groupStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get member details for each group
        $allGroups = [];
        foreach ($groups as $group) {
            $memberStmt = $pdo->prepare("
                SELECT m.user_id 
                FROM $groupMembersTable m 
                WHERE m.group_id = ? AND m.user_id != ?
            ");
            $memberStmt->execute([$group['id'], $userId]);
            $members = $memberStmt->fetchAll(PDO::FETCH_COLUMN);
            
            $group['members'] = $members;
            $allGroups[] = $group;
        }
    } catch (Exception $e) {
        $allGroups = [];
        error_log('Fehler beim Laden der Gruppen: ' . $e->getMessage());
    }
    
    // Get expense categories
    try {
        $categoryStmt = $pdo->query("SELECT id, name, icon FROM expense_categories ORDER BY name");
        $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Provide default categories if table doesn't exist
        $categories = [
            ['id' => 1, 'name' => 'Essen & Trinken', 'icon' => 'fa-utensils'],
            ['id' => 2, 'name' => 'Transport', 'icon' => 'fa-car'],
            ['id' => 3, 'name' => 'Wohnen', 'icon' => 'fa-home'],
            ['id' => 4, 'name' => 'Unterhaltung', 'icon' => 'fa-film'],
            ['id' => 5, 'name' => 'Einkaufen', 'icon' => 'fa-shopping-cart'],
            ['id' => 6, 'name' => 'Reisen', 'icon' => 'fa-plane'],
            ['id' => 7, 'name' => 'Gesundheit', 'icon' => 'fa-medkit'],
            ['id' => 8, 'name' => 'Sonstiges', 'icon' => 'fa-question-circle']
        ];
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
        $expenseCategory = $_POST['category'] ?? 'Sonstiges';
        $participants = $_POST['participants'] ?? [];
        $groupId = !empty($_POST['group_id']) ? intval($_POST['group_id']) : null;
        
        // Form validation with German messages
        if (empty($title)) {
            $errors[] = 'Beschreibung ist erforderlich';
        }
        
        if ($amount <= 0) {
            $errors[] = 'Betrag muss größer als null sein';
        }
        
        if (empty($participants)) {
            $errors[] = 'Wählen Sie mindestens einen Teilnehmer aus';
        }
        
        // Ensure current user is always included as participant
        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
        }
        
        // Validate participants exist
        if (!empty($participants)) {
            $participantIds = array_map('intval', $participants);
            $placeholders = str_repeat('?,', count($participantIds) - 1) . '?';
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id IN ($placeholders)");
            $checkStmt->execute($participantIds);
            $validCount = $checkStmt->fetchColumn();
            
            if ($validCount != count($participantIds)) {
                $errors[] = 'Einige ausgewählte Teilnehmer existieren nicht';
            }
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
                $success = 'Ausgabe erfolgreich erstellt';
                
                // Redirect to the main HaveToPay page
                header('Location: havetopay.php?success=added');
                exit;
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                error_log('HaveToPay Ausgabe hinzufügen Fehler: ' . $e->getMessage());
            }
        }
    }
} catch (Exception $e) {
    $errors[] = 'Ein Fehler ist aufgetreten: ' . $e->getMessage();
    error_log('HaveToPay Seite hinzufügen Fehler: ' . $e->getMessage());
    
    // If data can't be loaded, provide empty arrays
    $allUsers = [];
    $allGroups = [];
    $categories = [];
}

// Template rendering
require_once __DIR__ . '/../../templates/havetopay_add.php';
?>
