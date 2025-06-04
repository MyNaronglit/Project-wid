<?php
session_start();
include('php-backend/server.php'); // ไฟล์เชื่อมต่อ Database
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Wichien Dynamic Industry</title>
    <meta name='robots' content='noindex,follow' />


    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/css/settings.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/wdi/www.wdi.co.th/wp-content/themes/wdi/style.css" type="text/css" media="all" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/wdi/www.wdi.co.th/wp-includes/js/jquery/jquery-migrate.min.js"></script>
    <script src="/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>
    <script src="/wdi/www.wdi.co.th/wp-content/plugins/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" sizes="192x192" />
    
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/login.css" />
    <style>
        body {
            height: 100vh;
            /* ครอบคลุมทั้งความสูงของหน้าจอ */
            margin: 0;
            /* ลบขอบเขตด้านข้าง */
            display: contents;
            justify-content: center;
            align-items: center;
        }

        .d-flex {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* ใช้ความสูงทั้งหมดของหน้าจอ */
            background-image: url('/wdi/www.wdi.co.th/th/css/Amodern.webp');
        }

        .custom-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            justify-content: center;
            background-color: aliceblue;

        }

        .custom-login-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .custom-form-label {
            font-weight: 600;
        }

        .custom-btn-primary {
            background-color: #007bff;
            border: none;
            transition: 0.3s;
        }

        .custom-btn-primary:hover {
            background-color: #0056b3;
        }

        .custom-btn-signup {
            border: 1px solid #007bff;
            color: #007bff;
        }

        .custom-btn-signup:hover {
            background-color: #007bff;
            color: white;
        }

        .custom-forgot-password {
            color: #007bff;
        }

        .custom-forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <?php require 'nav-bar.php'; ?>

    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card custom-card" style="max-width: 400px; width: 100%;">
            <h3 class="text-center custom-login-title"><i class="fas fa-user"></i> Login</h3>
            <h4>User : admit@gaa.aa</h4>
            <h4>Pass : a123</h4>
            <form method="POST" action="php-backend/login_db.php">
                <div class="mb-3">
                    <label for="email" class="custom-form-label">Email</label>
                    <input type="text" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="custom-form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary w-100 mb-2 custom-btn-primary">Login</button>
                <a href="register.php" class="btn btn-outline-primary w-100 mb-2 custom-btn-signup">Sign up</a>
            </form>
            <div class="text-center mt-3">
                <a href="forgot-password.php" class="custom-forgot-password">Forgot your password?</a>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <div class="container">
            <p>Test © 2025 <a href="#">WICHIEN DYNAMIC</a> Co.,LTD.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');

        if (error) {
            let errorMessage = 'เกิดข้อผิดพลาดที่ไม่คาดคิด กรุณาลองใหม่อีกครั้ง'; // ข้อความเริ่มต้น

            switch (error) {
                case 'wrong_password':
                    errorMessage = 'รหัสผ่านไม่ถูกต้อง!';
                    break;
                case 'user_not_found_or_invalid':
                    errorMessage = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง.';
                    break;
                case 'db_connect_failed':
                    errorMessage = 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาลองใหม่อีกครั้งในภายหลัง.';
                    break;
                case 'internal_server_error':
                    errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้งในภายหลัง.';
                    break;
            }

            Swal.fire({
                icon: 'error',
                title: 'เข้าสู่ระบบไม่สำเร็จ',
                text: errorMessage,
                confirmButtonText: 'ตกลง'
            });

            // ลบ query parameter 'error' ออกจาก URL
            history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>

</html>