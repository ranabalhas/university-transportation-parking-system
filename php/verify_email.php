<?php
session_start();
include "db.php";

$token = $_GET['token'] ?? '';

/* INVALID TOKEN */

if (empty($token)) {

    die("Invalid token");
}

/* GET USER */

$stmt = $conn->prepare("
SELECT *
FROM students
WHERE verification_token=?
");

$stmt->bind_param("s", $token);

$stmt->execute();

$result = $stmt->get_result();

/*TOKEN EXISTS?*/

if ($result->num_rows == 0) {

    die("Invalid or expired token");
}

$user = $result->fetch_assoc();

/* VERIFY ACCOUNT*/

$update = $conn->prepare("
UPDATE students
SET is_verified=1,
verification_token=NULL
WHERE university_id=?
");

$update->bind_param("i", $user['university_id']);

$update->execute();

/* AUTO LOGIN*/

$_SESSION['university_id'] = $user['university_id'];

$_SESSION['username'] = $user['username'];

/* REDIRECT TO NOTICE PAGE*/

header("Location: /university_system/student/verify_notice.php?verified=1");

exit();

?>