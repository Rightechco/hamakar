<?php
// app/views/admin/contracts/view_contract.php
// نسخه نهایی با تمام اصلاحات

// استخراج متغیرها
$companyName = $companyInfo['name'];
$customerName = $customer->name;

$contractTitle = $contract->title;
$serviceTypeKey = $contract->service_type;
$serviceTitlePersian = $serviceTypes[$serviceTypeKey] ?? $serviceTypeKey;
$totalAmount = (float)$contract->total_amount;
$payment_terms_text = !empty($contract->description) ? $contract->description : "مطابق با توافق شفاهی طرفین.";

// ✅ اصلاح فرمت تاریخ برای سازگاری با تابع jdate
$startDate = $contract->start_date ? jdate('Y/m/d', strtotime($contract->start_date)) : 'نامشخص';
$endDate = $contract->end_date ? jdate('Y/m/d', strtotime($contract->end_date)) : 'نامحدود';

/**
 * ✅ تابع تولید کننده متن قرارداد با پوشش تمام خدمات
 */
function getContractClauses($serviceType, $serviceTitle, $paymentDescription) {
    // --- بخش اول: تعهدات مختص هر خدمت ---
    $service_specific_clauses = [];
    switch ($serviceType) {
        case 'wordpress_website_design':
        case 'dedicated_website_design':
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "طراحی، پیاده‌سازی و تحویل یک وب‌سایت اینترنتی پویا، که از این پس «پروژه» نامیده می‌شود، با جزئیات فنی و امکانات مندرج در پیوست شماره یک.",
                "ماده ۲: تعهدات مجری (طرف اول)" => "۱- تحلیل نیازمندی‌ها و طراحی ساختار و رابط کاربری (UI/UX) پروژه و اخذ تأییدیه از کارفرما. ۲- برنامه‌نویسی و پیاده‌سازی کامل بخش‌های مختلف پروژه. ۳- ارائه یک جلسه آموزش آنلاین جهت مدیریت وب‌سایت. ۴- ارائه سه (۳) ماه پشتیبانی فنی رایگان محدود به رفع ایرادات (باگ‌های) احتمالی.",
                "ماده ۳: تعهدات کارفرما (طرف دوم)" => "۱- ارائه کلیه اطلاعات، محتوا و دسترسی‌های لازم جهت اجرای پروژه. ۲- بررسی و اعلام نظر کتبی نسبت به موارد تحویل داده شده در هر مرحله حداکثر ظرف مدت ۳ روز کاری. ۳- پرداخت به‌موقع کلیه مبالغ قرارداد.",
            ];
            break;

        case 'seo_services':
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "ارائه خدمات بهینه‌سازی وب‌سایت برای موتورهای جستجو (SEO) با هدف بهبود رتبه و افزایش بازدیدکنندگان ارگانیک برای کلمات کلیدی منتخب.",
                "ماده ۲: تعهدات مجری (طرف اول)" => "۱- انجام بررسی و تحلیل فنی کامل وب‌سایت. ۲- تحقیق و توسعه استراتژی کلمات کلیدی. ۳- بهینه‌سازی داخلی (On-Page) و خارجی (Off-Page) وب‌سایت. ۴- ارائه گزارش عملکرد ماهانه از روند پیشرفت.",
                "ماده ۳: تعهدات کارفرما (طرف دوم)" => "۱- تأمین دسترسی کامل مجری به پنل مدیریت وب‌سایت و ابزارهای تحلیلی. ۲- همکاری در جهت تولید یا اصلاح محتوای وب‌سایت. ۳- عدم ایجاد تغییرات فنی اساسی بدون هماهنگی.",
            ];
            break;

        case 'social_media_management':
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "مدیریت، تولید محتوا و فعالیت در شبکه‌های اجتماعی کارفرما (مندرج در پیوست) با هدف افزایش تعامل و رشد مخاطبان.",
                "ماده ۲: تعهدات مجری (طرف اول)" => "۱- تدوین استراتژی و تقویم محتوایی ماهانه. ۲- تولید و انتشار محتوای گرافیکی و متنی بر اساس تقویم تأیید شده. ۳- مدیریت کامنت‌ها و پیام‌های دریافتی. ۴- ارائه گزارش عملکرد ماهانه.",
                "ماده ۳: تعهدات کارفرما (طرف دوم)" => "۱- ارائه کلیه دسترسی‌های لازم به حساب‌های شبکه‌های اجتماعی. ۲- تأمین素材 (عکس، ویدیو، اطلاعات) مورد نیاز. ۳- تأیید تقویم محتوایی در زمان مقرر.",
            ];
            break;

        case 'branding_marketing':
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "ارائه خدمات مشاوره و اجرای کمپین‌های بازاریابی دیجیتال و توسعه هویت برند کارفرما بر اساس اهداف مشخص شده در پیوست قرارداد.",
                "ماده ۲: تعهدات مجری (طرف اول)" => "۱- طراحی و تدوین استراتژی جامع بازاریابی. ۲- مدیریت و اجرای کمپین‌های تبلیغاتی آنلاین (مانند Google Ads و تبلیغات در شبکه‌های اجتماعی). ۳- طراحی یا بازنگری هویت بصری برند. ۴- تحلیل نتایج کمپین‌ها و ارائه گزارشات تحلیلی.",
                "ماده ۳: تعهدات کارفرما (طرف دوم)" => "۱- تعیین بودجه مشخص برای کمپین‌های تبلیغاتی. ۲- ارائه دقیق اطلاعات مربوط به بازار هدف و محصولات. ۳- همکاری مستمر و ارائه بازخورد در طول اجرای پروژه.",
            ];
            break;

        case 'content_production':
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "تولید محتوای متنی، تصویری یا ویدیویی برای وب‌سایت و یا سایر کانال‌های دیجیتال کارفرما، مطابق با تعداد و مشخصات ذکر شده در پیوست.",
                "ماده ۲: تعهدات مجری (طرف اول)" => "۱- تحقیق و گردآوری مطالب بر اساس موضوعات درخواستی کارفرما. ۲- نگارش و ویراستاری محتوای متنی به صورت کاملاً اختصاصی و بهینه شده برای موتورهای جستجو. ۳- امکان یک (۱) مرحله بازبینی و اصلاح محتوای تحویل داده شده توسط کارفرما.",
                "ماده ۳: تعهدات کارفرما (طرف دوم)" => "۱- ارائه دقیق موضوعات، کلمات کلیدی و دستورالعمل‌های محتوایی. ۲- بررسی محتوای تحویل داده شده و اعلام اصلاحات احتمالی ظرف مدت ۳ روز کاری.",
            ];
            break;

        case 'server_renewal':
        case 'support_renewal':
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "تمدید اشتراک خدمات «" . $serviceTitle . "» برای مدت یک سال شمسی از تاریخ شروع قرارداد.",
                "ماده ۲: تعهدات مجری (طرف اول)" => "۱- تضمین پایداری و در دسترس بودن سرویس در طول دوره قرارداد. ۲- ارائه پشتیبانی فنی در ساعات کاری متعارف جهت رفع مشکلات مرتبط با سرویس.",
                "ماده ۳: تعهدات کارفرما (طرف دوم)" => "پرداخت کامل هزینه تمدید سرویس در ابتدای دوره قرارداد. کارفرما مطلع است که عدم پرداخت به‌موقع ممکن است منجر به قطعی سرویس گردد.",
            ];
            break;

        default:
            $service_specific_clauses = [
                "ماده ۱: موضوع قرارداد" => "ارائه خدمات تحت عنوان «" . $serviceTitle . "» بر اساس توافقات.",
            ];
    }

    // --- بخش دوم: شرایط عمومی و حقوقی مشترک ---
    $common_clauses = [
        "ماده ۴: مبلغ قرارداد و شرایط پرداخت" => $paymentDescription,
        "ماده ۵: شرایط فورس ماژور" => "در صورت بروز حوادث قهریه و غیرمترقبه، تعهدات طرفین تا زمان رفع حالت فورس ماژور متوقف و پس از آن، مدت زمان توقف به مدت قرارداد افزوده خواهد شد.",
        "ماده ۶: حفظ محرمانگی" => "طرفین متعهد می‌گردند کلیه اطلاعاتی را که در حین اجرای این قرارداد از طرف مقابل دریافت می‌دارند، محرمانه تلقی نموده و از افشای آن به اشخاص ثالث خودداری نمایند.",
        "ماده ۷: مالکیت معنوی" => "کلیه حقوق مادی و معنوی ناشی از اجرای موضوع قرارداد پس از تسویه حساب کامل، متعلق به کارفرما (طرف دوم) خواهد بود.",
        "ماده ۸: حل اختلاف" => "در صورت بروز هرگونه اختلاف، طرفین تلاش خواهند نمود تا از طریق مذاکره مسالمت‌آمیز به تفاهم دست یابند. در غیر این صورت، موضوع به مراجع ذی‌صلاح قضایی ارجاع خواهد شد.",
        "ماده ۹: نسخ قرارداد" => "این قرارداد در ۹ ماده و دو نسخه متحدالمتن و با اعتبار واحد تنظیم گردیده و پس از امضای طرفین، برای ایشان لازم‌الاجرا می‌باشد.",
    ];

    return array_merge($service_specific_clauses, $common_clauses);
}

