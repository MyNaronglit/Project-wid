<?php
require_once('server.php');
$db = new server();
$ierp = $db->connect_sql();

// ดึงรายการสินค้า ทั้งการค้นหาและเรียงลำดับ
if (isset($_GET['action']) && $_GET['action'] == 'fetch_orders') {
    $orders = [];

    // ตรวจสอบค่าการค้นหา
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $searchTermEscaped = mysqli_real_escape_string($ierp, $searchTerm);

    // ตรวจสอบค่าการเรียงลำดับ (ค่าเริ่มต้นเป็น DESC)
    $sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'ASC' ? 'ASC' : 'DESC';

    // คิวรี่ข้อมูลสินค้า
    $queryPending = "SELECT product_id, item_number, product_name, created_at ,category , category_detail
                     FROM products ";

    // ถ้ามีการค้นหา ให้เพิ่มเงื่อนไข
    if (!empty($searchTermEscaped)) {
        $queryPending .= " WHERE product_name LIKE '%$searchTermEscaped%' 
                          OR item_number LIKE '%$searchTermEscaped%' ";
    }

    // เพิ่มเงื่อนไข ORDER BY
    $queryPending .= " ORDER BY created_at $sortOrder ";

    $resultPending = mysqli_query($ierp, $queryPending);

    while ($row = mysqli_fetch_assoc($resultPending)) {
        $orders[] = [
            'product_id'    => $row['product_id'],
            'item_number'   => $row['item_number'],
            'product_name'  => $row['product_name'],
            'created_at'    => $row['created_at'],
            'category'      => $row['category'],
            'category_detail' => $row['category_detail']
        ];
    }

    echo json_encode($orders);
    exit;
}




if (isset($_GET['action']) && $_GET['action'] == 'get_order_details' && isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($ierp, $_GET['id']);
    $query = "SELECT * FROM products WHERE product_id = '$product_id'";
    $result = mysqli_query($ierp, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        error_log("Order not found for ID: $product_id"); // Log error
        echo '<p class="text-center text-danger">Order not found.</p>';
        exit;
    }

    $product = mysqli_fetch_assoc($result);
    echo '<div class="product-details">';

    // แสดงรูปภาพถ้ามี
    if (!empty($product['image_path'])) {
        echo '<img src="../back-php/' . htmlspecialchars($product['image_path']) . '" width="100" height="auto" alt="Product Image">';
    }

    // ข้อมูลที่ต้องแสดง
    $fields = [
        'product_id'   => 'Order ID',
        'product_name' => 'Product',
        'item_number'  => 'SKU',
        'category' => 'Category',
        'category_detail' => 'Category Detail',
        'status'       => 'Status',
        'created_at'   => 'Created At'
    ];

    // วนลูปแสดงข้อมูลเฉพาะที่มีค่า
    foreach ($fields as $key => $label) {
        if (!empty($product[$key])) {
            echo '<p><strong>' . $label . ':</strong> ' . htmlspecialchars($product[$key]) . '</p>';
        }
    }

    // ปุ่ม Edit และ Delete
    echo '
        <button class="btn btn-info edit-product" data-product_id="' . htmlspecialchars($product['product_id']) . '">Edit</button>
        <button class="btn btn-danger delete-product" data-product_id="' . htmlspecialchars($product['product_id']) . '">✖ Delete</button>
    </div>';

    exit;
}


