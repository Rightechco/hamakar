<?php
// app/views/admin/marketing/research_form.php
// این کد با استفاده از Bootstrap 5 و Font Awesome طراحی شده است.
// توجه: این یک فایل View است و پردازش اصلی باید در کنترلر انجام شود.

// لیست شهرهای اصلی برای استخراج
$iranianCities = ['تهران', 'مشهد', 'اصفهان', 'کرج', 'شیراز', 'تبریز', 'اهواز', 'قم', 'کرمانشاه', 'ارومیه', 'رشت', 'زاهدان', 'همدان', 'کرمان', 'یزد', 'سمنان', 'اراک', 'بندرعباس', 'اسلامشهر', 'قزوین', 'سنندج', 'خرم‌آباد', 'گرگان', 'ساری', 'بوشهر', 'بیرجند', 'ایلام', 'شهرکرد', 'یاسوج', 'زنجان', 'بجنورد'];
$iranianProvinces = ['تهران', 'خراسان رضوی', 'اصفهان', 'البرز', 'فارس', 'آذربایجان شرقی', 'خوزستان', 'قم', 'کرمانشاه', 'آذربایجان غربی', 'گیلان', 'سیستان و بلوچستان', 'همدان', 'کرمان', 'یزد', 'سمنان', 'مرکزی', 'هرمزگان', 'قزوین', 'کردستان', 'لرستان', 'گلستان', 'مازندران', 'بوشهر', 'خراسان جنوبی', 'ایلام', 'چهارمحال و بختیاری', 'کهگیلویه و بویراحمد', 'زنجان', 'خراسان شمالی', 'اردبیل'];

// ✅ نگاشت کامل انگلیسی به فارسی برای استان‌ها و مراکز استان
$englishToPersianMapping = [
    'tehran' => 'تهران',
    'mashhad' => 'مشهد',
    'razavi khorasan' => 'خراسان رضوی',
    'isfahan' => 'اصفهان',
    'esfahan' => 'اصفهان',
    'alborz' => 'البرز',
    'karaj' => 'کرج',
    'fars' => 'فارس',
    'shiraz' => 'شیراز',
    'east azerbaijan' => 'آذربایجان شرقی',
    'tabriz' => 'تبریز',
    'khuzestan' => 'خوزستان',
    'ahvaz' => 'اهواز',
    'qom' => 'قم',
    'kermanshah' => 'کرمانشاه',
    'west azerbaijan' => 'آذربایجان غربی',
    'urmia' => 'ارومیه',
    'rasht' => 'رشت',
    'gilan' => 'گیلان',
    'sistan and baluchestan' => 'سیستان و بلوچستان',
    'zahedan' => 'زاهدان',
    'hamadan' => 'همدان',
    'kerman' => 'کرمان',
    'yazd' => 'یزد',
    'semnan' => 'سمنان',
    'arak' => 'اراک',
    'markazi' => 'مرکزی',
    'hormozgan' => 'هرمزگان',
    'bandar abbas' => 'بندرعباس',
    'qazvin' => 'قزوین',
    'sanandaj' => 'سنندج',
    'kurdistan' => 'کردستان',
    'khorramabad' => 'خرم‌آباد',
    'lorestan' => 'لرستان',
    'gorgan' => 'گرگان',
    'golestan' => 'گلستان',
    'sari' => 'ساری',
    'mazandaran' => 'مازندران',
    'bushehr' => 'بوشهر',
    'birjand' => 'بیرجند',
    'south khorasan' => 'خراسان جنوبی',
    'ilam' => 'ایلام',
    'shahrekord' => 'شهرکرد',
    'chaharmahal and bakhtiari' => 'چهارمحال و بختیاری',
    'yasuj' => 'یاسوج',
    'kohgiluyeh and boyer-ahmad' => 'کهگیلویه و بویراحمد',
    'zanjan' => 'زنجان',
    'bojnord' => 'بجنورد',
    'north khorasan' => 'خراسان شمالی',
    'ardabil' => 'اردبیل',
];

function formatPhoneNumber($phone) {
    $phone = preg_replace('/\D/', '', $phone); // حذف کاراکترهای غیرعددی
    if (strlen($phone) == 10 && substr($phone, 0, 1) == '9') {
        $phone = '0' . $phone;
    }
    return $phone;
}

