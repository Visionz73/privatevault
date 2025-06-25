-- Enhanced Zettelkasten Database Schema
-- Vollständige SQL-Statements zum Erstellen der Datenbankstruktur

-- ================================================
-- 1. Erweitere bestehende Notes Tabelle
-- ================================================

-- Füge neue Spalten zur notes Tabelle hinzu
ALTER TABLE notes 
ADD COLUMN IF NOT EXISTS is_shared BOOLEAN DEFAULT FALSE COMMENT 'Gibt an ob die Notiz geteilt wurde',
ADD COLUMN IF NOT EXISTS shared_with TEXT DEFAULT NULL COMMENT 'JSON array of user IDs die Zugriff haben',
ADD COLUMN IF NOT EXISTS visibility ENUM('private', 'shared', 'public') DEFAULT 'private' COMMENT 'Sichtbarkeitsebene der Notiz',
ADD COLUMN IF NOT EXISTS links_count INT DEFAULT 0 COMMENT 'Anzahl der Verknüpfungen zu/von dieser Notiz',
ADD COLUMN IF NOT EXISTS position_x FLOAT DEFAULT NULL COMMENT 'X-Position für Graph-Ansicht',
ADD COLUMN IF NOT EXISTS position_y FLOAT DEFAULT NULL COMMENT 'Y-Position für Graph-Ansicht';

-- Füge Indizes für bessere Performance hinzu
ALTER TABLE notes 
ADD INDEX IF NOT EXISTS idx_visibility (visibility),
ADD INDEX IF NOT EXISTS idx_shared (is_shared),
ADD INDEX IF NOT EXISTS idx_links_count (links_count);

-- ================================================
-- 2. Note Links Tabelle - Für Zettelkasten-Verknüpfungen
-- ================================================

