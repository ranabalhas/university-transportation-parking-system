<?php
include '../../php/db.php';
include '../includes/admin_session.php';

/* SEARCH*/
$search = "";

if(isset($_GET['search'])){

    $search = trim($_GET['search']);
}

/* QUERY*/

$sql = "
SELECT

parking_subscriptions.*,

students.username,
students.university_id

FROM parking_subscriptions

LEFT JOIN students
ON parking_subscriptions.university_id = students.university_id

WHERE
students.username LIKE ?
OR students.university_id LIKE ?

ORDER BY parking_subscriptions.id DESC

";

$stmt = $conn->prepare($sql);

$search_param = "%$search%";

$stmt->bind_param("ss", $search_param, $search_param);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang='en'>

<head>

<meta charset='UTF-8'>

<title>Parking Subscriptions</title>

<meta name='viewport'
content='width=device-width, initial-scale=1.0'>

<link rel='stylesheet'
href='../../css/style.css'>

<style>

.page-content{

    margin-left:351px;
    padding:30px;
}

.page-title{

    font-size:32px;
    font-weight:bold;

    color:#0b2d5c;

    margin-bottom:40px;
}

.top-bar{

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:25px;

    gap:20px;

    flex-wrap:wrap;
}

.search-box{

    flex:1;
}

.search-box input{

    width:100%;

    padding:14px;

    border-radius:10px;

    border:1px solid #ccc;

    font-size:15px;
}

.table-box{

    background:white;

    border-radius:18px;

    overflow:auto;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

table{

    width:100%;
    border-collapse:collapse;
}

th{

    background:#0b2d5c;
    color:white;

    padding:16px;
    text-align:left;
}

td{

    padding:15px;
    border-bottom:1px solid #eee;
}

tr:hover{

    background:#f8fafc;
}

.status{

    padding:6px 12px;

    border-radius:20px;

    font-size:13px;

    font-weight:bold;

    display:inline-block;
}

.active{

    background:#dcfce7;
    color:#166534;
}

.expired{

    background:#fee2e2;
    color:#991b1b;
}

.cancel-btn{

    background:#dc2626;

    color:white;

    padding:8px 12px;

    border-radius:8px;

    text-decoration:none;

    font-size:14px;
}

.cancel-btn:hover{

    background:#ef4444;
}

@media(max-width:900px){

.page-content{

    margin-left:0;
    padding:20px;
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

<div class="page-content">

<div class="page-title">🚗 Parking Subscriptions Management</div>

<div class="top-bar">

<form class="search-box" method="GET">

<input type="text"name="search"placeholder="Search by Student ID or Username..."value="<?= htmlspecialchars($search) ?>">
</form>
</div>
<div class="table-box">

<table>
<tr>
<th>ID</th>
<th>Student</th>
<th>University ID</th>
<th>Plan</th>
<th>Registration</th>
<th>Expiration</th>
<th>Status</th>
<th>Action</th>

</tr>

<?php while($row = $result->fetch_assoc()): ?>

<?php

$status =
(strtotime($row['expiration_date']) >= time())
? "Active"
: "Expired";

?>
<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['username']) ?></td>

<td><?= $row['university_id'] ?></td>

<td><?= htmlspecialchars($row['plan']) ?></td>

<td><?= $row['registration_date'] ?></td>

<td><?= $row['expiration_date'] ?></td>

<td>

<?php if($status == "Active"): ?>

<span class="status active">🟢 Active</span>

<?php else: ?>

<span class="status expired">🔴 Expired</span>

<?php endif; ?>

</td>

<td>

<a class="cancel-btn" href="cancel_parking_subscription.php?id=<?= $row['id'] ?>" onclick="return confirm('Cancel this parking subscription?')">Cancel</a>

</td>

</tr>

<?php endwhile; ?>

</table>
</div>
</div>
</body>
</html>