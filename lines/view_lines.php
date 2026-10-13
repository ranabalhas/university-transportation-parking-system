<?php

include '../../php/db.php';
include '../includes/admin_session.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$success = "";

if(isset($_SESSION['line_success'])){

    $success = $_SESSION['line_success'];

    unset($_SESSION['line_success']);
}
/* =========================
   DELETE LINE
========================= */

if(isset($_GET['delete'])){

    $line_id = intval($_GET['delete']);

    /* CHECK IF USED */

    $check = $conn->prepare("

    SELECT id
    FROM bus_trips
    WHERE bus_line_id=?

    ");

    $check->bind_param("i", $line_id);

    $check->execute();

    $result = $check->get_result();

    if($result->num_rows > 0){

        $error = "Cannot delete line because trips are assigned to it.";
    }

    else{

        $stmt = $conn->prepare("

        DELETE FROM bus_line
        WHERE id=?

        ");

        $stmt->bind_param("i", $line_id);

        $stmt->execute();

        header("Location:view_lines.php");
        exit();
    }
}

/* =========================
   GET LINES
========================= */

$lines = $conn->query("

SELECT *

FROM bus_line

ORDER BY line_name

");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>View Bus Lines</title>

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

    margin-bottom:45px;
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

    border-radius:10px;

    text-decoration:none;

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

    background:#0b2d5c;
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

/* =========================
   ALERT
========================= */

.error{

    background:#fee2e2;
    color:#991b1b;

    padding:14px;

    border-radius:10px;

    margin-bottom:20px;
}

.success{

    background:#dcfce7;
    color:#166534;

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

    .page-header{

        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    table{

        min-width:700px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="main">

    <div class="page-header">

        <h1>
            Bus Line Management
        </h1>

        <a href="add_line.php" class="add-btn">

            <i class="fa fa-plus"></i>
            Add Line

        </a>

    </div>
<?php if(!empty($success)): ?>

    <div class="success">

        <?= $success ?>

    </div>

<?php endif; ?>
    <?php if(isset($error)): ?>
	

        <div class="error">

            <?= $error ?>

        </div>

    <?php endif; ?>

    <div class="table-box">

        <table>

            <tr>

                <th>ID</th>

                <th>Line Name</th>

                <th>Actions</th>

            </tr>

            <?php while($line = $lines->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= $line['id'] ?>
                </td>

                <td>
                    <?= $line['line_name'] ?>
                </td>

                <td>

                    <a href="edit_line.php?id=<?= $line['id'] ?>"
                    class="action-btn edit-btn">

                        Edit

                    </a>

                    <a href="?delete=<?= $line['id'] ?>"
                    class="action-btn delete-btn"

                    onclick="return confirm(
                    'Delete this bus line?'
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