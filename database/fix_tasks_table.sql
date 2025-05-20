-- Fix the tasks table structure
-- First find the constraint name
SELECT CONSTRAINT_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'privatevault_db'
  AND TABLE_NAME = 'tasks'
  AND COLUMN_NAME = 'user_id'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Assuming the constraint name is 'fk_tasks_user_id', drop it first
-- Replace 'fk_tasks_user_id' with the actual constraint name from the query above
ALTER TABLE tasks DROP FOREIGN KEY fk_tasks_user_id;

-- Now you can safely modify the table
ALTER TABLE tasks 
  MODIFY COLUMN assigned_to INT NULL;  -- Make assigned_to optional
  
-- Now you can drop the column
ALTER TABLE tasks DROP COLUMN user_id;

-- If you need to restore user_id column properly:
-- ALTER TABLE tasks ADD COLUMN user_id INT NOT NULL DEFAULT 0;
-- UPDATE tasks SET user_id = created_by WHERE user_id = 0;
-- ALTER TABLE tasks MODIFY user_id INT NOT NULL;
