-- ============================================================================
-- Hamakar ERP Enterprise Modules Schema
-- Includes: Moadian Taxpayer, Sayad Treasury, Manufacturing BOM,
--           Procurement Requisitions, Contract Guarantees, Workflow Engine
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Taxpayer Invoices (سامانه مودیان و کارپوشه مالیاتی)
CREATE TABLE IF NOT EXISTS `taxpayer_invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `tax_id` VARCHAR(22) NOT NULL UNIQUE COMMENT 'Unique 22-char fiscal tax ID with Mod 11 checksum',
    `taxpayer_memory_id` VARCHAR(16) DEFAULT 'A19X0000001',
    `invoice_type` TINYINT DEFAULT 1 COMMENT '1: Electronic Type 1, 2: Type 2',
    `body_packet` LONGTEXT NULL COMMENT 'Encrypted JSON/XML payload sent to Moadian API',
    `status` ENUM('pending', 'sent', 'success', 'rejected', 'canceled') DEFAULT 'pending',
    `reference_id` VARCHAR(64) NULL COMMENT 'Moadian Tracking UID',
    `fiscal_code` VARCHAR(64) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_taxpayer_invoice_id` (`invoice_id`),
    INDEX `idx_taxpayer_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Treasury Cheques (خزانه‌داری و چک‌های صیادی ۱۶ رقمی)
CREATE TABLE IF NOT EXISTS `treasury_cheques` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` ENUM('received', 'paid') NOT NULL DEFAULT 'received',
    `sayad_id` VARCHAR(16) NOT NULL COMMENT '16-digit unique Sayad number',
    `bank_name` VARCHAR(100) NOT NULL,
    `branch_name` VARCHAR(100) NULL,
    `cheque_number` VARCHAR(50) NOT NULL,
    `party_name` VARCHAR(150) NOT NULL,
    `client_id` INT NULL,
    `amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `due_date` DATE NOT NULL,
    `issue_date` DATE NULL,
    `status` ENUM('pending', 'deposited', 'cleared', 'bounced', 'transferred', 'returned') DEFAULT 'pending',
    `drawer_national_id` VARCHAR(20) NULL,
    `description` TEXT NULL,
    `voucher_id` INT NULL COMMENT 'Linked financial voucher',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cheque_sayad` (`sayad_id`),
    INDEX `idx_cheque_due_date` (`due_date`),
    INDEX `idx_cheque_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bill of Materials (BOM) Header (فرمول ساخت)
CREATE TABLE IF NOT EXISTS `boms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL COMMENT 'Finished good item ID',
    `bom_code` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(200) NOT NULL,
    `batch_size` DECIMAL(12, 3) NOT NULL DEFAULT 1.000,
    `labor_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `overhead_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_bom_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BOM Items Detail (اقلام مواد اولیه و نیمه‌ساخته فرمول ساخت)
CREATE TABLE IF NOT EXISTS `bom_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bom_id` INT NOT NULL,
    `item_id` INT NOT NULL COMMENT 'Raw material item ID',
    `quantity` DECIMAL(12, 3) NOT NULL DEFAULT 1.000,
    `unit` VARCHAR(30) DEFAULT 'عدد',
    `scrap_rate` DECIMAL(5, 2) NOT NULL DEFAULT 0.00 COMMENT 'Percentage of scrap tolerance',
    `unit_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_bom_items_bom_id` (`bom_id`),
    INDEX `idx_bom_items_item_id` (`item_id`),
    FOREIGN KEY (`bom_id`) REFERENCES `boms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Production Orders (دستور کار و برنامه‌ریزی تولید)
CREATE TABLE IF NOT EXISTS `production_orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `bom_id` INT NOT NULL,
    `target_product_id` INT NOT NULL,
    `quantity` DECIMAL(12, 3) NOT NULL DEFAULT 1.000,
    `raw_warehouse_id` INT NOT NULL COMMENT 'Source warehouse for raw materials',
    `target_warehouse_id` INT NOT NULL COMMENT 'Destination warehouse for finished goods',
    `status` ENUM('draft', 'in_progress', 'completed', 'canceled') DEFAULT 'draft',
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `total_material_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `total_labor_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `total_overhead_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `unit_cost` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `created_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_prod_bom` (`bom_id`),
    INDEX `idx_prod_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Purchase Requisitions (تدارکات و درخواست خرید کالا)
CREATE TABLE IF NOT EXISTS `purchase_requisitions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `req_number` VARCHAR(50) NOT NULL UNIQUE,
    `department` VARCHAR(100) NOT NULL,
    `requester_id` INT NOT NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `status` ENUM('pending', 'approved', 'rejected', 'ordered', 'received') DEFAULT 'pending',
    `approver_id` INT NULL,
    `approved_at` DATETIME NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_req_status` (`status`),
    INDEX `idx_req_requester` (`requester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Contract Guarantees (تضامین، ضمانت‌نامه‌ها و اسناد تعهدآور)
CREATE TABLE IF NOT EXISTS `contract_guarantees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NULL,
    `party_id` INT NOT NULL COMMENT 'Contractor or client ID',
    `type` ENUM('bank_guarantee', 'promissory_note', 'cheque', 'retention_deposit', 'insurance') NOT NULL,
    `amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `reference_number` VARCHAR(100) NOT NULL,
    `issuer_bank` VARCHAR(100) NULL,
    `issue_date` DATE NOT NULL,
    `expiry_date` DATE NOT NULL,
    `status` ENUM('active', 'released', 'extended', 'seized') DEFAULT 'active',
    `description` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_guarantee_party` (`party_id`),
    INDEX `idx_guarantee_expiry` (`expiry_date`),
    INDEX `idx_guarantee_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Automation & Workflow Rules (موتور قوانین و اتوماسیون فرآیندها)
CREATE TABLE IF NOT EXISTS `workflow_rules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `trigger_event` VARCHAR(100) NOT NULL COMMENT 'e.g. invoice_overdue, cheque_due_soon, low_stock, new_lead',
    `conditions` JSON NULL COMMENT 'Rule conditions in JSON format',
    `action_type` VARCHAR(100) NOT NULL COMMENT 'send_sms, create_task, send_notification, email',
    `action_payload` JSON NULL COMMENT 'Parameters and templates for action',
    `is_active` TINYINT(1) DEFAULT 1,
    `last_triggered_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_wf_trigger` (`trigger_event`),
    INDEX `idx_wf_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
