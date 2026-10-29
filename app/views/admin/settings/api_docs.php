<?php
// app/views/admin/settings/api_docs.php - مستندات و راهنمای اتصال نرم‌افزارهای حسابداری (API)
$apiKey = $apiKey ?? null;
?>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#0f172a;">
                <i class="fas fa-code text-primary me-2"></i>مستندات و اتصال نرم‌افزارهای حسابداری (REST API)
            </h3>
            <span class="text-muted small">راهنمای یکپارچه‌سازی هماکار با سپیدار، هلو، شایگان و صدور خودکار فاکتورهای رسمی در سامانه مودیان</span>
        </div>
        <div>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=moadian_invoices" class="btn btn-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-shield-alt me-1"></i> مشاهده کارپوشه مودیان
            </a>
        </div>
    </div>

    <!-- API Key Notice Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h5 class="fw-bold mb-2 text-dark">
            <i class="fas fa-key text-warning me-2"></i>کلید امنیتی احراز هویت (API Authentication)
        </h5>
        <p class="text-muted small mb-3">
            برای ارسال درخواست به وب‌سرویس هماکار، این کلید را در هدر درخواست با نام <code>X-API-KEY</code> یا به صورت <code>Authorization: Bearer &lt;key&gt;</code> قرار دهید.
        </p>
        <div class="input-group" style="max-width:650px;">
            <input type="text" class="form-control font-monospace bg-light" id="api_key_field" value="<?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_live_acc_software_secret_key'); ?>" readonly>
            <button class="btn btn-outline-primary px-3" onclick="navigator.clipboard.writeText(document.getElementById('api_key_field').value); alert('کلید امنیتی کپی شد.');">
                <i class="far fa-copy me-1"></i> کپی کلید
            </button>
        </div>
    </div>

    <!-- Endpoints -->
    <div class="row g-4">
        <!-- Endpoint 1 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success px-2 py-1">GET</span>
                    <h5 class="fw-bold mb-0 font-monospace text-primary">/index.php?page=api&action=official_invoices</h5>
                </div>
                <p class="text-muted small mb-3">
                    دریافت لیست فاکتورهای رسمی تایید شده/در صف برای انتقال به نرم‌افزار حسابداری (سپیدار یا هلو).
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">پارامترهای ارسالی (Query Params):</label>
                        <ul class="small text-muted ps-3 mb-0">
                            <li><code>status</code>: وضعیت فاکتور (pending, success, canceled)</li>
                            <li><code>start_date</code>: فیلتر از تاریخ صدور (YYYY-MM-DD)</li>
                            <li><code>end_date</code>: فیلتر تا تاریخ صدور (YYYY-MM-DD)</li>
                            <li><code>limit</code>: تعداد رکوردها (پیش‌فرض: ۵۰)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">نمونه پاسخ موفق (HTTP 200):</label>
                        <pre class="bg-dark text-light p-3 rounded-3 font-monospace" style="direction:ltr; text-align:left; font-size:0.75rem; max-height:180px; overflow-y:auto;">
{
  "success": true,
  "count": 1,
  "data": [
    {
      "id": 42,
      "invoice_number": "INV-000042",
      "issue_date": "2024-10-29",
      "is_official": true,
      "customer": {
        "name": "شرکت داده‌پرداز",
        "economic_code": "411122334455",
        "national_id": "10109988776"
      },
      "financials": {
        "subtotal": 100000000,
        "vat_amount": 10000000,
        "total_amount": 110000000
      },
      "moadian": {
        "taxid": "A100HK0016B000000002A5",
        "status": "success",
        "uid": "TAX-UID-98AB..."
      }
    }
  ]
}
                        </pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Endpoint 2 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary px-2 py-1">POST</span>
                    <h5 class="fw-bold mb-0 font-monospace text-primary">/index.php?page=api&action=create_official_invoice</h5>
                </div>
                <p class="text-muted small mb-3">
                    ثبت فاکتور رسمی از طریق نرم‌افزار حسابداری در هماکار، تولید خودکار شناسه یکتای مالیاتی (TaxID) و ارسال مستقیم به کارپوشه مودیان.
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">نمونه بدنه ارسالی (JSON Body):</label>
                        <pre class="bg-dark text-light p-3 rounded-3 font-monospace" style="direction:ltr; text-align:left; font-size:0.75rem; max-height:220px; overflow-y:auto;">
{
  "invoice_number": "ACC-1403-8820",
  "customer_name": "شرکت گسترش فناوری پیشرو",
  "customer_economic_code": "411199887766",
  "customer_national_id": "10103344556",
  "customer_phone": "04134401179",
  "issue_date": "1403/08/10",
  "pattern_type": 1,
  "vat_rate": 10,
  "auto_send_moadian": true,
  "items": [
    {
      "product_name": "لایسنس نرم‌افزار سازمانی هماکار",
      "goods_code": "2710000138241",
      "quantity": 1,
      "unit_price": 50000000
    }
  ]
}
                        </pre>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">نمونه پاسخ موفق (HTTP 201):</label>
                        <pre class="bg-dark text-light p-3 rounded-3 font-monospace" style="direction:ltr; text-align:left; font-size:0.75rem; max-height:220px; overflow-y:auto;">
{
  "success": true,
  "message": "فاکتور رسمی با موفقیت ثبت و شناسه یکتای ۲۲ رقمی مالیاتی صادر شد.",
  "invoice_id": 43,
  "invoice_number": "ACC-1403-8820",
  "taxid": "A100HK0016B000000002B7",
  "uid": "TAX-UID-D84B9A7721",
  "status": "success",
  "financials": {
    "subtotal": 50000000,
    "vat_amount": 5000000,
    "total_amount": 55000000
  }
}
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
