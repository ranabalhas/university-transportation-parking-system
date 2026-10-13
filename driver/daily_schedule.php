<?php
session_start();
include '../php/db.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* CHECK LOGIN*/
if (!isset($_SESSION['driver_id'])) {
    header("Location: driver_login.html");
    exit();
}

$driver_id = $_SESSION['driver_id'];

/* GET DRIVER LINE*/
$stmt = $conn->prepare("
SELECT d.bus_line_id, bl.line_name
FROM driver d
JOIN bus_line bl
ON d.bus_line_id = bl.id
WHERE d.id=?
");

$stmt->bind_param("i", $driver_id);
$stmt->execute();

$driver = $stmt->get_result()->fetch_assoc();

$line_id = $driver['bus_line_id'];
$line_name = $driver['line_name'];

/* SELECTED DAY*/
$selected_day = $_GET['day'] ?? '';

/*  DAY TYPE LOGIC */
$allowed_days = [];

if ($selected_day == "Monday" || $selected_day == "Wednesday") {

    $allowed_days = ['Daily', 'MW'];

}
elseif ($selected_day == "Tuesday" || $selected_day == "Thursday") {

    $allowed_days = ['Daily', 'TTH'];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Driver Daily Schedule</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bus-dashboard">

<?php include 'driver_navbar.php'; ?>
<div class="auth-container">
<div class="auth-box dashboard-box">

<h2>📅 Daily Schedule</h2>

<!-- DAY SELECTOR -->
<form method="GET" style="margin-bottom:20px;">

    <select name="day" required>

        <option value="">Select Day</option>

        <option value="Monday"
        <?= ($selected_day == 'Monday') ? 'selected' : '' ?>>
        Monday
        </option>

        <option value="Tuesday"
        <?= ($selected_day == 'Tuesday') ? 'selected' : '' ?>>
        Tuesday
        </option>

        <option value="Wednesday"
        <?= ($selected_day == 'Wednesday') ? 'selected' : '' ?>>
        Wednesday
        </option>

        <option value="Thursday"
        <?= ($selected_day == 'Thursday') ? 'selected' : '' ?>>
        Thursday
        </option>

    </select>

    <button class="auth-btn"> Show Schedule </button>

</form>

<?php

/* SHOW SCHEDULE */
if ($selected_day != ''):

/* GET TRIPS */
$trips = $conn->prepare("
SELECT *
FROM bus_trips
WHERE bus_line_id=?
ORDER BY departure_time
");

$trips->bind_param("i", $line_id);

$trips->execute();

$trips_result = $trips->get_result();

/* MERGE TRIPS BY TIME */
$mergedTrips = [];

while ($trip = $trips_result->fetch_assoc()) {

    /* SKIP INVALID DAY TYPES */
    if (!in_array($trip['days'], $allowed_days)) {
        continue;
    }

    /* UNIQUE KEY */
    $tripKey = $trip['departure_time'] . '_' . $trip['arrival_time'];

    /* CREATE NEW MERGED TRIP */
    if (!isset($mergedTrips[$tripKey])) {

        $mergedTrips[$tripKey] = [
            'departure_time' => $trip['departure_time'],
            'arrival_time' => $trip['arrival_time'],
            'trip_ids' => [],
            'students' => []
        ];
    }

    /* STORE TRIP IDS */
    $mergedTrips[$tripKey]['trip_ids'][] = $trip['id'];
}

/*LOAD STUDENTS */
$total_students_today = 0;

foreach ($mergedTrips as $key => $mergedTrip) {

    foreach ($mergedTrip['trip_ids'] as $trip_id) {

       $students = $conn->prepare("
        SELECT s.username, s.phone, bs.address
        FROM bus_subscriptions bs

        JOIN students s
        ON bs.university_id = s.university_id

         WHERE bs.bus_trip_id=?
         AND bs.end_date >= CURDATE()

ORDER BY bs.address, s.username
");

        $students->bind_param("i", $trip_id);

        $students->execute();

        $students_result = $students->get_result();

        while ($st = $students_result->fetch_assoc()) {

            $mergedTrips[$key]['students'][] = $st;

            $total_students_today++;
        }
    }
}
?>

<!-- SUMMARY -->
<div class="bus-card">

<p><b>📅 Day:</b> <?= $selected_day ?></p>

<p><b>🚍 Line:</b> <?= $line_name ?></p>

<p><b>🚌 Active Trips:</b> <?= count($mergedTrips) ?></p>

<p><b>👥 Total Students Today:</b> <?= $total_students_today ?></p>

</div>

<?php
/* SHOW MERGED TRIPS*/
foreach ($mergedTrips as $trip):
?>

<div class="bus-card">

<h3>
🕒 <?= $trip['departure_time'] ?>
→
<?= $trip['arrival_time'] ?>
</h3>

<p>
<b>👥 Total Students:</b>
<?= count($trip['students']) ?>
</p>

<?php
$currentVillage = '';

foreach ($trip['students'] as $st):

    if ($currentVillage != $st['address']) {

        $currentVillage = $st['address'];
?>

<!-- VILLAGE -->
<div style="background:#f1f1f1;padding:10px;margin-top:15px;border-radius:10px;font-weight:bold;">

📍 <?= $currentVillage ?>

</div>

<?php } ?>

<!-- STUDENT -->
<div style="padding-left:20px;margin-top:10px;line-height:1.8;">

👤 <?= $st['username'] ?><br>

📞 <?= $st['phone'] ?>

</div>

<?php endforeach; ?>

<?php if (count($trip['students']) == 0): ?>

<p>No students for this trip.</p>

<?php endif; ?>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>
</div>

</body>
</html>