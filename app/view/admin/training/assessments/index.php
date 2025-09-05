<?php
// app/views/admin/training/assessments/index.php
$employees = $employees ?? [];
$skills = $skills ?? [];
?>
<h1 class="mb-4">مدیریت آزمون‌های مهارتی</h1>

<div class="card shadow-sm">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">ارسال درخواست ارزیابی به همکاران</h6></div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=send_peer_assessment_request" method="POST">
            <div class="mb-3">
                <label for="employee_id" class="form-label">کارمند مورد ارزیابی:</label>
                <select name="employee_id" id="employee_id" class="form-select" required>
                    <option value="">انتخاب کنید...</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee->id; ?>"><?php echo sanitize($employee->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="peer_ids" class="form-label">انتخاب همکاران ارزیابی‌کننده (با کنترل یا شیفت چندگانه انتخاب کنید):</label>
                <select name="peer_ids[]" id="peer_ids" class="form-select" multiple required style="min-height: 150px;">
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee->id; ?>"><?php echo sanitize($employee->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">ارسال درخواست</button>
        </form>
    </div>
</div>
```php
<?php
// app/views/employee/training/assessments/peer_form.php
$targetEmployee = $targetEmployee ?? null;
$skills = $skills ?? [];
?>
<h1 class="mb-4">ارزیابی عملکرد همکار: <?php echo sanitize($targetEmployee->name ?? ''); ?></h1>
<div class="card shadow-sm">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">فرم ارزیابی</h6></div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_peer_assessment" method="POST">
            <input type="hidden" name="user_id" value="<?php echo sanitize($targetEmployee->id ?? ''); ?>">
            <p class="text-muted">لطفاً عملکرد همکار خود را در هر یک از مهارت‌های زیر از ۱ (ضعیف) تا ۵ (عالی) ارزیابی کنید.</p>
            <?php foreach ($skills as $key => $label): ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo sanitize($label); ?></label>
                    <div class="d-flex justify-content-between">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="<?php echo $key; ?>-1" value="1" required>
                            <label class="form-check-label" for="<?php echo $key; ?>-1">۱</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="<?php echo $key; ?>-2" value="2">
                            <label class="form-check-label" for="<?php echo $key; ?>-2">۲</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="<?php echo $key; ?>-3" value="3">
                            <label class="form-check-label" for="<?php echo $key; ?>-3">۳</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="<?php echo $key; ?>-4" value="4">
                            <label class="form-check-label" for="<?php echo $key; ?>-4">۴</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="<?php echo $key; ?>-5" value="5">
                            <label class="form-check-label" for="<?php echo $key; ?>-5">۵</label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3">ارسال ارزیابی</button>
        </form>
    </div>
</div>
