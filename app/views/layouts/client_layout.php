<?php global $auth; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'پنل کاربری'; ?> - رایان تکرو</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    
    <link href="/assets/css/styles.css" rel="stylesheet">
</head>
<body>

<div class="wrapper d-flex">
    <?php include_once __DIR__ . '/../shared/sidebar.php'; // سایدبار مشترک با تمام نقش‌ها ?>
    
    <div id="content-wrapper" class="w-100">
        <?php include_once __DIR__ . '/../shared/navbar.php'; // نوبار مشترک ?>
        
        <main class="p-4">
            <?php FlashMessage::display(); ?>
            
            <?php echo $content; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>