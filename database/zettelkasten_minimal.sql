-- Minimal Zettelkasten Setup - Nur die wichtigsten Tabellen
-- Für schnelle Einrichtung ohne erweiterte Features

-- ================================================
-- 1. Erweitere Notes Tabelle (WICHTIG)
-- ================================================

ALTER TABLE notes 
ADD COLUMN IF NOT EXISTS is_shared BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS visibility ENUM('private', 'shared', 'public') DEFAULT 'private',
ADD COLUMN IF NOT EXISTS links_count INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS position_x FLOAT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS position_y FLOAT DEFAULT NULL;

-- ================================================
-- 2. Note Links - Kern des Zettelkasten Systems
-- ================================================

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
    INDEX idx_target (target_note_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 3. Note Shares - Für Freigaben zwischen Benutzern
-- ================================================

CREATE TABLE IF NOT EXISTS note_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    shared_by INT NOT NULL,
    shared_with INT NOT NULL,
    permission_level ENUM('read', 'edit', 'comment') DEFAULT 'read',
    shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_share (note_id, shared_with),
    INDEX idx_shared_with (shared_with)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 4. Trigger für automatische Link-Zählung
-- ================================================

DELIMITER //
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
END//
DELIMITER ;

DELIMITER //
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
END//
DELIMITER ;

-- Das war's! Minimal Setup komplett.
