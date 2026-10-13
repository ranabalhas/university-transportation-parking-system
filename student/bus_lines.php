<?php
session_start();
include '../php/db.php';

if (!isset($_SESSION['university_id'])) {
    header("Location: student_auth.html");
    exit();
}

$line_id = $_GET['line'] ?? 0;

// Get line info
$line = $conn->query("SELECT * FROM bus_line WHERE id='$line_id'")->fetch_assoc();

// Get villages
$villages = $conn->query("SELECT * FROM bus_villages WHERE bus_line_id='$line_id'");

// Get trips
$trips = $conn->query("SELECT * FROM bus_trips WHERE bus_line_id='$line_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bus Line</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">Bus Line Details</div>
</header>

<div class="auth-container">

<div class="auth-box">

    <h2><?php echo $line['line_name']; ?></h2>

    <h4>Villages:</h4>
    <ul>
        <?php while($v = $villages->fetch_assoc()): ?>
            <li><?php echo $v['village_name']; ?></li>
        <?php endwhile; ?>
    </ul>

    <h4>Available Trips:</h4>

    <?php while($t = $trips->fetch_assoc()): 
        
        // ✅ capacity check INSIDE loop
        $count = $conn->query("
        SELECT COUNT(*) as total 
        FROM bus_subscriptions 
        WHERE bus_trip_id='".$t['id']."' 
        AND end_date >= CURDATE()
        ")->fetch_assoc()['total'];

        $capacity = 40;
        $remaining = $capacity - $count;
    ?>

    <form action="../php/subscribe_bus.php" method="POST">
        <input type="hidden" name="trip_id" value="<?php echo $t['id']; ?>">
        <input type="hidden" name="line_id" value="<?php echo $line_id; ?>">

        <p>
            <b><?php echo $t['departure_time'] . " → " . $t['arrival_time']; ?></b><br>
            Seats left: <?php echo $remaining; ?>
        </p>

        <?php if ($remaining > 0): ?>
            <button class="auth-btn">Subscribe</button>
        <?php else: ?>
            <p style="color:red;">⚠️ This trip is full</p>
        <?php endif; ?>

        <hr>

    </form>

    <?php endwhile; ?>

</div>

</div>

</body>
</html>