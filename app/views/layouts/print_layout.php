<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($title ?? 'چاپ'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: #fff;
            color: #000;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        .container-fluid {
            padding: 2rem;
        }
    </style>
</head>
<body onload="window.print()">

<?php echo $content; ?>

</body>
</html>