// อัปเดตสถานะออเดอร์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $order_id = intval($_POST['order_id']);
    $new_status = trim($_POST['status']); // ตัดช่องว่างด้านหน้า/หลังออก
    $new_status = strtolower($new_status);

    // ตรวจสอบค่าว่าอยู่ใน ENUM หรือไม่
    if (!in_array($new_status, ['pending', 'completed', 'failed'])) {
        echo json_encode(["success" => false, "error" => "Invalid status"]);
        exit;
    }

    // ใช้ Prepared Statement ป้องกัน SQL Injection
    $stmt = $ierp->prepare("UPDATE orders SET payment_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    exit;
}
// ดึงข้อมูลสินค้าเพื่อแก้ไข
if (isset($_GET['action']) && $_GET['action'] === 'get_edit_product' && isset($_GET['id'])) {

    $product_id = $_GET['id'];

    $stmt = $ierp->prepare("SELECT * FROM products WHERE product_id = ? LIMIT 1");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ดึง detail images
        $detail_stmt = $ierp->prepare("SELECT detail_img_product, detail_img_id FROM detail_img_product WHERE detail_product_id = ?");
        $detail_stmt->bind_param("s", $product_id);
        $detail_stmt->execute();
        $detail_result = $detail_stmt->get_result();

        $detail_images = [];
        while ($detail_row = $detail_result->fetch_assoc()) {
            // เพิ่มทั้งแถว (ซึ่งเป็น associative array) เข้าไปใน $detail_images
            $detail_images[] = $detail_row;
        }

        // ✅ ดึง category ทั้งหมด
        $all_stmt = $ierp->query("SELECT category, category_detail FROM products");
        $all_products = [];
        while ($all_row = $all_stmt->fetch_assoc()) {
            $all_products[] = [
                'category' => $all_row['category'],
                'category_detail' => $all_row['category_detail']
            ];
        }

        echo json_encode([
            'product' => [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'item_number' => $row['item_number'],
                'Product_content_en' => $row['Product_content_en'],
                'Product_content_th' => $row['Product_content_th'],
                'status' => $row['status'],
                'category' => $row['category'],
                'category_detail' => $row['category_detail'],
                'created_at' => $row['created_at'],
                'image_path' => $row['image_path'],
                'RefID_img' => $row['RefID_img'],
                'product_function' => $row['product_function'],
                'product_func_image' => $row['product_func_image'],
                'manual_pdf' => $row['manual_pdf'],
                'youtube_links' => $row['youtube_links'],
                'detail_images' => $detail_images
            ],
            'all_products' => $all_products // ✅ ส่ง array นี้ไปให้ JS ใช้สร้าง dropdown
        ]);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }

    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_product') {

    // รับค่าจาก AJAX
    $product_id = mysqli_real_escape_string($ierp, $_POST['product_id']);
    $RefID_img = mysqli_real_escape_string($ierp, $_POST['RefID_img']);
    $product_name = mysqli_real_escape_string($ierp, $_POST['product_name']);
    $item_number = mysqli_real_escape_string($ierp, $_POST['item_number'] ?? '');
    $category = mysqli_real_escape_string($ierp, $_POST['category']);
    $category_detail = mysqli_real_escape_string($ierp, $_POST['category_detail']);
    $product_function = mysqli_real_escape_string($ierp, $_POST['product_function'] ?? '');
    $Product_content_en = mysqli_real_escape_string($ierp, $_POST['Product_content_en'] ?? '');

    // ดึงข้อมูลปัจจุบันจากฐานข้อมูลก่อน
    $query = "SELECT image_path, product_func_image ,car_image_upload ,car_image_upload_brand ,manual_pdf FROM products WHERE product_id = '$product_id' LIMIT 1";
    $result = mysqli_query($ierp, $query);
    $current_data = mysqli_fetch_assoc($result);

    $querydetail_img = "SELECT 	*  FROM detail_img_product WHERE detail_RefID_img = '$RefID_img'";
    $resultdetail_img = mysqli_query($ierp, $querydetail_img);
    $current_detail_img = mysqli_fetch_assoc($resultdetail_img);


    // กำหนดค่าของไฟล์เป็นค่าปัจจุบันในฐานข้อมูล
    $image = $current_data['image_path'];
    $product_func_image = $current_data['product_func_image'];
    $image_details_paths = $current_detail_img['detail_img_product'];
    $car_image_upload = $current_data['car_image_upload_brand'];
    $car_image_upload_brand = $current_data['car_image_upload_brand'];
    $youtube_links = null;
    $manual_pdf = $current_data['car_image_upload_brand'];

    if (!empty($_POST['youtube_links']) && is_array($_POST['youtube_links'])) {
        $cleanLinks = [];
        foreach ($_POST['youtube_links'] as $link) {
            $link = trim($link);
            if ($link !== "") {
                $cleanLinks[] = mysqli_real_escape_string($ierp, $link);
            }
        }
        if (count($cleanLinks) > 0) {
            $youtube_links = implode(',', $cleanLinks);
        }
    }

    // ตรวจสอบการอัปโหลดไฟล์หลัก
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        // ตรวจสอบและย้ายไฟล์ที่อัปโหลด
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $targetPath; // อัปเดตค่าหากมีการอัปโหลด
        } else {
            echo json_encode(["error" => "Failed to upload main image"]);
            exit;
        }
    }


    // อัปโหลดฟังก์ชันภาพ (support เดี่ยวและหลายไฟล์)
    if (isset($_FILES['product_func_image'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $paths = [];

        // ถ้าเป็น multiple (JS ส่ง name="product_func_image[]" นิยมเป็น array)
        if (is_array($_FILES['product_func_image']['tmp_name'])) {
            foreach ($_FILES['product_func_image']['tmp_name'] as $i => $tmpName) {
                if ($_FILES['product_func_image']['error'][$i] === UPLOAD_ERR_OK) {
                    $imageName = basename($_FILES['product_func_image']['name'][$i]);
                    $targetPath = $uploadDir . $imageName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $paths[] = $targetPath;
                    }
                }
            }
        }
        // ถ้าเป็น single file
        else {
            if ($_FILES['product_func_image']['error'] === UPLOAD_ERR_OK) {
                $imageName = basename($_FILES['product_func_image']['name']);
                $targetPath = $uploadDir . $imageName;
                if (move_uploaded_file($_FILES['product_func_image']['tmp_name'], $targetPath)) {
                    $paths[] = $targetPath;
                }
            }
        }

        if (!empty($paths)) {
            $product_func_image = implode(',', $paths);
        }
    }

    // ตรวจสอบการอัปโหลดไฟล์ car_image_upload
    if (isset($_FILES['car_image_upload']) && $_FILES['car_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['car_image_upload']['name']);
        $targetPath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['car_image_upload']['tmp_name'], $targetPath)) {
            $car_image_upload = $targetPath;
        } else {
            echo json_encode(["error" => "Failed to upload function image"]);
            exit;
        }
    }

    if (isset($_FILES['car_image_upload_brand']) && $_FILES['car_image_upload_brand']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['car_image_upload_brand']['name']);
        $targetPath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['car_image_upload_brand']['tmp_name'], $targetPath)) {
            $car_image_upload_brand = $targetPath;
        } else {
            echo json_encode(["error" => "Failed to upload function image"]);
            exit;
        }
    }

    if (isset($_FILES['manual_pdf']) && $_FILES['manual_pdf']['error'] === UPLOAD_ERR_OK) {
        $uploadBase = 'uploads/';
        if (!empty($item_number)) {
            $safe_item_number = preg_replace('/[^a-zA-Z0-9_\-]/', '', $item_number);
            $pdfUploadDir = $uploadBase . $safe_item_number . '/';
            if (!is_dir($pdfUploadDir)) {
                if (!mkdir($pdfUploadDir, 0777, true)) {
                    echo json_encode(["error" => "Failed to create directory for PDF: " . $pdfUploadDir]);
                    exit;
                }
            }
            $fname = basename($_FILES['manual_pdf']['name']);
            $dest  = $pdfUploadDir . $fname;
            if (move_uploaded_file($_FILES['manual_pdf']['tmp_name'], $dest)) {
                $manual_pdf = $dest;
            } else {
                echo json_encode(["error" => "Failed to upload manual PDF"]);
                exit;
            }
        } else {
            echo json_encode(["error" => "Item number is missing for PDF upload."]);
            exit;
        }
    }


    // อัปเดตข้อมูลสินค้า
    $query = "UPDATE products SET
        product_name = '$product_name',
        item_number = '$item_number',
        Product_content_en = '$Product_content_en',
        category = '$category',
        category_detail = '$category_detail',
        image_path = '$image',
        product_func_image = '$product_func_image',
        product_function = '$product_function',
        youtube_links = '$youtube_links',
        manual_pdf = '$manual_pdf'
        WHERE product_id = '$product_id'";

    if (mysqli_query($ierp, $query)) {
        // === สร้าง RefID หากยังไม่มี ===
        $uniqid = !empty($RefID_img) ? $RefID_img : uniqid('ref_', true);

        // === ลบรูป detail image ที่ถูกเลือกให้ลบ ===
        if (!empty($_POST['remove_detail_img_ids']) && is_array($_POST['remove_detail_img_ids'])) {
            foreach ($_POST['remove_detail_img_ids'] as $id) {
                $id = intval($id); // ป้องกัน SQL injection
                // ดึง path ของรูปก่อนจะลบ
                $imgResult = mysqli_query($ierp, "SELECT detail_img_product FROM detail_img_product WHERE detail_img_id = $id");
                if ($imgRow = mysqli_fetch_assoc($imgResult)) {
                    $imgPath = $imgRow['detail_img_product'];
                    if (file_exists($imgPath)) {
                        unlink($imgPath); // ลบไฟล์ออกจาก server
                    }
                }

                // ลบ record ในฐานข้อมูล
                mysqli_query($ierp, "DELETE FROM detail_img_product WHERE detail_img_id = $id");
            }
        }

        // === เพิ่มรูป detail image ===
        if (isset($_FILES['image_details']) && is_array($_FILES['image_details']['error'])) {
            $uploadDir = 'uploads/details/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['image_details']['error'] as $key => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $imageName = basename($_FILES['image_details']['name'][$key]);
                    $targetPath = $uploadDir . $imageName;

                    if (move_uploaded_file($_FILES['image_details']['tmp_name'][$key], $targetPath)) {
                        $insertDetailQuery = "INSERT INTO detail_img_product (detail_RefID_img, detail_product_id, detail_img_product)
                                          VALUES ('$uniqid', '$product_id', '$targetPath')";
                        mysqli_query($ierp, $insertDetailQuery);
                    }
                }
            }
        }

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update product"]);
    }
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_image_category_details') {
    header('Content-Type: application/json');

    $category = mysqli_real_escape_string($ierp, $_POST['category']);
    $category_detail = mysqli_real_escape_string($ierp, $_POST['category_details']);
    $image_category_details_path = '';

    if (isset($_FILES['image_category_details']) && $_FILES['image_category_details']['error'] === UPLOAD_ERR_OK) {
        $safe_category_detail = preg_replace('/[^a-zA-Z0-9_\-]/', '', $category_detail);
        $subUploadDir = 'Category-IMAG-' . $safe_category_detail . '/';
        $uploadDir = 'uploads/' . $subUploadDir;

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                echo json_encode(["success" => false, "message" => "Failed to create directory: " . $uploadDir]);
                exit;
            }
        }

        $image_name = basename($_FILES['image_category_details']['name']);
        $targetPath = $uploadDir . $image_name;

        if (move_uploaded_file($_FILES['image_category_details']['tmp_name'], $targetPath)) {
            $image_category_details_path = $targetPath;
        } else {
            echo json_encode(["success" => false, "message" => "Failed to upload main image_category_details"]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "No image uploaded or upload error."]);
        exit;
    }

    $stmt = mysqli_prepare($ierp, "UPDATE products SET image_category_detail_grouping = ? WHERE category_detail = ?");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $image_category_details_path, $category_detail);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(["success" => true, "message" => "Product image updated successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "No product found with the specified category detail, or image is already the same."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Failed to execute update query: " . mysqli_error($ierp)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare statement: " . mysqli_error($ierp)]);
    }
    exit;
}



