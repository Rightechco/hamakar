<?php
// app/controllers/HomeController.php - نسخه نهایی و اصلاح شده

class HomeController {
    public function index() {
        global $auth;
        if (!$auth->check()) {
            redirect(APP_URL . '/index.php?page=login&action=show');
            exit();
        }

        // ✅✅✅ بخش اصلاح شده برای شناسایی تمام نقش‌ها ✅✅✅
        if ($auth->hasRole(['admin', 'accountant', 'accountant_viewer'])) {
            // اگر کاربر ادمین، حسابدار یا مشاهده‌گر بود، به داشبورد ادمین هدایت شود
            redirect(APP_URL . '/index.php?page=admin&action=dashboard');
        } elseif ($auth->hasRole(['employee'])) {
            redirect(APP_URL . '/index.php?page=employee&action=dashboard');
        } elseif ($auth->hasRole(['client'])) {
            redirect(APP_URL . '/index.php?page=client&action=dashboard');
        } else {
            // برای هر حالت پیش‌بینی نشده دیگر، کاربر را خارج کن
            redirect(APP_URL . '/index.php?page=logout');
        }
        exit();
    }
}