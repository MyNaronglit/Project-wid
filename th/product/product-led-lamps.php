<?php
require_once('../php-backend/server.php');
$db = new server();
$ierp = $db->connect_sql();

function safe_base64_decode($input)
{
    if (!is_string($input) || empty($input)) {
        return null;
    }
    $input = urldecode($input);
    $decoded = base64_decode($input, true);
    return ($decoded !== false && is_string($decoded)) ? $decoded : $input;
}

$category_raw = isset($_GET['category']) && !empty($_GET['category']) ? safe_base64_decode($_GET['category']) : null;
$category01 = isset($_GET['category01']) && !empty($_GET['category01']) ? safe_base64_decode($_GET['category01']) : null;
$category_detail = isset($_GET['category_detail']) && !empty($_GET['category_detail']) ? safe_base64_decode($_GET['category_detail']) : null;
$car_brand_input = isset($_GET['car_brand_input']) && !empty($_GET['car_brand_input']) ? safe_base64_decode($_GET['car_brand_input']) : null;
$car_model_input = isset($_GET['car_model_input']) && !empty($_GET['car_model_input']) ? safe_base64_decode($_GET['car_model_input']) : null;

$rtnav = null;

if (!empty($category_raw)) {
    // กรณีมี category_raw
    $sql = "SELECT category FROM products WHERE category = ?";
    $stmt = $ierp->prepare($sql);
    $stmt->bind_param("s", $category_raw);
} elseif (!empty($category_detail)) {
    // กรณีมี category_detail
    $sql = "SELECT category, category_detail FROM products WHERE category_detail = ?";
    $stmt = $ierp->prepare($sql);
    $stmt->bind_param("s", $category_detail);
} elseif (!empty($car_brand_input) && !empty($category01)) {
    // กรณีมี car_brand_input และ category01
    $sql = "SELECT category, car_brand_input FROM products WHERE car_brand_input = ? AND category = ?";
    $stmt = $ierp->prepare($sql);
    $stmt->bind_param("ss", $car_brand_input, $category01);
} elseif (!empty($car_model_input)) {
    // กรณีมี car_model_input
    $sql = "SELECT category, car_brand_input, car_model_input FROM products WHERE car_model_input = ?";
    $stmt = $ierp->prepare($sql);
    $stmt->bind_param("s", $car_model_input);
} else {
    // ไม่มีข้อมูลเงื่อนไข
    $rtnav = null;
}

if (isset($stmt)) {
    $stmt->execute();
    $rtnav = $stmt->get_result();
}


// ทำ query เพื่อดึงข้อมูลสินค้าที่เกี่ยวข้องกับ category_detail นี้
$sql = "SELECT * FROM products WHERE category_detail = ? ORDER BY display_order ASC";
$stmt = $ierp->prepare($sql);
$stmt->bind_param("s", $category_detail);
$stmt->execute();
$result01 = $stmt->get_result();

// ทำ query เพื่อดึงข้อมูลสินค้าที่เกี่ยวข้องกับ category_detail นี้
$sql = "SELECT * FROM products WHERE  car_model_input = ? ORDER BY display_order ASC";
$stmt = $ierp->prepare($sql);
$stmt->bind_param("s", $car_model_input);
$stmt->execute();
$result02 = $stmt->get_result();


$sql_replacement_parts = "SELECT * FROM `products` WHERE category = ?  ORDER BY category_detail_display_order ASC, brand_display_order ASC";
$stmt_replacement_parts = $ierp->prepare($sql_replacement_parts);
$stmt_replacement_parts->bind_param("s", $category_raw);
$stmt_replacement_parts->execute();
$result_replacement_parts = $stmt_replacement_parts->get_result();

$sql_data_category = "SELECT * FROM `products` WHERE category = ? ORDER BY brand_display_order ASC";
$stmt_data_category = $ierp->prepare($sql_data_category);
$stmt_data_category->bind_param("s", $category_raw);
$stmt_data_category->execute();
$result_data_category = $stmt_data_category->get_result();


