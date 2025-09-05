<?php
// app/views/employee/training/needs_form.php
$isSubmitted = !empty($existingNeed);
?>
<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">نیازسنجی آموزشی سال <?php echo jdate('Y'); ?></h6>
    </div>
    <div class="card-body">
        <?php if ($isSubmitted): ?>
            <div class="alert alert-success">
                شما قبلاً نیازسنجی آموزشی خود را برای این سال ثبت کرده‌اید. وضعیت: <?php echo $existingNeed->status; ?>
            </div>
            <!-- می‌توانید جزئیات پاسخ‌های قبلی را نیز نمایش دهید -->
        <?php else: ?>
            <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_training_need" method="POST">
                <div class="mb-3">
                    <label for="strengths" class="form-label">نقاط قوت خود را بنویسید:</label>
                    <textarea name="strengths" id="strengths" rows="4" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="weaknesses" class="form-label">نقاط ضعفی که نیاز به توسعه دارند را بنویسید:</label>
                    <textarea name="weaknesses" id="weaknesses" rows="4" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ارسال</button>
            </form>
        <?php endif; ?>
    </div>
</div>