<?php
// app/config/app.php - نسخه نهایی و بدون تداخل (Anti-Warning)

// 1. تنظیمات مسیرها
// چک می‌کنیم اگر APPROOT قبلاً تعریف نشده بود، آن را تعریف کن
if (!defined('APPROOT')) {
    define('APPROOT', dirname(dirname(__FILE__)));
}

// چک می‌کنیم اگر URLROOT تعریف نشده بود
if (!defined('URLROOT')) {
    define('URLROOT', 'https://client.rightechco.ir');
}

// چک می‌کنیم اگر APP_URL تعریف نشده بود (برای سازگاری)
if (!defined('APP_URL')) {
    define('APP_URL', 'https://client.rightechco.ir');
}

// چک می‌کنیم اگر SITENAME تعریف نشده بود
if (!defined('SITENAME')) {
    define('SITENAME', 'سامانه هماکار');
}

// 2. تنظیمات محیط (Environment)
if (!defined('APP_ENV')) {
    $env = $_ENV['APP_ENV'] ?? 'production'; 
    define('APP_ENV', $env);
}

if (!defined('APP_SECRET')) {
    define('APP_SECRET', $_ENV['APP_SECRET'] ?? 'winbo_secret_key_7823467');
}

// 3. مدیریت نمایش خطاها
// این بخش می‌تواند اجرا شود چون تابع است و define نیست، اما بهتر است شرط بگذاریم
if (defined('APP_ENV')) {
    if (APP_ENV === 'development') {
        // در حالت توسعه: نمایش تمام خطاها
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        // در حالت انتشار (Production): مخفی کردن خطاها
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}