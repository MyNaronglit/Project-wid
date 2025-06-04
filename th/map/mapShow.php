<?php
require_once('../php-backend/server.php');
$db = new server();
$ierp = $db->connect_sql();

$sql = "SELECT map_name, map_latitude, map_longitude, map_description FROM stores";
$result = $ierp->query($sql);

$locations = array();
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $locations[] = array(
      'map_name' => $row['map_name'],
      'lat' => floatval($row['map_latitude']),
      'lng' => floatval($row['map_longitude']),
      'map_description' => $row['map_description']
    );
  }
}
$json_locations = json_encode($locations);
?>
<!DOCTYPE html>
<html>

<head>
  <title>Wichien Dynamic Industry</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-150x150.png" sizes="32x32" />
  <link rel="icon" href="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/cropped-WDI_siteicon_512-300x300.png" sizes="192x192" />

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <!-- Leaflet Routing -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

  <!-- Geocoder -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  <link rel="stylesheet" href="\wdi\www.wdi.co.th\wp-content\themes\wdi\css\bootstrap.min.css" type="text/css" media="all" />
  <link rel="stylesheet" href="\wdi\www.wdi.co.th\wp-content\themes\wdi\style.css" type="text/css" media="all" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Barlow', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
    }

    #layout-container {
      display: flex;
      height: 100vh;
    }

    #shop-sidebar {
      width: 30%;
      max-width: 400px;
      max-width: 500px;
      padding: 20px;
      overflow-y: auto;
      background-color: #ffffff;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
      border-right: 1px solid #e0e0e0;
    }

    #map-area {
      flex: 1;
    }

    .shop-title {
      font-size: 1.5em;
      margin-bottom: 15px;
      color: #333;
    }

    .search-input {
      width: 100%;
      padding: 10px 12px;
      font-size: 1em;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    .shop-card {
      background-color: #fafafa;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 15px;
      transition: all 0.2s ease;
    }

    .shop-card:hover {
      background-color: #f0f8ff;
      border-color: #aaa;
    }

    .shop-card strong {
      font-size: 1.1em;
      color: #222;
    }

    .shop-card p {
      margin: 6px 0;
      color: #555;
      font-size: 0.95em;
    }

    .shop-card .btn {
      margin-top: 10px;
      margin-right: 8px;
      padding: 6px 12px;
      font-size: 0.9em;
      border-radius: 5px;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
      color: white;
    }

    .btn-outline-primary {
      background-color: white;
      border: 1px solid #007bff;
      color: #007bff;
    }

    .btn-primary:hover,
    .btn-outline-primary:hover {
      opacity: 0.9;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      #layout-container {
        flex-direction: column;
      }

      #shop-sidebar {
        width: 100%;
        max-width: 100%;
        border-right: none;
        border-bottom: 1px solid #ddd;
      }
    }

    .routing-close-btn {
      position: absolute;
      top: 5px;
      right: 8px;
      background: transparent;
      border: none;
      font-size: 20px;
      font-weight: bold;
      color: #444;
      cursor: pointer;
      z-index: 1000;
    }

    .routing-close-btn:hover {
      color: red;
    }

    .tab-menu {
      display: flex;
      gap: 10px;
      margin-bottom: 10px;
    }

    .tab-menu li {
      list-style: none;
    }

    .tab-menu button {
      padding: 8px 16px;
      border: none;
      background: #eee;
      cursor: pointer;
    }

    .tab-menu button:hover {
      background: #ddd;
    }
  </style>
</head>

