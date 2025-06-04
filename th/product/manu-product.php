<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .sidebar-cat ul {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 5px;
        padding-left: 0;
        margin: 0;
        list-style: none;
    }

    .sidebar-cat ul>li.cat-item {
        background: #e5e5e5;
        border-radius: 2px;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.25s ease;
        height: 100%;
        display: revert-layer;
        align-items: stretch;
    }

    .sidebar-cat ul>li.cat-item:hover {
        background: #f0f4ff;
        box-shadow: 0 8px 20px rgba(50, 50, 93, 0.15), 0 3px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .sidebar-cat ul>li.cat-item.active {
        border-left: 5px solid #3b82f6;
        background-color: #e8f0fe;
    }

    .sidebar-cat ul>li.cat-item>a {
        display: flex;
        align-items: center;
        gap: 10px;
        /* ระยะห่างระหว่างไอคอนกับข้อความ */
        text-decoration: none;
        color: #1a202c;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 10px;
    }

    .sidebar-cat ul>li.cat-item i {
        font-size: 1.5rem;
        color: #3b82f6;
        flex-shrink: 0;
        /* ไม่ให้ไอคอนย่อ */
    }


    /* ลูกศรแสดงว่ามีเมนูย่อย */
    .sidebar-cat ul>li.cat-item.has-children>a::after {
        content: "▼";
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.7rem;
        color: #718096;
        transition: transform 0.3s ease;
    }

    /* หมุนลูกศรเมื่อเมนูย่อยแสดง */
    .sidebar-cat ul>li.cat-item.has-children:hover>a::after {
        transform: translateY(-50%) rotate(180deg);
        color: #3b82f6;
    }

    .sidebar-cat ul>li.cat-item>ul.children {
        margin-top: 10px;
        padding-left: 15px;
        display: none;
        border-left: 3px solid #3b82f6;
        border-radius: 0 8px 8px 0;
        background-color: #f9fbfe;
        box-shadow: inset 2px 0 6px rgba(59, 130, 246, 0.15);
    }

    .sidebar-cat ul>li.cat-item:hover>ul.children {
        display: block;
    }

    .sidebar-cat ul>li.cat-item>ul.children>li.cat-item-300 {
        background-color: #fff;
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 10px 16px;
        font-weight: 600;
        color: #2d3748;
        transition: background-color 0.3s ease, color 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .sidebar-cat ul>li.cat-item>ul.children>li.cat-item-300>a {
        color: inherit;
        font-weight: 600;
        font-size: 0.95rem;
        display: block;
        text-decoration: none;
    }

    .sidebar-cat ul>li.cat-item>ul.children>li.cat-item-300:hover {
        background-color: #3b82f6;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.5);
    }

    .sidebar-cat ul>li.cat-item>ul.children>li.cat-item-300:hover>a {
        color: #ffffff;
    }

    /* ปรับ scrollbar สำหรับเมนูย่อยถ้ายาว */
    .sidebar-cat ul>li.cat-item>ul.children {
        max-height: 250px;
        overflow-y: auto;
    }

    .sidebar-cat ul>li.cat-item>ul.children::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-cat ul>li.cat-item>ul.children::-webkit-scrollbar-thumb {
        background-color: #3b82f6;
        border-radius: 3px;
    }
</style>
<div style="width: 22%; display: inline-block;" class="sidebar-cat col-lg-2 col-md-2 col-sm-2 hidden-xs">
    <ul>
        <?php
        require_once('../php-backend/server.php');
        $db = new server();
        $ierp = $db->connect_sql();

        if ($ierp) {
            $category_order = [
                'DIAMOND Replacement Parts  Pickup, Car & Truck',
                'DIAMOND Replacement Parts  Motorcycle',
                'LED Lighting',
                'Incandescent Lighting',
                'Universal & Safety Accessories',
                'Bulbs',
                'FITT Vehicle Styling Accessories',
                'FACLITE Industrial Lighting',
                
            ];

            $icons = [
                'DIAMOND Replacement Parts  Pickup, Car & Truck' => 'bi-truck',
                'DIAMOND Replacement Parts  Motorcycle' => 'bi-bicycle',
                'Bulbs' => 'bi-lightbulb',
                'FACLITE Industrial Lighting' => 'bi bi-lamp-fill',
                'LED Lighting' => 'bi-lightbulb',
                'Incandescent Lighting' => 'bi bi-lightbulb-fill',
                'Universal & Safety Accessories' => 'bi-tools',
                'FITT Vehicle Styling Accessories' => 'bi-car-front'
            ];

            // Load all categories from DB
            $sql_category = "SELECT DISTINCT category FROM products";
            $result_category = $ierp->query($sql_category);

            if ($result_category && $result_category->num_rows > 0) {
                $all_categories = [];
                while ($row = $result_category->fetch_assoc()) {
                    $all_categories[] = $row['category'];
                }

                // Prepare storage for ordered and unordered HTML
                $html_groups = [];
                $processed = [];

                function encodeCategory($category) {
                    return urlencode(base64_encode($category));
                }

                function getDisplayName($category) {
                    return match ($category) {
                        'DIAMOND Replacement Parts  Pickup, Car & Truck' => 'DIAMOND Replacement Parts<br>Pickup, Car & Truck',
                        'DIAMOND Replacement Parts  Motorcycle' => 'DIAMOND Replacement Parts<br>Motorcycle',
                        default => htmlspecialchars($category)
                    };
                }

                // Process ordered categories
                foreach ($category_order as $cat) {
                    if (!in_array($cat, $all_categories)) continue;

                    $encoded = encodeCategory($cat);
                    $display = getDisplayName($cat);
                    $icon = $icons[$cat] ?? 'bi-box';
                    $decoded_category = isset($_GET['category']) ? base64_decode(urldecode($_GET['category']), true) : '';
                    $isActive = ($decoded_category === $cat) ? 'active' : '';

                    $html = "<li class='cat-item cat-item-299 {$isActive}'>
                                <a href='product-led-lamps.php?category={$encoded}'>
                                    <i class='bi {$icon}'></i>
                                    <span>{$display}</span>
                                </a>
                            </li>";

                    $html_groups[$cat] = "<ul>{$html}</ul>";
                    $processed[] = $cat;
                }

                // Process remaining (unordered) categories
                $other_html = '';
                foreach ($all_categories as $cat) {
                    if (in_array($cat, $processed) || empty($cat)) continue;

                    $encoded = encodeCategory($cat);
                    if (empty($encoded)) continue;

                    $display = getDisplayName($cat);
                    $icon = $icons[$cat] ?? 'bi-box';
                    $isActive = (isset($_GET['category']) && $_GET['category'] === $encoded) ? 'active' : '';

                    $other_html .= "<li class='cat-item cat-item-299 {$isActive}'>
                                        <a href='product-led-lamps.php?category={$encoded}'>
                                            <i class='bi {$icon}'></i>
                                            <span>{$display}</span>
                                        </a>
                                    </li>";
                }

                // Display categories in the defined order
                foreach ($category_order as $cat) {
                    if (isset($html_groups[$cat])) {
                        echo $html_groups[$cat];
                    }
                }

                // Display unordered categories
                if (!empty($other_html)) {
                    echo "<ul>{$other_html}</ul>";
                }
            } else {
                echo "<li>No categories found.</li>";
            }
        } else {
            echo "<li>Database connection error.</li>";
        }
        ?>
    </ul>
</div>

<script>
    // ป้องกันไม่ให้ลิงก์ทำงาน
    $('a').click(function(e) {
        e.preventDefault(); // ป้องกันการคลิกจากลิงก์
    });
</script>
<script>
    document.querySelectorAll('.sidebar-cat ul > li.cat-item').forEach(function(item) {
        item.addEventListener('click', function() {
            // ลบ active จากทุกอันก่อน
            document.querySelectorAll('.sidebar-cat ul > li.cat-item').forEach(function(i) {
                i.classList.remove('active');
            });

            // ใส่ active ให้ item ที่ถูกคลิก
            item.classList.add('active');
        });
    });
</script>