<?php
require_once('../php-backend/server.php');
$db = new server();
$ierp = $db->connect_sql();

// ลิสต์ลำดับที่ต้องการแสดง
$category_order = [
    'DIAMOND Replacement Parts Pickup, Car & Truck',
    'DIAMOND Replacement Parts Motorcycle',
    'LED Lighting',
    'Incandescent Lighting',
    'Universal & Safety Accessories',
    'Bulbs',
    'FITT Vehicle Styling Accessories',
    'FACLITE Industrial Lighting',
];

// ฟังก์ชัน normalize ชื่อ (ตัด space แปลก ๆ ทุกชนิด อักขระพิเศษ)
function normalize_category_name($str)
{
    // แปลง & เป็น and (ถ้าต้องการ)
    $str = str_replace('&', 'and', $str);
    // ตัดช่องว่างแบบต่าง ๆ ให้เป็น space ปกติ และลดช่องว่างต่อเนื่องเหลือ 1 ตัว
    $str = preg_replace('/\s+/u', ' ', $str);
    // trim ขอบซ้ายขวา
    $str = trim($str);
    // เปลี่ยนเป็น lowercase
    $str = mb_strtolower($str);
    return $str;
}

// ดึงหมวดหมู่ทั้งหมดจากฐานข้อมูล
$sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
$result = $ierp->query($sql);

$raw_categories = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $raw_categories[] = $row['category'];
    }
}

// สร้างลิสต์เรียงตาม $category_order โดยแมตช์แบบ normalize
$main_categories_ordered = [];
$matched = [];

foreach ($category_order as $ordered) {
    $normalized_ordered = normalize_category_name($ordered);
    foreach ($raw_categories as $i => $cat) {
        if (isset($matched[$i])) continue; // ข้ามที่แมตช์แล้ว

        $normalized_cat = normalize_category_name($cat);

        if ($normalized_ordered === $normalized_cat) {
            // เอาชื่อจริงจาก DB มาแสดง (ไม่แก้ไข)
            $main_categories_ordered[] = [
                'name' => $cat,
                'icon_class' => ''
            ];
            $matched[$i] = true;
            break;
        }
    }
}

// หมวดหมู่ที่เหลือ
$other_categories = [];
foreach ($raw_categories as $i => $cat) {
    if (!isset($matched[$i])) {
        $other_categories[] = [
            'name' => $cat,
            'icon_class' => ''
        ];
    }
}

// เรียงชื่อ A-Z หมวดหมู่ที่เหลือ
usort($other_categories, fn($a, $b) => strcmp($a['name'], $b['name']));

// รวมผลลัพธ์ทั้งหมด
$main_categories = array_merge($main_categories_ordered, $other_categories);
?>

<!DOCTYPE html>
<html lang="th-TH">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wichien Dynamic Industry</title>
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css">
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    
    <style>
        body {
            font-family: 'Barlow', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f8f8f8;
        }

        .main-category-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-item {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
        }

        .category-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: #a7d9ff;
        }

        .category-item a {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        .category-icon-wrapper {
            width: 96px;
            height: 96px;
            background-color: #e0f2fe;
            color: #2196f3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 1rem;
            border: 3px solid #90caf9;
            transition: all 0.3s ease-in-out;
        }

        .category-item:hover .category-icon-wrapper {
            background-color: #bbdefb;
            border-color: #64b5f6;
        }

        .category-item h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin: 0.5rem 0 0;
        }

        @media (max-width: 768px) {
            .main-category-buttons {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .main-category-buttons {
                grid-template-columns: 1fr;
            }

            .category-item {
                padding: 1rem;
            }

            .category-icon-wrapper {
                width: 80px;
                height: 80px;
                font-size: 2.5rem;
            }

            .category-item h3 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body class="home page-template page page-id-2792 woocommerce-no-js">
    <?php require '../nav-bar.php'; ?>

    <div id="main-categories-section" class="container mx-auto py-12 px-4">
        <h2 class="text-center text-4xl font-extrabold text-gray-900 mb-10" data-th="ประเภทสินค้าหลัก" data-en="Main product categories">
            Main product categories
        </h2>

        <ul class="main-category-buttons">
            <?php if (!empty($main_categories)): ?>
                <?php
                $category_icons = [
                    'DIAMOND Replacement Parts Pickup, Car & Truck' => 'bi-truck',
                    'DIAMOND Replacement Parts Motorcycle' => 'bi-bicycle',
                    'Bulbs' => 'bi-lightbulb',
                    'FACLITE Industrial Lighting' => 'bi-lamp-fill',
                    'LED Lighting' => 'bi-lightbulb',
                    'Incandescent Lighting' => 'bi-lightbulb-fill',
                    'Universal & Safety Accessories' => 'bi-tools',
                    'FITT Vehicle Styling Accessories' => 'bi-car-front'
                ];
                ?>
                <?php foreach ($main_categories as $category):
                    $name = htmlspecialchars($category['name']);
                    $encoded_name = urlencode(base64_encode($category['name']));
                    $icon_class = 'bi ' . ($category_icons[$category['name']] ?? 'bi-box');
                ?>
                    <li class="category-item">
                        <a href="../product/product-led-lamps.php?category=<?php echo $encoded_name; ?>">
                            <div class="category-icon-wrapper">
                                <i class="<?php echo $icon_class; ?>"></i>
                            </div>
                            <h3><?php echo $name; ?></h3>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600 col-span-full py-8">ไม่พบหมวดหมู่สินค้า</p>
            <?php endif; ?>
        </ul>
    </div>

    <?php require '../footer-page.php'; ?>
    <script src="/wdi/www.wdi.co.th/th/js-control/manage-index.js"></script>
</body>

</html>