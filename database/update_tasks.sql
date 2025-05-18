-- Update tasks table (if not already present)
ALTER TABLE tasks
  ADD COLUMN creator_id INT AFTER title,
  ADD COLUMN assignee_id INT AFTER creator_id;

-- Create sub_tasks table
CREATE TABLE IF NOT EXISTS sub_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  status ENUM('open','closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);
