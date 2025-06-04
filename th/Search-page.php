<?php
require_once('php-backend/server.php');
$db = new server();
$ierp = $db->connect_sql();

$search_term = isset($_GET['s']) ? trim($_GET['s']) : '';

if (!empty($search_term)) {
    $search_term = $ierp->real_escape_string($search_term);
    $sql = "SELECT * FROM products WHERE item_number LIKE '%$search_term%'  OR product_name LIKE '%$search_term%' OR category LIKE '%$search_term%'";
    $result = $ierp->query($sql);
}
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


    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" sizes="192x192" />
    <style>
        .navbar-search input[type="text"] {
            padding: 6px 10px;
            font-size: 1rem;
            width: 200px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .navbar-search button {
            padding: 6px 12px;
            font-size: 1rem;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .search-results-section {
            padding: 40px 0;
            min-height: 400px;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            overflow: hidden;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .product-card .product-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            width: 100%;
            padding: 15px;
            flex-grow: 1;/
        }

        .product-card .product-details {
            flex-grow: 1;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: inherit;
        }

        .product-card .card-title {
            font-size: 1.8rem;
            /* ปรับขนาดหัวข้อรหัสสินค้าให้เล็กลง */
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            /* ลดระยะห่างด้านล่าง */
        }

        .product-card .card-subtitle {
            font-size: 1em;
            /* ปรับขนาดหัวข้อชื่อสินค้าให้เล็กลง */
            color: #666;
            margin-bottom: 8px;
            /* ลดระยะห่างด้านล่าง */
        }

        .product-card .card-text {
            font-size: 1.2rem;
            /* ปรับขนาดข้อความรายละเอียดให้เล็กลง */
            color: #666;
            margin-bottom: 0;
            /* ลบ margin-bottom ถ้าไม่มีปุ่ม */
        }

        /* ส่วนของรูปภาพสินค้า (อยู่ขวา) */
        .product-image-wrapper {
            flex-shrink: 0;
            /* ป้องกันไม่ให้รูปภาพหดตัว */
            margin-left: 15px;
            /* เพิ่มระยะห่างด้านซ้ายของรูปภาพ */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card .product-thumb {
            max-width: 100px;
            /* กำหนดความกว้างสูงสุดของรูปภาพให้เล็กมาก */
            height: 100px;
            /* กำหนดความสูงเพื่อให้เป็นสี่เหลี่ยมจัตุรัส */
            object-fit: cover;
            /* ทำให้รูปภาพครอบคลุมพื้นที่ที่กำหนดโดยไม่ผิดสัดส่วน */
            border-radius: 4px;
            /* ทำให้รูปภาพมีขอบมนเล็กน้อย */
            border: 1px solid #eee;
            /* เพิ่มเส้นขอบเล็กน้อย */
        }

        /* สไตล์สำหรับปุ่ม (ถ้ามีปุ่มแยกต่างหาก) */
        .product-card .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
            align-self: flex-start;
            /* Align button to start */
            margin-top: 10px;
            /* เพิ่มระยะห่างจากข้อความด้านบน */
        }

        .product-card .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .no-results {
            text-align: center;
            padding: 50px 0;
            font-size: 1.2rem;
            color: #777;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 767.98px) {
            .product-card .product-link {
                flex-direction: column;
                /* เปลี่ยนเป็นแนวตั้งเมื่อหน้าจอเล็ก */
                text-align: center;
                /* จัดข้อความอยู่กลาง */
            }

            .product-image-wrapper {
                margin-left: 0;
                /* ลบ margin ด้านซ้าย */
                margin-top: 15px;
                /* เพิ่ม margin ด้านบน */
            }

            .product-card .product-thumb {
                max-width: 100px;
                /* อาจจะทำให้รูปใหญ่ขึ้นเล็กน้อยบนมือถือ */
                height: 100px;
            }

            .product-card .product-details {
                align-items: center;
                /* จัดข้อความอยู่กลางเมื่อเป็นแนวตั้ง */
            }
        }
    </style>

</head>

<body class="home page-template page-template-page-home page page-id-2792 woocommerce-no-js">

    <?php require 'nav-bar.php'; ?>

    <section class="search-results-section">
        <div class="container">
            <h2 class="mb-4">ผลการค้นหา: "<?= htmlspecialchars($search_term) ?>"</h2>
            <hr>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="row">
                    <?php while ($row = $result->fetch_assoc()):
                        $product_id_for_url = '';
                        if (isset($row['product_id'])) {
                            $product_id_for_url = urlencode(base64_encode($row['product_id']));
                        }
                    ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="product-card d-flex">
                                <a href="/wdi/www.wdi.co.th/th/product/view-product.php?product_id=<?= htmlspecialchars($product_id_for_url) ?>" class="product-link">
                                    <div class="product-image-wrapper">
                                        <img src="../th/adminkit-dev/static/back-php/<?= htmlspecialchars($row['image_path']) ?>" alt="Product Image" class="img-fluid product-thumb">
                                    </div>
                                    <div class="card-body product-details">
                                        <h4 class="card-title"> <?= htmlspecialchars($row['item_number']) ?></h4>
                                        <h5 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($row['product_name']) ?></h5>
                                        <p class="card-text">
                                            <strong>หมวดหมู่:</strong> <?= htmlspecialchars($row['category'] ?? 'N/A') ?><br>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p><i class="fas fa-exclamation-circle"></i> ไม่พบข้อมูลที่ค้นหา กรุณาลองคำค้นหาอื่น</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php require 'footer-page.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

</body>

</html>