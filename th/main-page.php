<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wichien Dynamic Industry</title>

    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="https://www.wdi.co.th/wp-content/plugins/revslider/public/assets/css/settings.css?ver=5.2.5.1" type="text/css" media="all" />
    <link rel="stylesheet" href="https://www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="https://www.wdi.co.th/wp-content/themes/wdi/style.css" type="text/css" media="all" />

    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.wdi.co.th/wp-includes/js/jquery/jquery-migrate.min.js"></script>
    <script src="https://www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>
    <script src="https://www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>
    <style>
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
    </style>
</head>

<body class="home page-template page-template-page-home page page-id-2792 woocommerce-no-js">

    <?php require 'nav-bar.php'; ?>
    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet" type="text/css" media="all" />
    <?php require 'slider-index-page.php'; ?>

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

            <div id="collapse_diamond" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_diamond">

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
                    <div class="shortcut-name">Choose Your Vehicle</div>
                    <div class="vehicle-shortcut-image"><a data-toggle="modal" data-target="#vehicleModal" href="#"><img src="https://www.wdi.co.th/wp-content/uploads/2018/12/CHOOSE-YOUR-VEHICLE-UF.jpg" /></a></div>
                </div>
    </div>
           

            <div class="clearfix"></div>


    <?php require 'footer-page.php'; ?>

    <!-- JavaScript for News Carousel -->
    <script src="/wdi/www.wdi.co.th/th/js-control/manage-index.js"></script>
</body>

</html>