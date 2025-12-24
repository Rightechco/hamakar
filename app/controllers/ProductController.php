<?php
// app/controllers/ProductController.php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Unit.php';
require_once __DIR__ . '/../models/Warehouse.php'; // ✅ اضافه کردن مدل انبار

class ProductController {
    private $productModel;
    private $categoryModel;
    private $unitModel;
    private $warehouseModel; // ✅ تعریف مدل انبار

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->unitModel = new Unit();
        $this->warehouseModel = new Warehouse(); // ✅ مقداردهی مدل انبار
    }

    public function create() {
        $categories = $this->categoryModel->getAll();
        $units = $this->unitModel->getAll();
        $warehouses = $this->warehouseModel->getAll(); // ✅ دریافت لیست انبارها
        
        view('admin/inventory/products/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'افزودن محصول جدید',
            'product' => null,
            'categories' => $categories,
            'units' => $units,
            'warehouses' => $warehouses // ✅ ارسال لیست انبارها به ویو
        ]);
    }

    public function edit($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            FlashMessage::set('message', 'محصول مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=products&action=index');
        }
        
        $categories = $this->categoryModel->getAll();
        $units = $this->unitModel->getAll();
        $warehouses = $this->warehouseModel->getAll(); // ✅ دریافت لیست انبارها
        
        view('admin/inventory/products/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش محصول',
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'warehouses' => $warehouses // ✅ ارسال لیست انبارها به ویو
        ]);
    }

    public function index() {
        $filters = [
            'search' => sanitize($_GET['search'] ?? null),
            'category_id' => sanitize($_GET['category_id'] ?? null),
        ];

        $products = $this->productModel->getFilteredProducts($filters);
        $categories = $this->categoryModel->getByType('product');

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($products);
            return;
        }

        view('admin/inventory/products/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت محصولات',
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'sku' => sanitize($_POST['sku']),
                'purchase_price' => sanitize($_POST['purchase_price']),
                'sale_price' => sanitize($_POST['sale_price']),
                'category_id' => sanitize($_POST['category_id']),
                'unit_id' => sanitize($_POST['unit_id']),
                'warehouse_id' => sanitize($_POST['warehouse_id']),
                'initial_inventory' => sanitize($_POST['initial_inventory']), // ✅ دریافت موجودی اولیه
                'description' => sanitize($_POST['description']),
                'image_path' => null,
            ];

            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                $uploadDir = 'public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
                    $data['image_path'] = $fileName;
                } else {
                    FlashMessage::set('message', 'خطا در آپلود تصویر محصول.', 'error');
                    redirect_back();
                }
            }

            if ($this->productModel->create($data)) {
                FlashMessage::set('message', 'محصول با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت محصول.', 'error');
            }
            redirect(APP_URL . '/index.php?page=products&action=index');
        }
    }

    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = $this->productModel->findById($id);
            if (!$product) {
                FlashMessage::set('message', 'محصول مورد نظر یافت نشد.', 'error');
                redirect(APP_URL . '/index.php?page=products&action=index');
                return;
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'sku' => sanitize($_POST['sku']),
                'category_id' => sanitize($_POST['category_id']),
                'purchase_price' => sanitize($_POST['purchase_price']),
                'sale_price' => sanitize($_POST['sale_price']),
                'description' => sanitize($_POST['description']),
                'unit_id' => sanitize($_POST['unit_id']), // ✅ استفاده از unit_id
                'image_path' => $product->image_path,
            ];
            
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                 $uploadDir = 'public/uploads/products/';
                 if (!is_dir($uploadDir)) {
                     mkdir($uploadDir, 0777, true);
                 }
                 $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
                 $uploadFile = $uploadDir . $fileName;
 
                 if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
                     if (!empty($product->image_path) && file_exists($uploadDir . $product->image_path)) {
                         unlink($uploadDir . $product->image_path);
                     }
                     $data['image_path'] = $fileName;
                 } else {
                     FlashMessage::set('message', 'خطا در آپلود تصویر محصول.', 'error');
                     redirect_back();
                 }
             }

            if ($this->productModel->update($id, $data)) {
                FlashMessage::set('message', 'محصول با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی محصول.', 'error');
            }
            redirect(APP_URL . '/index.php?page=products&action=index');
        }
    }

    public function delete($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            FlashMessage::set('message', 'محصول مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=products&action=index');
            return;
        }
        
        if ($this->productModel->delete($id)) {
            $uploadDir = 'public/uploads/products/';
            if (!empty($product->image_path) && file_exists($uploadDir . $product->image_path)) {
                unlink($uploadDir . $product->image_path);
            }
            FlashMessage::set('message', 'محصول با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف محصول.', 'error');
        }
        redirect(APP_URL . '/index.php?page=products&action=index');
    }
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
}
