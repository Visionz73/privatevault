-- Enhanced events table structure
ALTER TABLE events
  ADD COLUMN IF NOT EXISTS description TEXT AFTER title,
  ADD COLUMN IF NOT EXISTS location VARCHAR(255) AFTER description,
  ADD COLUMN IF NOT EXISTS start_datetime DATETIME AFTER location,
  ADD COLUMN IF NOT EXISTS end_datetime DATETIME AFTER start_datetime,
  ADD COLUMN IF NOT EXISTS all_day TINYINT(1) DEFAULT 0 AFTER end_datetime,
  ADD COLUMN IF NOT EXISTS recurrence VARCHAR(50) AFTER all_day,
  ADD COLUMN IF NOT EXISTS assigned_group_id INT AFTER assigned_to,
  ADD COLUMN IF NOT EXISTS color VARCHAR(20) DEFAULT '#4A90E2' AFTER assigned_group_id,
  ADD COLUMN IF NOT EXISTS reminder_minutes INT DEFAULT 30 AFTER color;

-- Add foreign key for group assignment if not exists
ALTER TABLE events
  ADD CONSTRAINT IF NOT EXISTS fk_events_group
  FOREIGN KEY (assigned_group_id) REFERENCES user_groups(id) ON DELETE SET NULL;

-- Update existing events to use start_datetime and end_datetime
UPDATE events 
SET start_datetime = CONCAT(event_date, ' 08:00:00'),
    end_datetime = CONCAT(event_date, ' 09:00:00')
WHERE start_datetime IS NULL AND event_date IS NOT NULL;
