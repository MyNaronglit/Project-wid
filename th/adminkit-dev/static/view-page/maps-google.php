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

$sql = "SELECT map_name, map_latitude, map_longitude, map_description FROM stores";
$result = $ierp->query($sql);

$locations = array();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $locations[] = array(
      'map_name' => $row['map_name'],
      'map_lat' => floatval($row['map_latitude']),
      'map_lng' => floatval($row['map_longitude']),
      'map_description' => $row['map_description']
    );
  }
}
$json_locations = json_encode($locations);

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
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
		<!-- Leaflet CSS -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
	<!-- Leaflet Routing Machine CSS -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
	<!-- Leaflet JS -->
	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
	<!-- Leaflet Routing Machine JS -->
	<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>
	<!-- Leaflet Control Geocoder -->
	<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

</head>

<body>
	<div class="wrapper">
	<?php require 'dashboard.php'; ?>

		<div class="main">
		<?php require 'nav-profile.php'; ?>

			<main class="content">
				<div class="container-fluid p-0">

					<div class="mb-3">
						<h1 class="h3 d-inline align-middle">Google Maps</h1>
					<!-- ปุ่ม Add Map -->
				<button id="openModal-addmap" class="btn btn-primary" style="margin-bottom: 10px;">Add Map</button>
					</div>

					<div class="row">
					<div class="col-12 col-lg-6">
						<div class="card">
						<div class="card-header">
							<h5 class="card-title">List Map</h5>
							<h6 class="card-subtitle text-muted">Displays List map view.</h6>
						</div>
						<input type="text" id="searchInput" placeholder="ค้นหาร้านค้า...">
						<div id="storeList" class="mt-3"></div>
						</div>
					</div>
				

						<div class="col-12 col-lg-6">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Map</h5>
									<h6 class="card-subtitle text-muted">Displays a map views.</h6>
								</div>
								<div class="card-body">
								<div id="map" style="height: 500px;"></div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</main>

			
		</div>
	</div>

	<script src="../js/app.js"></script>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-aWrwgr64q4b3TEZwQ0lkHI4lZK-moM4&callback=initMaps" async defer></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
  	const locations = <?php echo $json_locations; ?>;
	</script>
	<script src="../js-controller/maps-google.js"></script>

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