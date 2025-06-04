<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /wdi/www.wdi.co.th/th/login.php');
    exit;
}

// ป้องกันการแคชหน้า
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
require_once('../back-php/server.php');
$db = new server();
$ierp = $db->connect_sql();

$selected_category = $_GET['category'] ?? '';
$selected_brand = $_GET['car_brand_input'] ?? '';
$selected_model = $_GET['car_model_input'] ?? '';
$selected_detail = $_GET['category_detail'] ?? '';

// กำหนดว่าหมวดหมู่นี้เป็น special category หรือไม่ (แก้ตามจริง)
$special_categories = ['FITT Vehicle Styling Accessories', 'DIAMOND Replacement Parts  Pickup, Car & Truck', 'DIAMOND Replacement Parts  Motorcycle'];  
$is_special_category = in_array($selected_category, $special_categories);

// ดึง category ทั้งหมด
$cat_result = $ierp->query("SELECT DISTINCT category FROM products  WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");

// ถ้าเป็น special category ให้ดึง car_brand
$brand_result = null;
if ($is_special_category) {
    $stmt_brand = $ierp->prepare("SELECT DISTINCT car_brand_input FROM products WHERE category = ? ORDER BY car_brand_input ASC");
    $stmt_brand->bind_param("s", $selected_category);
    $stmt_brand->execute();
    $brand_result = $stmt_brand->get_result();
}

// ถ้าเป็น special category และเลือกแบรนด์รถแล้ว ให้ดึงรุ่นรถที่สัมพันธ์กับแบรนด์นั้น
$model_result = null;
if ($is_special_category && !empty($selected_brand)) {
    $stmt_model = $ierp->prepare("SELECT DISTINCT car_model_input FROM products WHERE category = ? AND car_brand_input = ? ORDER BY car_model_input ASC");
    $stmt_model->bind_param("ss", $selected_category, $selected_brand);
    $stmt_model->execute();
    $model_result = $stmt_model->get_result();
}

// ถ้าไม่ใช่ special category ดึง category_detail
$detail_result = null;
if (!$is_special_category && $selected_category) {
    $stmt_detail = $ierp->prepare("SELECT DISTINCT category_detail FROM products WHERE category = ? ORDER BY category_detail ASC");
    $stmt_detail->bind_param("s", $selected_category);
    $stmt_detail->execute();
    $detail_result = $stmt_detail->get_result();
}

// ดึงสินค้า ตามเงื่อนไขที่เลือก
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($selected_category) {
    $sql .= " AND category = ?";
    $params[] = $selected_category;
    $types .= "s";
}
if ($is_special_category && $selected_brand) {
    $sql .= " AND car_brand_input = ?";
    $params[] = $selected_brand;
    $types .= "s";
}
if ($is_special_category && $selected_model) {
    $sql .= " AND car_model_input = ?";
    $params[] = $selected_model;
    $types .= "s";
}
if (!$is_special_category && $selected_detail) {
    $sql .= " AND category_detail = ?";
    $params[] = $selected_detail;
    $types .= "s";
}
$sql .= " ORDER BY display_order ASC";

