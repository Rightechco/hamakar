<?php
// app/views/admin/contracts/create_edit.php
// نسخه کامل و نهایی فرم مدیریت و صدور قرارداد

global $categories;
$isEdit = isset($contract) && $contract !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=contracts_update&id=' . $contract->id : APP_URL . '/index.php?page=admin&action=contracts_store';
$buttonText = $isEdit ? 'ذخیره و بروزرسانی قرارداد' : 'ثبت و صدور نهایی قرارداد';
$pageTitle = $isEdit ? 'ویرایش تخصصی سند قرارداد' : 'صدور قرارداد جدید و تنظیم مفاد';

$existingClauses = $clauses ?? [];

// استخراج دقیق تاریخ‌های شمسی جهت درج ثابت در فیلدها هنگام ویرایش
$startDateJalali = ($isEdit && !empty($contract->start_date)) ? jdate('Y/m/d', strtotime($contract->start_date)) : '';
$endDateJalali = ($isEdit && !empty($contract->end_date)) ? jdate('Y/m/d', strtotime($contract->end_date)) : '';
$finalPaymentDueDateJalali = ($isEdit && !empty($contract->final_payment_due_date)) ? jdate('Y/m/d', strtotime($contract->final_payment_due_date)) : '';

if (!isset($serviceTypes) || empty($serviceTypes)) {
    $serviceTypes = [
        'wordpress_website_design' => 'طراحی سایت وردپرسی',
        'dedicated_website_design' => 'طراحی سایت اختصاصی',
        'branding_marketing'        => 'برندینگ و بازاریابی',
        'social_media_management'  => 'مدیریت شبکه‌های اجتماعی',
        'seo_services'             => 'خدمات سئو و بهینه‌سازی',
        'content_production'       => 'خدمات تولید محتوا',
        'server_renewal'           => 'تمدید سرور و هاستینگ', 
        'support_renewal'          => 'تمدید پشتیبانی سالانه', 
    ];
}
?>

<!-- کتابخانه‌های تقویم شمسی -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css"/>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet"/>

<style>
    .contract-builder-wrapper {
        font-family: 'Vazirmatn', sans-serif !important;
        color: #0f172a;
    }
    .fin-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 1.75rem;
    }
    .fin-card-header {
        padding: 1.25rem 1.75rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .fin-card-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.05rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .fin-card-title i { color: #4338ca; font-size: 1.15rem; }
    .form-label-custom {
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
        display: block;
    }
    .form-control-premium, .form-select-premium {
        border-radius: 12px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0.7rem 1.1rem !important;
        font-size: 0.88rem !important;
        background-color: #ffffff;
        color: #0f172a !important;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #4338ca !important;
        box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.12) !important;
    }
    .input-group-text-custom {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-left: none;
        border-radius: 0 12px 12px 0 !important;
        color: #64748b;
        padding: 0.7rem 1rem;
    }
    .datepicker-input { border-radius: 12px 0 0 12px !important; }
    .clause-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 18px;
        position: relative;
    }
    .clause-badge {
        background: #e0e7ff;
        color: #3730a3;
        font-weight: 800;
        font-size: 0.8rem;
        padding: 0.35rem 0.8rem;
        border-radius: 8px;
    }
    .btn-fin-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 14px;
        padding: 0.85rem 2.2rem;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
        cursor: pointer;
    }
    .btn-fin-secondary {
        background: #f1f5f9;
        color: #475569 !important;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 0.85rem 1.8rem;
        font-weight: 700;
        text-decoration: none;
    }
    .p-datepicker {
        font-family: 'Vazirmatn', sans-serif !important;
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
    }
</style>

