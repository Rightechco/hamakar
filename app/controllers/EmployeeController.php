<?php
// app/controllers/EmployeeController.php

require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/TrainingNeed.php';
require_once __DIR__ . '/../models/SkillAssessment.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/FlashMessage.php';
require_once __DIR__ . '/../lib/JalaliDate.php';

class EmployeeController {
    private $auth;
    private $attendanceModel;
    private $leaveRequestModel;
    private $trainingNeedModel;
    private $skillAssessmentModel;
    private $projectModel;
    private $taskModel;
    private $userModel;
    private $payrollModel;
    private $user;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['employee', 'admin']);
        
        $this->attendanceModel = new Attendance();
        $this->leaveRequestModel = new LeaveRequest();
        $this->trainingNeedModel = new TrainingNeed();
        $this->skillAssessmentModel = new SkillAssessment();
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->userModel = new User();
        $this->payrollModel = new Payroll();
        $this->user = $this->auth->user();
    }

    public function dashboard() {
        $openSession = $this->attendanceModel->getOpenSession($this->user->id);
        
        view('employee/dashboard', [
            'layout' => 'admin_layout',
            'title' => 'داشبورد کارمند',
            'user' => $this->user,
            'openSession' => $openSession
        ]);
    }

    public function processClocking() {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $openSession = $this->attendanceModel->getOpenSession($this->user->id);

        if ($openSession) {
            $this->attendanceModel->clockOut($openSession->id, $ipAddress);
            FlashMessage::set('message', 'خروج شما با موفقیت ثبت شد.');
        } else {
            $this->attendanceModel->clockIn($this->user->id, $ipAddress);
            FlashMessage::set('message', 'ورود شما با موفقیت ثبت شد.');
        }

        redirect(APP_URL . '/index.php?page=employee&action=dashboard');
    }

    public function leaveRequests() {
        $requests = $this->leaveRequestModel->getForUser($this->user->id);
        view('employee/leave/index', [
            'layout' => 'admin_layout',
            'title' => 'درخواست‌های مرخصی من',
            'requests' => $requests
        ]);
    }

    public function submitLeaveRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $leaveType = sanitize($_POST['leave_type']);
            $reason = sanitize($_POST['reason']);
            $gregorianStartDate = null;
            $gregorianEndDate = null;

            if ($leaveType === 'daily') {
                $startDateJalali = sanitize($_POST['start_date']);
                $endDateJalali = sanitize($_POST['end_date']);
                
                if (!empty($startDateJalali) && !empty($endDateJalali)) {
                    $gregorianStartDate = $this->convertJalaliToGregorianString($startDateJalali) . ' 00:00:00';
                    $gregorianEndDate = $this->convertJalaliToGregorianString($endDateJalali) . ' 23:59:59';
                }
            } else {
                $dateJalali = sanitize($_POST['start_date_hourly']);
                $startTime = sanitize($_POST['start_time']);
                $endTime = sanitize($_POST['end_time']);
                
                if (!empty($dateJalali) && !empty($startTime) && !empty($endTime)) {
                    $gregorianDate = $this->convertJalaliToGregorianString($dateJalali);
                    $gregorianStartDate = $gregorianDate . ' ' . $startTime;
                    $gregorianEndDate = $gregorianDate . ' ' . $endTime;
                }
            }

            if (empty($gregorianStartDate) || empty($gregorianEndDate) || empty($reason)) {
                FlashMessage::set('message', 'لطفاً تمام فیلدهای لازم را به درستی پر کنید.', 'error');
                redirect(APP_URL . '/index.php?page=employee&action=leave_requests');
                return;
            }

            $data = [
                'user_id' => $this->user->id,
                'leave_type' => $leaveType,
                'start_date' => $gregorianStartDate,
                'end_date' => $gregorianEndDate,
                'reason' => $reason
            ];
            
            if ($this->leaveRequestModel->create($data)) {
                FlashMessage::set('message', 'درخواست مرخصی شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت درخواست.', 'error');
            }
            redirect(APP_URL . '/index.php?page=employee&action=leave_requests');
        }
    }

    public function myPayslips() {
        $userId = $this->auth->user()->id;
        $payslips = $this->payrollModel->getPayrollsForUser($userId);

        view('employee/payslips/index', [
            'layout' => 'admin_layout',
            'title' => 'فیش‌های حقوقی من',
            'payslips' => $payslips
        ]);
    }

    public function viewMyPayslip($id) {
        $userId = $this->auth->user()->id;
        $payroll = $this->payrollModel->findPayrollByIdWithDetails($id);

        if (!$payroll || $payroll->user_id != $userId) {
            FlashMessage::set('message', 'شما به این صفحه دسترسی ندارید.', 'error');
            redirect(APP_URL . '/index.php?page=employee&action=dashboard');
            return;
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

    public function myProjects() {
        $this->auth->restrict(['employee']);
        $user = $this->auth->user();

        $projects = $this->projectModel->getProjectsByUserId($user->id);

        foreach ($projects as $project) {
            $project->tasks = $this->taskModel->getTasksByProjectIdAndUserId($project->id, $user->id);
            foreach ($project->tasks as $task) {
                $task->checklist_items = $this->taskModel->getChecklistItemsByTaskId($task->id);
            }
        }

        view('employee/projects/index', [
            'layout' => 'admin_layout',
            'title' => 'پروژه های من',
            'projects' => $projects
        ]);
    }

    public function updateTaskStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = sanitize($_POST['task_id']);
            $status = sanitize($_POST['status']);
            $userId = $this->auth->user()->id;

            $task = $this->taskModel->findById($taskId);

            if ($task && $task->assigned_to_user_id == $userId) {
                if ($this->taskModel->updateStatus($taskId, $status)) {
                    FlashMessage::set('message', 'وضعیت وظیفه با موفقیت به‌روزرسانی شد.');
                } else {
                    FlashMessage::set('message', 'خطا در به‌روزرسانی وظیفه.', 'error');
                }
            } else {
                FlashMessage::set('message', 'شما اجازه تغییر این وظیفه را ندارید.', 'error');
            }
            redirect_back();
        }
    }
    
    public function showTrainingNeedsForm() {
        $userId = $this->auth->user()->id;
        $currentYear = jdate('Y');
        $existingNeed = $this->trainingNeedModel->getNeedByUserIdAndYear($userId, $currentYear);
        
        view('employee/training/needs_form', [
            'layout' => 'admin_layout',
            'title' => 'نیازسنجی آموزشی من',
            'existingNeed' => $existingNeed
        ]);
    }

    public function submitTrainingNeed() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $this->auth->user()->id,
                'year' => jdate('Y'),
                'strengths' => sanitize($_POST['strengths']),
                'weaknesses' => sanitize($_POST['weaknesses']),
            ];
            if ($this->trainingNeedModel->create($data)) {
                FlashMessage::set('message', 'نیازسنجی شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت نیازسنجی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=employee&action=dashboard');
        }
    }
        
    public function showAssessmentForm() {
        $user = $this->auth->user();
        $currentYear = jdate('Y');
        
        $selfAssessmentDone = $this->skillAssessmentModel->getAssessmentByEvaluator($user->id, $user->id, 'self', $currentYear);
        
        $peers = $this->userModel->getPeersByUserId($user->id); 
        
        $managerAssessmentDone = $this->skillAssessmentModel->getAssessmentByEvaluator($user->manager_id, $user->id, 'manager', $currentYear);
        $peerAssessmentsDone = $this->skillAssessmentModel->getPeerAssessmentsStatus($user->id, $peers, $currentYear);
        
        view('employee/training/assessment_form', [
            'layout' => 'admin_layout',
            'title' => 'آزمون ۳۶۰ درجه مهارتی',
            'user' => $user,
            'skills' => $this->getStandardSkills(),
            'selfAssessmentDone' => $selfAssessmentDone,
            'managerAssessmentDone' => $managerAssessmentDone,
            'peerAssessmentsDone' => $peerAssessmentsDone,
            'peers' => $peers
        ]);
    }
    
    public function storeAssessmentScore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $evaluatorId = $this->auth->user()->id;
            $userId = sanitize($_POST['user_id']);
            $evaluationType = sanitize($_POST['evaluation_type']);
            $year = jdate('Y');
            
            $errors = [];
            foreach ($_POST['scores'] as $skill => $score) {
                if (empty($score)) {
                    $errors[] = "لطفاً برای همه مهارت‌ها امتیاز وارد کنید.";
                    break;
                }
                $data = [
                    'user_id' => $userId,
                    'evaluator_id' => $evaluatorId,
                    'evaluation_type' => $evaluationType,
                    'year' => $year,
                    'skill_category' => $skill,
                    'score' => sanitize($score),
                    'notes' => sanitize($_POST['notes'][$skill] ?? '')
                ];
                $this->skillAssessmentModel->createScore($data);
            }
            
            if (empty($errors)) {
                FlashMessage::set('message', 'ارزیابی شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', implode('<br>', $errors), 'error');
            }
            
            redirect(APP_URL . '/index.php?page=employee&action=show_assessment_form');
        }
    }
    
    public function viewAssessmentResults() {
        $user = $this->auth->user();
        $currentYear = jdate('Y');
        
        $results = $this->trainingNeedModel->getAssessmentResults($user->id, $currentYear);
        
        view('employee/training/assessment_results', [
            'layout' => 'admin_layout',
            'title' => 'نتایج ارزیابی مهارتی',
            'results' => $results,
            'skills' => $this->getStandardSkills()
        ]);
    }
    
    private function getStandardSkills() {
        return [
            'technical_skills' => 'مهارت‌های فنی',
            'communication' => 'مهارت ارتباطی',
            'teamwork' => 'کار تیمی',
            'problem_solving' => 'حل مسئله',
            'time_management' => 'مدیریت زمان'
        ];
    }

    public function showSelfAssessmentForm() {
        $this->auth->restrict(['employee']);
        $userId = $this->auth->user()->id;
        $currentYear = jdate('Y');
        
        $isSubmitted = $this->skillAssessmentModel->hasAssessmentBeenSubmitted($userId, $currentYear, 'self');
        $skills = $this->getAssessmentSkills();

        view('employee/training/assessments/self_form', [
            'layout' => 'admin_layout',
            'title' => 'آزمون خودارزیابی',
            'isSubmitted' => $isSubmitted,
            'skills' => $skills
        ]);
    }

    public function submitSelfAssessment() {
        $this->auth->restrict(['employee']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->auth->user()->id;
            $currentYear = jdate('Y');
            
            if ($this->skillAssessmentModel->hasAssessmentBeenSubmitted($userId, $currentYear, 'self')) {
                FlashMessage::set('message', 'شما قبلاً خودارزیابی خود را برای این سال ثبت کرده‌اید.', 'warning');
                redirect(APP_URL . '/index.php?page=employee&action=show_self_assessment_form');
                return;
            }

            $data = [
                'user_id' => $userId,
                'evaluator_id' => $userId,
                'evaluation_type' => 'self',
                'year' => $currentYear,
                'scores' => $_POST['scores'] ?? [],
                'notes' => sanitize($_POST['notes'] ?? '')
            ];
            
            if ($this->skillAssessmentModel->saveScores($data)) {
                FlashMessage::set('message', 'خودارزیابی شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت خودارزیابی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=employee&action=dashboard');
        }
    }
    
    public function showPeerAssessmentForm() {
        $this->auth->restrict(['employee']);
        $skills = $this->getAssessmentSkills();
        $employeeId = $_GET['id'] ?? null;
        $targetEmployee = null;

        if (!empty($employeeId)) {
            $targetEmployee = $this->userModel->findById($employeeId);
        }
        
        if (!$targetEmployee) {
             FlashMessage::set('message', 'کارمند مورد نظر یافت نشد.', 'error');
             redirect(APP_URL . '/index.php?page=employee&action=dashboard');
             return;
        }

        $isSubmitted = $this->skillAssessmentModel->hasAssessmentBeenSubmitted($employeeId, jdate('Y'), 'peer');
        
        view('employee/training/assessments/peer_form', [
            'layout' => 'admin_layout',
            'title' => 'ارزیابی همکار',
            'targetEmployee' => $targetEmployee,
            'skills' => $skills,
            'isSubmitted' => $isSubmitted
        ]);
    }

    public function submitPeerAssessment() {
        $this->auth->restrict(['employee']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = sanitize($_POST['user_id']);
            $evaluatorId = $this->auth->user()->id;
            $currentYear = jdate('Y');
            
            $data = [
                'user_id' => $userId,
                'evaluator_id' => $evaluatorId,
                'evaluation_type' => 'peer',
                'year' => $currentYear,
                'scores' => $_POST['scores'] ?? [],
                'notes' => sanitize($_POST['notes'] ?? '')
            ];

            if ($this->skillAssessmentModel->saveScores($data)) {
                FlashMessage::set('message', 'ارزیابی شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت ارزیابی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=employee&action=dashboard');
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
    
    public function toggleChecklistItem() {
        $this->auth->restrict(['employee']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = sanitize($_POST['item_id']);
            $isCompleted = sanitize($_POST['is_completed']);

            if ($this->taskModel->toggleChecklistItem($itemId, $isCompleted)) {
                http_response_code(200);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطا در به‌روزرسانی وضعیت.']);
            }
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
}