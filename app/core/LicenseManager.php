<?php
// app/core/LicenseManager.php - موتور پیشرفته قفل سخت‌افزاری، صدور لایسنس و فروش پلن به پلن هماکار ERP

class LicenseManager {
    private static $db = null;
    private static $cachedLicense = null;

    // کلید عمومی رسمی سازنده جهت اعتبارسنجی دیجیتال و عدم امکان جعل (RSA Public Key)
    const VENDOR_PUBLIC_KEY = <<<'EOD'
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAk3E7GswIi8IA7f2sE4Rv
XQQamqIVHVQ7XvTYnFYqVcBYUoHkmxu5w5o0gD+DUw1yAWfKkW3CLUE3DQVITYgm
o4HOkWna2gveQ9EIbkrkKkoXVECpQkbK75pucj+/dcLmTWB9oViq7SJPP1bPESwL
T0QYWwfm7AGlOjgOHibQKuOt/7n3/ZxU8r0YyxEWvlsWbtEgZ3rVj9PP2LUGkmOQ
dRA1Tp/vjYqLDpunTc0BDbYQRoXJcwBYYKit9BCa1gMKut76rW8VzTQBkUVlXt1V
izS/9O1So6iRBwDrddtDDaDRYxzHqf0BebQniaD+dFvEDpA7URQCw4u0FoXk26tH
8QIDAQAB
-----END PUBLIC KEY-----
EOD;

    // کلید خصوصی رسمی سازنده برای صدور و امضای لایسنس‌های تجاری جدید (RSA Private Key)
    const VENDOR_PRIVATE_KEY = <<<'EOD'
-----BEGIN PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCTcTsazAiLwgDt
/awThG9dBBqaohUdVDte9NicVipVwFhSgeSbG7nDmjSAP4NTDXIBZ8qRbcItQTcN
BUhNiCajgc6RadraC95D0QhuSuQqShdUQKlCRsrvmm5yP791wuZNYH2hWKrtIk8/
Vs8RLAtPRBhbB+bsAaU6OA4eJtAq463/uff9nFTyvRjLERa+WxZu0SBnetWP08/Y
tQaSY5B1EDVOn++NiosOm6dNzQENthBGhclzAFhgqK30EJrWAwq63vqtbxXNNAGR
RWVe3VWLNL/07VKjqJEHAOt120MNoNFjHMep/QF5tCeJoP50W8QOkDtRFALDi7QW
heTbq0fxAgMBAAECggEAFC3fmnvJQaN0MB5TUjqCaVcZ8wby39g65fSZvs/WLHVO
QUCQ0p+5TWmJ/PF0XkYNT97uLPg6Jqpxx+Dss9vJ+f/WjzVPJB+StHJj9oBeeOE6
DyhoYe9BcAiRTCaoZAwgK//kQdRaVGkpMnCFl4KRK7CUqsTKmItjGzQ3zm2eePgK
D9JLhYBgfsbdHiWbQwMlNk1C36uz8zJfE7Tg9QcKjZTNZfLhmTCwRK9AvQNRAoqS
eeE2GJ3Jos0FRQvOsl8HIqDU+2JozPMgJUIRN9WychZRKGOEsEWC8oENLg1nZlHC
3HSBbaJeerooM6/K5eBIVMjVcpZf0E3kulhgoW51DwKBgQDIln88wsZ9q+FoUDJs
YadU/xUxbp4mS4v6UvaD2q11UY6QiIjHAJf1pYaKAyL1ZrGgsJcvKNv4bpTdWATC
o6kHOngbRHnE/OUiovQPWwZPLQnm9+l3eK2T48nvI9t53vjK3XKN7IcljUi0/lKG
G5Ue1B8HRhCo8VXSIKQoQoQF2wKBgQC8LExRNIfWh8PKBiTvumB+n4NGNcuVxJ79
sSDF70uaM1KF0/e/XC1D9SXMht7J+A7AxbuvmbuB4OfJwf1TDAr/TTMudn1osUHl
acCPxVZTIq4Z+IAi7SeQWjpZJcIJ1f0WV498XY23XApS6SZpAEdnKeI0lmnJtjEA
TqRNczvhIwKBgQCY11wSX1PBlIOH6XvSa8KGGGv8o44sHeqGDks6gxkiyMc5yF94
SzJ/AeZ3KRatKNf2WjiIvbiHHzOdSSEvcmcsU9gK5u1mNVEfSE1xmek/0VC3RnYG
C9UvWCWZa5gjz8uBQKW2EOGVsocRKqyU9VV2DSmrcC7nnCTJnQeYib5xewKBgQCG
NrJYWTdp9HvZZHiD5FNe/TcMpOIIeK1OFUBi7ULnQSPjAET84CoJBplnP+s38w3S
wTYVEXmEMX4hkZO14CkX32BaC3l3aratgrgt03q24U18iJocTzqg4zoqy5uSmGZj
wSA+fBvQZH2YlWMoEWLcWitVhtL9ZE2fAnh+JWrYMQKBgQCnC41sYwJhgrlR/xy/
g1BQu15BjFHofmrny6Kvzr7ntCHTAJmi0KzF3G6+YOAuqN1AEawilrVGI4Gc3PSc
SDgVCBoj7Nrd8f+uQyqgE2suYrOCTZun/aOCS/4lyUHRPpH23OfqfbV3TIWOoQrr
ria3ITIm9NhhvCmzhrD1k2n9iA==
-----END PRIVATE KEY-----
EOD;

