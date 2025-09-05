<?php
// app/controllers/CategoryController.php

require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    private $auth;
    private $categoryModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin']);
        $this->categoryModel = new Category();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        view('admin/categories/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت دسته‌بندی‌ها',
            'categories' => $categories
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
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
    // Add edit, update, delete methods as needed.
}