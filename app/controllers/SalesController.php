<?php
// app/controllers/SalesController.php

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/JournalVoucher.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../models/Warehouse.php'; // ✅ بارگذاری مدل انبار
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
    private $warehouseModel; // ✅ تعریف ویژگی

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->saleModel = new Sale();
        $this->productModel = new Product();
        $this->clientModel = new Client();
        $this->journalVoucherModel = new JournalVoucher();
        $this->journalEntryModel = new JournalEntry();
        $this->accountModel = new Account();
        $this->warehouseModel = new Warehouse(); // ✅ مقداردهی اولیه
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
        $clients = $this->clientModel->getAllCustomers(); 
        $warehouses = $this->warehouseModel->getAll(); // اکنون بدون خطا اجرا می شود.

        view('admin/inventory/sales/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت فروش جدید',
            'products' => $products,
            'clients' => $clients,
            'warehouses' => $warehouses
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // ✅ اصلاح مقداردهی vat_rate و اطمینان از عدد بودن آن (از طریق sanitize در تابع)
            $vatRate = (float)sanitize($_POST['vat_rate'] ?? 9);
            
            $data = [
                'client_id' => sanitize($_POST['client_id']),
                'sale_date' => $this->convertJalaliToGregorianString($_POST['sale_date']),
                'status' => 'pending',
                'items' => $_POST['items'],
                'vat_rate' => $vatRate,
                'warehouse_id' => sanitize($_POST['warehouse_id'] ?? null), // ✅ استفاده از ??
                'user_id' => $this->auth->user()->id, 
            ];
            
            // ✅ منطق جبران: اگر انبار خالی بود، از اولین انبار استفاده کن
            if (empty($data['warehouse_id']) || $data['warehouse_id'] === 'null') {
                $warehouses = $this->warehouseModel->getAll();
                if (!empty($warehouses)) {
                    $data['warehouse_id'] = $warehouses[0]->id;
                } else {
                    FlashMessage::set('message', 'خطا: هیچ انباری در سیستم تعریف نشده است. لطفا ابتدا یک انبار ایجاد کنید.', 'error');
                    redirect(APP_URL . '/index.php?page=sales&action=create');
                    return;
                }
            }

            $subtotal = 0;
            $cogsAmount = 0;
            
            // محاسبه مبالغ
            if (is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $product = $this->productModel->findById($item['product_id']);
                    $subtotal += (float)$item['quantity'] * (float)$item['price'];
                    if ($product) {
                        $cogsAmount += (float)$item['quantity'] * (float)$product->purchase_price;
                    }
                }
            }
            
            $vatAmount = ($subtotal * $data['vat_rate']) / 100;
            $totalAmount = $subtotal + $vatAmount;
            
            $data['subtotal'] = $subtotal;
            $data['vat_amount'] = $vatAmount;
            $data['total_amount'] = $totalAmount;
            
            $saleId = $this->saleModel->createSale($data);
            
            if ($saleId) {
                // $this->sendSaleToAccounting(...) در متد updateStatus فراخوانی می‌شود
                FlashMessage::set('message', 'فاکتور فروش با موفقیت ثبت شد. پس از تکمیل شدن وضعیت، سند حسابداری آن صادر خواهد شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور فروش.', 'error');
            }
            redirect(APP_URL . '/index.php?page=sales&action=index');
        }
    }
    
    public function view($id) {
        $this->auth->restrict(['admin', 'accountant']);
    
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            $status = sanitize($_POST['status']);
            $sale = $this->saleModel->findById($id);

            if ($sale) {
                // اگر وضعیت به 'completed' تغییر کرد و قبلاً تکمیل نشده بود، سند را صادر کن
                if ($status === 'completed' && $sale->status !== 'completed' && $sale->status !== 'paid') {
                    $cogsAmount = $this->recalculateCogsForSale($sale->id);
                    $totalAmount = $sale->subtotal + $sale->vat_amount;
                    $this->sendSaleToAccounting($sale->id, $sale->sale_date, $sale->subtotal, $sale->vat_amount, $totalAmount, $cogsAmount);
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

    private function sendSaleToAccounting($saleId, $saleDate, $subtotal, $vatAmount, $totalAmount, $cogsAmount) {
        $accountsReceivable = $this->accountModel->findByCode('AR-1103');
        $salesRevenue = $this->accountModel->findByCode('SR-4101');
        $vatPayable = $this->accountModel->findByCode('LI-2103'); 
        $cogsAccount = $this->accountModel->findByCode('EX-5103');
        $inventoryAccount = $this->accountModel->findByCode('AS-1201');

        if (!$accountsReceivable || !$salesRevenue || !$vatPayable || !$cogsAccount || !$inventoryAccount) {
            error_log("Accounting accounts not found for sales transaction " . $saleId);
            FlashMessage::set('message', 'خطای حسابداری: حساب‌های سیستمی فروش (دریافتنی، درآمد، بهای تمام شده، موجودی یا مالیات پرداختنی) در کدینگ یافت نشدند.', 'error');
            return false;
        }
        
        $voucherId = $this->journalVoucherModel->create([
            'voucher_date' => $saleDate,
            'description' => 'سند فروش کالا به فاکتور شماره ' . $saleId,
            'user_id' => $this->auth->user()->id
        ]);

        if ($voucherId) {
            // ۱. ثبت درآمد و مالیات (فاکتور فروش)
            $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
            $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $subtotal]);
            $this->journalEntryModel->create($voucherId, ['account_id' => $vatPayable->id, 'debit' => 0, 'credit' => $vatAmount]);
            
            // ۲. ثبت بهای تمام شده کالای فروش رفته (COGS)
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
            if ($product) { 
                $cogs += $item->quantity * $product->purchase_price;
            }
        }
        return $cogs;
    }
}
