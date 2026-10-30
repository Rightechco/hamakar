<?php
namespace App\Core;

/**
 * Hion Enterprise Cryptographic Licensing & Hardware Lock Engine
 * معماری امنیتی، قفل سخت‌افزاری و تولید/اعتبارسنجی لایسنس دیجیتال
 */
class LicenseEngine {

    const ALGORITHM = OPENSSL_ALGO_SHA256;

    // کلید عمومی رسمی های‌آن جهت اعتبارسنجی ۱۰۰٪ آفلاین و ضد جعل
    const DEFAULT_PUBLIC_KEY = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuRMRNNp2ifayLKig6sKz
E/nh5G1oBXGuBE0Se9Djsowlj0empX+nDSWegGufjV/u84By5txguSza8JrI5yyk
0pKUI44kwJTVR07Rb0bjW/fe7c2sKh4zpdllloYNW9ZAQeuqFPUQlRgpq2mEHCaU
cwcPl5txfI16i5cp8arJTJIw7P3AgYVFY4oaDBuQ9RZOcvACPq9HDxzkUuZX5Ug/
3gOPZxn7z0WliYBwthbtJo2GTlX9+E+Pl07LX45olKcooB3+UjN1V7YMJtZXYQqy
H15qug7J5yJgGkwq4TH7OcVcFvSL+CsjvmFBejXVXklEbgfC6atfGbaYGhC2q13Q
iQIDAQAB
-----END PUBLIC KEY-----
EOD;

    // لیست کل ماژول‌های سیستم
    const AVAILABLE_MODULES = [
        'mod_pos_core'     => ['name' => 'صندوق فروشگاهی مکانیزه (آفلاین)', 'price' => 25000000, 'is_core' => true],
        'mod_pc_pos'       => ['name' => 'اتصال مستقیم کارتخوان بانکی (PC-POS)', 'price' => 3000000, 'is_core' => false],
        'mod_courier'      => ['name' => 'اپلیکیشن پیک موتوری با نشان و بلد', 'price' => 4500000, 'is_core' => false],
        'mod_boss_app'     => ['name' => 'داشبورد جیبی موبایل مدیر', 'price' => 3500000, 'is_core' => false],
        'mod_dual_screen'  => ['name' => 'نمایشگر دوم دید مشتری (Dual Screen)', 'price' => 2500000, 'is_core' => false],
        'mod_smart_sms'    => ['name' => 'باشگاه مشتریان و پیامک خودکار', 'price' => 2500000, 'is_core' => false],
        'mod_advanced_inv' => ['name' => 'انبارداری پیشرفته و کنترل ضایعات/انقضا', 'price' => 4000000, 'is_core' => false],
        'mod_online_shop'  => ['name' => 'فروشگاه اینترنتی آنلاین متصل به انبار', 'price' => 5000000, 'is_core' => false],
    ];

    /**
     * استخراج اثر انگشت یکتای سخت‌افزار (Machine Fingerprint)
     */
    public static function getMachineFingerprint() {
        $raw = [];

        // ۱. بررسی محیط ویندوز
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Motherboard Serial
            $mb = self::runShellSilently('wmic baseboard get serialnumber /value');
            if ($mb) $raw[] = trim(str_replace('SerialNumber=', '', $mb));

            // System UUID
            $uuid = self::runShellSilently('wmic csproduct get uuid /value');
            if ($uuid) $raw[] = trim(str_replace('UUID=', '', $uuid));

            // CPU Processor ID
            $cpu = self::runShellSilently('wmic cpu get processorid /value');
            if ($cpu) $raw[] = trim(str_replace('ProcessorId=', '', $cpu));

            // MAC Address
            $mac = self::runShellSilently('getmac /NH');
            if ($mac) {
                preg_match('/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/', $mac, $matches);
                if (!empty($matches[0])) $raw[] = $matches[0];
            }
        } else {
            // ۲. محیط لینوکس یا سرور ابری
            if (@is_readable('/etc/machine-id')) {
                $raw[] = trim(@file_get_contents('/etc/machine-id'));
            } elseif (@is_readable('/var/lib/dbus/machine-id')) {
                $raw[] = trim(@file_get_contents('/var/lib/dbus/machine-id'));
            }

            // خواندن سریال سیستم از sysfs
            if (@is_readable('/sys/class/dmi/id/product_uuid')) {
                $raw[] = trim(@file_get_contents('/sys/class/dmi/id/product_uuid'));
            }

            // هاست‌نیم و مسیر وب
            $raw[] = gethostname();
        }

