<?php
session_start();
include 'db.php';

if (!isset($_SESSION['university_id'])) {
    echo "⚠️ Please login first";
    exit();
}

$university_id = $_SESSION['university_id'];
$trip_id = $_POST['trip_id'];
$line_id = $_POST['line_id'];
$address = $_POST['address'];

$start = date("Y-m-d");
$end = date("Y-m-d", strtotime("+4 months"));

/* GET NEW TRIP INFO*/
$newTrip = $conn->prepare("
SELECT departure_time, arrival_time, days, capacity
FROM bus_trips
WHERE id=?
");
$newTrip->bind_param("i", $trip_id);
$newTrip->execute();
$new = $newTrip->get_result()->fetch_assoc();

$newDep = $new['departure_time'];
$newArr = $new['arrival_time'];
$newDays = $new['days'];
$capacity = $new['capacity'];

/* 1. CHECK CAPACITY*/
$stmt = $conn->prepare("
SELECT COUNT(*) as total
FROM bus_subscriptions
WHERE bus_trip_id=? AND end_date >= CURDATE()
");
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['total'];

if ($count >= $capacity) {
    echo "⚠️ Bus is full";
    exit();
}

/* 2. CHECK DUPLICATE SAME TRIP*/
$stmt = $conn->prepare("
SELECT 1 FROM bus_subscriptions
WHERE university_id=? AND bus_trip_id=? AND end_date >= CURDATE()
");
$stmt->bind_param("ii", $university_id, $trip_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo "⚠️ You already selected this trip";
    exit();
}

/* 3. CHECK TIME + DAY CONFLICT*/
$stmt = $conn->prepare("
SELECT bt.departure_time, bt.arrival_time, bt.days
FROM bus_subscriptions bs
JOIN bus_trips bt ON bs.bus_trip_id = bt.id
WHERE bs.university_id=? AND bs.end_date >= CURDATE()
");
$stmt->bind_param("i", $university_id);
$stmt->execute();
$existing = $stmt->get_result();

function daysConflict($d1, $d2) {
    if ($d1 == 'Daily' || $d2 == 'Daily') return true;
    if ($d1 == $d2) return true;
    return false;
}

while ($row = $existing->fetch_assoc()) {

    $oldDep = $row['departure_time'];
    $oldArr = $row['arrival_time'];
    $oldDays = $row['days'];

    // 🔥 Check day conflict first
    // Normalize time comparison
$sameTime = ($newDep == $oldDep && $newArr == $oldArr);

// 🔥 Rule 1: SAME TIME (block always)
if ($sameTime) {
    echo "⚠️ You cannot subscribe to same time with different day types. <br>
Please <a href='/university_system/student/bus_dashboard.php' style='color:blue; text-decoration:underline;'>go to your dashboard</a> and cancel it.<br>


⏳ Note: Cancellation is only allowed on the same day of subscription.";

    exit();
}

// 🔥 Rule 2: overlapping days + overlapping time
if (daysConflict($newDays, $oldDays)) {
    if (($newDep < $oldArr) && ($newArr > $oldDep)) {
        echo "⚠️ Time conflict with another trip";
        exit();
    }	
}}

/* 4. INSERT*/
$stmt = $conn->prepare("
INSERT INTO bus_subscriptions
(university_id, start_date, end_date, address, bus_line_id, bus_trip_id)
VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssii",
    $university_id,
    $start,
    $end,
    $address,
    $line_id,
    $trip_id
);

if ($stmt->execute()) {
    echo "✅ Subscription added successfully";
} else {
    echo "⚠️ Error adding subscription";
}
?>