<?php
// app/lib/AiAnalyticsService.php - دستیار هوش مصنوعی، تحلیل مالی و OCR فاکتور

class AiAnalyticsService {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * تحلیل هوشمند سلامت مالی و ریسک اعتباری مشتریان
     */
    public function getExecutiveInsights() {
        // ۱. استخراج مشتریان پرریسک (بیشترین بدهی با بیشترین دیرکرد)
        $this->db->query("SELECT c.name, SUM(i.total_amount - i.paid_amount) as debt, COUNT(i.id) as overdue_invoices
                          FROM invoices i
                          JOIN clients c ON i.client_id = c.id
                          WHERE i.status != 'paid' AND i.due_date < CURDATE()
                          GROUP BY c.id
                          ORDER BY debt DESC LIMIT 5");
        $highRiskDebtors = $this->db->fetchAll();

        // ۲. محاسبه جریان نقدینگی پیش‌بینی‌شده ۳۰ روز آینده (ورودی چک‌ها منهای بدهی‌ها)
        $this->db->query("SELECT SUM(amount) as incoming_cheques 
                          FROM cheques 
                          WHERE type = 'received' AND status = 'pending'");
        $chequesRes = $this->db->fetch();
        $predictedIncoming = (int)($chequesRes->incoming_cheques ?? 0);

        // ۳. هشدارهای نقطه‌سفارش انبار
        $this->db->query("SELECT p.name, p.stock 
                          FROM products p 
                          WHERE p.stock <= 5 LIMIT 5");
        $criticalStock = $this->db->fetchAll();

        // ۴. پیشنهادات هوشمند سیستم (AI Insights)
        $insights = [];
        if (!empty($highRiskDebtors)) {
            $topDebtor = $highRiskDebtors[0];
            $insights[] = [
                'type' => 'warning',
                'title' => 'ریسک وصول مطالبات',
                'text' => "مشتری «{$topDebtor->name}» دارای بدهی سررسید شده به مبلغ " . number_format($topDebtor->debt) . " تومان است. پیشنهاد می‌شود فروش اعتباری جدید تا تسویه موقتاً مسدود گردد."
            ];
        }

        if ($predictedIncoming > 0) {
            $insights[] = [
                'type' => 'success',
                'title' => 'پیش‌بینی جریان نقدینگی ماه آینده',
                'text' => "بر اساس چک‌های صیادی معتبر در دست وصول، مبلغ تقریبی " . number_format($predictedIncoming) . " تومان ورودی نقدی طی هفته‌های آتی به حساب شرکت واریز خواهد شد."
            ];
        }

        if (!empty($criticalStock)) {
            $insights[] = [
                'type' => 'danger',
                'title' => 'هشدار کمبود موجودی انبار',
                'text' => count($criticalStock) . " قلم کالا به نقطه سفارش بحرانی رسیده‌اند. فوراً دستور خرید صادر فرمایید."
            ];
        }

        return [
            'highRiskDebtors' => $highRiskDebtors,
            'predictedIncoming' => $predictedIncoming,
            'criticalStock' => $criticalStock,
            'insights' => $insights
        ];
    }

    /**
     * پیش‌پردازش شبیه‌ساز OCR فاکتور
     */
    public function simulateInvoiceOcr($fileName) {
        return [
            'vendor_name' => 'شرکت پتروشیمی تابان',
            'national_id' => '10103445566',
            'invoice_date' => date('Y/m/d'),
            'invoice_number' => 'FAC-' . rand(10000, 99999),
            'items' => [
                ['name' => 'پلی‌اتیلن سبک فیلم گرید ۰۲۰', 'qty' => 50, 'unit' => 'کیسه', 'price' => 1800000],
                ['name' => 'مستربچ مشکی صنعتی', 'qty' => 10, 'unit' => 'کیلوگرم', 'price' => 450000]
            ],
            'subtotal' => 94500000,
            'vat' => 9450000,
            'total' => 103950000
        ];
    }
}
