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
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/ui-forms.php" />

	<title>Forms | AdminKit Demo</title>

	<link href="css/app.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
	<?php require 'dashboard.php'; ?>


		<div class="main">
		<?php require 'nav-profile.php'; ?>


			<main class="content">
				<div class="container-fluid p-0">
					<h1 class="h3 mb-3">Add Product</h1>
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Product Information</h5>
								</div>
								<div class="card-body">
									<form action="AddProduct.php" method="POST" enctype="multipart/form-data">
										<?php include('errors.php'); ?>
			
										<div class="row mb-3">
											<div class="col-md-6">
												<label class="form-label">Item Number</label>
												<input type="text" class="form-control" name="item_number">
											</div>
											<div class="col-md-6">
												<label class="form-label">NameProduct</label>
												<input type="text" class="form-control" name="NameProduct">
											</div>
										</div>
			
										<div class="row mb-3">
											<div class="col-md-6">
												<label class="form-label">Price</label>
												<input type="number" class="form-control" name="price">
											</div>
											<div class="col-md-6">
												<label class="form-label">Product Quantity</label>
												<input type="number" class="form-control" name="product_quantity">
											</div>
										</div>
			
										<div class="mb-3">
											<label class="form-label">Product Description</label>
											<textarea class="form-control" rows="3" name="product_description"></textarea>
										</div>
			
										<div class="row mb-3">
											<div class="col-md-6">
												<label class="form-label">Product Category</label>
												<input type="text" class="form-control" name="product_category">
											</div>
											<div class="col-md-6">
												<label class="form-label">Product Status</label>
												<select class="form-control" name="product_status">
													<option value="available">Available</option>
													<option value="out_of_stock">Out of Stock</option>
													<option value="discontinued">Discontinued</option>
												</select>
											</div>
										</div>
			
										<!-- 🔥 ปุ่มอัปโหลดรูปภาพที่เพิ่มเข้ามา -->
										<div class="mb-3">
											<label class="form-label">Upload Product Image</label>
											<input type="file" class="form-control" name="product_image" accept="image/*">
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
				</div>
			</main>
			
			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>AdminKit</strong></a> - <a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>Bootstrap Admin Template</strong></a>								&copy;
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

	<script src="js/app.js"></script>

</body>

</html>