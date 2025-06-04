<?php
session_start();
require_once('server.php');

$db = new server();
$ierp = $db->connect_sql();

// ตรวจสอบว่ามี email ถูกส่งมา
if (!isset($_GET['email'])) {
    die("Invalid access");
}

$email = $_GET['email'];

// ค้นหาข้อมูลผู้ใช้
$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $ierp->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// ถ้าไม่พบข้อมูล ให้แจ้งเตือน
if (!$user) {
    die("User not found");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <!-- <link rel="preconnect" href="https://fonts.gstatic.com"> -->
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />


    <title>Forms | AdminKit Demo</title>

    <link href="css/app.css" rel="stylesheet">
    <link href="css/edit_profile.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="\newWab\MainWab-dev\assets\css\login.css" /> -->

</head>

<body>
    <div class="wrapper">
    <?php 
        // แสดง nav-profile.php เฉพาะเมื่อ role เป็น Admin หรือ Employee
        if ($user && in_array(strtolower($user['role']), ['admin', 'employee'])) {
            require 'dashboard.php';
        }
        ?>

        <div class="main">
        <?php 
        // แสดง nav-profile.php เฉพาะเมื่อ role เป็น Admin หรือ Employee
        if ($user && in_array(strtolower($user['role']), ['admin', 'employee'])) {
            require 'nav-profile.php';
        }
        ?>

            <main class="content">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">Form Edit Profile</div>
                            <div class="card-body p-4">
                                <form id="updateprofile" method="POST" enctype="multipart/form-data">
                                    <div class="text-center mb-3">
                                    <img id="profile-avatar" src="<?php echo !empty($user['image']) ? htmlspecialchars($user['image']) : 'https://shorturl.asia/8qHL1'; ?>" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Upload Product Image</label>
                                        <input type="file" class="form-control" name="profile_image" accept="image/*" value="<?php echo htmlspecialchars($user['image']); ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">First Name</label>
                                            <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Last Name</label>
                                            <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nationality</label>
                                        <select class="form-select" name="nationality" required>
                                            <option value="">Select Nationality</option>
                                            <option value="thai" <?php echo ($user['nationality'] == "thai") ? "selected" : ""; ?>>Thai</option>
                                            <option value="american" <?php echo ($user['nationality'] == "american") ? "selected" : ""; ?>>American</option>
                                            <option value="british" <?php echo ($user['nationality'] == "british") ? "selected" : ""; ?>>British</option>
                                            <option value="japanese" <?php echo ($user['nationality'] == "japanese") ? "selected" : ""; ?>>Japanese</option>
                                        </select>

                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Birthdate</label>
                                        <input type="date" class="form-control" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <div class="input-group">
                                            <input type="tel" class="form-control" name="phoneNumber" value="<?php echo htmlspecialchars($user['phonenumber']); ?>" required>
                                            <select class="custom-select" name="country" value="<?php echo htmlspecialchars($user['country']); ?>">
                                                <option value="thai">TH</option>
                                                <option value="american">US</option>
                                                <option value="british">UK</option>
                                                <option value="japanese">JP</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                        <button type="reset" class="btn btn-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>AdminKit</strong></a> - <a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>Bootstrap Admin Template</strong></a> &copy;
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Support</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Help Center</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Privacy</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Terms</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="js/app.js"></script>
	<script src="profile_manage.js"></script>

</body>

</html>