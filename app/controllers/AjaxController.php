<?php
// app/controllers/AjaxController.php - نسخه نهایی با چت خصوصی و ارسال پیوست
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
             http_response_code(403);
             echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
             exit();
        }
    }
    
    // ارسال پیام جدید به یک کاربر مشخص (با پشتیبانی از فایل یا عکس)
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->auth->check()) {
            $filePath = null;
            $fileName = null;

            if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/chat/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $orig = basename($_FILES['attachment']['name']);
                $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $orig);
                $targetFile = $uploadDir . $safeName;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                    $filePath = 'public/uploads/chat/' . $safeName;
                    $fileName = $orig;
                }
            }

            $receiverId = (int)($_POST['receiver_id'] ?? 0);
            $messageText = sanitize($_POST['message_text'] ?? '');

            if ($receiverId > 0 && (!empty($messageText) || !empty($filePath))) {
                $data = [
                    'sender_user_id' => $this->auth->user()->id,
                    'receiver_user_id' => $receiverId,
                    'message_text' => $messageText,
                    'file_path' => $filePath, 
                    'file_name' => $fileName
                ];
                
                if ($this->chatModel->createMessage($data)) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'status' => 'success',
                        'file_name' => $fileName,
                        'file_path' => $filePath ? (APP_URL . '/' . $filePath) : null
                    ]);
                    exit();
                }
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'پیام یا فایل معتبر ارسال نشده است.']);
            exit();
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
        header('Content-Type: application/json; charset=utf-8');
        $number = $_GET['number'] ?? 0;
        $words = numberToWords((int)$number);
        echo json_encode(['words' => $words . ' ریال']);
        exit();
    }
}
