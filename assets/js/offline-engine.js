/**
 * Hamakar ERP - Advanced Offline Engine & Desktop PWA Synchronizer
 * پشتیبانی کامل از کارکرد آفلاین، کش محلی IndexedDB و همگام‌سازی دوطرفه پس از اتصال اینترنت
 */

(function () {
    'use strict';

    const DB_NAME = 'HamakarERP_LocalDB';
    const DB_VERSION = 1;
    let db = null;
    let isOnline = navigator.onLine;
    let deferredInstallPrompt = null;

    // ۱. مقداردهی اولیه IndexedDB
    function initIndexedDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (event) {
                const db = event.target.result;
                if (!db.objectStoreNames.contains('pending_invoices')) {
                    const invoiceStore = db.createObjectStore('pending_invoices', { keyPath: 'local_id', autoIncrement: true });
                    invoiceStore.createIndex('created_at', 'created_at', { unique: false });
                }
                if (!db.objectStoreNames.contains('offline_logs')) {
                    db.createObjectStore('offline_logs', { keyPath: 'id', autoIncrement: true });
                }
            };

            request.onsuccess = function (event) {
                db = event.target.result;
                console.log('[Hamakar Offline] IndexedDB initialized successfully.');
                updateSyncBadge();
                resolve(db);
            };

            request.onerror = function (event) {
                console.error('[Hamakar Offline] IndexedDB failed to open:', event.target.errorCode);
                reject(event.target.errorCode);
            };
        });
    }

    // ذخیره فاکتور در صف محلی
    window.saveInvoiceLocally = function (invoiceData) {
        return new Promise((resolve, reject) => {
            if (!db) {
                reject('دیتابیس محلی هنوز آماده نشده است.');
                return;
            }
            const tx = db.transaction(['pending_invoices'], 'readwrite');
            const store = tx.objectStore('pending_invoices');
            invoiceData.created_at = new Date().toISOString();
            invoiceData.synced = 0;

            const req = store.add(invoiceData);
            req.onsuccess = function () {
                console.log('[Hamakar Offline] Invoice saved locally:', invoiceData);
                updateSyncBadge();
                showNotification('فاکتور در حافظه محلی سیستم ذخیره شد. به محض اتصال به اینترنت همگام‌سازی خواهد شد.', 'info');
                resolve(req.result);
            };
            req.onerror = function (e) {
                reject(e);
            };
        });
    };

    // دریافت تعداد موارد در انتظار سینک
    function getPendingCount() {
        return new Promise((resolve) => {
            if (!db) return resolve(0);
            const tx = db.transaction(['pending_invoices'], 'readonly');
            const store = tx.objectStore('pending_invoices');
            const countReq = store.count();
            countReq.onsuccess = function () {
                resolve(countReq.result);
            };
            countReq.onerror = function () {
                resolve(0);
            };
        });
    }

    // بروزرسانی بج گرافیکی سینک در صفحه
    async function updateSyncBadge() {
        const count = await getPendingCount();
        const badge = document.getElementById('offline-sync-count');
        const statusIcon = document.getElementById('network-status-icon');
        const statusText = document.getElementById('network-status-text');

        if (badge) {
            badge.innerText = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }

        if (statusIcon && statusText) {
            if (isOnline) {
                statusIcon.className = 'fas fa-circle text-success';
                statusText.innerText = count > 0 ? `آنلاین (${count} عملیات در صف)` : 'آنلاین (سرور متصل)';
            } else {
                statusIcon.className = 'fas fa-circle text-danger';
                statusText.innerText = `حالت آفلاین (${count} در صف محلی)`;
            }
        }
    }

    // ۲. همگام‌سازی خودکار داده‌های محلی با سرور ابری
    window.triggerOfflineSync = async function () {
        if (!navigator.onLine) {
            showNotification('دستگاه شما هم‌اکنون به اینترنت متصل نیست. همگام‌سازی امکان‌پذیر نمی‌باشد.', 'warning');
            return;
        }

        if (!db) return;

        const tx = db.transaction(['pending_invoices'], 'readonly');
        const store = tx.objectStore('pending_invoices');
        const getAllReq = store.getAll();

        getAllReq.onsuccess = async function () {
            const items = getAllReq.result;
            if (!items || items.length === 0) {
                showNotification('تمامی داده‌ها همگام هستند. موردی در صف محلی وجود ندارد.', 'success');
                updateSyncBadge();
                return;
            }

            const btn = document.getElementById('btn-manual-sync');
            if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> در حال سینک...';

            try {
                const response = await fetch('index.php?page=api&action=sync_offline_data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': 'hmk_ca6806f53a4007f7d910e4f519a48af2'
                    },
                    body: JSON.stringify({ items: items })
                });

                const res = await response.json();
                if (res.success) {
                    // پاکسازی رکوردهای سینک‌شده از IndexedDB
                    const clearTx = db.transaction(['pending_invoices'], 'readwrite');
                    clearTx.objectStore('pending_invoices').clear();
                    clearTx.oncomplete = function () {
                        updateSyncBadge();
                        showNotification(`همگام‌سازی موفق: ${items.length} فاکتور با موفقیت در پایگاه داده مرکزی ثبت گردید.`, 'success');
                    };
                } else {
                    showNotification('خطا در همگام‌سازی: ' + (res.message || 'پاسخ نامعتبر سرور'), 'error');
                }
            } catch (err) {
                console.error('[Hamakar Offline] Sync error:', err);
                showNotification('خطا در ارتباط با سرور هنگام همگام‌سازی.', 'error');
            } finally {
                if (btn) btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> سینک فوری';
            }
        };
    };

    // ۳. مدیریت رویدادهای شبکه (آنلاین / آفلاین)
    window.addEventListener('online', () => {
        isOnline = true;
        updateSyncBadge();
        showNotification('اتصال اینترنت برقرار شد. در حال بررسی و همگام‌سازی خودکار داده‌های محلی...', 'info');
        triggerOfflineSync();
    });

    window.addEventListener('offline', () => {
        isOnline = false;
        updateSyncBadge();
        showNotification('اتصال اینترنت قطع شد. سامانه به صورت خودکار به حالت کارکرد آفلاین محلی سوئیچ گردید.', 'warning');
    });

    // ۴. مدیریت نصب نسخه دسکتاپ ویندوز (PWA Desktop Install)
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredInstallPrompt = e;
        const installBtn = document.getElementById('btn-install-desktop-app');
        if (installBtn) {
            installBtn.style.display = 'inline-flex';
        }
    });

    window.installHamakarDesktopApp = function () {
        if (deferredInstallPrompt) {
            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[Hamakar PWA] User accepted desktop installation');
                    showNotification('نرم‌افزار هماکار با موفقیت به عنوان اپلیکیشن ویندوز نصب شد.', 'success');
                }
                deferredInstallPrompt = null;
            });
        } else {
            alert('برای نصب نسخه دسکتاپ هماکار در مرورگر کروم یا مایکروسافت اج:\nروی آیکون نصب (Install) در انتهای نوار آدرس کلیک فرمایید.');
        }
    };

    // توابع نمایش نوتیفیکیشن اختصاصی
    function showNotification(msg, type = 'info') {
        const toast = document.createElement('div');
        const bgColors = {
            'info': '#0284c7',
            'success': '#16a34a',
            'warning': '#d97706',
            'error': '#dc2626'
        };
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.left = '20px';
        toast.style.zIndex = '9999';
        toast.style.background = bgColors[type] || '#334155';
        toast.style.color = '#ffffff';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '12px';
        toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
        toast.style.fontSize = '0.85rem';
        toast.style.fontWeight = 'bold';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.maxWidth = '400px';
        toast.style.direction = 'rtl';
        toast.innerHTML = `<i class="fas fa-info-circle"></i> <span>${msg}</span>`;

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    // راه‌اندازی پس از بارگذاری DOM
    document.addEventListener('DOMContentLoaded', () => {
        initIndexedDB();
        updateSyncBadge();

        // ثبت Service Worker برای کش آفلاین در صورت پشتیبانی مرورگر
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').then((reg) => {
                console.log('[Hamakar PWA] Service Worker registered:', reg.scope);
            }).catch((err) => {
                console.warn('[Hamakar PWA] Service Worker registration skipped:', err);
            });
        }
    });

})();
