<?php
// app/controllers/LandingController.php

class LandingController {
    public function index() {
        // فعلاً کاربر را به صفحه لاگین بفرست تا خطا ندهد، بعداً ویو لندینگ را اینجا ست می‌کنیم
        global $auth;
        if ($auth->check()) {
            redirect(APP_URL . '/index.php?page=home');
        } else {
            view('auth/login', ['layout' => 'guest_layout', 'title' => 'ورود به سامانه']);
        }
    }
}