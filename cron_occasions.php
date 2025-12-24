<?php
// cron_occasions.php - اسکریپت اجرای خودکار پیامک‌های مناسبتی

// جلوگیری از اجرای مستقیم از طریق مرورگر
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

// بارگذاری تمام فایل‌های اصلی برنامه
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/config/sms.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/Client.php';
require_once __DIR__ . '/app/models/SmsCampaign.php'; // مدل جدید برای لاگ کمپین‌ها
require_once __DIR__ . '/app/lib/SmsService.php';
require_once __DIR__ . '/app/lib/Occasions.php';
require_once __DIR__ . '/app/lib/JalaliDate.php';

echo "Cron Job Started at: " . date('Y-m-d H:i:s') . "\n";

// ۱. بررسی مناسبت امروز
$occasion = Occasions::getTodaysOccasion();

if (!$occasion) {
    echo "No occasion for today. Exiting.\n";
    exit();
}

$todayKey = jdate('m-d');
$campaignModel = new SmsCampaign();

// ۲. بررسی اینکه آیا پیامک این مناسبت امسال قبلاً ارسال شده یا نه
if ($campaignModel->hasOccasionBeenSentThisYear($todayKey)) {
    echo "Occasion '{$occasion['name']}' for this year has already been sent. Exiting.\n";
    exit();
}

// ۳. دریافت لیست مشتریان
$clientModel = new Client();
$clients = $clientModel->getAllClients();
if (empty($clients)) {
    echo "No clients found. Exiting.\n";
    exit();
}

// ۴. ارسال پیامک
$smsService = new SmsService();
$successCount = 0;
foreach ($clients as $client) {
    if (!empty($client->phone)) {
        $finalMessage = str_replace('{client_name}', $client->name, $occasion['message']);
        if ($smsService->sendDirectSms($client->phone, $finalMessage)) {
            $successCount++;
        }
    }
}

// ۵. ثبت نتیجه در دیتابیس برای جلوگیری از ارسال مجدد
$campaignModel->logCampaign([
    'campaign_type' => 'occasion',
    'occasion_name' => $occasion['name'],
    'occasion_date_key' => $todayKey,
    'message_body' => $occasion['message'],
    'status' => 'sent',
    'recipients_count' => $successCount
]);

echo "Occasional SMS for '{$occasion['name']}' sent to {$successCount} recipients.\n";
echo "Cron Job Finished.\n";