CREATE TABLE IF NOT EXISTS note_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_note_id INT NOT NULL COMMENT 'ID der Quell-Notiz',
    target_note_id INT NOT NULL COMMENT 'ID der Ziel-Notiz',
    link_type ENUM('reference', 'backlink', 'bidirectional') DEFAULT 'reference' COMMENT 'Art der Verknüpfung',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungszeitpunkt',
    created_by INT NOT NULL COMMENT 'Benutzer der die Verknüpfung erstellt hat',
    
    FOREIGN KEY (source_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (target_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_link (source_note_id, target_note_id),
    INDEX idx_source (source_note_id),
    INDEX idx_target (target_note_id),
    INDEX idx_type (link_type),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Verknüpfungen zwischen Notizen für das Zettelkasten-System';

-- ================================================
-- 3. Note Shares Tabelle - Für spezifische Freigaben
-- ================================================

CREATE TABLE IF NOT EXISTS note_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL COMMENT 'ID der geteilten Notiz',
    shared_by INT NOT NULL COMMENT 'Benutzer der die Notiz geteilt hat',
    shared_with INT NOT NULL COMMENT 'Benutzer mit dem die Notiz geteilt wurde',
    permission_level ENUM('read', 'edit', 'comment') DEFAULT 'read' COMMENT 'Berechtigungsebene',
    shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Freigabe',
    expires_at TIMESTAMP NULL COMMENT 'Ablaufzeit der Freigabe (optional)',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Ob die Freigabe aktiv ist',
    
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_share (note_id, shared_with),
    INDEX idx_shared_with (shared_with),
    INDEX idx_note_id (note_id),
    INDEX idx_shared_by (shared_by),
    INDEX idx_active (is_active),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Freigaben von Notizen zwischen Benutzern';

-- ================================================
-- 4. Graph Nodes Tabelle - Für persistente Knotenpositionen
-- ================================================

CREATE TABLE IF NOT EXISTS graph_nodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Benutzer dem die Knotenposition gehört',
    note_id INT NOT NULL COMMENT 'ID der Notiz',
    x_position FLOAT NOT NULL COMMENT 'X-Position im Graph',
    y_position FLOAT NOT NULL COMMENT 'Y-Position im Graph',
    is_pinned BOOLEAN DEFAULT FALSE COMMENT 'Ob der Knoten fixiert ist',
    cluster_id VARCHAR(50) DEFAULT NULL COMMENT 'ID des Clusters (für Gruppierung)',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_note (user_id, note_id),
    INDEX idx_user_id (user_id),
    INDEX idx_cluster (cluster_id),
    INDEX idx_pinned (is_pinned)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Persistente Positionen von Notizen in der Graph-Ansicht';

-- ================================================
-- 5. Note Mentions Tabelle - Für automatische Verknüpfungen
-- ================================================

CREATE TABLE IF NOT EXISTS note_mentions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mentioning_note_id INT NOT NULL COMMENT 'Notiz die eine andere erwähnt',
    mentioned_note_id INT NOT NULL COMMENT 'Erwähnte Notiz',
    mention_text VARCHAR(255) NOT NULL COMMENT 'Text der Erwähnung (z.B. [[Note Title]])',
    mention_type ENUM('wiki_link', 'hashtag', 'title_reference') DEFAULT 'wiki_link' COMMENT 'Art der Erwähnung',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (mentioning_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (mentioned_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    
    INDEX idx_mentioning (mentioning_note_id),
    INDEX idx_mentioned (mentioned_note_id),
    INDEX idx_type (mention_type),
    INDEX idx_text (mention_text)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Automatisch erkannte Erwähnungen zwischen Notizen';

-- ================================================
-- 6. Note Collections - Für thematische Gruppierungen
-- ================================================

CREATE TABLE IF NOT EXISTS note_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Besitzer der Sammlung',
    name VARCHAR(255) NOT NULL COMMENT 'Name der Sammlung',
    description TEXT COMMENT 'Beschreibung der Sammlung',
    color VARCHAR(7) DEFAULT '#4A90E2' COMMENT 'Farbe für die Sammlung',
    is_shared BOOLEAN DEFAULT FALSE COMMENT 'Ob die Sammlung geteilt ist',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_shared (is_shared),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Thematische Sammlungen von Notizen';

-- ================================================
-- 7. Note Collection Items - Zuordnung von Notizen zu Sammlungen
-- ================================================

CREATE TABLE IF NOT EXISTS note_collection_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NOT NULL COMMENT 'ID der Sammlung',
    note_id INT NOT NULL COMMENT 'ID der Notiz',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Hinzufügung',
    sort_order INT DEFAULT 0 COMMENT 'Sortierreihenfolge in der Sammlung',
    
    FOREIGN KEY (collection_id) REFERENCES note_collections(id) ON DELETE CASCADE,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_collection_note (collection_id, note_id),
    INDEX idx_sort_order (sort_order),
    INDEX idx_collection (collection_id),
    INDEX idx_note (note_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Zuordnung von Notizen zu thematischen Sammlungen';

-- ================================================
-- 8. Database Triggers für automatische Link-Zählung
-- ================================================

-- Trigger für INSERT: Aktualisiert links_count wenn neue Verknüpfung erstellt wird
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_links_count_insert 
AFTER INSERT ON note_links 
FOR EACH ROW 
BEGIN 
    -- Aktualisiere links_count für source_note
    UPDATE notes SET links_count = (
        SELECT COUNT(*) FROM note_links 
        WHERE source_note_id = NEW.source_note_id OR target_note_id = NEW.source_note_id
    ) WHERE id = NEW.source_note_id;
    
    -- Aktualisiere links_count für target_note
    UPDATE notes SET links_count = (
        SELECT COUNT(*) FROM note_links 
        WHERE source_note_id = NEW.target_note_id OR target_note_id = NEW.target_note_id
    ) WHERE id = NEW.target_note_id;
END//
DELIMITER ;

-- Trigger für DELETE: Aktualisiert links_count wenn Verknüpfung gelöscht wird
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_links_count_delete 
AFTER DELETE ON note_links 
FOR EACH ROW 
BEGIN 
    -- Aktualisiere links_count für source_note
    UPDATE notes SET links_count = (
        SELECT COUNT(*) FROM note_links 
        WHERE source_note_id = OLD.source_note_id OR target_note_id = OLD.source_note_id
    ) WHERE id = OLD.source_note_id;
    
    -- Aktualisiere links_count für target_note
    UPDATE notes SET links_count = (
        SELECT COUNT(*) FROM note_links 
        WHERE source_note_id = OLD.target_note_id OR target_note_id = OLD.target_note_id
    ) WHERE id = OLD.target_note_id;
END//
DELIMITER ;

-- ================================================
-- 9. Views für häufige Abfragen
-- ================================================

-- View für alle Notizen mit ihren Verknüpfungs-Informationen
CREATE OR REPLACE VIEW v_notes_with_links AS
SELECT 
    n.*,
    COUNT(DISTINCT nl_out.id) as outbound_links,
    COUNT(DISTINCT nl_in.id) as inbound_links,
    COUNT(DISTINCT nl_out.id) + COUNT(DISTINCT nl_in.id) as total_links,
    GROUP_CONCAT(DISTINCT CONCAT(nl_out.target_note_id, ':', nl_out.link_type)) as outbound_targets,
    GROUP_CONCAT(DISTINCT CONCAT(nl_in.source_note_id, ':', nl_in.link_type)) as inbound_sources
FROM notes n
LEFT JOIN note_links nl_out ON n.id = nl_out.source_note_id
LEFT JOIN note_links nl_in ON n.id = nl_in.target_note_id
GROUP BY n.id;

-- View für geteilte Notizen mit Benutzerinformationen
CREATE OR REPLACE VIEW v_shared_notes AS
SELECT 
    n.*,
    ns.permission_level,
    ns.shared_at,
    ns.expires_at,
    u_owner.username as owner_username,
    u_shared.username as shared_with_username
FROM notes n
JOIN note_shares ns ON n.id = ns.note_id
JOIN users u_owner ON n.user_id = u_owner.id
JOIN users u_shared ON ns.shared_with = u_shared.id
WHERE ns.is_active = 1
AND (ns.expires_at IS NULL OR ns.expires_at > NOW());

-- ================================================
-- 10. Stored Procedures für komplexe Operationen
-- ================================================

-- Procedure zum Erstellen einer bidirektionalen Verknüpfung
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS CreateBidirectionalLink(
    IN p_source_id INT,
    IN p_target_id INT,
    IN p_user_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Erstelle Link von source zu target
    INSERT IGNORE INTO note_links (source_note_id, target_note_id, link_type, created_by)
    VALUES (p_source_id, p_target_id, 'bidirectional', p_user_id);
    
    -- Erstelle Link von target zu source
    INSERT IGNORE INTO note_links (source_note_id, target_note_id, link_type, created_by)
    VALUES (p_target_id, p_source_id, 'bidirectional', p_user_id);
    
    COMMIT;
END//
DELIMITER ;

-- Procedure zum Löschen aller Links einer Notiz
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS DeleteAllNoteLinks(
    IN p_note_id INT
)
BEGIN
    DELETE FROM note_links 
    WHERE source_note_id = p_note_id OR target_note_id = p_note_id;
END//
DELIMITER ;

-- ================================================
-- 11. Initiale Daten einfügen (optional)
-- ================================================

-- Beispiel-Collections für alle existierenden Benutzer
INSERT IGNORE INTO note_collections (user_id, name, description, color)
SELECT 
    id as user_id,
    'Favoriten' as name,
    'Wichtige und häufig verwendete Notizen' as description,
    '#FFD700' as color
FROM users;

INSERT IGNORE INTO note_collections (user_id, name, description, color)
SELECT 
    id as user_id,
    'Projektideen' as name,
    'Sammlung von Projektideen und Brainstorming' as description,
    '#4CAF50' as color
FROM users;

INSERT IGNORE INTO note_collections (user_id, name, description, color)
SELECT 
    id as user_id,
    'Wissensbasis' as name,
    'Grundlegende Wissensnotizen und Referenzen' as description,
    '#2196F3' as color
FROM users;

-- ================================================
-- 12. Performance-Optimierung
-- ================================================

-- Weitere nützliche Indizes
ALTER TABLE notes ADD INDEX IF NOT EXISTS idx_user_updated (user_id, updated_at DESC);
ALTER TABLE notes ADD INDEX IF NOT EXISTS idx_user_visibility (user_id, visibility);
ALTER TABLE note_links ADD INDEX IF NOT EXISTS idx_created_at (created_at);
ALTER TABLE note_shares ADD INDEX IF NOT EXISTS idx_permission (permission_level);

-- Analysiere Tabellen für bessere Performance
ANALYZE TABLE notes;
ANALYZE TABLE note_links;
ANALYZE TABLE note_shares;
ANALYZE TABLE graph_nodes;
ANALYZE TABLE note_mentions;
ANALYZE TABLE note_collections;
ANALYZE TABLE note_collection_items;

-- ================================================
-- Fertig! 
-- ================================================

-- Zeige alle erstellten Tabellen an
SHOW TABLES LIKE '%note%';

-- Zeige Triggers an
SHOW TRIGGERS LIKE '%note%';

-- Zeige Views an
SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_privatevault LIKE '%note%';