<body>
  <?php require '../nav-bar.php'; ?>
  <div id="main" class="site-main">
    <div class="container product-cat-container">
      <div id="layout-container">
        <div id="shop-sidebar">
          <ul class="tab-menu">
            <li><button onclick="showTab('distributor')">Information</button></li>
          </ul>
          <div id="distributor-controls">
            <h3 class="shop-title">Wichien Dynamic Industry co..,Ltd.</h3>
            <p>34/1 Mu 10 Pathumthani-Banglane Rd.., <br> Koobangluang, Lardlumkaew, <br>Pathumthani 12140 Thailand <br>(Head Office)</p>
            <br><br>
            <h2 class="shop-title">แผนที่ </h2>
            <p>Wichien Technology & Wichien Dynamic Industry</p>
          </div>
        </div>
        <div id="map-area" style="height: 600px;"></div>
      </div>
    </div>
  </div>

  <?php require '../footer-page.php'; ?>

  <script>
    const map = L.map('map-area').setView([13.7563, 100.5018], 10);

    // แผนที่พื้นฐาน
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let userLatLng = null;
    let userMarker = null;
    let routingControl = null;

    // หาตำแหน่งผู้ใช้
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          userLatLng = L.latLng(position.coords.latitude, position.coords.longitude);
          userMarker = L.marker(userLatLng, {
            title: "ตำแหน่งของฉัน (อัตโนมัติ)",
            draggable: true
          }).addTo(map);

          userMarker.bindPopup("นี่คือตำแหน่งของคุณ").openPopup();

          userMarker.on('dragend', function(evt) {
            userLatLng = evt.target.getLatLng();
          });

          map.setView(userLatLng, 13);
        },
        function() {
          console.log("ไม่สามารถดึงตำแหน่งผู้ใช้ได้");
        }
      );
    }

    // เลือกตำแหน่งบนแผนที่ด้วยคลิก
    map.on('click', function(e) {
      userLatLng = e.latlng;

      if (userMarker) {
        userMarker.setLatLng(userLatLng);
      } else {
        userMarker = L.marker(userLatLng, {
          title: "ตำแหน่งของฉัน",
          draggable: true
        }).addTo(map);

        userMarker.bindPopup("คุณเลือกตำแหน่งนี้").openPopup();

        userMarker.on('dragend', function(evt) {
          userLatLng = evt.target.getLatLng();
        });
      }

      map.setView(userLatLng, 14);
    });

    L.Control.geocoder({
      defaultMarkGeocode: true
    }).addTo(map);

    // ตำแหน่ง Distributor
    const distributorLocations = [{
        lat: 14.05472138117684,
        lng: 100.45184776584364,
        name: 'Wichien Dynamic Industry Co., Ltd.',
        description: 'โรงงานอุตสาหกรรมหลักของ Wichien'
      },
      {
        lat: 14.052325012193991,
        lng: 100.45593739405315,
        name: 'Wichien Technology & Design Center',
        description: 'ศูนย์เทคโนโลยีและออกแบบผลิตภัณฑ์'
      }
    ];

    distributorLocations.forEach(location => {
      const marker = L.marker([location.lat, location.lng]).addTo(map);
      const popupContent = `
      <strong>${location.name}</strong><br>
      ${location.description}<br>
      <button class="btn btn-sm btn-outline-primary mt-2 me-2" onclick="openGoogleMap(${location.lat}, ${location.lng})">ดูบน Google Maps</button>
      <button class="btn btn-sm btn-primary mt-2" onclick="goToDistributorGoogle(${location.lat}, ${location.lng})">นำทาง</button>
    `;
      marker.bindPopup(popupContent);
    });

    function openGoogleMap(lat, lng) {
      const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
      window.open(url, '_blank');
    }

    function goToDistributorGoogle(destLat, destLng) {
      if (!userLatLng) {
        alert("กรุณาคลิกที่แผนที่หรืออนุญาตให้เข้าถึงตำแหน่งก่อน");
        return;
      }
      const url = `https://www.google.com/maps/dir/?api=1&origin=${userLatLng.lat},${userLatLng.lng}&destination=${destLat},${destLng}`;
      window.open(url, '_blank');
    }
  </script>
</body>

</html>

<!-- function showTab(tab) {
  const storeTab = document.getElementById('store-controls');
  const distributorTab = document.getElementById('distributor-controls');

  if (tab === 'store') {
    storeTab.style.display = '';
    distributorTab.style.display = 'none';
    renderStoreList();

    distributorMarkers.forEach(m => map.removeLayer(m));
    distributorMarkers = [];
  } else if (tab === 'distributor') {
    storeTab.style.display = 'none';
    distributorTab.style.display = '';
    storeListDiv.innerHTML = '';

    storeMarkers.forEach(m => map.removeLayer(m));
    storeMarkers = [];

    distributorMarkers.forEach(m => map.removeLayer(m));
    distributorMarkers = [];

    distributorLocations.forEach(loc => {
      const marker = L.marker([loc.lat, loc.lng])
        .addTo(map)
        .bindPopup(`
          <strong>${loc.name}</strong><br>
          <button class="btn btn-sm btn-primary mt-2" onclick="goToStore(${loc.lat}, ${loc.lng})">นำทางบนแผนที่</button>
          <button class="btn btn-sm btn-outline-secondary mt-2" onclick="goToDistributorGoogle(${loc.lat}, ${loc.lng})">Google Maps</button>
        `);
      distributorMarkers.push(marker);
    });

    map.setView([14.0535, 100.4538], 16);
  }
} -->