<?php
// Create the required group tables if they don't exist
require_once __DIR__ . '/../src/lib/db.php';

try {
    // Check if user_groups table exists
    $result = $pdo->query("SHOW TABLES LIKE 'user_groups'")->rowCount();
    
    if ($result == 0) {
        // Create user_groups table
        $pdo->exec("
            CREATE TABLE user_groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Create user_group_members table
        $pdo->exec("
            CREATE TABLE user_group_members (
                group_id INT NOT NULL,
                user_id INT NOT NULL,
                added_by INT NOT NULL,
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (group_id, user_id),
                FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (added_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
} catch (PDOException $e) {
    // Log error instead of displaying it directly
    error_log('Error setting up group tables: ' . $e->getMessage());
}
?>
