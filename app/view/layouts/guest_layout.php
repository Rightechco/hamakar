<?php
// app/views/layouts/guest_layout.php

if (!isset($title)) $title = "سامانه مدیریت پرداخت مشتریان";
if (!isset($content)) $content = "";
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($title); ?> - سامانه CRM</title>
    <link href="<?php echo APP_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?php echo APP_URL; ?>/assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-light"> 
    <div class="d-flex justify-content-center align-items-center min-vh-100 p-3">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <?php FlashMessage::display(); ?>
            <?php echo $content; ?>
        </div>
    </div>

    <script src="<?php echo APP_URL; ?>/assets/js/jquery.min.js"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script>const APP_URL = "<?php echo APP_URL; ?>";</script>
    <script src="<?php echo APP_URL; ?>/assets/js/custom.js"></script>
</body>
</html>