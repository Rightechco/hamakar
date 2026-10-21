<?php
// cron_reminder.php - اسکریپت ارسال پیامک یادآوری تمدید

// ---------------------------------------------------------
// 1. بارگذاری تنظیمات و فایل‌های هسته
// ---------------------------------------------------------
// استفاده از __DIR__ برای اطمینان از مسیردهی صحیح
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';

// لود کردن فایل کانفیگ پیامک (اگر دارید)
if (file_exists(__DIR__ . '/app/config/sms.php')) {
    require_once __DIR__ . '/app/config/sms.php';
}

// لود کردن کلاس دیتابیس
require_once __DIR__ . '/app/core/Database.php';

// ✅ لود کردن سرویس پیامک از مسیر صحیح (app/lib)
if (file_exists(__DIR__ . '/app/lib/SmsService.php')) {
    require_once __DIR__ . '/app/lib/SmsService.php';
} else {
    die("Error: SmsService.php not found in app/lib/");
}

// ---------------------------------------------------------
// 2. شروع عملیات
// ---------------------------------------------------------
$db = new Database;
$sms = new SmsService();

echo "Checking for expiring licenses...\n";

// محاسبه تاریخ شروع و پایان برای 7 روز آینده
$targetDateStart = date('Y-m-d 00:00:00', strtotime('+7 days'));
$targetDateEnd = date('Y-m-d 23:59:59', strtotime('+7 days'));

// کوئری برای پیدا کردن لایسنس‌هایی که دقیقاً 7 روز دیگر منقضی می‌شوند
// و هنوز فعال هستند
$sql = "SELECT L.*, U.name, U.email as mobile, P.title as plan_title
        FROM user_licenses L 
        JOIN users U ON L.user_id = U.id 
        JOIN plans P ON L.plan_id = P.id
        WHERE L.status = 'active' 
        AND L.expires_at BETWEEN :start AND :end";

$db->query($sql);
$db->bind(':start', $targetDateStart);
$db->bind(':end', $targetDateEnd);
$expiringLicenses = $db->fetchAll();

if ($expiringLicenses) {
    $count = 0;
    foreach ($expiringLicenses as $item) {
        $mobile = $item->mobile; // فرض بر اینکه ایمیل همان موبایل است یا ستون mobile دارید
        $name = $item->name;
        $plan = $item->plan_title;
        
        // متن پیامک
        $message = "کاربر گرامی $name\nاشتراک «$plan» شما ۷ روز دیگر به پایان می‌رسد.\nلطفا جهت تمدید و جلوگیری از قطع دسترسی اقدام کنید.\n" . SITENAME;
        
        // ✅ ارسال پیامک با متد sendDirectSms از فایل SmsService
        // این متد در فایل شما وجود دارد و برای ارسال متنی مناسب است
        $result = $sms->sendDirectSms($mobile, $message);
        
        if ($result) {
            echo "[OK] SMS sent to $mobile\n";
            $count++;
        } else {
            echo "[ERR] Failed to send SMS to $mobile\n";
        }
    }
    echo "--------------------------------------------------\n";
    echo "Process completed. Total reminders sent: $count\n";
} else {
    echo "No licenses found expiring on " . date('Y-m-d', strtotime('+7 days')) . ".\n";
}