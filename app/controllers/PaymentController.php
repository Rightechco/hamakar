<?php
// app/controllers/PaymentController.php

class PaymentController extends Controller {

    private $db;
    private $invoiceModel;
    private $paymentModel;

    public function __construct() {
        // اتصال به دیتابیس
        $this->db = new Database;
        
        // تلاش برای لود کردن مدل‌های قدیمی (برای سازگاری با سایر بخش‌ها)
        if(file_exists('../app/models/Invoice.php')) {
            require_once '../app/models/Invoice.php';
            $this->invoiceModel = new Invoice();
        }
        if(file_exists('../app/models/Payment.php')) {
            require_once '../app/models/Payment.php';
            $this->paymentModel = new Payment();
        }
    }

    /**
     * متد جدید: پردازش خرید مستقیم از لندینگ (مودال)
     * 1. ثبت نام کاربر
     * 2. ایجاد درخواست پرداخت
     * 3. هدایت به زرین‌پال
     */
    public function direct_purchase() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // --- 1. دریافت و اعتبارسنجی ورودی‌ها ---
            $plan_id = $_POST['plan_id'] ?? 0;
            $name = trim($_POST['name']);
            $mobile = trim($_POST['mobile']); 
            $password = $_POST['password'];

            if (empty($plan_id) || empty($mobile) || empty($password)) {
                FlashMessage::set('لطفاً تمام فیلدها را پر کنید.', 'danger');
                redirect('index.php');
                return;
            }

            // --- 2. مدیریت کاربر (ثبت نام یا لاگین) ---
            $this->db->query("SELECT * FROM users WHERE email = :email"); // فرض: ایمیل همان موبایل است
            $this->db->bind(':email', $mobile);
            $existingUser = $this->db->single();

            $userId = null;

            if ($existingUser) {
                // اگر کاربر وجود دارد، از آیدی او استفاده می‌کنیم
                $userId = $existingUser->id;
            } else {
                // ثبت نام کاربر جدید
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                $this->db->query("INSERT INTO users (name, email, password, role, created_at) VALUES (:n, :e, :p, 'client', NOW())");
                $this->db->bind(':n', $name);
                $this->db->bind(':e', $mobile);
                $this->db->bind(':p', $hashedPass);
                
                if ($this->db->execute()) {
                    $userId = $this->db->lastInsertId();
                } else {
                    FlashMessage::set('خطا در ثبت نام کاربر.', 'danger');
                    redirect('index.php');
                    return;
                }
            }
            
            // لاگین خودکار (تنظیم سشن)
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $mobile;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = 'client';

            // --- 3. دریافت اطلاعات پلن ---
            $this->db->query("SELECT * FROM plans WHERE id = :id");
            $this->db->bind(':id', $plan_id);
            $plan = $this->db->single();

            if (!$plan) {
                FlashMessage::set('پلن انتخابی نامعتبر است.', 'danger');
                redirect('index.php');
                return;
            }

            // --- 4. اتصال به درگاه پرداخت (زرین‌پال) ---
            $amountToman = (int)$plan->price;
            $amountRial = $amountToman * 10;
            $description = "خرید اشتراک " . $plan->title . " - موبایل: " . $mobile;
            
            // خواندن تنظیمات یا استفاده از مقادیر پیش‌فرض
            $merchantID = defined('ZARINPAL_MERCHANT_ID') ? ZARINPAL_MERCHANT_ID : 'YOUR_MERCHANT_ID';
            $callbackURL = APP_URL . '/index.php?page=payment&action=callback';

            $data = [
                "merchant_id" => $merchantID,
                "amount" => $amountRial,
                "callback_url" => $callbackURL,
                "description" => $description,
                "metadata" => ["mobile" => $mobile]
            ];

