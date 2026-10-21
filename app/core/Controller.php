<?php
/*
 * app/core/Controller.php
 * کلاس مادر: همراه با پشتیبانی کامل از سیستم لایوت (سایدبار و نوبار)
 */
class Controller {
    
    // متد فراخوانی مدل
    public function model($model) {
        $path = '../app/models/' . $model . '.php';
        if (file_exists($path)) {
            require_once $path;
            return new $model();
        }
        $path2 = 'app/models/' . $model . '.php';
        if (file_exists($path2)) {
            require_once $path2;
            return new $model();
        }
        return null;
    }

    // متد فراخوانی ویو (View) - اصلاح‌شده جهت رندر لایوت، سایدبار و نوبار
    public function view($view, $data = []) {
        if (!empty($data)) {
            extract($data);
        }

        // ۱. پیدا کردن مسیر فایل ویو اصلی
        $viewFile1 = '../app/views/' . $view . '.php';
        $viewFile2 = 'app/views/' . $view . '.php';
        $viewFile3 = defined('APPROOT') ? APPROOT . '/views/' . $view . '.php' : null;

        $targetViewFile = null;
        if (file_exists($viewFile1)) {
            $targetViewFile = $viewFile1;
        } elseif (file_exists($viewFile2)) {
            $targetViewFile = $viewFile2;
        } elseif ($viewFile3 && file_exists($viewFile3)) {
            $targetViewFile = $viewFile3;
        }

        if (!$targetViewFile) {
            die("<div style='background:#f8d7da;color:#721c24;padding:20px;margin:20px;border:1px solid red;'>
                 <strong>View Error:</strong> فایل نمایش (View) پیدا نشد.<br>
                 نام ویو درخواست شده: <b>$view</b>
                 </div>");
        }

        // ۲. ضبط محتوای ویو درون متغیر $content
        ob_start();
        require_once $targetViewFile;
        $content = ob_get_clean();

        // ۳. بررسی و رندر لایوت (سایدبار و نوبار درون لایوت قرار دارند)
        $layoutName = $layout ?? 'client_layout';

        $layoutPath1 = '../app/views/layouts/' . $layoutName . '.php';
        $layoutPath2 = 'app/views/layouts/' . $layoutName . '.php';
        $layoutPath3 = defined('APPROOT') ? APPROOT . '/views/layouts/' . $layoutName . '.php' : null;

        if (file_exists($layoutPath1)) {
            require_once $layoutPath1;
        } elseif (file_exists($layoutPath2)) {
            require_once $layoutPath2;
        } elseif ($layoutPath3 && file_exists($layoutPath3)) {
            require_once $layoutPath3;
        } else {
            // اگر فایل لایوت یافت نشد، فقط خود محتوا چاپ شود
            echo $content;
        }
    }
}