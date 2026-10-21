<?php
// app/controllers/PurchasesController.php

require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/JournalVoucher.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../models/Warehouse.php'; 
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
    private $warehouseModel; 

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->purchaseModel = new Purchase();
        $this->productModel = new Product();
        $this->clientModel = new Client();
        $this->journalVoucherModel = new JournalVoucher();
        $this->journalEntryModel = new JournalEntry();
        $this->accountModel = new Account();
        $this->warehouseModel = new Warehouse(); 
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
        $vendors = $this->clientModel->getAllVendors(); 
        $warehouses = $this->warehouseModel->getAll(); 
        
        view('admin/inventory/purchases/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت خرید جدید',
            'products' => $products,
            'vendors' => $vendors,
            'warehouses' => $warehouses
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // ✅ اطمینان از مقداردهی عددی برای vat_rate
            $vatRate = (float)sanitize($_POST['vat_rate'] ?? 0);
            if ($vatRate === 0 && !empty($_POST['vat_rate'])) {
                 $vatRate = (float)filter_var($_POST['vat_rate'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? 0;
            }
            if ($vatRate === 0 && empty($_POST['vat_rate'])) {
                 $vatRate = 9; // مقدار پیش‌فرض
            }
            
            $data = [
                'vendor_id' => sanitize($_POST['vendor_id']),
                'purchase_date' => $this->convertJalaliToGregorianString($_POST['purchase_date']),
                'status' => 'pending',
                'items' => $_POST['items'],
                'vat_rate' => $vatRate, // ✅ استفاده از مقدار تمیز شده
                'warehouse_id' => sanitize($_POST['warehouse_id'] ?? null), 
                'user_id' => $this->auth->user()->id, 
            ];
            
            // اگر انبار انتخاب نشده بود، از اولین انبار موجود استفاده کن
            if (empty($data['warehouse_id'])) {
                $warehouses = $this->warehouseModel->getAll();
                if (!empty($warehouses)) {
                    $data['warehouse_id'] = $warehouses[0]->id;
                }
            }

            $subtotal = 0;
            if (is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $subtotal += (float)$item['quantity'] * (float)$item['price'];
                }
            }
            
            $vatAmount = ($subtotal * $vatRate) / 100;
            $totalAmount = $subtotal + $vatAmount;
            
            $data['subtotal'] = $subtotal;
            $data['vat_amount'] = $vatAmount;
            $data['total_amount'] = $totalAmount;
            
            $purchaseId = $this->purchaseModel->createPurchase($data);
            
            if ($purchaseId) {
                FlashMessage::set('message', 'فاکتور خرید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور خرید. ممکن است مشکل دیتابیس یا تراکنش وجود داشته باشد.', 'error');
            }
            redirect(APP_URL . '/index.php?page=purchases&action=index');
        }
    }

    public function view($id) {
        $this->auth->restrict(['admin', 'accountant']);

        $purchase = $this->purchaseModel->findByIdWithVendor($id); 
        $items = $this->purchaseModel->getItemsByPurchaseId($id); 

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
                'name' => 'سامانه یکپارچه هماکار',
                'economicCode' => '44552244',
                'phone' => '34401179',
                'address' => 'تبریز خیابان ولیعصر (عج)',
                'logo_path' => APP_URL . '/assets/img/mohesen-logo.webp', 
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
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            $status = sanitize($_POST['status']);
            $purchase = $this->purchaseModel->findById($id);

            if ($purchase) {
                // اگر وضعیت به 'paid' یا 'completed' تغییر کرد و قبلاً صادر نشده بود، سند را صادر کن
                if (($status === 'paid' || $status === 'completed') && $purchase->status !== 'paid' && $purchase->status !== 'completed') {
                    $this->sendPurchaseToAccounting($purchase->id, $purchase->purchase_date, $purchase->subtotal, $purchase->vat_amount);
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

    private function sendPurchaseToAccounting($purchaseId, $purchaseDate, $subtotal, $vatAmount) {
        $inventoryAccount = $this->accountModel->findByCode('AS-1201');
        $vatPayable = $this->accountModel->findByCode('LI-2103'); 
        $accountsPayable = $this->accountModel->findByCode('LI-2104');
        $totalAmount = $subtotal + $vatAmount;

        if (!$inventoryAccount || !$accountsPayable || !$vatPayable) {
            error_log("Accounting accounts not found for purchase transaction " . $purchaseId);
            FlashMessage::set('message', 'خطای حسابداری: حساب‌های سیستمی خرید (موجودی کالا، مالیات پرداختنی یا حساب‌های پرداختنی) در کدینگ حسابداری یافت نشدند.', 'error');
            return false;
        }

        $voucherId = $this->journalVoucherModel->create([
            'voucher_date' => $purchaseDate,
            'description' => 'سند خرید کالا به فاکتور شماره ' . $purchaseId,
            'user_id' => $this->auth->user()->id
        ]);
        if ($voucherId) {
            $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => $subtotal, 'credit' => 0]);
            $this->journalEntryModel->create($voucherId, ['account_id' => $vatPayable->id, 'debit' => $vatAmount, 'credit' => 0]);
            $this->journalEntryModel->create($voucherId, ['account_id' => $accountsPayable->id, 'debit' => 0, 'credit' => $totalAmount]);
            return true;
        }
        return false;
    }
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['id'])) {
            $purchaseId = sanitize($id);
            
            if ($this->purchaseModel->deletePurchase($purchaseId)) {
                FlashMessage::set('message', "فاکتور خرید شماره #{$purchaseId} با موفقیت حذف شد و موجودی انبار اصلاح گردید.");
            } else {
                FlashMessage::set('message', "خطا در حذف فاکتور خرید شماره #{$purchaseId}.", 'error');
            }
        }

        redirect(APP_URL . '/index.php?page=purchases&action=index');
    }
}
