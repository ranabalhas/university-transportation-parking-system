<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET ID */

if(!isset($_GET['id'])){

    header("Location:view_lines.php");
    exit();
}

$line_id = intval($_GET['id']);

/* GET LINE*/

$stmt = $conn->prepare("

SELECT *

FROM bus_line

WHERE id=?

");

$stmt->bind_param("i", $line_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    header("Location:view_lines.php");
    exit();
}

$line = $result->fetch_assoc();

/* =========================
   HANDLE UPDATE
========================= */

$success = "";
$error = "";
if(isset($_SESSION['line_update_success'])){

    $success = $_SESSION['line_update_success'];

    unset($_SESSION['line_update_success']);
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $line_name = trim($_POST['line_name']);

    if(empty($line_name)){

        $error = "Line name is required.";
    }

    else{

        $update = $conn->prepare("

        UPDATE bus_line

        SET line_name=?

        WHERE id=?

        ");

        $update->bind_param(

            "si",

            $line_name,
            $line_id
        );

        if($update->execute()){

    $_SESSION['line_update_success'] =
    "Bus line updated successfully.";

    header("Location: edit_line.php?id=$line_id");

    exit();
}

        else{

            $error = "Something went wrong.";
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

<title>Edit Bus Line</title>

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

.form-box{

    max-width:600px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.form-box h1{

    margin-top:0;
    color:#0b2d5c;
}

/* =========================
   INPUTS
========================= */

.input-group{

    margin-bottom:20px;
}

.input-group label{

    display:block;

    margin-bottom:8px;

    font-weight:bold;

    color:#333;
}

.input-group input{

    width:100%;

    padding:14px;

    border:1px solid #ccc;

    border-radius:10px;

    font-size:15px;

    box-sizing:border-box;
}

/* =========================
   BUTTON
========================= */

.save-btn{

    background:#2563eb;
    color:white;

    border:none;

    padding:14px 22px;

    border-radius:10px;

    font-size:15px;

    cursor:pointer;

    font-weight:bold;
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

    .form-box{

        padding:25px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="main">
<a href="/university_system/admin/lines/view_lines.php"class="back-btn"> ← Back to Lines</a>
    <div class="form-box">

        <h1>

            Edit Bus Line

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

            <div class="input-group">

                <label>

                    Line Name

                </label>

                <input type="text"

                name="line_name"

                value="<?= htmlspecialchars(
                $line['line_name']
                ) ?>"

                required>

            </div>

            <button type="submit"
            class="save-btn">

                <i class="fa fa-save"></i>

                Save Changes

            </button>

        </form>

    </div>

</div>

</body>
</html>