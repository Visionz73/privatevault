<?php
require_once __DIR__ . '/../config/database.php';

try {
    // Check if start_time column exists
    $stmt = $db->query("SHOW COLUMNS FROM events LIKE 'start_time'");
    if ($stmt->rowCount() == 0) {
        // Add start_time column if it doesn't exist
        $db->exec("ALTER TABLE events ADD COLUMN start_time TIME NULL AFTER all_day");
        echo "Added start_time column to events table.\n";
    }
    
    // Check if end_time column exists
    $stmt = $db->query("SHOW COLUMNS FROM events LIKE 'end_time'");
    if ($stmt->rowCount() == 0) {
        // Add end_time column if it doesn't exist
        $db->exec("ALTER TABLE events ADD COLUMN end_time TIME NULL AFTER start_time");
        echo "Added end_time column to events table.\n";
    }
    
    // Check if all_day column exists
    $stmt = $db->query("SHOW COLUMNS FROM events LIKE 'all_day'");
    if ($stmt->rowCount() == 0) {
        // Add all_day column if it doesn't exist
        $db->exec("ALTER TABLE events ADD COLUMN all_day BOOLEAN DEFAULT 1 AFTER event_date");
        echo "Added all_day column to events table.\n";
    }
    
    echo "Events table update complete!\n";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
