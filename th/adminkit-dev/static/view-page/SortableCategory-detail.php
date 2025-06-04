<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /wdi/www.wdi.co.th/th/login.php');
    exit;
}

// ป้องกันการแคชหน้า
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once('../back-php/server.php');
$db = new server();
$ierp = $db->connect_sql();

if (isset($_GET['category']) && isset($_GET['brand'])) {
    // 1. กรณีระบุ category + brand → ดึง models
    $category = $_GET['category'];
    $brand = $_GET['brand'];

    $stmt = $ierp->prepare("SELECT DISTINCT car_model_input, car_image_upload FROM products WHERE category = ? AND car_brand_input = ? AND car_model_input IS NOT NULL AND car_model_input != '' ORDER BY model_display_order  ASC ,car_model_input ASC");
    $stmt->bind_param("ss", $category, $brand);
    $stmt->execute();
    $models_result = $stmt->get_result();

    $models = [];
    while ($row = $models_result->fetch_assoc()) {
        $models[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($models);
    exit;
}

if (isset($_GET['category'])) {
    // 2. กรณีมีเฉพาะ category → ตรวจสอบว่าเป็นหมวดพิเศษหรือไม่
    $category = $_GET['category'];

    $special_categories = [
        'FITT Vehicle Styling Accessories',
        'DIAMOND Replacement Parts  Pickup, Car & Truck',
        'DIAMOND Replacement Parts  Motorcycle'
    ];

    if (in_array($category, $special_categories)) {
        // ดึงแบรนด์
        $stmt = $ierp->prepare("SELECT DISTINCT car_brand_input, car_image_upload_brand FROM products WHERE category = ? AND car_brand_input IS NOT NULL AND car_brand_input != '' ORDER BY brand_display_order ASC ,car_brand_input ASC");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $brands_result = $stmt->get_result();

        $brands = [];
        $brand_names = [];
        while ($row = $brands_result->fetch_assoc()) {
            $brands[] = $row;
            $brand_names[] = strtolower(trim($row['car_brand_input']));
        }

        // ดึงรุ่น (เฉพาะรุ่นที่ไม่ซ้ำกับชื่อแบรนด์)
        $stmt2 = $ierp->prepare("SELECT DISTINCT car_model_input, car_image_upload FROM products WHERE category = ? AND car_model_input IS NOT NULL AND car_model_input != '' ORDER BY model_display_order  ASC ,car_model_input ASC");
        $stmt2->bind_param("s", $category);
        $stmt2->execute();
        $models_result = $stmt2->get_result();

        $models = [];
        while ($row = $models_result->fetch_assoc()) {
            $model_name = strtolower(trim($row['car_model_input']));
            if (!in_array($model_name, $brand_names)) {
                $models[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'brands' => $brands,
            'models' => $models
        ]);
        exit;
    } else {
        // กรณี category ปกติ → ส่ง category_detail
        $stmt = $ierp->prepare("SELECT DISTINCT category_detail FROM products WHERE category = ? ORDER BY category_detail_display_order ASC, category_detail ASC");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        $details = [];
        while ($row = $result->fetch_assoc()) {
            $details[] = $row['category_detail'];
        }

        header('Content-Type: application/json');
        echo json_encode($details);
        exit;
    }
}


$category_result = $ierp->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="../img/icons/icon-48x48.png" />

    <title>Admin Pages</title>

    <link href="../css/app.css" rel="stylesheet">
    <link href="../css/addPD.css" rel="stylesheet">
    <!-- <link href="../css/page-sortable.css" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- Bootstrap CSS (in head or layout) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (optional for icon use) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>
    <div class="wrapper">
        <?php require 'dashboard.php'; ?>


        <div class="main">
            <?php require 'nav-profile.php'; ?>
            <div class="container my-4">
                <h3 class="mb-4">จัดเรียงลำดับหมวดย่อย (category_detail)</h3>

                <div class="mb-3">
                    <label for="categorySelect" class="form-label fw-semibold">เลือกหมวดหมู่หลัก (category):</label>
                    <select id="categorySelect" class="form-select">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php while ($row = $category_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['category']) ?>">
                                <?= htmlspecialchars($row['category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <ul id="categoryDetailList" class="list-group mb-4 d-none" style="cursor: grab;"></ul>
                <div class="row mb-4 d-none" id="brandModelContainer" style="cursor: grab;">
                    <div class="col-md-6">
                        <h5>แบรนด์รถ (ลากเลื่อนจัดเรียง)</h5>
                        <ul id="categoryDetailBandList" class="list-group"></ul>
                    </div>
                    <div class="col-md-6">
                        <label for="brandSelect" class="form-label fw-semibold">เลือกแบรนด์รถ:</label>
                        <select id="brandSelect" class="form-select mb-3" disabled>
                            <option value="">-- เลือกแบรนด์ --</option>
                        </select>
                        <h5>รุ่นรถ (ลากเลื่อนจัดเรียง)</h5>
                        <ul id="categoryDetailmodleList" class="list-group"></ul>
                    </div>
                </div>



                <div class="text-center">
                    <button id="saveOrderBtn" class="btn btn-primary btn-lg px-5 d-none">
                        <i class="bi bi-upload me-2"></i>บันทึกลำดับ
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script src="../js-controller/product_manage.js"></script>
    <script src="../js/app.js"></script>
    <script>
        const detailList = document.getElementById('categoryDetailList');
        const brandList = document.getElementById('categoryDetailBandList');
        const modelList = document.getElementById('categoryDetailmodleList');
        const brandModelContainer = document.getElementById('brandModelContainer');
        const brandSelect = document.getElementById('brandSelect');

        let sortableDetail = null;
        let sortableBrand = null;
        let sortableModel = null;

        const specialCategories = [
            'FITT Vehicle Styling Accessories',
            'DIAMOND Replacement Parts  Pickup, Car & Truck',
            'DIAMOND Replacement Parts  Motorcycle'
        ];

        $('#categorySelect').on('change', function() {
            const category = $(this).val();

            // Reset
            $('#categoryDetailList').addClass('d-none').empty();
            brandModelContainer.classList.add('d-none');
            brandList.innerHTML = '';
            modelList.innerHTML = '';
            brandSelect.innerHTML = '<option value="">-- เลือกแบรนด์ --</option>';
            brandSelect.disabled = true;
            $('#saveOrderBtn').addClass('d-none');

            if (sortableDetail) sortableDetail.destroy();
            if (sortableBrand) sortableBrand.destroy();
            if (sortableModel) sortableModel.destroy();

            if (!category) return;

            if (specialCategories.includes(category)) {
                fetch(`?category=${encodeURIComponent(category)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.brands && data.models) {
                            brandModelContainer.classList.remove('d-none');

                            const shownBrandNames = new Set();

                            // เติม list ด้านซ้าย (dragable list)
                            data.brands.forEach(brand => {
                                const brandName = brand.car_brand_input.trim();
                                if (shownBrandNames.has(brandName.toLowerCase())) return;
                                shownBrandNames.add(brandName.toLowerCase());

                                const li = document.createElement('li');
                                li.classList.add('list-group-item');
                                li.setAttribute('data-name', brandName);

                                let imgTag = '';
                                if (brand.car_image_upload_brand) {
                                    imgTag = `<img src="../back-php/${brand.car_image_upload_brand}" alt="${brandName}" style="height:40px; margin-right:10px;">`;
                                }

                                li.innerHTML = `<i class="bi bi-arrows-move me-2"></i>${imgTag}${brandName}`;
                                brandList.appendChild(li);

                                // เพิ่มเข้า dropdown
                                const opt = document.createElement('option');
                                opt.value = brandName;
                                opt.textContent = brandName;
                                brandSelect.appendChild(opt);
                            });

                            brandSelect.disabled = false;

                            // เปิด sortable สำหรับ brand list
                            sortableBrand = new Sortable(brandList, {
                                animation: 150,
                                ghostClass: 'bg-warning'
                            });

                            $('#saveOrderBtn').removeClass('d-none');
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching brand/model data:', err);
                    });

            } else {
                fetch(`?category=${encodeURIComponent(category)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            $('#categoryDetailList').removeClass('d-none').empty();
                            data.forEach(detail => {
                                $('#categoryDetailList').append(`
                            <li class="list-group-item" data-name="${detail}">
                                <i class="bi bi-arrows-move me-2"></i> ${detail}
                            </li>
                        `);
                            });

                            $('#saveOrderBtn').removeClass('d-none');

                            sortableDetail = new Sortable(detailList, {
                                animation: 150,
                                ghostClass: 'bg-warning'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching category details:', err);
                    });
            }
        });

        // ✅ โหลดรุ่นรถตามแบรนด์ที่เลือก
        brandSelect.addEventListener('change', function() {
            const category = $('#categorySelect').val();
            const brand = this.value;
            modelList.innerHTML = '';
            if (!brand || !category) return;

            fetch(`?category=${encodeURIComponent(category)}&brand=${encodeURIComponent(brand)}`)
                .then(res => res.json())
                .then(models => {
                    models.forEach(model => {
                        const modelName = model.car_model_input.trim();
                        const li = document.createElement('li');
                        li.classList.add('list-group-item');
                        li.setAttribute('data-name', modelName);

                        let imgTag = '';
                        if (model.car_image_upload) {
                            imgTag = `<img src="../back-php/${model.car_image_upload}" alt="${modelName}" style="height:40px; margin-right:10px;">`;
                        }

                        li.innerHTML = `<i class="bi bi-arrows-move me-2"></i>${imgTag}${modelName}`;
                        modelList.appendChild(li);
                    });

                    if (sortableModel) sortableModel.destroy();

                    sortableModel = new Sortable(modelList, {
                        animation: 150,
                        ghostClass: 'bg-warning'
                    });
                })
                .catch(err => {
                    console.error('Error loading models:', err);
                });
        });

        // ✅ Save Order
        $('#saveOrderBtn').on('click', async function() {
            const category = $('#categorySelect').val();

            if (!category) {
                alert('กรุณาเลือกหมวดหมู่ก่อนบันทึก');
                return;
            }

            let payload = {
                category
            };

            if (specialCategories.includes(category)) {
                const brandOrder = Array.from(brandList.children).map((el, index) => ({
                    name: el.dataset.name,
                    order: index + 1
                }));

                const modelOrder = Array.from(modelList.children).map((el, index) => ({
                    name: el.dataset.name,
                    order: index + 1
                }));

                payload.action = 'update_brand_model_order';
                payload.brandOrder = brandOrder;
                payload.modelOrder = modelOrder;
                payload.selectedBrand = brandSelect.value; // เพิ่มแบรนด์ที่เลือกไว้
            } else {
                const order = Array.from(detailList.children).map((el, index) => ({
                    name: el.dataset.name,
                    order: index + 1
                }));

                payload.action = 'update_category_detail_order';
                payload.Order = order;

            }

            fetch('../back-php/order_manage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.text())
                .then(msg => {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกเรียบร้อย',
                        text: msg
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: err.message || 'Unknown error'
                    });
                });
        });
    </script>

</body>

</html>