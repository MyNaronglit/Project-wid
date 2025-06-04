<?php
require_once('server.php');
$db = new server();
$ierp = $db->connect_sql(); // ตัวแปรนี้ต้องใช้ต่อด้านล่าง

if ($_POST['action'] === 'add_location') {
    $response = add_location($ierp, $_POST); // ใช้ $ierp แทน $lerp
    echo json_encode($response);
    exit;
}

function add_location($conn, $Formdata) {
    // ตรวจสอบว่าข้อมูลครบหรือไม่
    if (
        empty($Formdata['map_name']) ||
        empty($Formdata['map_description']) ||
        empty($Formdata['map_latitude']) ||
        empty($Formdata['map_longitude'])
    ) {
        return ['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'];
    }

    // เตรียมข้อมูล
    $name = $conn->real_escape_string($Formdata['map_name']);
    $description = $conn->real_escape_string($Formdata['map_description']);
    $latitude = floatval($Formdata['map_latitude']);
    $longitude = floatval($Formdata['map_longitude']);

    // SQL สำหรับเพิ่มข้อมูล
    $sql = "INSERT INTO stores (map_name, map_description, map_latitude, map_longitude)
            VALUES ('$name', '$description', $latitude, $longitude)";

    if ($conn->query($sql)) {
        return ['status' => 'success'];
    } else {
        return ['status' => 'error', 'message' => 'ไม่สามารถเพิ่มข้อมูลได้: ' . $conn->error];
    }
}



function fetch_datalocation($ierp) {
    $sql = "SELECT * FROM stores";
    $result = mysqli_query($ierp, $sql);

    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
}
