<?php
// functions.php
// ===========================
// ชุดฟังก์ชันช่วยเหลือทั่วไป
// ===========================

/**
 * สร้าง CSRF Token และบันทึกลง SESSION
 * คืนค่า Token เพื่อเอาไปฝังในฟอร์ม HTML
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        // สร้าง random bytes (32 bytes) แล้วแปลงเป็น hex
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * ตรวจสอบ CSRF Token จากฟอร์ม
 * @param string $token ที่มาจาก $_POST['csrf_token']
 * @return bool คืนค่า true ถ้า token ถูกต้อง
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !$token) {
        return false;
    }
    // ใช้ hash_equals เพื่อป้องกัน timing attack
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * ฟังก์ชัน sanitize ข้อมูล input เพื่อป้องกัน XSS
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
