<?php
include 'db.php';
session_start();

// ✅ Check login
if (!isset($_SESSION['university_id'])) {
    echo "⚠️ You must login first.";
    exit();
}

$university_id = $_SESSION['university_id'];
$plan = $_POST['plan'] ?? '';

// Get today's date
$registration_date = date("Y-m-d");

// Calculate expiration date
if ($plan == "Monthly") {
    $expiration_date = date("Y-m-d", strtotime("+1 month"));
} 
else if ($plan == "Semester") {
    $expiration_date = date("Y-m-d", strtotime("+4 months"));
} 
else {
    echo "⚠️ Invalid plan.";
    exit();
}

/* 1. GET PARKING CAPACITY */
$capResult = $conn->query("SELECT capacity FROM parking LIMIT 1");
$capacity = $capResult->fetch_assoc()['capacity'];

/*  2. COUNT ACTIVE SUBSCRIPTIONS */
$stmt = $conn->prepare("
SELECT COUNT(*) as total 
FROM parking_subscriptions 
WHERE expiration_date >= CURDATE()
");
$stmt->execute();

$count = $stmt->get_result()->fetch_assoc()['total'];

/* 3. CHECK CAPACITY*/
if ($count >= $capacity) {
    echo "⚠️ Parking is full";
    exit();
}

/*4. CHECK IF ALREADY SUBSCRIBED*/
$stmt = $conn->prepare("
SELECT * FROM parking_subscriptions 
WHERE university_id=? 
AND expiration_date >= CURDATE()
");
$stmt->bind_param("i", $university_id);
$stmt->execute();
$check = $stmt->get_result();

if ($check->num_rows > 0) {

    $row = $check->fetch_assoc();

    $reg = date("d F Y", strtotime($row['registration_date']));
    $exp = date("d F Y", strtotime($row['expiration_date']));

    echo "⚠️ You already have an active subscription\n\n";
    echo "Registered on: $reg\n";
    echo "Valid until: $exp";

    exit();
}

/* 5. INSERT SUBSCRIPTION */
$stmt = $conn->prepare("
INSERT INTO parking_subscriptions 
(university_id, plan, registration_date, expiration_date)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param("isss", $university_id, $plan, $registration_date, $expiration_date);

if ($stmt->execute()) {

    $reg = date("d F Y", strtotime($registration_date));
    $exp = date("d F Y", strtotime($expiration_date));

    echo "✅ Parking Registration Successful\n";
    echo "Registered on: $reg\n";
    echo "Valid until: $exp";

} else {
    echo "⚠️ Error occurred!";
}
?>
