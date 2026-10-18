<?php
include 'includes/admin_session.php';
include '../php/db.php';

/* STATISTICS*/

/* STUDENTS */
$students =
$conn->query("
SELECT COUNT(*) AS total
FROM students
")->fetch_assoc()['total'];

/* DRIVERS */
$drivers =
$conn->query("
SELECT COUNT(*) AS total
FROM driver
")->fetch_assoc()['total'];

/* TRIPS */
$trips =
$conn->query("
SELECT COUNT(*) AS total
FROM bus_trips
")->fetch_assoc()['total'];

/* BUS LINES */
$lines =
$conn->query("
SELECT COUNT(*) AS total
FROM bus_line
")->fetch_assoc()['total'];

/*SUBSCRIPTIONS BY LINE */

$line_chart_query = $conn->query("

SELECT

bus_line.line_name,
COUNT(bus_subscriptions.id) AS total_subscriptions

FROM bus_line

LEFT JOIN bus_subscriptions
ON bus_line.id = bus_subscriptions.bus_line_id

GROUP BY bus_line.id

ORDER BY total_subscriptions DESC

");

$line_labels = [];
$line_totals = [];

while($row = $line_chart_query->fetch_assoc()){

    $line_labels[] = $row['line_name'];
    $line_totals[] = $row['total_subscriptions'];
}
/* BUS SUBSCRIPTIONS */

$bus_subscriptions =
$conn->query("
SELECT COUNT(*) AS total
FROM bus_subscriptions
")->fetch_assoc()['total'];

/* PARKING SUBSCRIPTIONS*/

$parking_subscriptions =
$conn->query("
SELECT COUNT(*) AS total
FROM parking_subscriptions
")->fetch_assoc()['total'];

/* PARKING CAPACITY*/

$parking =
$conn->query("
SELECT capacity
FROM parking
LIMIT 1
")->fetch_assoc();

$total_parking_spaces = $parking['capacity'] ?? 0;

$occupied_parking =
$conn->query("
SELECT COUNT(*) AS total
FROM parking_subscriptions
WHERE expiration_date >= CURDATE()
")->fetch_assoc()['total'];

$available_parking =
$total_parking_spaces - $occupied_parking;

/* TRIP OCCUPANCY*/

$trip_occupancy_query = $conn->query("

SELECT
bus_trips.id,
bus_line.line_name,
bus_trips.departure_time,
COUNT(bus_subscriptions.id) AS subscribed,
bus_trips.capacity

FROM bus_trips

LEFT JOIN bus_subscriptions
ON bus_subscriptions.bus_trip_id = bus_trips.id

LEFT JOIN bus_line
ON bus_line.id = bus_trips.bus_line_id

GROUP BY bus_trips.id

ORDER BY subscribed DESC

LIMIT 5

");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{

    background:#f1f5f9;
}

/* =========================
   MAIN AREA
========================= */

.admin-main{

    margin-left:345px;
    padding:30px;
}

/* HEADER */

.dashboard-header{

    background:white;
    border-radius:20px;
    padding:30px;
    margin-bottom:30px;

    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
}

.dashboard-header h1{

    color:#0f172a;
    margin-bottom:10px;
}

.dashboard-header p{

    color:#64748b;
    line-height:1.6;
}

/* =========================
   STATS
========================= */

.stats-grid{

    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
}

.stat-card{

    background:white;
    border-radius:22px;
    padding:25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    transition:0.3s;
}

.stat-card:hover{

    transform:translateY(-5px);
}

.stat-top{

    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.stat-icon{

    width:65px;
    height:65px;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    font-size:28px;
}

.blue{
    background:#2563eb;
}

.green{
    background:#16a34a;
}

.orange{
    background:#ea580c;
}

.purple{
    background:#7c3aed;
}

.stat-card h2{

    font-size:38px;
    color:#0f172a;
}

.stat-card p{

    color:#64748b;
    font-size:15px;
}

/* =========================
   QUICK ACTIONS
========================= */

.quick-actions{

    margin-top:35px;
    background:white;
    padding:30px;
    border-radius:22px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
}

.quick-actions h2{

    margin-bottom:25px;
    color:#0f172a;
}

.action-grid{

    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));
    gap:18px;
}

.action-btn{

    background:#2563eb;
    color:white;
    text-decoration:none;
    padding:18px;
    border-radius:16px;
    transition:0.3s;
    display:flex;
    align-items:center;
    gap:12px;
    font-weight:bold;
}

.action-btn:hover{

    background:#1d4ed8;
    transform:translateY(-3px);
}

/* =========================
   CHART
========================= */

.chart-container{

    margin-top:35px;
    background:white;
    padding:30px;
    border-radius:22px;
    box-shadow:0 5px 20px rgba(0,0,0,0.06);
}

.chart-container h2{

    margin-bottom:25px;
    color:#0f172a;
}

#lineChart{

    max-height:420px;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px){

    .admin-main{

        margin-left:0;

        padding:20px;
    }

    .dashboard-header{

        padding:22px;
    }

    .dashboard-header h1{

        font-size:28px;
    }
}

</style>

</head>

<body>

<?php include 'includes/admin_sidebar.php'; ?>

<div class="admin-main">

<div class="dashboard-header">

<h1>
Welcome <?= $admin_name ?> 👋
</h1>

<p>Manage university transportation,drivers, trips, parking services,students, subscriptions, and reportsthrough one professional control system.
</p>

</div>

<!-- STATS -->

<div class="stats-grid">

<div class="stat-card">

<div class="stat-top">

<div>

<h2><?= $students ?></h2>

<p>Total Students</p>

</div>

<div class="stat-icon blue">

<i class="fa-solid fa-user-graduate"></i>

</div>

</div>

</div>

<div class="stat-card">

<div class="stat-top">
<div>

<h2><?= $drivers ?></h2>

<p>Total Drivers</p>

</div>

<div class="stat-icon green">

<i class="fa-solid fa-id-badge"></i>

</div>
</div>
</div>

<div class="stat-card">

<div class="stat-top">

<div>

<h2><?= $trips ?></h2>

<p>Total Trips</p>

</div>

<div class="stat-icon orange">

<i class="fa-solid fa-bus"></i>

</div>
</div>
</div>

<div class="stat-card">

<div class="stat-top">

<div>

<h2><?= $lines ?></h2>

<p>Bus Lines</p>

</div>

<div class="stat-icon purple">

<i class="fa-solid fa-route"></i>

</div>

</div>

</div>
<!-- BUS SUBSCRIPTIONS -->

<div class="stat-card">

<div class="stat-top">

<div>

<h2><?= $bus_subscriptions ?></h2>

<p>Bus Subscriptions</p>

</div>

<div class="stat-icon blue">

<i class="fa-solid fa-ticket"></i>

</div>

</div>

</div>

<!-- PARKING SUBSCRIPTIONS -->

<div class="stat-card">

<div class="stat-top">

<div>

<h2><?= $parking_subscriptions ?></h2>

<p>Parking Subscriptions</p>

</div>

<div class="stat-icon green">

<i class="fa-solid fa-car"></i>

</div>

</div>

</div>

</div>

<!-- QUICK ACTIONS -->

<div class="quick-actions">

<h2>Quick Actions</h2>

<div class="action-grid">

<a href="/university_system/admin/drivers/add_driver.php" class="action-btn">
<i class="fa-solid fa-user-plus"></i>Add Driver</a>

<a href="/university_system/admin/trips/add_trip.php" class="action-btn">
<i class="fa-solid fa-plus"></i>Add Trip</a>

<a href="/university_system/admin/lines/add_line.php" class="action-btn">
<i class="fa-solid fa-route"></i>Add Bus Line</a>

<a href="/university_system/admin/students/view_students.php" class="action-btn">
<i class="fa-solid fa-user-group"></i>View Students</a>

</div>

</div>
<!-- ANALYTICS-->

<div class="quick-actions" style="margin-top:30px;">

<h2>System Analytics</h2>

<!--TOP TRIPS -->

<div class="quick-actions" style="margin-top:30px;">

<h2>Top Occupied Trips</h2>

<table style="width:100%;border-collapse:collapse;margin-top:20px;">

<tr style="background:#2563eb;color:white;">

<th style="padding:14px;">Bus Line</th>
<th>Departure</th>
<th>Subscribed</th>
<th>Capacity</th>
<th>Occupancy</th>

</tr>

<?php while($trip = $trip_occupancy_query->fetch_assoc()):

$percentage = 0;

if($trip['capacity'] > 0){

$percentage =($trip['subscribed'] / $trip['capacity']) * 100;
}

?>

<tr style="background:white;border-bottom:1px solid #ddd;text-align:center;">

<td style="padding:14px;">
<?= $trip['line_name'] ?>
</td>

<td>
<?= date("g:i A", strtotime($trip['departure_time'])) ?>
</td>

<td>
<?= $trip['subscribed'] ?>
</td>

<td>
<?= $trip['capacity'] ?>
</td>

<td>

<div style="width:100%;background:#e2e8f0;border-radius:10px;overflow:hidden;height:18px;">

<div style="width:<?= $percentage ?>%;background:#16a34a;height:100%;">

</div>

</div>

<small>
<?= round($percentage) ?>%
</small>

</td>

</tr>

<?php endwhile; ?>

</table>

</div>
<div class="action-grid">

<!-- PARKING CHART -->

<div style="background:#f8fafc;padding:20px;border-radius:20px;">

<h3 style="margin-bottom:20px;color:#0f172a;">Parking Usage</h3>

<canvas id="parkingChart"></canvas>

</div>

<!-- USERS CHART -->

<div style="background:#f8fafc;padding:20px;border-radius:20px;">

<h3 style="margin-bottom:20px;color:#0f172a;">Users Overview</h3>

<canvas id="usersChart"></canvas>

</div>

</div>

</div>
<!-- SUBSCRIPTIONS CHART-->

<div class="chart-container">

<h2>Bus Subscription Distribution by Line</h2>

<canvas id="lineChart"></canvas>

</div>
</div>
<script>

/* PARKING CHART*/

new Chart(
document.getElementById('parkingChart'),

{
type:'doughnut',

data:{

labels:[
'Occupied',
'Available'
],

datasets:[{

data:[
<?= $occupied_parking ?>,
<?= $available_parking ?>
],

backgroundColor:[
'#dc2626',
'#16a34a']}]
}
});

/* USERS CHART */

new Chart(
document.getElementById('usersChart'),

{
type:'bar',

data:{

labels:[
'Students',
'Drivers'
],

datasets:[{

label:'Users',

data:[
<?= $students ?>,
<?= $drivers ?>
]

}]
}

});


const ctx = document.getElementById('lineChart');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?= json_encode($line_labels) ?>,

        datasets: [{

            label: 'Students',

            data: <?= json_encode($line_totals) ?>,

            backgroundColor: ['#2563eb', '#16a34a', '#ea580c', '#7c3aed', '#0f766e', '#dc2626'  ],

            borderRadius: 10

        }]
    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                display: false
            }
        },

        scales: {

            y: {

                beginAtZero: true,

                ticks: {

                    stepSize: 1
                }
            }
        }
    }
});


</script>
</body>
</html>