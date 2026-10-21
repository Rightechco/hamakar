<?php
// app/views/employee/dashboard.php - نسخه یولترا پرمیوم، زنده و هوشمند
$user = $user ?? null;
$openSession = $openSession ?? null;
$isClockedIn = !empty($openSession);
$clockInTimestamp = ($isClockedIn && !empty($openSession->clock_in)) ? strtotime($openSession->clock_in) : null;
?>

<style>
    /* Premium Root & CSS Variables */
    :root {
        --emp-card-bg: #ffffff;
        --emp-text-main: #0f172a;
        --emp-text-muted: #64748b;
        --emp-border: #f1f5f9;
        --emp-accent: #4f46e5;
        --emp-emerald: #10b981;
        --emp-rose: #f43f5e;
        --emp-amber: #f59e0b;
        --emp-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 12px -2px rgba(0, 0, 0, 0.02);
    }

    /* Live Pulse Indicator */
    .pulse-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 6px;
    }
    .pulse-green {
        background-color: var(--emp-emerald);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-emerald-anim 2s infinite;
    }
    .pulse-red {
        background-color: var(--emp-rose);
        box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7);
        animation: pulse-rose-anim 2s infinite;
    }

    @keyframes pulse-emerald-anim {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    @keyframes pulse-rose-anim {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(244, 63, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
    }

    /* Attendance Hero Box */
    .attendance-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px;
        color: #ffffff;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.25);
    }

    .attendance-hero-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .digital-clock-box {
        background: rgba(255, 255, 255, 0.07);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 1.25rem;
        text-align: center;
    }

    .elapsed-timer-display {
        font-size: 2.5rem;
        font-weight: 900;
        letter-spacing: 2px;
        font-family: monospace, 'Vazirmatn';
        color: #38dfa6;
        text-shadow: 0 0 20px rgba(56, 223, 166, 0.4);
    }

    /* Buttons Ultra Premium */
    .btn-clock-in {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 16px;
        padding: 0.9rem 2.2rem;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .btn-clock-in:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.45);
    }

    .btn-clock-out {
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 16px;
        padding: 0.9rem 2.2rem;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 10px 25px rgba(244, 63, 94, 0.35);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .btn-clock-out:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 30px rgba(244, 63, 94, 0.45);
    }

    .btn-leave-request {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 16px;
        padding: 0.9rem 1.8rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(8px);
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-leave-request:hover {
        background: rgba(255, 255, 255, 0.22);
        border-color: #ffffff;
        transform: translateY(-2px);
    }

    /* Dashboard Metric Cards */
    .emp-stat-card {
        background: var(--emp-card-bg);
        border-radius: 20px;
        border: 1px solid var(--emp-border);
        box-shadow: var(--emp-shadow);
        padding: 1.35rem 1.5rem;
        transition: all 0.3s ease;
    }
    .emp-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
    }

    .emp-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    /* Leave Modal Styling */
    .modal-content-glass {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .modal-header-premium {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        border: none;
    }
    .form-control-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    .form-control-premium:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }
</style>

<!-- Header & Welcome Banner -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">خوش آمدید، <?php echo htmlspecialchars($user->name ?? 'همکار گرامی'); ?> 👋</h1>
        <p class="text-muted small mb-0">پنل اختصاصی کارکرد، ثبت ورود و خروج زنده و مدیریت مرخصی‌ها</p>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white border text-dark rounded-pill px-3 py-2 shadow-sm font-monospace fw-bold d-inline-flex align-items-center gap-2">
            <i class="far fa-calendar-alt text-primary"></i>
            <span id="jalali-date-display"><?php echo jdate('l، j F Y'); ?></span>
        </span>
    </div>
</div>

