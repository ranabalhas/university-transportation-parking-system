<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/*SEARCH */

$search = "";

if(isset($_GET['search'])){

    $search = trim($_GET['search']);
}

/* QUERY */

$sql = "

SELECT

bus_subscriptions.id,
bus_subscriptions.start_date,
bus_subscriptions.end_date,

students.university_id,
students.username,

bus_line.line_name,

bus_trips.departure_time,
bus_trips.arrival_time,
bus_trips.days

FROM bus_subscriptions

INNER JOIN students
ON bus_subscriptions.university_id = students.university_id

INNER JOIN bus_line
ON bus_subscriptions.bus_line_id = bus_line.id

INNER JOIN bus_trips
ON bus_subscriptions.bus_trip_id = bus_trips.id

WHERE

students.username LIKE ?
OR
students.university_id LIKE ?
OR
bus_line.line_name LIKE ?

ORDER BY bus_subscriptions.id DESC

";

$stmt = $conn->prepare($sql);

$searchTerm = "%$search%";

$stmt->bind_param(

    "sss",

    $searchTerm,
    $searchTerm,
    $searchTerm
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Bus Subscriptions</title>

<link rel="stylesheet" href="../../css/style.css">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<style>

/* =========================
   PAGE
========================= */

.admin-page{

    margin-left:351px;
    padding:110px 70px 70px;
	margin-bottom:35px;
	
}

/* =========================
   HEADER
========================= */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;

    margin-bottom:70px;
}

.page-title{

    font-size:28px;
    font-weight:bold;
    color:#0f172a;
}

/* =========================
   SEARCH
========================= */

.search-box{

    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.search-box input{

    padding:12px;
    width:260px;

    border:1px solid #ccc;
    border-radius:10px;
}

.search-btn{

    background:#2563eb;
    color:white;

    border:none;

    padding:12px 18px;

    border-radius:10px;

    cursor:pointer;
}

/* =========================
   TABLE
========================= */

.table-box{

    background:white;

    border-radius:20px;

    overflow:auto;

    box-shadow:0 5px 15px rgba(0,0,0,0.08);
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

    padding:16px;
    border-bottom:1px solid #eee;
}

/* =========================
   BADGES
========================= */

.active-badge{

    background:#dcfce7;
    color:#166534;

    padding:6px 12px;

    border-radius:20px;

    font-size:13px;
    font-weight:bold;
}

.expired-badge{

    background:#fee2e2;
    color:#991b1b;

    padding:6px 12px;

    border-radius:20px;

    font-size:13px;
    font-weight:bold;
}

/* =========================
   BUTTON
========================= */

.cancel-btn{

    background:#dc2626;
    color:white;

    padding:8px 14px;

    border-radius:8px;

    text-decoration:none;

    font-size:14px;
}

.cancel-btn:hover{

    background:#ef4444;
}

/* =========================
   MOBILE
========================= */

@media(max-width:900px){

    .admin-page{

        margin-left:0;
        padding:20px;
    }

    .page-header{

        flex-direction:column;
        align-items:flex-start;
    }

    .search-box{

        width:100%;
    }

    .search-box input{

        width:100%;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>
<?php include '../includes/admin_navbar.php'; ?>

<div class="admin-page">

<div class="page-header">

<div class="page-title">🚌 Bus Subscriptions</div>

<form method="GET" class="search-box">

<input type="text"name="search"placeholder="Search student or line..."value="<?= htmlspecialchars($search) ?>">

<button class="search-btn">Search</button>

</form>

</div>

<div class="table-box">

<table>

<tr>

<th>Student</th>
<th>University ID</th>
<th>Bus Line</th>
<th>Trip</th>
<th>Days</th>
<th>Start</th>
<th>End</th>
<th>Status</th>
<th>Actions</th>

</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?= htmlspecialchars($row['username']) ?>
</td>

<td>
<?= $row['university_id'] ?>
</td>

<td>
<?= htmlspecialchars($row['line_name']) ?>
</td>

<td>

<?= $row['departure_time'] ?>

→

<?= $row['arrival_time'] ?>

</td>

<td>
<?= $row['days'] ?>
</td>

<td>
<?= $row['start_date'] ?>
</td>

<td>
<?= $row['end_date'] ?>
</td>

<td>

<?php

if(strtotime($row['end_date']) >= time()){

    echo "<span class='active-badge'>
    🟢 Active </span>";
}

else{

    echo "<span class='expired-badge'>
    🔴 Expired</span>";
}

?>

</td>

<td>

<a
href="cancel_bus_subscription.php?id=<?= $row['id'] ?>"
class="cancel-btn"onclick="return confirm('Cancel this subscription?')">Cancel</a>
</td>
</tr>

<?php endwhile; ?>

</table>
</div>
</div>
</body>
</html>