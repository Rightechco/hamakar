<?php
// app/lib/FlashMessage.php

class FlashMessage {

    /**
     * تنظیم پیام جدید با تشخیص هوشمند ترتیب ورودی‌ها
     */
    public static function set($arg1, $arg2 = null, $arg3 = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = '';
        $type = 'success';

        // اگر ورودی اول کلمه 'message' یا کلید باشد و ورودی دوم متن واقعی پیام باشد
        if ($arg1 === 'message' && !empty($arg2)) {
            $message = $arg2;
            $type = !empty($arg3) ? $arg3 : 'success';
        } else {
            // حالت استاندارد: ورودی اول متن پیام است
            $message = $arg1;
            $type = !empty($arg2) ? $arg2 : 'success';
        }

        // تبدیل error به danger برای استایل استاندارد Bootstrap 5
        if ($type === 'error') {
            $type = 'danger';
        }

        // اطمینان از معتبر بودن نوع کلاس
        if (!in_array($type, ['success', 'danger', 'warning', 'info'])) {
            $type = 'info';
        }

        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * نمایش پیام به همراه استایل شیک و دکمه بستن استاندارد
     */
    public static function display() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['flash_message']) && !empty($_SESSION['flash_message']['message'])) {
            $msg = $_SESSION['flash_message']['message'];
            $type = $_SESSION['flash_message']['type'] ?? 'success';

            if ($type === 'error') { $type = 'danger'; }
            if (!in_array($type, ['success', 'danger', 'warning', 'info'])) {
                $type = 'info';
            }

            $icon = 'fa-check-circle';
            if ($type === 'danger') { $icon = 'fa-exclamation-circle'; }
            elseif ($type === 'warning') { $icon = 'fa-exclamation-triangle'; }
            elseif ($type === 'info') { $icon = 'fa-info-circle'; }

            echo '
            <div class="alert alert-' . $type . ' alert-dismissible fade show rounded-4 shadow-sm border-0 my-3 d-flex align-items-center justify-content-between px-4 py-3" role="alert" style="direction: rtl;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas ' . $icon . ' fs-5"></i>
                    <span class="fw-bold fs-7">' . htmlspecialchars($msg) . '</span>
                </div>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';

            unset($_SESSION['flash_message']);
        }
    }

    public static function get($key = 'message') {
        self::display();
        return '';
    }
}