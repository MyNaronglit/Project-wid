
document.getElementById("openModal-addNews").addEventListener("click", function () {
    let modal = document.getElementById("newsModal");
    modal.style.display = "block";
    setTimeout(() => {
        modal.classList.add("show");
    }, 10);
});

// ปิดโมดัลเมื่อกดปุ่มปิด (×)
document.querySelector(".close").addEventListener("click", function () {
    closeModal();
});

// ปิดโมดัลเมื่อคลิกนอกฟอร์ม
window.addEventListener("click", function (event) {
    let modal = document.getElementById("newsModal");
    if (event.target === modal) {
        closeModal();
    }
});

// ✅ ฟังก์ชันปิดโมดัล (Slide Down)
function closeModal() {
    let modal = document.getElementById("newsModal");
    modal.classList.remove("show");
    setTimeout(() => {
        modal.style.display = "none";
    }, 300);
}

function submitNewsForm(newsId = null) {
    const newsForm = document.getElementById('newsForm');

    newsForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const userId = document.getElementById('user_id').value.trim();
        const title = document.getElementById('title').value.trim();
        const content = $('#content').summernote('code').trim();
        const username = document.getElementById('user_name').value.trim();
        const category = document.getElementById('category').value.trim();
        const tags = document.getElementById('tags').value.trim();

        const selectedCountry = document.querySelector('input[name="country"]:checked');
        const countries = selectedCountry ? selectedCountry.value : null;

        if (!countries) {
            Swal.fire('กรุณาเลือกประเทศ', '', 'warning');
            return;
        }

        const imageInput = document.getElementById('image');
        const formData = new FormData();

        formData.append('action', 'insert'); 
        formData.append('user_id', userId);
        formData.append('title', title);
        formData.append('content', content);
        formData.append('username', username);
        formData.append('category', category);
        formData.append('tags', tags);
        formData.append('countries', countries);

        if (newsId) {
            formData.append('news_id', newsId);
        }

      if (imageInput.files.length > 0) {
    formData.append('image', imageInput.files[0]);
    }

   const imageFiles = window.imageFiles || [];

    imageFiles.forEach(file => {
        formData.append('image_detail[]', file);
    }); 




        await sendToServer(formData);
    });
}


async function sendToServer(formData) {
    try {
        const response = await fetch('../back-php/manage_page.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        console.log("📌 ตอบกลับจากเซิร์ฟเวอร์:", data);

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: data.message,
                confirmButtonText: 'ตกลง'
            });
            window.location.href = "index.php";
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    } catch (error) {
        console.error('❌ Error:', error);
        Swal.fire('เกิดข้อผิดพลาดในการส่งข้อมูล', error.message, 'error');
    }
}

// โหลดฟังก์ชันเมื่อหน้าเว็บโหลดเสร็จ
document.addEventListener('DOMContentLoaded', submitNewsForm);

function highlightCountry(countryId) {
    // รีเซ็ตสถานะของ label ทั้งหมด
    const labels = document.querySelectorAll('.country-label');
    labels.forEach(label => label.classList.remove('selected'));

    // เพิ่มคลาส 'selected' ให้กับ label ที่ถูกเลือก
    const selectedLabel = document.querySelector(`label[for="${countryId}"]`);
    selectedLabel.classList.add('selected');
    
    // ให้เลือก radio button ที่เกี่ยวข้อง
    const selectedRadio = document.getElementById(countryId);
    selectedRadio.checked = true;
}

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("newsModal");
    const closeModal = document.querySelector(".modal .close");
    const publishBtn = document.getElementById("publishBtn");
    const updateBtn = document.getElementById("updateBtn");
    const newsForm = document.getElementById("newsForm");
    const modalTitle = document.getElementById("modalTitle");

    // ปุ่มเพิ่มข่าวใหม่
    document.getElementById("openModal-addNews").addEventListener("click", function () {
        // ตั้งค่า default เป็นเพิ่มข่าว
        modalTitle.textContent = "ฟอร์มเพิ่มข่าว";
        newsForm.reset();
        $('#content').summernote('code', '');

        publishBtn.style.display = "inline-block";
        updateBtn.style.display = "none";
        modal.style.display = "block";

        setTimeout(() => {
            modal.classList.add("show");
        }, 10);
    });

    // ปุ่มแก้ไขข่าว
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            const row = this.closest("tr");

            // ใส่ค่าลงฟอร์มจาก data
            document.getElementById("news_id").value = row.getAttribute("data-id") || "";
            document.getElementById("title").value = row.getAttribute("data-title") || "";
            document.getElementById("tags").value = row.getAttribute("data-tags") || "";
            document.getElementById("category").value = row.getAttribute("data-category") || "";
            $('#content').summernote('code', row.querySelector('.news-content').innerHTML || '');

            // เช็กและเลือก country
            const country = row.getAttribute("data-country") || "";
            if (country) {
                const countryRadio = document.querySelector(`input[name="country"][value="${country}"]`);
                if (countryRadio) countryRadio.checked = true;
            }

            // เปลี่ยนหัวข้อ
            modalTitle.textContent = "ฟอร์มแก้ไขข่าว";

            publishBtn.style.display = "none";
            updateBtn.style.display = "inline-block";
            modal.style.display = "block";

            setTimeout(() => {
                modal.classList.add("show");
            }, 10);
        });
    });

    // ปิด modal
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
        modal.classList.remove("show");
        publishBtn.style.display = "inline-block";
        updateBtn.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            modal.classList.remove("show");
            publishBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
        }
    });
});


