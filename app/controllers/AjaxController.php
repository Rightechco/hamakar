<?php
// app/controllers/AjaxController.php - نسخه نهایی با چت خصوصی

// این فایل‌ها باید در index.php اصلی بارگذاری شوند، اما برای اطمینان اینجا هم قرار می‌دهیم
require_once __DIR__ . '/../models/Chat.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../lib/numberToWords.php'; 

class AjaxController {
    private $auth;
    private $chatModel;
    private $userModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->chatModel = new Chat();
        $this->userModel = new User();
    }

    /**
     * دریافت لیست کاربران برای چت
     */
    public function getUsers() {
        if ($this->auth->check()) {
            $currentUserId = $this->auth->user()->id;
            $users = $this->userModel->getAllUsers();
            
            $otherUsers = array_filter($users, function($user) use ($currentUserId) {
                return $user->id != $currentUserId;
            });

            foreach($otherUsers as $user) {
                if (!empty($user->last_activity)) {
                    $last_seen = new DateTime($user->last_activity);
                    $now = new DateTime();
                    $diff_minutes = round(($now->getTimestamp() - $last_seen->getTimestamp()) / 60);
                    
                    if ($diff_minutes < 2) {
                        $user->online_status = 'آنلاین';
                        $user->status_color = 'success';
                    } else {
                        $user->online_status = "آخرین بازدید: {$diff_minutes} دقیقه پیش";
                        $user->status_color = 'muted';
                    }
                } else {
                    $user->online_status = 'آفلاین';
                    $user->status_color = 'muted';
                }
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'users' => array_values($otherUsers)]);
            exit();
        } else {
             header('Content-Type: application/json; charset=utf-8');
             http_response_code(403); // Forbidden
             echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
             exit();
        }
    }
    
    // ارسال پیام جدید به یک کاربر مشخص
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->auth->check()) {
            $data = [
                'sender_user_id' => $this->auth->user()->id,
                'receiver_user_id' => (int)$_POST['receiver_id'],
                'message_text' => sanitize($_POST['message_text'] ?? ''),
                'file_path' => null, 
                'file_name' => null
            ];
            
            if ($this->chatModel->createMessage($data)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['status' => 'success']);
                exit();
            }
        }
    }

    // دریافت پیام‌های یک گفتگوی خاص
    public function fetchMessages() {
        if ($this->auth->check()) {
            $currentUserId = $this->auth->user()->id;
            $partnerId = (int)($_GET['partner_id'] ?? 0);
            $lastId = (int)($_GET['last_id'] ?? 0);
            
            $messages = $this->chatModel->getConversationMessages($currentUserId, $partnerId, $lastId);
            
            foreach($messages as $message) {
                $message->is_sender = ($message->sender_user_id == $currentUserId);
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'messages' => $messages]);
            exit();
        }
    }
     public function numberToWords() {
        header('Content-Type: application/json');

        $number = $_GET['number'] ?? 0;

        $words = numberToWords((int)$number); // ✅ استفاده از تابع

        echo json_encode(['words' => $words . ' ریال']);
    }
// در فایل app/controllers/AjaxController.php

// app/controllers/AjaxController.php

// ۱. تایید لایسنس اپلیکیشن ویندوز
public function verify_scraper_license() {
    $key = $_POST['license_key'] ?? '';
    $hwid = $_POST['hwid'] ?? '';
    
    $db = Database::getInstance();
    $license = $db->query("SELECT * FROM user_subscriptions 
                         WHERE license_key = ? AND status = 'active' 
                         AND expiry_date > NOW()", [$key])->fetch();
                         
    if ($license) {
        // قفل کردن به اولین سیستمی که لایسنس را وارد می‌کند
        if (empty($license['hwid'])) {
            $db->query("UPDATE user_subscriptions SET hwid = ? WHERE id = ?", [$hwid, $license['id']]);
            echo json_encode(['status' => 'success', 'user_id' => $license['user_id']]);
        } elseif ($license['hwid'] === $hwid) {
            echo json_encode(['status' => 'success', 'user_id' => $license['user_id']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'این لایسنس قبلاً روی سیستم دیگری فعال شده است.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'لایسنس نامعتبر یا منقضی شده است.']);
    }
    exit;
}

// ۲. دریافت مستقیم سرنخ از اسکرپر پایتون به بخش بازاریابی سامانه
public function sync_scraper_data() {
    $db = Database::getInstance();
    $sql = "INSERT INTO marketing_leads (user_id, name, phone, category, source, city, description, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $params = [
        $_POST['user_id'] ?? 0,
        $_POST['title'] ?? 'آگهی اسکرپر',
        $_POST['phone'] ?? '',
        $_POST['category'] ?? 'General',
        $_POST['source'] ?? 'WinBo App',
        $_POST['city'] ?? '',
        $_POST['desc'] ?? ''
    ];

    if (!empty($params[2])) {
        $db->query($sql, $params);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failed']);
    }
    exit;
}
} 