<?php
require_once __DIR__ . '/../../config/database.php';

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            user_id INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            event_date DATE NOT NULL,
            all_day BOOLEAN DEFAULT 1,
            start_time TIME NULL,
            end_time TIME NULL,
            location VARCHAR(255),
            color VARCHAR(30) DEFAULT '#4A90E2',
            assigned_to INTEGER NULL,
            assigned_group_id INTEGER NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (assigned_group_id) REFERENCES groups(id) ON DELETE SET NULL
        )
    ");
    
    echo "Events table created or already exists!\n";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
