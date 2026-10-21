<?php
// app/core/FlashMessage.php

class FlashMessage {
    
    /**
     * تنظیم پیام فلش
     * اصلاح شده: ورودی $name حذف شد تا با فراخوانی‌های کنترلر هماهنگ شود.
     * * @param string $message متن پیام
     * @param string $type نوع پیام (success, danger, warning, info)
     */
    public static function set($message, $type = 'success') {
        // همیشه با یک نام ثابت ذخیره می‌کنیم تا خواندن آن در display ساده باشد
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * دریافت پیام (داخلی)
     */
    public static function get($name) {
        if (isset($_SESSION['flash_' . $name])) {
            $flash = $_SESSION['flash_' . $name];
            unset($_SESSION['flash_' . $name]); // پاک کردن پیام بعد از خواندن
            return $flash;
        }
        return null;
    }

    /**
     * نمایش پیام در قالب HTML
     */
    public static function display() {
        // تلاش برای دریافت پیامی که با نام 'message' ذخیره شده است
        $flash = self::get('message'); 
        
        if ($flash) {
            // مدیریت نوع رنگ برای بوت‌استرپ
            $type = $flash['type'];
            // اگر نوع error فرستاده شد، آن را به danger تبدیل کن تا قرمز شود
            if ($type === 'error') {
                $type = 'danger';
            }
            
            $alertClass = 'alert-' . $type;

            echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show m-3" role="alert">';
            
            // اگر تابع sanitize در Helpers وجود داشت از آن استفاده کن، وگرنه htmlspecialchars
            if (function_exists('sanitize')) {
                echo sanitize($flash['message']);
            } else {
                echo htmlspecialchars($flash['message']);
            }
            
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
    }
}