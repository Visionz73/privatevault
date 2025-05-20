<?php
require_once __DIR__ . '/../config/database.php';

try {
    echo "Starting database update...\n";
    
    // Check if start_time column exists
    $columnCheckStmt = $db->query("SHOW COLUMNS FROM events LIKE 'start_time'");
    if ($columnCheckStmt->rowCount() == 0) {
        // Add the missing start_time column
        $db->exec("ALTER TABLE events ADD COLUMN start_time TIME NULL AFTER event_date");
        echo "Added start_time column to events table.\n";
    } else {
        echo "start_time column already exists.\n";
    }
    
    // Check if end_time column exists
    $columnCheckStmt = $db->query("SHOW COLUMNS FROM events LIKE 'end_time'");
    if ($columnCheckStmt->rowCount() == 0) {
        // Add the missing end_time column
        $db->exec("ALTER TABLE events ADD COLUMN end_time TIME NULL AFTER start_time");
        echo "Added end_time column to events table.\n";
    } else {
        echo "end_time column already exists.\n";
    }
    
    // Check if all_day column exists
    $columnCheckStmt = $db->query("SHOW COLUMNS FROM events LIKE 'all_day'");
    if ($columnCheckStmt->rowCount() == 0) {
        // Add the all_day column
        $db->exec("ALTER TABLE events ADD COLUMN all_day BOOLEAN DEFAULT 1 AFTER event_date");
        echo "Added all_day column to events table.\n";
    } else {
        echo "all_day column already exists.\n";
    }
    
    echo "Database update completed successfully!\n";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
?>
