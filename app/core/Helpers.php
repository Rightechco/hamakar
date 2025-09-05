<?php
// app/core/Helpers.php - نسخه کامل و اصلاح شده

// Function to redirect
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Function to dump and die (for debugging)
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''));
}

// Function to load a view file
function view($path, $data = []) {
    // Extract data so variables are available in the view
    extract($data);

    // Determine the actual view file path
    $viewPath = __DIR__ . '/../views/' . $path . '.php';

    if (!file_exists($viewPath)) {
        die("View file not found: " . $viewPath);
    }

    // Start output buffering
    ob_start();
    require_once $viewPath;
    $content = ob_get_clean(); // Get the content of the view

    // Assume we're using a layout. You can customize this logic.
    // The layout will expect a $content variable.
    // For specific layouts, you might pass a 'layout' parameter in $data
    if (isset($layout) && file_exists(__DIR__ . '/../views/layouts/' . $layout . '.php')) {
        require_once __DIR__ . '/../views/layouts/' . $layout . '.php';
    } else {
        echo $content; // If no specific layout, just output the content
    }
}

/**
 * ✅ این تابع به جایگاه صحیح خود در خارج از تابع view منتقل شد
 * کاربر را به صفحه‌ای که از آن آمده است بازمی‌گرداند.
 */
function redirect_back() {
    // اگر آدرس صفحه قبلی در دسترس بود، به آن بازگرد
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // در غیر این صورت، به صفحه اصلی بازگرد
    redirect(APP_URL);
}
// app/core/Helpers.php

function log_activity($action, $description) {
    global $auth; // دسترسی به آبجکت auth
    if ($auth->check()) {
        $db = new Database();
        $db->query('INSERT INTO audit_logs (user_id, user_name, action_type, description) VALUES (:user_id, :user_name, :action_type, :description)');
        $db->bind(':user_id', $auth->user()->id);
        $db->bind(':user_name', $auth->user()->name);
        $db->bind(':action_type', $action);
        $db->bind(':description', $description);
        $db->execute();
    }
}
