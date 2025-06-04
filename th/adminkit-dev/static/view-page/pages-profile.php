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

$sql = "SELECT * FROM user WHERE role IN ('customer', 'employee')";
$result = $ierp->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$users[] = $row;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Pages</title>
	<link rel="shortcut icon" href="../img/icons/icon-48x48.png" />
	<link href="../css/app.css" rel="stylesheet">
	<style>
		/* ✅ Custom Modal Slide Up */
		.custom-modal {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 400px;
			/* ปรับขนาดตามต้องการ */
			background: white;
			box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
			border-radius: 10px;
			display: none;
			/* ซ่อน Modal ไว้ก่อน */
			z-index: 1050;
		}

		.modal-content {
			border-radius: 10px;
			overflow: hidden;
		}

		.modal-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 15px;
			border-bottom: 1px solid #ddd;
			border-radius: 10px 10px 0 0;
		}

		.modal-body {
			padding: 20px;
		}

		.text-end {
			padding: 10px 15px;
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
					<div class="mb-3">
						<h1 class="h3 d-inline align-middle">Profile</h1>
					</div>

					<div class="row">
						<!-- ✅ Profile Card -->
						<div style="width: 40%;">
							<div class="card shadow-sm rounded-lg">
								<div class="card-body text-center">
									<img id="profile-avatar" src="https://shorturl.asia/8qHL1" class="img-fluid rounded-circle mb-2" width="128" />
									<h5 id="profile-name">-</h5>
									<div class="text-muted mb-2" id="profile-role">-</div>
									<div class="text-muted mb-2" id="profile-email">-</div>
									<div class="text-muted mb-2" id="profile-phone">-</div>

									<div class="d-flex justify-content-center gap-2 mt-3">
										<a class="btn btn-primary btn-sm" onclick="openModal()">Edit Role</a>
										<a id="editProfileBtn" class="btn btn-warning btn-sm">Edit Profile</a>
										<a class="btn btn-danger btn-sm delete-user-btn" data-user-id="<?php echo $user['user_id']; ?>"href="#">Delete User</a>

									</div>
								</div>
							</div>
						</div>

						<!-- ✅ User List -->
						<div style="width: 60%;">
							<div class="card shadow-sm rounded-lg">
								<div class="card-body">
									<ul class="list-group">
										<?php foreach ($users as $user) : ?>
											<li class="list-group-item d-flex justify-content-between align-items-center user-item"
												data-user-id="<?php echo htmlspecialchars($user['user_id']); ?>"
												data-name="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>"
												data-email="<?php echo htmlspecialchars($user['email']); ?>"
												data-role="<?php echo htmlspecialchars($user['role']); ?>"
												data-phone="<?php echo htmlspecialchars($user['phonenumber']); ?>"
												data-avatar="<?php echo htmlspecialchars($user['image']); ?>">
												<div>
													<strong><?php echo htmlspecialchars($user['firstname'] . " " . $user['lastname']); ?></strong><br>
													<small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
												</div>
												<span class="badge bg-primary"><?php echo htmlspecialchars($user['role']); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>

			<!-- ✅ Modal Slide Up -->
			<div id="editRoleModal" class="custom-modal">
				<div class="modal-content">
					<div class="modal-header bg-warning text-white">
						<h5 class="modal-title">Edit User Role</h5>
						<button type="button" class="btn-close" onclick="closeModal()"></button>
					</div>
					<div class="modal-body">
						<form id="updateRoleForm">
							<div class="mb-3">
								<label class="form-label fw-bold">Select Role</label>
								<select class="form-select" id="userRole">
									<option value="admin">Admin</option>
									<option value="employee">Employee</option>
									<option value="customer">Customer</option>
								</select>
							</div>
							<div class="text-end">
								<button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
								<button type="submit" class="btn btn-warning">Update Role</button>
							</div>
						</form>
					</div>
				</div>
			</div>


			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>AdminKit</strong></a>
							</p>
						</div>
						<div class="col-6 text-end">
							<ul class="list-inline">
								<li class="list-inline-item"><a class="text-muted" href="#">Support</a></li>
								<li class="list-inline-item"><a class="text-muted" href="#">Privacy</a></li>
							</ul>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="../js/app.js"></script>
	<script src="../js-controller/product_manage.js"></script>
	
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