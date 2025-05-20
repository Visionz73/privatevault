-- User Groups Management

-- 1) User Groups Table
CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `created_by` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Group Membership Junction Table
CREATE TABLE IF NOT EXISTS `user_group_members` (
  `group_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `added_by` INT NOT NULL,
  `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`, `user_id`),
  FOREIGN KEY (`group_id`) REFERENCES `user_groups`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`added_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Modify tasks table to allow group assignments
ALTER TABLE `tasks` 
  ADD COLUMN `assigned_group_id` INT NULL AFTER `assigned_to`,
  ADD FOREIGN KEY (`assigned_group_id`) REFERENCES `user_groups`(`id`) ON DELETE SET NULL;

-- 4) Create index for faster lookups of user group membership
CREATE INDEX `idx_user_group_members_user_id` ON `user_group_members` (`user_id`);
```
