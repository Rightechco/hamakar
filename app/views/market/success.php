<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-success shadow text-center">
                <div class="card-body p-5">
                    <div class="mb-4 text-success display-1">
                        ✅
                    </div>
                    <h2 class="fw-bold text-success mb-3">پرداخت با موفقیت انجام شد</h2>
                    <p class="text-muted mb-4"><?= $data['message'] ?></p>
                    
                    <div class="bg-light p-3 rounded mb-4">
                        <small class="d-block text-muted mb-1">شماره سفارش:</small>
                        <strong class="fs-5"><?= $data['order_id'] ?></strong>
                    </div>

                    <a href="/employee/dashboard" class="btn btn-primary px-5">بازگشت به داشبورد</a>
                </div>
            </div>
        </div>
    </div>
</div>