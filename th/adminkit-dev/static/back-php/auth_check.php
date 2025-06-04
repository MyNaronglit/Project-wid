<?php
// auth_check.php
// ================================================
// ตรวจสอบสิทธิ์ (Session) และตั้งค่า Header ป้องกัน Cache
// ================================================

require_once __DIR__ . '/../config.php'; // เรียก config เพื่อเริ่ม session และตั้งค่า Security Headers
// (ใน config.php จะมี session_start() อยู่แล้ว)

if (!isset($_SESSION['user_id'])) {
    // หากยังไม่มี session user_id → redirect ไปหน้า login
    header('Location: /wdi/www.wdi.co.th/th/adminkit-dev/static/login.php');
    exit;
}

// ป้องกัน “กด Back” แล้วย้อนกลับมาดูหน้าเดิมหลัง Logout
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0
header("Expires: 0"); // Proxies

// (Optional) ตรวจสอบอายุ session:  
$max_inactive = 3600; // หน่วยวินาที (1 ชั่วโมง)
if (isset($_SESSION['logged_in_at']) && (time() - $_SESSION['logged_in_at'] > $max_inactive)) {
    // หาก inactive เกินกำหนด → ทำลาย session แล้วบังคับไป login ใหม่
    session_unset();
    session_destroy();
    header('Location: /wdi/www.wdi.co.th/th/adminkit-dev/static/login.php?timeout=1'); 
    exit;
}
?>