function extractCityProvince($address, $cities, $provinces, $mapping) {
    if (empty($address)) {
        return '---';
    }
    
    $lowerAddress = strtolower($address);
    $foundProvince = null;
    $foundCity = null;

    foreach ($provinces as $province) {
        if (strpos($address, $province) !== false) {
            $foundProvince = $province;
            break;
        }
    }
    foreach ($cities as $city) {
        if (strpos($address, $city) !== false) {
            $foundCity = $city;
            break;
        }
    }
    
    foreach ($mapping as $english => $persian) {
        if (strpos($lowerAddress, $english) !== false) {
            if (in_array($persian, $provinces)) {
                $foundProvince = $persian;
            } elseif (in_array($persian, $cities)) {
                $foundCity = $persian;
            }
        }
    }
    
    if ($foundProvince && $foundCity) {
        return "$foundProvince ، $foundCity";
    } elseif ($foundProvince) {
        return $foundProvince;
    } elseif ($foundCity) {
        return $foundCity;
    }
    
    return $address;
}


function paginate($total_records, $records_per_page, $current_page, $base_url, $tab_param) {
    $total_pages = ceil($total_records / $records_per_page);
    echo '<nav aria-label="Page navigation">';
    echo '<ul class="pagination justify-content-center">';
    $prev_page = max(1, $current_page - 1);
    $next_page = min($total_pages, $current_page + 1);
    
    echo '<li class="page-item ' . ($current_page == 1 ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="' . htmlspecialchars("{$base_url}&{$tab_param}={$prev_page}") . '">قبلی</a>';
    echo '</li>';
    
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">';
        echo '<a class="page-link" href="' . htmlspecialchars("{$base_url}&{$tab_param}={$i}") . '">' . $i . '</a>';
        echo '</li>';
    }
    
    echo '<li class="page-item ' . ($current_page == $total_pages ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="' . htmlspecialchars("{$base_url}&{$tab_param}={$next_page}") . '">بعدی</a>';
    echo '</li>';
    echo '</ul>';
    echo '</nav>';
}

?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تحقیقات بازاریابی و جمع‌آوری لید</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">راهنمای استفاده و دانلود</h6>
        </div>
        <div class="card-body">
            <p><strong>برنامه اسکرپر Google Maps:</strong></p>
            <ul>
                <li><strong>لینک دانلود:</strong>
                    <a href="<?php echo APP_URL; ?>/download/map_scraper.exe">دانلود فایل map_scraper.exe</a>
                </li>
                <li><strong>نحوه استفاده:</strong> برنامه را اجرا کرده، کلمات کلیدی و استان مورد نظر را وارد کنید و دکمه "شروع" را بزنید. اطلاعات در فایل اکسل و دیتابیس شما ذخیره می‌شوند.</li>
            </ul>
            <p class="mt-3"><strong>برنامه اسکرپر Niazrooz:</strong></p>
            <ul>
                <li><strong>لینک دانلود:</strong>
                    <a href="<?php echo APP_URL; ?>/download/niazrooz_scraper.exe">دانلود فایل niazrooz_scraper.exe</a>
                </li>
                <li><strong>نحوه استفاده:</strong> برنامه را اجرا کرده، کلمه کلیدی را وارد کنید. در صورت مشاهده کپچا، آن را به صورت دستی حل کرده و سپس فرآیند ادامه می‌یابد.</li>
            </ul>
            <p class="mt-3"><strong>برنامه اسکرپر Neshan:</strong></p>
            <ul>
                <li><strong>لینک دانلود:</strong>
                    <a href="<?php echo APP_URL; ?>/download/neshan_scraper.exe">دانلود فایل neshan_scraper.exe</a>
                </li>
                <li><strong>نحوه استفاده:</strong> برنامه را اجرا کرده و از لیست‌های کشویی، استان، شهر و دسته‌بندی مورد نظر خود را انتخاب کنید و دکمه "شروع" را بزنید. اطلاعات به‌صورت خودکار در فایل اکسل و دیتابیس شما ذخیره می‌شوند.</li>
            </ul>
        </div>
    </div>
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="google-tab" data-bs-toggle="tab" data-bs-target="#google-pane" type="button" role="tab" aria-controls="google-pane" aria-selected="true">
                اطلاعات Google Maps
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="niazrooz-tab" data-bs-toggle="tab" data-bs-target="#niazrooz-pane" type="button" role="tab" aria-controls="niazrooz-pane" aria-selected="false">
                اطلاعات Niazrooz
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="neshan-tab" data-bs-toggle="tab" data-bs-target="#neshan-pane" type="button" role="tab" aria-controls="neshan-pane" aria-selected="false">
                اطلاعات نشان
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <div class="tab-pane fade show active" id="google-pane" role="tabpanel" aria-labelledby="google-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نتایج Google Maps</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="google_filter_province" class="form-label">فیلتر استان</label>
                            <select class="form-select" id="google_filter_province">
                                <option value="">همه استان‌ها</option>
                                <?php foreach ($provinces as $province): ?>
                                    <option value="<?php echo htmlspecialchars($province); ?>">
                                        <?php echo htmlspecialchars($province); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="google_filter_keyword" class="form-label">فیلتر کلمه کلیدی</label>
                            <input type="text" class="form-control" id="google_filter_keyword" placeholder="مثال: کافه">
                        </div>
                        <div class="col-md-4">
                            <label for="google_filter_category" class="form-label">فیلتر نوع کسب‌وکار</label>
                            <input type="text" class="form-control" id="google_filter_category" placeholder="مثال: رستوران">
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="googleDataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>نام کسب‌وکار</th>
                                    <th>کلمه کلیدی</th>
                                    <th>تلفن</th>
                                    <th>ایمیل</th>
                                    <th>آدرس</th>
                                    <th style="width: 1%;">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($maps_leads)): ?>
                                    <?php foreach ($maps_leads as $lead): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($lead->business_name ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars($lead->keyword ?? '---'); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars(formatPhoneNumber(str_replace(',', '،', $lead->phone ?? '')))); ?></td>
                                            <td><?php echo htmlspecialchars($lead->email ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars(extractCityProvince($lead->address ?? '', $iranianCities, $iranianProvinces, $englishToPersianMapping)); ?></td>
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center align-items-center gap-1">
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="btn btn-info btn-sm" title="ارسال پیامک">
                                                        <i class="fas fa-sms"></i>
                                                    </a>
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="btn btn-success btn-sm" title="تماس">
                                                        <i class="fas fa-phone"></i>
                                                    </a>
                                                    <a href="mailto:<?php echo htmlspecialchars($lead->email ?? ''); ?>" class="btn btn-warning btn-sm" title="ارسال ایمیل">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    <a href="<?php echo htmlspecialchars($lead->website ?? ''); ?>" target="_blank" class="btn btn-primary btn-sm" title="مشاهده وبسایت">
                                                        <i class="fas fa-globe"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-around mt-2">
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editMapLead&id=<?php echo htmlspecialchars($lead->id ?? ''); ?>" class="btn btn-secondary btn-sm" title="ویرایش">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteMapLead&id=<?php echo htmlspecialchars($lead->id ?? ''); ?>" onclick="return confirm('آیا مطمئن هستید؟');" class="btn btn-danger btn-sm" title="حذف">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php 
                            $base_url_google = APP_URL . '/index.php?page=admin&action=showMarketingForm&niaz_page=' . ($niazerooz_current_page ?? 1);
                            paginate(
                                $maps_total_records, 
                                $records_per_page, 
                                $maps_current_page, 
                                $base_url_google,
                                'maps_page'
                            ); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="niazrooz-pane" role="tabpanel" aria-labelledby="niazrooz-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نتایج Niazrooz</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="niazrooz_filter_title" class="form-label">فیلتر عنوان آگهی</label>
                            <input type="text" class="form-control" id="niazrooz_filter_title" placeholder="مثال: طراحی وب">
                        </div>
                        <div class="col-md-6">
                            <label for="niazrooz_filter_owner" class="form-label">فیلتر نام صاحب آگهی</label>
                            <input type="text" class="form-control" id="niazrooz_filter_owner" placeholder="مثال: علی">
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="niazroozDataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>شناسه آگهی</th>
                                    <th>عنوان آگهی</th>
                                    <th>نام صاحب آگهی</th>
                                    <th>تلفن</th>
                                    <th>ایمیل</th>
                                    <th style="width: 1%;">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($niazerooz_leads)): ?>
                                    <?php foreach ($niazerooz_leads as $lead): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($lead->ad_id ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars($lead->ad_title ?? '---'); ?></td>
                                            <td>
                                                <?php 
                                                    if (!empty($lead->owner_name)) {
                                                        echo htmlspecialchars($lead->owner_name);
                                                    } else {
                                                        echo '---';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if (!empty($lead->phones)) {
                                                        $phone_numbers = explode('،', str_replace(',', '،', $lead->phones));
                                                        foreach ($phone_numbers as $phone) {
                                                            echo htmlspecialchars(formatPhoneNumber(trim($phone))) . '<br>';
                                                        }
                                                    } else {
                                                        echo '---';
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($lead->email ?? '---'); ?></td>
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center align-items-center gap-1">
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phones ?? ''); ?>" class="btn btn-info btn-sm" title="ارسال پیامک">
                                                        <i class="fas fa-sms"></i>
                                                    </a>
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phones ?? ''); ?>" class="btn btn-success btn-sm" title="تماس">
                                                        <i class="fas fa-phone"></i>
                                                    </a>
                                                    <a href="https://wa.me/<?php echo htmlspecialchars($lead->whatsapp ?? ''); ?>" target="_blank" class="btn btn-success btn-sm" title="ارسال واتساپ">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                    <a href="mailto:<?php echo htmlspecialchars($lead->email ?? ''); ?>" class="btn btn-warning btn-sm" title="ارسال ایمیل">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editNiazroozLead&id=<?php echo htmlspecialchars($lead->id ?? ''); ?>" class="btn btn-secondary btn-sm" title="ویرایش">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteNiazroozLead&id=<?php echo htmlspecialchars($lead->id ?? ''); ?>" onclick="return confirm('آیا مطمئن هستید؟');" class="btn btn-danger btn-sm" title="حذف">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php 
                            $base_url_niaz = APP_URL . '/index.php?page=admin&action=showMarketingForm&maps_page=' . ($maps_current_page ?? 1);
                            paginate(
                                $niazerooz_total_records, 
                                $records_per_page, 
                                $niazerooz_current_page, 
                                $base_url_niaz,
                                'niaz_page'
                            ); 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="neshan-pane" role="tabpanel" aria-labelledby="neshan-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">راهنمای استفاده و دانلود اسکرپر نشان</h6>
                </div>
                <div class="card-body">
                    <p><strong>برنامه اسکرپر Neshan:</strong></p>
                    <ul>
                        <li><strong>لینک دانلود:</strong>
                            <a href="<?php echo APP_URL; ?>/download/neshan_scraper.exe">دانلود فایل neshan_scraper.exe</a>
                        </li>
                        <li><strong>نحوه استفاده:</strong> برنامه را اجرا کرده و از لیست‌های کشویی، استان، شهر و دسته‌بندی مورد نظر خود را انتخاب کنید و دکمه "شروع" را بزنید. اطلاعات به‌صورت خودکار در فایل اکسل و دیتابیس شما ذخیره می‌شوند.</li>
                    </ul>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نتایج نشان</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="neshan_filter_title" class="form-label">فیلتر نام</label>
                            <input type="text" class="form-control" id="neshan_filter_title" placeholder="مثال: کافه">
                        </div>
                        <div class="col-md-4">
                            <label for="neshan_filter_address" class="form-label">فیلتر آدرس</label>
                            <input type="text" class="form-control" id="neshan_filter_address" placeholder="مثال: تهران">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="neshanDataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>دسته‌بندی</th>
                                    <th>تلفن</th>
                                    <th>ایمیل</th>
                                    <th>وبسایت</th>
                                    <th>آدرس</th>
                                    <th style="width: 1%;">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($neshan_leads)): ?>
                                    <?php foreach ($neshan_leads as $lead): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($lead->business_name ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars($lead->category ?? '---'); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars(formatPhoneNumber(str_replace(',', '،', $lead->phone ?? '')))); ?></td>
                                            <td><?php echo htmlspecialchars($lead->email ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars($lead->website ?? '---'); ?></td>
                                            <td><?php echo htmlspecialchars(extractCityProvince($lead->address ?? '', $iranianCities, $iranianProvinces, $englishToPersianMapping)); ?></td>
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center align-items-center gap-1">
                                                    <a href="sms:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="btn btn-info btn-sm" title="ارسال پیامک">
                                                        <i class="fas fa-sms"></i>
                                                    </a>
                                                    <a href="tel:<?php echo htmlspecialchars($lead->phone ?? ''); ?>" class="btn btn-success btn-sm" title="تماس">
                                                        <i class="fas fa-phone"></i>
                                                    </a>
                                                    <a href="mailto:<?php echo htmlspecialchars($lead->email ?? ''); ?>" class="btn btn-warning btn-sm" title="ارسال ایمیل">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editNeshanLead&id=<?php echo htmlspecialchars($lead->id ?? ''); ?>" class="btn btn-secondary btn-sm" title="ویرایش">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteNeshanLead&id=<?php echo htmlspecialchars($lead->id ?? ''); ?>" onclick="return confirm('آیا مطمئن هستید؟');" class="btn btn-danger btn-sm" title="حذف">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php 
                            $base_url_neshan = APP_URL . '/index.php?page=admin&action=showMarketingForm&maps_page=' . ($maps_current_page ?? 1) . '&niaz_page=' . ($niazerooz_current_page ?? 1);
                            paginate(
                                $neshan_total_records, 
                                $records_per_page, 
                                $neshan_current_page, 
                                $base_url_neshan,
                                'neshan_page'
                            ); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>