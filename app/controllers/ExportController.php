<?php
// app/controllers/ExportController.php - کنترلر جامع خروجی‌های رسمی اکسل و چاپی A4 هماکار ERP

class ExportController {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant', 'manager']);
    }

    /**
     * خروجی اکسل با انکودینگ استاندارد UTF-8 BOM جهت باز شدن بدون نقص در اکسل ویندوز
     */
    public function excel() {
        $type = sanitize($_GET['type'] ?? 'invoices');
        $filename = "گزارش_" . $type . "_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // اضافه کردن بایت اوردر مارک UTF-8 BOM جهت نمایش صحیح حروف فارسی در مایکروسافت اکسل
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        switch ($type) {
            case 'moadian':
                $this->exportMoadianExcel($out);
                break;
            case 'cheques':
                $this->exportChequesExcel($out);
                break;
            case 'journal':
                $this->exportJournalExcel($out);
                break;
            case 'vat':
                $this->exportVatExcel($out);
                break;
            case 'invoices':
            default:
                $this->exportInvoicesExcel($out);
                break;
        }

        fclose($out);
        exit();
    }

    private function exportInvoicesExcel($out) {
        fputcsv($out, ['سامانه سازمانی هماکار ERP - گزارش فاکتورهای فروش و خرید']);
        fputcsv($out, ['تاریخ تهیه: ' . date('Y/m/d H:i') . ' | کاربر: ' . ($this->auth->user()->name ?? 'مدیر')]);
        fputcsv($out, []); // سطر خالی
        fputcsv($out, ['ردیف', 'شماره فاکتور', 'مشتری / طرف حساب', 'تاریخ صدور', 'مبلغ کل (تومان)', 'وضعیت', 'نوع فاکتور', 'رسمیت مودیان', 'شناسه یکتای مالیاتی (TaxID)']);

        require_once __DIR__ . '/../models/Invoice.php';
        $invoiceModel = new Invoice();
        $invoices = $invoiceModel->getAllCombinedInvoices();

        $i = 1;
        foreach ($invoices as $inv) {
            $isOfficial = !empty($inv->is_official) ? 'رسمی (سامانه مودیان)' : 'غیررسمی (داخلی)';
            $taxid = $inv->moadian_taxid ?? '---';
            $num = $inv->invoice_number ?: ('FAC-' . $inv->id);
            fputcsv($out, [
                $i++,
                $num,
                $inv->client_name ?? 'مشتری آزاد',
                $inv->date,
                number_format($inv->total_amount),
                $inv->status,
                $inv->type === 'sale' ? 'فروش' : ($inv->type === 'purchase' ? 'خرید' : 'خدمات'),
                $isOfficial,
                $taxid
            ]);
        }
    }

    private function exportMoadianExcel($out) {
        fputcsv($out, ['کارپوشه سامانه مودیان مالیاتی کشور - هماکار ERP']);
        fputcsv($out, ['شناسه حافظه مالیاتی: A100HK | کد اقتصادی: ۱۴۰۰۹۱۵۶۳۲۱']);
        fputcsv($out, []);
        fputcsv($out, ['ردیف', 'شماره داخلی', 'شناسه یکتای ۲۲ رقمی (TaxID)', 'کد اقتصادی خریدار', 'شناسه ملی', 'مبلغ ناخالص (تومان)', 'ارزش افزوده ۱۰٪', 'مبلغ نهایی', 'وضعیت کارپوشه', 'کد پیگیری UID']);

        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $invoices = $moadianModel->getAll(5000);

        $i = 1;
        foreach ($invoices as $inv) {
            fputcsv($out, [
                $i++,
                $inv->inno,
                $inv->taxid,
                $inv->customer_economic_code ?: '---',
                $inv->customer_national_id ?: '---',
                number_format($inv->total_amount),
                number_format($inv->total_vat),
                number_format($inv->final_amount),
                $inv->status === 'success' ? 'تایید نهایی دارایی' : ($inv->status === 'pending' ? 'در انتظار ارسال' : 'ابطال / خطا'),
                $inv->uid ?: '---'
            ]);
        }
    }

    private function exportChequesExcel($out) {
        fputcsv($out, ['خزانه‌داری و مدیریت اسناد تجاری و چک‌های صیادی - هماکار ERP']);
        fputcsv($out, []);
        fputcsv($out, ['ردیف', 'نوع سند', 'شناسه صیادی (۱۶ رقم)', 'شماره سریال چک', 'بانک صادرکننده', 'صاحب حساب / مشتری', 'مبلغ چک (تومان)', 'تاریخ سررسید', 'وضعیت']);

        $this->db->query("SELECT c.*, cl.name AS client_name FROM cheques c LEFT JOIN clients cl ON c.client_id = cl.id ORDER BY c.due_date ASC");
        $cheques = $this->db->fetchAll();

        $i = 1;
        foreach ($cheques as $ch) {
            fputcsv($out, [
                $i++,
                $ch->type === 'received' ? 'چک دریافتی' : 'چک پرداختی',
                $ch->sayad_number ?? '---',
                $ch->cheque_number ?? '---',
                $ch->bank_name ?? '---',
                $ch->client_name ?? '---',
                number_format($ch->amount ?? 0),
                $ch->due_date ?? '---',
                $ch->status ?? 'در جریان'
            ]);
        }
    }

    private function exportJournalExcel($out) {
        fputcsv($out, ['گزارش رسمی دفتر روزنامه - هماکار ERP']);
        fputcsv($out, []);
        fputcsv($out, ['شماره سند', 'تاریخ سند', 'شرح سند', 'کد حساب', 'عنوان حساب', 'مبلغ بدهکار (تومان)', 'مبلغ بستانکار (تومان)']);

        $this->db->query("SELECT v.id AS voucher_id, v.voucher_date, v.description AS voucher_desc,
                                 ve.account_id, a.name AS account_name, a.code AS account_code,
                                 ve.debit, ve.credit
                          FROM vouchers v
                          JOIN voucher_entries ve ON v.id = ve.voucher_id
                          LEFT JOIN accounts a ON ve.account_id = a.id
                          ORDER BY v.voucher_date ASC, v.id ASC");
        $entries = $this->db->fetchAll();

        foreach ($entries as $e) {
            fputcsv($out, [
                $e->voucher_id,
                $e->voucher_date,
                $e->voucher_desc,
                $e->account_code ?: '---',
                $e->account_name ?: '---',
                number_format($e->debit ?? 0),
                number_format($e->credit ?? 0)
            ]);
        }
    }

    private function exportVatExcel($out) {
        fputcsv($out, ['گزارش رسمی مالیات بر ارزش افزوده و فصلی مودیان - هماکار ERP']);
        fputcsv($out, ['نرخ قانونی مالیات بر ارزش افزوده: ۱۰ درصد']);
        fputcsv($out, []);
        fputcsv($out, ['شماره فاکتور', 'تاریخ', 'طرف حساب', 'مبلغ خالص فروش', 'ارزش افزوده فروش', 'مبلغ کل فروش']);

        $this->db->query("SELECT * FROM invoices WHERE status != 'canceled' ORDER BY issue_date DESC");
        $invoices = $this->db->fetchAll();

        foreach ($invoices as $inv) {
            fputcsv($out, [
                $inv->invoice_number,
                $inv->issue_date,
                $inv->client_id,
                number_format($inv->subtotal ?? 0),
                number_format($inv->vat_amount ?? 0),
                number_format($inv->total_amount ?? 0)
            ]);
        }
    }
}
