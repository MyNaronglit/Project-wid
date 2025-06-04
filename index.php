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

        .carousel {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-inner .item {
            width: 100%;
            /* ปรับให้มีขนาด 100% ของ container */
            flex-shrink: 0;
            /* ไม่ให้ขนาดเล็กลงเมื่อเลื่อน */
        }

        .carousel-control-prev,
        .carousel-control-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
            color: #fff;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 0.5rem;
            cursor: pointer;
        }

        .carousel-control-prev {
            left: 0;
        }

        .carousel-control-next {
            right: 0;
        }

        .qr-code-grid {
            display: flex;
            flex-wrap: wrap;
            /* ทำให้รูปภาพขึ้นบรรทัดใหม่เมื่อไม่พอ */
            justify-content: center;
            /* จัดให้อยู่กึ่งกลางเมื่อมี 2 รูปต่อแถว */
            gap: 15px;
            /* ระยะห่างระหว่างรูปภาพ */
        }

        .qr-code-item {
            flex: 1 1 calc(50% - 15px);
            /* ทำให้แต่ละรูปกว้างประมาณ 50% ของ container ลบด้วย gap */
            max-width: calc(50% - 15px);
            /* จำกัดความกว้างไม่ให้เกิน 50% */
            text-align: center;
            /* จัดรูปภาพให้อยู่กึ่งกลางใน item ของมัน */
        }

        .qr-code-item img {
            max-width: 100%;
            /* ทำให้รูปภาพปรับขนาดตามความกว้างของ parent */
            height: auto;
            /* รักษาอัตราส่วนของรูปภาพ */
            display: block;
            /* ลบช่องว่างด้านล่างรูปภาพ (ถ้ามี) */
            margin: 0 auto;
            /* จัดรูปภาพให้อยู่กึ่งกลาง */
            border: 1px solid #ddd;
            /* เพิ่มกรอบเล็กน้อยให้ดูเรียบร้อย */
            border-radius: 5px;
            /* มุมโค้งมนเล็กน้อย */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* เพิ่มเงาเล็กน้อย */
        }

        /* ปรับแต่งสำหรับหน้าจอขนาดเล็ก (ไม่เกิน 768px - ต่ำกว่า md) */
        @media (max-width: 767.98px) {
            .qr-code-item {
                flex: 1 1 calc(50% - 10px);
                /* ปรับ gap เล็กน้อยสำหรับจอเล็ก */
                max-width: calc(50% - 10px);
                gap: 10px;
            }
        }
    </style>
</head>

<body class="home page-template page-template-page-home page page-id-2792 woocommerce-no-js">

    <?php require 'nav-bar.php'; ?>
    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet" type="text/css" media="all" />
    <?php require 'slider-index-page.php'; ?>
    <div class="container">
        <!-- News Carousel Section -->
        <div class="shortcut-container col-lg-4 col-md-4">
            <div class="shortcut" style="height: auto;">
                <div class="shortcut-name">News</div>
                <div id="carousel-news" class="carousel">
                    <ol class="carousel-indicators"></ol>
                    <div class="carousel-inner"></div>
                    <a class="carousel-control-prev" data-slide="prev">&#10094;</a>
                    <a class="carousel-control-next" data-slide="next">&#10095;</a>
                </div>

            </div>
        </div>

        <div class="shortcut-container col-lg-4 col-md-4 col-sm-6 panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="shortcut panel" style="height: auto;">

                <div class="shortcut-name">Latest Release</div>

                <div class="panel-heading panel-active" role="tab" id="heading_wdi">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_wdi" aria-expanded="true" aria-controls="collapse_wdi">
                        <h4 class="panel-title">WDI</h4>
                    </a>
                </div>


                <div class="panel-heading" role="tab" id="heading_diamond">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_diamond" aria-expanded="false" aria-controls="collapse_diamond">
                        <h4 class="panel-title">Diamond</h4>
                    </a>
                </div>
                <div class="panel-heading" role="tab" id="heading_FITT">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_FITT" aria-expanded="false" aria-controls="collapse_FITT">
                        <h4 class="panel-title">FITT</h4>
                    </a>
                </div>
                <div class="panel-heading" role="tab" id="heading_Faclite">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_Faclite" aria-expanded="false" aria-controls="collapse_Faclite">
                        <h4 class="panel-title">Faclite</h4>
                    </a>
                </div>

                <div id="collapse_diamond" class="panel-collapse collapse" role="tabpanel" aria-labelledby="">

                    <div id="carousel-diamond" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators carousel-release">
                            <li data-target="#carousel-diamond" data-slide-to="0" class="active"></li>
                            <li data-target="#carousel-diamond" data-slide-to="1"></li>
                            <li data-target="#carousel-diamond" data-slide-to="2"></li>
                            <li data-target="#carousel-diamond" data-slide-to="3"></li>
                            <li data-target="#carousel-diamond" data-slide-to="4"></li>

                    </div>

                </div>

            </div>
        </div>

        <div class="shortcut-container col-lg-4 col-md-4 col-sm-6">
            <div class="shortcut">
                <div class="shortcut-name text-center mb-3">Product Catalogue </div>
                <div class="qr-code-grid">
                    <div class="qr-code-item">
                        <img src="/wdi/www.wdi.co.th/wp-content/QR_code_web/2025 WDI E-Catalogue Motorbike.jpg" alt="2025 WDI E-Catalogue Motorbike QR Code" />
                    </div>
                    <div class="qr-code-item">
                        <img src="/wdi/www.wdi.co.th/wp-content/QR_code_web/2025 WDI E-Catalogue.jpg" alt="2025 WDI E-Catalogue QR Code" />
                    </div>
                    <div class="qr-code-item">
                        <img src="/wdi/www.wdi.co.th/wp-content/QR_code_web/FITT Catalogue.jpg" alt="FITT Catalogue QR Code" />
                    </div>
                    <div class="qr-code-item">
                        <img src="/wdi/www.wdi.co.th/wp-content/QR_code_web/MANUAL _ VDO 07-705 _ 18-033.jpg" alt="MANUAL & VDO 07-705 / 18-033 QR Code" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>


    <?php require 'footer-page.php'; ?>

    <!-- JavaScript for News Carousel -->
    <script src="/wdi/www.wdi.co.th/th/js-control/manage-index.js"></script>

</body>

</html>