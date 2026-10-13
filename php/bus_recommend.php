<?php
session_start();
include '../php/db.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['university_id'])) {
    header("Location:  /university_system/student/student_auth.php");
    exit();
}

/* SAVE VILLAGE IN SESSION*/

if (isset($_POST['village'])) {

    $_SESSION['selected_village'] = $_POST['village'];

    header("Location: bus_recommend.php");

    exit();
}

/* CHECK SESSION */

if (!isset($_SESSION['selected_village'])) {

    echo "Please select a village.";

    exit();
}

$village = $_SESSION['selected_village'];

/*  GET LINE */

$stmt = $conn->prepare("
SELECT bus_line_id 
FROM bus_villages 
WHERE village_name = ?
LIMIT 1
");

$stmt->bind_param("s", $village);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    echo "Village not found";

    exit();
}

$row = $result->fetch_assoc();

$line_id = $row['bus_line_id'];

/*GET LINE NAME */

$stmt = $conn->prepare("
SELECT line_name
FROM bus_line
WHERE id=?
");

$stmt->bind_param("i", $line_id);

$stmt->execute();

$line = $stmt->get_result()->fetch_assoc();

/* GET DRIVER INFO*/

$stmt = $conn->prepare("
SELECT name, phone
FROM driver
WHERE bus_line_id=?
LIMIT 1
");

$stmt->bind_param("i", $line_id);

$stmt->execute();

$driver = $stmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Bus Recommendation</title>
    <link rel="stylesheet" href="/university_system/css/style.css">
</head>
<body class="bus-recommend">

<?php include '../student/navbar.php'; ?>

<div class="auth-container">
<div class="auth-box">

<h2>🚌 <?php echo $line['line_name']; ?></h2>

<?php if ($driver): ?>

<div class="bus-card" style="margin-bottom:20px;">

    <h3>Driver Information</h3>

    <p><b>Name:</b> <?php echo $driver['name']; ?></p>

    <p><b>Phone:</b> <?php echo $driver['phone']; ?></p>

</div>

<?php endif; ?>

<div id="resultBox"></div>

<h3>Available Trips</h3>

<?php
$trips = $conn->prepare("SELECT * FROM bus_trips WHERE bus_line_id=?");
$trips->bind_param("i", $line_id);
$trips->execute();
$resTrips = $trips->get_result();

while ($t = $resTrips->fetch_assoc()) {

    $capacity = $t['capacity'];

    $count = $conn->query("
    SELECT COUNT(*) as total 
    FROM bus_subscriptions 
    WHERE bus_trip_id='{$t['id']}'
    AND end_date >= CURDATE()
    ")->fetch_assoc()['total'];

    $remaining = $capacity - $count;
?>

<div class="trip-card">

    <div class="trip-info">
        <b><?php echo $t['departure_time']; ?> → <?php echo $t['arrival_time']; ?></b><br>
        📅 <?php echo $t['days']; ?><br>
        Seats left: <?php echo $remaining; ?>
    </div>

    <?php if ($remaining <= 0): ?>
        <button class="auth-btn" disabled>Full</button>
    <?php else: ?>
        <button class='auth-btn'  onclick="subscribeBus(this, <?php echo $t['id']; ?>, <?php echo $line_id; ?>, '<?php echo $village; ?>')">
Subscribe
</button>
    <?php endif; ?>

</div>

<hr>

<?php } ?>

</div>
</div>

<div id="loadingSpinner" class="loading-overlay hidden">

    <div class="spinner-box">
        <div class="spinner"></div>
        <p>Please wait...</p>

    </div>
</div>

<script>
function subscribeBus(btn, tripId, lineId, address) {
	showLoading(btn, "Subscribing...");

    fetch('../php/subscribe_bus.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `trip_id=${tripId}&line_id=${lineId}&address=${address}`
    })
    .then(res => res.text())
    .then(data => {

        // remove old messages
        document.querySelectorAll('.local-msg').forEach(e => e.remove());

        // create message
        let msg = document.createElement("div");
        msg.className = "local-msg";
        msg.innerHTML = data;

        // insert inside the same card
        btn.parentElement.appendChild(msg);
	setTimeout(()=>{
	document.getElementById("loadingSpinner").classList.add("hidden");
	btn.innerText = "Subscribe";
	btn.disabled = false;},2000);

    });
}


function showLoading(button = null, text = "Please wait...") {

    document.getElementById("loadingSpinner").classList.remove("hidden");

    if (button) {

        button.disabled = true;

        button.innerText = text;
    }
}

</script>

</body>
</html>