<?php
// app/views/layouts/guest_layout.php - پوسته پرمیوم ورود و صفحات مهمان هماکار ERP
if (!isset($title)) $title = "ورود به پرتال سازمانی";
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="https://hamakar.ir/manifest.webmanifest">
    <link rel="apple-touch-icon" href="https://hamakar.ir/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="https://hamakar.ir/images/favicon-32x32.png">

    <title><?php echo sanitize($title); ?> | سامانه یکپارچه هماکار ERP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --primary-border: #c7d2fe;
            --primary-glow: rgba(79, 70, 229, 0.16);
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            font-family: 'Vazirmatn', sans-serif !important;
            font-feature-settings: "ss01", "ss02";
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(circle at 12% 15%, rgba(79, 70, 229, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 88% 85%, rgba(6, 182, 212, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.04) 0%, transparent 40%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            margin: 0;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <?php FlashMessage::display(); ?>
        <?php echo $content ?? ''; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
