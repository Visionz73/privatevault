<?php
// Setup Script für Benachrichtigungssystem
require_once __DIR__ . '/src/lib/db.php';

try {
    echo "Setting up notification system...\n";
    
    // Create notifications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT,
            type ENUM('info', 'success', 'warning', 'error', 'reminder') DEFAULT 'info',
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            scheduled_for TIMESTAMP NULL,
            data JSON NULL,
            INDEX idx_user_created (user_id, created_at),
            INDEX idx_scheduled (scheduled_for),
            INDEX idx_unread (user_id, read_at)
        )
    ");
    echo "✓ notifications table created\n";
    
    // Create notification_types table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            description TEXT,
            icon VARCHAR(50),
            color VARCHAR(7) DEFAULT '#3B82F6',
            enabled BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ notification_types table created\n";
    
    // Insert default notification types
    $pdo->exec("
        INSERT IGNORE INTO notification_types (name, description, icon, color) VALUES
        ('task_reminder', 'Aufgaben-Erinnerungen', 'fas fa-tasks', '#F59E0B'),
        ('calendar_event', 'Kalender-Ereignisse', 'fas fa-calendar', '#10B981'),
        ('note_reminder', 'Notiz-Erinnerungen', 'fas fa-sticky-note', '#8B5CF6'),
        ('system_alert', 'System-Benachrichtigungen', 'fas fa-exclamation-triangle', '#EF4444'),
        ('finance_update', 'Finanz-Updates', 'fas fa-euro-sign', '#059669'),
        ('document_upload', 'Dokument-Uploads', 'fas fa-file-upload', '#3B82F6'),
        ('security_warning', 'Sicherheitswarnungen', 'fas fa-shield-alt', '#DC2626'),
        ('user_activity', 'Benutzer-Aktivitäten', 'fas fa-user', '#6B7280')
    ");
    echo "✓ Default notification types inserted\n";
    
    // Add notification_settings column to users table if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN notification_settings JSON DEFAULT NULL");
        echo "✓ notification_settings column added to users table\n";
    } catch (Exception $e) {
        echo "ℹ notification_settings column already exists\n";
    }
    
    echo "\n🎉 Notification system setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
