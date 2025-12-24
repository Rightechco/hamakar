<h1 class="mb-4">داشبورد شما</h1>
<p>خوش آمدید، در این بخش می‌توانید خلاصه‌ای از وضعیت حساب خود را مشاهده کنید.</p>

<?php if (isset($stats) && $stats !== null): ?>
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">تعداد کل قراردادها</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['contracts_count']; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-signature fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">تعداد کل فاکتورها</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['invoices_count']; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">فاکتورهای پرداخت نشده</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['unpaid_invoices_count']; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">مبلغ کل بدهی</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_unpaid_amount']); ?> تومان</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php elseif (isset($error)): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div>
<?php else: ?>
    <div class="alert alert-info">اطلاعاتی برای نمایش در داشبورد شما وجود ندارد.</div>
<?php endif; ?>