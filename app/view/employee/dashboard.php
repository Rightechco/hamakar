<h1 class="mb-4">داشبورد شما، <?php echo sanitize($user->name); ?></h1>

<div class="card text-center shadow">
    <div class="card-header">
        سیستم حضور و غیاب
    </div>
    <div class="card-body">
        <h5 class="card-title">
            <?php if ($openSession): ?>
                شما در حال حاضر در محل کار حضور دارید.
            <?php else: ?>
                شما در حال حاضر خارج از محل کار هستید.
            <?php endif; ?>
        </h5>
        <p class="card-text">
            <?php if ($openSession): ?>
                زمان ورود شما: <?php echo jdate('H:i:s - Y/m/d', strtotime($openSession->clock_in)); ?>
            <?php else: ?>
                برای ثبت ورود خود، روی دکمه زیر کلیک کنید.
            <?php endif; ?>
        </p>

        <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=process_clocking" method="POST">
            <?php if ($openSession): ?>
                <button type="submit" class="btn btn-danger btn-lg">ثبت خروج</button>
            <?php else: ?>
                <button type="submit" class="btn btn-success btn-lg">ثبت ورود</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-footer text-muted">
        ساعت سرور: <?php echo date('H:i:s'); ?>
    </div>
</div>