<?php
// app/views/shared/offline_sync_widget.php - ابزارک هوشمند وضعیت شبکه، کارکرد آفلاین و نصب دسکتاپ
?>
<div id="hamakar-offline-widget" style="position:fixed; bottom:18px; left:20px; z-index:1050; display:flex; align-items:center; gap:8px; background:rgba(255, 255, 255, 0.95); backdrop-filter:blur(12px); border:1px solid rgba(226, 232, 240, 0.9); padding:6px 14px; border-radius:30px; box-shadow:0 6px 20px rgba(0,0,0,0.08); font-size:0.8rem; font-weight:600; direction:rtl;">
    <!-- وضعیت شبکه -->
    <div class="d-flex align-items-center gap-2">
        <i id="network-status-icon" class="fas fa-circle text-success" style="font-size:0.65rem;"></i>
        <span id="network-status-text" class="text-dark" style="font-size:0.75rem;">آنلاین (سرور متصل)</span>
        <span id="offline-sync-count" class="badge bg-danger rounded-pill" style="display:none; font-size:0.65rem;">0</span>
    </div>

    <!-- دکمه همگام‌سازی دستی -->
    <button id="btn-manual-sync" type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-pill" onclick="window.triggerOfflineSync()" title="همگام‌سازی دستی داده‌های آفلاین" style="font-size:0.72rem;">
        <i class="fas fa-sync-alt text-primary"></i> سینک
    </button>

    <!-- دکمه نصب اپلیکیشن دسکتاپ / ویندوز -->
    <button id="btn-install-desktop-app" type="button" class="btn btn-sm btn-primary py-1 px-2 rounded-pill shadow-sm" onclick="window.installHamakarDesktopApp()" style="display:none; font-size:0.72rem;">
        <i class="fab fa-windows me-1"></i> نصب دسکتاپ
    </button>
</div>

<script src="<?php echo APP_URL; ?>/assets/js/offline-engine.js"></script>
