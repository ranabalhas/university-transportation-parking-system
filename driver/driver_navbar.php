<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../php/db.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: driver_login.html");
    exit();
}

$driver_id = $_SESSION['driver_id'];

$stmt = $conn->prepare("
SELECT d.*, bl.line_name
FROM driver d
JOIN bus_line bl
ON d.bus_line_id = bl.id
WHERE d.id=?
");

$stmt->bind_param("i", $driver_id);
$stmt->execute();

$driver = $stmt->get_result()->fetch_assoc();
?>

<header class="driver-navbar">

<div class="driver-logo">🚌 Driver Portal</div>

<nav class="driver-menu">

<a href="driver_dashboard.php">🏠 Dashboard</a>

<a href="daily_schedule.php">📅 Daily Schedule</a>

<div class="driver-info">

<span>
👤 <?= $driver['name'] ?>
</span>

<span class="line-badge"><?= $driver['line_name'] ?></span>

<a href="/university_system/php/logout.php"class="logout-btn">🚪 Logout</a>

</div>

</nav>

</header>