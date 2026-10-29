<?php
// app/models/BackupManager.php - موتور پشتیبان‌گیری فیزیکی و ابری و بازگردانی پایگاه داده هماکار

class BackupManager {
    private $db;
    private $backupDir;

    public function __construct() {
        $this->db = new Database();
        $this->backupDir = dirname(dirname(__DIR__)) . '/backups';
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `system_backups` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `filename` VARCHAR(255) NOT NULL,
            `filepath` VARCHAR(500) NOT NULL,
            `filesize` BIGINT NOT NULL DEFAULT 0,
            `backup_type` ENUM('database', 'full', 'manual', 'auto') DEFAULT 'database',
            `created_by` VARCHAR(100) DEFAULT 'مدیر سیستم',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    /**
     * تولید اسکریپت SQL کامل از کل پایگاه داده
     */
    public function generateSqlDump() {
        $sql = "-- ========================================================\n";
        $sql .= "-- HAMAKAR ENTERPRISE ERP - FULL DATABASE BACKUP\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Host: " . DB_HOST . " | Database: " . DB_NAME . "\n";
        $sql .= "-- ========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        // دریافت لیست تمام جداول
        $this->db->query("SHOW TABLES");
        $tables = $this->db->fetchAll();

        foreach ($tables as $tObj) {
            $table = array_values((array)$tObj)[0];
            
            // رد کردن جداول موقت در صورت وجود
            if (strpos($table, 'tmp_') === 0) continue;

            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Table structure for table `{$table}`\n";
            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            // ساختار جدول
            $this->db->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $this->db->fetch();
            $createTableSql = array_values((array)$createRow)[1] ?? '';
            $sql .= $createTableSql . ";\n\n";

            // داده‌های جدول
            $this->db->query("SELECT * FROM `{$table}`");
            $rows = $this->db->fetchAll();
            if (!empty($rows)) {
                $sql .= "-- Dumping data for table `{$table}`\n";
                foreach ($rows as $rObj) {
                    $row = (array)$rObj;
                    $cols = array_map(function($c) { return "`" . str_replace("`", "``", $c) . "`"; }, array_keys($row));
                    $vals = array_map(function($v) {
                        if (is_null($v)) return "NULL";
                        return "'" . addslashes($v) . "'";
                    }, array_values($row));

                    $sql .= "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- Backup completed successfully.\n";

        return $sql;
    }

    /**
     * ایجاد بکاپ آنلاین روی هاست
     */
    public function createCloudBackup($type = 'manual', $createdBy = 'مدیر سیستم') {
        $filename = 'Hamakar_Backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupDir . '/' . $filename;

        $dump = $this->generateSqlDump();
        if (file_put_contents($filepath, $dump) !== false) {
            $filesize = filesize($filepath);

            $this->db->query("INSERT INTO system_backups (filename, filepath, filesize, backup_type, created_by) 
                              VALUES (:fn, :fp, :fs, :bt, :cb)");
            $this->db->bind(':fn', $filename);
            $this->db->bind(':fp', $filepath);
            $this->db->bind(':fs', $filesize);
            $this->db->bind(':bt', $type);
            $this->db->bind(':cb', $createdBy);
            $this->db->execute();

            return [
                'success' => true,
                'filename' => $filename,
                'filesize' => $filesize,
                'message' => "بکاپ آنلاین با موفقیت در هاست ذخیره گردید ($filename)."
            ];
        }

        return ['success' => false, 'message' => 'خطا در نوشتن فایل بکاپ روی هاست.'];
    }

    /**
     * دانلود مستقیم فایل بکاپ فیزیکی به کامپیوتر کاربر
     */
    public function downloadPhysicalBackup() {
        $filename = 'Hamakar_Physical_Backup_' . date('Y-m-d_His') . '.sql';
        $dump = $this->generateSqlDump();

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($dump));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $dump;
        exit();
    }

    /**
     * لیست تمام بکاپ‌های موجود روی هاست
     */
    public function getAllBackups() {
        $this->db->query("SELECT * FROM system_backups ORDER BY id DESC");
        $dbBackups = $this->db->fetchAll();

        // همچنین بررسی فایل‌های داخل پوشه backups
        $files = glob($this->backupDir . '/*.sql');
        $existingFiles = [];
        foreach ($dbBackups as $b) {
            $existingFiles[$b->filename] = true;
        }

        // اگر فایلی روی دیسک بود ولی در دیتابیس نبود، اضافه کن
        foreach ($files as $f) {
            $fn = basename($f);
            if (!isset($existingFiles[$fn])) {
                $this->db->query("INSERT INTO system_backups (filename, filepath, filesize, backup_type) 
                                  VALUES (:fn, :fp, :fs, 'manual')");
                $this->db->bind(':fn', $fn);
                $this->db->bind(':fp', $f);
                $this->db->bind(':fs', filesize($f));
                $this->db->execute();
            }
        }

        $this->db->query("SELECT * FROM system_backups ORDER BY id DESC");
        return $this->db->fetchAll();
    }

    /**
     * بازگردانی (Restore) دیتابیس از روی فایل SQL
     */
    public function restoreFromSql($sqlContent) {
        if (empty($sqlContent)) {
            return ['success' => false, 'message' => 'محتوای فایل اسکریپت پشتیبان خالی است.'];
        }

        try {
            // غیرفعال کردن چک کلیدهای خارجی برای ایمنی بازگردانی
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $this->db->execute();

            // اجرای تفکیک‌شده دستورات
            $queries = explode(";\n", $sqlContent);
            foreach ($queries as $q) {
                $q = trim($q);
                if (!empty($q) && strpos($q, '--') !== 0) {
                    try {
                        $this->db->query($q);
                        $this->db->execute();
                    } catch (Exception $e) {
                        // ادامه اجرای سایر دستورات بدون توقف کامل
                    }
                }
            }

            $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
            $this->db->execute();

            return ['success' => true, 'message' => 'پایگاه داده با موفقیت از روی نسخه پشتیبان بازگردانی شد.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'خطا در بازگردانی: ' . $e->getMessage()];
        }
    }

    /**
     * بازگردانی از یک بکاپ آنلاین موجود روی هاست
     */
    public function restoreBackupById($id) {
        $this->db->query("SELECT * FROM system_backups WHERE id = :id");
        $this->db->bind(':id', $id);
        $b = $this->db->fetch();
        if (!$b || !file_exists($b->filepath)) {
            return ['success' => false, 'message' => 'فایل بکاپ مورد نظر یافت نشد.'];
        }

        $content = file_get_contents($b->filepath);
        return $this->restoreFromSql($content);
    }

    /**
     * دانلود یک فایل بکاپ خاص از هاست
     */
    public function downloadBackupById($id) {
        $this->db->query("SELECT * FROM system_backups WHERE id = :id");
        $this->db->bind(':id', $id);
        $b = $this->db->fetch();
        if (!$b || !file_exists($b->filepath)) {
            die('فایل بکاپ یافت نشد.');
        }

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $b->filename . '"');
        header('Content-Length: ' . filesize($b->filepath));
        readfile($b->filepath);
        exit();
    }

    /**
     * حذف یک بکاپ از هاست
     */
    public function deleteBackupById($id) {
        $this->db->query("SELECT * FROM system_backups WHERE id = :id");
        $this->db->bind(':id', $id);
        $b = $this->db->fetch();
        if ($b) {
            if (file_exists($b->filepath)) {
                @unlink($b->filepath);
            }
            $this->db->query("DELETE FROM system_backups WHERE id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
            return true;
        }
        return false;
    }
}
