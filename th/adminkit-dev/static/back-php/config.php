<?php
// config.php
// ==============================================================
// ตั้งค่าการเชื่อมต่อฐานข้อมูล (PDO) & ความปลอดภัยของ Session
// ==============================================================

// 1) บังคับให้เชื่อมต่อผ่าน HTTPS เท่านั้น
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    // หากไม่ใช่ HTTPS ให้ redirect ไปยัง HTTPS
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit;
}

// 2) ตั้ง HTTP Strict Transport Security (HSTS)
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

// 3) ตั้ง HTTP Security Headers อื่นๆ
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'");

// 4) ตั้งค่าการเชื่อมต่อฐานข้อมูลด้วย PDO (ตัวอย่าง MySQL)
//    ปรับค่า DSN, USERNAME, PASSWORD ให้ตรงกับระบบคุณ
define('DB_HOST', 'localhost');
define('DB_NAME', 'wdi_db');
define('DB_CHARSET', 'utf8mb4');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // PDO::ATTR_EMULATE_PREPARES => false; // ปกติจะตั้งเป็น false เพื่อใช้ Native Prepared Statements
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $pdo_options);
} catch (PDOException $e) {
    // หากเชื่อมต่อ DB ไม่สำเร็จ แสดงข้อความหรือ Log ตามต้องการ
    error_log("Database Connection Error: " . $e->getMessage());
    die('Database connection failed.');
}

// 5) ตั้งค่า Session ให้ปลอดภัยที่สุด
//    ต้องเรียกก่อน session_start()
ini_set('session.use_strict_mode', 1);          // บังคับให้ PHP ยอมใช้ session_id ที่ถูกสร้างใหม่เท่านั้น
ini_set('session.cookie_lifetime', 0);           // ปิดทันทีกับ Browser ปิด
ini_set('session.gc_maxlifetime', 3600);         // กำหนดอายุ session หน่วยเป็นวินาที (3600 = 1 ชั่วโมง)
ini_set('session.cookie_httponly', 1);           // ไม่ให้ JavaScript มองเห็น cookie
ini_set('session.cookie_secure', 1);             // ส่ง cookie เฉพาะผ่าน HTTPS เท่านั้น
ini_set('session.cookie_samesite', 'Lax');       // ลดความเสี่ยง CSRF (SameSite=Lax)

session_name('MYAPPSESSID'); // เปลี่ยนชื่อ session cookie ชื่อเดิม “PHPSESSID” เพื่อความปลอดภัยเล็กน้อย
session_start();            // เริ่มต้น session

// 6) ตรวจสอบว่า session ถูกสร้างมาจาก HTTPS จริงหรือไม่ (Optional เพิ่มเติม)
//    ถ้า session cookie ที่ส่งมาผิดประเภท ให้ทำลาย session เดิมแล้วเริ่มใหม่
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}
?>
