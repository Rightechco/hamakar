<?php
// app/controllers/SalesController.php

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/JournalVoucher.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../lib/JalaliDate.php';
require_once __DIR__ . '/../core/Helpers.php';


class SalesController {
    private $auth;
    private $saleModel;
    private $productModel;
    private $clientModel;
    private $journalVoucherModel;
    private $journalEntryModel;
    private $accountModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->saleModel = new Sale();
        $this->productModel = new Product();
        $this->clientModel = new Client();
        $this->journalVoucherModel = new JournalVoucher();
        $this->journalEntryModel = new JournalEntry();
        $this->accountModel = new Account();
    }

    public function index() {
        $sales = $this->saleModel->getAllSales();
        view('admin/inventory/sales/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت فروش‌ها',
            'sales' => $sales
        ]);
    }

    public function create() {
        $products = $this->productModel->getAll();
        $clients = $this->clientModel->getAllClients();
        view('admin/inventory/sales/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت فروش جدید',
            'products' => $products,
            'clients' => $clients
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => sanitize($_POST['client_id']),
                'sale_date' => $this->convertJalaliToGregorianString($_POST['sale_date']),
                'status' => 'pending',
                'items' => $_POST['items']
            ];
            
            $totalAmount = 0;
            $cogsAmount = 0;
            foreach ($data['items'] as $item) {
                $product = $this->productModel->findById($item['product_id']);
                $totalAmount += $item['quantity'] * $item['price'];
                $cogsAmount += $item['quantity'] * $product->purchase_price;
            }
            $data['total_amount'] = $totalAmount;
            
            $saleId = $this->saleModel->createSale($data);
            
            if ($saleId) {
                // ایجاد سند حسابداری پس از ثبت موفق فاکتور
                $this->sendSaleToAccounting($saleId, $data['sale_date'], $totalAmount, $cogsAmount);

                FlashMessage::set('message', 'فاکتور فروش با موفقیت ثبت شد و سند آن صادر گردید.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور فروش.', 'error');
            }
            redirect(APP_URL . '/index.php?page=sales&action=index');
        }
    }
    public function view($id) {
        $sale = $this->saleModel->findByIdWithClient($id);
        $items = $this->saleModel->getItemsBySaleId($id);

        if (!$sale) {
            FlashMessage::set('message', 'فاکتور فروش مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=sales&action=index');
            return;
        }

        view('admin/inventory/sales/view', [
            'layout' => 'admin_layout',
            'title' => 'مشاهده فاکتور فروش',
            'sale' => $sale,
            'items' => $items,
            'companyInfo' => [
                'name' => 'نرم افزاری محسن',
                'economicCode' => '۴۴۵۵۲۲۴۴',
                'phone' => '۴۴۵۵۲۲۴۴',
                'address' => 'تهران خیابان ولیعصر (عج)',
            ]
        ]);
    }

    
    
    private function convertJalaliToGregorianString($jalaliDate) {
        if (empty($jalaliDate)) return null;
        $englishDate = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $jalaliDate);
        list($j_year, $j_month, $j_day) = explode('/', $englishDate);
        $gregorian_array = jalali_to_gregorian((int)$j_year, (int)$j_month, (int)$j_day);
        return implode('-', $gregorian_array);
    }
   

public function updateStatus($id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
        $status = sanitize($_POST['status']);
        $sale = $this->saleModel->findById($id);

        if ($sale) {
            // Only send to accounting if the status is changing to 'completed'
            if ($status === 'completed' && $sale->status !== 'completed') {
                // Corrected call: Pass the required four arguments from the $sale object
                // Note: The recalculateCogsForSale method is needed here to get the cogsAmount
                $cogsAmount = $this->recalculateCogsForSale($sale->id);
                $this->sendSaleToAccounting($sale->id, $sale->sale_date, $sale->total_amount, $cogsAmount);
            }

            if ($this->saleModel->updateStatus($id, $status)) {
                FlashMessage::set('message', 'وضعیت فاکتور فروش با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی وضعیت فاکتور فروش.', 'error');
            }
        } else {
            FlashMessage::set('message', 'فاکتور فروش یافت نشد.', 'error');
        }
        redirect(APP_URL . '/index.php?page=sales&action=view&id=' . $id);
    }
}

private function sendSaleToAccounting($saleId, $saleDate, $totalAmount, $cogsAmount) {
    $accountsReceivable = $this->accountModel->findByCode('AR-1103');
    $salesRevenue = $this->accountModel->findByCode('SR-4101');
    $cogsAccount = $this->accountModel->findByCode('EX-5103');
    $inventoryAccount = $this->accountModel->findByCode('AS-1201');

    if (!$accountsReceivable || !$salesRevenue || !$cogsAccount || !$inventoryAccount) {
        error_log("Accounting accounts not found for sales transaction " . $saleId);
        return false;
    }
    
    $voucherId = $this->journalVoucherModel->create([
        'voucher_date' => $saleDate,
        'description' => 'سند فروش کالا به فاکتور شماره ' . $saleId,
        'user_id' => $this->auth->user()->id
    ]);

    if ($voucherId) {
        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
        $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $totalAmount]);
        $this->journalEntryModel->create($voucherId, ['account_id' => $cogsAccount->id, 'debit' => $cogsAmount, 'credit' => 0]);
        $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $cogsAmount]);
        return true;
    }
    return false;
}

private function recalculateCogsForSale($saleId) {
    $items = $this->saleModel->getItemsBySaleId($saleId);
    $cogs = 0;
    foreach ($items as $item) {
        $product = $this->productModel->findById($item->product_id);
        if ($product) { // Ensure product exists before trying to access purchase_price
            $cogs += $item->quantity * $product->purchase_price;
        }
    }
    return $cogs;
}
}