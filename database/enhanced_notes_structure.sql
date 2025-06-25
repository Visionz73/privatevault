-- Enhanced Notes Database Structure
-- This script creates an improved structure for the notes system

-- Main notes table (enhanced)
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    content TEXT,
    content_type ENUM('text', 'markdown', 'html') DEFAULT 'text',
    color VARCHAR(7) DEFAULT '#fbbf24',
    is_pinned BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    is_favorite BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT FALSE,
    parent_id INT NULL,
    template_id INT NULL,
    category_id INT NULL,
    word_count INT DEFAULT 0,
    reading_time_minutes INT DEFAULT 0,
    position_x FLOAT NULL,
    position_y FLOAT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES notes(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES note_categories(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category_id),
    INDEX idx_archived (is_archived),
    INDEX idx_pinned (is_pinned),
    INDEX idx_favorite (is_favorite),
    INDEX idx_parent (parent_id),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    FULLTEXT KEY ft_title_content (title, content)
);

-- Note categories
CREATE TABLE IF NOT EXISTS note_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#6b7280',
    icon VARCHAR(50) DEFAULT 'folder',
    sort_order INT DEFAULT 0,
    parent_id INT NULL,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES note_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_category (user_id, name),
    INDEX idx_user_sort (user_id, sort_order)
);

-- Note tags (improved)
CREATE TABLE IF NOT EXISTS note_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    tag_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_note_tag (note_id, tag_name),
    INDEX idx_tag_name (tag_name),
    INDEX idx_note_id (note_id)
);

-- Note links/connections
CREATE TABLE IF NOT EXISTS note_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_note_id INT NOT NULL,
    target_note_id INT NOT NULL,
    link_type ENUM('reference', 'backlink', 'bidirectional', 'sequence') DEFAULT 'reference',
    link_text VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (source_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (target_note_id) REFERENCES notes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_link (source_note_id, target_note_id, link_type),
    INDEX idx_source (source_note_id),
    INDEX idx_target (target_note_id),
    INDEX idx_type (link_type)
);

-- Note templates
CREATE TABLE IF NOT EXISTS note_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    template_content TEXT,
    template_type ENUM('note', 'daily', 'meeting', 'project', 'research', 'custom') DEFAULT 'note',
    is_system BOOLEAN DEFAULT FALSE,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_type (user_id, template_type),
    INDEX idx_usage (usage_count DESC)
);

-- Note versions (for version history)
CREATE TABLE IF NOT EXISTS note_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    content TEXT,
    version_number INT NOT NULL,
    change_summary VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    INDEX idx_note_version (note_id, version_number),
    INDEX idx_created (created_at)
);

-- Note attachments
CREATE TABLE IF NOT EXISTS note_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    INDEX idx_note_id (note_id),
    INDEX idx_uploaded (uploaded_at)
);

-- Daily notes (special type)
CREATE TABLE IF NOT EXISTS daily_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    date DATE NOT NULL,
    user_id INT NOT NULL,
    mood ENUM('excellent', 'good', 'neutral', 'bad', 'terrible') NULL,
    weather VARCHAR(50) NULL,
    gratitude_entries TEXT NULL,
    tasks_completed INT DEFAULT 0,
    habits_tracked JSON NULL,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date),
    INDEX idx_user_date (user_id, date)
);

-- Note statistics
CREATE TABLE IF NOT EXISTS note_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_notes INT DEFAULT 0,
    total_words INT DEFAULT 0,
    total_connections INT DEFAULT 0,
    categories_used INT DEFAULT 0,
    most_used_tags JSON NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_stats (user_id)
);

-- Search history
CREATE TABLE IF NOT EXISTS note_search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_query VARCHAR(255) NOT NULL,
    results_count INT DEFAULT 0,
    search_filters JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_search (user_id, created_at),
    INDEX idx_query (search_query)
);

-- Insert default categories
INSERT IGNORE INTO note_categories (user_id, name, description, color, icon, is_system) VALUES
(0, 'Inbox', 'Schnelle Notizen und Ideen', '#6b7280', 'inbox', TRUE),
(0, 'Projekte', 'Projektbezogene Notizen', '#3b82f6', 'folder-open', TRUE),
(0, 'Meeting', 'Besprechungsnotizen', '#10b981', 'users', TRUE),
(0, 'Ideen', 'Kreative Ideen und Einfälle', '#f59e0b', 'lightbulb', TRUE),
(0, 'Wissen', 'Wissensdatenbank', '#8b5cf6', 'book', TRUE),
(0, 'Daily Notes', 'Tägliche Notizen', '#ef4444', 'calendar', TRUE);

-- Insert default templates
INSERT IGNORE INTO note_templates (user_id, name, description, template_content, template_type, is_system) VALUES
(0, 'Daily Note', 'Vorlage für tägliche Reflexion', '# {{date}}\n\n## 🌅 Morgen\n- **Ziel für heute:** \n- **Prioritäten:** \n\n## 📝 Notizen\n\n## 🎯 Aufgaben\n- [ ] \n\n## 💭 Reflexion\n- **Was lief gut:** \n- **Was kann verbessert werden:** \n\n## 🙏 Dankbarkeit\n1. \n2. \n3. ', 'daily', TRUE),
(0, 'Meeting Notes', 'Vorlage für Besprechungsnotizen', '# Meeting: {{title}}\n\n**Datum:** {{date}}\n**Teilnehmer:** \n**Dauer:** \n\n## Agenda\n1. \n\n## Diskussion\n\n## Entscheidungen\n- \n\n## Action Items\n- [ ] \n\n## Nächste Schritte\n', 'meeting', TRUE),
(0, 'Projekt Übersicht', 'Vorlage für Projektnotizen', '# Projekt: {{title}}\n\n## Überblick\n**Status:** \n**Deadline:** \n**Verantwortlich:** \n\n## Ziele\n- \n\n## Aufgaben\n- [ ] \n\n## Ressourcen\n\n## Notizen\n\n## Risiken & Probleme\n', 'project', TRUE),
(0, 'Wissensnotiz', 'Vorlage für Wissenssammlung', '# {{title}}\n\n## Quelle\n- **Autor:** \n- **Datum:** \n- **Link:** \n\n## Zusammenfassung\n\n## Wichtige Punkte\n- \n\n## Meine Gedanken\n\n## Verknüpfungen\n- [[]], 'research', TRUE);
