<?php
// app/controllers/ProductController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php'; // ✅ Import Category model for dropdowns
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/Helpers.php';
class ProductController {
    private $auth;
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->productModel = new Product();
        $this->categoryModel = new Category(); // ✅ Instantiate Category model
    }

    public function index() {
        $filters = [
            'search' => sanitize($_GET['search'] ?? null),
            'category_id' => sanitize($_GET['category_id'] ?? null),
        ];

        $products = $this->productModel->getFilteredProducts($filters);
        $categories = $this->categoryModel->getAll();

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

    public function create() {
        $categories = $this->categoryModel->getAll(); // ✅ Fetch all categories
        view('admin/inventory/products/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'افزودن محصول جدید',
            'product' => null,
            'categories' => $categories
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'sku' => sanitize($_POST['sku']),
                'category_id' => sanitize($_POST['category_id']),
                'purchase_price' => sanitize($_POST['purchase_price']),
                'sale_price' => sanitize($_POST['sale_price']),
                'description' => sanitize($_POST['description']),
                'unit' => sanitize($_POST['unit']),
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

    public function edit($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            FlashMessage::set('message', 'محصول مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=products&action=index');
            return;
        }
        $categories = $this->categoryModel->getAll(); // ✅ Fetch all categories
        view('admin/inventory/products/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش محصول',
            'product' => $product,
            'categories' => $categories
        ]);
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
                'unit' => sanitize($_POST['unit']),
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
            // Delete product image file
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
}