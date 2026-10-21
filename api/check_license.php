<?php
// مسیر فایل: public_html/api/check_license.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// غیرفعال کردن نمایش خطاهای PHP در خروجی (تا فرمت جیسون خراب نشود)
error_reporting(0);
ini_set('display_errors', 0);

// اتصال به دیتابیس
$db = new mysqli("localhost", "winboo_comdb", "oV2&7d~O]B7&KaZo", "winboo_comdb");
$db->set_charset("utf8mb4");

if ($db->connect_error) {
    echo json_encode(['status' => false, 'message' => 'DB Error']);
    exit;
}

// دریافت ورودی JSON
$json = file_get_contents('php://input');
$input = json_decode($json, true);

if (!$input) { $input = $_POST; } // پشتیبانی از فرم دیتا

$license_key = isset($input['license_key']) ? $db->real_escape_string($input['license_key']) : null;
$hwid = isset($input['hwid']) ? $db->real_escape_string($input['hwid']) : null;

if (!$license_key) {
    echo json_encode(['status' => false, 'message' => 'کد لایسنس ارسال نشده است']);
    exit;
}

// بررسی لایسنس
$sql = "SELECT * FROM licenses WHERE license_key = '$license_key' LIMIT 1";
$result = $db->query($sql);

if ($result->num_rows == 0) {
    echo json_encode(['status' => false, 'message' => 'لایسنس نامعتبر است']);
    exit;
}

$license = $result->fetch_assoc();

if ($license['is_active'] == 0) {
    echo json_encode(['status' => false, 'message' => 'لایسنس غیرفعال است']);
    exit;
}

if (strtotime($license['expires_at']) < time()) {
    echo json_encode(['status' => false, 'message' => 'لایسنس منقضی شده است']);
    exit;
}

// قفل سخت‌افزاری
if (empty($license['hwid'])) {
    $db->query("UPDATE licenses SET hwid = '$hwid' WHERE id = " . $license['id']);
} elseif ($license['hwid'] !== $hwid) {
    echo json_encode(['status' => false, 'message' => 'لایسنس روی سیستم دیگری قفل شده است']);
    exit;
}

echo json_encode([
    'status' => true,
    'plan' => $license['plan'],
    'max_records' => (int)$license['max_records'],
    'used_records' => (int)$license['used_records'],
    'max_bots' => (int)$license['max_bots'],
    'expires_at' => $license['expires_at']
]);
?>