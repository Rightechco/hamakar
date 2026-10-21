<?php
// app/views/employee/leave/index.php - نسخه اولترا پرمیوم با تقویم شمسی
$requests = $requests ?? [];
?>

<!-- کتابخانه تقویم شمسی (Persian Datepicker) -->
<link rel="stylesheet" href="https://unpkg.com/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://unpkg.com/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<style>
    .leave-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .form-control-leave {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.85rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
        background-color: #ffffff;
    }

    .form-control-leave:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        background-color: #ffffff;
    }

    .leave-type-selector {
        background: #f1f5f9;
        border-radius: 14px;
        padding: 0.35rem;
        display: flex;
        gap: 0.25rem;
    }

    .leave-type-option {
        flex: 1;
        text-align: center;
    }

    .leave-type-option input[type="radio"] {
        display: none;
    }

    .leave-type-option label {
        display: block;
        padding: 0.65rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        color: #64748b;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        margin: 0;
    }

    .leave-type-option input[type="radio"]:checked + label {
        background: #ffffff;
        color: #4338ca;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .btn-submit-leave {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 14px;
        padding: 0.9rem 2rem;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        width: 100%;
    }

    .btn-submit-leave:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
    }

    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-premium td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }

    /* شخصی‌سازی استایل تقویم پاپ‌آپ */
    .datepicker-container {
        font-family: inherit !important;
        border-radius: 16px !important;
        box-shadow: 0 20px 35px -5px rgba(0,0,0,0.15) !important;
        border: 1px solid #e2e8f0 !important;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت درخواست‌های مرخصی</h1>
        <p class="text-muted small mb-0">ثبت مرخصی‌های روزانه و ساعتی، پیگیری وضعیت تایید سرپرست و مشاهده تاریخچه</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- فرم ثبت درخواست مرخصی -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="leave-card-header">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-plus text-indigo-600" style="color:#6366f1;"></i>
                    <span>ثبت درخواست مرخصی جدید</span>
                </h6>
            </div>

            <div class="card-body p-4">
                <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_leave_request" method="POST" id="leave-form">
                    
                    <!-- انتخاب نوع مرخصی -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-2">نوع مرخصی درخواست‌شده:</label>
                        <div class="leave-type-selector">
                            <div class="leave-type-option">
                                <input type="radio" name="leave_type" id="type_daily" value="daily" checked>
                                <label for="type_daily"><i class="far fa-calendar-alt me-1"></i> روزانه</label>
                            </div>
                            <div class="leave-type-option">
                                <input type="radio" name="leave_type" id="type_hourly" value="hourly">
                                <label for="type_hourly"><i class="far fa-clock me-1"></i> ساعتی</label>
                            </div>
                        </div>
                    </div>

                    <!-- فیلدهای مرخصی روزانه -->
                    <div id="daily-fields">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="start_date_daily" class="form-label small fw-bold text-dark">از تاریخ:</label>
                                <input type="text" class="form-control form-control-leave jalali-datepicker text-center font-monospace" name="start_date" id="start_date_daily" placeholder="انتخاب تاریخ..." readonly style="cursor: pointer; background-color: #fff;">
                            </div>
                            <div class="col-6">
                                <label for="end_date_daily" class="form-label small fw-bold text-dark">تا تاریخ:</label>
                                <input type="text" class="form-control form-control-leave jalali-datepicker text-center font-monospace" name="end_date" id="end_date_daily" placeholder="انتخاب تاریخ..." readonly style="cursor: pointer; background-color: #fff;">
                            </div>
                        </div>
                    </div>

                    <!-- فیلدهای مرخصی ساعتی -->
                    <div id="hourly-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="start_date_hourly" class="form-label small fw-bold text-dark">در تاریخ:</label>
                            <input type="text" class="form-control form-control-leave jalali-datepicker text-center font-monospace" name="start_date_hourly" id="start_date_hourly" placeholder="انتخاب تاریخ..." readonly style="cursor: pointer; background-color: #fff;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="start_time" class="form-label small fw-bold text-dark">از ساعت:</label>
                                <input type="time" class="form-control form-control-leave text-center font-monospace" name="start_time" id="start_time">
                            </div>
                            <div class="col-6">
                                <label for="end_time" class="form-label small fw-bold text-dark">تا ساعت:</label>
                                <input type="time" class="form-control form-control-leave text-center font-monospace" name="end_time" id="end_time">
                            </div>
                        </div>
                    </div>

                    <!-- دلیل درخواست -->
                    <div class="mb-4">
                        <label for="reason" class="form-label small fw-bold text-dark">دلیل درخواست مرخصی: <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control form-control-leave" rows="4" placeholder="شرح مختصر از علت درخواست..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit-leave d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>ثبت و ارسال درخواست</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول تاریخچه درخواست‌ها -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="leave-card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-indigo-600" style="color:#6366f1;"></i>
                    <span>تاریخچه درخواست‌های شما</span>
                </h6>
                <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
                    تعداد: <?php echo count($requests); ?>
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">نوع</th>
                                <th>بازه زمانی / تاریخ</th>
                                <th class="text-center">وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($requests)): ?>
                                <?php foreach ($requests as $req): ?>
                                    <?php
                                        $typeLabel = ($req->leave_type == 'daily') ? 'روزانه' : 'ساعتی';
                                        $typeBadge = ($req->leave_type == 'daily') ? 'bg-primary' : 'bg-info';

                                        $status = $req->status ?? 'pending';
                                        $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                        $statusText = 'در انتظار بررسی';

                                        if ($status === 'approved' || $status === 'confirmed') {
                                            $statusBadge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                            $statusText = 'تایید شده';
                                        } elseif ($status === 'rejected') {
                                            $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                            $statusText = 'رد شده';
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold <?php echo $typeBadge; ?> bg-opacity-10 text-dark border">
                                                <?php echo $typeLabel; ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark font-monospace small">
                                            <?php if ($req->leave_type == 'daily'): ?>
                                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                                <?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($req->start_date)) : $req->start_date; ?>
                                                <span class="text-muted mx-1">تا</span>
                                                <?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($req->end_date)) : $req->end_date; ?>
                                            <?php else: ?>
                                                <i class="far fa-clock text-muted me-1"></i>
                                                <?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($req->start_date)) : $req->start_date; ?>
                                                <span class="text-muted mx-1">از</span>
                                                <?php echo function_exists('jdate') ? jdate('H:i', strtotime($req->start_date)) : $req->start_date; ?>
                                                <span class="text-muted mx-1">تا</span>
                                                <?php echo function_exists('jdate') ? jdate('H:i', strtotime($req->end_date)) : $req->end_date; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $statusBadge; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-xmark fs-2 mb-2 d-block opacity-40"></i>
                                        هنوز هیچ درخواست مرخصی ثبت نکرده‌اید.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // فعال‌سازی تقویم پاپ‌آپ شمسی روی کلاس .jalali-datepicker
    $('.jalali-datepicker').persianDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        initialValue: false,
        persianDigit: false
    });

    // مدیریت سوئیچ روزانه/ساعتی
    const leaveTypeRadios = document.querySelectorAll('input[name="leave_type"]');
    const dailyFields = document.getElementById('daily-fields');
    const hourlyFields = document.getElementById('hourly-fields');
    const startDateDaily = document.getElementById('start_date_daily');
    const endDateDaily = document.getElementById('end_date_daily');
    const startDateHourly = document.getElementById('start_date_hourly');

    function toggleFields() {
        if (document.getElementById('type_daily').checked) {
            dailyFields.style.display = 'block';
            hourlyFields.style.display = 'none';
            endDateDaily.name = 'end_date';
            startDateDaily.name = 'start_date';
            startDateHourly.name = 'start_date_hourly_disabled';
        } else {
            dailyFields.style.display = 'none';
            hourlyFields.style.display = 'block';
            endDateDaily.name = 'end_date_disabled';
            startDateDaily.name = 'start_date_disabled';
            startDateHourly.name = 'start_date';
        }
    }

    leaveTypeRadios.forEach(radio => radio.addEventListener('change', toggleFields));
    toggleFields();
});
</script>