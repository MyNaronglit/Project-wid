<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /wdi/www.wdi.co.th/th/login.php');
    exit;
}

// ป้องกันการแคชหน้า
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
require_once('../back-php/server.php');
$db = new server();
$ierp = $db->connect_sql();

$query = "SELECT * FROM Products";
$result = mysqli_query($ierp, $query);
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

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="../img/icons/icon-48x48.png" />

	<title>Admin Pages</title>

	<link href="../css/app.css" rel="stylesheet">
	<link href="../css/addPD.css" rel="stylesheet">
	<link href="../css/page-product.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">


	<style>
		.order-list .list-group-item {
			cursor: pointer;
			transition: background 0.3s;
		}

		.order-list .list-group-item:hover {
			background: #f8f9fa;
		}

		.order-list i {
			color: #007bff;
			margin-right: 10px;
		}

		.order-details {
			padding: 20px;
			background: #f8f9fa;
			border-radius: 5px;
		}

		.branch-container {
			margin-bottom: 1.5rem;
			padding: 1rem;
		}

		.branch-title {
			display: block;
			font-weight: 600;
			font-size: 1.1rem;
			color: #333;
			margin-bottom: 0.75rem;
		}

		.branch-grid {
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 1rem;
		}

		.branch-option {
			display: flex;
			justify-content: center;
			align-items: center;
			border: 2px solid #ddd;
			border-radius: 9999px;
			padding: 0.75rem;
			background-color: white;
			cursor: pointer;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
			transition: all 0.2s ease-in-out;
		}

		.branch-option:hover {
			border-color: #3b82f6;
			box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
			transform: scale(1.02);
		}

		.branch-option input[type="radio"] {
			display: none;
		}

		.branch-option input[type="radio"]:checked+span {
			background-color: #3b82f6;
			color: white;
			padding: 0.3rem 1rem;
			border-radius: 999px;
			transition: all 0.3s;
		}

		.branch-option span {
			font-weight: 500;
			color: #444;
			transition: all 0.2s ease-in-out;
		}

		.latest-release-container {
			margin-bottom: 1.5rem;
			padding: 1rem;

		}

		.latest-title {
			display: block;
			font-weight: 600;
			font-size: 1.1rem;
			color: #333;
			margin-bottom: 0.75rem;
		}

		.latest-options {
			display: flex;
			gap: 1rem;
			justify-content: center;
			align-items: center;
		}

		.latest-option {
			display: flex;
			justify-content: center;
			align-items: center;
			border: 2px solid #ccc;
			border-radius: 9999px;
			padding: 0.5rem 1.5rem;
			background-color: white;
			cursor: pointer;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
			transition: all 0.25s ease;
			position: relative;
		}

		.latest-option:hover {
			border-color: #3b82f6;
			box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
			transform: translateY(-1px);
		}

		.latest-option input[type="radio"] {
			display: none;
		}

		.latest-option input[type="radio"]:checked+span {
			background-color: #3b82f6;
			color: white;
			padding: 0.25rem 1rem;
			border-radius: 999px;
			font-weight: 600;
			transition: all 0.3s;
		}

		.latest-option span {
			color: #444;
			font-weight: 500;
			transition: all 0.2s ease-in-out;
		}

		.country-container {
			display: flex;
			gap: 20px;
			flex-wrap: wrap;
		}

		.country-container input[type="checkbox"] {
			display: none;
		}

		.country-label {
			border: 2px solid transparent;
			padding: 10px;
			border-radius: 10px;
			display: flex;
			flex-direction: column;
			align-items: center;
			cursor: pointer;
			transition: all 0.3s ease;
		}

		input[type="checkbox"]:checked+.country-label {
			border-color: #3b82f6;
			box-shadow: 0 0 0 5px rgb(69 119 200 / 88%);
		}

		.country-label img {
			width: 100px;
			height: auto;
		}

		.country-name {
			margin-top: 5px;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<div class="wrapper">
		<?php require 'dashboard.php'; ?>


		<div class="main">
			<?php require 'nav-profile.php'; ?>


			<main class="content">
				<div class="container-fluid p-0">
					<h1 class="h3 mb-3">All Products</h1>
					<button id="openModal-addProduct" class="btn btn-primary" style="margin-bottom: 10px;">Add Product</button>
					<button id="openModal-addimgcategory-detail" class="btn btn-primary" style="margin-bottom: 10px;">Add Iamge Category detail</button>
					<div style="margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 10px; padding-right: 20px;">
						<input type="text" id="searchInput" placeholder="ค้นหาสินค้า..." style="padding: 10px; width: 200px;">
						<button id="searchBtn" style="padding: 10px; cursor: pointer;">ค้นหา</button>
						<button id="sortLatest" style="padding: 10px; cursor: pointer;">ล่าสุด</button>
						<button id="sortOldest" style="padding: 10px; cursor: pointer;">เก่าสุด</button>
					</div>
					<div class="row">
						<!-- กล่องแสดงสินค้าทั้งหมด -->
						<div class="col-md-6">
							<div class="card shadow-sm">
								<div class="card-header bg-primary text-white">
									<h5 class="mb-0"><i class="fa-solid fa-box"></i> All Products</h5>
								</div>
								<div class="card-body order-list p-0">
									<ul class="list-group list-group-flush">
										<li class="list-group-item text-center text-muted">Loading Products...</li>
									</ul>
								</div>
							</div>
						</div>

						<!-- กล่องแสดงรายละเอียดสินค้า -->
						<div class="col-md-6">
							<div class="card shadow-sm">
								<div class="card-header bg-primary text-white">
									<h5 class="mb-0"><i class="fa-solid fa-info-circle"></i> Product Details</h5>
								</div>
								<div class="card-body" id="order-details">
									<p class="text-center text-muted">Select an Product to see details.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>


		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
	<script src="../js-controller/product_manage.js"></script>
	<script src="../js/app.js"></script>
	
    <script>
        function logoutUser() {
            fetch('/logout.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                }) // ส่งคำขอไปที่ logout.php
                .then(response => {
                    // ล้างแคชและป้องกันการย้อนกลับ
                    window.location.replace('/wdi/www.wdi.co.th/th/index.php');
                })
                .catch(error => console.error('Logout error:', error));
        }
    </script>
</body>

</html>