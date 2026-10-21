<?php
// app/core/Helpers.php - نسخه اصلاح‌شده و ایمن

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''));
}

function view($path, $data = []) {
    extract($data);

    $viewPath = __DIR__ . '/../views/' . $path . '.php';

    if (!file_exists($viewPath)) {
        die("View file not found: " . $viewPath);
    }

    ob_start();
    require_once $viewPath;
    $content = ob_get_clean();

    $layoutName = $layout ?? 'client_layout';
    $layoutPath = __DIR__ . '/../views/layouts/' . $layoutName . '.php';

    if (file_exists($layoutPath)) {
        require_once $layoutPath;
    } else {
        echo $content;
    }
}

function redirect_back() {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    redirect(defined('APP_URL') ? APP_URL : '/');
}

function log_activity($action, $description) {
    global $auth;
    if (isset($auth) && $auth->check()) {
        $db = new Database();
        $db->query('INSERT INTO audit_logs (user_id, user_name, action_type, description) VALUES (:user_id, :user_name, :action_type, :description)');
        $db->bind(':user_id', $auth->user()->id);
        $db->bind(':user_name', $auth->user()->name);
        $db->bind(':action_type', $action);
        $db->bind(':description', $description);
        $db->execute();
    }
}

// بررسی عدم وجود تابع جهت جلوگیری از خطای Redeclare
if (!function_exists('getStatusInPersian')) {
    function getStatusInPersian($status) {
        switch ($status) {
            case 'todo':
                return 'برای انجام';
            case 'in_progress':
                return 'در حال انجام';
            case 'review':
                return 'در حال بررسی';
            case 'done':
                return 'انجام شده';
            default:
                return $status;
        }
    }
}