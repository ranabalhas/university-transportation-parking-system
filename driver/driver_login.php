<?php
session_start();
include '../php/db.php';

$driver_id = $_POST['driver_id'];
$password = $_POST['password'];
if (empty($driver_id) || empty($password)) {

    header("Location: /university_system/driver/driver_login.php?error=empty");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM driver WHERE id=?");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $driver = $result->fetch_assoc();

    if (password_verify($password, $driver['password'])) {

        session_regenerate_id(true);
        $_SESSION['driver_id'] = $driver['id'];
		

        if (!empty($driver['first_login']) && $driver['first_login'] == 1) {
            header("Location: /university_system/driver/change_password.php");
            exit();
        }

        header("Location: /university_system/driver/driver_dashboard.php");
        exit();

    } else {
       header("Location: /university_system/driver/driver_login.php?error=wrong");
        exit();
    }
} else {

    header("Location: /university_system/driver/driver_login.php?error=notfound");
    exit();
}
?>