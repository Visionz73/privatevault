<?php
// Set up necessary tables for calendar functionality
require_once __DIR__ . '/../src/lib/db.php';

try {
    // Enhanced events table with group support
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            location VARCHAR(255),
            event_date DATE NOT NULL,
            start_time TIME,
            end_time TIME,
            all_day TINYINT(1) DEFAULT 1,
            created_by INT NOT NULL,
            assigned_to INT NULL,
            assigned_group_id INT NULL,
            color VARCHAR(20) DEFAULT '#4A90E2',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (assigned_group_id) REFERENCES user_groups(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Event reminders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS event_reminders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            reminder_time DATETIME NOT NULL,
            sent TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Create index for faster lookups
    $result = $pdo->query("
        SELECT COUNT(*) as idx_exists
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
        AND table_name = 'events'
        AND index_name = 'idx_event_dates'
    ");
    
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row['idx_exists'] == 0) {
        $pdo->exec("CREATE INDEX idx_event_dates ON events(event_date)");
    }

} catch (PDOException $e) {
    error_log('Error creating calendar tables: ' . $e->getMessage());
}
?>