$stmt = $ierp->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$product_result = $stmt->get_result();
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
            <!-- <div class="mb-4 text-center">
                <button type="button" id="btnProductOrder" class="btn btn-primary me-2">จัดการลำดับสินค้า</button>
                <button type="button" id="btnCategoryOrder" class="btn btn-outline-primary">จัดการลำดับหมวดหมู่</button>
            </div> -->
            <div class="container py-4">
                <h2 class="text-center fw-bold mb-4 fs-3">จัดการลำดับสินค้า</h2>

                <form method="get" class="bg-light p-3 rounded shadow-sm mb-4">
                    <div class="row g-3 align-items-center">
                        <!-- Category + Car Brand in same row -->
                        <div class="col-12">
                            <div class="row g-3 align-items-center">
                                <!-- Category -->
                                <div class="col-md-auto">
                                    <label for="category-select" class="form-label fw-semibold">หมวดหมู่:</label>
                                </div>
                                <div class="col-md-4">
                                    <select name="category" id="category-select" onchange="this.form.submit()" class="form-select">
                                        <option value="">-- ทั้งหมด --</option>
                                        <?php while ($row = $cat_result->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($row['category']) ?>"
                                                <?= $row['category'] === $selected_category ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($row['category']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Car Brand -->
                                <?php if ($is_special_category): ?>
                                    <div class="col-md-auto">
                                        <label for="car_brand-select" class="form-label fw-semibold">แบรนด์รถ:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="car_brand_input" id="car_brand-select" onchange="this.form.submit()" class="form-select">
                                            <option value="">-- เลือกแบรนด์ --</option>
                                            <?php while ($b = $brand_result->fetch_assoc()): ?>
                                                <option value="<?= htmlspecialchars($b['car_brand_input']) ?>"
                                                    <?= $b['car_brand_input'] === $selected_brand ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($b['car_brand_input']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Car Model in separate row -->
                        <?php if ($is_special_category): ?>
                            <div class="col-12">
                                <div class="row g-3 align-items-center mt-1">
                                    <div class="col-md-auto">
                                        <label for="car_model-select" class="form-label fw-semibold">รุ่นรถ:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="car_model_input" id="car_model-select" onchange="this.form.submit()" class="form-select">
                                            <option value="">-- เลือกรุ่น --</option>
                                            <?php while ($m = $model_result->fetch_assoc()): ?>
                                                <option value="<?= htmlspecialchars($m['car_model_input']) ?>"
                                                    <?= $m['car_model_input'] === $selected_model ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($m['car_model_input']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($detail_result && $detail_result->num_rows > 0): ?>
                            <!-- category_detail when not special category -->
                            <div class="col-md-auto">
                                <label for="detail-select" class="form-label fw-semibold">รายละเอียด:</label>
                            </div>
                            <div class="col-md-4">
                                <select name="category_detail" id="detail-select" onchange="this.form.submit()" class="form-select">
                                    <option value="">-- ทั้งหมด --</option>
                                    <?php while ($row = $detail_result->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['category_detail']) ?>"
                                            <?= $row['category_detail'] === $selected_detail ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['category_detail']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>


                <!-- ส่วนแสดงรายการสินค้าและปุ่มบันทึก เหมือนเดิม -->
                <?php if ($product_result && $product_result->num_rows > 0): ?>
                    <ul id="adminProductList" class="list-group mb-4" style="max-height: 500px; overflow-y: auto;">
                        <?php while ($row = $product_result->fetch_assoc()): ?>
                            <li data-id="<?= $row['product_id'] ?>" class="list-group-item d-flex align-items-center gap-3 border rounded shadow-sm mb-2">
                                <div class="flex-shrink-0 rounded border overflow-hidden" style="width: 80px; height: 80px;">
                                    <img src="../back-php/<?= htmlspecialchars($row['image_path'] ?? 'default_image.jpg') ?>"
                                        alt="<?= htmlspecialchars($row['product_name']) ?>" class="img-fluid w-100 h-100 object-fit-cover">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold"><?= htmlspecialchars($row['item_number']) ?></div>
                                    <div class="fw-semibold"><?= htmlspecialchars($row['product_name']) ?></div>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($row['category']) ?>
                                        <?php if (!$is_special_category): ?>
                                            / <?= htmlspecialchars($row['category_detail']) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="text-muted" title="ลากเพื่อจัดลำดับ">
                                    <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <div class="text-center">
                        <button id="saveOrderBtn" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="bi bi-upload me-2"></i>บันทึกลำดับสินค้า
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-emoji-frown display-4 mb-3"></i>
                        <p class="fs-5 fst-italic">ไม่พบสินค้าที่ตรงกับเงื่อนไขที่เลือก</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script src="../js-controller/product_manage.js"></script>
    <script src="../js/app.js"></script>
    <script>
        const sortable = new Sortable(document.getElementById('adminProductList'), {
            animation: 150
        });

        document.getElementById('saveOrderBtn').addEventListener('click', function() {
            const order = Array.from(document.querySelectorAll('#adminProductList li')).map((el, index) => ({
                id: el.dataset.id,
                order: index + 1
            }));

            fetch('../back-php/order_manage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'Update_adminProductList',
                        order: order
                    })
                })
                .then(res => res.text())
                .then(msg => {
                    // ใช้ SweetAlert2 แทน alert()
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกเรียบร้อย',
                        text: msg,
                        confirmButtonColor: '#3085d6'
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: err,
                        confirmButtonColor: '#d33'
                    });
                });
        });
    </script>
<!-- <script>
document.getElementById('btnProductOrder').addEventListener('click', function() {
    document.getElementById('productOrderSection').style.display = 'block';
    document.getElementById('categoryOrderSection').style.display = 'none';
    this.classList.add('btn-primary');
    this.classList.remove('btn-outline-primary');
    document.getElementById('btnCategoryOrder').classList.remove('btn-primary');
    document.getElementById('btnCategoryOrder').classList.add('btn-outline-primary');
});
document.getElementById('btnCategoryOrder').addEventListener('click', function() {
    document.getElementById('productOrderSection').style.display = 'none';
    document.getElementById('categoryOrderSection').style.display = 'block';
    this.classList.add('btn-primary');
    this.classList.remove('btn-outline-primary');
    document.getElementById('btnProductOrder').classList.remove('btn-primary');
    document.getElementById('btnProductOrder').classList.add('btn-outline-primary');
});
</script> -->

    <script>
        function logoutUser() {
            fetch('/logout.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                }) // ส่งคำขอไปที่ logout.php
                .then(response => {
                    // ล้างแคชและป้องกันการย้อนกลับ
                    window.location.replace('/wdi/www.wdi.co.th/th/index.php');
                })
                .catch(error => console.error('Logout error:', error));
        }
    </script>
</body>

</html>