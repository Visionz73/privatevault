<?php
// Set up necessary tables for group tags functionality
require_once __DIR__ . '/../src/lib/db.php';

try {
    // Create group_tags table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS group_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            color VARCHAR(20) NOT NULL DEFAULT '#4A90E2',
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Create group_tag_assignments table for many-to-many relationship
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS group_tag_assignments (
            group_id INT NOT NULL,
            tag_id INT NOT NULL,
            assigned_by INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (group_id, tag_id),
            FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES group_tags(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Create index for faster lookups
    $result = $pdo->query("
        SELECT COUNT(*) as idx_exists
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
        AND table_name = 'group_tag_assignments'
        AND index_name = 'idx_tag_id'
    ");
    
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row['idx_exists'] == 0) {
        $pdo->exec("CREATE INDEX idx_tag_id ON group_tag_assignments(tag_id)");
    }

} catch (PDOException $e) {
    error_log('Error creating group tags tables: ' . $e->getMessage());
}
?>
