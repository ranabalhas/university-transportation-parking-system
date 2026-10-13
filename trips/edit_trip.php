<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* =========================
   GET TRIP ID
========================= */

if(!isset($_GET['id'])){

    header("Location:view_trips.php");
    exit();
}

$trip_id = intval($_GET['id']);

/* =========================
   GET TRIP
========================= */

$stmt = $conn->prepare("

SELECT *
FROM bus_trips
WHERE id=?

");

$stmt->bind_param("i", $trip_id);

$stmt->execute();

$trip = $stmt->get_result()->fetch_assoc();

if(!$trip){

    header("Location:view_trips.php");
    exit();
}

/* =========================
   GET BUS LINES
========================= */

$lines = $conn->query("

SELECT *
FROM bus_line
ORDER BY line_name

");

/* =========================
   UPDATE
========================= */

$success = "";
$error = "";
if(isset($_GET['success'])){

    if($_GET['success'] == 'updated'){

        $success = "Trip updated successfully.";
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $bus_line_id = intval($_POST['bus_line_id']);

    $departure_time = $_POST['departure_time'];

    $arrival_time = $_POST['arrival_time'];

    $days = trim($_POST['days']);

    $capacity = intval($_POST['capacity']);

    if(
        empty($departure_time) || empty($arrival_time) || empty($days) || $capacity <= 0
    ){

        $error = "Please fill all fields correctly.";
    }

    else{

    /* CHECK CURRENT SUBSCRIBED STUDENTS */

    $checkCapacity = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM bus_subscriptions
    WHERE bus_trip_id = ?
    ");

    $checkCapacity->bind_param("i", $trip_id);

    $checkCapacity->execute();

    $currentStudents =
    $checkCapacity->get_result()->fetch_assoc()['total'];

    /* VALIDATE CAPACITY */

    if($capacity < $currentStudents){

        $error =
        "Capacity cannot be less than the current number of subscribed students (" .
        $currentStudents .
        ").";

    }else{

        $stmt = $conn->prepare("

        UPDATE bus_trips

        SET

        arrival_time=?,
        departure_time=?,
        days=?,
        capacity=?,
        bus_line_id=?

        WHERE id=?

        ");

        $stmt->bind_param(

            "sssiii",

            $arrival_time,
            $departure_time,
            $days,
            $capacity,
            $bus_line_id,
            $trip_id
        );

        if($stmt->execute()){

            header(
            "Location: edit_trip.php?id="
            .$trip_id.
            "&success=updated"
            );

            exit();

        }else{

            $error = "Something went wrong.";
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Trip</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

/* =========================
   PAGE
========================= */

body{

    margin:0;
    font-family:Arial;
    background:#f4f7fb;
}

/* =========================
   MAIN
========================= */

.main{

    margin-left:351px;
    padding:110px 30px 30px;
}

/* =========================
   CARD
========================= */

.form-card{

    max-width:700px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.form-card h1{

    margin-top:0;
    color:#0b2d5c;
}

/* =========================
   FORM
========================= */

.form-group{

    margin-bottom:20px;
}

.form-group label{

    display:block;

    margin-bottom:8px;

    font-weight:bold;

    color:#334155;
}

.form-group input,
.form-group select{

    width:100%;

    padding:14px;

    border:1px solid #dbeafe;

    border-radius:10px;

    font-size:15px;

    box-sizing:border-box;
}

/* =========================
   BUTTON
========================= */

.submit-btn{

    width:100%;

    padding:14px;

    border:none;

    background:#2563eb;

    color:white;

    font-size:16px;

    font-weight:bold;

    border-radius:12px;

    cursor:pointer;
}
.back-btn{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#2563eb;

    font-weight:bold;
}


/* =========================
   ALERTS
========================= */

.success{

    background:#dcfce7;
    color:#166534;

    padding:14px;

    border-radius:10px;

    margin-bottom:20px;
}

.error{

    background:#fee2e2;
    color:#991b1b;

    padding:14px;

    border-radius:10px;

    margin-bottom:20px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .main{

        margin-left:0;
        padding:120px 15px 20px;
    }

    .form-card{

        padding:20px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="main">
<a href="/university_system/admin/trips/view_trips.php"class="back-btn"> ← Back to Trips</a>
    <div class="form-card">

        <h1>
            Edit Trip
        </h1>

        <?php if($success): ?>

            <div class="success">

                <?= $success ?>

            </div>

        <?php endif; ?>

        <?php if($error): ?>

            <div class="error">

                <?= $error ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label>
                    Bus Line
                </label>

                <select name="bus_line_id" required>

                    <?php while($line = $lines->fetch_assoc()): ?>

                    <option value="<?= $line['id'] ?>"

                    <?= $trip['bus_line_id'] == $line['id']
                    ? 'selected'
                    : '' ?>

                    >

                        <?= $line['line_name'] ?>

                    </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Departure Time
                </label>

                <input type="time"

                name="departure_time"

                value="<?= $trip['departure_time'] ?>"

                required>

            </div>

            <div class="form-group">

                <label>
                    Arrival Time
                </label>

                <input type="time"

                name="arrival_time"

                value="<?= $trip['arrival_time'] ?>"

                required>

            </div>

            <div class="form-group">

                <label>
                    Operating Days
                </label>

                <select name="days" required>

                    <option value="MW"

                    <?= $trip['days']=="MW"
                    ? 'selected'
                    : '' ?>

                    >
                        MW
                    </option>

                    <option value="TTH"

                    <?= $trip['days']=="TTH"
                    ? 'selected'
                    : '' ?>

                    >
                        TTH
                    </option>

                    <option value="Daily"

                    <?= $trip['days']=="Daily"
                    ? 'selected'
                    : '' ?>

                    >
                        Daily
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Capacity
                </label>

                <input type="number"

                name="capacity"

                value="<?= $trip['capacity'] ?>"

                required>

            </div>

            <button class="submit-btn">

                Update Trip

            </button>

        </form>

    </div>

</div>

<script>

if(window.history.replaceState){

    window.history.replaceState(
        null,
        null,
        window.location.pathname + "?id=<?= $trip_id ?>"
    );
}

</script>
</body>
</html>