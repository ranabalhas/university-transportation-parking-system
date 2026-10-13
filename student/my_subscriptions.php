<?php
session_start();
include '../php/db.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* =========================
   CHECK LOGIN
========================= */
if (!isset($_SESSION['university_id'])) {
    header("Location: student_auth.php");
    exit();
}

$university_id = $_SESSION['university_id'];

/* =========================
   GET STUDENT
========================= */
$stmt = $conn->prepare("
SELECT *
FROM students
WHERE university_id=?
");

$stmt->bind_param("i", $university_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

/* =========================
   GET BUS SUBSCRIPTIONS
========================= */
$bus = $conn->prepare("
SELECT 
    bs.*,
    bl.line_name,
    bt.departure_time,
    bt.arrival_time,
    bt.days

FROM bus_subscriptions bs

JOIN bus_line bl
ON bs.bus_line_id = bl.id

JOIN bus_trips bt
ON bs.bus_trip_id = bt.id

WHERE bs.university_id=?

ORDER BY bs.end_date DESC
");

$bus->bind_param("i", $university_id);
$bus->execute();

$bus_result = $bus->get_result();

/* =========================
   GET PARKING SUBSCRIPTIONS
========================= */
$parking = $conn->prepare("
SELECT *
FROM parking_subscriptions
WHERE university_id=?
ORDER BY expiration_date DESC
");

$parking->bind_param("i", $university_id);
$parking->execute();

$parking_result = $parking->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">

<title>My Subscriptions</title>

<link rel="stylesheet" href="../css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
    margin:0;
    font-family:Arial;
    background:#f4f7fb;
}

/* =========================
   NAVBAR
========================= */

.portal-navbar {

    display:flex;
    justify-content:space-between;
    align-items:center;

    background:#0b2d5c;

    padding:18px 40px;

    color:white;
}

.portal-navbar a {

    color:white;
    text-decoration:none;
    margin-left:20px;
    font-weight:bold;
}

/* =========================
   HERO
========================= */

.hero-section {

    background:linear-gradient(135deg,#0b2d5c,#123d77);

    color:white;

    padding:60px 20px;

    text-align:center;
}

.hero-section h1 {

    margin:0;
    font-size:45px;
}

.hero-section p {

    margin-top:15px;
    font-size:20px;
}

/* =========================
   CONTAINER
========================= */

.main-container {

    width:90%;
    max-width:1200px;

    margin:auto;

    margin-top:40px;
    margin-bottom:60px;
}

/* =========================
   SECTION TITLE
========================= */

.section-title {

    font-size:30px;

    margin-bottom:25px;

    color:#0b2d5c;
}

/* =========================
   CARDS
========================= */

.subscription-grid {

    display:grid;

    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));

    gap:25px;
}

.sub-card {

    background:white;

    border-radius:20px;

    padding:25px;

    box-shadow:0 5px 15px rgba(0,0,0,0.08);

    transition:0.3s;
}

.sub-card:hover {

    transform:translateY(-5px);
}

.sub-card h3 {

    margin-top:0;
    color:#0b2d5c;
}

.sub-card p {

    line-height:1.9;
    color:#333;
}

.active {

    color:green;
    font-weight:bold;
}

.expired {

    color:red;
    font-weight:bold;
}

.empty-box {

    background:white;

    padding:30px;

    border-radius:15px;

    text-align:center;

    color:#666;

    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.back-btn {

    display:inline-block;

    margin-top:40px;

    background:#0b2d5c;

    color:white;

    padding:14px 24px;

    border-radius:10px;

    text-decoration:none;

    font-weight:bold;
}

.back-btn:hover {

    background:#123d77;
}

</style>
</head>

<body>

<?php include 'navbar.php'; ?>
<!-- HERO -->
<section class="hero-section">

<h1>
📋 My Subscriptions
</h1>

<p>
View all your transportation and parking subscriptions
in one place.
</p>

</section>

<div class="main-container">

<!-- =========================
     BUS SUBSCRIPTIONS
========================= -->

<h2 class="section-title">
🚌 Bus Subscriptions
</h2>

<?php if ($bus_result->num_rows > 0): ?>

<div class="subscription-grid">

<?php while($row = $bus_result->fetch_assoc()): ?>

<?php
$status =
(strtotime($row['end_date']) >= time())
? "Active"
: "Expired";
?>

<div class="sub-card">

<h3>
<?= $row['line_name'] ?>
</h3>

<p>

🕒
<?= $row['departure_time'] ?>
→
<?= $row['arrival_time'] ?>

<br>

📅 Type:
<b><?= $row['days'] ?></b>

<br>

📍 Village:
<?= $row['address'] ?>

<br>

⏳ Valid Until:
<?= $row['end_date'] ?>

<br><br>

Status:
<span class="<?= strtolower($status) ?>">
<?= $status ?>
</span>

</p>

</div>

<?php endwhile; ?>

</div>

<?php else: ?>

<div class="empty-box">

No bus subscriptions found.

</div>

<?php endif; ?>

<!-- =========================
     PARKING SUBSCRIPTIONS
========================= -->

<h2 class="section-title" style="margin-top:60px;">
🚗 Parking Subscriptions
</h2>

<?php if ($parking_result->num_rows > 0): ?>

<div class="subscription-grid">

<?php while($row = $parking_result->fetch_assoc()): ?>

<?php
$status =
(strtotime($row['expiration_date']) >= time())
? "Active"
: "Expired";
?>

<div class="sub-card">

<h3>
<?= $row['plan'] ?> Plan
</h3>

<p>

🗓 Registered On:
<?= $row['registration_date'] ?>

<br>

⏳ Valid Until:
<?= $row['expiration_date'] ?>

<br><br>

Status:
<span class="<?= strtolower($status) ?>">
<?= $status ?>
</span>

</p>

</div>

<?php endwhile; ?>

</div>

<?php else: ?>

<div class="empty-box">

No parking subscriptions found.

</div>

<?php endif; ?>

<a href="student_portal.php" class="back-btn">
⬅ Back to Portal
</a>

</div>

</body>
</html>