// ... (การเชื่อมต่อฐานข้อมูลของคุณ)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_product') {

    // รับค่าจาก AJAX
    $product_name       = isset($_POST['product_name'])       ? mysqli_real_escape_string($ierp, $_POST['product_name'])       : null;
    $item_number        = isset($_POST['item_number'])        ? mysqli_real_escape_string($ierp, $_POST['item_number'])        : null;
    $status             = isset($_POST['status'])             ? mysqli_real_escape_string($ierp, $_POST['status'])             : null;
    $category           = isset($_POST['category'])           ? mysqli_real_escape_string($ierp, $_POST['category'])           : null;
    $category_detail    = isset($_POST['category_detail'])    ? mysqli_real_escape_string($ierp, $_POST['category_detail'])    : null;
    $Product_content_en    = isset($_POST['Product_content_en'])    ? mysqli_real_escape_string($ierp, $_POST['Product_content_en'])    : null;
    $Product_content_th    = isset($_POST['Product_content_th'])    ? mysqli_real_escape_string($ierp, $_POST['Product_content_th'])    : null;
    $product_function   = isset($_POST['product_function'])   ? mysqli_real_escape_string($ierp, $_POST['product_function'])   : null;
    $car_model_input    = isset($_POST['car_model_input'])    ? mysqli_real_escape_string($ierp, $_POST['car_model_input'])    : null;
    $car_brand_input    = isset($_POST['car_brand_input'])    ? mysqli_real_escape_string($ierp, $_POST['car_brand_input'])    : null;

    $youtube_links = null;
    if (!empty($_POST['youtube_links']) && is_array($_POST['youtube_links'])) {
        $cleanLinks = [];
        foreach ($_POST['youtube_links'] as $link) {
            $link = trim($link);
            if ($link !== "") {
                $cleanLinks[] = mysqli_real_escape_string($ierp, $link);
            }
        }
        if (count($cleanLinks) > 0) {
            $youtube_links = implode(',', $cleanLinks);
        }
    }

    $image = null;
    $product_func_image = [];

    if (empty($product_func_image)) {
        $product_func_image = null;
    }
    $image_details_paths = [];
    $car_image_upload = [];
    $car_image_upload_brand = [];
    $uniqid = strrev(uniqid());
    $manual_pdf = null;
    $ProductSheet_pdf = null;

    // ตรวจสอบการอัปโหลดไฟล์หลัก
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $targetPath;
        } else {
            echo json_encode(["error" => "Failed to upload main image"]);
            exit;
        }
    }

    if (isset($_FILES['product_func_image']) && is_array($_FILES['product_func_image']['tmp_name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['product_func_image']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['product_func_image']['error'][$i] === UPLOAD_ERR_OK) {
                $imageName = basename($_FILES['product_func_image']['name'][$i]);
                $targetPath = $uploadDir . $imageName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $product_func_image[] = $targetPath;
                }
            }
        }

        // แปลง array เป็น string แยกด้วยคอมมา
        $product_func_image = implode(',', $product_func_image);
    }


    // ตรวจสอบการอัปโหลดไฟล์ car_image_upload
    if (isset($_FILES['car_image_upload']) && $_FILES['car_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['car_image_upload']['name']);
        $targetPath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['car_image_upload']['tmp_name'], $targetPath)) {
            $car_image_upload = $targetPath;
        } else {
            echo json_encode(["error" => "Failed to upload function image"]);
            exit;
        }
    }

    if (isset($_FILES['car_image_upload_brand']) && $_FILES['car_image_upload_brand']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['car_image_upload_brand']['name']);
        $targetPath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['car_image_upload_brand']['tmp_name'], $targetPath)) {
            $car_image_upload_brand = $targetPath;
        } else {
            echo json_encode(["error" => "Failed to upload function image"]);
            exit;
        }
    }

    if (isset($_FILES['manual_pdf']) && $_FILES['manual_pdf']['error'] === UPLOAD_ERR_OK) {
        // กำหนด base upload directory
        $uploadBase = 'uploads/';

        // สร้างชื่อโฟลเดอร์สำหรับ PDF โดยใช้ $item_number
        // ตรวจสอบให้แน่ใจว่า $item_number มีค่าก่อนใช้งาน
        if (!empty($item_number)) {
            // ทำความสะอาด $item_number เพื่อให้เป็นชื่อโฟลเดอร์ที่ปลอดภัย
            $safe_item_number = preg_replace('/[^a-zA-Z0-9_\-]/', '', $item_number); // อนุญาตเฉพาะตัวอักษร, ตัวเลข, ขีดเส้นใต้, ขีดกลาง

            $pdfUploadDir = $uploadBase . $safe_item_number . '/';

            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!is_dir($pdfUploadDir)) {
                if (!mkdir($pdfUploadDir, 0777, true)) {
                    // หากสร้างโฟลเดอร์ไม่สำเร็จ
                    echo json_encode(["error" => "Failed to create directory for PDF: " . $pdfUploadDir]);
                    exit;
                }
            }

            $fname = basename($_FILES['manual_pdf']['name']);
            $dest  = $pdfUploadDir . $fname; // กำหนดปลายทางไฟล์ PDF ภายในโฟลเดอร์ใหม่

            if (move_uploaded_file($_FILES['manual_pdf']['tmp_name'], $dest)) {
                $manual_pdf = $dest; // บันทึกเส้นทางเต็มของไฟล์ PDF
            } else {
                echo json_encode(["error" => "Failed to upload manual PDF"]);
                exit;
            }
        } else {
            echo json_encode(["error" => "Item number is missing for PDF upload."]);
            exit;
        }
    }


    if (isset($_FILES['ProductSheet_pdf']) && $_FILES['ProductSheet_pdf']['error'] === UPLOAD_ERR_OK) {
        $uploadBase = 'uploads/';
        if (!empty($item_number)) {
            $safe_item_number = preg_replace('/[^a-zA-Z0-9_\-]/', '', $item_number);

            $pdfUploadDir = $uploadBase . 'ProductSheet' . $safe_item_number . '/';

            if (!is_dir($pdfUploadDir)) {
                if (!mkdir($pdfUploadDir, 0777, true)) {
                    echo json_encode(["error" => "Failed to create directory for PDF: " . $pdfUploadDir]);
                    exit;
                }
            }
            $fname = basename($_FILES['ProductSheet_pdf']['name']);
            $dest  = $pdfUploadDir . $fname;

            if (move_uploaded_file($_FILES['ProductSheet_pdf']['tmp_name'], $dest)) {
                $ProductSheet_pdf = $dest;
            } else {
                echo json_encode(["error" => "Failed to upload ProductSheet PDF"]);
                exit;
            }
        } else {
            echo json_encode(["error" => "Item number is missing for PDF upload."]);
            exit;
        }
    }

    // เพิ่มข้อมูลสินค้าหลัก
    $query = "INSERT INTO products (RefID_img, product_name, item_number, status, category, image_path, category_detail, Product_content_en, Product_content_th,  product_function, product_func_image , car_model_input , car_brand_input , car_image_upload , car_image_upload_brand ,manual_pdf, youtube_links ,ProductSheet_pdf)
              VALUES ( '$uniqid', '$product_name', '$item_number', '$status', '$category', '$image', '$category_detail', '$Product_content_en','$Product_content_th',  '$product_function', '$product_func_image' , '$car_model_input' , '$car_brand_input' , '$car_image_upload' , '$car_image_upload_brand' ,'$manual_pdf' ,'$youtube_links', '$ProductSheet_pdf')";

    if (mysqli_query($ierp, $query)) {
        // ดึง product_id ของสินค้าที่เพิ่งเพิ่ม
        $product_id = mysqli_insert_id($ierp);

        // ตรวจสอบและบันทึกรูปภาพ detail หลายรูป
        if (isset($_FILES['image_details']) && is_array($_FILES['image_details']['error'])) {
            $uploadDir = 'uploads/details/'; // โฟลเดอร์สำหรับเก็บ detail images
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // สร้างโฟลเดอร์หากยังไม่มี
            }

            foreach ($_FILES['image_details']['error'] as $key => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $imageName = basename($_FILES['image_details']['name'][$key]);
                    $targetPath = $uploadDir . $imageName;

                    if (move_uploaded_file($_FILES['image_details']['tmp_name'][$key], $targetPath)) {
                        $insertDetailQuery = "INSERT INTO detail_img_product (detail_RefID_img, detail_product_id, 	detail_img_product)
                                              VALUES ('$uniqid', '$product_id', '$targetPath')";
                        mysqli_query($ierp, $insertDetailQuery); // ไม่ต้องตรวจสอบ error ตรงนี้ก็ได้ หรือจะทำ log ก็ได้
                    }
                }
            }
        }
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to add product"]);
    }
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    $product_id = mysqli_real_escape_string($ierp, $_POST['product_id']);

    // ดึง path รูปก่อนลบ (หากต้องการลบไฟล์จากโฟลเดอร์จริง)
    $query_img = mysqli_query($ierp, "SELECT image_path, product_func_image FROM products WHERE product_id = '$product_id'");
    $img = mysqli_fetch_assoc($query_img);
    if ($img) {
        if (!empty($img['image_path']) && file_exists($img['image_path'])) {
            unlink($img['image_path']); // ลบรูปหลัก
        }
        if (!empty($img['product_func_image']) && file_exists($img['product_func_image'])) {
            unlink($img['product_func_image']); // ลบรูป function
        }
    }

    // ลบ detail images
    mysqli_query($ierp, "DELETE FROM detail_img_product WHERE detail_product_id = '$product_id'");

    // ลบข้อมูลสินค้า
    $deleteProduct = "DELETE FROM products WHERE product_id = '$product_id'";
    if (mysqli_query($ierp, $deleteProduct)) {
        echo json_encode(["success" => true, "message" => "ลบสินค้าสำเร็จ"]);
    } else {
        echo json_encode(["error" => "ไม่สามารถลบสินค้าได้"]);
    }
    exit;
}