$allClauses = getContractClauses($serviceTypeKey, $serviceTitlePersian, $payment_terms_text);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "قرارداد " . sanitize($contractTitle); ?></title>
    <style>
        /* ... کد CSS مدرن از پاسخ‌های قبلی ... */
        body { font-family: 'Vazirmatn', sans-serif; background-color: #f4f7f6; color: #333; line-height: 1.8; font-size: 14px; margin: 0; }
        .contract-container { max-width: 850px; margin: 40px auto; padding: 40px 50px; background-color: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.07); }
        .contract-header { text-align: center; border-bottom: 2px solid #e0e0e0; padding-bottom: 20px; margin-bottom: 30px; }
        .contract-header h1 { font-size: 26px; color: #1a237e; margin: 0; }
        .section-title { font-size: 18px; font-weight: bold; color: #3949ab; margin-top: 30px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 1px solid #c5cae9; }
        .parties-section { display: flex; justify-content: space-between; gap: 30px; }
        .party-box { flex: 1; background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; }
        .party-box h4 { margin-top: 0; color: #1a237e; }
        .party-box p { margin: 5px 0; font-size: 13px; }
        .clauses-section p { text-align: justify; margin-bottom: 20px; }
        .signature-section { margin-top: 70px; display: flex; justify-content: space-around; text-align: center; padding-top: 30px; border-top: 1px dashed #ccc; }
        .print-button-container { text-align: center; margin-bottom: 20px; }
        .print-button { background-color: #3949ab; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-size: 16px; font-family: 'Vazirmatn'; }
        @media print { body { background-color: #fff; } .contract-container { margin: 0; padding: 0; box-shadow: none; border-radius: 0; } .print-button-container { display: none; } }
    </style>
</head>
<body>
    <div class="contract-container">
        <div class="print-button-container">
            <button class="print-button" onclick="window.print()">چاپ قرارداد</button>
        </div>

        <header class="contract-header">
            <h1>بسمه تعالی</h1>
            <h2>قرارداد <?php echo sanitize($contractTitle); ?></h2>
        </header>

        <section class="parties-section">
            <div class="party-box">
                <h4>طرف اول قرارداد (مجری):</h4>
                <p>شرکت <strong><?php echo sanitize($companyName); ?></strong> به نشانی <?php echo sanitize($companyInfo['address']); ?> و شماره تماس <?php echo sanitize($companyInfo['phone']); ?></p>
            </div>
            <div class="party-box">
                <h4>طرف دوم قرارداد (کارفرما):</h4>
                <p>آقا/خانم/شرکت <strong><?php echo sanitize($customerName); ?></strong> به نشانی <?php echo sanitize($customer->address); ?> و شماره تماس <?php echo sanitize($customer->phone); ?></p>
            </div>
        </section>

        <p style="text-align: justify; margin-top: 20px;">
            این قرارداد بر اساس قوانین جاری جمهوری اسلامی ایران در تاریخ <strong><?php echo $startDate; ?></strong> منعقد گردیده و برای طرفین لازم‌الاجرا می‌باشد. مدت زمان اجرای کامل موضوع قرارداد از تاریخ شروع به مدت <strong><?php echo $endDate === 'نامحدود' ? 'نامحدود' : ('تا تاریخ ' . $endDate); ?></strong> می‌باشد.
        </p>

        <section class="clauses-section">
            <?php foreach ($allClauses as $title => $clause): ?>
                <h3 class="section-title"><?php echo sanitize($title); ?></h3>
                <p><?php echo nl2br(sanitize($clause)); ?></p>
            <?php endforeach; ?>
        </section>
        
        <section class="signature-section">
            <div class="signature-box">
                <span>مهر و امضای طرف اول</span>
                <p>(<?php echo sanitize($companyName); ?>)</p>
            </div>
            <div class="signature-box">
                <span>امضای طرف دوم</span>
                <p>(<?php echo sanitize($customerName); ?>)</p>
            </div>
        </section>
    </div>
</body>
</html>