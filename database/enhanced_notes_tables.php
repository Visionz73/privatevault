<?php
// Enhanced notes database structure for Second Brain functionality

require_once __DIR__ . '/../config.php';

try {
    // Enhanced notes table with additional fields
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            color VARCHAR(7) DEFAULT '#fbbf24',
            is_pinned BOOLEAN DEFAULT FALSE,
            is_archived BOOLEAN DEFAULT FALSE,
            node_position_x FLOAT DEFAULT NULL,
            node_position_y FLOAT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at),
            INDEX idx_pinned (is_pinned),
            INDEX idx_archived (is_archived)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Enhanced note_tags table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            tag_name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_note_tag (note_id, tag_name),
            INDEX idx_tag_name (tag_name),
            INDEX idx_note_id (note_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Bidirectional note links table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source_note_id INT NOT NULL,
            target_note_id INT NOT NULL,
            link_type ENUM('reference', 'mention', 'relates_to', 'follows_from') DEFAULT 'reference',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT NOT NULL,
            FOREIGN KEY (source_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (target_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_link (source_note_id, target_note_id, link_type),
            INDEX idx_source_note (source_note_id),
            INDEX idx_target_note (target_note_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Note versions for history tracking
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_versions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            version_number INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT NOT NULL,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_note_id (note_id),
            INDEX idx_version (note_id, version_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Note reminders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_reminders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            user_id INT NOT NULL,
            reminder_date DATETIME NOT NULL,
            is_completed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_reminder_date (reminder_date),
            INDEX idx_user_reminders (user_id, is_completed)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Semantic clusters for automatic grouping
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_clusters (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            cluster_name VARCHAR(255) NOT NULL,
            description TEXT,
            color VARCHAR(7) DEFAULT '#6366f1',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Note cluster membership
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_cluster_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cluster_id INT NOT NULL,
            note_id INT NOT NULL,
            similarity_score FLOAT DEFAULT 0.0,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cluster_id) REFERENCES note_clusters(id) ON DELETE CASCADE,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_cluster_member (cluster_id, note_id),
            INDEX idx_cluster_id (cluster_id),
            INDEX idx_note_id (note_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci    ");

    // Only output if run directly (not included)
    if (basename($_SERVER['PHP_SELF']) === 'enhanced_notes_tables.php') {
        echo "Enhanced notes tables created successfully!\n";
    }

} catch (PDOException $e) {
    // Only output if run directly (not included)
    if (basename($_SERVER['PHP_SELF']) === 'enhanced_notes_tables.php') {
        echo "Error creating enhanced tables: " . $e->getMessage() . "\n";
    }
}
?>
