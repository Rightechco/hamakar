/**
 * Hamakar ERP - Universal Enterprise Persian Export Engine (Excel & PDF/Print)
 * سیستم خروجی اکسل هوشمند (RTL) و چاپ و تولید PDF رسمی با سربرگ، مهر و امضای شرکتی
 */

(function () {
    'use strict';

    window.HamakarExport = {
        companyInfo: {
            name: 'سامانه یکپارچه سازمانی هماکار ERP',
            subName: 'گزارش رسمی مالی و حسابداری',
            economicCode: '۱۴۰۰۹۱۵۶۳۲۱',
            nationalId: '۱۴۰۰۹۱۵۶۳۲۱',
            phone: '۰۴۱-۳۴۴۰۱۱۷۹'
        },

        // دریافت تاریخ شمسی جاری
        getTodayJalali: function () {
            try {
                if (typeof persianDate !== 'undefined') {
                    return new persianDate().format('YYYY/MM/DD');
                }
            } catch (e) {}
            var d = new Date();
            return d.toLocaleDateString('fa-IR');
        },

        /**
         * ۱. خروجی اکسل هوشمند راست‌به‌چپ (RTL Excel .xlsx)
         */
        toExcel: function (target, filename, title) {
            title = title || document.title || 'گزارش سامانه هماکار';
            filename = (filename || 'گزارش_هماکار') + '_' + this.getTodayJalali().replace(/[\/\s:]/g, '-');

            var tableEl = this._resolveTable(target);
            if (!tableEl) {
                alert('جدولی برای خروجی اکسل یافت نشد.');
                return;
            }

            if (typeof XLSX === 'undefined') {
                alert('کتابخانه XLSX در حال بارگذاری است، لطفاً چند لحظه بعد تلاش فرمایید.');
                return;
            }

            // استخراج داده‌ها از جدول با حذف ستون‌های عملیات
            var tableClone = tableEl.cloneNode(true);
            tableClone.querySelectorAll('.btn, button, .dropdown, th:last-child, td:last-child').forEach(function (el) {
                if (el.innerText.includes('عملیات') || el.closest('td:last-child') || el.closest('th:last-child')) {
                    el.remove();
                }
            });

            var ws = XLSX.utils.table_to_sheet(tableClone, { raw: false });

            // راست‌به‌چپ کردن شیت در اکسل
            if (!ws['!views']) ws['!views'] = [];
            ws['!views'].push({ RTL: true });

            // ساخت کتاب کار (Workbook)
            var wb = XLSX.utils.book_new();
            wb.Workbook = { Views: [{ RTL: true }] };
            XLSX.utils.book_append_sheet(wb, ws, 'گزارش مالی');

            // ذخیره و دانلود فایل اکسل واقعی
            XLSX.writeFile(wb, filename + '.xlsx');
        },

        /**
         * ۲. خروجی رسمی PDF با فونت فارسی وزیرمتن و صفحه A4
         */
        toPdf: function (target, filename, title, orientation) {
            title = title || document.title || 'گزارش رسمی مالی هماکار';
            filename = (filename || 'گزارش_رسمی') + '_' + this.getTodayJalali().replace(/[\/\s:]/g, '-');
            orientation = orientation || 'landscape';

            var contentEl = this._resolveContent(target);
            if (!contentEl) {
                alert('محتوایی برای تولید PDF یافت نشد.');
                return;
            }

            var printContainer = this._buildOfficialDocument(contentEl, title);
            document.body.appendChild(printContainer);

            if (typeof html2pdf !== 'undefined') {
                var opt = {
                    margin: [8, 8, 8, 8],
                    filename: filename + '.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, letterRendering: true },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: orientation }
                };

                html2pdf().set(opt).from(printContainer).save().then(function () {
                    document.body.removeChild(printContainer);
                }).catch(function (err) {
                    console.error('PDF generation error:', err);
                    document.body.removeChild(printContainer);
                    window.print();
                });
            } else {
                document.body.removeChild(printContainer);
                this.printOfficial(target, title, orientation);
            }
        },

        /**
         * ۳. چاپ مستقیم و استاندارد A4 (Official Print)
         */
        printOfficial: function (target, title, orientation) {
            title = title || document.title || 'گزارش رسمی هماکار';
            var contentEl = this._resolveContent(target);
            if (!contentEl) {
                window.print();
                return;
            }

            var printContainer = this._buildOfficialDocument(contentEl, title);
            printContainer.id = 'hamakar-active-print-doc';
            document.body.appendChild(printContainer);

            // استایل مخصوص چاپ
            var style = document.createElement('style');
            style.id = 'hamakar-print-styles';
            style.innerHTML = `
                @media print {
                    body > *:not(#hamakar-active-print-doc) {
                        display: none !important;
                    }
                    #hamakar-active-print-doc {
                        display: block !important;
                        position: absolute !important;
                        left: 0 !important;
                        top: 0 !important;
                        width: 100% !important;
                        margin: 0 !important;
                        padding: 10mm !important;
                        background: #fff !important;
                        box-sizing: border-box !important;
                    }
                    @page {
                        size: A4 ${orientation || 'landscape'};
                        margin: 8mm 6mm;
                    }
                }
            `;
            document.head.appendChild(style);

            setTimeout(function () {
                window.print();
                setTimeout(function () {
                    if (printContainer && printContainer.parentNode) {
                        printContainer.parentNode.removeChild(printContainer);
                    }
                    if (style && style.parentNode) {
                        style.parentNode.removeChild(style);
                    }
                }, 1000);
            }, 300);
        },

        // ساخت سربرگ و سند رسمی جهت چاپ و PDF
        _buildOfficialDocument: function (contentEl, title) {
            var container = document.createElement('div');
            container.style.direction = 'rtl';
            container.style.fontFamily = "'Vazirmatn', sans-serif";
            container.style.color = '#0f172a';
            container.style.background = '#ffffff';
            container.style.padding = '12px';
            container.style.width = '100%';
            container.style.boxSizing = 'border-box';

            var serialNo = 'HKR-' + Math.floor(100000 + Math.random() * 900000);
            var today = this.getTodayJalali();

            var headerHtml = `
                <div style="border: 1.5px solid #0f172a; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; background: #f8fafc;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 25%; text-align: right; vertical-align: middle;">
                                <div style="font-weight: 900; font-size: 1.15rem; color: #4f46e5;">هماکار ERP</div>
                                <div style="font-size: 0.72rem; color: #64748b;">سامانه یکپارچه مدیریت سازمانی</div>
                            </td>
                            <td style="width: 50%; text-align: center; vertical-align: middle;">
                                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 900; color: #0f172a;">${title}</h3>
                                <div style="font-size: 0.75rem; color: #475569; margin-top: 4px;">کد اقتصادی: ۱۴۰۰۹۱۵۶۳۲۱ | شناسه ملی: ۱۴۰۰۹۱۵۶۳۲۱</div>
                            </td>
                            <td style="width: 25%; text-align: left; vertical-align: middle; font-size: 0.75rem; line-height: 1.6;">
                                <div><strong>تاریخ گزارش:</strong> <span style="font-family:'Consolas',monospace;">${today}</span></div>
                                <div><strong>شماره پیگیری:</strong> <span style="font-family:'Consolas',monospace;">${serialNo}</span></div>
                                <div><strong>وضعیت:</strong> رسمی و معتبر</div>
                            </td>
                        </tr>
                    </table>
                </div>
            `;

            var clone = contentEl.cloneNode(true);
            clone.querySelectorAll('.btn, button, .dropdown, select, input, .pagination, td:last-child:has(button), th:last-child:has(button)').forEach(function (e) {
                if (e.innerText.includes('عملیات')) {
                    e.remove();
                }
            });

            // تنظیم استایل جداول در چاپ
            clone.querySelectorAll('table').forEach(function (tbl) {
                tbl.style.width = '100%';
                tbl.style.borderCollapse = 'collapse';
                tbl.style.fontSize = '0.78rem';
                tbl.style.marginBottom = '16px';
                tbl.querySelectorAll('th').forEach(function (th) {
                    th.style.border = '1px solid #cbd5e1';
                    th.style.padding = '8px 6px';
                    th.style.background = '#f1f5f9';
                    th.style.color = '#0f172a';
                    th.style.fontWeight = 'bold';
                    th.style.textAlign = 'center';
                });
                tbl.querySelectorAll('td').forEach(function (td) {
                    td.style.border = '1px solid #e2e8f0';
                    td.style.padding = '7px 6px';
                    td.style.textAlign = 'center';
                });
            });

            var footerHtml = `
                <div style="margin-top: 24px; border-top: 1.5px solid #cbd5e1; padding-top: 14px;">
                    <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 0.8rem;">
                        <tr>
                            <td style="width: 33.3%;">
                                <strong>تهیه‌کننده (امور مالی):</strong>
                                <div style="height: 50px; border-bottom: 1px dashed #cbd5e1; margin: 6px 20px;"></div>
                            </td>
                            <td style="width: 33.3%;">
                                <strong>تاییدکننده (مدیر مالی):</strong>
                                <div style="height: 50px; border-bottom: 1px dashed #cbd5e1; margin: 6px 20px;"></div>
                            </td>
                            <td style="width: 33.3%;">
                                <strong>تصویب‌کننده (مدیریت عامل):</strong>
                                <div style="height: 50px; border-bottom: 1px dashed #cbd5e1; margin: 6px 20px;"></div>
                            </td>
                        </tr>
                    </table>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px; font-size:0.68rem; color:#94a3b8; border-top:1px solid #f1f5f9; padding-top:6px;">
                        <span>تلفن تماس پشتیبانی: ۰۴۱-۳۴۴۰۱۱۷۹ | سامانه ابری هماکار</span>
                        <span>این سند رسمی از طریق سامانه یکپارچه هماکار صادر و دارای اصالت دیجیتال می‌باشد.</span>
                    </div>
                </div>
            `;

            container.innerHTML = headerHtml;
            container.appendChild(clone);
            container.insertAdjacentHTML('beforeend', footerHtml);

            return container;
        },

        _resolveTable: function (target) {
            if (typeof target === 'string') {
                return document.querySelector(target);
            }
            if (target && target.tagName === 'TABLE') {
                return target;
            }
            if (target && target.querySelector) {
                return target.querySelector('table');
            }
            return document.querySelector('main table') || document.querySelector('table');
        },

        _resolveContent: function (target) {
            if (typeof target === 'string') {
                return document.querySelector(target);
            }
            if (target && target.nodeType) {
                return target;
            }
            var tbl = document.querySelector('main .table-responsive') || document.querySelector('main table');
            if (tbl) return tbl.closest('.card') || tbl;
            return document.querySelector('main');
        },

        /**
         * تزریق خودکار نوار ابزار خروجی پرمیوم روی جداول گزارشات
         */
        autoAttachToolbars: function () {
            // پیدا کردن تمام جداول یا کارت‌های گزارش در صفحات حسابداری
            var isReportPage = window.location.href.includes('accounting') ||
                               window.location.href.includes('invoices') ||
                               window.location.href.includes('moadian') ||
                               window.location.href.includes('treasury') ||
                               window.location.href.includes('reports');

            if (!isReportPage) return;

            document.querySelectorAll('.card-header, .table-responsive, table').forEach(function (el) {
                var card = el.closest('.card, .moadian-card, .card-modern');
                if (card && !card.querySelector('.hamakar-export-toolbar')) {
                    var header = card.querySelector('.card-header, .d-flex:has(h5), .d-flex:has(h6), .d-flex:has(h4)');
                    if (header && !header.querySelector('.hamakar-export-toolbar')) {
                        var pageTitle = (document.querySelector('h3, h4, h2')?.innerText || 'گزارش مالی').trim();
                        var toolbar = document.createElement('div');
                        toolbar.className = 'hamakar-export-toolbar d-flex align-items-center gap-1.5 flex-wrap ms-auto';
                        toolbar.innerHTML = `
                            <button type="button" class="btn btn-sm btn-outline-success py-1 px-2.5 rounded-pill shadow-xs fw-bold" onclick="HamakarExport.toExcel(this.closest('.card, .moadian-card'), '${pageTitle}', '${pageTitle}')" title="خروجی اکسل کامل (XLSX)">
                                <i class="fas fa-file-excel text-success me-1"></i> اکسل
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5 rounded-pill shadow-xs fw-bold" onclick="HamakarExport.toPdf(this.closest('.card, .moadian-card'), '${pageTitle}', '${pageTitle}')" title="تولید و دانلود فایل PDF">
                                <i class="fas fa-file-pdf text-danger me-1"></i> PDF
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-pill shadow-xs fw-bold" onclick="HamakarExport.printOfficial(this.closest('.card, .moadian-card'), '${pageTitle}')" title="چاپ رسمی A4 با سربرگ">
                                <i class="fas fa-print text-primary me-1"></i> چاپ A4
                            </button>
                        `;
                        header.appendChild(toolbar);
                    }
                }
            });
        }
    };

    // اجرای خودکار پس از بارگذاری کامل صفحه
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            HamakarExport.autoAttachToolbars();
        }, 300);
    });

    $(document).ajaxComplete(function () {
        setTimeout(function () {
            HamakarExport.autoAttachToolbars();
        }, 300);
    });
})();
