<?php
// مسیر فایل: public_html/api/update_usage.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

error_reporting(0);
ini_set('display_errors', 0);

$db = new mysqli("localhost", "winboo_comdb", "oV2&7d~O]B7&KaZo", "winboo_comdb");

$json = file_get_contents('php://input');
$input = json_decode($json, true);

$license_key = isset($input['license_key']) ? $db->real_escape_string($input['license_key']) : null;
$count = isset($input['count']) ? (int)$input['count'] : 1;

if ($license_key) {
    $db->query("UPDATE licenses SET used_records = used_records + $count WHERE license_key = '$license_key'");
    echo json_encode(['status' => true]);
} else {
    echo json_encode(['status' => false]);
}
?>