$input = json_decode(file_get_contents('php://input'), true);

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($input['action']) &&
    $input['action'] === 'Update_adminProductList' &&
    isset($input['order']) &&
    is_array($input['order'])
) {
    // เตรียม statement สำหรับอัปเดต display_order
    $stmt = $ierp->prepare("UPDATE products SET display_order = ? WHERE product_id = ?");
    $stmt->bind_param('ii', $displayOrder, $productId);

    $ierp->begin_transaction();
    try {
        foreach ($input['order'] as $item) {
            $productId     = (int) $item['id'];
            $displayOrder  = (int) $item['order'];
            $stmt->execute();
        }
        $ierp->commit();
        echo 'OK';
    } catch (Exception $e) {
        $ierp->rollback();
        http_response_code(500);
        echo 'Error: ' . $e->getMessage();
    }

    $stmt->close();
    $ierp->close();
} else {
    http_response_code(400);
    echo 'Invalid request';
}





$input = json_decode(file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($input['action']) || empty($input['category'])) {
    http_response_code(400);
    exit('Missing action or category');
}

function update_order($db, $sql, $data, $category) {
    $stmt = $db->prepare($sql);
    foreach ($data as $item) {
        if (!$stmt) continue;
        $stmt->bind_param("iss", $item['order'], $category, $item['name']);
        $stmt->execute();
    }
    if ($stmt) $stmt->close();
}

