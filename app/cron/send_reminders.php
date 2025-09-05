<?php
// app/cron/send_reminders.php
// این اسکریپت قرار است به صورت زمان بندی شده (Cron Job) اجرا شود.
// مسئول بررسی سررسیدهای قراردادها و ارسال پیامک های یادآوری/صورتحساب است.

// اطمینان از اجرای اسکریپت فقط از طریق خط فرمان یا cron (نه مرورگر)
if (php_sapi_name() !== 'cli' && !isset($_SERVER['HTTP_USER_AGENT'])) {
    die("Access denied. This script can only be run from the command line or a cron job.");
}

// 1. بارگذاری فایل های ضروری سیستم
// مسیرها باید نسبت به محل این فایل کرون تنظیم شوند (public_html/app/cron/).
// برای رسیدن به public_html باید دو بار ../ بزنیم.
require_once __DIR__ . '/../../app/config/app.php';      // برای APP_URL, APP_ENV
require_once __DIR__ . '/../../app/config/database.php';  // برای تنظیمات دیتابیس
require_once __DIR__ . '/../../app/config/sms.php';       // برای API Key و BODY_ID ها

require_once __DIR__ . '/../../app/core/Database.php';   // کلاس مدیریت دیتابیس
require_once __DIR__ . '/../../app/lib/SmsService.php';  // سرویس ارسال پیامک
require_once __DIR__ . '/../../app/lib/JalaliDate.php';  // *** اضافه شده: تابع تبدیل تاریخ شمسی ***
require_once __DIR__ . '/../../app/models/Contract.php'; // مدل قراردادها
require_once __DIR__ . '/../../app/models/Client.php';   // مدل کارفرما (برای دسترسی به شماره تلفن)


// 2. مقداردهی اولیه سرویس ها و مدل ها
$db = new Database(); 
$smsService = new SmsService(); 
$contractModel = new Contract();
$clientModel = new Client(); // نیاز به ClientModel برای گرفتن اطلاعات تماس کارفرما


// 3. دریافت تاریخ امروز (میلادی)
$today = date('Y-m-d');
error_log("Cron Job: Running reminders for {$today}"); // ثبت شروع عملیات در لاگ سرور


// 4. ارسال یادآوری یک هفته قبل
$contracts1Week = $contractModel->getContractsForRenewalReminder(7);
error_log("Cron Job: Found " . count($contracts1Week) . " contracts for 1-week reminder.");
foreach ($contracts1Week as $contract) {
    // اگر قبلاً در 24 ساعت گذشته این یادآوری ارسال نشده باشد
    if (empty($contract->last_reminder_1week_sent_at) || strtotime($contract->last_reminder_1week_sent_at) < strtotime($today)) {
        
        // تبدیل تاریخ میلادی به شمسی برای آرگومان پیامک
        $shamsi_next_renewal_date = jdate('Y/m/d', strtotime($contract->next_renewal_date));
        
        $args = [
            0 => $contract->title,                              // {0} عنوان قرارداد
            1 => number_format($contract->total_amount),        // {1} مبلغ کل
            2 => $shamsi_next_renewal_date                      // {2} تاریخ سررسید (شمسی)
        ];
        
        $clientPhone = $contract->client_phone; // شماره تلفن کارفرما از جوین در مدل قرارداد
        
        if (!empty($clientPhone) && $smsService->sendSmsByPattern($clientPhone, SMS_BODY_ID_1WEEK_REMINDER, $args)) {
            $contractModel->updateReminderTimestamp($contract->id, 'last_reminder_1week_sent_at');
            error_log("SMS: 1-week reminder sent for Contract ID {$contract->id} to {$clientPhone}.");
        } else {
            error_log("SMS Error: Failed to send 1-week reminder for Contract ID {$contract->id} to {$clientPhone} (Phone empty or SMS failed).");
        }
    }
}

