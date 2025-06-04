<?php
// เชื่อมต่อฐานข้อมูล
require_once('server.php');
$db = new server();
$ierp = $db->connect_sql();
$errors = array();

if (isset($_POST['submit'])) {
    $firstName = mysqli_real_escape_string($ierp, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($ierp, $_POST['lastName']);
    $email = mysqli_real_escape_string($ierp, $_POST['email']);
    $nationality = mysqli_real_escape_string($ierp, $_POST['nationality']);
    $birthdate = mysqli_real_escape_string($ierp, $_POST['birthdate']);
    $phoneNumber = mysqli_real_escape_string($ierp, $_POST['phoneNumber']);
    $country = mysqli_real_escape_string($ierp, $_POST['country']);
    $password = mysqli_real_escape_string($ierp, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($ierp, $_POST['confirmPassword']);

    // ตรวจสอบว่าข้อมูลถูกกรอกครบ
    if (empty($firstName)) array_push($errors, "First Name is required");
    if (empty($lastName)) array_push($errors, "Last Name is required");
    if (empty($email)) array_push($errors, "Email is required");
    if (empty($nationality)) array_push($errors, "Nationality is required");
    if (empty($birthdate)) array_push($errors, "Birthdate is required");
    if (empty($phoneNumber)) array_push($errors, "Phone Number is required");
    if (empty($country)) array_push($errors, "country is required");
    if (empty($password)) array_push($errors, "Password is required");
    if ($password != $confirmPassword) array_push($errors, "Passwords do not match");

    // เช็คว่ามี email ซ้ำหรือไม่
    $user_check_query = "SELECT * FROM user WHERE email='$email' LIMIT 1";
    $query = mysqli_query($ierp, $user_check_query);
    $user = mysqli_fetch_assoc($query);

    if ($user) { // ถ้า email มีอยู่แล้ว
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // ถ้าไม่มี error ให้เพิ่มข้อมูล
    if (count($errors) == 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // ใช้ password_hash() แทน md5()
        $sql = "INSERT INTO user (firstName, lastName, email, nationality, birthdate, phoneNumber, country, password) 
                VALUES ('$firstName', '$lastName', '$email', '$nationality', '$birthdate', '$phoneNumber', '$country', '$hashed_password')";

        if (mysqli_query($ierp, $sql)) {
            $_SESSION['email'] = $email;
            $_SESSION['success'] = "You are now registered";
            header('location: login.php');
            exit;
        } else {
            array_push($errors, "Email already exists");
            $_SESSION['error'] = "Email already exists";
            header('location: index.php');
        }
    }
}
?>
