<?php
// app/views/admin/ai/analytics.php - دستیار هوش مصنوعی و تحلیل مالی سازمانی هماکار
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-brain text-primary me-2"></i>دستیار هوش مصنوعی و تحلیل مالی سازمانی
            </h3>
            <span class="text-muted small">پردازش و آنالیز داده‌های ERP، کشف ناهنجاری‌های مالی، پیش‌بینی نقدینگی و اسکنر هوشمند فاکتور (OCR)</span>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 font-monospace">
            <i class="fas fa-sparkles me-1"></i> Hamakar AI Core Active
        </span>
    </div>

    <!-- AI Diagnostic Insights -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-wand-magic-sparkles text-primary me-2"></i>هشدارهای استراتژیک و پیشنهادهای هوشمند</h5>
        </div>
        <?php foreach ($insights['insights'] as $ins): ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-<?php echo $ins['type']; ?>">
                    <h6 class="fw-bold text-<?php echo $ins['type']; ?> mb-2">
                        <i class="fas fa-circle-exclamation me-1"></i> <?php echo $ins['title']; ?>
                    </h6>
                    <p class="text-secondary small mb-0 lh-base"><?php echo $ins['text']; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-4">
        <!-- Debtor Risk Analysis -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">مشتریان با بالاترین ریسک اعتباری و بدهی معوق</h6>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">اولویت پیگیری حقوقی</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>نام مشتری</th>
                                <th>تعداد فاکتور معوق</th>
                                <th>میزان بدهی سررسید شده</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($insights['highRiskDebtors'])): ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">مشتری با ریسک بحرانی شناسایی نشد.</td></tr>
                            <?php else: ?>
                                <?php foreach ($insights['highRiskDebtors'] as $d): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($d->name); ?></td>
                                        <td class="font-monospace text-center"><?php echo $d->overdue_invoices; ?></td>
                                        <td class="font-monospace text-danger fw-bold"><?php echo number_format($d->debt); ?> تومان</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Intelligent OCR Invoice Scanner -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-qrcode text-primary me-2"></i>اسکنر و OCR هوشمند فاکتورهای خرید</h6>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?page=admin&action=ai_analytics" method="POST" enctype="multipart/form-data" class="mb-3">
                        <label class="form-label fw-bold small text-secondary">آپلود عکس فاکتور فیزیکی یا PDF فاکتور خرید:</label>
                        <div class="input-group">
                            <input type="file" name="invoice_scan" required class="form-control rounded-start-3" accept="image/*,.pdf">
                            <button type="submit" class="btn btn-primary rounded-end-3 px-3">
                                <i class="fas fa-bolt me-1"></i> پردازش OCR
                            </button>
                        </div>
                    </form>

                    <?php if ($ocrResult): ?>
                        <div class="p-3 rounded-4 mt-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <h6 class="fw-bold text-success mb-2"><i class="fas fa-check-circle me-1"></i> استخراج خودکار اقلام فاکتور با هوش مصنوعی:</h6>
                            <div class="small mb-1"><strong>فروشنده:</strong> <?php echo $ocrResult['vendor_name']; ?> (شناسه ملی: <?php echo $ocrResult['national_id']; ?>)</div>
                            <div class="small mb-2"><strong>شماره و تاریخ فاکتور:</strong> <?php echo $ocrResult['invoice_number']; ?> - <?php echo $ocrResult['invoice_date']; ?></div>
                            <ul class="list-group list-group-flush small mb-2">
                                <?php foreach ($ocrResult['items'] as $it): ?>
                                    <li class="list-group-item bg-transparent px-0 py-1 d-flex justify-content-between">
                                        <span><?php echo $it['name']; ?> (<?php echo $it['qty'] . ' ' . $it['unit']; ?>)</span>
                                        <strong class="font-monospace"><?php echo number_format($it['price'] * $it['qty']); ?> ت</strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span class="fw-bold text-dark">جمع نهایی با مالیات:</span>
                                <strong class="text-primary font-monospace"><?php echo number_format($ocrResult['total']); ?> تومان</strong>
                            </div>
                            <a href="index.php?page=purchases&action=create" class="btn btn-sm btn-outline-success rounded-3 mt-3 w-100">
                                <i class="fas fa-file-import me-1"></i> تبدیل مستقیم به فاکتور خرید سیستم
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-file-image fa-2x mb-2 d-block text-secondary text-opacity-50"></i>
                            <small>با بارگذاری تصویر فاکتور کاغذی، هوش مصنوعی هماکار اقلام، قیمت‌ها و مالیات را بدون نیاز به تایپ دستی استخراج می‌نماید.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