<!-- ========================================================= -->
<!-- ULTRA PREMIUM ATTENDANCE & LEAVE HERO WIDGET -->
<!-- ========================================================= -->
<div class="attendance-hero-card mb-4">
    <div class="row align-items-center g-4">
        
        <!-- ستون وضعیت و اطلاعات خروج/ورود -->
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-2 mb-3">
                <?php if ($isClockedIn): ?>
                    <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center">
                        <span class="pulse-indicator pulse-green"></span>
                        حاضر در محل کار / در حال ثبت کارکرد
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-30 rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center">
                        <span class="pulse-indicator pulse-red"></span>
                        خارج از مجموعه / ورود ثبت نشده
                    </span>
                <?php endif; ?>
                <span class="text-white-50 small font-monospace">IP: <?php echo $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; ?></span>
            </div>

            <h2 class="h4 fw-bold text-white mb-2">
                <?php echo $isClockedIn ? 'شیفت کاری شما فعال است' : 'آماده شروع شیفت کاری جدید هستید؟'; ?>
            </h2>
            <p class="text-white-50 small mb-4 text-justify-custom" style="max-width: 520px;">
                <?php if ($isClockedIn): ?>
                    ورود شما در ساعت <strong class="text-white font-monospace"><?php echo jdate('H:i:s', $clockInTimestamp); ?></strong> ثبت شده است. پس از پایان تایم کاری، ثبت خروج را انجام دهید.
                <?php else: ?>
                    جهت محاسبه دقیق ساعت کاری، بیمه و اضافه کاری، بلافاصله پس از استقرار در محل کار، دکمه «ثبت ورود کاری» را بفشارید.
                <?php endif; ?>
            </p>

            <!-- اکشن‌ها: ثبت ورود/خروج + دکمه درخواست مرخصی -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=process_clocking" method="POST" class="m-0">
                    <?php if ($isClockedIn): ?>
                        <button type="submit" class="btn-clock-out d-inline-flex align-items-center gap-2">
                            <i class="fas fa-right-from-bracket fs-5"></i>
                            <span>ثبت خروج کاری</span>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn-clock-in d-inline-flex align-items-center gap-2">
                            <i class="fas fa-right-to-bracket fs-5"></i>
                            <span>ثبت ورود کاری</span>
                        </button>
                    <?php endif; ?>
                </form>

                <button type="button" class="btn-leave-request" data-bs-toggle="modal" data-bs-target="#quickLeaveModal">
                    <i class="fas fa-calendar-plus"></i>
                    <span>ثبت درخواست مرخصی</span>
                </button>
            </div>
        </div>

        <!-- ستون نمایش زنده تایمر و ساعت دیجیتال -->
        <div class="col-lg-5">
            <div class="digital-clock-box">
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">
                    <?php echo $isClockedIn ? 'مدت زمان کارکرد زنده امروز' : 'ساعت رسمی کشور'; ?>
                </span>

                <?php if ($isClockedIn): ?>
                    <div class="elapsed-timer-display" id="active-elapsed-timer">00:00:00</div>
                    <small class="text-white-50 d-block mt-2 font-monospace">شروع: <?php echo jdate('H:i', $clockInTimestamp); ?></small>
                <?php else: ?>
                    <div class="elapsed-timer-display text-white" id="live-digital-clock">00:00:00</div>
                    <small class="text-white-50 d-block mt-2">زمان محلی همگام شده با سرور</small>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================= -->
<!-- CARDS & QUICK METRICS -->
<!-- ========================================================= -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="emp-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">کارکرد ماه جاری</span>
                <h4 class="fw-extrabold text-dark font-monospace mb-0">۱۶۰ <small class="fs-7 text-muted font-normal">ساعت</small></h4>
            </div>
            <div class="emp-icon-box bg-indigo-50 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-business-time"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="emp-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">مانده مرخصی استحقاقی</span>
                <h4 class="fw-extrabold text-dark font-monospace mb-0">۲.۵ <small class="fs-7 text-muted font-normal">روز</small></h4>
            </div>
            <div class="emp-icon-box bg-emerald-50 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="emp-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">پروژه‌ها و تسک‌های فعال</span>
                <h4 class="fw-extrabold text-dark font-monospace mb-0">۴ <small class="fs-7 text-muted font-normal">مورد</small></h4>
            </div>
            <div class="emp-icon-box bg-sky-50 text-sky-600" style="background:#e0f2fe; color:#0284c7;">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="emp-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">درخواست‌های در انتظار</span>
                <h4 class="fw-extrabold text-dark font-monospace mb-0">۱ <small class="fs-7 text-muted font-normal">درخواست</small></h4>
            </div>
            <div class="emp-icon-box bg-amber-50 text-amber-600" style="background:#fef3c7; color:#b45309;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: QUICK LEAVE REQUEST -->
