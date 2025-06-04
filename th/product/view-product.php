<?php
require_once('../php-backend/server.php');
$db = new server();
$ierp = $db->connect_sql();

function safe_base64_decode($input)
{
    $decoded = base64_decode($input, true);
    // เช็คว่า decode สำเร็จ และ encode กลับแล้วตรงกับ input เดิม
    return ($decoded !== false && base64_encode($decoded) === $input) ? $decoded : $input;
}

// ถอดรหัสค่า product_id (ถ้ามี) หรือให้เป็น null ถ้าไม่ส่งมา
$product_id = null;
if (isset($_GET['product_id'])) {
    $raw = safe_base64_decode($_GET['product_id']);
    // แปลงเป็น integer อีกที เพื่อความปลอดภัย
    $product_id = intval($raw);
}

if ($product_id) {
    // ดึงข้อมูลสินค้าหลัก
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $ierp->prepare($sql);
    if (! $stmt) {
        die("Prepare failed: " . $ierp->error);
    }
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "ไม่พบสินค้าที่คุณเลือก";
        exit;
    }

    // ดึงรูปภาพรายละเอียดสินค้าโดยอ้างอิงจาก RefID_img
    $sql_detail_img = "SELECT detail_img_product FROM detail_img_product WHERE detail_RefID_img = ?";
    $stmt2 = $ierp->prepare($sql_detail_img);
    if (! $stmt2) {
        die("Prepare failed: " . $ierp->error);
    }
    $stmt2->bind_param("s", $product['RefID_img']);
    $stmt2->execute();
    $result_detail_img = $stmt2->get_result();

    $detail_img_product = [];
    if ($result_detail_img && $result_detail_img->num_rows > 0) {
        // ดึงทั้งหมดเป็น array ของ associative arrays
        $detail_img_product = $result_detail_img->fetch_all(MYSQLI_ASSOC);
    }

    // ที่นี่คุณก็พร้อมจะใช้ $product และ $detail_img_product ต่อไป
} else {
    echo "No product selected.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="th-TH">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Wichien Dynamic Industry | LED Interior Lamp 138 mm. </title>

    <meta name='robots' content='noindex,follow' />
    <link rel='dns-prefetch' href='//fonts.googleapis.com' />

    <link rel='stylesheet' id='contact-form-7-css' href='/wdi/www.wdi.co.th/wp-content/plugins/contact-form-7/includes/css/styles.css?ver=5.0.2' type='text/css' media='all' />
    <link rel='stylesheet' id='rs-plugin-settings-css' href='/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/css/settings.css?ver=5.2.5.1' type='text/css' media='all' />
    <style id='rs-plugin-settings-inline-css' type='text/css'>
        #rs-demo-id {}
    </style>
    <link rel='stylesheet' id='woocommerce-layout-css' href='/wdi/www.wdi.co.th/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=3.4.8' type='text/css' media='all' />
    <link rel='stylesheet' id='woocommerce-smallscreen-css' href='/wdi/www.wdi.co.th/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=3.4.8' type='text/css' media='only screen and (max-width: 768px)' />
    <link rel='stylesheet' id='woocommerce-general-css' href='/wdi/www.wdi.co.th/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=3.4.8' type='text/css' media='all' />
    <style id='woocommerce-inline-inline-css' type='text/css'>
        .woocommerce form .form-row .required {
            visibility: visible;
        }
    </style>
    <link rel='stylesheet' id='wpsl-styles-css' href='/wdi/www.wdi.co.th/wp-content/plugins/wp-store-locator/css/styles.min.css?ver=2.2.15' type='text/css' media='all' />
    <link rel='stylesheet' id='wpfront-scroll-top-css' href='/wdi/www.wdi.co.th/wp-content/plugins/wpfront-scroll-top/css/wpfront-scroll-top.min.css?ver=2.0.1' type='text/css' media='all' />
    <link rel='stylesheet' id='bootstrap-css' href='/wdi/www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css?ver=9a30e44c1415efb53499654793754fec' type='text/css' media='all' />
    <link rel='stylesheet' id='WDI-style-css' href='/wdi/www.wdi.co.th/wp-content/themes/wdi/style.css' type='text/css' media='all' />
    <link rel='stylesheet' id='font-awesome-css' href='/wdi/www.wdi.co.th/wp-content/themes/wdi/css/font-awesome.min.css?ver=9a30e44c1415efb53499654793754fec' type='text/css' media='all' />
    <link rel='stylesheet' id='googleFonts-css' href='/wdi/fonts.googleapis.com/css?family=Play%3A400%2C700&#038;ver=9a30e44c1415efb53499654793754fec' type='text/css' media='all' />
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-includes/js/jquery/jquery.js?ver=1.12.4'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js?ver=5.2.5.1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.revolution.min.js?ver=5.2.5.1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/themes/wdi/js/bootstrap.js?ver=1'></script>
    <script type='text/javascript' src='/wdi/www.wdi.co.th/wp-content/themes/wdi/js/functions.js?ver=1'></script>
    <link rel='https://api.w.org/' href='/wdi/www.wdi.co.th/th/wp-json/' />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" sizes="192x192" />
    <link rel="apple-touch-icon-precomposed" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" />
    <meta name="msapplication-TileImage" content="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden;
            font-family: 'Barlow', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f8f8f8;

        }

        .detail-images img {
            border: 1px solid #ccc;
            padding: 2px;
        }

        .detail-images img:hover {
            border-color: #007bff;
        }

        .post-product.product {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .product-images-wrapper {
            flex: 0 0 auto;
            width: 400px;
            /* ปรับขนาดตามต้องการ */
        }

        .main-image {
            margin-bottom: 10px;
        }

        .detail-images {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .summary.entry-summary {
            flex: 1 1 auto;
            min-width: 300px;
            /* ป้องกันไม่ให้ข้อความแคบเกินไป */
        }

        .product-details-table {
            width: 100%;
            /* ทำให้ตารางกว้าง 100% ของพื้นที่ที่มี */
            table-layout: fixed;
            /* ทำให้คอลัมน์กว้างเท่ากัน และข้อความตัดบรรทัด */
            word-wrap: break-word;
            /* ข้อความยาวๆ จะตัดบรรทัดได้ */
        }

        .product-details-table th {
            text-align: left;
            width: 120px;
            /* ปรับขนาดความกว้างของหัวข้อ */
            font-weight: normal;
            color: #555;
            padding: 5px 0;
        }

        .product-details-table td {
            padding: 5px 0;
        }

        .product-details-table td.news-content {
            overflow-wrap: break-word;
            /* สำหรับเนื้อหาที่มีคำยาวๆ */
            word-break: break-word;
            /* สำหรับเบราว์เซอร์เก่า */
        }

        .product-details-table img {
            max-width: 100%;
            /* ทำให้รูปภาพภายในตารางไม่เกินขอบตาราง */
            height: auto;
            /* รักษาอัตราส่วนของรูปภาพ */
        }

        .selected-thumb {
            border: 2px solid #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            border-radius: 4px;
        }

        .main-image {
            width: 300px;
            height: 300px;
            overflow: hidden;
        }

        .main-product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .breadcrumb-separator {
            font-family: 'Arial Black', Arial, sans-serif;
            font-weight: normal;
            color: #000;
        }

        /* สำหรับรูปภาพภายใน function-image */
        .function-image img {
            max-width: 100%;
            /* ทำให้รูปภาพไม่เกินความกว้างของพื้นที่ที่มี */
            height: auto;
            /* รักษาอัตราส่วนของรูปภาพ */
            /* ถ้าคุณต้องการให้รูปภาพมีขนาดสูงสุดที่ 300px บนหน้าจอใหญ่ */
            /* และให้มันย่อลงมาบนหน้าจอเล็ก ให้เพิ่ม max-width: 300px; */
            max-width: 300px;
            /* จำกัดความกว้างสูงสุดของรูปภาพ */
        }

        /* คุณอาจต้องการให้แต่ละรูปภาพมีขนาดที่พอดีและตัดลงมาเมื่อหน้าจอเล็ก */
        /* สามารถใช้ flex-basis หรือ width เพื่อควบคุมขนาดของแต่ละรูปได้ */
        .function-image a {
            flex: 1 1 calc(50% - 5px);
            /* แสดง 2 รูปต่อแถวใน mobile (ถ้าต้องการ) */
            /* flex: 1 1 calc(100% - 0px); แสดง 1 รูปต่อแถวใน mobile */
            /* หรือปรับตามที่คุณต้องการให้มีกี่รูปต่อแถว */
            max-width: 300px;
            /* จำกัดขนาดของ link ที่ครอบรูปภาพ */
        }

        /* ใช้ Media Queries เพื่อปรับขนาดรูปภาพใน mobile */
        @media (max-width: 768px) {

            /* สำหรับหน้าจอแท็บเล็ตและมือถือ */
            .function-image a {
                flex: 1 1 calc(50% - 5px);
                /* 2 รูปต่อแถวใน mobile */
            }
        }

        @media (max-width: 480px) {

            /* สำหรับหน้าจอมือถือขนาดเล็ก */
            .function-image a {
                flex: 1 1 100%;
                /* 1 รูปต่อแถวใน mobile */
            }
        }

        @media (max-width: 768px) {
            .product-cat-container>div {
                flex-direction: column !important;
                width: 100% !important;
                margin-left: 0 !important;
                flex-wrap: wrap;
                overflow-x: hidden;
            }
        }
    </style>
</head>

<body class="archive tax-product_cat term-led-interior-lamps term-9 woocommerce woocommerce-page woocommerce-no-js">

    <?php require '../nav-bar.php'; ?>


    <div id="main" class="site-main">
        <div class="container product-cat-container">
            <div style="display: flex;">
                <?php require 'manu-product.php'; ?>

                <div style="flex-grow: 1; width: 80%;">
                    <div id="primary" class="content-area" style="margin-left: 20px;">
                        <main id="main" class="site-main" role="main">
                            <?php
                            // ตั้งค่าเริ่มต้นเป็นค่าว่าง
                            $category_main_from_db = '';
                            $category_detail_from_db = '';
                            $car_brand_input_from_db = '';
                            $car_model_input_from_db = '';
                            $product_name_from_db = '';

                            // ใช้ค่าจาก $product ที่ดึงมาก่อนหน้า
                            if (!empty($product)) {
                                $category_main_from_db = !empty($product['category']) ? $product['category'] : '';
                                $category_detail_from_db = !empty($product['category_detail']) ? $product['category_detail'] : '';
                                $car_brand_input_from_db = !empty($product['car_brand_input']) ? $product['car_brand_input'] : '';
                                $car_model_input_from_db = !empty($product['car_model_input']) ? $product['car_model_input'] : '';
                                $product_name_from_db = !empty($product['product_name']) ? $product['product_name'] : '';
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

                                    if (!empty($product_name_from_db)) {
                                        $breadcrumbs[] = [
                                            'text' => htmlspecialchars($product_name_from_db),
                                            'link' => "#" // หรือลิงก์ไปยังหน้าสินค้าจริง
                                        ];
                                    }

                                    // วนลูปเพื่อแสดง Breadcrumb
                                    foreach ($breadcrumbs as $index => $breadcrumb) {
                                        // ถ้าเป็นตัวสุดท้าย ให้ใช้ <strong> เพื่อทำตัวหนา
                                        if ($index === count($breadcrumbs) - 1) {
                                            echo '<strong aria-current="page">' . $breadcrumb['text'] . '</strong>';
                                        } else {
                                            echo '<a href="' . $breadcrumb['link'] . '">' . $breadcrumb['text'] . '</a>';
                                            echo '<span class="breadcrumb-separator">&nbsp;&#47;&nbsp;</span>';
                                        }
                                    }
                                    ?>
                                </nav>
                            <?php endif; ?>


                            <div class="post-product product">
                                <div class="product-images-wrapper">
                                    <div class="main-image" style="position: relative; display: inline-block;">
                                        <?php
                                        $mainImageUrl = '';
                                        if (!empty($product['image_path'])) {
                                            if (strpos($product['image_path'], 'http') === 0) {
                                                $mainImageUrl = htmlspecialchars($product['image_path']);
                                            } else {
                                                $mainImageUrl = '../adminkit-dev/static/back-php/' . htmlspecialchars($product['image_path']);
                                            }
                                        }
                                        // แสดง img หลัก
                                        echo "<img 
                id='main-product-image' 
                src='{$mainImageUrl}' 
                alt='Product Image' 
                data-current-src='{$mainImageUrl}' 
                style='display:block;max-width:100%;height:auto;cursor:pointer;' 
            />";
                                        ?>
                                        <!-- 🔍 ไอคอนแว่นขยาย -->
                                        <div class="zoom-icon" style="position: absolute; bottom: 8px; right: 8px; border: 2px solid #b2b2b2; border-radius: 50%; padding: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.25); transition: transform 0.2s ease, box-shadow 0.2s ease; z-index: 10; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px);">
                                            <i class="fas fa-search-plus" style="color:#989898; font-size:18px;"></i>
                                        </div>
                                    </div>
                                    <?php
                                    if (!empty($product['ProductSheet_pdf'])) {
                                        // ตรวจสอบว่าเป็นลิงก์เต็มหรือไฟล์ในระบบ
                                        $manualUrl = (strpos($product['ProductSheet_pdf'], 'http') === 0)
                                            ? htmlspecialchars($product['ProductSheet_pdf'])
                                            : '../adminkit-dev/static/back-php/' . htmlspecialchars($product['ProductSheet_pdf']);
                                    ?>
                                        <div style="margin-top: 15px;">
                                            <a
                                                href="<?php echo $manualUrl; ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="
                                                        display: inline-block;
                                                        background-color: #dd1706;
                                                        color: white;
                                                        text-decoration: none;
                                                        padding: 10px 20px;
                                                        font-size: 14px;
                                                        border-radius: 4px;
                                                        transition: background-color 0.3s ease;
                                                    "
                                                onmouseover="this.style.backgroundColor='rgb(233 116 11);';"
                                                onmouseout="this.style.backgroundColor='rgb(233 116 11);';">
                                                <i class="fas fa-download"></i> Download Product sheet
                                            </a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="detail-images" style="margin-top:10px;">
                                        <?php
                                        // thumbnail แรก = main image
                                        if (!empty($mainImageUrl)) {
                                            echo "<img width='100' height='100' src='{$mainImageUrl}' class='detail-thumbnail' data-detail-src='{$mainImageUrl}' style='cursor:pointer; margin-right:5px; margin-bottom:5px;' />";
                                        }
                                        // thumbnails จาก detail_img_product
                                        if (!empty($detail_img_product) && is_array($detail_img_product)) {
                                            foreach ($detail_img_product as $row) {
                                                $url = is_array($row) && isset($row['detail_img_product'])
                                                    ? $row['detail_img_product']
                                                    : (is_array($row) && isset($row[0]) ? $row[0] : '');
                                                if ($url) {
                                                    $full = (strpos($url, 'http') === 0)
                                                        ? htmlspecialchars($url)
                                                        : '../adminkit-dev/static/back-php/' . htmlspecialchars($url);
                                                    echo "<img width='100' height='100' src='{$full}' class='detail-thumbnail' data-detail-src='{$full}' style='cursor:pointer; margin-right:5px; margin-bottom:5px;' />";
                                                }
                                            }
                                        }
                                        ?>
                                    </div>

                                    <!-- ================== เพิ่มส่วนแสดงปุ่ม PDF ================== -->
                                    <?php
                                    if (!empty($product['manual_pdf'])) {
                                        // ตรวจสอบว่าเป็นลิงก์เต็มหรือไฟล์ในระบบ
                                        $manualUrl = (strpos($product['manual_pdf'], 'http') === 0)
                                            ? htmlspecialchars($product['manual_pdf'])
                                            : '../adminkit-dev/static/back-php/' . htmlspecialchars($product['manual_pdf']);
                                    ?>
                                        <div style="margin-top: 15px;">
                                            <a
                                                href="<?php echo $manualUrl; ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="
                                                        display: inline-block;
                                                        background-color:rgb(255, 0, 0);
                                                        color: white;
                                                        text-decoration: none;
                                                        padding: 10px 20px;
                                                        font-size: 14px;
                                                        border-radius: 4px;
                                                        transition: background-color 0.3s ease;
                                                    "
                                                onmouseover="this.style.backgroundColor='rgb(233 116 11);';"
                                                onmouseout="this.style.backgroundColor='rgb(233 116 11);';">
                                                📄 Manual (PDF)
                                            </a>
                                        </div>



                                        <script>
                                            document.getElementById('btn-show-pdf').addEventListener('click', function() {
                                                var pdfContainer = document.getElementById('pdf-container');
                                                var pdfFrame = document.getElementById('manual-pdf-frame');

                                                // ถ้ายังไม่เคยตั้ง src ให้ตั้ง ตอนกดครั้งแรก
                                                if (!pdfFrame.getAttribute('src')) {
                                                    pdfFrame.setAttribute('src', '<?php echo $manualUrl; ?>');
                                                }

                                                // สลับการแสดง/ซ่อน container
                                                if (pdfContainer.style.display === 'none') {
                                                    pdfContainer.style.display = 'block';
                                                } else {
                                                    pdfContainer.style.display = 'none';
                                                }
                                            });
                                        </script>
                                    <?php
                                    }
                                    ?>


                                </div>


                                <div class="summary entry-summary">
                                    <h1 class="product_title entry-title">
                                        <div class="text-left text-base font-medium">
                                            <?php echo htmlspecialchars($product['item_number']); ?>
                                        </div>
                                        <div class="text-left text-xl font-semibold">
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                        </div>
                                    </h1>
                                    <?php
                                    $language = 'en';

                                    if (isset($_COOKIE['language']) && in_array($_COOKIE['language'], ['th', 'en'])) {
                                        $language = $_COOKIE['language'];
                                    }

                                    // Helper function to check if content is truly empty (including <p><br></p>)
                                    function isContentTrulyEmpty($content)
                                    {
                                        // Trim whitespace and remove <p><br></p> and <br> tags
                                        $cleanedContent = trim(str_replace(['<p><br></p>', '<br>'], '', $content));
                                        return empty($cleanedContent);
                                    }

                                    if ($language === 'en') {
                                        $product_content_en = $product["Product_content_en"] ?? '';
                                        $product_content_th = $product["Product_content_th"] ?? '';

                                        if (!isContentTrulyEmpty($product_content_en)) {
                                            $content = $product_content_en;
                                        } elseif (!isContentTrulyEmpty($product_content_th)) {
                                            $content = $product_content_th;
                                        } else {
                                            $content = ''; // Fallback to empty if both are empty
                                        }
                                    } else { // language is 'th'
                                        $product_content_th = $product["Product_content_th"] ?? '';
                                        $product_content_en = $product["Product_content_en"] ?? '';

                                        if (!isContentTrulyEmpty($product_content_th)) {
                                            $content = $product_content_th;
                                        } elseif (!isContentTrulyEmpty($product_content_en)) {
                                            $content = $product_content_en;
                                        } else {
                                            $content = ''; // Fallback to empty if both are empty
                                        }
                                    }
                                    ?>

                                    <div class="product_meta">
                                        <table class="product-details-table">
                                            <tbody>
                                                <tr>
                                                    <td class="news-content">
                                                        <div id="content-th" style="display: none;">
                                                            <?php echo htmlspecialchars_decode(
                                                                !empty($product["Product_content_th"]) && !isContentTrulyEmpty($product["Product_content_th"])
                                                                    ? $product["Product_content_th"]
                                                                    : (!empty($product["Product_content_en"]) && !isContentTrulyEmpty($product["Product_content_en"]) ? $product["Product_content_en"] : '')
                                                            ); ?>
                                                        </div>

                                                        <div id="content-en" style="display: none;">
                                                            <?php echo htmlspecialchars_decode(
                                                                !empty($product["Product_content_en"]) && !isContentTrulyEmpty($product["Product_content_en"])
                                                                    ? $product["Product_content_en"]
                                                                    : (!empty($product["Product_content_th"]) && !isContentTrulyEmpty($product["Product_content_th"]) ? $product["Product_content_th"] : '')
                                                            ); ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php if (!empty($product['product_func_image'])): ?>
                                        <?php
                                        $imageList = explode(',', $product['product_func_image']);
                                        ?>
                                        <div class="function-image" style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 10px;">
                                            <?php foreach ($imageList as $image): ?>
                                                <?php
                                                $image = trim($image);
                                                $imageUrl = (strpos($image, 'http') === 0)
                                                    ? htmlspecialchars($image)
                                                    : '../adminkit-dev/static/back-php/' . htmlspecialchars($image);
                                                ?>
                                                <a href="<?php echo $imageUrl; ?>" class="fancybox" data-fancybox="gallery">
                                                    <img
                                                        src="<?php echo $imageUrl; ?>"
                                                        class="attachment-medium size-medium wp-post-image"
                                                        data-current-src="<?php echo $imageUrl; ?>"
                                                        style="cursor: pointer; max-width: 100%; height: auto;" />
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>


                                </div>

                            </div>
                            <div>
                                <?php
                                if (!empty($product['youtube_links'])) {
                                    $links = explode(',', $product['youtube_links']);
                                    echo '<div class="youtube-video-section" style="margin-top: 20px;">';
                                    echo '<h4 style="margin-bottom: 10px;">🎬 วิดีโอแนะนำสินค้า</h4>';

                                    // เปิด container เป็น flex-wrap
                                    echo '<div style="display: flex; flex-wrap: wrap; gap: 4%; justify-content: flex-start;">';

                                    foreach ($links as $url) {
                                        $url = trim($url);

                                        // ดึง video ID จากลิงก์
                                        if (preg_match('/youtu\.be\/([^\?\&]+)/', $url, $matches)) {
                                            $videoId = $matches[1];
                                        } elseif (preg_match('/v=([^\&]+)/', $url, $matches)) {
                                            $videoId = $matches[1];
                                        } else {
                                            continue; // ข้ามถ้าไม่ match
                                        }

                                        // ฝัง iframe วิดีโอ (48% ต่อช่อง = 2 ช่องต่อแถว)
                                        echo '<div style="flex: 0 0 48%; margin-bottom: 20px;">';
                                        echo '<iframe width="100%" height="250" src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                        echo '</div>';
                                    }

                                    echo '</div>'; // ปิด flex container
                                    echo '</div>'; // ปิด youtube-video-section
                                }
                                ?>
                            </div>

                    </div>
                    </main>
                </div>
            </div>
        </div>

    </div>

    <script>
        (function($) {
            $(window).load(function() {
                /* Fancybox */
                $('.fancybox').fancybox();

                $('.fancybox-media')
                    .attr('rel', 'media-gallery')
                    .fancybox({
                        openEffect: 'none',
                        closeEffect: 'none',
                        prevEffect: 'none',
                        nextEffect: 'none',

                        arrows: false,
                        helpers: {
                            media: {},
                            buttons: {}
                        }
                    });
            });
        })(jQuery);
    </script>

    </div>
    <!-- #main -->
    <?php require '../footer-page.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@panzoom/panzoom/dist/panzoom.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setLanguageContent(lang) {
                const isThai = lang === 'th';
                document.getElementById('content-th').style.display = isThai ? 'block' : 'none';
                document.getElementById('content-en').style.display = isThai ? 'none' : 'block';
            }

            const savedLang = localStorage.getItem('language') || 'en';
            setLanguageContent(savedLang);

            document.querySelectorAll('.lang-th a').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    localStorage.setItem('language', 'th');
                    setLanguageContent('th');
                });
            });

            document.querySelectorAll('.lang-en a').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    localStorage.setItem('language', 'en');
                    setLanguageContent('en');
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // เตรียม array ของ URL ทั้งหมด
            const mainImg = document.getElementById('main-product-image');
            const detailThumbs = document.querySelectorAll('.detail-thumbnail');
            const images = [mainImg.dataset.currentSrc];
            detailThumbs.forEach(t => images.push(t.dataset.detailSrc));

            // ฟังก์ชันเปิด modal ซูม
            function openImageZoom(startIndex = 0) {
                let idx = startIndex;
                Swal.fire({
                    showCloseButton: true,
                    showConfirmButton: false,
                    background: '#fff',
                    width: 'auto',
                    html: `
        <div id="zoom-wrapper" style="
            position:relative;
            overflow:hidden;
            touch-action:none;
            max-width:90vw;
        ">
          <i id="zoom-prev" class="fas fa-chevron-left" style="
            position:absolute; left:8px; top:50%; transform:translateY(-50%);
            font-size:24px; color:rgba(255,255,255,0.9);
            padding:6px; background:rgba(0,0,0,0.3);
            border-radius:50%; cursor:pointer; z-index:1000;
          "></i>
          <img id="zoom-image" src="${images[idx]}" style="
            display:block; max-width:130vh; height:auto; margin:0 auto; cursor:grab;
          " />
          <i id="zoom-next" class="fas fa-chevron-right" style="
            position:absolute; right:8px; top:50%; transform:translateY(-50%);
            font-size:24px; color:rgba(255,255,255,0.9);
            padding:6px; background:rgba(0,0,0,0.3);
            border-radius:50%; cursor:pointer; z-index:1000;
          "></i>
        </div>
      `,
                    didOpen: () => {
                        const imgEl = document.getElementById('zoom-image');
                        const panzoom = Panzoom(imgEl, {
                            maxScale: 5,
                            minScale: 1,
                            contain: 'outside'
                        });
                        imgEl.parentElement.addEventListener('wheel', panzoom.zoomWithWheel);
                        // ปุ่ม prev/next
                        document.getElementById('zoom-prev').onclick = () => {
                            idx = (idx - 1 + images.length) % images.length;
                            imgEl.src = images[idx];
                            panzoom.reset();
                        };
                        document.getElementById('zoom-next').onclick = () => {
                            idx = (idx + 1) % images.length;
                            imgEl.src = images[idx];
                            panzoom.reset();
                        };
                    },
                    customClass: {
                        popup: 'rounded-xl shadow-lg'
                    }
                });
            }

            // ผูก event: คลิกรูปหลัก หรือไอคอน
            document.querySelector('.zoom-icon').addEventListener('click', e => {
                e.stopPropagation();
                openImageZoom(0);
            });
            mainImg.addEventListener('click', () => openImageZoom(0));

            // เปลี่ยน main image เมื่อคลิก thumbnail
            detailThumbs.forEach((t, i) => {
                t.addEventListener('click', () => {
                    const src = t.dataset.detailSrc;
                    mainImg.src = src;
                    mainImg.dataset.currentSrc = src;
                });
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.detail-thumbnail');
            const mainImage = document.getElementById('main-product-image');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    const newSrc = this.getAttribute('data-detail-src');
                    if (newSrc) {
                        mainImage.setAttribute('src', newSrc);
                        mainImage.setAttribute('data-current-src', newSrc);

                        // ลบ class ที่ active อยู่
                        thumbnails.forEach(t => t.classList.remove('selected-thumb'));
                        this.classList.add('selected-thumb');
                    }
                });
            });
        });
    </script>

</body>

</html>
<div style="display:none">
    <a href="https://www.vertasoft.com/">https://www.vertasoft.com/</a>
    <a href="https://sbtylink.com/">https://sbtylink.com/</a>
    <a href="https://www.gfinorlando.com/">https://www.gfinorlando.com/</a>
    <a href="https://pakjobscareer.com/">https://pakjobscareer.com/</a>
    <a href="https://altwazn.com/">https://altwazn.com/</a>
    <a href="https://www.cafecounsel.com/">https://www.cafecounsel.com/</a>
    <a href="https://heylink.me/situsresmiobc4d/">https://heylink.me/situsresmiobc4d/</a>
</div>