<?php
// app/config/payment.php

// مرچنت کد خود را که از زرین پال دریافت کرده‌اید، در اینجا قرار دهید
define('ZARINPAL_MERCHANT_ID', '63c04e41-1e73-4fa7-9f84-5f6d62868a8a');

// آدرس‌های API زرین پال (از مستندات رسمی)
define('ZARINPAL_API_REQUEST', 'https://api.zarinpal.com/pg/v4/payment/request.json');
define('ZARINPAL_API_VERIFY', 'https://api.zarinpal.com/pg/v4/payment/verify.json');
define('ZARINPAL_GATEWAY_URL', 'https://www.zarinpal.com/pg/StartPay/');

// آدرس بازگشت کاربر پس از پرداخت (این آدرس را در تنظیمات درگاه خود در زرین پال نیز وارد کنید)
define('ZARINPAL_CALLBACK_URL', APP_URL . '/index.php?page=payment&action=callback');