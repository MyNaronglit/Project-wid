<?php
// ต้องตั้งค่าก่อนเรียก session_start()
ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1); // เปิดเมื่อใช้ HTTPS จริง
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    // 'secure' => true, // เปิดใช้งานเมื่อใช้ HTTPS จริง
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once('server.php');

$db = new server();
$ierp = $db->connect_sql();

if (!$ierp) {
    error_log("Database connection failed: " . mysqli_connect_error());
    header('location: ../login.php?error=db_connect_failed');
    exit;
}

$errors = array();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email)) {
        array_push($errors, "Email is required");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Invalid email format");
    }

    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $email_escaped = mysqli_real_escape_string($ierp, $email);

        // เปลี่ยน 'id' เป็น 'user_id' ตามโครงสร้างจริงของตาราง user
        $query = "SELECT user_id, email, password, role FROM user WHERE email = ?";
        $stmt = mysqli_prepare($ierp, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email_escaped);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);

                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['user_id']; // ใช้ user_id แทน id
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['success'] = "You are now logged in";

                    if ($user['role'] == 'admin' || $user['role'] == 'employee') {
                        header('location: /wdi/www.wdi.co.th/th/adminkit-dev/static/view-page/index.php');
                    } else {
                        header('location: ../index.php');
                    }
                    exit;
                } else {
                    header('location: ../login.php?error=wrong_password');
                    exit;
                }
            } else {
                header('location: ../login.php?error=user_not_found_or_invalid');
                exit;
            }

            mysqli_stmt_close($stmt);
        } else {
            error_log("Failed to prepare statement: " . mysqli_error($ierp));
            header('location: ../login.php?error=internal_server_error');
            exit;
        }
    } else {
        header('location: ../login.php?error=user_not_found_or_invalid');
        exit;
    }
}
?>
