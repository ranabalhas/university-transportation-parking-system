<?php
include '../../php/db.php';
include '../includes/admin_session.php';

/* REPORT 1 TOTAL ACTIVE BUS SUBSCRIPTIONS */

$bus_subscriptions = $conn->query("

SELECT COUNT(*) AS total
FROM bus_subscriptions
WHERE end_date >= CURDATE()

")->fetch_assoc()['total'];

/* REPORT 2 ACTIVE PARKING SUBSCRIPTIONS*/

$parking_subscriptions = $conn->query("

SELECT COUNT(*) AS total
FROM parking_subscriptions

WHERE expiration_date >= CURDATE()

")->fetch_assoc()['total'];

/*  REPORT 3 MOST USED BUS LINES*/

$most_used_lines = $conn->query("

SELECT
bus_line.line_name,
COUNT(bus_subscriptions.id) AS total_subscriptions

FROM bus_subscriptions

INNER JOIN bus_line
ON bus_subscriptions.bus_line_id = bus_line.id

GROUP BY bus_line.id

ORDER BY total_subscriptions DESC

LIMIT 5

");

/* REPORT 4 PEAK TRIP TIMES*/

$peak_trips = $conn->query("

SELECT

bus_trips.departure_time,
COUNT(bus_subscriptions.id) AS total_students

FROM bus_subscriptions

INNER JOIN bus_trips
ON bus_subscriptions.bus_trip_id = bus_trips.id

GROUP BY bus_trips.departure_time

ORDER BY total_students DESC

LIMIT 5

");

/* REPORT 5  FULL / CROWDED TRIPS */

$full_trips = $conn->query("

SELECT

bus_trips.id,
bus_trips.departure_time,
bus_trips.arrival_time,
bus_trips.capacity,
bus_trips.days,

bus_line.line_name,

COUNT(bus_subscriptions.id) AS occupied

FROM bus_trips

LEFT JOIN bus_subscriptions
ON bus_trips.id = bus_subscriptions.bus_trip_id

INNER JOIN bus_line
ON bus_trips.bus_line_id = bus_line.id

GROUP BY bus_trips.id

HAVING occupied >= (capacity * 0.8)

ORDER BY occupied DESC

");

/*  PARKING ANALYTIC */

$parking = $conn->query("

SELECT *
FROM parking
LIMIT 1

")->fetch_assoc();

$capacity = $parking['capacity'];

$occupied = $parking_subscriptions;

$parking_percentage = 0;

if($capacity > 0){

    $parking_percentage =
    ($occupied / $capacity) * 100;
}


/* CHART DATA */

/* MOST USED LINES */
$line_labels = [];
$line_data = [];

$chart_lines = $conn->query("

SELECT

bus_line.line_name,
COUNT(bus_subscriptions.id) AS total

FROM bus_subscriptions

INNER JOIN bus_line
ON bus_subscriptions.bus_line_id = bus_line.id

GROUP BY bus_line.id

");

while($row = $chart_lines->fetch_assoc()){

    $line_labels[] = $row['line_name'];
    $line_data[] = $row['total'];
}

/* PEAK TIMES */
$trip_labels = [];
$trip_data = [];

$chart_trips = $conn->query("

SELECT

bus_trips.departure_time,
COUNT(bus_subscriptions.id) AS total

FROM bus_subscriptions

INNER JOIN bus_trips
ON bus_subscriptions.bus_trip_id = bus_trips.id

GROUP BY bus_trips.departure_time

");

while($row = $chart_trips->fetch_assoc()){

    $trip_labels[] = $row['departure_time'];
    $trip_data[] = $row['total'];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Reports & Statistics</title>

<meta name="viewport"content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"href="../../css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
   TOP STATS
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
}

.stat-card h3{

    color:#64748b;
    margin-bottom:15px;
}

.stat-number{

    font-size:40px;
    font-weight:bold;

    color:#0b2d5c;
}

.stat-icon{

    position:absolute;

    right:25px;
    top:20px;

    font-size:50px;

    
}

/* =========================
   REPORT BOX
========================= */

.report-box{

    background:white;

    border-radius:20px;

    padding:30px;

    margin-bottom:30px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.report-box h2{

    color:#0b2d5c;

    margin-bottom:25px;
}

/* TABLE */

table{

    width:100%;

    border-collapse:collapse;
}

table th{

    background:#0b2d5c;

    color:white;

    padding:14px;

    text-align:left;
}

table td{

    padding:14px;

    border-bottom:1px solid #eee;
}

/* =========================
   PROGRESS BAR
========================= */

.progress-container{

    width:100%;

    height:28px;

    background:#e5e7eb;

    border-radius:30px;

    overflow:hidden;

    margin-top:20px;
}
.progress-bar{

    height:100%;

    width:<?= $parking_percentage ?>%;

    background:linear-gradient(
    90deg,
    #2563eb,
    #38bdf8
    );
    display:flex;

    justify-content:center;
    align-items:center;

    color:white;

    font-weight:bold;
}
/* =========================
   CHARTS
========================= */

.charts-grid{
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(420px,1fr));
    gap:25px;
    margin-bottom:35px;
}

.chart-card{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:
    0 5px 20px rgba(0,0,0,0.08);
}

.chart-card h2{

    margin-bottom:20px;

    color:#0b2d5c;

    font-size:22px;
}

.chart-card canvas{

    width:100% !important;

    max-height:320px;
}
/* RESPONSIVE */

@media(max-width:900px){

.page-content{

    margin-left:0;
    padding:20px;
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

<div class="page-content">

<div class="page-title">

📊 Reports & Statistics Dashboard

</div>

<!-- TOP CARDS-->

<div class="stats-grid">

<div class="stat-card">

<div class="stat-icon">🚌</div>

<h3>Active Bus Subscriptions</h3>

<div class="stat-number">

<?= $bus_subscriptions ?>

</div>

</div>

<div class="stat-card">

<div class="stat-icon">🚗</div>

<h3>Parking Subscriptions</h3>

<div class="stat-number">

<?= $parking_subscriptions ?>

</div>

</div>

</div>
<!--CHARTS SECTION-->

<div class="charts-grid">

    <!-- MOST USED LINES -->
    <div class="chart-card">

        <h2>🚌 Bus Subscription Distribution</h2>

        <canvas id="lineChart"></canvas>

    </div>
    <!-- PEAK TIMES -->
    <div class="chart-card">

        <h2>⏰ Peak Departure Times</h2>

        <canvas id="tripChart"></canvas> </div>

    <!-- PARKING -->
    <div class="chart-card">

        <h2>🚗 Parking Utilization</h2>

        <canvas id="parkingChart"></canvas>
    </div>

    <!-- SUBSCRIPTIONS -->
    <div class="chart-card">

        <h2>📊 System Usage Distribution</h2>

        <canvas id="subscriptionChart"></canvas>
    </div>
</div>

<!-- MOST USED LINES -->

<div class="report-box">

<h2>🏆 Most Used Bus Lines</h2>

<table>
<tr>
<th>Bus Line</th>
<th>Total Subscriptions</th>
</tr>

<?php while($line = $most_used_lines->fetch_assoc()): ?>

<tr>
<td>

<?= $line['line_name'] ?>

</td>
<td>

<?= $line['total_subscriptions'] ?>

</td>
</tr>

<?php endwhile; ?>

</table>
</div>

<!--PEAK TRIP TIMES -->
<div class="report-box">

<h2>⏰ Peak Trip Times</h2>

<table>

<tr>
<th>Departure Time</th>
<th>Total Students</th>

</tr>
<?php while($trip = $peak_trips->fetch_assoc()): ?>

<tr>

<td>

<?= $trip['departure_time'] ?>

</td>
<td>

<?= $trip['total_students'] ?>

</td>
</tr>

<?php endwhile; ?>

</table>
</div>

<!-- CROWDED TRIPS -->
<div class="report-box">

<h2>🔥 Crowded Trips</h2>

<table>

<tr>

<th>Line</th>
<th>Departure</th>
<th>Arrival</th>
<th>Days</th>
<th>Capacity</th>
<th>Occupied</th>

</tr>

<?php while($trip = $full_trips->fetch_assoc()): ?>

<tr>
<td>

<?= $trip['line_name'] ?>

</td>
<td>

<?= $trip['departure_time'] ?>

</td>
<td>

<?= $trip['arrival_time'] ?>

</td>
<td>

<?= $trip['days'] ?>

</td>
<td>

<?= $trip['capacity'] ?>

</td>
<td>

<?= $trip['occupied'] ?>

</td>
</tr>

<?php endwhile; ?>

</table>
</div>

<!-- PARKING ANALYTICS-->

<div class="report-box">

<h2>🚗 Parking Occupancy Analytics</h2>

<p>Parking Occupancy:<strong>

<?= round($parking_percentage) ?>% </strong></p>

<div class="progress-container">
<div class="progress-bar">

<?= round($parking_percentage) ?>%

</div>
</div>
</div>
</div>
<script>

/* MOST USED LINES */

new Chart(
document.getElementById('lineChart'),
{
    type:'bar',

    data:{
        labels: <?= json_encode($line_labels) ?>,

        datasets:[{

            label:'Students',

            data: <?= json_encode($line_data) ?>,

            backgroundColor:'#2563eb',

            borderRadius:10
        }]  },

    options:{
        responsive:true
    }});

/* PEAK TIMES */

new Chart(
document.getElementById('tripChart'),
{
    type:'line',

    data:{
        labels: <?= json_encode($trip_labels) ?>,

        datasets:[{

            label:'Students',

            data: <?= json_encode($trip_data) ?>,

            borderColor:'#ea580c',

            backgroundColor:'rgba(234,88,12,0.2)',

            tension:0.4,

            fill:true
        }]
    },

    options:{
        responsive:true
    }
});

/* PARKING UTILIZATION */

new Chart(
document.getElementById('parkingChart'),
{
    type:'doughnut',

    data:{
        labels:['Occupied','Available'],

        datasets:[{

            data:[
                <?= $occupied ?>,
                <?= $capacity - $occupied ?>
            ],

            backgroundColor:[
                '#2563eb',
                '#e5e7eb'
            ]
        }]
    },

    options:{
        responsive:true
    }});

/*SYSTEM DISTRIBUTION */

new Chart(
document.getElementById('subscriptionChart'),
{
    type:'pie',

    data:{
        labels:[
            'Bus Subscriptions',
            'Parking Subscriptions'
        ],

        datasets:[{

            data:[
                <?= $bus_subscriptions ?>,
                <?= $parking_subscriptions ?>
            ],

            backgroundColor:[
                '#7c3aed',
                '#16a34a'
            ]
        }]},

    options:{
        responsive:true
    }});
</script>
</body>
</html>