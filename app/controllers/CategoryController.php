<?php
// app/controllers/CategoryController.php - نسخه نهایی و اصلاح شده

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../core/Helpers.php'; // اطمینان از وجود Helpers

class CategoryController {
    private $auth;
    private $categoryModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin']);
        $this->categoryModel = new Category();
    }

    public function index() {
        $filters = [
            'search' => sanitize($_GET['search'] ?? null),
            'type' => sanitize($_GET['type'] ?? null)
        ];
        
        $categories = $this->categoryModel->getFilteredCategories($filters); // ✅ فراخوانی متد جدید
        
        view('admin/inventory/categories/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت دسته‌بندی‌ها',
            'categories' => $categories,
            'filters' => $filters
        ]);
    }
    
    public function create() {
        view('admin/inventory/categories/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'افزودن دسته‌بندی جدید',
            'category' => null
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'type' => sanitize($_POST['type'] ?? 'product'),
                'description' => sanitize($_POST['description'] ?? '')
            ];
            
            if ($this->categoryModel->create($data)) {
                FlashMessage::set('message', 'دسته‌بندی با موفقیت ایجاد شد.');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد دسته‌بندی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=categories&action=index');
        }
    }

    public function edit($id) {
        $category = $this->categoryModel->findById($id);
        if (!$category) {
            FlashMessage::set('message', 'دسته‌بندی مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=categories&action=index');
        }
        view('admin/inventory/categories/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش دسته‌بندی',
            'category' => $category
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'type' => sanitize($_POST['type'] ?? 'product'),
                'description' => sanitize($_POST['description'] ?? '')
            ];
            if ($this->categoryModel->update($id, $data)) {
                FlashMessage::set('message', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی دسته‌بندی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=categories&action=index');
        }
    }

    public function delete($id) {
        if ($this->categoryModel->delete($id)) {
            FlashMessage::set('message', 'دسته‌بندی با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف دسته‌بندی. ممکن است به محصولات یا خدمات دیگری مرتبط باشد.', 'error');
        }
        redirect(APP_URL . '/index.php?page=categories&action=index');
    }
}