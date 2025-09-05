<?php
// app/models/FinancialReport.php

class FinancialReport {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * تراز آزمایشی دو ستونی را برای یک بازه زمانی مشخص برمی‌گرداند.
     */
    public function getTrialBalance($startDate, $endDate) {
        $sql = "
            SELECT
                a.code,
                a.name,
                SUM(je.debit) as total_debit,
                SUM(je.credit) as total_credit
            FROM journal_entries je
            JOIN journal_vouchers jv ON je.voucher_id = jv.id
            JOIN accounts a ON je.account_id = a.id
            WHERE jv.voucher_date BETWEEN :start_date AND :end_date
            GROUP BY je.account_id
            ORDER BY a.code
        ";
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->fetchAll();
    }
    
    /**
     * محاسبات لازم برای صورت سود و زیان را انجام می‌دهد.
     */
    public function getProfitAndLossData($startDate, $endDate) {
        $sql = "
            SELECT
                a.type,
                a.name,
                SUM(je.credit - je.debit) as balance  -- درآمدها ماهیت بستانکار و هزینه‌ها بدهکار دارند
            FROM journal_entries je
            JOIN journal_vouchers jv ON je.voucher_id = jv.id
            JOIN accounts a ON je.account_id = a.id
            WHERE jv.voucher_date BETWEEN :start_date AND :end_date
              AND a.type IN ('income', 'expense')
            GROUP BY je.account_id
            ORDER BY a.type, a.name
        ";
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->fetchAll();
    }
    

public function getBalanceSheetData($asOfDate) {
    // محاسبه مانده حساب‌ها از ابتدای فعالیت تا تاریخ مورد نظر
    $sql = "
        SELECT
            a.type,
            a.name,
            SUM(je.debit - je.credit) as balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        JOIN accounts a ON je.account_id = a.id
        WHERE jv.voucher_date <= :as_of_date
          AND a.type IN ('asset', 'liability', 'equity')
        GROUP BY je.account_id
        HAVING balance != 0
        ORDER BY a.type, a.name
    ";
    
    $this->db->query($sql);
    $this->db->bind(':as_of_date', $asOfDate);
    return $this->db->fetchAll();
}
    
public function getAccountLedger($accountId, $startDate, $endDate) {
    // ۱. محاسبه مانده اولیه (مانده از قبل)
    $this->db->query("
        SELECT SUM(je.debit - je.credit) as opening_balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id = :account_id AND jv.voucher_date < :start_date
    ");
    $this->db->bind(':account_id', $accountId);
    $this->db->bind(':start_date', $startDate);
    $openingBalanceRow = $this->db->fetch();
    $openingBalance = $openingBalanceRow ? (float)$openingBalanceRow->opening_balance : 0;

    // ۲. دریافت تراکنش‌های دوره انتخاب شده
    $this->db->query("
        SELECT jv.voucher_date, jv.description, je.debit, je.credit
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id = :account_id AND jv.voucher_date BETWEEN :start_date AND :end_date
        ORDER BY jv.voucher_date, jv.id
    ");
    $this->db->bind(':account_id', $accountId);
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $transactions = $this->db->fetchAll();

    return [
        'opening_balance' => $openingBalance,
        'transactions' => $transactions
    ];
}

public function getGeneralJournal($startDate, $endDate) {
    // ۱. ابتدا تمام اسناد در بازه زمانی مورد نظر را پیدا می‌کنیم
    $this->db->query("
        SELECT id, voucher_date, description
        FROM journal_vouchers
        WHERE voucher_date BETWEEN :start_date AND :end_date
        ORDER BY voucher_date, id
    ");
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $vouchers = $this->db->fetchAll();

    // ۲. برای هر سند، آرتیکل‌های آن را واکشی می‌کنیم
    foreach ($vouchers as $voucher) {
        $this->db->query("
            SELECT je.debit, je.credit, a.code as account_code, a.name as account_name
            FROM journal_entries je
            JOIN accounts a ON je.account_id = a.id
            WHERE je.voucher_id = :voucher_id
        ");
        $this->db->bind(':voucher_id', $voucher->id);
        $voucher->entries = $this->db->fetchAll();
    }
    
    return $vouchers;
}

public function getVatReportData($startDate, $endDate) {
    // محاسبه کل مالیات بر ارزش افزوده فروش (بستانکاری مالیاتی)
    $this->db->query("SELECT SUM(vat_amount) as total_sales_vat FROM invoices WHERE issue_date BETWEEN :start_date AND :end_date AND status != 'canceled'");
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $salesVat = $this->db->fetch()->total_sales_vat ?? 0;

    // محاسبه کل مالیات بر ارزش افزوده خرید (بدهکاری مالیاتی یا اعتبار)
    $this->db->query("SELECT SUM(vat_amount) as total_purchase_vat FROM expenses WHERE expense_date BETWEEN :start_date AND :end_date");
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $purchaseVat = $this->db->fetch()->total_purchase_vat ?? 0;

    return [
        'sales_vat' => (float)$salesVat,
        'purchase_vat' => (float)$purchaseVat,
        'payable_vat' => (float)$salesVat - (float)$purchaseVat
    ];
}

public function getClosingEntriesData($fiscalYearEndDate) {
    $fiscalYearStartDate = date('Y-01-01', strtotime($fiscalYearEndDate)); // فرض سال مالی از ابتدای سال میلادی

    // ۱. دریافت مانده تمام حساب‌های درآمدی
    $this->db->query("
        SELECT account_id, SUM(credit - debit) as balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        JOIN accounts a ON je.account_id = a.id
        WHERE jv.voucher_date BETWEEN :start_date AND :end_date AND a.type = 'income'
        GROUP BY je.account_id
        HAVING balance != 0
    ");
    $this->db->bind(':start_date', $fiscalYearStartDate);
    $this->db->bind(':end_date', $fiscalYearEndDate);
    $incomeAccounts = $this->db->fetchAll();

    // ۲. دریافت مانده تمام حساب‌های هزینه‌ای
    $this->db->query("
        SELECT account_id, SUM(debit - credit) as balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        JOIN accounts a ON je.account_id = a.id
        WHERE jv.voucher_date BETWEEN :start_date AND :end_date AND a.type = 'expense'
        GROUP BY je.account_id
        HAVING balance != 0
    ");
    $this->db->bind(':start_date', $fiscalYearStartDate);
    $this->db->bind(':end_date', $fiscalYearEndDate);
    $expenseAccounts = $this->db->fetchAll();

    return [
        'income_accounts' => $incomeAccounts,
        'expense_accounts' => $expenseAccounts
    ];
}

    public function getSubledger($accountId, $entityType, $entityId, $startDate, $endDate) {
        
        // ۱. محاسبه مانده اولیه (مانده از قبل) برای این شخص/حساب خاص
        $this->db->query("
            SELECT SUM(je.debit - je.credit) as opening_balance
            FROM journal_entries je
            JOIN journal_vouchers jv ON je.voucher_id = jv.id
            WHERE je.account_id = :account_id 
              AND je.entity_type = :entity_type 
              AND je.entity_id = :entity_id 
              AND jv.voucher_date < :start_date
        ");
        $this->db->bind(':account_id', $accountId);
        $this->db->bind(':entity_type', $entityType);
        $this->db->bind(':entity_id', $entityId);
        $this->db->bind(':start_date', $startDate);
        $openingBalanceRow = $this->db->fetch();
        $openingBalance = $openingBalanceRow ? (float)$openingBalanceRow->opening_balance : 0;

        // ۲. دریافت تراکنش‌های دوره انتخاب شده برای این شخص/حساب خاص
        $this->db->query("
            SELECT jv.voucher_date, jv.description, je.debit, je.credit
            FROM journal_entries je
            JOIN journal_vouchers jv ON je.voucher_id = jv.id
            WHERE je.account_id = :account_id 
              AND je.entity_type = :entity_type 
              AND je.entity_id = :entity_id 
              AND jv.voucher_date BETWEEN :start_date AND :end_date
            ORDER BY jv.voucher_date, jv.id
        ");
        $this->db->bind(':account_id', $accountId);
        $this->db->bind(':entity_type', $entityType);
        $this->db->bind(':entity_id', $entityId);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $transactions = $this->db->fetchAll();

        // ۳. برگرداندن نتیجه نهایی
        return [
            'opening_balance' => $openingBalance,
            'transactions' => $transactions
        ];
    }
    
public function getBudgetVsActualReport($year, $month) {
    // استفاده از LEFT JOIN برای اینکه حتی حساب‌هایی که هزینه واقعی نداشته‌اند نیز در گزارش بیایند
    $sql = "
        SELECT 
            b.account_id,
            a.name AS account_name,
            b.budget_amount,
            IFNULL(actual.actual_amount, 0) AS actual_amount,
            (b.budget_amount - IFNULL(actual.actual_amount, 0)) AS variance
        FROM 
            budgets b
        JOIN 
            accounts a ON b.account_id = a.id
        LEFT JOIN (
            -- محاسبه هزینه واقعی برای هر حساب در دوره مشخص
            SELECT 
                je.account_id,
                SUM(je.debit) AS actual_amount
            FROM 
                journal_entries je
            JOIN 
                journal_vouchers jv ON je.voucher_id = jv.id
            WHERE 
                YEAR(jv.voucher_date) = :year AND MONTH(jv.voucher_date) = :month
            GROUP BY 
                je.account_id
        ) AS actual ON b.account_id = actual.account_id
        WHERE 
            b.period_year = :year AND b.period_month = :month
    ";
    
    $this->db->query($sql);
    $this->db->bind(':year', $year);
    $this->db->bind(':month', $month);
    
    return $this->db->fetchAll();
}

public function getUnreconciledTransactions($accountId, $statementDate) {
    $this->db->query("
        SELECT je.id, jv.voucher_date, jv.description, je.debit, je.credit
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id = :account_id 
          AND je.reconciliation_id IS NULL
          AND jv.voucher_date <= :statement_date
        ORDER BY jv.voucher_date, jv.id
    ");
    $this->db->bind(':account_id', $accountId);
    $this->db->bind(':statement_date', $statementDate);
    return $this->db->fetchAll();
}

public function getCashFlowData($startDate, $endDate) {
    // ۱. ابتدا ID تمام حساب‌های نقدی و بانکی (نوع دارایی) را پیدا می‌کنیم
    // این یک روش ساده است. در سیستم واقعی ممکن است نیاز به تفکیک بیشتری باشد.
    $this->db->query("SELECT id FROM accounts WHERE type = 'asset' AND (name LIKE '%بانک%' OR name LIKE '%صندوق%')");
    $cashAccountRows = $this->db->fetchAll();
    if (empty($cashAccountRows)) {
        return ['opening_balance' => 0, 'transactions' => []]; // اگر حساب بانکی/نقدی تعریف نشده باشد
    }
    $cashAccountIds = array_column($cashAccountRows, 'id');
    $placeholders = implode(',', array_fill(0, count($cashAccountIds), '?'));

    // ۲. مانده اولیه حساب‌های نقدی
    $this->db->query("
        SELECT SUM(je.debit - je.credit) as opening_balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id IN ($placeholders) AND jv.voucher_date < ?
    ");
    $openingBalanceRow = $this->db->fetch(array_merge($cashAccountIds, [$startDate]));
    $openingBalance = $openingBalanceRow ? (float)$openingBalanceRow->opening_balance : 0;

    // ۳. تمام تراکنش‌های نقدی در دوره زمانی مشخص
    $this->db->query("
        SELECT jv.voucher_date, jv.description, je.debit, je.credit
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id IN ($placeholders) AND jv.voucher_date BETWEEN ? AND ?
        ORDER BY jv.voucher_date, jv.id
    ");
    $transactions = $this->db->fetchAll(array_merge($cashAccountIds, [$startDate, $endDate]));
    
    return [
        'opening_balance' => $openingBalance,
        'transactions' => $transactions
    ];
}

public function getExpenseAnalysis($startDate, $endDate) {
    $sql = "
        SELECT 
            a.name AS account_name,
            SUM(je.debit) as total_expense
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        JOIN accounts a ON je.account_id = a.id
        WHERE jv.voucher_date BETWEEN :start_date AND :end_date
          AND a.type = 'expense'
        GROUP BY je.account_id
        HAVING total_expense > 0
        ORDER BY total_expense DESC
    ";
    $this->db->query($sql);
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    return $this->db->fetchAll();
}
 
public function getFullProfitAndLossReport($startDate, $endDate) {
    // ۱. درآمدها و هزینه‌ها (بدون COGS)
    $this->db->query("
        SELECT a.type, a.name, SUM(je.credit - je.debit) as balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        JOIN accounts a ON je.account_id = a.id
        WHERE jv.voucher_date BETWEEN :start_date AND :end_date
          AND a.type IN ('income', 'expense') AND a.code != 'EX-5103'
        GROUP BY je.account_id
        ORDER BY a.type, a.name
    ");
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $pnlData = $this->db->fetchAll();

    // ۲. بهای تمام‌شده کالای فروش‌رفته (COGS)
    $sql_cogs = "
        SELECT SUM(je.debit) as total_cogs
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        JOIN accounts a ON je.account_id = a.id
        WHERE jv.voucher_date BETWEEN :start_date AND :end_date
          AND a.code = 'EX-5103'
    ";
    $this->db->query($sql_cogs);
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $total_cogs = $this->db->fetch()->total_cogs ?? 0;
    
    $totalIncome = 0;
    $totalExpenses = 0;
    $incomes = [];
    $expenses = [];

    foreach ($pnlData as $item) {
        if ($item->type === 'income') {
            $incomes[] = $item;
            $totalIncome += $item->balance;
        } else {
            $item->balance = abs($item->balance);
            $expenses[] = $item;
            $totalExpenses += $item->balance;
        }
    }

    // محاسبه سود ناخالص و خالص
    $grossProfit = $totalIncome - $total_cogs;
    $netProfit = $grossProfit - $totalExpenses;

    return [
        'incomes' => $incomes,
        'totalIncome' => $totalIncome,
        'totalCogs' => $total_cogs,
        'grossProfit' => $grossProfit,
        'expenses' => $expenses,
        'totalExpenses' => $totalExpenses,
        'netProfit' => $netProfit,
    ];
}

}