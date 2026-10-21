<?php
class HomeController extends Controller {
    
    private $db;

    public function __construct() {
        // اتصال به دیتابیس
        $this->db = new Database;
    }

    public function index() {
        global $auth;

        // 1. اگر کاربر لاگین است، به داشبورد هدایت شود
        if (isset($auth) && $auth->check()) {
            // دریافت URL پایه (اگر تعریف شده باشد)
            $base = defined('APP_URL') ? APP_URL : 'index.php';
            
            if ($auth->isAdmin()) {
                redirect($base . '?page=admin&action=dashboard');
            } elseif ($auth->isEmployee()) {
                redirect($base . '?page=employee&action=dashboard');
            } else {
                redirect($base . '?page=client&action=dashboard');
            }
            exit;
        }

        // 2. اگر کاربر مهمان است، پلن‌ها را دریافت کن و لندینگ را نشان بده
        $plans = [];
        try {
            // کوئری برای دریافت پلن‌های فعال
            $this->db->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY is_featured DESC, price ASC");
            $plans = $this->db->fetchAll();
        } catch (Exception $e) {
            // اگر جدول وجود نداشت، لیست خالی برگردان
            $plans = []; 
        }

        $data = [
            'title' => 'Winbo | دستیار هوشمند کسب‌وکار',
            'plans' => $plans
        ];

        // 3. نمایش ویو
        // اولویت با ویوی لندینگ است
        if (defined('APPROOT') && file_exists(APPROOT . '/views/landing/index.php')) {
            $this->view('landing/index', $data);
        } elseif (defined('APPROOT') && file_exists(APPROOT . '/views/home/index.php')) {
            $this->view('home/index', $data);
        } else {
            // اگر هیچ ویویی نبود، به صفحه ورود بفرست
            redirect('index.php?page=login&action=show');
        }
    }
}