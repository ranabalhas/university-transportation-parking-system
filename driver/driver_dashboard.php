<?php
session_start();
include '../php/db.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['driver_id'])) {
    header("Location:  /university_system/driver/driver_login.html");
    exit();
}

$driver_id = $_SESSION['driver_id'];

/*  GET DRIVER INFO*/
$stmt = $conn->prepare("
SELECT d.*, bl.line_name
FROM driver d
JOIN bus_line bl ON d.bus_line_id = bl.id
WHERE d.id=?
");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();

$line_id = $driver['bus_line_id'];

/* GET ANNOUNCEMENTS*/

$announcements = $conn->query("

SELECT *
FROM announcements
WHERE audience='drivers'
OR audience='all'

ORDER BY created_at DESC
LIMIT 3
");

/* 2. GET TRIPS */
$trips = $conn->prepare("
SELECT * FROM bus_trips
WHERE bus_line_id=?
ORDER BY departure_time
");
$trips->bind_param("i", $line_id);
$trips->execute();
$trips_result = $trips->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bus-dashboard">

<?php include 'driver_navbar.php'; ?>

<section class="hero small-hero">
    <h1>Welcome Driver 👋</h1>
    <p><?php echo $driver['line_name']; ?></p>
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

No announcements available.

</div>

<?php endif; ?>

</div>
<div class="auth-container">
<div class="auth-box dashboard-box">

<h2>🚌 Your Trips</h2>

<a href="daily_schedule.php" class="auth-btn" style="display:inline-block; margin-bottom:20px;">
📅 View Daily Schedule
</a>

<?php while ($trip = $trips_result->fetch_assoc()): ?> 

<?php
/* 3. GET STUDENTS PER TRIP */
$students = $conn->prepare("
SELECT s.username, s.email,s.phone, bs.address, bt.days,bs.end_date
FROM bus_subscriptions bs
JOIN students s ON bs.university_id = s.university_id
JOIN bus_trips bt ON bs.bus_trip_id = bt.id
WHERE bs.bus_trip_id=? AND bs.end_date >= CURDATE()
");
$students->bind_param("i", $trip['id']);
$students->execute();
$students_result = $students->get_result();

/* 4. COUNT CAPACITY */
$count = $students_result->num_rows;
$capacity = $trip['capacity'];
?>

<div class="bus-card">

<h3>
🕒 <?php echo $trip['departure_time'] . " → " . $trip['arrival_time']; ?>
</h3>

<p>📅 <?php echo $trip['days']; ?></p>

<p>📊 <b><?php echo $count; ?> / <?php echo $capacity; ?> students</b></p>

<br>

<table style="width:100%; border-collapse:collapse; text-align:center;">
<tr style="background:#eee;">
    <th>Name</th>
    <th>Email</th>
	<th>Phone</th>
    <th>Village</th>
    <th>Day</th>
</tr>

<?php if ($count > 0): ?>
    <?php while ($st = $students_result->fetch_assoc()): ?>
    
<tr>

    <td><?= $st['username'] ?></td>

    <td><?= $st['email'] ?></td>

    <td>
        <?= !empty($st['phone']) ? $st['phone'] : 'No phone' ?>
    </td>

    <td><?= $st['address'] ?></td>

    <td><?= $st['days'] ?></td>

    
</tr>

    <?php endwhile; ?>

<?php else: ?>

<tr>
    <td colspan="6">No students yet</td>
</tr>

<?php endif; ?>
</table>

</div>

<?php endwhile; ?>

</div>
</div>
<div id="toast" class="toast"></div>

<style>
/* =========================
   ANNOUNCEMENTS
========================= */

.announcement-container{

    width:min(1100px,95%);
    margin:25px auto;
    background:white;
    border-radius:24px;
    padding:25px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
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
</style>
<script>

function showToast(message, type = "success") {

    let toast = document.getElementById("toast");

    toast.innerText = message;

    toast.className = "toast show";

    if (type === "success") {

        toast.style.background = "#22c55e";

    }

    setTimeout(() => {

        toast.className = "toast";

    }, 3000);
}

/* SUCCESS MESSAGE*/

const success =
new URLSearchParams(window.location.search).get("success");

if (success === "password_changed") {

    showToast("Password updated successfully");
}

/* REMOVE URL PARAMETERS AFTER REFRESH */

if (window.history.replaceState) {

    window.history.replaceState(
        null,
        null,
        window.location.pathname
    );
}
</script>
</body>
</html>