<?php
// Create necessary tables for user groups functionality
require_once __DIR__ . '/../src/lib/db.php';

try {
    // Create user_groups table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB;
    ");

    // Use consistent table name - user_group_members
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_group_members (
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            added_by INT NOT NULL,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (group_id, user_id),
            FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (added_by) REFERENCES users(id)
        ) ENGINE=InnoDB;
    ");

    // Check and create index if needed
    $result = $pdo->query("
        SELECT COUNT(*) as idx_exists
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
        AND table_name = 'user_group_members'
        AND index_name = 'user_group_idx'
    ");
    
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row['idx_exists'] == 0) {
        $pdo->exec("CREATE INDEX user_group_idx ON user_group_members(user_id)");
    }

} catch (PDOException $e) {
    // In production, handle this error gracefully
    error_log('Error creating group tables: ' . $e->getMessage());
}
?>
