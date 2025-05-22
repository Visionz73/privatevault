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

    // 1. First check if users table has required fields
    $userColumnsExist = false;
    try {
        $stmt = $pdo->query("DESCRIBE users");
        $userColumnsExist = true;
    } catch (PDOException $e) {
        echo "Error: Users table not found. Please create a users table first.<br>";
        throw $e;
    }
    
    // 2. Check and create groups table if needed
    if (!tableExists($pdo, 'groups')) {
        $sql = "CREATE TABLE groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'groups';
        
        // Add a default group
        $pdo->exec("INSERT INTO groups (name, description, created_by) VALUES ('Default Group', 'Default expense group', 1)");
    }
    
    // 3. Check and create group_members table if needed
    if (!tableExists($pdo, 'group_members')) {
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
    
    // 4. Check and create expenses table if needed
    if (!tableExists($pdo, 'expenses')) {
        $sql = "CREATE TABLE expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            amount DECIMAL(10, 2) NOT NULL,
            payer_id INT NOT NULL,
            group_id INT NULL,
            expense_date DATE NOT NULL,
            expense_category VARCHAR(50) DEFAULT 'Other',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expenses';
    }
    
    // 5. Check and create expense_participants table if needed
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
    
    // 6. Check and create expense_categories table if needed
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
                // Ignore duplicate key errors (23000)
                if ($e->getCode() != '23000') {
                    throw $e;
                }
            }
        }
    }

    // Commit the transaction
    $pdo->commit();
    
    // Display success message
    if (!empty($tablesCreated)) {
        echo "HaveToPay tables created successfully: " . implode(', ', $tablesCreated);
    } else {
        echo "All required HaveToPay tables already exist.";
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
