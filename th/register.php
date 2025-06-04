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

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
    <link rel='stylesheet' href='/wdi/www.wdi.co.th/wp-content/themes/wdi/css/bootstrap.min.css'>
    <link rel='stylesheet' href='/wdi/www.wdi.co.th/wp-content/themes/wdi/style.css'>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/login.css" />
    <script src='/wdi/www.wdi.co.th/wp-content/themes/wdi/js/bootstrap.js'></script>
    <style>

        .main-info {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 120px);
            padding-bottom: 50px;
        }
        .d-flex {
            display: flex;
            justify-content: center;
            
        }


        .custom-card {
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
        }

        /* ป้องกันเนื้อหาล้นจอบนหน้าจอที่เล็ก */
        @media (max-height: 700px) {
            .main-info {
                min-height: auto;
                padding-top: 70px;
                padding-bottom: 50px;
            }
        }
    </style>
</head>

<body style="background-image: url('/wdi/www.wdi.co.th/th/css/Amodern.webp');">
    <!-- ***** Header Area Start ***** -->
    <?php require 'nav-bar.php'; ?>

    <!-- ***** Header Area End ***** -->
    <div class="main-info header-text">
        <div class="container d-flex justify-content-center align-items-center" style="flex-grow: 1;">
            <div class="card custom-card" style="max-width: 500px; width: 100%;">
                <h3 class="text-center custom-login-title"><i class="fas fa-user-plus"></i> Sign Up</h3>
                <form method="POST" action="register_db.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="custom-form-label">First Name</label>
                            <input type="text" class="form-control" name="firstName" placeholder="Enter first name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="custom-form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastName" placeholder="Enter last name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="custom-form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="custom-form-label">Nationality</label>
                        <select class="form-select" name="nationality" required>
                            <option value="">Select Nationality</option>
                            <option value="thai">Thai</option>
                            <option value="american">American</option>
                            <option value="british">British</option>
                            <option value="japanese">Japanese</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="custom-form-label">Birthdate</label>
                        <input type="date" class="form-control" name="birthdate" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="custom-form-label">Phone Number</label>
                        <div class="input-group" style="position: relative;">
                            <input type="tel" class="form-control" name="phoneNumber" placeholder="Enter phone number" required>
                            <select class="custom-select" name="country" style=" top: 8px; right: 8px; width: 30px;  padding: 0; font-size: 0.7rem; z-index: 10;">
                                <option value="thai">TH</option>
                                <option value="american">US</option>
                                <option value="british">UK</option>
                                <option value="japanese">JP</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="custom-form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="custom-form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm password" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100 mb-2 custom-btn-primary">Sign Up</button>
                </form>
            </div>
        </div>
    </div>


    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Test © 2025 <a href="#">WICHIEN DYNAMIC</a>Co.,LTD.

                        <br>Design: <a href="https://templatemo.com" target="_blank" title="free CSS templates">TemplateMo</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="mna01Nw.js"></script>

    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="php-backend/register.js"></script>


</body>

</html>