        // فالبک برای هاست‌های اشتراکی
        if (isset($_SERVER['SERVER_ADDR'])) $raw[] = $_SERVER['SERVER_ADDR'];
        if (isset($_SERVER['HTTP_HOST']))   $raw[] = $_SERVER['HTTP_HOST'];
        $raw[] = php_uname('n') . '-' . php_uname('m');

        $combined = implode('|#|', array_filter($raw));
        if (empty($combined)) {
            $combined = 'GENERIC-FALLBACK-' . php_uname();
        }

        // ایجاد هش ۶۴ بیتی یکتا و تبدیل به فرمت خوانا: HN-XXXX-XXXX-XXXX
        $hash = strtoupper(hash('sha256', $combined . 'HION_SALT_2026_SECURE'));
        $code = 'HN-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4);

        return $code;
    }

    private static function runShellSilently($command) {
        if (!function_exists('shell_exec')) return null;
        try {
            $out = @shell_exec($command);
            return $out ? trim($out) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * صدور لایسنس دیجیتال با امضای نامتقارن RSA
     * این متد در سمت سرور صدور (hamakar.ir یا hion.ir) با کلید خصوصی اجرا می‌شود.
     */
    public static function issueLicense(array $params, $privateKeyPem) {
        $required = ['customer_name', 'store_name', 'phone', 'modules'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                throw new \InvalidArgumentException("فیلد الزامی {$field} ارائه نشده است.");
            }
        }

        $now = date('Y-m-d H:i:s');
        $expiresAt = !empty($params['expires_at']) ? $params['expires_at'] : date('Y-m-d H:i:s', strtotime('+1 year'));

        // ساخت کد لایسنس منحصر به فرد
        $licenseKey = !empty($params['license_key']) 
            ? $params['license_key'] 
            : 'HION-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4)) . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 4, 4)) . '-' . date('y');

        $payload = [
            'license_version' => '2.0',
            'license_key'     => $licenseKey,
            'customer_name'   => $params['customer_name'],
            'store_name'      => $params['store_name'],
            'phone'           => $params['phone'],
            'city'            => !empty($params['city']) ? $params['city'] : 'ایران',
            'machine_id'      => !empty($params['machine_id']) ? strtoupper(trim($params['machine_id'])) : '*',
            'domain'          => !empty($params['domain']) ? strtolower(trim($params['domain'])) : '*',
            'plan_tier'       => !empty($params['plan_tier']) ? $params['plan_tier'] : 'commercial',
            'modules'         => (array)$params['modules'],
            'issued_at'       => $now,
            'expires_at'      => $expiresAt,
            'limits'          => [
                'max_terminals' => isset($params['max_terminals']) ? (int)$params['max_terminals'] : 1,
                'max_couriers'  => isset($params['max_couriers']) ? (int)$params['max_couriers'] : 3,
            ]
        ];

        // سورت کلیدها برای یکپارچگی امضا
        ksort($payload);
        $canonicalJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // تولید امضای دیجیتال با RSA
        $privRes = openssl_get_privatekey($privateKeyPem);
        if (!$privRes) {
            throw new \RuntimeException("کلید خصوصی نامعتبر است: " . openssl_error_string());
        }

        $rawSignature = '';
        $ok = openssl_sign($canonicalJson, $rawSignature, $privRes, self::ALGORITHM);
        if (!$ok) {
            throw new \RuntimeException("خطا در ایجاد امضای دیجیتال لایسنس.");
        }

        $finalBundle = [
            'payload'   => $payload,
            'signature' => base64_encode($rawSignature)
        ];

        // پکیج نهایی به صورت توکن Base64 فشرده و فایل JSON
        $token = base64_encode(json_encode($finalBundle, JSON_UNESCAPED_UNICODE));

        return [
            'license_key'    => $licenseKey,
            'license_token'  => $token,
            'license_file'   => json_encode($finalBundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'payload'        => $payload,
            'signature'      => base64_encode($rawSignature)
        ];
    }

    /**
     * اعتبارسنجی ۱۰۰٪ آفلاین لایسنس با کلید عمومی (Public Key)
     * این متد داخل کلاینت صندوق یا پنل مشتری اجرا می‌شود و به اینترنت نیاز ندارد.
     */
    public static function verifyLicense($tokenOrJson, $publicKeyPem = null, $currentMachineId = null, $currentDomain = null) {
        $tokenOrJson = trim($tokenOrJson);
        $data = null;

        if (substr($tokenOrJson, 0, 1) === '{') {
            $data = json_decode($tokenOrJson, true);
        } else {
            $decoded = base64_decode($tokenOrJson, true);
            if ($decoded) {
                $data = json_decode($decoded, true);
            }
        }

        if (!$data || empty($data['payload']) || empty($data['signature'])) {
            return [
                'valid'   => false,
                'status'  => 'MALFORMED',
                'message' => 'فرمت فایل یا کد لایسنس نامعتبر یا مخدوش است.'
            ];
        }

        $payload = $data['payload'];
        $signature = base64_decode($data['signature']);

        // ۱. بررسی امضای دیجیتال
        ksort($payload);
        $canonicalJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $publicKeyPem = $publicKeyPem ?: self::DEFAULT_PUBLIC_KEY;
        $pubRes = openssl_get_publickey($publicKeyPem);
        if (!$pubRes) {
            return [
                'valid'   => false,
                'status'  => 'INVALID_PUBLIC_KEY',
                'message' => 'کلید عمومی سیستم مفقود یا نامعتبر است.'
            ];
        }

        $verify = openssl_verify($canonicalJson, $signature, $pubRes, self::ALGORITHM);
        if ($verify !== 1) {
            return [
                'valid'   => false,
                'status'  => 'TAMPERED',
                'message' => 'امضای دیجیتال لایسنس نامعتبر است! اطلاعات لایسنس دستکاری شده است.'
            ];
        }

        // ۲. بررسی تاریخ انقضا
        $now = time();
        $expireTime = strtotime($payload['expires_at']);
        if ($now > $expireTime) {
            $daysExpired = floor(($now - $expireTime) / 86400);
            return [
                'valid'        => false,
                'status'       => 'EXPIRED',
                'days_expired' => $daysExpired,
                'message'      => "اعتبار لایسنس شما {$daysExpired} روز پیش منقضی شده است. لطفاً جهت تمدید اقدام فرمایید.",
                'payload'      => $payload
            ];
        }

        // ۳. بررسی قفل سخت‌افزاری (در صورتی که ماشین کد محدود شده باشد)
        if ($payload['machine_id'] !== '*') {
            $expectedMachine = strtoupper(trim($payload['machine_id']));
            $actualMachine = strtoupper(trim($currentMachineId ?: self::getMachineFingerprint()));

            if ($expectedMachine !== $actualMachine) {
                return [
                    'valid'            => false,
                    'status'           => 'MACHINE_MISMATCH',
                    'message'          => "این لایسنس برای سخت‌افزار دیگری ({$expectedMachine}) صادر شده و روی این سیستم ({$actualMachine}) قابل اجرا نیست.",
                    'expected_machine' => $expectedMachine,
                    'actual_machine'   => $actualMachine,
                    'payload'          => $payload
                ];
            }
        }

        // ۴. بررسی دامنه (در صورت وجود محدودیت دامنه)
        if (!empty($payload['domain']) && $payload['domain'] !== '*') {
            $expectedDomain = strtolower(trim($payload['domain']));
            $actualDomain = strtolower(trim($currentDomain ?: (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')));
            
            // حذف پورت از دامنه
            $actualDomain = preg_replace('/:\d+$/', '', $actualDomain);

            if ($actualDomain && $expectedDomain !== $actualDomain && $expectedDomain !== 'demo.hyon.ir') {
                return [
                    'valid'           => false,
                    'status'          => 'DOMAIN_MISMATCH',
                    'message'         => "این لایسنس مخصوص دامنه {$expectedDomain} می‌باشد.",
                    'expected_domain' => $expectedDomain,
                    'actual_domain'   => $actualDomain,
                    'payload'         => $payload
                ];
            }
        }

        $daysRemaining = floor(($expireTime - $now) / 86400);

        return [
            'valid'          => true,
            'status'         => 'ACTIVE',
            'days_remaining' => $daysRemaining,
            'message'        => "لایسنس معتبر است ({$daysRemaining} روز باقی‌مانده).",
            'payload'        => $payload,
            'modules'        => $payload['modules'],
            'has_module'     => function($moduleKey) use ($payload) {
                return in_array($moduleKey, $payload['modules']);
            }
        ];
    }
}
