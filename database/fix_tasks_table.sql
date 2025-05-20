-- Fix the tasks table structure
ALTER TABLE tasks 
  MODIFY COLUMN assigned_to INT NULL,  -- Make assigned_to optional
  DROP COLUMN user_id;                 -- Remove redundant user_id column
  
-- If you need to restore user_id column properly:
-- ALTER TABLE tasks ADD COLUMN user_id INT NOT NULL DEFAULT 0;
-- UPDATE tasks SET user_id = created_by WHERE user_id = 0;
-- ALTER TABLE tasks MODIFY user_id INT NOT NULL;
