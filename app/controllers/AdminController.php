<?php
// app/controllers/AdminController.php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// فراخوانی فایل‌ها
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Contract.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/TicketReply.php';
require_once __DIR__ . '/../config/sms.php';
require_once __DIR__ . '/../lib/SmsService.php';
require_once __DIR__ . '/../lib/JalaliDate.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../models/JournalVoucher.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/FinancialReport.php';
require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/FixedAsset.php';
require_once __DIR__ . '/../models/ClientLog.php';
require_once __DIR__ . '/../models/ClientContact.php';
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../models/TrainingNeed.php';
require_once __DIR__ . '/../models/TrainingCourse.php';
require_once __DIR__ . '/../models/SkillAssessment.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/MarketingLead.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Category.php';

class AdminController {
    private $auth;
    private $userModel, $clientModel, $contractModel, $invoiceModel, $paymentModel, $projectModel, $taskModel, $reportModel, $ticketModel, $ticketReplyModel, $smsService, $attendanceModel, $leaveRequestModel, $accountModel, $journalVoucherModel, $journalEntryModel, $expenseModel, $financialReportModel, $budgetModel, $fixedAssetModel, $clientLogModel, $clientContactModel, $announcementModel, $trainingNeedModel, $trainingCourseModel, $skillAssessmentModel, $categoryModel;
    private $db;
    private $marketingLeadModel;
    private $productModel, $purchaseModel, $saleModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        
        $this->userModel = new User();
        $this->clientModel = new Client();
        $this->contractModel = new Contract();
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->reportModel = new Report();
        $this->ticketModel = new Ticket();
        $this->ticketReplyModel = new TicketReply();
        $this->smsService = new SmsService();
        $this->attendanceModel = new Attendance();
        $this->leaveRequestModel = new LeaveRequest();
        $this->accountModel = new Account();
        $this->journalVoucherModel = new JournalVoucher();
        $this->journalEntryModel = new JournalEntry();
        $this->expenseModel = new Expense();
        $this->financialReportModel = new FinancialReport();
        $this->budgetModel = new Budget();
        $this->fixedAssetModel = new FixedAsset();
        $this->clientLogModel = new ClientLog();
        $this->clientContactModel = new ClientContact();
        $this->announcementModel = new Announcement();
        $this->trainingNeedModel = new TrainingNeed();
        $this->trainingCourseModel = new TrainingCourse();
        $this->skillAssessmentModel = new SkillAssessment();
        $this->db = new Database(); 
        $this->marketingLeadModel = new MarketingLead();
        $this->productModel = new Product(); 
        $this->purchaseModel = new Purchase();
        $this->saleModel = new Sale();
         $this->categoryModel = new Category();
    }
     
    public function dashboard() {
        $usersCount = $this->userModel->countAll();
        $clientsCount = $this->clientModel->countAll();
        $contractsCount = $this->contractModel->countAll();
        $invoicesCount = $this->invoiceModel->countAll();
        
        $user = $this->auth->user();
        $announcements = $this->announcementModel->getAnnouncementsForRole($user->role);

        view('admin/dashboard', [
            'layout' => 'admin_layout',
            'title' => 'Admin Dashboard',
            'usersCount' => $usersCount,
            'clientsCount' => $clientsCount,
            'contractsCount' => $contractsCount,
            'invoicesCount' => $invoicesCount,
            'announcements' => $announcements
        ]);
    }
    
    public function users() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        
        $filters = [
            'role' => sanitize($_GET['role'] ?? null),
            'status' => sanitize($_GET['status'] ?? null),
            'start_date' => $this->convertJalaliToGregorianString($_GET['start_date'] ?? null),
            'end_date' => $this->convertJalaliToGregorianString($_GET['end_date'] ?? null),
            'search' => sanitize($_GET['search'] ?? null),
        ];

        $users = $this->userModel->getFilteredUsers($filters);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($users);
            return;
        }

        view('admin/users/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت کاربران',
            'users' => $users,
            'filters' => $filters
        ]);
    }
    public function exportUsersToExcel() {
        $this->auth->restrict(['admin', 'accountant']);
        $users = $this->userModel->getAllUsers();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'نام');
        $sheet->setCellValue('B1', 'ایمیل');
        $sheet->setCellValue('C1', 'نقش');
        $sheet->setCellValue('D1', 'وضعیت');
        $sheet->setCellValue('E1', 'تاریخ ثبت');

        $rowNumber = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $rowNumber, $user->name);
            $sheet->setCellValue('B' . $rowNumber, $user->email);
            $sheet->setCellValue('C' . $rowNumber, $user->role);
            $sheet->setCellValue('D' . $rowNumber, $user->status);
            $sheet->setCellValue('E' . $rowNumber, htmlspecialchars(jdate('Y/m/d', strtotime($user->created_at))));
            $rowNumber++;
        }

        ob_clean();
        ob_end_flush();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="users_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function showImportUsersForm() {
        $this->auth->restrict(['admin']);
        view('admin/users/import', [
            'layout' => 'admin_layout',
            'title' => 'ورود کاربران از اکسل'
        ]);
    }
    public function importUsers() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $importedCount = 0;

                for ($row = 2; $row <= $highestRow; $row++) { // شروع از ردیف دوم (بعد از هدر)
                    $data = [
                        'name'     => $sheet->getCell('A' . $row)->getValue(),
                        'email'    => $sheet->getCell('B' . $row)->getValue(),
                        'password' => $sheet->getCell('C' . $row)->getValue() ?? 'password', // کلمه عبور پیش‌فرض
                        'role'     => $sheet->getCell('D' . $row)->getValue() ?? 'employee', // نقش پیش‌فرض
                    ];

                    if (!empty($data['email']) && !empty($data['name'])) {
                        if (!$this->userModel->findByEmail($data['email'])) { // بررسی عدم تکراری بودن ایمیل
                            if ($this->userModel->create($data)) {
                                $importedCount++;
                            }
                        }
                    }
                }
                
                FlashMessage::set('message', "تعداد $importedCount کاربر با موفقیت از فایل اکسل وارد شد.");

            } catch (Exception $e) {
                FlashMessage::set('message', 'خطا در خواندن فایل اکسل: ' . $e->getMessage(), 'error');
            }

            redirect(APP_URL . '/index.php?page=admin&action=users');
        }
    }

    public function createUser() {
        view('admin/users/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Create User',
            'user' => null
        ]);
    }

    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'password' => sanitize($_POST['password'] ?? ''),
                'password_confirmation' => sanitize($_POST['password_confirmation'] ?? ''),
                'role' => sanitize($_POST['role'] ?? 'client')
            ];

            $validator = Validator::make($data);
            $isValid = $validator->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
                'role' => 'required'
            ]);

            if ($isValid) {
                if ($this->userModel->findByEmail($data['email'])) {
                    FlashMessage::set('message', 'ایمیل وارد شده قبلاً استفاده شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_create');
                }

                if ($this->userModel->create($data)) {
                    FlashMessage::set('message', 'کاربر با موفقیت ایجاد شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=users');
                } else {
                    FlashMessage::set('message', 'خطا در ایجاد کاربر.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_create');
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=users_create');
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=users_create');
        }
    }

    public function editUser($id) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            FlashMessage::set('message', 'کاربر مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=users');
        }
        view('admin/users/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Edit User',
            'user' => $user
        ]);
    }

    public function updateUser($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'role' => sanitize($_POST['role'] ?? 'client'),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            $validator = Validator::make($data);
            $isValid = $validator->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'role' => 'required',
                'status' => 'required'
            ]);

            if ($isValid) {
                $existingUser = $this->userModel->findByEmail($data['email']);
                if ($existingUser && $existingUser->id != $id) {
                    FlashMessage::set('message', 'ایمیل وارد شده قبلاً توسط کاربر دیگری استفاده شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_edit&id=' . $id);
                }

                if ($this->userModel->update($id, $data)) {
                    FlashMessage::set('message', 'کاربر با موفقیت به روزرسانی شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=users');
                } else {
                    FlashMessage::set('message', 'خطا در به روزرسانی کاربر.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_edit&id=' . $id);
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=users_edit&id=' . $id);
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=users');
        }
    }

    public function deleteUser($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->delete($id)) {
                FlashMessage::set('message', 'کاربر با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف کاربر.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=users');
    }
    
    public function clients() { 
        $clients = $this->clientModel->getAllClients(); 
        $users = $this->userModel->getAllUsers(); 
        $userNames = [];
        foreach($users as $user) {
            $userNames[$user->id] = $user->name;
        }

        view('admin/clients/index', [ 
            'layout' => 'admin_layout',
            'title' => 'مدیریت مشتریان', 
            'clients' => $clients, 
            'userNames' => $userNames 
        ]);
    }

    public function createClient() { 
        $users = $this->userModel->getAllUsers(); 
        view('admin/clients/create_edit', [ 
            'layout' => 'admin_layout',
            'title' => 'افزودن مشتری جدید',
            'client' => null,
            'users' => $users 
        ]);
    }

    public function storeClient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'contact_person' => sanitize($_POST['contact_person'] ?? null),
                'email' => sanitize($_POST['email'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'user_id' => !empty($_POST['user_id']) ? sanitize($_POST['user_id']) : null,
                'company_name' => sanitize($_POST['company_name'] ?? null),
                'user_type' => sanitize($_POST['user_type'] ?? 'real'),
                'national_code' => sanitize($_POST['national_code'] ?? null),
                'birth_date' => $this->convertJalaliToGregorianString($_POST['birth_date_jalali'] ?? ''),
                'profile_image' => null,
                'national_card_image' => null,
                'company_logo_image' => null,
            ];

            $uploadErrors = [];
            if (!empty($_FILES['profile_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['profile_image'], 'clients_images');
                if ($uploadResult['success']) { $data['profile_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود تصویر پروفایل: ' . $uploadResult['message']; }
            }
            if (!empty($_FILES['national_card_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['national_card_image'], 'clients_images');
                if ($uploadResult['success']) { $data['national_card_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود تصویر کارت ملی: ' . $uploadResult['message']; }
            }
            if (!empty($_FILES['company_logo_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['company_logo_image'], 'clients_images');
                if ($uploadResult['success']) { $data['company_logo_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود لوگوی شرکت: ' . $uploadResult['message']; }
            }
            if (!empty($uploadErrors)) {
                 FlashMessage::set('message', implode('<br>', $uploadErrors), 'error');
                 redirect(APP_URL . '/index.php?page=admin&action=clients_create');
                 return;
            }

            $validationRules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
            ];
            if ($data['user_type'] === 'real') {
                $validationRules['user_id'] = 'required';
            }
            $validator = Validator::make($data);
            $isValid = $validator->validate($validationRules);

            if ($isValid) {
                $existingClient = $this->clientModel->findByEmail($data['email']);
                if ($existingClient) {
                    FlashMessage::set('message', 'ایمیل وارد شده قبلاً برای مشتری دیگری استفاده شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_create');
                    return;
                }
                
                $existingUserLink = !empty($data['user_id']) ? $this->clientModel->findByUserId($data['user_id']) : null;
                if ($existingUserLink) {
                    FlashMessage::set('message', 'این کاربر قبلاً به مشتری دیگری مرتبط شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_create');
                    return;
                }
                
                $clientId = $this->clientModel->create($data);
                
                if ($clientId) {
                    if ($data['user_type'] === 'legal' && isset($_POST['contacts'])) {
                        foreach ($_POST['contacts'] as $contact) {
                            if (empty($contact['name']) || empty($contact['phone'])) continue;
                            
                            $contactUserId = $this->userModel->create([
                                'name' => sanitize($contact['name']),
                                'email' => sanitize($contact['email'] ?? ''),
                                'mobile_number' => sanitize($contact['phone']),
                                'role' => 'client_contact',
                                'status' => 'active',
                                'password' => 'password',
                            ], true);

                            if ($contactUserId) {
                                $this->clientContactModel->create([
                                    'client_id' => $clientId,
                                    'user_id' => $contactUserId,
                                    'position' => sanitize($contact['position'] ?? null)
                                ]);
                            }
                        }
                    }

                    FlashMessage::set('message', 'مشتری با موفقیت ایجاد شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=clients');
                } else {
                    FlashMessage::set('message', 'خطا در ایجاد مشتری.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_create');
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=clients_create');
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=clients_create');
        }
    }

    public function editClient($id) { 
        $client = $this->clientModel->findById($id); 
        $users = $this->userModel->getAllUsers(); 
        
        if (!$client) {
            FlashMessage::set('message', 'مشتری مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
        
        $contacts = $client->user_type == 'legal' ? $this->clientContactModel->getContactsByClientId($id) : [];

        view('admin/clients/create_edit', [ 
            'layout' => 'admin_layout',
            'title' => 'ویرایش مشتری',
            'client' => $client, 
            'users' => $users,
            'contacts' => $contacts
        ]);
    }

    public function updateClient($id) { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldClient = $this->clientModel->findById($id);

            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'contact_person' => sanitize($_POST['contact_person'] ?? null),
                'email' => sanitize($_POST['email'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'user_id' => !empty($_POST['user_id']) ? sanitize($_POST['user_id']) : null,
                'company_name' => sanitize($_POST['company_name'] ?? null),
                'user_type' => sanitize($_POST['user_type'] ?? 'real'),
                'national_code' => sanitize($_POST['national_code'] ?? null),
                'birth_date' => $this->convertJalaliToGregorianString($_POST['birth_date_jalali'] ?? ''),
                'profile_image' => $oldClient->profile_image ?? null,
                'national_card_image' => $oldClient->national_card_image ?? null,
                'company_logo_image' => $oldClient->company_logo_image ?? null,
            ];

            $uploadErrors = [];
            if (!empty($_FILES['profile_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['profile_image'], 'clients_images');
                if ($uploadResult['success']) { $data['profile_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود تصویر پروفایل: ' . $uploadResult['message']; }
            }
            if (!empty($_FILES['national_card_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['national_card_image'], 'clients_images');
                if ($uploadResult['success']) { $data['national_card_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود تصویر کارت ملی: ' . $uploadResult['message']; }
            }
            if (!empty($_FILES['company_logo_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['company_logo_image'], 'clients_images');
                if ($uploadResult['success']) { $data['company_logo_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود لوگوی شرکت: ' . $uploadResult['message']; }
            }
            if (!empty($uploadErrors)) {
                 FlashMessage::set('message', implode('<br>', $uploadErrors), 'error');
                 redirect(APP_URL . '/index.php?page=admin&action=clients_edit&id=' . $id);
                 return;
            }

            $validationRules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
            ];
            if ($data['user_type'] === 'real') {
                $validationRules['user_id'] = 'required';
            }
            $validator = Validator::make($data);
            $isValid = $validator->validate($validationRules);

            if ($isValid) {
                $existingClient = $this->clientModel->findByEmail($data['email']);
                if ($existingClient && $existingClient->id != $id) {
                    FlashMessage::set('message', 'ایمیل وارد شده قبلاً برای مشتری دیگری استفاده شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_edit&id=' . $id);
                }
                $existingUserLink = !empty($data['user_id']) ? $this->clientModel->findByUserId($data['user_id']) : null;
                if ($existingUserLink && $existingUserLink->id != $id) {
                    FlashMessage::set('message', 'این کاربر قبلاً به مشتری دیگری مرتبط شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_edit&id=' . $id);
                }

                if ($this->clientModel->update($id, $data)) {
                    if ($data['user_type'] === 'legal' && isset($_POST['contacts'])) {
                        $this->clientContactModel->deleteByClientId($id);
                        
                        foreach ($_POST['contacts'] as $contact) {
                            if (empty($contact['name']) || empty($contact['phone'])) continue;
                            
                            $existingContactUser = $this->userModel->findByEmail(sanitize($contact['email'] ?? ''));

                            if ($existingContactUser) {
                                $contactUserId = $existingContactUser->id;
                            } else {
                                $contactUserId = $this->userModel->create([
                                    'name' => sanitize($contact['name']),
                                    'email' => sanitize($contact['email'] ?? ''),
                                    'mobile_number' => sanitize($contact['phone']),
                                    'role' => 'client_contact',
                                    'status' => 'active',
                                    'password' => 'password',
                                ], true);
                            }

                            if ($contactUserId) {
                                $this->clientContactModel->create([
                                    'client_id' => $id,
                                    'user_id' => $contactUserId,
                                    'position' => sanitize($contact['position'] ?? null)
                                ]);
                            }
                        }
                    }

                    FlashMessage::set('message', 'مشتری با موفقیت به روزرسانی شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=clients');
                } else {
                    FlashMessage::set('message', 'خطا در به روزرسانی مشتری.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_edit&id=' . $id);
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=clients_edit&id=' . $id);
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
    }
    
    public function deleteClient($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->clientModel->delete($id)) {
                FlashMessage::set('message', 'مشتری با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف مشتری.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=clients');
    }

    public function viewClientLogs($id) {
        $client = $this->clientModel->findById($id);
        if (!$client) {
            FlashMessage::set('message', 'مشتری مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
        $logs = $this->clientLogModel->getLogsByClientId($id);
        $contacts = $client->user_type == 'legal' ? $this->clientContactModel->getContactsByClientId($id) : [];

        view('admin/clients/logs', [
            'layout' => 'admin_layout',
            'title' => 'لاگ های مشتری',
            'client' => $client,
            'logs' => $logs,
            'contacts' => $contacts
        ]);
    }

    public function storeClientLog() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => sanitize($_POST['client_id'] ?? ''),
                'log_type' => sanitize($_POST['log_type'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'log_date' => sanitize($_POST['log_date'] ?? date('Y-m-d H:i:s')),
                'user_id' => $this->auth->user()->id,
                'contact_user_id' => sanitize($_POST['contact_id'] ?? null)
            ];

            $validator = Validator::make($data);
            $isValid = $validator->validate([
                'client_id' => 'required',
                'log_type' => 'required',
                'description' => 'required',
            ]);

            if ($isValid) {
                if ($this->clientLogModel->create($data)) {
                    FlashMessage::set('message', 'لاگ مشتری با موفقیت ثبت شد.');
                } else {
                    FlashMessage::set('message', 'خطا در ثبت لاگ مشتری.', 'error');
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_values($validator->errors())), 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=clients_logs&id=' . $data['client_id']);
        }
    }

    private function uploadFile($file, $uploadDir) {
        $targetDir = "public/uploads/{$uploadDir}/";
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        $fileName = basename($file["name"]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileType;
        $targetFile = $targetDir . $newFileName;
        $uploadOk = 1;

        if ($file["size"] > 500000) { // 500KB
            return ['success' => false, 'message' => 'حجم فایل بسیار بزرگ است.'];
        }
        if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" ) {
            return ['success' => false, 'message' => 'فقط فرمت های JPG, JPEG, PNG و GIF مجاز هستند.'];
        }
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ['success' => true, 'path' => $newFileName];
        } else {
            return ['success' => false, 'message' => 'خطا در آپلود فایل.'];
        }
    }

    private function convertJalaliToGregorianString($jalaliDate) {
    if (empty($jalaliDate)) {
        return null;
    }

    $englishDate = $this->convertPersianNumbersToEnglish($jalaliDate);

    if (strpos($englishDate, '/') === false) {
        return null;
    }
    
    list($j_year, $j_month, $j_day) = explode('/', $englishDate);
    
    $gregorian_array = jalali_to_gregorian((int)$j_year, (int)$j_month, (int)$j_day);
    
    return implode('-', $gregorian_array);
}

private function convertPersianNumbersToEnglish($string) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($persian, $english, $string);
}

    public function contracts() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        
        $filters = [
            'client_id' => sanitize($_GET['client_id'] ?? null),
            'status' => sanitize($_GET['status'] ?? null),
            'start_date' => $this->convertJalaliToGregorianString($_GET['start_date'] ?? null),
            'end_date' => $this->convertJalaliToGregorianString($_GET['end_date'] ?? null),
            'search' => sanitize($_GET['search'] ?? null),
        ];
        
        $contracts = $this->contractModel->getFilteredContracts($filters);
        $clients = $this->clientModel->getAllClients();
        $clientNames = [];
        foreach($clients as $client) {
            $clientNames[$client->id] = $client->name;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($contracts);
            return;
        }

        view('admin/contracts/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت قراردادها',
            'contracts' => $contracts,
            'clientNames' => $clientNames,
            'clients' => $clients,
            'filters' => $filters,
            'serviceTypes' => $this->getServiceTypes()
        ]);
    }

    private function getServiceTypes() {
        return [
            'wordpress_website_design' => 'طراحی سایت وردپرسی',
            'dedicated_website_design' => 'طراحی سایت اختصاصی',
            'branding_marketing' => 'برندینگ و بازاریابی',
            'social_media_management' => 'مدیریت شبکه های اجتماعی',
            'seo_services' => 'خدمات سئو',
            'content_production' => 'خدمات تولید محتوا',
            'server_renewal' => 'تمدید سرور',
            'support_renewal' => 'تمدید پشتیبانی',
        ];
    }

    public function createContract() {
        $this->auth->restrict(['admin', 'accountant']);
        $clients = $this->clientModel->getAllClients();
        view('admin/contracts/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Create Contract',
            'contract' => null,
            'clients' => $clients,
            'serviceTypes' => $this->getServiceTypes()
        ]);
    }
    public function storeContract() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contractData = [
                'client_id' => sanitize($_POST['client_id']),
                'title' => sanitize($_POST['title']),
                'service_type' => sanitize($_POST['service_type']),
                'description' => sanitize($_POST['description']),
                'total_amount' => sanitize($_POST['total_amount']),
                'prepayment_amount' => sanitize($_POST['prepayment_amount'] ?? 0),
                'start_date' => $this->convertJalaliToGregorianString($_POST['start_date'] ?? null),
                'end_date' => $this->convertJalaliToGregorianString($_POST['end_date'] ?? null),
                'final_payment_due_date' => $this->convertJalaliToGregorianString($_POST['final_payment_due_date'] ?? null),
                'renewal_type' => sanitize($_POST['renewal_type']),
                'next_renewal_date' => $this->convertJalaliToGregorianString($_POST['next_renewal_date'] ?? null),
                'status' => sanitize($_POST['status']),
            ];

            $contractId = $this->contractModel->create($contractData);

            if ($contractId) {
                
        $client = $this->clientModel->findById($contractData['client_id']);
        if ($client && !empty($client->phone)) {
            $message = "مشتری گرامی، قرارداد شما با عنوان '{$contractData['title']}' با موفقیت در سیستم ثبت گردید.\nرایان تکرو";
            $this->smsService->sendDirectSms($client->phone, $message);
        }
                $totalAmount = (float)$contractData['total_amount'];
                $prepaymentAmount = (float)$contractData['prepayment_amount'];

                if ($prepaymentAmount > 0) {
                    $prepaymentInvoiceData = [
                        'invoice_number' => $this->generateNextInvoiceNumber($this->invoiceModel->getLastInvoiceNumber()),
                        'client_id' => $contractData['client_id'],
                        'contract_id' => $contractId,
                        'issue_date' => $contractData['start_date'],
                        'due_date' => $contractData['start_date'],
                        'subtotal' => $prepaymentAmount,
                        'vat_rate' => 0,
                        'description' => 'پیش پرداخت قرارداد: ' . $contractData['title'],
                        'status' => 'pending',
                    ];
                    $this->createInvoiceAndJournal($prepaymentInvoiceData);
                }

                $remainingAmount = $totalAmount - $prepaymentAmount;
                if ($remainingAmount > 0) {
                    $finalInvoiceData = [
                        'invoice_number' => $this->generateNextInvoiceNumber($this->invoiceModel->getLastInvoiceNumber(true)),
                        'client_id' => $contractData['client_id'],
                        'contract_id' => $contractId,
                        'issue_date' => $contractData['start_date'],
                        'due_date' => $contractData['final_payment_due_date'],
                        'subtotal' => $remainingAmount,
                        'vat_rate' => 9,
                        'description' => 'تسویه نهایی قرارداد: ' . $contractData['title'],
                        'status' => 'pending',
                    ];
                    $this->createInvoiceAndJournal($finalInvoiceData);
                }

                FlashMessage::set('message', 'قرارداد و فاکتورهای مربوطه با موفقیت ایجاد شدند.');
                redirect(APP_URL . '/index.php?page=admin&action=contracts');
                
            } else {
                FlashMessage::set('message', 'خطا در ایجاد قرارداد.', 'error');
                redirect_back();
            }
        }
    }
    private function createInvoiceAndJournal($invoiceData) {
        $accountsReceivable = $this->accountModel->findByCode('AR-1103');
        $salesRevenue = $this->accountModel->findByCode('SR-4101');

        if (!$accountsReceivable) {
            die('خطای حیاتی: حساب "حساب‌های دریافتنی" با کد AR-1103 در کدینگ حسابداری شما یافت نشد. لطفاً آن را ایجاد یا کد آن را اصلاح کنید.');
        }
        if (!$salesRevenue) {
            die('خطای حیاتی: حساب "درآمد فروش" با کد SR-4101 در کدینگ حسابداری شما یافت نشد. لطفاً آن را ایجاد یا کد آن را اصلاح کنید.');
        }
        $subtotal = (float)($invoiceData['subtotal'] ?? 0);
        $vatRate = (float)($invoiceData['vat_rate'] ?? 0);
        $vatAmount = ($subtotal * $vatRate) / 100;
        $totalAmount = $subtotal + $vatAmount;

        $invoiceData['vat_amount'] = $vatAmount;
        $invoiceData['total_amount'] = $totalAmount;

        $invoiceId = $this->invoiceModel->create($invoiceData);

        if (!$invoiceId) {
            FlashMessage::set('message', 'خطا در ایجاد فاکتور. لطفاً دوباره تلاش کنید.', 'error');
            return false;
        }

        $smsSentStatus = 'success';
        $client = $this->clientModel->findById($invoiceData['client_id']);
        
        if ($client && !empty($client->phone)) {
            $invoiceNumber = $invoiceData['invoice_number'];
            $amountFormatted = number_format($invoiceData['total_amount']);
            
            $paymentLink = APP_URL . '/index.php?page=client&action=invoice_view&id=' . $invoiceId;
            
            $message = "مشتری گرامی،\nفاکتور جدیدی به شماره {$invoiceNumber} به مبلغ {$amountFormatted} تومان برای شما صادر گردید.\nبرای مشاهده و پرداخت، به لینک زیر مراجعه کنید:\n{$paymentLink}\nبا تشکر، رایان تکرو";

            try {
                $isSent = $this->smsService->sendDirectSms($client->phone, $message);
                if (!$isSent) {
                    $smsSentStatus = 'warning';
                }
            } catch (Exception $e) {
                $smsSentStatus = 'error';
                error_log("SMS sending error for invoice " . $invoiceId . ": " . $e->getMessage());
            }
        } else {
            $smsSentStatus = 'info_no_phone';
        }

        $description = 'بابت: ' . $invoiceData['description'];
        $voucherData = [
            'voucher_date' => $invoiceData['issue_date'],
            'description' => $description,
            'user_id' => $this->auth->user()->id,
        ];
        $voucherId = $this->journalVoucherModel->create($voucherData);

        if ($voucherId) {
            $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
            $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $subtotal]);
            
            if ($vatAmount > 0) {
                $vatPayable = $this->accountModel->findByCode('LI-2103');
                if ($vatPayable) {
                    $this->journalEntryModel->create($voucherId, ['account_id' => $vatPayable->id, 'debit' => 0, 'credit' => $vatAmount]);
                }
            }
        } else {
            error_log("Error creating journal voucher for invoice " . $invoiceId);
        }

        return ['invoiceId' => $invoiceId, 'smsStatus' => $smsSentStatus];
    }
    public function editContract($id) {
        $this->auth->restrict(['admin', 'accountant']);
        $contract = $this->contractModel->findById($id);
        $clients = $this->clientModel->getAllClients();

        if (!$contract) {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=contracts');
        }

        $start_date_jalali = ($contract->start_date) ? jdate('Y/m/d', strtotime($contract->start_date)) : '';
        $end_date_jalali = ($contract->end_date) ? jdate('Y/m/d', strtotime($contract->end_date)) : '';
        $next_renewal_date_jalali = ($contract->next_renewal_date) ? jdate('Y/m/d', strtotime($contract->next_renewal_date)) : '';

        view('admin/contracts/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Edit Contract',
            'contract' => $contract,
            'clients' => $this->clientModel->getAllClients(),
            'serviceTypes' => $this->getServiceTypes(),
            'start_date_jalali' => $start_date_jalali,
            'end_date_jalali' => $end_date_jalali,
            'next_renewal_date_jalali' => $next_renewal_date_jalali
        ]);
    }

    public function updateContract($id) {
        $this->auth->restrict(['admin', 'accountant']); 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $data = [
                'client_id' => sanitize($_POST['client_id']),
                'title' => sanitize($_POST['title']),
                'service_type' => sanitize($_POST['service_type']),
                'description' => sanitize($_POST['description']),
                'total_amount' => sanitize($_POST['total_amount']),
                'renewal_type' => sanitize($_POST['renewal_type']),
                'status' => sanitize($_POST['status']),

                'start_date' => $this->convertJalaliToGregorianString($_POST['start_date'] ?? null),
                'end_date' => $this->convertJalaliToGregorianString($_POST['end_date'] ?? null),
                'next_renewal_date' => $this->convertJalaliToGregorianString($_POST['next_renewal_date'] ?? null),
            ];
            
            $validator = Validator::make($data);
            $isValid = $validator->validate([ /* ... validation rules ... */ ]);

            if ($isValid) {
                if ($this->contractModel->update($id, $data)) {
                    FlashMessage::set('message', 'قرارداد با موفقیت به‌روزرسانی شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=contracts');
                } else {
                    FlashMessage::set('message', 'خطا در به‌روزرسانی قرارداد.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=contracts_edit&id=' . $id);
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=contracts_edit&id=' . $id);
            }
        }
    }

    public function deleteContract($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->contractModel->delete($id)) {
                FlashMessage::set('message', 'قرارداد با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف قرارداد.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=contracts');
    }
    
    public function viewContract($id) {
        $contract = $this->contractModel->findById($id);
        if (!$contract) {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=contracts');
        }
        $client = $this->clientModel->findById($contract->client_id); 

        view('admin/contracts/view_contract', [
            'layout' => 'guest_layout',
            'title' => 'جزئیات قرارداد',
            'contract' => $contract,
            'customer' => $client, 
            'serviceTypes' => $this->getServiceTypes(),
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو',
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'email' => 'info@rayantakro.com',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
                'signature_path' => APP_URL . '/assets/img/company_signature.png',
                'seal_path' => APP_URL . '/assets/img/company_seal.png',
            ]
        ]);
    }

    
public function invoices() {
    $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
    
    $filters = [
        'client_id' => sanitize($_GET['client_id'] ?? null),
        'status' => sanitize($_GET['status'] ?? null),
        'start_date' => $this->convertJalaliToGregorianString($_GET['start_date'] ?? null),
        'end_date' => $this->convertJalaliToGregorianString($_GET['end_date'] ?? null),
        'search' => sanitize($_GET['search'] ?? null),
        'invoice_type' => sanitize($_GET['invoice_type'] ?? null) // ✅ This line needs to be added
    ];
    
    $invoices = $this->invoiceModel->getFilteredInvoices($filters);
    $clients = $this->clientModel->getAllClients();

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($invoices);
        return;
    }
    
    view('admin/invoices/index', [
        'layout' => 'admin_layout',
        'title' => 'مدیریت فاکتورها',
        'invoices' => $invoices,
        'clients' => $clients,
        'filters' => $filters,
        'invoiceTypes' => ['product' => 'فاکتور محصول', 'contract' => 'فاکتور قرارداد'] // ✅ This line needs to be added for the filter dropdown
    ]);
}

    public function createInvoice() {
        $this->auth->restrict(['admin', 'accountant']);
        $clients = $this->clientModel->getAllClients();
        $contracts = $this->contractModel->getAllContracts();
        $lastInvoiceNumber = $this->invoiceModel->getLastInvoiceNumber();
        $newInvoiceNumber = $this->generateNextInvoiceNumber($lastInvoiceNumber);

        view('admin/invoices/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'صدور فاکتور جدید',
            'invoice' => null,
            'clients' => $clients,
            'contracts' => $contracts,
            'newInvoiceNumber' => $newInvoiceNumber
        ]);
    }
    
    public function storeInvoice() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceData = [
                'invoice_number' => sanitize($_POST['invoice_number'] ?? ''),
                'client_id' => sanitize($_POST['client_id'] ?? ''),
                'contract_id' => !empty($_POST['contract_id']) ? sanitize($_POST['contract_id']) : null,
                'issue_date' => $this->convertJalaliToGregorianString($_POST['issue_date']),
                'due_date' => $this->convertJalaliToGregorianString($_POST['due_date']),
                'subtotal' => sanitize($_POST['subtotal'] ?? 0),
                'vat_rate' => sanitize($_POST['vat_rate'] ?? 9),
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'pending'),
                'invoice_type' => sanitize($_POST['invoice_type'] ?? 'contract'),
            ];
            
            $result = $this->createInvoiceAndJournal($invoiceData);

            if ($result && is_array($result) && isset($result['invoiceId'])) {
                $message = 'فاکتور با موفقیت ایجاد و سند آن صادر شد.';
                
                if ($result['smsStatus'] === 'success') {
                    $message .= ' پیامک اطلاع‌رسانی با موفقیت ارسال شد.';
                } elseif ($result['smsStatus'] === 'warning') {
                    $message .= ' اما در ارسال پیامک اطلاع‌رسانی مشکلی پیش آمد.';
                } elseif ($result['smsStatus'] === 'error') {
                    $message .= ' اما در ارسال پیامک اطلاع‌رسانی خطای سیستمی رخ داد. (لاگ‌ها را بررسی کنید)';
                } elseif ($result['smsStatus'] === 'info_no_phone') {
                    $message .= ' (شماره تماس مشتری یافت نشد، پیامک ارسال نشد.)';
                }
                
                FlashMessage::set('message', $message);
                redirect(APP_URL . '/index.php?page=admin&action=invoices');
            } else {
                redirect_back();
            }
        }
    }

    public function editInvoice($id) {
        $this->auth->restrict(['admin', 'accountant']);
        $invoice = $this->invoiceModel->findById($id);
        if (!$invoice) {
            FlashMessage::set('message', 'فاکتور مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=invoices');
            return;
        }

        $clients = $this->clientModel->getAllClients();
        $contracts = $this->contractModel->getAllContracts();
        $issue_date_jalali = !empty($invoice->issue_date) ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '';
        $due_date_jalali = !empty($invoice->due_date) ? jdate('Y/m/d', strtotime($invoice->due_date)) : '';

        view('admin/invoices/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش فاکتور',
            'invoice' => $invoice,
            'clients' => $clients,
            'contracts' => $contracts,
            'issue_date_jalali' => $issue_date_jalali,
            'due_date_jalali' => $due_date_jalali
        ]);
    }
    
    public function updateInvoice($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $subtotal = (float)sanitize($_POST['subtotal'] ?? 0);
            $vatRate = (float)sanitize($_POST['vat_rate'] ?? 9);
            $vatAmount = ($subtotal * $vatRate) / 100;
            $totalAmount = $subtotal + $vatAmount;

            $data = [
                'invoice_number' => sanitize($_POST['invoice_number'] ?? ''),
                'client_id' => sanitize($_POST['client_id'] ?? ''),
                'contract_id' => !empty($_POST['contract_id']) ? (int)sanitize($_POST['contract_id']) : null,
                'issue_date' => $this->convertJalaliToGregorianString($_POST['issue_date']),
                'due_date' => $this->convertJalaliToGregorianString($_POST['due_date']),
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'pending'),
            ];

            if ($this->invoiceModel->update($id, $data)) {
                FlashMessage::set('message', 'فاکتور با موفقیت به‌روزرسانی شد.');
                redirect(APP_URL . '/index.php?page=admin&action=invoices');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی فاکتور.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=invoices_edit&id=' . $id);
            }
        }
    }
    public function deleteInvoice($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->invoiceModel->delete($id)) { /* ... */ }
        }
        redirect(APP_URL . '/index.php?page=admin&action=invoices');
    }
    
    public function viewInvoice($id) {
        $invoice = $this->invoiceModel->findByIdWithClient($id);
        if (!$invoice) {
            FlashMessage::set('message', 'فاکتور مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=invoices');
        }
        
        $payments = $this->paymentModel->getAllPaymentsByInvoiceId($id);

        $invoice->issue_date_jalali = !empty($invoice->issue_date) ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '---';
        $invoice->due_date_jalali = !empty($invoice->due_date) ? jdate('Y/m/d', strtotime($invoice->due_date)) : '---';

        view('admin/invoices/view_invoice', [
            'layout' => 'guest_layout',
            'title' => 'جزئیات قرارداد',
            'invoice' => $invoice,
            'payments' => $payments,
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو',
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'email' => 'info@rayantakro.com',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
                'signature_path' => APP_URL . '/assets/img/company_signature.png',
                'seal_path' => APP_URL . '/assets/img/company_seal.png',
            ]
        ]);
    }
  
    public function addPaymentToInvoice() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'invoice_id' => sanitize($_POST['invoice_id'] ?? ''),
                'amount' => sanitize($_POST['amount'] ?? ''),
                'payment_date' => !empty($_POST['payment_date']) ? $this->convertJalaliToGregorianString($_POST['payment_date']) : date('Y-m-d'),
            ];

            if ($this->paymentModel->create($data)) {
                $this->invoiceModel->updatePaymentStatus($data['invoice_id'], $data['amount']);
                
                $bankAccount = $this->accountModel->findByCode('BK-1101');
                $accountsReceivable = $this->accountModel->findByCode('AR-1103');

                if (!$bankAccount || !$accountsReceivable) {
                    FlashMessage::set('message', 'پرداخت ثبت شد اما سند حسابداری به دلیل عدم تعریف حساب‌های پیش‌فرض (بانک/دریافتنی) صادر نشد.', 'warning');
                    redirect(APP_URL . '/index.php?page=admin&action=view_invoice&id=' . $data['invoice_id']);
                    return;
                }

                $invoice = $this->invoiceModel->findById($data['invoice_id']);
                $voucherData = [
                    'voucher_date' => $data['payment_date'],
                    'description' => 'بابت دریافت وجه فاکتور شماره ' . ($invoice->invoice_number ?? $data['invoice_id']),
                    'user_id' => $this->auth->user()->id,
                ];
                $voucherId = $this->journalVoucherModel->create($voucherData);

                if ($voucherId) {
                    $this->journalEntryModel->create($voucherId, ['account_id' => $bankAccount->id, 'debit' => $data['amount'], 'credit' => 0]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => 0, 'credit' => $data['amount']]);
                }
                
                FlashMessage::set('message', 'پرداخت با موفقیت ثبت و سند آن صادر شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت پرداخت.', 'error');
            }
            
            redirect(APP_URL . '/index.php?page=admin&action=view_invoice&id=' . $data['invoice_id']);
        }
    }
    private function generateNextInvoiceNumber($lastInvoiceNumber) {
        if (empty($lastInvoiceNumber)) {
            return 'INV-000001';
        }
        $prefix = 'INV-';
        if (strpos($lastInvoiceNumber, $prefix) === 0) {
            $numberPart = substr($lastInvoiceNumber, strlen($prefix));
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
                return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        }
        return 'INV-000001';
    }

    public function projects() {
    $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
    
    // دریافت فیلترها از درخواست
    $filters = [
        'search' => sanitize($_GET['search'] ?? null),
        'category_id' => sanitize($_GET['category_id'] ?? null),
        'status' => sanitize($_GET['status'] ?? null),
    ];
    
    // فراخوانی متدهای مدل برای دریافت اطلاعات مورد نیاز
    $projects = $this->projectModel->getFilteredProjects($filters); // ✅ متد فیلتر شده جدید
    $categories = $this->categoryModel->getAll();
    $projectSummary = $this->projectModel->getProjectStatusSummary(); 

    // بررسی نوع درخواست برای پاسخ AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($projects);
        return;
    }

    // ارسال داده‌ها به ویو
    view('admin/projects/index', [
        'layout' => 'admin_layout',
        'title' => 'مدیریت پروژه‌ها',
        'projects' => $projects,
        'categories' => $categories,
        'projectSummary' => $projectSummary,
        'filters' => $filters
    ]);
}
    public function createProject() {
        $clients = $this->clientModel->getAllClients();
        $contracts = $this->contractModel->getAllContracts();

        view('admin/projects/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ایجاد پروژه جدید',
            'project' => null,
            'clients' => $clients,
            'contracts' => $contracts,
            'start_date_jalali' => '',
            'end_date_jalali' => ''
        ]);
    }

    public function storeProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $startDateJalali = !empty($_POST['start_date']) ? sanitize($_POST['start_date']) : null;
            $dueDateJalali = !empty($_POST['due_date']) ? sanitize($_POST['due_date']) : null;

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description']),
                'client_id' => !empty($_POST['client_id']) ? sanitize($_POST['client_id']) : null,
                
                'start_date' => $startDateJalali ? $this->convertJalaliToGregorianString($startDateJalali) : null,
                'due_date' => $dueDateJalali ? $this->convertJalaliToGregorianString($dueDateJalali) : null,
                
                'contract_id' => !empty($_POST['contract_id']) ? sanitize($_POST['contract_id']) : null,
                'budget' => !empty($_POST['budget']) ? sanitize($_POST['budget']) : 0,
                
                'status' => sanitize($_POST['status']),
            ];

            if ($this->projectModel->create($data)) {
                FlashMessage::set('message', 'پروژه با موفقیت ایجاد شد.');
                redirect(APP_URL . '/index.php?page=admin&action=projects');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد پروژه.', 'error');
                redirect_back();
            }
        }
    }
    
    public function viewProject($id) {
    $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
    $project = $this->projectModel->findById($id);

    if (!$project) {
        FlashMessage::set('message', 'پروژه مورد نظر یافت نشد.', 'error');
        redirect(APP_URL . '/index.php?page=admin&action=projects');
        return;
    }

    $members = $this->projectModel->getProjectMembers($id);
    $tasks = $this->taskModel->getTasksByProjectId($id);
    $employees = $this->userModel->getAllEmployees();
    
    $total_tasks = count($tasks);
    $completed_tasks = 0;
    
    // دریافت چک‌لیست‌ها برای هر وظیفه و محاسبه وظایف تکمیل شده
    foreach ($tasks as $task) {
        $task->checklist_items = $this->taskModel->getChecklistItemsByTaskId($task->id);
        if ($task->status === 'done') {
            $completed_tasks++;
        }
    }

    $progress = ($total_tasks > 0) ? round(($completed_tasks / $total_tasks) * 100) : 0;
    
    if ($project->start_date) {
        $project->start_date_jalali = jdate('Y/m/d', strtotime($project->start_date));
    }
    if ($project->due_date) {
        $project->due_date_jalali = jdate('Y/m/d', strtotime($project->due_date));
    }

    view('admin/projects/view', [
        'layout' => 'admin_layout',
        'title' => 'مشاهده پروژه: ' . sanitize($project->name),
        'project' => $project,
        'members' => $members,
        'tasks' => $tasks,
        'all_users' => $employees,
        'progress' => $progress
    ]);
}

    public function createTask($projectId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'project_id' => $projectId,
            'title' => sanitize($_POST['title']),
            'description' => sanitize($_POST['description'] ?? ''),
            'assigned_to_user_id' => !empty($_POST['assigned_to_user_id']) ? sanitize($_POST['assigned_to_user_id']) : null,
            // Corrected code to handle an empty due_date field properly
            'due_date' => !empty($_POST['due_date']) ? $this->convertJalaliToGregorianString($_POST['due_date']) : null,
            
            // New fields
            'due_in_days' => !empty($_POST['due_in_days']) ? sanitize($_POST['due_in_days']) : null,
            'due_in_hours' => !empty($_POST['due_in_hours']) ? sanitize($_POST['due_in_hours']) : null,
            'notes' => sanitize($_POST['notes'] ?? ''),
            
            'priority' => sanitize($_POST['priority'] ?? 'medium'),
            'status' => 'todo',
        ];

        if ($this->taskModel->createTask($data)) {
            FlashMessage::set('message', 'وظیفه جدید با موفقیت ایجاد شد.');
        } else {
            FlashMessage::set('message', 'خطا در ایجاد وظیفه.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=view_project&id=' . $projectId);
    }
}


    public function editProject($id) {
        $project = $this->projectModel->findById($id);
        if (!$project) {
            FlashMessage::set('message', 'پروژه مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=projects');
            return;
        }

        $clients = $this->clientModel->getAllClients();
        $contracts = $this->contractModel->getAllContracts();
        $start_date_jalali = !empty($project->start_date) ? jdate('Y/m/d', strtotime($project->start_date)) : '';
        $end_date_jalali = !empty($project->due_date) ? jdate('Y/m/d', strtotime($project->due_date)) : '';

        view('admin/projects/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش پروژه',
            'project' => $project,
            'clients' => $clients,
            'contracts' => $contracts,
            'start_date_jalali' => $start_date_jalali,
            'end_date_jalali' => $end_date_jalali,
        ]);
    }

    public function updateProject($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $startDateJalali = !empty($_POST['start_date']) ? sanitize($_POST['start_date']) : null;
            $dueDateJalali = !empty($_POST['due_date']) ? sanitize($_POST['due_date']) : null;

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description']),
                'client_id' => !empty($_POST['client_id']) ? sanitize($_POST['client_id']) : null,
                
                'start_date' => $startDateJalali ? $this->convertJalaliToGregorianString($startDateJalali) : null,
                'due_date' => $dueDateJalali ? $this->convertJalaliToGregorianString($dueDateJalali) : null,
                
                'contract_id' => !empty($_POST['contract_id']) ? sanitize($_POST['contract_id']) : null,
                'budget' => !empty($_POST['budget']) ? sanitize($_POST['budget']) : 0,
                'status' => sanitize($_POST['status']),
            ];

            if ($this->projectModel->update($id, $data)) {
                FlashMessage::set('message', 'پروژه با موفقیت به‌روزرسانی شد.');
                redirect(APP_URL . '/index.php?page=admin&action=projects');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی پروژه.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=projects_edit&id=' . $id);
            }
        }
    }

    public function deleteProject($id) {
        if ($this->projectModel->delete($id)) {
            FlashMessage::set('message', 'پروژه با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف پروژه.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=projects');
    }
   
    public function sendInvoiceReminder($id) {
        $invoice = $this->invoiceModel->findByIdWithClient($id);
        
        if ($invoice && !empty($invoice->client_phone)) {
            if (empty($invoice->due_date) || $invoice->due_date == '0000-00-00') {
                FlashMessage::set('message', 'تاریخ سررسید برای این فاکتور ثبت نشده است.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=invoices');
                return;
            }

            $contractTitle = !empty($invoice->contract_title) ? $invoice->contract_title : 'خدمات کلی';
            $amount = number_format($invoice->total_amount);
            $dueDate = jdate('Y/m/d', strtotime($invoice->due_date));
            $message = "مشتری گرامی،\nیادآوری پرداخت فاکتور مربوط به «{$contractTitle}» به مبلغ {$amount} تومان در تاریخ {$dueDate}.\nبا تشکر.\nرایان تکرو";

            $isSent = $this->smsService->sendDirectSms($invoice->client_phone, $message);
            
            if ($isSent) {
                FlashMessage::set('message', 'یادآوری پرداخت برای فاکتور با موفقیت ارسال شد.');
            } else {
                FlashMessage::set('message', 'خطا در ارسال پیامک. لطفاً لاگ‌ها را بررسی کنید.', 'error');
            }

        } else {
            FlashMessage::set('message', 'اطلاعات فاکتور یا شماره تماس مشتری یافت نشد.', 'error');
        }
        
        redirect(APP_URL . '/index.php?page=admin&action=invoices');
    }

    public function sendContractReminder($id) {
        $contract = $this->contractModel->findById($id);
        
        if ($contract) {
            $client = $this->clientModel->findById($contract->client_id);

            if ($client && !empty($client->phone)) {
                if (empty($contract->next_renewal_date) || $contract->next_renewal_date == '0000-00-00') {
                    FlashMessage::set('message', 'تاریخ تمدید برای این قرارداد ثبت نشده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=contracts');
                    return;
                }
                
                $amount = number_format($contract->total_amount);
                $renewalDate = jdate('Y/m/d', strtotime($contract->next_renewal_date));
                $message = "مشتری گرامی،\nیادآوری تمدید قرارداد «{$contract->title}» به مبلغ {$amount} تومان در تاریخ {$renewalDate}.\nلطفاً جهت هماهنگی تماس بگیرید.\nرایان تکرو";

                $isSent = $this->smsService->sendDirectSms($client->phone, $message);

                if ($isSent) {
                    FlashMessage::set('message', "یادآوری تمدید برای «{$contract->title}» با موفقیت ارسال شد.");
                } else {
                     FlashMessage::set('message', 'خطا در ارسال پیامک. لطفاً لاگ‌ها را بررسی کنید.', 'error');
                }

            } else {
                 FlashMessage::set('message', 'شماره تماس مشتری برای این قرارداد یافت نشد.', 'error');
            }
        } else {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد.', 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=contracts');
    }

    public function reports() {
        $this->auth->restrict(['admin']);
        $invoiceSummary = $this->reportModel->getInvoiceFinancialSummary();
        $projectSummaryRaw = $this->reportModel->getProjectStatusSummary();
        $userTaskSummary = $this->reportModel->getUserTaskSummary();
        $recentClients = $this->reportModel->getRecentClients();
        
        $projectSummary = [
            'not_started' => 0,
            'in_progress' => 0,
            'finished' => 0,
            'on_hold' => 0,
            'canceled' => 0,
        ];
        if (is_array($projectSummaryRaw)) {
            foreach($projectSummaryRaw as $row) {
                if (isset($projectSummary[$row->status])) {
                    $projectSummary[$row->status] = $row->count;
                }
            }
        }

        view('admin/reports/index', [
            'layout' => 'admin_layout',
            'title' => 'داشبورد گزارشات',
            'invoiceSummary' => $invoiceSummary,
            'projectSummary' => $projectSummary,
            'userTaskSummary' => $userTaskSummary,
            'recentClients' => $recentClients
        ]);
    }

    public function tickets() {
        $this->auth->restrict(['admin']);
        $tickets = $this->ticketModel->getAllForAdmin();
        view('admin/tickets/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت تیکت‌ها',
            'tickets' => $tickets
        ]);
    }

    public function viewTicket($id) {
        $ticket = $this->ticketModel->findByIdWithDetails($id);
        if(!$ticket) {
            redirect_to_404();
            return;
        }

        $replies = $this->ticketReplyModel->getRepliesByTicketId($id);

        view('shared/tickets/view', [
            'layout' => 'admin_layout',
            'title' => 'مشاهده تیکت: ' . sanitize($ticket->subject),
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    public function storeTicketReply($ticketId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['body'])) {
            $user = $this->auth->user();

            $replyId = $this->ticketReplyModel->create([
                'ticket_id' => $ticketId,
                'user_id' => $user->id,
                'body' => $_POST['body']
            ]);

            if ($replyId) {
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                    $uploadDir = 'uploads/attachments/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = uniqid() . '-' . basename($_FILES['attachment']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadFile)) {
                        $this->ticketReplyModel->createAttachment($replyId, $_FILES['attachment']['name'], $uploadFile, $_FILES['attachment']['size']);
                    }
                }
                
                $this->ticketModel->updateStatus($ticketId, 'answered');
                FlashMessage::set('message', 'پاسخ شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت پاسخ.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=view_ticket&id=' . $ticketId);
        } else {
            FlashMessage::set('message', 'متن پاسخ نمی‌تواند خالی باشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=view_ticket&id=' . $ticketId);
        }
    }
    
    public function attendanceReport() {
        $this->auth->restrict(['admin']);
        $filters = [
            'start_date' => !empty($_POST['start_date']) ? jalali_to_gregorian($_POST['start_date']) : null,
            'end_date' => !empty($_POST['end_date']) ? jalali_to_gregorian($_POST['end_date']) : null,
        ];

        $records = $this->attendanceModel->getAllRecords($filters);
        
        view('admin/attendance/report', [
            'layout' => 'admin_layout',
            'title' => 'گزارش حضور و غیاب',
            'records' => $records,
            'filters' => $filters
        ]);
    }

    public function manageLeaveRequests() {
        $this->auth->restrict(['admin']);
        $pendingRequests = $this->leaveRequestModel->getAllPending();
        view('admin/leave/manage', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت درخواست‌های مرخصی',
            'requests' => $pendingRequests
        ]);
    }

    public function processLeaveRequest($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = sanitize($_POST['status']);
            $adminNotes = sanitize($_POST['admin_notes']);
            $adminId = $this->auth->user()->id;

            if ($this->leaveRequestModel->processRequest($id, $status, $adminNotes, $adminId)) {
                FlashMessage::set('message', 'درخواست با موفقیت بررسی شد.');
            } else {
                FlashMessage::set('message', 'خطا در بررسی درخواست.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=manage_leave_requests');
        }
    }

    public function smsPanel() {
        $this->auth->restrict(['admin']);
        $allOccasions = Occasions::getAllOccasions();
        
        view('admin/sms/panel', [
            'layout' => 'admin_layout',
            'title' => 'پنل ارسال پیامک',
            'allOccasions' => $allOccasions
        ]);
    }

    public function sendCustomBulkSms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message_body'])) {
            $messageText = sanitize($_POST['message_body']);
            $messageBodyWithUnsubscribe = $messageText . "\nلغو11";

            $clients = $this->clientModel->getAllClients();
            $phoneNumbers = [];
            foreach ($clients as $client) {
                if (!empty($client->phone)) {
                    $phoneNumbers[] = $client->phone;
                }
            }

            if (!empty($phoneNumbers)) {
                $isSent = $this->smsService->sendDirectSms($phoneNumbers, $messageBodyWithUnsubscribe);
                if ($isSent) {
                    FlashMessage::set('message', "پیامک سفارشی برای ارسال به " . count($phoneNumbers) . " مشتری در صف قرار گرفت.");
                } else {
                    FlashMessage::set('message', 'خطا در ارسال پیامک گروهی. لطفاً لاگ‌های سرور را بررسی کنید.', 'error');
                }
            } else {
                FlashMessage::set('message', 'هیچ مشتری با شماره تلفن معتبر یافت نشد.', 'warning');
            }
            redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
        }
    }

    public function sendOccasionalSms() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['occasion_message'])) {
            redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
            return;
        }

        $occasionMessageTemplate = $_POST['occasion_message'];
        $clients = $this->clientModel->getAllClients();
        $successCount = 0;

        foreach ($clients as $client) {
            if (!empty($client->phone)) {
                $finalMessage = str_replace('{client_name}', $client->name, $occasionMessageTemplate);
                if ($this->smsService->sendDirectSms($client->phone, $finalMessage)) {
                    $successCount++;
                }
            }
        }
        
        FlashMessage::set('message', "پیامک مناسبتی با موفقیت به {$successCount} مشتری ارسال شد.");
        redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
    }


    public function sendSelectedOccasionSms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['occasion_key'])) {
            $occasionKey = sanitize($_POST['occasion_key']);
            $allOccasions = Occasions::getAllOccasions();

            if (isset($allOccasions[$occasionKey])) {
                $occasion = $allOccasions[$occasionKey];
                FlashMessage::set('message', "پیامک مناسبتی '{$occasion['name']}' با موفقیت ارسال شد.");
            } else {
                FlashMessage::set('message', 'مناسبت انتخاب شده معتبر نیست.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
        }
    }

    public function payrolls() {
        $this->auth->restrict(['admin']);
        $payrollModel = new Payroll();
        $payrolls = $payrollModel->getAllPayrolls();
        
        view('admin/payrolls/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت حقوق و دستمزد',
            'payrolls' => $payrolls
        ]);
    }

    public function createPayroll() {
        $this->auth->restrict(['admin']);
        $userModel = new User();
        $employees = $userModel->getAllEmployees();
        
        view('admin/payrolls/create', [
            'layout' => 'admin_layout',
            'title' => 'صدور فیش حقوقی جدید',
            'employees' => $employees
        ]);
    }

    public function storePayroll() {
        $this->auth->restrict(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payrollModel = new Payroll();

            $earnings = $_POST['earnings'] ?? [];
            $deductions = $_POST['deductions'] ?? [];
            
            $gross_earnings = array_sum(array_column($earnings, 'amount'));
            $total_deductions = array_sum(array_column($deductions, 'amount'));
            $net_pay = $gross_earnings - $total_deductions;

            $payrollData = [
                'user_id' => sanitize($_POST['user_id']),
                'pay_period_year' => sanitize($_POST['year']),
                'pay_period_month' => sanitize($_POST['month']),
                'gross_earnings' => $gross_earnings,
                'total_deductions' => $total_deductions,
                'net_pay' => $net_pay,
                'status' => 'finalized',
                'notes' => sanitize($_POST['notes'] ?? null)
            ];
            
            $itemsData = [];
            foreach($earnings as $item) {
                if(!empty($item['description']) && !empty($item['amount'])) {
                    $itemsData[] = ['type' => 'earning', 'description' => sanitize($item['description']), 'amount' => sanitize($item['amount'])];
                }
            }
            foreach($deductions as $item) {
                 if(!empty($item['description']) && !empty($item['amount'])) {
                    $itemsData[] = ['type' => 'deduction', 'description' => sanitize($item['description']), 'amount' => sanitize($item['amount'])];
                }
            }

            $payrollId = $payrollModel->createPayroll($payrollData, $itemsData);

            if ($payrollId) {
                $tax_amount = 0; $employee_insurance_amount = 0;
                foreach ($deductions as $item) {
                    if (mb_strpos($item['description'], 'مالیات') !== false) $tax_amount = (float)$item['amount'];
                    if (mb_strpos($item['description'], 'بیمه') !== false) $employee_insurance_amount = (float)$item['amount'];
                }

                $employer_insurance_amount = ($gross_earnings > 0) ? round($gross_earnings * 0.23) : 0;
                $total_insurance_payable = $employee_insurance_amount + $employer_insurance_amount;

                $salaryExpAccount = $this->accountModel->findByCode('EX-5101');
                $insuranceExpAccount = $this->accountModel->findByCode('EX-5102');
                $taxPayableAccount = $this->accountModel->findByCode('LI-2101');
                $insurancePayableAccount = $this->accountModel->findByCode('LI-2102');
                $bankAccount = $this->accountModel->findByCode('BK-1101');

                if (!$salaryExpAccount || !$insuranceExpAccount || !$taxPayableAccount || !$insurancePayableAccount || !$bankAccount) {
                    FlashMessage::set('message', 'فیش حقوقی ثبت شد اما سند حسابداری به دلیل عدم تعریف کامل حساب‌های پیش‌فرض حقوق و دستمزد صادر نشد.', 'warning');
                    redirect(APP_URL . '/index.php?page=admin&action=payrolls');
                    return;
                }

                $employee = $this->userModel->findById($_POST['user_id']);
                $voucherDesc = "سند هزینه حقوق " . jdate_words(['mm' => $_POST['month']])['mm'] . " " . $_POST['year'] . " برای " . $employee->name;

                $voucherId = $this->journalVoucherModel->create([
                    'voucher_date' => date('Y-m-d'),
                    'description' => $voucherDesc,
                    'user_id' => $this->auth->user()->id
                ]);

                if ($voucherId) {
                    $this->journalEntryModel->create($voucherId, ['account_id' => $salaryExpAccount->id, 'debit' => $gross_earnings, 'credit' => 0, 'entity_type' => 'employee', 'entity_id' => $employee->id]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $insuranceExpAccount->id, 'debit' => $employer_insurance_amount, 'credit' => 0]);
                    
                    $this->journalEntryModel->create($voucherId, ['account_id' => $bankAccount->id, 'debit' => 0, 'credit' => $net_pay]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $taxPayableAccount->id, 'debit' => 0, 'credit' => $tax_amount]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $insurancePayableAccount->id, 'debit' => 0, 'credit' => $total_insurance_payable]);
                }

                FlashMessage::set('message', 'فیش حقوقی و سند حسابداری آن با موفقیت صادر شد.');
                redirect(APP_URL . '/index.php?page=admin&action=payrolls');

            } else {
                FlashMessage::set('message', 'خطا در صدور فیش حقوقی.', 'error');
                redirect_back();
            }
        }
    }

    public function viewPayslip($id) {
        $payrollModel = new Payroll();
        $payroll = $payrollModel->findPayrollByIdWithDetails($id);

        if (!$payroll) {
            redirect_to_404();
        }
        
        view('shared/payslip/view', [
            'layout' => 'admin_layout',
            'title' => 'مشاهده فیش حقوقی',
            'payroll' => $payroll,
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو',
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
            ]
        ]);
    }

    public function payrollSettings() {
        $this->auth->restrict(['admin']);
        $settingModel = new PayrollSetting();
        $settings = $settingModel->getAllSettings();
        view('admin/payrolls/settings', [
            'layout' => 'admin_layout',
            'title' => 'تنظیمات سالانه حقوق و دستمزد',
            'settings' => $settings
        ]);
    }

    public function storePayrollSettings() {
        $this->auth->restrict(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel = new PayrollSetting();
            
            $data = [
                'setting_year' => sanitize($_POST['year']),
                'base_salary_monthly' => sanitize($_POST['base_salary_monthly']),
                'work_days_in_month' => sanitize($_POST['work_days_in_month']),
                'housing_allowance' => sanitize($_POST['housing_allowance']),
                'family_allowance' => sanitize($_POST['family_allowance']),
                'seniority_per_year' => sanitize($_POST['seniority_per_year']),
            ];

            if ($settingModel->createOrUpdate($data)) {
                FlashMessage::set('message', 'تنظیمات با موفقیت ذخیره شد.');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره تنظیمات.', 'error');
            }
            redirect_back();
        }
    }
    public function detailedReports() {
        $filters = [
            'invoice_status' => sanitize($_POST['invoice_status'] ?? ''),
            'project_status' => sanitize($_POST['project_status'] ?? ''),
            'start_date' => !empty($_POST['start_date']) ? jalali_to_gregorian($_POST['start_date']) : null,
            'end_date' => !empty($_POST['end_date']) ? jalali_to_gregorian($_POST['end_date']) : null,
        ];

        $reportModel = new Report();
        $reports = [
            'invoices' => $reportModel->getInvoicesReport($filters),
            'projects' => $reportModel->getProjectsReport($filters),
            'employees' => $reportModel->getEmployeeActivityReport()
        ];

        view('admin/reports/detailed', [
            'layout' => 'admin_layout',
            'title' => 'گزارشات جامع',
            'filters' => $filters,
            'reports' => $reports
        ]);
    }
    public function deletePayroll($id) {
        $payrollModel = new Payroll();
        if ($payrollModel->delete($id)) {
            FlashMessage::set('message', 'فیش حقوقی با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف فیش حقوقی.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=payrolls');
    }

    public function showImportForm() {
        $this->auth->restrict(['admin', 'accountant']);
        view('admin/clients/import', [
            'layout' => 'admin_layout',
            'title' => 'ورود مشتریان از اکسل'
        ]);
    }

    public function importClients() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $importedCount = 0;

                for ($row = 1; $row <= $highestRow; $row++) { 
                    $data = [
                        'name'         => $sheet->getCell('A' . $row)->getValue(),
                        'company_name' => $sheet->getCell('B' . $row)->getValue(),
                        'email'        => $sheet->getCell('C' . $row)->getValue(),
                        'phone'        => $sheet->getCell('D' . $row)->getValue(),
                        'address'      => $sheet->getCell('E' . $row)->getValue(),
                    ];

                    if (!empty($data['email'])) {
                        if ($this->clientModel->create($data)) {
                            $importedCount++;
                        }
                    }
                }
                
                FlashMessage::set('message', "تعداد $importedCount مشتری با موفقیت از فایل اکسل وارد شد.");

            } catch (Exception $e) {
                FlashMessage::set('message', 'خطا در خواندن فایل اکسل: ' . $e->getMessage(), 'error');
            }

            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
    }

    public function exportClients() {
        $this->auth->restrict(['admin', 'accountant']);
        $clients = $this->clientModel->getAllClients();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'نام');
        $sheet->setCellValue('B1', 'نام شرکت');
        $sheet->setCellValue('C1', 'ایمیل');
        $sheet->setCellValue('D1', 'تلفن');
        $sheet->setCellValue('E1', 'آدرس');

        $rowNumber = 2;
        foreach ($clients as $client) {
            $sheet->setCellValue('A' . $rowNumber, $client->name);
            $sheet->setCellValue('B' . $rowNumber, $client->company_name);
            $sheet->setCellValue('C' . $rowNumber, $client->email);
            $sheet->setCellValue('D' . $rowNumber, $client->phone);
            $sheet->setCellValue('E' . $rowNumber, $client->address);
            $rowNumber++;
        }

        ob_clean();
        ob_end_flush();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="clients_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function accountingAccounts() {
    $this->auth->restrict(['admin', 'accountant']);    
    $accounts = $this->accountModel->getAllHierarchical();
    view('admin/accounting/accounts/index', [
        'layout' => 'admin_layout',
        'title' => 'کدینگ حسابداری',
        'accounts' => $accounts
    ]);
}
    public function createVoucherForm() {
    $this->auth->restrict(['admin', 'accountant']);
    $accounts = $this->accountModel->getAll();
    view('admin/accounting/vouchers/create', [
        'layout' => 'admin_layout',
        'title' => 'ثبت سند جدید',
        'accounts' => $accounts
    ]);
}

    public function storeVoucher() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $totalDebit = 0;
            $totalCredit = 0;
            if (!empty($_POST['entries'])) {
                foreach ($_POST['entries'] as $entry) {
                    $totalDebit += (float)($entry['debit'] ?? 0);
                    $totalCredit += (float)($entry['credit'] ?? 0);
                }
            }

            if (abs($totalDebit - $totalCredit) > 0.01 || $totalDebit === 0) {
                FlashMessage::set('message', 'سند تراز نیست یا مبلغ آن صفر است!', 'error');
                redirect_back();
                return;
            }

            $voucherData = [
                'voucher_date' => $this->convertJalaliToGregorianString($_POST['voucher_date']),
                'description' => sanitize($_POST['description']),
                'user_id' => $this->auth->user()->id,
            ];
            $voucherId = $this->journalVoucherModel->create($voucherData);

            if ($voucherId) {
                foreach ($_POST['entries'] as $entry) {
                    if (!empty($entry['account_id'])) {
                        $this->journalEntryModel->create($voucherId, $entry);
                    }
                }
                FlashMessage::set('message', 'سند حسابداری با موفقیت ثبت شد.');
                redirect(APP_URL . '/index.php?page=admin&action=create_voucher_form');
            } else {
                FlashMessage::set('message', 'خطا در ثبت سند.', 'error');
                redirect_back();
            }
        }
    }
    public function accountingExpenses() {
        $this->auth->restrict(['admin', 'accountant']);
        view('admin/accounting/expenses/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت هزینه‌ها',
            'expense_accounts' => $this->accountModel->getAccountsByType('expense'),
            'payment_accounts' => $this->accountModel->getAccountsByType('asset')
        ]);
    }

    public function storeExpense() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $expenseData = [
                'expense_date' => jalali_to_gregorian($_POST['expense_date']),
                'amount' => sanitize($_POST['amount']),
                'description' => sanitize($_POST['description']),
                'vendor' => sanitize($_POST['vendor'] ?? null),
                'expense_account_id' => sanitize($_POST['expense_account_id']),
                'payment_account_id' => sanitize($_POST['payment_account_id']),
            ];
            $this->expenseModel->create($expenseData);

            $voucherData = [
                'voucher_date' => $expenseData['expense_date'],
                'description' => 'ثبت هزینه: ' . $expenseData['description'],
                'user_id' => $this->auth->user()->id,
            ];
            $voucherId = $this->journalVoucherModel->create($voucherData);
            if ($voucherId) {
                $this->journalEntryModel->create($voucherId, [
                    'account_id' => $expenseData['expense_account_id'],
                    'debit' => $expenseData['amount'],
                    'credit' => 0
                ]);
                $this->journalEntryModel->create($voucherId, [
                    'account_id' => $expenseData['payment_account_id'],
                    'debit' => 0,
                    'credit' => $expenseData['amount']
                ]);
            }
            
            FlashMessage::set('message', 'هزینه با موفقیت ثبت و سند حسابداری آن صادر شد.');
            redirect(APP_URL . '/index.php?page=admin&action=accounting_expenses');
        }
    }


    public function financialReports() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارشات مالی'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['trialBalance'] = $this->financialReportModel->getTrialBalance($startDate, $endDate);
                
                $pnlData = $this->financialReportModel->getProfitAndLossData($startDate, $endDate);
                
                $totalIncome = 0; $totalExpenses = 0;
                $incomes = []; $expenses = [];
                foreach ($pnlData as $item) {
                    if ($item->type === 'income') {
                        $incomes[] = $item;
                        $totalIncome += $item->balance;
                    } else {
                        $item->balance = abs($item->balance);
                        $expenses[] = $item;
                        $totalExpenses += $item->balance;
                    }
                }
                $netProfit = $totalIncome - $totalExpenses;
                
                $data['profitAndLoss'] = [
                    'incomes' => $incomes, 'expenses' => $expenses,
                    'totalIncome' => $totalIncome, 'totalExpenses' => $totalExpenses,
                    'netProfit' => $netProfit
                ];

                $bsData = $this->financialReportModel->getBalanceSheetData($endDate);
                $assets = []; $liabilities = []; $equity = [];
                $totalAssets = 0; $totalLiabilities = 0; $totalEquity = 0;

                foreach ($bsData as $item) {
                    if ($item->type === 'asset') {
                        $assets[] = $item;
                        $totalAssets += $item->balance;
                    } elseif ($item->type === 'liability') {
                        $item->balance = abs($item->balance);
                        $liabilities[] = $item;
                        $totalLiabilities += $item->balance;
                    } elseif ($item->type === 'equity') {
                        $item->balance = abs($item->balance);
                        $equity[] = $item;
                        $totalEquity += $item->balance;
                    }
                }
                
                $data['balanceSheet'] = [
                    'assets' => $assets, 'liabilities' => $liabilities, 'equity' => $equity,
                    'totalAssets' => $totalAssets, 'totalLiabilities' => $totalLiabilities,
                    'totalEquity' => $totalEquity, 'netProfitForPeriod' => $netProfit,
                ];
            }

            $data['startDateJalali'] = $_POST['start_date'];
            $data['endDateJalali'] = $_POST['end_date'];
        }

        view('admin/accounting/reports/index', $data);
    }

    public function createAccountForm() {
        $this->auth->restrict(['admin', 'accountant']);
        $accounts = $this->accountModel->getAll();
        view('admin/accounting/accounts/create', [
            'layout' => 'admin_layout',
            'title' => 'افزودن حساب جدید',
            'accounts' => $accounts
        ]);
    }

    public function storeAccount() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'parent_id' => sanitize($_POST['parent_id']),
                'code' => sanitize($_POST['code']),
                'name' => sanitize($_POST['name']),
                'type' => sanitize($_POST['type']),
            ];

            if ($this->accountModel->create($data)) {
                FlashMessage::set('message', 'حساب جدید با موفقیت ایجاد شد.');
                redirect(APP_URL . '/index.php?page=admin&action=accounting_accounts');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد حساب.', 'error');
                redirect_back();
            }
        }
    }

    public function accountLedger() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'دفتر کل حساب',
            'allAccounts' => $this->accountModel->getAll()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountId = $_POST['account_id'];
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($accountId && $startDate && $endDate) {
                $data['ledgerData'] = $this->financialReportModel->getAccountLedger($accountId, $startDate, $endDate);
                $selectedAccount = $this->accountModel->findById($accountId);
                $data['selectedAccountName'] = $selectedAccount ? $selectedAccount->name : '';
                $data['selectedAccountId'] = $accountId;
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/ledger', $data);
    }

    public function generalJournal() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'دفتر روزنامه'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['journalData'] = $this->financialReportModel->getGeneralJournal($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/general_journal', $data);
    }

    public function vatReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارش مالیات بر ارزش افزوده'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['reportData'] = $this->financialReportModel->getVatReportData($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/vat_report', $data);
    }

    public function closeFiscalYear() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            $incomeSummaryAccount = $this->accountModel->findByCode('EQ-3100');
            $retainedEarningsAccount = $this->accountModel->findByCode('EQ-3101');
            if (!$incomeSummaryAccount || !$retainedEarningsAccount) {
                FlashMessage::set('message', 'خطا: حساب‌های سیستمی (خلاصه سود و زیان / سود انباشته) تعریف نشده‌اند.', 'error');
                redirect_back();
                return;
            }

            $closingData = $this->financialReportModel->getClosingEntriesData($endDate);
            
            $voucherId = $this->journalVoucherModel->create([
                'voucher_date' => $endDate,
                'description' => 'سند اختتامیه برای سال مالی منتهی به ' . $_POST['end_date'],
                'user_id' => $this->auth->user()->id
            ]);

            if (!$voucherId) {
                FlashMessage::set('message', 'خطا در ایجاد سند اختتامیه.', 'error');
                redirect_back();
                return;
            }

            $totalIncome = 0;
            $totalExpenses = 0;

            foreach ($closingData['income_accounts'] as $account) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $account->account_id, 'debit' => $account->balance, 'credit' => 0]);
                $totalIncome += $account->balance;
            }
            if ($totalIncome > 0) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => 0, 'credit' => $totalIncome]);
            }

            foreach ($closingData['expense_accounts'] as $account) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $account->balance]);
                $totalExpenses += $account->balance;
            }
            if ($totalExpenses > 0) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => $totalExpenses, 'credit' => 0]);
            }
            
            $netProfit = $totalIncome - $totalExpenses;
            if ($netProfit > 0) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => $netProfit, 'credit' => 0]);
                $this->journalEntryModel->create($voucherId, ['account_id' => $retainedEarningsAccount->id, 'debit' => 0, 'credit' => $netProfit]);
            } else {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => 0, 'credit' => abs($netProfit)]);
                $this->journalEntryModel->create($voucherId, ['account_id' => $retainedEarningsAccount->id, 'debit' => abs($netProfit), 'credit' => 0]);
            }

            FlashMessage::set('message', 'سال مالی با موفقیت بسته شد و سند اختتامیه صادر گردید.');
            redirect(APP_URL . '/index.php?page=admin&action=general_journal');

        } else {
            view('admin/accounting/closing/index', [
                'layout' => 'admin_layout',
                'title' => 'بستن سال مالی'
            ]);
        }
    }

    public function deleteVoucher($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->journalVoucherModel->delete($id)) {
                FlashMessage::set('message', 'سند حسابداری با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف سند. لطفاً لاگ‌ها را بررسی کنید.', 'error');
            }
            redirect_back();
        }
    }

    public function subledgerReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'دفتر تفصیلی',
            'allAccounts' => $this->accountModel->getAll(),
            'allClients' => $this->clientModel->getAllClients()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountId = $_POST['account_id'];
            $entityId = $_POST['entity_id'];
            $entityType = 'client';
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($accountId && $entityId && $startDate && $endDate) {
                $data['ledgerData'] = $this->financialReportModel->getSubledger($accountId, $entityType, $entityId, $startDate, $endDate);
                
                $selectedAccount = $this->accountModel->findById($accountId);
                $selectedEntity = $this->clientModel->findById($entityId);

                $data['selectedAccountName'] = $selectedAccount ? $selectedAccount->name : '';
                $data['selectedEntityName'] = $selectedEntity ? $selectedEntity->name : '';
                $data['selectedAccountId'] = $accountId;
                $data['selectedEntityId'] = $entityId;
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/subledger', $data);
    }

    public function reverseVoucher($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->journalVoucherModel->reverse($id, $this->auth->user()->id)) {
                FlashMessage::set('message', 'سند معکوس با موفقیت صادر شد.');
            } else {
                FlashMessage::set('message', 'خطا در صدور سند معکوس.', 'error');
            }
            redirect_back();
        }
    }

    public function budgeting() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'account_id' => $_POST['account_id'],
                'period_year' => $_POST['period_year'],
                'period_month' => $_POST['period_month'],
                'budget_amount' => $_POST['budget_amount']
            ];
            if ($this->budgetModel->saveOrUpdate($data)) {
                FlashMessage::set('message', 'بودجه با موفقیت ذخیره شد.');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره بودجه.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=budgeting&year=' . $data['period_year']);
        }

        $currentYear = $_GET['year'] ?? jdate('Y');
        $data = [
            'layout' => 'admin_layout',
            'title' => 'بودجه‌بندی',
            'expenseAccounts' => $this->accountModel->getAccountsByType('expense'),
            'budgets' => $this->budgetModel->getBudgetsByYear($currentYear),
            'currentYear' => $currentYear
        ];
        view('admin/accounting/budgeting', $data);
    }

    public function deleteBudget($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->budgetModel->delete($id)) {
                FlashMessage::set('message', 'ردیف بودجه حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف.', 'error');
            }
            redirect_back();
        }
    }

    public function budgetReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارش بودجه'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'];
            $month = $_POST['month'];

            $data['reportData'] = $this->financialReportModel->getBudgetVsActualReport($year, $month);
            $data['selectedYear'] = $year;
            $data['selectedMonth'] = $month;
        }

        view('admin/accounting/budget_report', $data);
    }

    public function bankReconciliation() {
        $this->auth->restrict(['admin', 'accountant']);
        $data = [
            'layout' => 'admin_layout',
            'title' => 'مغایرت‌گیری بانکی',
            'bankAccounts' => $this->accountModel->getAccountsByType('asset')
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_reconciliation'])) {
            $accountId = $_POST['account_id'];
            $statementDate = $this->convertJalaliToGregorianString($_POST['statement_date']);
            $statementBalance = $_POST['statement_balance'];

            if ($accountId && $statementDate && isset($statementBalance)) {
                $unreconciled_tx = $this->financialReportModel->getUnreconciledTransactions($accountId, $statementDate);
                
                $data['deposits'] = array_filter($unreconciled_tx, function($tx) {
                    return $tx->debit > 0;
                });
                $data['payments'] = array_filter($unreconciled_tx, function($tx) {
                    return $tx->credit > 0;
                });
                
                $data['selectedAccountId'] = $accountId;
                $data['statementDate'] = $statementDate;
                $data['statementBalance'] = $statementBalance;
                $data['statementDateJalali'] = $_POST['statement_date'];
            }
        }
        
        view('admin/accounting/reconciliation', $data);
    }

    public function processReconciliation() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reconciliationData = [
                'account_id' => $_POST['account_id'],
                'statement_date' => $_POST['statement_date'],
                'statement_balance' => $_POST['statement_balance'],
                'user_id' => $this->auth->user()->id
            ];

            $reconciliationId = (new Reconciliation())->create($reconciliationData);

            if ($reconciliationId) {
                $entryIds = $_POST['entry_ids'] ?? [];
                (new JournalEntry())->markAsReconciled($entryIds, $reconciliationId);
                
                FlashMessage::set('message', 'مغایرت‌گیری با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت عملیات مغایرت‌گیری.', 'error');
            }

            redirect(APP_URL . '/index.php?page=admin&action=bank_reconciliation');
        }
    }
    public function manageFixedAssets() {
        $this->auth->restrict(['admin', 'accountant']);
        $data = [
            'layout' => 'admin_layout',
            'title' => 'مدیریت دارایی‌های ثابت',
            'assetAccounts' => $this->accountModel->getAccountsByType('asset'),
            'expenseAccounts' => $this->accountModel->getAccountsByType('expense'),
            'fixedAssets' => $this->fixedAssetModel->getAll(),
        ];
        view('admin/accounting/fixed_assets/index', $data);
    }

    public function storeFixedAsset() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'asset_name' => sanitize($_POST['asset_name']),
                'asset_code' => sanitize($_POST['asset_code']),
                'purchase_date' => $this->convertJalaliToGregorianString($_POST['purchase_date']),
                'purchase_cost' => sanitize($_POST['purchase_cost']),
                'salvage_value' => sanitize($_POST['salvage_value']),
                'useful_life_years' => sanitize($_POST['useful_life_years']),
                'asset_account_id' => sanitize($_POST['asset_account_id']),
                'expense_account_id' => sanitize($_POST['expense_account_id']),
                'accumulated_depreciation_account_id' => sanitize($_POST['accumulated_depreciation_account_id']),
            ];

            if ($this->fixedAssetModel->create($data)) {
                FlashMessage::set('message', 'دارایی جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت دارایی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=fixed_assets');
        }
    }

    public function deleteFixedAsset($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->fixedAssetModel->delete($id)) {
                FlashMessage::set('message', 'دارایی با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف.', 'error');
            }
            redirect_back();
        }
    }

    public function showDepreciationForm() {
        $this->auth->restrict(['admin', 'accountant']);
        view('admin/accounting/fixed_assets/run_depreciation', [
            'layout' => 'admin_layout',
            'title' => 'اجرای عملیات استهلاک'
        ]);
    }

    public function runDepreciation() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect_back();

        $upToDateStr = $this->convertJalaliToGregorianString($_POST['depreciation_date']);
        $upToDate = new DateTime($upToDateStr);

        $assets = $this->fixedAssetModel->getAllForDepreciation();
        $entries = [];
        $totalDepreciation = 0;

        foreach ($assets as $asset) {
            $depreciableCost = (float)$asset->purchase_cost - (float)$asset->salvage_value;
            if ($depreciableCost <= 0 || (int)$asset->useful_life_years <= 0) continue;
            $monthlyDepreciation = $depreciableCost / ((int)$asset->useful_life_years * 12);
            
            $startDate = new DateTime($asset->last_depreciation_date ?? $asset->purchase_date);
            
            $interval = $startDate->diff($upToDate);
            $monthsToDepreciate = ($interval->y * 12) + $interval->m;

            if ($monthsToDepreciate > 0) {
                $depreciationAmount = $monthsToDepreciate * $monthlyDepreciation;
                
                $totalAccumulated = $this->financialReportModel->getAccountLedger($asset->accumulated_depreciation_account_id, '1970-01-01', $upToDateStr)['running_balance_raw'];
                if (($totalAccumulated + $depreciationAmount) > $depreciableCost) {
                    $depreciationAmount = $depreciableCost - $totalAccumulated;
                }

                if ($depreciationAmount > 0) {
                    $entries[] = ['account_id' => $asset->expense_account_id, 'debit' => $depreciationAmount, 'credit' => 0];
                    $entries[] = ['account_id' => $asset->accumulated_depreciation_account_id, 'debit' => 0, 'credit' => $depreciationAmount];
                    $totalDepreciation += $depreciationAmount;
                    $this->fixedAssetModel->updateLastDepreciationDate($asset->id, $upToDateStr);
                }
            }
        }

        if ($totalDepreciation > 0) {
            $voucherId = $this->journalVoucherModel->create([
                'voucher_date' => $upToDateStr,
                'description' => 'سند استهلاک دوره منتهی به ' . $_POST['depreciation_date'],
                'user_id' => $this->auth->user()->id
            ]);
            foreach ($entries as $entry) {
                $this->journalEntryModel->create($voucherId, $entry);
            }
            FlashMessage::set('message', 'سند استهلاک با موفقیت صادر شد.');
        } else {
            FlashMessage::set('message', 'هیچ استهلاک جدیدی برای محاسبه در این دوره وجود نداشت.', 'info');
        }
        
        redirect(APP_URL . '/index.php?page=admin&action=general_journal');
    }
    public function cashFlowReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'صورت جریان وجوه نقد'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $cashFlowData = $this->financialReportModel->getCashFlowData($startDate, $endDate);
                
                $inflows = array_filter($cashFlowData['transactions'], function($tx) {
                    return $tx->debit > 0;
                });
                $outflows = array_filter($cashFlowData['transactions'], function($tx) {
                    return $tx->credit > 0;
                });
                
                $totalInflow = array_sum(array_column($inflows, 'debit'));
                $totalOutflow = array_sum(array_column($outflows, 'credit'));
                $netCashFlow = $totalInflow - $totalOutflow;

                $data['reportData'] = [
                    'opening_balance' => $cashFlowData['opening_balance'],
                    'inflows' => $inflows,
                    'outflows' => $outflows,
                    'total_inflow' => $totalInflow,
                    'total_outflow' => $totalOutflow,
                    'net_cash_flow' => $netCashFlow,
                    'ending_balance' => $cashFlowData['opening_balance'] + $netCashFlow
                ];
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/cash_flow_report', $data);
    }

    public function getAutomatedPayrollData() {
        header('Content-Type: application/json');
        $userId = $_GET['user_id'] ?? 0;
        $year = $_GET['year'] ?? jdate('Y');

        $employee = $this->userModel->findById($userId);
        $settings = (new PayrollSetting())->getSettingsByYear($year);

        if (!$employee || !$settings) {
            echo json_encode(['status' => 'error', 'message' => 'اطلاعات کارمند یا تنظیمات یافت نشد.']);
            exit;
        }

        $earnings = [];
        $deductions = [];

        $earnings[] = ['description' => 'حقوق پایه', 'amount' => $settings->base_salary_monthly];
        $earnings[] = ['description' => 'حق مسکن', 'amount' => $settings->housing_allowance];

        if ($employee->children_count > 0) {
            $earnings[] = ['description' => 'حق اولاد', 'amount' => $settings->family_allowance * $employee->children_count];
        }

        if ($employee->hire_date) {
            $hireDate = new DateTime($employee->hire_date);
            $today = new DateTime();
            $yearsOfService = $hireDate->diff($today)->y;
            if ($yearsOfService > 0) {
                $seniorityPay = $yearsOfService * $settings->seniority_per_year * $settings->work_days_in_month;
                $earnings[] = ['description' => 'پایه سنوات', 'amount' => $seniorityPay];
            }
        }
        
        $gross_earnings = array_sum(array_column($earnings, 'amount'));

        $insurance_amount = $gross_earnings * 0.07;
        $deductions[] = ['description' => 'بیمه تامین اجتماعی (سهم کارمند)', 'amount' => $insurance_amount];
        
        $tax_amount = ($gross_earnings > 10000000) ? ($gross_earnings - 10000000) * 0.10 : 0;
        $deductions[] = ['description' => 'مالیات بر درآمد', 'amount' => $tax_amount];


        echo json_encode([
            'status' => 'success',
            'earnings' => $earnings,
            'deductions' => $deductions
        ]);
        exit;
    }

    public function expenseAnalysisReport() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);

        $data = [
            'layout' => 'admin_layout',
            'title' => 'آنالیز هزینه‌ها'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['reportData'] = $this->financialReportModel->getExpenseAnalysis($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/expense_analysis', $data);
    }

    public function exportInvoicesToExcel() {
        $this->auth->restrict(['admin', 'accountant']);
        $invoices = $this->invoiceModel->getAllInvoices();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'شماره فاکتور');
        $sheet->setCellValue('B1', 'مشتری');
        $sheet->setCellValue('C1', 'مبلغ کل');
        $sheet->setCellValue('D1', 'تاریخ صدور');
        $sheet->setCellValue('E1', 'وضعیت');

        $rowNumber = 2;
        foreach ($invoices as $invoice) {
            $sheet->setCellValue('A' . $rowNumber, $invoice->invoice_number);
            $sheet->setCellValue('B' . $rowNumber, $invoice->client_name);
            $sheet->setCellValue('C' . $rowNumber, $invoice->total_amount);
            $sheet->setCellValue('D' . $rowNumber, htmlspecialchars(jdate('Y/m/d', strtotime($invoice->issue_date))));
            $sheet->setCellValue('E' . $rowNumber, $invoice->status);
            $rowNumber++;
        }

        ob_clean();
        ob_end_flush();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="invoices_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportContractsToExcel() {
        $this->auth->restrict(['admin', 'accountant']);
        $contracts = $this->contractModel->getAllContracts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'عنوان');
        $sheet->setCellValue('B1', 'مشتری');
        $sheet->setCellValue('C1', 'مبلغ کل');
        $sheet->setCellValue('D1', 'تاریخ شروع');
        $sheet->setCellValue('E1', 'وضعیت');

        $rowNumber = 2;
        foreach ($contracts as $contract) {
            $sheet->setCellValue('A' . $rowNumber, $contract->title);
            $sheet->setCellValue('B' . $rowNumber, $contract->client_name);
            $sheet->setCellValue('C' . $rowNumber, $contract->total_amount);
            $sheet->setCellValue('D' . $rowNumber, htmlspecialchars(jdate('Y/m/d', strtotime($contract->start_date))));
            $sheet->setCellValue('E' . $rowNumber, $contract->status);
            $rowNumber++;
        }
        
        ob_clean();
        ob_end_flush();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="contracts_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function announcementsIndex() {
        $this->auth->restrict(['admin']);
        $announcements = $this->announcementModel->getAll();
        view('admin/announcements/index', ['layout' => 'admin_layout', 'title' => 'مدیریت اطلاعیه‌ها', 'announcements' => $announcements]);
    }

    public function announcementsCreate() {
        $this->auth->restrict(['admin']);
        view('admin/announcements/create_edit', ['layout' => 'admin_layout', 'title' => 'ایجاد اطلاعیه جدید', 'announcement' => null]);
    }
    
    public function announcementsStore() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => sanitize($_POST['title']),
                'body' => $_POST['body'],
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

    public function announcementsEdit($id) {
        $this->auth->restrict(['admin']);
        $announcement = $this->announcementModel->findById($id);
        if (!$announcement) {
            FlashMessage::set('message', 'اطلاعیه مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
        }
        view('admin/announcements/create_edit', ['layout' => 'admin_layout', 'title' => 'ویرایش اطلاعیه', 'announcement' => $announcement]);
    }
    
    public function announcementsUpdate($id) {
        $this->auth->restrict(['admin']);
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
    
    public function announcementsDelete($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->announcementModel->delete($id)) {
                FlashMessage::set('message', 'اطلاعیه با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف اطلاعیه.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
    }
    
    public function manageTrainingCourses() {
        $this->auth->restrict(['admin']);
        $courses = $this->trainingCourseModel->getAll();
        view('admin/training/courses/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت دوره‌های آموزشی',
            'courses' => $courses
        ]);
    }
    
    public function storeTrainingCourse() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'course_title' => sanitize($_POST['course_title']),
                'description' => sanitize($_POST['description']),
                'target_audience' => implode(',', $_POST['target_audience'] ?? [])
            ];
            if ($this->trainingCourseModel->create($data)) {
                FlashMessage::set('message', 'دوره آموزشی با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت دوره آموزشی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=manage_training_courses');
        }
    }
    
    public function trainingNeedsAnalysis() {
        $this->auth->restrict(['admin']);
        $currentYear = $_GET['year'] ?? jdate('Y');
        
        $reportData = [
            'weakness_analysis' => [],
            'course_analysis' => [],
            'group_analysis' => []
        ];
        
        $reportData['weakness_analysis'] = $this->trainingCourseModel->getAnalysisReport($currentYear)['weakness_analysis'];
        $reportData['course_analysis'] = $this->trainingCourseModel->getAnalysisReport($currentYear)['course_analysis'];
        
        $reportData['group_analysis'] = $this->trainingNeedModel->getGroupAssessmentAnalysis($currentYear);

        view('admin/training/reports/analysis', [
            'layout' => 'admin_layout',
            'title' => 'گزارش تحلیلی نیازسنجی',
            'report' => $reportData,
            'currentYear' => $currentYear
        ]);
    }
    
    public function showAssessmentForm($userId) {
        $this->auth->restrict(['admin', 'employee']);
    }
    
    public function storeAssessmentScore() {
        $this->auth->restrict(['admin', 'employee']);
    }

    public function trainingNeeds() {
        $this->auth->restrict(['admin']);
        $pendingNeeds = $this->trainingNeedModel->getAllPending();
        view('admin/training/needs/index', [
            'layout' => 'admin_layout',
            'title' => 'نیازسنجی آموزشی',
            'pendingNeeds' => $pendingNeeds
        ]);
    }
    
    public function viewTrainingNeed($id) {
        $this->auth->restrict(['admin']);
        $trainingNeed = $this->trainingNeedModel->findById($id);
        if (!$trainingNeed) {
            FlashMessage::set('message', 'نیازسنجی مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=training_needs');
        }
        $courses = $this->trainingCourseModel->getAll();
        view('admin/training/needs/view', [
            'layout' => 'admin_layout',
            'title' => 'بررسی نیازسنجی',
            'trainingNeed' => $trainingNeed,
            'courses' => $courses
        ]);
    }
    
    public function processTrainingNeed($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'development_areas' => sanitize($_POST['development_areas']),
                'course_suggestions' => implode(',', $_POST['course_suggestions'] ?? []),
                'status' => sanitize($_POST['status']),
                'manager_id' => $this->auth->user()->id,
            ];
            if ($this->trainingNeedModel->update($id, $data)) {
                FlashMessage::set('message', 'نیازسنجی با موفقیت بررسی شد.');
            } else {
                FlashMessage::set('message', 'خطا در بررسی نیازسنجی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=training_needs');
        }
    }
    public function manageSkillAssessments() {
        $this->auth->restrict(['admin']);
        $employees = $this->userModel->getAllEmployees();
        $skills = $this->getAssessmentSkills();
        view('admin/training/assessments/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت آزمون‌های مهارتی',
            'employees' => $employees,
            'skills' => $skills
        ]);
    }
    
    public function sendPeerAssessmentRequest() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = sanitize($_POST['employee_id']);
            $peerIds = $_POST['peer_ids'] ?? [];
            $skillsToAssess = $_POST['skills_to_assess'] ?? [];
            
            FlashMessage::set('message', 'درخواست ارزیابی برای همکاران با موفقیت ارسال شد.');
            redirect(APP_URL . '/index.php?page=admin&action=manage_skill_assessments');
        }
    }

    private function getAssessmentSkills() {
        return [
            'technical_skills' => 'مهارت‌های فنی',
            'communication' => 'مهارت‌های ارتباطی',
            'problem_solving' => 'حل مسئله',
            'teamwork' => 'کار تیمی'
        ];
    }
    
    public function showApiSettings() {
        $this->auth->restrict(['admin']);
        
        $sql = "SELECT * FROM api_keys";
        $this->db->query($sql);
        $keys = $this->db->fetchAll();

        view('admin/settings/api_keys', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت کلیدهای API',
            'apiKeys' => $keys
        ]);
    }
    
    public function storeApiKey() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'provider' => sanitize($_POST['provider']),
                'api_key' => sanitize($_POST['api_key']),
                'daily_limit' => sanitize($_POST['daily_limit']),
                'status' => sanitize($_POST['status']),
                'last_reset_date' => date('Y-m-d')
            ];
            
            $sql = "INSERT INTO api_keys (provider, api_key, daily_limit, status, last_reset_date) VALUES (:provider, :api_key, :daily_limit, :status, :last_reset_date)";
            
            $this->db->query($sql);
            $this->db->bind(':provider', $data['provider']);
            $this->db->bind(':api_key', $data['api_key']);
            $this->db->bind(':daily_limit', $data['daily_limit']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':last_reset_date', $data['last_reset_date']);
            
            if ($this->db->execute()) {
                FlashMessage::set('message', 'کلید API با موفقیت ذخیره شد.');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره کلید API.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=showApiSettings');
        }
    }
    
    public function deleteApiKey($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sql = "DELETE FROM api_keys WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            if ($this->db->execute()) {
                FlashMessage::set('message', 'کلید API با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف کلید API.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=showApiSettings');
    }
    
    private function searchAndExtractClients($keyword, $province, $api_provider) {
        $locations = [
            'آذربایجان شرقی' => ['lat' => 38.07, 'lng' => 46.29, 'radius' => 50000],
            'آذربایجان غربی' => ['lat' => 37.55, 'lng' => 45.07, 'radius' => 50000],
            'اردبیل' => ['lat' => 38.24, 'lng' => 48.29, 'radius' => 50000],
            'اصفهان' => ['lat' => 32.65, 'lng' => 51.67, 'radius' => 50000],
            'البرز' => ['lat' => 35.84, 'lng' => 50.99, 'radius' => 50000],
            'ایلام' => ['lat' => 33.63, 'lng' => 46.42, 'radius' => 50000],
            'بوشهر' => ['lat' => 28.92, 'lng' => 50.83, 'radius' => 50000],
            'تهران' => ['lat' => 35.6892, 'lng' => 51.3890, 'radius' => 50000],
            'چهارمحال و بختیاری' => ['lat' => 32.32, 'lng' => 50.81, 'radius' => 50000],
            'خراسان جنوبی' => ['lat' => 32.86, 'lng' => 59.21, 'radius' => 50000],
            'خراسان رضوی' => ['lat' => 36.26, 'lng' => 59.61, 'radius' => 50000],
            'خراسان شمالی' => ['lat' => 37.47, 'lng' => 57.32, 'radius' => 50000],
            'خوزستان' => ['lat' => 31.31, 'lng' => 48.67, 'radius' => 50000],
            'زنجان' => ['lat' => 36.67, 'lng' => 48.48, 'radius' => 50000],
            'سمنان' => ['lat' => 35.57, 'lng' => 53.39, 'radius' => 50000],
            'سیستان و بلوچستان' => ['lat' => 29.49, 'lng' => 60.85, 'radius' => 50000],
            'فارس' => ['lat' => 29.62, 'lng' => 52.53, 'radius' => 50000],
            'قزوین' => ['lat' => 36.26, 'lng' => 50.00, 'radius' => 50000],
            'قم' => ['lat' => 34.64, 'lng' => 50.87, 'radius' => 50000],
            'کردستان' => ['lat' => 35.31, 'lng' => 47.00, 'radius' => 50000],
            'کرمان' => ['lat' => 30.29, 'lng' => 57.06, 'radius' => 50000],
            'کرمانشاه' => ['lat' => 34.32, 'lng' => 47.07, 'radius' => 50000],
            'کهگیلویه و بویراحمد' => ['lat' => 30.66, 'lng' => 51.58, 'radius' => 50000],
            'گلستان' => ['lat' => 36.83, 'lng' => 54.43, 'radius' => 50000],
            'گیلان' => ['lat' => 37.27, 'lng' => 49.58, 'radius' => 50000],
            'لرستان' => ['lat' => 33.46, 'lng' => 48.35, 'radius' => 50000],
            'مازندران' => ['lat' => 36.56, 'lng' => 53.05, 'radius' => 50000],
            'مرکزی' => ['lat' => 34.09, 'lng' => 49.69, 'radius' => 50000],
            'هرمزگان' => ['lat' => 27.18, 'lng' => 56.28, 'radius' => 50000],
            'همدان' => ['lat' => 34.79, 'lng' => 48.51, 'radius' => 50000],
            'یزد' => ['lat' => 31.89, 'lng' => 54.36, 'radius' => 50000],
        ];

        if (!isset($locations[$province])) {
            return [];
        }

        $locationData = $locations[$province];
        $results = [];

        $activeKey = $this->getActiveApiKey($api_provider);
        if (!$activeKey) {
            FlashMessage::set('message', "هیچ کلید API فعالی برای پلتفرم {$api_provider} وجود ندارد.", 'error');
            return [];
        }
        $apiKey = $activeKey->api_key;
        $lat = $locationData['lat'];
        $lng = $locationData['lng'];


        if ($api_provider === 'google') {
            $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . urlencode($keyword) . "&location=" . $lat . "," . $lng . "&radius=" . $locationData['radius'] . "&key=" . $apiKey;
            $response = @file_get_contents($url);
            $data = json_decode($response, true);
            $this->incrementApiKeyUsage($activeKey->id);

            if (isset($data['results'])) {
                foreach ($data['results'] as $place) {
                    $results[] = [
                        'name' => $place['name'] ?? null,
                        'email' => $this->getHunterIoEmail($place['website'] ?? null),
                        'phone' => $place['formatted_phone_number'] ?? null,
                        'address' => $place['formatted_address'] ?? null,
                        'company_name' => $place['name'] ?? null,
                        'user_type' => 'legal',
                        'website' => $place['website'] ?? null,
                        'source' => $api_provider
                    ];
                }
            }
        } elseif ($api_provider === 'neshan') {
            $url = "https://api.neshan.org/v1/search?term=" . urlencode($keyword) . "&lat=" . $lat . "&lng=" . $lng;

            $headers = [
                "Api-Key: " . $apiKey,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $this->incrementApiKeyUsage($activeKey->id);
            $data = json_decode($response, true);

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $results[] = [
                        'name' => $item['title'] ?? null,
                        'email' => $this->getHunterIoEmail($item['website'] ?? null),
                        'phone' => $item['phone'] ?? null,
                        'address' => $item['address'] ?? null,
                        'company_name' => $item['title'] ?? null,
                        'user_type' => 'legal',
                        'website' => $item['website'] ?? null,
                        'source' => $api_provider
                    ];
                }
            }
        } elseif ($api_provider === 'balad') {
            $url = "https://developers.balad.tech/search/..." . urlencode($keyword) . "...&api_key=" . $apiKey;
            
            FlashMessage::set('message', "پلتفرم بلد هنوز پیاده‌سازی نشده است.");
            return [];
        }

        return $results;
    }
    
    private function getHunterIoEmail($domain) {
        if (empty($domain)) {
            return null;
        }
        $apiKey = 'YOUR_HUNTER_API_KEY';
        $url = "https://api.hunter.io/v2/domain-search?domain=" . urlencode($domain) . "&api_key=" . $apiKey;
        
        if (ini_get('allow_url_fopen')) {
            $response = @file_get_contents($url);
            if ($response === FALSE) {
                error_log("Error fetching data from Hunter.io for domain: " . $domain);
                return null;
            }
            $data = json_decode($response, true);
            
            if (isset($data['data']['emails'][0]['value'])) {
                return $data['data']['emails'][0]['value'];
            }
        } else {
            error_log("Warning: allow_url_fopen is disabled. Cannot use file_get_contents for API calls.");
        }
        return null;
    }
    private function saveLeadsToDb($leads, $provider) {
        $importedCount = 0;
        foreach ($leads as $lead) {
            if (!empty($lead['email'])) { 
                $sql = "INSERT INTO marketing_leads (name, company_name, email, phone, address, website, source) VALUES (:name, :company_name, :email, :phone, :address, :website, :source)";
                $this->db->query($sql);
                $this->db->bind(':name', $lead['name'] ?? null);
                $this->db->bind(':company_name', $lead['company_name'] ?? null);
                $this->db->bind(':email', $lead['email'] ?? null);
                $this->db->bind(':phone', $lead['phone'] ?? null);
                $this->db->bind(':address', $lead['address'] ?? null);
                $this->db->bind(':website', $lead['website'] ?? null);
                $this->db->bind(':source', $provider);

                try {
                    $this->db->execute();
                    $importedCount++;
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        continue;
                    }
                }
            }
        }
        return $importedCount;
    }
    
    public function showMarketingForm() {
        $this->auth->restrict(['admin']);
        
        $maps_page = sanitize($_GET['maps_page'] ?? 1);
        $niaz_page = sanitize($_GET['niaz_page'] ?? 1);
        $neshan_page = sanitize($_GET['neshan_page'] ?? 1);
        $records_per_page = 20;

        $provinces = [
            'آذربایجان شرقی', 'آذربایجان غربی', 'اردبیل', 'اصفهان', 'البرز', 'ایلام',
            'بوشهر', 'تهران', 'چهارمحال و بختیاری', 'خراسان جنوبی', 'خراسان رضوی',
            'خراسان شمالی', 'خوزستان', 'زنجان', 'سمنان', 'سیستان و بلوچستان',
            'فارس', 'قزوین', 'قم', 'کردستان', 'کرمان', 'کرمانشاه', 'کهگیلویه و بویراحمد',
            'گلستان', 'گیلان', 'لرستان', 'مازندران', 'مرکزی', 'هرمزگان', 'همدان',
            'یزد',
        ];

        $maps_leads = $this->marketingLeadModel->getMapsLeadsPaginated($maps_page);
        $maps_total_records = $this->marketingLeadModel->countMapsLeads();
        $niazerooz_leads = $this->marketingLeadModel->getNiazroozLeadsPaginated($niaz_page);
        $niazerooz_total_records = $this->marketingLeadModel->countNiazroozLeads();
        $neshan_leads = $this->marketingLeadModel->getNeshanLeadsPaginated($neshan_page);
        $neshan_total_records = $this->marketingLeadModel->countNeshanLeads();

        view('admin/marketing/research_form', [
            'layout' => 'admin_layout',
            'title' => 'تحقیقات بازاریابی و جمع‌آوری لید',
            'provinces' => $provinces,
            'maps_leads' => $maps_leads,
            'maps_total_records' => $maps_total_records,
            'maps_current_page' => $maps_page,
            'niazerooz_leads' => $niazerooz_leads,
            'niazerooz_total_records' => $niazerooz_total_records,
            'niazerooz_current_page' => $niaz_page,
            'neshan_leads' => $neshan_leads,
            'neshan_total_records' => $neshan_total_records,
            'neshan_current_page' => $neshan_page,
            'records_per_page' => $records_per_page,
        ]);
    }
    
    public function deleteMapLead($id) {
        $this->auth->restrict(['admin']);
        if ($this->marketingLeadModel->deleteMapsLead($id)) {
            FlashMessage::set('message', 'لید با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف لید.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=showMarketingForm');
    }

    public function deleteNiazroozLead($id) {
        $this->auth->restrict(['admin']);
        if ($this->marketingLeadModel->deleteNiazroozLead($id)) {
            FlashMessage::set('message', 'لید با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف لید.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=showMarketingForm');
    }

    public function deleteNeshanLead($id) {
        $this->auth->restrict(['admin']);
        if ($this->marketingLeadModel->deleteNeshanLead($id)) {
            FlashMessage::set('message', 'لید با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف لید.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=showMarketingForm');
    }

    public function products() {
        $this->auth->restrict(['admin', 'accountant']);
        $products = $this->productModel->getAll();
        view('admin/inventory/products/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت محصولات',
            'products' => $products
        ]);
    }
    
    public function storeProduct() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'sku' => sanitize($_POST['sku']),
                'purchase_price' => sanitize($_POST['purchase_price']),
                'sale_price' => sanitize($_POST['sale_price']),
            ];
            
            if ($this->productModel->create($data)) {
                FlashMessage::set('message', 'محصول جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت محصول.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=products');
        }
    }
    
    public function purchases() {
        $this->auth->restrict(['admin', 'accountant']);
        $purchases = $this->purchaseModel->getAllPurchases();
        view('admin/inventory/purchases/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت خریدها',
            'purchases' => $purchases
        ]);
    }
    
    public function createPurchase() {
        $this->auth->restrict(['admin', 'accountant']);
        $products = $this->productModel->getAll();
        $vendors = $this->clientModel->getAllClients();
        view('admin/inventory/purchases/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت خرید جدید',
            'products' => $products,
            'vendors' => $vendors
        ]);
    }
    
    public function storePurchase() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'vendor_id' => sanitize($_POST['vendor_id']),
                'purchase_date' => $this->convertJalaliToGregorianString($_POST['purchase_date']),
                'status' => 'pending',
                'items' => $_POST['items']
            ];
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }
            $data['total_amount'] = $totalAmount;
            
            $purchaseId = $this->purchaseModel->createPurchase($data);
            
            if ($purchaseId) {
                $inventoryAccount = $this->accountModel->findByCode('AS-1201');
                $accountsPayable = $this->accountModel->findByCode('LI-2104');

                if ($inventoryAccount && $accountsPayable) {
                    $voucherId = $this->journalVoucherModel->create([
                        'voucher_date' => $data['purchase_date'],
                        'description' => 'سند خرید کالا به فاکتور شماره ' . $purchaseId,
                        'user_id' => $this->auth->user()->id
                    ]);
                    if ($voucherId) {
                        $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => $totalAmount, 'credit' => 0]);
                        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsPayable->id, 'debit' => 0, 'credit' => $totalAmount]);
                    }
                }
                
                FlashMessage::set('message', 'فاکتور خرید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور خرید.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=purchases');
        }
    }
    
    public function sales() {
        $this->auth->restrict(['admin', 'accountant']);
        $sales = $this->saleModel->getAllSales();
        view('admin/inventory/sales/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت فروش‌ها',
            'sales' => $sales
        ]);
    }

    public function createSale() {
        $this->auth->restrict(['admin', 'accountant']);
        $products = $this->productModel->getAll();
        $clients = $this->clientModel->getAllClients();
        view('admin/inventory/sales/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت فروش جدید',
            'products' => $products,
            'clients' => $clients
        ]);
    }
    
    public function storeSale() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => sanitize($_POST['client_id']),
                'sale_date' => $this->convertJalaliToGregorianString($_POST['sale_date']),
                'status' => 'pending',
                'items' => $_POST['items']
            ];
            
            $totalAmount = 0;
            $cogsAmount = 0;
            foreach ($data['items'] as $item) {
                $product = $this->productModel->findById($item['product_id']);
                $totalAmount += $item['quantity'] * $item['price'];
                $cogsAmount += $item['quantity'] * $product->purchase_price;
            }
            $data['total_amount'] = $totalAmount;
            
            $saleId = $this->saleModel->createSale($data);
            
            if ($saleId) {
                $accountsReceivable = $this->accountModel->findByCode('AR-1103');
                $salesRevenue = $this->accountModel->findByCode('SR-4101');
                $cogsAccount = $this->accountModel->findByCode('EX-5103');
                $inventoryAccount = $this->accountModel->findByCode('AS-1201');
                
                if ($accountsReceivable && $salesRevenue && $cogsAccount && $inventoryAccount) {
                    $voucherId = $this->journalVoucherModel->create([
                        'voucher_date' => $data['sale_date'],
                        'description' => 'سند فروش کالا به فاکتور شماره ' . $saleId,
                        'user_id' => $this->auth->user()->id
                    ]);
                    if ($voucherId) {
                        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
                        $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $totalAmount]);
                        
                        $this->journalEntryModel->create($voucherId, ['account_id' => $cogsAccount->id, 'debit' => $cogsAmount, 'credit' => 0]);
                        $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $cogsAmount]);
                    }
                }
                
                FlashMessage::set('message', 'فاکتور فروش با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور فروش.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=sales');
        }
    }
    
    public function profitAndLossReport() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارش سود و زیان'
        ];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);
    
            if ($startDate && $endDate) {
                $data['reportData'] = $this->financialReportModel->getFullProfitAndLossReport($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }
    
        view('admin/accounting/profit_and_loss', $data);
    }
    public function addChecklistItem() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'task_id' => sanitize($_POST['task_id']),
            'item_text' => sanitize($_POST['item_text']),
        ];

        // از مدل Task برای افزودن آیتم استفاده می‌کنیم
        $taskId = $data['task_id'];
        if ($this->taskModel->addChecklistItem($data['task_id'], $data['item_text'])) {
            FlashMessage::set('message', 'آیتم چک‌لیست با موفقیت اضافه شد.');
        } else {
            FlashMessage::set('message', 'خطا در افزودن آیتم چک‌لیست.', 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=view_project&id=' . $this->taskModel->getProjectIdByTaskId($taskId));
    }
}

public function addProjectMember() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $projectId = sanitize($_POST['project_id']);
        $userId = sanitize($_POST['user_id']);
        $role = sanitize($_POST['role']);

        if ($this->projectModel->addMember($projectId, $userId, $role)) {
            FlashMessage::set('message', 'عضو با موفقیت به پروژه اضافه شد.');
        } else {
            FlashMessage::set('message', 'خطا در اضافه کردن عضو.', 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=view_project&id=' . $projectId);
    }
}

}