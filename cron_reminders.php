<?php
// cron_contracts.php - اسکریپت اجرای خودکار یادآوری قراردادهای ماهانه

// جلوگیری از اجرای مستقیم از طریق مرورگر
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

// بارگذاری فایل‌های اصلی برنامه
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/config/sms.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/Contract.php';
require_once __DIR__ . '/app/models/Client.php';
require_once __DIR__ . '/app/lib/SmsService.php';
require_once __DIR__ . '/app/lib/JalaliDate.php';

echo "Contract Reminder Cron Job Started at: " . date('Y-m-d H:i:s') . "\n";

$contractModel = new Contract();
$clientModel = new Client();
$smsService = new SmsService();
$today = new DateTime();

// ۱. دریافت تمام قراردادهای ماهانه و فعال
$monthlyContracts = $contractModel->getActiveMonthlyContracts();

if (empty($monthlyContracts)) {
    echo "No active monthly contracts found. Exiting.\n";
    exit();
}

foreach ($monthlyContracts as $contract) {
    $renewalDate = new DateTime($contract->next_renewal_date);
    $interval = $today->diff($renewalDate);
    $daysUntilDue = (int)$interval->format('%r%a'); // تعداد روزهای باقی‌مانده (مثبت یا منفی)

    // اگر یادآوری برای این دوره قبلاً ارسال شده، ادامه نده
    if ($contract->last_reminder_sent_for === $contract->next_renewal_date) {
        continue;
    }

    $client = $clientModel->findById($contract->client_id);
    if (!$client || empty($client->phone)) {
        continue; // اگر مشتری یا شماره تلفن وجود ندارد، ادامه نده
    }

    $message = '';
    $shouldUpdateReminderStatus = false;

    // ۲. منطق ارسال پیامک بر اساس زمان باقی‌مانده
    if ($daysUntilDue == 7) {
        // یادآوری یک هفته مانده
        $message = "مشتری گرامی،\nتمدید قرارداد ماهانه «{$contract->title}» شما یک هفته دیگر سررسید می‌شود.\n- رایان تکرو";
    } elseif ($daysUntilDue == 1) {
        // یادآوری یک روز مانده
        $message = "مشتری گرامی،\nقرارداد «{$contract->title}» شما فردا سررسید می‌شود.\nلطفاً جهت تمدید اقدام فرمایید.\n- رایان تکرو";
    } elseif ($daysUntilDue == 0) {
        // یادآوری روز سررسید به همراه لینک پرداخت
        $paymentLink = APP_URL . '/index.php?page=payment&action=request&contract_id=' . $contract->id; // فرض می‌کنیم لینک پرداخت به این شکل است
        $message = "مشتری گرامی،\nامروز سررسید قرارداد «{$contract->title}» شماست.\nبرای پرداخت و تمدید آنلاین از لینک زیر استفاده کنید:\n{$paymentLink}\n- رایان تکرو";
        $shouldUpdateReminderStatus = true; // فقط در روز آخر، وضعیت یادآوری را آپدیت کن
    }

    // ارسال پیامک در صورت وجود متن
    if (!empty($message)) {
        echo "Sending reminder to {$client->phone} for contract #{$contract->id}...\n";
        $smsService->sendDirectSms($client->phone, $message);
    }

    // ۳. به‌روزرسانی وضعیت در دیتابیس
    if ($shouldUpdateReminderStatus) {
        $contractModel->updateLastReminderDate($contract->id, $contract->next_renewal_date);
        echo "Updated last reminder date for contract #{$contract->id}.\n";
    }

    // ۴. تمدید خودکار تاریخ قرارداد برای ماه بعد
    if ($daysUntilDue < 0) { // اگر تاریخ سررسید گذشته باشد
        $newRenewalDate = $renewalDate->modify('+1 month')->format('Y-m-d');
        $contractModel->renewMonthlyContract($contract->id, $newRenewalDate);
        echo "Renewed contract #{$contract->id} to {$newRenewalDate}.\n";
    }
}

echo "Cron Job Finished.\n";