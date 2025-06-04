// ✅ ฟังก์ชันเปิดโมดัล (Slide Up)
function openModal() {
    let modal = document.getElementById('editRoleModal');
    modal.style.display = "block";
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

// ✅ ฟังก์ชันปิดโมดัล (Slide Down)
function closeModal() {
    let modal = document.getElementById('editRoleModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = "none";
    }, 300);
}

// ✅ โหลดข้อมูล User ไปยัง Profile Card เมื่อกดที่รายการ
document.addEventListener("DOMContentLoaded", function() {
    let userItems = document.querySelectorAll('.user-item');
    let deleteUserBtn = document.querySelector(".delete-user-btn");
    let updateRoleForm = document.getElementById('updateRoleForm');

    userItems.forEach(item => {
        item.addEventListener('click', function() {
            let userId = this.getAttribute("data-user-id"); // ใช้ Vanilla JS
            let name = this.getAttribute("data-name");
            let email = this.getAttribute("data-email");
            let role = this.getAttribute("data-role");
            let avatar = this.getAttribute("data-avatar");
            let phone = this.getAttribute("data-phone");

            // อัปเดตข้อมูลใน Profile Card
            document.getElementById('profile-name').textContent = name;
            document.getElementById('profile-role').textContent = role;
            document.getElementById('profile-email').textContent = email;
            document.getElementById('profile-phone').textContent = phone;
            document.getElementById('profile-avatar').src = avatar;

            // อัปเดตค่าในปุ่ม Delete User
            deleteUserBtn.setAttribute("data-user-id", userId);

            // อัปเดตค่าใน Modal (Edit Role)
            document.getElementById('userRole').value = role;
            updateRoleForm.setAttribute("data-email", email);
        });
    });

    // ✅ ปรับปรุง Event Listener ของปุ่ม Delete User
    deleteUserBtn.addEventListener("click", function(e) {
        e.preventDefault();
        let userId = this.getAttribute("data-user-id"); // ดึงค่า user_id

        if (!userId) {
            alert("Please select a user first.");
            return;
        }
        
        if (confirm("Are you sure you want to delete this user?")) {
            if (!userId) {
                alert("User ID is missing.");
                return;
            }
        
            fetch("profile_manage.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=deleteUser&user_id=${encodeURIComponent(userId)}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload(); // รีเฟรชหน้า
            })
            .catch(error => {
                alert("Error deleting user.");
                console.error("Error:", error);
            });
        }
        
    });


    // ✅ ฟังก์ชันอัปเดต Role
    document.getElementById('updateRoleForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        let selectedRole = document.getElementById('userRole').value;
        let email = this.getAttribute('data-email');
    
        $.ajax({
            url: 'profile_manage.php',
            method: 'POST',
            data: { action: 'update_role', email: email, role: selectedRole },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('User role updated successfully');
                    document.getElementById('profile-role').textContent = selectedRole;
                    closeModal();
                } else {
                    alert('Error updating role');
                }
            },
            error: function() {
                alert('AJAX request failed');
            }
        });
    });
    
});

$(document).ready(function () {
    let selectedEmail = ''; // เก็บอีเมลของผู้ใช้ที่เลือก

    $('.user-item').click(function () {
        let name = $(this).data('name');
        let email = $(this).data('email');
        let role = $(this).data('role');
        let avatar = $(this).data('avatar');

        // อัปเดตข้อมูลใน Profile Card
        $('#profile-name').text(name);
        $('#profile-role').text(role);
        $('#profile-email').text(email);
        $('#profile-avatar').attr('src', avatar);

        // อัปเดต email ในตัวแปร
        selectedEmail = email;

        // เปลี่ยนลิงก์ของปุ่ม Edit Profile
        $('#editProfileBtn').attr('href', 'edit_profile.php?email=' + selectedEmail);
    });
});

document.getElementById('updateprofile').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    formData.append("action", "update_profile");

    $.ajax({
        url: 'profile_manage.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log(response); // ดูข้อมูล response ทั้งหมด
        console.log(response.role); // ดูค่าของ response.role
            if (response.status === 'success') {
                alert('Profile updated successfully');

                // ตรวจสอบ role เพื่อเปลี่ยนหน้า
                if (response.role === 'admin' || response.role === 'employee') {
                    window.location.href = 'pages-profile.php'; // ✅ Admin & Employee -> pages-profile.php
                } else if (response.role === 'customer') {
                    window.location.href = '/newWab/MainWab-dev/profile.php'; // ✅ Customer -> profile.php
                } else {
                    alert('Role not recognized, redirecting to default profile page.');
                    window.location.href = 'pages-profile.php'; // เผื่อกรณีอื่นๆ
                }
            } else {
                alert('Error updating profile');
            }
        },
        error: function() {
            alert('AJAX request failed');
        }
    });
});


