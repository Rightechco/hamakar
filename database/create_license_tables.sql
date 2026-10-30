-- Database Schema for Hion & Hamakar Modular Software Licensing
CREATE TABLE IF NOT EXISTS `licenses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `license_key` VARCHAR(64) NOT NULL UNIQUE,
  `customer_name` VARCHAR(191) NOT NULL,
  `store_name` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(32) NOT NULL,
  `city` VARCHAR(64) DEFAULT NULL,
  `machine_id` VARCHAR(64) NOT NULL DEFAULT '*',
  `domain` VARCHAR(191) NOT NULL DEFAULT '*',
  `plan_tier` VARCHAR(32) NOT NULL DEFAULT 'custom',
  `modules` TEXT NOT NULL COMMENT 'JSON array of authorized modules',
  `price_paid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `payment_ref` VARCHAR(64) DEFAULT NULL,
  `status` ENUM('ACTIVE', 'EXPIRED', 'SUSPENDED', 'REVOKED') NOT NULL DEFAULT 'ACTIVE',
  `issued_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `license_token` LONGTEXT NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_license_phone` (`phone`),
  INDEX `idx_license_machine` (`machine_id`),
  INDEX `idx_license_status` (`status`),
  INDEX `idx_license_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `license_notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `license_id` INT UNSIGNED NOT NULL,
  `notification_type` VARCHAR(32) NOT NULL,
  `recipient_phone` VARCHAR(32) NOT NULL,
  `message_body` TEXT NOT NULL,
  `status` ENUM('SENT', 'FAILED', 'PENDING') NOT NULL DEFAULT 'PENDING',
  `sent_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_notif_license` (`license_id`),
  INDEX `idx_notif_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
