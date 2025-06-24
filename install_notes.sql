-- Notes database schema for Private Vault
-- Run this in your MySQL database

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
);

CREATE TABLE IF NOT EXISTS note_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    tag_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_note_tag (note_id, tag_name),
    INDEX idx_tag_name (tag_name)
);

-- ============================================================
-- ÜBERPRÜFUNG DER DATENBANKSTRUKTUR
-- ============================================================

-- 1) Alle Tabellen in der Datenbank anzeigen
SHOW TABLES;

-- 2) Detaillierte Struktur der wichtigsten Tabellen überprüfen
DESCRIBE users;
DESCRIBE notes;
DESCRIBE note_tags;
DESCRIBE tasks;
DESCRIBE documents;
DESCRIBE document_categories;

-- 3) Überprüfung der Foreign Key Constraints
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    REFERENCED_TABLE_SCHEMA = DATABASE()
    AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME;

-- 4) Überprüfung der Indizes
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE
FROM 
    INFORMATION_SCHEMA.STATISTICS 
WHERE 
    TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- 5) Anzahl der Datensätze in jeder Tabelle
SELECT 
    TABLE_NAME,
    TABLE_ROWS
FROM 
    INFORMATION_SCHEMA.TABLES 
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;

-- 6) Überprüfung der Tabellenstatus und Speicher-Engine
SELECT 
    TABLE_NAME,
    ENGINE,
    TABLE_COLLATION,
    CREATE_TIME,
    UPDATE_TIME
FROM 
    INFORMATION_SCHEMA.TABLES 
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;

-- 7) Spezielle Überprüfung für die Notes-Tabelle
SELECT 
    COUNT(*) as total_notes,
    COUNT(CASE WHEN is_pinned = 1 THEN 1 END) as pinned_notes,
    COUNT(CASE WHEN is_archived = 1 THEN 1 END) as archived_notes
FROM notes;

-- 8) Überprüfung der Note-Tags Verknüpfungen
SELECT 
    COUNT(*) as total_tags,
    COUNT(DISTINCT tag_name) as unique_tags,
    COUNT(DISTINCT note_id) as notes_with_tags
FROM note_tags;

-- 9) Test-Abfrage: Notes mit ihren Tags
SELECT 
    n.id,
    n.title,
    n.color,
    n.is_pinned,
    GROUP_CONCAT(nt.tag_name) as tags
FROM notes n
LEFT JOIN note_tags nt ON n.id = nt.note_id
GROUP BY n.id, n.title, n.color, n.is_pinned
LIMIT 10;

-- 10) Überprüfung auf potentielle Probleme
-- Verwaiste Tags (ohne zugehörige Notes)
SELECT COUNT(*) as orphaned_tags
FROM note_tags nt
LEFT JOIN notes n ON nt.note_id = n.id
WHERE n.id IS NULL;

-- Notes ohne gültigen User
SELECT COUNT(*) as notes_without_user
FROM notes n
LEFT JOIN users u ON n.user_id = u.id
WHERE u.id IS NULL;

-- ============================================================
-- SCHNELL-ÜBERPRÜFUNG (Nur diese Zeilen ausführen für schnelle Kontrolle)
-- ============================================================

-- Einfache Tabellen-Übersicht
SELECT 
    TABLE_NAME as 'Tabelle',
    TABLE_ROWS as 'Anzahl Zeilen',
    ENGINE as 'Engine'
FROM 
    INFORMATION_SCHEMA.TABLES 
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;

-- Status der wichtigsten Tabellen
SELECT 'users' as tabelle, COUNT(*) as anzahl FROM users
UNION ALL
SELECT 'notes' as tabelle, COUNT(*) as anzahl FROM notes
UNION ALL
SELECT 'note_tags' as tabelle, COUNT(*) as anzahl FROM note_tags
UNION ALL
SELECT 'tasks' as tabelle, COUNT(*) as anzahl FROM tasks
UNION ALL
SELECT 'documents' as tabelle, COUNT(*) as anzahl FROM documents
UNION ALL
SELECT 'document_categories' as tabelle, COUNT(*) as anzahl FROM document_categories;
