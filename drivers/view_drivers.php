<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET DRIVERS */

$drivers = $conn->query("
SELECT driver.*,
bus_line.line_name

FROM driver

LEFT JOIN bus_line
ON driver.bus_line_id = bus_line.id

ORDER BY driver.id DESC

");

$success = "";

if(isset($_SESSION['driver_success'])){

    $success = $_SESSION['driver_success'];

    unset($_SESSION['driver_success']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"content="width=device-width, initial-scale=1.0">

<title>View Drivers</title>

<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{

    margin:0;
    font-family:Arial;
    background:#f1f5f9;
}

/* =========================
   MAIN CONTENT
========================= */

.main-content{

    margin-left:330px;
    padding:110px 70px 70px;
	padding-top:150px;
}

/* =========================
   TOP BAR
========================= */

.top-bar{

    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    flex-wrap:wrap;
    gap:15px;
}

.top-bar h1{

    margin:0;
    color:#0f172a;
}

/* =========================
   ADD BUTTON
========================= */

.add-btn{

    background:#2563eb;
    color:white;
    text-decoration:none;
    padding:12px 18px;
    border-radius:12px;
    font-weight:bold;
    transition:0.3s;
}

.add-btn:hover{

    background:#1d4ed8;
}

/* =========================
   TABLE BOX
========================= */

.table-box{

    background:white;

    border-radius:20px;

    overflow:hidden;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* =========================
   TABLE
========================= */

table{

    width:100%;

    border-collapse:collapse;
}

thead{

    background:#0f172a;
    color:white;
}

th, td{

    padding:18px;
    text-align:left;
    border-bottom:1px solid #e2e8f0;
}

tbody tr:hover{

    background:#f8fafc;
}

/* =========================
   BADGES
========================= */

.badge{

    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.inactive{

    background:#fee2e2;

    color:#991b1b;
}
.first-login{

    background:#fef3c7;
    color:#92400e;
}

.active{

    background:#dcfce7;

    color:#166534;
}

/* =========================
   ACTION BUTTONS
========================= */

.actions{

    display:flex;
    gap:10px;
    flex-wrap:nowrap;
}

.action-btn{

    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:14px;
    font-weight:bold;
    transition:0.3s;
}

.edit-btn{

    background:#2563eb;
}

.reset-btn{

    background:red;
}
.deactivate-btn{

    background:#dc2626;
}

.activate-btn{

    background:#16a34a;
}

.action-btn:hover{

    opacity:0.9;
}

.success-box{

    background:#dcfce7;
    color:#166534;
    padding:18px;
    border-radius:12px;
    margin-bottom:20px;
    line-height:1.7;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .main-content{

        margin-left:0;

        padding:120px 15px 20px;
    }

    .table-box{

        overflow-x:auto;
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

<div class="main-content">
<?php if($success): ?>

<div class="success-box">
    <?= $success ?>
</div>

<?php endif; ?>
    <!-- TOP -->
    <div class="top-bar">

        <h1>
            🚍 Driver Management
        </h1>

        <a href="/university_system/admin/drivers/add_driver.php"class="add-btn">

            <i class="fa fa-plus"></i>Add Driver </a>

    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table>
            <thead>
                <tr>

                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Bus Line</th>
                    <th>Status</th>
                    <th>Actions</th>

                </tr>
            </thead>

            <tbody>
                <?php while($driver = $drivers->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $driver['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($driver['name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($driver['phone']) ?>
                    </td>

                    <td>

                        <?= $driver['line_name']
                        ?? 'No Line Assigned' ?>

                    </td>

                    <td>

<?php if($driver['status'] == 'inactive'): ?>

    <span class="badge inactive">  Inactive</span>

<?php elseif($driver['first_login'] == 1): ?>

    <span class="badge first-login">  Temporary Password </span>

<?php else: ?>

    <span class="badge active"> Active </span>

<?php endif; ?>

</td>
                    <td>

                        <div class="actions">

                            <!-- EDIT -->
                            <a href="/university_system/admin/drivers/edit_driver.php?id=<?= $driver['id'] ?>"
                            class="action-btn edit-btn">

                                <i class="fa fa-pen"></i>

                            </a>

                            <!-- RESET -->
                            <a href="/university_system/admin/drivers/reset_driver_password.php?id=<?= $driver['id'] ?>"
                            class="action-btn reset-btn">

                                <i class="fa fa-key"></i>

                            </a>
<?php if($driver['status'] == 'active'): ?>

<a href="/university_system/admin/drivers/toggle_driver_status.php?id=<?= $driver['id'] ?>"
class="action-btn deactivate-btn"

onclick="return confirm('Deactivate this driver?')">

    <i class="fa fa-user-slash"></i>

</a>

<?php else: ?>

<a href="/university_system/admin/drivers/toggle_driver_status.php?id=<?= $driver['id'] ?>"
class="action-btn activate-btn"

onclick="return confirm('Activate this driver?')">

    <i class="fa fa-user-check"></i>

</a>

<?php endif; ?>
                        </div>

                    </td>

                </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>