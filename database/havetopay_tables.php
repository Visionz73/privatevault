<?php
/**
 * Creates the necessary tables for the HaveToPay module
 */

// Check if we already have a database connection
if (!isset($pdo)) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../src/lib/db.php';
}

// Function to check if a table exists
function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Function to check if a column exists in a table
function columnExists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SELECT $column FROM $table LIMIT 0");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Track created tables and modifications
    $tablesCreated = [];
    $tablesModified = [];
    
    // Determine which groups table exists (user_groups or groups)
    $groupsTable = 'user_groups';
    $groupMembersTable = 'user_group_members';
    
    if (!tableExists($pdo, 'user_groups') && tableExists($pdo, 'groups')) {
        $groupsTable = 'groups';
        $groupMembersTable = 'group_members';
    }
    
    // Create the groups table if it doesn't exist at all
    if (!tableExists($pdo, $groupsTable)) {
        $sql = "CREATE TABLE $groupsTable (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )";
        $pdo->exec($sql);
        $tablesCreated[] = $groupsTable;
        
        // Also create the group members table
        $sql = "CREATE TABLE $groupMembersTable (
            id INT AUTO_INCREMENT PRIMARY KEY,
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES $groupsTable(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_membership (group_id, user_id)
        )";
        $pdo->exec($sql);
        $tablesCreated[] = $groupMembersTable;
    }
    
    // 1. Create or modify expenses table
    if (!tableExists($pdo, 'expenses')) {
        $sql = "CREATE TABLE expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            amount DECIMAL(15, 2) NOT NULL,  -- Changed from DECIMAL(10, 2) to allow larger amounts
            payer_id INT NOT NULL,
            group_id INT NULL,
            expense_date DATE NOT NULL,
            expense_category VARCHAR(50) DEFAULT 'Other',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (group_id) REFERENCES $groupsTable(id) ON DELETE SET NULL
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expenses';
    } else {
        // Check and add title column if missing
        if (!columnExists($pdo, 'expenses', 'title')) {
            $sql = "ALTER TABLE expenses ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT 'Unnamed Expense' AFTER id";
            $pdo->exec($sql);
            $tablesModified[] = 'expenses (added title column)';
        }
        
        // Check and add group_id column if missing
        if (!columnExists($pdo, 'expenses', 'group_id')) {
            $sql = "ALTER TABLE expenses ADD COLUMN group_id INT NULL AFTER payer_id";
            $pdo->exec($sql);
            
            // Add foreign key constraint
            $sql = "ALTER TABLE expenses ADD CONSTRAINT fk_expense_group FOREIGN KEY (group_id) REFERENCES $groupsTable(id) ON DELETE SET NULL";
            $pdo->exec($sql);
            $tablesModified[] = 'expenses (added group_id column)';
        }
        
        // Check and add expense_category column if missing
        if (!columnExists($pdo, 'expenses', 'expense_category')) {
            $sql = "ALTER TABLE expenses ADD COLUMN expense_category VARCHAR(50) DEFAULT 'Other' AFTER expense_date";
            $pdo->exec($sql);
            $tablesModified[] = 'expenses (added expense_category column)';
        }
        
        // Fix amount column size if it exists
        try {
            $sql = "ALTER TABLE expenses MODIFY amount DECIMAL(15, 2) NOT NULL";
            $pdo->exec($sql);
            $tablesModified[] = 'expenses (increased amount column size)';
        } catch (Exception $e) {
            error_log("Failed to alter expenses.amount: " . $e->getMessage());
        }
    }
    
    // 2. Create expense_participants table if not exists
    if (!tableExists($pdo, 'expense_participants')) {
        $sql = "CREATE TABLE expense_participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            expense_id INT NOT NULL,
            user_id INT NOT NULL,
            share_amount DECIMAL(15, 2) NOT NULL,  -- Changed from DECIMAL(10, 2) to allow larger amounts
            is_settled TINYINT(1) DEFAULT 0,
            settled_date TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_participation (expense_id, user_id)
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expense_participants';
    } else {
        // Check and add settled_date column if missing
        if (!columnExists($pdo, 'expense_participants', 'settled_date')) {
            $sql = "ALTER TABLE expense_participants ADD COLUMN settled_date TIMESTAMP NULL DEFAULT NULL AFTER is_settled";
            $pdo->exec($sql);
            $tablesModified[] = 'expense_participants (added settled_date column)';
        }
        
        // Fix share_amount column size
        try {
            $sql = "ALTER TABLE expense_participants MODIFY share_amount DECIMAL(15, 2) NOT NULL";
            $pdo->exec($sql);
            $tablesModified[] = 'expense_participants (increased share_amount column size)';
        } catch (Exception $e) {
            error_log("Failed to alter expense_participants.share_amount: " . $e->getMessage());
        }
    }
    
    // 3. Create expense_categories table if not exists
    if (!tableExists($pdo, 'expense_categories')) {
        $sql = "CREATE TABLE expense_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            icon VARCHAR(50) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_category_name (name)
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expense_categories';
        
        // Insert default categories
        $categories = [
            ['Food', 'fa-utensils'],
            ['Transportation', 'fa-car'],
            ['Housing', 'fa-home'],
            ['Utilities', 'fa-bolt'],
            ['Entertainment', 'fa-film'],
            ['Shopping', 'fa-shopping-cart'],
            ['Travel', 'fa-plane'],
            ['Health', 'fa-medkit'],
            ['Other', 'fa-question-circle']
        ];
        
        $insertStmt = $pdo->prepare("INSERT INTO expense_categories (name, icon) VALUES (?, ?)");
        foreach ($categories as $category) {
            try {
                $insertStmt->execute($category);
            } catch (PDOException $e) {
                // Ignore duplicate key errors
                if ($e->getCode() != '23000') {
                    throw $e;
                }
            }
        }
    }

    // Commit the transaction
    $pdo->commit();
    
    // Show success message
    $messages = [];
    if (!empty($tablesCreated)) {
        $messages[] = "Tables created: " . implode(', ', $tablesCreated);
    }
    if (!empty($tablesModified)) {
        $messages[] = "Tables modified: " . implode(', ', $tablesModified);
    }
    
    if (!empty($messages)) {
        echo "HaveToPay tables setup successfully. " . implode(' ', $messages);
    } else {
        echo "All HaveToPay tables already exist with the correct structure.";
    }
    
} catch (PDOException $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Output error information
    echo "Database error: " . $e->getMessage();
    error_log("HaveToPay tables creation error: " . $e->getMessage());
}
?>
