-- ============================================================
-- Schema für „privatevault_db“ (MariaDB-kompatibel)
-- ============================================================

-- 1) Datenbank anlegen
CREATE DATABASE IF NOT EXISTS `privatevault_db`
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
USE `privatevault_db`;

-- ============================================================
-- 2) Tabelle: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT             NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(50)     NOT NULL UNIQUE,
  `email`         VARCHAR(120)    NOT NULL UNIQUE,
  `password`      VARCHAR(255)    NOT NULL,
  `role`          ENUM('admin','member','guest') NOT NULL DEFAULT 'guest',
  `first_name`    VARCHAR(80)     NULL,
  `last_name`     VARCHAR(80)     NULL,
  `dob`           DATE            NULL,
  `nationality`   VARCHAR(80)     NULL,
  `street`        VARCHAR(120)    NULL,
  `zip`           VARCHAR(20)     NULL,
  `city`          VARCHAR(80)     NULL,
  `country`       VARCHAR(80)     NULL,
  `phone`         VARCHAR(40)     NULL,
  `private_email` VARCHAR(120)    NULL,
  `bio`           TEXT            NULL,
  `links`         JSON            NULL,
  `location`      VARCHAR(80)     NULL,
  `job_title`     VARCHAR(100)    NULL,
  `department`    VARCHAR(100)    NULL,
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3) Tabelle: document_categories
-- ============================================================
CREATE TABLE IF NOT EXISTS `document_categories` (
  `id`   INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `document_categories` (`name`) VALUES
  ('Verträge'), ('Rechnungen'), ('Versicherungen'), ('IDs'), ('Sonstige');

-- ============================================================
-- 4) Tabelle: documents
-- ============================================================
CREATE TABLE IF NOT EXISTS `documents` (
  `id`                   INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id`              INT         NOT NULL,
  `title`                VARCHAR(255) NOT NULL,
  `filename`             VARCHAR(255) NOT NULL,
  `original_name`        VARCHAR(255) NOT NULL,
  `start_date`           DATE        NULL,
  `end_date`             DATE        NULL,
  `reminder_days_before` INT         NOT NULL DEFAULT 7,
  `note`                 TEXT        NULL,
  `category_id`          INT         NOT NULL,
  `is_deleted`           TINYINT(1)  NOT NULL DEFAULT 0,
  `upload_date`          DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `document_categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5) Tabelle: tasks
-- ============================================================
CREATE TABLE IF NOT EXISTS `tasks` (
  `id`          INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT        NULL,
  `assigned_to` INT         NOT NULL COMMENT 'User ID des Empfängers',
  `created_by`  INT         NOT NULL COMMENT 'User ID des Erstellers',
  `due_date`    DATE        NULL,
  `status`      ENUM('open','done') NOT NULL DEFAULT 'open',
  `created_at`  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
