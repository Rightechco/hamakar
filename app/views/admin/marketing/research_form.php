<?php
// app/views/admin/marketing/research_form.php

// توابع کمکی
if (!function_exists('jalali_date')) {
    function jalali_date($date) {
        if (empty($date) || $date == '0000-00-00 00:00:00') return '-';
        if (class_exists('IntlDateFormatter')) {
            try {
                $timestamp = strtotime($date);
                $formatter = new IntlDateFormatter("fa_IR@calendar=persian", IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Asia/Tehran', IntlDateFormatter::TRADITIONAL, "yyyy/MM/dd");
                return $formatter->format($timestamp);
            } catch (Exception $e) {}
        }
        return date('Y-m-d', strtotime($date));
    }
}

if (!function_exists('paginate')) {
    function paginate($total_records, $records_per_page, $current_page, $base_url, $page_param) {
        $total_pages = ceil($total_records / $records_per_page);
        if ($total_pages <= 1) return;
        echo '<nav aria-label="Page navigation" class="mt-4"><ul class="pagination justify-content-center">';
        $prev_class = ($current_page <= 1) ? 'disabled' : '';
        $prev_url = ($current_page > 1) ? $base_url . ($current_page - 1) : '#';
        echo '<li class="page-item ' . $prev_class . '"><a class="page-link" href="' . $prev_url . '">قبلی</a></li>';
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . $base_url . '1">1</a></li>';
            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $current_page) ? 'active' : '';
            echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $base_url . $i . '">' . $i . '</a></li>';
        }
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            echo '<li class="page-item"><a class="page-link" href="' . $base_url . $total_pages . '">' . $total_pages . '</a></li>';
        }
        $next_class = ($current_page >= $total_pages) ? 'disabled' : '';
        $next_url = ($current_page < $total_pages) ? $base_url . ($current_page + 1) : '#';
        echo '<li class="page-item ' . $next_class . '"><a class="page-link" href="' . $next_url . '">بعدی</a></li>';
        echo '</ul></nav>';
    }
}

$city_translations = [
    'Tehran' => 'تهران', 'Mashhad' => 'مشهد', 'Isfahan' => 'اصفهان', 'Karaj' => 'کرج',
    'Shiraz' => 'شیراز', 'Tabriz' => 'تبریز', 'Ahvaz' => 'اهواز', 'Qom' => 'قم',
    'Kermanshah' => 'کرمانشاه', 'Urmia' => 'ارومیه', 'Rasht' => 'رشت', 'Zahedan' => 'زاهدان',
    'Hamadan' => 'همدان', 'Kerman' => 'کرمان', 'Yazd' => 'یزد', 'Ardabil' => 'اردبیل',
    'Bandar Abbas' => 'بندرعباس', 'Arak' => 'اراک', 'Islamshahr' => 'اسلامشهر', 'Zanjan' => 'زنجان',
    'Sanandaj' => 'سنندج', 'Qazvin' => 'قزوین', 'Khorramabad' => 'خرم‌آباد', 'Gorgan' => 'گرگان',
    'Sari' => 'ساری', 'Shahriar' => 'شهریار', 'Qods' => 'قدس', 'Kashan' => 'کاشان',
    'Dezful' => 'دزفول', 'Babol' => 'بابل', 'Amol' => 'آمل', 'Nishapur' => 'نیشابور',
    'Saveh' => 'ساوه', 'Varamin' => 'ورامین', 'Qarchak' => 'قرچک', 'Bushehr' => 'بوشهر'
];
?>