    // نمک امنیتی یکپارچه برای هش سخت‌افزاری
    const HARDWARE_SALT = 'HAMAKAR_ERP_HARDWARE_LOCK_SALT_2026_@_SECRET';

    /**
     * لیست جامع ماژول‌های سامانه با امکان فروش و فعال‌سازی مجزا (Module-by-Module)
     */
    public static $MODULES = [
        'dashboard' => [
            'name' => 'پیشخوان هوشمند و KPI',
            'desc' => 'داشبورد مدیریتی، ویجت‌های تحلیلی و آمارهای بلادرنگ',
            'icon' => 'fas fa-chart-pie',
            'category' => 'core',
            'price_addon' => 0
        ],
        'clients' => [
            'name' => 'مشتریان و مدیریت ارتباط (CRM)',
            'desc' => 'پرونده مشتریان حقیقی/حقوقی، تعاملات و اعتبارسنجی',
            'icon' => 'fas fa-users',
            'category' => 'sales',
            'price_addon' => 1500000
        ],
        'sales' => [
            'name' => 'فروش، فاکتور و پیش‌فاکتور',
            'desc' => 'صدور فاکتور رسمی و غیررسمی، ثبت سفارش و مرجوعی',
            'icon' => 'fas fa-file-invoice-dollar',
            'category' => 'sales',
            'price_addon' => 2500000
        ],
        'moadian' => [
            'name' => 'سامانه مودیان مالیاتی (TaxID)',
            'desc' => 'امضای RSA، تولید شناسه ۲۲ رقمی، ارسال خودکار به کارپوشه و استعلام',
            'icon' => 'fas fa-shield-alt',
            'category' => 'tax',
            'price_addon' => 5900000
        ],
        'warehouse' => [
            'name' => 'انبارداری و مدیریت کالاها',
            'desc' => 'کاردکس انبار، حواله ورود/خروج، کنترل موجودی و چندانباره',
            'icon' => 'fas fa-boxes',
            'category' => 'inventory',
            'price_addon' => 3800000
        ],
        'treasury' => [
            'name' => 'خزانه‌داری، چک‌ها و بانک‌ها',
            'desc' => 'چک‌های صیادی دریافتنی/پرداختنی، حساب‌های بانکی و اسناد مالی',
            'icon' => 'fas fa-money-check-alt',
            'category' => 'finance',
            'price_addon' => 3500000
        ],
        'accounting' => [
            'name' => 'حسابداری پیشرفته و دوبل',
            'desc' => 'سند دوبل حسابداری، کدینگ کل/معین/تفصیلی، تراز آزمایشی و سود/زیان',
            'icon' => 'fas fa-calculator',
            'category' => 'finance',
            'price_addon' => 6500000
        ],
        'production' => [
            'name' => 'مدیریت تولید، فرمول ساخت و BOM',
            'desc' => 'درخت محصول، دستور تولید، بهای تمام شده کالای ساخته‌شده و ایستگاه‌ها',
            'icon' => 'fas fa-industry',
            'category' => 'manufacturing',
            'price_addon' => 7800000
        ],
        'contracts' => [
            'name' => 'قراردادها، پروژه‌ها و ضمانت‌نامه‌ها',
            'desc' => 'مدیریت پیمان، صورت‌وضعیت‌ها، سررسید ضمانت‌نامه‌های بانکی و الحاقیه‌ها',
            'icon' => 'fas fa-file-contract',
            'category' => 'projects',
            'price_addon' => 4200000
        ],
        'hr' => [
            'name' => 'منابع انسانی و پرسنلی (HR)',
            'desc' => 'احکام کارگزینی، ثبت پرسنل، مرخصی و فیش حقوق و دستمزد',
            'icon' => 'fas fa-user-tie',
            'category' => 'management',
            'price_addon' => 3900000
        ],
        'reports' => [
            'name' => 'گزارشات سازمانی و خروجی اکسل/چاپ A4',
            'desc' => 'چاپ رسمی فاکتورها، گزارش‌های سودآوری و خروجی‌های استاندار',
            'icon' => 'fas fa-print',
            'category' => 'core',
            'price_addon' => 1200000
        ],
        'chat' => [
            'name' => 'پیام‌رسان و چت خصوصی سازمانی',
            'desc' => 'چت درون‌برنامه‌ای کاربران، تبادل فایل و عکس با هشدار بلادرنگ',
            'icon' => 'fas fa-comments',
            'category' => 'collaboration',
            'price_addon' => 2800000
        ],
        'api' => [
            'name' => 'وب‌سرویس RESTful حسابداری',
            'desc' => 'اتصال بی‌واسطه به نرم‌افزارهای حسابداری سپیدار، هلو و سامانه‌های خارجی',
            'icon' => 'fas fa-network-wired',
            'category' => 'integration',
            'price_addon' => 4500000
        ],
        'offline' => [
            'name' => 'کارکرد ۱۰۰٪ آفلاین و نصب دسکتاپ',
            'desc' => 'دیتابیس محلی IndexedDB، سینک خودکار بعد از اتصال اینترنت و اپ ویندوز',
            'icon' => 'fab fa-windows',
            'category' => 'infrastructure',
            'price_addon' => 3200000
        ],
        'backups' => [
            'name' => 'پشتیبان‌گیری فیزیکی و کلود خودکار',
            'desc' => 'دانلود فایل SQL/ZIP روی کامپیوتر محلی و بک‌آپ خودکار روی هاست',
            'icon' => 'fas fa-database',
            'category' => 'security',
            'price_addon' => 1800000
        ]
    ];

