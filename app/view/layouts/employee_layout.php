<?php
// app/views/layouts/employee_layout.php
global $auth;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($title ?? 'پنل کارمندی'); ?> - رایان تکرو</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .wrapper { display: flex; }
        #sidebar { min-width: 250px; max-width: 250px; background: #343a40; color: #fff; transition: all 0.3s; }
        #content { width: 100%; padding: 20px; }
        /* استایل‌های ساده برای سایدبار کارمند */
        .sidebar-header { padding: 20px; background: #212529; text-align: center; }
        .sidebar .list-unstyled a { padding: 10px; font-size: 1.1em; display: block; color: #adb5bd; text-decoration: none; }
        .sidebar .list-unstyled a:hover { color: #fff; background: #495057; }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>پنل کارمندی</h3>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="index.php?page=employee&action=dashboard"><i class="fas fa-tachometer-alt"></i> داشبورد</a>
            </li>
            <li>
                <a href="index.php?page=employee&action=leave_requests"><i class="fas fa-calendar-alt"></i> درخواست مرخصی</a>
            </li>
            <li>
                <a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </li>
        </ul>
    </nav>

    <div id="content">
        <div class="container-fluid">
            <?php FlashMessage::display(); ?>
            <?php echo $content; // محتوای اصلی صفحه در اینجا نمایش داده می‌شود ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>