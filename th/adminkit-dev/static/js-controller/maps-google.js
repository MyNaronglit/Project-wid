$(document).on('click', '#openModal-addmap', function () {
    Swal.fire({
        title: 'Add New Map',
        html:
            '<input id="map_name" class="swal2-input" placeholder="Name">' +
            '<textarea id="map_description" class="swal2-textarea" placeholder="Description"></textarea>' +
            '<input id="map_latitude" class="swal2-input" placeholder="Latitude">' +
            '<input id="map_longitude" class="swal2-input" placeholder="Longitude">',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: () => {
            const name = $('#map_name').val().trim();
            const description = $('#map_description').val().trim();
            const latitude = $('#map_latitude').val().trim();
            const longitude = $('#map_longitude').val().trim();

            if (!name || !latitude || !longitude) {
                Swal.showValidationMessage('กรุณากรอก Name, Latitude และ Longitude');
                return false;
            }

            return { name, description, latitude, longitude };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // ส่งข้อมูลไปเพิ่มตำแหน่ง
            add_location(result.value);
        }
    });
});

function add_location(mapData) {
    const formData = new FormData();
    formData.append('action', 'add_location');
    formData.append('map_name', mapData.name);
    formData.append('map_description', mapData.description);
    formData.append('map_latitude', mapData.latitude);
    formData.append('map_longitude', mapData.longitude);

    $.ajax({
        url: 'maps-controll.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            try {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire('เพิ่มตำแหน่งสำเร็จ', '', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่สามารถเพิ่มตำแหน่งได้', 'error');
                }
            } catch (e) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถประมวลผลข้อมูลได้', 'error');
                console.error('Parse error:', e, response);
            }
        },
        error: function (err) {
            Swal.fire('ข้อผิดพลาด', 'เกิดปัญหาในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
            console.error(err);
        }
    });
}












const storeListDiv = document.getElementById('storeList');
const searchInput = document.getElementById('searchInput');

// สร้างแผนที่
const map = L.map('map').setView([13.7563, 100.5018], 10);

// พื้นฐานแผนที่
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// ตัวแปร global
let userLatLng = null;
let userMarker = null;
let routingControl = null;

// กดคลิกเพื่อเลือกตำแหน่งผู้ใช้
map.on('click', function (e) {
  userLatLng = e.latlng;

  if (userMarker) {
    userMarker.setLatLng(userLatLng);
  } else {
    userMarker = L.marker(userLatLng, { title: "ตำแหน่งของฉัน", draggable: true }).addTo(map);
    userMarker.bindPopup("คุณเลือกตำแหน่งนี้").openPopup();

    userMarker.on('dragend', function (evt) {
      userLatLng = evt.target.getLatLng();
    });
  }

  map.setView(userLatLng, 14);
});

// ปุ่มค้นหาในแผนที่
L.Control.geocoder({
  defaultMarkGeocode: true
}).addTo(map);

// ฟังก์ชันแสดงรายชื่อร้านค้า
function renderStoreList(filter = '') {
  storeListDiv.innerHTML = '';
  locations
    .filter(loc => loc.map_name.toLowerCase().includes(filter.toLowerCase()))
    .forEach((location) => {
      const div = document.createElement('div');
      div.className = 'store-item mb-3 p-2 border rounded';

      div.innerHTML = `
        <strong>${location.map_name}</strong><br>
        ${location.map_description}<br>
        <button class="btn btn-sm btn-outline-primary mt-1 me-2" onclick="openInGoogleMaps(${location.map_lat}, ${location.map_lng})">ดูบน Google Maps</button>
        <button class="btn btn-sm btn-primary mt-1" onclick="routeToStore(${location.map_lat}, ${location.map_lng})">นำทาง</button>
      `;
      storeListDiv.appendChild(div);
    });
}

function openInGoogleMaps(lat, lng) {
  const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
  window.open(url, '_blank');
}

function routeToStore(destLat, destLng) {
  if (!userLatLng) {
    alert("กรุณาคลิกที่แผนที่เพื่อเลือกตำแหน่งของคุณก่อน");
    return;
  }

  if (routingControl) {
    map.removeControl(routingControl);
  }

  routingControl = L.Routing.control({
    waypoints: [
      L.latLng(userLatLng.lat, userLatLng.lng),
      L.latLng(destLat, destLng)
    ],
    routeWhileDragging: false
  }).addTo(map);
}

searchInput.addEventListener('input', () => {
  renderStoreList(searchInput.value);
});

document.addEventListener('DOMContentLoaded', () => {
  renderStoreList();

  // ปักหมุดร้านค้าบนแผนที่
  locations.forEach(location => {
    const marker = L.marker([location.map_lat, location.map_lng]).addTo(map);
    marker.bindPopup(`<strong>${location.map_name}</strong><br>${location.map_description}`);
  });
});
