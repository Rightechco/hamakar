<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($title ?? 'چاپ'); ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />

    <style>
        body { font-family: 'Vazirmatn', sans-serif; background: white; color: black; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body onload="window.print()" class="p-8">

    <div class="max-w-5xl mx-auto">
        <?php echo $content; ?>
    </div>

</body>
</html>