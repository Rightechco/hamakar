<?php
// app/views/admin/sms/panel.php - نسخه نهایی با طراحی حرفه‌ای
?>

<h1 class="mb-4">پنل بازاریابی پیامکی</h1>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-pen-alt me-2"></i>ارسال کمپین سفارشی</h6>
            </div>
            <div class="card-body">
                <p>متن دلخواه خود را برای ارسال به تمام مشتریان در کادر زیر وارد نمایید.</p>
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=send_custom_bulk_sms" method="POST">
                    <div class="mb-3">
                        <label for="message_body" class="form-label">متن پیامک:</label>
                        <textarea name="message_body" id="message_body" class="form-control" rows="6" placeholder="پیام خود را اینجا بنویسید..." required></textarea>
                        <small class="form-text text-muted mt-2">فراموش نکنید که عبارت `لغو11` برای ارسال انبوه الزامی است.</small>
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary w-100" onclick="return confirm('آیا از ارسال این پیامک به تمام مشتریان مطمئن هستید؟')">
                        <i class="fas fa-broadcast-tower me-2"></i>ارسال گروهی
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-star me-2"></i>تقویم مناسبت‌ها و ارسال دستی</h6>
            </div>
            <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                <p>از این بخش می‌توانید پیامک مناسبتی را به صورت دستی ارسال کنید.</p>
                <ul class="list-group list-group-flush">
                    <?php foreach ($allOccasions as $date => $occasion): ?>
                        <?php if (empty($occasion['message'])) continue; // از مناسبت‌های بدون پیام رد شو ?>
                        <?php
                            $isToday = (jdate('m-d') === $date);
                            $messageTemplate = str_replace('{client_name}', 'نام مشتری', $occasion['message']);
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $isToday ? 'list-group-item-success' : ''; ?>">
                            <div>
                                <strong><?php echo sanitize($occasion['name']); ?></strong>
                                <small class="d-block text-muted"><?php echo jdate('d F', strtotime(jdate('Y').'-'.$date)); ?></small>
                            </div>
                            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=send_occasional_sms" method="POST" class="ms-2">
                                <input type="hidden" name="occasion_message" value="<?php echo htmlspecialchars($occasion['message']); ?>">
                                <button type="submit" class="btn btn-sm <?php echo $isToday ? 'btn-success' : 'btn-outline-secondary'; ?>" title="ارسال پیامک: <?php echo htmlspecialchars($messageTemplate); ?>" onclick="return confirm('آیا از ارسال پیامک «<?php echo sanitize($occasion['name']); ?>» به تمام مشتریان مطمئن هستید؟')">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>