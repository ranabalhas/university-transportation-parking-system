<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET DRIVER ID*/

if(!isset($_GET['id'])){

    die("Driver ID missing.");
}

$id = intval($_GET['id']);

/* GET DRIVER*/

$stmt = $conn->prepare("

SELECT *
FROM driver
WHERE id=?

");

$stmt->bind_param("i", $id);
$stmt->execute();

$driver = $stmt->get_result()->fetch_assoc();

if(!$driver){

    die("Driver not found.");
}

/*  GENERATE TEMP PASSWORD */

$temp_password = "driver" . rand(1000,9999);

$hashed_password = password_hash( $temp_password, PASSWORD_DEFAULT);

/* UPDATE PASSWORD */

$update = $conn->prepare("

UPDATE driver

SET
password=?,
first_login=1

WHERE id=?

");

$update->bind_param(
    "si",
    $hashed_password,
    $id
);

$update->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Password Reset</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

    margin-left:351px;

    padding:110px 40px 40px;
}

/* =========================
   CARD
========================= */

.reset-card{

    max-width:650px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* =========================
   TITLE
========================= */

.reset-card h1{

    margin-top:0;

    color:#0f172a;

    margin-bottom:25px;
}

/* =========================
   SUCCESS BOX
========================= */

.success-box{

    background:#dcfce7;

    color:#166534;

    padding:25px;

    border-radius:16px;

    line-height:1.8;

    font-size:17px;
}

/* PASSWORD STYLE */

.password{

    display:inline-block;

    background:#0f172a;

    color:#fbbf24;

    padding:10px 18px;

    border-radius:10px;

    font-size:22px;

    font-weight:bold;

    margin-top:12px;
}

/* =========================
   BUTTONS
========================= */

.buttons{

    margin-top:30px;

    display:flex;

    gap:15px;

    flex-wrap:wrap;
}

.btn{

    text-decoration:none;

    padding:14px 18px;

    border-radius:12px;

    font-weight:bold;

    color:white;

    transition:0.3s;
}

.blue-btn{

    background:#2563eb;
}

.dark-btn{

    background:#0f172a;
}

.btn:hover{

    opacity:0.9;
}
.back-btn{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#2563eb;

    font-weight:bold;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .main-content{

        margin-left:0;

        padding:120px 20px 20px;
    }

    .reset-card{

        padding:25px;
    }

    .password{

        font-size:18px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>
<?php include '../includes/admin_navbar.php'; ?>

<div class="main-content">

<a href="/university_system/admin/drivers/view_drivers.php"class="back-btn"> ← Back to Drivers</a>
    <div class="reset-card">

        <h1>
            🔑 Password Reset Successful
        </h1>

        <div class="success-box">

            Driver password has been reset successfully.

            <br><br>

            Driver:
            <b>
                <?= htmlspecialchars($driver['name']) ?>
            </b>

            <br><br>

            Temporary Password:

            <br>

            <div class="password">

                <?= $temp_password ?>

            </div>

            <br><br>

            Driver will be required to change
            password on next login.

        </div>

    </div>

</div>

</body>
</html>