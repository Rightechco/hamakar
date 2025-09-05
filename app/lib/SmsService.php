<?php
// app/lib/SmsService.php - نسخه نهایی با تفکیک کامل ارسال تکی و گروهی

class SmsService {

    /**
     * ارسال پیامک با استفاده از الگو (برای یادآوری‌ها)
     */
    public function sendSmsByPattern($recipientNumber, $patternCode, $args = []) {
        // این متد به درستی کار می‌کند و نیازی به تغییر ندارد
        try {
            ini_set("soap.wsdl_cache_enabled", "0");
            $client = new SoapClient("http://api.payamak-panel.com/post/Send.asmx?wsdl", ["encoding" => "UTF-8"]);
            $data = [
                "username" => SMS_USERNAME,
                "password" => SMS_PASSWORD,
                "text" => $args,
                "to" => $recipientNumber,
                "bodyId" => $patternCode
            ];
            $result = $client->SendByBaseNumber($data);
            $responseValue = $result->SendByBaseNumberResult;
            if (is_string($responseValue) && strlen($responseValue) > 10 && is_numeric($responseValue)) {
                return true;
            }
        } catch (SoapFault $e) {
            error_log("SMS SoapFault Error (Pattern Send): " . $e->getMessage());
        }
        return false;
    }

    /**
     * ✅ ارسال پیامک مستقیم (بدون الگو) با منطق تفکیک شده
     */
    public function sendDirectSms($recipientNumbers, $messageBody) {
        if (empty(SMS_USERNAME) || empty(SMS_PASSWORD) || empty(SMS_SENDER_NUMBER)) {
            error_log("SMS Service Error: Credentials or Sender Number are not configured.");
            return false;
        }

        try {
            ini_set("soap.wsdl_cache_enabled", "0");
            $client = new SoapClient("http://api.payamak-panel.com/post/send.asmx?wsdl", ["encoding" => "UTF-8"]);

            $data = [
                'username' => SMS_USERNAME,
                'password' => SMS_PASSWORD,
                'from' => SMS_SENDER_NUMBER,
                'text' => $messageBody,
                'isflash' => false
            ];

            // --- تفکیک منطق بر اساس نوع ورودی ---
            if (is_array($recipientNumbers)) {
                // --- حالت ارسال گروهی ---
                $data['to'] = $recipientNumbers;
                $result = $client->SendSimpleSMS($data);
                $response = $result->SendSimpleSMSResult;
                
                // بررسی پاسخ موفق برای ارسال گروهی
                if (isset($response->long) && is_array($response->long) && $response->long[0] > 0) {
                    error_log("Direct Bulk SMS queued successfully. First Batch ID: " . $response->long[0]);
                    return true;
                } else {
                    error_log("Direct Bulk SMS API Error. Response: " . print_r($response, true));
                    return false;
                }

            } else {
                // --- حالت ارسال تکی (برای پیامک مناسبتی) ---
                $data['to'] = $recipientNumbers;
                $result = $client->SendSimpleSMS2($data);
                $response = $result->SendSimpleSMS2Result;

                // بررسی پاسخ موفق برای ارسال تکی (هر عدد مثبت)
                if (is_numeric($response) && $response > 0) {
                    error_log("Direct Single SMS sent successfully. RecID: " . $response);
                    return true;
                } else {
                    error_log("Direct Single SMS API Error. Response Code: " . print_r($response, true));
                    return false;
                }
            }

        } catch (SoapFault $e) {
            error_log("SMS SoapFault Error (Direct Send): " . $e->getMessage());
            return false;
        }
    }
}