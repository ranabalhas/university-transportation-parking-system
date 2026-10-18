<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET PARKING CAPACITY */

$parking = $conn->query("

SELECT *
FROM parking
LIMIT 1

")->fetch_assoc();

$total_capacity = $parking['capacity'];

/* COUNT ACTIVE SUBSCRIPTIONS*/

$occupied_query = $conn->query("

SELECT COUNT(*) AS total

FROM parking_subscriptions

WHERE expiration_date >= CURDATE()

");

$occupied = $occupied_query->fetch_assoc()['total'];

$available = $total_capacity - $occupied;

if($available < 0){

    $available = 0;
}

/* OCCUPANCY PERCENTAGE */

$percentage = 0;

if($total_capacity > 0){

    $percentage = ($occupied / $total_capacity) * 100;
}

/*  UPDATE CAPACITY*/

$success = "";

if(isset($_POST['update_capacity'])){

    $new_capacity = intval($_POST['capacity']);

    if($new_capacity > 0){

        $stmt = $conn->prepare("

        UPDATE parking
        SET capacity=?
        WHERE id=?

        ");

        $stmt->bind_param(

            "ii",

            $new_capacity,
            $parking['id']
        );

        $stmt->execute();

        $success = "Parking capacity updated successfully.";

        header("Refresh:1");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Parking Management</title>

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"
href="../../css/style.css">

<style>

.page-content{

    margin-left:351px;
    padding:30px;
}

.page-title{

    font-size:34px;
    font-weight:bold;

    color:#0b2d5c;

    margin-bottom:30px;
}

/* =========================
   CARDS
========================= */

.stats-grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(260px,1fr));

    gap:25px;

    margin-bottom:35px;
}

.stat-card{

    background:white;

    border-radius:20px;

    padding:30px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);

    position:relative;

    overflow:hidden;
}

.stat-card h3{

    color:#64748b;

    margin-bottom:15px;

    font-size:18px;
}

.stat-number{

    font-size:42px;

    font-weight:bold;

    color:#0b2d5c;
}

.stat-icon{

    position:absolute;

    right:25px;
    top:25px;

    font-size:55px;

   
}

/* =========================
   ANALYTICS BOX
========================= */

.analytics-box{

    background:white;

    border-radius:20px;

    padding:30px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);

    margin-bottom:30px;
}

.analytics-box h2{

    color:#0b2d5c;

    margin-bottom:20px;
}

/* =========================
   PROGRESS BAR
========================= */

.progress-container{

    width:100%;

    height:30px;

    background:#e5e7eb;

    border-radius:30px;

    overflow:hidden;

    margin-top:20px;
}

.progress-bar{

    height:100%;

    background:linear-gradient(
    90deg,
    #2563eb,
    #38bdf8
    );

    width:<?= $percentage ?>%;

    display:flex;

    align-items:center;
    justify-content:center;

    color:white;

    font-weight:bold;

    transition:0.5s;
}

/* =========================
   UPDATE FORM
========================= */

.capacity-form{

    margin-top:25px;

    display:flex;

    gap:15px;

    flex-wrap:wrap;
}

.capacity-form input{

    flex:1;

    min-width:220px;

    padding:14px;

    border-radius:10px;

    border:1px solid #ccc;

    font-size:15px;
}

.capacity-form button{

    padding:14px 22px;

    border:none;

    border-radius:10px;

    background:#2563eb;

    color:white;

    font-weight:bold;

    cursor:pointer;

    transition:0.3s;
}

.capacity-form button:hover{

    background:#1d4ed8;
}

/* SUCCESS */

.success-msg{

    background:#dcfce7;

    color:#166534;

    padding:14px;

    border-radius:10px;

    margin-bottom:20px;

    font-weight:bold;
}

/* RESPONSIVE */

@media(max-width:900px){

.page-content{

    margin-left:0;
    padding:20px;
}

}

</style>
</head>
<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="page-content">

<div class="page-title">🚗 Parking Management Dashboard</div>

<?php if($success): ?>

<div class="success-msg">

<?= $success ?>

</div>

<?php endif; ?>

<!-- =========================
     STATISTICS
========================= -->

<div class="stats-grid">

<div class="stat-card">

<div class="stat-icon">🚗</div>

<h3>Total Capacity</h3>

<div class="stat-number">

<?= $total_capacity ?>

</div>

</div>

<div class="stat-card">

<div class="stat-icon">🅿️</div>

<h3>Occupied Spaces</h3>

<div class="stat-number">

<?= $occupied ?>

</div>

</div>

<div class="stat-card">

<div class="stat-icon">✅</div>

<h3>Available Spaces</h3>

<div class="stat-number">

<?= $available ?>

</div>

</div>

</div>

<!--ANALYTICS-->

<div class="analytics-box">

<h2>📊 Parking Occupancy Analytics</h2>

<p>Current Occupancy:<strong>

<?= round($percentage) ?>%

</strong>
</p>

<div class="progress-container">

<div class="progress-bar">

<?= round($percentage) ?>%

</div>
</div>

<!--UPDATE CAPACITY-->

<form method="POST"class="capacity-form"><input

type="number"name="capacity"placeholder="Enter new parking capacity"required>

<button type="submit"name="update_capacity">Update Capacity</button>

</form>
</div>
</div>
</body>
</html>