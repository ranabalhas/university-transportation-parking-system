<?php
include 'db.php';
session_start();

if (!isset($_SESSION['university_id'])) {
    echo "no_subscription";
    exit();
}

$university_id = $_SESSION['university_id'];

$sql = "
SELECT plan, registration_date, expiration_date
FROM parking_subscriptions
WHERE university_id = '$university_id'
ORDER BY expiration_date DESC
LIMIT 1
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    $today = date("Y-m-d"); // 🔥 add this

    // 🔥 CHECK if expired
    if ($row['expiration_date'] < $today) {

        echo "⚠️ Your subscription has expired\n";
        echo "Please subscribe again.";

    } else {

        // Format dates
        $reg = date("d F Y", strtotime($row['registration_date']));
        $exp = date("d F Y", strtotime($row['expiration_date']));

        echo "You registered on: $reg\n";
        echo "Your parking validity ends on: $exp";
    }

} else {
    echo "no_subscription";
}
?>