<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET BUS LINES*/

$lines = $conn->query("
SELECT *
FROM bus_line
ORDER BY line_name
");

/* ADD DRIVER*/

$success = "";
$error = "";
if(isset($_GET['success'])){

    $temp_password = "";

    if(isset($_SESSION['driver_temp_password'])){

        $temp_password = htmlspecialchars($_SESSION['driver_temp_password']);

        unset($_SESSION['driver_temp_password']);
    }

    $success = "Driver added successfully.<br><br>

    Temporary Password: <b>$temp_password</b>
    ";
}
if(isset($_POST['add_driver'])){

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $bus_line_id = $_POST['bus_line_id'];
	
	/* CHECK PHONE ALREADY EXISTS */

$checkPhone = $conn->prepare("
SELECT id, name
FROM driver
WHERE phone = ?
");

$checkPhone->bind_param("s", $phone);
$checkPhone->execute();

$phoneResult = $checkPhone->get_result();

if($phoneResult->num_rows > 0){

    $existingDriver = $phoneResult->fetch_assoc();

    $error =
    "Phone number <b>" .
    htmlspecialchars($phone) .
    "</b> is currently assigned to driver <b>" .
    htmlspecialchars($existingDriver['name']) .
    "</b>. Please use another phone number.";

}
else{

    /* GENERATE TEMP PASSWORD */

    $temp_password = "driver123";

    $hashed_password = password_hash(
        $temp_password,
        PASSWORD_DEFAULT
    );

    /* INSERT DRIVER */

    $stmt = $conn->prepare("
    INSERT INTO driver
    (name, phone, password, bus_line_id, first_login)
    VALUES (?, ?, ?, ?, 1)
    ");

    $stmt->bind_param(
        "sssi",
        $name,
        $phone,
        $hashed_password,
        $bus_line_id
    );

    if($stmt->execute()){

        $_SESSION['driver_success'] =
        "Driver added successfully.<br><br>
        Temporary Password:
        <b>$temp_password</b>";

        header("Location: view_drivers.php");
        exit();

    }else{

        $error = "Something went wrong.";
    }
}
}
    
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Driver</title>

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

    margin-left:380px;
    padding:110px 40px 40px;
}

/* =========================
   PAGE TITLE
========================= */

.page-title{

    margin-bottom:25px;
}

.page-title h1{

    margin:0;
    color:#0f172a;
}

/* =========================
   FORM BOX
========================= */

.form-box{

    max-width:650px;
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* =========================
   INPUT GROUP
========================= */

.input-group{

    margin-bottom:20px;
}

.input-group label{

    display:block;
    margin-bottom:8px;
    font-weight:bold;
    color:#334155;
}

.input-group input,
.input-group select{

    width:100%;
    padding:14px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:15px;
    box-sizing:border-box;
}

.input-group input:focus,
.input-group select:focus{

    outline:none;

    border-color:#2563eb;
}

/* =========================
   BUTTON
========================= */

.back-btn{

    display:inline-block;
    margin-bottom:20px;
    text-decoration:none;
    color:#2563eb;
    font-weight:bold;
}
.submit-btn{

    background:#2563eb;
    color:white;
    border:none;
    padding:14px 20px;
    border-radius:12px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.submit-btn:hover{

    background:#1d4ed8;
}

/* =========================
   ALERTS
========================= */

.success-box{

    background:#dcfce7;
    color:#166534;
    padding:18px;
    border-radius:12px;
    margin-bottom:20px;
    line-height:1.7;
}

.error-box{

    background:#fee2e2;
    color:#991b1b;
    padding:18px;
    border-radius:12px;
    margin-bottom:20px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .main-content{

        margin-left:0;

        padding:120px 20px 20px;
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

<div class="main-content">
<a href="/university_system/admin/drivers/view_drivers.php"class="back-btn"> ← Back to Drivers</a>

    <div class="page-title">

        <h1>
            ➕ Add New Driver
        </h1>

    </div>

    <div class="form-box">

        <?php if($success): ?>

            <div class="success-box">
                <?= $success ?>
            </div>

        <?php endif; ?>

        <?php if($error): ?>

            <div class="error-box">
                <?= $error ?>
            </div>

        <?php endif; ?>

        <form method="POST" id="driverForm">

            <!-- NAME -->
            <div class="input-group">

                <label>Driver Name</label>

                <input type="text"name="name"required>

            </div>

            <!-- PHONE -->
            <div class="input-group">

                <label>  Phone Number </label>

                <input type="text" name="phone"required>

            </div>

            <!-- BUS LINE -->
            <div class="input-group">

                <label>
                    Assign Bus Line
                </label>

                <select name="bus_line_id" required>

                    <option value="">  Select Bus Line </option>

                    <?php while($line = $lines->fetch_assoc()): ?>

                        <option value="<?= $line['id'] ?>">

                            <?= htmlspecialchars($line['line_name']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <!-- BUTTON -->
            <button type="submit" name="add_driver" class="submit-btn"> Add Driver</button>

        </form>
    </div>

</div>
<script>

if(window.history.replaceState){

    const url = new URL(window.location);

    url.searchParams.delete("success");

    window.history.replaceState(
        {},
        "",
        url
    );
}


window.addEventListener("pageshow", function(){

    document.getElementById("driverForm").reset();

});




</script>
</body>
</html>