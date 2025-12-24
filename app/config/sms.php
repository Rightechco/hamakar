<?php
// app/config/sms.php - نسخه نهایی و به‌روز شده

// ✅ نام کاربری و رمز عبور وب‌سرویس خود را از پنل پیامکی دریافت و جایگزین کنید
define('SMS_USERNAME', $_ENV['SMS_USERNAME'] ?? '9148874622');
define('SMS_PASSWORD', $_ENV['SMS_PASSWORD'] ?? 'ZEGL7');

// ✅ شماره خطی که با آن پیامک ارسال می‌کنید
define('SMS_SENDER_NUMBER', $_ENV['SMS_SENDER_NUMBER'] ?? '+9850004000001179'); 

// ✅ آدرس پایه جدید API
define('SMS_API_BASE_URL', 'https://api.payamak-panel.com');

// --- شناسه الگوهای پیامکی شما (این بخش بدون تغییر باقی می‌ماند) ---
define('SMS_BODY_ID_1WEEK_REMINDER', 342657); // شناسه الگوی یادآوری قرارداد
define('SMS_BODY_ID_INVOICE_PAYMENT', 342659); // شناسه الگوی یادآوری فاکتور