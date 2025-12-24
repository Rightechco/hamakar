<?php
// app/views/employee/training/assessments/self_form.php
$isSubmitted = $isSubmitted ?? false;
$skills = $skills ?? [];
?>
<h1 class="mb-4">آزمون خودارزیابی - سال <?php echo jdate('Y'); ?></h1>

<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">ارزیابی عملکرد خود</h6>
    </div>
    <div class="card-body">
        <?php if ($isSubmitted): ?>
            <div class="alert alert-success">
                شما قبلاً خودارزیابی خود را برای این سال ثبت کرده‌اید.
            </div>
        <?php else: ?>
            <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_self_assessment" method="POST">
                <?php foreach ($skills as $key => $title): ?>
                    <div class="mb-3">
                        <label class="form-label"><strong><?php echo sanitize($title); ?></strong></label>
                        <div class="d-flex align-items-center">
                            <span class="me-3">امتیاز شما:</span>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                    <label class="form-check-label" for="score-<?php echo $key; ?>-<?php echo $i; ?>"><?php echo $i; ?></label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mb-3">
                    <label for="notes" class="form-label">یادداشت‌های اضافی:</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">ثبت خودارزیابی</button>
            </form>
        <?php endif; ?>
    </div>
</div>
```php
<?php
// app/views/employee/training/assessments/peer_form.php
$targetEmployee = $targetEmployee ?? null;
$skills = $skills ?? [];
?>
<h1 class="mb-4">ارزیابی عملکرد: <?php echo sanitize($targetEmployee->name); ?></h1>

<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">فرم ارزیابی ۳۶۰ درجه برای <?php echo sanitize($targetEmployee->name); ?></h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_peer_assessment" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $targetEmployee->id; ?>">
            
            <?php foreach ($skills as $key => $title): ?>
                <div class="mb-3">
                    <label class="form-label"><strong><?php echo sanitize($title); ?></strong></label>
                    <div class="d-flex align-items-center">
                        <span class="me-3">امتیاز شما:</span>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                <label class="form-check-label" for="score-<?php echo $key; ?>-<?php echo $i; ?>"><?php echo $i; ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="mb-3">
                <label for="notes" class="form-label">یادداشت‌های اضافی:</label>
                <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">ثبت ارزیابی</button>
        </form>
    </div>
</div>
