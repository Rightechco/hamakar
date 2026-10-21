<?php
// callback.php - مدیریت بازگشت از زرین پال
$db_host = "localhost";
$db_user = "winboo_comdb";
$db_pass = "oV2&7d~O]B7&KaZo";
$db_name = "winboo_comdb";
$zarinpal_merchant = "YOUR_ZARINPAL_MERCHANT_ID"; // مرچنت کد خود را وارد کنید

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset("utf8mb4");

$Authority = $_GET['Authority'];
$Status = $_GET['Status'];

if ($Status == 'OK') {
    // 1. یافتن تراکنش
    $sql = "SELECT * FROM transactions WHERE authority = '$Authority' AND status = 'pending'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $amount = $row['amount'];
        $license_key = $row['license_key'];
        
        // 2. تایید نهایی با زرین پال
        $data = ['merchant_id' => $zarinpal_merchant, 'authority' => $Authority, 'amount' => $amount * 10];
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
        
        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            $ref_id = $result['data']['ref_id'];
            
            // 3. آپدیت تراکنش و کیف پول
            $conn->query("UPDATE transactions SET status = 'success', ref_id = '$ref_id' WHERE authority = '$Authority'");
            $conn->query("UPDATE user_licenses SET wallet = wallet + $amount WHERE license_key = '$license_key'");
            
            echo "<div style='text-align:center;font-family:Tahoma;margin-top:50px;color:green;'>";
            echo "<h1>تراکنش موفق!</h1>";
            echo "<p>کیف پول شما مبلغ " . number_format($amount) . " تومان شارژ شد.</p>";
            echo "<p>کد پیگیری: $ref_id</p>";
            echo "<p>می‌توانید به نرم‌افزار بازگردید.</p>";
            echo "</div>";
        } else {
            echo "<h1 style='text-align:center;color:red;'>تراکنش ناموفق (تایید نشد)</h1>";
        }
    } else {
        echo "<h1 style='text-align:center;color:red;'>تراکنش یافت نشد یا قبلاً پردازش شده است.</h1>";
    }
} else {
    echo "<h1 style='text-align:center;color:red;'>تراکنش توسط کاربر لغو شد.</h1>";
}
?>