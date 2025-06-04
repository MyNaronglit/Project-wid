<?php
session_start();
require_once('server.php');

$db = new server();
$ierp = $db->connect_sql();

// ✅ ฟังก์ชันลบผู้ใช้
function deleteUser($userId, $ierp) {
    if (!$userId || !is_numeric($userId)) {
        return json_encode(["status" => "error", "message" => "Invalid user ID."]);
    }

    // ตรวจสอบก่อนว่ามี User นี้อยู่จริงหรือไม่
  
    // ถ้ามีอยู่จริง ให้ทำการลบ
    $stmt = $ierp->prepare("DELETE FROM user WHERE user_id = $userId");


    
    if ($stmt->execute()) {
        $response = ["status" => "success", "message" => "User deleted successfully."];
    } else {
        $response = ["status" => "error", "message" => "Error deleting user."];
    }

    $stmt->close();
    return json_encode($response);
}


// ✅ ฟังก์ชันอัปเดตบทบาทของผู้ใช้
function updateUserRole($email, $newRole, $ierp) {
    if (empty($email) || empty($newRole)) {
        return json_encode(["status" => "error", "message" => "Email and role are required."]);
    }

    $stmt = $ierp->prepare("UPDATE user SET role = ? WHERE email = ?");
    if (!$stmt) {
        return json_encode(["status" => "error", "message" => "Error preparing the SQL statement."]);
    }

    $stmt->bind_param("ss", $newRole, $email);
    $response = $stmt->execute() ? 
                ["status" => "success", "message" => "Role updated successfully."] :
                ["status" => "error", "message" => "Error updating role."];

    $stmt->close();
    return json_encode($response);
}

// ✅ ฟังก์ชันอัปเดตโปรไฟล์ผู้ใช้
function updateUserProfile($ierp, $formData, $imagePath = null) {
    // สร้าง SQL query ขึ้นอยู่กับว่ามีการอัปโหลดภาพหรือไม่
    $sql = $imagePath ? 
        "UPDATE user SET firstName=?, lastName=?, nationality=?, birthdate=?, phonenumber=?, country=?, image=? WHERE email=?" :
        "UPDATE user SET firstName=?, lastName=?, nationality=?, birthdate=?, phonenumber=?, country=? WHERE email=?";

    // เตรียม statement
    $stmt = $ierp->prepare($sql);
    if (!$stmt) {
        return json_encode(["status" => "error", "message" => "Error preparing the SQL statement."]);
    }

    // Bind parameters
    if ($imagePath) {
        $stmt->bind_param("ssssssss", 
            $formData['firstName'], 
            $formData['lastName'], 
            $formData['nationality'], 
            $formData['birthdate'], 
            $formData['phoneNumber'], 
            $formData['country'], 
            $imagePath, 
            $formData['email']
        );
    } else {
        $stmt->bind_param("sssssss", 
            $formData['firstName'], 
            $formData['lastName'], 
            $formData['nationality'], 
            $formData['birthdate'], 
            $formData['phoneNumber'], 
            $formData['country'], 
            $formData['email']
        );
    }

    // Execute query
    $updateResult = $stmt->execute();

    // Get user role after update
    $sqlRole = "SELECT role FROM user WHERE email = ?";
    $stmtRole = $ierp->prepare($sqlRole);
    $stmtRole->bind_param("s", $formData['email']);
    $stmtRole->execute();
    $resultRole = $stmtRole->get_result();
    $user = $resultRole->fetch_assoc();
    $role = $user['role'] ?? 'customer';  // Set 'guest' if no role is found

    // Check the result of the update
    if ($updateResult) {
        $response = [
            "status" => "success",
            "message" => "Profile updated successfully.",
            "role" => $role // Include role in the response
        ];
    } else {
        $response = [
            "status" => "error",
            "message" => "Error updating profile."
        ];
    }

    $stmt->close();
    $stmtRole->close();

    // Return response as JSON
    return json_encode($response);
}


// ✅ จัดการคำขอ POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? null;

    switch ($action) {
        case "deleteUser":
            $userId = $_POST['user_id'] ?? null;
            echo deleteUser($userId, $ierp);
            break;

        case "update_role":
            $email = $_POST['email'] ?? null;
            $newRole = $_POST['role'] ?? null;
            echo updateUserRole($email, $newRole, $ierp);
            break;

        case "update_profile":
            $formData = [
                "email" => $_POST['email'] ?? null,
                "firstName" => $_POST['firstName'] ?? null,
                "lastName" => $_POST['lastName'] ?? null,
                "nationality" => $_POST['nationality'] ?? null,
                "birthdate" => $_POST['birthdate'] ?? null,
                "phoneNumber" => $_POST['phoneNumber'] ?? null,
                "country" => $_POST['country'] ?? null,
            ];

            $imagePath = null;
            if (!empty($_FILES['profile_image']['name'])) {
                $targetDir = "uploads/";
                $filename = time() . "_" . basename($_FILES["profile_image"]["name"]); // ป้องกันไฟล์ซ้ำ
                $imagePath = $targetDir . $filename;

                // ตรวจสอบประเภทไฟล์เพื่อความปลอดภัย
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES["profile_image"]["type"], $allowedTypes)) {
                    echo json_encode(["status" => "error", "message" => "Invalid image type."]);
                    exit;
                }

                if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $imagePath)) {
                    echo json_encode(["status" => "error", "message" => "Error uploading file."]);
                    exit;
                }
            }

            echo updateUserProfile($ierp, $formData, $imagePath);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Invalid action."]);
    }
    exit();
}
?>