<!-- ========================================================= -->
<div class="modal fade" id="quickLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-glass">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-plus text-indigo-400"></i>
                    <span>ثبت درخواست مرخصی جدید</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_leave_request" method="POST">
                    
                    <!-- نوع مرخصی -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">نوع مرخصی:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="leave_type" id="type_daily" value="daily" checked onchange="toggleLeaveFields('daily')">
                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="type_daily">
                                    روزانه (استحقاقی/استعلاجی)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="leave_type" id="type_hourly" value="hourly" onchange="toggleLeaveFields('hourly')">
                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="type_hourly">
                                    ساعتی (طول روز)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- بخش مرخصی روزانه -->
                    <div id="daily-fields-wrapper" class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="start_date" class="form-label fw-bold text-secondary small">از تاریخ (شمسی):</label>
                            <input type="text" class="form-control form-control-premium font-monospace" id="start_date" name="start_date" placeholder="1405/05/20">
                        </div>
                        <div class="col-6">
                            <label for="end_date" class="form-label fw-bold text-secondary small">تا تاریخ (شمسی):</label>
                            <input type="text" class="form-control form-control-premium font-monospace" id="end_date" name="end_date" placeholder="1405/05/22">
                        </div>
                    </div>

                    <!-- بخش مرخصی ساعتی -->
                    <div id="hourly-fields-wrapper" class="row g-3 mb-3 d-none">
                        <div class="col-12 mb-2">
                            <label for="start_date_hourly" class="form-label fw-bold text-secondary small">تاریخ مرخصی (شمسی):</label>
                            <input type="text" class="form-control form-control-premium font-monospace" id="start_date_hourly" name="start_date_hourly" placeholder="1405/05/20">
                        </div>
                        <div class="col-6">
                            <label for="start_time" class="form-label fw-bold text-secondary small">از ساعت:</label>
                            <input type="time" class="form-control form-control-premium font-monospace" id="start_time" name="start_time">
                        </div>
                        <div class="col-6">
                            <label for="end_time" class="form-label fw-bold text-secondary small">تا ساعت:</label>
                            <input type="time" class="form-control form-control-premium font-monospace" id="end_time" name="end_time">
                        </div>
                    </div>

                    <!-- علت مرخصی -->
                    <div class="mb-4">
                        <label for="reason" class="form-label fw-bold text-secondary small">علت و توضیحات درخواست:</label>
                        <textarea class="form-control form-control-premium" id="reason" name="reason" rows="3" placeholder="توضیحات ضروری برای مدیریت..." required></textarea>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-3">
                        <button type="button" class="btn btn-light border btn-sm rounded-3 px-3 fw-bold" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-4 fw-bold">
                            <i class="fas fa-paper-plane me-1"></i>
                            ارسال درخواست
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- JAVASCRIPT: REAL-TIME TIMERS & MODAL TOGGLES -->
<!-- ========================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ۱. به‌روزرسانی ساعت رسمی زنده
    const liveClockEl = document.getElementById('live-digital-clock');
    if (liveClockEl) {
        setInterval(() => {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            liveClockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }, 1000);
    }

    // ۲. محاسبه زنده مدت زمان حضور زنده در صورت ورود
    <?php if ($isClockedIn && $clockInTimestamp): ?>
    const clockInTime = <?php echo $clockInTimestamp; ?> * 1000;
    const elapsedEl = document.getElementById('active-elapsed-timer');

    if (elapsedEl) {
        function updateElapsedTime() {
            const now = new Date().getTime();
            let diff = Math.floor((now - clockInTime) / 1000);

            if (diff < 0) diff = 0;

            const hrs = String(Math.floor(diff / 3600)).padStart(2, '0');
            const mins = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const secs = String(diff % 60).padStart(2, '0');

            elapsedEl.textContent = `${hrs}:${mins}:${secs}`;
        }
        updateElapsedTime();
        setInterval(updateElapsedTime, 1000);
    }
    <?php endif; ?>
});

// سوئیچ فیلدهای مرخصی روزانه/ساعتی
function toggleLeaveFields(type) {
    const dailyWrapper = document.getElementById('daily-fields-wrapper');
    const hourlyWrapper = document.getElementById('hourly-fields-wrapper');

    if (type === 'hourly') {
        dailyWrapper.classList.add('d-none');
        hourlyWrapper.classList.remove('d-none');
    } else {
        hourlyWrapper.classList.add('d-none');
        dailyWrapper.classList.remove('d-none');
    }
}
</script>