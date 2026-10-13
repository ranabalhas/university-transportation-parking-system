<?php
session_start();

include '../php/db.php';

/* NO CACHE */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* CHECK LOGIN */
if (!isset($_SESSION['university_id'])) {

    header("Location: student_auth.php");
    exit();
}

$university_id = $_SESSION['university_id'];

/* UPDATE CANCEL STATUS */
$conn->query("
UPDATE bus_subscriptions
SET can_cancel = 0
WHERE start_date < CURDATE()
");

/* GET SUBSCRIPTIONS */
$sql = "
SELECT bs.*, bs.can_cancel,  bl.line_name,  bt.arrival_time,  bt.departure_time,  bt.days

FROM bus_subscriptions bs

JOIN bus_line bl
ON bs.bus_line_id = bl.id

JOIN bus_trips bt
ON bs.bus_trip_id = bt.id

WHERE bs.university_id = '$university_id'
AND bs.end_date > CURDATE()
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bus Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bus-dashboard">

<?php include 'navbar.php'; ?>

<section class="hero small-hero">
    <h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>
    <p>Manage your university transportation easily</p>
</section>

<div class="auth-container">

<div class="auth-box dashboard-box">

<h2>🚌 Your Subscriptions</h2>

<?php if ($result->num_rows > 0): ?>

    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="bus-card">
            <h3><?php echo $row['line_name']; ?></h3>

            <p>🕒 <?php echo $row['departure_time'] . " → " . $row['arrival_time']; ?></p>

            <p>📅 Day Type: <b><?php echo $row['days']; ?></b></p>

            <p>⏳ Valid until: <?php echo $row['end_date']; ?></p>

<?php if ($row['can_cancel'] == 1): ?>
    <button class="auth-btn" style="background:#e74c3c;"onclick="cancelSub(this, <?php echo $row['id']; ?>)">
Cancel
</button>
<?php else: ?>
    <p style="color:gray;">❌ Cannot cancel</p>
<?php endif; ?>
        </div><br>

    <?php endwhile; ?>

    <a href="/university_system/student/find_bus.php" class="auth-btn">
        ➕ Add New Subscription
    </a>

<?php else: ?>

    <p style="text-align:center; color:#555;"> You don't have any subscriptions yet. </p><br>
<a href="/university_system/student/find_bus.php" class="auth-btn">   Find My Bus </a>

<?php endif; ?>

</div>
</div>

<div id="loadingSpinner" class="loading-overlay hidden">

    <div class="spinner-box">

        <div class="spinner"></div>

        <p>Please wait...</p>

    </div>

</div>
<script>
window.addEventListener("pageshow", function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});


function cancelSub(btn, id) {
showLoading();
    fetch('/university_system/php/cancel_subscription.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'sub_id=' + id
    })
    .then(res => res.text())
    .then(data => {

   
   setTimeout(() => {
    btn.closest('.bus-card').remove();
    document.getElementById("loadingSpinner").classList.add("hidden");

    // show message AFTER spinner is hidden
    setTimeout(() => {
        alert(data);
    }, 50);

}, 1000);

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