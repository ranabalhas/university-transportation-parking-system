<?php
session_start();
include "db.php";

$university_id = trim($_POST['university_id']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

$_SESSION['old'] = [

    'university_id' => $university_id,
    'email' => $email,
    'phone' => $phone,
    'username' => $username
];

/* EMPTY CHECK*/

if (
    empty($university_id) || empty($email) || empty($phone) || empty($username) || empty($password)) 
	{
     sleep(1);
    header("Location: /university_system/student/student_auth.php?error=empty");
    exit();
}

/* UNIVERSITY ID VALIDATION*/

if (!is_numeric($university_id)) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=invalid_id");
    exit();
}

/*PASSWORD MATCH*/

if ($password !== $confirm_password) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=password_mismatch");
    exit();
}

/*PASSWORD LENGTH */

if (strlen($password) < 8) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=weak_password");
    exit();
}
/*PASSWORD STRENGTH*/

if (
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password)
) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=password_rules");
    exit();
}

/* CHECK UNIVERSITY RECORD*/

$official = $conn->prepare("
SELECT *
FROM university_students
WHERE university_id=? AND university_email=?
");

$official->bind_param("is", $university_id, $email);

$official->execute();

$official_result = $official->get_result();

if ($official_result->num_rows == 0) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=student_not_found");
    exit();
}

/* CHECK DUPLICATES */

$check = $conn->prepare("
SELECT *
FROM students
WHERE university_id=? OR username=? OR email=? OR phone=?
");

$check->bind_param("isss", $university_id, $username, $email,$phone);

$check->execute();

$result = $check->get_result();

if ($user = $result->fetch_assoc()) {

    /* FULL DUPLICATE ACCOUNT */
    if (
        $user['university_id'] == $university_id && $user['username'] == $username && $user['email'] == $email)
		{
sleep(1);
        header("Location: /university_system/student/student_auth.php?error=duplicate");

    }

    /* UNIVERSITY ID EXISTS */
    elseif ($user['university_id'] == $university_id) {
sleep(1);
        header("Location: /university_system/student/student_auth.php?error=id_exists");

    }

    /* USERNAME EXISTS */
    elseif ($user['username'] == $username) {
sleep(1);
        header("Location: /university_system/student/student_auth.php?error=username_exists");

    }

    /* EMAIL EXISTS */
    elseif ($user['email'] == $email) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=email_exists");

}

elseif ($user['phone'] == $phone) {
sleep(1);
    header("Location: /university_system/student/student_auth.php?error=phone_exists");
}
    exit();
}

/*
   HASH PASSWORD */

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* GENERATE TOKEN*/

$token = bin2hex(random_bytes(32));

/* INSERT ACCOUNT*/

$stmt = $conn->prepare("
INSERT INTO students
(university_id,email,phone,username,password,verification_token)

VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
"isssss",$university_id,$email,$phone,$username,$hashed_password,$token);

if ($stmt->execute()) {
sleep(1);
    /* SEND EMAIL */
    require 'send_verification.php';

} else {

    echo "Database Error";
}
?>