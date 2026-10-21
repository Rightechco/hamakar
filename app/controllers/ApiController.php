<?php
// app/controllers/ApiController.php - نسخه هماهنگ با دیتابیس شما

class ApiController {

    private $db;

    public function __construct() {
        // جلوگیری از نمایش خطاهای PHP که فرمت جیسون را خراب می‌کنند
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // تمیز کردن بافر خروجی (حذف فاصله‌های احتمالی قبل از اجرا)
        if (ob_get_length()) ob_clean();

        // تنظیم هدرها
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, GET");

        // اتصال به دیتابیس (از کانفیگ‌های موجود استفاده می‌کند یا اتصال مستقیم)
        // اگر کلاس Database دارید:
        if (class_exists('Database')) {
            $this->db = new Database();
        } else {
            // اتصال دستی در صورت عدم وجود کلاس Database
            // اطلاعات اتصال را چک کنید
            try {
                $this->db = new PDO("mysql:host=localhost;dbname=winboo_comdb;charset=utf8mb4", "winboo_comdb", "oV2&7d~O]B7&KaZo");
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo json_encode(['status' => false, 'message' => 'DB Connection Error']);
                exit;
            }
        }
    }

    private function get_input() {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? $_POST;
    }

    // بررسی لایسنس
    public function check_license() {
        $input = $this->get_input();
        $license_key = $input['license_key'] ?? '';
        $hwid = $input['hwid'] ?? '';

        if (empty($license_key)) {
            echo json_encode(['status' => false, 'message' => 'کد لایسنس ارسال نشده است']);
            exit;
        }

        // کوئری بر اساس نام جدول شما: user_licenses
        if (method_exists($this->db, 'query')) {
            // استفاده از کلاس Database شما
            $this->db->query("SELECT * FROM user_licenses WHERE license_key = :key LIMIT 1");
            $this->db->bind(':key', $license_key);
            $license = $this->db->single();
        } else {
            // استفاده از PDO دستی
            $stmt = $this->db->prepare("SELECT * FROM user_licenses WHERE license_key = :key LIMIT 1");
            $stmt->execute([':key' => $license_key]);
            $license = $stmt->fetch(PDO::FETCH_OBJ);
        }

        if (!$license) {
            echo json_encode(['status' => false, 'message' => 'لایسنس یافت نشد']);
            exit;
        }

        // بررسی وضعیت (در دیتابیس شما status است نه is_active)
        if ($license->status !== 'active') {
            echo json_encode(['status' => false, 'message' => 'لایسنس غیرفعال است']);
            exit;
        }

        // بررسی تاریخ انقضا
        if (strtotime($license->expires_at) < time()) {
            echo json_encode(['status' => false, 'message' => 'لایسنس منقضی شده است']);
            exit;
        }

        // بررسی و ثبت HWID (قفل سخت‌افزاری)
        // اگر ستون hwid در دیتابیس شما وجود ندارد، باید اضافه شود.
        // فرض بر این است که اضافه کرده‌اید.
        $current_hwid = $license->hwid ?? null;

        if (empty($current_hwid)) {
            // ثبت قفل جدید
            if (method_exists($this->db, 'query')) {
                $this->db->query("UPDATE user_licenses SET hwid = :hwid WHERE id = :id");
                $this->db->bind(':hwid', $hwid);
                $this->db->bind(':id', $license->id);
                $this->db->execute();
            } else {
                $upd = $this->db->prepare("UPDATE user_licenses SET hwid = :hwid WHERE id = :id");
                $upd->execute([':hwid' => $hwid, ':id' => $license->id]);
            }
        } elseif ($current_hwid !== $hwid) {
            echo json_encode(['status' => false, 'message' => 'لایسنس روی دستگاه دیگری قفل شده است']);
            exit;
        }

        // خروجی موفق
        echo json_encode([
            'status' => true,
            'plan' => $license->plan_type ?? 'standard',
            'max_records' => (int)($license->max_records ?? 100),
            'used_records' => (int)($license->used_records ?? 0),
            'max_bots' => (int)($license->max_bots ?? 1),
            'expires_at' => $license->expires_at
        ]);
        exit;
    }

    // آپدیت مصرف
    public function update_usage() {
        $input = $this->get_input();
        $license_key = $input['license_key'] ?? '';
        $count = (int)($input['count'] ?? 1);

        if ($license_key) {
            if (method_exists($this->db, 'query')) {
                $this->db->query("UPDATE user_licenses SET used_records = used_records + :count WHERE license_key = :key");
                $this->db->bind(':count', $count);
                $this->db->bind(':key', $license_key);
                $this->db->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE user_licenses SET used_records = used_records + :count WHERE license_key = :key");
                $stmt->execute([':count' => $count, ':key' => $license_key]);
            }
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false]);
        }
        exit;
    }
}