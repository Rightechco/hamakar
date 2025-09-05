<?php
// app/controllers/AnnouncementController.php

require_once __DIR__ . '/../models/Announcement.php';

class AnnouncementController {
    private $auth;
    private $announcementModel;

    public function __construct() {
        $this->auth = new Auth();
        // فقط ادمین اجازه مدیریت اطلاعیه‌ها را دارد
        $this->auth->restrict(['admin']);
        $this->announcementModel = new Announcement();
    }

    /**
     * نمایش لیست تمام اطلاعیه‌ها
     */
    public function index() {
        $announcements = $this->announcementModel->getAll();
        view('admin/announcements/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت اطلاعیه‌ها',
            'announcements' => $announcements
        ]);
    }

    /**
     * نمایش فرم ایجاد اطلاعیه جدید
     */
    public function create() {
        view('admin/announcements/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ایجاد اطلاعیه جدید',
            'announcement' => null
        ]);
    }

    /**
     * ذخیره اطلاعیه جدید در دیتابیس
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => sanitize($_POST['title']),
                'body' => $_POST['body'], // محتوای HTML از ویرایشگر
                'target_roles' => implode(',', $_POST['target_roles'] ?? ['all'])
            ];

            if ($this->announcementModel->create($data)) {
                FlashMessage::set('message', 'اطلاعیه با موفقیت ایجاد شد.');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد اطلاعیه.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
        }
    }

    /**
     * نمایش فرم ویرایش اطلاعیه
     */
    public function edit($id) {
        $announcement = $this->announcementModel->findById($id);
        if (!$announcement) {
            FlashMessage::set('message', 'اطلاعیه مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
        }
        view('admin/announcements/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش اطلاعیه',
            'announcement' => $announcement
        ]);
    }

    /**
     * به‌روزرسانی اطلاعیه
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => sanitize($_POST['title']),
                'body' => $_POST['body'],
                'target_roles' => implode(',', $_POST['target_roles'] ?? ['all'])
            ];
            if ($this->announcementModel->update($id, $data)) {
                FlashMessage::set('message', 'اطلاعیه با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی اطلاعیه.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
        }
    }

    /**
     * حذف اطلاعیه
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->announcementModel->delete($id)) {
                FlashMessage::set('message', 'اطلاعیه با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف اطلاعیه.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
    }
}