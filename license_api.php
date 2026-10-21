<?php
// فایل: public_html/license_api.php

// 1. تنظیمات هدر برای JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");

// 2. غیرفعال کردن نمایش خطاها (تا فرمت JSON خراب نشود)
error_reporting(0);
ini_set('display_errors', 0);

// 3. اتصال مستقیم به دیتابیس
$servername = "localhost";
$username = "winboo_comdb";
$password = "oV2&7d~O]B7&KaZo";
$dbname = "winboo_comdb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'Database Connection Failed']);
    exit;
}

// 4. دریافت ورودی (JSON یا POST)
$json = file_get_contents('php://input');
$input = json_decode($json, true);
if (!$input) {
    $input = $_POST;
}

// 5. تشخیص عملیات (Action)
$action = $_GET['action'] ?? '';

// =======================================================
// عملیات: بررسی لایسنس (check_license)
// =======================================================
if ($action == 'check_license') {
    $license_key = $input['license_key'] ?? '';
    $hwid = $input['hwid'] ?? '';

    if (empty($license_key)) {
        echo json_encode(['status' => false, 'message' => 'No license key provided']);
        exit;
    }

    // کوئری (با توجه به نام جدول شما: user_licenses)
    $stmt = $conn->prepare("SELECT * FROM user_licenses WHERE license_key = :key LIMIT 1");
    $stmt->execute([':key' => $license_key]);
    $license = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$license) {
        echo json_encode(['status' => false, 'message' => 'Invalid License']);
        exit;
    }

    // بررسی وضعیت
    if ($license['status'] !== 'active') {
        echo json_encode(['status' => false, 'message' => 'License is inactive']);
        exit;
    }

    // بررسی انقضا
    if (strtotime($license['expires_at']) < time()) {
        echo json_encode(['status' => false, 'message' => 'License expired']);
        exit;
    }

    // بررسی HWID (قفل سخت‌افزاری)
    if (empty($license['hwid'])) {
        // ثبت اولین قفل
        $upd = $conn->prepare("UPDATE user_licenses SET hwid = :hwid WHERE id = :id");
        $upd->execute([':hwid' => $hwid, ':id' => $license['id']]);
    } elseif ($license['hwid'] !== $hwid) {
        echo json_encode(['status' => false, 'message' => 'License locked to another device']);
        exit;
    }

    // موفقیت
    echo json_encode([
        'status' => true,
        'plan' => $license['plan_type'],
        'max_records' => (int)$license['max_records'],
        'used_records' => (int)$license['used_records'],
        'max_bots' => (int)$license['max_bots'],
        'expires_at' => $license['expires_at']
    ]);
    exit;
}

// =======================================================
// عملیات: آپدیت مصرف (update_usage)
// =======================================================
if ($action == 'update_usage') {
    $license_key = $input['license_key'] ?? '';
    $count = (int)($input['count'] ?? 1);

    if (!empty($license_key)) {
        $stmt = $conn->prepare("UPDATE user_licenses SET used_records = used_records + :count WHERE license_key = :key");
        $stmt->execute([':count' => $count, ':key' => $license_key]);
        echo json_encode(['status' => true]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Missing key']);
    }
    exit;
}

// اگر هیچ اکشنی ست نشده بود
echo json_encode(['status' => false, 'message' => 'Invalid Action']);
?>