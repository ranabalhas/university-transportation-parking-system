<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {

    header("Location:   /university_system/admin/admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$admin_name = $_SESSION['admin_name'];
?>