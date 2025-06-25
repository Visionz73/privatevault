<?php
// Enhanced Zettelkasten Database Tables
require_once __DIR__ . '/../src/lib/db.php';

try {
    echo "Erstelle erweiterte Zettelkasten-Tabellen...\n";
    
    // Erweitere die Notes Tabelle um Zettelkasten-Features
    $pdo->exec("
        ALTER TABLE notes 
        ADD COLUMN IF NOT EXISTS is_shared BOOLEAN DEFAULT FALSE,
        ADD COLUMN IF NOT EXISTS shared_with TEXT DEFAULT NULL COMMENT 'JSON array of user IDs',
        ADD COLUMN IF NOT EXISTS visibility ENUM('private', 'shared', 'public') DEFAULT 'private',
        ADD COLUMN IF NOT EXISTS links_count INT DEFAULT 0,
        ADD COLUMN IF NOT EXISTS position_x FLOAT DEFAULT NULL COMMENT 'Node position for graph view',
        ADD COLUMN IF NOT EXISTS position_y FLOAT DEFAULT NULL COMMENT 'Node position for graph view'
    ");
    
    // Note Links Tabelle für Zettelkasten-Verknüpfungen
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source_note_id INT NOT NULL,
            target_note_id INT NOT NULL,
            link_type ENUM('reference', 'backlink', 'bidirectional') DEFAULT 'reference',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT NOT NULL,
            FOREIGN KEY (source_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (target_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_link (source_note_id, target_note_id),
            INDEX idx_source (source_note_id),
            INDEX idx_target (target_note_id),
            INDEX idx_type (link_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Note Shares Tabelle für spezifische Freigaben
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_shares (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            shared_by INT NOT NULL,
            shared_with INT NOT NULL,
            permission_level ENUM('read', 'edit', 'comment') DEFAULT 'read',
            shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL,
            is_active BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_share (note_id, shared_with),
            INDEX idx_shared_with (shared_with),
            INDEX idx_note_id (note_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Graph Nodes Tabelle für persistente Knotenpositionen
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS graph_nodes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            note_id INT NOT NULL,
            x_position FLOAT NOT NULL,
            y_position FLOAT NOT NULL,
            is_pinned BOOLEAN DEFAULT FALSE,
            cluster_id VARCHAR(50) DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_note (user_id, note_id),
            INDEX idx_user_id (user_id),
            INDEX idx_cluster (cluster_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Note Mentions Tabelle für automatische Verknüpfungen
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_mentions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            mentioning_note_id INT NOT NULL,
            mentioned_note_id INT NOT NULL,
            mention_text VARCHAR(255) NOT NULL,
            mention_type ENUM('wiki_link', 'hashtag', 'title_reference') DEFAULT 'wiki_link',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (mentioning_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (mentioned_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            INDEX idx_mentioning (mentioning_note_id),
            INDEX idx_mentioned (mentioned_note_id),
            INDEX idx_type (mention_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Note Collections für thematische Gruppierungen
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_collections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            color VARCHAR(7) DEFAULT '#4A90E2',
            is_shared BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Note Collection Items
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_collection_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            collection_id INT NOT NULL,
            note_id INT NOT NULL,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            sort_order INT DEFAULT 0,
            FOREIGN KEY (collection_id) REFERENCES note_collections(id) ON DELETE CASCADE,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_collection_note (collection_id, note_id),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Trigger für automatische Link-Zählung
    $pdo->exec("
        CREATE TRIGGER IF NOT EXISTS update_links_count_insert 
        AFTER INSERT ON note_links 
        FOR EACH ROW 
        BEGIN 
            UPDATE notes SET links_count = (
                SELECT COUNT(*) FROM note_links 
                WHERE source_note_id = NEW.source_note_id OR target_note_id = NEW.source_note_id
            ) WHERE id = NEW.source_note_id;
            
            UPDATE notes SET links_count = (
                SELECT COUNT(*) FROM note_links 
                WHERE source_note_id = NEW.target_note_id OR target_note_id = NEW.target_note_id
            ) WHERE id = NEW.target_note_id;
        END
    ");
    
    $pdo->exec("
        CREATE TRIGGER IF NOT EXISTS update_links_count_delete 
        AFTER DELETE ON note_links 
        FOR EACH ROW 
        BEGIN 
            UPDATE notes SET links_count = (
                SELECT COUNT(*) FROM note_links 
                WHERE source_note_id = OLD.source_note_id OR target_note_id = OLD.source_note_id
            ) WHERE id = OLD.source_note_id;
            
            UPDATE notes SET links_count = (
                SELECT COUNT(*) FROM note_links 
                WHERE source_note_id = OLD.target_note_id OR target_note_id = OLD.target_note_id
            ) WHERE id = OLD.target_note_id;
        END    ");
    
    echo "Erweiterte Zettelkasten-Tabellen erfolgreich erstellt!\n";
    
} catch (PDOException $e) {
    echo "Fehler beim Erstellen der Tabellen: " . $e->getMessage() . "\n";
}
?>