<div class="contract-builder-wrapper">
    <!-- هدر -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1"><?php echo $pageTitle; ?></h1>
            <p class="text-muted small mb-0">تنظیم مشخصات طرفین، مواد حقوقی، مالیات سفارشی و انتخابگر تاریخ شمسی</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts" class="btn btn-fin-secondary d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت به لیست</span>
        </a>
    </div>

    <form action="<?php echo $formAction; ?>" method="POST" id="contractForm">
        
        <!-- ۱. مشخصات عمومی -->
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title"><i class="fas fa-file-contract"></i> اطلاعات عمومی و طرفین قرارداد</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-custom">کارفرما (طرف دوم):</label>
                        <select class="form-select form-select-premium" name="client_id" required>
                            <option value="">-- انتخاب کارفرما --</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client->id; ?>" <?php echo ($isEdit && $contract->client_id == $client->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client->name); ?> (<?php echo htmlspecialchars($client->email); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">عنوان قرارداد:</label>
                        <input type="text" class="form-control form-control-premium" name="title" value="<?php echo $isEdit ? htmlspecialchars($contract->title) : ''; ?>" placeholder="مثال: قرارداد جامع برندینگ و بازاریابی" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">نوع خدمات موضوع قرارداد:</label>
                        <select class="form-select form-select-premium" name="service_type" id="service_type" onchange="loadDefaultTemplates(this.value)" required>
                            <option value="">-- انتخاب نوع خدمت --</option>
                            <?php foreach ($serviceTypes as $key => $val): ?>
                                <option value="<?php echo $key; ?>" <?php echo ($isEdit && $contract->service_type == $key) ? 'selected' : ''; ?>>
                                    <?php echo $val; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">وضعیت اجرایی قرارداد:</label>
                        <select class="form-select form-select-premium" name="status" required>
                            <option value="pending" <?php echo ($isEdit && $contract->status == 'pending') ? 'selected' : ''; ?>>در انتظار (Pending)</option>
                            <option value="active" <?php echo ($isEdit && $contract->status == 'active') ? 'selected' : ''; ?>>فعال (Active)</option>
                            <option value="completed" <?php echo ($isEdit && $contract->status == 'completed') ? 'selected' : ''; ?>>تکمیل شده (Completed)</option>
                            <option value="canceled" <?php echo ($isEdit && $contract->status == 'canceled') ? 'selected' : ''; ?>>لغو شده (Canceled)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ۲. ویرایشگر مواد حقوقی -->
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title"><i class="fas fa-balance-scale"></i> مواد و بنود حقوقی قرارداد (قابل ویرایش)</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-indigo rounded-3 px-3" style="border: 1px solid #c7d2fe; color: #3730a3;" onclick="triggerTemplateReload()">
                        <i class="fas fa-sync-alt me-1"></i> بارگذاری الگو
                    </button>
                    <button type="button" class="btn btn-sm btn-indigo text-white rounded-3 px-3" style="background:#3730a3;" onclick="addClauseRow()">
                        <i class="fas fa-plus me-1"></i> افزودن ماده جدید
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <div id="clausesContainer">
                    <?php if (!empty($existingClauses)): ?>
                        <?php foreach ($existingClauses as $index => $clause): ?>
                            <div class="clause-row" id="clause-row-<?php echo $index; ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="clause-badge">ماده <?php echo $index + 1; ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeClauseRow('clause-row-<?php echo $index; ?>')">
                                        <i class="fas fa-trash-alt me-1"></i> حذف ماده
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-premium fw-bold mb-2" name="clauses[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($clause->title); ?>" placeholder="عنوان ماده" required>
                                </div>
                                <div>
                                    <textarea class="form-control form-control-premium" name="clauses[<?php echo $index; ?>][content]" rows="3" placeholder="متن کامل ماده حقوقی..." required><?php echo htmlspecialchars($clause->content); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ۳. شرایط مالی، درصد مالیات سفارشی و انتخابگر تاریخ‌های شمسی -->
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title"><i class="fas fa-coins"></i> شرایط مالی و زمان‌بندی (انتخابگر تاریخ شمسی)</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-3">
                    <div class="col-md-3">
                        <label class="form-label-custom">مبلغ کل قرارداد (تومان):</label>
                        <input type="number" class="form-control form-control-premium font-monospace" name="total_amount" value="<?php echo $isEdit ? htmlspecialchars($contract->total_amount) : ''; ?>" placeholder="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">مبلغ پیش‌پرداخت (تومان):</label>
                        <input type="number" class="form-control form-control-premium font-monospace" name="prepayment_amount" value="<?php echo $isEdit ? htmlspecialchars($contract->prepayment_amount ?? 0) : '0'; ?>" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">درصد مالیات (بر ارزش افزوده):</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-premium font-monospace" name="vat_rate" value="<?php echo $isEdit ? htmlspecialchars($contract->vat_rate ?? 0) : '0'; ?>" placeholder="مثلاً 9 یا 0">
                            <span class="input-group-text bg-light border-start-0 rounded-end-3 text-muted small">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">تاریخ سررسید تسویه نهایی:</label>
                        <div class="input-group">
                            <span class="input-group-text-custom"><i class="far fa-calendar-alt"></i></span>
                            <input type="text" class="form-control form-control-premium datepicker-input persian-datepicker" name="final_payment_due_date" value="<?php echo $finalPaymentDueDateJalali; ?>" autocomplete="off" placeholder="1405/01/01">
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">تاریخ شروع قرارداد (شمسی):</label>
                        <div class="input-group">
                            <span class="input-group-text-custom"><i class="far fa-calendar-alt"></i></span>
                            <input type="text" class="form-control form-control-premium datepicker-input persian-datepicker" name="start_date" value="<?php echo $startDateJalali; ?>" autocomplete="off" placeholder="1405/01/01" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">تاریخ پایان قرارداد (شمسی):</label>
                        <div class="input-group">
                            <span class="input-group-text-custom"><i class="far fa-calendar-alt"></i></span>
                            <input type="text" class="form-control form-control-premium datepicker-input persian-datepicker" name="end_date" value="<?php echo $endDateJalali; ?>" autocomplete="off" placeholder="1405/12/29">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">دوره تمدید خودکار:</label>
                        <select class="form-select form-select-premium" name="renewal_type">
                            <option value="none" <?php echo ($isEdit && $contract->renewal_type == 'none') ? 'selected' : ''; ?>>یک‌باره (بدون تمدید)</option>
                            <option value="monthly" <?php echo ($isEdit && $contract->renewal_type == 'monthly') ? 'selected' : ''; ?>>تکرار ماهانه</option>
                            <option value="yearly" <?php echo ($isEdit && $contract->renewal_type == 'yearly') ? 'selected' : ''; ?>>تکرار سالانه</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label-custom">توضیحات و شرایط اختصاصی متمم:</label>
                        <textarea class="form-control form-control-premium" name="description" rows="3" placeholder="شرح کامل تعهدات خاص، فازبندی‌ها یا توضیحات متمم..."><?php echo $isEdit ? htmlspecialchars($contract->description) : ''; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-3 mb-5">
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts" class="btn btn-fin-secondary">انصراف</a>
            <button type="submit" class="btn btn-fin-primary">
                <i class="fas fa-check-circle me-1"></i> <?php echo $buttonText; ?>
            </button>
        </div>
    </form>
</div>

<!-- اسکریپت‌ها -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<script>
let clauseIndex = <?php echo !empty($existingClauses) ? count($existingClauses) : 0; ?>;

$(document).ready(function() {
    initPersianDatepickers();
});

function initPersianDatepickers() {
    $('.persian-datepicker').pDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        initialValue: false,
        observer: true,
        persianDigit: false,
        toolbox: { calendarSwitch: { enabled: false } }
    });
}

