<?php
// winbo_api.php - نسخه نهایی و امن (V23.8)
// این فایل واسط امن بین نرم‌افزار دسکتاپ و دیتابیس است
header('Content-Type: application/json; charset=utf-8');

// --- تنظیمات دیتابیس (اطلاعات خود را چک کنید) ---
$db_host = "localhost";
$db_user = "winboo_comdb";
$db_pass = "oV2&7d~O]B7&KaZo";
$db_name = "winboo_comdb";
$api_secret = "MY_SECURE_KEY_700921"; // کلید امنیتی مشترک با نرم‌افزار

// 1. بررسی امنیت: فقط درخواست‌هایی که کلید صحیح دارند پردازش می‌شوند
if (!isset($_POST['secret']) || $_POST['secret'] !== $api_secret) {
    echo json_encode(['success' => false, 'message' => 'خطای امنیتی: دسترسی غیرمجاز است.']);
    exit;
}

// 2. اتصال به دیتابیس با مدیریت خطا
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'خطای اتصال به پایگاه داده سرور']);
    exit;
}
$conn->set_charset("utf8mb4");

$action = $_POST['action'] ?? '';

// 3. مسیریابی درخواست‌ها
switch ($action) {
    case 'check_license':
        check_license($conn);
        break;
    case 'fetch_announcements':
        fetch_announcements($conn);
        break;
    case 'fetch_tickets':
        fetch_tickets($conn);
        break;
    case 'send_ticket':
        send_ticket($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'دستور نامعتبر']);
}

// --- توابع پردازش ---

function check_license($conn) {
    $key = $conn->real_escape_string($_POST['license_key']);
    $hwid = $conn->real_escape_string($_POST['hwid']);
    
    // کوئری با JOIN برای دریافت نام کاربر و اطلاعات پلن (جایگزین کوئری مستقیم پایتون)
    $sql = "SELECT 
                ul.id AS lic_id,
                ul.expires_at, 
                ul.status, 
                ul.hwid,
                p.title AS plan_name, 
                p.description AS plan_desc,
                u.name AS user_name
            FROM user_licenses ul
            JOIN plans p ON ul.plan_id = p.id
            JOIN users u ON ul.user_id = u.id
            WHERE ul.license_key = '$key' 
            LIMIT 1";
            
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 1. بررسی وضعیت فعال بودن
        if ($row['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => "متاسفیم {$row['user_name']}، این لایسنس غیرفعال شده است."]);
            return;
        }

        // 2. بررسی تاریخ انقضا
        if (strtotime($row['expires_at']) < time()) {
            echo json_encode(['success' => false, 'message' => "مهلت لایسنس {$row['user_name']} به پایان رسیده است."]);
            return;
        }

        // 3. بررسی قفل سخت‌افزاری (HWID)
        if (!empty($row['hwid']) && $row['hwid'] !== $hwid) {
            echo json_encode(['success' => false, 'message' => "خطای امنیتی: این لایسنس روی دستگاه دیگری قفل شده است."]);
            return;
        }

        // 4. قفل کردن روی سیستم جدید (اگر اولین بار است)
        if (empty($row['hwid'])) {
            $conn->query("UPDATE user_licenses SET hwid = '$hwid' WHERE id = " . $row['lic_id']);
            $row['hwid'] = $hwid; // آپدیت مقدار برای بازگشت به کلاینت
        }

        // بازگرداندن اطلاعات کامل موفقیت‌آمیز
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => "کد لایسنس نامعتبر است."]);
    }
}

function fetch_announcements($conn) {
    // نام جدول طبق فایل پایتون شما "annuncment" است
    $sql = "SELECT title, content, created_at FROM annuncment ORDER BY created_at DESC LIMIT 5";
    $res = $conn->query($sql);
    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode(['success' => true, 'announcements' => $data]);
}

function fetch_tickets($conn) {
    $key = $conn->real_escape_string($_POST['license_key']);
    // دریافت تیکت‌های کاربر خاص
    $sql = "SELECT subject, message, admin_reply, status, created_at FROM support_tickets WHERE license_key = '$key' ORDER BY created_at DESC";
    $res = $conn->query($sql);
    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode(['success' => true, 'tickets' => $data]);
}

function send_ticket($conn) {
    $key = $conn->real_escape_string($_POST['license_key']);
    $user_name = $conn->real_escape_string($_POST['user_name']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $msg = $conn->real_escape_string($_POST['message']);
    
    // اطمینان از وجود جدول (برای جلوگیری از خطا در اولین اجرا)
    $check_table = "CREATE TABLE IF NOT EXISTS support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        license_key VARCHAR(255),
        user_name VARCHAR(255),
        subject VARCHAR(255),
        message TEXT,
        admin_reply TEXT,
        status VARCHAR(50) DEFAULT 'open',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $conn->query($check_table);

    $sql = "INSERT INTO support_tickets (license_key, user_name, subject, message, status) VALUES ('$key', '$user_name', '$subject', '$msg', 'open')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تیکت با موفقیت ثبت شد.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطا در ثبت تیکت: ' . $conn->error]);
    }
}

$conn->close();
?>