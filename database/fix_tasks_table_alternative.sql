-- Drop all foreign keys on the tasks table
SET @database = 'privatevault_db';
SET @table = 'tasks';

SET @query = CONCAT("SELECT CONCAT('ALTER TABLE ', table_name, ' DROP FOREIGN KEY ', constraint_name, ';') 
                    FROM information_schema.key_column_usage 
                    WHERE referenced_table_name IS NOT NULL
                    AND table_schema = '", @database, "'
                    AND table_name = '", @table, "'");

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now you can modify the table
ALTER TABLE tasks 
  MODIFY COLUMN assigned_to INT NULL,  -- Make assigned_to optional
  DROP COLUMN user_id;                 -- Remove redundant user_id column
