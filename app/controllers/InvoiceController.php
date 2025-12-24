<?php
// app/controllers/InventoryController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Unit.php';
require_once __DIR__ . '/../models/Warehouse.php';
require_once __DIR__ . '/../models/InventoryLog.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../core/Helpers.php';


class InventoryController {
    private $auth;
    private $unitModel;
    private $warehouseModel;
    private $inventoryLogModel;
    private $productModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant']);
        $this->unitModel = new Unit();
        $this->warehouseModel = new Warehouse();
        $this->inventoryLogModel = new InventoryLog();
        $this->productModel = new Product();
    }

    public function units() {
        $units = $this->unitModel->getAll();
        view('admin/inventory/units/index', [
            'layout' => 'admin_layout',
            'title' => 'واحدها',
            'units' => $units
        ]);
    }
    
    public function createUnit() {
        view('admin/inventory/units/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'افزودن واحد جدید',
            'unit' => null
        ]);
    }

    public function storeUnit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
            ];
            if ($this->unitModel->create($data)) {
                FlashMessage::set('message', 'واحد جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت واحد. ممکن است نام واحد تکراری باشد.', 'error');
            }
            redirect(APP_URL . '/index.php?page=inventory&action=units');
        }
    }

    public function warehouses() {
        $warehouses = $this->warehouseModel->getAll();
        view('admin/inventory/warehouses/index', [
            'layout' => 'admin_layout',
            'title' => 'لیست انبارها',
            'warehouses' => $warehouses
        ]);
    }

    public function createWarehouse() {
        view('admin/inventory/warehouses/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'افزودن انبار جدید',
            'warehouse' => null
        ]);
    }

    public function storeWarehouse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'location' => sanitize($_POST['location'] ?? null),
            ];
            if ($this->warehouseModel->create($data)) {
                FlashMessage::set('message', 'انبار جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت انبار.', 'error');
            }
            redirect(APP_URL . '/index.php?page=inventory&action=warehouses');
        }
    }
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            $status = sanitize($_POST['status']);
            if ($this->invoiceModel->updateStatus($id, $status)) {
                FlashMessage::set('message', 'وضعیت فاکتور با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی وضعیت فاکتور.', 'error');
            }
        } else {
            FlashMessage::set('message', 'درخواست نامعتبر.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=view_invoice&id=' . $id);
    }

}