switch ($input['action']) {
    case 'update_category_detail_order':
        update_order($ierp, "UPDATE products SET category_detail_display_order=? WHERE category=? AND category_detail=?", $input['Order'] ?? [], $input['category']);
        echo "อัปเดตลำดับหมวดย่อยเรียบร้อยแล้ว";
        break;

    case 'update_brand_order':
        update_order($ierp, "UPDATE products SET brand_display_order=? WHERE category=? AND car_brand_input=?", $input['brandOrder'] ?? [], $input['category']);
        echo "อัปเดตลำดับแบรนด์เรียบร้อยแล้ว";
        break;

    case 'update_model_order':
        update_order($ierp, "UPDATE products SET model_display_order=? WHERE category=? AND car_model_input=?", $input['modelOrder'] ?? [], $input['category']);
        echo "อัปเดตลำดับรุ่นรถเรียบร้อยแล้ว";
        break;

    case 'update_brand_model_order':
        update_order($ierp, "UPDATE products SET brand_display_order=? WHERE category=? AND car_brand_input=?", $input['brandOrder'] ?? [], $input['category']);
        $selectedBrand = $input['selectedBrand'] ?? '';
        $stmt = $ierp->prepare("UPDATE products SET model_display_order=? WHERE category=? AND car_brand_input=? AND car_model_input=?");
        foreach ($input['modelOrder'] ?? [] as $item) {
            if (!$stmt) continue;
            $stmt->bind_param("isss", $item['order'], $input['category'], $selectedBrand, $item['name']);
            $stmt->execute();
        }
        if ($stmt) $stmt->close();
        echo "อัปเดตลำดับแบรนด์และรุ่นรถเรียบร้อยแล้ว";
        break;

    default:
        http_response_code(400);
        echo "Invalid action";
}
$ierp->close();
