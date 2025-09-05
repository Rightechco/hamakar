<?php
// app/models/Attendance.php

class Attendance {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // بررسی اینکه آیا کاربر یک جلسه فعال (ورود زده اما خروج نزده) دارد یا خیر
    public function getOpenSession($userId) {
        $this->db->query("SELECT * FROM attendance_logs WHERE user_id = :user_id AND clock_out IS NULL ORDER BY id DESC LIMIT 1");
        $this->db->bind(':user_id', $userId);
        return $this->db->fetch();
    }

    // ثبت ورود
    public function clockIn($userId, $ipAddress) {
        $this->db->query("INSERT INTO attendance_logs (user_id, clock_in, ip_address_in) VALUES (:user_id, NOW(), :ip_address)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':ip_address', $ipAddress);
        return $this->db->execute();
    }

    // ثبت خروج
    public function clockOut($logId, $ipAddress) {
        // ابتدا رکورد را برای محاسبه زمان دریافت می‌کنیم
        $this->db->query("SELECT clock_in FROM attendance_logs WHERE id = :log_id");
        $this->db->bind(':log_id', $logId);
        $log = $this->db->fetch();
        
        if ($log) {
            $clockInTime = new DateTime($log->clock_in);
            $clockOutTime = new DateTime();
            $duration = $clockOutTime->getTimestamp() - $clockInTime->getTimestamp();
            $durationMinutes = round($duration / 60);

            $this->db->query("UPDATE attendance_logs SET clock_out = NOW(), ip_address_out = :ip_address, total_duration = :duration WHERE id = :log_id");
            $this->db->bind(':log_id', $logId);
            $this->db->bind(':ip_address', $ipAddress);
            $this->db->bind(':duration', $durationMinutes);
            return $this->db->execute();
        }
        return false;
    }

    // دریافت تمام رکوردهای حضور و غیاب برای گزارش ادمین
    public function getAllRecords($filters = []) {
        $sql = "SELECT att.*, u.name as employee_name FROM attendance_logs att JOIN users u ON att.user_id = u.id WHERE 1=1";
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(att.clock_in) >= :start_date";
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(att.clock_in) <= :end_date";
        }
        $sql .= " ORDER BY att.clock_in DESC";

        $this->db->query($sql);

        if (!empty($filters['start_date'])) $this->db->bind(':start_date', $filters['start_date']);
        if (!empty($filters['end_date'])) $this->db->bind(':end_date', $filters['end_date']);

        return $this->db->fetchAll();
    }
}