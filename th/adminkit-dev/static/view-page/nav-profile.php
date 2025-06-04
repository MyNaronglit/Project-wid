<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons"></script>

<nav class="navbar navbar-expand navbar-light navbar-bg">
	<a class="sidebar-toggle js-sidebar-toggle">
		<i class="hamburger align-self-center"></i>
	</a>

	<div class="navbar-collapse collapse">
		<ul class="navbar-nav navbar-align">
			<li class="nav-item dropdown">


				<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
					<i class="bi bi-gear text-dark fs-3"></i>
				</a>

				<div class="dropdown-menu dropdown-menu-end">

					<a class="dropdown-item" href="#" id="helpCenter">
						<i class="align-middle me-1" data-feather="help-circle"></i> Help Center
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="/wdi/www.wdi.co.th/th/adminkit-dev/static/back-php/logout.php">Log out</a>

				</div>
			</li>
		</ul>
	</div>
</nav>

<script>
	// เมื่อคลิกที่ Help Center
	document.getElementById('helpCenter').addEventListener('click', function(e) {
		e.preventDefault(); // ป้องกันการเปลี่ยนเส้นทาง

		Swal.fire({
			title: 'ช่องทางการติดต่อ',
			text: 'หากคุณมีคำถามหรือต้องการความช่วยเหลือ สามารถติดต่อเราได้ที่:',
			icon: 'info',
			showCancelButton: true,
			confirmButtonText: 'โทรหาเรา',
			cancelButtonText: 'ส่งอีเมล',
			showCloseButton: true,
			html: `
                <ul>
                    <li><strong>โทรศัพท์:</strong> +66 123 456 789</li>
                    <li><strong>อีเมล:</strong> IT support@example.com</li>
                    <li><strong>ไลน์:</strong> @supportline</li>
                </ul>
            `
		}).then((result) => {
			if (result.isConfirmed) {
				// เมื่อกด "โทรหาเรา"
				window.location.href = 'tel:+66123456789'; // ลิงก์ไปที่หมายเลขโทรศัพท์
			} else if (result.isDismissed) {
				// เมื่อกด "ส่งอีเมล"
				window.location.href = 'mailto:support@example.com'; // เปิดอีเมล
			}
		});
	});
</script>