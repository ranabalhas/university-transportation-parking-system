<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
include '../php/db.php';

if (!isset($_SESSION['university_id'])) {
    header("Location: student_auth.php");
    exit();
}

$university_id = $_SESSION['university_id'];

/* GET STUDENT INFO*/
$stmt = $conn->prepare("
SELECT *
FROM students
WHERE university_id=?
");

$stmt->bind_param("i", $university_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$username = $student['username'];
$email = $student['email'];
$phone = $student['phone'] ?? '';

/* =========================
   GET ANNOUNCEMENTS
========================= */

$announcements = $conn->query("

SELECT *
FROM announcements
WHERE audience='students'
OR audience='all'

ORDER BY created_at DESC
LIMIT 3

");


?>

<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="UTF-8">

<title>Student Portal</title>

<link rel="stylesheet" href="../css/style.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* =========================
   BODY
========================= */

body {

    margin:0;
    overflow-x:hidden;
    font-family:Arial, sans-serif;
    background-position:center;
    color:white;
}

/* =========================
   HERO
========================= */

.portal-hero {
    text-align:center;
    padding:80px 20px 40px;
}

.portal-hero h1 {

    color:#0b2d5c;
    font-size:42px;
    margin-bottom:20px;
    line-height:1.3;
}

.portal-hero p {

    color:#334155;
    font-size:18px;
    max-width:700px;
    margin:auto;
    line-height:1.7;
}

/* =========================
   CARDS
========================= */

.portal-cards {
    width:min(1200px,92%);
    margin:auto;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:25px;
    margin-top:10px;
    margin-bottom:60px;
}

.portal-card {
    background:#0b2d5c;
    backdrop-filter:blur(10px);
    border-radius:25px;
    padding:22px;
    transition:0.3s;
    border:1px solid rgba(255,255,255,0.2);
}

.portal-card:hover {
    transform:translateY(-8px);
}

.portal-card i {
    font-size:36px;
    margin-bottom:12px;
    color:#fbbf24;
}

.portal-card h2 {
    color:#fbbf24;
    margin-bottom:15px;
}

.portal-card p {
      font-size:14px;
	  margin-bottom:8px;
     line-height:1.5;
     color:white;
}

/* =========================
   BUTTON
========================= */

.portal-btn {

    display:block;
    width:100%;
    margin-top:20px;
    padding:12px;
    background:#fbbf24;
    color:white;
    border-radius:10px;
    font-weight:bold;
    transition:0.3s;
}

.portal-btn:hover {
    background:#1d4ed8;
}


/* =========================
   ANNOUNCEMENTS
========================= */

.announcement-container{

    width:min(1100px,95%);
    margin:25px auto;
    background:white;
    border-radius:24px;
    padding:25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.announcement-container h2{

    color:#0b2d5c;
    margin-bottom:22px;
}

/* CARD */

.announcement-card{
    background:#f8fafc;
    padding:20px;
    border-radius:18px;
    margin-bottom:18px;
    transition:0.3s;
    border-left:7px solid #2563eb;
}

.announcement-card:hover{

    transform:translateY(-4px);
}

/* PRIORITIES */

.priority-normal{

    border-left-color:#2563eb;
}

.priority-important{

    border-left-color:#f59e0b;
}

.priority-urgent{

    border-left-color:#dc2626;
}

/* HEADER */

.announcement-header{

    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    margin-bottom:15px;
}

.announcement-title{
    font-size:22px;
    font-weight:bold;
    color:#0f172a;
}

/* BADGES */

.priority-badge{

    padding:6px 12px;
    border-radius:20px;
    color:white;
    font-size:12px;
    font-weight:bold;
}

.badge-normal{

    background:#2563eb;
}

.badge-important{

    background:#f59e0b;
}

.badge-urgent{

    background:#dc2626;
}

/* MESSAGE */

.announcement-message{
    color:#334155;
    line-height:1.8;
    margin-bottom:12px;
}

.announcement-date{

    font-size:13px;
    color:#64748b;
}

/* EMPTY */

.announcement-empty{

    text-align:center;
    color:#64748b;
    padding:20px;
}

/* MOBILE */

@media(max-width:768px){

    .announcement-header{

        flex-direction:column;

        align-items:flex-start;
    }

    .announcement-title{

        font-size:18px;
    }
}

/* =========================
   FOOTER
========================= */

.portal-footer {

    text-align:center;

    padding:12px;

    background:rgba(0,0,0,0.4);

    font-size:15px;
}

/* =========================
   MOBILE RESPONSIVE
========================= */

@media (max-width:768px){

    body{
        overflow-x:hidden;
    }

    .portal-hero{

        padding:25px 15px 10px;
    }

    .portal-hero h1{

        font-size:30px;

        line-height:1.3;

        margin-bottom:12px;
    }

    .portal-hero p{

        font-size:15px;

        line-height:1.7;

        width:100%;
    }

    .portal-cards{

        width:92%;

        grid-template-columns:1fr;

        gap:18px;

        margin-top:20px;
    }

    .portal-card{
width:100%;
        padding:14px;

        border-radius:20px;
    }

    .portal-card h2{

        font-size:28px;
    }

    .portal-card p{

        font-size:15px;
    }

    .portal-btn{

        font-size:15px;

        padding:13px;
    }

    .portal-footer{

        font-size:13px;

        padding:12px;
    }
}
</style>
</head>

<body>

<?php include 'navbar.php'; ?>
<!-- HERO -->
<section class="portal-hero">

<h1>Welcome <?= $username ?> 👋</h1>

<p>Welcome to the University Transportation & Parking Services Portal.Manage your bus subscriptions, parking registration,
account settings, and university transportation servicesthrough one unified platform.</p>

</section>

<!-- CARDS -->
<section class="portal-cards">

<!-- BUS -->
<div class="portal-card">

<i class="fa fa-bus"></i>

<h2>Bus Transportation</h2>

<p>Manage your university transportation subscriptions,
view your routes, and access bus schedules easily.</p>

<a href="bus_dashboard.php" class="portal-btn">Open Bus System</a>

</div>

<!-- PARKING -->
<div class="portal-card">

<i class="fa fa-parking"></i>

<h2>Parking Services</h2>

<p>Subscribe to parking plans, check your registration,
and manage your campus parking access.</p>

<a href="parking_subscription.php" class="portal-btn">Open Parking System</a>
</div>

<!-- SUBSCRIPTIONS -->
<div class="portal-card">

<i class="fa fa-id-card"></i>

<h2>My Subscriptions</h2>

<p>View all your active transportation and parking subscriptions in one place.</p>

<a href="my_subscriptions.php" class="portal-btn">View Subscriptions</a>

</div>
</section>

<!-- ANNOUNCEMENTS-->
<div class="announcement-container">
<h2>
📢 Latest Announcements
</h2>

<?php if($announcements->num_rows > 0): ?>

    <?php while($note = $announcements->fetch_assoc()): ?>

    <div class="announcement-card
         priority-<?= strtolower($note['priority']) ?>">

        <div class="announcement-header">

<div class="announcement-title">

<?= htmlspecialchars($note['title']) ?>

</div>

<div class="priority-badge
badge-<?= strtolower($note['priority']) ?>">

<?= ucfirst($note['priority']) ?>

</div>

</div>

        <div class="announcement-message">

            <?= $note['message'] ?>

        </div>

        <div class="announcement-date">

            <?= date("F j, Y - g:i A",
            strtotime($note['created_at'])) ?>

        </div>

    </div>

    <?php endwhile; ?>

<?php else: ?>

<div class="announcement-empty">

No announcements available.</div>

<?php endif; ?>

</div>
<!-- FOOTER -->
<footer class="portal-footer">

University Transportation & Parking Services System

</footer>
</body>
</html>