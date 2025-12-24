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

    /**
     * دریافت تمام رکوردهای حضور و غیاب با فیلترهای اختیاری.
     * این متد برای گزارش عملکرد و حضور و غیاب استفاده می‌شود.
     * @param array $filters (user_id, start_date, end_date, limit)
     * @return array
     */
    public function getAllRecords($filters = []) {
        $sql = "SELECT att.*, u.name as employee_name FROM attendance_logs att JOIN users u ON att.user_id = u.id WHERE 1=1";
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND att.user_id = :user_id"; // فیلتر بر اساس کارمند
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND att.clock_in >= :start_date"; // فیلتر بر اساس تاریخ شروع
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND att.clock_in <= :end_date_time"; // فیلتر بر اساس تاریخ پایان
        }
        
        $sql .= " ORDER BY att.clock_in DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit";
        }

        $this->db->query($sql);
        
        if (!empty($filters['user_id'])) {
            $this->db->bind(':user_id', $filters['user_id']);
        }
        if (!empty($filters['start_date'])) {
            $this->db->bind(':start_date', $filters['start_date'] . ' 00:00:00'); // تضمین شروع روز
        }
        if (!empty($filters['end_date'])) {
            // برای پوشش دادن رکوردها تا پایان روز
            $this->db->bind(':end_date_time', $filters['end_date'] . ' 23:59:59'); 
        }
        if (!empty($filters['limit'])) {
            // Bind کردن limit به عنوان INTEGER
            $this->db->bind(':limit', (int)$filters['limit']);
        }

        return $this->db->fetchAll();
    }
    
    /**
     * محاسبه خلاصه‌ای از آمار حضور و غیاب (ساعت کارکرد، تعداد غیبت و غیره)
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @return object
     */
    public function getAttendanceSummary($userId, $startDate, $endDate) {
        // محاسبه کل مدت زمان کارکرد (به دقیقه)
        $this->db->query("
            SELECT 
                COALESCE(SUM(total_duration), 0) AS total_minutes
            FROM attendance_logs
            WHERE user_id = :user_id 
              AND clock_in >= :start_date 
              AND clock_in <= :end_date_time
              AND total_duration IS NOT NULL
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':start_date', $startDate . ' 00:00:00');
        $this->db->bind(':end_date_time', $endDate . ' 23:59:59');
        $result = $this->db->fetch();

        // محاسبه تعداد روزهای کاری ثبت شده
        $this->db->query("
            SELECT COUNT(DISTINCT DATE(clock_in)) AS work_days_count
            FROM attendance_logs
            WHERE user_id = :user_id 
              AND clock_in >= :start_date 
              AND clock_in <= :end_date_time
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':start_date', $startDate . ' 00:00:00');
        $this->db->bind(':end_date_time', $endDate . ' 23:59:59');
        $workDays = $this->db->fetch()->work_days_count;

        return (object)[
            'total_minutes' => $result->total_minutes,
            'total_hours' => round($result->total_minutes / 60, 2),
            'work_days_count' => $workDays,
            // (سایر محاسبات مربوط به تأخیر و غیبت در کنترلر انجام شود.)
        ];
    }
}
