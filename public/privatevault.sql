-- Datenbank anlegen und auswählen
CREATE DATABASE IF NOT EXISTS `privatevault_db`
  CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
USE `privatevault_db`;

-- users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `role` ENUM('admin','member','guest') NOT NULL DEFAULT 'guest',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP 
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- tasks (für dein Dashboard)
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `is_done` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP 
    ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `doc_type` VARCHAR(50),
  `start_date` DATE,
  `end_date` DATE,
  `reminder_days_before` SMALLINT NOT NULL DEFAULT 7,
  `note` TEXT,
  `upload_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `iv` VARBINARY(16),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- tags
CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  UNIQUE(`user_id`,`name`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- document_tags
CREATE TABLE IF NOT EXISTS `document_tags` (
  `doc_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY(`doc_id`,`tag_id`),
  FOREIGN KEY (`doc_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  UNIQUE(`user_id`,`name`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- finance_entries
CREATE TABLE IF NOT EXISTS `finance_entries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` ENUM('income','expense') NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` CHAR(3) NOT NULL DEFAULT 'EUR',
  `entry_date` DATE NOT NULL,
  `category_id` INT NOT NULL,
  `note` TEXT,
  `receipt_file` VARCHAR(255),
  `iv` VARBINARY(16),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- reminders_log
CREATE TABLE IF NOT EXISTS `reminders_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `entity_type` ENUM('document','finance') NOT NULL,
  `entity_id` INT NOT NULL,
  `remind_date` DATE NOT NULL,
  `sent_at` DATETIME NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admin-Seed (Passworthash muss gültig sein)
INSERT IGNORE INTO `users` (`username`,`password_hash`,`email`,`role`)
VALUES (
  'admin',
  '$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
  'admin@local',
  'admin'
);

