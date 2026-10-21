<?php
// app/views/admin/payrolls/create_payroll.php
$title = $title ?? 'صدور فیش حقوقی جدید';
$employees = $employees ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 10px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .payroll-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .premium-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
    }

    .premium-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    .form-control, .form-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.75rem 2rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }

    /* Floating Dynamic Table */
    .table-ultra-dynamic {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
    }

    .table-ultra-dynamic th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 14px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-ultra-dynamic td {
        padding: 6px 10px;
        vertical-align: middle;
    }

    .btn-remove-row {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fee2e2;
        background-color: #fef2f2;
        color: #ef4444;
        transition: all 0.2s;
    }

    .btn-remove-row:hover {
        background-color: #ef4444;
        color: #ffffff;
    }
</style>

<div class="payroll-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($title); ?></h3>
            <p class="text-muted small mb-0">صدور و محاسبه کارکرد، حقوق پایه، مزایا و کسورات ماهانه پرسنل</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=payrolls" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست فیش‌ها
        </a>
    </div>

    <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=store_payroll" method="POST">

        <!-- Basic Info Card -->
        <div class="premium-card">
            <div class="premium-card-header d-flex align-items-center">
                <i class="fas fa-user-clock text-primary fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold fs-6 text-dark">اطلاعات پایه کارمند و دوره مالی</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="user_id" class="form-label fw-medium small text-dark">انتخاب کارمند <span class="text-danger">*</span></label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="" selected disabled>یک کارمند را انتخاب کنید...</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?php echo htmlspecialchars($employee->id); ?>"><?php echo htmlspecialchars($employee->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label fw-medium small text-dark">سال مالی <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="year" name="year" value="<?php echo jdate('Y'); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="month" class="form-label fw-medium small text-dark">ماه <span class="text-danger">*</span></label>
                        <select class="form-select" id="month" name="month" required>
                             <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo (jdate('n') == $m) ? 'selected' : ''; ?>>
                                    <?php echo jdate('F', mktime(0,0,0,$m,1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings and Deductions Two Column Layout -->
        <div class="row g-4">
            <!-- Earnings (مزایا) -->
            <div class="col-lg-6">
                <div class="premium-card">
                    <div class="premium-card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-plus-circle text-success fs-5 me-2"></i>
                            <h6 class="mb-0 fw-bold fs-6 text-dark">حقوق و مزایا (Earnings)</h6>
                        </div>
                        <button type="button" id="add-earning-row" class="btn btn-sm btn-light text-success border fw-medium rounded-2">
                            <i class="fas fa-plus me-1"></i> افزودن مزایا
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table-ultra-dynamic">
                                <thead>
                                    <tr>
                                        <th>شرح مزایا</th>
                                        <th style="width: 180px;">مبلغ (تومان)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="earnings-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions (کسورات) -->
            <div class="col-lg-6">
                 <div class="premium-card">
                    <div class="premium-card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-minus-circle text-warning fs-5 me-2"></i>
                            <h6 class="mb-0 fw-bold fs-6 text-dark">کسورات (Deductions)</h6>
                        </div>
                        <button type="button" id="add-deduction-row" class="btn btn-sm btn-light text-warning border fw-medium rounded-2">
                            <i class="fas fa-plus me-1"></i> افزودن کسورات
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table-ultra-dynamic">
                                <thead>
                                    <tr>
                                        <th>شرح کسورات</th>
                                        <th style="width: 180px;">مبلغ (تومان)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="deductions-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notes & Action Button -->
        <div class="premium-card">
            <div class="card-body p-4">
                <div class="mb-3">
                    <label for="notes" class="form-label fw-medium small text-dark">توضیحات و یادداشت (اختیاری):</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="یادداشت یا توضیحات خاص فیش حقوقی..."></textarea>
                </div>
                <div class="d-flex justify-content-end pt-2 border-top">
                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="fas fa-check-circle me-1.5"></i> صدور نهایی فیش حقوقی
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('user_id');
    const yearInput = document.getElementById('year');
    const monthSelect = document.getElementById('month');
    const earningsTableBody = document.getElementById('earnings-tbody');
    const deductionsTableBody = document.getElementById('deductions-tbody');
    const addEarningBtn = document.getElementById('add-earning-row');
    const addDeductionBtn = document.getElementById('add-deduction-row');

    function fetchPayrollData() {
        const userId = employeeSelect.value;
        const year = yearInput.value;
        const month = monthSelect.value;
        if (!userId || !year || !month) return;

        earningsTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>در حال محاسبه اطلاعات و کارکرد...</td></tr>';
        deductionsTableBody.innerHTML = '';

        fetch(`index.php?page=admin&action=get_payroll_data&user_id=${userId}&year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    populateTable(earningsTableBody, data.earnings, 'earnings');
                    populateTable(deductionsTableBody, data.deductions, 'deductions');
                } else {
                    alert(data.message);
                    earningsTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">اطلاعاتی یافت نشد</td></tr>';
                }
            })
            .catch(err => {
                earningsTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">خطا در بارگذاری اطلاعات</td></tr>';
            });
    }

    function populateTable(tbody, items, type) {
        tbody.innerHTML = '';
        let i = 0;
        if(items && items.length > 0) {
            items.forEach(item => {
                addRow(tbody, type, i, item.description, item.amount);
                i++;
            });
        } else {
            addRow(tbody, type, 0, '', '');
        }
    }
    
    function addRow(tbody, type, index, desc = '', amount = '') {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="${type}[${index}][description]" class="form-control" value="${desc}" placeholder="عنوان آیتم..."></td>
            <td><input type="number" name="${type}[${index}][amount]" class="form-control fw-bold" value="${amount}" placeholder="0"></td>
            <td class="text-center"><button type="button" class="btn-remove-row remove-row" title="حذف ردیف"><i class="fas fa-times"></i></button></td>
        `;
        tbody.appendChild(row);
        
        row.querySelector('.remove-row').addEventListener('click', function() {
            this.closest('tr').remove();
        });
    }

    addEarningBtn.addEventListener('click', function() {
        addRow(earningsTableBody, 'earnings', earningsTableBody.children.length);
    });
    
    addDeductionBtn.addEventListener('click', function() {
        addRow(deductionsTableBody, 'deductions', deductionsTableBody.children.length);
    });

    employeeSelect.addEventListener('change', fetchPayrollData);
    yearInput.addEventListener('change', fetchPayrollData);
    monthSelect.addEventListener('change', fetchPayrollData);
});
</script>