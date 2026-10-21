<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-success">فروشگاه ماژول‌های وین‌بو</h1>
            <p class="text-muted">امکانات سامانه خود را با خرید ماژول‌های اختصاصی گسترش دهید</p>
        </div>

        <div class="row g-4">
            <?php foreach($data['modules'] as $mod): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success p-2 rounded">
                                افزونه رسمی
                            </span>
                        </div>
                        <h3 class="card-title h4 fw-bold mb-3"><?= htmlspecialchars($mod->title) ?></h3>
                        <p class="card-text text-muted mb-4" style="height: 60px; overflow: hidden;">
                            <?= htmlspecialchars($mod->short_description) ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-success mb-0"><?= number_format($mod->price) ?> <small>تومان</small></span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 pt-0">
                        <a href="/market/show/<?= $mod->slug ?>" class="btn btn-outline-success w-100 py-2">مشاهده و خرید</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="col-12 mt-5">
                <div class="card bg-dark text-white border-0 overflow-hidden">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-8 p-5">
                            <h2 class="fw-bold mb-3">اپلیکیشن ویندوزی (جمع‌آوری دیتا)</h2>
                            <p class="lead mb-4">آیا به دنبال استخراج شماره تماس از دیوار، شیپور و نقشه گوگل هستید؟ نرم‌افزار مستقل ویندوزی ما را امتحان کنید.</p>
                            <a href="/market/windows" class="btn btn-warning btn-lg px-5 fw-bold">خرید لایسنس ویندوز</a>
                        </div>
                        <div class="col-md-4 bg-secondary bg-opacity-25 h-100 d-flex align-items-center justify-content-center display-1 text-white-50">
                            WINDOWS APP
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>