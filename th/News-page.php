<?php
require_once('php-backend/server.php');
$db = new server();
$ierp = $db->connect_sql();


?>

<!DOCTYPE html>
<html lang="th-TH">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        Wichien Dynamic Industry </title>

    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/css/settings.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/style.css" type="text/css" media="all" />

    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/wdi/www.wdi.co.th/wp-includes/js/jquery/jquery-migrate.min.js"></script>
    <script src="/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>
    <script src="/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" sizes="192x192" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Barlow', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* เพิ่ม Barlow เป็นอันดับแรก */
            margin: 0;
            background-color: #f8f8f8;
        }
    </style>
</head>

<body class="home page-template page-template-page-home page page-id-2792 woocommerce-no-js">

    <?php require 'nav-bar.php'; ?>
    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet" type="text/css" media="all" />
    <?php require 'slider-index-page.php'; ?>
    <div class="container">



    </div>

    <div class="clearfix"></div>


    <?php require 'footer-page.php'; ?>

    <!-- JavaScript for News Carousel -->
    <script src="/wdi/www.wdi.co.th/th/js-control/manage-index.js"></script>

</body>

</html>