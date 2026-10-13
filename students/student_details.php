<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET STUDENT ID */

if(!isset($_GET['id'])){

    header("Location:view_students.php");
    exit();
}

$student_id = intval($_GET['id']);

/* GET STUDENT*/

$stmt = $conn->prepare("

SELECT *
FROM students
WHERE university_id=?

");

$stmt->bind_param("i", $student_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    header("Location:view_students.php");
    exit();
}

$student = $result->fetch_assoc();

/* BUS SUBSCRIPTIONS */

$busSubs = $conn->prepare("

SELECT

bus_subscriptions.*,

bus_trips.departure_time,
bus_trips.arrival_time,
bus_trips.days,
bus_line.line_name

FROM bus_subscriptions

JOIN bus_trips
ON bus_subscriptions.bus_trip_id = bus_trips.id

JOIN bus_line
ON bus_trips.bus_line_id = bus_line.id

WHERE bus_subscriptions.university_id=?

");

$busSubs->bind_param("i", $student_id);

$busSubs->execute();

$busSubscriptions = $busSubs->get_result();

/* =========================
   PARKING SUBSCRIPTIONS
========================= */

$parkingSubs = $conn->prepare("

SELECT *
FROM parking_subscriptions

WHERE university_id=?

");

$parkingSubs->bind_param("i", $student_id);

$parkingSubs->execute();

$parkingSubscriptions = $parkingSubs->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Student Details</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* =========================
   PAGE
========================= */

body{

    margin:0;
    font-family:Arial;
    background:#f4f7fb;
}

.admin-page{

    margin-left:351px;
    padding:110px 70px 70px;
	
	
}

/* =========================
   CARDS
========================= */

.card{

    background:white;

    border-radius:20px;

    padding:25px;

    margin-bottom:55px;

    box-shadow:0 5px 18px rgba(0,0,0,0.08);
}

.card h2{

    margin-top:0;

    color:#0b2d5c;
}

/* =========================
   INFO GRID
========================= */

.info-grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));

    gap:20px;
}

.info-item{

    background:#f8fafc;

    padding:18px;

    border-radius:14px;
}

.info-item h4{

    margin:0 0 8px;

    color:#334155;
}

.info-item p{

    margin:0;

    color:#111827;
}

/* =========================
   STATUS
========================= */

.status{

    display:inline-block;

    padding:8px 14px;

    border-radius:20px;

    font-size:14px;

    font-weight:bold;
}

.verified{

    background:#dcfce7;
    color:#166534;
}

.not-verified{

    background:#fee2e2;
    color:#991b1b;
}

/* =========================
   TABLE
========================= */

table{

    width:100%;

    border-collapse:collapse;
}

th{

    background:#0b2d5c;

    color:white;

    padding:14px;

    text-align:left;
}

td{

    padding:14px;

    border-bottom:1px solid #eee;
}

/* =========================
   EMPTY
========================= */

.empty{

    padding:20px;

    text-align:center;

    color:#64748b;
}

/* =========================
   MOBILE
========================= */

@media(max-width:900px){

    .admin-page{

        margin-left:0;
        padding:15px;
    }

    table{

        display:block;
        overflow-x:auto;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="admin-page">

    <!-- STUDENT INFO -->

    <div class="card">

        <h2>

            <i class="fa-solid fa-user-graduate"></i>
            Student Information

        </h2>

        <div class="info-grid">

            <div class="info-item">

                <h4>University ID</h4>

                <p><?= $student['university_id'] ?></p>

            </div>

            <div class="info-item">

                <h4>Username</h4>

                <p>  <?= htmlspecialchars($student['username']) ?> </p>

            </div>

            <div class="info-item">
                <h4>Email</h4>
                <p>
                    <?= htmlspecialchars($student['email']) ?>
                </p>
            </div>

            <div class="info-item">

                <h4>Phone</h4>

                <p>
                    <?= htmlspecialchars($student['phone']) ?>
                </p>
            </div>

            <div class="info-item">

                <h4>Status</h4>

                <?php if($student['is_verified'] == 1): ?>

                    <span class="status verified">Verified </span>

                <?php else: ?>

                    <span class="status not-verified">Not Verified </span>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- BUS SUBSCRIPTIONS -->

    <div class="card">

        <h2><i class="fa-solid fa-bus"></i> Bus Subscriptions </h2>

        <?php if($busSubscriptions->num_rows > 0): ?>

        <table>

            <tr>

                <th>Line</th>

                <th>Departure</th>

                <th>Arrival</th>

                <th>Days</th>
				<th>Status</th>

            </tr>

            <?php while($bus = $busSubscriptions->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= $bus['line_name'] ?>
                </td>

                <td>
                    <?= $bus['departure_time'] ?>
                </td>

                <td>
                    <?= $bus['arrival_time'] ?>
                </td>

                <td>
                    <?= $bus['days'] ?>
                </td>
				<td>

<?php

if(strtotime($bus['end_date']) >= time()){

    echo "<span style='color:limegreen;font-weight:bold;'>
    🟢 Active </span>";
}

else{

    echo "<span style='color:red;font-weight:bold;'>
    🔴 Expired</span>";
}
?>

</td>
            </tr>

            <?php endwhile; ?>

        </table>

        <?php else: ?>

            <div class="empty">No bus subscriptions found. </div>

        <?php endif; ?>

    </div>

    <!-- PARKING SUBSCRIPTIONS -->

    <div class="card">

        <h2> <i class="fa-solid fa-square-parking"></i>  Parking Subscriptions</h2>

        <?php if($parkingSubscriptions->num_rows > 0): ?>

        <table>

           <tr>

    <th>Plan</th>

    <th>Registration Date</th>

    <th>Expiration Date</th>
	<th>Status</th>

</tr>
            <?php while($parking = $parkingSubscriptions->fetch_assoc()): ?>

            <tr>

    <td>
        <?= htmlspecialchars($parking['plan']) ?>
    </td>

    <td>
        <?= $parking['registration_date'] ?>
    </td>

    <td>
        <?= $parking['expiration_date'] ?>
    </td>

<td>

<?php

if(strtotime($parking['expiration_date']) >= time()){

    echo "<span style='color:limegreen;font-weight:bold;'>
    🟢 Active </span>";
}

else{

    echo "<span style='color:red;font-weight:bold;'>
    🔴 Expired </span>";
}
?>
</td>
</tr>

            <?php endwhile; ?>

        </table>

        <?php else: ?>

            <div class="empty"> No parking subscriptions found. </div>

        <?php endif; ?>
    </div>

</div>
</body>
</html>