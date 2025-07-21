-- Create notifications table for storing notification history and pending notifications
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_scheduled (scheduled_for),
    INDEX idx_unread (user_id, read_at)
);

-- Create notification_types table for categorizing notifications
CREATE TABLE IF NOT EXISTS notification_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#3B82F6',
    enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default notification types
INSERT IGNORE INTO notification_types (name, description, icon, color) VALUES
('task_reminder', 'Aufgaben-Erinnerungen', 'fas fa-tasks', '#F59E0B'),
('calendar_event', 'Kalender-Ereignisse', 'fas fa-calendar', '#10B981'),
('note_reminder', 'Notiz-Erinnerungen', 'fas fa-sticky-note', '#8B5CF6'),
('system_alert', 'System-Benachrichtigungen', 'fas fa-exclamation-triangle', '#EF4444'),
('finance_update', 'Finanz-Updates', 'fas fa-euro-sign', '#059669'),
('document_upload', 'Dokument-Uploads', 'fas fa-file-upload', '#3B82F6'),
('security_warning', 'Sicherheitswarnungen', 'fas fa-shield-alt', '#DC2626'),
('user_activity', 'Benutzer-Aktivitäten', 'fas fa-user', '#6B7280');

-- Create user_notification_preferences table for individual preferences
CREATE TABLE IF NOT EXISTS user_notification_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type_id INT NOT NULL,
    email_enabled BOOLEAN DEFAULT TRUE,
    push_enabled BOOLEAN DEFAULT TRUE,
    desktop_enabled BOOLEAN DEFAULT TRUE,
    sound_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (notification_type_id) REFERENCES notification_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_type (user_id, notification_type_id)
);

-- Add notification_settings column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS notification_settings JSON DEFAULT NULL;
