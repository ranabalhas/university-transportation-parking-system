<?php

include '../../php/db.php';
include '../includes/admin_session.php';

$success = "";

if(isset($_SESSION['trip_success'])){

    $success = $_SESSION['trip_success'];

    unset($_SESSION['trip_success']);
}

/* =========================
   DELETE TRIP
========================= */

if(isset($_GET['delete'])){

    $trip_id = intval($_GET['delete']);

    /* CHECK IF STUDENTS ARE SUBSCRIBED */

    $check = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM bus_subscriptions
    WHERE bus_trip_id = ?
    ");

    $check->bind_param("i", $trip_id);

    $check->execute();

    $result = $check->get_result()->fetch_assoc();

    if($result['total'] > 0){

        $_SESSION['trip_success'] =
        "Cannot delete trip because students are subscribed to it.";

    }else{

        $stmt = $conn->prepare("
        DELETE FROM bus_trips
        WHERE id=?
        ");

        $stmt->bind_param("i", $trip_id);

        $stmt->execute();

        $_SESSION['trip_success'] =
        "Trip deleted successfully.";
    }

    header("Location: view_trips.php");
    exit();
}

/* =========================
   GET TRIPS
========================= */

$trips = $conn->query("

SELECT

bus_trips.*,
bus_line.line_name

FROM bus_trips

LEFT JOIN bus_line
ON bus_trips.bus_line_id = bus_line.id

ORDER BY departure_time ASC, bus_line.line_name ASC

");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>View Trips</title>

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
   HEADER
========================= */

.page-header{

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:25px;
}

.page-header h1{

    margin:0;
    color:#0b2d5c;
}

/* =========================
   BUTTON
========================= */

.add-btn{

    background:#1d4ed8;
    color:white;

    padding:12px 18px;

    text-decoration:none;

    border-radius:10px;

    font-weight:bold;
}

/* =========================
   TABLE
========================= */

.table-box{

    background:white;

    border-radius:20px;

    overflow:auto;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

table{

    width:100%;
    border-collapse:collapse;
}

th{

    background:#0f172a;
    color:white;

    padding:16px;
    text-align:left;
}

td{

    padding:15px;
    border-bottom:1px solid #eee;
}

/* =========================
   ACTIONS
========================= */

.action-btn{

    padding:8px 14px;

    border-radius:8px;

    text-decoration:none;

    color:white;

    font-size:14px;

    margin-right:5px;
}

.edit-btn{

    background:#2563eb;
}

.delete-btn{

    background:#dc2626;
}

.success-box{

    background:#dcfce7;

    color:#166534;

    padding:15px;

    border-radius:12px;

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

    .page-header{

        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    table{

        min-width:900px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="main">

    <div class="page-header">

        <h1>Trip Management</h1>

        <a href="add_trip.php" class="add-btn">
            <i class="fa fa-plus"></i>Add Trip</a>

    </div>
<?php if($success): ?>

<div class="success-box">

    <?= $success ?>

</div>

<?php endif; ?>
    <div class="table-box">

        <table>

            <tr>

                <th>ID</th>

                <th>Bus Line</th>

                <th>Departure</th>

                <th>Arrival</th>

                <th>Days</th>

                <th>Capacity</th>

                <th>Actions</th>

            </tr>

            <?php while($trip = $trips->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= $trip['id'] ?>
                </td>

                <td>
                    <?= $trip['line_name'] ?>
                </td>

                <td>
                    <?= date("g:i A",
                    strtotime($trip['departure_time'])) ?>
                </td>

                <td>
                    <?= date("g:i A",
                    strtotime($trip['arrival_time'])) ?>
                </td>

                <td>
                    <?= $trip['days'] ?>
                </td>

                <td>
                    <?= $trip['capacity'] ?>
                </td>

                <td>

                    <a href="edit_trip.php?id=<?= $trip['id'] ?>" class="action-btn edit-btn">
                        Edit
                    </a>

                    <a href="?delete=<?= $trip['id'] ?>"
                    class="action-btn delete-btn"

                    onclick="return confirm(
                    'Delete this trip?'
                    )">

                        Delete

                    </a>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>
</html>