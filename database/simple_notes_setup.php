<?php
// Erstelle die grundlegenden Notes-Tabellen für das Dashboard
require_once __DIR__ . '/../src/lib/db.php';

try {
    echo "Erstelle Notes-Tabellen...\n";
    
    // Notes table - kompatibel mit der bestehenden API
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            color VARCHAR(7) DEFAULT '#fbbf24',
            is_pinned BOOLEAN DEFAULT FALSE,
            is_archived BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at),
            INDEX idx_pinned (is_pinned),
            INDEX idx_archived (is_archived)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Note tags table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            tag_name VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_note_tag (note_id, tag_name),
            INDEX idx_tag_name (tag_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "Notes-Tabellen erfolgreich erstellt!\n";
    
} catch (PDOException $e) {
    echo "Fehler beim Erstellen der Notes-Tabellen: " . $e->getMessage() . "\n";
}
?>