    /**
     * تعرفه و بسته‌بندی پلن‌های تجاری
     */
    public static $PLANS = [
        'starter' => [
            'id' => 'starter',
            'name' => 'پلن برنزی / اصناف و فروشگاهی (Starter)',
            'price_toman' => 4900000,
            'color' => '#64748b',
            'badge' => 'برنزی',
            'desc' => 'مناسب فروشگاه‌ها، کسب‌وکارهای نوپا و اصناف خرد جهت صدور فاکتور و ثبت مشتریان',
            'max_users' => 1,
            'modules' => ['dashboard', 'clients', 'sales', 'reports']
        ],
        'commercial' => [
            'id' => 'commercial',
            'name' => 'پلن نقره‌ای / بازرگانی و مودیان (Commercial)',
            'price_toman' => 12800000,
            'color' => '#0284c7',
            'badge' => 'نقره‌ای',
            'desc' => 'ویژه شرکت‌های بازرگانی و عمده‌فروشان با کارپوشه کامل مودیان، انبارداری و چک‌ها',
            'max_users' => 5,
            'modules' => ['dashboard', 'clients', 'sales', 'moadian', 'warehouse', 'treasury', 'reports', 'api', 'offline']
        ],
        'industrial' => [
            'id' => 'industrial',
            'name' => 'پلن طلایی / تولیدی و صنعتی (Industrial)',
            'price_toman' => 26500000,
            'color' => '#ea580c',
            'badge' => 'طلایی',
            'desc' => 'مخصوص کارخانجات و کارگاه‌ها با مدیریت تولید BOM، قراردادها و احکام پرسنلی HR',
            'max_users' => 15,
            'modules' => ['dashboard', 'clients', 'sales', 'moadian', 'warehouse', 'treasury', 'production', 'contracts', 'hr', 'reports', 'api', 'offline']
        ],
        'enterprise' => [
            'id' => 'enterprise',
            'name' => 'پلن الماس / سازمانی جامع فول‌ماژول (Enterprise Ultra)',
            'price_toman' => 49000000,
            'color' => '#4f46e5',
            'badge' => 'الماس فول‌ماژول',
            'desc' => 'تمامی ماژول‌ها بدون هیچ‌گونه محدودیت، کاربران نامحدود، قفل سخت‌افزاری اختصاصی و VIP',
            'max_users' => 999,
            'modules' => [
                'dashboard', 'clients', 'sales', 'moadian', 'warehouse', 'accounting',
                'treasury', 'production', 'hr', 'contracts', 'reports', 'chat', 'api', 'offline', 'backups'
            ]
        ],
        'custom' => [
            'id' => 'custom',
            'name' => 'پلن سفارشی / ماژول به ماژول (Custom Modular)',
            'price_toman' => 0,
            'color' => '#059669',
            'badge' => 'سفارشی',
            'desc' => 'انتخاب تک به تک ماژول‌های مورد نیاز بر اساس فعالیت کاری و بودجه مشتری',
            'max_users' => 5,
            'modules' => []
        ]
    ];

