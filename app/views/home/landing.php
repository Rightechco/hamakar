<div class="container py-5 text-center">
    <h1 class="mb-4">شخصی‌سازی سامانه هوشمند همکار</h1>
    <p class="lead mb-5">فقط هزینه‌ی ماژول‌هایی را پرداخت کنید که به آن‌ها نیاز دارید.</p>

    <div class="row" id="plans-container">
        <?php foreach($plans as $plan): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title"><?= $plan['name'] ?></h5>
                    <p class="text-muted small"><?= $plan['description'] ?></p>
                    <h4 class="text-success mb-3"><?= number_format($plan['price']) ?> <small>تومان</small></h4>
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input module-check" type="checkbox" 
                               data-price="<?= $plan['price'] ?>" data-id="<?= $plan['id'] ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card bg-light mt-5 p-4 sticky-bottom shadow">
        <div class="d-flex justify-content-between align-items-center">
            <h3>جمع کل: <span id="total-price" class="text-primary">0</span> تومان</h3>
            <button class="btn btn-warning btn-lg px-5 font-bold" onclick="startPurchase()">ثبت‌نام و خرید نهایی</button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.module-check').forEach(el => {
    el.addEventListener('change', () => {
        let total = 0;
        document.querySelectorAll('.module-check:checked').forEach(c => total += parseFloat(c.dataset.price));
        document.getElementById('total-price').innerText = total.toLocaleString();
    });
});
function startPurchase() { window.location.href = '/auth/register'; }
</script>