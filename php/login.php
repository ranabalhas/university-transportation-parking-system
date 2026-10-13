<?php
session_start();
include 'db.php';

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

/*  EMPTY CHECK*/

if (
    !isset($_POST['username']) || !isset($_POST['password'])) {

    header("Location: ../student/student_auth.php?error=invalid");
    exit();
}

/* GET USER*/

$stmt = $conn->prepare("
SELECT *
FROM students
WHERE username=?
");

$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();

/* USER EXISTS? */

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    /* EMAIL VERIFIED? */

    if ($user['is_verified'] == 0) {

        header("Location: ../student/student_auth.php?error=verify_email");
        exit();
    }

    /* PASSWORD CHECK */

    if (password_verify($password, $user['password'])) {
		session_regenerate_id(true);

        $_SESSION['university_id'] = $user['university_id'];

        $_SESSION['username'] = $user['username'];
		sleep(1);

        header("Location: /university_system/student/student_portal.php");

        exit();

    } else {

        header("Location: ../student/student_auth.php?error=invalid");
        exit();
    }

} else {

    header("Location: ../student/student_auth.php?error=nouser");
    exit();
}
?>