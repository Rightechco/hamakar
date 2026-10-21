<div class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/market" class="text-decoration-none text-muted">فروشگاه</a></li>
                <li class="breadcrumb-item active text-success" aria-current="page"><?= $data['module']->title ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-4"><?= $data['module']->title ?></h1>
                <div class="bg-white p-4 rounded shadow-sm border mb-4">
                    <h5 class="fw-bold border-bottom pb-3 mb-3">توضیحات کامل</h5>
                    <div class="text-muted lh-lg">
                        <?= nl2br(htmlspecialchars($data['module']->full_description)) ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">خرید ماژول</h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-muted">قیمت:</span>
                            <span class="h4 text-success mb-0 fw-bold"><?= number_format($data['module']->price) ?> تومان</span>
                        </div>

                        <ul class="list-unstyled mb-4 small text-muted">
                            <li class="mb-2">✅ تحویل آنی پس از پرداخت</li>
                            <li class="mb-2">✅ نصب رایگان توسط پشتیبانی</li>
                            <li class="mb-2">✅ بروزرسانی‌های مادام‌العمر</li>
                        </ul>

                        <a href="/market/checkout/web_module/<?= $data['module']->id ?>" class="btn btn-success w-100 py-3 fw-bold">
                            افزودن به پنل کاربری
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>