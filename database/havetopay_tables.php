<?php
/**
 * This file creates the necessary tables for the HaveToPay module
 * if they don't already exist.
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

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Track created tables
    $tablesCreated = [];
    
    // Check if we need to create the groups table for foreign key references
    $needsGroupsTable = false;
    if (!tableExists($pdo, 'groups')) {
        $needsGroupsTable = true;
        $sql = "CREATE TABLE groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'groups';
        
        // Create group_members table
        $sql = "CREATE TABLE group_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_membership (group_id, user_id)
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'group_members';
    }
    
    // 1. Create expenses table if not exists
    if (!tableExists($pdo, 'expenses')) {
        $foreignKeyGroupClause = $needsGroupsTable ? 
            ", FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL" : 
            "";
            
        $sql = "CREATE TABLE expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL, -- Added title field
            description TEXT NULL, -- Changed description to allow NULL
            amount DECIMAL(10, 2) NOT NULL,
            payer_id INT NOT NULL,
            group_id INT NULL,
            expense_date DATE NOT NULL,
            expense_category VARCHAR(50) NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE CASCADE
            $foreignKeyGroupClause
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expenses';
    }
    
    // 2. Create expense_participants table if not exists
    if (!tableExists($pdo, 'expense_participants')) {
        $sql = "CREATE TABLE expense_participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            expense_id INT NOT NULL,
            user_id INT NOT NULL,
            share_amount DECIMAL(10, 2) NOT NULL,
            is_settled TINYINT(1) DEFAULT 0,
            settled_date TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_participation (expense_id, user_id)
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expense_participants';
    }
    
    // 3. Create settlements table if not exists
    if (!tableExists($pdo, 'settlements')) {
        $sql = "CREATE TABLE settlements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            payer_id INT NOT NULL,
            receiver_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            settlement_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE RESTRICT,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE RESTRICT
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'settlements';
    }
    
    // 4. Create expense_categories table if not exists
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
        
        $stmt = $pdo->prepare("INSERT INTO expense_categories (name, icon) VALUES (?, ?)");
        foreach ($categories as $category) {
            // Catch and ignore duplicate key errors
            try {
                $stmt->execute($category);
            } catch (PDOException $e) {
                if ($e->getCode() != 23000) { // 23000 is duplicate key error
                    throw $e;
                }
            }
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    // Show success message if any tables were created
    if (!empty($tablesCreated)) {
        echo "HaveToPay tables created successfully: " . implode(', ', $tablesCreated);
    }
    
} catch (PDOException $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Output error information (consider logging this instead in production)
    echo "Database error: " . $e->getMessage();
    
    // If this was included via require, we should still let the script continue
    // but log the error for investigation
    error_log("HaveToPay tables creation error: " . $e->getMessage());
}
?>