            $jsonData = json_encode($data);
            $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
            curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)]);

            $result = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            
            $result = json_decode($result, true);

            if ($err) {
                 FlashMessage::set('خطا در اتصال به درگاه: ' . $err, 'danger');
                 redirect('index.php');
            } elseif (empty($result['errors']) && !empty($result['data']['authority'])) {
                // ذخیره اطلاعات در سشن
                $_SESSION['payment_authority'] = $result['data']['authority'];
                $_SESSION['payment_amount'] = $amountToman; // ذخیره به تومان
                
                // **مهم:** نوع پرداخت را مشخص می‌کنیم تا در بازگشت بدانیم باید لایسنس صادر کنیم
                $_SESSION['payment_type'] = 'plan_purchase'; 
                $_SESSION['payment_plan_id'] = $plan_id;
                $_SESSION['payment_user_id'] = $userId;

                // هدایت به درگاه
                header('Location: https://www.zarinpal.com/pg/StartPay/' . $result['data']['authority']);
                exit();
            } else {
                $msg = $result['errors']['message'] ?? 'خطای ناشناخته';
                FlashMessage::set('خطا در ایجاد تراکنش: ' . $msg, 'danger');
                redirect('index.php');
            }
        }
    }

    /**
     * متد قدیمی: پرداخت فاکتور (برای پنل کاربری)
     */
    public function request() {
        $invoiceId = $_GET['invoice_id'] ?? 0;
        
        // استفاده از مدل برای گرفتن فاکتور
        if ($this->invoiceModel) {
            $invoice = $this->invoiceModel->findByIdWithClient($invoiceId);
        } else {
            // فال‌بک دستی اگر مدل نبود
            $this->db->query("SELECT * FROM invoices WHERE id = :id");
            $this->db->bind(':id', $invoiceId);
            $invoice = $this->db->single();
            if($invoice) {
                // پر کردن مقادیر احتمالی خالی برای جلوگیری از خطا
                $invoice->client_email = ''; 
                $invoice->client_phone = '';
                $invoice->invoice_number = $invoice->id;
            }
        }

        if (!$invoice) {
            FlashMessage::set('فاکتور یافت نشد.', 'danger');
            // چون صفحه قبلی مشخص نیست به داشبورد هدایت میکنیم
            redirect('index.php?page=client&action=dashboard');
            return;
        }

        $merchantID = defined('ZARINPAL_MERCHANT_ID') ? ZARINPAL_MERCHANT_ID : 'YOUR_MERCHANT_ID';
        $amountRial = (int)$invoice->total_amount * 10;
        $callbackURL = APP_URL . '/index.php?page=payment&action=callback';

        $data = [
            "merchant_id" => $merchantID,
            "amount" => $amountRial,
            "callback_url" => $callbackURL,
            "description" => "پرداخت فاکتور شماره " . $invoice->invoice_number,
            "metadata" => [
                "email" => $invoice->client_email ?? '',
                "mobile" => $invoice->client_phone ?? ''
            ]
        ];

        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)]);

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if (empty($result['errors']) && !empty($result['data']['authority'])) {
            $_SESSION['payment_authority'] = $result['data']['authority'];
            $_SESSION['payment_amount'] = (int)$invoice->total_amount;
            
            // **مهم:** نوع پرداخت را فاکتور مشخص می‌کنیم
            $_SESSION['payment_type'] = 'invoice_payment';
            $_SESSION['payment_invoice_id'] = $invoiceId;

            header('Location: https://www.zarinpal.com/pg/StartPay/' . $result['data']['authority']);
            exit();
        } else {
            FlashMessage::set('خطا در اتصال به درگاه.', 'danger');
            redirect('index.php?page=client&action=dashboard');
        }
    }

    /**
     * متد بازگشت از درگاه (Callback)
     * مدیریت هر دو حالت: خرید پلن و پرداخت فاکتور
     */
    public function callback() {
        $authority = $_GET['Authority'] ?? '';
        $status = $_GET['Status'] ?? '';

        if (empty($_SESSION['payment_authority']) || $_SESSION['payment_authority'] !== $authority) {
            FlashMessage::set('تراکنش نامعتبر است.', 'danger');
            redirect('index.php');
            return;
        }

        $amountToman = $_SESSION['payment_amount'];
        $paymentType = $_SESSION['payment_type'] ?? 'invoice_payment';

        if ($status == 'OK') {
            $merchantID = defined('ZARINPAL_MERCHANT_ID') ? ZARINPAL_MERCHANT_ID : 'YOUR_MERCHANT_ID';
            
            $data = [
                "merchant_id" => $merchantID,
                "authority" => $authority,
                "amount" => $amountToman * 10, // تبدیل به ریال
            ];
            
            $jsonData = json_encode($data);
            $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
            curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)]);

            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result, true);

            if (empty($result['errors']) && ($result['data']['code'] == 100 || $result['data']['code'] == 101)) {
                $refId = $result['data']['ref_id'];
                
                // === حالت ۱: خرید پلن (از لندینگ) ===
                if ($paymentType === 'plan_purchase') {
                    $planId = $_SESSION['payment_plan_id'];
                    $userId = $_SESSION['payment_user_id'];
                    
                    // صدور لایسنس
                    $this->issueLicense($userId, $planId, $refId, $amountToman);
                    
                    // پاکسازی سشن
                    unset($_SESSION['payment_authority'], $_SESSION['payment_plan_id'], $_SESSION['payment_amount'], $_SESSION['payment_type']);

                    FlashMessage::set("خرید موفق بود. لایسنس شما فعال شد. کد رهگیری: $refId", 'success');
                    redirect('index.php?page=client&action=dashboard');
                } 
                // === حالت ۲: پرداخت فاکتور (از پنل) ===
                else {
                    $invoiceId = $_SESSION['payment_invoice_id'];
                    
                    // ثبت در دیتابیس
                    if ($this->paymentModel) {
                        $this->paymentModel->create([
                            'invoice_id' => $invoiceId,
                            'amount' => $amountToman,
                            'payment_date' => date('Y-m-d'),
                            'method' => 'Zarinpal',
                            'transaction_id' => $refId,
                            'notes' => 'پرداخت آنلاین موفق'
                        ]);
                    }
                    if ($this->invoiceModel) {
                        $this->invoiceModel->updatePaymentStatus($invoiceId, $amountToman);
                    }
                    
                    unset($_SESSION['payment_authority'], $_SESSION['payment_invoice_id'], $_SESSION['payment_amount'], $_SESSION['payment_type']);
                    
                    FlashMessage::set("فاکتور پرداخت شد. کد رهگیری: $refId", 'success');
                    redirect('index.php?page=client&action=dashboard'); // ریدایرکت امن‌تر
                }

            } else {
                $msg = $result['errors']['message'] ?? 'تراکنش تایید نشد.';
                FlashMessage::set('خطا در تایید پرداخت: ' . $msg, 'danger');
                redirect('index.php');
            }
        } else {
            FlashMessage::set('پرداخت توسط کاربر لغو شد.', 'warning');
            redirect('index.php');
        }
    }

    /**
     * صدور لایسنس پس از پرداخت موفق
     */
    private function issueLicense($user_id, $plan_id, $refId, $amount) {
        $this->db->query("SELECT * FROM plans WHERE id = :id");
        $this->db->bind(':id', $plan_id);
        $plan = $this->db->single();

        // تولید لایسنس
        $key = 'WINBO-' . strtoupper(substr(md5(uniqid()), 0, 16));
        $days = isset($plan->days) ? $plan->days : 30;
        $expires = date('Y-m-d H:i:s', strtotime("+$days days"));

        // 1. ثبت در جدول لایسنس‌ها
        $this->db->query("INSERT INTO user_licenses (user_id, plan_id, license_key, expires_at, status, created_at) 
                          VALUES (:u, :p, :k, :e, 'active', NOW())");
        $this->db->bind(':u', $user_id);
        $this->db->bind(':p', $plan_id);
        $this->db->bind(':k', $key);
        $this->db->bind(':e', $expires);
        $this->db->execute();
        
        // 2. تلاش برای ثبت در جدول payments جهت گزارش‌گیری
        try {
            $this->db->query("INSERT INTO payments (user_id, amount, transaction_id, method, status, created_at, notes) 
                              VALUES (:u, :a, :t, 'Zarinpal', 'paid', NOW(), :n)");
            $this->db->bind(':u', $user_id);
            $this->db->bind(':a', $amount);
            $this->db->bind(':t', $refId);
            $this->db->bind(':n', "خرید پلن: " . ($plan->title ?? ''));
            $this->db->execute();
        } catch (Exception $e) { /* اگر جدول متفاوت بود نادیده بگیر */ }
    }
}