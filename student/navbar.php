<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../php/db.php';

if (!isset($_SESSION['university_id'])) {
    header("Location: student_auth.php");
    exit();
}

$university_id = $_SESSION['university_id'];

$stmt = $conn->prepare("
SELECT *
FROM students
WHERE university_id=?
");

$stmt->bind_param("i", $university_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$username = $student['username'];
?>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* =========================
   NAVBAR
========================= */

.portal-navbar {
    felex-wrap:wrap;
    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:18px 40px;

    background:#0b2d5c;

    position:sticky;
    top:0;
    z-index:999;
}

.portal-logo {

    font-size:24px;
    font-weight:bold;
    color:white;
}

/* =========================
   MENU
========================= */

.portal-menu {

    display:flex;
    gap:20px;
    align-items:center;
}

.portal-menu a {

    color:white;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}

.portal-menu a:hover {

    color:#ffd166;
}

/* =========================
   DROPDOWN
========================= */

.dropdown {

    position:relative;
}

.dropdown-btn {

    background:#123d77;
    border:2px solid #f0b44c;

    color:white;

    padding:12px 18px;

    border-radius:12px;

    cursor:pointer;

    font-size:16px;
    font-weight:bold;
}

.dropdown-content {

    display:none;

    position:absolute;

    right:0;

    background:white;

    width:320px;

    border-radius:18px;

    overflow:hidden;

    box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

.dropdown:hover .dropdown-content {

    display:block;
}

.account-header {

    background:#0b2d5c;

    color:white;

    padding:25px;
}

.account-header h3 {

    margin:0;
    font-size:28px;
}

.account-header p {

    margin-top:10px;
    font-size:17px;
}

.dropdown-content a {

    display:block;

    padding:18px 25px;

    text-decoration:none;

    color:#0b2d5c;

    font-size:18px;

    border-bottom:1px solid #eee;

    transition:0.3s;
}

.dropdown-content a:hover {

    background:#f5f5f5;
}
/* =========================
   MOBILE NAVBAR
========================= */

@media (max-width:768px){

    .portal-navbar{

        flex-direction:column;

        align-items:center;

        padding:12px;

        gap:12px;
    }

    .portal-logo{

        font-size:20px;

        text-align:center;

        line-height:1.3;
    }

    .portal-menu{

        width:100%;

        justify-content:center;

        flex-wrap:wrap;

        gap:10px;
    }

    .portal-menu a{

        font-size:13px;

        margin:0;
    }

    .dropdown-btn{

        padding:8px 12px;

        font-size:13px;
    }

    .dropdown-content{

        width:260px;

        right:50%;

        transform:translateX(50%);
    }

    .account-header h3{

        font-size:22px;
    }

    .account-header p{

        font-size:14px;
    }

    .dropdown-content a{

        font-size:15px;

        padding:14px 18px;
    }
}


</style>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<header class="portal-navbar">

<div class="portal-logo">
🎓 University Transportation System
</div>

<div class="portal-menu">

<a href="/university_system/student/student_portal.php">
<i class="fa-solid fa-house"></i> Home
</a>

<a href="/university_system/student/find_bus.php">
<i class="fa-solid fa-bus"></i> Bus
</a>

<a href="/university_system/student/parking_subscription.php">
<i class="fa-solid fa-car-side"></i> Parking
</a>

<a href="/university_system/student/my_subscriptions.php">
<i class="fa-solid fa-clipboard-list"></i> Subscriptions
</a>
<div class="dropdown">

<button class="dropdown-btn">

👤 My Account
<i class="fa fa-angle-down"></i>

</button>

<div class="dropdown-content">

<div class="account-header">

<h3><?= $username ?></h3>

<p>ID: <?= $university_id ?></p>

</div>
<a href="/university_system/student/student_portal.php">
🏠 Home
</a>
<a href="/university_system/student/profile.php">
🔧 Profile Settings
</a>

<a href="/university_system/student/bus_dashboard.php">
🚌 Bus Transportation
</a>

<a href="/university_system/student/parking_subscription.php">
🚗 Parking System
</a>

<a href="/university_system/student/my_subscriptions.php">
📋 My Subscriptions
</a>

<a href="/university_system/php/logout.php">
🚪 Logout
</a>

</div>
</div>

</div>

</header>