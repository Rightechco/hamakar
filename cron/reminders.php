<?php
// /cron/reminders.php

// بارگذاری فایل‌های اصلی و نیازمندی‌ها
require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../models/Contract.php';
require_once __DIR__ . '/../lib/SmsService.php';

// --- شروع منطق اصلی ---

$contractModel = new Contract();
$smsService = new SmsService();

// ۱. یادآوری ۷ روز مانده
$targetDate7 = date('Y-m-d', strtotime('+7 days'));
$contractsFor7DayReminder = $contractModel->getDueContractsForReminder($targetDate7, '7day');
foreach ($contractsFor7DayReminder as $contract) {
    $message = "یادآوری: قرارداد '{$contract->title}' شما ۷ روز دیگر سررسید می‌شود. لطفاً جهت تمدید اقدام فرمایید.\nرایان تکرو";
    $smsService->sendDirectSms($contract->client_phone, $message);
    // علامت‌گذاری به عنوان ارسال شده برای جلوگیری از ارسال مجدد
    $contractModel->markReminderAsSent($contract->id, '7day');
}

// ۲. یادآوری ۱ روز مانده
$targetDate1 = date('Y-m-d', strtotime('+1 day'));
$contractsFor1DayReminder = $contractModel->getDueContractsForReminder($targetDate1, '1day');
foreach ($contractsFor1DayReminder as $contract) {
    $message = "یادآوری فوری: قرارداد '{$contract->title}' شما فردا سررسید می‌شود.\nرایان تکرو";
    $smsService->sendDirectSms($contract->client_phone, $message);
    $contractModel->markReminderAsSent($contract->id, '1day');
}

echo "Reminder script executed successfully.";