document.getElementById("updateBtn").addEventListener("click", function (e) {
    e.preventDefault();

    const newsid = document.getElementById('news_id').value.trim();
    const userId = document.getElementById('user_id').value.trim();
    const userName = document.getElementById('user_name').value.trim();
    const title = document.getElementById('title').value.trim();
    const content = $('#content').summernote('code').trim();
    const category = document.getElementById('category').value.trim();
    const tags = document.getElementById('tags').value.trim();

    const selectedCountryElement = document.querySelector('input[name="country"]:checked');

    // ❗ แสดง SweetAlert2 ถ้ายังไม่ได้เลือกประเทศ
    if (!selectedCountryElement) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกประเทศ',
            text: 'คุณยังไม่ได้เลือกประเทศสำหรับข่าวนี้',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    const countries = selectedCountryElement.value;

    const imageInput = document.getElementById('image');
    const image = imageInput.files.length > 0 ? imageInput.files[0] : null;

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('news_id', newsid);
    formData.append('user_id', userId);
    formData.append('username', userName);
    formData.append('title', title);
    formData.append('content', content);
    formData.append('category', category);
    formData.append('tags', tags);
    formData.append('countries', countries);

    if (image) {
        formData.append('image', image);
    }

    console.log("FormData entries:");
    for (const entry of formData.entries()) {
        console.log(entry);
    }

    save_SaveAndAlert(formData);
});

// ใช้ querySelectorAll เพื่อจับทุกๆ ปุ่มที่มี class deleteBtn
document.querySelectorAll(".deleteBtn").forEach(button => {
    button.addEventListener("click", function (e) {
        e.preventDefault();

        // ดึง news_id จาก data-id ของปุ่ม
        const newsid = button.getAttribute('data-id');

        if (!newsid) {
            alert("ไม่พบข้อมูลข่าวที่ต้องการลบ");
            return;
        }

        // แสดงกล่องยืนยันก่อนลบ
        if (confirm("คุณต้องการลบข่าวนี้ใช่หรือไม่?")) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('news_id', newsid);

            console.log("FormData entries for delete:");
            for (const entry of formData.entries()) {
                console.log(entry);
            }

            save_SaveAndAlert(formData);
        } else {
            // ผู้ใช้ยกเลิกการลบ
            console.log("ยกเลิกการลบข่าว");
        }
    });
});

async function save_SaveAndAlert(formData) {
    try {
        $.ajax({
            url: "../back-php/manage_page.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log("Response data:", data); 

                // ถ้าเป็นข้อมูลที่ส่งมาจาก PHP ซึ่งเป็น JSON string
                const responseData = JSON.parse(data);

                // ตรวจสอบว่า response ส่งมาเป็น success หรือไม่
                if (responseData && responseData.success) {
                    alert("ระบบ : " + responseData.message);
                    location.reload();  // รีเฟรชหน้าเมื่อสำเร็จ
                } else {
                    alert("ระบบ: " + (responseData.message || 'ไม่พบรายละเอียด'));
                }
            },
            error: function (jqXHR, status, err) {
                console.error("AJAX Error:", status, err, jqXHR.responseText);
                alert("เกิดข้อผิดพลาดในการส่งข้อมูล: " + jqXHR.responseText);
            },
        });
    } catch (error) {
        console.error("Error during AJAX request:", error);
        alert("เกิดข้อผิดพลาดในการดำเนินการ!");
    }
}

document.getElementById("searchBtn").addEventListener("click", function () {
    const searchTerm = document.getElementById("searchInput").value.trim().toLowerCase();

    const rows = document.querySelectorAll("#newsTableBody tr");
    rows.forEach(function (row) {
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

document.getElementById("sortLatest").addEventListener("click", function () {
    sortNews("latest");
});

document.getElementById("sortOldest").addEventListener("click", function () {
    sortNews("oldest");
});

function sortNews(order) {
    const rows = Array.from(document.querySelectorAll("#newsTableBody tr"));
    rows.sort(function (a, b) {
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
    rows.forEach(function (row) {
        tbody.appendChild(row);
    });
}

