<?php
// app/views/admin/training/assessments/index.php - نسخه اولترا پرمیوم
$employees = $employees ?? [];
$skills = $skills ?? [];
?>

<style>
    .assessment-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }
    
    .peer-selector-box {
        max-height: 260px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 0.75rem;
        background: #ffffff;
    }

    .peer-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.6rem 0.85rem;
        border-radius: 10px;
        margin-bottom: 0.35rem;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .peer-item-card:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }

    .peer-item-card input[type="checkbox"]:checked + label {
        font-weight: 700;
        color: #4f46e5;
    }

    .btn-send-assessment {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
        transition: all 0.25s ease;
    }

    .btn-send-assessment:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.35);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت آزمون‌های مهارتی 360 درجه</h1>
        <p class="text-muted small mb-0">ایجاد و ارسال درخواست‌های ارزیابی عملکرد و مهارتی پرسنل توسط همکاران</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="assessment-card-header d-flex align-items-center gap-2">
        <div class="rounded-3 bg-indigo-50 p-2 text-indigo-600 d-flex align-items-center justify-content-center" style="background:#e0e7ff; color:#4f46e5; width:38px; height:38px;">
            <i class="fas fa-users-gear fs-5"></i>
        </div>
        <h6 class="m-0 fw-bold text-dark">ارسال درخواست ارزیابی 360 درجه همکاران</h6>
    </div>

    <div class="card-body p-4">
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=send_peer_assessment_request" method="POST">
            <div class="row g-4 mb-4">
                
                <!-- انتخاب کارمند مورد ارزیابی -->
                <div class="col-md-6">
                    <label for="employee_id" class="form-label fw-bold text-secondary small">کارمند مورد ارزیابی (تارگت):</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="fas fa-user-check"></i></span>
                        <select name="employee_id" id="employee_id" class="form-select rounded-end-3" required>
                            <option value="">انتخاب کنید...</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?php echo $employee->id; ?>"><?php echo sanitize($employee->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- انتخاب مهارت‌های ارزیابی -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small">مهارت‌های مورد ارزیابی:</label>
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        <?php foreach ($skills as $key => $label): ?>
                            <div class="form-check form-check-inline bg-light border rounded-pill px-3 py-1.5 m-0">
                                <input class="form-check-input" type="checkbox" name="skills_to_assess[]" value="<?php echo $key; ?>" id="skill-<?php echo $key; ?>" checked>
                                <label class="form-check-label small fw-bold text-dark cursor-pointer" for="skill-<?php echo $key; ?>">
                                    <?php echo sanitize($label); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- انتخاب همکاران ارزیابی‌کننده -->
                <div class="col-12">
                    <label class="form-label fw-bold text-secondary small d-flex justify-content-between align-items-center mb-2">
                        <span>انتخاب همکاران ارزیابی‌کننده (یک یا چند نفر):</span>
                        <span class="text-muted font-normal" style="font-size:0.775rem;">تعداد انتخاب‌شده: <span id="selected-peers-count" class="fw-bold text-indigo-600" style="color:#4f46e5;">0</span> نفر</span>
                    </label>

                    <div class="peer-selector-box">
                        <?php foreach ($employees as $employee): ?>
                            <div class="peer-item-card">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input peer-checkbox" type="checkbox" name="peer_ids[]" value="<?php echo $employee->id; ?>" id="peer-<?php echo $employee->id; ?>">
                                    <label class="form-check-label text-dark small cursor-pointer" for="peer-<?php echo $employee->id; ?>">
                                        <?php echo sanitize($employee->name); ?>
                                    </label>
                                </div>
                                <span class="badge bg-light text-muted font-monospace border">ID: <?php echo $employee->id; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-send-assessment d-inline-flex align-items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>ارسال درخواست‌های ارزیابی</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const peerCheckboxes = document.querySelectorAll('.peer-checkbox');
    const countDisplay = document.getElementById('selected-peers-count');
    const targetSelect = document.getElementById('employee_id');

    function updateCount() {
        const checked = document.querySelectorAll('.peer-checkbox:checked').length;
        if(countDisplay) countDisplay.textContent = checked;
    }

    peerCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    // غیرفعال کردن خود کارمند در لیست ارزیابی‌کنندگان
    if (targetSelect) {
        targetSelect.addEventListener('change', function() {
            const targetId = this.value;
            peerCheckboxes.forEach(cb => {
                const parent = cb.closest('.peer-item-card');
                if (cb.value === targetId) {
                    cb.checked = false;
                    cb.disabled = true;
                    if(parent) parent.style.opacity = '0.4';
                } else {
                    cb.disabled = false;
                    if(parent) parent.style.opacity = '1';
                }
            });
            updateCount();
        });
    }
});
</script>