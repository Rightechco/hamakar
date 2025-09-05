<?php
// app/controllers/PurchasesController.php

require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/JournalVoucher.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../lib/JalaliDate.php';
require_once __DIR__ . '/../core/Helpers.php';


class PurchasesController {
    private $auth;
    private $purchaseModel;
    private $productModel;
    private $clientModel;
    private $journalVoucherModel;
    private $journalEntryModel;
    private $accountModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->purchaseModel = new Purchase();
        $this->productModel = new Product();
        $this->clientModel = new Client();
        $this->journalVoucherModel = new JournalVoucher();
        $this->journalEntryModel = new JournalEntry();
        $this->accountModel = new Account();
    }

    public function index() {
        $purchases = $this->purchaseModel->getAllPurchases();
        view('admin/inventory/purchases/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت خریدها',
            'purchases' => $purchases
        ]);
    }

    public function create() {
        $products = $this->productModel->getAll();
        $vendors = $this->clientModel->getAllClients();
        view('admin/inventory/purchases/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت خرید جدید',
            'products' => $products,
            'vendors' => $vendors
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'vendor_id' => sanitize($_POST['vendor_id']),
                'purchase_date' => $this->convertJalaliToGregorianString($_POST['purchase_date']),
                'status' => 'pending',
                'items' => $_POST['items']
            ];
            
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }
            $data['total_amount'] = $totalAmount;
            
            $purchaseId = $this->purchaseModel->createPurchase($data);
            
            if ($purchaseId) {
                // ایجاد سند حسابداری پس از ثبت موفق فاکتور
                $this->sendPurchaseToAccounting($purchaseId, $data['purchase_date'], $totalAmount);
                
                FlashMessage::set('message', 'فاکتور خرید با موفقیت ثبت شد و سند آن صادر گردید.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور خرید.', 'error');
            }
            redirect(APP_URL . '/index.php?page=purchases&action=index');
        }
    }

    public function view($id) {
    $this->auth->restrict(['admin', 'accountant']);

    $purchase = $this->purchaseModel->findByIdWithVendor($id); // You will need to create this method in the Purchase model
    $items = $this->purchaseModel->getItemsByPurchaseId($id); // You will also need this method in the Purchase model

    if (!$purchase) {
        FlashMessage::set('message', 'فاکتور خرید مورد نظر یافت نشد.', 'error');
        redirect(APP_URL . '/index.php?page=purchases&action=index');
        return;
    }
    
    view('admin/inventory/purchases/view', [
        'layout' => 'admin_layout',
        'title' => 'مشاهده فاکتور خرید',
        'purchase' => $purchase,
        'items' => $items,
        'companyInfo' => [
            'name' => 'نرم افزاری محسن',
            'economicCode' => '44552244',
            'phone' => '44552244',
            'address' => 'تهران خیابان ولیعصر (عج)',
            'logo_path' => APP_URL . '/assets/img/mohesen-logo.webp', // Assuming you have a logo file
        ]
    ]);
}

// You will also need to add the following helper function if it's not already in your controller or a global helper file
private function convertJalaliToGregorianString($jalaliDate) {
    if (empty($jalaliDate)) return null;
    $englishDate = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $jalaliDate);
    list($j_year, $j_month, $j_day) = explode('/', $englishDate);
    $gregorian_array = jalali_to_gregorian((int)$j_year, (int)$j_month, (int)$j_day);
    return implode('-', $gregorian_array);
}


public function updateStatus($id) {
    $this->auth->restrict(['admin', 'accountant']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
        $status = sanitize($_POST['status']);
        $purchase = $this->purchaseModel->findById($id);

        if ($purchase) {
            // Only send to accounting if the status is changing to 'paid' or 'completed'
            if (($status === 'paid' || $status === 'completed') && $purchase->status !== 'paid' && $purchase->status !== 'completed') {
                // Corrected call: Pass the required three arguments from the $purchase object
                $this->sendPurchaseToAccounting($purchase->id, $purchase->purchase_date, $purchase->total_amount);
            }

            if ($this->purchaseModel->updateStatus($id, $status)) {
                FlashMessage::set('message', 'وضعیت فاکتور خرید با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی وضعیت فاکتور خرید.', 'error');
            }
        } else {
            FlashMessage::set('message', 'فاکتور خرید یافت نشد.', 'error');
        }
    }
    redirect(APP_URL . '/index.php?page=purchases&action=view&id=' . $id);
}

private function sendPurchaseToAccounting($purchaseId, $purchaseDate, $totalAmount) {
        $inventoryAccount = $this->accountModel->findByCode('AS-1201');
        $accountsPayable = $this->accountModel->findByCode('LI-2104');

        if (!$inventoryAccount || !$accountsPayable) {
            error_log("Accounting accounts not found for purchase transaction " . $purchaseId);
            return false;
        }

        $voucherId = $this->journalVoucherModel->create([
            'voucher_date' => $purchaseDate,
            'description' => 'سند خرید کالا به فاکتور شماره ' . $purchaseId,
            'user_id' => $this->auth->user()->id
        ]);
        if ($voucherId) {
            $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => $totalAmount, 'credit' => 0]);
            $this->journalEntryModel->create($voucherId, ['account_id' => $accountsPayable->id, 'debit' => 0, 'credit' => $totalAmount]);
            return true;
        }
        return false;
    }
    
}