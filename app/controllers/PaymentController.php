<?php
// app/controllers/PaymentController.php

class PaymentController {

    private $invoiceModel;
    private $paymentModel;

    public function __construct() {
        // لود کردن مدل‌های مورد نیاز
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
    }

    /**
     * مرحله اول: ایجاد درخواست پرداخت و هدایت کاربر به درگاه
     */
    public function request() {
        $invoiceId = $_GET['invoice_id'] ?? 0;
        $invoice = $this->invoiceModel->findByIdWithClient($invoiceId);

        if (!$invoice) {
            FlashMessage::set('message', 'فاکتور برای پرداخت یافت نشد.', 'error');
            redirect_back();
            return;
        }

        $data = [
            "merchant_id" => ZARINPAL_MERCHANT_ID,
            "amount" => (int)$invoice->total_amount * 10, // مبلغ به ریال
            "callback_url" => ZARINPAL_CALLBACK_URL,
            "description" => "پرداخت فاکتور شماره " . $invoice->invoice_number,
            "metadata" => [
                "email" => $invoice->client_email,
                "mobile" => $invoice->client_phone
            ]
        ];

        $jsonData = json_encode($data);
        $ch = curl_init(ZARINPAL_API_REQUEST);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)]);

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if (empty($result['errors']) && !empty($result['data']['authority'])) {
            // ذخیره اطلاعات تراکنش در سشن برای استفاده در مرحله تایید
            $_SESSION['payment_authority'] = $result['data']['authority'];
            $_SESSION['payment_invoice_id'] = $invoiceId;
            $_SESSION['payment_amount'] = (int)$invoice->total_amount;

            // هدایت کاربر به صفحه پرداخت زرین پال
            header('Location: ' . ZARINPAL_GATEWAY_URL . $result['data']['authority']);
            exit();
        } else {
            $error_message = $result['errors']['message'] ?? 'خطا در اتصال به درگاه پرداخت.';
            FlashMessage::set('message', 'خطا: ' . $error_message, 'error');
            redirect_back();
        }
    }

    /**
     * مرحله دوم: تایید پرداخت پس از بازگشت از درگاه
     */
    public function callback() {
        $authority = $_GET['Authority'] ?? '';
        $status = $_GET['Status'] ?? '';

        // بررسی اینکه آیا اطلاعات تراکنش در سشن وجود دارد
        if (empty($_SESSION['payment_authority']) || $_SESSION['payment_authority'] !== $authority) {
            FlashMessage::set('message', 'اطلاعات تراکنش نامعتبر است.', 'error');
            redirect(APP_URL); // به صفحه اصلی هدایت شود
            return;
        }

        $invoiceId = $_SESSION['payment_invoice_id'];
        $amount = $_SESSION['payment_amount'];

        if ($status == 'OK') {
            // فرآیند تایید نهایی تراکنش
            $data = [
                "merchant_id" => ZARINPAL_MERCHANT_ID,
                "authority" => $authority,
                "amount" => $amount * 10, // مبلغ به ریال
            ];
            $jsonData = json_encode($data);
            $ch = curl_init(ZARINPAL_API_VERIFY);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)]);

            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result, true);

            if (empty($result['errors']) && $result['data']['code'] == 100) {
                // پرداخت موفق
                $refId = $result['data']['ref_id'];
                
                // ثبت پرداخت در دیتابیس
                $paymentData = [
                    'invoice_id' => $invoiceId,
                    'amount' => $amount,
                    'payment_date' => date('Y-m-d'),
                    'method' => 'Zarinpal',
                    'transaction_id' => $refId,
                    'notes' => 'پرداخت موفق آنلاین'
                ];
                $this->paymentModel->create($paymentData);

                // آپدیت وضعیت فاکتور و تمدید قرارداد (اگر لازم بود)
                $this->invoiceModel->updatePaymentStatus($invoiceId, $amount);
                
                FlashMessage::set('message', "پرداخت با موفقیت انجام شد. کد رهگیری: {$refId}");
                
                // پاک کردن اطلاعات از سشن
                unset($_SESSION['payment_authority'], $_SESSION['payment_invoice_id'], $_SESSION['payment_amount']);
                
                // هدایت به صفحه مشاهده همان فاکتور
                redirect(APP_URL . '/index.php?page=client&action=invoice_view&id=' . $invoiceId);

            } else {
                $error_message = $result['errors']['message'] ?? 'تراکنش در مرحله تایید ناموفق بود.';
                FlashMessage::set('message', 'خطا در تایید تراکنش: ' . $error_message, 'error');
                redirect(APP_URL . '/index.php?page=client&action=invoice_view&id=' . $invoiceId);
            }
        } else {
            // پرداخت توسط کاربر لغو شده است
            FlashMessage::set('message', 'پرداخت توسط شما لغو شد.', 'info');
            redirect(APP_URL . '/index.php?page=client&action=invoice_view&id=' . $invoiceId);
        }
    }
}