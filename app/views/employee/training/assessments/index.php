<?php
// app/views/employee/training/assessments/peer_form.php
$targetEmployee = $targetEmployee ?? null;
$skills = $skills ?? [];
?>
<h1 class="mb-4">ارزیابی عملکرد: <?php echo sanitize($targetEmployee->name); ?></h1>

<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">فرم ارزیابی ۳۶۰ درجه</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_peer_assessment" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $targetEmployee->id; ?>">
            
            <?php foreach ($skills as $key => $title): ?>
                <div class="mb-3">
                    <label class="form-label"><strong><?php echo sanitize($title); ?></strong></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" value="1" required>
                            <label class="form-check-label">۱ (ضعیف)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" value="2" required>
                            <label class="form-check-label">۲</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" value="3" required>
                            <label class="form-check-label">۳ (متوسط)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" value="4" required>
                            <label class="form-check-label">۴</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scores[<?php echo $key; ?>]" value="5" required>
                            <label class="form-check-label">۵ (عالی)</label>
                        </div>
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
