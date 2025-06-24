-- ===============================================
-- SECOND BRAIN NOTIZ-SYSTEM - KOMPLETTE SQL STRUKTUR
-- ===============================================
-- Erstellt am: 24. Juni 2025
-- Beschreibung: Vollständige Datenbankstruktur für Second Brain Funktionalität
-- mit Graph-Ansicht, bidirektionalen Links, Tags, Clustering und mehr

-- Basis Users Tabelle (falls noch nicht vorhanden)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- KERN-TABELLEN FÜR NOTIZEN
-- ===============================================

-- Haupttabelle für Notizen mit allen Second Brain Features
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    content_preview VARCHAR(500) GENERATED ALWAYS AS (
        SUBSTRING(REGEXP_REPLACE(content, '<[^>]*>', ''), 1, 500)
    ) STORED,
    color VARCHAR(7) DEFAULT '#fbbf24',
    is_pinned BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    is_favorite BOOLEAN DEFAULT FALSE,
    
    -- Graph-Visualisierung
    node_position_x FLOAT DEFAULT NULL,
    node_position_y FLOAT DEFAULT NULL,
    node_size ENUM('small', 'medium', 'large') DEFAULT 'medium',
    
    -- Metadaten
    word_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    last_viewed_at TIMESTAMP NULL,
    
    -- Zeitstempel
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indizes
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_pinned (is_pinned),
    INDEX idx_archived (is_archived),
    INDEX idx_favorite (is_favorite),
    FULLTEXT idx_search (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- TAG-SYSTEM
-- ===============================================

-- Tag-Definitionstabelle
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#6366f1',
    description TEXT,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_tag (user_id, name),
    INDEX idx_user_id (user_id),
    INDEX idx_name (name),
    INDEX idx_usage_count (usage_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verbindungstabelle zwischen Notizen und Tags
CREATE TABLE IF NOT EXISTS note_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_note_tag (note_id, tag_id),
    INDEX idx_note_id (note_id),
    INDEX idx_tag_id (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- BIDIREKTIONALE VERLINKUNG
-- ===============================================

-- Bidirektionale Links zwischen Notizen
CREATE TABLE IF NOT EXISTS note_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_note_id INT NOT NULL,
    target_note_id INT NOT NULL,
    
    -- Link-Eigenschaften
    link_type ENUM('reference', 'mention', 'relates_to', 'follows_from', 'contradicts') DEFAULT 'reference',
    link_strength FLOAT DEFAULT 1.0, -- Für gewichtete Graphen
    anchor_text VARCHAR(255), -- Der Text, der verlinkt wurde
    context_snippet TEXT, -- Textumgebung um den Link
    
    -- Metadaten
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    is_auto_generated BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (source_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (target_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Verhindert Selbstverlinkung und doppelte Links
    CONSTRAINT chk_no_self_link CHECK (source_note_id != target_note_id),
    UNIQUE KEY unique_link (source_note_id, target_note_id, link_type),
    
    INDEX idx_source_note (source_note_id),
    INDEX idx_target_note (target_note_id),
    INDEX idx_link_type (link_type),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- VERSIONIERUNG
-- ===============================================

-- Versionshistorie für Notizen
CREATE TABLE IF NOT EXISTS note_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    version_number INT NOT NULL DEFAULT 1,
    change_summary VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_note_id (note_id),
    INDEX idx_version (note_id, version_number),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- ERINNERUNGEN & NOTIFICATIONS
-- ===============================================

-- Erinnerungen für Notizen
CREATE TABLE IF NOT EXISTS note_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    user_id INT NOT NULL,
    reminder_date DATETIME NOT NULL,
    reminder_type ENUM('once', 'daily', 'weekly', 'monthly') DEFAULT 'once',
    message VARCHAR(255),
    is_completed BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_reminder_date (reminder_date),
    INDEX idx_user_reminders (user_id, is_completed),
    INDEX idx_active_reminders (is_active, reminder_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- CLUSTERING & AI FEATURES
-- ===============================================

-- Semantische Cluster für automatische Gruppierung
CREATE TABLE IF NOT EXISTS note_clusters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cluster_name VARCHAR(255) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#6366f1',
    cluster_algorithm ENUM('manual', 'keyword', 'semantic', 'temporal') DEFAULT 'manual',
    is_auto_generated BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_auto_generated (is_auto_generated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Zugehörigkeit von Notizen zu Clustern
CREATE TABLE IF NOT EXISTS note_cluster_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cluster_id INT NOT NULL,
    note_id INT NOT NULL,
    similarity_score FLOAT DEFAULT 0.0,
    confidence_level FLOAT DEFAULT 1.0,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cluster_id) REFERENCES note_clusters(id) ON DELETE CASCADE,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_cluster_member (cluster_id, note_id),
    INDEX idx_cluster_id (cluster_id),
    INDEX idx_note_id (note_id),
    INDEX idx_similarity (similarity_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- SUCH- & FILTER-SYSTEM
-- ===============================================

-- Gespeicherte Suchanfragen
CREATE TABLE IF NOT EXISTS saved_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_name VARCHAR(255) NOT NULL,
    search_query TEXT NOT NULL,
    search_filters JSON, -- Tags, Datum, etc.
    usage_count INT DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_usage_count (usage_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- BENUTZER-EINSTELLUNGEN
-- ===============================================

-- Graph-Darstellungseinstellungen pro Benutzer
CREATE TABLE IF NOT EXISTS user_graph_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    
    -- Layout-Einstellungen
    default_layout ENUM('force', 'circular', 'hierarchical', 'grid') DEFAULT 'force',
    node_size_factor FLOAT DEFAULT 1.0,
    link_strength_factor FLOAT DEFAULT 1.0,
    show_labels BOOLEAN DEFAULT TRUE,
    show_clusters BOOLEAN DEFAULT TRUE,
    
    -- Farb-Schema
    theme ENUM('light', 'dark', 'auto') DEFAULT 'auto',
    node_color_scheme JSON,
    
    -- Filter-Einstellungen
    default_filters JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_settings (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- STATISTIKEN & ANALYTICS
-- ===============================================

-- Tägliche Statistiken
CREATE TABLE IF NOT EXISTS daily_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    stat_date DATE NOT NULL,
    
    notes_created INT DEFAULT 0,
    notes_updated INT DEFAULT 0,
    notes_viewed INT DEFAULT 0,
    links_created INT DEFAULT 0,
    tags_used INT DEFAULT 0,
    search_queries INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, stat_date),
    INDEX idx_stat_date (stat_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- TRIGGERS FÜR AUTOMATISIERUNG
-- ===============================================

-- Trigger: Word Count automatisch berechnen
DELIMITER $$
CREATE TRIGGER tr_notes_word_count 
BEFORE UPDATE ON notes
FOR EACH ROW
BEGIN
    IF NEW.content != OLD.content THEN
        SET NEW.word_count = (
            LENGTH(TRIM(NEW.content)) - LENGTH(REPLACE(TRIM(NEW.content), ' ', '')) + 1
        );
    END IF;
END$$

-- Trigger: Automatische Versionierung
CREATE TRIGGER tr_notes_version_on_update
AFTER UPDATE ON notes
FOR EACH ROW
BEGIN
    IF NEW.content != OLD.content OR NEW.title != OLD.title THEN
        INSERT INTO note_versions (note_id, title, content, version_number, created_by)
        SELECT 
            NEW.id,
            OLD.title,
            OLD.content,
            COALESCE(MAX(version_number), 0) + 1,
            NEW.user_id
        FROM note_versions WHERE note_id = NEW.id;
    END IF;
END$$

-- Trigger: Tag Usage Count aktualisieren
CREATE TRIGGER tr_tag_usage_increment
AFTER INSERT ON note_tags
FOR EACH ROW
BEGIN
    UPDATE tags 
    SET usage_count = usage_count + 1 
    WHERE id = NEW.tag_id;
END$$

CREATE TRIGGER tr_tag_usage_decrement
AFTER DELETE ON note_tags
FOR EACH ROW
BEGIN
    UPDATE tags 
    SET usage_count = usage_count - 1 
    WHERE id = OLD.tag_id;
END$$

DELIMITER ;

-- ===============================================
-- VIEWS FÜR HÄUFIGE ABFRAGEN
-- ===============================================

-- View: Notizen mit allen verbundenen Informationen
CREATE OR REPLACE VIEW v_notes_complete AS
SELECT 
    n.id,
    n.user_id,
    n.title,
    n.content,
    n.content_preview,
    n.color,
    n.is_pinned,
    n.is_archived,
    n.is_favorite,
    n.node_position_x,
    n.node_position_y,
    n.word_count,
    n.view_count,
    n.created_at,
    n.updated_at,
    
    -- Tag-Informationen
    GROUP_CONCAT(DISTINCT t.name ORDER BY t.name) as tags,
    COUNT(DISTINCT nt.tag_id) as tag_count,
    
    -- Link-Informationen
    COUNT(DISTINCT nl_out.id) as outgoing_links,
    COUNT(DISTINCT nl_in.id) as incoming_links,
    
    -- Cluster-Informationen
    GROUP_CONCAT(DISTINCT nc.cluster_name) as clusters
    
FROM notes n
LEFT JOIN note_tags nt ON n.id = nt.note_id
LEFT JOIN tags t ON nt.tag_id = t.id
LEFT JOIN note_links nl_out ON n.id = nl_out.source_note_id
LEFT JOIN note_links nl_in ON n.id = nl_in.target_note_id
LEFT JOIN note_cluster_members ncm ON n.id = ncm.note_id
LEFT JOIN note_clusters nc ON ncm.cluster_id = nc.id
GROUP BY n.id;

-- View: Graph-Daten für Visualisierung
CREATE OR REPLACE VIEW v_graph_data AS
SELECT 
    n.id as node_id,
    n.title as node_label,
    n.color as node_color,
    n.node_position_x,
    n.node_position_y,
    n.is_pinned,
    COUNT(DISTINCT nl_out.id) + COUNT(DISTINCT nl_in.id) as connection_count,
    GROUP_CONCAT(DISTINCT t.name) as node_tags
FROM notes n
LEFT JOIN note_links nl_out ON n.id = nl_out.source_note_id
LEFT JOIN note_links nl_in ON n.id = nl_in.target_note_id
LEFT JOIN note_tags nt ON n.id = nt.note_id
LEFT JOIN tags t ON nt.tag_id = t.id
WHERE n.is_archived = FALSE
GROUP BY n.id;

-- ===============================================
-- INITIAL DATA
-- ===============================================

-- Standard-Tags erstellen
INSERT IGNORE INTO tags (user_id, name, color, description) VALUES 
(1, 'DailyThoughts', '#fbbf24', 'Tägliche Gedanken und Notizen'),
(1, 'Ideen', '#10b981', 'Kreative Ideen und Einfälle'),
(1, 'Dokumentation', '#3b82f6', 'Technische Dokumentation'),
(1, 'ToDo', '#ef4444', 'Aufgaben und To-Do Items'),
(1, 'Inspiration', '#8b5cf6', 'Inspirationen und Motivationen'),
(1, 'Wissen', '#f59e0b', 'Faktenwissen und Lerninhalte');

-- ===============================================
-- PERFORMANCE OPTIMIERUNGEN
-- ===============================================

-- Zusätzliche Indizes für bessere Performance
CREATE INDEX idx_notes_search ON notes(title(50), content(100));
CREATE INDEX idx_links_bidirectional ON note_links(source_note_id, target_note_id);
CREATE INDEX idx_tags_search ON tags(name(20));

-- ===============================================
-- FERTIG!
-- ===============================================
-- Die Datenbankstruktur für dein Second Brain System ist jetzt vollständig!
-- 
-- Hauptfeatures:
-- ✅ Notizen mit Graph-Positionen
-- ✅ Bidirektionale Verlinkung
-- ✅ Tag-System mit Farben
-- ✅ Automatische Versionierung
-- ✅ Erinnerungen & Notifications
-- ✅ Clustering & AI-Features
-- ✅ Such- & Filter-System
-- ✅ Benutzer-Einstellungen
-- ✅ Statistiken & Analytics
-- ✅ Performance-Optimierungen
