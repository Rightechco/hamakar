<?php
// app/core/Auth.php

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function attempt($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $user = $this->db->fetch();

        // --- DEBUG BLOCK (برای تشخیص مشکل لاگین) ---
        if (APP_ENV === 'development') { // مطمئن شوید APP_ENV روی development است
            if (!$user) {
                error_log("DEBUG-AUTH: Attempt failed - User not found for email: " . $email);
            } else {
                $passwordMatches = password_verify($password, $user->password);
                error_log("DEBUG-AUTH: Attempt - User found: " . $user->email . ", Password Match: " . ($passwordMatches ? 'Yes' : 'No'));
                error_log("DEBUG-AUTH: Input Password (Plain Text - FOR DEBUG ONLY!): " . $password);
                error_log("DEBUG-AUTH: DB Hashed Password: " . $user->password);
            }
        }
        // --- END DEBUG BLOCK ---

        if ($user && password_verify($password, $user->password)) {
            error_log("DEBUG-AUTH: Password verified successfully for user: " . $user->email);
            $this->createSession($user);
            error_log("DEBUG-AUTH: Session created for user: " . $user->email);
            return true; // لاگین موفق
        }
        error_log("DEBUG-AUTH: Login failed for email: " . $email . " (User not found or password mismatch)");
        return false; // لاگین ناموفق
    }

    public function createSession($user) {
        session_regenerate_id(true); // تولید مجدد Session ID برای امنیت بیشتر
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['logged_in'] = true;
        error_log("DEBUG-AUTH: createSession() executed. SESSION CONTENT (After creation): " . var_export($_SESSION, true));
    }

    public function check() {
        $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        error_log("DEBUG-AUTH: check() called. Is logged in? " . ($isLoggedIn ? 'Yes' : 'No') . ". Current session ID: " . session_id());
        if ($isLoggedIn) {
            error_log("DEBUG-AUTH: User in session: " . ($_SESSION['user_email'] ?? 'N/A') . ", Role: " . ($_SESSION['user_role'] ?? 'N/A'));
        }
        return $isLoggedIn;
    }

    public function user() {
        if ($this->check()) {
            return (object)[
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }

    public function isAdmin() {
        return $this->check() && ($this->user()->role ?? '') === 'admin';
    }

    public function isEmployee() {
        return $this->check() && ($this->user()->role ?? '') === 'employee';
    }

    public function isClient() {
        return $this->check() && ($this->user()->role ?? '') === 'client';
    }

    public function restrict(array $allowedRoles) {
        if (!$this->check()) {
            error_log("DEBUG-AUTH: RESTRICT - Not logged in. Redirecting to login. Current URL: " . (APP_URL . ($_SERVER['REQUEST_URI'] ?? 'N/A')));
            FlashMessage::set('message', 'برای دسترسی به این بخش، ابتدا وارد شوید.', 'error');
            header('Location: ' . APP_URL . '/index.php?page=login&action=show');
            exit();
        }

        $userRole = $this->user()->role;
        if (!in_array($userRole, $allowedRoles)) {
            error_log("DEBUG-AUTH: RESTRICT - Role '{$userRole}' not allowed. Allowed: " . implode(', ', $allowedRoles) . ". Redirecting to login. Current URL: " . (APP_URL . ($_SERVER['REQUEST_URI'] ?? 'N/A')));
            FlashMessage::set('message', 'شما مجوز دسترسی به این بخش را ندارید.', 'error');
            header('Location: ' . APP_URL . '/index.php?page=login&action=show');
            exit();
        }
        error_log("DEBUG-AUTH: RESTRICT - Access GRANTED for role '{$userRole}' to URL: " . (APP_URL . ($_SERVER['REQUEST_URI'] ?? 'N/A')));
    }
    
    public function hasRole(array $roles) {
        if ($this->check()) {
            return in_array($this->user()->role, $roles);
        }
        return false;
    }

}