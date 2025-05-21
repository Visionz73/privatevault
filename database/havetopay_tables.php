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
    
    // 1. Create expenses table if not exists
    if (!tableExists($pdo, 'expenses')) {
        $sql = "CREATE TABLE expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            description VARCHAR(255) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payer_id INT NOT NULL,
            expense_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE CASCADE
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
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_participation (expense_id, user_id)
        )";
        $pdo->exec($sql);
        $tablesCreated[] = 'expense_participants';
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
