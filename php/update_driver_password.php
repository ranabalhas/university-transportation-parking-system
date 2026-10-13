<?php
session_start();
include 'db.php';

if (!isset($_SESSION['driver_id'])) {
    echo "⚠️ Login required";
    exit();
}

$driver_id = $_SESSION['driver_id'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

/* PASSWORD MATCH */
if ($new_password != $confirm_password) {

    header("Location: ../driver/change_password.php?error=mismatch");
    exit();
}

/* PASSWORD LENGTH */
if (strlen($new_password) < 8) {

    header("Location: ../driver/change_password.php?error=weak");
    exit();
}

/* PASSWORD RULES */
if (
    !preg_match('/[A-Z]/', $new_password) ||
    !preg_match('/[a-z]/', $new_password) ||
    !preg_match('/[0-9]/', $new_password)
) {

    header("Location: ../driver/change_password.php?error=rules");
    exit();
}
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
UPDATE driver 
SET password=?, first_login=0 
WHERE id=?
");

$stmt->bind_param("si", $hashed, $driver_id);

if ($stmt->execute()) {
    header("Location: /university_system/driver/driver_dashboard.php?success=password_changed");
exit();
   
} else {
    echo "⚠️ Error updating password";
}
?>