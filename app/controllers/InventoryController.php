<?php
// app/controllers/InventoryController.php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Unit.php';
require_once __DIR__ . '/../models/Warehouse.php';
require_once __DIR__ . '/../models/InventoryLog.php';
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../lib/JalaliDate.php';
require_once __DIR__ . '/../lib/numberToWords.php';

class InventoryController {
    private $auth;
    private $unitModel;
    private $warehouseModel;
    private $inventoryLogModel;
    private $productModel;
    private $purchaseModel;
    private $saleModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant', 'inventory_manager']);
        $this->unitModel = new Unit();
        $this->warehouseModel = new Warehouse();
        $this->inventoryLogModel = new InventoryLog();
        $this->productModel = new Product();
        $this->purchaseModel = new Purchase();
        $this->saleModel = new Sale();
    }
    
    // متدهای مربوط به واحدها (Units)
    public function units() {
        $units = $this->unitModel->getAll();
        view('admin/inventory/units/index', ['units' => $units, 'layout' => 'admin_layout']);
    }
    public function createUnit() {
        view('admin/inventory/units/create_edit', ['unit' => null, 'layout' => 'admin_layout']);
    }
    public function storeUnit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            if (!empty($name) && $this->unitModel->create(['name' => $name])) {
                FlashMessage::set('message', 'واحد جدید با موفقیت ایجاد شد.');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد واحد. ممکن است نام تکراری باشد.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=inventory&action=units');
    }
    public function editUnit($id) {
        $unit = $this->unitModel->findById($id);
        if (!$unit) {
            FlashMessage::set('message', 'واحد مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=inventory&action=units');
        }
        view('admin/inventory/units/create_edit', ['unit' => $unit, 'layout' => 'admin_layout']);
    }
    public function updateUnit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            if (!empty($name) && $this->unitModel->update($id, ['name' => $name])) {
                FlashMessage::set('message', 'واحد با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی واحد.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=inventory&action=units');
    }
    public function deleteUnit($id) {
        if ($this->unitModel->delete($id)) {
            FlashMessage::set('message', 'واحد با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف واحد.', 'error');
        }
        redirect(APP_URL . '/index.php?page=inventory&action=units');
    }


    // متدهای مربوط به انبارها (Warehouses)
    public function warehouses() {
        $warehouses = $this->warehouseModel->getAll();
        view('admin/inventory/warehouses/index', ['warehouses' => $warehouses, 'layout' => 'admin_layout']);
    }
    public function createWarehouse() {
        view('admin/inventory/warehouses/create_edit', ['warehouse' => null, 'layout' => 'admin_layout']);
    }
    public function storeWarehouse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['name' => sanitize($_POST['name']), 'location' => sanitize($_POST['location'])];
            if ($this->warehouseModel->create($data)) {
                FlashMessage::set('message', 'انبار جدید با موفقیت ایجاد شد.');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد انبار.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=inventory&action=warehouses');
    }
    public function editWarehouse($id) {
        $warehouse = $this->warehouseModel->findById($id);
        if (!$warehouse) {
            FlashMessage::set('message', 'انبار مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=inventory&action=warehouses');
        }
        view('admin/inventory/warehouses/create_edit', ['warehouse' => $warehouse, 'layout' => 'admin_layout']);
    }
    public function updateWarehouse($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['name' => sanitize($_POST['name']), 'location' => sanitize($_POST['location'])];
            if ($this->warehouseModel->update($id, $data)) {
                FlashMessage::set('message', 'انبار با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی انبار.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=inventory&action=warehouses');
    }
    public function deleteWarehouse($id) {
        if ($this->warehouseModel->delete($id)) {
            FlashMessage::set('message', 'انبار با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف انبار.', 'error');
        }
        redirect(APP_URL . '/index.php?page=inventory&action=warehouses');
    }
    
    // متد ثبت ورودی انبار
    public function logIn() {
        // ... (در اینجا باید فاکتور خرید را با لاگ انبار مرتبط کنید)
    }
    
    // متد ثبت خروجی انبار
    public function logOut() {
        // ... (در اینجا باید فاکتور فروش را با لاگ انبار مرتبط کنید)
    }

    /**
     * نمایش گزارش موجودی یک انبار خاص
     */
    public function warehouseReport() {
        $warehouses = $this->warehouseModel->getAll();
        
        $reportData = [];
        $selectedWarehouseId = sanitize($_GET['warehouse_id'] ?? null);
        
        if ($selectedWarehouseId) {
            $reportData = $this->inventoryLogModel->getDetailedReportByWarehouse($selectedWarehouseId);
        }

        view('admin/inventory/reports/warehouse_report', [
            'layout' => 'admin_layout',
            'title' => 'گزارش موجودی انبار',
            'warehouses' => $warehouses,
            'reportData' => $reportData,
            'selectedWarehouseId' => $selectedWarehouseId
        ]);
    }
    
    /**
     * نمایش گزارش گردش یک محصول خاص
     */
    public function productFlowReport($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            FlashMessage::set('message', 'محصول مورد نظر یافت نشد.', 'error');
            redirect_back();
        }
        
        $logs = $this->inventoryLogModel->getLogsByProduct($id);
        
        view('admin/inventory/reports/product_flow_report', [
            'layout' => 'admin_layout',
            'title' => 'گزارش گردش محصول: ' . $product->name,
            'product' => $product,
            'logs' => $logs
        ]);
    }

    /**
     * چاپ رسید خرید
     */
    public function printPurchaseReceipt($purchaseId) {
        $purchase = $this->purchaseModel->findByIdWithVendor($purchaseId);
        if (!$purchase) {
            FlashMessage::set('message', 'فاکتور خرید یافت نشد.', 'error');
            redirect_back();
        }
        $items = $this->purchaseModel->getItemsByPurchaseId($purchaseId);

        view('admin/inventory/receipts/purchase_receipt', [
            'layout' => 'print_layout', 
            'title' => 'رسید خرید',
            'purchase' => $purchase,
            'items' => $items
        ]);
    }

    /**
     * چاپ رسید فروش
     */
    public function printSaleReceipt($saleId) {
        $sale = $this->saleModel->findByIdWithClient($saleId);
        if (!$sale) {
            FlashMessage::set('message', 'فاکتور فروش یافت نشد.', 'error');
            redirect_back();
        }
        $items = $this->saleModel->getItemsBySaleId($saleId);

        view('admin/inventory/receipts/sale_receipt', [
            'layout' => 'print_layout',
            'title' => 'رسید فروش',
            'sale' => $sale,
            'items' => $items
        ]);
    }
}