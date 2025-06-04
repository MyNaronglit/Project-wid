<?php
// เชื่อมต่อฐานข้อมูล
require_once('server.php');
$db = new server();
$ierp = $db->connect_sql();

// ฟังก์ชันดึงข้อมูลสินค้า
function show_details()
{
    global $ierp;

    try {
        $sql = "SELECT product_id, name, category, description, image_path FROM products";
        $stmt = mysqli_prepare($ierp, $sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $products = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $products;
    } catch (Exception $e) {
        return ["error" => $e->getMessage()];
    }
}
?>