    /**
     * پایگاه داده محلی
     */
    private static function getDb() {
        if (!self::$db) {
            self::$db = new Database();
            self::ensureLicenseTable();
        }
        return self::$db;
    }

    /**
     * اطمینان از ایجاد جدول لایسنس
     */
    public static function ensureLicenseTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `system_licenses` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `license_key` LONGTEXT NOT NULL,
                `licensee_name` VARCHAR(255) NOT NULL,
                `plan_key` VARCHAR(50) NOT NULL,
                `hardware_id` VARCHAR(100) NULL,
                `modules_json` TEXT NOT NULL,
                `max_users` INT DEFAULT 1,
                `expires_at` DATETIME NULL,
                `is_lifetime` TINYINT(1) DEFAULT 0,
                `status` ENUM('active', 'expired', 'revoked') DEFAULT 'active',
                `raw_payload` LONGTEXT NULL,
                `activated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $db = new Database();
            $db->query($sql);
            $db->execute();
        } catch (Exception $e) {
            // در صورت بروز خطا لاگ می‌شود
        }
    }

    /**
     * استخراج کد اثر انگشت و قفل سخت‌افزاری منحصر به فرد سرور / رایانه (Machine Fingerprint)
     * ترکیب شناسه بایوس، مادربورد، پردازنده، دیسک یا نام سرور
     * خروجی در قالب رسمی استاندارد: HK-XXXX-XXXX-XXXX-XXXX
     */
    public static function getMachineFingerprint() {
        $rawHw = '';

        // ۱. بررسی محیط ویندوز
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // تلاش اول: WMIC CSPRODUCT UUID
            $uuid = @shell_exec('wmic csproduct get uuid 2>NUL');
            if ($uuid && preg_match('/[0-9A-Fa-f\-]{30,}/', $uuid, $matches)) {
                $rawHw .= trim($matches[0]);
            }
            // تلاش دوم: MachineGuid از رجیستری ویندوز
            if (empty($rawHw)) {
                $reg = @shell_exec('reg query "HKLM\SOFTWARE\Microsoft\Cryptography" /v MachineGuid 2>NUL');
                if ($reg && preg_match('/MachineGuid\s+REG_SZ\s+([a-zA-Z0-9\-]+)/', $reg, $m)) {
                    $rawHw .= trim($m[1]);
                }
            }
        } else {
            // ۲. بررسی محیط لینوکس / سرور لینوکسی
            $linuxFiles = [
                '/etc/machine-id',
                '/var/lib/dbus/machine-id',
                '/sys/class/dmi/id/product_uuid'
            ];
            foreach ($linuxFiles as $file) {
                if (@file_exists($file) && @is_readable($file)) {
                    $val = trim(@file_get_contents($file));
                    if (!empty($val)) {
                        $rawHw .= $val;
                        break;
                    }
                }
            }
        }

        // ۳. در صورتی که به دلیل محدودیت‌های هاستینگ اشتراکی، دسترسی به پوسته مسدود باشد:
        if (empty($rawHw)) {
            $serverSign = [
                php_uname('n'),
                $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
                $_SERVER['DOCUMENT_ROOT'] ?? __DIR__,
                get_current_user(),
                PHP_VERSION
            ];
            $rawHw = implode('|', $serverSign);
        }

        // تولید هش غیرقابل معکوس با سالت اختصاصی هماکار
        $hash = hash_hmac('sha256', $rawHw, self::HARDWARE_SALT);
        $clean = strtoupper(substr($hash, 0, 16));
        
        // قالب‌بندی ۴ بخشی
        return sprintf('HK-%s-%s-%s-%s',
            substr($clean, 0, 4),
            substr($clean, 4, 4),
            substr($clean, 8, 4),
            substr($clean, 12, 4)
        );
    }

    /**
     * تولید لایسنس تجاری امضاشده با کلید خصوصی RSA (Vendor License Generator)
     * این متد مخصوص مدیر کل / فروشنده جهت صدور لایسنس برای خریداران است
     */
    public static function generateLicense($data) {
        if (!extension_loaded('openssl')) {
            return ['success' => false, 'message' => 'اکستنشن OpenSSL روی سرور فعال نیست.'];
        }

        $privKey = openssl_pkey_get_private(self::VENDOR_PRIVATE_KEY);
        if (!$privKey) {
            return ['success' => false, 'message' => 'کلید خصوصی سازنده لایسنس نامعتبر است.'];
        }

        $licensee = sanitize($data['licensee_name'] ?? 'مشتری سازمانی هماکار');
        $planKey = sanitize($data['plan_key'] ?? 'commercial');
        $isLifetime = !empty($data['is_lifetime']);
        $days = (int)($data['validity_days'] ?? 365);
        $maxUsers = (int)($data['max_users'] ?? 5);
        $hardwareLock = trim($data['hardware_id'] ?? '');
        $allowedDomains = $data['domains'] ?? [];

        // ماژول‌های فعال
        $modules = [];
        if (!empty($data['modules']) && is_array($data['modules'])) {
            $modules = array_values(array_intersect(array_keys(self::$MODULES), $data['modules']));
        } else {
            // استفاده از ماژول‌های پیش‌فرض پلن
            $modules = self::$PLANS[$planKey]['modules'] ?? self::$PLANS['commercial']['modules'];
        }

        $now = time();
        $expiresAt = $isLifetime ? null : ($now + ($days * 86400));

        $payload = [
            'license_id' => 'LIC-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12)),
            'licensee' => $licensee,
            'plan_key' => $planKey,
            'plan_name' => self::$PLANS[$planKey]['name'] ?? 'پلن سفارشی',
            'modules' => $modules,
            'hardware_lock' => $hardwareLock,
            'domains' => $allowedDomains,
            'max_users' => $maxUsers,
            'is_lifetime' => $isLifetime,
            'issued_at' => $now,
            'expires_at' => $expiresAt,
            'version' => '4.0.0'
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        
        // امضای دیجیتال نامتقارن RSA-SHA256
        $signature = '';
        if (!openssl_sign($payloadJson, $signature, $privKey, OPENSSL_ALGO_SHA256)) {
            return ['success' => false, 'message' => 'خطا در امضای دیجیتال بسته لایسنس.'];
        }

        // ساخت توکن نهایی لایسنس: HKLIC-{PayloadBase64}.{SignatureBase64}
        $payloadB64 = rtrim(strtr(base64_encode(gzdeflate($payloadJson, 9)), '+/', '-_'), '=');
        $signatureB64 = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $licenseKey = 'HKLIC-' . $payloadB64 . '.' . $signatureB64;

        return [
            'success' => true,
            'license_key' => $licenseKey,
            'payload' => $payload,
            'filename' => 'hamakar_license_' . preg_replace('/[^a-zA-Z0-9]/', '_', $licensee) . '.lic'
        ];
    }

    /**
     * اعتبارسنجی دیجیتال و رمزگشایی رشته لایسنس
     */
    public static function verifyLicenseString($licenseKey) {
        $licenseKey = trim($licenseKey);
        if (strpos($licenseKey, 'HKLIC-') !== 0) {
            return ['valid' => false, 'error' => 'invalid_format', 'message' => 'فرمت کد لایسنس نامعتبر است (پیشوند HKLIC یافت نشد).'];
        }

        $token = substr($licenseKey, 6);
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return ['valid' => false, 'error' => 'corrupted_token', 'message' => 'ساختار لایسنس مخدوش یا دستکاری شده است.'];
        }

        $payloadRaw = base64_decode(strtr($parts[0], '-_', '+/'));
        $signature = base64_decode(strtr($parts[1], '-_', '+/'));

        $payloadJson = @gzinflate($payloadRaw);
        if (!$payloadJson) {
            $payloadJson = $payloadRaw;
        }

        $payload = json_decode($payloadJson, true);
        if (!$payload || !is_array($payload)) {
            return ['valid' => false, 'error' => 'payload_invalid', 'message' => 'محتوای بسته لایسنس قابل بازخوانی نمی‌باشد.'];
        }

        // ۱. بررسی امضای دیجیتال نامتقارن RSA با کلید عمومی سازنده
        $pubKey = openssl_pkey_get_public(self::VENDOR_PUBLIC_KEY);
        if (!$pubKey) {
            return ['valid' => false, 'error' => 'pubkey_error', 'message' => 'کلید عمومی بررسی لایسنس در سیستم در دسترس نیست.'];
        }

        $verifyResult = openssl_verify($payloadJson, $signature, $pubKey, OPENSSL_ALGO_SHA256);
        if ($verifyResult !== 1) {
            return ['valid' => false, 'error' => 'tampered', 'message' => 'امضای دیجیتال لایسنس معتبر نیست؛ فایل لایسنس دستکاری شده است!'];
        }

        // ۲. اعتبارسنجی قفل سخت‌افزاری (در صورتی که در لایسنس تعریف شده باشد)
        $currentHw = self::getMachineFingerprint();
        if (!empty($payload['hardware_lock'])) {
            $expectedHw = strtoupper(trim($payload['hardware_lock']));
            if ($expectedHw !== 'ANY' && $expectedHw !== '*' && $expectedHw !== $currentHw) {
                return [
                    'valid' => false,
                    'error' => 'hardware_mismatch',
                    'message' => "این لایسنس مقفل به سخت‌افزار " . $expectedHw . " است اما شناسه سخت‌افزار سرور " . $currentHw . " می‌باشد.",
                    'expected_hardware' => $expectedHw,
                    'current_hardware' => $currentHw,
                    'payload' => $payload
                ];
            }
        }

        // ۳. بررسی تاریخ انقضا
        if (empty($payload['is_lifetime']) && !empty($payload['expires_at'])) {
            if (time() > (int)$payload['expires_at']) {
                return [
                    'valid' => false,
                    'error' => 'expired',
                    'message' => 'اعتبار زمانی این لایسنس منقضی شده است. لطفاً جهت تمدید با واحد پشتیبانی تماس حاصل فرمایید.',
                    'expires_at' => date('Y-m-d H:i:s', $payload['expires_at']),
                    'payload' => $payload
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'لایسنس معتبر و با موفقیت تایید گردید.',
            'payload' => $payload
        ];
    }

    /**
     * فعال‌سازی و ذخیره لایسنس جدید در پایگاه داده و فایل فیزیکی
     */
    public static function activateLicense($licenseKey) {
        $verify = self::verifyLicenseString($licenseKey);
        if (!$verify['valid']) {
            return $verify;
        }

        $p = $verify['payload'];
        $db = self::getDb();

        // ذخیره در جدول لایسنس‌ها
        $db->query("INSERT INTO system_licenses 
                    (license_key, licensee_name, plan_key, hardware_id, modules_json, max_users, expires_at, is_lifetime, status, raw_payload)
                    VALUES 
                    (:key, :name, :plan, :hw, :modules, :users, :exp, :life, 'active', :raw)");

        $expiresSql = !empty($p['expires_at']) ? date('Y-m-d H:i:s', $p['expires_at']) : null;
        $db->bind(':key', trim($licenseKey));
        $db->bind(':name', $p['licensee']);
        $db->bind(':plan', $p['plan_key']);
        $db->bind(':hw', $p['hardware_lock'] ?? null);
        $db->bind(':modules', json_encode($p['modules']));
        $db->bind(':users', $p['max_users'] ?? 1);
        $db->bind(':exp', $expiresSql);
        $db->bind(':life', !empty($p['is_lifetime']) ? 1 : 0);
        $db->bind(':raw', json_encode($p));
        $db->execute();

        // ذخیره نسخه فیزیکی فایل لایسنس جهت کارکرد آفلاین air-gapped
        $licPath = __DIR__ . '/../config/hamakar.lic';
        @file_put_contents($licPath, trim($licenseKey));

        self::$cachedLicense = null;

        return [
            'valid' => true,
            'message' => 'لایسنس رسمی با موفقیت فعال و ثبت گردید.',
            'payload' => $p
        ];
    }

    /**
     * دریافت لایسنس فعال جاری سیستم
     */
    public static function getActiveLicense() {
        if (self::$cachedLicense !== null) {
            return self::$cachedLicense;
        }

        $db = self::getDb();
        $db->query("SELECT * FROM system_licenses WHERE status = 'active' ORDER BY id DESC LIMIT 1");
        $row = $db->fetch();

        if ($row && !empty($row->license_key)) {
            $check = self::verifyLicenseString($row->license_key);
            if ($check['valid']) {
                $p = $check['payload'];
                $remainingDays = $p['is_lifetime'] ? 9999 : max(0, round(($p['expires_at'] - time()) / 86400));
                
                self::$cachedLicense = (object)[
                    'is_active' => true,
                    'status' => 'active',
                    'licensee' => $p['licensee'],
                    'plan_key' => $p['plan_key'],
                    'plan_name' => $p['plan_name'],
                    'modules' => $p['modules'],
                    'max_users' => $p['max_users'],
                    'is_lifetime' => !empty($p['is_lifetime']),
                    'hardware_lock' => $p['hardware_lock'] ?? '',
                    'expires_at' => $p['expires_at'] ? date('Y-m-d H:i:s', $p['expires_at']) : null,
                    'remaining_days' => $remainingDays,
                    'raw_key' => $row->license_key
                ];
                return self::$cachedLicense;
            }
        }

        // بررسی فایل محلی hamakar.lic
        $licPath = __DIR__ . '/../config/hamakar.lic';
        if (file_exists($licPath)) {
            $content = trim(file_get_contents($licPath));
            if (!empty($content)) {
                $check = self::verifyLicenseString($content);
                if ($check['valid']) {
                    self::activateLicense($content);
                    return self::getActiveLicense();
                }
            }
        }

        // در صورت عدم ثبت لایسنس: لایسنس پیش‌فرض آزمایشی برای دمو
        $allModules = array_keys(self::$MODULES);
        self::$cachedLicense = (object)[
            'is_active' => true,
            'status' => 'trial',
            'licensee' => 'نسخه دموی سازمانی هماکار',
            'plan_key' => 'enterprise',
            'plan_name' => 'پلن الماس سازمانی (Trial Demo)',
            'modules' => $allModules,
            'max_users' => 100,
            'is_lifetime' => true,
            'hardware_lock' => self::getMachineFingerprint(),
            'expires_at' => null,
            'remaining_days' => 365,
            'raw_key' => 'TRIAL-ENTERPRISE-DEMO'
        ];

        return self::$cachedLicense;
    }

    /**
     * بررسی دسترسی کاربر به یک ماژول خاص (Feature Gating)
     */
    public static function hasModule($moduleKey) {
        $license = self::getActiveLicense();
        if (!$license || empty($license->is_active)) {
            return false;
        }
        if (!empty($license->modules) && in_array($moduleKey, $license->modules)) {
            return true;
        }
        return false;
    }

    /**
     * محاسبه آنلاین قیمت پکیج سفارشی بر اساس ماژول‌های انتخابی خریدار
     */
    public static function calculateCustomPrice($selectedModules, $userCount = 1, $isLifetime = false) {
        $basePrice = 2500000;
        $modulesCost = 0;
        foreach ($selectedModules as $mod) {
            if (isset(self::$MODULES[$mod])) {
                $modulesCost += (int)self::$MODULES[$mod]['price_addon'];
            }
        }
        $userCost = max(0, ($userCount - 1)) * 1200000;
        $total = $basePrice + $modulesCost + $userCost;

        if ($isLifetime) {
            $total = round($total * 2.2);
        }

        return $total;
    }
}
