<?php
// เชื่อมต่อฐานข้อมูล
require_once('server.php');
$db = new server();
$ierp = $db->connect_sql();
$errors = array();

if (isset($_POST['submit'])) {
    $item_number = trim($_POST['item_number']);
    $NameProduct = trim($_POST['NameProduct']);
    $price = trim($_POST['price']);
    $product_quantity = trim($_POST['product_quantity']);
    $product_description = trim($_POST['product_description']);
    $product_category = trim($_POST['product_category']);
    $product_status = $_POST['product_status'];

    // ตรวจสอบว่าข้อมูลถูกกรอกครบ
    if (empty($item_number)) array_push($errors, "Item number is required.");
    if (empty($NameProduct)) array_push($errors, "Product name is required.");
    if (empty($price)) array_push($errors, "Price is required.");
    if (empty($product_quantity)) array_push($errors, "Product quantity is required.");
    if (empty($product_description)) array_push($errors, "Product description is required.");
    if (empty($product_category)) array_push($errors, "Product category is required.");
    if (empty($product_status)) array_push($errors, "Product status is required.");

    // ตรวจสอบว่าหมายเลขสินค้า (item_number) ซ้ำหรือไม่
    $check_query = "SELECT * FROM products WHERE item_number = ?";
    $stmt_check = mysqli_prepare($ierp, $check_query);
    mysqli_stmt_bind_param($stmt_check, "s", $item_number);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $existing_product = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);

    if ($existing_product) {
        array_push($errors, "Item number already exists.");
    }

    // ถ้าไม่มี error ให้เพิ่มข้อมูลลงในฐานข้อมูล
    if (count($errors) == 0) {
        // เตรียมคำสั่ง (statement) สำหรับการแทรกข้อมูล
        $sql = "INSERT INTO products (item_number, name, price, quantity, description, category, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($ierp, $sql);

        // ตรวจสอบการเตรียมคำสั่ง
        if ($stmt) {
            // ผูกค่ากับคำสั่ง SQL
            mysqli_stmt_bind_param($stmt, "ssdiiss", $item_number, $NameProduct, $price, $product_quantity, $product_description, $product_category, $product_status);

            // ประมวลผลคำสั่ง
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Product added successfully.";
                header('Location: pages-blank.php'); // เปลี่ยนเส้นทางไปหน้า index
                exit;
            } else {
                array_push($errors, "Failed to add product: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            array_push($errors, "Failed to prepare SQL statement.");
        }
    }

    // เก็บ error ไว้ใน session เพื่อนำไปแสดงผล
    $_SESSION['errors'] = $errors;
    header('Location: index.php'); // เปลี่ยนเส้นทางกลับไปหน้าเพิ่มสินค้า
    exit;
}
?>
