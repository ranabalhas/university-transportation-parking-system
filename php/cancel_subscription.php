<?php
session_start();
include 'db.php';

if (!isset($_SESSION['university_id'])) {
    echo "⚠️ Login required";
    exit();
}

$sub_id = $_POST['sub_id'];
$university_id = $_SESSION['university_id'];

/* CHECK IF CAN CANCEL */
$stmt = $conn->prepare("
SELECT can_cancel FROM bus_subscriptions
WHERE id=? AND university_id=?
");
$stmt->bind_param("ii", $sub_id, $university_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo "⚠️ Subscription not found";
    exit();
}

$row = $res->fetch_assoc();

if ($row['can_cancel'] == 0) {
    echo "⚠️ You can no longer cancel this subscription";
    exit();
}

/* DELETE */
$stmt = $conn->prepare("DELETE FROM bus_subscriptions WHERE id=?");
$stmt->bind_param("i", $sub_id);

if ($stmt->execute()) {
    echo "✅ Subscription cancelled";
} else {
    echo "⚠️ Error cancelling";
}
?>