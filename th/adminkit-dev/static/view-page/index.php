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


$email = $_SESSION['email'] ?? null;
$row = null;
if ($email) {
    $stmt = $ierp->prepare("SELECT * FROM user WHERE email = ? ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();
}
$user_id = isset($row['user_id']) ? trim($row['user_id']) : '';
$firstname = isset($row['firstname']) ? trim($row['firstname']) : '';
$lastname = isset($row['lastname']) ? trim($row['lastname']) : '';

$full_name = $firstname . " " . $lastname;


$sql = "SELECT * FROM news";
$result = $ierp->query($sql);

$news_id = null;
$sql = "SELECT news_id FROM news";
$rt = $ierp->query($sql);
if ($rt->num_rows > 0) {
    $news = $rt->fetch_assoc();
    $news_id = $news['news_id'];
}



$sql = "SELECT * FROM news";

// ตรวจสอบการค้นหา
if (isset($_GET['search'])) {
    $searchTerm = '%' . $_GET['search'] . '%';
    $sql .= " WHERE news_title LIKE ? OR news_author LIKE ? OR news_categories LIKE ? OR news_tags LIKE ?";
}

// จัดเรียง
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'latest') {
        $sql .= " ORDER BY news_date DESC";
    } elseif ($_GET['sort'] === 'oldest') {
        $sql .= " ORDER BY news_date ASC";
    }
}

// Prepare and execute the query
$stmt = $ierp->prepare($sql);
if (isset($_GET['search'])) {
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}
$stmt->execute();
$result = $stmt->get_result();

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

    <link rel="canonical" href="https://demo-basic.adminkit.io/" />

    <title>Admin Pages</title>

    <link href="../css/app.css" rel="stylesheet">
    <link href="../css/addPD.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../styles.css">
    <script defer src="../script.js"></script> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Summernote CSS & JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        .preview-container img {
            width: 120px;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }
    </style>

</head>

