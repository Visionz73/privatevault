-- Database schema updates for enhanced task features

-- Add new columns to tasks table
ALTER TABLE tasks 
ADD COLUMN IF NOT EXISTS estimated_budget DECIMAL(10,2) NULL,
ADD COLUMN IF NOT EXISTS estimated_hours DECIMAL(5,2) NULL,
ADD COLUMN IF NOT EXISTS category VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS tags TEXT NULL;

-- Create subtasks table
CREATE TABLE IF NOT EXISTS task_subtasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);

-- Create task comments table for future use
CREATE TABLE IF NOT EXISTS task_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create task time tracking table
CREATE TABLE IF NOT EXISTS task_time_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    hours_worked DECIMAL(5,2) NOT NULL,
    description TEXT,
    work_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Update priority column to support urgent priority
ALTER TABLE tasks 
MODIFY COLUMN priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium';

-- Add indexes for better performance
ALTER TABLE task_subtasks ADD INDEX idx_task_id (task_id);
ALTER TABLE task_comments ADD INDEX idx_task_id (task_id);
ALTER TABLE task_time_logs ADD INDEX idx_task_id (task_id);
ALTER TABLE tasks ADD INDEX idx_category (category);
ALTER TABLE tasks ADD INDEX idx_priority (priority);
