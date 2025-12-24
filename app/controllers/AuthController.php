<?php
// app/controllers/AuthController.php

class AuthController {
    private $auth;
    private $userModel;

    public function __construct() {
        $this->auth = new Auth();
        // بارگذاری مدل User در اینجا، چون AuthController از آن استفاده می کند
        // در این نسخه index.php، User.php در index.php بارگذاری می شود.
        $this->userModel = new User(); 
    }

    public function showLogin() {
        error_log("DEBUG-AUTH-CTL: showLogin() called. Is logged in? " . ($this->auth->check() ? 'Yes' : 'No'));
        if ($this->auth->check()) {
            error_log("DEBUG-AUTH-CTL: Already logged in, redirecting from showLogin.");
            if ($this->auth->isAdmin()) {
                redirect(APP_URL . '/index.php?page=admin&action=dashboard');
            } elseif ($this->auth->isEmployee()) {
                redirect(APP_URL . '/index.php?page=employee&action=dashboard');
            } elseif ($this->auth->isClient()) {
                redirect(APP_URL . '/index.php?page=client&action=dashboard');
            }
        }
        view('auth/login', ['layout' => 'guest_layout', 'title' => 'Login']);
    }

    public function login() {
        error_log("DEBUG-AUTH-CTL: login() called (POST or GET).");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');
            $password = sanitize($_POST['password'] ?? '');

            $validator = Validator::make($_POST);
            $isValid = $validator->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($isValid) {
                error_log("DEBUG-AUTH-CTL: Validation passed for email: " . $email);
                if ($this->auth->attempt($email, $password)) {
                    error_log("DEBUG-AUTH-CTL: Auth attempt was SUCCESSFUL. Redirecting to dashboard.");
                    FlashMessage::set('message', 'خوش آمدید!');
                    // Redirection after successful login
                    if ($this->auth->isAdmin()) {
                        header('Location: ' . APP_URL . '/index.php?page=admin&action=dashboard');
                    } elseif ($this->auth->isEmployee()) {
                        header('Location: ' . APP_URL . '/index.php?page=employee&action=dashboard');
                    } elseif ($this->auth->isClient()) {
                        header('Location: ' . APP_URL . '/index.php?page=client&action=dashboard');
                    }
                    exit(); // Exit after header redirect
                } else {
                    error_log("DEBUG-AUTH-CTL: Auth attempt FAILED. Redirecting back to login.");
                    FlashMessage::set('message', 'ایمیل یا رمز عبور اشتباه است.', 'error');
                    redirect(APP_URL . '/index.php?page=login&action=show');
                }
            } else {
                error_log("DEBUG-AUTH-CTL: Validation FAILED. Redirecting back to login. Errors: " . var_export($validator->errors(), true));
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=login&action=show');
            }
        } else {
            error_log("DEBUG-AUTH-CTL: Login method called with GET. Redirecting to show login form.");
            redirect(APP_URL . '/index.php?page=login&action=show');
        }
    }


public function logout() {
    // 1. پاک کردن تمام متغیرهای سشن
    $_SESSION = array();

    // 2. اگر از کوکی برای مدیریت سشن استفاده می‌شود، آن را منقضی کن
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
 redirect(APP_URL . '/index.php?page=login&action=show');
    exit(); // اطمینان از توقف اجرای اسکریپت

}

}