<style>
    .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s ease-in-out; border: none; }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .btn-sms { background-color: #E3F2FD; color: #1976D2; } .btn-sms:hover { background-color: #1976D2; color: #fff; }
    .btn-call { background-color: #E8F5E9; color: #2E7D32; } .btn-call:hover { background-color: #2E7D32; color: #fff; }
    .btn-wa { background-color: #DCF8C6; color: #075E54; } .btn-wa:hover { background-color: #25D366; color: #fff; }
    .btn-mail { background-color: #FFF3E0; color: #EF6C00; } .btn-mail:hover { background-color: #EF6C00; color: #fff; }
    .btn-web { background-color: #F3E5F5; color: #7B1FA2; } .btn-web:hover { background-color: #7B1FA2; color: #fff; }
    .btn-edit { background-color: #ECEFF1; color: #455A64; } .btn-edit:hover { background-color: #455A64; color: #fff; }
    .btn-del { background-color: #FFEBEE; color: #C62828; } .btn-del:hover { background-color: #C62828; color: #fff; }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تحقیقات بازاریابی و جمع‌آوری لید</h1>
    </div>

    <div class="card shadow mb-4 border-start-primary border-0 border-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">راهنمای استفاده و دانلود</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <strong class="text-primary"><i class="fab fa-google me-1"></i> Google Maps Scraper:</strong>
                        <ul class="mt-2 mb-0 small text-muted">
                            <li><a href="<?php echo APP_URL; ?>/download/map_scraper.exe" class="text-decoration-none fw-bold">دانلود map_scraper.exe</a></li>
                            <li>اجرا کنید، کلمه کلیدی و استان را وارد کرده و شروع کنید.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <strong class="text-info"><i class="fas fa-newspaper me-1"></i> Niazrooz Scraper:</strong>
                        <ul class="mt-2 mb-0 small text-muted">
                            <li><a href="<?php echo APP_URL; ?>/download/niazrooz_scraper.exe" class="text-decoration-none fw-bold">دانلود niazrooz_scraper.exe</a></li>
                            <li>کلمه کلیدی را وارد کنید. کپچا را دستی حل کنید.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <strong class="text-success"><i class="fas fa-map-pin me-1"></i> Neshan Scraper:</strong>
                        <ul class="mt-2 mb-0 small text-muted">
                            <li><a href="<?php echo APP_URL; ?>/download/neshan_scraper.exe" class="text-decoration-none fw-bold">دانلود neshan_scraper.exe</a></li>
                            <li>استان، شهر و دسته را انتخاب و شروع کنید.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <strong class="text-danger"><i class="fas fa-cubes me-1"></i> Divar Scraper:</strong>
                        <ul class="mt-2 mb-0 small text-muted">
                            <li><a href="<?php echo APP_URL; ?>/download/divar_scraper.exe" class="text-decoration-none fw-bold">دانلود divar_scraper.exe</a></li>
                            <li>دسته‌بندی و شهر را انتخاب کنید.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <strong class="text-warning text-dark"><i class="fas fa-bullhorn me-1"></i> Sheypoor Scraper:</strong>
                        <ul class="mt-2 mb-0 small text-muted">
                            <li><a href="<?php echo APP_URL; ?>/download/sheypoor_scraper.exe" class="text-decoration-none fw-bold">دانلود sheypoor_scraper.exe</a></li>
                            <li>جستجو کنید و داده‌ها را استخراج نمایید.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active fw-bold" id="google-tab" data-bs-toggle="tab" data-bs-target="#google-pane" type="button" role="tab"><i class="fab fa-google text-primary me-1"></i> Google Maps <span class="badge bg-secondary rounded-pill ms-1"><?php echo number_format($maps_total_records ?? 0); ?></span></button></li>
        <li class="nav-item" role="presentation"><button class="nav-link fw-bold" id="niazrooz-tab" data-bs-toggle="tab" data-bs-target="#niazrooz-pane" type="button" role="tab"><i class="fas fa-newspaper text-info me-1"></i> Niazrooz <span class="badge bg-secondary rounded-pill ms-1"><?php echo number_format($niazerooz_total_records ?? 0); ?></span></button></li>
        <li class="nav-item" role="presentation"><button class="nav-link fw-bold" id="neshan-tab" data-bs-toggle="tab" data-bs-target="#neshan-pane" type="button" role="tab"><i class="fas fa-map-pin text-success me-1"></i> Neshan <span class="badge bg-secondary rounded-pill ms-1"><?php echo number_format($neshan_total_records ?? 0); ?></span></button></li>
        <li class="nav-item" role="presentation"><button class="nav-link fw-bold" id="divar-tab" data-bs-toggle="tab" data-bs-target="#divar-pane" type="button" role="tab"><i class="fas fa-cubes text-danger me-1"></i> Divar <span class="badge bg-secondary rounded-pill ms-1"><?php echo number_format($divar_total_records ?? 0); ?></span></button></li>
        <li class="nav-item" role="presentation"><button class="nav-link fw-bold" id="sheypoor-tab" data-bs-toggle="tab" data-bs-target="#sheypoor-pane" type="button" role="tab"><i class="fas fa-bullhorn text-warning me-1"></i> Sheypoor <span class="badge bg-secondary rounded-pill ms-1"><?php echo number_format($sheypoor_total_records ?? 0); ?></span></button></li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <div class="tab-pane fade show active" id="google-pane" role="tabpanel" aria-labelledby="google-tab">
            <div class="card shadow mb-4 border-top-0 rounded-0 rounded-bottom">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="googleDataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr><th>نام کسب‌وکار</th><th>دسته‌بندی</th><th>شهر</th><th>تلفن</th><th>آدرس</th><th>عملیات</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($maps_leads)): ?>
                                    <?php foreach ($maps_leads as $lead): ?>
                                        <?php 
                                            $display_city = $lead->city ?? '';
                                            $address = $lead->address ?? '';
                                            if (empty($display_city) && !empty($address)) {
                                                foreach ($city_translations as $en => $fa) {
                                                    if (stripos($address, $en) !== false || stripos($address, $fa) !== false) {
                                                        $display_city = $fa; break;
                                                    }
                                                }
                                            }
                                            if (empty($display_city)) $display_city = 'نامشخص';
                                            $phone = str_replace('Numéro de téléphone:', '', $lead->phone ?? '');
                                            $phone = trim($phone);
                                            if (empty($phone)) $phone = '---';
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($lead->business_name ?? '---'); ?></td>
                                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($lead->keyword ?? '---'); ?></span></td>
                                            <td class="text-primary fw-bold"><?php echo htmlspecialchars($display_city); ?></td>
                                            <td dir="ltr" class="text-end fw-bold"><?php echo htmlspecialchars($phone); ?></td>
                                            <td class="small text-muted text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($address); ?>"><?php echo htmlspecialchars($address); ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="tel:<?php echo htmlspecialchars($phone); ?>" class="action-btn btn-call" title="تماس"><i class="fas fa-phone"></i></a>
                                                    <a href="sms:<?php echo htmlspecialchars($phone); ?>" class="action-btn btn-sms" title="پیامک"><i class="fas fa-sms"></i></a>
                                                    <?php if (!empty($lead->website)): ?><a href="<?php echo htmlspecialchars($lead->website); ?>" target="_blank" class="action-btn btn-web" title="وبسایت"><i class="fas fa-globe"></i></a><?php endif; ?>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteMapLead&id=<?php echo $lead->id; ?>" onclick="return confirm('حذف شود؟');" class="action-btn btn-del" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?><tr><td colspan="6" class="text-center py-4 text-muted">داده‌ای یافت نشد.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                        <?php $base_url = APP_URL . '/index.php?page=admin&action=showMarketingForm&niaz_page='.($niazerooz_current_page??1).'&neshan_page='.($neshan_current_page??1).'&divar_page='.($divar_current_page??1).'&sheypoor_page='.($sheypoor_current_page??1); paginate($maps_total_records, $records_per_page, $maps_current_page, $base_url . '&maps_page=', 'maps_page'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="niazrooz-pane" role="tabpanel" aria-labelledby="niazrooz-tab">
            <div class="card shadow mb-4 border-top-0 rounded-0 rounded-bottom">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="niazroozDataTable" width="100%" cellspacing="0">
                            <thead class="table-light"><tr><th>عنوان</th><th>صاحب آگهی</th><th>تلفن</th><th>ایمیل</th><th>عملیات</th></tr></thead>
                            <tbody>
                                <?php if (!empty($niazerooz_leads)): ?>
                                    <?php foreach ($niazerooz_leads as $lead): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($lead->ad_title ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars($lead->owner_name ?? '---'); ?></td>
                                            <td dir="ltr" class="text-end fw-bold"><?php echo htmlspecialchars($lead->phones ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars($lead->email ?? '---'); ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phones ?? ''); ?>" class="action-btn btn-call" title="تماس"><i class="fas fa-phone"></i></a>
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phones ?? ''); ?>" class="action-btn btn-sms" title="پیامک"><i class="fas fa-sms"></i></a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteNiazroozLead&id=<?php echo $lead->id; ?>" onclick="return confirm('حذف شود؟');" class="action-btn btn-del" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?><tr><td colspan="5" class="text-center py-4 text-muted">داده‌ای یافت نشد.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                        <?php $base_url = APP_URL . '/index.php?page=admin&action=showMarketingForm&maps_page='.($maps_current_page??1).'&neshan_page='.($neshan_current_page??1).'&divar_page='.($divar_current_page??1).'&sheypoor_page='.($sheypoor_current_page??1); paginate($niazerooz_total_records, $records_per_page, $niazerooz_current_page, $base_url . '&niaz_page=', 'niaz_page'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="neshan-pane" role="tabpanel" aria-labelledby="neshan-tab">
            <div class="card shadow mb-4 border-top-0 rounded-0 rounded-bottom">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="neshanDataTable" width="100%" cellspacing="0">
                            <thead class="table-light"><tr><th>نام</th><th>دسته‌بندی</th><th>تلفن</th><th>آدرس</th><th>عملیات</th></tr></thead>
                            <tbody>
                                <?php if (!empty($neshan_leads)): ?>
                                    <?php foreach ($neshan_leads as $lead): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($lead->business_name ?? '---'); ?></td>
                                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($lead->category ?? '---'); ?></span></td>
                                            <td dir="ltr" class="text-end fw-bold"><?php echo htmlspecialchars($lead->phone ?? '---'); ?></td>
                                            <td class="small text-muted text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($lead->address ?? ''); ?>"><?php echo htmlspecialchars($lead->address ?? ''); ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="action-btn btn-call" title="تماس"><i class="fas fa-phone"></i></a>
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="action-btn btn-sms" title="پیامک"><i class="fas fa-sms"></i></a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteNeshanLead&id=<?php echo $lead->id; ?>" onclick="return confirm('حذف شود؟');" class="action-btn btn-del" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?><tr><td colspan="5" class="text-center py-4 text-muted">داده‌ای یافت نشد.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                        <?php $base_url = APP_URL . '/index.php?page=admin&action=showMarketingForm&maps_page='.($maps_current_page??1).'&niaz_page='.($niazerooz_current_page??1).'&divar_page='.($divar_current_page??1).'&sheypoor_page='.($sheypoor_current_page??1); paginate($neshan_total_records, $records_per_page, $neshan_current_page, $base_url . '&neshan_page=', 'neshan_page'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="divar-pane" role="tabpanel" aria-labelledby="divar-tab">
            <div class="card shadow mb-4 border-top-0 rounded-0 rounded-bottom">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light"><tr><th>عنوان</th><th>استان / شهر / محله</th><th>تلفن</th><th>لینک</th><th>عملیات</th></tr></thead>
                            <tbody>
                                <?php if (!empty($divar_leads)): ?>
                                    <?php foreach ($divar_leads as $lead): ?>
                                        <tr>
                                            <td class="fw-bold text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($lead->title ?? ''); ?>"><?php echo htmlspecialchars($lead->title ?? '---'); ?></td>
                                            <td class="text-primary fw-bold">
                                                <?php 
                                                    $loc = [];
                                                    if (!empty($lead->province)) $loc[] = $lead->province;
                                                    if (!empty($lead->city)) $loc[] = $lead->city;
                                                    if (!empty($lead->district)) $loc[] = $lead->district;
                                                    echo implode(' - ', $loc);
                                                ?>
                                            </td>
                                            <td dir="ltr" class="text-end fw-bold"><?php echo htmlspecialchars($lead->phone ?? '---'); ?></td>
                                            <td><a href="<?php echo htmlspecialchars($lead->ad_link ?? '#'); ?>" target="_blank" class="action-btn btn-web" title="مشاهده"><i class="fas fa-external-link-alt"></i></a></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="action-btn btn-call" title="تماس"><i class="fas fa-phone"></i></a>
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="action-btn btn-sms" title="پیامک"><i class="fas fa-sms"></i></a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteDivarLead&id=<?php echo $lead->id; ?>" onclick="return confirm('حذف شود؟');" class="action-btn btn-del" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?><tr><td colspan="5" class="text-center py-4 text-muted">داده‌ای یافت نشد.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                        <?php $base_url = APP_URL . '/index.php?page=admin&action=showMarketingForm&maps_page='.($maps_current_page??1).'&niaz_page='.($niazerooz_current_page??1).'&neshan_page='.($neshan_current_page??1).'&sheypoor_page='.($sheypoor_current_page??1); paginate($divar_total_records, $records_per_page, $divar_current_page, $base_url . '&divar_page=', 'divar_page'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="sheypoor-pane" role="tabpanel" aria-labelledby="sheypoor-tab">
            <div class="card shadow mb-4 border-top-0 rounded-0 rounded-bottom">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light"><tr><th>عنوان</th><th>استان / شهر / محله</th><th>قیمت</th><th>تلفن</th><th>لینک</th><th>عملیات</th></tr></thead>
                            <tbody>
                                <?php if (!empty($sheypoor_leads)): ?>
                                    <?php foreach ($sheypoor_leads as $lead): ?>
                                        <tr>
                                            <td class="fw-bold text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($lead->title ?? ''); ?>"><?php echo htmlspecialchars($lead->title ?? '---'); ?></td>
                                            <td class="text-primary fw-bold">
                                                <?php 
                                                    $loc = [];
                                                    if (!empty($lead->province)) $loc[] = $lead->province;
                                                    if (!empty($lead->city)) $loc[] = $lead->city;
                                                    if (!empty($lead->district)) $loc[] = $lead->district;
                                                    echo implode(' - ', $loc);
                                                ?>
                                            </td>
                                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($lead->price ?? '---'); ?></span></td>
                                            <td dir="ltr" class="text-end fw-bold text-primary"><?php echo htmlspecialchars($lead->phone ?? '---'); ?></td>
                                            <td><a href="<?php echo htmlspecialchars($lead->ad_link ?? '#'); ?>" target="_blank" class="action-btn btn-web" title="مشاهده"><i class="fas fa-external-link-alt"></i></a></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="action-btn btn-call" title="تماس"><i class="fas fa-phone"></i></a>
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="action-btn btn-sms" title="پیامک"><i class="fas fa-sms"></i></a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteSheypoorLead&id=<?php echo $lead->id; ?>" onclick="return confirm('حذف شود؟');" class="action-btn btn-del" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?><tr><td colspan="6" class="text-center py-4 text-muted">داده‌ای یافت نشد.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                        <?php $base_url = APP_URL . '/index.php?page=admin&action=showMarketingForm&maps_page='.($maps_current_page??1).'&niaz_page='.($niazerooz_current_page??1).'&neshan_page='.($neshan_current_page??1).'&divar_page='.($divar_current_page??1); paginate($sheypoor_total_records, $records_per_page, $sheypoor_current_page, $base_url . '&sheypoor_page=', 'sheypoor_page'); ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>