<?php
/**
 * Hamakar & Hion Automated License Expiration & Notification Cron Job
 * این اسکریپت به صورت خودکار روزانه اجرا شده و هشدارهای انقضا را پیامک می‌کند.
 */

require_once __DIR__ . '/app/config/sms.php';
require_once __DIR__ . '/app/lib/SmsService.php';

echo "=== Hion/Hamakar License Expiration Cron Started [" . date('Y-m-d H:i:s') . "] ===\n";

try {
    // اتصال به دیتابیس همکار یا های‌آن
    $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
    $dbName = $_ENV['DB_NAME'] ?? 'hamakar_hamdbb';
    $dbUser = $_ENV['DB_USER'] ?? 'hamakar_hamdbb';
    $dbPass = $_ENV['DB_PASS'] ?? 'EMyCP[Pk]JfX@n)4';

    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\Throwable $e) {
    die("Database Connection Error: " . $e->getMessage() . "\n");
}

$sms = new SmsService();

// ۱. بررسی لایسنس‌های منقضی‌شونده ظرف ۷ روز آینده
$stmt7 = $pdo->prepare("
    SELECT * FROM licenses 
    WHERE status = 'ACTIVE' 
      AND expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
      AND id NOT IN (
          SELECT license_id FROM license_notifications 
          WHERE notification_type = 'EXPIRY_7DAYS' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
      )
");
$stmt7->execute();
$expiring7 = $stmt7->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($expiring7) . " licenses expiring in 7 days.\n";
foreach ($expiring7 as $lic) {
    $daysLeft = max(1, round((strtotime($lic['expires_at']) - time()) / 86400));
    $msg = "مدیر گرامی {$lic['store_name']}،\nلایسنس سامانه های‌آن شما تا {$daysLeft} روز دیگر منقضی می‌شود. جهت جلوگیری از توقف صندوق، برای تمدید به hion.ir مراجعه فرمایید.";
    
    $sent = $sms->sendDirectSms($lic['phone'], $msg);
    $notifStatus = $sent ? 'SENT' : 'FAILED';
    
    $ins = $pdo->prepare("INSERT INTO license_notifications (license_id, notification_type, recipient_phone, message_body, status, sent_at, created_at) VALUES (:lid, 'EXPIRY_7DAYS', :phone, :msg, :st, NOW(), NOW())");
    $ins->execute([
        ':lid' => $lic['id'],
        ':phone' => $lic['phone'],
        ':msg' => $msg,
        ':st' => $notifStatus
    ]);
    echo "Sent 7-day reminder to {$lic['phone']} ({$lic['store_name']}): {$notifStatus}\n";
}

// ۲. به‌روزرسانی خودکار وضعیت لایسنس‌های گذشته از موعد به EXPIRED
$upd = $pdo->query("UPDATE licenses SET status = 'EXPIRED' WHERE status = 'ACTIVE' AND expires_at < NOW()");
$affected = $upd->rowCount();
echo "Updated {$affected} licenses to EXPIRED.\n";

echo "=== Cron Job Completed Successfully ===\n";