$sql_car_brand_input = "SELECT product_id , product_name,car_model_input,image_path, car_image_upload FROM `products` WHERE car_brand_input = ? and category = ? ORDER BY model_display_order ASC";
$stmt_car_brand_input = $ierp->prepare($sql_car_brand_input);
$stmt_car_brand_input->bind_param("ss", $car_brand_input, $category01);
$stmt_car_brand_input->execute();
$result_car_brand_input = $stmt_car_brand_input->get_result();
?>
<!DOCTYPE html>
<html lang="th-TH">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wichien Dynamic Industry </title>

    <meta name='robots' content='noindex,follow' />
    <link rel='dns-prefetch' href='//fonts.googleapis.com' />

    <link rel='stylesheet' id='contact-form-7-css' href='/wdi/www.wdi.co.th/wp-content/plugins/contact-form-7/includes/css/styles.css?ver=5.0.2' type='text/css' media='all' />
    <link rel='stylesheet' id='rs-plugin-settings-css' href='/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/css/settings.css?ver=5.2.5.1' type='text/css' media='all' />
    <link rel='stylesheet' id='woocommerce-layout-css' href='/wdi/www.wdi.co.th/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=3.4.8' type='text/css' media='all' />
    <link rel='stylesheet' id='woocommerce-smallscreen-css' href='/wdi/www.wdi.co.th/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=3.4.8' type='text/css' media='only screen and (max-width: 768px)' />
    <link rel='stylesheet' id='woocommerce-general-css' href='/wdi/www.wdi.co.th/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=3.4.8' type='text/css' media='all' />
    <link rel='stylesheet' id='wpsl-styles-css' href='/wdi/www.wdi.co.th/wp-content/plugins/wp-store-locator/css/styles.min.css?ver=2.2.15' type='text/css' media='all' />
    <link rel='stylesheet' id='wpfront-scroll-top-css' href='/wdi/www.wdi.co.th/wp-content/plugins/wpfront-scroll-top/css/wpfront-scroll-top.min.css?ver=2.0.1' type='text/css' media='all' />
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/style.css" type="text/css" media="all" />
    <link rel='stylesheet' id='font-awesome-css' href='/wdi/www.wdi.co.th/wp-content/themes/wdi/css/font-awesome.min.css?ver=9a30e44c1415efb53499654793754fec' type='text/css' media='all' />
    <link rel='stylesheet' id='googleFonts-css' href='https://fonts.googleapis.com/css?family=Play%3A400%2C700&#038;ver=9a30e44c1415efb53499654793754fec' type='text/css' media='all' />
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-includes/js/jquery/jquery.js?ver=1.12.4'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js?ver=5.2.5.1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.revolution.min.js?ver=5.2.5.1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/themes/wdi/js/bootstrap.js?ver=1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/themes/wdi/js/functions.js?ver=1'></script>
    <link rel='https://api.w.org/' href='/wdi/www.wdi.co.th/th/wp-json/' />


    <style type="text/css">
        .qtranxs_flag_en {
            background-image: url(http://wdi-th.com/wp-content/plugins/qtranslate-x/flags/gb.png);
            background-repeat: no-repeat;
        }

        .qtranxs_flag_TH {
            background-image: url(http://wdi-th.com/wp-content/plugins/qtranslate-x/flags/th.png);
            background-repeat: no-repeat;
        }
    </style>
    <link hreflang="en" href="/wdi/www.wdi.co.th/en/product-category/led-lamps/led-interior-lamps/" rel="alternate" />
    <link hreflang="th" href="/wdi/www.wdi.co.th/th/product-category/led-lamps/led-interior-lamps/" rel="alternate" />
    <link hreflang="x-default" href="/wdi/www.wdi.co.th/product-category/led-lamps/led-interior-lamps/" rel="alternate" />
    <meta name="generator" content="qTranslate-X 3.4.6.8" />
    <noscript>
        <style>
            .woocommerce-product-gallery {
                opacity: 1 !important;
            }
        </style>
    </noscript>
    <meta name="generator" content="Powered by Slider Revolution 5.2.5.1 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface." />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" sizes="192x192" />
    <link rel="apple-touch-icon-precomposed" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" />
    <meta name="msapplication-TileImage" content="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" />
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

<body class="archive tax-product_cat term-led-interior-lamps term-9 woocommerce woocommerce-page woocommerce-no-js">
    <?php require '../nav-bar.php'; ?>

    <div id="main" class="site-main">
        <div class="container product-cat-container">
            <div style="display: flex;">
                <?php require 'manu-product.php'; ?>

                <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
                    <div id="primary" class="content-area">
                        <main id="main" class="site-main" role="main">

                            <?php
                            // ตั้งค่าเริ่มต้นเป็นค่าว่าง
                            $category_main_from_db = '';
                            $category_detail_from_db = '';
                            $car_brand_input_from_db = '';
                            $car_model_input_from_db = '';
                            $product_name_from_db = ''; // เพิ่มตัวแปรนี้เข้ามาเพื่อให้ครอบคลุมกรณี product_name_from_db

                            // ถ้ามีข้อมูลใน $rtnav ให้ดึงค่ามาใช้
                            if ($rtnav && $rtnav->num_rows > 0) {
                                $row = $rtnav->fetch_assoc();
                                $category_main_from_db = !empty($row['category']) ? $row['category'] : '';
                                $category_detail_from_db = !empty($row['category_detail']) ? $row['category_detail'] : '';
                                $car_brand_input_from_db = !empty($row['car_brand_input']) ? $row['car_brand_input'] : '';
                                $car_model_input_from_db = !empty($row['car_model_input']) ? $row['car_model_input'] : '';
                                // สมมติว่ามี $product_name_from_db ด้วย
                                $product_name_from_db = !empty($row['product_name']) ? $row['product_name'] : '';
                            }
                            ?>
                            <?php if (!empty($category_main_from_db) || !empty($category_detail_from_db) || !empty($car_brand_input_from_db) || !empty($car_model_input_from_db) || !empty($product_name_from_db)): ?>
                                <nav class="woocommerce-breadcrumb">
                                    <?php
                                    // สร้างอาร์เรย์เก็บส่วนประกอบของ Breadcrumb
                                    $breadcrumbs = [];

                                    if (!empty($category_main_from_db)) {
                                        $breadcrumbs[] = [
                                            'text' => htmlspecialchars($category_main_from_db),
                                            'link' => "/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=" . base64_encode($category_main_from_db)
                                        ];
                                    }

                                    if (!empty($category_detail_from_db)) {
                                        $breadcrumbs[] = [
                                            'text' => htmlspecialchars($category_detail_from_db),
                                            'link' => "/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category_detail=" . base64_encode($category_detail_from_db)
                                        ];
                                    }

                                    if (!empty($car_brand_input_from_db)) {
                                        $breadcrumbs[] = [
                                            'text' => htmlspecialchars($car_brand_input_from_db),
                                            'link' => "/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category01=" . base64_encode($category_main_from_db) . "&car_brand_input=" . base64_encode($car_brand_input_from_db)
                                        ];
                                    }

                                    if (!empty($car_model_input_from_db)) {
                                        $breadcrumbs[] = [
                                            'text' => htmlspecialchars($car_model_input_from_db),
                                            'link' => "/wdi/www.wdi.co.th/th/product/product-led-lamps.php?car_model_input=" . base64_encode($car_model_input_from_db)
                                        ];
                                    }

                                    // เพิ่ม product_name_from_db เข้าไปใน breadcrumb ถ้ามีและต้องการให้เป็นส่วนหนึ่งของ breadcrumb
                                    if (!empty($product_name_from_db)) {
                                        $breadcrumbs[] = [
                                            'text' => htmlspecialchars($product_name_from_db),
                                            'link' => "#" // หรือลิงก์ไปยังหน้าสินค้าจริง
                                        ];
                                    }


                                    // วนลูปเพื่อแสดง Breadcrumb
                                    foreach ($breadcrumbs as $index => $breadcrumb) {
                                        // ถ้าเป็นตัวสุดท้าย ให้เพิ่ม class 'current-breadcrumb' หรือใช้ <strong>
                                        if ($index === count($breadcrumbs) - 1) {
                                            echo '<strong aria-current="page">' . $breadcrumb['text'] . '</strong>';
                                        } else {
                                            echo '<a href="' . $breadcrumb['link'] . '">' . $breadcrumb['text'] . '</a>';
                                            echo '&nbsp;&#47;&nbsp;'; // ตัวคั่น
                                        }
                                    }
                                    ?>
                                </nav>
                            <?php endif; ?>





                            <?php
                            $has_replacement = $result_replacement_parts && $result_replacement_parts->num_rows > 0;
                            $has_LEDLampsCategory = $result_data_category && $result_data_category->num_rows > 0;
                            $has_car_brand_input = $result_car_brand_input && $result_car_brand_input->num_rows > 0;
                            $has_accessories01 = $result01 && $result01->num_rows > 0;
                            $has_accessories02 = $result02 && $result02->num_rows > 0;
                            ?>

                            <?php
                            $category_match_led = false;

                            if ($has_replacement && $result_replacement_parts->num_rows > 0) {
                                // รายการหมวดหมู่ที่ต้อง “ไม่แสดง” (รายการยกเว้น)
                                $exclude_categories = [
                                    'DIAMOND Replacement Parts  Pickup, Car & Truck',
                                    'DIAMOND Replacement Parts  Motorcycle',
                                    'FITT Vehicle Styling Accessories'
                                ];

                                // ตรวจสอบว่ามีหมวดหมู่ใดบ้างที่ไม่อยู่ในรายการยกเว้น
                                $result_replacement_parts->data_seek(0);
                                while ($row = $result_replacement_parts->fetch_assoc()) {
                                    $category_trim = trim($row['category']);
                                    if (!in_array($category_trim, $exclude_categories, true)) {
                                        $category_match_led = true;
                                        break;
                                    }
                                }

                                if ($category_match_led && isset($has_LEDLampsCategory) && $has_LEDLampsCategory) {
                                    // ถ้ามีหมวดหมู่ที่ไม่ถูกยกเว้น จึงแสดงผล
                                    $result_replacement_parts->data_seek(0);
                            ?>
                                    <ul class="product-catalog row">
                                        <?php
                                        $shown = [];
                                        while ($row = $result_replacement_parts->fetch_assoc()) {
                                            $category_trim = trim($row['category']);

                                            // ข้ามถ้าหมวดหมู่นี้อยู่ในรายการยกเว้น
                                            if (in_array($category_trim, $exclude_categories, true)) {
                                                continue;
                                            }

                                            $category_detail = htmlspecialchars($row['category_detail']);
                                            $key = $category_detail;

                                            if (!isset($shown[$key])) {
                                                $shown[$key] = true;

                                                // รูปภาพ
                                                $img = $row['image_category_detail_grouping'];
                                                $img_src = '';
                                                if (!empty($img)) {
                                                    $img_src = (strpos($img, 'http') === 0)
                                                        ? $img
                                                        : "../adminkit-dev/static/back-php/" . htmlspecialchars($img);
                                                }

                                                echo "<li class='post-product col-lg-4 col-md-6 mb-4'>";
                                                $encoded_category_detail = urlencode(base64_encode($row['category_detail']));

                                                echo "<a href='./product-led-lamps.php?category_detail={$encoded_category_detail}' class='block transition hover:scale-[1.02] duration-200'>";

                                                echo "<div class='product-catalog-image bg-white shadow rounded overflow-hidden'>";
                                                if (!empty($img_src)) {
                                                    echo "<img src='{$img_src}' width='300' height='225' alt='Image for {$category_detail}' class='w-full object-cover' />";
                                                } else {
                                                    echo "<div class='w-[300px] h-[225px] flex items-center justify-center bg-gray-200 text-gray-500'>No Image</div>";
                                                }
                                                echo "</div>";

                                                echo "<div class='text-center px-4 py-3'>";
                                                echo "<h4 style='background-color: white; height:50px; font-size:20px;' class='text-center font-semibold text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200'>{$category_detail}</h4>";
                                                echo "</div>";

                                                echo "</a></li>";
                                            }
                                        }
                                        ?>
                                    </ul>
                                <?php
                                } else {
                                    // กรณีไม่ตรงกลุ่มหมวดหมู่ด้านบน ให้แสดงรายการอื่น (ที่ไม่ใช่ LED Lighting)
                                    $result_replacement_parts->data_seek(0);
                                ?>
                                    <ul class="product-catalog row">
                                        <?php
                                        $shown = [];

                                        while ($row = $result_replacement_parts->fetch_assoc()) {
                                            if (trim($row['category']) !== 'LED Lighting') {
                                                $car_brand_input = htmlspecialchars($row['car_brand_input']);
                                                $category = htmlspecialchars($row['category']);
                                                $key = $car_brand_input . '|' . $category;

                                                if (!isset($shown[$key])) {
                                                    $shown[$key] = true;

                                                    $img = $row['car_image_upload_brand'];
                                                    $img_src = '';
                                                    if (!empty($img)) {
                                                        $img_src = (strpos($img, 'http') === 0)
                                                            ? $img
                                                            : "../adminkit-dev/static/back-php/" . htmlspecialchars($img);
                                                    }

                                                    echo "<li class='post-product col-lg-4 col-md-6 mb-4'>";

                                                    $encoded_car_brand_input = urlencode(base64_encode($row['car_brand_input']));
                                                    $encoded_category = urlencode(base64_encode($row['category']));

                                                    echo "<a href='./product-led-lamps.php?car_brand_input={$encoded_car_brand_input}&category01={$encoded_category}' class='block transition hover:scale-[1.02] duration-200'>";

                                                    echo "<div class='product-catalog-image bg-white shadow rounded overflow-hidden'>";
                                                    if (!empty($img_src)) {
                                                        echo "<img src='{$img_src}' alt='Image for {$car_brand_input}' class='w-full object-cover rounded' />";
                                                    } else {
                                                        echo "<div class='w-[300px] h-[225px] flex items-center justify-center bg-gray-200 text-gray-500'>No Image</div>";
                                                    }
                                                    echo "</div>";

                                                    echo "<div class='text-center px-4 py-3'>";
                                                    echo "<h4 style='background-color: white; height:50px; font-size:20px;' class='text-center font-semibold text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200'>{$car_brand_input}</h4>";
                                                    echo "</div>";

                                                    echo "</a></li>";
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                            <?php
                                }
                            }
                            ?>
                            <ul class="product-catalog">
                                <?php
                                if ($has_car_brand_input) {
                                    $shown = []; // เก็บ car_model_input ที่แสดงไปแล้ว

                                    while ($row = $result_car_brand_input->fetch_assoc()) {
                                        $car_model_input = htmlspecialchars($row['car_model_input']);


                                        if (!isset($shown[$car_model_input])) {
                                            $shown[$car_model_input] = true;

                                            echo "<li class='post-product'>";
                                            $encoded_car_model_input = urlencode(base64_encode($car_model_input));

                                            echo "<a href='./product-led-lamps.php?car_model_input={$encoded_car_model_input}'>";
                                            echo "<div class='product-catalog-image'>";
                                            $img = $row['car_image_upload'];
                                            $img_src = strpos($img, 'http') === 0 ? $img : "../adminkit-dev/static/back-php/" . htmlspecialchars($img);
                                            echo "<img src='{$img_src}' width='300' height='225' />";
                                            echo "</div>";
                                            // ชื่อแบรนด์และหมวดหมู่
                                            echo "<div class='text-center px-4 py-3'>";
                                            echo "<h4 style='background-color: white; height:50px; font-size:20px;' class='text-center font-semibold text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200'>" . $car_model_input . "</h4>";
                                            echo "</div>";
                                            echo "</a></li>";
                                        }
                                    }
                                }
                                ?>
                            </ul>

                            <ul class="product-catalog grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6 bg-gradient-to-br from-gray-100 to-white dark:from-gray-900 dark:to-gray-800">
                                <?php
                                if ($has_accessories01) {
                                    while ($row = $result01->fetch_assoc()) {
                                        $product_id = htmlspecialchars($row['product_id']);
                                        echo "<li class='post-product bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition duration-300 ease-in-out overflow-hidden flex flex-col'>";
                                        $encoded_product_id = urlencode(base64_encode($product_id));

                                        echo "<a href='./view-product.php?product_id={$encoded_product_id}' class='flex flex-col h-full'>";

                                        echo "<div class='product-catalog-image overflow-hidden'>";
                                        $img = $row['image_path'];
                                        $img_src = strpos($img, 'http') === 0 ? $img : "../adminkit-dev/static/back-php/" . htmlspecialchars($img);
                                        echo "<img src='{$img_src}' alt='" . htmlspecialchars($row['product_name']) . "' class='w-full h-48 object-cover transition-transform duration-300 hover:scale-110 rounded-t-3xl' />";
                                        echo "</div>";

                                        echo "<h4 style='background-color: white; height: 55px;margin-left: 10px;' class='text-left text-xl font-semibold text-gray-800 dark:text-red px-4 py-4 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200'>";
                                        echo "<div>" . htmlspecialchars($row['item_number']) . "</div>";
                                        echo "<div>" . htmlspecialchars($row['product_name']) . "</div>";
                                        echo "</h4>";


                                        echo "</a></li>";
                                    }
                                }
                                ?>
                            </ul>


                            <ul class="product-catalog product-catalog grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6 bg-gradient-to-br from-gray-100 to-white dark:from-gray-900 dark:to-gray-800">
                                <?php
                                if ($has_accessories02) {
                                    while ($row = $result02->fetch_assoc()) {
                                        $product_id = htmlspecialchars($row['product_id']);
                                        echo "<li class='post-product bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition duration-300 ease-in-out overflow-hidden flex flex-col'>";
                                        $encoded_product_id = urlencode(base64_encode($product_id));

                                        echo "<a href='./view-product.php?product_id={$encoded_product_id}' class='flex flex-col h-full'>";
                                        echo "<div class='product-catalog-image overflow-hidden'>";
                                        $img = $row['image_path'];
                                        $img_src = strpos($img, 'http') === 0 ? $img : "../adminkit-dev/static/back-php/" . htmlspecialchars($img);
                                        echo "<img src='{$img_src}' alt='" . htmlspecialchars($row['product_name']) . "' class='w-full h-48 object-cover transition-transform duration-300 hover:scale-110 rounded-t-3xl' />";

                                        echo "</div>";
                                        echo "<h4 style='background-color: white; height: 55px;margin-left: 10px;' class='text-left text-xl font-semibold text-gray-800 dark:text-white px-4 py-4 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200'>";
                                        echo "<div>" . htmlspecialchars($row['item_number']) . "</div>";
                                        echo "<div>" . htmlspecialchars($row['product_name']) . "</div>";
                                        echo "</h4>";


                                        echo "</a></li>";
                                    }
                                }
                                ?>
                            </ul>

                            <?php
                            // ถ้าไม่มีสินค้าเลยทั้ง 2 กลุ่ม ให้แสดงข้อความนี้
                            if (!$has_replacement && !$has_accessories01 && !$has_LEDLampsCategory && !$has_accessories02 && !$has_car_brand_input) {
                                echo "<ul class='product-catalog'><li>No products found for this category detail.</li></ul>";
                            }
                            //test
                            ?>


                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #main -->
    <?php require '../footer-page.php'; ?>


    <div id="wpfront-scroll-top-container">
        <img src="https://www.wdi.co.th/wp-content/plugins/wpfront-scroll-top/images/icons/1.png" alt="" />
    </div>
    <script type="text/javascript">
        function wpfront_scroll_top_init() {
            if (typeof wpfront_scroll_top == "function" && typeof jQuery !== "undefined") {
                wpfront_scroll_top({
                    "scroll_offset": 100,
                    "button_width": 0,
                    "button_height": 0,
                    "button_opacity": 0.8,
                    "button_fade_duration": 200,
                    "scroll_duration": 400,
                    "location": 1,
                    "marginX": 20,
                    "marginY": 20,
                    "hide_iframe": false,
                    "auto_hide": false,
                    "auto_hide_after": 2,
                    "button_action": "top",
                    "button_action_element_selector": "",
                    "button_action_container_selector": "html, body",
                    "button_action_element_offset": 0
                });
            } else {
                setTimeout(wpfront_scroll_top_init, 100);
            }
        }
        wpfront_scroll_top_init();
    </script>
    <script type="application/ld+json">
        {
            "@context": "https:\/\/schema.org\/",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": "1",
                "item": {
                    "name": "Home",
                    "@id": "https:\/\/www.wdi.co.th\/th"
                }
            }, {
                "@type": "ListItem",
                "position": "2",
                "item": {
                    "name": "\u0e2a\u0e34\u0e19\u0e04\u0e49\u0e32",
                    "@id": "https:\/\/www.wdi.co.th\/th\/products\/"
                }
            }, {
                "@type": "ListItem",
                "position": "3",
                "item": {
                    "name": "LED Lamps",
                    "@id": "https:\/\/www.wdi.co.th\/th\/product-category\/led-lamps\/"
                }
            }, {
                "@type": "ListItem",
                "position": "4",
                "item": {
                    "name": "LED Interior Lamps"
                }
            }]
        }
    </script>
    <script type="text/javascript">
        var c = document.body.className;
        c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
        document.body.className = c;
    </script>
    <script type='text/javascript'>
        /* <![CDATA[ */
        var wpcf7 = {
            "apiSettings": {
                "root": "https:\/\/www.wdi.co.th\/th\/wp-json\/contact-form-7\/v1",
                "namespace": "contact-form-7\/v1"
            },
            "recaptcha": {
                "messages": {
                    "empty": "Please verify that you are not a robot."
                }
            }
        };
        /* ]]> */
    </script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=5.0.2'></script>
    <script type='text/javascript'>
        /* <![CDATA[ */
        var wc_add_to_cart_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/th\/?wc-ajax=%%endpoint%%",
            "i18n_view_cart": "View cart",
            "cart_url": "https:\/\/www.wdi.co.th\/th\/inquiry\/",
            "is_cart": "",
            "cart_redirect_after_add": "no"
        };
        /* ]]> */
    </script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart.min.js?ver=3.4.8'></script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js?ver=2.70'></script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.4'></script>
    <script type='text/javascript'>
        /* <![CDATA[ */
        var woocommerce_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/th\/?wc-ajax=%%endpoint%%"
        };
        /* ]]> */
    </script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min.js?ver=3.4.8'></script>
    <script type='text/javascript'>
        /* <![CDATA[ */
        var wc_cart_fragments_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/th\/?wc-ajax=%%endpoint%%",
            "cart_hash_key": "wc_cart_hash_fefb973b518d4142c80e8330e94cdb98",
            "fragment_name": "wc_fragments_fefb973b518d4142c80e8330e94cdb98"
        };
        /* ]]> */
    </script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/woocommerce/assets/js/frontend/cart-fragments.min.js?ver=3.4.8'></script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-content/plugins/wpfront-scroll-top/js/wpfront-scroll-top.min.js?ver=2.0.1'></script>
    <script type='text/javascript' src='https://www.wdi.co.th/wp-includes/js/wp-embed.min.js?ver=9a30e44c1415efb53499654793754fec'></script>
</body>

</html>