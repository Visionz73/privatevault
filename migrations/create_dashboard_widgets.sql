CREATE TABLE IF NOT EXISTS user_dashboard_widgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_position INT NOT NULL,
    widget_type VARCHAR(50) NOT NULL,
    widget_config JSON DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_slot (user_id, slot_position)
);

-- Insert default widgets for existing users
INSERT INTO user_dashboard_widgets (user_id, slot_position, widget_type) 
SELECT id, 1, 'inbox' FROM users WHERE NOT EXISTS (
    SELECT 1 FROM user_dashboard_widgets WHERE user_id = users.id
);

INSERT INTO user_dashboard_widgets (user_id, slot_position, widget_type) 
SELECT id, 2, 'documents' FROM users WHERE NOT EXISTS (
    SELECT 1 FROM user_dashboard_widgets WHERE user_id = users.id AND slot_position = 2
);

INSERT INTO user_dashboard_widgets (user_id, slot_position, widget_type) 
SELECT id, 3, 'calendar' FROM users WHERE NOT EXISTS (
    SELECT 1 FROM user_dashboard_widgets WHERE user_id = users.id AND slot_position = 3
);

INSERT INTO user_dashboard_widgets (user_id, slot_position, widget_type) 
SELECT id, 4, 'havetopay' FROM users WHERE NOT EXISTS (
    SELECT 1 FROM user_dashboard_widgets WHERE user_id = users.id AND slot_position = 4
);
