<?php
// app/controllers/ServiceController.php

require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/Validator.php';


class ServiceController {
    private $auth;
    private $serviceModel;
    private $categoryModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->serviceModel = new Service();
        $this->categoryModel = new Category();
    }

    public function index() {
        $services = $this->serviceModel->getAll();
        view('admin/inventory/services/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت خدمات',
            'services' => $services
        ]);
    }

    public function create() {
        // ✅ اکنون متد getByType در مدل Category وجود دارد و این فراخوانی بدون خطا انجام می‌شود.
        $categories = $this->categoryModel->getByType('service');
        view('admin/inventory/services/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'افزودن خدمت جدید',
            'service' => null,
            'categories' => $categories
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'price' => sanitize($_POST['price']),
                'description' => sanitize($_POST['description'] ?? ''),
                'category_id' => sanitize($_POST['category_id'] ?? null)
            ];

            if ($this->serviceModel->create($data)) {
                FlashMessage::set('message', 'خدمت جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت خدمت.', 'error');
            }
            redirect(APP_URL . '/index.php?page=services&action=index');
        }
    }

    public function edit($id) {
        $service = $this->serviceModel->findById($id);
        $categories = $this->categoryModel->getByType('service'); // ✅ از این متد برای ویرایش هم استفاده می‌کنیم تا فقط دسته‌بندی‌های مربوط به خدمات نمایش داده شود.
        if (!$service) {
            FlashMessage::set('message', 'خدمت مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=services&action=index');
        }
        view('admin/inventory/services/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش خدمت',
            'service' => $service,
            'categories' => $categories
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'price' => sanitize($_POST['price']),
                'description' => sanitize($_POST['description'] ?? ''),
                'category_id' => sanitize($_POST['category_id'] ?? null)
            ];

            if ($this->serviceModel->update($id, $data)) {
                FlashMessage::set('message', 'خدمت با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی خدمت.', 'error');
            }
            redirect(APP_URL . '/index.php?page=services&action=index');
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->serviceModel->delete($id)) {
                FlashMessage::set('message', 'خدمت با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف خدمت.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=services&action=index');
    }
}