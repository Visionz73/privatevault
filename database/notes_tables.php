<?php
// Create necessary tables for notes/Zettelkasten functionality
require_once __DIR__ . '/../src/lib/db.php';

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Notes table - main content storage
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT,
            type ENUM('note', 'daily', 'knowledge', 'documentation', 'template') DEFAULT 'note',
            category_id INT NULL,
            user_id INT NOT NULL,
            parent_id INT NULL,
            is_favorite TINYINT(1) DEFAULT 0,
            is_archived TINYINT(1) DEFAULT 0,
            is_deleted TINYINT(1) DEFAULT 0,
            tags TEXT NULL,
            metadata JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES notes(id) ON DELETE SET NULL,
            FOREIGN KEY (category_id) REFERENCES note_categories(id) ON DELETE SET NULL,
            INDEX idx_user_type (user_id, type),
            INDEX idx_created_at (created_at),
            INDEX idx_tags (tags(255)),
            FULLTEXT(title, content)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Note categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            color VARCHAR(20) DEFAULT '#4A90E2',
            icon VARCHAR(50) DEFAULT 'fa-folder',
            user_id INT NOT NULL,
            parent_id INT NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES note_categories(id) ON DELETE CASCADE,
            INDEX idx_user_parent (user_id, parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Note links table - for Zettelkasten connections
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source_note_id INT NOT NULL,
            target_note_id INT NOT NULL,
            link_type ENUM('reference', 'backlink', 'mention', 'related') DEFAULT 'reference',
            context TEXT NULL,
            strength TINYINT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (source_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (target_note_id) REFERENCES notes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_link (source_note_id, target_note_id, link_type),
            INDEX idx_source (source_note_id),
            INDEX idx_target (target_note_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Note attachments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_attachments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            filename VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_type VARCHAR(50) NOT NULL,
            file_size INT NOT NULL,
            upload_path VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            INDEX idx_note_id (note_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Note templates table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS note_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            template_content LONGTEXT NOT NULL,
            type ENUM('daily', 'meeting', 'project', 'knowledge', 'documentation', 'custom') DEFAULT 'custom',
            is_system TINYINT(1) DEFAULT 0,
            user_id INT NOT NULL,
            usage_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_type (user_id, type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Daily notes table - special handling for daily notes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS daily_notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            date DATE NOT NULL,
            user_id INT NOT NULL,
            mood ENUM('great', 'good', 'okay', 'bad', 'terrible') NULL,
            weather VARCHAR(50) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_date (user_id, date),
            INDEX idx_date (date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Insert default categories
    $defaultCategories = [
        ['name' => 'Persönliche Notizen', 'color' => '#4A90E2', 'icon' => 'fa-user'],
        ['name' => 'Projekte', 'color' => '#10B981', 'icon' => 'fa-project-diagram'],
        ['name' => 'Wissen', 'color' => '#F59E0B', 'icon' => 'fa-brain'],
        ['name' => 'Dokumentation', 'color' => '#8B5CF6', 'icon' => 'fa-book'],
        ['name' => 'Homelab', 'color' => '#EF4444', 'icon' => 'fa-server'],
        ['name' => 'Netzwerk', 'color' => '#06B6D4', 'icon' => 'fa-network-wired'],
        ['name' => 'Einrichtung', 'color' => '#84CC16', 'icon' => 'fa-cogs']
    ];

    // Get all users to create default categories for each
    $users = $pdo->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($users as $userId) {
        foreach ($defaultCategories as $index => $category) {
            $pdo->prepare("
                INSERT IGNORE INTO note_categories (name, color, icon, user_id, sort_order)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([
                $category['name'],
                $category['color'],
                $category['icon'],
                $userId,
                $index
            ]);
        }
    }

    // Insert default templates
    $defaultTemplates = [
        [
            'name' => 'Daily Note',
            'description' => 'Template für tägliche Notizen',
            'type' => 'daily',
            'content' => "# {{date}}\n\n## 📋 Aufgaben\n- [ ] \n\n## 💭 Gedanken\n\n\n## 📚 Gelerntes\n\n\n## 🔗 Verknüpfungen\n\n\n## 📝 Notizen\n\n"
        ],
        [
            'name' => 'Meeting Notes',
            'description' => 'Template für Meeting-Protokolle',
            'type' => 'meeting',
            'content' => "# Meeting: {{title}}\n\n**Datum:** {{date}}\n**Teilnehmer:** \n**Dauer:** \n\n## 📋 Agenda\n1. \n\n## 📝 Notizen\n\n\n## ✅ Action Items\n- [ ] \n\n## 🔗 Verknüpfungen\n\n"
        ],
        [
            'name' => 'Projekt Dokumentation',
            'description' => 'Template für Projektdokumentation',
            'type' => 'project',
            'content' => "# Projekt: {{title}}\n\n## 🎯 Ziel\n\n\n## 📋 Anforderungen\n- \n\n## 🔧 Technische Details\n\n\n## 📈 Status\n- [ ] Planung\n- [ ] Entwicklung\n- [ ] Testing\n- [ ] Deployment\n\n## 🔗 Verknüpfungen\n\n\n## 📝 Notizen\n\n"
        ],
        [
            'name' => 'Wissensbasis Eintrag',
            'description' => 'Template für Wissensmanagement',
            'type' => 'knowledge',
            'content' => "# {{title}}\n\n## 📚 Zusammenfassung\n\n\n## 🔍 Details\n\n\n## 💡 Key Insights\n- \n\n## 🔗 Verwandte Themen\n\n\n## 📖 Quellen\n- \n\n## 🏷️ Tags\n#\n"
        ]
    ];

    foreach ($users as $userId) {
        foreach ($defaultTemplates as $template) {
            $pdo->prepare("
                INSERT IGNORE INTO note_templates (name, description, type, template_content, is_system, user_id)
                VALUES (?, ?, ?, ?, 1, ?)
            ")->execute([
                $template['name'],
                $template['description'],
                $template['type'],
                $template['content'],
                $userId
            ]);
        }
    }

    $pdo->commit();
    echo "Notes system database tables created successfully!";

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Error creating notes tables: ' . $e->getMessage());
    echo "Error creating notes tables: " . $e->getMessage();
}
?>
