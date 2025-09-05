<h1 class="mb-4"><?php echo htmlspecialchars($title); ?></h1>

<form action="<?php echo APP_URL; ?>/index.php?page=admin&action=store_payroll" method="POST">

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">اطلاعات پایه</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="user_id" class="form-label">انتخاب کارمند <span class="text-danger">*</span></label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="" selected disabled>یک کارمند را انتخاب کنید...</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo htmlspecialchars($employee->id); ?>"><?php echo htmlspecialchars($employee->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="year" class="form-label">سال <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="year" name="year" value="<?php echo jdate('Y'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="month" class="form-label">ماه <span class="text-danger">*</span></label>
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

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header"><h6 class="m-0">حقوق و مزایا (Earnings)</h6></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>شرح</th><th>مبلغ</th><th></th></tr></thead>
                        <tbody id="earnings-tbody">
                            </tbody>
                    </table>
                    <button type="button" id="add-earning-row" class="btn btn-success btn-sm">افزودن ردیف مزایا</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="card shadow mb-4">
                <div class="card-header"><h6 class="m-0">کسورات (Deductions)</h6></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>شرح</th><th>مبلغ</th><th></th></tr></thead>
                        <tbody id="deductions-tbody">
                            </tbody>
                    </table>
                    <button type="button" id="add-deduction-row" class="btn btn-warning btn-sm">افزودن ردیف کسورات</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="notes" class="form-label">یادداشت (اختیاری):</label>
        <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
    </div>

    <hr>
    <button type="submit" class="btn btn-primary btn-lg">صدور نهایی فیش حقوقی</button>
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('user_id');
    const yearInput = document.getElementById('year');
    const earningsTableBody = document.getElementById('earnings-tbody');
    const deductionsTableBody = document.getElementById('deductions-tbody');
    const addEarningBtn = document.getElementById('add-earning-row');
    const addDeductionBtn = document.getElementById('add-deduction-row');

    function fetchPayrollData() {
        const userId = employeeSelect.value;
        const year = yearInput.value;
        if (!userId || !year) return;

        // نمایش یک حالت لودینگ (اختیاری)
        earningsTableBody.innerHTML = '<tr><td colspan="3" class="text-center">در حال محاسبه...</td></tr>';
        deductionsTableBody.innerHTML = '';

        fetch(`index.php?page=admin&action=get_payroll_data&user_id=${userId}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    populateTable(earningsTableBody, data.earnings, 'earnings');
                    populateTable(deductionsTableBody, data.deductions, 'deductions');
                } else {
                    alert(data.message);
                    earningsTableBody.innerHTML = '';
                }
            });
    }

    function populateTable(tbody, items, type) {
        tbody.innerHTML = ''; // پاک کردن جدول
        let i = tbody.children.length;
        items.forEach(item => {
            addRow(tbody, type, i, item.description, item.amount);
            i++;
        });
    }
    
    function addRow(tbody, type, index, desc = '', amount = '') {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="${type}[${index}][description]" class="form-control form-control-sm" value="${desc}"></td>
            <td><input type="number" name="${type}[${index}][amount]" class="form-control form-control-sm" value="${amount}"></td>
            <td><button type="button" class="btn btn-danger btn-sm py-0 px-2 remove-row">×</button></td>
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
});
</script>