function loadDefaultTemplates(serviceType) {
    if (!serviceType) return;

    const container = document.getElementById('clausesContainer');
    if (container.children.length > 0) {
        if (!confirm('آیا مایلید مواد پیش‌فرض جدید بارگذاری شوند؟ (مواد فعلی جایگزین خواهند شد)')) {
            return;
        }
    }

    const url = '<?php echo APP_URL; ?>/index.php?page=admin&action=get_contract_templates_ajax&service_type=' + encodeURIComponent(serviceType);

    fetch(url)
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.status === 'success' && data.clauses.length > 0) {
                    container.innerHTML = '';
                    clauseIndex = 0;
                    data.clauses.forEach(clause => {
                        addClauseRowWithData(clause.title, clause.content);
                    });
                }
            } catch (e) {
                console.error("خطا در پاسخ سرور:", text);
            }
        })
        .catch(err => console.error('خطا در ارتباط با سرور:', err));
}

function triggerTemplateReload() {
    const serviceType = document.getElementById('service_type').value;
    if (!serviceType) {
        alert('لطفاً ابتدا نوع خدمت را انتخاب کنید.');
        return;
    }
    loadDefaultTemplates(serviceType);
}

function addClauseRowWithData(title = '', content = '') {
    const container = document.getElementById('clausesContainer');
    const rowId = `clause-row-${clauseIndex}`;
    
    const html = `
        <div class="clause-row" id="${rowId}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="clause-badge">ماده ${clauseIndex + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeClauseRow('${rowId}')">
                    <i class="fas fa-trash-alt me-1"></i> حذف
                </button>
            </div>
            <div class="mb-2">
                <input type="text" class="form-control form-control-premium fw-bold mb-2" name="clauses[${clauseIndex}][title]" value="${title}" placeholder="عنوان ماده" required>
            </div>
            <div>
                <textarea class="form-control form-control-premium" name="clauses[${clauseIndex}][content]" rows="3" placeholder="متن کامل ماده حقوقی..." required>${content}</textarea>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    clauseIndex++;
}

function addClauseRow() {
    addClauseRowWithData('', '');
}

function removeClauseRow(rowId) {
    const element = document.getElementById(rowId);
    if (element) element.remove();
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if (!$isEdit && empty($existingClauses)): ?>
        const initialType = document.getElementById('service_type').value;
        if (initialType) loadDefaultTemplates(initialType);
    <?php endif; ?>
});
</script>