<body>
    <div class="wrapper">
        <?php require 'dashboard.php'; ?>
        <div class="main">
            <?php require 'nav-profile.php'; ?>

            <button id="openModal-addNews">+ เพิ่มข่าวใหม่</button>

            <h2>📰 รายการข่าวทั้งหมด</h2>

            <!-- ฟอร์มค้นหาข่าว -->
            <div style="margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 10px; padding-right: 20px;">
                <input type="text" id="searchInput" placeholder="ค้นหาข่าว..." style="padding: 10px; width: 200px;">
                <button id="searchBtn" style="padding: 10px; cursor: pointer;">ค้นหา</button>
                <button id="sortLatest" style="padding: 10px; cursor: pointer;">ล่าสุด</button>
                <button id="sortOldest" style="padding: 10px; cursor: pointer;">เก่าสุด</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>หัวข้อข่าว</th>
                        <th>ผู้เขียน</th>
                        <th>หมวดหมู่</th>
                        <th>แท็ก</th>
                        <th>ภาษา</th>
                        <th>เนื้อหา</th>
                        <th>วันที่เผยแพร่</th>
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                    </tr>
                </thead>
                <tbody id="newsTableBody">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-id='{$row["news_id"]}' 
                data-title='" . htmlspecialchars($row["news_title"], ENT_QUOTES) . "' 
                data-author='" . htmlspecialchars($row["news_author"], ENT_QUOTES) . "' 
                data-category='" . htmlspecialchars($row["news_categories"], ENT_QUOTES) . "' 
                data-tags='" . htmlspecialchars($row["news_tags"], ENT_QUOTES) . "'
                news-date='" . htmlspecialchars($row["news_date"], ENT_QUOTES) . "'>";
                            // news-image='" . htmlspecialchars($row["news_image"], ENT_QUOTES) . "'>";
                            // news-language='" . htmlspecialchars($row["news_language"], ENT_QUOTES) . "'>

                            echo "<td>" . htmlspecialchars($row["news_title"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["news_author"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["news_categories"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["news_tags"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["news_language"]) . "</td>";
                            echo "<td class='news-content'>" . htmlspecialchars_decode($row["news_content"]) . "</td>";
                            echo "<td class='news-date'>" . htmlspecialchars($row["news_date"]) . "</td>";
                            echo "<td><button class='edit-btn' data-id='{$row["news_id"]}'>แก้ไข</button></td>";
                            echo "<td><button class='deleteBtn' data-id='{$row["news_id"]}' style='background-color: #f44336; color: white; border: none; padding: 10px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; cursor: pointer;'>ลบ</button></td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center; color: #999;'>ไม่มีข้อมูลข่าว</td></tr>";
                    }
                    ?>
                </tbody>
            </table>



            <div id="newsModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h1 id="modalTitle">ฟอร์มข่าว</h1>
                    <form id="newsForm">
                        <input type="hidden" id="news_id">
                        <input type="hidden" id="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" id="user_name" value="<?php echo $full_name; ?>">

                        <div class="country-container">
                            <input type="radio" id="usa" name="country" value="USA">
                            <label for="usa" class="country-label">
                                <img src="https://flagcdn.com/w320/us.png" alt="USA">
                                <span class="country-name">USA</span>
                            </label>

                            <input type="radio" id="th" name="country" value="TH">
                            <label for="th" class="country-label">
                                <img src="https://flagcdn.com/w320/th.png" alt="Thailand">
                                <span class="country-name">Thailand</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="title">หัวข้อข่าว</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="content">เนื้อหาข่าว</label>
                            <textarea id="content" rows="5" name="content"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">อัปโหลดรูปภาพ</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="image_detail">อัปโหลดรูปรายละเอียด (เลือกได้หลายรูป)</label>
                            <input type="file" id="image_detail" name="image_detail[]" accept="image/*" multiple>
                            <div id="image_detail_preview" class="preview-container mt-2 flex flex-wrap gap-2"></div>
                        </div>


                        <div class="sidebar">
                            <div>
                                <label>หมวดหมู่</label>
                                <select id="category">
                                    <option value="general">ข่าวทั่วไป</option>
                                    <option value="sport">ข่าวกีฬา</option>
                                    <option value="social">ข่าวบันเทิง</option>
                                </select>
                            </div>
                            <div>
                                <label>แท็ก</label>
                                <input type="text" id="tags" name="tags" placeholder="ใส่แท็ก...">
                            </div>
                        </div>
                        <button type="submit" id="publishBtn">เผยแพร่ข่าว</button>
                        <button type="button" id="updateBtn" style="display: none;">อัพเดตข่าว</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

    <script src="../js/app.js"></script>
    <script src="../js-controller/add_new.js"></script>
<script>
    window.imageFiles = []; // กำหนดเป็น global บน window
    const previewContainer = document.getElementById('image_detail_preview');
    const fileInput = document.getElementById('image_detail');

    fileInput.addEventListener('change', function (event) {
        const newFiles = Array.from(event.target.files);

        newFiles.forEach(file => {
            if (!window.imageFiles.some(f => f.name === file.name && f.size === file.size)) {
                window.imageFiles.push(file);

                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = "w-[120px] h-auto rounded-lg object-cover shadow";
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });

        fileInput.value = '';
    });
</script>


    <script>
        document.getElementById("searchBtn").addEventListener("click", function() {
            const searchTerm = document.getElementById("searchInput").value.trim().toLowerCase();

            const rows = document.querySelectorAll("#newsTableBody tr");
            rows.forEach(function(row) {
                const title = row.querySelector("td:nth-child(1)").textContent.toLowerCase();
                const author = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                const category = row.querySelector("td:nth-child(3)").textContent.toLowerCase();
                const tags = row.querySelector("td:nth-child(4)").textContent.toLowerCase();

                if (title.includes(searchTerm) || author.includes(searchTerm) || category.includes(searchTerm) || tags.includes(searchTerm)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

        document.getElementById("sortLatest").addEventListener("click", function() {
            sortNews("latest");
        });

        document.getElementById("sortOldest").addEventListener("click", function() {
            sortNews("oldest");
        });

        function sortNews(order) {
            const rows = Array.from(document.querySelectorAll("#newsTableBody tr"));
            rows.sort(function(a, b) {
                const dateA = new Date(a.querySelector(".news-date").textContent);
                const dateB = new Date(b.querySelector(".news-date").textContent);

                if (order === "latest") {
                    return dateB - dateA; // ล่าสุด: เรียงจากวันที่ล่าสุดไปหาก่อน
                } else {
                    return dateA - dateB; // เก่าสุด: เรียงจากวันที่เก่าก่อน
                }
            });

            // Reorder rows in the table
            const tbody = document.getElementById("newsTableBody");
            rows.forEach(function(row) {
                tbody.appendChild(row);
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#content').summernote({
                placeholder: 'พิมพ์เนื้อหาข่าวที่นี่...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['forecolor', 'backcolor']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>



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