// 5. ارسال یادآوری یک روز قبل
$contracts1Day = $contractModel->getContractsForRenewalReminder(1);
error_log("Cron Job: Found " . count($contracts1Day) . " contracts for 1-day reminder.");
foreach ($contracts1Day as $contract) {
    // اگر قبلاً در 24 ساعت گذشته این یادآوری ارسال نشده باشد
    if (empty($contract->last_reminder_1day_sent_at) || strtotime($contract->last_reminder_1day_sent_at) < strtotime($today)) {
        // تبدیل تاریخ میلادی به شمسی برای آرگومان پیامک
        $shamsi_next_renewal_date = jdate('Y/m/d', strtotime($contract->next_renewal_date));

        $args = [
            0 => $contract->title, 
            1 => number_format($contract->total_amount), 
            2 => $shamsi_next_renewal_date 
        ];
        $clientPhone = $contract->client_phone;
        
        if (!empty($clientPhone) && $smsService->sendSmsByPattern($clientPhone, SMS_BODY_ID_1DAY_REMINDER, $args)) {
            $contractModel->updateReminderTimestamp($contract->id, 'last_reminder_1day_sent_at');
            error_log("SMS: 1-day reminder sent for Contract ID {$contract->id} to {$clientPhone}.");
        } else {
            error_log("SMS Error: Failed to send 1-day reminder for Contract ID {$contract->id} to {$clientPhone} (Phone empty or SMS failed).");
        }
    }
}

// 6. ارسال پیامک صورتحساب (در روز سررسید)
$contractsToday = $contractModel->getContractsForRenewalReminder(0);
error_log("Cron Job: Found " . count($contractsToday) . " contracts for today's invoice.");
foreach ($contractsToday as $contract) {
    // اگر قبلاً در 24 ساعت گذشته این پیامک پرداخت ارسال نشده باشد
    if (empty($contract->last_invoice_sent_at) || strtotime($contract->last_invoice_sent_at) < strtotime($today)) {
        // !!! لینک پرداخت واقعی باید اینجا ساخته شود یا از جایی دریافت شود
        // فرض می کنیم لینک پرداخت برای هر فاکتور/قرارداد ساخته می شود
        $paymentLink = APP_URL . '/client/invoice/' . $contract->id . '/pay'; 

        // تبدیل تاریخ میلادی به شمسی برای آرگومان پیامک
        $shamsi_next_renewal_date = jdate('Y/m/d', strtotime($contract->next_renewal_date));

        $args = [
            0 => $contract->title, 
            1 => number_format($contract->total_amount), 
            2 => $shamsi_next_renewal_date, 
            3 => $paymentLink // {3}
        ];
        $clientPhone = $contract->client_phone;
        
        if (!empty($clientPhone) && $smsService->sendSmsByPattern($clientPhone, SMS_BODY_ID_INVOICE_PAYMENT, $args)) {
            $contractModel->updateReminderTimestamp($contract->id, 'last_invoice_sent_at');
            error_log("SMS: Invoice payment reminder sent for Contract ID {$contract->id} to {$clientPhone}.");
            
            // --- منطق به روزرسانی تاریخ سررسید بعدی ---
            // این بخش باید بعد از موفقیت آمیز بودن تمدید (مثلا دریافت پرداخت) اجرا شود.
            // در حال حاضر، این منطق فقط فرض می کند که پیامک ارسال شده و تاریخ بعدی را محاسبه می کند.
            // ممکن است بخواهید این را به یک تابع جداگانه موکول کنید که پس از تایید پرداخت واقعی اجرا شود.
            $newRenewalDate = null;
            if ($contract->renewal_type == 'monthly') {
                $newRenewalDate = date('Y-m-d', strtotime($contract->next_renewal_date . ' +1 month'));
            } elseif ($contract->renewal_type == 'yearly') {
                $newRenewalDate = date('Y-m-d', strtotime($contract->next_renewal_date . ' +1 year'));
            }
            if ($newRenewalDate) {
                // توجه: متد update در مدل Contract نیاز به آرایه کامل داده ها دارد.
                // بهتر است یک متد جداگانه مثل updateNextRenewalDateOrStatus در مدل Contract ایجاد کنید.
                // برای سادگی فعلاً به این شکل (فقط next_renewal_date را آپدیت می کنیم):
                $db->query("UPDATE contracts SET next_renewal_date = :new_date WHERE id = :id");
                $db->bind(':new_date', $newRenewalDate);
                $db->bind(':id', $contract->id);
                $db->execute();
                
                error_log("Contract ID {$contract->id}: Next renewal date updated to {$newRenewalDate}.");
            }
        } else {
            error_log("SMS Error: Failed to send invoice payment reminder for Contract ID {$contract->id} to {$clientPhone} (Phone empty or SMS failed).");
        }
    }
}

error_log("Cron Job: Reminders script finished.");
?>